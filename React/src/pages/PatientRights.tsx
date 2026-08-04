import Breadcrumb from '../components/Breadcrumb'

const rights = [
  {
    icon: '🏥',
    title: 'حق دریافت اطلاعات',
    items: [
      'دریافت اطلاعات کامل درباره وضعیت دندانی خود',
      'آگاهی از گزینه‌های درمانی موجود',
      'اطلاع از هزینه‌های درمان قبل از شروع',
      'دسترسی به پرونده پزشکی خود',
    ],
  },
  {
    icon: '✅',
    title: 'حق رضایت آگاهانه',
    items: [
      'تصمیم‌گیری درباره درمان خود پس از دریافت توضیحات کامل',
      'امتناع از هر درمانی که نپذیرفته‌اید',
      'درخواست نظر دوم از پزشک دیگر',
      'تایید کتبی قبل از درمان‌های پیچیده',
    ],
  },
  {
    icon: '🔒',
    title: 'حق محرمانگی',
    items: [
      'محرمانه ماندن اطلاعات پزشکی شما',
      'عدم افشای اطلاعات به اشخاص ثالث بدون اجازه',
      'حفاظت از داده‌های شخصی',
      'حق اصلاح اطلاعات نادرست',
    ],
  },
  {
    icon: '⚕️',
    title: 'حق درمان با کیفیت',
    items: [
      'دریافت خدمات حرفه‌ای با بالاترین استاندارد',
      'استفاده از تجهیزات استریل و بهداشتی',
      'رعایت پروتکل‌های بهداشتی',
      'درمان با محصولات استاندارد تأیید شده',
    ],
  },
  {
    icon: '🤝',
    title: 'حق احترام و کرامت',
    items: [
      'برخورد محترمانه و انسانی از تیم کلینیک',
      'عدم تبعیض در ارائه خدمات',
      'حق ابراز نگرانی‌ها بدون ترس از تلافی',
      'شنیده شدن سوالات و نگرانی‌ها',
    ],
  },
  {
    icon: '💬',
    title: 'حق شکایت',
    items: [
      'ارائه بازخورد و شکایت درباره خدمات',
      'پیگیری شکایت در اسرع وقت',
      'مراجعه به سازمان نظام پزشکی در صورت نیاز',
      'جبران خسارت در موارد قصور',
    ],
  },
]

export default function PatientRights() {
  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-12">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'حقوق بیمار' }]} />
          <h1 className="text-3xl md:text-4xl font-black mt-4 mb-2">منشور حقوق بیمار</h1>
          <p className="text-white/80">احترام به حقوق شما اولویت اول کلینیک دکتر علی‌پسندی است</p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="bg-gradient-to-l from-[#E8F4FD] to-[#E0F7F7] border border-[#08CBCD]/30 rounded-2xl p-6 mb-8 text-center">
          <div className="text-4xl mb-3">⚕️</div>
          <h2 className="text-xl font-bold text-[#071F3F] mb-2">تعهد ما به شما</h2>
          <p className="text-gray-600 max-w-2xl mx-auto">کلینیک دندانپزشکی دکتر کیوان علی‌پسندی متعهد است که در تمام مراحل درمان، حقوق بیماران را محترم بشمارد و بالاترین استانداردهای مراقبت پزشکی را رعایت کند.</p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {rights.map(right => (
            <div key={right.title} className="bg-white rounded-2xl p-6 shadow-sm">
              <div className="text-4xl mb-4">{right.icon}</div>
              <h3 className="font-bold text-[#071F3F] text-lg mb-4">{right.title}</h3>
              <ul className="space-y-2">
                {right.items.map(item => (
                  <li key={item} className="flex items-start gap-2 text-sm text-gray-600">
                    <svg className="w-4 h-4 text-[#08CBCD] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" /></svg>
                    {item}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-8 bg-[#071F3F] rounded-2xl p-6 text-white text-center">
          <h3 className="font-bold text-xl mb-2">انتقاد یا پیشنهاد دارید؟</h3>
          <p className="text-white/70 text-sm mb-4">نظرات شما به ما کمک می‌کند تا خدمات بهتری ارائه دهیم</p>
          <a href="mailto:rights@fasdent.ir" className="inline-block bg-[#08CBCD] text-[#071F3F] font-bold px-6 py-3 rounded-xl hover:opacity-90 transition-opacity">
            ارسال بازخورد
          </a>
        </div>
      </div>
    </div>
  )
}
