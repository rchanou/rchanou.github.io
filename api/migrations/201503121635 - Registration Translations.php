<?php

require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$translationsSplitByCulture = array(
    "en-US" => array(
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
        'str_emailText' => 'By providing your email, you agree to receive periodic messages from ##TRACKNAME## notifying you of exclusive offers, special discounts, and the latest news on upcoming special events. You can withdraw your consent at any time.',
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
    )
);

$translations = array(); //Flattening and formatting array for processing
foreach($translationsSplitByCulture as $culture => $translationsForCulture)
{
    foreach($translationsForCulture as $key => $translation)
    {
        $translations[] = array(
            "namespace" => "Registration",
            "name" => $key,
            "culture" => $culture,
            "defaultValue" => $translation,
            "value" => $translation,
            "description" => null
        );
    }
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt = $conn->prepare("INSERT INTO Translations (Namespace, Name, Culture, DefaultValue, Value, Description, Created) VALUES (:Namespace, :Name, :Culture, :DefaultValue, :Value, :Description, GETDATE())");

foreach($translations as $translation) {
	$Namespace = 'Registration';
    $Name = $translation['name'];
    $Culture = $translation['culture'];
    $DefaultValue = $translation['defaultValue'];
    $Value = $translation['value'];
    $Description = $translation['description'];
	
	$sth = $conn->prepare("SELECT * FROM dbo.Translations WHERE Namespace = :Namespace AND Name = :Name AND Culture = :Culture");
	$sth->bindParam(':Namespace', $Namespace);
	$sth->bindParam(':Name', $Name);
    $sth->bindParam(':Culture', $Culture);
	$sth->execute();
	$existingEntry = $sth->fetchAll();
	
	// If it doesn't exist, insert it
	if(count($existingEntry) === 0) {
		$stmt->bindParam(':Namespace', $Namespace);
		$stmt->bindParam(':Name', $Name);
		$stmt->bindParam(':Culture', $Culture);
		$stmt->bindParam(':DefaultValue', $DefaultValue);
		$stmt->bindParam(':Value', $Value);
		$stmt->bindParam(':Description', $Description);
		$stmt->execute();
		echo "Inserting " . $Name . "<br/>";
	} else {
		echo "Did not create " . $Name . " (" . $Culture . ") - (already exists)<br/>";
	}

}

$settings = array(
    array(
        'Namespace'    => 'Registration',
        'Name'         => 'currentCulture',
        'Type'         => 'String',
        'DefaultValue' => "en-US",
        'Value'        => "en-US",
        'Description'  => 'The current culture for Registration',
        'IsPublic'     => true
    )
);


foreach($settings as $setting) {
    try {
        $existing = $db->settings->match(array(
            'Namespace' => $setting['Namespace']
        , 'Name'    => $setting['Name']
        ));
        if (empty($existing)) {
            $db->settings->create($setting);
            echo 'Setting (' . $setting['Namespace'] . ', ' . $setting['Name']  . ') successfully imported!';
            echo '<br>';
        }
        else {
            echo 'Setting (' . $setting['Namespace'] . ', ' . $setting['Name'] . ') already exists!';
            echo '<br>';
        }
    }
    catch (Exception $e) {
        echo 'Unable to import setting (' . $setting['Namespace'] . ', ' . $setting['Name'] . ')! ' . $e->getMessage();
        echo '<br>';
    }
}

echo '<p/>';

// Confirm success
die('Successfully imported Registration translations and settings.');