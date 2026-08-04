import { Link } from 'react-router'
import { useWordPress } from '../contexts/WordPressContext'
import { MenuItem } from '../types/wordpress'

// Convert WordPress menu items to simple links
function convertMenuToLinks(menuItems: MenuItem[]): Array<{ label: string; href: string }> {
  const result: Array<{ label: string; href: string }> = []
  
  menuItems.forEach(item => {
    result.push({
      label: item.title,
      href: item.url.replace(window.FASDENT_REACT?.site?.url || 'https://fasdent.ir', ''),
    })
    
    item.children.forEach(child => {
      result.push({
        label: child.title,
        href: child.url.replace(window.FASDENT_REACT?.site?.url || 'https://fasdent.ir', ''),
      })
    })
  })
  
  return result
}

export default function Footer() {
  const { data, isLoaded } = useWordPress()
  
  // Get phone number from WordPress data
  const phoneNumber = isLoaded ? data.phone : '09201441469'
  const phoneLink = isLoaded ? data.phone_link : '+989201441469'
  
  // Get footer menu from WordPress
  const footerLinks = isLoaded && data.menus.footer.length > 0 
    ? convertMenuToLinks(data.menus.footer)
    : [
        { label: 'درباره ما', href: '/about' },
        { label: 'قیمت‌ها', href: '/pricing' },
        { label: 'گالری', href: '/gallery' },
        { label: 'وبلاگ', href: '/knowledge-base' },
        { label: 'سوالات متداول', href: '/faq' },
        { label: 'تماس با ما', href: '/contact' },
      ]

  const legalLinks = isLoaded && data.menus.legal.length > 0 
    ? convertMenuToLinks(data.menus.legal)
    : [
        { label: 'حریم خصوصی', href: '/privacy-policy' },
        { label: 'قوانین و مقررات', href: '/cancellation-policy' },
        { label: 'حقوق بیماران', href: '/patient-rights' },
        { label: 'سلب مسئولیت پزشکی', href: '/medical-disclaimer' },
        { label: 'نقشه سایت', href: '/sitemap' },
      ]

  const servicesLinks = [
    { label: 'ایمپلنت دندان', href: '/services/dental-implant/single-implant' },
    { label: 'ارتودنسی', href: '/services/orthodontics/metal-braces' },
    { label: 'ترمیم لبخند', href: '/services/cosmetic-dentistry/smile-design' },
    { label: 'عصب‌کشی', href: '/services/endodontics/root-canal-treatment' },
    { label: 'کرون زیرکونیا', href: '/services/prosthodontics/zirconia-crown' },
    { label: 'لمینت دندان', href: '/services/cosmetic-dentistry/dental-laminate' },
  ]

  return (
    <footer className="bg-[#071F3F] text-white" dir="rtl">
      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* Brand */}
          <div>
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] flex items-center justify-center">
                <span className="text-2xl">🦷</span>
              </div>
              <div>
                <div className="font-bold text-white">کلینیک دندانپزشکی</div>
                <div className="text-[#08CBCD] text-sm">دکتر کیوان صمدی</div>
              </div>
            </div>
            <p className="text-gray-400 text-sm leading-relaxed mb-4">
              ارائه خدمات دندانپزشکی تخصصی با بالاترین استانداردهای کیفی توسط دکتر کیوان صمدی
            </p>
            <div className="flex gap-3">
              {[
                { name: 'instagram', icon: '📸', href: 'https://instagram.com/fasdent' },
                { name: 'telegram', icon: '✉️', href: 'https://t.me/fasdent' },
                { name: 'whatsapp', icon: '💬', href: `https://wa.me/${phoneLink}` },
              ].map(({ name, icon, href }) => (
                <a key={name} href={href} className="w-9 h-9 bg-white/10 hover:bg-[#08CBCD] rounded-lg flex items-center justify-center transition-colors" aria-label={name} target="_blank" rel="noopener noreferrer">
                  <span className="text-sm">{icon}</span>
                </a>
              ))}
            </div>
            
            {/* Contact Info */}
            <div className="mt-6 space-y-3">
              <div className="flex items-center gap-3">
                <svg className="w-5 h-5 text-[#08CBCD]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <a href={`tel:${phoneLink}`} className="text-sm hover:text-[#08CBCD] transition-colors ltr">{phoneNumber}</a>
              </div>
              <div className="flex items-center gap-3">
                <svg className="w-5 h-5 text-[#08CBCD]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span className="text-sm">تهران، خیابان ولیعصر، ساختمان آسمان، طبقه ۳، واحد ۳۰۱</span>
              </div>
              <div className="flex items-center gap-3">
                <svg className="w-5 h-5 text-[#08CBCD]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span className="text-sm">شنبه تا پنجشنبه ۹ صبح تا ۲۰</span>
              </div>
            </div>
          </div>

          {/* Services */}
          <div>
            <h3 className="font-bold text-white mb-4 pb-2 border-b border-white/10">خدمات ما</h3>
            <ul className="space-y-2">
              {servicesLinks.map(({ label, href }) => (
                <li key={href}>
                  <Link to={href} className="text-gray-400 hover:text-[#08CBCD] text-sm transition-colors flex items-center gap-2">
                    <span className="w-1 h-1 rounded-full bg-[#08CBCD]" />
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="font-bold text-white mb-4 pb-2 border-b border-white/10">لینک‌های سریع</h3>
            <ul className="space-y-2">
              {footerLinks.map(({ label, href }) => (
                <li key={href}>
                  <Link to={href} className="text-gray-400 hover:text-[#08CBCD] text-sm transition-colors flex items-center gap-2">
                    <span className="w-1 h-1 rounded-full bg-[#08CBCD]" />
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Legal */}
          <div>
            <h3 className="font-bold text-white mb-4 pb-2 border-b border-white/10">قوانین</h3>
            <ul className="space-y-2">
              {legalLinks.map(({ label, href }) => (
                <li key={href}>
                  <Link to={href} className="text-gray-400 hover:text-[#08CBCD] text-sm transition-colors flex items-center gap-2">
                    <span className="w-1 h-1 rounded-full bg-[#08CBCD]" />
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Bottom bar */}
        <div className="mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
          <div className="text-gray-400 text-sm">
            © {new Date().getFullYear()} کلیه حقوق مادی و معنوی این سایت متعلق به کلینیک دندانپزشکی فسدنت می‌باشد.
          </div>
          <Link
            to="/appointment"
            className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity"
          >
            رزرو نوبت آنلاین
          </Link>
        </div>
      </div>
    </footer>
  )
}
