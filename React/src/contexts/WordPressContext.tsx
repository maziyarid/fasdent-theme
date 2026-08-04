import React, { createContext, useContext, useState, useEffect } from 'react';
import { WordPressData, defaultWordPressData } from '../types/wordpress';

interface WordPressContextType {
  data: WordPressData;
  setData: (data: WordPressData) => void;
  isLoaded: boolean;
}

const WordPressContext = createContext<WordPressContextType>({
  data: defaultWordPressData,
  setData: () => {},
  isLoaded: false,
});

export function WordPressProvider({ children }: { children: React.ReactNode }) {
  const [data, setData] = useState<WordPressData>(defaultWordPressData);
  const [isLoaded, setIsLoaded] = useState(false);

  useEffect(() => {
    // Check if we're in WordPress environment
    if (typeof window !== 'undefined' && window.FASDENT_REACT) {
      setData(window.FASDENT_REACT);
      setIsLoaded(true);
    } else {
      // In development, use default data
      setIsLoaded(true);
    }
  }, []);

  return (
    <WordPressContext.Provider value={{ data, setData, isLoaded }}>
      {children}
    </WordPressContext.Provider>
  );
}

export function useWordPress() {
  const context = useContext(WordPressContext);
  if (!context) {
    throw new Error('useWordPress must be used within a WordPressProvider');
  }
  return context;
}
