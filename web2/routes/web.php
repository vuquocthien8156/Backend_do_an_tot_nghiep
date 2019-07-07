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
Route::get('wellcome', 'LoginController@wellcome')->name('wellcome');
Route::post('dangnhap', 'LoginController@login', '_token');
Route::post('dangnhapsdt', 'LoginController@loginsdt', '_token');
Route::get('logout', 'LoginController@logout');
Route::get('verify', 'LoginController@verifyView')->name('verify');
Route::get('changePasswordAdmin', 'LoginController@changePasswordAdmin')->name('changePasswordAdmin');
Route::post('verify', ['as' => 'verify', 'uses' => 'LoginController@verify']);
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
	Route::get('thong-ke', 'ProductController@statisticalView')->name('thong-ke');
	Route::get('search-thong-ke', 'ProductController@searchStatistical');
	Route::get('show-more-img', 'ProductController@showMoreImg');
	Route::post('update-img', 'ProductController@updateImg');
});

Route::prefix('discount')->group(function() {
	Route::get('add-view', 'ProductController@viewAddDiscount')->name('discount-add');
	Route::post('add-new', 'ProductController@addDiscount');
	Route::get('manage-discount', 'ProductController@discountView')->name('manage-discount');
	Route::get('search', 'ProductController@searchDiscount');
	Route::post('delete', 'ProductController@deleteDiscount');
	Route::post('edit', 'ProductController@editDiscount');
	Route::get('show-more-img', 'ProductController@showMoreImg');
	Route::post('update-img', 'ProductController@updateImgDiscount');
});

Route::prefix('news')->group(function() {
	Route::get('news-view', 'ProductController@viewAddNews')->name('news-add');
	Route::post('add-new', 'ProductController@addNews');
	Route::get('manage-news', 'ProductController@NewsView1')->name('manage-news');
	Route::get('search', 'ProductController@searchNews');
	Route::post('delete', 'ProductController@deleteNews');
	Route::post('edit', 'ProductController@editNews');
	Route::post('update-img', 'ProductController@updateImgNews');
	Route::get('show-more-img', 'ProductController@showMoreImg');
});

Route::prefix('Branch')->group(function() {
	Route::get('manage', 'BranchController@branchView')->name('manage-branch');
	Route::get('search', 'BranchController@searchBranch');
	Route::post('save', 'BranchController@saveBranch');
	Route::post('delete', 'BranchController@deleteBranch');
	Route::post('update', 'BranchController@updateBranch');
});

Route::prefix('permission')->group(function() {
	Route::get('manage', 'PermissionController@PermissionView')->name('permission');
	Route::get('listPermission', 'PermissionController@listPermissionUser');
	Route::post('create', 'PermissionController@createPermission');
	Route::post('update', 'PermissionController@updatePermission');
	Route::post('delete', 'PermissionController@deletePermission');
});

Route::prefix('order')->group(function() { 
	Route::get('manage', 'OrderController@orderView')->name('order');
	Route::get('search', 'OrderController@searchOrder');
	Route::get('detail', 'OrderController@detailOrder');
	Route::post('accept', 'OrderController@accepthOrder');
	Route::post('edit', 'OrderController@editOrder');
	Route::post('delete', 'OrderController@deleteOrder');
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

Route::post('api/login-fb', 'FacebookAuthController@loginfb');
Route::get('api/listRankProduct', 'ProductController@searchRankProduct');
Route::get('api/TheMostFavoriteProduct', 'ProductController@getIdSp');
Route::get('api/listProduct', 'ProductController@searchProductAPI');
Route::get('api/likedProduct', 'LoginController@getLikedProduct');//sp dc yeu thich
Route::get('api/like', 'LoginController@requestLike');//thích sp
Route::get('api/productType', 'LoginController@productType');
Route::get('api/productDetail', 'LoginController@productDetail');

Route::get('api/news', 'LoginController@news');

Route::post('api/add-cart', 'LoginController@addCart');
Route::post('api/update-cart', 'LoginController@updateCart');
Route::post('api/delete-cart', 'LoginController@deleteCart');
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
Route::post('api/addOrder', 'LoginController@addOrder');
Route::post('api/paymentOnline', 'LoginController@paymentOnline');

Route::get('api/getQuantityAndPrice', 'LoginController@getQuantityAndPrice');//new
Route::get('api/getDiscount', 'LoginController@getSlideShow');

Route::get('api/getLogPointUser', 'LoginController@getAllLogPointUser');
Route::get('api/getPointUser', 'LoginController@getPointUser');
Route::get('api/verify',['as' => 'getlienhe', 'uses' => 'LoginController@lienhe']);
Route::post('api/verify', ['as' => 'postlienhe', 'uses' => 'LoginController@postlienhe']);
Route::get('ChangePassWord', 'LoginController@ChangePassWord');
Route::post('submitChange', 'LoginController@submitChange');
