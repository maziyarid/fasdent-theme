import { createBrowserRouter } from 'react-router'
import Layout from './components/Layout'
import Home from './pages/Home'
import About from './pages/About'
import Pricing from './pages/Pricing'
import Gallery from './pages/Gallery'
import Appointment from './pages/Appointment'
import Contact from './pages/Contact'
import FAQ from './pages/FAQ'
import Services from './pages/Services'
import ServiceDetail from './pages/ServiceDetail'
import KnowledgeBase from './pages/KnowledgeBase'
import Sitemap from './pages/Sitemap'
import MedicalDisclaimer from './pages/MedicalDisclaimer'
import CancellationPolicy from './pages/CancellationPolicy'
import PatientRights from './pages/PatientRights'
import PrivacyPolicy from './pages/PrivacyPolicy'
import PostCategory from './pages/PostCategory'
import Tags from './pages/Tags'
import NotFound from './pages/NotFound'

export const router = createBrowserRouter([
  {
    path: '/',
    Component: Layout,
    children: [
      { index: true, Component: Home },
      { path: 'about', Component: About },
      { path: 'pricing', Component: Pricing },
      { path: 'gallery', Component: Gallery },
      { path: 'appointment', Component: Appointment },
      { path: 'contact', Component: Contact },
      { path: 'faq', Component: FAQ },
      { path: 'services', Component: Services },
      { path: 'services/:category/:service', Component: ServiceDetail },
      { path: 'services/:category', Component: ServiceDetail },
      { path: 'knowledge-base', Component: KnowledgeBase },
      { path: 'knowledge-base/:slug', Component: KnowledgeBase },
      { path: 'sitemap', Component: Sitemap },
      { path: 'medical-disclaimer', Component: MedicalDisclaimer },
      { path: 'cancellation-policy', Component: CancellationPolicy },
      { path: 'patient-rights', Component: PatientRights },
      { path: 'privacy-policy', Component: PrivacyPolicy },
      { path: 'category/:slug', Component: PostCategory },
      { path: 'tag/:slug', Component: Tags },
      { path: '*', Component: NotFound },
    ],
  },
])
