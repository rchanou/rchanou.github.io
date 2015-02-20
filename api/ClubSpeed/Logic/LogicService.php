<?php

namespace ClubSpeed\Logic;

/**
 * The service container
 * for all logic classes.
 */
class LogicService {

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * An associative array used for storing instantiated ClubSpeed business classes.
     */
    private $_lazy;
    
    /**
     * Constructs a new instance of the CSBooking class.
     *
     * The CSBooking constructor requires an instantiated CSConnection class for injection.
     *
     * @param CSConnection $db The CSConnection class to inject.
     */
    public function __construct(&$db) {
        $this->db = $db;
        $this->_lazy = array();
    }

    // this structure works fine, but requires us to call functions instead of referencing properties
    // this may be bad practice (!!!) make sure we test this --
    // if all else fails, bypass the __get, and call the methods externally instead of the properties
    function __get($prop) {

        return $this->load($prop);

        // switch($prop) {
        //     case 'booking':                     return $this->booking();
        //     case 'bookingAvailability':         return $this->bookingAvailability();
        //     case 'bookingAvailabilityPublic':   return $this->bookingAvailabilityPublic();
        //     case 'checks':                      return $this->checks();
        //     case 'checkDetails':                return $this->checkDetails();
        //     case 'checkTotals':                 return $this->checkTotals();
        //     case 'controlPanel':                return $this->controlPanel();
        //     case 'customers':                   return $this->customers();
        //     case 'events':                      return $this->events();
        //     case 'facebook':                    return $this->facebook();
        //     case 'helpers':                     return $this->helpers();
        //     case 'passwords':                   return $this->passwords();
        //     case 'products':                    return $this->products();
        //     case 'replication':                 return $this->replication();
        //     case 'reservations':                return $this->reservations();
        //     case 'screenTemplate':              return $this->screenTemplate();
        //     case 'screenTemplateDetail':        return $this->screenTemplateDetail();
        //     case 'taxes':                       return $this->taxes();
        //     case 'translations':                return $this->translations();
        //     case 'users':                       return $this->users();
        //     default:                            throw new \CSException("Attempted to access an invalid CSLogic subclass! Received: " . $prop);
        // }
    }

    private function load($prop) {
        $prop = '\ClubSpeed\Logic\\' . ucfirst($prop) . 'Logic'; // hacky -- we can go back to the old way detailed below, if desired
        if (!isset($this->_lazy[$prop]))
            $this->_lazy[$prop] = new $prop($this, $this->db);
        return $this->_lazy[$prop];
    }
}