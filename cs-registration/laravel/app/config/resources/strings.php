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

    private static $defaultSpanish; //TODO: TEST ONLY DATA

    private function __construct() { }
    private static $initialized = false;

    public static function getDefaultEnglish()
    {
        self::initialize();
        return self::$defaultEnglish;
    }

    public static function getDefaultSpanish() //TODO: TEST ONLY DATA
    {
        self::initialize();
        return self::$defaultSpanish;
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
            'str_facebook' => 'Create a new account with Facebook',
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
            'str_LicenseNumber.required' => 'License # is required'
        );

        self::$cultureNames = array(
            'en-US' => 'English (US)',
            'en-GB' => 'English (UK)',
            'en-NZ' => 'English (NZ)',
            'en-AU' => 'English (AU)',
            'en-IE' => 'English (IE)',
            'en-CA' => 'English (CA)',
            'es-MX' => 'Español',
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

        //TODO: Remove this. For testing purposes only.
        self::$defaultSpanish = array(
            "welcomeMessage" => "Bienvenidos a nuestra track!",
            "registerHeader" => "Registra",
            "newAccount" => "Crea una nueva cuenta",
            "facebook" => "Crea una nueva cuenta con Facebook",
            "backButton" => "Vuelta",
            "step2Header" => "Crea una cuenta",
            "yourPicture" => "Su foto",
            "birthdate" => "Data de nacimiento",
            "mobilephone" => "Número de teléfono móvil",
            "howdidyouhearaboutus" => "¿Cómo se enteró de nosotros?",
            //"dropdownOptions" => array("radio" => "Radio", "magazine" => "Magazina", "billboard" => "El Billboard"),
            "firstname" => "Primer nombre",
            "lastname" => "Apellido",
            "racername" => "Nombre del corredor",
            "email" => "Su Email",
            "emailsMessage" => "Le enviaremos los resultados y ofertas especiales. No compartimos su correo electrónico.",
            "emailsOptOut" => "No envia por correo electrónico mis resultados y ofertas especiales.",
            "step2Clear" => "Borra",
            "step2Submit" => "Submit-o",
            "step3Header" => "Términos y Condiciones",
            "step3Terms" => "Lorem ipsum-o dolor sit amet, per partem omnesque te, te usu eros postulant. Ea mel nulla offendit consetetur. Et his decore intellegat efficiendi, et falli praesent est. Iriure suavitate eu est, id platonem voluptatum scribentur ius.
                            <p/>Quis moderatius quo cu. Laudem intellegebat pro te. Nam an dictas reprehendunt, cum an insolens accommodare, falli consul blandit cu pri. Ut mel blandit voluptua electram, an his quando aperiam, id antiopam intellegebat quo. Ad movet ridens volumus duo. Id intellegam philosophia theophrastus eum, elit everti deleniti no sed.
                            <p/>Ius an accusata reprehendunt, eruditi laoreet appareat te pri, qui cu tamquam ceteros singulis. Omnium iudicabit corrumpit vis ad. Mel denique rationibus ne, ex mea hinc vitae vidisse, et eos reprimique repudiandae. Aliquid fierent assueverit ut cum, aliquid principes sit ei.
                            <p/>Nemore epicuri consulatu an nec, autem tempor democritum ea pri, natum melius audiam has cu. Duo omnes posidonium cu, sanctus veritus tacimates mel ei. Platonem reprehendunt id sea, nec in menandri pertinacia, diceret consulatu iracundia mei at. In fugit facete salutandi cum, sit zril democritum adversarium ei. Cu eam tollit aeterno accusamus.
                            <p/>Vix impetus eruditi laoreet eu. Nobis eirmod cu vel, in quo praesent inciderint. Eu his melius pericula, ius ea zril vocibus, an usu mundi maluisset voluptatum. Vix ea laoreet tincidunt, sea ad dolor convenire. An laboramus necessitatibus pro.
                            Lorem ipsum-o dolor sit amet, per partem omnesque te, te usu eros postulant. Ea mel nulla offendit consetetur. Et his decore intellegat efficiendi, et falli praesent est. Iriure suavitate eu est, id platonem voluptatum scribentur ius.
                            <p/>Quis moderatius quo cu. Laudem intellegebat pro te. Nam an dictas reprehendunt, cum an insolens accommodare, falli consul blandit cu pri. Ut mel blandit voluptua electram, an his quando aperiam, id antiopam intellegebat quo. Ad movet ridens volumus duo. Id intellegam philosophia theophrastus eum, elit everti deleniti no sed.
                            <p/>Ius an accusata reprehendunt, eruditi laoreet appareat te pri, qui cu tamquam ceteros singulis. Omnium iudicabit corrumpit vis ad. Mel denique rationibus ne, ex mea hinc vitae vidisse, et eos reprimique repudiandae. Aliquid fierent assueverit ut cum, aliquid principes sit ei.
                            <p/>Nemore epicuri consulatu an nec, autem tempor democritum ea pri, natum melius audiam has cu. Duo omnes posidonium cu, sanctus veritus tacimates mel ei. Platonem reprehendunt id sea, nec in menandri pertinacia, diceret consulatu iracundia mei at. In fugit facete salutandi cum, sit zril democritum adversarium ei. Cu eam tollit aeterno accusamus.
                            <p/>Vix impetus eruditi laoreet eu. Lorem ipsum-o dolor sit amet, per partem omnesque te, te usu eros postulant. Ea mel nulla offendit consetetur. Et his decore intellegat efficiendi, et falli praesent est. Iriure suavitate eu est, id platonem voluptatum scribentur ius.
                            <p/>Quis moderatius quo cu. Laudem intellegebat pro te. Nam an dictas reprehendunt, cum an insolens accommodare, falli consul blandit cu pri. Ut mel blandit voluptua electram, an his quando aperiam, id antiopam intellegebat quo. Ad movet ridens volumus duo. Id intellegam philosophia theophrastus eum, elit everti deleniti no sed.
                            <p/>Ius an accusata reprehendunt, eruditi laoreet appareat te pri, qui cu tamquam ceteros singulis. Omnium iudicabit corrumpit vis ad. Mel denique rationibus ne, ex mea hinc vitae vidisse, et eos reprimique repudiandae. Aliquid fierent assueverit ut cum, aliquid principes sit ei.
                            <p/>Nemore epicuri consulatu an nec, autem tempor democritum ea pri, natum melius audiam has cu. Duo omnes posidonium cu, sanctus veritus tacimates mel ei. Platonem reprehendunt id sea, nec in menandri pertinacia, diceret consulatu iracundia mei at. In fugit facete salutandi cum, sit zril democritum adversarium ei. Cu eam tollit aeterno accusamus.
                            <p/>Vix impetus eruditi laoreet eu. Nobis eirmod cu vel, in quo praesent inciderint. Eu his melius pericula, ius ea zril vocibus, an usu mundi maluisset voluptatum. Vix ea laoreet tincidunt, sea ad dolor convenire. An laboramus necessitatibus pro.
                            Lorem ipsum-o dolor sit amet, per partem omnesque te, te usu eros postulant. Ea mel nulla offendit consetetur. Et his decore intellegat efficiendi, et falli praesent est. Iriure suavitate eu est, id platonem voluptatum scribentur ius.
                            <p/>Quis moderatius quo cu. Laudem intellegebat pro te. Nam an dictas reprehendunt, cum an insolens accommodare, falli consul blandit cu pri. Ut mel blandit voluptua electram, an his quando aperiam, id antiopam intellegebat quo. Ad movet ridens volumus duo. Id intellegam philosophia theophrastus eum, elit everti deleniti no sed.
                            <p/>Ius an accusata reprehendunt, eruditi laoreet appareat te pri, qui cu tamquam ceteros singulis. Omnium iudicabit corrumpit vis ad. Mel denique rationibus ne, ex mea hinc vitae vidisse, et eos reprimique repudiandae. Aliquid fierent assueverit ut cum, aliquid principes sit ei.
                            <p/>Nemore epicuri consulatu an nec, autem tempor democritum ea pri, natum melius audiam has cu. Duo omnes posidonium cu, sanctus veritus tacimates mel ei. Platonem reprehendunt id sea, nec in menandri pertinacia, diceret consulatu iracundia mei at. In fugit facete salutandi cum, sit zril democritum adversarium ei. Cu eam tollit aeterno accusamus.
                            <p/>Vix impetus eruditi laoreet eu. Nobis eirmod cu vel, in quo praesent inciderint. Eu his melius pericula, ius ea zril vocibus, an usu mundi maluisset voluptatum. Vix ea laoreet tincidunt, sea ad dolor convenire. An laboramus necessitatibus pro.",
            "step3DoNotAgree" => "No estoy de acuerdo (Empieza de nuevo)",
            "step3Agree" => "Estoy de acuerdo, Signo",
            "step3PleaseSign" => "Por favor entra su nombre",
            "step3Clear" => "Borra",
            "step3Done" => "Terminado",
            "registrationCompleteMessage" => "Lorem ipsum-o dolor sit amet, per partem omnesque te, te usu eros postulant. Ea mel nulla offendit consetetur. Et his decore intellegat efficiendi, et falli praesent est. Iriure suavitate eu est, id platonem voluptatum scribentur ius.",
            "registrationComplete" => "Registro Completo",
            "completeRegistration" => "Completa registro",
            "step1HeaderTitle" => "",
            'step1fbHeaderTitle' => 'Registra con Facebook',
            "step2HeaderTitle" => "Crea una cuenta",
            "step3HeaderTitle" => "Términos y Condiciones",
            "step4HeaderTitle" => "Registro Completo",
            "step1PageTitle" => "Kiosko registro - Step 1",
            "step2PageTitle" => "Kiosko registro - Step 2",
            "step3PageTitle" => "Kiosko registro - Step 3",
            "step4PageTitle" => "Kiosko registro - Step 4",
            'signHere' => 'Por favor, firme su nombre',
            'clearSignature' => 'Borra',
            'cancelSigning' => 'Cancela',
            'startSigning' => 'Estoy de acuerdo, Signo',
            'required' => 'Necesario',
            'mustBeAValidEmailAddress' => 'Debe ser un email válido',
            'poweredByClubSpeed' => 'Impulsado por Club Speed',
            'Male' => 'Hombre',
            'Female' => 'Mujer',
            'Other' => 'Otro',
            'Gender' => 'Sexo',
            'switchToFacebookPic' => 'Usa la foto del Facebook'
        );

        self::$initialized = true;
    }
}

/* //TODO: Very few of these match up with what I need. I need to discuss where the strings I'm using will be coming from, and how the clients will edit them.
{
   "en-US":{
      "strAddress":"Address",
      "strAmount":"Amount",
      "strBillingAddress":"Billing Address",
      "strBillingCity":"Billing City",
      "strBillingCountry":"Billing Country",
      "strBillingState":"Billing State",
      "strBillingZip":"Billing Zip",
      "strBonusValue":"Bonus Value",
      "strByAmount":"By Amount",
      "strByPercent":"By Percent",
      "strCadetsPerHeat":"Cadets per heat",
      "strCardID":"Card ID",
      "strCategory":"Category",
      "strCity":"City",
      "strComValue":"Com Value",
      "strConnected":"Connected",
      "strCost":"Cost",
      "strCountry":"Country",
      "strCreditCardNo":"Credit Card No",
      "strCurrencyName":"Currency Name",
      "strCurrencyRate":"Currency Rate",
      "strCurrencySymbol":"Currency Symbol",
      "strCVV":"CVV",
      "strDataSaveFailed":"There was an error saving the data",
      "strDataSaveSuccesful":"Data was saved successfully",
      "strDate":"Date",
      "strDateOfBirth":"Date of Birth",
      "strDescription":"Description",
      "strDisplayName":"Display Name",
      "strDollars":"Dollars",
      "strDriversLicense":"Drivers License",
      "strEmail":"Email Address",
      "strEmailFrom":"Email from",
      "strEmailTo":"Email to",
      "strEmailType":"Email Type",
      "strEndDate":"End Date",
      "strEndX":"End X",
      "strEntitle1":"Entitle1",
      "strEntitle2":"Entitle 2",
      "strEntitle3":"Entitle 3",
      "strEntitle4":"Entitle 4",
      "strEntitle5":"Entitle 5",
      "strEventTypes":"Event Type",
      "strExpirationDate":"Expiration Date",
      "strExportName":"Export Name",
      "strFileName":"File Name",
      "strFirstName":"First Name",
      "strFlagName":"Flag Name",
      "strFormName":"Form Name",
      "strGroupName":"Group Name",
      "strGST":"GST",
      "strGuardianMinorSignature":"Signature of Guardian and Minor",
      "strHeatTypeName":"Heat Type Name",
      "strHotelName":"Hotel Name",
      "strIgnoreIfBelow":"Ignore if below",
      "strIPAddress":"IP Address",
      "strItemNumber":"Item Number",
      "strKMeter":"Kilometer",
      "strKMeters":"KiloMeters",
      "strLapsOrMinutes":"Laps or Minutes",
      "strLastName":"Last Name",
      "strMeter":"Meter",
      "strMeters":"Meters",
      "strMile":"Mile",
      "strMiles":"Miles",
      "strMinorSignature":"Signature of Minor Participant",
      "strNA":"N\/A",
      "strName":"Name",
      "strNo":"No",
      "strOrderDate":"Order Date",
      "strOtherPoint":"Other Point",
      "strPaidValue":"Paid Value",
      "strParentPrintedName":"Printed Name of Parent\/Guardian",
      "strParentSignature":"Signature of Parent\/Guardian",
      "strPassword":"Password",
      "strPercentage":"Percentage",
      "strPhoneNumber":"Phone Number",
      "strPointByNumber":"Point {0}",
      "strPointItem":"Point Item",
      "strPoints":"Points",
      "strPONumber":"PO Number",
      "strPositionDescription":"Position Description",
      "strPositionMinExpLevel":"Min. Exp Levl",
      "strPositionName":"Position Name",
      "strPrice1":"Price 1",
      "strPrice2":"Price 2",
      "strPrice3":"Price 3",
      "strPrice4":"Price 4",
      "strPrice5":"Price 5",
      "strPriceCadet":"Price Cadet",
      "strPromotionCode":"Promotion Code",
      "strQuantity":"Quantity",
      "strRacerName":"Racer Name",
      "strRacersPerHeat":"Racers per heat",
      "strReceived":"Received",
      "strReceivedDate":"Received Date",
      "strReOrderPoint":"Reorder Point",
      "strRequired":"Required",
      "strRequiredDate":"Required Date",
      "strReservationPoints":"Reservation Points",
      "strScheduleDuration":"Schedule Duration",
      "strSetOf":"Set of",
      "strSettingValue":"Setting Value",
      "strShippingAddress":"Shipping Address",
      "strShippingCity":"Shipping City",
      "strShippingCountry":"Shipping Country",
      "strShippingState":"Shipping State",
      "strShippingZip":"Shipping Zip Code",
      "strSignHere":"Sign Name Here",
      "strSourceName":"Source Name",
      "strStartDate":"Start Date",
      "strStartFrom":"Start From",
      "strStartX":"Start X",
      "strState":"State",
      "strStock":"Stock",
      "strSubItem":"Sub Item",
      "strTaskName":"Task name",
      "strTaxes":"Tax",
      "strTerminalName":"Terminal Name",
      "strToday":"Today",
      "strTrackLength":"Track Length",
      "strTrackNo":"Track No",
      "strUnitCost":"Unit Cost",
      "strUserName":"User Name",
      "strVolume":"Volume",
      "strVoucherCode":"Voucher Code",
      "strVoucherName":"Voucher Name",
      "strWebPassword":"Web Password",
      "strWidth":"Width",
      "strWinBy":"Win by",
      "stryard":"Yard",
      "strYards":"Yards",
      "strYes":"Yes",
      "strZipCode":"Zip Code"
   }
}

 */