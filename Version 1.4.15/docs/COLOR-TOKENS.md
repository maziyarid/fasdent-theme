# قرارداد توکن رنگ — نسخه ۱.۴.۱

این فایل مرجع مشترک Figma و WordPress برای مرحله Recolor است. مقدارهای canonical در `assets/design-tokens.json` نگهداری می‌شوند و در CSS با نام‌های زیر در دسترس‌اند:

| کاربرد | CSS Variable | مقدار |
| --- | --- | --- |
| Hero و سکشن قهوه‌ای اصلی | `--color-hero-brown-primary` | `#4A3022` |
| سطح قهوه‌ای ثانویه | `--color-hero-brown-light` | `#684936` |
| Header، Footer و تیره‌ترین سطح | `--color-dark-espresso` | `#2C1D16` |
| پس‌زمینه روشن | `--color-cream-primary` | `#F2E9DA` |
| Card و Surface روشن | `--color-cream-light` | `#F8F2E8` |
| Accent محدود | `--color-caramel-accent` | `#C58D52` |

## دامنه مرحله اول

توکن‌ها در این تحویل فقط روی Homepage فعال شده‌اند. صفحات داخلی عمداً روی پالت نسخه ۱.۴.۰ مانده‌اند تا Home در عرض‌های ۱۴۴۰ و ۳۹۰ تأیید شود. پس از تأیید، همین توکن‌ها بدون تغییر Layout به همه Templateها تعمیم داده می‌شوند.

## قواعد کنتراست

- متن Espresso روی CTA کاراملی: `5.64:1` — مجاز برای متن معمولی طبق WCAG AA.
- متن Cream روی Brown Primary: `10.04:1` — مجاز.
- متن Cream Light روی Brown Light: `7.27:1` — مجاز.
- Caramel روی Cream Primary: `2.39:1` — برای متن معمولی ممنوع؛ فقط Accent غیرمتنی/تزئین محدود.
- Caramel روی Brown Primary: `4.21:1` — برای متن کوچک استفاده نشود.

هیچ Glow یا Gradient شدید کاراملی تعریف نشده است.
