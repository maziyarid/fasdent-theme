/**
 * Font Awesome 7 Pro icon helper — local CSS only, no CDN.
 * Usage: <FaIcon icon="fa-solid fa-tooth" className="text-xl" />
 */
type Props = {
  icon: string
  className?: string
  title?: string
  style?: React.CSSProperties
}

export default function FaIcon({ icon, className = '', title, style }: Props) {
  return (
    <i
      className={`${icon} ${className}`.trim()}
      title={title}
      style={style}
      aria-hidden={title ? undefined : true}
      role={title ? 'img' : undefined}
    />
  )
}
