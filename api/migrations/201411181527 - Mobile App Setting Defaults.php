<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$trackInfoHtml = file_get_contents(__DIR__.'/resources/201411181527 - HTML01 - track info.html');
$menuItemsJson = file_get_contents(__DIR__.'/resources/201411181527 - Default menuItems.json');

/*
Font Awesome Icons: http://fortawesome.github.io/Font-Awesome/icons/
Font Awesome to PNG: http://fa2png.io/ (saved icons as 1024x1024 #fff)
*/
$settings = array(
    array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'trackInfoHtml',
        'Type'         => 'HTML',
        'DefaultValue' => $trackInfoHtml,
        'Value'        => $trackInfoHtml,
        'Description'  => 'The html template to be used for track info originating from the ClubSpeed PHP API',
				'IsPublic'     => true
    ),
		array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'menuItems',
        'Type'         => 'JSON',
        'DefaultValue' => $menuItemsJson,
        'Value'        => $menuItemsJson,
        'Description'  => 'The JSON Object containing the menu items shown on the Main Menu of the mobile application',
				'IsPublic'     => true
    ),
		array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'appId',
        'Type'         => 'String',
        'DefaultValue' => '',
        'Value'        => '',
        'Description'  => 'The JSON Object containing the menu items shown on the Main Menu of the mobile application',
				'IsPublic'     => true
    ),
		array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'appId',
        'Type'         => 'String',
        'DefaultValue' => '',
        'Value'        => '',
        'Description'  => 'The JSON Object containing the menu items shown on the Main Menu of the mobile application',
				'IsPublic'     => true
    ),
		array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'forceLogin',
        'Type'         => 'Boolean',
        'DefaultValue' => true,
        'Value'        => true,
        'Description'  => 'Force users to login to the application',
				'IsPublic'     => true
    ),
		array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'defaultApiKey',
        'Type'         => 'String',
        'DefaultValue' => '',
        'Value'        => '',
        'Description'  => 'Default API key used if we are not forcing users to login',
				'IsPublic'     => true
    ),
		array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'enableAccountCreation',
        'Type'         => 'Boolean',
        'DefaultValue' => true,
        'Value'        => true,
        'Description'  => 'Enable the ability to create an account',
				'IsPublic'     => true
    ),
		array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'enableFacebook',
        'Type'         => 'Boolean',
        'DefaultValue' => true,
        'Value'        => true,
        'Description'  => 'Enable Facebook login and other functionality',
				'IsPublic'     => true
    )
);

/* TODO Translation strings */

/* SETTINGS FOR ACCOUNT CREATION
// Waiver stuff, age restrictions?
'enablePictureTaking' => true,
'defaultCountry' => '',
'genderShown' => true,
'genderRequired' => true
'emailShown' => true,
'emailRequired' => true,
'passwordShown' => true,
'passwordRequired' => true,
'consentToMailShown' => true,
'consentToMailChecked' => false,
'consentToMailText' => '',
'companyShown' => true,
'companyRequired' => false,
'firstNameShown' => true,
'firstNameRequired' => true,
'lastNameShown' => true,
'lastNameRequired' => true,
'racerNameShown' => true,
'racerNameRequired' => false,
'birthDateShown' => true,
'birthDateRequired' => true,
'genderShown' => true,
'genderRequired' => true,
// TODO Call for Where did you hear about us list
'whereDidYouHearAboutUsShown' => true,
'whereDidYouHearAboutUsRequired' => false,
'addressShown' => true,
'addressRequired' => false,
'cityShown' => true,
'cityRequired' => false,
'stateShown' => true,
'stateRequired' => false,
'zipShown' => true,
'zipRequired' => false,
'countryShown' => true,
'countryRequired' => false,
// TODO Call for List of countries?
'cellShown' => true,
'cellRequired' => false,
'cellText' => '',
'licenseNumberShown' => true,
'licenseNumberRequired' => false,
// TODO Call for Custom label strings
'custom1Shown' => true,
'custom1Required' => false,
'custom2Shown' => true,
'custom2Required' => false,
'custom3Shown' => true,
'custom3Required' => false,
'custom4Shown' => true,
'custom4Required' => false,
*/

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

// COPY DEFAULT ICONS TO ASSETS
$src = 'resources/MobileApp/icons/';
$dst = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'MobileApp' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
$files = glob("resources/MobileApp/icons/*.png");

// Ensure the directory exists, if not, create it!
if(!is_dir($dst)) mkdir($dst, null, true);

// Copy each file
foreach($files as $file){
	$file_to_go = str_replace($src, $dst, $file);
	copy($file, $file_to_go);
}

// Fix permissions on Windows (works on 2003?). This is because by default the uplaoded imaged
// does not inherit permissions from the folder it is moved to. Instead, it retains the
// permissions of the temporary folder.
exec('c:\windows\system32\icacls.exe ' . $dst . ' /inheritance:e');

die('Successfully imported Mobile Application settings and defaults.');