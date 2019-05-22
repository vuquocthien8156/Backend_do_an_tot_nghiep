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
Route::post('permission', 'Permi	ssionController@Permission');

Route::get('auth/facebook', 'FacebookAuthController@redirectToProvider')->name('facebook.login') ;
Route::get('auth/facebook/callback', 'FacebookAuthController@handleProviderCallback');

//Điều khoản sử dụng
Route::get('rule', 'LoginController@rule')->name('rules') ;
//api
Route::post('api/register', 'RegisterController@registerAPI');

Route::post('api/login' , 'LoginController@loginAPI');
Route::get('api/getInfoByEmail' , 'LoginController@getInfoByEmail');
Route::post('api/login-by-phone', 'LoginController@loginsdtAPI');
Route::get('api/checkLoginExist', 'LoginController@check');

Route::post('api/updateInfo', 'LoginController@requestUpdateInfo');
Route::post('api/uploadImage', 'LoginController@uploadImage');

// Route::get('api/login/facebook', 'FacebookAuthController@handleProviderCallback');//đăng nhap lần đầu và insert thông tin
// Route::post('api/insert-no-mail', 'FacebookAuthController@insertNoMail');
// Route::post('api/update-id_fb', 'LoginController@updateIdFB');
// Route::post('api/update-email', 'LoginController@updateEmail');//update email theo fb_id và có hash pass

Route::post('api/login-fb', 'FacebookAuthController@loginfb');

Route::get('api/listRankProduct', 'ProductController@searchRankProduct');
Route::get('api/TheMostFavoriteProduct', 'ProductController@getIdSp');
Route::get('api/listProduct', 'ProductController@searchProductAPI');
Route::get('api/likedProduct', 'LoginController@getLikedProduct');//sp dc yeu thich
Route::get('api/like', 'LoginController@requestLike');//thích sp
Route::get('api/checkLikeByUser', 'LoginController@checkLikeProductByUser');
Route::get('api/productType', 'LoginController@productType');
Route::get('api/productDetail', 'LoginController@productDetail');

Route::get('api/news', 'LoginController@news');

Route::get('api/add-cart', 'LoginController@addCart');
Route::post('api/delete-cart', 'LoginController@deleteCart');
Route::post('api/delete-all-cart-of-customer', 'LoginController@deleteCartCustomer');
Route::post('api/cart/update-quantity', 'LoginController@updateQuantity');
Route::get('api/getCartOfCustomer', 'LoginController@getCartOfCustomer');

Route::get('api/getEvaluate', 'LoginController@getEvaluate');
Route::get('api/getChildEvaluate', 'LoginController@getChildEvaluate');

Route::get('api/getBranch', 'LoginController@getBranch');

Route::get('api/addEvaluate', 'LoginController@addEvaluate');

Route::get('api/addThanks', 'LoginController@addThanks');

Route::get('api/getOrderOfCustomer', 'LoginController@getAllOrder');