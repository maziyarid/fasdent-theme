# FasDent Production Deployment Checklist

## Release Information

- **Theme Version:** v1.4.29
- **Plugin Version:** v1.3.4
- **Build Date:** 2026-08-15
- **Status:** Ready for Staging → Runtime Evidence → Production

---

## Pre-Deployment: Code Review ✅

### Code-Level Fixes (Issues 1-6)

- [x] **Issue 1:** Homepage NAP hard-coded fallback removed
  - File: `front-page.php`
  - Change: No fallback values, SSOT-only display
  
- [x] **Issue 2:** Operational Health validates clinic_street
  - File: `includes/health-checks.php`
  - Change: Added `check_street_missing()` method
  
- [x] **Issue 3:** Partial/invalid Local PostalAddress prevented
  - File: `includes/site-settings.php`
  - Change: NAP_Helper validates completeness before schema emission
  
- [x] **Issue 4:** Booking Slots ↔ Opening Hours cross-validation
  - File: `includes/validators.php`
  - Change: `validate_booking_hours_interoperability()` method
  
- [x] **Issue 5:** Form fail-safe with phone route
  - File: `includes/forms.php`
  - Change: Contract A - phone required for form readiness
  
- [x] **Issue 6:** NAP readiness helper (shared contract)
  - File: `includes/site-settings.php`
  - Change: NAP_Helper class with shared validation

### Health/Schema Contracts (Issues 7-13)

- [x] **Issue 7:** Rank Math Health freshness + deployment determinism
  - Explicit observation probe before health checks
  
- [x] **Issue 8:** Rank Math Graph runtime observation metadata
  - `observed_at`, `rank_math_version`, `context` logged
  
- [x] **Issue 9:** Schema Repair human-readable diff
  - Documented in service-content.php
  
- [x] **Issue 10:** Schema Repair environment validation
  - `can_apply_repair()` checks `WP_ENVIRONMENT_TYPE=staging`
  
- [x] **Issue 11:** Opening Hours ↔ Booking UX cross-validator
  - `generate_available_dates()` returns non_operational if no usable slots
  
- [x] **Issue 12:** Owner-approved NAP Freeze
  - NAP_Freeze class with 17 fields
  
- [x] **Issue 13:** Home/Contact/Footer NAP consistency test
  - `tests/nap-consistency-test.php`

---

## Stage 1: Staging Deployment

### Environment Setup

- [ ] Staging environment created
- [ ] `WP_ENVIRONMENT_TYPE=staging` defined
- [ ] PHP 8.2+ verified
- [ ] WordPress 6.4+ verified
- [ ] Production-identical configuration

### Deploy Artifacts

- [ ] Theme v1.4.29 uploaded
- [ ] Plugin v1.3.4 uploaded
- [ ] SHA256 checksums verified:
  - Theme: `________________________`
  - Plugin: `________________________`

### Initial Smoke Tests

- [ ] Site loads without errors
- [ ] Admin accessible
- [ ] Plugin activates without errors
- [ ] Theme switches without errors

---

## Stage 2: Runtime Evidence Collection

### Health Checks

Run: `wp eval 'var_dump(\Alipasandi\FasDent\run_health_checks());'`

- [ ] Overall status: PASS (no critical)
- [ ] `street_missing`: PASS
- [ ] `address_missing`: PASS or WARNING (acceptable)
- [ ] `phone_missing`: PASS
- [ ] `booking_hours_interoperability`: PASS
- [ ] `form_readiness`: PASS or WARNING (acceptable)
- [ ] `nap_completeness`: PASS
- [ ] `rank_math_observation`: PASS
- [ ] `loopback_status`: PASS
- [ ] `wp_cron_status`: PASS
- [ ] `debug_display`: PASS

### NAP Consistency Test

Run: `wp eval 'var_dump(\Alipasandi\FasDent\Tests\test_nap_consistency());'`

- [ ] Test passed: YES
- [ ] No mismatches found
- [ ] All 5 sources consistent (Home, Contact, Footer, Email, Schema)

### Booking/Hours Validation

Run: `wp eval 'var_dump(\Alipasandi\FasDent\validate_booking_hours());'`

- [ ] Status: PASS or WARNING (acceptable)
- [ ] Usable slots > 0
- [ ] No critical interoperability issues

### NAP Freeze

Run: `wp eval 'var_dump(\Alipasandi\FasDent\NAP_Freeze::is_frozen());'`

- [ ] NAP frozen: YES
- [ ] Owner approval documented
- [ ] Integrity verification: PASS

### Rank Math Observation

Run: `wp eval 'var_dump(get_option("alipasandi_last_observation"));'`

- [ ] Observation exists: YES
- [ ] `observed_at`: Recent (within 15 minutes)
- [ ] `rank_math_version`: Matches active version
- [ ] `context`: `health_probe` or `runtime_evidence`

---

## Stage 3: Production Hard Gates

### Runtime Environment

- [ ] **PHP 8.2+** verified (actual runtime, not just CLI)
- [ ] **WordPress 6.4+** verified
- [ ] **Production-identical staging** confirmed
- [ ] **WP-CLI functional** (tested with actual commands)

### Code Quality

- [ ] **PHPUnit tests** passing
- [ ] **WPCS/PHPCS** passing (no violations)
- [ ] **PHPStan** passing (no errors)
- [ ] **Deprecated functions** scan: clean

### Rank Math

- [ ] **Rank Math active** (not inactive/reactivated)
- [ ] **LocalBusiness schema** validates
- [ ] **streetAddress** non-empty
- [ ] **Observation freshness** verified

### Email/SMTP

- [ ] **SMTP configured** (host, port, auth)
- [ ] **SPF record** configured
- [ ] **DKIM configured** (if available)
- [ ] **DMARC policy** configured
- [ ] **Inbox delivery** verified (test email received)

### Debug Configuration

- [ ] **WP_DEBUG** = true (acceptable for logging)
- [ ] **WP_DEBUG_DISPLAY** = false (REQUIRED)
- [ ] **WP_DEBUG_LOG** = true (acceptable)
- [ ] **Debug log private** (not publicly accessible)
- [ ] **Log rotation** configured
- [ ] **PII review** completed (no sensitive data in logs)

### Infrastructure

- [ ] **Loopback** HTTP 200 (not 503)
- [ ] **WP-Cron** functional
- [ ] **Backup** system configured
- [ ] **Restore** tested
- [ ] **Host redirects** configured
- [ ] **Canonical URLs** correct
- [ ] **Indexability** allowed (no noindex)

### Performance & Security

- [ ] **Security headers** configured (CSP, X-Frame-Options, etc.)
- [ ] **Cache** configured
- [ ] **post_modified/lastmod** verified
- [ ] **CWV** passing (Core Web Vitals)
- [ ] **Browser compatibility** tested
- [ ] **A11y** passing (accessibility)
- [ ] **Admin RTL** verified (for Persian)

### Compliance

- [ ] **Privacy policy** published
- [ ] **Claims register** completed
- [ ] **Asset approvals** documented
- [ ] **Medical claims** reviewed:
  - [ ] "ایمپلنت تخصصی"
  - [ ] "برندهای معتبر جهانی"
  - [ ] "جراحی تخصصی"
  - [ ] "بدون تراش دندان‌های مجاور"
  - [ ] All outcome/comparative claims registered

---

## Stage 4: Owner Approvals

### NAP Freeze Approval

- [ ] Owner name: `________________`
- [ ] Approval date: `________________`
- [ ] Signature hash: `________________`
- [ ] All 17 fields frozen:
  - [ ] business_name
  - [ ] clinic_phone_display
  - [ ] clinic_phone_e164
  - [ ] clinic_street
  - [ ] clinic_city
  - [ ] clinic_region
  - [ ] clinic_country
  - [ ] clinic_postal
  - [ ] clinic_geo_lat
  - [ ] clinic_geo_lng
  - [ ] clinic_map_embed
  - [ ] clinic_notify_email
  - [ ] mail_from
  - [ ] mail_from_name
  - [ ] booking_slots
  - [ ] opening_hours
  - [ ] social_urls

### Horizon/Lead Targets

- [ ] **Horizon 365** approved by owner
- [ ] **Lead 0** approved by owner
- [ ] Approval documented

### Claims Register

- [ ] All medical claims reviewed
- [ ] No unapproved claims
- [ ] Register signed off

---

## Stage 5: Production Deployment

### Pre-Deployment

- [ ] All hard gates passed
- [ ] All owner approvals obtained
- [ ] Backup created
- [ ] Rollback plan documented

### Deployment

- [ ] Theme v1.4.29 deployed to production
- [ ] Plugin v1.3.4 deployed to production
- [ ] SHA256 checksums verified
- [ ] Site accessible

### Post-Deployment Verification

- [ ] Health checks run on production
- [ ] NAP consistency test passed
- [ ] Rank Math schema validates
- [ ] Form submissions work
- [ ] Email delivery verified
- [ ] No errors in debug log

---

## Sign-Off

### Deployment Team

- **Deployed by:** `________________`
- **Deployment date:** `________________`
- **Deployment time:** `________________`

### Owner Approval

- **Approved by:** `________________`
- **Approval date:** `________________`
- **Notes:** `________________`

---

## Emergency Rollback

If issues detected post-deployment:

1. **Immediate action:**
   - Revert to previous version
   - Restore from backup

2. **Contact:**
   - Technical lead: `________________`
   - Owner: `________________`

3. **Rollback commands:**
   ```bash
   wp theme activate previous-theme
   wp plugin deactivate fasdent-plugin
   wp plugin activate previous-plugin-version
   ```

---

**END OF CHECKLIST**