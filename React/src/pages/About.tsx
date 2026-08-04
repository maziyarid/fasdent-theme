import { Link } from 'react-router'
import Breadcrumb from '../components/Breadcrumb'

const credentials = [
  { icon: '🎓', title: 'دکترای دندانپزشکی', sub: 'دانشگاه علوم پزشکی تهران' },
  { icon: '🏅', title: 'فلوشیپ زیبایی', sub: 'آکادمی ملی دندانپزشکی ایران' },
  { icon: '📜', title: 'عضو نظام پزشکی', sub: 'پروانه شماره ۱۲۳۴۵' },
  { icon: '🌍', title: 'دوره‌های بین‌المللی', sub: 'اروپا، آمریکا و آسیا' },
]

const teamMembers = [
  { name: 'دکتر کیوان علی‌پسندی', role: 'دندانپزشک متخصص', specialty: 'ایمپلنت و زیبایی', avatar: '👨‍⚕️' },
  { name: 'دکتر سارا موسوی', role: 'متخصص ارتودنسی', specialty: 'ارتودنسی و الاینر', avatar: '👩‍⚕️' },
  { name: 'دکتر رضا حسینی', role: 'متخصص جراحی دهان', specialty: 'ایمپلنت و جراحی', avatar: '👨‍⚕️' },
]

export default function About() {
  return (
    <div className="pt-[88px]">
      {/* Hero */}
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'درباره ما' }]} />
          <h1 className="text-4xl md:text-5xl font-black mt-4 mb-4">درباره کلینیک ما</h1>
          <p className="text-white/80 text-lg max-w-2xl">با بیش از ۱۵ سال تجربه، کلینیک دکتر علی‌پسندی یکی از معتبرترین مراکز دندانپزشکی تخصصی در تهران است</p>
        </div>
      </div>

      {/* Doctor intro */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="relative">
              <div className="rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#E8F4FD] to-[#E0F7F7] aspect-square max-w-md mx-auto flex items-center justify-center">
                <span className="text-[160px]">👨‍⚕️</span>
              </div>
              <div className="absolute -bottom-4 -left-4 bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] text-white rounded-2xl p-4 shadow-xl">
                <div className="text-3xl font-black">۱۵+</div>
                <div className="text-sm opacity-90">سال تجربه</div>
              </div>
            </div>
            <div>
              <span className="inline-block bg-blue-50 text-[#0D54AF] text-sm font-medium px-4 py-1.5 rounded-full mb-4">دکتر ما</span>
              <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">دکتر کیوان علی‌پسندی</h2>
              <p className="text-[#0D54AF] font-semibold mb-4">متخصص دندانپزشکی ترمیمی، زیبایی و ایمپلنت</p>
              <p className="text-gray-600 leading-relaxed mb-6">
                دکتر کیوان علی‌پسندی با بیش از ۱۵ سال سابقه فعالیت حرفه‌ای، تخصص خود را در زمینه دندانپزشکی زیبایی، ایمپلنت و درمان‌های ترمیمی به کار گرفته است. ایشان با گذراندن دوره‌های پیشرفته بین‌المللی در اروپا و آمریکا، از جدیدترین تکنیک‌های دندانپزشکی مطلع هستند.
              </p>
              <p className="text-gray-600 leading-relaxed mb-8">
                فلسفه کاری دکتر علی‌پسندی بر پایه درمان بیمارمحور، استفاده از تکنولوژی پیشرفته، و ایجاد محیطی آرام و مطمئن برای بیماران استوار است. ایشان معتقدند هر بیمار نیازهای منحصربه‌فردی دارد که باید به صورت شخصی‌سازی شده به آن پرداخته شود.
              </p>
              <div className="grid grid-cols-2 gap-4">
                {credentials.map(c => (
                  <div key={c.title} className="flex items-start gap-3 bg-gray-50 rounded-xl p-3">
                    <span className="text-2xl">{c.icon}</span>
                    <div>
                      <p className="font-semibold text-sm text-[#071F3F]">{c.title}</p>
                      <p className="text-xs text-gray-500">{c.sub}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Mission */}
      <section className="py-20 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">چشم‌انداز و ارزش‌های ما</h2>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {[
              { icon: '🎯', title: 'ماموریت ما', text: 'ارائه بالاترین کیفیت مراقبت دندانپزشکی با رویکرد بیمارمحور و استفاده از جدیدترین تکنولوژی‌ها' },
              { icon: '👁️', title: 'چشم‌انداز ما', text: 'تبدیل شدن به مرجع اول دندانپزشکی تخصصی در ایران با استانداردهای بین‌المللی' },
              { icon: '💎', title: 'ارزش‌های ما', text: 'صداقت، حرفه‌ای‌گری، احترام به بیمار، و پیشرفت مستمر در علم و تکنولوژی دندانپزشکی' },
            ].map(item => (
              <div key={item.title} className="bg-white rounded-2xl p-8 shadow-sm text-center card-hover">
                <div className="text-5xl mb-4">{item.icon}</div>
                <h3 className="text-xl font-bold text-[#071F3F] mb-3">{item.title}</h3>
                <p className="text-gray-600 leading-relaxed">{item.text}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Team */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">تیم متخصصان ما</h2>
            <p className="text-gray-500">با تجربه‌ترین دندانپزشکان متخصص در کنار شما هستند</p>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {teamMembers.map(member => (
              <div key={member.name} className="bg-[#F4F9FF] rounded-2xl p-6 text-center card-hover">
                <div className="w-24 h-24 rounded-full bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] flex items-center justify-center text-5xl mx-auto mb-4 shadow-lg">
                  {member.avatar}
                </div>
                <h3 className="font-bold text-lg text-[#071F3F] mb-1">{member.name}</h3>
                <p className="text-[#0D54AF] font-medium text-sm mb-2">{member.role}</p>
                <p className="text-gray-500 text-sm">{member.specialty}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Clinic photos */}
      <section className="py-20 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-black text-[#071F3F] mb-4">محیط کلینیک</h2>
            <p className="text-gray-500">فضایی آرام و مجهز برای درمانی راحت</p>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
            {[
              'https://images.unsplash.com/photo-1588776814546-1ffbb2b1b3e1?w=500&h=400&fit=crop&auto=format',
              'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=500&h=400&fit=crop&auto=format',
              'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=500&h=400&fit=crop&auto=format',
              'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=500&h=400&fit=crop&auto=format',
              'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=500&h=400&fit=crop&auto=format',
              'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=500&h=400&fit=crop&auto=format',
            ].map((src, i) => (
              <div key={i} className="rounded-2xl overflow-hidden shadow-md card-hover aspect-video">
                <img src={src} alt={`محیط کلینیک ${i + 1}`} className="w-full h-full object-cover" />
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 bg-gradient-to-l from-[#08CBCD] to-[#0D54AF] text-white">
        <div className="max-w-3xl mx-auto px-4 text-center">
          <h2 className="text-3xl font-black mb-4">آماده شروع هستید؟</h2>
          <p className="text-white/80 mb-6">برای مشاوره رایگان با ما تماس بگیرید</p>
          <Link to="/appointment" className="inline-flex items-center gap-2 bg-white text-[#0D54AF] font-bold px-8 py-4 rounded-xl hover:shadow-2xl transition-all">
            رزرو نوبت مشاوره رایگان
          </Link>
        </div>
      </section>
    </div>
  )
}
