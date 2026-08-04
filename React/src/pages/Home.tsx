import { Link } from 'react-router'
import { useState } from 'react'
import { services, serviceCategories } from '../data/services'

const stats = [
  { num: '۱۵+', label: 'سال تجربه' },
  { num: '۸,۰۰۰+', label: 'بیمار راضی' },
  { num: '۱۲', label: 'تخصص درمانی' },
  { num: '۹۸٪', label: 'رضایت بیماران' },
]

const testimonials = [
  {
    name: 'مریم احمدی',
    text: 'بهترین دندانپزشکی بودم. ایمپلنت من بدون هیچ دردی انجام شد و نتیجه فوق‌العاده‌ای داشت.',
    service: 'ایمپلنت دندان',
    rating: 5,
    avatar: 'م',
  },
  {
    name: 'علی رضایی',
    text: 'دکتر علی‌پسندی بسیار حرفه‌ای و دلسوز هستند. ارتودنسی ام را با الاینر انجام دادم و کاملاً راضی هستم.',
    service: 'الاینر شفاف',
    rating: 5,
    avatar: 'ع',
  },
  {
    name: 'فاطمه کریمی',
    text: 'طراحی لبخندم کاملاً تغییر کرد. لمینت‌ها بسیار طبیعی به نظر می‌رسند. ممنون از تیم حرفه‌ای.',
    service: 'طراحی لبخند',
    rating: 5,
    avatar: 'ف',
  },
]

const blogPosts = [
  {
    title: 'ایمپلنت دندان چقدر طول می‌کشد؟',
    excerpt: 'راهنمای جامع مراحل، زمان‌بندی و مراقبت‌های لازم برای ایمپلنت دندان',
    category: 'ایمپلنت',
    date: '۱۵ آبان ۱۴۰۳',
    readTime: '۷ دقیقه',
    img: 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=400&h=250&fit=crop&auto=format',
    slug: 'implant-duration',
  },
  {
    title: 'الاینر یا براکت فلزی؟ کدام بهتر است؟',
    excerpt: 'مقایسه جامع دو روش ارتودنسی و راهنمای انتخاب مناسب برای شما',
    category: 'ارتودنسی',
    date: '۸ آبان ۱۴۰۳',
    readTime: '۵ دقیقه',
    img: 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=400&h=250&fit=crop&auto=format',
    slug: 'aligner-vs-braces',
  },
  {
    title: 'راهنمای کامل مراقبت از دندان‌ها',
    excerpt: 'نکات طلایی برای حفظ سلامت دهان و دندان در خانه',
    category: 'بهداشت دهان',
    date: '۱ آبان ۱۴۰۳',
    readTime: '۴ دقیقه',
    img: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=250&fit=crop&auto=format',
    slug: 'dental-care-guide',
  },
]

const faqItems = [
  { q: 'آیا نوبت آنلاین دارید؟', a: 'بله، می‌توانید از طریق سایت یا تلفن نوبت رزرو کنید.' },
  { q: 'بیمه قبول می‌کنید؟', a: 'خدمات برخی بیمه‌های تکمیلی را قبول می‌کنیم. لطفاً با ما تماس بگیرید.' },
  { q: 'آیا امکان درمان اورژانسی وجود دارد؟', a: 'بله، در موارد اورژانسی همان روز پذیرش داریم.' },
  { q: 'کجا واقع شده‌اید؟', a: 'تهران، خیابان ولیعصر، بالاتر از میدان ونک' },
]

export default function Home() {
  const [openFaq, setOpenFaq] = useState<number | null>(null)

  return (
    <div className="pt-[88px]">
      {/* Hero */}
      <section className="relative overflow-hidden bg-gradient-to-br from-[#071F3F] via-[#0D54AF] to-[#08CBCD] text-white min-h-[90vh] flex items-center">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute inset-0" style={{ backgroundImage: 'radial-gradient(circle at 2px 2px, white 1px, transparent 0)', backgroundSize: '40px 40px' }} />
        </div>
        <div className="absolute left-0 top-0 w-1/2 h-full opacity-20 hidden lg:block">
          <img
            src="https://images.unsplash.com/photo-1588776814546-1ffbb2b1b3e1?w=900&h=800&fit=crop&auto=format"
            alt=""
            className="w-full h-full object-cover"
            aria-hidden="true"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-[#0D54AF] to-transparent" />
        </div>

        <div className="relative max-w-7xl mx-auto px-4 py-20 grid lg:grid-cols-2 gap-12 items-center">
          <div className="animate-fade-up">
            <div className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 text-sm mb-6">
              <span className="w-2 h-2 bg-[#08CBCD] rounded-full animate-pulse" />
              کلینیک تخصصی دندانپزشکی در تهران
            </div>
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-black leading-tight mb-6">
              لبخندی زیباتر
              <br />
              <span className="text-[#08CBCD]">با دکتر علی‌پسندی</span>
            </h1>
            <p className="text-lg text-white/80 leading-relaxed mb-8 max-w-xl">
              با بیش از ۱۵ سال تجربه در دندانپزشکی تخصصی، ما به شما کمک می‌کنیم لبخندی که آرزویش را داشتید داشته باشید. از ایمپلنت تا طراحی لبخند، همه با بالاترین کیفیت.
            </p>
            <div className="flex flex-wrap gap-4">
              <Link
                to="/appointment"
                className="flex items-center gap-2 bg-[#08CBCD] hover:bg-white text-[#071F3F] font-bold px-7 py-3.5 rounded-xl transition-all shadow-lg hover:shadow-xl"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                رزرو نوبت رایگان
              </Link>
              <Link
                to="/services"
                className="flex items-center gap-2 border-2 border-white/40 hover:border-white text-white font-bold px-7 py-3.5 rounded-xl transition-all backdrop-blur-sm"
              >
                مشاهده خدمات
              </Link>
            </div>

            {/* Trust badges */}
            <div className="flex flex-wrap gap-4 mt-8">
              {['عضو نظام پزشکی', 'دارای مدرک تخصصی', 'تجهیزات پیشرفته'].map(b => (
                <div key={b} className="flex items-center gap-2 text-sm text-white/70">
                  <svg className="w-4 h-4 text-[#08CBCD]" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" /></svg>
                  {b}
                </div>
              ))}
            </div>
          </div>

          {/* Doctor card */}
          <div className="hidden lg:flex justify-center">
            <div className="relative bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-8 max-w-sm w-full animate-float">
              <div className="w-32 h-32 rounded-2xl bg-gradient-to-br from-[#08CBCD] to-[#0D54AF] mx-auto mb-4 flex items-center justify-center text-5xl shadow-2xl">
                👨‍⚕️
              </div>
              <h2 className="text-center font-bold text-xl mb-1">دکتر کیوان علی‌پسندی</h2>
              <p className="text-center text-[#08CBCD] text-sm mb-4">متخصص دندانپزشکی ترمیمی و زیبایی</p>
              <div className="grid grid-cols-2 gap-3">
                {stats.slice(0, 4).map(s => (
                  <div key={s.label} className="bg-white/10 rounded-xl p-3 text-center">
                    <div className="text-2xl font-black text-[#08CBCD]">{s.num}</div>
                    <div className="text-xs text-white/70 mt-0.5">{s.label}</div>
                  </div>
                ))}
              </div>
              <Link
                to="/about"
                className="block mt-4 text-center text-sm text-[#08CBCD] hover:text-white transition-colors"
              >
                بیشتر بدانید ←
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Stats bar */}
      <section className="bg-white border-b border-gray-100 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 py-6">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {stats.map(s => (
              <div key={s.label} className="text-center">
                <div className="text-3xl font-black gradient-text">{s.num}</div>
                <div className="text-sm text-gray-500 mt-1">{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Services */}
      <section className="py-20 bg-gradient-to-b from-white to-[#F4F9FF]" aria-label="خدمات ما">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <span className="inline-block bg-blue-50 text-[#0D54AF] text-sm font-medium px-4 py-1.5 rounded-full mb-3">خدمات تخصصی</span>
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">چه خدماتی ارائه می‌دهیم؟</h2>
            <p className="text-gray-500 max-w-2xl mx-auto">از پیشگیری تا درمان‌های پیشرفته، ما طیف کاملی از خدمات دندانپزشکی را با بالاترین کیفیت ارائه می‌دهیم</p>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {serviceCategories.map(cat => (
              <Link
                key={cat.slug}
                to={`/services/${cat.slug}/${services.find(s => s.categorySlug === cat.slug)?.slug || ''}`}
                className="group bg-white rounded-2xl p-5 text-center hover:shadow-xl transition-all card-hover border border-gray-100 hover:border-[#08CBCD]/30"
              >
                <div className="text-4xl mb-3">{cat.icon}</div>
                <h3 className="text-sm font-bold text-[#071F3F] group-hover:text-[#0D54AF] transition-colors leading-snug">{cat.title}</h3>
                <div className="mt-2 text-xs text-gray-400 group-hover:text-[#08CBCD] transition-colors">
                  {services.filter(s => s.categorySlug === cat.slug).length} درمان
                </div>
              </Link>
            ))}
          </div>
          <div className="text-center mt-8">
            <Link
              to="/services"
              className="inline-flex items-center gap-2 text-[#0D54AF] font-semibold hover:text-[#08CBCD] transition-colors"
            >
              مشاهده همه خدمات
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" /></svg>
            </Link>
          </div>
        </div>
      </section>

      {/* Why us */}
      <section className="py-20 bg-[#F4F9FF]" aria-label="چرا ما">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <span className="inline-block bg-blue-50 text-[#0D54AF] text-sm font-medium px-4 py-1.5 rounded-full mb-3">چرا دکتر علی‌پسندی؟</span>
              <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-6">متخصصانی که به شما اهمیت می‌دهند</h2>
              <div className="space-y-4">
                {[
                  { icon: '🏆', title: 'تجربه ۱۵+ ساله', desc: 'سابقه درخشان در درمان بیش از ۸۰۰۰ بیمار' },
                  { icon: '🔬', title: 'تکنولوژی پیشرفته', desc: 'استفاده از جدیدترین تجهیزات دندانپزشکی دنیا' },
                  { icon: '❤️', title: 'رویکرد بیمارمحور', desc: 'آرامش و رضایت شما اولویت اول ماست' },
                  { icon: '📋', title: 'درمان جامع', desc: 'برنامه درمانی شخصی‌سازی شده برای هر بیمار' },
                  { icon: '💰', title: 'قیمت منصفانه', desc: 'بهترین کیفیت با قیمت‌های رقابتی و شفاف' },
                  { icon: '🕐', title: 'ساعات کاری مناسب', desc: 'شنبه تا پنج‌شنبه ۹ صبح تا ۸ شب' },
                ].map(item => (
                  <div key={item.title} className="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div className="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl flex-shrink-0">{item.icon}</div>
                    <div>
                      <h3 className="font-bold text-[#071F3F] mb-1">{item.title}</h3>
                      <p className="text-sm text-gray-500">{item.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            <div className="relative">
              <div className="rounded-3xl overflow-hidden shadow-2xl">
                <img
                  src="https://images.unsplash.com/photo-1588776814546-1ffbb2b1b3e1?w=700&h=800&fit=crop&auto=format"
                  alt="کلینیک دندانپزشکی دکتر علی‌پسندی"
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="absolute -bottom-4 -right-4 bg-white rounded-2xl shadow-xl p-4 flex items-center gap-3">
                <div className="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">⭐</div>
                <div>
                  <div className="font-black text-[#071F3F]">۴.۹/۵</div>
                  <div className="text-xs text-gray-500">از ۱۲۰۰+ نظر</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Before/After */}
      <section className="py-20 bg-white" aria-label="نمونه کارها">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <span className="inline-block bg-blue-50 text-[#0D54AF] text-sm font-medium px-4 py-1.5 rounded-full mb-3">نمونه کارها</span>
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">نتایج واقعی، بیماران واقعی</h2>
            <p className="text-gray-500">مشاهده تحول لبخند بیماران ما</p>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {[
              { before: 'قبل از طراحی لبخند', after: 'بعد از طراحی لبخند', service: 'طراحی لبخند', img: 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=400&h=300&fit=crop&auto=format' },
              { before: 'قبل از ایمپلنت', after: 'بعد از ایمپلنت', service: 'ایمپلنت دندان', img: 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=400&h=300&fit=crop&auto=format' },
              { before: 'قبل از لمینت', after: 'بعد از لمینت', service: 'لمینت دندان', img: 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=400&h=300&fit=crop&auto=format' },
            ].map((item, i) => (
              <div key={i} className="group relative rounded-2xl overflow-hidden shadow-lg card-hover bg-gray-50">
                <img src={item.img} alt={item.service} className="w-full h-56 object-cover" />
                <div className="p-4">
                  <span className="inline-block bg-[#0D54AF] text-white text-xs px-3 py-1 rounded-full mb-2">{item.service}</span>
                  <div className="flex justify-between text-sm text-gray-500">
                    <span>{item.before}</span>
                    <span>→</span>
                    <span className="text-[#08CBCD] font-medium">{item.after}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>
          <div className="text-center mt-8">
            <Link to="/gallery" className="inline-flex items-center gap-2 border-2 border-[#0D54AF] text-[#0D54AF] hover:bg-[#0D54AF] hover:text-white px-6 py-3 rounded-xl font-semibold transition-all">
              مشاهده گالری کامل
            </Link>
          </div>
        </div>
      </section>

      {/* Testimonials */}
      <section className="py-20 bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white" aria-label="نظرات بیماران">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <span className="inline-block bg-white/10 text-[#08CBCD] text-sm font-medium px-4 py-1.5 rounded-full mb-3">نظرات بیماران</span>
            <h2 className="text-3xl md:text-4xl font-black mb-4">بیماران ما چه می‌گویند؟</h2>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {testimonials.map((t, i) => (
              <div key={i} className="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6">
                <div className="flex gap-0.5 mb-4">
                  {Array(t.rating).fill(0).map((_, j) => (
                    <svg key={j} className="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                  ))}
                </div>
                <p className="text-white/80 text-sm leading-relaxed mb-4">«{t.text}»</p>
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-[#08CBCD] flex items-center justify-center font-bold text-[#071F3F]">{t.avatar}</div>
                  <div>
                    <p className="font-semibold text-sm">{t.name}</p>
                    <p className="text-xs text-white/60">{t.service}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section className="py-20 bg-white" aria-label="سوالات متداول">
        <div className="max-w-3xl mx-auto px-4">
          <div className="text-center mb-12">
            <span className="inline-block bg-blue-50 text-[#0D54AF] text-sm font-medium px-4 py-1.5 rounded-full mb-3">سوالات متداول</span>
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">پرسش‌های رایج</h2>
          </div>
          <div className="space-y-3">
            {faqItems.map((faq, i) => (
              <div key={i} className="border border-gray-200 rounded-xl overflow-hidden">
                <button
                  onClick={() => setOpenFaq(openFaq === i ? null : i)}
                  className="w-full flex items-center justify-between p-4 text-right hover:bg-gray-50 transition-colors"
                  aria-expanded={openFaq === i}
                >
                  <span className="font-semibold text-[#071F3F]">{faq.q}</span>
                  <svg className={`w-5 h-5 text-[#0D54AF] flex-shrink-0 transition-transform ${openFaq === i ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" /></svg>
                </button>
                {openFaq === i && (
                  <div className="px-4 pb-4 text-gray-600 text-sm leading-relaxed border-t border-gray-100 pt-3">
                    {faq.a}
                  </div>
                )}
              </div>
            ))}
          </div>
          <div className="text-center mt-6">
            <Link to="/faq" className="text-[#0D54AF] font-semibold hover:text-[#08CBCD] transition-colors">
              مشاهده همه سوالات ←
            </Link>
          </div>
        </div>
      </section>

      {/* Blog */}
      <section className="py-20 bg-[#F4F9FF]" aria-label="وبلاگ">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <span className="inline-block bg-blue-50 text-[#0D54AF] text-sm font-medium px-4 py-1.5 rounded-full mb-3">وبلاگ آموزشی</span>
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">آخرین مقالات</h2>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {blogPosts.map((post, i) => (
              <Link key={i} to={`/knowledge-base/${post.slug}`} className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all card-hover group">
                <div className="h-48 overflow-hidden bg-gray-100">
                  <img src={post.img} alt={post.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                </div>
                <div className="p-5">
                  <div className="flex items-center gap-3 mb-3">
                    <span className="bg-blue-50 text-[#0D54AF] text-xs px-2 py-1 rounded-full">{post.category}</span>
                    <span className="text-gray-400 text-xs">{post.readTime} مطالعه</span>
                  </div>
                  <h3 className="font-bold text-[#071F3F] mb-2 group-hover:text-[#0D54AF] transition-colors">{post.title}</h3>
                  <p className="text-sm text-gray-500 leading-relaxed">{post.excerpt}</p>
                  <div className="mt-3 text-xs text-gray-400">{post.date}</div>
                </div>
              </Link>
            ))}
          </div>
          <div className="text-center mt-8">
            <Link to="/knowledge-base" className="inline-flex items-center gap-2 bg-[#0D54AF] text-white px-6 py-3 rounded-xl font-semibold hover:bg-[#08CBCD] transition-colors">
              مشاهده همه مقالات
            </Link>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 bg-gradient-to-l from-[#08CBCD] to-[#0D54AF] text-white" aria-label="رزرو نوبت">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="text-3xl md:text-4xl font-black mb-4">آماده شروع هستید؟</h2>
          <p className="text-white/80 text-lg mb-8 max-w-2xl mx-auto">همین امروز نوبت مشاوره رایگان خود را رزرو کنید و اولین قدم را به سمت لبخندی درخشان بردارید</p>
          <div className="flex flex-wrap justify-center gap-4">
            <Link
              to="/appointment"
              className="flex items-center gap-2 bg-white text-[#0D54AF] font-bold px-8 py-4 rounded-xl hover:shadow-2xl transition-all"
            >
              🗓️ رزرو نوبت آنلاین
            </Link>
            <a
              href="tel:+982188888888"
              className="flex items-center gap-2 border-2 border-white text-white font-bold px-8 py-4 rounded-xl hover:bg-white/10 transition-all ltr"
            >
              📞 ۰۲۱-۸۸۸۸۸۸۸۸
            </a>
          </div>
        </div>
      </section>
    </div>
  )
}
