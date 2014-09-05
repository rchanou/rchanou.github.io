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

            'countries' => array('' => '','Afghanistan' => 'Afghanestan',
                'Albania' => 'Shqiperia',
                'Algeria' => 'Al Jaza\'ir',
                'Andorra' => 'Andorra',
                'Angola' => 'Angola',
                'Antigua and Barbuda' => 'Antigua and Barbuda',
                'Argentina' => 'Argentina',
                'Armenia' => 'Hayastan',
                'Australia' => 'Australia',
                'Austria' => 'Oesterreich',
                'Azerbaijan' => 'Azarbaycan Respublikasi',
                'The Bahamas' => 'The Bahamas',
                'Bahrain' => 'Al Bahrayn',
                'Bangladesh' => 'Bangladesh',
                'Barbados' => 'Barbados',
                'Belarus' => 'Byelarus',
                'Belgium' => 'Belgie',
                'Belize' => 'Belice',
                'Benin' => 'Benin',
                'Bhutan' => 'Drukyul',
                'Bolivia' => 'Bolivia',
                'Bosnia and Herzegovina' => 'Bosna i Hercegovina',
                'Botswana' => 'Botswana',
                'Brazil' => 'Brasil',
                'Brunei' => 'Brunei',
                'Bulgaria' => 'Republika Bulgariya',
                'Burkina Faso' => 'Burkina Faso',
                'Burundi' => 'Burundi',
                'Cambodia' => 'Kampuchea',
                'Cameroon' => 'Cameroon',
                'Canada' => 'Canada',
                'Cape Verde' => 'Cabo Verde',
                'Central African Republic' => 'Republique Centrafricaine',
                'Chad' => 'Tchad',
                'Chile' => 'Chile',
                'China' => 'Zhong Guo',
                'Colombia' => 'Colombia',
                'Comoros' => 'Comores',
                'Congo, Republic of the' => 'Republique du Congo',
                'Congo, Democratic Republic of the' => 'Republique Democratique du Congo',
                'Costa Rica' => 'Costa Rica',
                'Cote d\'Ivoire' => 'Cote d\'Ivoire',
                'Croatia' => 'Hrvatska',
                'Cuba' => 'Cuba',
                'Cyprus' => 'Kypros',
                'Czech Republic' => 'Ceska Republika',
                'Denmark' => 'Danmark',
                'Djibouti' => 'Djibouti',
                'Dominica' => 'Dominica',
                'Dominican Republic' => 'Republica Dominicana',
                'Ecuador' => 'Ecuador',
                'Egypt' => 'Misr',
                'El Salvador' => 'El Salvador',
                'Equatorial Guinea' => 'Guinea Ecuatorial',
                'Eritrea' => 'Ertra',
                'Estonia' => 'Eesti',
                'Ethiopia' => 'YeItyop\'iya',
                'Fiji' => 'Fiji',
                'Finland' => 'Suomi',
                'France' => 'France or Republique Francaise',
                'Gabon' => 'Gabon',
                'The Gambia' => 'The Gambia',
                'Georgia' => 'Sak\'art\'velo',
                'Germany' => 'Deutschland',
                'Ghana' => 'Ghana',
                'Greece' => 'Ellas',
                'Grenada' => 'Grenada',
                'Guatemala' => 'Guatemala',
                'Guinea' => 'Guinee',
                'Guinea-Bissau' => 'Guine-Bissau',
                'Guyana' => 'Guyana',
                'Haiti' => 'Haiti',
                'Honduras' => 'Honduras',
                'Hungary' => 'Magyarorszag',
                'Iceland' => 'Island',
                'India' => 'India, Bharat',
                'Indonesia' => 'Indonesia',
                'Iran' => 'Iran, Persia',
                'Iraq' => 'Al Iraq',
                'Ireland' => 'Ireland or Eire',
                'Israel' => 'Yisra\'el',
                'Italy' => 'Italia',
                'Jamaica' => 'Jamaica',
                'Japan' => 'Nippon',
                'Jordan' => 'Al Urdun',
                'Kazakhstan' => 'Qazaqstan',
                'Kenya' => 'Kenya',
                'Kiribati' => 'Kiribati',
                'Korea, North' => 'Choson or Choson-minjujuui-inmin-konghwaguk',
                'Korea, South' => 'Taehan-min\'guk',
                'Kuwait' => 'Al Kuwayt',
                'Kyrgyzstan' => 'Kyrgyz Respublikasy',
                'Laos' => 'Sathalanalat Paxathipatai Paxaxon Lao',
                'Latvia' => 'Latvija',
                'Lebanon' => 'Lubnan',
                'Lesotho' => 'Lesotho',
                'Liberia' => 'Liberia',
                'Libya' => 'Libya',
                'Liechtenstein' => 'Liechtenstein',
                'Lithuania' => 'Lietuva',
                'Luxembourg' => 'Luxembourg',
                'Macedonia' => 'Makedonija',
                'Madagascar' => 'Madagascar',
                'Malawi' => 'Malawi',
                'Malaysia' => 'Malaysia',
                'Maldives' => 'Dhivehi Raajje',
                'Mali' => 'Mali',
                'Malta' => 'Malta',
                'Marshall Islands' => 'Marshall Islands',
                'Mauritania' => 'Muritaniyah',
                'Mauritius' => 'Mauritius',
                'Mexico' => 'Mexico',
                'Federated States of Micronesia' => 'Federated States of Micronesia',
                'Moldova' => 'Moldova',
                'Monaco' => 'Monaco',
                'Mongolia' => 'Mongol Uls',
                'Morocco' => 'Al Maghrib',
                'Mozambique' => 'Mocambique',
                'Myanmar (Burma)' => 'Myanma Naingngandaw',
                'Namibia' => 'Namibia',
                'Nauru' => 'Nauru',
                'Nepal' => 'Nepal',
                'Netherlands' => 'Nederland',
                'New Zealand' => 'New Zealand',
                'Nicaragua' => 'Nicaragua',
                'Niger' => 'Niger',
                'Nigeria' => 'Nigeria',
                'Norway' => 'Norge',
                'Oman' => 'Uman',
                'Pakistan' => 'Pakistan',
                'Palau' => 'Belau',
                'Panama' => 'Panama',
                'Papua New Guinea' => 'Papua New Guinea',
                'Paraguay' => 'Paraguay',
                'Peru' => 'Peru',
                'Philippines' => 'Pilipinas',
                'Poland' => 'Polska',
                'Portugal' => 'Portugal',
                'Qatar' => 'Qatar',
                'Romania' => 'Romania',
                'Russia' => 'Rossiya',
                'Rwanda' => 'Rwanda',
                'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
                'Saint Lucia' => 'Saint Lucia',
                'Samoa' => 'Samoa',
                'San Marino' => 'San Marino',
                'Sao Tome and Principe' => 'Sao Tome e Principe',
                'Saudi Arabia' => 'Al Arabiyah as Suudiyah',
                'Senegal' => 'Senegal',
                'Serbia and Montenegro' => 'Srbija-Crna Gora',
                'Seychelles' => 'Seychelles',
                'Sierra Leone' => 'Sierra Leone',
                'Singapore' => 'Singapore',
                'Slovakia' => 'Slovensko',
                'Slovenia' => 'Slovenija',
                'Solomon Islands' => 'Solomon Islands',
                'Somalia' => 'Somalia',
                'South Africa' => 'South Africa',
                'Spain' => 'Espana',
                'Sri Lanka' => 'Sri Lanka',
                'Sudan' => 'As-Sudan',
                'Suriname' => 'Suriname',
                'Swaziland' => 'Swaziland',
                'Sweden' => 'Sverige',
                'Switzerland' => 'Suisse (French)',
                'Syria' => 'Suriyah',
                'Taiwan' => 'T\'ai-wan',
                'Tajikistan' => 'Jumhurii Tojikistan',
                'Tanzania' => 'Tanzania',
                'Thailand' => 'Muang Thai',
                'Tolo' => 'Togo',
                'Tonga' => 'Tonga',
                'Trinidad and Tobago' => 'Trinidad and Tobago',
                'Tunisia' => 'Tunis',
                'Turkey' => 'Turkiye',
                'Turkmenistan' => 'Turkmenistan',
                'Tuvalu' => 'Tuvalu',
                'Uganda' => 'Uganda',
                'Ukraine' => 'Ukrayina',
                'United Arab Emirates' => 'Al Imarat al Arabiyah al Muttahidah',
                'United Kingdom' => 'United Kingdom',
                'United States' => 'United States',
                'Uruguay' => 'Uruguay',
                'Uzbekistan' => 'Uzbekiston Respublikasi',
                'Vanuatu' => 'Vanuatu',
                'Vatican City (Holy See)' => 'Santa Sede (Citta del Vaticano)',
                'Venezuela' => 'Venezuela',
                'Vietnam' => 'Viet Nam',
                'Yemen' => 'Al Yaman',
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