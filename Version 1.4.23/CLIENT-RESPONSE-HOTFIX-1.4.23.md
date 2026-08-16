# Hotfix پاسخ نهایی — ۱.۴.۲۳

خطای Fatal در SEO fallback نسخه ۱.۴.۲۲ شناسایی و رفع شد.

- علت: استفاده اشتباه از `get_canonical_url()`؛ این نام Filter وردپرس است، نه Function عمومی.
- اصلاح: استفاده از API صحیح `wp_get_canonical_url( $post_id )`.
- Hardening: اگر به هر دلیل Function در دسترس نباشد، `get_permalink( $post_id )` به‌عنوان fallback امن استفاده می‌شود و Frontend Fatal نمی‌دهد.
- Scope: فقط SEO fallback Theme؛ Service Plugin 1.1.0 بدون تغییر است.
- Versioning: به‌دلیل تغییر PHP، Theme به ۱.۴.۲۳ bump شد و Artifact/Hash/Tag جدید دارد.

Test Staging الزامی:

1. Rank Math active: Theme fallback metadata اجرا نشود؛ duplicate zero.
2. Rank Math inactive: About و Service pages بدون Fatal؛ canonical/OG URL صحیح.
3. Rank Math reactivate: metadata/schema duplicate zero.

Production Approval همچنان منوط به Preflight و Evidence واقعی است.
