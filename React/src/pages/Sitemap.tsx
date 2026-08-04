import { Link } from 'react-router'
import { useState } from 'react'
import Breadcrumb from '../components/Breadcrumb'
import { services, serviceCategories } from '../data/services'

const mainLinks = [
  { title: 'صفحه اصلی', href: '/', icon: '🏠' },
  { title: 'درباره ما', href: '/about', icon: '👨‍⚕️' },
  { title: 'قیمت‌ها', href: '/pricing', icon: '💰' },
  { title: 'گالری', href: '/gallery', icon: '📷' },
  { title: 'رزرو نوبت', href: '/appointment', icon: '📅' },
  { title: 'تماس با ما', href: '/contact', icon: '📞' },
  { title: 'پرسش‌های متداول', href: '/faq', icon: '❓' },
  { title: 'وبلاگ', href: '/knowledge-base', icon: '📚' },
]

const policyLinks = [
  { title: 'سیاست حریم خصوصی', href: '/privacy-policy' },
  { title: 'سلب مسئولیت پزشکی', href: '/medical-disclaimer' },
  { title: 'سیاست لغو نوبت', href: '/cancellation-policy' },
  { title: 'حقوق بیمار', href: '/patient-rights' },
]

export default function Sitemap() {
  const [search, setSearch] = useState('')

  const allLinks = [
    ...mainLinks.map(l => ({ ...l, category: 'صفحات اصلی' })),
    ...serviceCategories.map(c => ({ title: c.title, href: `/services/${c.slug}/${services.find(s => s.categorySlug === c.slug)?.slug || ''}`, icon: c.icon, category: 'دپارتمان‌های تخصصی' })),
    ...services.map(s => ({ title: s.title, href: `/services/${s.categorySlug}/${s.slug}`, icon: s.icon, category: `خدمات - ${s.category}` })),
    ...policyLinks.map(l => ({ ...l, icon: '📋', category: 'صفحات قانونی' })),
  ]

  const filtered = search
    ? allLinks.filter(l => l.title.includes(search) || l.category.includes(search))
    : allLinks

  const grouped = filtered.reduce<Record<string, typeof allLinks>>((acc, link) => {
    const cat = link.category
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(link)
    return acc
  }, {})

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'نقشه سایت' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-6">نقشه سایت</h1>
          <div className="relative max-w-xl">
            <input
              type="search"
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="جستجوی صفحه..."
              className="w-full bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-3 text-white placeholder-white/60 focus:border-[#08CBCD] outline-none text-sm"
            />
            <svg className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          </div>
        </div>
      </div>

      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="space-y-8">
            {Object.entries(grouped).map(([category, links]) => (
              <div key={category} className="bg-white rounded-2xl p-6 shadow-sm">
                <h2 className="font-bold text-[#071F3F] text-lg mb-4 pb-2 border-b border-gray-100">{category}</h2>
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                  {links.map(link => (
                    <Link
                      key={link.href}
                      to={link.href}
                      className="flex items-center gap-2 p-3 rounded-xl hover:bg-blue-50 hover:text-[#0D54AF] transition-colors text-gray-700 group text-sm"
                    >
                      <span className="text-lg">{link.icon}</span>
                      <span className="group-hover:text-[#0D54AF]">{link.title}</span>
                    </Link>
                  ))}
                </div>
              </div>
            ))}
          </div>

          {Object.keys(grouped).length === 0 && (
            <div className="text-center py-16 text-gray-400">
              <div className="text-5xl mb-4">🔍</div>
              <p>صفحه‌ای یافت نشد</p>
            </div>
          )}
        </div>
      </section>
    </div>
  )
}
