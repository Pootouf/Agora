/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#3498DB',
        success: '#4CAF50',
        danger: '#E74C3C',
        warning: '#F39C12',
      },
      fontFamily: {
        lato: ["Lato", "Arial", "sans-serif"]
      }
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms')
  ],
}
