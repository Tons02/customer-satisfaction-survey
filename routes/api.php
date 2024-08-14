<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\StoreNameController;
use App\Http\Controllers\Api\SurveyAnswerController;
use App\Http\Controllers\Api\QuestionnaireController;
use App\Http\Controllers\Api\QuestionAnswerController;
use App\Http\Controllers\Api\VoucherValidityController;
use App\Http\Controllers\Api\QuestionClassificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login',[AuthController::class,'login']);

//Survey client
Route::get('survey-answer-get-form-history-id/{id}/{security_code}/{entry_code}',[SurveyAnswerController::class,'getSurveyAnswer']);
Route::get('check-survey/{survey_id}/{security_code}',[SurveyAnswerController::class,'checkSurvey']);
Route::get('register-entry-code-checker/{mobile_number}/{entry_code}',[SurveyAnswerController::class,'checkEntryCode']);

Route::get("get-done-survey", [SurveyAnswerController::class, 'getPublicDoneSurvey']);

Route::post('create-survey',[SurveyAnswerController::class,'createSurvey']);
Route::patch('survey-answer-update-survey-answer/{id}',[SurveyAnswerController::class,'updateSurveyAnswer']);



// SMS Controller
Route::post('send-verification-code-reset-password', [SmsController::class, 'sendverificationcoderesetpassword'])->middleware('throttle:10,5'); 
Route::post('send-verification-code', [SmsController::class, 'sendverificationcode'])->middleware('throttle:10,5'); 
Route::post('validate-code', [SmsController::class, 'validatecode'])->middleware('throttle:10,5'); 

//forget password 
Route::patch('forgetpassword/{mobileNumber}',[AuthController::class,'forgetPassword']);

Route::group(["middleware" => ["auth:sanctum"]], function () {

    //Auth Controller
    Route::patch('changepassword',[AuthController::class,'changedPassword']);
    Route::post('logout',[AuthController::class,'logout']);
    Route::patch('resetpassword/{id}',[AuthController::class,'resetPassword']);

    //Role Controller
    Route::put('role-archived/{id}',[RoleController::class,'archived']);
    Route::resource("role", RoleController::class);

    //Users Controller
    Route::put('user-archived/{id}',[UserController::class,'archived']);
    Route::resource("users", UserController::class);

    //Questionnaire Controller
    Route::resource("questionnaire", QuestionnaireController::class);
    Route::put('questionnaire-archived/{id}',[QuestionnaireController::class,'archived']);

    //Survey Answers Controller
    Route::get("get-survey-answers", [SurveyAnswerController::class, 'getAllSurveyAnswer']);
    Route::patch("claiming-voucher/{id}", [SurveyAnswerController::class, 'claimingVoucher']);
    Route::patch("extend-voucher", [SurveyAnswerController::class, 'extendVoucher']);
    Route::put('survey-answer-archived/{id}',[SurveyAnswerController::class,'archived']);

    //Question Answers Controller
    Route::resource("question-answer", QuestionAnswerController::class);

    //Voucher Validity Controller
    Route::resource("voucher-validity", VoucherValidityController::class);
    Route::put('voucher-validity-archived/{id}',[VoucherValidityController::class,'archived']);

    
    //Province Controller
    Route::resource("province", ProvinceController::class);
    Route::put('province-archived/{id}',[ProvinceController::class,'archived']);

    
    //Store Name Controller
    Route::get("store-name-public-api", [StoreNameController::class, 'getAllStoreNames']);
    Route::resource("store-name", StoreNameController::class);
    Route::put('store-name-archived/{id}',[StoreNameController::class,'archived']);
    
});
