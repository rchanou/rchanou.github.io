<?php

namespace ClubSpeed\Payments;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Tokens;
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

/*
    The Omnipay service used to load default omnipay settings,
    as well as interface with common Omnipay methods.
*/
class OmnipayService {

    private $logic;
    private $db;
    private $allowed;
    private $gateway;
    protected $currency;
    protected $formatter;
    protected $locale;

    public function __construct(&$logic, &$db) {
        $this->logic = $logic;
        $this->db = $db;
        $this->allowed = array(
              'AuthorizeNet_AIM' => 'direct'
            , 'AuthorizeNet_SIM' => 'redirect'
            , 'Dummy'            => 'direct'
            , 'iDEAL'            => 'redirect'
            , 'Payflow_Pro'      => 'direct'
            , 'PayPal_Express'   => 'redirect'
            , 'PayPal_Pro'       => 'direct'
            , 'Rabobank'         => 'redirect'
            , 'SagePay_Direct'   => 'direct'
            , 'SagePay_Server'   => 'redirect'
            , 'Stripe'           => 'direct'
            , 'WorldPayXML'      => 'direct'
        );
    }

    public function available() {
        $allowed =& $this->allowed;
        $names = array_keys($allowed);
        $processors = array_intersect(Omnipay::find(), $names);
        $available = array();
        $stuff = array_walk($processors, function($val, $key) use (&$available, &$allowed) {
            $processor = Omnipay::create($val);
            $available[] = array(
                  'name'    => $val
                , 'type'    => $allowed[$val]
                , 'options' => array_keys($processor->getParameters())
            );
        });
        return $available;
    }

    public function current() {
        $allowed  = $this->allowed;
        $settings = $this->logic->controlPanel->get('Booking', 'onlineBookingPaymentProcessorSettings');
        $setting  = $settings[0];
        $current  = $setting->SettingValue;
        $decoded  = json_decode($current, true);
        if (!$decoded)
            throw new \CSException('Control panel setting for Booking.onlineBookingPaymentProcessorSettings could not be decoded!');
        if (!isset($decoded['type']))
            $decoded['type'] = $allowed[$decoded['name']];
        return $decoded;
    }

    protected function init($data) {
        if (!isset($data['name']) || empty($data['name'])) {
            $message = 'Attempted to process a payment without providing a payment processor name!';
            Log::error($message, Enums::NSP_PAYMENTS);
            throw new \CSException($message);
        }
        $name = $data['name'];
        $available = $this->available();
        $allowed = Arrays::contains($available, function($processor) use ($name) {
            return (isset($processor['name']) && $processor['name'] === $name);
        });
        if (!$allowed) {
            $message = 'Attempted to process a payment by using an unsupported payment processor! Received: ' . $name;
            Log::error($message, Enums::NSP_PAYMENTS);
            throw new \CSException($message);
        }
        $this->gateway = Omnipay::create(@$data['name']);
        $this->gateway->initialize(@$data['options'] ?: array());
        // $this->logPrefix = 'Check #' . @$data['check']['checkId'] . ': Base: ';
        Log::info('Starting payment using processor: ' . $name, Enums::NSP_PAYMENTS);

        // load all defaults
        try {
            $locale = $this->logic->controlPanel->get('Booking', 'numberFormattingLocale');
            $locale = $locale[0];
            $locale = $locale->SettingValue ?: \Locale::getDefault(); // safe?
        }
        catch(\Exception $e) {
            $locale = \Locale::getDefault(); // and if this fails?
        }
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        try {
            $currency = $this->logic->controlPanel->get('Booking', 'currency');
            $currency = $currency[0];
            $currency = $currency->SettingValue ?: $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
        }
        catch (\Exception $e) {
            $currency = $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
        }

        $this->locale = $locale;
        $this->formatter = $formatter;
        $this->currency = $currency;
    }

    protected function wrapper($params = array(), $callback) {
        $this->init($params);

        // set any expected defaults which are missing
        if (!isset($params['currency']))
            $params['currency'] = $this->currency;

        try {
            $res = $callback($params);
        }
        catch(\Exception $e) {
            return $this->failure(null, $e->getMessage());
        }
        return $this->handle($res); // this should be able to throw "uncaught" exceptions (will be caught by restler)
    }

    /*
        Logical flows

        * purchase -> success
        * purchase -> failure
        * purchase -> redirect -> front end -> complete -> success
        * purchase -> redirect -> front end -> complete -> failure
    */
    public function purchase($params = array()) {
        $gateway =& $this->gateway;
        return $this->wrapper($params, function($options) use (&$gateway) {
            return $gateway->purchase($options)->send();
        });
    }

    /*
        Logical flows

        * redirect -> complete -> success
        * redirect -> complete -> failure
    */
    public function complete($params = array()) {
        $gateway =& $this->gateway;
        return $this->wrapper($params, function($options) use (&$gateway) {
            return $gateway->completePurchase($options)->send();
        });
    }

    protected function handle($res) {
        if ($res->isSuccessful())
            return $this->success($res);
        else if ($res->isRedirect())
            return $this->redirect($res);
        else
            return $this->failure($res);
    }

    protected function success($res) {
        return array(
            'type'      => 'success',
            'code'      => $res->getCode(),
            'message'   => $res->getMessage(),
            'reference' => $res->getTransactionReference(),
            'data'      => $res->getData()
        );
    }

    protected function redirect($res) {
        return array(
            'type'           => 'redirect',
            'redirectUrl'    => $res->getRedirectUrl(),
            'redirectMethod' => $res->getRedirectMethod(),
            'redirectData'   => $res->getRedirectData()
        );
    }

    protected function failure($res, $message = 'Unknown failure!') {
        $code = null;
        if (!is_null($res)) {
            $code = $res->getCode();
            $_message = $res->getMessage();
            if (!empty($_message))
                $message = $_message;
        }
        $message = (empty($code) ? '' : ($code . ': ')) . $message;
        Log::error($message, Enums::NSP_PAYMENTS);
        throw new \CSException($message);
    }
}
