const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: {
    main: './assets/src/ts/main.ts',
    admin: './assets/src/scss/admin.scss',
  },
  module: {
    rules: [
      {
        test: /\.ts$/,
        use: 'ts-loader',
        exclude: /node_modules/,
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: 'sass-loader',
            options: {
              // Enable source maps
              implementation: require('sass'),
              sourceMap: true,
              sassOptions: {
                // Enable modern Sass features
                outputStyle: 'expanded',
              },
            },
          },
        ],
      },
    ],
  },
  resolve: {
    extensions: ['.ts', '.js'],
  },
  output: {
    filename: 'js/[name].js',
    path: path.resolve(__dirname, 'assets/dist'),
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
    }),
  ],
  externals: {
    jquery: 'jQuery',
  },
  mode: 'development',
  devtool: 'source-map',
};
