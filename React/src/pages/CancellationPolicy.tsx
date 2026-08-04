import { Link } from 'react-router'
import Breadcrumb from '../components/Breadcrumb'

export default function CancellationPolicy() {
  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-12">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'سیاست لغو نوبت' }]} />
          <h1 className="text-3xl md:text-4xl font-black mt-4">سیاست لغو و تغییر نوبت</h1>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 py-12 space-y-6">
        {[
          {
            icon: '⏰',
            title: 'لغو نوبت با اطلاع قبلی ۲۴ ساعته',
            text: 'در صورتی که نوبت خود را حداقل ۲۴ ساعت قبل از وقت تعیین شده لغو کنید، هیچ هزینه‌ای دریافت نخواهد شد و می‌توانید به راحتی نوبت جدید رزرو کنید.',
            color: 'bg-green-50 border-green-200',
          },
          {
            icon: '⚠️',
            title: 'لغو نوبت کمتر از ۲۴ ساعت',
            text: 'لغو نوبت در کمتر از ۲۴ ساعت قبل ممکن است مشمول کارمزد ۵۰٪ ویزیت شود. این سیاست برای حفاظت از وقت بیماران دیگر در نظر گرفته شده است.',
            color: 'bg-yellow-50 border-yellow-200',
          },
          {
            icon: '❌',
            title: 'عدم مراجعه بدون اطلاع',
            text: 'در صورت عدم مراجعه بدون اطلاع قبلی (No-Show)، هزینه ویزیت کامل محاسبه خواهد شد.',
            color: 'bg-red-50 border-red-200',
          },
          {
            icon: '🔄',
            title: 'تغییر وقت نوبت',
            text: 'می‌توانید تا ۲ بار نوبت خود را تغییر دهید. تغییر نوبت باید حداقل ۴ ساعت قبل اطلاع داده شود.',
            color: 'bg-blue-50 border-blue-200',
          },
          {
            icon: '🚨',
            title: 'موارد اورژانسی',
            text: 'در موارد اورژانس پزشکی یا حوادث غیرمترقبه، لغو نوبت بدون هزینه اضافی پذیرفته می‌شود. لطفاً با ما تماس بگیرید.',
            color: 'bg-purple-50 border-purple-200',
          },
        ].map(item => (
          <div key={item.title} className={`rounded-2xl p-5 border ${item.color}`}>
            <div className="flex items-start gap-4">
              <span className="text-3xl">{item.icon}</span>
              <div>
                <h2 className="font-bold text-[#071F3F] text-lg mb-2">{item.title}</h2>
                <p className="text-gray-600 leading-relaxed text-sm">{item.text}</p>
              </div>
            </div>
          </div>
        ))}

        <div className="bg-white rounded-2xl p-6 shadow-sm">
          <h2 className="font-bold text-[#071F3F] text-lg mb-4">نحوه لغو نوبت</h2>
          <div className="grid md:grid-cols-3 gap-4">
            {[
              { icon: '📞', method: 'تماس تلفنی', detail: '۰۲۱-۸۸۸۸۸۸۸۸' },
              { icon: '💬', method: 'واتساپ', detail: 'پیام به شماره کلینیک' },
              { icon: '📧', method: 'ایمیل', detail: 'info@fasdent.ir' },
            ].map(m => (
              <div key={m.method} className="text-center bg-gray-50 rounded-xl p-4">
                <div className="text-3xl mb-2">{m.icon}</div>
                <p className="font-semibold text-[#071F3F] text-sm">{m.method}</p>
                <p className="text-gray-500 text-xs mt-1">{m.detail}</p>
              </div>
            ))}
          </div>
        </div>

        <div className="text-center">
          <Link to="/appointment" className="inline-flex items-center gap-2 bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition-opacity">
            رزرو نوبت جدید
          </Link>
        </div>
      </div>
    </div>
  )
}
