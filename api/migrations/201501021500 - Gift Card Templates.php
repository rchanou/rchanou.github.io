<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums;
Log::info('Running ' . basename(__FILE__, '.php') . ' migrations', Enums::NSP_MIGRATIONS);

$giftCardHtml = file_get_contents(__DIR__.'/resources/201501021500 - HTML01 - gift card.html');
$giftCardHtml = str_replace('##TRACKURL##',$_SERVER['HTTP_HOST'],$giftCardHtml);

$settings = array(
    array(
      'Namespace'    => 'Booking',
      'Name'         => 'giftCardEmailBodyHtml',
      'Type'         => 'HTML',
      'DefaultValue' => $giftCardHtml,
      'Value'        => $giftCardHtml,
      'Description'  => 'The HTML template to be used for gift card emails originating from the ClubSpeed PHP API'
    ),
    array(
      'Namespace'    => 'Booking',
      'Name'         => 'giftCardEmailSubject',
      'Type'         => 'String',
      'DefaultValue' => '{{business}} Gift Card',
      'Value'        => '{{business}} Gift Card',
      'Description'  => ''
    )
);

foreach($settings as $setting) {
    try {
        $existing = $db->settings->match(array(
            'Namespace' => $setting['Namespace']
            , 'Name'    => $setting['Name']
        ));
        if (empty($existing))
        {
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

// COPY DEFAULT IMAGES TO ASSETS
$src = 'resources/templates/giftcardemails/';
$dst = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'giftcardemails' . DIRECTORY_SEPARATOR;
$files = glob("resources/templates/giftcardemails/*.*");

// Ensure the directory exists, if not, create it!
if(!is_dir($dst)) mkdir($dst, null, true);

// Copy each file
foreach($files as $file){
    $file_to_go = str_replace($src, $dst, $file);
    copy($file, $file_to_go);
}

// Fix permissions on Windows (works on 2003?). This is because by default the uploaded imaged
// does not inherit permissions from the folder it is moved to. Instead, it retains the
// permissions of the temporary folder.
exec('c:\windows\system32\icacls.exe ' . $dst . ' /inheritance:e');

die('Successfully imported receipt email templates.');
