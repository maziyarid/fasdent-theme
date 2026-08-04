import { RouterProvider } from 'react-router'
import { WordPressProvider } from './contexts/WordPressContext'
import { router } from './routes'

export default function App() {
  return (
    <WordPressProvider>
      <RouterProvider router={router} />
    </WordPressProvider>
  )
}
