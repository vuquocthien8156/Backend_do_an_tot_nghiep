<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get('home', 'logintestController@d')->name('home');
Route::get('login', 'LoginController@loginView')->name('login');
Route::post('dangnhap', 'LoginController@login', '_token');
Route::post('dangnhapsdt', 'LoginController@loginsdt', '_token');
Route::get('logout', 'LoginController@logout');
Route::get('register', 'RegisterController@registerView')->name('register');
Route::post('dangky', 'RegisterController@register');

Route::get('manage/account', 'AccountController@AccountView')->name('manage-account');
Route::get('manage/search', 'AccountController@search');
Route::post('manage/delete', 'AccountController@deleteAccount');
Route::post('manage/edit', 'AccountController@editAccount');

Route::prefix('product')->group(function() {
	Route::get('add-view', 'ProductController@viewAddProduct')->name('product-add');
	Route::post('add-new', 'ProductController@addProduct');
	Route::get('manage', 'ProductController@productView')->name('manage-product');
	Route::get('search', 'ProductController@searchProduct');
	Route::post('delete', 'ProductController@deleteProduct');
	Route::post('edit', 'ProductController@editProduct');
});

Route::get('permission', 'PermissionController@PermissionView');
Route::post('permission', 'PermissionController@Permission');

Route::get('auth/facebook', 'FacebookAuthController@redirectToProvider')->name('facebook.login') ;
Route::get('auth/facebook/callback', 'FacebookAuthController@handleProviderCallback');


//api
<<<<<<< HEAD
Route::post('api/register', 'RegisterController@register');
Route::post('api/registerPhoneNumber', 'RegisterController@registerForPhone');
=======
Route::post('api/register' , 'RegisterController@register');
>>>>>>> 47a5dcf9095712c128e3787f80f70178e2990e0e
Route::post('api/deleteAccount', 'AccountController@deleteAccount');
Route::post('api/dangnhap' , 'LoginController@login');
Route::post('api/dangnhapsdt', 'LoginController@loginsdt');
Route::get('api/listcAcount', 'AccountController@searchAccount');
<<<<<<< HEAD
Route::get('api/listcRankProduct', 'ProductController@searchRankProduct');
Route::get('api/checkLoginExist', 'LoginController@check');
Route::get('api/checkRegisterExist', 'RegisterController@check');
Route::get('api/logout', 'LoginController@logout');
=======
Route::get('api/checkExist', 'LoginController@check');
Route::get('api/logout' , 'LoginController@logout');
>>>>>>> 47a5dcf9095712c128e3787f80f70178e2990e0e
Route::get('api/listProduct', 'ProductController@searchProduct');
Route::get('api/auth/facebook/callback', 'FacebookAuthController@handleProviderCallback');
Route::get('api/updateInfo', 'LoginController@requestUpdateInfo');
Route::get('api/likedProduct', 'LoginController@getLikedProduct');
Route::get('api/like', 'LoginController@requestLike');