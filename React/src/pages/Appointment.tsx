import { useState } from 'react'
import Breadcrumb from '../components/Breadcrumb'

const services = [
  'معاینه و مشاوره اولیه', 'ایمپلنت دندان', 'ارتودنسی و الاینر',
  'طراحی لبخند', 'لمینت و کراون', 'بلیچینگ', 'عصب‌کشی',
  'جراحی دهان', 'دندانپزشکی کودکان', 'اورژانس دندان', 'سایر',
]

const times = ['۹:۰۰', '۹:۳۰', '۱۰:۰۰', '۱۰:۳۰', '۱۱:۰۰', '۱۱:۳۰', '۱۲:۰۰', '۱۴:۰۰', '۱۴:۳۰', '۱۵:۰۰', '۱۵:۳۰', '۱۶:۰۰', '۱۶:۳۰', '۱۷:۰۰', '۱۷:۳۰', '۱۸:۰۰', '۱۸:۳۰', '۱۹:۰۰', '۱۹:۳۰']

type Step = 1 | 2 | 3 | 4

export default function Appointment() {
  const [step, setStep] = useState<Step>(1)
  const [form, setForm] = useState({
    name: '', phone: '', service: '', date: '', time: '', notes: '', isNew: 'yes',
  })
  const [submitted, setSubmitted] = useState(false)

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setSubmitted(true)
  }

  if (submitted) {
    return (
      <div className="pt-[88px] min-h-screen flex items-center justify-center bg-[#F4F9FF]">
        <div className="bg-white rounded-3xl p-12 shadow-xl max-w-md w-full mx-4 text-center">
          <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">✅</div>
          <h2 className="text-2xl font-black text-[#071F3F] mb-3">نوبت شما ثبت شد!</h2>
          <p className="text-gray-600 mb-2">کارشناسان ما به زودی با شماره <strong className="ltr">{form.phone}</strong> با شما تماس خواهند گرفت.</p>
          <p className="text-gray-500 text-sm mb-6">در صورت تغییر برنامه، لطفاً حداقل ۲۴ ساعت قبل اطلاع دهید.</p>
          <button onClick={() => { setSubmitted(false); setStep(1); setForm({ name: '', phone: '', service: '', date: '', time: '', notes: '', isNew: 'yes' }) }} className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white px-8 py-3 rounded-xl font-semibold">
            رزرو نوبت جدید
          </button>
        </div>
      </div>
    )
  }

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'رزرو نوبت' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-4">رزرو نوبت آنلاین</h1>
          <p className="text-white/80 text-lg">رزرو نوبت آسان، سریع و رایگان</p>
        </div>
      </div>

      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-4xl mx-auto px-4">
          {/* Steps indicator */}
          <div className="flex items-center justify-center gap-2 mb-10">
            {([1, 2, 3] as Step[]).map((s, i) => (
              <div key={s} className="flex items-center gap-2">
                <div className={`w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-all ${step >= s ? 'bg-[#0D54AF] text-white' : 'bg-gray-200 text-gray-500'}`}>
                  {step > s ? '✓' : s}
                </div>
                {i < 2 && <div className={`w-12 h-0.5 ${step > s ? 'bg-[#0D54AF]' : 'bg-gray-200'}`} />}
              </div>
            ))}
          </div>
          <div className="flex justify-center gap-8 mb-8 text-sm">
            <span className={step >= 1 ? 'text-[#0D54AF] font-medium' : 'text-gray-400'}>اطلاعات شخصی</span>
            <span className={step >= 2 ? 'text-[#0D54AF] font-medium' : 'text-gray-400'}>انتخاب خدمت</span>
            <span className={step >= 3 ? 'text-[#0D54AF] font-medium' : 'text-gray-400'}>زمان‌بندی</span>
          </div>

          <form onSubmit={handleSubmit}>
            <div className="bg-white rounded-2xl p-8 shadow-sm">
              {/* Step 1 */}
              {step === 1 && (
                <div className="space-y-5">
                  <h2 className="text-xl font-bold text-[#071F3F] mb-6">اطلاعات شخصی</h2>
                  <div className="grid md:grid-cols-2 gap-5">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">نام و نام خانوادگی *</label>
                      <input
                        type="text"
                        required
                        value={form.name}
                        onChange={e => setForm({ ...form, name: e.target.value })}
                        placeholder="مثال: علی محمدی"
                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">شماره موبایل *</label>
                      <input
                        type="tel"
                        required
                        value={form.phone}
                        onChange={e => setForm({ ...form, phone: e.target.value })}
                        placeholder="۰۹۱۲۱۲۳۴۵۶۷"
                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all ltr text-right"
                        dir="ltr"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">آیا بیمار جدید هستید؟</label>
                    <div className="flex gap-4">
                      {['yes', 'no'].map(v => (
                        <label key={v} className="flex items-center gap-2 cursor-pointer">
                          <input
                            type="radio"
                            name="isNew"
                            value={v}
                            checked={form.isNew === v}
                            onChange={e => setForm({ ...form, isNew: e.target.value })}
                            className="w-4 h-4 text-[#0D54AF]"
                          />
                          <span className="text-sm text-gray-700">{v === 'yes' ? 'بله، بیمار جدید هستم' : 'خیر، قبلاً مراجعه کرده‌ام'}</span>
                        </label>
                      ))}
                    </div>
                  </div>
                  <button
                    type="button"
                    onClick={() => form.name && form.phone ? setStep(2) : null}
                    className="w-full bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white py-3.5 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity"
                  >
                    مرحله بعد
                  </button>
                </div>
              )}

              {/* Step 2 */}
              {step === 2 && (
                <div className="space-y-5">
                  <h2 className="text-xl font-bold text-[#071F3F] mb-6">انتخاب خدمت</h2>
                  <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
                    {services.map(s => (
                      <button
                        key={s}
                        type="button"
                        onClick={() => setForm({ ...form, service: s })}
                        className={`p-3 rounded-xl text-sm text-right border-2 transition-all ${form.service === s ? 'border-[#0D54AF] bg-blue-50 text-[#0D54AF] font-semibold' : 'border-gray-200 text-gray-700 hover:border-[#08CBCD]'}`}
                      >
                        {s}
                      </button>
                    ))}
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">توضیحات اضافی (اختیاری)</label>
                    <textarea
                      value={form.notes}
                      onChange={e => setForm({ ...form, notes: e.target.value })}
                      rows={3}
                      placeholder="هر توضیح یا سوالی که دارید اینجا بنویسید..."
                      className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all resize-none"
                    />
                  </div>
                  <div className="flex gap-3">
                    <button type="button" onClick={() => setStep(1)} className="flex-1 border-2 border-gray-200 text-gray-600 py-3.5 rounded-xl font-bold text-sm hover:bg-gray-50 transition-colors">
                      مرحله قبل
                    </button>
                    <button
                      type="button"
                      onClick={() => form.service ? setStep(3) : null}
                      className="flex-1 bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white py-3.5 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity"
                    >
                      مرحله بعد
                    </button>
                  </div>
                </div>
              )}

              {/* Step 3 */}
              {step === 3 && (
                <div className="space-y-5">
                  <h2 className="text-xl font-bold text-[#071F3F] mb-6">انتخاب زمان</h2>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">تاریخ مورد نظر *</label>
                    <input
                      type="date"
                      required
                      value={form.date}
                      onChange={e => setForm({ ...form, date: e.target.value })}
                      min={new Date().toISOString().split('T')[0]}
                      className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#08CBCD] focus:ring-2 focus:ring-[#08CBCD]/20 outline-none transition-all ltr"
                      dir="ltr"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-3">ساعت مورد نظر *</label>
                    <div className="grid grid-cols-4 md:grid-cols-6 gap-2">
                      {times.map(t => (
                        <button
                          key={t}
                          type="button"
                          onClick={() => setForm({ ...form, time: t })}
                          className={`py-2 rounded-lg text-sm font-medium border transition-all ltr ${form.time === t ? 'bg-[#0D54AF] text-white border-[#0D54AF]' : 'border-gray-200 text-gray-600 hover:border-[#08CBCD]'}`}
                        >
                          {t}
                        </button>
                      ))}
                    </div>
                  </div>

                  {/* Summary */}
                  <div className="bg-gray-50 rounded-xl p-4 text-sm">
                    <h3 className="font-bold text-[#071F3F] mb-3">خلاصه رزرو</h3>
                    <div className="space-y-1.5 text-gray-600">
                      <p>👤 <strong>{form.name}</strong></p>
                      <p>📞 <span className="ltr">{form.phone}</span></p>
                      <p>🦷 <strong>{form.service}</strong></p>
                      {form.date && <p>📅 {form.date}</p>}
                      {form.time && <p>🕐 <span className="ltr">{form.time}</span></p>}
                    </div>
                  </div>

                  <div className="flex gap-3">
                    <button type="button" onClick={() => setStep(2)} className="flex-1 border-2 border-gray-200 text-gray-600 py-3.5 rounded-xl font-bold text-sm hover:bg-gray-50 transition-colors">
                      مرحله قبل
                    </button>
                    <button
                      type="submit"
                      className="flex-1 bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white py-3.5 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity"
                    >
                      ثبت نهایی نوبت
                    </button>
                  </div>
                </div>
              )}
            </div>
          </form>

          {/* Info boxes */}
          <div className="grid md:grid-cols-3 gap-4 mt-8">
            {[
              { icon: '📞', title: 'تماس مستقیم', text: 'برای رزرو تلفنی با ۰۲۱-۸۸۸۸۸۸۸۸ تماس بگیرید' },
              { icon: '💬', title: 'واتساپ', text: 'از طریق واتساپ نوبت بگیرید' },
              { icon: '🕐', title: 'ساعات کاری', text: 'شنبه تا پنج‌شنبه ۹ تا ۲۰' },
            ].map(info => (
              <div key={info.title} className="bg-white rounded-xl p-4 shadow-sm text-center">
                <div className="text-3xl mb-2">{info.icon}</div>
                <h3 className="font-bold text-[#071F3F] text-sm mb-1">{info.title}</h3>
                <p className="text-gray-500 text-xs">{info.text}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  )
}
