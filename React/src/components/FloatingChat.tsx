import { useState } from 'react'
import FaIcon from './FaIcon'
import { clinic } from '../data/clinic'

const channels = [
  { icon: 'fa-solid fa-phone', label: 'تماس تلفنی', sub: clinic.phoneDisplay, href: clinic.phoneLink, color: '#0D54AF' },
  { icon: 'fa-brands fa-whatsapp', label: 'واتساپ', sub: 'پیام آنلاین', href: clinic.whatsapp, color: '#25D366' },
  { icon: 'fa-brands fa-instagram', label: 'اینستاگرام پزشک', sub: '@' + clinic.instagramDoctor, href: clinic.instagramDoctorUrl, color: '#E1306C' },
  { icon: 'fa-brands fa-instagram', label: 'اینستاگرام کلینیک', sub: '@' + clinic.instagramClinic, href: clinic.instagramClinicUrl, color: '#C13584' },
]

export default function FloatingChat() {
  const [open, setOpen] = useState(false)

  return (
    <div className="fixed bottom-6 left-6 z-50 flex flex-col items-end gap-3" dir="rtl">
      {open && (
        <div
          className="bg-white rounded-2xl shadow-2xl border border-gray-100 w-64 overflow-hidden animate-fade-up"
          role="dialog"
          aria-label="راه‌های ارتباطی"
        >
          <div className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] px-4 py-3 flex items-center justify-between">
            <div>
              <p className="text-white font-bold text-sm">ارتباط با ما</p>
              <p className="text-white/80 text-xs">روش ارتباطی خود را انتخاب کنید</p>
            </div>
            <button
              onClick={() => setOpen(false)}
              className="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition-colors"
              aria-label="بستن"
            >
              <FaIcon icon="fa-solid fa-xmark" className="text-white text-sm" />
            </button>
          </div>
          <div className="p-2">
            {channels.map((ch) => (
              <a
                key={ch.label}
                href={ch.href}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors"
              >
                <span
                  className="w-10 h-10 rounded-full flex items-center justify-center text-white text-lg"
                  style={{ backgroundColor: ch.color }}
                >
                  <FaIcon icon={ch.icon} />
                </span>
                <span className="text-right">
                  <span className="block text-sm font-bold text-[#071F3F]">{ch.label}</span>
                  <span className="block text-xs text-gray-500 ltr" dir="ltr">{ch.sub}</span>
                </span>
              </a>
            ))}
          </div>
        </div>
      )}
      <button
        onClick={() => setOpen((v) => !v)}
        className="w-14 h-14 rounded-full bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] text-white shadow-lg flex items-center justify-center text-xl hover:scale-105 transition-transform"
        aria-label={open ? 'بستن منوی ارتباط' : 'باز کردن منوی ارتباط'}
      >
        <FaIcon icon={open ? 'fa-solid fa-xmark' : 'fa-solid fa-comments'} />
      </button>
    </div>
  )
}
