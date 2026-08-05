/**
 * Real clinic data for Fasdent
 * All contact and business info — no demo values.
 */
export const clinic = {
  name: 'کلینیک تخصصی دندانپزشکی فسدنت',
  doctor: {
    name: 'دکتر کیوان علی پسندی',
    title: 'دکتری حرفه ای ( ایمپلنتولوژیست )',
    license: '۱۹۱۷۴۰ شماره نظام',
    experience: 'بیش از ۱۰ سال سابقه',
    instagram: 'Dr.keyvan_alipasandi',
    image: '',
  },
  phone: '09201441469',
  phoneIntl: '+989201441469',
  phoneDisplay: '۰۹۲۰۱۴۴۱۴۶۹',
  phoneLink: 'tel:+989201441469',
  whatsapp: 'https://wa.me/989201441469',
  email: 'Dr.keyvan.alipasandii@gmail.com',
  address: 'نوشهر، میدان آزادی، ستارخان شمالی، ساختمان امیراد ۱، طبقه پنجم',
  workingHours: 'ساعت کاری از ساعت ۱۱ صبح الي ۱۹ شب',
  instagramClinic: 'Fasdent.clinic',
  instagramDoctor: 'Dr.keyvan_alipasandi',
  instagramClinicUrl: 'https://instagram.com/Fasdent.clinic',
  instagramDoctorUrl: 'https://instagram.com/Dr.keyvan_alipasandi',
  googleMapEmbed:
    'https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3200.9933219676263!2d51.50583407631748!3d36.65061127611691!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzbCsDM5JzAyLjIiTiA1McKwMzAnMzAuMyJF!5e0!3m2!1sen!2sus!4v1785923326577!5m2!1sen!2sus',
  googleMapLat: 36.65059966805383,
  googleMapLng: 51.50844075294953,
  services: ['ایمپلنت', 'ترمیم', 'جراحی دندان عقل', 'عصب کشی'] as const,
  implantBrands: ['Bego', 'Megagen', 'Straumann', 'Sic', '3zahn'] as const,
  logo: '',
  favicon: '',
  clinicImages: [
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage.jpg',
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage1.jpg',
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage2.jpg',
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage3.jpg',
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage4.jpg',
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage5.jpg',
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage6.jpg',
    '/wp-content/themes/fasdent-theme/assets/images/ClinicImage7.jpg',
  ],
} as const

export type Clinic = typeof clinic

export function getClinicRuntime() {
  const w = typeof window !== 'undefined' ? (window as any).FASDENT_REACT : null
  if (!w?.clinic) return clinic
  return {
    ...clinic,
    name: w.clinic.name || clinic.name,
    doctor: {
      ...clinic.doctor,
      name: w.clinic.doctor || clinic.doctor.name,
      title: w.clinic.doctor_title || clinic.doctor.title,
      license: w.clinic.license || clinic.doctor.license,
      experience: w.clinic.experience || clinic.doctor.experience,
      instagram: w.clinic.instagram_doctor || clinic.doctor.instagram,
    },
    phone: w.clinic.phone || clinic.phone,
    phoneIntl: w.clinic.phone_intl || clinic.phoneIntl,
    phoneLink: w.clinic.phone_link || clinic.phoneLink,
    whatsapp: w.clinic.whatsapp || clinic.whatsapp,
    email: w.clinic.email || clinic.email,
    address: w.clinic.address || clinic.address,
    workingHours: w.clinic.working_hours || clinic.workingHours,
    instagramClinic: w.clinic.instagram_clinic || clinic.instagramClinic,
    instagramDoctor: w.clinic.instagram_doctor || clinic.instagramDoctor,
    googleMapEmbed: w.clinic.google_map || clinic.googleMapEmbed,
    services: (w.services as string[]) || clinic.services,
    implantBrands: (w.implant_brands as string[]) || clinic.implantBrands,
  }
}

export function themeAsset(path: string): string {
  const w = typeof window !== 'undefined' ? (window as any).FASDENT_REACT : null
  const base = (w?.theme?.assets as string) || '/wp-content/themes/fasdent-theme/assets'
  return `${base.replace(/\/$/, '')}/${path.replace(/^\//, '')}`
}
