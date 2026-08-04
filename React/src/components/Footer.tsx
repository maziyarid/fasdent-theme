import { Link } from 'react-router'

export default function Footer() {
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
                <div className="text-[#08CBCD] text-sm">دکتر کیوان علی‌پسندی</div>
              </div>
            </div>
            <p className="text-gray-400 text-sm leading-relaxed mb-4">
              ارائه خدمات دندانپزشکی تخصصی با بالاترین استانداردهای کیفی در تهران
            </p>
            <div className="flex gap-3">
              {['instagram', 'telegram', 'whatsapp'].map(s => (
                <a key={s} href="#" className="w-9 h-9 bg-white/10 hover:bg-[#08CBCD] rounded-lg flex items-center justify-center transition-colors" aria-label={s}>
                  <span className="text-sm">{s === 'instagram' ? '📸' : s === 'telegram' ? '✈️' : '💬'}</span>
                </a>
              ))}
            </div>
          </div>

          {/* Services */}
          <div>
            <h3 className="font-bold text-white mb-4 pb-2 border-b border-white/10">خدمات ما</h3>
            <ul className="space-y-2">
              {[
                ['ایمپلنت دندان', '/services/dental-implant/single-implant'],
                ['ارتودنسی', '/services/orthodontics/metal-braces'],
                ['طراحی لبخند', '/services/cosmetic-dentistry/smile-design'],
                ['عصب‌کشی', '/services/endodontics/root-canal-treatment'],
                ['کراون زیرکونیا', '/services/prosthodontics/zirconia-crown'],
                ['لمینت دندان', '/services/cosmetic-dentistry/dental-laminate'],
              ].map(([label, href]) => (
                <li key={href}>
                  <Link to={href} className="text-gray-400 hover:text-[#08CBCD] text-sm transition-colors flex items-center gap-2">
                    <span className="w-1 h-1 rounded-full bg-[#08CBCD]" />
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Links */}
          <div>
            <h3 className="font-bold text-white mb-4 pb-2 border-b border-white/10">لینک‌های مفید</h3>
            <ul className="space-y-2">
              {[
                ['درباره ما', '/about'],
                ['قیمت‌ها', '/pricing'],
                ['گالری نمونه کارها', '/gallery'],
                ['پرسش‌های متداول', '/faq'],
                ['وبلاگ', '/knowledge-base'],
                ['رزرو نوبت', '/appointment'],
                ['حقوق بیمار', '/patient-rights'],
                ['سیاست حریم خصوصی', '/privacy-policy'],
              ].map(([label, href]) => (
                <li key={href}>
                  <Link to={href} className="text-gray-400 hover:text-[#08CBCD] text-sm transition-colors flex items-center gap-2">
                    <span className="w-1 h-1 rounded-full bg-[#08CBCD]" />
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="font-bold text-white mb-4 pb-2 border-b border-white/10">اطلاعات تماس</h3>
            <ul className="space-y-3">
              <li className="flex items-start gap-3 text-sm text-gray-400">
                <svg className="w-5 h-5 text-[#08CBCD] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span>تهران، خیابان ولیعصر، بالاتر از میدان ونک، پلاک ۱۲۳</span>
              </li>
              <li className="flex items-center gap-3 text-sm">
                <svg className="w-5 h-5 text-[#08CBCD] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                <a href="tel:+982188888888" className="text-gray-400 hover:text-[#08CBCD] transition-colors ltr">۰۲۱-۸۸۸۸۸۸۸۸</a>
              </li>
              <li className="flex items-center gap-3 text-sm">
                <svg className="w-5 h-5 text-[#08CBCD] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                <a href="mailto:info@fasdent.ir" className="text-gray-400 hover:text-[#08CBCD] transition-colors ltr">info@fasdent.ir</a>
              </li>
              <li className="flex items-start gap-3 text-sm text-gray-400">
                <svg className="w-5 h-5 text-[#08CBCD] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>شنبه تا پنج‌شنبه: ۹:۰۰ - ۲۰:۰۰<br/>جمعه: تعطیل</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom bar */}
      <div className="border-t border-white/10 py-4 px-4">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-gray-500">
          <p>© ۱۴۰۳ کلینیک دندانپزشکی دکتر کیوان علی‌پسندی. تمامی حقوق محفوظ است.</p>
          <div className="flex items-center gap-4">
            <Link to="/privacy-policy" className="hover:text-[#08CBCD] transition-colors">حریم خصوصی</Link>
            <Link to="/medical-disclaimer" className="hover:text-[#08CBCD] transition-colors">سلب مسئولیت</Link>
            <Link to="/sitemap" className="hover:text-[#08CBCD] transition-colors">نقشه سایت</Link>
          </div>
        </div>
      </div>
    </footer>
  )
}
