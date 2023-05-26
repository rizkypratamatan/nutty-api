<?php

use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DatabaseImportController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\WhatsappController;
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
    //worksheet
    Route::post("/worksheet", [WorksheetController::class, "index"]);
    Route::post("/worksheet/call", [WorksheetController::class, "call"]);
    Route::post("/worksheet/result", [WorksheetController::class, "result"]);
    Route::post("/worksheet/result-user", [WorksheetController::class, "resultUser"]);

    //website
    Route::post('/get-websites', [WebsiteController::class, 'getWebsites'])->name('get-websites');
    Route::post('/add-website', [WebsiteController::class, 'addWebsite'])->name('add-website');
    Route::post('/delete-website', [WebsiteController::class, 'deleteWebsite'])->name('delete-website');
    Route::post('/get-website-by-id', [WebsiteController::class, 'getWebsiteById'])->name('get-website-by-id');
    Route::post('/update-website', [WebsiteController::class, 'updateWebsiteById'])->name('update-website');

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
    Route::post('/add-license', [LicenseController::class, 'addLicense'])->name('add-license');
    Route::post('/get-license', [LicenseController::class, 'getLicense'])->name('get-license');
    Route::post('/get-license-by-id', [LicenseController::class, 'getLicenseById'])->name('get-license-by-id');
    
    //report
    Route::post('/report', [ReportController::class, 'userReport'])->name('report');
    Route::post('/add-report', [ReportController::class, 'addReport'])->name('add-report');
    Route::post('/delete-report', [ReportController::class, 'deleteReport'])->name('delete-report');
    Route::post('/update-report', [ReportController::class, 'updateReport'])->name('update-report');
    Route::post('/get-report-by-id', [ReportController::class, 'getReportById'])->name('get-report-by-id');
    
    //role
    Route::post('/get-all-role', [RoleController::class, 'getRole'])->name('get-all-role');
    Route::post('/add-role', [RoleController::class, 'addRole'])->name('add-role');
    Route::post('/delete-role', [RoleController::class, 'deleteRole'])->name('delete-role');
    Route::post('/get-role-by-id', [RoleController::class, 'getRoleById'])->name('get-role-by-id');
    Route::post('/update-role', [RoleController::class, 'updateRoleById'])->name('update-role');

    //whatsapp
    Route::group(['prefix' => 'whatsapp'], function(){
        Route::post('get-chats', [WhatsappController::class, 'getChats'])->name('get-chats');
        Route::post('delete-chat', [WhatsappController::class, 'deleteChat'])->name('delete-chat');
        Route::post('get-chat-by-id', [WhatsappController::class, 'getChatById'])->name('get-chat-by-id');
        Route::post('send-bulk-chat', [WhatsappController::class, 'sendBulkChat'])->name('send-bulk-chat');
        Route::post('send-single-chat', [WhatsappController::class, 'sendSingleChat'])->name('send-single-chat');

        
    });

    Route::group(['prefix' => 'sms'], function(){
        Route::post('get-messages', [SmsController::class, 'getMessages'])->name('get-messages');
        Route::post('delete-message', [SmsController::class, 'deleteMessage'])->name('delete-message');
        Route::post('get-message-by-id', [SmsController::class, 'getMessageById'])->name('get-message-by-id');
        Route::post('send-bulk-message', [SmsController::class, 'sendBulkMessage'])->name('send-bulk-message');
        Route::post('send-single-message', [SmsController::class, 'sendSingleMessage'])->name('send-single-message');
    });

    //database
    Route::post('/add-database', [DatabaseController::class, 'addDatabase'])->name('add-database');
    Route::post('/delete-database', [DatabaseController::class, 'deleteDatabase'])->name('delete-database');
    Route::post('/get-database', [DatabaseController::class, 'getDatabase'])->name('get-database');
    Route::post('/get-database-by-id', [DatabaseController::class, 'getDatabaseById'])->name('get-database-by-id');
    Route::post('/update-database', [DatabaseController::class, 'updateDatabaseById'])->name('update-database');

    // database import
    Route::post('/import-database', [DatabaseImportController::class, 'importDatabase'])->name('import-database');
    Route::post('/import-initialize-data', [DatabaseImportController::class, 'initializeData'])->name('import-initialize-data');

});
