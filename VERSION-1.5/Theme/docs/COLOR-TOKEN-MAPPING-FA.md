# نگاشت نهایی Color Tokenها — Home

مرجع canonical مقدارها: `assets/design-tokens.json`. این سند فقط نحوه مصرف Tokenها را توضیح می‌دهد و مقدار رنگ مستقلی تعریف نمی‌کند.

| Component / State | Token اصلی | Token مکمل | قاعده مصرف |
| --- | --- | --- | --- |
| Header | Dark Espresso | Cream Light / Caramel | Background با Espresso؛ متن و Navigation با Cream؛ Active/لوگو با Caramel |
| Hero | Hero Brown Primary | Hero Brown Light | Brown Primary سطح اصلی؛ Brown Light فقط Depth بسیار ظریف و محدود |
| Hero Heading | Cream Light | Caramel | متن اصلی Cream؛ فقط عبارت «عادت نکن.» Caramel |
| Hero Body Text | Cream Primary | — | Caramel برای متن توضیحی استفاده نمی‌شود |
| Primary CTA | Caramel Accent | Dark Espresso | Background کاراملی و متن Espresso؛ نسبت کنتراست `5.64:1` |
| Secondary Button | Transparent | Cream Light / Caramel | متن Cream؛ Border روشن؛ Caramel فقط Border محدود در Hover |
| Light Section Background | Cream Primary | — | ایجاد فضای تنفس بین سکشن‌های Brown/Espresso |
| Light Card / Surface | Cream Light | Dark Espresso | Card از Background روشن تفکیک می‌شود؛ متن اصلی Espresso |
| Secondary Text on Light | Hero Brown Light | — | متن فرعی خوانا؛ Caramel برای متن معمولی ممنوع |
| Dark Section | Hero Brown Primary | Cream Primary | تناوب با سکشن‌های روشن؛ Brown سطح غالب کل صفحه نمی‌شود |
| Footer | Dark Espresso | Cream / Caramel | متن و لینک Cream؛ Caramel فقط Highlight یا Hover |
| Icon | Caramel Accent | Hero Brown Primary | Caramel برای آیکن‌های منتخب؛ روی سطح روشن متن همراه Espresso/Brown است |
| Active State | Caramel Accent | Dark Espresso | روی Header/Footer تیره استفاده می‌شود؛ روی سطح Cream متن Active همچنان Espresso است |
| Hover | Context-dependent | Caramel Accent | Caramel فقط برای Border/Highlight یا متن روی Espresso مجاز است |
| Focus | Cream Light + Dark Espresso | — | Focus Ring دو‌رنگ برای دیده‌شدن هم‌زمان روی سطوح روشن و تیره؛ Caramel روی Cream استفاده نمی‌شود |
| Border / Divider | Caramel Accent با Opacity محدود | Dark Espresso با Opacity محدود | صرفاً تفکیک بصری؛ سطح بزرگ ایجاد نمی‌کند |
| Main Text | Dark Espresso | — | متن اصلی روی Cream/Cream Light |
| Text on Brown | Cream Primary / Cream Light | — | Caramel روی Brown برای متن کوچک استفاده نمی‌شود |
| Mobile Sticky CTA | Dark Espresso / Caramel | Cream | Bar با Espresso، CTA رزرو با Caramel و متن Espresso؛ Safe Area پایین رعایت می‌شود |

## قوانین غیرقابل‌تغییر

- `#C58D52` فقط Accent است؛ Surface بزرگ، Glow شدید یا متن معمولی روی Cream نیست.
- Caramel روی Cream Primary (`2.39:1`) برای متن معمولی ممنوع است.
- Caramel روی Brown Primary (`4.21:1`) برای متن کوچک ممنوع است.
- Espresso روی Caramel CTA (`5.64:1`) مجاز است.
- صفحات داخلی تا تأیید تصویری Home Recolor نمی‌شوند.
