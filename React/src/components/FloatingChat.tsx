import { useState } from 'react'

const channels = [
  { icon: '📞', label: 'تماس تلفنی', sub: '۰۲۱-۸۸۸۸۸۸۸۸', href: 'tel:+982188888888', color: '#0D54AF' },
  { icon: '💬', label: 'واتساپ', sub: 'پیام آنلاین', href: 'https://wa.me/989121234567', color: '#25D366' },
  { icon: '✈️', label: 'تلگرام', sub: '@fasdentclinic', href: 'https://t.me/fasdentclinic', color: '#0088cc' },
  { icon: '📸', label: 'اینستاگرام', sub: '@fasdent.ir', href: 'https://instagram.com/fasdent.ir', color: '#E1306C' },
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
              <svg className="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <div className="p-2">
            {channels.map(ch => (
              <a
                key={ch.label}
                href={ch.href}
                target={ch.href.startsWith('http') ? '_blank' : undefined}
                rel="noopener noreferrer"
                className="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group"
              >
                <div
                  className="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                  style={{ backgroundColor: ch.color + '20' }}
                >
                  {ch.icon}
                </div>
                <div>
                  <p className="text-sm font-semibold text-gray-800">{ch.label}</p>
                  <p className="text-xs text-gray-500 ltr">{ch.sub}</p>
                </div>
              </a>
            ))}
          </div>
        </div>
      )}
      <button
        onClick={() => setOpen(!open)}
        className="relative w-14 h-14 bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] rounded-full shadow-2xl flex items-center justify-center text-white hover:scale-110 transition-transform float-chat-btn"
        aria-label="تماس با کلینیک"
        aria-expanded={open}
      >
        {open ? (
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
        ) : (
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
        )}
      </button>
    </div>
  )
}
