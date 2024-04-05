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

    //USER OPERATIONS
    const USER_SAVE = "User successfully save.";
    const LOGIN_USER = "Sucessfully login.";
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

    //SMS OPERATIONS
    const SMS_OTP_SAVE = "OTP sent successfully";
    const SMS_OTP_UPDATE = "Survey Answer successfully updated.";
    const SMS_OTP_DISPLAY = "Survey Answer display successfully.";

    //VOUCHER VALIDITY OPERATIONS
    const VOUCHER_VALIDITY_SAVE = "Voucher Validity successfully save.";
    const VOUCHER_VALIDITY_UPDATE = "Voucher Validity successfully updated.";
    const VOUCHER_VALIDITY_DISPLAY = "Voucher Validity display successfully.";
    const VOUCHER_VALIDITY_ALREADY_USE = "Unable to Archive, Voucher Validity already in used!";
    

    //GLOBAL MESSAGE
    const NO_CHANGES = "No Changes";
    const INVALID_STATUS = "Invalid Status";
    const INVALID_ID = "Invalid ID";
    const NOT_FOUND = "No Data Found";
    const INVALID_ACTION = "Invalid action.";
    const ARCHIVE_STATUS = "Successfully archived.";
    const RESTORE_STATUS = "Successfully restore.";
    const LOGOUT_USER = "Logout Successfully";
    
}

?>