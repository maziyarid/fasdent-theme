import Breadcrumb from '../components/Breadcrumb'

const sections = [
  {
    title: '۱. جمع‌آوری اطلاعات',
    text: 'ما اطلاعاتی که شما مستقیماً در اختیار ما می‌گذارید مانند نام، شماره تماس، ایمیل و اطلاعات پزشکی را جمع‌آوری می‌کنیم. همچنین برخی اطلاعات مرور وبسایت به صورت خودکار ثبت می‌شود.',
  },
  {
    title: '۲. استفاده از اطلاعات',
    text: 'اطلاعات شما برای ارائه خدمات دندانپزشکی، رزرو نوبت، ارتباط با شما، و بهبود کیفیت خدمات استفاده می‌شود. اطلاعات پزشکی شما محرمانه است.',
  },
  {
    title: '۳. اشتراک‌گذاری اطلاعات',
    text: 'ما اطلاعات شخصی شما را بدون رضایت صریح شما به اشخاص ثالث نمی‌فروشیم. در موارد ضروری مانند ارجاع به متخصص، با اجازه شما اطلاعات منتقل می‌شود.',
  },
  {
    title: '۴. امنیت اطلاعات',
    text: 'ما از اقدامات امنیتی مناسب برای حفاظت از اطلاعات شخصی شما استفاده می‌کنیم. اطلاعات پزشکی شما در سیستم‌های امن ذخیره می‌شود.',
  },
  {
    title: '۵. کوکی‌ها',
    text: 'وبسایت ما از کوکی‌ها برای بهبود تجربه کاربری استفاده می‌کند. می‌توانید تنظیمات مرورگر خود را برای مدیریت کوکی‌ها تغییر دهید.',
  },
  {
    title: '۶. حقوق شما',
    text: 'شما حق دارید به اطلاعات خود دسترسی داشته باشید، آن را اصلاح کنید، یا درخواست حذف دهید. برای این موارد با ما تماس بگیرید.',
  },
  {
    title: '۷. تغییرات سیاست',
    text: 'ما ممکن است این سیاست را به‌روز کنیم. تغییرات مهم از طریق وبسایت یا ایمیل اطلاع‌رسانی خواهند شد.',
  },
]

export default function PrivacyPolicy() {
  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-12">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'سیاست حریم خصوصی' }]} />
          <h1 className="text-3xl md:text-4xl font-black mt-4">سیاست حریم خصوصی</h1>
          <p className="text-white/70 mt-2 text-sm">آخرین بازبینی: آبان ۱۴۰۳</p>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 py-12 space-y-4">
        <div className="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-blue-700 text-sm leading-relaxed">
          حریم خصوصی شما برای ما اهمیت بسیار زیادی دارد. این سیاست توضیح می‌دهد که چگونه اطلاعات شخصی شما را جمع‌آوری، استفاده و محافظت می‌کنیم.
        </div>
        {sections.map(section => (
          <div key={section.title} className="bg-white rounded-2xl p-6 shadow-sm">
            <h2 className="font-bold text-[#071F3F] text-lg mb-3">{section.title}</h2>
            <p className="text-gray-600 leading-relaxed text-sm">{section.text}</p>
          </div>
        ))}
        <div className="bg-white rounded-2xl p-6 shadow-sm">
          <h2 className="font-bold text-[#071F3F] text-lg mb-3">۸. تماس با ما</h2>
          <p className="text-gray-600 text-sm leading-relaxed mb-3">برای سوالات درباره حریم خصوصی با ما تماس بگیرید:</p>
          <div className="space-y-2 text-sm text-gray-600">
            <p>📧 <a href="mailto:privacy@fasdent.ir" className="text-[#0D54AF] hover:text-[#08CBCD] ltr">privacy@fasdent.ir</a></p>
            <p>📞 <a href="tel:+982188888888" className="text-[#0D54AF] hover:text-[#08CBCD] ltr">۰۲۱-۸۸۸۸۸۸۸۸</a></p>
            <p>📍 تهران، خیابان ولیعصر، بالاتر از میدان ونک</p>
          </div>
        </div>
      </div>
    </div>
  )
}
