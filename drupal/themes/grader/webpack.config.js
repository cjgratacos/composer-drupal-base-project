var path = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

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
             }
         ]
    },
    resolve: {
        extensions: [".js", ".scss","scss"]
    },
    plugins: [
        new ExtractTextPlugin("main.css")
    ]
};