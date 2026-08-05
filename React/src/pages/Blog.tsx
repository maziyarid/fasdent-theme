import { Link } from 'react-router'
import { useState } from 'react'
import { useWpPosts } from '../hooks/useWpPosts'
import FaIcon from '../components/FaIcon'

function stripHtml(html: string) {
  return html.replace(/<[^>]+>/g, '').trim()
}

export default function Blog() {
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [q, setQ] = useState('')
  const { posts, total, loading, error } = useWpPosts({ page, perPage: 9, search: q })

  const totalPages = Math.max(1, Math.ceil(total / 9))

  return (
    <div dir="rtl">
      <section className="bg-gradient-to-l from-[#0D54AF] to-[#08CBCD] py-16 px-4">
        <div className="max-w-5xl mx-auto text-center text-white">
          <h1 className="text-3xl md:text-4xl font-black mb-3">وبلاگ و مقالات</h1>
          <p className="text-white/80 mb-6">آخرین مطالب آموزشی و خبرهای کلینیک فسدنت</p>
          <form
            onSubmit={(e) => { e.preventDefault(); setPage(1); setQ(search) }}
            className="max-w-md mx-auto"
          >
            <div className="relative">
              <input
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="جستجو در مقالات..."
                className="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-3 text-white placeholder-white/60 focus:border-white outline-none text-sm"
              />
              <button type="submit" className="absolute left-3 top-1/2 -translate-y-1/2 text-white/80" aria-label="جستجو">
                <FaIcon icon="fa-solid fa-magnifying-glass" />
              </button>
            </div>
          </form>
        </div>
      </section>

      <section className="max-w-6xl mx-auto px-4 py-12">
        {loading && (
          <div className="text-center py-16 text-gray-500">
            <FaIcon icon="fa-solid fa-spinner fa-spin" className="text-3xl mb-3" />
            <p>در حال بارگذاری مطالب...</p>
          </div>
        )}
        {error && (
          <div className="text-center py-16">
            <FaIcon icon="fa-solid fa-triangle-exclamation" className="text-4xl text-amber-500 mb-3" />
            <p className="text-gray-600 mb-2">امکان دریافت مطالب از سرور نبود.</p>
            <p className="text-sm text-gray-400">{error}</p>
            <p className="text-sm text-gray-500 mt-4">اگر تازه تم را فعال کرده‌اید، یک‌بار پیوندهای یکتا را در تنظیمات وردپرس ذخیره کنید.</p>
          </div>
        )}
        {!loading && !error && posts.length === 0 && (
          <div className="text-center py-16">
            <FaIcon icon="fa-solid fa-newspaper" className="text-5xl text-gray-300 mb-4" />
            <p className="text-gray-600">هنوز مطلبی منتشر نشده است.</p>
          </div>
        )}
        {!loading && posts.length > 0 && (
          <>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {posts.map((post) => {
                const img = post._embedded?.['wp:featuredmedia']?.[0]?.source_url
                return (
                  <Link
                    key={post.id}
                    to={`/blog/${post.slug}`}
                    className="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition-shadow group"
                  >
                    <div className="aspect-video bg-gradient-to-br from-[#0D54AF]/10 to-[#08CBCD]/10 flex items-center justify-center overflow-hidden">
                      {img ? (
                        <img src={img} alt="" className="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                      ) : (
                        <FaIcon icon="fa-solid fa-tooth" className="text-4xl text-[#0D54AF]/40" />
                      )}
                    </div>
                    <div className="p-5">
                      <time className="text-xs text-gray-400">{new Date(post.date).toLocaleDateString('fa-IR')}</time>
                      <h2
                        className="font-bold text-[#071F3F] mt-1 mb-2 line-clamp-2 group-hover:text-[#0D54AF]"
                        dangerouslySetInnerHTML={{ __html: post.title.rendered }}
                      />
                      <p className="text-sm text-gray-600 line-clamp-3">{stripHtml(post.excerpt.rendered)}</p>
                    </div>
                  </Link>
                )
              })}
            </div>
            {totalPages > 1 && (
              <div className="flex justify-center gap-2 mt-10">
                <button
                  disabled={page <= 1}
                  onClick={() => setPage((p) => Math.max(1, p - 1))}
                  className="px-4 py-2 rounded-xl border disabled:opacity-40"
                >
                  قبلی
                </button>
                <span className="px-4 py-2 text-sm text-gray-600">صفحه {page} از {totalPages}</span>
                <button
                  disabled={page >= totalPages}
                  onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                  className="px-4 py-2 rounded-xl border disabled:opacity-40"
                >
                  بعدی
                </button>
              </div>
            )}
          </>
        )}
      </section>
    </div>
  )
}
