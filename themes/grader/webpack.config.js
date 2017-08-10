var path = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

var extractSass = new ExtractTextPlugin({
    filename: "main.css"
});

module.export = {
    entry: [
        "./src/js/app.js"
    ],
    output: {
        path: path.resolve(__dirname, 'src/dist/'),
        filename: "bundle.js",
        publicPath: "/src/dist"
    },
    module: {
         rules: [
             {
                 test: /\.js$/,
                 use: [
                     {
                        loader: 'babel-loader',
                        options: {
                            presets: ['es2015']
                        }
                     }
                 ]
             },
             {
                 test: /\.scss$/,
                 include: [
                     path.resolve(__dirname, 'src/scss')
                 ],
                 use: extractSass.extract({
                     use: [
                         'css-loader',
                         'sass-loader'
                     ]
                 })
             }
         ]
    },
    resolve: {
        extensions: [".js", ".scss","scss"]
    },
    plugins: [
        extractSass
    ]
};