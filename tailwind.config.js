import defaultTheme from 'tailwindcss/defaultTheme';

export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        nordic: {
          background: '#F4F1EE',     // soft bone
          primary: '#1A1C1E',        // near-black
          accent: '#6C7A89',         // iron gray
          wood: '#A67B5B',           // warm wood
          ember: '#C1440E',          // deep orange-red
          highlight: '#557A95',      // desaturated fjord blue
        },
      },
    },
  },
  plugins: [],
}
