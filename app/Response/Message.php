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
    const REGISTRATION_SUCCESSFULLY = "Survey saved successfully";
    const SURVEY_FORM_INVALID = "No Survey form has been set. Please contact the administrator.";
    const SURVEY_VALIDITY_INVALID = "No Survey duration has been set. Please contact the administrator.";
    const ENTRY_CODE_NOT_DONE = "Survey Answer not done";
    const RECEIPT_NUMBER_AVAILABLE = "Receipt available";
    const RECEIPT_NUMBER_ALREADY_USED = "Receipt already used";
    const RECEIPT_NUMBER_NOT_DONE = "Survey Answer not done";
    const RECEIPT_NUMBER_ALREADY_CLAIMED = "Entry code already claimed";
    const INVALID_CREDENTIALS = "Invalid credentials for this number";
    const EXIST_CREDENTIALS = "The Firstname, Lastname and Birthday is already used.";
    const INVALID_MOBILE_NUMBER = "The mobile number you entered does not match any records. Please double-check the number and try again.";
    const INVALID_RECEIPT_NUMBER = "The receipt number you provided does not match any records for the specified mobile number.";
    const INVALID_STORE = "Invalid store name for the specified mobile number and receipt number.";
    const EXIST_NUMBER = "The number is already associated with another voucher.";
    const RECEIPT_EXPIRED = "The claiming duration for this receipt has passed.";

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
    const PROVINCE_ID_INVALID = "The selected store linked to an inactive province. Please contact support";

    //TRIGGER SET UP OPERATIONS
    const TRIGGER_SAVE = "Trigger Set Up successfully save.";
    const TRIGGER_UPDATE = "Trigger Set Up successfully updated.";
    const TRIGGER_DISPLAY = "Trigger Set Up display successfully.";
    const TRIGGER_ALREADY_USE = "Unable to Archive, rigger Set Up already in used!";

    //RECEIPT NUMBER OPERATIONS
    const RECEIPT_NUMBER_SAVE = "Receipt Numbers successfully save.";
    const RECEIPT_NUMBER_UPDATE = "Receipt Numbers successfully updated.";
    const RECEIPT_NUMBER_DISPLAY = "Receipt Numbers display successfully.";
    const RECEIPT_NUMBER_LIMIT = "Receipt limit exceeded for this store. No further receipts can be accepted.";
    const TRIGGER_INVALID = "No trigger point has been set. Please contact the administrator.";
    const SURVEY_INTERVAL_INVALID = "No Survey Interval has been set. Please contact the administrator.";
    const SURVEY_PERIOD_INVALID = "No Survey Period has been set. Please contact the administrator.";
    const SURVEY_PERIOD_DONE = "The survey period has already ended.";
    const SURVEY_NEW_PERIOD_INVALID = "The new survey form's valid from date must be equal to or later than today's date.";
    const STORE_ID_INVALID = "Your account is linked to an closed store. Please contact support";
    const NO_STORE_ID = "Your account is not linked to any store. Please contact support.";

    //SURVEY_INTERVAL OPERATIONS
    const SURVEY_INTERVAL_SAVE = "Survey interval successfully save.";
    const SURVEY_INTERVAL_UPDATE = "Survey interval successfully updated.";
    const SURVEY_INTERVAL_DISPLAY = "Survey interval display successfully.";

     //SURVEY_PERIOD OPERATIONS
     const SURVEY_PERIOD_SAVE = "Survey period successfully save.";
     const SURVEY_PERIOD_UPDATE = "Survey period successfully updated.";
     const SURVEY_PERIOD_DISPLAY = "Survey period display successfully.";
     const SURVEY_PERIOD_ALREADY_USE = "Unable to Archive, Survey period already in used!";
     const SURVEY_PERIOD_ALREADY_CLOSED = "Survey period already closed";

    //CHART MESSAGE
    const CHART_FOR_AGE = "Chart data display successfully.";

    //GLOBAL MESSAGE
    const NO_CHANGES = "No Changes";
    const INVALID_STATUS = "Invalid Status";
    const INVALID_ID = "Invalid ID";
    const ALREADY_EXIST = "Data already exists. Please update your existing data.";
    const NOT_FOUND = "No Data Found";
    const INVALID_ACTION = "Invalid action.";
    const ARCHIVE_STATUS = "Successfully archived.";
    const RESTORE_STATUS = "Successfully restore.";

}

?>
