import Breadcrumb from '../components/Breadcrumb'

export default function MedicalDisclaimer() {
  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-12">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'سلب مسئولیت پزشکی' }]} />
          <h1 className="text-3xl md:text-4xl font-black mt-4">سلب مسئولیت پزشکی</h1>
        </div>
      </div>
      <div className="max-w-4xl mx-auto px-4 py-12">
        <div className="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-8 flex items-start gap-3">
          <svg className="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
          <p className="text-amber-700 text-sm leading-relaxed">
            اطلاعات ارائه شده در این وبسایت صرفاً برای اهداف آموزشی و اطلاعاتی است و جایگزین مشاوره، تشخیص یا درمان پزشکی حرفه‌ای نمی‌شود.
          </p>
        </div>

        <div className="prose max-w-none space-y-6 text-gray-700">
          {[
            {
              title: '۱. هدف اطلاعات',
              text: 'محتوای این وبسایت صرفاً برای ارائه اطلاعات عمومی درباره خدمات دندانپزشکی است. این اطلاعات جایگزین مشاوره پزشکی شخصی نمی‌شود.'
            },
            {
              title: '۲. ضرورت مشاوره پزشکی',
              text: 'قبل از هرگونه تصمیم‌گیری درباره درمان دندانپزشکی، حتماً با یک دندانپزشک مجاز و واجد شرایط مشورت کنید. وضعیت هر بیمار منحصربه‌فرد است.'
            },
            {
              title: '۳. دقت اطلاعات',
              text: 'ما تلاش می‌کنیم اطلاعات دقیق و به‌روز ارائه دهیم، اما تضمینی درباره کامل بودن یا صحت اطلاعات نمی‌دهیم.'
            },
            {
              title: '۴. نتایج فردی',
              text: 'نتایج درمان‌های دندانپزشکی برای هر بیمار متفاوت است. نمونه‌های نشان داده شده نمایانگر نتایج معمول نیستند.'
            },
            {
              title: '۵. اورژانس پزشکی',
              text: 'در صورت اورژانس دندانپزشکی یا پزشکی، فوراً با اورژانس یا پزشک خود تماس بگیرید. با ما از طریق ۰۲۱-۸۸۸۸۸۸۸۸ تماس بگیرید.'
            },
          ].map(section => (
            <div key={section.title} className="bg-white rounded-2xl p-6 shadow-sm">
              <h2 className="text-xl font-bold text-[#071F3F] mb-3">{section.title}</h2>
              <p className="leading-relaxed">{section.text}</p>
            </div>
          ))}
        </div>

        <div className="mt-8 text-center text-gray-500 text-sm">
          <p>آخرین بازبینی: آبان ۱۴۰۳</p>
        </div>
      </div>
    </div>
  )
}
