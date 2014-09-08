<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/OnlineBookingAvailability_V.php');

class DbOnlineBookingAvailability_V extends DbCollection {

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\OnlineBookingAvailability_V');
        $this->dbToJson = array(
            'HeatDescription'               => 'heatDescription'
            , 'HeatEndsAt'                  => 'heatEndsAt'
            , 'HeatNo'                      => 'heatNo'
            , 'HeatSpotsAvailableCombined'  => 'heatSpotsTotalCombined'
            , 'HeatSpotsAvailableOnline'    => 'heatSpotsAvailableOnline'
            , 'HeatSpotsTotalActual'        => 'heatSpotsTotalActual'
            , 'HeatStartsAt'                => 'heatStartsAt'
            , 'HeatTypeNo'                  => 'heatTypeNo'
            , 'IsPublic'                    => 'isPublic'
            , 'OnlineBookingsID'            => 'onlineBookingsId'
            , 'Price1'                      => 'price1'
            , 'ProductDescription'          => 'productDescription'
            , 'ProductsID'                  => 'productsId'
            , 'ProductSpotsAvailableOnline' => 'productSpotsAvailableOnline'
            , 'ProductSpotsTotal'           => 'productSpotsTotal'
            , 'ProductSpotsUsed'            => 'productSpotsUsed'
        );
        parent::secondaryInit();
    }

    /**;
     * Combs a reference to an array of existing data,
     * looking for a key whose value equals the provided value argument.
     *
     * @param mixed[mixed] $data The data to search.
     * @param mixed $key The key for which to search.
     * @param mixed $val The value for which to search.
     *
     * @return mixed The reference to the data entry if the key-value combination is found, a null-reference if not.
     */
    private static function &findExisting(&$data, $key, $val) {
        // this could really be its own helper function
        $existing = null;
        foreach($data as $current) {
            // this notation may give us problems with PHP 5.3 -- CHECK IT
            if (isset($current[$key]) && $current[$key] == $val) {
                $existing =& $current;
                return $existing;
            }
        }
        return $existing;
    }

    /**
     * Document: TODO
     */
    public function compress($data = array(), $single = false) {

        // does this belong here, or in the cs-booking class? -- sort of stretching the model ideal here
        $return = array(
            'bookings' => array()
        );
        $bookings =& $return['bookings'];
        if (isset($data) && !is_array($data))
            $data = array($data); // convert a single record to an array for the foreach syntax to function

        if (!is_null($data)) {
            foreach($data as $row) {
                $existingHeat = self::findExisting($bookings, 'heatId', $row->HeatNo);
                if ($existingHeat == null) {
                    $existingHeat = array(
                          'heatId'                      => $row->HeatNo
                        , 'heatDescription'             => $row->HeatDescription
                        , 'heatStartsAt'                => $row->HeatStartsAt
                        , 'heatEndsAt'                  => $row->HeatEndsAt
                        , 'heatSpotsTotalActual'        => $row->HeatSpotsTotalActual
                        , 'heatSpotsAvailableCombined'  => $row->HeatSpotsAvailableCombined
                        , 'heatSpotsAvailableOnline'    => $row->HeatSpotsAvailableOnline
                        , 'products'                    => array()
                    );
                    $bookings[] =& $existingHeat;
                }
                $existingHeat['products'][] = array(
                      'onlineBookingsId'            => $row->OnlineBookingsID
                    , 'productsId'                  => $row->ProductsID
                    , 'productDescription'          => $row->ProductDescription
                    , 'price'                       => $row->Price1
                    , 'productSpotsAvailableOnline' => $row->ProductSpotsAvailableOnline
                    , 'productSpotsTotal'           => $row->ProductSpotsTotal
                );
            }
        }
        if ($single === true) { // do this, or return the single item namespaced?
            return @$return['bookings'][0] ?: null;
        }
        return $return;
    }
}