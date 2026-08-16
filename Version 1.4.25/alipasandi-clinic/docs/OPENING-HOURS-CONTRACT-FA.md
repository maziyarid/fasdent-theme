# Contract ساعات کاری — Release 1.4.25 / Plugin 1.3.0

Owner: **Alipasandi Service Content Plugin**. Timezone: **WordPress timezone**؛ Production contract = `Asia/Tehran`.

## Grammar

هر روز حداکثر یک خط:

```text
MO=09:00-13:00,14:00-18:00
TU=09:00-17:00
FR=CLOSED
```

روزها: `MO TU WE TH FR SA SU`. زمان: `HH:MM` 24-hour. Split hours با comma. `CLOSED` یعنی تعطیل. بازه overlap یا end<=start رد می‌شود. روز تکراری رد می‌شود.

Structured Data فقط از مقدار **validated/canonicalized** به `OpeningHoursSpecification` تبدیل می‌شود؛ textarea خام مستقیم وارد Schema نمی‌شود. اگر ساعات رسمی خالی باشد، Schema ساعات کاری را حدس نمی‌زند. Appointment time نیز Availability قطعی نیست؛ فقط وقتی ساعات رسمی تنظیم شده باشد، Server روز/ساعت خارج از Schedule را رد می‌کند.
