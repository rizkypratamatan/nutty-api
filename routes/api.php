<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactGroupController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DatabaseImportController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\WhatsappLogController;
use App\Http\Controllers\WorksheetController;
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

//whatsapp
Route::group(['prefix' => 'whatsapp'], function(){
    //testting only
    Route::post('test-send-single-chat', [WhatsappLogController::class, 'testSendSingleChat'])->name('test-send-single-chat');
    Route::post('test-send-bulk-chat', [WhatsappLogController::class, 'testSendBulkChat'])->name('test-send-bulk-chat');
    
});

//sms testing only
Route::group(['prefix' => 'sms'], function(){
    Route::post('send-test-bulk-message', [SmsLogController::class, 'sendTestBulkMessage'])->name('send-test-bulk-message');
    Route::post('send-test-single-message', [SmsLogController::class, 'sendTestSingleMessage'])->name('send-test-single-message');
});



Route::group(["middleware" => ["authentication"]], function() {
    //message-template
    Route::post('/get-all-template', [MessageTemplateController::class, 'index'])->name('get-all-template');
    Route::post('/add-template', [MessageTemplateController::class, 'store'])->name('add-template');
    Route::post('/delete-template', [MessageTemplateController::class, 'delete'])->name('delete-template');
    Route::post('/get-template-by-id', [MessageTemplateController::class, 'show'])->name('get-template-by-id');
    Route::post('/update-template', [MessageTemplateController::class, 'update'])->name('update-template');

    //contact group
    Route::post('/get-all-contact-group', [ContactGroupController::class, 'index'])->name('get-all-contact-group');
    Route::post('/add-contact-group', [ContactGroupController::class, 'store'])->name('add-contact-group');
    Route::post('/delete-contact-group', [ContactGroupController::class, 'delete'])->name('delete-contact-group');
    Route::post('/get-contact-group-by-id', [ContactGroupController::class, 'show'])->name('get-contact-group-by-id');
    Route::post('/update-contact-group', [ContactGroupController::class, 'update'])->name('update-contact-group');

    //contact
    Route::post('/get-all-contact', [ContactController::class, 'index'])->name('get-all-contact');
    Route::post('/add-contact', [ContactController::class, 'store'])->name('add-contact');
    Route::post('/delete-contact', [ContactController::class, 'delete'])->name('delete-contact');
    Route::post('/get-contact-by-id', [ContactController::class, 'show'])->name('get-contact-by-id');
    Route::post('/update-contact', [ContactController::class, 'update'])->name('update-contact');

    //worksheet
    // Route::post("/worksheet", [WorksheetController::class, "index"]);
    // Route::post("/worksheet/call", [WorksheetController::class, "call"]);
    // Route::post("/worksheet/result", [WorksheetController::class, "result"]);
    // Route::post("/worksheet/result-user", [WorksheetController::class, "resultUser"]);

    Route::post("/worksheet/crm", [WorksheetController::class, "getCrmData"]);

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
        Route::post('get-chats', [WhatsappLogController::class, 'getChats'])->name('get-chats');
        Route::post('delete-chat', [WhatsappLogController::class, 'deleteChat'])->name('delete-chat');
        Route::post('get-chat-by-id', [WhatsappLogController::class, 'getChatById'])->name('get-chat-by-id');
        Route::post('send-bulk-chat', [WhatsappLogController::class, 'sendBulkChat'])->name('send-bulk-chat');
        Route::post('send-single-chat', [WhatsappLogController::class, 'sendSingleChat'])->name('send-single-chat');
    });

    Route::group(['prefix' => 'sms'], function(){
        Route::post('get-messages', [SmsLogController::class, 'getMessages'])->name('get-messages');
        Route::post('delete-message', [SmsLogController::class, 'deleteMessage'])->name('delete-message');
        Route::post('get-message-by-id', [SmsLogController::class, 'getMessageById'])->name('get-message-by-id');
        Route::post('send-bulk-message', [SmsLogController::class, 'sendBulkMessage'])->name('send-bulk-message');
        Route::post('send-single-message', [SmsLogController::class, 'sendSingleMessage'])->name('send-single-message');
    });

    Route::group(['prefix' => 'email'], function(){
        Route::post('get-emails', [EmailLogController::class, 'getMessages'])->name('get-email');
        Route::post('delete-email', [EmailLogController::class, 'deleteMessage'])->name('delete-email');
        Route::post('get-email-by-id', [EmailLogController::class, 'getMessageById'])->name('get-email-by-id');
        Route::post('send-bulk-email', [EmailLogController::class, 'sendBulkMessage'])->name('send-bulk-email');
        Route::post('send-single-email', [EmailLogController::class, 'sendSingleMessage'])->name('send-single-email');
    });

    //database
    Route::post('/add-database', [DatabaseController::class, 'addDatabase'])->name('add-database');
    Route::post('/delete-database', [DatabaseController::class, 'deleteDatabase'])->name('delete-database');
    Route::post('/get-database', [DatabaseController::class, 'getDatabase'])->name('get-database');
    Route::post('/get-database-by-id', [DatabaseController::class, 'getDatabaseById'])->name('get-database-by-id');
    Route::post('/update-database', [DatabaseController::class, 'updateDatabaseById'])->name('update-database');

    // database import
    Route::post('/import-database', [DatabaseImportController::class, 'importDatabase'])->name('import-database');
    Route::post('/import-database-delete', [DatabaseImportController::class, 'historyDelete'])->name('import-history-delete');
    Route::post('/import-database-history', [DatabaseImportController::class, 'history'])->name('import-database-history');

    // register
    Route::post('/register', [RegisterController::class, 'addRegister'])->name('register');

});
