module.exports = {
    baseUrl: './',
    assetsDir: 'assets',
    lintOnSave: true,

    devServer: {
        proxy: {
            '/api': {
                logLevel: 'debug',
                target: 'http://localhost:8081',
                auth: 'admin:admin'
            }
        }
    },

    configureWebpack: {
        resolve: {
            // .mjs needed for https://github.com/graphql/graphql-js/issues/1272
            extensions: ['*', '.mjs', '.js', '.vue', '.json', '.gql', '.graphql']
        },
        module: {
            rules: [ // fixes https://github.com/graphql/graphql-js/issues/1272
                {
                    test: /\.mjs$/,
                    include: /node_modules/,
                    type: 'javascript/auto'
                }
            ]
        }
    },

    outputDir: undefined,
    runtimeCompiler: undefined,
    productionSourceMap: undefined,
    parallel: undefined,
    css: undefined
}