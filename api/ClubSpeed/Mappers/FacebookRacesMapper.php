<?php

namespace ClubSpeed\Mappers;
use ClubSpeed\Utility\Arrays as Arrays;

class FacebookRacesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'races';
        $this->register(array(
              'CustID'         => 'customerId'
            , 'Access_Token'   => 'token'
            , 'HeatNo'         => 'heatId'
            , 'HeatTypeName'   => 'heatType'
            , 'FinishPosition' => ''
            , 'Finish'         => 'heatFinishTime'
        ));
    }

    protected final function compress($data) {
        if (!isset($data) || is_null($data))
            return null;
        $table = $this->namespace ?: "records";
        $compressed = array(
            $table => array()
        );
        $inner =& $compressed[$table];
        if (isset($data) && !is_array($data))
            $data = array($data);
        if (!empty($data)) {
            $inner = Arrays::group($data, function($val) {
                return array(
                    'CustID'       => $val->CustID,
                    'Access_Token' => $val->Access_Token
                );
            });
            foreach($inner as $key => $group) {
                $self =& $this; // php 5.3 nonsense
                $inner[$key] = array_reduce($group, function($carry, $current) use (&$self) {
                    if (!is_array($carry)) {
                        $carry = $self->map('client', array(
                            'CustID'       => $current->CustID,
                            'Access_Token' => $current->Access_Token,
                            'races'        => array()
                        ));
                    }
                    $carry['races'][] = $self->map('client', array(
                        'HeatNo'         => $current->HeatNo,
                        'HeatTypeName'   => $current->HeatTypeName,
                        'FinishPosition' => $current->FinishPosition,
                        'Finish'         => $current->Finish
                    ));
                    return $carry;
                });
            }
        }

        return $compressed;
    }
}