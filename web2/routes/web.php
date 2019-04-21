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
Route::get('api', 'logintestController@d')->name('home');
Route::get('login', 'LoginController@loginView');
Route::post('dangnhap', 'LoginController@login', '_token');
Route::post('dangnhapsdt', 'LoginController@loginsdt', '_token');
Route::get('logout', 'LoginController@logout');
Route::get('register', 'RegisterController@registerView');
Route::post('dangky', 'RegisterController@register');

Route::get('manage/account', 'AccountController@AccountView')->name('manage-account');
Route::get('manage/search', 'AccountController@search');
Route::post('manage/delete', 'AccountController@deleteAccount');
Route::post('manage/edit', 'AccountController@editAccount');

Route::get('permission', 'PermissionController@PermissionView');
Route::post('permission', 'PermissionController@Permission');

Route::get('auth/facebook', 'FacebookAuthController@redirectToProvider')->name('facebook.login') ;
Route::get('auth/facebook/callback', 'FacebookAuthController@handleProviderCallback');


//api
Route::post('api/deleteAccount', 'AccountController@deleteAccount');
Route::post('api/dangnhap', 'LoginController@login');
Route::post('api/dangnhapsdt', 'LoginController@loginsdt');
Route::get('api/listcAcount', 'AccountController@search');
Route::get('api/checkExist', 'LoginController@check');
Route::get('api/logout', 'LoginController@logout');
