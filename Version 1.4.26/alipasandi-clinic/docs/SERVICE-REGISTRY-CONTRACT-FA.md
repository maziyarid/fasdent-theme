# Service Registry Contract — Plugin 1.3.1

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

`alipasandi_allowed_services()` فقط labelهای `bookable=true` همین Registry را برمی‌گرداند. Theme Appointment UI و Server handler هر دو همین function را مصرف می‌کنند.

افزودن Service جدید نیازمند Release کنترل‌شده و بررسی Registry validation، page association در صورت وجود، form behavior، navigation، sitemap/indexability، schema، migration/health، content/medical approval، redirects و Staging QA است.
