import { useState } from 'react'
import { Link } from 'react-router'
import Breadcrumb from '../components/Breadcrumb'

const faqCategories = [
  {
    cat: 'ایمپلنت',
    icon: '🦷',
    items: [
      { q: 'ایمپلنت دندان چیست؟', a: 'ایمپلنت دندان یک پیچ تیتانیوم است که جایگزین ریشه دندان از دست رفته می‌شود. روی آن یک کراون قرار می‌گیرد که ظاهر و عملکرد دندان طبیعی را شبیه‌سازی می‌کند.' },
      { q: 'ایمپلنت چقدر دوام دارد؟', a: 'با مراقبت صحیح، ایمپلنت می‌تواند تمام عمر دوام بیاورد. خود پیچ تیتانیوم معمولاً برای ۲۵ تا ۳۰ سال یا بیشتر مشکلی ایجاد نمی‌کند.' },
      { q: 'آیا ایمپلنت دردناک است؟', a: 'عمل ایمپلنت تحت بی‌حسی موضعی انجام می‌شود. درد حین عمل احساس نمی‌شود. پس از عمل ممکن است درد و ورم خفیفی داشته باشید که با داروهای ساده کنترل می‌شود.' },
      { q: 'چه کسانی کاندید ایمپلنت هستند؟', a: 'اکثر بزرگسالان با استخوان فک کافی و بدون بیماری‌های کنترل نشده (مثل دیابت نوع ۱ یا پوکی استخوان شدید) می‌توانند ایمپلنت بگذارند.' },
    ],
  },
  {
    cat: 'ارتودنسی',
    icon: '😁',
    items: [
      { q: 'الاینر یا براکت فلزی؟ کدام بهتر است؟', a: 'بستگی به وضعیت دندان‌های شما دارد. الاینر برای موارد خفیف تا متوسط و نامرئی بودن مناسب است. براکت فلزی برای موارد پیچیده‌تر و با هزینه کمتر.' },
      { q: 'ارتودنسی چقدر طول می‌کشد؟', a: 'بسته به پیچیدگی وضعیت، معمولاً ۱۲ تا ۲۴ ماه طول می‌کشد. ارتودنسی جزئی ممکن است کوتاه‌تر باشد.' },
      { q: 'آیا باید رتاینر بگذارم؟', a: 'بله، پس از اتمام درمان ارتودنسی، استفاده از رتاینر برای حفظ نتیجه ضروری است. معمولاً چند سال باید استفاده شود.' },
    ],
  },
  {
    cat: 'زیبایی',
    icon: '✨',
    items: [
      { q: 'بلیچینگ چقدر دندان را سفید می‌کند؟', a: 'بلیچینگ حرفه‌ای می‌تواند دندان‌ها را تا ۸ سایه روشن‌تر کند. نتیجه بستگی به رنگ اولیه دندان دارد.' },
      { q: 'لمینت دندان دردناک است؟', a: 'خیر، فرآیند لمینت با بی‌حسی موضعی انجام می‌شود و بیمار درد احساس نمی‌کند. حساسیت موقت بعد از عمل ممکن است وجود داشته باشد.' },
      { q: 'طراحی لبخند DSD چیست؟', a: 'Digital Smile Design (DSD) یک روش پیشرفته است که با شبیه‌سازی دیجیتال، نتیجه نهایی لبخند را قبل از شروع درمان به بیمار نشان می‌دهد.' },
    ],
  },
  {
    cat: 'عمومی',
    icon: '🏥',
    items: [
      { q: 'چقدر باید به دندانپزشک بروم؟', a: 'توصیه می‌شود هر ۶ ماه یک بار برای چکاپ و جرم‌گیری مراجعه کنید. بیمارانی که مشکلات خاص دارند ممکن است بیشتر نیاز به مراجعه داشته باشند.' },
      { q: 'آیا نوبت اورژانسی دارید؟', a: 'بله، در موارد دندان درد شدید، شکستگی دندان یا عفونت، همان روز پذیرش داریم.' },
      { q: 'بیمه قبول می‌کنید؟', a: 'خدمات برخی بیمه‌های تکمیلی را قبول می‌کنیم. لطفاً با ما تماس بگیرید تا بررسی کنیم.' },
      { q: 'آیا خدمات دندانپزشکی کودکان ارائه می‌دهید؟', a: 'بله، یک متخصص دندانپزشکی کودکان در کلینیک ما به کودکان از سن ۱ سالگی خدمات ارائه می‌دهد.' },
    ],
  },
  {
    cat: 'مراقبت‌ها',
    icon: '💊',
    items: [
      { q: 'بعد از ایمپلنت چه بخورم؟', a: 'در هفته اول غذاهای نرم و خنک مصرف کنید. از غذاهای سخت، ترد، و داغ خودداری کنید.' },
      { q: 'چطور دندان‌هایم را مسواک بزنم؟', a: 'دو بار در روز با خمیردندان فلوراید مسواک بزنید. از نخ دندان یا ایریگاتور استفاده کنید. مسواک نرم را توصیه می‌کنیم.' },
      { q: 'برای کودکان از چه سنی مسواک بزنیم؟', a: 'از اولین دندان کودک (معمولاً ۶ ماهگی) مسواک شروع کنید. از ژل یا خمیردندان کودکان با مقدار کم فلوراید استفاده کنید.' },
    ],
  },
]

export default function FAQ() {
  const [openItem, setOpenItem] = useState<string | null>(null)
  const [search, setSearch] = useState('')
  const [activeCategory, setActiveCategory] = useState('همه')

  const allFaqs = faqCategories.flatMap(cat => cat.items.map(item => ({ ...item, cat: cat.cat })))

  const filtered = allFaqs.filter(faq => {
    const matchesCat = activeCategory === 'همه' || faq.cat === activeCategory
    const matchesSearch = !search || faq.q.includes(search) || faq.a.includes(search)
    return matchesCat && matchesSearch
  })

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'پرسش‌های متداول' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-6">پرسش‌های متداول</h1>
          {/* Search */}
          <div className="relative max-w-xl">
            <input
              type="search"
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="جستجو در سوالات..."
              className="w-full bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-3 text-white placeholder-white/60 focus:border-[#08CBCD] outline-none text-sm"
            />
            <svg className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          </div>
        </div>
      </div>

      {/* Category filter */}
      <div className="sticky top-[88px] z-30 bg-white border-b border-gray-100 shadow-sm py-4">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex gap-2 overflow-x-auto pb-1">
            <button onClick={() => setActiveCategory('همه')} className={`flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-all ${activeCategory === 'همه' ? 'bg-[#0D54AF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}>همه</button>
            {faqCategories.map(cat => (
              <button key={cat.cat} onClick={() => setActiveCategory(cat.cat)} className={`flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium transition-all ${activeCategory === cat.cat ? 'bg-[#0D54AF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}>
                {cat.icon} {cat.cat}
              </button>
            ))}
          </div>
        </div>
      </div>

      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-4xl mx-auto px-4">
          {filtered.length === 0 ? (
            <div className="text-center py-16 text-gray-400">
              <div className="text-5xl mb-4">🔍</div>
              <p>سوالی یافت نشد. کلمه دیگری جستجو کنید.</p>
            </div>
          ) : (
            <div className="space-y-3">
              {filtered.map((faq, i) => (
                <div key={i} className="bg-white rounded-2xl overflow-hidden shadow-sm" itemScope itemType="https://schema.org/Question">
                  <button
                    onClick={() => setOpenItem(openItem === `${i}` ? null : `${i}`)}
                    className="w-full flex items-center justify-between p-5 text-right hover:bg-gray-50 transition-colors"
                    aria-expanded={openItem === `${i}`}
                  >
                    <div className="flex items-center gap-3">
                      <span className="bg-blue-50 text-[#0D54AF] text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0">{faq.cat}</span>
                      <span className="font-semibold text-[#071F3F] text-sm" itemProp="name">{faq.q}</span>
                    </div>
                    <svg className={`w-5 h-5 text-[#0D54AF] flex-shrink-0 transition-transform ${openItem === `${i}` ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" /></svg>
                  </button>
                  {openItem === `${i}` && (
                    <div className="px-5 pb-5 text-gray-600 text-sm leading-relaxed border-t border-gray-100 pt-4" itemScope itemType="https://schema.org/Answer" itemProp="acceptedAnswer">
                      <p itemProp="text">{faq.a}</p>
                    </div>
                  )}
                </div>
              ))}
            </div>
          )}

          {/* CTA */}
          <div className="mt-10 bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] rounded-2xl p-6 text-white text-center">
            <h3 className="font-bold text-xl mb-2">سوال شما اینجا نیست؟</h3>
            <p className="text-white/80 text-sm mb-4">با ما تماس بگیرید تا به سوالات شما پاسخ دهیم</p>
            <div className="flex justify-center gap-4">
              <Link to="/contact" className="bg-white text-[#0D54AF] px-6 py-2.5 rounded-xl font-semibold text-sm hover:shadow-lg transition-all">
                ارسال پیام
              </Link>
              <a href="tel:+982188888888" className="border border-white text-white px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-white/10 transition-all ltr">
                ۰۲۱-۸۸۸۸۸۸۸۸
              </a>
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}
