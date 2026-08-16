# Contract ساعات کاری — Release 1.4.29 / Plugin 1.3.4

Owner: **Alipasandi Service Content Plugin**. Timezone: **WordPress timezone تأییدشده توسط Clinic Owner**؛ کد هیچ timezone مستقل یا hard-coded استفاده نمی‌کند.

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

**تعطیلات مناسبتی، تعطیلی موقت، override تاریخ خاص، vacation، و SpecialOpeningHoursSpecification در 1.3.1 پشتیبانی نمی‌شوند.** این موارد فقط در Feature جداگانه و Versioned Release آینده اضافه می‌شوند؛ تا آن زمان نباید با تغییر مصنوعی Grammar هفتگی شبیه‌سازی شوند.
