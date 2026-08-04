import { useParams, Link } from 'react-router'
import Breadcrumb from '../components/Breadcrumb'

const categoryData: Record<string, { label: string; icon: string; description: string }> = {
  implant: { label: 'ایمپلنت دندان', icon: '🦷', description: 'مقالات آموزشی درباره ایمپلنت دندان، مراحل و مراقبت‌های لازم' },
  orthodontics: { label: 'ارتودنسی', icon: '😁', description: 'راهنمای جامع انواع ارتودنسی، الاینر و براکت' },
  cosmetic: { label: 'دندانپزشکی زیبایی', icon: '✨', description: 'مقالات درباره طراحی لبخند، لمینت و بلیچینگ' },
  children: { label: 'دندانپزشکی کودکان', icon: '👶', description: 'راهنمای مراقبت از دندان کودکان در هر مرحله رشد' },
  hygiene: { label: 'بهداشت دهان', icon: '🪥', description: 'نکات مهم برای حفظ سلامت دهان و دندان' },
  general: { label: 'دندانپزشکی عمومی', icon: '🏥', description: 'اطلاعات عمومی درباره خدمات دندانپزشکی' },
}

const samplePosts = [
  { title: 'مقاله اول این دسته‌بندی', excerpt: 'خلاصه مقاله...', date: '۱۵ آبان ۱۴۰۳', readTime: '۵ دقیقه', img: 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=400&h=250&fit=crop&auto=format' },
  { title: 'مقاله دوم این دسته‌بندی', excerpt: 'خلاصه مقاله...', date: '۸ آبان ۱۴۰۳', readTime: '۴ دقیقه', img: 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=400&h=250&fit=crop&auto=format' },
  { title: 'مقاله سوم این دسته‌بندی', excerpt: 'خلاصه مقاله...', date: '۱ آبان ۱۴۰۳', readTime: '۶ دقیقه', img: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=250&fit=crop&auto=format' },
]

export default function PostCategory() {
  const { slug = '' } = useParams()
  const cat = categoryData[slug] || { label: slug, icon: '📚', description: 'مقالات این دسته‌بندی' }

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'وبلاگ', href: '/knowledge-base' }, { label: cat.label }]} />
          <div className="flex items-center gap-4 mt-4">
            <span className="text-5xl">{cat.icon}</span>
            <div>
              <h1 className="text-3xl md:text-4xl font-black">{cat.label}</h1>
              <p className="text-white/70 mt-1">{cat.description}</p>
            </div>
          </div>
        </div>
      </div>

      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {samplePosts.map((post, i) => (
              <Link key={i} to={`/knowledge-base/${slug}-post-${i + 1}`} className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all card-hover group">
                <div className="h-48 overflow-hidden bg-gray-100">
                  <img src={post.img} alt={post.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                </div>
                <div className="p-5">
                  <span className="inline-block bg-blue-50 text-[#0D54AF] text-xs px-2 py-1 rounded-full mb-3">{cat.label}</span>
                  <h3 className="font-bold text-[#071F3F] mb-2 group-hover:text-[#0D54AF] transition-colors">{post.title}</h3>
                  <p className="text-gray-500 text-sm">{post.excerpt}</p>
                  <div className="flex justify-between text-xs text-gray-400 mt-3">
                    <span>{post.date}</span>
                    <span>{post.readTime}</span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  )
}
