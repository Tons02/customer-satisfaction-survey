<?php
namespace App\Response;

class Message
{
    //STATUS CODES
    const CREATED_STATUS = 201;
    const UNPROCESS_STATUS = 422;
    const DATA_NOT_FOUND = 404;
    const SUCESS_STATUS = 200;
    const DENIED_STATUS = 403;
    const CUT_OFF_STATUS = 409;

    //AUTH OPERATIONS
    const LOGIN_USER = "Sucessfully login.";
    const LOGIN_FAILED = "The provided credentials are incorrect.";
    const RESET_PASSWORD = "The Password has been reset";
    const CHANGE_PASSWORD = "Password change successfully";
    const LOGOUT_USER = "You are successfully logged out.";
    

    //USER OPERATIONS
    const USER_SAVE = "User successfully save.";
    const USER_UPDATE = "User successfully updated.";
    const USER_DISPLAY = "User display successfully.";
    
    //ROLE OPERATIONS
    const ROLE_SAVE = "Role successfully save.";
    const ROLE_UPDATE = "Role successfully updated.";
    const ROLE_DISPLAY = "Role display successfully.";
    const ROLE_ALREADY_USE = "Unable to Archive, Role already in used!";

    //QUESTIONNAIRE OPERATIONS
    const QUESTIONNAIRE_SAVE = "Questionnaire successfully save.";
    const QUESTIONNAIRE_UPDATE = "Questionnaire successfully updated.";
    const QUESTIONNAIRE_DISPLAY = "Questionnaire display successfully.";
    const QUESTIONNAIRE_ALREADY_USE = "Unable to Archive, Questionnaire already in used!";

    //SURVEY ANSWER OPERATIONS
    const SURVEY_ANSWER_SAVE = "Survey Answer successfully save.";
    const SURVEY_ANSWER_UPDATE = "Survey Answer successfully updated.";
    const SURVEY_ANSWER_DISPLAY = "Survey Answer display successfully.";
    const SURVEY_ANSWER_ALREADY_USE = "Unable to Archive, Survey Answer already in used!";
    const SURVEY_ANSWER_NOT_DONE = "Survey answer not completed.";
    const SURVEY_ANSWER_ALREADY_DONE = "Survey Answer already done!";

    //Voucher
    const VOUCHER_CLAIM_SUCCESSFULLY = "Voucher successfully claimed";


    //QUESTION ANSWER FOR CHARTS
    const QUESTION_ANSWER_DISPLAY = "Question answers display successfully";

    //CHECKING SURVEY
    const CHECK_SURVEY_VALID = "Continue to survey";
    const CHECK_SURVEY_INVALID = "Survey not exist";


    //REGISTER FORM
    const REGISTRATION_SUCCESSFULLY = "Registration successfully";
    const ENTRY_CODE_AVAILABLE = "Entry code available";
    const ENTRY_CODE_NOT_DONE = "Survey Answer not done";
    const ENTRY_CODE_ALREADY_CLAIMED = "Entry code already claimed";
    const INVALID_CREDENTIALS = "Invalid credentials for this number";
    const EXIST_CREDENTIALS = "The Firstname, Lastname and Birthday is already used.";
    const INVALID_RECEIPT_NUMBER = "Invalid receipt number.";
    const EXIST_NUMBER = "The number is already associated with another voucher.";


    //SMS OPERATIONS
    const SMS_OTP_SAVE = "OTP sent successfully";
    const SMS_OTP_UPDATE = "Survey Answer successfully updated.";
    const SMS_OTP_DISPLAY = "Survey Answer display successfully.";

    //VOUCHER VALIDITY OPERATIONS
    const VOUCHER_VALIDITY_SAVE = "Voucher Validity successfully save.";
    const VOUCHER_VALIDITY_UPDATE = "Voucher Validity successfully updated.";
    const VOUCHER_VALIDITY_DISPLAY = "Voucher Validity display successfully.";
    const VOUCHER_VALIDITY_ALREADY_USE = "Unable to Archive, Voucher Validity already in used!";
    const VOUCHER_VALIDITY_EXTEND = "Voucher validity successfully extended";

    //PROVINCE OPERATIONS
    const PROVINCE_SAVE = "Province successfully save.";
    const PROVINCE_UPDATE = "Province successfully updated.";
    const PROVINCE_DISPLAY = "Province display successfully.";
    const PROVINCE_ALREADY_USE = "Unable to Archive, Province already in used!";

    //STORE NAME OPERATIONS
    const STORE_NAME_SAVE = "Store Name successfully save.";
    const STORE_NAME_UPDATE = "Store Name successfully updated.";
    const STORE_NAME_DISPLAY = "Store Name display successfully.";
    const STORE_NAME_ALREADY_USE = "Unable to Archive, Store Name already in used!";

    //CHART MESSAGE
    const CHART_FOR_AGE = "Chart data display successfully.";

    //GLOBAL MESSAGE
    const NO_CHANGES = "No Changes";
    const INVALID_STATUS = "Invalid Status";
    const INVALID_ID = "Invalid ID";
    const NOT_FOUND = "No Data Found";
    const INVALID_ACTION = "Invalid action.";
    const ARCHIVE_STATUS = "Successfully archived.";
    const RESTORE_STATUS = "Successfully restore.";
    
}

?>