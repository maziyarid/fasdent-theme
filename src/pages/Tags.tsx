import { useParams, Link } from 'react-router'
import Breadcrumb from '../components/Breadcrumb'
import { services } from '../data/services'

const allTags = Array.from(new Set(services.flatMap(s => s.tags)))

export default function Tags() {
  const { slug = '' } = useParams()

  const relatedServices = services.filter(s => s.tags.includes(slug))
  const isTagPage = slug.length > 0

  if (isTagPage) {
    return (
      <div className="pt-[88px]">
        <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
          <div className="max-w-7xl mx-auto px-4">
            <Breadcrumb items={[{ label: 'برچسب‌ها', href: '/tag' }, { label: `#${slug}` }]} />
            <h1 className="text-3xl md:text-4xl font-black mt-4">#{slug}</h1>
            <p className="text-white/70 mt-1">{relatedServices.length} نتیجه یافت شد</p>
          </div>
        </div>

        <section className="py-12 bg-[#F4F9FF]">
          <div className="max-w-7xl mx-auto px-4">
            {relatedServices.length > 0 ? (
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                {relatedServices.map(service => (
                  <Link
                    key={service.slug}
                    to={`/services/${service.categorySlug}/${service.slug}`}
                    className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all card-hover group"
                  >
                    <div className="h-40 overflow-hidden bg-gray-100">
                      <img src={service.image} alt={service.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    </div>
                    <div className="p-4">
                      <span className="text-xs bg-blue-50 text-[#0D54AF] px-2 py-0.5 rounded-full">{service.category}</span>
                      <h3 className="font-bold text-[#071F3F] mt-2 mb-1 group-hover:text-[#0D54AF] transition-colors">{service.title}</h3>
                      <p className="text-sm text-gray-500 line-clamp-2">{service.description}</p>
                    </div>
                  </Link>
                ))}
              </div>
            ) : (
              <div className="text-center py-20 text-gray-400">
                <div className="text-5xl mb-4">🏷️</div>
                <p>محتوایی با این برچسب یافت نشد</p>
              </div>
            )}

            {/* All tags */}
            <div className="mt-12 bg-white rounded-2xl p-6 shadow-sm">
              <h2 className="font-bold text-[#071F3F] mb-4">همه برچسب‌ها</h2>
              <div className="flex flex-wrap gap-2">
                {allTags.map(tag => (
                  <Link
                    key={tag}
                    to={`/tag/${tag}`}
                    className={`px-3 py-1.5 rounded-full text-sm font-medium transition-colors ${tag === slug ? 'bg-[#0D54AF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-[#0D54AF]'}`}
                  >
                    #{tag}
                  </Link>
                ))}
              </div>
            </div>
          </div>
        </section>
      </div>
    )
  }

  return (
    <div className="pt-[88px]">
      <div className="bg-gradient-to-br from-[#071F3F] to-[#0D54AF] text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Breadcrumb items={[{ label: 'برچسب‌ها' }]} />
          <h1 className="text-3xl md:text-4xl font-black mt-4">همه برچسب‌ها</h1>
        </div>
      </div>
      <section className="py-12 bg-[#F4F9FF]">
        <div className="max-w-7xl mx-auto px-4">
          <div className="bg-white rounded-2xl p-6 shadow-sm">
            <div className="flex flex-wrap gap-3">
              {allTags.map(tag => (
                <Link
                  key={tag}
                  to={`/tag/${tag}`}
                  className="px-4 py-2 bg-gray-100 hover:bg-[#0D54AF] hover:text-white text-gray-700 rounded-full text-sm font-medium transition-all"
                >
                  #{tag}
                  <span className="mr-1.5 text-xs opacity-70">({services.filter(s => s.tags.includes(tag)).length})</span>
                </Link>
              ))}
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}
