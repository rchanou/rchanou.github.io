<?php

namespace ClubSpeed\Database;

/**
 * The ClubSpeed database object 
 * used to contain all underlying DbCollections.
 */
class CSDatabase {

    private $conn;
    private $connResource;
    private $_lazy;

    public function __construct(&$CSConnection, &$ResourceConnection) {
        // should this secondary connection be in here, or should we have a separate csdatabase-style instance for the resource items?
        // note that if we don't inject two connections here, we will still have to inject two databases into the logic class anyways
        // also note that if we do not separate the database classes, then passthrough direct methods (such as query and exec) will always to go ClubSpeedV8

        $this->conn = $CSConnection;
        $this->connResource = $ResourceConnection;
        $this->_lazy = array();
    }

    function __get($prop) {
        switch($prop) {
            case 'authenticationTokens':        return $this->authenticationTokens();
            case 'checks':                      return $this->checks();
            case 'customers':                   return $this->customers();
            case 'onlineBookings':              return $this->onlineBookings();
            case 'onlineBookingAvailability':   return $this->onlineBookingAvailability();
            case 'onlineBookingReservations':   return $this->onlineBookingReservations();
            case 'screenTemplate':              return $this->screenTemplate();
            case 'screenTemplateDetail':        return $this->screenTemplateDetail();
            case 'resourceSets':                return $this->resourceSets();
            default:                            throw new \CSException("Attempted to access an invalid CSDatabase subclass! Received: " . $prop);
        }
    }

    public function authenticationTokens() {
        if (!isset($this->_lazy['authenticationTokens'])) {
            require_once(__DIR__.'/Collections/DbAuthenticationTokens.php');
            $this->_lazy['authenticationTokens'] = new \ClubSpeed\Database\Collections\DbAuthenticationTokens($this->conn);
        }
        return $this->_lazy['authenticationTokens'];
    }

    public function checks() {
        if (!isset($this->_lazy['checks'])) {
            require_once(__DIR__.'/Collections/DbChecks.php');
            $this->_lazy['checks'] = new \ClubSpeed\Database\Collections\DbChecks($this->conn);
        }
        return $this->_lazy['checks'];
    }

    public function customers() {
        if (!isset($this->_lazy['customers'])) {
            require_once(__DIR__.'/Collections/DbCustomers.php');
            $this->_lazy['customers'] = new \ClubSpeed\Database\Collections\DbCustomers($this->conn);
        }
        return $this->_lazy['customers'];
    }

    public function onlineBookings() {
        if (!isset($this->_lazy['onlineBookings'])) {
            require_once(__DIR__.'/Collections/DbOnlineBookings.php');
            $this->_lazy['onlineBookings'] = new \ClubSpeed\Database\Collections\DbOnlineBookings($this->conn);
        }
        return $this->_lazy['onlineBookings'];
    }

    public function onlineBookingAvailability() {
        if (!isset($this->_lazy['onlineBookingAvailability'])) {
            require_once(__DIR__.'/Collections/DbOnlineBookingAvailability_V.php');
            $this->_lazy['onlineBookingAvailability'] = new \ClubSpeed\Database\Collections\DbOnlineBookingAvailability_V($this->conn);
        }
        return $this->_lazy['onlineBookingAvailability'];
    }

    public function onlineBookingReservations() {
        if (!isset($this->_lazy['onlineBookingReservations'])) {
            require_once(__DIR__.'/Collections/DbOnlineBookingReservations.php');
            $this->_lazy['onlineBookingReservations'] = new \ClubSpeed\Database\Collections\DbOnlineBookingReservations($this->conn);
        }
        return $this->_lazy['onlineBookingReservations'];
    }
    
    public function screenTemplate() {
        if (!isset($this->_lazy['screenTemplate'])) {
            require_once(__DIR__.'/Collections/DbScreenTemplate.php');
            $this->_lazy['screenTemplate'] = new \ClubSpeed\Database\Collections\DbScreenTemplate($this->conn);
        }
        return $this->_lazy['screenTemplate'];
    }

    public function screenTemplateDetail() {
        if (!isset($this->_lazy['screenTemplateDetail'])) {
            require_once(__DIR__.'/Collections/DbScreenTemplateDetail.php');
            $this->_lazy['screenTemplateDetail'] = new \ClubSpeed\Database\Collections\DbScreenTemplateDetail($this->conn);
        }
        return $this->_lazy['screenTemplateDetail'];
    }

    public function resourceSets() {
        if (!isset($this->_lazy['resourceSets'])) {
            require_once(__DIR__.'/Collections/DbResourceSets.php');
            $this->_lazy['resourceSets'] = new \ClubSpeed\Database\Collections\DbResourceSets($this->connResource);
        }
        return $this->_lazy['resourceSets'];
    }

    public function query($sql, $params = array()) {
        return $this->conn->query($sql, $params); // pass through
    }

    public function exec($sql, $params = array()) {
        return $this->conn->exec($sql, $params); // pass through
    }
}