/*
 * becvar-site frontend webpack builder
 */
const Encore = require('@symfony/webpack-encore');

Encore
    // set build path
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // register common assets
    .addEntry('scrollbar-css', './assets/css/scrollbar.css')
    .addEntry('page-loading-css', './assets/css/page-loading.css')
    .addEntry('page-loading-js', './assets/js/page-loading.js')

    // register public-page assets
    .addEntry('public-css', './assets/css/public.css')
    .addEntry('public-js', './assets/js/public.js')

    // registr admin-page assets
    .addEntry('admin-css', './assets/css/admin.css')

    // register error-page assets
    .addEntry('error-page-css', './assets/css/error-page.css')
    .addEntry('fluid-animation-js', './assets/js/fluid-animation.js')
    
    // register image-viewer assets
    .addEntry('image-viewer-css', './assets/css/image-viewer.css')
    
    // register code-paste assets
    .addEntry('atom-one-dark-css', './assets/css/atom-one-dark.css')
    .addEntry('paste-add-css', './assets/css/paste-add.css')
    .addEntry('paste-view-css', './assets/css/paste-view.css')
    .addEntry('code-paste-js', './assets/js/code-paste.js')

    // register lightgallery 
    .addEntry('lightgallery-css', './assets/lightgallery/css/lightgallery.css')
    .addEntry('lg-transitions-css', './assets/lightgallery/css/lg-transitions.css')
    .addEntry('lightgallery-js', './assets/lightgallery/js/lightgallery.js')
    .addEntry('lg-autoplay-js', './assets/lightgallery/js/lg-autoplay.js')
    .addEntry('lg-zoom-js', './assets/lightgallery/js/lg-zoom.js')

    // register bootstrap
    .addEntry('bootstrap-css', './node_modules/bootstrap/dist/css/bootstrap.css')
    .addEntry('bootstrap-js', './node_modules/bootstrap/dist/js/bootstrap.bundle.js')
    .addEntry('bootstrap-icons-css', './node_modules/bootstrap-icons/font/bootstrap-icons.css')

    // register boxicons
    .addEntry('boxicons-css', './node_modules/boxicons/css/boxicons.css')

    // register fontawesome
    .addEntry('fontawesome-css', './node_modules/@fortawesome/fontawesome-free/css/all.css')

    // register purecounter
    .addEntry('purecounter-js', './assets/js/purecounter.js')

    // register waypoints
    .addEntry('skills-progress-js', './assets/js/skills-progress.js')

    // register visitor status updater
    .addEntry('update-visitor-status-js', './assets/js/update-visitor-status.js')

    // register admin chat function
    .addEntry('admin-chat-js', './assets/js/admin-chat.js')

    // register admin terminal function
    .addEntry('admin-terminal', './assets/js/admin-terminal.js')

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
