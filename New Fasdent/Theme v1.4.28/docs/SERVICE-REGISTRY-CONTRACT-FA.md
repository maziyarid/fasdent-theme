# Service Registry Contract — Plugin 1.3.3

SSOT = `alipasandi_service_registry()` در Plugin. **تمام گزینه‌های قابل انتخاب Appointment، چه page-backed و چه non-page، داخل همین Registry هستند. Secondary hard-coded appointment list وجود ندارد.**

## Schema دقیق هر Entry

هر Entry بعد از normalization دقیقاً این شش field را دارد:

| Field | Contract |
|---|---|
| `key` | required؛ lowercase ASCII slug؛ `^[a-z0-9][a-z0-9-]{0,63}$`؛ unique |
| `label` | required؛ plain text؛ non-empty |
| `bookable` | required boolean؛ فقط entryهای true وارد Appointment allowlist می‌شوند |
| `page_slug` | slash-separated ASCII slug؛ برای non-page می‌تواند خالی باشد |
| `content_managed` | required boolean؛ اگر true باشد `page_slug` الزامی است و entry در migration/health Service Content شرکت می‌کند |
| `icon` | required key؛ باید داخل `alipasandi_service_registry_icon_allowlist` باشد |

Unknown field، malformed type، duplicate/invalid key، empty label، invalid boolean، invalid page slug، managed entry بدون page، یا invalid icon **Fail Closed** است: Entry وارد Registry/UI/Server allowlist نمی‌شود و `service_registry_invalid` در Site Health Critical می‌شود. جزئیات در `registry_issues` Health report قابل مشاهده است.

Legacy filter callback که string-list برگرداند فقط برای keyهای default شناخته‌شده Compatibility دارد و به‌صورت warning ثبت می‌شود؛ string ناشناخته خودکار Service جدید نمی‌سازد.

## Current Registry

Page-backed/content-managed:

`implant`, `crown`, `surgery`, `general`

Bookable non-page entries در همان Registry:

`consultation`, `root-canal`, `cleaning`, `other`

`alipasandi_bookable_services()` map از **stable key → official display label** برمی‌گرداند و Theme Appointment UI value را همان stable key قرار می‌دهد. `alipasandi_allowed_services()` فقط stable keyهای bookable را به Server allowlist می‌دهد؛ Email با `alipasandi_service_label(key)` label رسمی را resolve می‌کند. Display Label هرگز Identity نیست.

افزودن Service جدید نیازمند Release کنترل‌شده و بررسی Registry validation، page association در صورت وجود، form behavior، navigation، sitemap/indexability، schema، migration/health، content/medical approval، redirects و Staging QA است.


## Duplicate label / page identity

Duplicate display label به‌دلیل stable-key submission collision امنیتی ایجاد نمی‌کند، اما UX ambiguity است و `registry_warnings` Health آن را non-silent گزارش می‌کند. برای `content_managed=true` فقط syntax `page_slug` کافی نیست: Health وجود Page، unique stable identity و duplicate `_alipasandi_service_key` را نیز بررسی می‌کند.

## Primary UI contract

Registry extensibility به معنی auto-expansion UI اصلی Theme نیست. چهار Service فعلی Primary Design Lock هستند؛ Service پنجم برای Home/Nav/Services Hub نیازمند Theme/UI + SEO release جداگانه است.
