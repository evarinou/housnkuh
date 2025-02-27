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
        primary: {
          DEFAULT: '#e17564',
        },
        secondary: {
          DEFAULT: '#09122c',
        }
      },
      fontWeight: {
        normal: '400',
        medium: '500',
        semibold: '600',
        bold: '700',
      },
      animation: {
        'spin-slow': 'spin 8s linear infinite', // Langsame Drehung für das Settings-Icon
      }
    },
  },
  plugins: [],
}