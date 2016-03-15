<?php

require_once(app_path().'/includes/includes.php');

class QuickPOSController extends BaseController
{
    public function settings()
    {
        $quickPOSSettings = CS_API::getJSON('controlPanel',array('filter' => 'name like quick%'));

        if (!isset($quickPOSSettings->controlPanel))
        {
            return Redirect::to('/disconnected');
        }
        $quickPOSSettingsCheckedData = array();
        $quickPOSSettingsData = array();

        foreach($quickPOSSettings->controlPanel as $setting)
        {
          $quickPOSSettingsCheckedData[$setting->name] = ($setting->value ? 'checked' : '');
          $quickPOSSettingsData[$setting->name] = $setting->value;
        }

        $trackIds = array();
        if (isset($quickPOSSettingsData['QuickPOSTrackNumbers']) && $quickPOSSettingsData['QuickPOSTrackNumbers'] != '')
        {
            $trackIds = explode(',', $quickPOSSettingsData['QuickPOSTrackNumbers']);
        }

        $categories = CS_API::getJSON('categories');
        if ($categories === null)
        {
            return Redirect::to('/disconnected');
        }
        $categoriesFiltered = array();
        foreach($categories as $category)
        {
            if ($category->enabled && !$category->deleted)
            {
                $categoriesFiltered[] = $category;
            }
        }

        $tracks = CS_API::getJSON('tracks');
        if (!isset($tracks->tracks))
        {
            return Redirect::to('/disconnected');
        }
        $tracks = $tracks->tracks;

        $heatTypes = CS_API::getJSON('heattypes', array('order' => 'name', 'limit' => 999));
        if ($heatTypes === null)
        {
            return Redirect::to('/disconnected');
        }
        $heatTypesFiltered = array();
        foreach($heatTypes as $heatType)
        {
            if ($heatType->enabled && !$heatType->deleted)
            {
                $heatTypesFiltered[] = $heatType;
            }
        }

        $heatTypesMigrationWasRun = false;
        if (property_exists($heatTypesFiltered[0], 'productId'))
        {
            $heatTypesMigrationWasRun = true;
        }

        $products = CS_API::getJSON('products', array('order' => 'description', 'limit' => 999));
        if (!isset($products->products))
        {
            return Redirect::to('/disconnected');
        }
        $products = $products->products;

        $productsFiltered = array();
        foreach($products as $product)
        {
            if ($product->enabled && !$product->deleted)
            {
                $productsFiltered[] = $product;
            }
        }

        return View::make('/screens/quickpos/settings',
            array('controller' => 'QuickPOSController',
                'isChecked' => $quickPOSSettingsCheckedData,
                'categories' => $categoriesFiltered,
                'tracks' => $tracks,
                'trackIds' => $trackIds,
                'heatTypes' => $heatTypesFiltered,
                'products' => $productsFiltered,
                'quickPOSSettings' => $quickPOSSettingsData,
                'heatTypesMigrationWasRun' => $heatTypesMigrationWasRun
            ));
    }

    public function updateSettings()
    {
        $input = Input::all();

        $quickPOSSettings = array();
        $quickPOSSettings['QuickPOSAddCustomer'] = isset($input['QuickPOSAddCustomer']) ? 1 : 0;
        $quickPOSSettings['QuickPOSTrackNumbers'] = isset($input['QuickPOSTrackNumbers']) ? implode(',',$input['QuickPOSTrackNumbers']) : '';
        $quickPOSSettings['QuickPOSDefaultCategoryId'] = $input['QuickPOSDefaultCategoryId'];

        $result = CS_API::updateSettingsFor('MainEngine',$quickPOSSettings);

        if ($result === false)
        {
            return Redirect::to('quickpos/settings')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        foreach($input as $currentInputKey => $currentInputValue)
        {
            if (str_contains($currentInputKey,'defaultProductForHeatTypesId-'))
            {
                $heatTypeId = (int)str_replace('defaultProductForHeatTypesId-','',$currentInputKey);
                $productId = $currentInputValue;
                if ($productId === 'null')
                {
                    $productId = null;
                }
                $result = CS_API::update('heattypes',$heatTypeId,array('productId' => $productId));
                if ($result === false)
                {
                    return Redirect::to('quickpos/settings')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
                }
                else if ($result === null)
                {
                    return Redirect::to('/disconnected');
                }
            }
        }
        return Redirect::to('quickpos/settings')->with( array('message' => 'Settings updated successfully!'));
    }
}
