import { useState } from 'react'
import Breadcrumb from '../components/Breadcrumb'
import { Link } from 'react-router'

const categories = ['همه', 'ایمپلنت', 'ارتودنسی', 'طراحی لبخند', 'لمینت', 'کراون', 'سفیدکردن']

const galleryItems = [
  { cat: 'طراحی لبخند', title: 'طراحی لبخند کامل', before: 'دندان‌های زرد و نامرتب', after: 'لبخند درخشان با لمینت', img: 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=600&h=450&fit=crop&auto=format', badge: 'محبوب' },
  { cat: 'ایمپلنت', title: 'ایمپلنت تکی', before: 'فضای خالی دندان', after: 'دندان طبیعی با ایمپلنت', img: 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=600&h=450&fit=crop&auto=format', badge: '' },
  { cat: 'ارتودنسی', title: 'اصلاح اوکلوژن', before: 'دندان‌های ازدحام', after: 'ردیف منظم دندان‌ها', img: 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=600&h=450&fit=crop&auto=format', badge: '' },
  { cat: 'لمینت', title: 'لمینت ۸ دندان', before: 'دندان‌های کوچک و شکسته', after: 'لبخند هالیوودی', img: 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=600&h=450&fit=crop&auto=format', badge: 'جدید' },
  { cat: 'کراون', title: 'کراون زیرکونیا', before: 'دندان شکسته', after: 'کراون کامل طبیعی', img: 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=600&h=450&fit=crop&auto=format', badge: '' },
  { cat: 'سفیدکردن', title: 'بلیچینگ حرفه‌ای', before: 'دندان‌های تیره', after: '۸ سایه روشن‌تر', img: 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=600&h=450&fit=crop&auto=format', badge: '' },
  { cat: 'طراحی لبخند', title: 'طراحی لبخند DSD', before: 'عدم تناسب دندانی', after: 'لبخند متناسب با صورت', img: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=600&h=450&fit=crop&auto=format', badge: '' },
  { cat: 'ایمپلنت', title: 'All-on-4', before: 'بی‌دندانی کامل', after: 'دندان‌های ثابت جدید', img: 'https://images.unsplash.com/photo-1588776814546-1ffbb2b1b3e1?w=600&h=450&fit=crop&auto=format', badge: 'ویژه' },
  { cat: 'لمینت', title: 'لمینت ترکیبی', before: 'فاصله بین دندان‌ها', after: 'دندان‌های صاف و متراکم', img: 'https://images.unsplash.com/photo-1617529497471-9218633199c0?w=600&h=450&fit=crop&auto=format', badge: '' },
]

export default function Gallery() {
  const [activeCategory, setActiveCategory] = useState('همه')
  const [lightbox, setLightbox] = useState<number | null>(null)

  const filtered = activeCategory === 'همه' ? galleryItems : galleryItems.filter(g => g.cat === activeCategory)

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'گالری' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-4">گالری نمونه کارها</h1>
          <p className="text-white/80 text-lg max-w-2xl">مشاهده تحول واقعی در لبخند بیماران ما</p>
        </div>
      </div>

      {/* Filter */}
      <section className="py-6 bg-white border-b border-gray-100 sticky top-[88px] z-30 shadow-sm">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex gap-2 overflow-x-auto pb-1">
            {categories.map(cat => (
              <button
                key={cat}
                onClick={() => setActiveCategory(cat)}
                className={`flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-all ${activeCategory === cat ? 'bg-[#0D54AF] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Gallery grid */}
      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filtered.map((item, i) => (
              <div
                key={i}
                className="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all card-hover cursor-pointer"
                onClick={() => setLightbox(galleryItems.indexOf(item))}
              >
                <div className="relative h-56 overflow-hidden bg-gray-100">
                  <img src={item.img} alt={item.title} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                  {item.badge && (
                    <span className="absolute top-3 right-3 bg-[#08CBCD] text-white text-xs px-2 py-1 rounded-full font-medium">
                      {item.badge}
                    </span>
                  )}
                  <div className="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                    <svg className="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                  </div>
                </div>
                <div className="p-4">
                  <span className="inline-block bg-blue-50 text-[#0D54AF] text-xs px-2 py-0.5 rounded-full mb-2">{item.cat}</span>
                  <h3 className="font-bold text-[#071F3F] mb-2">{item.title}</h3>
                  <div className="flex gap-2 text-xs">
                    <div className="flex-1 bg-red-50 text-red-600 rounded-lg px-2 py-1.5">
                      <div className="font-medium mb-0.5">قبل</div>
                      <div className="text-red-500">{item.before}</div>
                    </div>
                    <div className="flex-1 bg-green-50 text-green-600 rounded-lg px-2 py-1.5">
                      <div className="font-medium mb-0.5">بعد</div>
                      <div className="text-green-500">{item.after}</div>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
          {filtered.length === 0 && (
            <div className="text-center py-20 text-gray-400">
              <div className="text-5xl mb-4">📷</div>
              <p>نمونه‌ای در این دسته‌بندی یافت نشد</p>
            </div>
          )}
        </div>
      </section>

      {/* Lightbox */}
      {lightbox !== null && (
        <div
          className="fixed inset-0 bg-black/90 z-[200] flex items-center justify-center p-4"
          onClick={() => setLightbox(null)}
        >
          <div className="relative max-w-2xl w-full" onClick={e => e.stopPropagation()}>
            <button
              onClick={() => setLightbox(null)}
              className="absolute -top-10 left-0 text-white hover:text-[#08CBCD] transition-colors"
              aria-label="بستن"
            >
              <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <img src={galleryItems[lightbox].img} alt={galleryItems[lightbox].title} className="w-full rounded-2xl" />
            <div className="mt-4 text-white text-center">
              <h3 className="font-bold text-lg">{galleryItems[lightbox].title}</h3>
              <div className="flex justify-center gap-6 mt-2 text-sm text-gray-300">
                <span>قبل: {galleryItems[lightbox].before}</span>
                <span>→</span>
                <span className="text-[#08CBCD]">بعد: {galleryItems[lightbox].after}</span>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* CTA */}
      <section className="py-16 bg-gradient-to-l from-[#08CBCD] to-[#0D54AF] text-white">
        <div className="max-w-3xl mx-auto px-4 text-center">
          <h2 className="text-3xl font-black mb-4">لبخند رویایی خود را بسازید</h2>
          <p className="text-white/80 mb-6">با مشاوره رایگان شروع کنید</p>
          <Link to="/appointment" className="inline-flex items-center gap-2 bg-white text-[#0D54AF] font-bold px-8 py-4 rounded-xl hover:shadow-2xl transition-all">
            رزرو مشاوره رایگان
          </Link>
        </div>
      </section>
    </div>
  )
}
