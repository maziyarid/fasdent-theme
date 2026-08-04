import { useState } from 'react'
import { Link, useParams } from 'react-router'
import Breadcrumb from '../components/Breadcrumb'

const categories = [
  { slug: 'implant', label: 'ایمپلنت', icon: '🦷', count: 8 },
  { slug: 'orthodontics', label: 'ارتودنسی', icon: '😁', count: 6 },
  { slug: 'cosmetic', label: 'زیبایی', icon: '✨', count: 10 },
  { slug: 'children', label: 'کودکان', icon: '👶', count: 5 },
  { slug: 'hygiene', label: 'بهداشت دهان', icon: '🪥', count: 12 },
  { slug: 'general', label: 'عمومی', icon: '🏥', count: 15 },
]

const articles = [
  {
    slug: 'implant-duration',
    title: 'ایمپلنت دندان چقدر طول می‌کشد؟',
    excerpt: 'راهنمای جامع مراحل، زمان‌بندی و مراقبت‌های لازم برای ایمپلنت دندان. همه چیزی که قبل از ایمپلنت باید بدانید.',
    category: 'implant',
    categoryLabel: 'ایمپلنت',
    date: '۱۵ آبان ۱۴۰۳',
    readTime: '۷ دقیقه',
    author: 'دکتر کیوان علی‌پسندی',
    difficulty: 'مبتدی',
    img: 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=400&h=250&fit=crop&auto=format',
    tags: ['ایمپلنت', 'مراحل درمان'],
    featured: true,
  },
  {
    slug: 'aligner-vs-braces',
    title: 'الاینر یا براکت فلزی؟ مقایسه جامع',
    excerpt: 'کدام روش ارتودنسی برای شما مناسب‌تر است؟ مقایسه هزینه، زمان، راحتی و نتیجه.',
    category: 'orthodontics',
    categoryLabel: 'ارتودنسی',
    date: '۸ آبان ۱۴۰۳',
    readTime: '۵ دقیقه',
    author: 'دکتر کیوان علی‌پسندی',
    difficulty: 'مبتدی',
    img: 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=400&h=250&fit=crop&auto=format',
    tags: ['ارتودنسی', 'الاینر', 'براکت'],
    featured: true,
  },
  {
    slug: 'dental-care-guide',
    title: 'راهنمای کامل مراقبت از دندان‌ها در خانه',
    excerpt: 'نکات طلایی برای حفظ سلامت دهان و دندان. مسواک زدن صحیح، نخ دندان و بیشتر.',
    category: 'hygiene',
    categoryLabel: 'بهداشت دهان',
    date: '۱ آبان ۱۴۰۳',
    readTime: '۴ دقیقه',
    author: 'دکتر کیوان علی‌پسندی',
    difficulty: 'مبتدی',
    img: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=250&fit=crop&auto=format',
    tags: ['بهداشت', 'مسواک', 'مراقبت'],
    featured: false,
  },
  {
    slug: 'teeth-whitening-guide',
    title: 'بلیچینگ دندان: همه چیز درباره سفیدکردن',
    excerpt: 'روش‌های مختلف سفیدکردن دندان، مزایا، معایب و توصیه‌های دندانپزشکی.',
    category: 'cosmetic',
    categoryLabel: 'زیبایی',
    date: '۲۵ مهر ۱۴۰۳',
    readTime: '۶ دقیقه',
    author: 'دکتر کیوان علی‌پسندی',
    difficulty: 'متوسط',
    img: 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=400&h=250&fit=crop&auto=format',
    tags: ['بلیچینگ', 'زیبایی دندان'],
    featured: false,
  },
  {
    slug: 'children-dental-care',
    title: 'مراقبت از دندان کودکان: از ۰ تا ۱۲ سال',
    excerpt: 'راهنمای کامل مراقبت دهانی کودکان در هر مرحله رشد.',
    category: 'children',
    categoryLabel: 'کودکان',
    date: '۱۸ مهر ۱۴۰۳',
    readTime: '۸ دقیقه',
    author: 'دکتر کیوان علی‌پسندی',
    difficulty: 'مبتدی',
    img: 'https://images.unsplash.com/photo-1617529497471-9218633199c0?w=400&h=250&fit=crop&auto=format',
    tags: ['کودکان', 'دندان شیری'],
    featured: false,
  },
  {
    slug: 'gum-disease-signs',
    title: 'علائم بیماری لثه که نباید نادیده بگیرید',
    excerpt: 'خونریزی لثه، تورم، و سایر هشدارهای مهم بیماری پریودنتال.',
    category: 'general',
    categoryLabel: 'عمومی',
    date: '۱۰ مهر ۱۴۰۳',
    readTime: '۵ دقیقه',
    author: 'دکتر کیوان علی‌پسندی',
    difficulty: 'مبتدی',
    img: 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=400&h=250&fit=crop&auto=format',
    tags: ['لثه', 'پریودنتال', 'بیماری لثه'],
    featured: false,
  },
]

const difficultyColors: Record<string, string> = {
  'مبتدی': 'bg-green-100 text-green-700',
  'متوسط': 'bg-yellow-100 text-yellow-700',
  'پیشرفته': 'bg-red-100 text-red-700',
}

export default function KnowledgeBase() {
  const { slug } = useParams()
  const [search, setSearch] = useState('')
  const [activeCategory, setActiveCategory] = useState<string | null>(null)

  if (slug) {
    const article = articles.find(a => a.slug === slug)
    if (!article) return <div className="pt-[88px] text-center py-20">مقاله یافت نشد</div>

    return (
      <div className="pt-[88px]">
        <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-12">
          <div className="max-w-7xl mx-auto px-4">
            <Breadcrumb items={[{ label: 'وبلاگ', href: '/knowledge-base' }, { label: article.title }]} />
          </div>
        </div>

        <div className="max-w-4xl mx-auto px-4 py-10">
          {/* Article meta */}
          <div className="flex flex-wrap gap-3 mb-4 text-sm">
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${difficultyColors[article.difficulty]}`}>{article.difficulty}</span>
            <span className="text-gray-500">✍️ {article.author}</span>
            <span className="text-gray-500">📅 {article.date}</span>
            <span className="text-gray-500">⏱️ {article.readTime}</span>
          </div>

          <h1 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">{article.title}</h1>
          <p className="text-gray-600 text-lg leading-relaxed mb-6">{article.excerpt}</p>

          <div className="rounded-2xl overflow-hidden mb-8 shadow-lg">
            <img src={article.img} alt={article.title} className="w-full h-64 object-cover" />
          </div>

          {/* Key takeaways */}
          <div className="bg-gradient-to-l from-[#E8F4FD] to-[#E0F7F7] border border-[#08CBCD]/30 rounded-2xl p-5 mb-8">
            <h2 className="font-bold text-[#071F3F] mb-3 flex items-center gap-2">
              📌 نکات کلیدی این مقاله
            </h2>
            <ul className="space-y-2">
              {['محتوای آموزنده درباره ' + article.title, 'توصیه‌های عملی از متخصص', 'پاسخ به سوالات رایج'].map((item, i) => (
                <li key={i} className="flex items-center gap-2 text-sm text-gray-700">
                  <svg className="w-4 h-4 text-[#08CBCD] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" /></svg>
                  {item}
                </li>
              ))}
            </ul>
          </div>

          {/* Article content */}
          <div className="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-4">
            <p>این مقاله محتوای جامعی درباره {article.title} ارائه می‌دهد. اطلاعات مندرج در این مقاله بر اساس آخرین یافته‌های علمی و تجربیات بالینی دکتر علی‌پسندی تهیه شده است.</p>
            <h2 className="text-xl font-bold text-[#071F3F] mt-6">مقدمه</h2>
            <p>دندانپزشکی مدرن پیشرفت‌های شگرفی داشته و روش‌های درمانی بسیار بهتر و کمتر دردناکی در دسترس بیماران قرار گرفته است. در این مقاله به طور جامع به این موضوع می‌پردازیم.</p>
            <h2 className="text-xl font-bold text-[#071F3F] mt-6">سوالات متداول</h2>
            <p>بیماران زیادی سوالاتی درباره این موضوع مطرح می‌کنند. در اینجا پاسخ جامع به متداول‌ترین سوالات را ارائه می‌دهیم.</p>
          </div>

          {/* Tags */}
          <div className="flex flex-wrap gap-2 mt-8 pt-6 border-t border-gray-100">
            {article.tags.map(tag => (
              <Link key={tag} to={`/tag/${tag}`} className="bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-[#0D54AF] px-3 py-1 rounded-full text-sm transition-colors">
                #{tag}
              </Link>
            ))}
          </div>

          {/* Author bio */}
          <div className="mt-8 bg-[#F4F9FF] rounded-2xl p-5 flex items-start gap-4">
            <div className="w-16 h-16 rounded-xl bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] flex items-center justify-center text-3xl flex-shrink-0">👨‍⚕️</div>
            <div>
              <p className="font-bold text-[#071F3F]">{article.author}</p>
              <p className="text-[#0D54AF] text-sm mb-2">متخصص دندانپزشکی ترمیمی و زیبایی</p>
              <p className="text-gray-600 text-sm leading-relaxed">با بیش از ۱۵ سال تجربه در دندانپزشکی تخصصی، دکتر علی‌پسندی یکی از برجسته‌ترین متخصصان دندانپزشکی در ایران است.</p>
            </div>
          </div>

          {/* CTA */}
          <div className="mt-8 bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] rounded-2xl p-6 text-white text-center">
            <h3 className="font-bold text-xl mb-2">نیاز به مشاوره دارید؟</h3>
            <p className="text-white/80 text-sm mb-4">با دکتر علی‌پسندی مشاوره رایگان داشته باشید</p>
            <Link to="/appointment" className="inline-block bg-white text-[#0D54AF] px-6 py-3 rounded-xl font-bold text-sm hover:shadow-xl transition-all">
              رزرو نوبت رایگان
            </Link>
          </div>
        </div>
      </div>
    )
  }

  // Hub page
  const filtered = articles.filter(a => {
    const matchesCat = !activeCategory || a.category === activeCategory
    const matchesSearch = !search || a.title.includes(search) || a.excerpt.includes(search)
    return matchesCat && matchesSearch
  })

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'وبلاگ' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-6">پایگاه دانش دندانپزشکی</h1>
          <div className="relative max-w-xl">
            <input
              type="search"
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="جستجو در مقالات..."
              className="w-full bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-3 text-white placeholder-white/60 focus:border-[#08CBCD] outline-none text-sm"
            />
            <svg className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          </div>
        </div>
      </div>

      {/* Categories */}
      <div className="bg-white border-b border-gray-100 py-4 sticky top-[88px] z-30 shadow-sm">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex gap-2 overflow-x-auto pb-1">
            <button onClick={() => setActiveCategory(null)} className={`flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-all ${!activeCategory ? 'bg-[#0D54AF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}>همه</button>
            {categories.map(cat => (
              <button key={cat.slug} onClick={() => setActiveCategory(activeCategory === cat.slug ? null : cat.slug)} className={`flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium transition-all ${activeCategory === cat.slug ? 'bg-[#0D54AF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}>
                {cat.icon} {cat.label}
                <span className="text-xs opacity-70">({cat.count})</span>
              </button>
            ))}
          </div>
        </div>
      </div>

      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filtered.map((article) => (
              <Link
                key={article.slug}
                to={`/knowledge-base/${article.slug}`}
                className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all card-hover group"
              >
                <div className="relative h-48 overflow-hidden bg-gray-100">
                  <img src={article.img} alt={article.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  {article.featured && (
                    <span className="absolute top-3 right-3 bg-[#08CBCD] text-white text-xs px-2 py-1 rounded-full font-medium">ویژه</span>
                  )}
                </div>
                <div className="p-5">
                  <div className="flex items-center gap-2 mb-3">
                    <span className="bg-blue-50 text-[#0D54AF] text-xs px-2 py-1 rounded-full">{article.categoryLabel}</span>
                    <span className={`text-xs px-2 py-0.5 rounded-full ${difficultyColors[article.difficulty]}`}>{article.difficulty}</span>
                  </div>
                  <h3 className="font-bold text-[#071F3F] mb-2 group-hover:text-[#0D54AF] transition-colors line-clamp-2">{article.title}</h3>
                  <p className="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-3">{article.excerpt}</p>
                  <div className="flex items-center justify-between text-xs text-gray-400">
                    <span>{article.date}</span>
                    <span>⏱️ {article.readTime}</span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
          {filtered.length === 0 && (
            <div className="text-center py-16 text-gray-400">
              <div className="text-5xl mb-4">📚</div>
              <p>مقاله‌ای یافت نشد</p>
            </div>
          )}
        </div>
      </section>
    </div>
  )
}
