# Service Registry Contract — Plugin 1.3.0

SSOT = `alipasandi_service_registry()` در Plugin. هر Service می‌تواند metadata زیر داشته باشد:

- `key`
- `label`
- `bookable`
- `page_slug`
- `content_managed`
- `icon`

چهار Service فعلی: implant, crown, surgery, general. Appointment options برای Serviceهای page-backed از Registry derive می‌شوند. گزینه‌های غیرصفحه‌ای فقط از filter صریح `alipasandi_appointment_service_extras` می‌آیند.

افزودن Service پنجم نیازمند یک Release کنترل‌شده و بررسی Registry، page association، form options، navigation، sitemap/indexability، schema، migration/health، content/medical approval، redirects و Staging QA است. صرف افزودن یک string یا ساخت Page کافی نیست.
