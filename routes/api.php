<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/control/{serial}', 'ItemController@getItem');
Route::get('/control/', 'ItemController@storeItem');
Route::get('/test/', function() {
    $interestDetails = ['unique identifier', 'ExponentPushToken[IMhSDAKhZuoH6zjxZ9fvMP]'];
    $expo = \ExponentPhpSDK\Expo::normalSetup();
    $expo->subscribe($interestDetails[0], $interestDetails[1]);
    $notification = ['body' => 'quan stupod!','title' => 'thich the'];
    dd($expo->notify($interestDetails[0], $notification));
});
Route::post('/expo/token', 'ItemController@storeToken');
Route::post('/mobile/item/save', 'ItemController@saveItem');


