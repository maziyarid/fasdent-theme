import { useParams, Link } from 'react-router'
import { useState } from 'react'
import Breadcrumb from '../components/Breadcrumb'
import { getService, getServicesByCategory, serviceCategories } from '../data/services'

export default function ServiceDetail() {
  const { category = '', service = '' } = useParams()
  const [openFaq, setOpenFaq] = useState<number | null>(null)

  const serviceData = getService(category, service)
  const catData = serviceCategories.find(c => c.slug === category)
  const relatedServices = getServicesByCategory(category).filter(s => s.slug !== service).slice(0, 3)

  if (!serviceData) {
    return (
      <div className="pt-[88px] min-h-screen flex items-center justify-center bg-[#F4F9FF]">
        <div className="text-center">
          <div className="text-6xl mb-4">🦷</div>
          <h2 className="text-2xl font-black text-[#071F3F] mb-2">خدمت یافت نشد</h2>
          <Link to="/services" className="text-[#0D54AF] hover:text-[#08CBCD] transition-colors">
            بازگشت به خدمات
          </Link>
        </div>
      </div>
    )
  }

  return (
    <div className="pt-[88px]">
      {/* Hero */}
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-12">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[
            { label: 'خدمات', href: '/services' },
            { label: catData?.title || category, href: `/services/${category}/${service}` },
            { label: serviceData.title },
          ]} />
          <div className="mt-4 flex items-start gap-4">
            <div className="text-5xl">{serviceData.icon}</div>
            <div>
              <h1 className="text-3xl md:text-4xl font-black mb-2">{serviceData.title}</h1>
              <p className="text-white/80">{serviceData.description}</p>
              <div className="flex flex-wrap gap-3 mt-3">
                <span className="bg-white/10 px-3 py-1 rounded-full text-sm">⏱️ {serviceData.duration}</span>
                <span className="bg-white/10 px-3 py-1 rounded-full text-sm">💰 {serviceData.price}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-10">
        <div className="grid lg:grid-cols-3 gap-8">
          {/* Main content */}
          <div className="lg:col-span-2 space-y-8">
            {/* Key Takeaways */}
            <div className="bg-gradient-to-l from-[#E8F4FD] to-[#E0F7F7] border border-[#08CBCD]/30 rounded-2xl p-5">
              <h2 className="flex items-center gap-2 font-bold text-[#071F3F] mb-3">
                <svg className="w-5 h-5 text-[#08CBCD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                نکات کلیدی
              </h2>
              <ul className="space-y-2">
                {serviceData.keyTakeaways.map((item, i) => (
                  <li key={i} className="flex items-center gap-2 text-sm text-gray-700">
                    <span className="w-5 h-5 rounded-full bg-[#08CBCD] text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">{i + 1}</span>
                    {item}
                  </li>
                ))}
              </ul>
            </div>

            {/* Hero image */}
            <div className="rounded-2xl overflow-hidden shadow-lg">
              <img src={serviceData.image} alt={serviceData.title} className="w-full h-64 object-cover" />
            </div>

            {/* Description */}
            <div className="bg-white rounded-2xl p-6 shadow-sm">
              <h2 className="text-xl font-bold text-[#071F3F] mb-4">{serviceData.title} چیست؟</h2>
              <p className="text-gray-600 leading-relaxed">{serviceData.longDescription}</p>
            </div>

            {/* Benefits */}
            <div className="bg-white rounded-2xl p-6 shadow-sm">
              <h2 className="text-xl font-bold text-[#071F3F] mb-4">مزایا</h2>
              <div className="grid grid-cols-2 gap-3">
                {serviceData.benefits.map((benefit, i) => (
                  <div key={i} className="flex items-center gap-2 bg-green-50 rounded-xl p-3 text-sm text-gray-700">
                    <svg className="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" /></svg>
                    {benefit}
                  </div>
                ))}
              </div>
            </div>

            {/* Steps */}
            <div className="bg-white rounded-2xl p-6 shadow-sm">
              <h2 className="text-xl font-bold text-[#071F3F] mb-5">مراحل درمان</h2>
              <div className="space-y-4">
                {serviceData.steps.map((step, i) => (
                  <div key={i} className="flex items-start gap-4">
                    <div className="w-10 h-10 rounded-full bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] text-white flex items-center justify-center font-bold flex-shrink-0">
                      {i + 1}
                    </div>
                    <div className="flex-1 border-b border-gray-100 pb-4">
                      <h3 className="font-bold text-[#071F3F] mb-1">{step.title}</h3>
                      <p className="text-gray-500 text-sm">{step.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* FAQ */}
            <div className="bg-white rounded-2xl p-6 shadow-sm" itemScope itemType="https://schema.org/FAQPage">
              <h2 className="text-xl font-bold text-[#071F3F] mb-4">سوالات متداول</h2>
              <div className="space-y-3">
                {serviceData.faq.map((faq, i) => (
                  <div key={i} className="border border-gray-100 rounded-xl overflow-hidden" itemScope itemType="https://schema.org/Question">
                    <button
                      onClick={() => setOpenFaq(openFaq === i ? null : i)}
                      className="w-full flex items-center justify-between p-4 text-right hover:bg-gray-50 transition-colors"
                      aria-expanded={openFaq === i}
                    >
                      <span className="font-medium text-sm text-[#071F3F]" itemProp="name">{faq.q}</span>
                      <svg className={`w-4 h-4 text-[#0D54AF] flex-shrink-0 transition-transform ${openFaq === i ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    {openFaq === i && (
                      <div className="px-4 pb-4 text-sm text-gray-600 border-t border-gray-100 pt-3" itemScope itemType="https://schema.org/Answer" itemProp="acceptedAnswer">
                        <p itemProp="text">{faq.a}</p>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>

            {/* Tags */}
            <div className="flex flex-wrap gap-2">
              {serviceData.tags.map(tag => (
                <Link
                  key={tag}
                  to={`/tag/${tag}`}
                  className="bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-[#0D54AF] px-3 py-1 rounded-full text-sm transition-colors"
                >
                  #{tag}
                </Link>
              ))}
            </div>
          </div>

          {/* Sidebar */}
          <div className="space-y-5">
            {/* CTA card */}
            <div className="bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] rounded-2xl p-6 text-white sticky top-28">
              <h3 className="font-bold text-xl mb-2">آماده شروع؟</h3>
              <p className="text-white/80 text-sm mb-4">با ما تماس بگیرید یا نوبت رزرو کنید</p>
              <div className="text-2xl font-black mb-1">{serviceData.price}</div>
              <p className="text-xs text-white/60 mb-4">* قیمت ممکن است بسته به وضعیت متفاوت باشد</p>
              <Link
                to="/appointment"
                className="block bg-white text-[#0D54AF] text-center py-3 rounded-xl font-bold text-sm hover:shadow-xl transition-all mb-2"
              >
                رزرو نوبت رایگان
              </Link>
              <a
                href="tel:+982188888888"
                className="block border-2 border-white text-white text-center py-2.5 rounded-xl font-semibold text-sm hover:bg-white/10 transition-all ltr"
              >
                ۰۲۱-۸۸۸۸۸۸۸۸
              </a>
            </div>

            {/* Related services */}
            {relatedServices.length > 0 && (
              <div className="bg-white rounded-2xl p-5 shadow-sm">
                <h3 className="font-bold text-[#071F3F] mb-4">خدمات مرتبط</h3>
                <div className="space-y-3">
                  {relatedServices.map(related => (
                    <Link
                      key={related.slug}
                      to={`/services/${related.categorySlug}/${related.slug}`}
                      className="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group"
                    >
                      <div className="text-2xl">{related.icon}</div>
                      <div>
                        <p className="font-medium text-sm text-[#071F3F] group-hover:text-[#0D54AF] transition-colors">{related.title}</p>
                        <p className="text-xs text-gray-500">{related.price}</p>
                      </div>
                    </Link>
                  ))}
                </div>
              </div>
            )}

            {/* Doctor card */}
            <div className="bg-white rounded-2xl p-5 shadow-sm">
              <div className="flex items-center gap-3 mb-3">
                <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] flex items-center justify-center text-3xl">👨‍⚕️</div>
                <div>
                  <p className="font-bold text-[#071F3F] text-sm">دکتر کیوان علی‌پسندی</p>
                  <p className="text-xs text-gray-500">متخصص دندانپزشکی</p>
                </div>
              </div>
              <p className="text-xs text-gray-500 leading-relaxed">با ۱۵+ سال تجربه در درمان‌های تخصصی دندانپزشکی</p>
              <Link to="/about" className="block mt-3 text-center text-sm text-[#0D54AF] hover:text-[#08CBCD] transition-colors">
                درباره دکتر بیشتر بدانید ←
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
