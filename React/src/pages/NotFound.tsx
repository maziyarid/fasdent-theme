import { Link } from 'react-router'
import { services } from '../data/services'

const popularLinks = [
  { label: 'صفحه اصلی', href: '/', icon: '🏠' },
  { label: 'خدمات', href: '/services', icon: '🦷' },
  { label: 'رزرو نوبت', href: '/appointment', icon: '📅' },
  { label: 'تماس', href: '/contact', icon: '📞' },
  { label: 'پرسش‌های متداول', href: '/faq', icon: '❓' },
  { label: 'وبلاگ', href: '/knowledge-base', icon: '📚' },
]

export default function NotFound() {
  return (
    <div className="pt-[88px] min-h-screen bg-[#F4F9FF] flex items-center justify-center py-16">
      <div className="max-w-2xl mx-auto px-4 text-center">
        <div className="text-8xl mb-6">😅</div>
        <h1 className="text-8xl font-black gradient-text mb-4">۴۰۴</h1>
        <h2 className="text-2xl font-bold text-[#071F3F] mb-3">صفحه مورد نظر پیدا نشد</h2>
        <p className="text-gray-500 mb-10 leading-relaxed">
          متأسفیم! صفحه‌ای که دنبالش می‌گردید وجود ندارد یا منتقل شده است.
          می‌توانید به صفحه اصلی برگردید یا از لینک‌های زیر استفاده کنید.
        </p>

        <div className="grid grid-cols-2 md:grid-cols-3 gap-3 mb-10">
          {popularLinks.map(link => (
            <Link
              key={link.href}
              to={link.href}
              className="flex items-center gap-2 bg-white rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-[#08CBCD] border border-transparent transition-all group"
            >
              <span className="text-2xl">{link.icon}</span>
              <span className="text-sm font-medium text-gray-700 group-hover:text-[#0D54AF] transition-colors">{link.label}</span>
            </Link>
          ))}
        </div>

        <div className="flex justify-center gap-4">
          <Link
            to="/"
            className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white px-8 py-3 rounded-xl font-bold hover:opacity-90 transition-opacity"
          >
            بازگشت به صفحه اصلی
          </Link>
          <a
            href="tel:+982188888888"
            className="border-2 border-[#0D54AF] text-[#0D54AF] px-8 py-3 rounded-xl font-bold hover:bg-[#0D54AF] hover:text-white transition-all ltr"
          >
            تماس با ما
          </a>
        </div>

        {/* Popular services */}
        <div className="mt-12 bg-white rounded-2xl p-6 shadow-sm text-right">
          <h3 className="font-bold text-[#071F3F] mb-4">خدمات پرطرفدار</h3>
          <div className="flex flex-wrap gap-2">
            {services.slice(0, 8).map(s => (
              <Link
                key={s.slug}
                to={`/services/${s.categorySlug}/${s.slug}`}
                className="bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-[#0D54AF] px-3 py-1.5 rounded-full text-sm transition-colors"
              >
                {s.title}
              </Link>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
