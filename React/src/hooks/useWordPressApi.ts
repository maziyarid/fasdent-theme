import { useState, useEffect, useCallback } from 'react'

interface WordPressPost {
  id: number
  date: string
  slug: string
  status: string
  title: { rendered: string }
  content: { rendered: string; protected: boolean }
  excerpt: { rendered: string; protected: boolean }
  featured_media: number
  _embedded?: {
    'wp:featuredmedia'?: Array<{
      id: number
      source_url: string
      alt_text: string
    }>
  }
}

interface WordPressPage {
  id: number
  date: string
  slug: string
  status: string
  title: { rendered: string }
  content: { rendered: string; protected: boolean }
  _embedded?: {
    'wp:featuredmedia'?: Array<{
      id: number
      source_url: string
      alt_text: string
    }>
  }
}

interface FetchOptions {
  endpoint: string
  params?: Record<string, any>
}

export function useWordPressApi<T = any>(options: FetchOptions) {
  const [data, setData] = useState<T | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<Error | null>(null)

  const fetchData = useCallback(async () => {
    setLoading(true)
    setError(null)

    try {
      // Get REST URL from window or use default
      const restUrl = window.FASDENT_REACT?.rest_url || '/wp-json/'
      const nonce = window.FASDENT_REACT?.nonce || ''
      
      // Build URL
      const url = new URL(options.endpoint, restUrl)
      
      // Add query parameters
      if (options.params) {
        Object.entries(options.params).forEach(([key, value]) => {
          if (value !== undefined && value !== null) {
            url.searchParams.append(key, String(value))
          }
        })
      }

      const response = await fetch(url.toString(), {
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce,
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const result = await response.json()
      setData(result)
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Failed to fetch data'))
      console.error('WordPress API Error:', err)
    } finally {
      setLoading(false)
    }
  }, [options.endpoint, options.params])

  useEffect(() => {
    fetchData()
  }, [fetchData])

  const refetch = useCallback(() => {
    fetchData()
  }, [fetchData])

  return { data, loading, error, refetch }
}

export function usePosts(params?: {
  per_page?: number
  page?: number
  categories?: number[]
  search?: string
  _embed?: boolean
}) {
  return useWordPressApi<WordPressPost[]>({
    endpoint: '/wp/v2/posts',
    params: {
      per_page: params?.per_page || 10,
      page: params?.page || 1,
      categories: params?.categories,
      search: params?.search,
      _embed: params?._embed || true,
    },
  })
}

export function usePost(slug: string) {
  return useWordPressApi<WordPressPost[]>({
    endpoint: `/wp/v2/posts`,
    params: {
      slug,
      _embed: true,
    },
  })
}

export function usePage(slug: string) {
  return useWordPressApi<WordPressPage[]>({
    endpoint: `/wp/v2/pages`,
    params: {
      slug,
      _embed: true,
    },
  })
}

export function useCategories() {
  return useWordPressApi<Array<{
    id: number
    name: string
    slug: string
    count: number
  }>>({
    endpoint: '/wp/v2/categories',
    params: {
      per_page: 100,
    },
  })
}

export function useTags() {
  return useWordPressApi<Array<{
    id: number
    name: string
    slug: string
    count: number
  }>>({
    endpoint: '/wp/v2/tags',
    params: {
      per_page: 100,
    },
  })
}
