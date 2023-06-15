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

Route::domain('admin.' . config('app.self_domain'))->group(function () {
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Auth::routes();
    Route::get('/home', 'Admin\HomeController@index')->name('home');
    Route::get('/adminuser/list', 'Admin\AdminUser\AdminUserController@list')->name('adminuser/list');
    Route::get('/adminuser', 'Admin\AdminUser\AdminUserController@get')->name('adminuser/get');
    Route::delete('/adminuser', 'Admin\AdminUser\AdminUserController@delete')->name('adminuser/delete');
    Route::put('/adminuser', 'Admin\AdminUser\AdminUserController@update')->name('adminuser/update');
    Route::post('/password/change', 'Auth\ChangePasswordController@sendResetLinkEmail')->name('adminuser/password/change');

    Route::get('/chart/list/{chart_phase}', 'Admin\Chart\ChartController@list')->where('chart_phase', '(released|provisioned)')->name('chart/list');
    Route::get('/chart/{chart_phase}', 'Admin\Chart\ChartController@get')->where('chart_phase', '(released|provisioned)')->name('chart/get');
    Route::post('/chart', 'Admin\Chart\ChartController@register')->name('chart/register');
    Route::put('/chart/{chart_phase}', 'Admin\Chart\ChartController@modify')->where('chart_phase', '(released|provisioned)')->name('chart/modify');
    Route::delete('/chart', 'Admin\Chart\ChartController@delete')->name('chart/delete');
    Route::post('/chart/release', 'Admin\Chart\ChartController@release')->name('chart/release');
    Route::post('/chart/rollback', 'Admin\Chart\ChartController@rollback')->name('chart/rollback');

    Route::get('/chartterm/list', 'Admin\ChartTerm\ChartTermController@list')->name('chartterm/list');
    Route::get('/chartterm/get', 'Admin\ChartTerm\ChartTermController@get')->name('chartterm/get');
    Route::delete('/chartterm/delete', 'Admin\ChartTerm\ChartTermController@delete')->name('chartterm/delete');
    Route::post('/chartterm/release', 'Admin\ChartTerm\ChartTermController@release')->name('chartterm/release');
    Route::post('/chartterm/rollback', 'Admin\ChartTerm\ChartTermController@rollback')->name('chartterm/rollback');
    Route::post('/chartterm/resolve', 'Admin\ChartTerm\ChartTermController@resolve')->name('chartterm/resolve');

    Route::get('/chartrankingitem/itunessearch', 'Admin\ChartRankingItem\ChartRankingItemController@itunessearch')->name('chartrankingitem/itunessearch');
    Route::get('/chartrankingitem/notattached', 'Admin\ChartRankingItem\ChartRankingItemController@notattached')->name('chartrankingitem/notattached');

    Route::post('/artist/register', 'Admin\Artist\ArtistController@register')->name('artist/register');
    Route::post('/artist/modify', 'Admin\Artist\ArtistController@modify')->name('artist/modify');
    Route::post('/artist/delete', 'Admin\Artist\ArtistController@delete')->name('artist/delete');
    Route::post('/artist/release', 'Admin\Artist\ArtistController@release')->name('artist/release');
    Route::post('/artist/rollback', 'Admin\Artist\ArtistController@rollback')->name('artist/rollback');
    Route::get('/artist/search/{artist_phase}', 'Admin\Artist\ArtistController@search')->where('artist_phase', '(released|provisioned)')->name('artist/search');

    Route::post('/music/register', 'Admin\Music\MusicController@register')->name('music/register');
    Route::post('/music/modify', 'Admin\Music\MusicController@modify')->name('music/modify');
    Route::post('/music/delete', 'Admin\Music\MusicController@delete')->name('music/delete');
    Route::post('/music/release', 'Admin\Music\MusicController@release')->name('music/release');
    Route::post('/music/rollback', 'Admin\Music\MusicController@rollback')->name('music/rollback');
    Route::get('/music/search/{music_phase}', 'Admin\Music\MusicController@search')->where('music_phase', '(released|provisioned)')->name('music/search');
    Route::get('/music/promotion_video_broken_links', 'Admin\Music\MusicController@promotion_video_broken_links')->name('music/promotion_video_broken_links');
});

Route::domain('www.' . config('app.self_domain'))->group(function () {
//    Route::get('/', function () { return view('welcome'); });
    Route::match(['get', 'post'], '/', 'WWW\Top\TopController@top')->name('top');
    Route::match(['get', 'post'], '/chart/{chartNameValue?}/{countryIdValue?}/{endDateValue?}', 'WWW\Chart\ChartController@chart')
        ->where('endDateValue', '[0-9]{4}-[0-1]{0,1}[0-9]{1}-[0-3]{0,1}[0-9]{1}')
        ->where('countryIdValue', '[a-zA-Z]{2}')
        ->name('chart/get');
    Route::match(['get', 'post'], '/howtouse', 'WWW\Statics\StaticsController@howtouse')->name('howtouse');
    Route::match(['get', 'post'], '/privacypolicy', 'WWW\Statics\StaticsController@privacypolicy')->name('privacypolicy');
    Route::match(['get', 'post'], '/termsofuse', 'WWW\Statics\StaticsController@termsofuse')->name('termsofuse');
    Route::match(['get', 'post'], '/contactus', 'WWW\Statics\StaticsController@contactus')->name('contactus');
    Route::match(['post'], '/contactmail', 'WWW\Statics\ContactMailController@send')->name('contactmail');
    Route::match(['get', 'post'], '/mailsent', 'WWW\Statics\StaticsController@mailsent')->name('mailsent');
    Route::match(['post'], '/music/resetpromotionvideos', 'WWW\Music\MusicController@resetPromotionVideos')->name('music/resetpromotionvideos');
    Route::fallback('WWW\Chart\ChartController@chart');
});
