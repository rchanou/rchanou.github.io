<?php

namespace ClubSpeed\Mappers;
use ClubSpeed\Utility\Arrays;

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
        $inner =& $return[$this->namespace];
        if (isset($data) && !is_array($data))
            $data = array($data); // convert a single record to an array for the foreach syntax to function
        if (!is_null($data)) {
            $inner = Arrays::group($data, function($val) {
                return array(
                    'HeatNo' => $val->HeatNo
                );
            });
            foreach($inner as $key => $group) {
                $self =& $this; // php 5.3 nonsense
                $inner[$key] = array_reduce($group, function($carry, $current) use (&$self) {
                    if (!is_array($carry)) {
                        $carry = $self->map('client', array(
                            'HeatNo'                     => $current->HeatNo,
                            'HeatDescription'            => $current->HeatDescription,
                            'HeatStartsAt'               => $current->HeatStartsAt,
                            'HeatSpotsTotalActual'       => $current->HeatSpotsTotalActual,
                            'HeatSpotsAvailableCombined' => $current->HeatSpotsAvailableCombined,
                            'HeatSpotsAvailableOnline'   => $current->HeatSpotsAvailableOnline,
                            'HeatTypeNo'                 => $current->HeatTypeNo,
                            'products'                    => array()
                        ));
                    }
                    $product = array( // map later
                        'OnlineBookingsID'            => $current->OnlineBookingsID,
                        'Price1'                      => $current->Price1,
                        'ProductDescription'          => $current->ProductDescription,
                        'ProductsID'                  => $current->ProductsID,
                        'ProductSpotsAvailableOnline' => $current->ProductSpotsAvailableOnline,
                        'ProductSpotsTotal'           => $current->ProductSpotsTotal,
                        'ProductType'                 => $current->ProductType
                    );
                    if (isset($product['ProductType']) && !empty($product['ProductType'])) {
                        $product['ProductType'] = call_user_func(function($productType) {
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
                        }, $product['ProductType']);
                    }
                    if (!empty($product) && !\ClubSpeed\Utility\Objects::isEmpty($product)) {
                        $carry['products'][] = $self->map('client', $product);
                    }
                    return $carry;
                });
            }
        }
        return $return;
    }
}