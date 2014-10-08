<?php

namespace ClubSpeed\Mail;

class MailService {

    private static $logic;
    private static $interface;

    public static $settings;

    private function __construct() {} // prevent creation of "static" class

    public static function initialize(&$logic) {
        self::$logic = $logic;

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
    }

    public static function useInterface($interface) {
        $interface = __NAMESPACE__ . '\\' . ucfirst($interface) . 'Mailer';
        self::$interface = new $interface();
    }

    public static function builder() {
        return new MailBuilder(); // serve the builder for external use
    }

    public static function send(MailBuilder $mail) {
        return self::$interface->send($mail);
    }
}