<?php

namespace ClubSpeed\Database;

/**
 * The ClubSpeed database object 
 * used to contain all underlying DbCollections.
 */
class DbService {

    private $conn;
    private $connResource;
    private $connLogs;
    private $_lazy;

    public function __construct(&$CSConnection, &$ResourceConnection, &$LogConnection) {
        // should this secondary connection be in here, or should we have a separate csdatabase-style instance for the resource items?
        // note that if we don't inject two connections here, we will still have to inject two databases into the logic class anyways
        // also note that if we do not separate the database classes, then passthrough direct methods (such as query and exec) will always to go ClubSpeedV8

        $this->conn = $CSConnection;
        $this->connResource = $ResourceConnection;
        $this->connLogs = $LogConnection;
        $this->_lazy = array();
    }

    function __get($prop) {

        return $this->load($prop);

        // switch($prop) {
        //     case 'authenticationTokens':        return $this->authenticationTokens();
        //     case 'checks':                      return $this->checks();
        //     case 'checkDetails':                return $this->checkDetails();
        //     case 'checkTotals':                 return $this->checkTotals_V();
        //     case 'controlPanel':                return $this->controlPanel();
        //     case 'customers':                   return $this->customers();
        //     case 'onlineBookings':              return $this->onlineBookings();
        //     case 'onlineBookingAvailability':   return $this->onlineBookingAvailability();
        //     case 'onlineBookingReservations':   return $this->onlineBookingReservations();
        //     case 'products':                    return $this->products();
        //     case 'screenTemplate':              return $this->screenTemplate();
        //     case 'screenTemplateDetail':        return $this->screenTemplateDetail();
        //     case 'resourceSets':                return $this->resourceSets();
        //     case 'taxes':                       return $this->taxes();
        //     case 'users':                       return $this->users();
        //     default:                            throw new \CSException("Attempted to access an invalid CSDatabase subclass! Received: " . $prop);
        // }
    }

    private function load($prop) {
        switch(strtolower($prop)) {
            // hacky way to select the right connection
            // will split into 3 separate contexts when/if time allows
            case 'resourcesets':
                $conn =& $this->connResource; // needs to use the resource connection injection
                $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
                break;
            case 'logs':
                $conn =& $this->connLogs; // needs to use the logs connection injection
                $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
                break;
            case 'fb_customers_new':
                $conn =& $this->conn;
                $record = __NAMESPACE__ . '\Records\\FB_Customers_New'; // doesn't match the table naming scheme, load explicitly
                break;
            default:
                $conn =& $this->conn;
                $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
                break;
        }
        // $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
        if (!isset($this->_lazy[$prop])) {
            $this->_lazy[$prop] = new DbCollection($conn, $record);
        }
        return $this->_lazy[$prop];
    }

    // public function authenticationTokens() {
    //     if (!isset($this->_lazy['authenticationTokens'])) {
    //         require_once(__DIR__.'/Collections/DbAuthenticationTokens.php');
    //         $this->_lazy['authenticationTokens'] = new \ClubSpeed\Database\Collections\DbAuthenticationTokens($this->conn);
    //     }
    //     return $this->_lazy['authenticationTokens'];
    // }

    // public function checks() {
    //     if (!isset($this->_lazy['checks'])) {
    //         require_once(__DIR__.'/Collections/DbChecks.php');
    //         $this->_lazy['checks'] = new \ClubSpeed\Database\Collections\DbChecks($this->conn);
    //     }
    //     return $this->_lazy['checks'];
    // }

    // public function checkDetails() {
    //     if (!isset($this->_lazy['checkDetails'])) {
    //         require_once(__DIR__.'/Collections/DbCheckDetails.php');
    //         $this->_lazy['checkDetails'] = new \ClubSpeed\Database\Collections\DbCheckDetails($this->conn);
    //     }
    //     return $this->_lazy['checkDetails'];
    // }

    // public function checkTotals() {
    //     if (!isset($this->_lazy['checkTotals'])) {
    //         require_once(__DIR__.'/Collections/DbCheckTotals_V.php');
    //         $this->_lazy['checkTotals'] = new \ClubSpeed\Database\Collections\DbCheckTotals_V($this->conn);
    //     }
    //     return $this->_lazy['checkTotals'];
    // }

    // public function controlPanel() {
    //     if (!isset($this->_lazy['controlPanel'])) {
    //         require_once(__DIR__.'/Collections/DbControlPanel.php');
    //         $this->_lazy['controlPanel'] = new \ClubSpeed\Database\Collections\DbControlPanel($this->conn);
    //     }
    //     return $this->_lazy['controlPanel'];
    // }

    // public function customers() {
    //     if (!isset($this->_lazy['customers'])) {
    //         require_once(__DIR__.'/Collections/DbCustomers.php');
    //         $this->_lazy['customers'] = new \ClubSpeed\Database\Collections\DbCustomers($this->conn);
    //     }
    //     return $this->_lazy['customers'];
    // }

    // public function onlineBookings() {
    //     if (!isset($this->_lazy['onlineBookings'])) {
    //         require_once(__DIR__.'/Collections/DbOnlineBookings.php');
    //         $this->_lazy['onlineBookings'] = new \ClubSpeed\Database\Collections\DbOnlineBookings($this->conn);
    //     }
    //     return $this->_lazy['onlineBookings'];
    // }

    // public function onlineBookingAvailability() {
    //     if (!isset($this->_lazy['onlineBookingAvailability'])) {
    //         require_once(__DIR__.'/Collections/DbOnlineBookingAvailability_V.php');
    //         $this->_lazy['onlineBookingAvailability'] = new \ClubSpeed\Database\Collections\DbOnlineBookingAvailability_V($this->conn);
    //     }
    //     return $this->_lazy['onlineBookingAvailability'];
    // }

    // public function onlineBookingReservations() {
    //     if (!isset($this->_lazy['onlineBookingReservations'])) {
    //         require_once(__DIR__.'/Collections/DbOnlineBookingReservations.php');
    //         $this->_lazy['onlineBookingReservations'] = new \ClubSpeed\Database\Collections\DbOnlineBookingReservations($this->conn);
    //     }
    //     return $this->_lazy['onlineBookingReservations'];
    // }

    // public function products() {
    //     if (!isset($this->_lazy['products'])) {
    //         require_once(__DIR__.'/Collections/DbProducts.php');
    //         $this->_lazy['products'] = new \ClubSpeed\Database\Collections\DbProducts($this->conn);
    //     }
    //     return $this->_lazy['products'];
    // }
    
    // public function screenTemplate() {
    //     if (!isset($this->_lazy['screenTemplate'])) {
    //         require_once(__DIR__.'/Collections/DbScreenTemplate.php');
    //         $this->_lazy['screenTemplate'] = new \ClubSpeed\Database\Collections\DbScreenTemplate($this->conn);
    //     }
    //     return $this->_lazy['screenTemplate'];
    // }

    // public function screenTemplateDetail() {
    //     if (!isset($this->_lazy['screenTemplateDetail'])) {
    //         require_once(__DIR__.'/Collections/DbScreenTemplateDetail.php');
    //         $this->_lazy['screenTemplateDetail'] = new \ClubSpeed\Database\Collections\DbScreenTemplateDetail($this->conn);
    //     }
    //     return $this->_lazy['screenTemplateDetail'];
    // }

    // public function resourceSets() {
    //     if (!isset($this->_lazy['resourceSets'])) {
    //         require_once(__DIR__.'/Collections/DbResourceSets.php');
    //         $this->_lazy['resourceSets'] = new \ClubSpeed\Database\Collections\DbResourceSets($this->connResource);
    //     }
    //     return $this->_lazy['resourceSets'];
    // }

    // public function taxes() {
    //     if (!isset($this->_lazy['taxes'])) {
    //         require_once(__DIR__.'/Collections/DbTaxes.php');
    //         $this->_lazy['taxes'] = new \ClubSpeed\Database\Collections\DbTaxes($this->conn);
    //     }
    //     return $this->_lazy['taxes'];
    // }

    // public function users() {
    //     if (!isset($this->_lazy['users'])) {
    //         require_once(__DIR__.'/Collections/DbUsers.php');
    //         $this->_lazy['users'] = new \ClubSpeed\Database\Collections\DbUsers($this->conn);
    //     }
    //     return $this->_lazy['users'];
    // }

    public function query($sql, $params = array()) {
        return $this->conn->query($sql, $params); // pass through
    }

    public function exec($sql, $params = array()) {
        return $this->conn->exec($sql, $params); // pass through
    }
}