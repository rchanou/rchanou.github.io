<?php

/**
 * Class Settings
 *
 * This class holds the settings that are used throughout the registration website.
 * Default settings are included.
 *
 * Ultimately, a global session variable 'settings' is assigned to the desired settings array.
 * That array is then used to enforce custom settings throughout the website.
 */
class Settings {

    private static $defaultSettings;

    private function __construct() { }
    private static $initialized = false;

    public static function getDefaultSettings()
    {
        self::initialize();
        return self::$defaultSettings;
    }
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$defaultSettings = array(
            'Reg_EnableFacebook' => true,
            'Reg_CaptureProfilePic' => false, //Replaced showPicture config setting
            'showBirthDate' => true, //Always required
            'requireBirthDate' => true, //Always required
            'CfgRegPhoneShow' => true,
            'CfgRegPhoneReq' => false,
            'CfgRegSrcShow' => true, //How did you hear about us?
            'CfgRegSrcReq' => false,
            'showFirstName' => true, //Always required
            'requireFirstName' => true, //Always required
            'showLastName' => true, //Always required
            'requireLastName' => true, //Always required
            'CfgRegRcrNameShow' => true, //Always required
            'CfgRegRcrNameReq' => true, //Always required
            'CfgRegEmailShow' => true,
            'CfgRegEmailReq' => false,
            'dropdownOptions' => array('0' => '', '1' => 'Radio', '2' => 'Magazine', '3' => 'Billboard'),
            'AllowDuplicateEmail' => true,
            'enableWaiverStep' => true,
            'Waiver1' => '',
            'CfgRegAddShow' => true,
            'CfgRegAddReq' => false,
            'CfgRegCityShow' => true,
            'CfgRegCityReq' => false,
            'CfgRegStateShow' => true,
            'CfgRegStateReq' => false,
            'CfgRegZipShow' => true,
            'CfgRegZipReq' => false,
            'CfgRegCntryShow' => true,
            'CfgRegCntryReq' => false,
            'cfgRegCustTxt1Show' => false,
            'cfgRegCustTxt1Req' => false,
            'cfgRegCustTxt2Show' => false,
            'cfgRegCustTxt2Req' => false,
            'cfgRegCustTxt3Show' => false,
            'cfgRegCustTxt3Req' => false,
            'cfgRegCustTxt4Show' => false,
            'cfgRegCustTxt4Req' => false,
            'CfgRegValidateGrp' => false,
            'CfgRegDrvrLicShow' => false,
            'BusinessName' => '',
            'AgeNeedParentWaiver' => '18',

            'states' => array('' => '','Alaska' => 'Alaska',
                'Alabama' => 'Alabama',
                'Arkansas' => 'Arkansas',
                'American Samoa' => 'American Samoa',
                'Arizona' => 'Arizona',
                'California' => 'California',
                'Colorado' => 'Colorado',
                'Connecticut' => 'Connecticut',
                'District Of Columbia' => 'District Of Columbia',
                'Delaware' => 'Delaware',
                'Florida' => 'Florida',
                'Georgia' => 'Georgia',
                'Guam' => 'Guam',
                'Hawaii' => 'Hawaii',
                'Iowa' => 'Iowa',
                'Idaho' => 'Idaho',
                'Illinois' => 'Illinois',
                'Indiana' => 'Indiana',
                'Kansas' => 'Kansas',
                'Kentucky' => 'Kentucky',
                'Louisiana' => 'Louisiana',
                'Massachusetts' => 'Massachusetts',
                'Maryland' => 'Maryland',
                'Maine' => 'Maine',
                'Michigan' => 'Michigan',
                'Minnesota' => 'Minnesota',
                'Missouri' => 'Missouri',
                'Northern Mariana Islands' => 'Northern Mariana Islands',
                'Mississippi' => 'Mississippi',
                'Montana' => 'Montana',
                'North Carolina' => 'North Carolina',
                'North Dakota' => 'North Dakota',
                'Nebraska' => 'Nebraska',
                'New Hampshire' => 'New Hampshire',
                'New Jersey' => 'New Jersey',
                'New Mexico' => 'New Mexico',
                'Nevada' => 'Nevada',
                'New York' => 'New York',
                'Ohio' => 'Ohio',
                'Oklahoma' => 'Oklahoma',
                'Oregon' => 'Oregon',
                'Pennsylvania' => 'Pennsylvania',
                'Puerto Rico' => 'Puerto Rico',
                'Rhode Island' => 'Rhode Island',
                'South Carolina' => 'South Carolina',
                'South Dakota' => 'South Dakota',
                'Tennessee' => 'Tennessee',
                'Texas' => 'Texas',
                'Utah' => 'Utah',
                'Virginia' => 'Virginia',
                'Virgin Islands' => 'Virgin Islands',
                'Vermont' => 'Vermont',
                'Washington' => 'Washington',
                'Wisconsin' => 'Wisconsin',
                'West Virginia' => 'West Virginia',
                'Wyoming' => 'Wyoming',
                'Armed Forces Americas' => 'Armed Forces Americas',
                'Armed Forces Others' => 'Armed Forces Others',
                'Armed Forces Pacific' => 'Armed Forces Pacific'),

            'canadianProvinces' => array('' => '','Alberta' => 'Alberta',
                'British Columbia' => 'British Columbia',
                'Manitoba' => 'Manitoba',
                'New Brunswick' => 'New Brunswick',
                'Newfoundland and Labrador' => 'Newfoundland and Labrador',
                'Nova Scotia' => 'Nova Scotia',
                'Ontario' => 'Ontario',
                'Prince Edward Island' => 'Prince Edward Island',
                'Quebec' => 'Quebec',
                'Saskatchewan' => 'Saskatchewan',
                'Northwest Territories' => 'Northwest Territories',
                'Nunavut' => 'Nunavut',
                'Yukon' => 'Yukon'),

            'countries' => array('' => '','Afghanistan' => 'Afghanistan',
                'Åland Islands' => 'Åland Islands',
                'Albania' => 'Albania',
                'Algeria' => 'Algeria',
                'American Samoa' => 'American Samoa',
                'Andorra' => 'Andorra',
                'Angola' => 'Angola',
                'Anguilla' => 'Anguilla',
                'Antarctica' => 'Antarctica',
                'Antigua and Barbuda' => 'Antigua and Barbuda',
                'Argentina' => 'Argentina',
                'Armenia' => 'Armenia',
                'Aruba' => 'Aruba',
                'Australia' => 'Australia',
                'Austria' => 'Austria',
                'Azerbaijan' => 'Azerbaijan',
                'Bahamas' => 'Bahamas',
                'Bahrain' => 'Bahrain',
                'Bangladesh' => 'Bangladesh',
                'Barbados' => 'Barbados',
                'Belarus' => 'Belarus',
                'Belgium' => 'Belgium',
                'Belize' => 'Belize',
                'Benin' => 'Benin',
                'Bermuda' => 'Bermuda',
                'Bhutan' => 'Bhutan',
                'Bolivia, Plurinational State of' => 'Bolivia, Plurinational State of',
                'Bonaire, Sint Eustatius and Saba' => 'Bonaire, Sint Eustatius and Saba',
                'Bosnia and Herzegovina' => 'Bosnia and Herzegovina',
                'Botswana' => 'Botswana',
                'Bouvet Island' => 'Bouvet Island',
                'Brazil' => 'Brazil',
                'British Indian Ocean Territory' => 'British Indian Ocean Territory',
                'Brunei Darussalam' => 'Brunei Darussalam',
                'Bulgaria' => 'Bulgaria',
                'Burkina Faso' => 'Burkina Faso',
                'Burundi' => 'Burundi',
                'Cambodia' => 'Cambodia',
                'Cameroon' => 'Cameroon',
                'Canada' => 'Canada',
                'Cape Verde' => 'Cape Verde',
                'Cayman Islands' => 'Cayman Islands',
                'Central African Republic' => 'Central African Republic',
                'Chad' => 'Chad',
                'Chile' => 'Chile',
                'China' => 'China',
                'Christmas Island' => 'Christmas Island',
                'Cocos (Keeling) Islands' => 'Cocos (Keeling) Islands',
                'Colombia' => 'Colombia',
                'Comoros' => 'Comoros',
                'Congo' => 'Congo',
                'Congo, the Democratic Republic of the' => 'Congo, the Democratic Republic of the',
                'Cook Islands' => 'Cook Islands',
                'Costa Rica' => 'Costa Rica',
                'Côte d\'Ivoire' => 'Côte d\'Ivoire',
                'Croatia' => 'Croatia',
                'Cuba' => 'Cuba',
                'Curaçao' => 'Curaçao',
                'Cyprus' => 'Cyprus',
                'Czech Republic' => 'Czech Republic',
                'Denmark' => 'Denmark',
                'Djibouti' => 'Djibouti',
                'Dominica' => 'Dominica',
                'Dominican Republic' => 'Dominican Republic',
                'Ecuador' => 'Ecuador',
                'Egypt' => 'Egypt',
                'El Salvador' => 'El Salvador',
                'Equatorial Guinea' => 'Equatorial Guinea',
                'Eritrea' => 'Eritrea',
                'Estonia' => 'Estonia',
                'Ethiopia' => 'Ethiopia',
                'Falkland Islands (Malvinas)' => 'Falkland Islands (Malvinas)',
                'Faroe Islands' => 'Faroe Islands',
                'Fiji' => 'Fiji',
                'Finland' => 'Finland',
                'France' => 'France',
                'French Guiana' => 'French Guiana',
                'French Polynesia' => 'French Polynesia',
                'French Southern Territories' => 'French Southern Territories',
                'Gabon' => 'Gabon',
                'Gambia' => 'Gambia',
                'Georgia' => 'Georgia',
                'Germany' => 'Germany',
                'Ghana' => 'Ghana',
                'Gibraltar' => 'Gibraltar',
                'Greece' => 'Greece',
                'Greenland' => 'Greenland',
                'Grenada' => 'Grenada',
                'Guadeloupe' => 'Guadeloupe',
                'Guam' => 'Guam',
                'Guatemala' => 'Guatemala',
                'Guernsey' => 'Guernsey',
                'Guinea' => 'Guinea',
                'Guinea-Bissau' => 'Guinea-Bissau',
                'Guyana' => 'Guyana',
                'Haiti' => 'Haiti',
                'Heard Island and McDonald Islands' => 'Heard Island and McDonald Islands',
                'Holy See (Vatican City State)' => 'Holy See (Vatican City State)',
                'Honduras' => 'Honduras',
                'Hong Kong' => 'Hong Kong',
                'Hungary' => 'Hungary',
                'Iceland' => 'Iceland',
                'India' => 'India',
                'Indonesia' => 'Indonesia',
                'Iran, Islamic Republic of' => 'Iran, Islamic Republic of',
                'Iraq' => 'Iraq',
                'Ireland' => 'Ireland',
                'Isle of Man' => 'Isle of Man',
                'Israel' => 'Israel',
                'Italy' => 'Italy',
                'Jamaica' => 'Jamaica',
                'Japan' => 'Japan',
                'Jersey' => 'Jersey',
                'Jordan' => 'Jordan',
                'Kazakhstan' => 'Kazakhstan',
                'Kenya' => 'Kenya',
                'Kiribati' => 'Kiribati',
                'Korea, Democratic People\'s Republic of' => 'Korea, Democratic People\'s Republic of',
                'Korea, Republic of' => 'Korea, Republic of',
                'Kuwait' => 'Kuwait',
                'Kyrgyzstan' => 'Kyrgyzstan',
                'Lao People\'s Democratic Republic' => 'Lao People\'s Democratic Republic',
                'Latvia' => 'Latvia',
                'Lebanon' => 'Lebanon',
                'Lesotho' => 'Lesotho',
                'Liberia' => 'Liberia',
                'Libya' => 'Libya',
                'Liechtenstein' => 'Liechtenstein',
                'Lithuania' => 'Lithuania',
                'Luxembourg' => 'Luxembourg',
                'Macao' => 'Macao',
                'Macedonia, the former Yugoslav Republic of' => 'Macedonia, the former Yugoslav Republic of',
                'Madagascar' => 'Madagascar',
                'Malawi' => 'Malawi',
                'Malaysia' => 'Malaysia',
                'Maldives' => 'Maldives',
                'Mali' => 'Mali',
                'Malta' => 'Malta',
                'Marshall Islands' => 'Marshall Islands',
                'Martinique' => 'Martinique',
                'Mauritania' => 'Mauritania',
                'Mauritius' => 'Mauritius',
                'Mayotte' => 'Mayotte',
                'Mexico' => 'Mexico',
                'Micronesia, Federated States of' => 'Micronesia, Federated States of',
                'Moldova, Republic of' => 'Moldova, Republic of',
                'Monaco' => 'Monaco',
                'Mongolia' => 'Mongolia',
                'Montenegro' => 'Montenegro',
                'Montserrat' => 'Montserrat',
                'Morocco' => 'Morocco',
                'Mozambique' => 'Mozambique',
                'Myanmar' => 'Myanmar',
                'Namibia' => 'Namibia',
                'Nauru' => 'Nauru',
                'Nepal' => 'Nepal',
                'Netherlands' => 'Netherlands',
                'New Caledonia' => 'New Caledonia',
                'New Zealand' => 'New Zealand',
                'Nicaragua' => 'Nicaragua',
                'Niger' => 'Niger',
                'Nigeria' => 'Nigeria',
                'Niue' => 'Niue',
                'Norfolk Island' => 'Norfolk Island',
                'Northern Mariana Islands' => 'Northern Mariana Islands',
                'Norway' => 'Norway',
                'Oman' => 'Oman',
                'Pakistan' => 'Pakistan',
                'Palau' => 'Palau',
                'Palestinian Territory, Occupied' => 'Palestinian Territory, Occupied',
                'Panama' => 'Panama',
                'Papua New Guinea' => 'Papua New Guinea',
                'Paraguay' => 'Paraguay',
                'Peru' => 'Peru',
                'Philippines' => 'Philippines',
                'Pitcairn' => 'Pitcairn',
                'Poland' => 'Poland',
                'Portugal' => 'Portugal',
                'Puerto Rico' => 'Puerto Rico',
                'Qatar' => 'Qatar',
                'Réunion' => 'Réunion',
                'Romania' => 'Romania',
                'Russian Federation' => 'Russian Federation',
                'Rwanda' => 'Rwanda',
                'Saint Barthélemy' => 'Saint Barthélemy',
                'Saint Helena, Ascension and Tristan da Cunha' => 'Saint Helena, Ascension and Tristan da Cunha',
                'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
                'Saint Lucia' => 'Saint Lucia',
                'Saint Martin (French part)' => 'Saint Martin (French part)',
                'Saint Pierre and Miquelon' => 'Saint Pierre and Miquelon',
                'Saint Vincent and the Grenadines' => 'Saint Vincent and the Grenadines',
                'Samoa' => 'Samoa',
                'San Marino' => 'San Marino',
                'Sao Tome and Principe' => 'Sao Tome and Principe',
                'Saudi Arabia' => 'Saudi Arabia',
                'Senegal' => 'Senegal',
                'Serbia' => 'Serbia',
                'Seychelles' => 'Seychelles',
                'Sierra Leone' => 'Sierra Leone',
                'Singapore' => 'Singapore',
                'Sint Maarten (Dutch part)' => 'Sint Maarten (Dutch part)',
                'Slovakia' => 'Slovakia',
                'Slovenia' => 'Slovenia',
                'Solomon Islands' => 'Solomon Islands',
                'Somalia' => 'Somalia',
                'South Africa' => 'South Africa',
                'South Georgia and the South Sandwich Islands' => 'South Georgia and the South Sandwich Islands',
                'South Sudan' => 'South Sudan',
                'Spain' => 'Spain',
                'Sri Lanka' => 'Sri Lanka',
                'Sudan' => 'Sudan',
                'Suriname' => 'Suriname',
                'Svalbard and Jan Mayen' => 'Svalbard and Jan Mayen',
                'Swaziland' => 'Swaziland',
                'Sweden' => 'Sweden',
                'Switzerland' => 'Switzerland',
                'Syrian Arab Republic' => 'Syrian Arab Republic',
                'Taiwan, Province of China' => 'Taiwan, Province of China',
                'Tajikistan' => 'Tajikistan',
                'Tanzania, United Republic of' => 'Tanzania, United Republic of',
                'Thailand' => 'Thailand',
                'Timor-Leste' => 'Timor-Leste',
                'Togo' => 'Togo',
                'Tokelau' => 'Tokelau',
                'Tonga' => 'Tonga',
                'Trinidad and Tobago' => 'Trinidad and Tobago',
                'Tunisia' => 'Tunisia',
                'Turkey' => 'Turkey',
                'Turkmenistan' => 'Turkmenistan',
                'Turks and Caicos Islands' => 'Turks and Caicos Islands',
                'Tuvalu' => 'Tuvalu',
                'Uganda' => 'Uganda',
                'Ukraine' => 'Ukraine',
                'United Arab Emirates' => 'United Arab Emirates',
                'United Kingdom' => 'United Kingdom',
                'United States' => 'United States',
                'United States Minor Outlying Islands' => 'United States Minor Outlying Islands',
                'Uruguay' => 'Uruguay',
                'Uzbekistan' => 'Uzbekistan',
                'Vanuatu' => 'Vanuatu',
                'Venezuela, Bolivarian Republic of' => 'Venezuela, Bolivarian Republic of',
                'Viet Nam' => 'Viet Nam',
                'Virgin Islands, British' => 'Virgin Islands, British',
                'Virgin Islands, U.S.' => 'Virgin Islands, U.S.',
                'Wallis and Futuna' => 'Wallis and Futuna',
                'Western Sahara' => 'Western Sahara',
                'Yemen' => 'Yemen',
                'Zambia' => 'Zambia',
                'Zimbabwe' => 'Zimbabwe')
            //TODO: Add and handle unique e-mail option.
        );

        /* //TODO: Analysis of current settings and how they fit into the current app
         {
           "CfgRegType":false, //TODO: Unknown. Unused.
           "CfgRegAddReq":true, //TODO: USED!
           "CfgRegAddShow":true, //TODO: USED!
           "CfgRegCityReq":true, //TODO: Unused.
           "CfgRegCityShow":true, //TODO: Unused.
           "CfgRegCntryReq":true, //TODO: Unused.
           "CfgRegCntryShow":true, //TODO: Unused.
           "CfgRegDrvrLicReq":false, //TODO: Unused.
           "CfgRegDrvrLicShow":false, //TODO: Unused.
           "CfgRegEmailShow":true, //TODO: USED!
           "CfgRegEmailReq":false, //TODO: USED!
           "CfgRegHotelReq":false, //TODO: Unused.
           "CfgRegHotelShow":false, //TODO: Unused.
           "CfgRegPhoneReq":true, //TODO: USED!
           "CfgRegPhoneShow":true, //TODO: USED!
           "CfgRegRcrNameShow":true, //TODO: USED!
           "CfgRegRcrNameReq":true, //TODO: USED!
           "CfgRegSrcReq":false, //TODO: How did you hear about us? USED!
           "CfgRegSrcShow":true, //TODO: How did you hear about us? USED!
           "CfgRegStateReq":false, //TODO: Unused.
           "CfgRegStateShow":false, //TODO: Unused.
           "CfgRegZipReq":true, //TODO: Unused.
           "CfgRegZipShow":true, //TODO: Unused.
           "CfgRegWaiverTrmsInstrcns":"Read Waiver and Accept Terms at the Bottom of the Page", //TODO: This isn't a setting! Someone cheated!
           "CfgRegPrntWaiver":true, //TODO: Unused.
           "CfgRegUseEsign":false, //TODO: ?
           "CfgRegUseMsign":false, //TODO: ?
           "CfgRegValidateGrp":true, //TODO: ?
           "CfgRegWaiverPrntrName":"", //TODO: Okay, I can consider this a setting.
           "CfgRegWlcmeTxt":"Welcome...", //TODO: This isn't a setting, you cheaters!
           "CfgRegDisblEmlForMinr":false, //TODO: Unused.
           "cfgRegCustTxt1req":false, //TODO: USED!
           "cfgRegCustTxt1Show":false, //TODO: USED!
           "cfgRegCustTxt2req":false, //TODO: USED!
           "cfgRegCustTxt2Show":false, //TODO: USED!
           "cfgRegCustTxt3req":false, //TODO: USED!
           "cfgRegCustTxt3Show":false, //TODO: USED!
           "cfgRegCustTxt4req":false, //TODO: USED!
           "cfgRegCustTxt4Show":false, //TODO: USED!
           "cfgRegAllowMinorToSign":false,
           "cfgRegShowBeenHereBefr":true //TODO: Unused. Need to implement?
         }
         */

        self::$initialized = true;
    }
} 