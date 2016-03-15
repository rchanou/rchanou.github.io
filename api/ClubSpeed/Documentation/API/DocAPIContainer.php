<?php

namespace ClubSpeed\Documentation\API;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;

class DocAPIContainer {

    private static $_lazy;
    private static $data;
    private static $calls;
    private static $models;

    private function __construct() {}

    public static function init() {
        self::$_lazy = array(); // necessary? we always want everything
        self::$data = self::loadData();
        self::expand();
    }

    private static function parseJson($file) {
        return json_decode(file_get_contents($file), true);
    }

    private static function loadData() {
        return self::parseJson(__DIR__.'/api.json');
    }

    private static function expand() {
        self::expandCalls();
        self::expandModels();
    }

    private static function getModel($name) {
        return Arrays::first(self::$data['models'], function($model) use ($name) {
            return $model['id'] === $name;
        });
    }

    private static function expandModels() {
        $resources =& self::$data['resources'];
        foreach($resources as &$resource) {
            $model = self::getModel($resource['model']);
            $resource['info'] = $model['properties'];
        }
    }

    private static function expandCalls() {
        // not yet complete: more to do, when time allows
        $resources =& self::$data['resources'];
        foreach($resources as &$resource) {
            foreach($resource['calls'] as $callKey => &$call) {
                if (!isset($call['access']) || empty($call['access']))
                    $call['access'] = 'private';
                $call['access'] = ucfirst(strtolower($call['access']));
                if ($call['access'] === 'Private')
                    $call['access_icon'] = 'lock';
                $callPropertyNames = array_merge(
                      (isset($call['required'])  ? is_array($call['required'])  ? $call['required']  : array($call['required'])  : array())
                    , (isset($call['available']) ? is_array($call['available']) ? $call['available'] : array($call['available']) : array())
                );

                switch(strtoupper($call['verb'])) {
                    case 'DELETE':
                        $call['verb_icon'] = 'remove';
                        $call['header'] = 'Delete';
                        $call['header_icon'] = 'remove';
                        $call['type'] = 'remove'; // for css
                        break;
                    case 'POST':
                        $call['verb_icon'] = 'export';
                        $call['header'] = 'Create'; // more logic needed
                        $call['header_icon'] = 'plus';
                        $call['type'] = 'create'; // for css
                        break;
                    case 'PUT':
                        $call['header'] = 'Update';
                        $call['header_icon'] = 'pencil';
                        $call['verb_icon'] = 'pencil';
                        $call['type'] = 'update';
                        break;
                    // more todo
                }
                $call['request'] = array();
                foreach($callPropertyNames as $propertyName) {
                    $model = Arrays::first(self::$data['models'], function($modelDefinition) use ($resource) {
                        return $modelDefinition['id'] === $resource['model'];
                    });
                    $property = Arrays::first($model['properties'], function($prop) use ($propertyName) {
                        return strtolower($propertyName) === strtolower($prop['name']);
                    });
                    $call['request'][$property['name']] = Convert::convert(
                        isset($property['default']) ? $property['default'] : ""
                        , $property['type']
                    );
                }
            }
        }
    }

    public static function getData() {
        $data = array(
            'sections' => array()
        );
        $data['sections'][] = new DocAuthentication();
        $data['sections'][] = new DocRESTful();
        $data['sections'][] = new DocQueryOperations();
        $data['sections'][] = new DocTypicalUsage();

        // $data['sections'][] = self::$data['resources'][0]; // not yet complete: to be used in the future
        
        $data['sections'][] = new DocBooking();
        $data['sections'][] = new DocBookingAvailability();
        $data['sections'][] = new DocCheckDetails();
        $data['sections'][] = new DocChecks();
        $data['sections'][] = new DocCheckTotals();
        $data['sections'][] = new DocEventReservationLink();
        $data['sections'][] = new DocEventReservations();
        $data['sections'][] = new DocEvents();
        $data['sections'][] = new DocEventStatus();
        $data['sections'][] = new DocEventTypes();
        $data['sections'][] = new DocHeatDetails();
        $data['sections'][] = new DocHeatMain();
        $data['sections'][] = new DocPasswords();
        $data['sections'][] = new DocPayment();
        $data['sections'][] = new DocProcessPayment();
        $data['sections'][] = new DocProducts();
        $data['sections'][] = new DocRacers();
        $data['sections'][] = new DocReservations();
        $data['sections'][] = new DocScreenTemplate();
        $data['sections'][] = new DocCustomers();

        return $data;
    }
}
