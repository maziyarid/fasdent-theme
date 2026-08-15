> HISTORICAL / SUPERSEDED by Release 1.4.28 / Plugin 1.3.3.

# Security Notes — 1.4.26 / Plugin 1.3.1

## Form ownership
Theme هیچ Handler، Mail، Validation یا Rate-limit ندارد. Plugin missing/outdated ⇒ interactive form disabled. Plugin تنها owner پردازش Contact/Appointment Request است.

## Rate limit
- Scope مستقل برای `contact` و `appointment`.
- Raw IP ذخیره نمی‌شود.
- Key: HMAC-SHA256 از action + validated client IP با WordPress salt؛ صرفاً **one-way rate-limit identifier** است، نه password/cryptographic identity claim.
- Counter در WordPress transient با الگوی get→increment→set است و **atomic نیست**. Abuse در مقیاس بالا owner = Edge/WAF/Redis atomic counter.
- `REMOTE_ADDR` پیش‌فرض است. اگر IP معتبر در دسترس نباشد، submission fail-closed می‌شود و operational warning در PHP log ثبت می‌شود؛ quota مشترک `unknown` ساخته نمی‌شود.

## Trusted proxy example
فقط بعد از دریافت IP/CIDR رسمی Proxy از Hosting از filter `alipasandi_form_client_ip` استفاده شود. `X-Forwarded-For` هرگز بدون allowlist Proxy trust نشود. نمونه مفهومی:

```php
add_filter( 'alipasandi_form_client_ip', function ( $remote ) {
    $trusted_proxy_ips = array( '203.0.113.10' ); // REPLACE with hosting evidence.
    if ( ! in_array( $remote, $trusted_proxy_ips, true ) ) {
        return $remote;
    }
    $xff = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) : array();
    $candidate = isset( $xff[0] ) ? trim( $xff[0] ) : '';
    return filter_var( $candidate, FILTER_VALIDATE_IP ) ? $candidate : $remote;
}, 10, 1 );
```

## Mail
Notify Email و From باید explicitly configured باشند. From باید email معتبر روی canonical `home_url()` domain باشد. Policy فعلی: exact canonical domain یا subdomain مستقیم/چندسطحی زیر همان registrable project host (مثلاً `mail.fasdent.ir`) پذیرفته می‌شود؛ external/look-alike domain رد می‌شود. استفاده از subdomain فقط با تأیید Hosting و Evidence واقعی SPF/DKIM/DMARC/SMTP مجاز است. `wp_mail=true` فقط قبول transport را نشان می‌دهد؛ Production PASS نیازمند SMTP provider + SPF/DKIM/DMARC + inbox receipt + delivered headers است.


## HTTP / UX rate-limit semantics
فرم‌ها از POST/Redirect/GET روی `admin-post.php` استفاده می‌کنند و state کاربر پس از redirect با `form_status=rate_limited` نمایش داده می‌شود. UI پیام فارسی واضح + مسیر تماس مستقیم دارد. این flow در 1.4.26 عمداً response document نهایی را 429 نمی‌کند؛ اگر در آینده API/XHR endpoint اضافه شود، همان endpoint باید HTTP `429 Too Many Requests` و `Retry-After` مناسب داشته باشد.
