# Security & Operations Notes

## فرم و Rate Limit

- IP پیش‌فرض فقط `REMOTE_ADDR` است؛ هیچ `X-Forwarded-For` به‌طور کور trusted نیست.
- Hosting پشت Proxy فقط پس از allowlist همان Proxy می‌تواند از filter `alipasandi_form_client_ip` Client IP verified بدهد.
- ۵ درخواست/۱۵ دقیقه per action/IP؛ پیام فارسی اختصاصی دارد.
- Transient get/set کاملاً atomic نیست؛ برای ترافیک فعلی Risk محدود پذیرفته شده. Abuse واقعی باید در Cloudflare/WAF به‌صورت atomic rate-limit شود.
- Nonce، Honeypot، server validation و notes size limit فعال‌اند.
- User input در From header استفاده نمی‌شود؛ Reply-To جعلی تولید نمی‌شود.
- خطای Mail موفقیت نشان نمی‌دهد؛ جزئیات SMTP/PHP/path به Visitor نمایش داده نمی‌شود.
- Theme/Site Plugin form content، name، phone، notes یا email body را Log نمی‌کنند.

## SMTP Production Gate

Exact SMTP Plugin/version، domain-based From، SPF، DKIM، DMARC، logging state، retention، view role، deletion process و provider failure monitoring باید روی Staging/Production ثبت شوند.

## Local SEO

Rank Math Free/PRO/version/license/update owner باید ثبت شود. Multiple Locations خاموش. KML/Local Sitemap تا زمانی که تمام NAP/Geo outputها SSOT و فقط نوشهر بودنشان ثابت نشده **Disabled** است.

## Deactivate/Delete

هیچ uninstall hook یا `uninstall.php` وجود ندارد. Service Meta، Keys و NAP در Deactivate/Delete باقی می‌مانند. Cleanup فقط با ابزار جدا و confirmation صریح در Release آینده مجاز است.
