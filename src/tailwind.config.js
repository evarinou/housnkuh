/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Quicksand', 'system-ui', '-apple-system', 'sans-serif'],
      },
      colors: {
        // Markenfarben definieren für einheitliche Verwendung
        'primary': {
          DEFAULT: '#e17564',
          light: '#e89385',
          dark: '#c6624f',
        },
        'secondary': {
          DEFAULT: '#09122c',
          light: '#1a224d',
          dark: '#050a1a',
        },
        'brand': {
          red: '#e17564',
          navy: '#09122c',
          white: '#ffffff',
        }
      },
      boxShadow: {
        'brand': '0 4px 6px rgba(9, 18, 44, 0.1)',
      },
      animation: {
        'spin-slow': 'spin 8s linear infinite',
        'pulse-slow': 'pulse 4s ease-in-out infinite',
      }
    },
  },
  plugins: [],
}