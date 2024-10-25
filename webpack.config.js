/*
 * becvar-site frontend webpack builder
 */
const Encore = require('@symfony/webpack-encore');

Encore
    // set build path
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // common assets
    .addEntry('scrollbar-css', './assets/css/scrollbar.css')
    .addEntry('page-loading-js', './assets/js/page-loading.js')
    .addEntry('page-loading-css', './assets/css/page-loading.css')

    // public page assets
    .addEntry('public-js', './assets/js/public.js')
    .addEntry('public-css', './assets/css/public.css')

    // admin page assets
    .addEntry('admin-css', './assets/css/admin.css')

    // error page assets
    .addEntry('error-page-css', './assets/css/error-page.css')

    // bootstrap
    .addEntry('bootstrap-css', './node_modules/bootstrap/dist/css/bootstrap.css')
    .addEntry('bootstrap-js', './node_modules/bootstrap/dist/js/bootstrap.bundle.js')
    .addEntry('bootstrap-icons-css', './node_modules/bootstrap-icons/font/bootstrap-icons.css')

    // boxicons
    .addEntry('boxicons-css', './node_modules/boxicons/css/boxicons.css')

    // fontawesome
    .addEntry('fontawesome-css', './node_modules/@fortawesome/fontawesome-free/css/all.css')

    // purecounter
    .addEntry('purecounter-js', './assets/js/purecounter.js')

    // waypoints
    .addEntry('skills-progress-js', './assets/js/skills-progress.js')

    // visitor status updater
    .addEntry('update-visitor-status-js', './assets/js/update-visitor-status.js')

    .addEntry('metrics-chart', './assets/js/metrics-chart.js')

    // copy static assets
    .copyFiles(
        {
            from: './assets/img', 
            to: 'images/[path][name].[ext]' 
        }
    )

    // other webpack configs
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
;

module.exports = Encore.getWebpackConfig();
