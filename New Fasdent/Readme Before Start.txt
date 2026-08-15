Hi. Please help me satisfy the client's needs based on the requirements. I'll now provide the client's final feedback. Please read it carefully, analyse the files, rewrite the theme/plugin completely, and provide a zip file of the new version. Moreover, give me a report in Markdown format like the ones I have shared so far. Think systematically, use a tree-of-thought approach, and diagnose using circular reasoning. For all things you do, have hypotheses; consider all of them, experiment with them, and find the best possible solution and output perfection.







Last comment of the client:



سلام، ممنون. Release 1.4.28 / Plugin 1.3.3 این بار با Extract و Code Review مستقیم Theme، Plugin، QA Pack، Manifest و Sourceهای اجرایی بررسی شد.



ابتدا نتیجه مثبت:



WP-CLI blocker نسخه 1.3.2 واقعاً اصلاح شده است.



WP_CLI::add_command() اکنون صحیح است و Hardening جدید Schema Repair نیز از نظر Source جهت درستی دارد:



Production Apply block



Staging confirmed dry-run plan_sha256



Full-plan PASS requirement



pre-write revalidation



payload hash drift protection



sanitizer drift protection



post-write verification



best-effort rollback



CLI-only Apply



Rank Math observation freshness/version، static front-page Health و Production Debug guard نیز واقعاً اضافه شده‌اند.



Artifact hashes نیز با Manifest برابرند:



Theme 1.4.28:



1e07600cadd4bbb50de2edd7cc1e2e8379d76fdfcef980c3f1718ea1976a8100



Plugin 1.3.3:



bf01b35e02abecc6e64800f851e442761e70d8bf3348a3775fcac88df9d4768f



با این حال قبل از ورود به Runtime Evidence چند Code-level contract دیگر پیدا شد که ترجیح می‌دهیم همین حالا بسته شوند.



1. Homepage NAP hard-coded fallback باید حذف شود — HIGH



در front-page.php هنوز برای clinic_street fallback مستقیم:



ستارخان، امیراد ۱، طبقه ۵



وجود دارد.



این با SSOT contract فعلی مغایر است.



در حالت clinic_street خالی ممکن است:



Home → آدرس نمایش دهد Contact → آدرس خالی باشد Schema → streetAddress خالی باشد



و NAP داخل خود سایت Drift کند.



حتی اگر این آدرس صحیح باشد، Operational data نباید دو Source داشته باشد.



لطفاً hard-coded Street fallback از Template حذف شود.



Home فقط SSOT Plugin را بخواند.



Missing data → fail-safe/non-operational UI؛ نه نمایش مقدار Template.



2. Operational Health باید clinic_street را Critical validate کند — HIGH SEO



Health فعلی clinic_address را بررسی می‌کند ولی Rank Math streetAddress را از clinic_street می‌سازد.



بنابراین امکان دارد Health از نظر Address Pass شود ولی Schema:



streetAddress=""



داشته باشد.



لطفاً:



street_missing



به Operational Health اضافه شود.



همچنین Local Schema readiness و Health بهتر است از Contract مشترک استفاده کنند.



3. Partial/invalid Local PostalAddress نباید silently emit شود



Rank Math integration فعلی address object را ایجاد می‌کند حتی اگر clinic_street خالی باشد.



لطفاً policy مشخص شود:



Local NAP کامل → validated PostalAddress



Required NAP ناقص → Health Critical + schema fail-safe



نه Schema نیمه‌کاره با empty required field.



4. Booking Slots ↔️ Opening Hours cross-validation لازم است — HIGH



Booking Slots و Opening Hours هرکدام جداگانه validate می‌شوند، اما interoperability آنها Health checked نیست.



مثال:



Slots:



20:00



Opening:



MO=09:00-17:00



هر دو setting به‌تنهایی valid هستند ولی هیچ Monday slot قابل درخواست نیست.



اگر هیچ Slot در هیچ روز Open داخل Opening Hours قرار نگیرد، Form technically ready است اما عملاً unusable.



لطفاً helper/Health contract اضافه شود:



حداقل یک configured booking slot باید با حداقل یک open range overlap داشته باشد.



zero usable slots across weekly schedule = Critical.



open day بدون slot می‌تواند Warning باشد مگر Business Rule خلاف آن را بخواهد.



5. Form fail-safe و Phone route



Form readiness در حال حاضر Mail configuration را enforce می‌کند، ولی وجود Phone معتبر شرط interactive form نیست.



در صورت mail provider failure، UI کاربر را به تماس مستقیم ارجاع می‌دهد؛ بنابراین Production ideally باید یک Direct Phone route معتبر نیز داشته باشد.



لطفاً یکی از دو Contract را انتخاب کنید:



A) valid operational Phone جزء Form readiness باشد.



یا



B) Error copy فقط وقتی Phone موجود است direct-contact instruction بدهد و در حالت بدون Phone alternative مناسب داشته باشد.



ما Error pathی نمی‌خواهیم که بگوید «تماس بگیرید» ولی شماره‌ای وجود نداشته باشد.



6. NAP readiness helper مشترک



برای جلوگیری از Driftهای بعدی پیشنهاد می‌کنیم یک helper مشترک داشته باشیم که Required Local NAP را تعریف کند:



business_name clinic_street clinic_city clinic_region clinic_country



و هر field دیگری که برای Local Entity Production لازم است.



Operational Health و Rank Math integration هر دو از همین contract استفاده کنند.



7. Rank Math Health freshness و deployment determinism



Health observation اکنون version-aware + timestamp-aware شده که اصلاح خوبی است.



اما observation بعد 15 دقیقه stale می‌شود و فقط با render شدن Front/Contact refresh می‌شود.







بنابراین wp alipasandi service health ممکن است صرفاً به دلیل نبود Frontend request اخیر fail کند.



لطفاً Runbook قبل Health این probe را صریح کند:



request Front Page



request Contact



سپس Health



یا یک explicit local-entity observation probe اضافه شود.



Health deployment نباید به Traffic تصادفی وابسته باشد.



8. Rank Math Graph runtime



Front و Contact هر دو باید observation matched برای exact Rank Math 1.0.276 داشته باشند.



علاوه بر state, Evidence:



context observed_at rank_math_version



ثبت شود.



9. Schema Repair Human-readable Diff



Payload SHA حفاظت بسیار خوبی است.



اما در Staging Repair Evidence برای هر Service علاوه بر SHA، snapshot human-readable قبل/بعد لازم است:



H1 Intro FAQ CTA Image ID/ALT Internal links



هدف Audit پزشکی/محتوایی است.



10. Schema Repair environment



Apply فقط روی isolated Clone مجاز است.



Clone باید صریحاً:



WP_ENVIRONMENT_TYPE=staging



داشته باشد.



صرف اینکه production نباشد به‌عنوان procedure کافی نیست.



11. Opening Hours ↔️ Booking UX



date-aware UI فعلی PASS Source است.



Cross-validator بالا باید همان Contract را در Health reflect کند تا UI به وضعیتی نرسد که هیچ Date قابل استفاده‌ای نداشته باشد.



12. Owner-approved NAP Freeze



قبل هر Rank Math/Form runtime evidence:



Business name Display phone E.164 Display address Schema street City Region Country Postal Geo Map Notify Email Mail From Mail From Name Booking Slots Opening Hours Social URLs



Freeze و sign-off شود.



clinic_address و clinic_street دو field متفاوت‌اند؛ Guide باید تفاوتشان و الزام Update همزمان در location change را توضیح دهد.



13. Home/Contact/Footer NAP consistency test



بعد از NAP Freeze exact visible values در:



Home Contact Footer Appointment mail body Rank Math JSON-LD



Diff شوند.



Location/phone mismatch = FAIL.



14. Production environment blockers هنوز بدون تغییر باقی‌اند



موارد زیر همچنان Hard Gate هستند و Code Review آنها را PASS نمی‌کند:



PHP 8.2 real runtime WordPress 6.4 real runtime Production-identical WP 7.0.4 / PHP 8.5.7 smoke actual WP-CLI PHPUnit WPCS/PHPCS PHPStan/deprecated scan Rank Math active/inactive/reactivated SMTP/SPF/DKIM/DMARC/inbox Production Debug hardening Loopback 503/WP-Cron Backup/Restore Host redirects/canonical/indexability Security headers Cache/post_modified/lastmod CWV/browser/A11y/Admin RTL Privacy/Claims/Asset approvals



15. Production WP_DEBUG



Production snapshot قبلی هنوز:



WP_DEBUG=true



WP_DEBUG_DISPLAY=true



WP_DEBUG_LOG=true



داشته است.



WP_DEBUG_DISPLAY=false قبل Freeze اجباری است.



Debug log در صورت حفظ باید private + retention/rotation + PII review داشته باشد.



16. Production schema-invalid Services



چهار Service Production snapshot قبلی هنوز نیازمند Staging-only repair evidence هستند.



Apply روی Live ممنوع باقی بماند.



17. Rank Math



Final Owner باید Active باشد.



Current deactivated state برای Production Final قابل قبول نیست.



18. Loopback/WP-Cron



HTTP 503 باید قبل Freeze توسط Hosting حل شود.



19. Horizon/Lead



Horizon 365 و Lead 0 هنوز Owner approval می‌خواهند.



20. Claims



اصطلاحاتی مثل:



«ایمپلنت تخصصی»



«برندهای معتبر جهانی»



«جراحی تخصصی»



«بدون تراش دندان‌های مجاور»



و هر ادعای Outcome/Comparative باید وارد Medical Claim Register نهایی شوند.



+10,000 تا approval خاموش بماند.



21. Build decision



از نظر ما 1.4.28 دیگر WP-CLI blocker نسخه قبل را ندارد.



اما موارد 1 تا 6 بالا Code-level هستند.



اگر اصلاح شوند:



Version/Hash جدید لازم است و Runtime Evidence باید روی Build جدید جمع‌آوری شود.



ترجیح ما این است که قبل از صرف زمان برای Staging QA طولانی، این Contractهای کوچک بسته شوند.



پیشنهاد Release بعدی:



Theme 1.4.29 / Plugin 1.3.4



اگر Plugin فقط Health/Schema contract و Theme فقط Home fallback تغییر می‌کنند.



بعد از آن دیگر هیچ تغییر نظری جدید نمی‌خواهیم مگر Code Review defect واقعی جدید پیدا کند.



وضعیت فعلی



Artifact Integrity = PASS Previous WP-CLI Blocker = FIXED Schema Repair Safety = PASS SOURCE Booking Date/Time = PASS SOURCE Stable Service Key = PASS SOURCE Emergency SEO Fallback = PASS SOURCE NAP Single Source = PASS WITH REMAINING DRIFT Local Schema Health Contract = NEEDS PATCH Booking/Open-hours Cross Contract = NEEDS PATCH Runtime = NOT PROVEN Production Approval = NO



لطفاً ابتدا موارد Code-level بالا را پاسخ/اصلاح کنید.







اگر Code تغییر کرد Build جدید بدهید؛ سپس همان exact hash جدید روی Staging Deploy و Runtime Evidence اجرا شود











Please read carefully, then respond to all the needs without any omissions. Think systematically, use a tree of thoughts, and diagnose using circular reasoning. Everything you do has hypotheses; consider all of them, experiment with them, find the best possible solution and output perfection.







At the end, compare yours with Qwen's reasoning; the things I trust most are inaccurate, but catch the idea and apply it to the theme.