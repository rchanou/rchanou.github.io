<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class GiftCardsController
 * View: giftcards.blade.php
 * URL: /giftcards
 *
 * Description:
 * TODO
 */
class GiftCardsController extends BaseController
{
    public function entry()
    {
        $settings = Settings::getSettings(true); //Force a refresh of all settings
        Session::put('settings',$settings);
        if (!isset($settings['giftCardSalesEnabled']) || $settings['giftCardSalesEnabled'] === false)
        {
            return Redirect::to('step1');
        }
        checkForCultureChange();

        $giftCardProducts = CS_API::getAllGiftCardProducts();

        $giftCardsAvailableForOnlineSale = array(); //Packaging list of gift cards to be made available online
        if (isset($settings['giftCardsAvailableForOnlineSale']))
        {
            $giftCardsAvailableForOnlineSale = $settings['giftCardsAvailableForOnlineSale'];
            $giftCardsAvailableForOnlineSale = json_decode($giftCardsAvailableForOnlineSale);
            if (isset($giftCardsAvailableForOnlineSale->giftCardProductIDs))
            {
                $giftCardsAvailableForOnlineSale = $giftCardsAvailableForOnlineSale->giftCardProductIDs;
            }
            else
            {
                $giftCardsAvailableForOnlineSale = array();
            }
        }

        //Merging the list of existing and enabled gift card products with whether or not they're available online
        $giftCardProductsMerged = array();
        foreach($giftCardProducts as $currentProduct)
        {
            if (isset($currentProduct->enabled) && $currentProduct->enabled == true &&
                in_array($currentProduct->productId,$giftCardsAvailableForOnlineSale))
            {
                $currentProductMerged = array(
                    'productId' => $currentProduct->productId,
                    'description' => $currentProduct->description,
                    'price1' => $currentProduct->price1
                );
                $giftCardProductsMerged[] = $currentProductMerged;
            }
        }

        Session::put('giftCardProducts',$giftCardProductsMerged);

        $locale = $settings['numberFormattingLocale'];
        $moneyFormatter = new NumberFormatter($locale,  NumberFormatter::CURRENCY);
        $currency = $settings['currency'];

        //Render the page
        $view = '/giftcards';
        if (isset($settings['responsive']) && $settings['responsive'] == true)
        {
            $view = '/giftcards-responsive';
        }

        //Render the page
        return View::make($view,
            array(
                'images' => Images::getImageAssets(),
                'strings' => Strings::getStrings(),
                'giftCardOptions' => $giftCardProductsMerged,
                'authenticated' => Session::get('authenticated'),
                'loginToAccountErrors' => Session::get('loginToAccountErrors'),
                'createAccountErrors' => Session::get('createAccountErrors'),
                'settings' => $settings,
                'moneyFormatter' => $moneyFormatter,
                'currency' => $currency
            )
        );
    }
}