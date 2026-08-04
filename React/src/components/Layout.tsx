import { Outlet, useLocation } from 'react-router'
import { useEffect } from 'react'
import Header from './Header'
import Footer from './Footer'
import FloatingChat from './FloatingChat'
import ScrollToTop from './ScrollToTop'

export default function Layout() {
  const { pathname } = useLocation()

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }, [pathname])

  return (
    <div className="min-h-screen flex flex-col" dir="rtl">
      <a href="#main-content" className="skip-link">رفتن به محتوای اصلی</a>
      <Header />
      <main id="main-content" className="flex-1">
        <Outlet />
      </main>
      <Footer />
      <FloatingChat />
      <ScrollToTop />
    </div>
  )
}
