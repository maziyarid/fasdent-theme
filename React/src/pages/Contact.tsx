import { useState } from 'react'
import Breadcrumb from '../components/Breadcrumb'

export default function Contact() {
  const [form, setForm] = useState({ name: '', phone: '', email: '', subject: '', message: '' })
  const [sent, setSent] = useState(false)

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setSent(true)
  }

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'تماس با ما' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-4">تماس با ما</h1>
          <p className="text-white/80 text-lg">آماده پاسخگویی به سوالات شما هستیم</p>
        </div>
      </div>

      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid lg:grid-cols-5 gap-8">
            {/* Contact info */}
            <div className="lg:col-span-2 space-y-4">
              {[
                { icon: '📍', title: 'آدرس', lines: ['تهران، خیابان ولیعصر', 'بالاتر از میدان ونک، پلاک ۱۲۳'] },
                { icon: '📞', title: 'تلفن', lines: ['۰۲۱-۸۸۸۸۸۸۸۸', '۰۹۱۲-۱۲۳-۴۵۶۷'] },
                { icon: '📧', title: 'ایمیل', lines: ['info@fasdent.ir'] },
                { icon: '🕐', title: 'ساعات کاری', lines: ['شنبه تا پنج‌شنبه: ۹:۰۰ - ۲۰:۰۰', 'جمعه: تعطیل'] },
              ].map(item => (
                <div key={item.title} className="bg-white rounded-2xl p-5 shadow-sm flex items-start gap-4">
                  <div className="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl flex-shrink-0">
                    {item.icon}
                  </div>
                  <div>
                    <h3 className="font-bold text-[#071F3F] mb-1">{item.title}</h3>
                    {item.lines.map(l => (
                      <p key={l} className="text-sm text-gray-600">{l}</p>
                    ))}
                  </div>
                </div>
              ))}

              {/* Social */}
              <div className="bg-white rounded-2xl p-5 shadow-sm">
                <h3 className="font-bold text-[#071F3F] mb-3">شبکه‌های اجتماعی</h3>
                <div className="flex gap-3">
                  {[
                    { name: 'اینستاگرام', icon: '📸', url: '#' },
                    { name: 'تلگرام', icon: '✈️', url: '#' },
                    { name: 'واتساپ', icon: '💬', url: '#' },
                    { name: 'یوتیوب', icon: '▶️', url: '#' },
                  ].map(s => (
                    <a
                      key={s.name}
                      href={s.url}
                      className="w-10 h-10 bg-gray-100 hover:bg-[#08CBCD] rounded-xl flex items-center justify-center text-lg transition-colors"
                      aria-label={s.name}
                    >
                      {s.icon}
                    </a>
                  ))}
                </div>
              </div>
            </div>

            {/* Form */}
            <div className="lg:col-span-3">
              {sent ? (
                <div className="bg-white rounded-2xl p-10 shadow-sm text-center h-full flex flex-col items-center justify-center">
                  <div className="text-5xl mb-4">✅</div>
                  <h2 className="text-2xl font-black text-[#071F3F] mb-2">پیام شما ارسال شد!</h2>
                  <p className="text-gray-600 mb-6">در اسرع وقت با شما تماس خواهیم گرفت.</p>
                  <button onClick={() => setSent(false)} className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white px-6 py-3 rounded-xl font-semibold">
                    ارسال پیام جدید
                  </button>
                </div>
              ) : (
                <div className="bg-white rounded-2xl p-6 shadow-sm">
                  <h2 className="text-xl font-bold text-[#071F3F] mb-6">ارسال پیام</h2>
                  <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid md:grid-cols-2 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">نام *</label>
                        <input type="text" required value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} placeholder="نام و نام خانوادگی" className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">تلفن *</label>
                        <input type="tel" required value={form.phone} onChange={e => setForm({ ...form, phone: e.target.value })} placeholder="۰۹۱۲..." className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all ltr text-right" dir="ltr" />
                      </div>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1.5">ایمیل</label>
                      <input type="email" value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} placeholder="email@example.com" className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all ltr" dir="ltr" />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1.5">موضوع *</label>
                      <select required value={form.subject} onChange={e => setForm({ ...form, subject: e.target.value })} className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all bg-white">
                        <option value="">انتخاب موضوع...</option>
                        <option>استعلام قیمت</option>
                        <option>سوال درباره خدمات</option>
                        <option>رزرو نوبت</option>
                        <option>انتقاد و پیشنهاد</option>
                        <option>سایر</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1.5">پیام *</label>
                      <textarea required value={form.message} onChange={e => setForm({ ...form, message: e.target.value })} rows={5} placeholder="پیام خود را اینجا بنویسید..." className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all resize-none" />
                    </div>
                    <button type="submit" className="w-full bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white py-3.5 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity">
                      ارسال پیام
                    </button>
                  </form>
                </div>
              )}
            </div>
          </div>

          {/* Map placeholder */}
          <div className="mt-8 bg-white rounded-2xl overflow-hidden shadow-sm h-64 flex items-center justify-center border border-gray-200">
            <div className="text-center text-gray-400">
              <div className="text-5xl mb-2">🗺️</div>
              <p className="font-medium">نقشه موقعیت کلینیک</p>
              <p className="text-sm">تهران، خیابان ولیعصر، بالاتر از میدان ونک</p>
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}
