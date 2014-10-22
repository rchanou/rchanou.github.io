<?php

namespace ClubSpeed\Templates;

class TemplateService {

    private static $loader = new \Twig_Loader_Filesystem('../../views');
    private static $twig = new \Twig_Environment($loader);

    private function __construct() {} // prevent "static" class declaration

    public static function build($templateName, $data) {
        $template = self::$twig->loadTemplate($templateName);
        return $template->render($data);
    }
}