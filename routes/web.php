<?php

use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;


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

Route::get('/', function() {

    return view('index/index');

});

Route::any('/encryption/rsa', [EncryptionController::class, "rsa"]);
Route::get('/encryption/rsa/{keySize}', [EncryptionController::class, "rsaGenerateKey"]);

Route::post('/', [IndexController::class, "index"]);
