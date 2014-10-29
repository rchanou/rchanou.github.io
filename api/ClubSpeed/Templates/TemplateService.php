<?php

namespace ClubSpeed\Templates;

class TemplateService {

    private static $loader;
    private static $twig;
    private static $ready = false;

    private function __construct() {} // prevent "static" class declaration

    public static function build($templateName, $data) {
        if (!self::$ready)
            self::init();
        $template = self::$twig->loadTemplate($templateName);
        return $template->render($data);
    }

    private static function init() {
        self::$loader = new \Twig_Loader_Filesystem(__DIR__.'/../../views');
        self::$twig = new \Twig_Environment(self::$loader);
        self::$ready = true;
    }
}