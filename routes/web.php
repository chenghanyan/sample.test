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

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');
//资源路由
Route::resource('users', 'UsersController');
//获取登录页
Route::get('login', 'SessionsController@create')->name('login');
//提交登录信息
Route::post('login', 'SessionsController@store')->name('login');
//退出登录
Route::delete('logout', 'SessionsController@destroy')->name('logout');
//激活账号
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');
//密码重置页面
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// 生成token动作
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// 接受token链接页面，验证码token
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//重置密码动作
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');