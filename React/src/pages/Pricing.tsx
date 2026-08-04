import { useState } from 'react'
import { Link } from 'react-router'
import Breadcrumb from '../components/Breadcrumb'

const pricingData = [
  {
    category: 'ایمپلنت دندان',
    icon: '🦷',
    items: [
      { name: 'ایمپلنت تکی (ایمپلنت + کراون)', price: 'از ۱۲,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'All-on-4 (یک فک)', price: 'از ۴۵,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'All-on-6 (یک فک)', price: 'از ۵۸,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'پیوند استخوان (در صورت نیاز)', price: 'از ۸,۰۰۰,۰۰۰', unit: 'تومان' },
    ],
  },
  {
    category: 'ارتودنسی',
    icon: '😁',
    items: [
      { name: 'ارتودنسی ثابت فلزی', price: 'از ۱۸,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'ارتودنسی ثابت سرامیکی', price: 'از ۲۴,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'الاینر شفاف (کل دوره)', price: 'از ۳۵,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'رتاینر (نگهدارنده)', price: 'از ۲,۵۰۰,۰۰۰', unit: 'تومان' },
    ],
  },
  {
    category: 'دندانپزشکی زیبایی',
    icon: '✨',
    items: [
      { name: 'بلیچینگ حرفه‌ای (هر فک)', price: 'از ۳,۵۰۰,۰۰۰', unit: 'تومان' },
      { name: 'لمینت سرامیکی (هر دندان)', price: 'از ۶,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'طراحی لبخند (بسته کامل)', price: 'از ۲۰,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'کامپوزیت ونیر (هر دندان)', price: 'از ۲,۵۰۰,۰۰۰', unit: 'تومان' },
    ],
  },
  {
    category: 'پروتز دندان',
    icon: '💎',
    items: [
      { name: 'کراون زیرکونیا (هر دندان)', price: 'از ۸,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'کراون سرامیک فلز (هر دندان)', price: 'از ۴,۵۰۰,۰۰۰', unit: 'تومان' },
      { name: 'پل دندانی سه واحده', price: 'از ۱۲,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'پروتز کامل متحرک (یک فک)', price: 'از ۷,۰۰۰,۰۰۰', unit: 'تومان' },
    ],
  },
  {
    category: 'دندانپزشکی عمومی',
    icon: '🏥',
    items: [
      { name: 'معاینه و چکاپ کامل', price: '۵۰۰,۰۰۰', unit: 'تومان' },
      { name: 'جرم‌گیری و پالیش', price: 'از ۱,۵۰۰,۰۰۰', unit: 'تومان' },
      { name: 'پر کردن کامپوزیت (یک سطح)', price: 'از ۲,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'کشیدن دندان ساده', price: 'از ۱,۰۰۰,۰۰۰', unit: 'تومان' },
    ],
  },
  {
    category: 'درمان‌های تخصصی',
    icon: '🔬',
    items: [
      { name: 'درمان ریشه (تک کانال)', price: 'از ۳,۵۰۰,۰۰۰', unit: 'تومان' },
      { name: 'درمان ریشه (چند کانال)', price: 'از ۵,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'کشیدن دندان عقل جراحی', price: 'از ۳,۰۰۰,۰۰۰', unit: 'تومان' },
      { name: 'پیوند لثه', price: 'از ۶,۰۰۰,۰۰۰', unit: 'تومان' },
    ],
  },
]

const packages = [
  {
    title: 'پکیج مراقبت پایه',
    price: '۳,۵۰۰,۰۰۰',
    period: 'سالانه',
    color: 'from-gray-600 to-gray-800',
    features: ['معاینه سالانه (۲ بار)', 'جرم‌گیری (۲ بار)', 'رادیوگرافی (۱ بار)', 'مشاوره رایگان'],
    popular: false,
  },
  {
    title: 'پکیج مراقبت جامع',
    price: '۶,۵۰۰,۰۰۰',
    period: 'سالانه',
    color: 'from-[#0D54AF] to-[#08CBCD]',
    features: ['معاینه سالانه (۴ بار)', 'جرم‌گیری (۴ بار)', 'رادیوگرافی کامل', 'مشاوره نامحدود', 'فلوراید تراپی', 'اولویت پذیرش'],
    popular: true,
  },
  {
    title: 'پکیج VIP',
    price: '۱۲,۰۰۰,۰۰۰',
    period: 'سالانه',
    color: 'from-[#071F3F] to-[#0D54AF]',
    features: ['همه موارد جامع', 'CT-Scan رایگان', 'بلیچینگ سالانه', 'تخفیف ۱۰٪ درمان‌ها', 'پذیرش اورژانس ۲۴ساعته', 'مدیر درمان اختصاصی'],
    popular: false,
  },
]

export default function Pricing() {
  const [activeCategory, setActiveCategory] = useState<string | null>(null)

  const filtered = activeCategory
    ? pricingData.filter(p => p.category === activeCategory)
    : pricingData

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'قیمت‌ها' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-4">تعرفه خدمات</h1>
          <p className="text-white/80 text-lg max-w-2xl">قیمت‌های شفاف و منصفانه برای بهترین کیفیت درمان</p>
        </div>
      </div>

      {/* Notice */}
      <div className="bg-amber-50 border-b border-amber-200 py-3 px-4">
        <div className="max-w-7xl mx-auto flex items-center gap-2 text-amber-700 text-sm">
          <svg className="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          قیمت‌ها بر اساس وضعیت هر بیمار متغیر است. برای استعلام دقیق قیمت، لطفاً با ما تماس بگیرید یا نوبت مشاوره رزرو کنید.
        </div>
      </div>

      {/* Category filter */}
      <section className="py-8 bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex flex-wrap gap-2">
            <button
              onClick={() => setActiveCategory(null)}
              className={`px-4 py-2 rounded-xl text-sm font-medium transition-all ${!activeCategory ? 'bg-[#0D54AF] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
            >
              همه خدمات
            </button>
            {pricingData.map(p => (
              <button
                key={p.category}
                onClick={() => setActiveCategory(p.category === activeCategory ? null : p.category)}
                className={`px-4 py-2 rounded-xl text-sm font-medium transition-all ${activeCategory === p.category ? 'bg-[#0D54AF] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
              >
                {p.icon} {p.category}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Price tables */}
      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-6">
            {filtered.map(cat => (
              <div key={cat.category} className="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] px-5 py-4 flex items-center gap-3">
                  <span className="text-2xl">{cat.icon}</span>
                  <h2 className="text-white font-bold text-lg">{cat.category}</h2>
                </div>
                <table className="w-full">
                  <tbody>
                    {cat.items.map((item, i) => (
                      <tr key={i} className={i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                        <td className="px-5 py-3 text-sm text-gray-700">{item.name}</td>
                        <td className="px-5 py-3 text-sm font-bold text-[#0D54AF] text-left ltr whitespace-nowrap">
                          {item.price} {item.unit}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Packages */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">پکیج‌های مراقبتی سالانه</h2>
            <p className="text-gray-500">با خرید پکیج سالانه از مزایای بیشتری بهره‌مند شوید</p>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {packages.map(pkg => (
              <div key={pkg.title} className={`rounded-2xl overflow-hidden shadow-lg ${pkg.popular ? 'ring-4 ring-[#08CBCD] scale-105' : ''}`}>
                {pkg.popular && (
                  <div className="bg-[#08CBCD] text-white text-center py-2 text-sm font-bold">
                    محبوب‌ترین
                  </div>
                )}
                <div className={`bg-gradient-to-br ${pkg.color} text-white p-6`}>
                  <h3 className="text-xl font-bold mb-2">{pkg.title}</h3>
                  <div className="flex items-baseline gap-2">
                    <span className="text-3xl font-black">{pkg.price}</span>
                    <span className="text-sm opacity-80">تومان / {pkg.period}</span>
                  </div>
                </div>
                <div className="p-6 bg-white">
                  <ul className="space-y-3 mb-6">
                    {pkg.features.map(f => (
                      <li key={f} className="flex items-center gap-2 text-sm text-gray-700">
                        <svg className="w-5 h-5 text-[#08CBCD] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" /></svg>
                        {f}
                      </li>
                    ))}
                  </ul>
                  <Link
                    to="/appointment"
                    className={`block text-center py-3 rounded-xl font-semibold text-sm transition-all ${pkg.popular ? 'bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white hover:opacity-90' : 'border-2 border-[#0D54AF] text-[#0D54AF] hover:bg-[#0D54AF] hover:text-white'}`}
                  >
                    رزرو این پکیج
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Insurance */}
      <section className="py-16 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="bg-white rounded-2xl p-8 shadow-sm">
            <h2 className="text-2xl font-black text-[#071F3F] mb-6 text-center">بیمه‌های پذیرفته شده</h2>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {['بیمه ایران', 'بیمه ملت', 'بیمه پارسیان', 'بیمه دانا', 'بیمه البرز', 'بیمه آرمان', 'بیمه کوثر', 'بیمه سینا'].map(ins => (
                <div key={ins} className="flex items-center gap-2 bg-gray-50 rounded-xl p-3 text-sm text-gray-700">
                  <svg className="w-5 h-5 text-[#08CBCD]" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" /></svg>
                  {ins}
                </div>
              ))}
            </div>
            <p className="text-center text-gray-500 text-sm mt-4">* پوشش بیمه بستگی به نوع خدمت و قرارداد دارد. برای استعلام با ما تماس بگیرید.</p>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 bg-gradient-to-l from-[#08CBCD] to-[#0D54AF] text-white">
        <div className="max-w-3xl mx-auto px-4 text-center">
          <h2 className="text-3xl font-black mb-4">برای مشاوره رایگان تماس بگیرید</h2>
          <p className="text-white/80 mb-6">کارشناسان ما آماده پاسخگویی به سوالات شما هستند</p>
          <div className="flex flex-wrap justify-center gap-4">
            <Link to="/appointment" className="bg-white text-[#0D54AF] font-bold px-8 py-4 rounded-xl hover:shadow-2xl transition-all">
              رزرو نوبت رایگان
            </Link>
            <a href="tel:+982188888888" className="border-2 border-white text-white font-bold px-8 py-4 rounded-xl hover:bg-white/10 transition-all ltr">
              ۰۲۱-۸۸۸۸۸۸۸۸
            </a>
          </div>
        </div>
      </section>
    </div>
  )
}
