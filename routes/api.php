<?php

use App\Http\Controllers\Api\MallShopApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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



Route::get('/manager/youtube_proc', [MallShopApi::class, 'youtube_proc']);



Route::post('/erp/file_transfer', [MallShopApi::class, 'file_transfer']);



Route::post('/delete_cach', [MallShopApi::class, 'delete_cach']);




