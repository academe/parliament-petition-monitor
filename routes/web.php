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

Route::middleware('throttle:30,1')->group(function () {
    // There is just the one page for now.

    Route::redirect('/', '/overview');

    // ReportController

    Route::get('/overview/{petitionNumber?}', 'ReportController@simpleOverview')
        ->name('overview');
});
