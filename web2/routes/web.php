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
Route::get('loginView', 'LoginController@loginView')->name('login');
Route::post('dangnhap', 'LoginController@login', '_token');
Route::post('dangnhapsdt', 'LoginController@loginsdt', '_token');
Route::get('logout', 'LoginController@logout');
Route::get('register', 'RegisterController@registerView')->name('register');
Route::post('dangky', 'RegisterController@register');

Route::get('manage/account', 'AccountController@AccountView')->name('manage-account');
Route::get('manage/search', 'AccountController@search');
Route::get('manage/excel-account', 'AccountController@exportAccount');
Route::post('manage/delete', 'AccountController@deleteAccount');
Route::post('manage/edit', 'AccountController@editAccount');


Route::prefix('products')->group(function() {
	Route::get('add-view', 'ProductController@viewAddProduct')->name('product-add');
	Route::post('add-new', 'ProductController@addProduct');
	Route::get('manage-product', 'ProductController@productView')->name('manage-product');
	Route::get('search', 'ProductController@searchProduct');
	Route::post('delete', 'ProductController@deleteProduct');
	Route::post('edit', 'ProductController@editProduct');
	Route::get('productDetail', 'ProductController@detailView')->name('details');
	Route::get('news', 'ProductController@newsView')->name('news');
	Route::get('discount', 'ProductController@KM')->name('news');
	Route::get('excel-product', 'ProductController@exportProduct');
	Route::get('thong-ke', 'ProductController@statisticalView');
	Route::get('search-thong-ke', 'ProductController@searchStatistical');
});

Route::prefix('Branch')->group(function() {
	// Route::get('add-view', 'ProductController@viewAddProduct')->name('product-add');
	// Route::post('add-new', 'ProductController@addProduct');
	Route::get('manage', 'BranchController@branchView')->name('manage-branch');
	Route::get('search', 'BranchController@searchBranch');
	Route::post('save', 'BranchController@saveBranch');
	Route::post('delete', 'BranchController@deleteBranch');
	Route::post('update', 'BranchController@updateBranch');
	// Route::post('delete', 'ProductController@deleteProduct');
	// Route::post('edit', 'ProductController@editProduct');
});

Route::prefix('permission')->group(function() {
	Route::get('manage', 'PermissionController@PermissionView')->name('permission');
	Route::get('listPermission', 'PermissionController@listPermissionUser');
	Route::post('create', 'PermissionController@createPermission');
	Route::post('update', 'PermissionController@updatePermission');
	Route::post('delete', 'PermissionController@deletePermission');
});



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
Route::post('api/changePassword', 'LoginController@updatePassword');
Route::post('api/updatePhone', 'LoginController@updateNumberPhone');

Route::post('api/updateInfo', 'LoginController@requestUpdateInfo');
Route::post('api/uploadImage', 'LoginController@uploadImage');
Route::post('api/uploadManyImage', 'LoginController@uploadManyImage');

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

Route::post('api/add-cart', 'LoginController@addCart');
Route::post('api/update-cart', 'LoginController@updateCart');
Route::post('api/delete-cart', 'LoginController@deleteCart');
Route::post('api/delete-all-cart-of-customer', 'LoginController@deleteCartCustomer');
Route::post('api/cart/updateToppingCart', 'LoginController@updateToppingCart');
Route::get('api/getCartOfCustomer', 'LoginController@getCartOfCustomer');

Route::get('api/getEvaluate', 'LoginController@getEvaluate');//new
Route::get('api/getChildEvaluate', 'LoginController@getChildEvaluate');//new

Route::get('api/getBranch', 'LoginController@getBranch');

Route::post('api/addEvaluate', 'LoginController@addEvaluate');
Route::post('api/addChildEvaluate', 'LoginController@addChildEvaluate');

Route::post('api/addThanks', 'LoginController@addThanks');

Route::get('api/getAllAddressByUser', 'LoginController@getAddressOrderUser');
Route::post('api/insertAddressOrder', 'LoginController@insertAddresOrderUser');
Route::post('api/updateAddressOrder', 'LoginController@updateAddresOrderUser');

Route::get('api/getOrderOfCustomer', 'LoginController@getAllOrder');
Route::get('api/getOrderDetail', 'LoginController@getOrderDetail');
Route::get('api/addOrder', 'LoginController@addOrder');

Route::get('api/getChildImage', 'LoginController@getChildImage');//new

Route::get('api/getQuantityAndPrice', 'LoginController@getQuantityAndPrice');//new
Route::get('api/getSlideShow', 'LoginController@getSlideShow');
