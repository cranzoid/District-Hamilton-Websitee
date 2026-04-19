/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/Filament/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        ink: {
          DEFAULT: '#0E0E0E',
          soft: '#1C1A17',
          muted: '#6B6258',
        },
        paper: {
          DEFAULT: '#F7F3EC',
          warm: '#EFE8DB',
          deep: '#E6DFD0',
        },
        brand: {
          DEFAULT: '#B8860B',
          light: '#D6AB39',
          dark: '#8F6908',
        },
        ember: {
          DEFAULT: '#B8381F',
          light: '#D4543B',
          dark: '#8F2A16',
        },
        /* legacy aliases kept so old blades don't break mid-rebuild */
        primary: '#B8860B',
        'primary-light': '#D6AB39',
        'primary-dark': '#8F6908',
        secondary: '#1C1A17',
        accent: '#B8860B',
        dark: '#0E0E0E',
        light: '#FFFFFF',
      },
      fontFamily: {
        sans: ['"Inter Tight"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        display: ['Fraunces', 'ui-serif', 'Georgia', 'serif'],
      },
      boxShadow: {
        'card': '0 1px 2px rgba(14,14,14,0.04), 0 8px 24px rgba(14,14,14,0.06)',
        'card-hover': '0 4px 12px rgba(14,14,14,0.08), 0 24px 48px rgba(14,14,14,0.12)',
        'inset-line': 'inset 0 -1px 0 rgba(14,14,14,0.08)',
      },
      letterSpacing: {
        'eyebrow': '0.18em',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
