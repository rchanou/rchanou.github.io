<?php

namespace ClubSpeed\Mail;
use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Utility\Types;

class MailService {

    private static $logic;
    private static $interfaceName;
    private static $interface;
    private static $ready;

    public static $settings;

    private function __construct() {} // prevent creation of "static" class

    public static function initialize(&$logic) {
        self::$logic = $logic;
        self::$ready = false;
    }

    public static function builder() {
        return new MailBuilder(); // serve the builder for external use
    }

    private static function getSetting($name) {
        $uow = UnitOfWork::build()
            ->action('all')
            ->where(array(
                  'TerminalName' => 'MainEngine'
                , 'SettingName'  => $name
            ));
        self::$logic->controlPanel->uow($uow);
        if (empty($uow->data))
            return null; // setting does not exist in control panel - just use the default according to PHP
        $data = Arrays::first($uow->data);
        $value = $data->SettingValue; // do we want to is_null and check for DefaultValue as well?
        if (!empty($data->DataType)) {
            $type = null;
            if (is_numeric($data->DataType) || $data->DataType === 'email') // strings will contain a number, signifying the max length
                $type = Types::$string;
            else
                $type = Types::byName($data->DataType);
            $value = Convert::convert($value, $type);
        }
        return $value;
    }

    private static function getSettings() {
        $settings = array(
              'SMTPServer'                       => self::getSetting('SMTPServer')                           ?: '127.0.0.1'
            , 'SMTPServerAuthenticationPassword' => self::getSetting('SMTPServerAuthenticationPassword')     ?: ''
            , 'SMTPServerAuthenticationUserName' => self::getSetting('SMTPServerAuthenticationUserName')     ?: ''
            , 'SMTPServerPort'                   => self::getSetting('SMTPServerPort')                       ?: '25'
            , 'SMTPServerUseAuthentiation'       => self::getSetting('SMTPServerUseAuthentiation') /*[sic]*/ ?: false
            , 'SMTPServerUseSSL'                 => self::getSetting('SMTPServerUseSSL')                     ?: false
            , 'SMTPServerEncryptionType'         => self::getSetting('SMTPServerEncryptionType')             ?: 'ssl'
        );
        return $settings;
    }

    private static function load() {
        $settings = self::getSettings();
        $interface = __NAMESPACE__ . '\\' . ucfirst(self::$interfaceName) . 'Mailer';
        self::$interface = new $interface($settings);
        self::$ready = true;
    }

    public static function useInterface($interfaceName) {
        self::$interfaceName = $interfaceName;
    }

    public static function send(MailBuilder $mail) {
        if (!self::$ready)
            self::load(); // don't actually load until we need to send a message
        return self::$interface->send($mail);
    }

    //TODO: A little hacky, but didn't want to risk breaking existing functionality - could be rolled into existing MailBuilder and SwiftMailer
    public static function sendWithInlineImages(MailBuilder $mail,$inlineImages = array()) {
        if (!self::$ready)
            self::load(); // don't actually load until we need to send a message
        return self::$interface->sendWithInlineImages($mail,$inlineImages);
    }
}