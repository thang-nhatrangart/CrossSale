module.exports = {
  plugins: {
    'postcss-preset-env': {
      stage: 4,
      browsers: ['last 5 versions', 'not ie <= 8'],
    },
    cssnano: {},
  },
}
