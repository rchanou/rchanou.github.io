<?php

namespace ClubSpeed\Mail;

class MailService {

    private static $logic;
    private static $interfaceName;
    private static $interface;
    private static $ready;

    public static $settings;

    private function __construct() {} // prevent creation of "static" class

    public static function initialize(&$logic, $interfaceName) {
        self::$logic = $logic;
        self::$interfaceName = $interfaceName;
        self::$ready = false;
    }

    public static function builder() {
        return new MailBuilder(); // serve the builder for external use
    }

    private static function load() {
        // consider making this lazy loading so it does not get fired off on each call
        self::$settings = self::$logic->helpers->getControlPanelSettings(
            "MainEngine",
            array(
                  "SMTPServer"
                , "SMTPServerAuthenticationPassword"
                , "SMTPServerAuthenticationUserName"
                , "SMTPServerPort"
                , "SMTPServerUseAuthentiation"
                , "SMTPServerUseSSL"
            )
        );
        if (!isset(self::$settings['SMTPServerPort']))
            self::$settings['SMTPServerPort'] = "25";
        $interface = __NAMESPACE__ . '\\' . ucfirst(self::$interfaceName) . 'Mailer';
        self::$interface = new $interface(self::$settings);
        self::$ready = true;
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