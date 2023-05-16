<?php

use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//login
Route::post('/login', [LoginController::class, 'userLogin'])->name('login');
Route::post('/logout', [LoginController::class, 'userLogout'])->name('logout');



Route::group(["middleware" => ["authentication"]], function() {
    //user-group
    Route::post('/get-user-group', [UserGroupController::class, 'getUserGroup'])->name('get-user-group');
    Route::post('/add-user-group', [UserGroupController::class, 'addUserGroup'])->name('add-user-group');
    Route::post('/delete-user-group', [UserGroupController::class, 'deleteUserGroup'])->name('delete-user-group');
    Route::post('/get-user-group-by-id', [UserGroupController::class, 'getUserGroupById'])->name('get-user-group-by-id');
    Route::post('/update-user-group', [UserGroupController::class, 'updateUserGroupById'])->name('update-user-group');
    
    //user
    Route::post('/add-user', [UserController::class, 'addUser'])->name('add-user');
    Route::post('/update-user', [UserController::class, 'updateUserById'])->name('update-user');
    Route::post('/delete-user', [UserController::class, 'deleteUser'])->name('delete-user');
    Route::post('/get-user-by-id', [UserController::class, 'getUserById'])->name('get-user-by-id');
    Route::post('/get-all-user', [UserController::class, 'getAllUser'])->name('get-all-user');
    
    //license
    Route::post('/delete-license', [LicenseController::class, 'deleteLicense'])->name('delete-license');
    Route::post('/update-license', [LicenseController::class, 'updateLicense'])->name('update-license');
    
    //report
    Route::post('/report', [ReportController::class, 'userReport'])->name('report');
    Route::post('/add-report', [ReportController::class, 'addReport'])->name('add-report');
    
    //role
    Route::post('/get-all-role', [RoleController::class, 'getRole'])->name('get-all-role');
    Route::post('/add-role', [RoleController::class, 'addRole'])->name('add-role');
    Route::post('/delete-role', [RoleController::class, 'deleteRole'])->name('delete-role');
    Route::post('/get-role-by-id', [RoleController::class, 'getRoleById'])->name('get-role-by-id');
    Route::post('/update-role', [RoleController::class, 'updateRoleById'])->name('update-role');
});
