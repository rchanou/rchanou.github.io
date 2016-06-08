<?php

class Version
{
    public $restler;
    private $logic;

    // Versions of various applications and modules
    public $speedscreenVersion = '2.0.0';
    public $apiVersion = '1.7';
    public $apiLastUpdatedAt = '5/16/2016 14:00';

    function __construct(){
        $this->logic = $GLOBALS['logic'];
        // header('Access-Control-Allow-Origin: *'); //Here for all /say
    }

    public function index($desiredData, $sub = null) {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        switch($desiredData) {
            case "current":
                return $this->current();
                break;
            case "api":
                return $this->api();
                break;
            case "os":
                return $this->os();
                break;
            case "sql":
                return $this->sql();
                break;
            case "eurekas":
                return $this->eurekas();
                break;
            case "booking":
                return $this->booking();
                break;
            case "oldbooking":
                return $this->oldbooking();
                break;
            case "daytonabooking":
                return $this->daytonabooking();
                break;
            case "php":
                return $this->php();
                break;
            case "cardpresentprocessor":
                return $this->cardPresentProcessor();
                break;
            default:
                throw new RestException(401, "Invalid version parameter!");
        }
    }

    public function current()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT TOP 1 * FROM Version_CS order by UpdatedDate desc";
        $tsql_params = array();

        $rows = $this->run_query($tsql, $tsql_params);

        $output = array();

        if(count($rows) == 0)
        {
            $_GET['suppress_response_codes'] = true;
            throw new RestException(412, 'No results returned.');
        }
        else
        {
            $output["CurrentVersion"] = $rows[0]["CurrentVersion"];
            $output["LastUpdated"] = $rows[0]["UpdatedDate"];
        }

        return $output;
    }

    public function api()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $output["CurrentVersion"] = $this->apiVersion;
        $output["LastUpdated"] = $this->apiLastUpdatedAt;
        return $output;
    }

    public function os()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $output["OS"] = php_uname('s');
        $output["Version"] = php_uname('v');
        return $output;
    }

    public function sql()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT @@version";
        $tsql_params = array();

        $rows = $this->run_query($tsql, $tsql_params);

        $output = count($rows) > 0 ? $rows[0][''] : null;
        return array('SqlVersion' => $output);
    }

    public function eurekas() {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) { // or private access?
            throw new RestException(401, "Invalid authorization!");
        }
        if (!$this->logic->version->hasEurekas())
            throw new RestException(404);
        return;
    }

    public function booking() {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT SettingName, SettingValue FROM ControlPanel WHERE TerminalName = 'Booking' AND SettingName IN ('onlineBookingPaymentProcessorSettings', 'registrationEnabled')";
        $tsql_params = array();
        $rows = $this->run_query($tsql, $tsql_params);
        $settings = array();
        foreach($rows as $currentSetting)
        {
            $settings[$currentSetting["SettingName"]] = $currentSetting["SettingValue"];
        }
        if (isset($settings['onlineBookingPaymentProcessorSettings']))
        {
            $settings['onlineBookingPaymentProcessorSettings'] = json_decode($settings['onlineBookingPaymentProcessorSettings']);
        }

        $paymentProcessor = isset($settings["onlineBookingPaymentProcessorSettings"]->name) ? $settings["onlineBookingPaymentProcessorSettings"]->name : null;
        $testMode = isset($settings["onlineBookingPaymentProcessorSettings"]->options->testMode) ? (bool)$settings["onlineBookingPaymentProcessorSettings"]->options->testMode : null;
        $registrationEnabled = isset($settings["registrationEnabled"]) ? (bool)$settings["registrationEnabled"] : null;

        $output = array(
          'PaymentProcessor' => $paymentProcessor,
          'TestMode' => $testMode,
          'Enabled' => $registrationEnabled
        );
        return $output;
    }

    public function oldbooking() {
        if (@$_REQUEST['secretkey'] !== "GusGus6021023!!GiftCardCustomer") {
            throw new RestException(401, "Invalid authorization!");
        }
        $tsql = "SELECT label as SettingName, htmlText as SettingValue from ReservationSettings where label in ('PayflowUser','PayflowVendor','PayflowPartner','PayflowPassword','VSPVendorName','EncryptionPassword')";
        $tsql_params = array();
        $rows = $this->run_query($tsql, $tsql_params);
        $settings = array();
        foreach($rows as $currentSetting)
        {
            $settings[$currentSetting["SettingName"]] = $currentSetting["SettingValue"];
        }
        $paypal = array(
          'PayflowUser' => isset($settings['PayflowUser']) ? $settings['PayflowUser'] : "",
          'PayflowVendor' => isset($settings['PayflowVendor']) ? $settings['PayflowVendor'] : "",
          'PayflowPartner' => isset($settings['PayflowPartner']) ? $settings['PayflowPartner'] : "",
          'PayflowPassword' => isset($settings['PayflowPassword']) ? $settings['PayflowPassword'] : ""
        );
        $sagepay = array(
          'VSPVendorName' => isset($settings['VSPVendorName']) ? $settings['VSPVendorName'] : "",
          'EncryptionPassword' => isset($settings['EncryptionPassword']) ? $settings['EncryptionPassword'] : ""
        );
        $output = array(
          'paypal' => $paypal,
          'sagepay' => $sagepay
        );
        return $output;
    }

    public function daytonabooking() {
        if (@$_REQUEST['secretkey'] !== "GusGus6021023!!GiftCardCustomer") {
            throw new RestException(401, "Invalid authorization!");
        }
        $tsql = "SELECT Label as SettingName, htmlText as SettingValue from EventReservationSettings where label in ('VSPVendorName','EncryptionPassword')";
        $tsql_params = array();
        $rows = $this->run_query($tsql, $tsql_params);
        $settings = array();
        foreach($rows as $currentSetting)
        {
            $settings[$currentSetting["SettingName"]] = $currentSetting["SettingValue"];
        }
        $sagepay = array(
          'VSPVendorName' => isset($settings['VSPVendorName']) ? $settings['VSPVendorName'] : "",
          'EncryptionPassword' => isset($settings['EncryptionPassword']) ? $settings['EncryptionPassword'] : ""
        );
        $output = array(
          'sagepay' => $sagepay
        );
        return $output;
    }

    public function php() {
        $phpversion = phpversion();
        $output = array(
          'php' => $phpversion
        );
        return $output;
    }

    // (could not use whitelisting as I cannot guarantee/know all of the TerminalNames in advance via /settings/get and /controlPanel doesn't support whitelisting)
    public function cardPresentProcessor() {
        if (@$_REQUEST['secretkey'] !== "GusGus6021023!!GiftCardCustomer") {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT TerminalName, SettingName, SettingValue, Fixed, CreatedDate FROM ControlPanel WHERE SettingName IN ('CardPresentProcessor')";
        $tsql_params = array();
        $rows = $this->run_query($tsql, $tsql_params);
        $settings = array();
        foreach($rows as $currentSetting)
        {
            $settings[$currentSetting["TerminalName"]] = $currentSetting;
        }

        $output = array(
          'CardPresentProcessors' => $settings
        );
        return $output;
    }

    private function run_query($tsql, $params = array()) {
        $tsql_original = $tsql . ' ';
        // Connect
        try {
            $conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            // Prepare statement
            $stmt = $conn->prepare($tsql);

            // Execute statement
            $stmt->execute($params);

            // Put in array
            $output = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {
            die('Exception Message:'  . $e->getMessage()  . '<br/>(Line: '. $e->getLine() . ')' . '<br/>Passed query: ' . $tsql_original . '<br/>Parameters passed: ' . print_r($params,true));
        }

        return $output;
    }
}