<?php

namespace ClubSpeed\Mappers;

class BookingAvailabilityMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'bookings';
        $this->register(array(
            'HeatDescription'               => ''
            , 'HeatNo'                      => 'heatId'
            , 'HeatSpotsAvailableCombined'  => ''
            , 'HeatSpotsAvailableOnline'    => ''
            , 'HeatSpotsTotalActual'        => ''
            , 'HeatStartsAt'                => ''
            , 'HeatEndsAt'                  => ''
            , 'HeatTypeNo'                  => 'heatTypeId'
            , 'IsPublic'                    => ''
            , 'OnlineBookingsID'            => ''
            , 'ProductType'                 => ''
            , 'Price1'                      => ''
            , 'ProductDescription'          => ''
            , 'ProductsID'                  => ''
            , 'ProductSpotsAvailableOnline' => ''
            , 'ProductSpotsTotal'           => ''
            , 'ProductSpotsUsed'            => ''
        ));
    }

    /**
     * Document: TODO
     */
    protected final function compress($data = array(), $select = array()) {

        // does this belong here, or in the cs-booking class? -- sort of stretching the model ideal here
        $return = array(
            $this->namespace => array()
        );
        $bookings =& $return[$this->namespace];
        if (isset($data) && !is_array($data))
            $data = array($data); // convert a single record to an array for the foreach syntax to function

        if (!is_null($data)) {
            $bookingKeys = array(
                  'HeatNo'
                , 'HeatDescription'
                , 'HeatStartsAt'
                , 'HeatSpotsTotalActual'
                , 'HeatSpotsAvailableCombined'
                , 'HeatSpotsAvailableOnline'
                , 'HeatTypeNo'
            );
            $productKeys = array(
                  'OnlineBookingsID'
                , 'Price1'
                , 'ProductDescription'
                , 'ProductsID'
                , 'ProductSpotsAvailableOnline'
                , 'ProductSpotsTotal'
                , 'ProductType'
            );
            $map = $this->_map['client'];
            $bookingKeys = array_values(array_intersect(array_keys($map), $bookingKeys)); // get the filtered list of columns for checks
            $productKeys = array_values(array_intersect(array_keys($map), $productKeys)); // get the filtered list of columns for checkdetails

            foreach($data as $row) {
                $existingBooking =& self::findExisting($bookings, $map['HeatNo'], $row->HeatNo);
                if ($existingBooking == null) {
                    $existingBooking = array();
                    foreach($bookingKeys as $key)
                        $existingBooking[$map[$key]] = $row->{$key};
                    $existingBooking['products'] = array();
                    $bookings[] =& $existingBooking;
                }
                foreach($productKeys as $key)
                    $product[$map[$key]] = $row->{$key};
                if (isset($map['ProductType']) && !empty($product[$map['ProductType']])) {
                    // special case -- convert original Enum to readable value
                    $product[$map['ProductType']] = call_user_func(function($productType) {
                        switch($productType) {
                            case 1:  return 'RegularItem';
                            case 2:  return 'PointItem';
                            case 3:  return 'FoodItem';
                            case 4:  return 'ReservationItem';
                            case 5:  return 'GameCardItem';
                            case 6:  return 'MembershipItem';
                            case 7:  return 'GiftCardItem';
                            case 8:  return 'EntitleItem';
                            default: return 'RegularItem';
                        }
                    }, $row->ProductType);
                }
                if (!empty($product) && !\ClubSpeed\Utility\Objects::isEmpty($product)) {
                    $existingBooking['products'][] = $product;
                }
            }
        }
        return $return;
    }
}