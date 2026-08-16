# Compatibility Contract

| Component | Supported contract |
|---|---|
| Theme | 1.4.23 |
| Required Site Plugin | Alipasandi Service Content >= 1.1.0 |
| Site Plugin theme integration | Alipasandi Clinic >= 1.4.22؛ سایر Themeها Data/UI را حفظ می‌کنند ولی presentation integration ندارند |
| WordPress minimum | 6.4 |
| PHP minimum | 8.0 |
| Production target | WordPress version ثبت‌شده Staging؛ PHP 8.2 |
| Service API | 1.1 |

Theme در Plugin missing/outdated Admin Notice و Site Health Critical می‌دهد. Plugin روی Theme قدیمی Notice می‌دهد. Frontend در نبود Plugin، Meta موجود را قبل از legacy file می‌خواند؛ Deactivate/Delete هیچ Dataای حذف نمی‌کند.

Compatibility claim برای WP 6.2/6.3 و PHP 7.4 حذف شد؛ Release فقط نسخه‌هایی را اعلام می‌کند که CI/Staging باید واقعاً نگهداری کند.
