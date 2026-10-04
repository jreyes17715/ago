module.exports = {
  content: [
    './blocks/**/*.php',
    './parts/**/*.php',
    './template-parts/**/*.php',
    './*.php',
    './assets/src/**/*.css',
  ],
  theme: {
    extend: {
      colors: {
        'bvt-blue':       '#0077FF',
        'bvt-blue-dark':  '#003C78',
        'bvt-gray-mid':   '#4A5A72',
        'bvt-gray-light': '#F0F4F9',
        'bvt-gray-dark':  '#353535',
        'bvt-white':      '#ffffff',
      },
    },
  },
  plugins: [],
};
