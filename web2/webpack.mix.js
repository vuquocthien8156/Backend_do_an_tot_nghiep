let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.browserSync(process.env.APP_URL);

mix

	.js('resources/assets/js/common.js', 'public/js')
    .js('resources/assets/js/Branch/Branch.js', 'public/js/Branch')
    .js('resources/assets/js/login/login/login.js', 'public/js/login/login')
    .js('resources/assets/js/permission/permission/permission.js', 'public/js/permission/permission')
    .js('resources/assets/js/order/order/order.js', 'public/js/order/order')
    .js('resources/assets/js/account/account/account.js', 'public/js/account/account')
    .js('resources/assets/js/account/account/jquery.fancybox.min.js', 'public/js/account/account')
    .js('resources/assets/js/product/product/product.js', 'public/js/product/product')
    .js('resources/assets/js/product/product/thongke.js', 'public/js/product/product')
    .js('resources/assets/js/product/product/jquery.fancybox.min.js', 'public/js/product/product')
    .js('resources/assets/js/product/product/addProduct.js', 'public/js/product/product')
    .js('resources/assets/js/register/register/register.js', 'public/js/register/register')
    .js('resources/assets/js/test/test/test.js', 'public/js/test/test')
    .js('resources/assets/js/bootstrap.min.js', 'public/js/config')
    .js('resources/assets/js/jquery.actual.min.js', 'public/js/config')
    .js('resources/assets/js/jquery.scrollTo.min.js', 'public/js/config')
    .js('resources/assets/js/main.js', 'public/js/config')

    // add new JS config above this line
    .js('resources/assets/js/app.js', 'public/js')
    .extract(['vue'])

    .sass('resources/assets/scss/app.scss', 'public/css')
    

    .scripts([
        'resources/assets/js/libs/modernizr/modernizr-custom.js',
    ], 'public/js/ui.js')
    .scripts([
        'resources/assets/js/libs/prefixfree/prefixfree.min.js',
        'resources/assets/js/libs/prefixfree/prefixfree.viewport-units.js',
    ], 'public/js/lib/prefixfree.min.js')
    .scripts([
		'resources/assets/js/libs/nicecountryinput/niceCountryInput.js'
    ], 'public/js/lib/niceCountryInput.js')
    .scripts([
		'resources/assets/js/libs/notify/notify.min.js'
    ], 'public/js/lib/notify.min.js')
;


if (mix.inProduction()) {
    mix.version();
}