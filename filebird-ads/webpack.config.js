const paths = require('./paths')
const environment = (process.env.NODE_ENV || 'development').trim()
const sm = environment === 'development'

module.exports = {
  entry: [paths.src + '/index.js'],
  output: {
    path: paths.build,
    filename: 'fbv-ads.js',
    publicPath: '/',
  },
  resolve: {
    alias: {
      '@': paths.src,
    },
    extensions: ['.js'],
  },
  module: {
    rules: [
      // JavaScript: Use Babel to transpile JavaScript files
      { test: /\.js$/, use: ['babel-loader'] },

      {
        // For pure CSS - /\.css$/i,
        // For Sass/SCSS - /\.((c|sa|sc)ss)$/i,
        // For Less - /\.((c|le)ss)$/i,
        test: /\.((c|sa|sc)ss)$/i,
        use: ['style-loader', { loader: 'css-loader', options: { sourceMap: sm } }, { loader: 'postcss-loader', options: { sourceMap: sm } }, { loader: 'sass-loader', options: { sourceMap: sm } }],
      },

      // Images: Copy image files to build folder
      { test: /\.(?:ico|gif|png|jpg|jpeg)$/i, type: 'asset/resource' },

      // Fonts and SVGs: Inline files
      { test: /\.(woff(2)?|eot|ttf|otf|svg|)$/, type: 'asset/inline' },
    ],
  },
}
