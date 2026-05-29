/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './ru/**/*.php',
    './admin/**/*.php',
    './includes/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#ecfeff',
          100: '#cffafe',
          200: '#a5f3fc',
          300: '#67e8f9',
          400: '#22d3ee',
          500: '#06b6d4',
          600: '#0891b2',
          700: '#0e7490',
          800: '#155e75',
          900: '#164e63',
        },
        ink: '#0b1220',
      },
      fontFamily: {
        sans: ['DM Sans', 'system-ui', 'sans-serif'],
        display: ['Outfit', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        soft: '0 18px 50px rgba(11,18,32,0.10)',
      },
      keyframes: {
        iconNudge: {
          '0%, 100%': { transform: 'translateY(0)' },
          '35%': { transform: 'translateY(-3px)' },
          '65%': { transform: 'translateY(1px)' },
        },
      },
      animation: {
        'icon-nudge': 'iconNudge 0.45s cubic-bezier(0.34, 1.45, 0.64, 1) both',
      },
    },
  },
  plugins: [],
};
