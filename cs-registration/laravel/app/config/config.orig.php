<?php

/*
 * These configuration settings control which Club Speed server is communicated with for the application, along
 * with the necessary public API key and private key.
 *
 */
return array(

    'baseAPIURL' => '127.0.0.1/api/index.php',
    'apiKey' => 'cs-dev',
    'privateKey' => 'SHOULD_MATCH_API_CONFIG',

    'LocationID' => '1',
    'localized' => false,
    'minorSignatureWithParent' => true, //Whether or not both the minor and parent sign simultaneously
    'showPicture' => true, //Whether or not to allow a profile picture to be selected
    'assetsURL' => '/assets/cs-registration/',

    'defaultCountry' => 'United States',
    //'emailText' => 'Your custom e-mail checkbox text can go here. Anything here will overwrite the default.',
    'showTextingWaiver' => false,
    'textingWaiver' => 'By entering my phone number, I agree to receive text messages containing race status and other news. You can unsubscribe at any time.'

);
