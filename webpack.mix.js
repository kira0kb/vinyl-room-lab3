const mix = require('laravel-mix');

mix.setPublicPath('public')
    .js('resources/js/app.js', 'public/js')
    .sass('resources/scss/app.scss', 'public/css', {
        sassOptions: {
            quietDeps: true,
            silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
        },
    })
    .options({ processCssUrls: false })
    .version()
    .disableNotifications();

// При watch изменения Blade, SCSS и JS обновляют страницу на порту 3000.
if (mix.inWatch()) {
    mix.browserSync({
        proxy: 'http://127.0.0.1:8000',
        host: '127.0.0.1',
        port: 3000,
        open: false,
        notify: false,
        ui: false,
        files: ['resources/views/**/*.blade.php', 'public/css/*.css', 'public/js/*.js'],
    });
}
