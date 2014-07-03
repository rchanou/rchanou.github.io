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

        self::$defaultEnglish = array(
            'welcomeMessage' => 'Welcome to our track!',
            'registerHeader' => 'Register',
            'newAccount' => 'Create a new account',
            'facebook' => 'Create a new account with Facebook',
            'backButton' => 'Back',
            'step2Header' => 'Create an Account',
            'yourPicture' => 'Your Picture',
            'birthdate' => 'Birth Date',
            'mobilephone' => 'Mobile Phone',
            'howdidyouhearaboutus' => 'How did you hear about us?',
            //'dropdownOptions' => array('no_response' => '', 'radio' => 'Radio', 'magazine' => 'Magazine', 'billboard' => 'Billboard'),
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'racername' => 'Racer Name',
            'email' => 'Email Address',
            'emailsMessage' => 'We will email your results and special offers.<br/> We do not share your e-mail.',
            'emailsOptOut' => 'Do not email my results or special offers.',
            'step2Clear' => 'Clear',
            'step2Submit' => 'Submit',
            'step3Header' => 'Terms & Conditions',
            'step3DoNotAgree' => 'Do Not Agree (Start Over)',
            'step3Agree' => 'I Agree, Sign',
            'step3AgreeNoSig' => 'I Agree',
            'step3PleaseSign' => 'Please Sign Your Name',
            'step3Clear' => 'Clear',
            'step3Done' => 'Done',
            'registrationCompleteMessage' => 'Thanks for completing registration. <br/>We\'ll see you on the track!',
            'registrationComplete' => 'Registration Complete',
            'completeRegistration' => 'Back to Main Menu',
            'step1HeaderTitle' => '',
            'step1fbHeaderTitle' => 'Register with Facebook',
            'step2HeaderTitle' => 'Create an Account',
            'step3HeaderTitle' => 'Terms & Conditions',
            'step4HeaderTitle' => 'Registration Complete',
            'step1PageTitle' => 'Registration Kiosk - Step 1',
            'step2PageTitle' => 'Registration Kiosk - Step 2',
            'step3PageTitle' => 'Registration Kiosk - Step 3',
            'step4PageTitle' => 'Registration Kiosk - Step 4',
            'signHere' => 'Please Sign Your Name',
            'clearSignature' => 'Clear',
            'cancelSigning' => 'Cancel',
            'startSigning' => 'I Agree, Sign',
            'required' => 'Required',
            'mustBeAValidEmailAddress' => 'Must be a valid e-mail address',
            'poweredByClubSpeed' => 'Powered by Club Speed',
            'Male' => 'Male',
            'Female' => 'Female',
            'Other' => 'Other',
            'gender' => 'Gender',
            'switchToFacebookPic' => 'Switch back to Facebook profile picture',
            'emailText' => 'By providing your email, you agree to receive periodic messages from ##TRACKNAME## notifying you of exclusive offers, special discounts, and the latest news on upcoming special events. You can withdraw your consent at any time.'
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