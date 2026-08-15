# پاسخ فنی — Hardening Service Meta قبل از Staging (۱.۴.۲۰)

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**Build کاندید Staging:** **۱.۴.۲۰**  
**Design:** Locked  
**Production Approval:** هنوز خیر

---

## خلاصه تغییرات نسبت به ۱.۴.۱۹

تمام موارد فنی ۱–۱۵ که Code-level بودند در **۱.۴.۲۰** اعمال شدند. Design / URL / Layout بدون تغییر است.

| # | درخواست | اقدام در ۱.۴.۲۰ |
|---|---|---|
| 1 | Permission per-object | `auth_callback`: `current_user_can('edit_post', $object_id)` + فقط ۴ Service page |
| 2 | Nonce / Save guards | Nonce اختصاصی · verify · `edit_post` · autosave skip · revision skip · اگر `alipasandi_svc` در POST نباشد Meta پاک نمی‌شود |
| 3 | Revision | Meta در `_wp_put_post_revision` کپی و در `wp_restore_post_revision` بازگردانده می‌شود |
| 4 | Autosave | `DOING_AUTOSAVE` / `wp_is_post_autosave` → return؛ Meta overwrite نمی‌شود |
| 5 | Migration Flag | Option `alipasandi_service_meta_status_v1` با وضعیت **per-page**: migrated / existing / failed · `completed=true` فقط وقتی هر ۴ OK |
| 6 | Idempotent | Meta موجود هرگز overwrite نمی‌شود (metadata_exists) |
| 7–8 | Fallback دقیق | **فقط اگر Meta اصلاً وجود نداشته باشد** → Legacy · فیلد عمداً خالی = خالی می‌ماند |
| 9 | schema_version | داخل object: `schema_version: 1` |
| 10 | Sanitization | Plain text / wp_kses محدود برای body و FAQ (لینک `<a>` مجاز) / absint برای image_id |
| 11 | Output escaping | Titles: `esc_html` · body/FAQ: `wp_kses` · URLs: `esc_url` · attrs: `esc_attr` |
| 12–13 | Image | فیلد `image_id` (Attachment ID ترجیحی) + filename legacy + ALT مستقل (خالی = تزئینی) |
| 14 | REST | `show_in_rest = false` — عمداً Public نیست |
| 15 | تشخیص صفحه | **Slug** در `{implant,crown,surgery,general}` (± parent `services`) — نه Title |

---

## تعریف Fallback (نهایی)

```
اگر metadata_exists( post, ID, _alipasandi_service ) → Meta منبع کامل صفحه است
  (حتی اگر title/intro/CTA عمداً خالی باشند — Legacy برنمی‌گردد)
در غیر این صورت → alipasandi_get_service_legacy() فقط خواندنی
```

service-data.php هرگز Write یا Sync معکوس نمی‌شود.

---

## Migration Status (برای Staging Report)

پس از Deploy، وضعیت از Option خوانده می‌شود:

```
alipasandi_service_meta_status_v1 = {
  completed: true|false,
  pages: {
    implant: migrated|existing|failed,
    crown:   ...,
    surgery: ...,
    general: ...
  }
}
```

اگر یک صفحه در اولین اجرا failed باشد، Global completed ست نمی‌شود و اجرای مجدد فقط همان صفحه را retry می‌کند — بدون دست زدن به Metaهای موفق.

---

## Revision / Restore

- هنگام ساخت Revision، Meta همراه کپی می‌شود.  
- Restore Revision → Meta بازگردانده می‌شود.  
- در Maintenance Guide مستند شد.

---

## Rank Math / NAP / Redirect (تأیید معماری — Evidence روی Staging)

| موضوع | موضع |
|---|---|
| Rank Math | برای Staging قابل قبول؛ Configuration واقعی باید Pass شود |
| NAP Drift | ترجیح: Schema Local از Options تغذیه شود؛ یا Checklist Sync اجباری در Launch |
| Single Location | فقط نوشهر — بدون Multiple Location تهران |
| Redirect | Host-level (HTTP/HTTPS/www) روی Server · Page-level در Rank Math Redirects |
| Appointment Noindex | تأیید اولیه · CTA follow باقی می‌ماند · از Sitemap خارج |
| Phone / +10k | Default تا تأیید شما · Claim خاموش تا Verified |
| Live ≠ Final | QA فقط روی Staging ۱.۴.۲۰ |

---

## Production Status

| | |
|---|---|
| Design | **Locked** |
| Service Migration architecture | **تأیید + Hardened در ۱.۴.۲۰** |
| Production Approval | **خیر** |

**مرحله بعد:** Deploy **۱.۴.۲۰** روی Staging → Evidence کامل (Migration status چهار صفحه، Diff واقعی View-Source، Meta Box save/reload، Idempotency، Revision/Autosave، Form/Mail، Rank Math config، Schema، CWV، …).

Buildی که QA Pass می‌کند باید همین **۱.۴.۲۰** (یا Build بعدی با version جدید در صورت تغییر Code) باشد.

---

## فایل

`alipasandi-clinic-1.4.20.zip` — Staging Build
