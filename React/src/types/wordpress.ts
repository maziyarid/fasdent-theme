// WordPress data types for React integration

export interface WordPressSite {
  name: string;
  description: string;
  url: string;
  language: string;
  direction: 'rtl' | 'ltr';
}

export interface MenuItem {
  id: number;
  title: string;
  url: string;
  classes: string[];
  parent: number;
  children: MenuItem[];
}

export interface WordPressUser {
  id: number;
  name: string;
  email: string;
}

export interface WordPressData {
  site: WordPressSite;
  phone: string;
  phone_link: string;
  booking_url: string;
  menus: {
    main: MenuItem[];
    footer: MenuItem[];
    legal: MenuItem[];
  };
  rest_url: string;
  nonce: string;
  current_user: WordPressUser | null;
}

// Default data for development
export const defaultWordPressData: WordPressData = {
  site: {
    name: 'فسدنت',
    description: 'کلینیک تخصصی دندانپزشکی فسدنت - دکتر کیوان صمدی',
    url: 'https://fasdent.ir',
    language: 'fa',
    direction: 'rtl',
  },
  phone: '09201441469',
  phone_link: '+989201441469',
  booking_url: 'https://fasdent.ir/appointment/',
  menus: {
    main: [],
    footer: [],
    legal: [],
  },
  rest_url: 'https://fasdent.ir/wp-json/',
  nonce: '',
  current_user: null,
};
