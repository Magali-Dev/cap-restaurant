import Encore from '@symfony/webpack-encore';

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addStyleEntry('styles', './assets/styles/app.css')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSingleRuntimeChunk()
;

// NE PAS APPELER configureBabel si tu as déjà un .babelrc ou babel.config.js
// Encore.configureBabel(null);

export default Encore.getWebpackConfig();
