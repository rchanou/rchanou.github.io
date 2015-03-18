<?php

/**
 * Class Strings
 *
 * This class holds the strings that are used throughout the registration website.
 * By default, English language strings are included.
 *
 * Ultimately, a global session variable 'strings' is assigned to the desired strings array.
 * That array is then used to populate the text throughout the website.
 */
class Strings {
    private static $defaultEnglish;
    private static $cultureNames;

    private function __construct() { }
    private static $initialized = false;

    public static function getDefaultEnglish()
    {
        self::initialize();
        return self::$defaultEnglish;
    }

    public static function getCultureNames()
    {
        self::initialize();
        return self::$cultureNames;
    }

    private static function initialize()
    {
        if (self::$initialized) return;

        //Club Speed requires a str prefix. _ was added for convenience.
        self::$defaultEnglish = array(
            'str_welcomeMessage' => 'Welcome to our track!',
            'str_registerHeader' => 'Register',
            'str_newAccount' => 'Create a new account',
            'str_checkIn' => 'Check in with an existing account',
            'str_checkInFinal' => 'Complete check-in',
            'str_facebook' => 'Create a new account with Facebook',
            'str_connectFacebook' => 'Connect your account to Facebook?',
            'str_connectFacebookYes' => 'Yes, connect my account to Facebook!',
            'str_connectFacebookNo' => 'Nah, let\'s just finish registration!',
            'str_cancel' => 'Cancel',
            'str_connectFacebookDisclaimer' => 'By logging into Facebook you are authorizing Club Speed, Inc. to post your personal Race Result information to Your Facebook Wall and you agree to release Club Speed, Inc. from any liability occurring from the posting of any and all Personal Internet Data.',
            'str_checkInFailure' => 'We were unable to locate your account with the details you provided.',
            'str_backButton' => 'Back',
            'str_step2Header' => 'Create an Account',
            'str_yourPicture' => 'Your Picture',
            'str_birthdate' => 'Birth Date',
            'str_mobilephone' => 'Mobile Phone',
            'str_howdidyouhearaboutus' => 'How did you hear about us?',
            'str_firstname' => 'First Name',
            'str_lastname' => 'Last Name',
            'str_racername' => 'Racer Name',
            'str_email' => 'Email Address',
            'str_emailsMessage' => 'We will email your results and special offers.<br/> We do not share your e-mail.',
            'str_emailsOptOut' => 'Do not email my results or special offers.',
            'str_emailsOptIn' => 'Please email me my results and special offers.',
            'str_step2Clear' => 'Clear',
            'str_step2Submit' => 'Submit',
            'str_checkInClear' => 'Clear',
            'str_checkInSubmit' => 'Submit',
            'str_step2SubmitCannot' => 'Too young to register',
            'str_step3Header' => 'Terms & Conditions',
            'str_step3DoNotAgree' => 'Do Not Agree (Start Over)',
            'str_step3Agree' => 'I Agree, Sign',
            'str_step3AgreeNoSig' => 'I Agree',
            'str_step3PleaseSign' => 'Please Sign Your Name',
            'str_step3Clear' => 'Clear',
            'str_step3Done' => 'Done',
            'str_registrationCompleteMessage' => 'Thanks for completing registration. <br/>We\'ll see you on the track!',
            'str_registrationComplete' => 'Registration Complete',
            'str_completeRegistration' => 'Back to Main Menu',
            'str_step1HeaderTitle' => ' ',
            'str_step1fbHeaderTitle' => 'Register with Facebook',
            'str_step2HeaderTitle' => 'Create an Account',
            'str_step3HeaderTitle' => 'Terms & Conditions',
            'str_step4HeaderTitle' => 'Registration Complete',
            'str_step1PageTitle' => 'Registration Kiosk - Step 1',
            'str_step2PageTitle' => 'Registration Kiosk - Step 2',
            'str_step3PageTitle' => 'Registration Kiosk - Step 3',
            'str_step4PageTitle' => 'Registration Kiosk - Step 4',
            'str_signHere' => 'Please Sign Your Name',
            'str_clearSignature' => 'Clear',
            'str_cancelSigning' => 'Cancel',
            'str_startSigning' => 'I Agree, Sign',
            'str_required' => 'Required',
            'str_mustBeAValidEmailAddress' => 'Must be a valid e-mail address',
            'str_poweredByClubSpeed' => 'Powered by Club Speed',
            'str_Male' => 'Male',
            'str_Female' => 'Female',
            'str_Other' => 'Other',
            'str_gender' => 'Gender',
            'str_switchToFacebookPic' => 'Switch back to Facebook profile picture',
            'str_emailText' => Config::has('config.emailText') ? Config::get('config.emailText') : 'By providing your email, you agree to receive periodic messages from ##TRACKNAME## notifying you of exclusive offers, special discounts, and the latest news on upcoming special events. You can withdraw your consent at any time.',
            'str_states' => 'State',
            'str_Address' => 'Address line 1',
            'str_Address2' => 'Address line 2',
            'str_Zip' => 'Postal Code',
            'str_countries' => 'Country',
            'str_city' => 'City',
            'str_Custom1' => 'Custom 1',
            'str_Custom2' => 'Custom 2',
            'str_Custom3' => 'Custom 3',
            'str_Custom4' => 'Custom 4',
            'str_imherefor' => 'I\'m here for',
            'str_walkIn' => 'Walk-in',
            'str_LicenseNumber' => 'License #',
            'str_termsAndConditionsCheckBox' => 'I have read and agree to the Terms and Conditions.',
            'str_buttonDisabledText' => 'Please check the box above to continue',
            'str_defaultSourceText' => 'Please choose one',
            'str_imageError' => 'There was a problem with your image. Please make sure the file size is not too large.',
            'str_State' => 'State',
            'str_Province/Territory' => 'Province/Territory',
            'str_State/Territory' => 'State/Territory',
            'str_Unable to Connect' => 'Unable to Connect',
            'str_disconnectedMessage' => 'Unable to connect to Club Speed. <br/>Please try again in a few minutes. <br/>If the issue persists, contact Club Speed support.',
            'str_howDidYouHearAboutUs_Missing' => 'Please select a \'How did you hear about us?\' option.',
            'str_emailAlreadyRegistered' => 'This e-mail address has already been registered.',
            'str_birthdate.required' => 'The birth date is required.',
            'str_birthdate.before' => 'The birth date must be in the past.',
            'str_birthdate.date' => 'The birth date must be a valid date.',
            'str_mobilephone.required' => 'Mobile phone is required.',
            'str_howdidyouhearaboutus.required' => 'How Did You Hear About Us is required.',
            'str_firstname.required' => 'First name is required.',
            'str_lastname.required' => 'Last name is required.',
            'str_racername.required' => 'Racer name is required.',
            'str_email.required' => 'E-mail address is required.',
            'str_email.email' => 'E-mail address must be valid.',
            'str_Address.required' => 'Address is required.',
            'str_Country.required' => 'Country is required.',
            'str_City.required' => 'City is required.',
            'str_State.required' => 'State is required.',
            'str_Zip.required' => 'Zip is required.',
            'str_Custom1.required' => 'This field is required',
            'str_Custom2.required' => 'This field is required',
            'str_Custom3.required' => 'This field is required',
            'str_Custom4.required' => 'This field is required',
            'str_LicenseNumber.required' => 'License # is required',
            'str_problemWithRegistration' => 'There was a problem submitting your information. Please try again.'
        );

        self::$cultureNames = array(
            'en-US' => 'English (US)',
            'en-GB' => 'English (UK)',
            'en-NZ' => 'English (NZ)',
            'en-AU' => 'English (AU)',
            'en-IE' => 'English (IE)',
            'en-CA' => 'English (CA)',
            'es-MX' => 'Español',
            'es-CR' => 'Español (CR)',
            'es-ES' => 'Castellano',
            'es-PR' => 'Español (PR)',
            'ru-RU' => 'Pусский язык',
            'fr-CA' => 'Français',
            'de-DE' => 'Deutsch',
            'nl-NL' => 'Nederlands',
            'pl-PL' => 'Język polski',
            'da-DK' => 'Dansk',
            'ar-AE' => 'العربية',
            'it-IT' => 'Italiano',
            'bg-BG' => 'български език',
            'sv-SE' => 'Svenska'
        );

        self::$initialized = true;
    }
}

