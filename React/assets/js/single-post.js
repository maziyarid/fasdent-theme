/**
 * Fasdent — Single Post: progressive-enhancement behaviors.
 *
 * Append this file to your main script bundle (or enqueue separately,
 * after your main bundle, with defer/in-footer). Vanilla JS, no
 * dependencies. Every feature checks for its markup before wiring up,
 * so this file is safe to load on any page.
 */
( function () {
	"use strict";

	var prefersReducedMotion = window.matchMedia
		? window.matchMedia( "(prefers-reduced-motion: reduce)" ).matches
		: false;

	document.addEventListener( "DOMContentLoaded", function () {
		initReadingProgress();
		initJumpLinks();
		initReactions();
	} );

	/* ------------------------------------------------------------------
	 * Reading progress bar
	 * ------------------------------------------------------------------ */
	function initReadingProgress() {
		var bar = document.querySelector( ".single-post .reading-progress" );
		var article = document.querySelector( ".single-post .post-content" );

		if ( !bar || !article ) {
			return;
		}

		var ticking = false;

		function update() {
			ticking = false;

			var rect = article.getBoundingClientRect();
			var articleTop = rect.top + window.scrollY;
			var articleHeight = article.offsetHeight;
			var viewport = window.innerHeight;
			var scrolled = window.scrollY - articleTop + viewport;
			var total = articleHeight;

			var percent = total > 0 ? ( scrolled / total ) * 100 : 0;
			percent = Math.max( 0, Math.min( 100, percent ) );

			bar.style.setProperty( "--fp-progress", percent + "%" );
			bar.setAttribute( "aria-valuenow", String( Math.round( percent ) ) );
		}

		function requestUpdate() {
			if ( !ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}

		window.addEventListener( "scroll", requestUpdate, { passive: true } );
		window.addEventListener( "resize", requestUpdate );
		update();
	}

	/* ------------------------------------------------------------------
	 * Jump links: mobile collapse, smooth scroll, scrollspy
	 * ------------------------------------------------------------------ */
	function initJumpLinks() {
		var nav = document.querySelector( ".post-jump-links" );
		if ( !nav ) {
			return;
		}

		var toggle = nav.querySelector( ".jump-links-toggle" );
		var list = nav.querySelector( ".jump-links-list" );
		var links = Array.prototype.slice.call( nav.querySelectorAll( "a[href^=\"#\"]" ) );

		// Collapse by default on narrow viewports only; wide viewports
		// (where a sidebar TOC usually exists too) keep it open.
		var collapseQuery = window.matchMedia( "(max-width: 1023px)" );

		function applyCollapsedState( collapsed ) {
			nav.setAttribute( "data-collapsed", collapsed ? "true" : "false" );
			if ( toggle ) {
				toggle.setAttribute( "aria-expanded", collapsed ? "false" : "true" );
			}
		}

		if ( toggle && list ) {
			applyCollapsedState( collapseQuery.matches );

			toggle.addEventListener( "click", function () {
				var isCollapsed = nav.getAttribute( "data-collapsed" ) === "true";
				applyCollapsedState( !isCollapsed );
			} );
		}

		// Smooth scroll + focus management for keyboard/screen-reader users.
		links.forEach( function ( link ) {
			link.addEventListener( "click", function ( event ) {
				var targetId = link.getAttribute( "href" ).slice( 1 );
				var target = targetId ? document.getElementById( targetId ) : null;

				if ( !target ) {
					return;
				}

				event.preventDefault();

				target.scrollIntoView( {
					behavior: prefersReducedMotion ? "auto" : "smooth",
					block: "start",
				} );

				if ( history.pushState ) {
					history.pushState( null, "", "#" + targetId );
				}

				// Defer focus until the scroll settles so screen readers
				// announce the right heading instead of the old scroll spot.
				window.setTimeout( function () {
					target.focus( { preventScroll: true } );
				}, prefersReducedMotion ? 0 : 400 );
			} );
		} );

		// Scrollspy: highlight the link for the section currently in view.
		var headingIds = links
			.map( function ( link ) {
				return link.getAttribute( "href" ).slice( 1 );
			} )
			.filter( Boolean );

		var headings = headingIds
			.map( function ( id ) {
				return document.getElementById( id );
			} )
			.filter( Boolean );

		if ( !headings.length || !window.IntersectionObserver ) {
			return;
		}

		var linkById = {};
		links.forEach( function ( link ) {
			linkById[ link.getAttribute( "href" ).slice( 1 ) ] = link;
		} );

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					var link = linkById[ entry.target.id ];
					if ( !link ) {
						return;
					}
					if ( entry.isIntersecting ) {
						links.forEach( function ( l ) {
							l.removeAttribute( "aria-current" );
						} );
						link.setAttribute( "aria-current", "true" );
					}
				} );
			},
			{ rootMargin: "-20% 0px -70% 0px", threshold: 0 }
		);

		headings.forEach( function ( heading ) {
			observer.observe( heading );
		} );
	}

	/* ------------------------------------------------------------------
	 * Reactions ("helpful / thanks / accurate" buttons)
	 *
	 * Requires a server-side handler for the `fasdent_react` AJAX action —
	 * see the comment block at the top of single.php for the functions.php
	 * snippet to add. Without it, votes still get a responsive local UI
	 * (via localStorage) but will not persist server-side counts.
	 * ------------------------------------------------------------------ */
	function initReactions() {
		var container = document.querySelector( "[data-post-reactions]" );
		if ( !container ) {
			return;
		}

		var ajaxUrl = container.getAttribute( "data-ajax-url" );
		var nonce = container.getAttribute( "data-nonce" );
		var postId = container.getAttribute( "data-post-id" );
		var storageKey = "fasdent_reacted_" + postId;

		var alreadyReacted = null;
		try {
			alreadyReacted = window.localStorage.getItem( storageKey );
		} catch ( err ) {
			alreadyReacted = null;
		}

		var buttons = Array.prototype.slice.call(
			container.querySelectorAll( ".reaction-btn" )
		);

		buttons.forEach( function ( button ) {
			if ( alreadyReacted && button.getAttribute( "data-reaction" ) === alreadyReacted ) {
				markAsVoted( button );
			}
			if ( alreadyReacted ) {
				button.disabled = true;
			}

			button.addEventListener( "click", function () {
				if ( alreadyReacted ) {
					return;
				}

				var reaction = button.getAttribute( "data-reaction" );
				alreadyReacted = reaction;

				buttons.forEach( function ( b ) {
					b.disabled = true;
				} );
				markAsVoted( button );
				bumpCount( button );

				try {
					window.localStorage.setItem( storageKey, reaction );
				} catch ( err ) {
					// Storage unavailable (private browsing, quota, etc.) —
					// the vote still submits, it just won't be remembered
					// locally on this device.
				}

				if ( ajaxUrl && nonce && postId ) {
					submitReaction( ajaxUrl, {
						action: "fasdent_post_reaction",
						post_id: postId,
						reaction: reaction,
						nonce: nonce,
					} ).catch( function () {
						// Network/server error: the optimistic local UI
						// already reflects the vote, so we fail silently
						// rather than interrupt the reader.
					} );
				}
			} );
		} );

		function markAsVoted( button ) {
			button.setAttribute( "aria-pressed", "true" );
		}

		function bumpCount( button ) {
			var countEl = button.querySelector( ".reaction-btn__count" );
			if ( !countEl ) {
				return;
			}
			var current = parseInt( button.getAttribute( "data-count" ), 10 ) || 0;
			var next = current + 1;
			button.setAttribute( "data-count", String( next ) );
			countEl.textContent = "(" + next.toLocaleString( "fa-IR" ) + ")";
			countEl.removeAttribute( "hidden" );
		}

		function submitReaction( url, payload ) {
			var body = new URLSearchParams();
			Object.keys( payload ).forEach( function ( key ) {
				body.append( key, payload[ key ] );
			} );

			return fetch( url, {
				method: "POST",
				credentials: "same-origin",
				headers: { "Content-Type": "application/x-www-form-urlencoded" },
				body: body.toString(),
			} ).then( function ( response ) {
				if ( !response.ok ) {
					throw new Error( "fasdent_react request failed" );
				}
				return response.json();
			} );
		}
	}
} )();
