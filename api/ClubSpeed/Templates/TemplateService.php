<?php

namespace ClubSpeed\Templates;

class TemplateService {

    private static $loader = null;
    private static $twig = null;
    private static $loaderString = null;
    private static $twigString = null;
    private static $ready = false;

    private function __construct() {} // prevent "static" class declaration

    public static function init() {
        self::$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../views');
        self::$twig = new \Twig_Environment(self::$loader);

        self::$loaderString = new \Twig_Loader_String();
        self::$twigString = new \Twig_Environment(self::$loaderString);
        self::$ready = true;
    }

    public static function build($filename, $data) {
        if (!self::$ready)
            self::init();
        $template = self::$twig->loadTemplate($filename);
        return $template->render($data);
    }

    // silly extension to use twig with strings
    public static function buildFromString($templateString, $data) {
        if (!self::$ready)
            self::init();
        $template = self::$twigString->loadTemplate($templateString);
        return $template->render($data);
    }
}