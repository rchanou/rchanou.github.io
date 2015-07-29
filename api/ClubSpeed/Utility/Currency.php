<?php

namespace ClubSpeed\Utility;

class Currency {

    private static $formatter;
    private static $locale;
    private static $currency;
    private static $loaded = false;

    private function __construct() {} // prevent any initialization of this class

    public static function toCurrencyString($number) {
        if (!self::$loaded)
            self::load();
        return self::$formatter->formatCurrency($number, self::$currency);
    }

    private static function load() {
        $logic = $GLOBALS['logic']; // yay. fix later if we actually keep this.
        try {
            $locale = $logic->controlPanel->get('Booking', 'numberFormattingLocale');
            $locale = $locale[0];
            $locale = $locale->SettingValue ?: \Locale::getDefault(); // safe?
        }
        catch(\Exception $e) {
            $locale = \Locale::getDefault(); // and if this fails?
        }
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        try {
            $currency = $logic->controlPanel->get('Booking', 'currency');
            $currency = $currency[0];
            $currency = $currency->SettingValue ?: $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
        }
        catch (\Exception $e) {
            $currency = $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
        }

        self::$locale = $locale;
        self::$formatter = $formatter;
        self::$currency = $currency;
        self::$loaded = true;
    }

}