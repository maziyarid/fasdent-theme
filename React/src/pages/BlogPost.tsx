import { Link, useParams } from 'react-router'
import { useWpPost } from '../hooks/useWpPosts'
import FaIcon from '../components/FaIcon'
import { clinic } from '../data/clinic'

export default function BlogPost() {
  const { slug } = useParams()
  const { post, loading, error } = useWpPost(slug)

  if (loading) {
    return (
      <div className="py-24 text-center text-gray-500" dir="rtl">
        <FaIcon icon="fa-solid fa-spinner fa-spin" className="text-3xl mb-3" />
        <p>در حال بارگذاری...</p>
      </div>
    )
  }

  if (error || !post) {
    return (
      <div className="py-24 text-center" dir="rtl">
        <FaIcon icon="fa-solid fa-file-circle-exclamation" className="text-5xl text-gray-300 mb-4" />
        <h1 className="text-2xl font-bold text-[#071F3F] mb-2">مطلب پیدا نشد</h1>
        <p className="text-gray-500 mb-6">ممکن است آدرس اشتباه باشد یا مطلب حذف شده باشد.</p>
        <Link to="/blog" className="inline-flex items-center gap-2 bg-[#0D54AF] text-white px-5 py-2.5 rounded-xl">
          <FaIcon icon="fa-solid fa-arrow-right" />
          بازگشت به وبلاگ
        </Link>
      </div>
    )
  }

  const img = post._embedded?.['wp:featuredmedia']?.[0]?.source_url
  const author = post._embedded?.author?.[0]?.name || clinic.doctor.name

  return (
    <article dir="rtl">
      <section className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] py-12 px-4">
        <div className="max-w-3xl mx-auto text-white">
          <Link to="/blog" className="text-white/80 text-sm hover:text-white inline-flex items-center gap-2 mb-4">
            <FaIcon icon="fa-solid fa-arrow-right" />
            وبلاگ
          </Link>
          <h1
            className="text-3xl md:text-4xl font-black leading-tight"
            dangerouslySetInnerHTML={{ __html: post.title.rendered }}
          />
          <div className="flex flex-wrap gap-4 mt-4 text-sm text-white/80">
            <span className="inline-flex items-center gap-1.5">
              <FaIcon icon="fa-solid fa-calendar" />
              {new Date(post.date).toLocaleDateString('fa-IR')}
            </span>
            <span className="inline-flex items-center gap-1.5">
              <FaIcon icon="fa-solid fa-user-doctor" />
              {author}
            </span>
          </div>
        </div>
      </section>

      <div className="max-w-3xl mx-auto px-4 py-10">
        {img && (
          <img src={img} alt="" className="w-full rounded-2xl mb-8 shadow-sm" />
        )}
        <div
          className="prose prose-lg max-w-none text-[#1a2f4a] leading-8"
          dangerouslySetInnerHTML={{ __html: post.content.rendered }}
        />
        <div className="mt-12 p-6 rounded-2xl bg-[#E8F4FD] flex items-center gap-4">
          <div className="w-14 h-14 rounded-full bg-gradient-to-br from-[#0D54AF] to-[#08CBCD] flex items-center justify-center text-white text-xl">
            <FaIcon icon="fa-solid fa-user-doctor" />
          </div>
          <div>
            <p className="font-bold text-[#071F3F]">{clinic.doctor.name}</p>
            <p className="text-sm text-gray-600">{clinic.doctor.title}</p>
          </div>
        </div>
      </div>
    </article>
  )
}
