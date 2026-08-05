import { useEffect, useState } from 'react'

export type WpPost = {
  id: number
  date: string
  slug: string
  title: { rendered: string }
  excerpt: { rendered: string }
  content: { rendered: string }
  link: string
  _embedded?: {
    'wp:featuredmedia'?: Array<{ source_url?: string }>
    author?: Array<{ name?: string }>
  }
}

function apiRoot(): string {
  const w = typeof window !== 'undefined' ? (window as any).FASDENT_REACT : null
  return w?.api?.root || '/wp-json/'
}

export function useWpPosts(opts: { page?: number; perPage?: number; search?: string; category?: number } = {}) {
  const { page = 1, perPage = 10, search = '', category } = opts
  const [posts, setPosts] = useState<WpPost[]>([])
  const [total, setTotal] = useState(0)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false
    async function load() {
      setLoading(true)
      setError(null)
      try {
        const params = new URLSearchParams({
          page: String(page),
          per_page: String(perPage),
          _embed: '1',
        })
        if (search) params.set('search', search)
        if (category) params.set('categories', String(category))
        const res = await fetch(`${apiRoot()}wp/v2/posts?${params.toString()}`)
        if (!res.ok) throw new Error(`HTTP ${res.status}`)
        const data = (await res.json()) as WpPost[]
        const totalHeader = res.headers.get('X-WP-Total')
        if (!cancelled) {
          setPosts(data)
          setTotal(totalHeader ? parseInt(totalHeader, 10) : data.length)
        }
      } catch (e) {
        if (!cancelled) setError(e instanceof Error ? e.message : 'خطا در دریافت مطالب')
      } finally {
        if (!cancelled) setLoading(false)
      }
    }
    load()
    return () => {
      cancelled = true
    }
  }, [page, perPage, search, category])

  return { posts, total, loading, error }
}

export function useWpPost(slug?: string) {
  const [post, setPost] = useState<WpPost | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    if (!slug) {
      setLoading(false)
      setError('slug missing')
      return
    }
    let cancelled = false
    async function load() {
      setLoading(true)
      setError(null)
      try {
        const res = await fetch(`${apiRoot()}wp/v2/posts?slug=${encodeURIComponent(slug!)}&_embed=1`)
        if (!res.ok) throw new Error(`HTTP ${res.status}`)
        const data = (await res.json()) as WpPost[]
        if (!cancelled) {
          setPost(data[0] || null)
          if (!data[0]) setError('not found')
        }
      } catch (e) {
        if (!cancelled) setError(e instanceof Error ? e.message : 'خطا')
      } finally {
        if (!cancelled) setLoading(false)
      }
    }
    load()
    return () => {
      cancelled = true
    }
  }, [slug])

  return { post, loading, error }
}
