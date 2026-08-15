# Contract ساعات کاری — Release 1.4.28 / Plugin 1.3.3

Owner: **Alipasandi Service Content Plugin**. Timezone: **WordPress timezone**؛ Production contract = `Asia/Tehran`.

## Grammar رسمی

هر روز حداکثر یک خط و فقط با day-codeهای زیر تعریف می‌شود:

| Code | Day |
|---|---|
| `MO` | Monday |
| `TU` | Tuesday |
| `WE` | Wednesday |
| `TH` | Thursday |
| `FR` | Friday |
| `SA` | Saturday |
| `SU` | Sunday |

نمونه split-hours:

```text
MO=09:00-13:00,14:00-18:00
```

نمونه روز تعطیل:

```text
FR=CLOSED
```

زمان فقط `HH:MM` در فرمت 24 ساعته است. چند بازه با comma جدا می‌شوند. `CLOSED` یعنی آن روز هیچ بازه فعالی ندارد. روز تکراری، بازه overlap، ساعت نامعتبر، و `end <= start` رد می‌شوند.

## Structured Data

فقط مقدار **validated + canonicalized** به `OpeningHoursSpecification` تبدیل می‌شود. مقدار raw textarea هیچ‌گاه مستقیم وارد JSON-LD نمی‌شود. `CLOSED` هیچ `OpeningHoursSpecification` جعلی تولید نمی‌کند. Split ranges به چند Specification مستقل برای همان `dayOfWeek` تبدیل می‌شوند.

اگر setting خالی باشد، Schema ساعات کاری را حدس نمی‌زند و `openingHoursSpecification` حذف می‌شود.

## Appointment request

Booking time به معنی Availability قطعی نیست؛ فقط «زمان پیشنهادی درخواست نوبت» است. اگر ساعات رسمی تنظیم شده باشد، Server روز `CLOSED` و time خارج بازه رسمی را رد می‌کند. اگر ساعات رسمی خالی باشد، سیستم Availability را ادعا/حدس نمی‌زند و صرفاً configured proposal times را به‌عنوان درخواست دریافت می‌کند.

## Temporary / holiday closure

**تعطیلات مناسبتی، تعطیلی موقت، override تاریخ خاص، vacation، و SpecialOpeningHoursSpecification در 1.3.3 پشتیبانی نمی‌شوند.** این موارد فقط در Feature جداگانه و Versioned Release آینده اضافه می‌شوند؛ تا آن زمان نباید با تغییر مصنوعی Grammar هفتگی شبیه‌سازی شوند.


## Lead-time و Date-aware UI

UI بعد از انتخاب Date فقط proposal slotهایی را قابل انتخاب نگه می‌دارد که (۱) در weekly opening-hours همان weekday باشند، و (۲) نسبت به current WordPress-timezone clock + versioned lead-time آینده باشند. این filtering فقط UX است؛ Server-side validation authoritative باقی می‌ماند. اگر JavaScript غیرفعال باشد همه proposal times قابل مشاهده‌اند و Server validation در Submit اعمال می‌شود.

Current lead-time candidate = **0 minutes**: same-day request مجاز است، ولی selected datetime باید strictly greater than current WordPress datetime باشد. Buffer مثبت (مثلاً 30/60 دقیقه) Business Rule جدید و نیازمند Owner approval + release/hash جدید است.
