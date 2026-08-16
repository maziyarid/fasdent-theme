# آرشیو پرامپت فاز اول Figma — مرجع اجرایی فعلی نیست

> این سند فقط تاریخچه تبدیل تصویر اولیه به مرجع طراحی است. از نسخه ۱.۴.۱ به بعد، اجرای مستقیم در WordPress Theme انجام می‌شود و مرجع رنگ فقط `assets/design-tokens.json` و `docs/COLOR-TOKENS.md` است. پالت مشکی/طلایی نوشته‌شده در ادامه نباید دوباره اعمال شود.

این پرامپت را بدون خلاصه‌سازی برای Figma ارسال کنید:

---

برای وب‌سایت RTL کلینیک دندانپزشکی Dr Keyvan Alipasandi یک Design System و طراحی High-Fidelity فاز اول بساز. خروجی باید Premium، مینیمال، پزشکی، مدرن و قابل اعتماد باشد و از دو ماکاپ مشکی–طلایی ایمپلنت و روکش به‌عنوان مرجع کیفیت و Art Direction استفاده کند؛ اما کل سایت را مشکی نکن. تناوب سکشن‌های زغالی و استخوانی برای حفظ حس پزشکی و خوانایی الزامی است.

## محدوده قطعی فاز اول

1. Foundations و Design Tokens
2. Component Library اصلی
3. Header، Footer و Mobile Menu
4. Homepage در Desktop 1440px و Mobile 390px
5. یک Service Page مرجع برای «ایمپلنت دندان» در Desktop 1440px و Mobile 390px
6. Template مشترک صفحات خدمات، به‌گونه‌ای که روکش زیرکونیا، جراحی و لثه و درمان عمومی با همان Componentها و بدون طراحی مجدد از صفر ساخته شوند
7. Prototype باز و بسته‌شدن منوی موبایل

صفحات About، Contact، Appointment و FAQ در این فاز فقط در سطح قواعد Template و Component coverage در نظر گرفته شوند و طراحی کامل مستقل لازم نیست. Before/After فقط برای Homepage و صفحه مرجع ایمپلنت نگه داشته شود؛ برای صفحات دیگر Before/After تولید نکن.

## هویت بصری و Tokens

- Direction: RTL و زبان فارسی
- Font: Vazirmatn، ترجیحاً وزن‌های 400، 500 و 700؛ وزن اضافه پیشنهاد نشود مگر با دلیل مشخص
- Charcoal: `#101010`
- Champagne Gold Accent: `#D6A54A`
- Bone: `#F7F5F1`
- Gray: `#A7A7A7`
- White فقط در صورت نیاز به کنتراست
- طلایی فقط برای CTA، آیکن، Divider باریک، Border کنترل‌شده و Highlight کوتاه استفاده شود؛ سطح بزرگ طلایی، Glow، Gradient شدید و Shadow سنگین ممنوع است
- Card radius: 10–12px
- Button radius: 6–8px
- Minimum button height: 48px
- Card padding: 24–32px
- Shadow: بسیار کم و خنثی
- Section spacing: Desktop 80–96px، Mobile 56–64px
- Container: Desktop max-width 1320px با حدود 60px فاصله جانبی؛ Tablet حدود 32px؛ Mobile 16px
- Breakpoints: Desktop ≥1200px، Tablet 768–1199px، Mobile <768px
- هیچ Frame یا Component نباید Horizontal Scroll ایجاد کند

برای Color، Typography، Spacing، Radius، Border، Shadow و Container از Figma Variables/Styles استفاده کن. Componentها با Auto Layout، Constraints و نام‌گذاری منظم ساخته شوند. اندازه‌ها، Gapها، Paddingها، Max-widthها و رفتار تغییر Layout بین Breakpointها در Dev Mode قابل اندازه‌گیری باشند.

## Header و Navigation — الزام RTL

Header زغالی، Sticky و جمع‌وجور باشد.

- Desktop: لوگو دقیقاً سمت راست، Navigation در میانه، CTA مستقل «رزرو نوبت» دقیقاً سمت چپ
- Mobile: لوگو دقیقاً سمت راست و Hamburger دقیقاً سمت چپ
- CTA رزرو نوبت جزو لینک‌های Navigation محسوب نشود
- لینک‌های اصلی فقط: «صفحه اصلی / درباره ما / خدمات / تماس با ما»
- «مقالات»، «گالری» و «سوالات متداول» در Navigation اصلی نمایش داده نشوند
- مقالات و FAQ می‌توانند از Footer یا Contextual Links قابل دسترسی باشند؛ گالری در این فاز نمایش داده نشود
- فقط یک Navigation Source/Component برای Desktop و Mobile تعریف شود تا منوها از هم جدا و ناسازگار نشوند

Mobile Menu یک Overlay واقعی تمام‌صفحه زیر Header باشد. Stateهای Closed و Open طراحی شوند. هنگام Open بودن، Body Scroll و تعامل با محتوای پشت Overlay غیرفعال باشد. Focus order، Close action، Escape behavior و Active link در Annotation توضیح داده شوند.

## Homepage Hero — مهم‌ترین بخش

Hero باید Composition تبلیغاتی Premium داشته باشد و هر سه عنصر زیر را کنترل‌شده ترکیب کند:

1. متن واقعی HTML در مرکز/ناحیه اصلی محتوا
2. تصویر واقعی پزشک، دکتر کیوان علی‌پسندی
3. رندر Premium و تمیز ایمپلنت تک‌دندان

حضور تصویر پزشک در Hero هر دو نسخه Desktop 1440 و Mobile 390 قطعی و غیرقابل حذف است. تصویر پزشک را به سکشن پایین‌تر منتقل نکن و در Mobile آن را Hide نکن. نور و Background تصویر پزشک با `#101010` و Accent طلایی هماهنگ باشد، بدون متن Bake‌شده روی تصویر.

متن Hero دقیقاً:

- Eyebrow: «ایمپلنت تخصصی دندان»
- H1: «به جای خالی، عادت نکن.»
- Supporting line: «با برندهای معتبر جهانی»
- Primary CTA: «رزرو نوبت»
- Secondary CTA: «مشاهده خدمات»

Desktop: پزشک، متن و ایمپلنت سه ناحیه مشخص ولی یکپارچه بسازند؛ از Collage شلوغ خودداری کن. نقطه تمرکز اصلی H1 و CTA باشد و تصاویر با Crop کنترل‌شده آن را پشتیبانی کنند.

Mobile 390: Hero مستقل طراحی شود، نه نسخه Shrink‌شده Desktop. ترتیب ادراکی باید Text-first باشد؛ H1 و CTA اصلی در First View قابل مشاهده باشند، سپس Composition تصویری پزشک و ایمپلنت ادامه پیدا کند. پزشک همچنان واضح و قابل تشخیص باشد. CTA اصلی تمام‌عرض و Secondary CTA نیز واضح باشد. Header نباید فضای First View را بیش از حد اشغال کند.

برای Hero این مشخصات تصویری را Annotate کن:

- Doctor source crop: 1:1؛ Desktop display حدود 4:5 یا 1:1، Mobile display حدود 4:5 با Safe Area برای سر و شانه
- Implant render source: 4:5 یا 1:1 با Background تیره و فضای منفی کافی
- Hero تصاویر بدون متن، بدون Watermark و بدون صحنه جراحی شلوغ
- Crop focal point و Safe Area هر تصویر برای Desktop و Mobile مشخص شود

## Homepage Sections

ساختار محتوای فعلی حفظ شود و متن‌های اصلی از صفر بازنویسی نشوند. تمرکز روی Visual Design، spacing، typography، responsive behavior و کیفیت تصویر باشد.

### خدمات

فقط چهار کارت:

1. ایمپلنت دندان
2. روکش دندان
3. جراحی و لثه
4. درمان عمومی

Desktop چهار ستون، Tablet دو ستون، Mobile یک ستون. ارتفاع، Padding، محل آیکن و CTA در کارت‌ها یکسان باشد. آیکن‌ها Line Art ظریف مشکی/طلایی باشند. لینک هر کارت باید ظاهر یک لینک واقعی و واضح داشته باشد.

### درباره/اعتماد

سکشن زغالی با تیتر «تخصص، تجربه، اعتماد» و متن کوتاه. از پاراگراف طولانی استفاده نشود. آمار «سال تجربه» و «پرونده فعال» ممنوع است. «بیش از ۱۰٬۰۰۰ کیس درمانی» فقط به‌صورت Optional/Conditional Component طراحی شود و در Variant پیش‌فرض خاموش باشد تا پس از تأیید مستندات فعال شود. جایگزین پیش‌فرض می‌تواند «طرح درمان اختصاصی / برندهای معتبر / نوبت‌دهی آسان / پیگیری درمان» باشد.

### Footer

Footer زغالی، مرتب و کم‌ارتفاع باشد. اطلاعات ثابت:

- تلفن: `0920 144 1469`
- آدرس: «تهران، جردن، قبادیان غربی، پلاک ۱۱، واحد ۱۰»
- وب‌سایت: `drkeyvanalipasandi.com`

لینک‌های خدمات، FAQ و مقالات آموزشی در Footer قابل نمایش هستند. لینک گالری نمایش داده نشود.

## Template مشترک صفحات خدمات

تمام چهار Landing Page مستقل بمانند و از یک Template مشترک استفاده کنند. ترتیب Componentها:

1. Breadcrumb
2. Service Hero
3. معرفی کوتاه درمان
4. مزایا/کاربردها
5. مراحل درمان
6. مناسب چه افرادی است
7. Trust/Statistics
8. FAQ
9. CTA رزرو
10. Footer

در Layout دو ستونه Desktop متن سمت راست و تصویر سمت چپ باشد. در Mobile همیشه ابتدا متن و CTA و سپس تصویر قرار گیرد. Service Hero از رندر پزشکی تمیز و Premium استفاده کند، نه تصویر کارتونی یا صحنه جراحی شلوغ. برای هر Placeholder تصویری Aspect Ratio، Crop، Focal Point، Safe Area و پیشنهاد Desktop/Mobile را Annotate کن.

صفحه «ایمپلنت دندان» را به‌عنوان نمونه کامل طراحی کن و از متن و ساختار فعلی پروژه استفاده کن. سه صفحه دیگر فقط با Swap محتوا/تصویر از همین Template قابل ساخت باشند.

## Forms و States

برای Form Components حالت‌های Default، Focus، Error، Success، Loading و Disabled بساز. فرم رزرو ساده و سریع بماند و این جمله واضح و نزدیک Submit/Date-Time Step نمایش داده شود:

«تاریخ و ساعت انتخاب‌شده پیشنهادی است و نوبت پس از تماس کلینیک قطعی خواهد شد.»

در Mobile، CTA اصلی در بخش‌های مهم تمام‌عرض باشد.

## SEO، Accessibility و Performance Guardrails

طراحی باید امکان پیاده‌سازی Semantic و سبک در WordPress را حفظ کند:

- در هر صفحه فقط یک H1؛ Headingها بر اساس H2/H3 hierarchy، نه صرفاً اندازه ظاهری
- تمام متن‌ها از جمله Hero و FAQ متن واقعی باشند و داخل تصویر قرار نگیرند
- Breadcrumb قابل نمایش در صفحات داخلی
- Navigation و کارت خدمات به‌صورت لینک واقعی قابل پیاده‌سازی باشند
- FAQ در DOM قابل Crawl بماند حتی اگر Accordion باشد
- Contrast حالت‌های متن و CTA را حداقل در سطح WCAG AA بررسی و نتیجه را Annotate کن
- Focus Visible برای Link، Button، Menu و Form Control طراحی شود
- Motion کوتاه و محدود؛ بدون Slider سنگین، Hero video یا Parallax
- Layout ثابت با Aspect Ratio مشخص برای تصاویر تا CLS ایجاد نشود
- Hero باید با Image بهینه قابل پیاده‌سازی باشد و به Asset یا افکت سنگین وابسته نشود
- متن مفید پزشکی فدای مینیمال‌بودن نشود؛ محتوا در سکشن‌های کوتاه و قابل مرور سازمان‌دهی شود

## Components مورد انتظار

- Header/Desktop، Header/Mobile
- Primary Navigation item states
- Button: Gold / Outline Dark / Outline Light؛ Default/Hover/Focus/Disabled/Loading
- Mobile Menu Overlay: Closed/Open
- Hero/Home Desktop و Mobile
- Hero/Service Desktop و Mobile
- Section Heading
- Service Card و تمام states
- Benefit Card
- Treatment Step
- Trust Stat با Variant آمار تأییدشده On/Off
- Breadcrumb
- Accordion/FAQ Closed/Open/Focus
- Form Controls و Message states
- CTA Banner
- Footer Desktop/Mobile

## خروجی نهایی مورد انتظار

- صفحه Foundations/Tokens
- صفحه Component Library
- Homepage 1440px و 390px
- Implant Service Reference 1440px و 390px
- Prototype منوی موبایل
- Responsive annotations برای 1200، 768 و عرض‌های زیر 768
- Asset specification شامل Aspect Ratio، Crop، Safe Area و Export format پیشنهادی WebP/AVIF
- یک صفحه کوتاه Handoff Notes شامل رفتارهای Sticky Header، Mobile Overlay، Card reflow، CTA width و ترتیب متن/تصویر

قبل از طراحی سایر صفحات، همین Design System، Homepage و Service Reference برای تأیید ارائه شوند. هیچ صفحه اضافی یا Variation تزئینی خارج از این Scope تولید نکن.

---
