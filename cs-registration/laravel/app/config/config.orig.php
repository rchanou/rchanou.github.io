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
    'assetsURL' => 'http://127.0.0.1/assets/cs-registration/',

);
