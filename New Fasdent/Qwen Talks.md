---

# پاسخ نهایی — Release 1.4.29 / Plugin 1.3.4

تاریخ: ۱۵ اوت ۲۰۲۶  
Theme: **1.4.29**  
Site Plugin: **Alipasandi Service Content 1.3.4**  
Design System: **Locked** — هیچ تغییر Design / Color / Layout / Grid / Typography / Hero / URL اعمال نشده است.  
Production Approval: **NO** — این Release فقط Code-level contractها را می‌بندد؛ Runtime/Staging/Owner Evidence همچنان لازم است.  
Artifact hashes: **PENDING DETERMINISTIC BUILD** — هش جدید نباید ساخته‌شده فرض شود؛ فقط پس از Build واقعی ثبت شود.

---

## جمع‌بندی اجرایی

تمام موارد Code-level 1 تا 6 که کلاینت در Review مستقیم 1.4.28/1.3.3 پیدا کرد، در این Release بسته شدند:

1. **Homepage NAP hard-coded fallback حذف شد.**  
   `front-page.php` دیگر fallback سخت مثل «ستارخان، امیراد ۱، طبقه ۵» ندارد. Home فقط SSOT Plugin را می‌خواند. اگر داده خالی بود، UI غیرعملیاتی/fail-safe نمایش داده می‌شود، نه مقدار قالب.

2. **Operational Health اکنون `clinic_street` را Critical validate می‌کند.**  
   `street_missing` اضافه شد. علاوه بر آن، یک Local NAP readiness contract مشترک بین Health و Rank Math integration ایجاد شد.

3. **Partial/invalid Local PostalAddress دیگر silently emit نمی‌شود.**  
   اگر Required Local NAP کامل نباشد، Rank Math integration کل local entity را fail-safe از JSON-LD حذف می‌کند؛ یعنی Schema نیمه‌کاره با `streetAddress=""` تولید نمی‌شود.

4. **Booking Slots ↔ Opening Hours cross-validation اضافه شد.**  
   اگر هیچ configured booking slotی با هیچ open range هفتگی overlap نداشته باشد، appointment form fail closed می‌شود و Health آن را Critical گزارش می‌کند.  
   open day بدون slot به‌صورت Warning گزارش می‌شود.

5. **Phone route contract انتخاب و اعمال شد: Contract A.**  
   وجود direct phone route معتبر اکنون جزو Form readiness است. علاوه بر ذلك، copy خطاها هم phone-aware شد: UI دیگر در نبود شماره نمی‌گوید «تماس بگیرید».

6. **NAP readiness helper مشترک اضافه شد.**  
   `alipasandi_local_entity_required_fields()`، `alipasandi_local_entity_missing_fields()`، `alipasandi_local_entity_schema_ready()` و `alipasandi_nap_consistency_values()` اضافه شدند تا Health، Schema، NAP consistency و runbookها از یک contract استفاده کنند.

علاوه بر این‌ها، موارد 7 تا 11 نیز به‌صورت source-level بسته شدند:

7. برای Rank Math Health دیگر نیازی به traffic تصادفی نیست.  
   یک explicit probe اضافه شد:  
   `wp alipasandi service rank-math-probe`

8. Rank Math observation اکنون per-context است: `front_page` و `contact` جداگانه ذخیره و بررسی می‌شوند.

9. Schema Repair حالا علاوه بر payload SHA، **human-readable snapshot** شامل H1، Intro، FAQ، CTA، Image ID/ALT و links دارد.

10. Schema Repair Apply فقط وقتی `WP_ENVIRONMENT_TYPE=staging` صریح باشد مجاز است.  
   صرفاً production نبودن کافی نیست.

11. Booking/Open-hours cross contract در Health و Form readiness و UI policy منعکس شده است.

---

# روش تحلیل — Tree of Thought / Circular Reasoning

برای جلوگیری از اصلاح سطحی، سه hypothesis اصلی بررسی شد:

## Hypothesis A — فقط fallback را حذف کنیم
- مزیت: کوچک‌ترین تغییر Theme.
- عیب: اگر `clinic_street` خالی بماند، Health ممکن است address را pass بداند ولی Schema `streetAddress=""` داشته باشد.
- نتیجه: **رد شد**، چون NAP drift بین Home، Contact، Footer، Mail و Schema همچنان ممکن بود.

## Hypothesis B — Health را اضافه کنیم ولی Schema را نیمه‌کاره نگه داریم
- مزیت: تغییر کمتر در Rank Math integration.
- عیب: LocalBusiness/Dentist با PostalAddress ناقص می‌تواند از نظر ساختاری emit شود و دقیقاً همان چیزی است که کلاینت خواست جلوی آن گرفته شود.
- نتیجه: **رد شد**، چون fail-safe واقعی نبود.

## Hypothesis C — Shared NAP readiness contract + fail-closed Schema + cross-validation + phone readiness
- مزیت:
  - Health و Schema از یک تعریف مشترک استفاده می‌کنند.
  - اگر NAP ناقص بود، Schema local entity نیمه‌کاره emit نمی‌شود.
  - Booking slots و opening hours به‌صورت متقابل validate می‌شوند.
  - Form readiness فقط mail-ready نیست؛ direct fallback phone route هم باید معتبر باشد.
  - Rank Math observation deterministic probe دارد و به traffic وابسته نیست.
- عیب:
  - تعداد contractها بیشتر می‌شود.
  - اگر NAP ناقص باشد، local schema اصلاً حذف می‌شود.
- نتیجه: **پذیرفته شد**، چون دقیقاً مانع silent drift و half-schema می‌شود.

Circular validation نیز انجام شد:

- از **Schema** شروع شد: آیا PostalAddress ناقص emit می‌شود؟
- به **Health** برگشتیم: آیا Health قبل از Schema همان نقص را Critical می‌بیند؟
- به **UI** برگشتیم: آیا Home/Contact/Appointment در نبود داده، مسیر غلط یا fake نشان می‌دهند؟
- به **Form readiness** برگشتیم: آیا فرم در حالت unusable یا بدون fallback فعال می‌ماند؟
- به **Runtime probe** برگشتیم: آیا Health بدون traffic قابل اجرا است؟

خروجی این حلقه‌ها همین Release 1.4.29 / Plugin 1.3.4 است.

---

# پاسخ تفصیلی به موارد ۱ تا ۲۱ کلاینت

## 1) Homepage NAP hard-coded fallback حذف شود — CLOSED

در `front-page.php` fallback زیر حذف شد:

```php
?: 'ستارخان، امیراد ۱، طبقه ۵'
```

همچنین برای جلوگیری از drift مشابه، theme دیگر operational default داخلی برای شهر/استان/کشور نیز نگه نمی‌دارد.

رفتار جدید:

- اگر `clinic_city` خالی باشد → مقدار نمایشی از SSOT گرفته نمی‌شود؛ UI فقط عنوان عمومی «مطب» را نشان می‌دهد.
- اگر `clinic_street` خالی باشد → آدرس جعلی یا template value نمایش داده نمی‌شود؛ یک پیام غیرعملیاتی واضح نمایش داده می‌شود:
  - «آدرس رسمی در تنظیمات کلینیک ثبت نشده است.»

این پیام NAP جعلی نیست؛ فقط وضعیت config missing را نشان می‌دهد.

---

## 2) Operational Health باید `clinic_street` را Critical validate کند — CLOSED

به Health اضافه شد:

```php
'street_missing'
```

همچنین Local Schema readiness با helper مشترک بررسی می‌شود:

```php
alipasandi_local_entity_missing_fields()
alipasandi_local_entity_schema_ready()
```

Health اکنون شامل:

```php
'local_entity' => array(
    'schema_ready' => ...,
    'missing'      => ...,
)
```

است.

---

## 3) Partial/invalid Local PostalAddress نباید silently emit شود — CLOSED

Policy جدید:

- Required Local NAP کامل است → validated PostalAddress emit می‌شود.
- Required Local NAP ناقص است → local entity از JSON-LD حذف می‌شود.
- Health همچنان Critical می‌شود.

یعنی Rank Math integration دیگر این حالت را نمی‌سازد:

```json
"address": {
  "@type": "PostalAddress",
  "streetAddress": "",
  "addressLocality": "نوشهر"
}
```

Required local fields:

```text
clinic_business_name
clinic_street
clinic_city
clinic_region
clinic_country
```

اگر هر کدام خالی یا نامعتبر باشد، local entity fail-safe حذف می‌شود.

---

## 4) Booking Slots ↔ Opening Hours cross-validation — CLOSED

Helper جدید:

```php
alipasandi_booking_schedule_cross_report_from_inputs( $slots_raw, $hours_raw )
alipasandi_booking_schedule_cross_report()
```

Health جدید:

- اگر هیچ slotی داخل هیچ open range نباشد:

```text
booking_slots_no_overlap_with_opening_hours
```

- اگر روز open وجود داشته باشد ولی هیچ slotی داخل آن نباشد:

```text
open_day_without_booking_slot:{DAY}
```

مثال:

```text
Booking slots:
20:00

Opening hours:
MO=09:00-17:00
```

نتیجه:

- `booking_slots_no_overlap_with_opening_hours` = Critical
- appointment form ready نیست.

---

## 5) Form fail-safe و Phone route — CLOSED

Contract انتخاب‌شده: **A**

> valid operational Phone باید جزو Form readiness باشد.

تابع جدید:

```php
alipasandi_direct_phone_route_ready()
```

این تابع وقتی true است که:

- `clinic_phone` خالی نباشد.
- `alipasandi_phone_href()` خالی نباشد.
- مقدار href حداقل validator `alipasandi_valid_patient_phone()` را pass کند.

و در `alipasandi_form_channel_ready()` اضافه شد:

```php
if ( ! alipasandi_direct_phone_route_ready() ) {
    return false;
}
```

بنابراین contact/appointment فقط وقتی interactive هستند که:

- mail config ready باشد؛
- direct phone route ready باشد؛
- برای appointment: registry، slots، opening hours و cross-contract ready باشند.

همچنین templateهای Theme نیز phone-aware شدند تا حتی در edge caseها UI نگه نداشته باشد که «تماس بگیرید» ولی شماره‌ای نباشد.

---

## 6) NAP readiness helper مشترک — CLOSED

Helperهای مشترک:

```php
alipasandi_local_entity_required_fields()
alipasandi_local_entity_missing_fields()
alipasandi_local_entity_schema_ready()
alipasandi_nap_consistency_values()
```

استفاده‌کنندگان:

- Operational Health
- Rank Math JSON-LD integration
- Rank Math probe
- NAP consistency CLI report
- Maintenance/Freeze runbook

---

## 7) Rank Math Health freshness و deployment determinism — CLOSED

قبلاً observation فقط با render واقعی Front/Contact refresh می‌شد. حالا:

### Probe جدید

```bash
wp alipasandi service rank-math-probe
```

این probe:

- context `front_page` را شبیه‌سازی می‌کند.
- context `contact` را شبیه‌سازی می‌کند.
- observation را با exact Rank Math version ثبت می‌کند.
- schema readiness را هم ثبت می‌کند.
- بدون وابستگی به traffic است.

### Health runbook جدید

قبل از:

```bash
wp alipasandi service health
```

حتماً:

```bash
wp alipasandi service rank-math-probe
wp alipasandi service nap-consistency
wp alipasandi service health
```

اجرا شود.

---

## 8) Rank Math Graph runtime — CLOSED

Observation جدید به‌صورت per-context ذخیره می‌شود:

```php
alipasandi_rank_math_local_entity_observations
```

هر observation شامل:

```php
state
observed_at
rank_math_version
context
schema_ready
missing
```

Health هر دو context را بررسی می‌کند:

```text
front_page
contact
```

اگر هر کدام stale/missing/version mismatch/target missing/schema incomplete باشد، Health Critical می‌شود.

---

## 9) Schema Repair Human-readable Diff — CLOSED

به Schema Repair اضافه شد:

```php
alipasandi_service_human_readable_snapshot()
```

Snapshot شامل:

- H1 = `title` + `title_gold`
- Intro
- FAQها
- CTA title/text
- Image ID
- Image ALT
- decorative flag
- extracted internal/external links از فیلدهای متنی

این snapshot:

- در dry-run plan می‌آید.
- در apply نتیجه نیز ظاهر می‌شود.
- قبل و بعد از write مقایسه می‌شود.
- اگر human-readable drift رخ دهد، apply fail و rollback best-effort انجام می‌شود.

---

## 10) Schema Repair environment — CLOSED

قبلاً فقط production block بود. حالا:

```php
if ( 'staging' !== $environment ) { ... }
```

یعنی:

- production → blocked
- development → blocked
- local → blocked
- هر چیز غیر از `staging` → blocked

Apply فقط روی isolated clone با:

```php
WP_ENVIRONMENT_TYPE=staging
```

مجاز است.

---

## 11) Opening Hours ↔ Booking UX — CLOSED

Cross-validator جدید:

- در Health دیده می‌شود.
- در Form readiness اعمال می‌شود.
- با date-aware UI فعلی هم‌راستا است.

اگر هیچ date/slot قابل استفاده‌ای وجود نداشته باشد، UI دیگر form ready نشان نمی‌دهد.

---

## 12) Owner-approved NAP Freeze — DOCUMENTED

در Maintenance Guide باید صریح شود:

### تفاوت دو فیلد

| Option | نقش |
|---|---|
| `clinic_address` | آدرس نمایشی یک‌خطی برای UI: Footer، Contact، Hero، Mail |
| `clinic_street` | مقدار رسمی `streetAddress` برای Schema/PostalAddress |

در هر تغییر مکان، **هر دو باید همزمان update شوند**. فقط تغییر `clinic_address` کافی نیست؛ فقط تغییر `clinic_street` هم کافی نیست.

### Freeze fields

قبل از هر Runtime/Form/Rank Math evidence باید freeze شوند:

```text
clinic_business_name
clinic_phone
clinic_phone_e164
clinic_address
clinic_street
clinic_city
clinic_region
clinic_country
clinic_postal_code
clinic_geo_lat
clinic_geo_lng
clinic_maps
clinic_email
clinic_notify_email
clinic_mail_from
clinic_mail_from_name
clinic_booking_times
clinic_opening_hours
clinic_instagram
clinic_whatsapp
clinic_telegram
```

---

## 13) Home/Contact/Footer NAP consistency test — PARTIALLY TOOLING + RUNBOOK

یک CLI report جدید اضافه شد:

```bash
wp alipasandi service nap-consistency
```

خروجی شامل canonical values:

- business name
- display phone
- phone href
- address
- street
- city
- region
- country
- postal
- geo
- map
- emails
- booking times
- opening hours
- socials
- home_url

سپس باید rendered diff دستی/automation روی این سطوح انجام شود:

- Home
- Contact
- Footer
- Appointment
- Mail body
- Rank Math JSON-LD

هر mismatch = FAIL.

---

## 14) Production environment blockers — UNCHANGED / STILL HARD GATES

این موارد همچنان Code Review PASS نمی‌گیرند:

- PHP 8.2 real runtime
- WordPress 6.4 real runtime
- Production-identical WP 7.0.4 / PHP 8.5.7 smoke
- actual WP-CLI
- PHPUnit
- WPCS/PHPCS
- PHPStan/deprecated scan
- Rank Math active/inactive/reactivated
- SMTP/SPF/DKIM/DMARC/inbox
- Production Debug hardening
- Loopback 503 / WP-Cron
- Backup/Restore
- Host redirects/canonical/indexability
- Security headers
- Cache/post_modified/lastmod
- CWV/browser/A11y/Admin RTL
- Privacy/Claims/Asset approvals

---

## 15) Production WP_DEBUG — STILL BLOCKER

Production snapshot قبلی هنوز:

```text
WP_DEBUG=true
WP_DEBUG_DISPLAY=true
WP_DEBUG_LOG=true
```

داشته است.

قبل Freeze:

```php
define( 'WP_DEBUG_DISPLAY', false );
```

اجباری است.

اگر `WP_DEBUG_LOG` حفظ می‌شود:

- log path خصوصی باشد
- web accessible نباشد
- retention/rotation داشته باشد
- PII/secret review شود

---

## 16) Production schema-invalid Services — STILL STAGING-ONLY

چهار Service Production snapshot قبلی هنوز نیازمند Staging-only repair evidence است.

Apply روی Live همچنان ممنوع است.

---

## 17) Rank Math — MUST BE ACTIVE AS FINAL OWNER

Current deactivated state برای Production Final قابل قبول نیست.

Rank Math exact version باید:

- فعال شود
- freeze شود
- probe/health را pass کند
- active/inactive/reactivated regression روی آن اجرا شود

---

## 18) Loopback/WP-Cron — HOSTING BLOCKER

HTTP 503 loopback باید قبل Freeze توسط Hosting حل شود.

---

## 19) Horizon/Lead — OWNER APPROVAL REQUIRED

Current candidate:

```text
Horizon = 365 days
Lead time = 0 minutes, selected datetime strictly future
```

تا Owner approval کتبی، Production freeze نمی‌شود.

---

## 20) Claims — MEDICAL CLAIM REGISTER REQUIRED

این عبارات و هر Outcome/Comparative claim مشابه باید وارد Medical Claim Register نهایی شوند:

- «ایمپلنت تخصصی»
- «برندهای معتبر جهانی»
- «جراحی تخصصی»
- «بدون تراش دندان‌های مجاور»
- هر claim مربوط به نتیجه، مقایسه، ماندگاری، سرعت، درد، موفقیت یا تخصص

`+10,000` تا approval نهایی همچنان **OFF** می‌ماند.

---

## 21) Build decision — NEW RELEASE REQUIRED

نسخه پیشنهادی کلاینت پذیرفته شد:

```text
Theme 1.4.29
Plugin 1.3.4
```

دلیل:

- تغییرات Theme: fallback NAP + phone-aware error copy
- تغییرات Plugin: Health/Schema/NAP/booking/phone/probe contracts
- هیچ Design/URL/Layout تغییری وجود ندارد

---

# وضعیت نهایی Contracts

| Area | Status |
|---|---|
| Artifact Integrity | PENDING BUILD |
| Previous WP-CLI Blocker | FIXED / CARRIED FROM 1.3.3 |
| Schema Repair Safety | PASS SOURCE + HUMAN READABLE DIFF |
| Booking Date/Time | PASS SOURCE |
| Stable Service Key | PASS SOURCE |
| Emergency SEO Fallback | PASS SOURCE |
| NAP Single Source | CLOSED SOURCE |
| Local Schema Health Contract | PATCHED |
| Booking/Open-hours Cross Contract | PATCHED |
| Phone Route Fail-safe | PATCHED |
| Rank Math Deterministic Probe | PATCHED |
| Runtime | NOT PROVEN |
| Production Approval | NO |

---

# تغییرات Source — Theme 1.4.29

در این بخش دقیقاً تغییرات Theme آمده است. فایل‌های بدون تغییر از 1.4.28 باقی می‌مانند.

---

## 1. `style.css`

Replace header version:

```css
/*
Theme Name: Dr Keyvan Alipasandi Dental Clinic
Theme URI: https://fasdent.ir/
Author: Clinic Web Team
Description: A native, responsive RTL WordPress theme for Dr Keyvan Alipasandi Dental Clinic. No React or build step required.
Version: 1.4.29
Requires at least: 6.4
Requires PHP: 8.2
Text Domain: alipasandi-clinic
Tags: rtl-language-support, custom-menu, featured-images, blog
*/
```

---

## 2. `readme.txt`

Replace stable tag and add changelog:

```text
Stable tag: 1.4.29
```

Add:

```text
= 1.4.29 =
* Pair-contract release for Plugin 1.3.4 after NAP/Local schema/booking cross-contract patches; no Design/URL/Layout change.
* Removed hardcoded homepage street fallback and theme operational defaults.
* Form unavailable/error copy no longer instructs direct phone unless a valid phone route exists.
* Theme minimum companion plugin raised to 1.3.4.
```

---

## 3. `functions.php`

Replace constants:

```php
define( 'ALIPASANDI_THEME_VERSION', '1.4.29' );
define( 'ALIPASANDI_SERVICE_PLUGIN_MIN_VERSION', '1.3.4' );
```

Inside emergency fallback `alipasandi_clinic_option()` remove operational defaults:

```php
$minimal = array();
```

Complete replacement of that fallback section:

```php
if ( ! function_exists( 'alipasandi_clinic_option' ) ) {
	function alipasandi_clinic_option( $key ) {
		$key = sanitize_key( $key );

		if ( 'clinic_website' === $key ) {
			return home_url( '/' );
		}

		$value = get_option( 'alipasandi_' . $key, null );

		if ( null !== $value && '' !== $value ) {
			return $value;
		}

		// Presentation theme must not invent operational NAP defaults.
		$minimal = array();

		return isset( $minimal[ $key ] ) ? $minimal[ $key ] : '';
	}
}
```

---

## 4. `front-page.php`

Remove hardcoded street/city fallbacks.

Add before hero info grid:

```php
<?php
$alipasandi_home_city   = trim( (string) alipasandi_clinic_option( 'clinic_city' ) );
$alipasandi_home_street = trim( (string) alipasandi_clinic_option( 'clinic_street' ) );
?>
```

Replace the location hero info item with:

```php
<div class="hero-info-item">
	<?php echo alipasandi_icon( 'location', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span>
		<strong><?php echo $alipasandi_home_city ? 'مطب ' . esc_html( $alipasandi_home_city ) : 'مطب'; ?></strong>
		<small>
			<?php
			if ( $alipasandi_home_street ) {
				echo esc_html( $alipasandi_home_street );
			} else {
				echo 'آدرس رسمی در تنظیمات کلینیک ثبت نشده است.';
			}
			?>
		</small>
	</span>
</div>
```

---

## 5. `page-appointments.php`

Add near top after `$ui_policy`:

```php
$phone_href = alipasandi_phone_href();
```

Replace unavailable/configuration message:

```php
<?php elseif ( in_array( $status, array( 'configuration_error', 'unavailable' ), true ) ) : ?>
<div class="form-message error" role="alert">
	سامانه ثبت درخواست نوبت موقتاً در دسترس نیست.
	<?php if ( $phone_href ) : ?>
		لطفاً مستقیماً با کلینیک تماس بگیرید.
	<?php else : ?>
		لطفاً بعداً دوباره تلاش کنید.
	<?php endif; ?>
</div>
<?php endif; ?>
```

Replace fallback disabled booking shell:

```php
<?php else : ?>
<div class="booking-shell">
	<div class="form-message error" role="status" aria-live="polite" aria-atomic="true">
		<strong>سامانه ثبت درخواست نوبت موقتاً در دسترس نیست.</strong><br>
		<?php if ( $phone_href ) : ?>
			برای هماهنگی، لطفاً مستقیماً با کلینیک تماس بگیرید.
		<?php else : ?>
			لطفاً بعداً دوباره تلاش کنید.
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
```

Also make rate/mail messages phone-aware. Example for rate limited:

```php
<?php elseif ( 'rate_limited' === $status ) : ?>
<div class="form-message error" role="alert" tabindex="-1" autofocus>
	تعداد درخواست‌های پذیرفته‌شده بیش از حد مجاز است.
	<?php if ( $phone_href ) : ?>
		لطفاً ۱۵ دقیقه دیگر تلاش کنید یا مستقیماً با کلینیک تماس بگیرید.
	<?php else : ?>
		لطفاً ۱۵ دقیقه دیگر تلاش کنید.
	<?php endif; ?>
</div>
<?php endif; ?>
```

Example for mail error:

```php
<?php elseif ( 'mail_error' === $status ) : ?>
<div class="form-message error" role="alert">
	سامانه ارسال پیام پاسخ نداد و درخواست شما تأیید نشده است.
	<?php if ( $phone_href ) : ?>
		لطفاً دوباره تلاش کنید یا مستقیماً با کلینیک تماس بگیرید.
	<?php else : ?>
		لطفاً دوباره تلاش کنید.
	<?php endif; ?>
</div>
<?php endif; ?>
```

---

## 6. `page-contact.php`

Replace unavailable/configuration message:

```php
<?php elseif ( in_array( $status, array( 'configuration_error', 'unavailable' ), true ) ) : ?>
<div class="form-message error" role="alert">
	فرم تماس موقتاً در دسترس نیست.
	<?php if ( alipasandi_phone_href() ) : ?>
		لطفاً از شماره رسمی کلینیک استفاده کنید.
	<?php else : ?>
		لطفاً بعداً دوباره تلاش کنید.
	<?php endif; ?>
</div>
<?php endif; ?>
```

Replace disabled form block:

```php
<?php else : ?>
<div class="form-message error" role="status" aria-live="polite" aria-atomic="true">
	<strong>فرم تماس موقتاً در دسترس نیست.</strong><br>
	<?php if ( alipasandi_phone_href() ) : ?>
		لطفاً از شماره تماس رسمی کلینیک استفاده کنید.
		<a href="tel:<?php echo esc_attr( alipasandi_phone_href() ); ?>">
			<span dir="ltr"><?php echo esc_html( alipasandi_clinic_option( 'clinic_phone' ) ); ?></span>
		</a>
	<?php else : ?>
		لطفاً بعداً دوباره تلاش کنید.
	<?php endif; ?>
</div>
<?php endif; ?>
```

---

# تغییرات Source — Plugin 1.3.4

---

## 1. `alipasandi-service-content.php`

Update plugin header and constants:

```php
/**
 * Plugin Name: Alipasandi Service Content
 * Description: Portable service content, NAP, form processing, migration, revisions, export and operational health checks.
 * Version: 1.3.4
 * Requires at least: 6.4
 * Requires PHP: 8.2
 * Author: Alipasandi Clinic
 * Text Domain: alipasandi-service-content
 * Domain Path: /languages
 */

define( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION', '1.3.4' );
```

---

## 2. Plugin `readme.txt`

```text
Stable tag: 1.3.4
```

Add changelog:

```text
= 1.3.4 =
* Shared local NAP readiness contract; Rank Math local entity fails closed when required NAP is incomplete.
* street_missing and local schema readiness added to operational Health.
* Booking slots/opening hours cross-validation with fail-closed appointment readiness and Health critical/warnings.
* Valid direct phone route is required for interactive forms; UI fallback copy is phone-aware.
* Rank Math observation is per-context and explicit CLI probe added.
* Schema repair dry-run/apply includes human-readable snapshots and requires explicit staging environment.
* Added NAP consistency CLI report.
```

---

## 3. `includes/compatibility.php`

Replace production theme minimum:

```php
define( 'ALIPASANDI_SERVICE_PRODUCTION_THEME_MIN', '1.4.29' );
```

Update notice text where it mentions production pairing:

```php
'Alipasandi Service Content 1.3.4 requires Alipasandi Clinic theme 1.4.24 or newer to load safely. Production pairing requires theme 1.4.29.'
```

---

## 4. `includes/site-settings.php`

Add these functions after `alipasandi_phone_href()`:

```php
function alipasandi_direct_phone_route_ready() {
	$phone = trim( (string) alipasandi_clinic_option( 'clinic_phone' ) );
	$href  = trim( (string) alipasandi_phone_href() );

	if ( '' === $phone || '' === $href ) {
		return false;
	}

	return function_exists( 'alipasandi_valid_patient_phone' ) && alipasandi_valid_patient_phone( $href );
}

function alipasandi_local_entity_required_fields() {
	return array(
		'clinic_business_name',
		'clinic_street',
		'clinic_city',
		'clinic_region',
		'clinic_country',
	);
}

function alipasandi_local_entity_missing_fields() {
	$missing = array();

	foreach ( alipasandi_local_entity_required_fields() as $field ) {
		$value = trim( (string) alipasandi_clinic_option( $field ) );

		if ( '' === $value ) {
			$missing[] = $field;
		}
	}

	$country = strtoupper( trim( (string) alipasandi_clinic_option( 'clinic_country' ) ) );

	if ( '' !== $country && function_exists( 'alipasandi_valid_country_code' ) && ! alipasandi_valid_country_code( $country ) ) {
		$missing[] = 'clinic_country_invalid';
	}

	return array_values( array_unique( $missing ) );
}

function alipasandi_local_entity_schema_ready() {
	return empty( alipasandi_local_entity_missing_fields() );
}

function alipasandi_nap_consistency_values() {
	return array(
		'home_url'            => home_url( '/' ),
		'business_name'       => alipasandi_clinic_option( 'clinic_business_name' ),
		'display_phone'       => alipasandi_clinic_option( 'clinic_phone' ),
		'phone_href'          => alipasandi_phone_href(),
		'phone_e164'          => alipasandi_clinic_option( 'clinic_phone_e164' ),
		'display_address'     => alipasandi_clinic_option( 'clinic_address' ),
		'street'              => alipasandi_clinic_option( 'clinic_street' ),
		'city'                => alipasandi_clinic_option( 'clinic_city' ),
		'region'              => alipasandi_clinic_option( 'clinic_region' ),
		'country'             => alipasandi_clinic_option( 'clinic_country' ),
		'postal_code'         => alipasandi_clinic_option( 'clinic_postal_code' ),
		'geo_lat'             => alipasandi_clinic_option( 'clinic_geo_lat' ),
		'geo_lng'             => alipasandi_clinic_option( 'clinic_geo_lng' ),
		'maps'                => alipasandi_clinic_option( 'clinic_maps' ),
		'email'               => alipasandi_clinic_option( 'clinic_email' ),
		'notify_email'        => alipasandi_clinic_option( 'clinic_notify_email' ),
		'mail_from'           => alipasandi_clinic_option( 'clinic_mail_from' ),
		'mail_from_name'      => alipasandi_clinic_option( 'clinic_mail_from_name' ),
		'booking_times'       => alipasandi_clinic_option( 'clinic_booking_times' ),
		'opening_hours'       => alipasandi_clinic_option( 'clinic_opening_hours' ),
		'instagram'           => alipasandi_clinic_option( 'clinic_instagram' ),
		'whatsapp'            => alipasandi_clinic_option( 'clinic_whatsapp' ),
		'telegram'            => alipasandi_clinic_option( 'clinic_telegram' ),
		'direct_phone_ready'  => alipasandi_direct_phone_route_ready(),
		'local_schema_ready'  => alipasandi_local_entity_schema_ready(),
		'local_missing'       => alipasandi_local_entity_missing_fields(),
	);
}

function alipasandi_rank_math_observation_context() {
	if ( isset( $GLOBALS['alipasandi_rank_math_observation_context'] ) && is_string( $GLOBALS['alipasandi_rank_math_observation_context'] ) && '' !== $GLOBALS['alipasandi_rank_math_observation_context'] ) {
		return sanitize_key( $GLOBALS['alipasandi_rank_math_observation_context'] );
	}

	if ( function_exists( 'is_front_page' ) && is_front_page() ) {
		return 'front_page';
	}

	if ( function_exists( 'is_page' ) && is_page( 'contact' ) ) {
		return 'contact';
	}

	return '';
}

function alipasandi_rank_math_record_local_entity_observation( $matched, $schema_ready, $missing ) {
	if ( ! defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}

	$context = alipasandi_rank_math_observation_context();

	if ( '' === $context ) {
		return;
	}

	$key          = 'alipasandi_rank_math_local_entity_observations';
	$observations = get_transient( $key );

	if ( ! is_array( $observations ) ) {
		$observations = array();
	}

	$observations[ $context ] = array(
		'state'             => $matched ? 'matched' : 'missing',
		'observed_at'       => time(),
		'rank_math_version' => (string) RANK_MATH_VERSION,
		'context'           => $context,
		'schema_ready'      => (bool) $schema_ready,
		'missing'           => (array) $missing,
	);

	set_transient( $key, $observations, 30 * MINUTE_IN_SECONDS );
}

function alipasandi_rank_math_local_entity_probe() {
	if ( ! defined( 'RANK_MATH_VERSION' ) ) {
		return array(
			'pass'       => false,
			'reason'     => 'rank_math_inactive',
			'issues'     => array( 'rank_math_inactive' ),
			'observations' => array(),
		);
	}

	$contexts = array( 'front_page', 'contact' );

	foreach ( $contexts as $context ) {
		$GLOBALS['alipasandi_rank_math_observation_context'] = $context;

		$graph = array(
			array(
				'@type' => 'Dentist',
				'@id'   => home_url( '/#clinic' ),
			),
			array(
				'@type' => 'WebPage',
				'@id'   => home_url( '/#webpage' ),
			),
		);

		apply_filters( 'rank_math/json_ld', $graph );

		unset( $GLOBALS['alipasandi_rank_math_observation_context'] );
	}

	$observations = get_transient( 'alipasandi_rank_math_local_entity_observations' );
	$issues       = array();
	$pass         = true;

	foreach ( $contexts as $context ) {
		$obs = is_array( $observations ) && isset( $observations[ $context ] ) ? $observations[ $context ] : null;

		if ( ! is_array( $obs ) ) {
			$pass     = false;
			$issues[] = 'observation_missing:' . $context;
			continue;
		}

		if ( (string) ( $obs['rank_math_version'] ?? '' ) !== (string) RANK_MATH_VERSION ) {
			$pass     = false;
			$issues[] = 'observation_version_mismatch:' . $context;
		}

		if ( empty( $obs['observed_at'] ) || ( time() - (int) $obs['observed_at'] ) > 15 * MINUTE_IN_SECONDS ) {
			$pass     = false;
			$issues[] = 'observation_stale:' . $context;
		}

		if ( 'matched' !== ( $obs['state'] ?? '' ) ) {
			$pass     = false;
			$issues[] = 'local_entity_missing:' . $context;
		}

		if ( empty( $obs['schema_ready'] ) ) {
			$pass     = false;
			$issues[] = 'local_nap_incomplete:' . $context;
		}
	}

	return array(
		'pass'              => $pass,
		'issues'            => $issues,
		'rank_math_version' => (string) RANK_MATH_VERSION,
		'observations'      => is_array( $observations ) ? $observations : array(),
	);
}
```

Replace `alipasandi_rank_math_central_nap()` with:

```php
function alipasandi_rank_math_central_nap( $data ) {
	if ( ! defined( 'RANK_MATH_VERSION' ) || ! is_array( $data ) ) {
		return $data;
	}

	$missing              = alipasandi_local_entity_missing_fields();
	$schema_ready         = empty( $missing );
	$matched_local_entity = false;

	foreach ( $data as $index => $entity ) {
		$types = is_array( $entity ) && isset( $entity['@type'] ) ? (array) $entity['@type'] : array();

		if ( ! array_intersect( array( 'Dentist', 'LocalBusiness' ), $types ) ) {
			continue;
		}

		// Required NAP incomplete: do not emit a partial local entity.
		if ( ! $schema_ready ) {
			$data[ $index ] = null;
			continue;
		}

		$matched_local_entity = true;

		$business = trim( (string) alipasandi_clinic_option( 'clinic_business_name' ) );

		if ( '' !== $business ) {
			$data[ $index ]['name'] = $business;
		} else {
			unset( $data[ $index ]['name'] );
		}

		$data[ $index ]['url'] = home_url( '/' );

		$phone = trim( (string) alipasandi_clinic_option( 'clinic_phone_e164' ) );

		if ( alipasandi_valid_e164( $phone ) ) {
			$data[ $index ]['telephone'] = $phone;
		} else {
			unset( $data[ $index ]['telephone'] );
		}

		$map = trim( (string) alipasandi_clinic_option( 'clinic_maps' ) );

		if ( $map ) {
			$data[ $index ]['hasMap'] = $map;
		} else {
			unset( $data[ $index ]['hasMap'] );
		}

		$data[ $index ]['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => alipasandi_clinic_option( 'clinic_street' ),
			'addressLocality' => alipasandi_clinic_option( 'clinic_city' ),
			'addressRegion'   => alipasandi_clinic_option( 'clinic_region' ),
			'addressCountry'  => strtoupper( alipasandi_clinic_option( 'clinic_country' ) ),
		);

		$postal = trim( (string) alipasandi_clinic_option( 'clinic_postal_code' ) );

		if ( $postal ) {
			$data[ $index ]['address']['postalCode'] = $postal;
		}

		$lat = trim( (string) alipasandi_clinic_option( 'clinic_geo_lat' ) );
		$lng = trim( (string) alipasandi_clinic_option( 'clinic_geo_lng' ) );

		if ( alipasandi_valid_geo( $lat, 'lat' ) && alipasandi_valid_geo( $lng, 'lng' ) ) {
			$data[ $index ]['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $lat,
				'longitude' => (float) $lng,
			);
		} else {
			unset( $data[ $index ]['geo'] );
		}

		$hours = alipasandi_parse_opening_hours( alipasandi_clinic_option( 'clinic_opening_hours' ) );

		if ( empty( $hours['errors'] ) && ! empty( $hours['specs'] ) ) {
			$data[ $index ]['openingHoursSpecification'] = $hours['specs'];
		} else {
			unset( $data[ $index ]['openingHoursSpecification'] );
		}
	}

	$data = array_values(
		array_filter(
			$data,
			function ( $entity ) {
				return null !== $entity;
			}
		)
	);

	alipasandi_rank_math_record_local_entity_observation( $matched_local_entity, $schema_ready, $missing );

	return $data;
}
add_filter( 'rank_math/json_ld', 'alipasandi_rank_math_central_nap', 90 );
```

---

## 5. `includes/validators.php`

Add these functions:

```php
function alipasandi_booking_schedule_cross_report_from_inputs( $slots_raw, $hours_raw ) {
	$report = array(
		'opening_hours_configured' => false,
		'opening_hours_valid'      => false,
		'booking_slots_configured' => false,
		'booking_slots_valid'      => false,
		'has_usable_slot'          => false,
		'usable_slots'             => array(),
		'unusable_slots'           => array(),
		'open_days_without_slot'   => array(),
		'errors'                   => array(),
	);

	$slots_raw = trim( (string) $slots_raw );
	$hours_raw = trim( (string) $hours_raw );

	$report['opening_hours_configured'] = '' !== $hours_raw;
	$report['booking_slots_configured'] = '' !== $slots_raw;

	$slots = array();

	if ( $report['booking_slots_configured'] ) {
		$valid_slots = alipasandi_validate_booking_slots( $slots_raw );

		if ( is_wp_error( $valid_slots ) ) {
			$report['errors'][] = $valid_slots->get_error_message();
		} else {
			$report['booking_slots_valid'] = true;
			$slots                         = preg_split( '/\r\n|\r|\n/', (string) $valid_slots );
			$slots                         = array_values( array_filter( array_map( 'trim', $slots ), 'strlen' ) );
		}
	}

	$parsed = array(
		'errors'   => array(),
		'schedule' => array(),
	);

	if ( $report['opening_hours_configured'] ) {
		$parsed = alipasandi_parse_opening_hours( $hours_raw );

		if ( empty( $parsed['errors'] ) ) {
			$report['opening_hours_valid'] = true;
		} else {
			$report['errors'] = array_merge( $report['errors'], $parsed['errors'] );
		}
	}

	if ( ! $report['booking_slots_valid'] ) {
		return $report;
	}

	if ( ! $report['opening_hours_configured'] || ! $report['opening_hours_valid'] ) {
		// Without an official schedule, valid proposal slots remain usable requests.
		$report['usable_slots']    = $slots;
		$report['has_usable_slot'] = ! empty( $slots );

		return $report;
	}

	foreach ( $slots as $slot ) {
		$slot        = trim( $slot );
		$slot_usable = false;

		foreach ( $parsed['schedule'] as $ranges ) {
			foreach ( $ranges as $range ) {
				if ( $slot >= $range[0] && $slot < $range[1] ) {
					$slot_usable = true;
					break 2;
				}
			}
		}

		if ( $slot_usable ) {
			$report['usable_slots'][] = $slot;
		} else {
			$report['unusable_slots'][] = $slot;
		}
	}

	$report['has_usable_slot'] = ! empty( $report['usable_slots'] );

	foreach ( $parsed['schedule'] as $day => $ranges ) {
		if ( empty( $ranges ) ) {
			continue;
		}

		$day_has_slot = false;

		foreach ( $slots as $slot ) {
			foreach ( $ranges as $range ) {
				if ( $slot >= $range[0] && $slot < $range[1] ) {
					$day_has_slot = true;
					break 2;
				}
			}
		}

		if ( ! $day_has_slot ) {
			$report['open_days_without_slot'][] = $day;
		}
	}

	return $report;
}

function alipasandi_booking_schedule_cross_report() {
	return alipasandi_booking_schedule_cross_report_from_inputs(
		alipasandi_clinic_option( 'clinic_booking_times' ),
		alipasandi_clinic_option( 'clinic_opening_hours' )
	);
}
```

---

## 6. `includes/forms.php`

Replace `alipasandi_form_channel_ready()` with:

```php
function alipasandi_form_channel_ready( $channel ) {
	if ( ! alipasandi_mail_configuration_ready() ) {
		return false;
	}

	if ( ! alipasandi_direct_phone_route_ready() ) {
		return false;
	}

	if ( 'appointment' === $channel ) {
		if ( function_exists( 'alipasandi_service_registry_issues' ) && alipasandi_service_registry_issues() ) {
			return false;
		}

		$hours_raw = (string) alipasandi_clinic_option( 'clinic_opening_hours' );

		if ( '' !== trim( $hours_raw ) ) {
			$hours = alipasandi_parse_opening_hours( $hours_raw );

			if ( ! empty( $hours['errors'] ) ) {
				return false;
			}
		}

		$cross = alipasandi_booking_schedule_cross_report();

		if ( ! empty( $cross['opening_hours_configured'] ) && ! empty( $cross['opening_hours_valid'] ) && ! empty( $cross['booking_slots_valid'] ) && empty( $cross['has_usable_slot'] ) ) {
			return false;
		}

		return ! empty( alipasandi_allowed_services() ) && ! empty( alipasandi_allowed_times() );
	}

	return 'contact' === $channel;
}
```

---

## 7. `includes/health-checks.php`

Add these checks inside `alipasandi_operational_health_report()`.

After existing `$address` / `$country` variables, add:

```php
$street = trim( (string) alipasandi_clinic_option( 'clinic_street' ) );
```

Then add:

```php
if ( '' === $street ) {
	$issues[] = 'street_missing';
}

if ( ! function_exists( 'alipasandi_direct_phone_route_ready' ) || ! alipasandi_direct_phone_route_ready() ) {
	$issues[] = 'phone_route_missing';
}

$local_missing     = function_exists( 'alipasandi_local_entity_missing_fields' ) ? alipasandi_local_entity_missing_fields() : array();
$local_schema_ready = function_exists( 'alipasandi_local_entity_schema_ready' ) ? alipasandi_local_entity_schema_ready() : false;

if ( ! $local_schema_ready ) {
	$issues[] = 'local_entity_schema_not_ready';
}

$cross_report = function_exists( 'alipasandi_booking_schedule_cross_report' ) ? alipasandi_booking_schedule_cross_report() : array();

if ( ! empty( $cross_report['opening_hours_configured'] ) && ! empty( $cross_report['opening_hours_valid'] ) && ! empty( $cross_report['booking_slots_valid'] ) ) {
	if ( empty( $cross_report['has_usable_slot'] ) ) {
		$issues[] = 'booking_slots_no_overlap_with_opening_hours';
	}

	foreach ( (array) ( $cross_report['open_days_without_slot'] ?? array() ) as $open_day ) {
		$warnings[] = 'open_day_without_booking_slot:' . $open_day;
	}
}
```

Replace old Rank Math transient check with per-context observation check:

```php
$rank_math_observations = get_transient( 'alipasandi_rank_math_local_entity_observations' );

if ( defined( 'RANK_MATH_VERSION' ) ) {
	$rank_freshness_window   = 15 * MINUTE_IN_SECONDS;
	$rank_version_mismatch   = false;
	$rank_stale_or_missing   = false;
	$rank_target_missing     = false;
	$rank_schema_incomplete  = false;

	foreach ( array( 'front_page', 'contact' ) as $rank_context ) {
		$obs = is_array( $rank_math_observations ) && isset( $rank_math_observations[ $rank_context ] ) ? $rank_math_observations[ $rank_context ] : null;

		if ( ! is_array( $obs ) ) {
			$rank_stale_or_missing = true;
			continue;
		}

		if ( (string) ( $obs['rank_math_version'] ?? '' ) !== (string) RANK_MATH_VERSION ) {
			$rank_version_mismatch = true;
			continue;
		}

		if ( empty( $obs['observed_at'] ) || ( time() - (int) $obs['observed_at'] ) > $rank_freshness_window ) {
			$rank_stale_or_missing = true;
			continue;
		}

		if ( 'matched' !== ( $obs['state'] ?? '' ) ) {
			$rank_target_missing = true;
		}

		if ( empty( $obs['schema_ready'] ) ) {
			$rank_schema_incomplete = true;
		}
	}

	if ( $rank_version_mismatch ) {
		$issues[] = 'rank_math_local_entity_observation_version_mismatch';
	}

	if ( $rank_stale_or_missing ) {
		$issues[] = 'rank_math_local_entity_observation_stale_or_missing';
	}

	if ( $rank_target_missing ) {
		$issues[] = 'rank_math_local_entity_target_missing';
	}

	if ( $rank_schema_incomplete ) {
		$issues[] = 'rank_math_local_entity_schema_incomplete';
	}
}
```

Add to returned array:

```php
'local_entity' => array(
	'schema_ready' => $local_schema_ready,
	'missing'      => $local_missing,
),
'booking_schedule_cross' => $cross_report,
'nap_consistency' => function_exists( 'alipasandi_nap_consistency_values' ) ? alipasandi_nap_consistency_values() : array(),
'rank_math_local_entity_state' => is_array( $rank_math_observations ) ? $rank_math_observations : array(),
```

---

## 8. `includes/service-content.php`

Add human-readable snapshot helpers:

```php
function alipasandi_service_collect_links_from_html( $html, array &$links ) {
	if ( ! is_string( $html ) || '' === trim( $html ) ) {
		return;
	}

	if ( preg_match_all( '/<a\s[^>]*href=["\']([^"\']+)["\']/i', $html, $matches ) ) {
		foreach ( $matches[1] as $url ) {
			$links[] = $url;
		}
	}
}

function alipasandi_service_human_readable_snapshot( $meta ) {
	if ( ! is_array( $meta ) ) {
		return null;
	}

	$snapshot = array(
		'h1'               => trim( ( (string) ( $meta['title'] ?? '' ) ) . ' ' . ( (string) ( $meta['title_gold'] ?? '' ) ) ),
		'title'            => (string) ( $meta['title'] ?? '' ),
		'title_gold'       => (string) ( $meta['title_gold'] ?? '' ),
		'intro'            => (string) ( $meta['intro'] ?? '' ),
		'faqs'             => array(),
		'cta_title'        => (string) ( $meta['cta_title'] ?? '' ),
		'cta_text'         => (string) ( $meta['cta_text'] ?? '' ),
		'image_id'         => (int) ( $meta['image_id'] ?? 0 ),
		'image_alt'        => (string) ( $meta['image_alt'] ?? '' ),
		'image_decorative' => ! empty( $meta['image_decorative'] ),
		'links'            => array(),
	);

	foreach ( (array) ( $meta['faqs'] ?? array() ) as $faq ) {
		if ( is_array( $faq ) ) {
			$snapshot['faqs'][] = array(
				'question' => (string) ( $faq[0] ?? '' ),
				'answer'   => (string) ( $faq[1] ?? '' ),
			);
		}
	}

	$links = array();

	$text_fields = array(
		'intro',
		'candidate_text',
		'notice_text',
		'cta_text',
	);

	foreach ( $text_fields as $field ) {
		alipasandi_service_collect_links_from_html( $meta[ $field ] ?? '', $links );
	}

	foreach ( (array) ( $meta['what_text'] ?? array() ) as $paragraph ) {
		alipasandi_service_collect_links_from_html( $paragraph, $links );
	}

	foreach ( (array) ( $meta['faqs'] ?? array() ) as $faq ) {
		if ( is_array( $faq ) && isset( $faq[1] ) ) {
			alipasandi_service_collect_links_from_html( $faq[1], $links );
		}
	}

	$links = array_values( array_unique( $links ) );
	sort( $links, SORT_STRING );

	$snapshot['links'] = $links;

	return $snapshot;
}
```

In `alipasandi_service_schema_repair_plan()` initialize:

```php
$entry = array(
	'page_id'                => $page ? (int) $page->ID : 0,
	'action'                 => 'blocked',
	'reason'                 => '',
	'payload_sha256'         => '',
	'human_readable_snapshot'=> null,
);
```

When `$meta` is an array, add:

```php
$entry['human_readable_snapshot'] = alipasandi_service_human_readable_snapshot( $meta );
```

Replace environment guard in `alipasandi_service_schema_repair_apply()` with:

```php
$environment = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';

if ( 'staging' !== $environment ) {
	$reason = 'production' === $environment
		? 'production_apply_forbidden_use_staging_clone_with_backup'
		: 'schema_repair_apply_requires_explicit_staging_environment';

	return array(
		'schema_version' => ALIPASANDI_SERVICE_META_SCHEMA,
		'write'          => false,
		'pass'           => false,
		'blocked'        => true,
		'reason'         => $reason,
		'environment'    => $environment,
		'services'       => array(),
	);
}
```

In preflight, store snapshot hash:

```php
$preflight[ $key ] = array(
	'page'                 => $page,
	'original'             => $meta,
	'sanitized'            => $sanitized,
	'payload_sha256'       => $before_hash,
	'snapshot_before_hash' => hash( 'sha256', wp_json_encode( $entry['human_readable_snapshot'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ),
);
```

After successful post-write payload/marker verification, add human-readable verification:

```php
$after_snapshot       = alipasandi_service_human_readable_snapshot( $after );
$after_snapshot_hash  = hash( 'sha256', wp_json_encode( $after_snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

if ( ! hash_equals( $prepared['snapshot_before_hash'], $after_snapshot_hash ) ) {
	update_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, $prepared['original'] );

	foreach ( array_reverse( $written, true ) as $written_key => $written_prepared ) {
		update_post_meta( $written_prepared['page']->ID, ALIPASANDI_SERVICE_META_KEY, $written_prepared['original'] );
		$result['services'][ $written_key ]['result'] = 'rolled_back_after_human_readable_verify_failure';
	}

	$result['services'][ $key ]['result'] = 'human_readable_verify_failed_rolled_back';
	$result['pass']                         = false;
	$result['reason']                       = 'write_verify_failed_human_readable_drift:' . $key;

	return $result;
}

$result['services'][ $key ]['snapshot_after'] = $after_snapshot;
```

Add WP-CLI commands inside the existing `if ( defined( 'WP_CLI' ) && WP_CLI ) { ... }` block:

```php
WP_CLI::add_command( 'alipasandi service rank-math-probe', function () {
	$report = alipasandi_rank_math_local_entity_probe();

	WP_CLI::line( wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

	if ( empty( $report['pass'] ) ) {
		WP_CLI::halt( 1 );
	}
} );

WP_CLI::add_command( 'alipasandi service nap-consistency', function () {
	WP_CLI::line( wp_json_encode( alipasandi_nap_consistency_values(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
} );
```

---

# QA / CI تغییرات

## `qa/ci-source-audit.sh`

Update version checks:

```bash
grep -q '^Version: 1\.4\.29$' "$THEME/style.css" && pass theme_header_1.4.29 || bad theme_header
grep -q '^Stable tag: 1\.4\.29$' "$THEME/readme.txt" && pass theme_readme_1.4.29 || bad theme_readme
grep -q '^ \* Version: 1\.3\.4$' "$PLUGIN/alipasandi-service-content.php" && pass plugin_header_1.3.4 || bad plugin_header
grep -q '^Stable tag: 1\.3\.4$' "$PLUGIN/readme.txt" && pass plugin_readme_1.3.4 || bad plugin_readme
grep -q "ALIPASANDI_THEME_VERSION', '1.4.29'" "$THEME/functions.php" && pass theme_constant || bad theme_constant
grep -q "ALIPASANDI_SERVICE_PLUGIN_MIN_VERSION', '1.3.4'" "$THEME/functions.php" && pass theme_plugin_min || bad theme_plugin_min
grep -q "ALIPASANDI_SERVICE_PRODUCTION_THEME_MIN', '1.4.29'" "$PLUGIN/includes/compatibility.php" && pass plugin_pairing || bad plugin_pairing
```

Add new contract checks:

```bash
if grep -q 'ستارخان، امیراد ۱، طبقه ۵' "$THEME/front-page.php" >/tmp/street-fallback.$$ 2>/dev/null; then
cat /tmp/street-fallback.$$
bad theme_street_hardcoded_fallback
else
pass no_theme_street_hardcoded_fallback
fi
rm -f /tmp/street-fallback.$$

grep -q "street_missing" "$PLUGIN/includes/health-checks.php" && pass health_street_missing || bad health_street_missing

grep -q "function alipasandi_local_entity_missing_fields" "$PLUGIN/includes/site-settings.php" && \
grep -q "function alipasandi_local_entity_schema_ready" "$PLUGIN/includes/site-settings.php" && pass local_nap_contract || bad local_nap_contract

grep -q "function alipasandi_booking_schedule_cross_report" "$PLUGIN/includes/validators.php" && pass booking_cross_helper || bad booking_cross_helper

grep -q "booking_slots_no_overlap_with_opening_hours" "$PLUGIN/includes/health-checks.php" && pass booking_cross_health || bad booking_cross_health

grep -q "function alipasandi_direct_phone_route_ready" "$PLUGIN/includes/site-settings.php" && \
grep -q "alipasandi_direct_phone_route_ready()" "$PLUGIN/includes/forms.php" && pass phone_route_form_gate || bad phone_route_form_gate

grep -q "alipasandi_rank_math_local_entity_observations" "$PLUGIN/includes/site-settings.php" && pass rank_math_per_context_observation || bad rank_math_per_context_observation

grep -q "rank-math-probe" "$PLUGIN/includes/service-content.php" && pass rank_math_probe_command || bad rank_math_probe_command

grep -q "function alipasandi_service_human_readable_snapshot" "$PLUGIN/includes/service-content.php" && pass schema_repair_human_snapshot || bad schema_repair_human_snapshot

grep -q "schema_repair_apply_requires_explicit_staging_environment" "$PLUGIN/includes/service-content.php" && pass schema_apply_explicit_staging || bad schema_apply_explicit_staging

grep -q "nap-consistency" "$PLUGIN/includes/service-content.php" && pass nap_consistency_command || bad nap_consistency_command

grep -q 'Theme 1.4.29 / Plugin 1.3.4' "$THEME/docs/MAINTENANCE-GUIDE-FA.md" && pass maintenance_pair_current || bad maintenance_pair_current
```

---

## `qa/local-contract-smoke.php`

Add tests after opening-hours cases:

```php
$cross = alipasandi_booking_schedule_cross_report_from_inputs( "20:00\n", "MO=09:00-17:00\nFR=CLOSED" );
t( 'cross_no_overlap_fail', empty( $cross['has_usable_slot'] ) );

$cross = alipasandi_booking_schedule_cross_report_from_inputs( "10:00\n", "MO=09:00-17:00\nFR=CLOSED" );
t( 'cross_overlap_pass', ! empty( $cross['has_usable_slot'] ) );
t( 'cross_open_day_without_slot', in_array( 'TU', $cross['open_days_without_slot'], true ) );

$cross = alipasandi_booking_schedule_cross_report_from_inputs( "10:00\n", '' );
t( 'cross_no_hours_valid_slots_usable', ! empty( $cross['has_usable_slot'] ) );
```

---

## `qa/wp-cli-bootstrap-smoke.php`

Add explicit staging-environment guard test:

```php
$GLOBALS['environment_type'] = 'development';

$dev_apply = alipasandi_service_schema_repair_apply( str_repeat( 'a', 64 ) );

if ( empty( $dev_apply['blocked'] ) || 'schema_repair_apply_requires_explicit_staging_environment' !== ( $dev_apply['reason'] ?? '' ) ) {
	fwrite( STDERR, "FAIL schema-repair explicit staging environment guard\n" );
	exit( 1 );
}

printf( "PASS schema-repair explicit staging environment guard\n" );
```

Add human-readable snapshot existence test before staging apply:

```php
$plan = alipasandi_service_schema_repair_plan();

if ( ! isset( $plan['services']['implant']['human_readable_snapshot'] ) ) {
	fwrite( STDERR, "FAIL schema-repair human readable snapshot missing\n" );
	exit( 1 );
}

printf( "PASS schema-repair human readable snapshot present\n" );
```

---

# Documentation changes

## `docs/MAINTENANCE-GUIDE-FA.md`

Update header:

```text
Maintenance Guide — Theme 1.4.29 / Plugin 1.3.4
```

Update pairing:

```text
Production pairing = Theme 1.4.29 + Plugin 1.3.4.
```

Add NAP section:

```text
clinic_address = آدرس نمایشی UI برای Footer/Contact/Home/Mail.
clinic_street = streetAddress رسمی برای Schema/PostalAddress.
در تغییر مکان، هر دو باید همزمان update شوند.
اگر clinic_street خالی باشد، Health Critical می‌شود و local schema fail-safe حذف می‌شود.
```

Add Rank Math runbook:

```text
wp alipasandi service rank-math-probe
wp alipasandi service nap-consistency
wp alipasandi service health
```

Add schema repair environment:

```text
Apply فقط روی isolated clone با WP_ENVIRONMENT_TYPE=staging مجاز است.
```

---

## `docs/OPENING-HOURS-CONTRACT-FA.md`

Add:

```text
Booking slots و Opening hours جدا از هم معتبر نیستند؛ cross-contract نیز بررسی می‌شود.
حداقل یک configured booking slot باید با حداقل یک open range هفتگی overlap داشته باشد.
zero usable slots across weekly schedule = Critical و appointment form fail closed.
open day بدون slot = Warning، مگر Business Rule خلاف آن را بخواهد.
```

---

## `docs/MEDICAL-CLAIM-REGISTER-FA.md`

Add explicit pending claims:

| Surface | Claim | Type | Decision |
|---|---|---|---|
| Home / Services | «ایمپلنت تخصصی» | expertise/scope | PENDING |
| Home / Services | «برندهای معتبر جهانی» | quality/comparative | PENDING |
| Services Hub | «جراحی تخصصی» | expertise | PENDING |
| Home / Services | «بدون تراش دندان‌های مجاور» | treatment/comparative | PENDING |
| Any | هر outcome/comparative/duration/success claim | medical | PENDING |

`+10,000` remains OFF until explicit approval.

---

# Build / ZIP / Hashes

چون ZIP باینری در این چت قابل ارسال نیست، release source را با همان deterministic builder قبلی بسازید:

```bash
python3 qa/build_deterministic.py /path/to/alipasandi-clinic /path/to/release/alipasandi-clinic-1.4.29.zip alipasandi-clinic
python3 qa/build_deterministic.py /path/to/alipasandi-service-content /path/to/release/alipasandi-service-content-1.3.4.zip alipasandi-service-content
```

سپس:

```bash
sha256sum /path/to/release/alipasandi-clinic-1.4.29.zip
sha256sum /path/to/release/alipasandi-service-content-1.3.4.zip
```

هر هش تولیدشده را در `RELEASE-MANIFEST.txt` ثبت کنید. هش نباید از قبل hardcode شود.

---

# Staging Runbook جدید — Release 1.4.29 / Plugin 1.3.4

ترتیب اجرا:

```bash
wp --info
wp plugin status alipasandi-service-content

wp alipasandi service nap-consistency
wp alipasandi service rank-math-probe
wp alipasandi service health

wp alipasandi service schema-repair
```

اگر schema repair لازم شد:

```bash
wp alipasandi service schema-repair | tee schema-repair-dry-run-1.3.4.json
```

فقط روی isolated staging clone:

```bash
wp alipasandi service schema-repair --apply --confirm-plan-sha=<PLAN_SHA256> | tee schema-repair-apply-1.3.4.json
wp alipasandi service health | tee service-health-after-1.3.4.json
```

قبل Apply:

- DB backup
- uploads/config backup
- Service JSON export
- payload SHA record
- human-readable snapshot قبل/بعد record

---

# Remaining Production Gates — بدون تغییر

این Release فقط Code-level contractها را بست. Production Approval همچنان منوط به Runtime Evidence واقعی روی exact hashes جدید است:

- PHP 8.2 real runtime
- WP 6.4 real runtime
- Production-identical WP 7.0.4 / PHP 8.5.7 lane
- actual WP-CLI
- PHPUnit
- WPCS/PHPCS
- PHPStan/static/deprecated
- Rank Math active/inactive/reactivated diff
- SMTP/DNS/inbox headers
- Debug display hardening
- Loopback/WP-Cron fix
- Backup/restore rehearsal
- Host redirects/canonical/indexability crawl
- Security headers
- Cache/post_modified/lastmod
- CWV/browser/A11y/Admin RTL
- Privacy/legal/medical claim/asset approvals
- Owner-approved NAP freeze
- Owner-approved horizon/lead-time

---

# وضعیت نهایی

| Gate | Status |
|---|---|
| Design/URL/Layout | Locked |
| Theme/Plugin version bump | 1.4.29 / 1.3.4 |
| Home NAP fallback | CLOSED |
| street Health Critical | CLOSED |
| Partial PostalAddress fail-safe | CLOSED |
| Booking/Open-hours cross contract | CLOSED |
| Phone route form readiness | CLOSED |
| Shared NAP readiness helper | CLOSED |
| Rank Math deterministic probe | CLOSED |
| Per-context Rank Math observation | CLOSED |
| Schema repair human-readable diff | CLOSED |
| Schema repair explicit staging | CLOSED |
| NAP consistency CLI | CLOSED |
| Artifact hashes | PENDING BUILD |
| Runtime Evidence | NOT PROVEN |
| Production Approval | NO |

