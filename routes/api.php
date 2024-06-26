<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\SurveyAnswerController;
use App\Http\Controllers\Api\QuestionnaireController;
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
Route::post('create-survey',[SurveyAnswerController::class,'createSurvey']);
Route::patch('survey-answer-update-survey-answer/{id}',[SurveyAnswerController::class,'updateSurveyAnswer']);



// SMS Controller
Route::post('send-verification-code-reset-password', [SmsController::class, 'sendverificationcoderesetpassword'])->middleware('throttle:100,1'); 
Route::post('send-verification-code', [SmsController::class, 'sendverificationcode'])->middleware('throttle:100,1'); 
Route::post('validate-code', [SmsController::class, 'validatecode'])->middleware('throttle:100,1'); 

//forget password 
Route::patch('forgetpassword/{mobileNumber}',[AuthController::class,'forgetPassword']);



Route::group(["middleware" => ["auth:sanctum"]], function () {

    //Auth Controller
    Route::patch('changepassword',[AuthController::class,'changedPassword']);
    Route::post('logout',[AuthController::class,'logout']);
    Route::patch('resetpassword/{id}',[AuthController::class,'resetPassword']);

    //Role Controller
    Route::resource("role", RoleController::class);
    Route::put('role-archived/{id}',[RoleController::class,'archived']);

    //Users Controller
    Route::resource("users", UserController::class);
    Route::put('user-archived/{id}',[UserController::class,'archived']);

    //Questionnaire Controller
    Route::resource("questionnaire", QuestionnaireController::class);
    Route::put('questionnaire-archived/{id}',[QuestionnaireController::class,'archived']);

    //Survey Answers Controller
    
    Route::resource("survey-answer", SurveyAnswerController::class);
    Route::put('survey-answer-archived/{id}',[SurveyAnswerController::class,'archived']);

    //Voucher Validity Controller
    Route::resource("voucher-validity", VoucherValidityController::class);
    Route::put('voucher-validity-archived/{id}',[VoucherValidityController::class,'archived']);
    
});
