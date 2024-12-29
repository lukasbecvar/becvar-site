/** frontend assets build configuration */
const Encore = require('@symfony/webpack-encore');

Encore
    // set build path
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // common assets
    .addEntry('scrollbar-css', './assets/css/scrollbar.scss')
    .addEntry('page-loading-js', './assets/js/page-loading.js')
    .addEntry('page-loading-css', './assets/css/page-loading.scss')

    // public page assets
    .addEntry('public-js', './assets/js/public.js')
    .addEntry('public-css', './assets/css/public.scss')

    // admin page assets
    .addEntry('admin-css', './assets/css/admin.scss')

    // error page assets
    .addEntry('error-page-css', './assets/css/error-page.scss')

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

    // visitors metrics chart script
    .addEntry('visitors-metrics-js', './assets/js/visitors-metrics.js')

    // copy static assets
    .copyFiles(
        {
            from: './assets/img', 
            to: 'images/[path][name].[ext]' 
        }
    )

    // other webpack configs
    .splitEntryChunks()
    .enableSassLoader()
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
