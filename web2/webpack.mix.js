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
    .js('resources/assets/js/login/login/login.js', 'public/js/login/login')
    .js('resources/assets/js/permission/permission/permission.js', 'public/js/permission/permission')
    .js('resources/assets/js/account/account/account.js', 'public/js/account/account')
    .js('resources/assets/js/register/register/register.js', 'public/js/register/register')
    .js('resources/assets/js/test/test/test.js', 'public/js/test/test')
    .js('resources/assets/js/customer/manage-customer/manage-customer.js', 'public/js/customer/manage-customer')
    .js('resources/assets/js/customer/manage-card-member/manage-card-member.js', 'public/js/customer/manage-card-member')
    .js('resources/assets/js/customer/manage-appointment/manage-appointment.js', 'public/js/customer/manage-appointment')
    .js('resources/assets/js/customer/make-appointment/make-appointment.js', 'public/js/customer/make-appointment')

    .js('resources/assets/js/vehicle/manage-vehicle/manage-vehicle.js', 'public/js/vehicle/manage-vehicle')
    .js('resources/assets/js/vehicle/manage-vehicle/jquery.fancybox.min.js', 'public/js/vehicle/manage-vehicle')
    .js('resources/assets/js/vehicle/manage-vehicle/see-more-description.js', 'public/js/vehicle/manage-vehicle')

    .js('resources/assets/js/trading/manage-trading/manage-trading.js', 'public/js/trading/manage-trading')
    .js('resources/assets/js/trading/manage-trading/see-more-feedback.js', 'public/js/trading/manage-trading')      

    .js('resources/assets/js/setting/manufacture-model/manufacture-model.js', 'public/js/setting/manufacture-model')
    .js('resources/assets/js/setting/create-branch/create-branch.js', 'public/js/setting/create-branch')
    .js('resources/assets/js/setting/birthday/birthday.js', 'public/js/setting/birthday')
    .js('resources/assets/js/setting/bank-tranfer/bank-tranfer.js', 'public/js/setting/bank-tranfer')

    .js('resources/assets/js/notification/manage-notification/manage-notification.js', 'public/js/notification/manage-notification')

    .js('resources/assets/js/rescue/manage-rescue/manage-rescue.js', 'public/js/rescue/manage-rescue')

    .js('resources/assets/js/employees/manage-employees/manage-employees.js', 'public/js/employees/manage-employees')

    .js('resources/assets/js/conversation/chat-user/chat-user.js', 'public/js/conversation/chat-user')

    .js('resources/assets/js/conversation/chat-user/chat-user2.js', 'public/js/conversation/chat-user')

    .js('resources/assets/js/conversation/chat-user/chat-user3.js', 'public/js/conversation/chat-user')

    .js('resources/assets/js/authorization/authorization-user-web/authorization-user-web.js', 'public/js/authorization/authorization-user-web')

    .js('resources/assets/js/config/banner.js', 'public/js/config')

    .js('resources/assets/js/forwarder/vehicle-transfer/vehicle-transfer.js', 'public/js/forwarder/vehicle-transfer')

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