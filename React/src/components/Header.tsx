import { useState, useEffect, useRef } from 'react'
import { Link, useLocation } from 'react-router'

const navItems = [
  { label: 'خانه', href: '/' },
  {
    label: 'خدمات', href: '/services',
    children: [
      { label: 'ایمپلنت دندان', href: '/services/dental-implant/single-implant' },
      { label: 'ارتودنسی', href: '/services/orthodontics/metal-braces' },
      { label: 'دندانپزشکی زیبایی', href: '/services/cosmetic-dentistry/smile-design' },
      { label: 'جراحی دهان', href: '/services/oral-surgery/wisdom-tooth-extraction' },
      { label: 'اندودنتیکس', href: '/services/endodontics/root-canal-treatment' },
      { label: 'پریودنتیکس', href: '/services/periodontics/gum-graft' },
      { label: 'دندانپزشکی کودکان', href: '/services/pediatric-dentistry/fissure-sealant' },
      { label: 'دندانپزشکی عمومی', href: '/services/general-dentistry/dental-checkup' },
      { label: 'پروتز دندان', href: '/services/prosthodontics/zirconia-crown' },
      { label: 'اورژانس دندان', href: '/services/dental-emergency/emergency-toothache' },
    ],
  },
  { label: 'درباره ما', href: '/about' },
  { label: 'قیمت‌ها', href: '/pricing' },
  { label: 'گالری', href: '/gallery' },
  { label: 'وبلاگ', href: '/knowledge-base' },
  { label: 'پرسش‌های متداول', href: '/faq' },
  { label: 'تماس', href: '/contact' },
]

export default function Header() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)
  const [openDropdown, setOpenDropdown] = useState<string | null>(null)
  const [mobileExpanded, setMobileExpanded] = useState<string | null>(null)
  const location = useLocation()
  const drawerRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 20)
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  useEffect(() => {
    setMobileOpen(false)
    setOpenDropdown(null)
  }, [location.pathname])

  useEffect(() => {
    if (mobileOpen) {
      document.body.classList.add('mobile-nav-open')
      drawerRef.current?.focus()
    } else {
      document.body.classList.remove('mobile-nav-open')
    }
    return () => document.body.classList.remove('mobile-nav-open')
  }, [mobileOpen])

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') { setMobileOpen(false); setOpenDropdown(null) }
    }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [])

  const isActive = (href: string) =>
    href === '/' ? location.pathname === '/' : location.pathname.startsWith(href)

  return (
    <header
      className={`fixed top-0 right-0 left-0 z-50 transition-all duration-300 ${
        scrolled ? 'bg-white shadow-lg' : 'bg-white/95 backdrop-blur-md'
      }`}
      role="banner"
    >
      {/* Top bar */}
      <div className="bg-gradient-to-l from-[#071F3F] to-[#0D54AF] text-white py-1.5 px-4">
        <div className="max-w-7xl mx-auto flex items-center justify-between text-xs">
          <div className="flex items-center gap-4">
            <span className="flex items-center gap-1">
              <svg className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
              <a href="tel:+982188888888" className="ltr hover:text-[#08CBCD] transition-colors">۰۲۱-۸۸۸۸۸۸۸۸</a>
            </span>
            <span className="flex items-center gap-1">
              <svg className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/></svg>
              شنبه تا پنج‌شنبه ۹ تا ۲۰
            </span>
          </div>
          <div className="hidden md:flex items-center gap-3">
            <span>تهران، خیابان ولیعصر، بالاتر از میدان ونک</span>
          </div>
        </div>
      </div>

      {/* Main header */}
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex items-center h-16 gap-4">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-3 flex-shrink-0 order-1" aria-label="صفحه اصلی">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] flex items-center justify-center shadow-md">
              <span className="text-white text-lg">🦷</span>
            </div>
            <div className="hidden sm:block">
              <div className="font-bold text-[#071F3F] text-sm leading-tight">کلینیک دندانپزشکی</div>
              <div className="text-[#0D54AF] text-xs font-medium">دکتر کیوان علی‌پسندی</div>
            </div>
          </Link>

          {/* Desktop nav */}
          <nav className="hidden lg:flex items-center gap-0.5 order-2 flex-1 justify-center" aria-label="فهرست اصلی">
            {navItems.map((item) => (
              <div
                key={item.href}
                className="relative"
                onMouseEnter={() => item.children && setOpenDropdown(item.label)}
                onMouseLeave={() => setOpenDropdown(null)}
              >
                <Link
                  to={item.href}
                  className={`flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
                    isActive(item.href)
                      ? 'text-[#0D54AF] bg-blue-50'
                      : 'text-gray-700 hover:text-[#0D54AF] hover:bg-blue-50'
                  }`}
                  aria-current={isActive(item.href) ? 'page' : undefined}
                  aria-haspopup={item.children ? 'true' : undefined}
                  aria-expanded={item.children ? openDropdown === item.label : undefined}
                >
                  {item.label}
                  {item.children && (
                    <svg className={`w-3.5 h-3.5 transition-transform ${openDropdown === item.label ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                    </svg>
                  )}
                </Link>
                {item.children && openDropdown === item.label && (
                  <div className="absolute top-full right-0 mt-1 w-52 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50">
                    {item.children.map(child => (
                      <Link
                        key={child.href}
                        to={child.href}
                        className="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#0D54AF] transition-colors"
                      >
                        {child.label}
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </nav>

          {/* Header actions */}
          <div className="flex items-center gap-2 order-3 mr-auto">
            <Link
              to="/appointment"
              className="hidden md:flex items-center gap-2 bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity shadow-md"
            >
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              رزرو نوبت
            </Link>
            {/* Hamburger */}
            <button
              id="primary-menu-toggle"
              onClick={() => setMobileOpen(!mobileOpen)}
              className="lg:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5 rounded-xl hover:bg-gray-100 transition-colors"
              aria-label={mobileOpen ? 'بستن منو' : 'باز کردن منو'}
              aria-expanded={mobileOpen}
              aria-controls="primary-navigation"
            >
              <span className={`block w-5 h-0.5 bg-gray-700 transition-all duration-300 ${mobileOpen ? 'rotate-45 translate-y-2' : ''}`} />
              <span className={`block w-5 h-0.5 bg-gray-700 transition-all duration-300 ${mobileOpen ? 'opacity-0' : ''}`} />
              <span className={`block w-5 h-0.5 bg-gray-700 transition-all duration-300 ${mobileOpen ? '-rotate-45 -translate-y-2' : ''}`} />
            </button>
          </div>
        </div>
      </div>

      {/* Mobile backdrop */}
      {mobileOpen && (
        <div
          className="nav-backdrop fixed inset-0 bg-black/50 z-40 lg:hidden"
          onClick={() => setMobileOpen(false)}
          aria-hidden="true"
        />
      )}

      {/* Mobile drawer */}
      <div
        id="primary-navigation"
        ref={drawerRef}
        tabIndex={-1}
        className={`site-nav fixed top-0 right-0 h-full w-72 bg-white z-50 lg:hidden transform transition-transform duration-300 overflow-y-auto ${
          mobileOpen ? 'translate-x-0' : 'translate-x-full'
        }`}
        aria-hidden={!mobileOpen}
      >
        <div className="flex items-center justify-between p-4 border-b border-gray-100">
          <div>
            <div className="font-bold text-[#071F3F] text-sm">کلینیک دندانپزشکی</div>
            <div className="text-[#0D54AF] text-xs">دکتر کیوان علی‌پسندی</div>
          </div>
          <button
            onClick={() => setMobileOpen(false)}
            className="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100"
            aria-label="بستن منو"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <nav className="p-3">
          {navItems.map((item) => (
            <div key={item.href}>
              <div className="flex items-center">
                <Link
                  to={item.href}
                  className={`flex-1 flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors ${
                    isActive(item.href) ? 'text-[#0D54AF] bg-blue-50' : 'text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  {item.label}
                </Link>
                {item.children && (
                  <button
                    onClick={() => setMobileExpanded(mobileExpanded === item.label ? null : item.label)}
                    className="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100"
                    aria-expanded={mobileExpanded === item.label}
                    aria-label={`زیرمنوی ${item.label}`}
                  >
                    <svg className={`w-4 h-4 transition-transform ${mobileExpanded === item.label ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" /></svg>
                  </button>
                )}
              </div>
              {item.children && mobileExpanded === item.label && (
                <div className="mr-4 border-r-2 border-[#08CBCD] pr-3 mt-1 mb-2">
                  {item.children.map(child => (
                    <Link
                      key={child.href}
                      to={child.href}
                      className="block px-3 py-2 text-sm text-gray-600 hover:text-[#0D54AF] rounded-lg hover:bg-blue-50 transition-colors"
                    >
                      {child.label}
                    </Link>
                  ))}
                </div>
              )}
            </div>
          ))}
          <div className="mt-4 pt-4 border-t border-gray-100">
            <Link
              to="/appointment"
              className="flex items-center justify-center gap-2 w-full bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white py-3 rounded-xl font-semibold text-sm"
            >
              رزرو نوبت آنلاین
            </Link>
            <a
              href="tel:+982188888888"
              className="flex items-center justify-center gap-2 w-full mt-3 border-2 border-[#0D54AF] text-[#0D54AF] py-3 rounded-xl font-semibold text-sm ltr"
            >
              ۰۲۱-۸۸۸۸۸۸۸۸
            </a>
          </div>
        </nav>
      </div>
    </header>
  )
}
