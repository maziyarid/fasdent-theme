import { Link } from 'react-router'
import { useState } from 'react'
import Breadcrumb from '../components/Breadcrumb'
import { services, serviceCategories } from '../data/services'

export default function Services() {
  const [activeCategory, setActiveCategory] = useState<string | null>(null)

  const filtered = activeCategory
    ? services.filter(s => s.categorySlug === activeCategory)
    : services

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'خدمات' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-4">خدمات تخصصی</h1>
          <p className="text-white/80 text-lg max-w-2xl">طیف کاملی از خدمات دندانپزشکی با بالاترین کیفیت و مجرب‌ترین متخصصان</p>
        </div>
      </div>

      {/* Category filter */}
      <div className="sticky top-[88px] z-30 bg-white border-b border-gray-100 shadow-sm py-4">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex gap-2 overflow-x-auto pb-1">
            <button
              onClick={() => setActiveCategory(null)}
              className={`flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-all ${!activeCategory ? 'bg-[#0D54AF] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
            >
              همه خدمات
            </button>
            {serviceCategories.map(cat => (
              <button
                key={cat.slug}
                onClick={() => setActiveCategory(activeCategory === cat.slug ? null : cat.slug)}
                className={`flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium transition-all ${activeCategory === cat.slug ? 'bg-[#0D54AF] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
              >
                {cat.icon} {cat.title}
              </button>
            ))}
          </div>
        </div>
      </div>

      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filtered.map(service => (
              <Link
                key={service.slug}
                to={`/services/${service.categorySlug}/${service.slug}`}
                className="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all card-hover"
              >
                <div className="h-40 overflow-hidden bg-gray-100">
                  <img src={service.image} alt={service.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                </div>
                <div className="p-5">
                  <div className="flex items-center justify-between mb-3">
                    <span className="bg-blue-50 text-[#0D54AF] text-xs px-2 py-1 rounded-full">{service.category}</span>
                    <span className="text-xs text-gray-500">{service.duration}</span>
                  </div>
                  <h3 className="font-bold text-[#071F3F] text-lg mb-2 group-hover:text-[#0D54AF] transition-colors">{service.title}</h3>
                  <p className="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-4">{service.description}</p>
                  <div className="flex items-center justify-between">
                    <span className="text-[#0D54AF] font-bold text-sm">{service.price}</span>
                    <span className="flex items-center gap-1 text-[#08CBCD] text-sm font-medium group-hover:gap-2 transition-all">
                      اطلاعات بیشتر
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" /></svg>
                    </span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Category overview */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <h2 className="text-3xl font-black text-[#071F3F] mb-8 text-center">دپارتمان‌های تخصصی</h2>
          <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
            {serviceCategories.map(cat => (
              <button
                key={cat.slug}
                onClick={() => setActiveCategory(cat.slug)}
                className="group text-center bg-[#F4F9FF] hover:bg-gradient-to-br hover:from-[#0D54AF] hover:to-[#08CBCD] rounded-2xl p-5 transition-all duration-300 cursor-pointer"
              >
                <div className="text-4xl mb-3">{cat.icon}</div>
                <h3 className="text-sm font-bold text-[#071F3F] group-hover:text-white transition-colors">{cat.title}</h3>
                <p className="text-xs text-gray-500 group-hover:text-white/70 transition-colors mt-1">
                  {services.filter(s => s.categorySlug === cat.slug).length} درمان
                </p>
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 bg-gradient-to-l from-[#08CBCD] to-[#0D54AF] text-white">
        <div className="max-w-3xl mx-auto px-4 text-center">
          <h2 className="text-3xl font-black mb-4">نمی‌دانید کدام درمان مناسب شماست؟</h2>
          <p className="text-white/80 mb-6">مشاوره رایگان با متخصصان ما داشته باشید</p>
          <Link to="/appointment" className="inline-flex items-center gap-2 bg-white text-[#0D54AF] font-bold px-8 py-4 rounded-xl hover:shadow-2xl transition-all">
            رزرو مشاوره رایگان
          </Link>
        </div>
      </section>
    </div>
  )
}
