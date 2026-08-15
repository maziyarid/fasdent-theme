# Medical Claim Register — Release 1.4.28 / Plugin 1.3.3

Status: **NOT APPROVED / Doctor + Clinic Owner sign-off required before Production Freeze.**

این Register فقط Service Meta نیست؛ **Home hard-coded copy، Services Hub، Service Meta/legacy fallback، About، FAQ و approved Posts** همگی باید در Freeze نهایی پوشش داده شوند. Database/rendered content مرجع نهایی است.

## Known claims already present in current Theme source

| Surface | Claim / phrase | Type | Current decision | Required evidence/reviewer |
|---|---|---|---|---|
| Home hero | «ایمپلنت تخصصی دندان» | expertise/scope | PENDING | Doctor sign-off |
| Home hero | «با برندهای معتبر جهانی» | quality/comparative | PENDING | Owner confirms brands actually used + Doctor review |
| Home hero | «عملکرد و ظاهر طبیعی را بازسازی کند» | outcome | PENDING | Doctor review; keep conditional wording |
| Home service card | «بدون تراش دندان‌های مجاور» | treatment/comparative | PENDING | Doctor review; verify applicability/context |
| Home service card | «ترکیبی از استحکام و ظاهر طبیعی» | outcome/material | PENDING | Doctor review |
| Home service card | «با رویکرد تخصصی» | expertise | PENDING | Doctor review |
| Home trust | «تجهیزات به‌روز» / «افزایش دقت درمان» | quality/outcome | PENDING | Owner inventory + Doctor review |
| Home trust | «مواد و برندهای معتبر» | quality | PENDING | Owner approval/source |
| Home heading | «راهکارهای تخصصی برای لبخندی ماندگار» | expertise/longevity | PENDING | Doctor review |
| Services Hub implant | «جراحی تخصصی» | expertise | PENDING | Doctor review |
| Services Hub implant | «بدون تراش دندان مجاور» | treatment/comparative | PENDING | Doctor review |
| Services Hub implant | «برندهای معتبر» | quality | PENDING | Owner approval/source |
| Services Hub crown | «استحکام مناسب» / «نزدیک به دندان طبیعی» | outcome/material | PENDING | Doctor review |
| Services Hub surgery | «با رویکرد حفظ بافت» | treatment/outcome | PENDING | Doctor review |
| Conditional Home metric | «بیش از ۱۰٬۰۰۰ کیس درمانی تأییدشده» | numerical | **OFF / NOT VERIFIED** | Must remain disabled until documented evidence + owner/doctor sign-off |

## Freeze procedure

1. Export final Service JSON from the exact Production-candidate database and store its SHA-256.
2. Snapshot/version Home + Services Hub hard-coded copy from the exact Theme hash.
3. Crawl/render final Home, Services Hub, 4 Services, About, FAQ and approved Posts.
4. Add every numerical, comparative, outcome, expertise, safety, duration, longevity, candidacy and superlative statement to this register.
5. Doctor records `APPROVED / REWRITE / REMOVE` and review date for every row.
6. Store doctor sign-off, final content snapshots and artifact hashes together.
7. `+10,000` remains OFF until its evidence row is explicitly APPROVED.

Do not mark this register PASS from source ZIP alone; rendered DB content may differ from source/legacy content.
