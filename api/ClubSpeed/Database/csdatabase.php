<?php

namespace ClubSpeed\Database;
require_once(__DIR__.'/Collections/DbAuthenticationTokens.php');
require_once(__DIR__.'/Collections/DbCustomers.php');
require_once(__DIR__.'/Collections/DbOnlineBookings.php');
require_once(__DIR__.'/Collections/DbOnlineBookingAvailability_V.php');
require_once(__DIR__.'/Collections/DbOnlineBookingReservations.php');
require_once(__DIR__.'/Collections/DbScreenTemplate.php');
require_once(__DIR__.'/Collections/DbScreenTemplateDetail.php');

/**
 * The ClubSpeed database object 
 * used to contain all underlying DbCollections.
 */
class CSDatabase {

    private $conn;

    public $authenticationTokens;
    public $customers;
    public $onlineBookings;
    public $onlineBookingReservations;
    public $screenTemplate;
    public $screenTemplateDetail;

    public function __construct(&$CSConnection) {
        $this->conn                         = $CSConnection;
        $this->authenticationTokens         = new \ClubSpeed\Database\Collections\DbAuthenticationTokens($this->conn);
        $this->customers                    = new \ClubSpeed\Database\Collections\DbCustomers($this->conn);
        $this->onlineBookings               = new \ClubSpeed\Database\Collections\DbOnlineBookings($this->conn);
        $this->onlineBookingAvailability    = new \ClubSpeed\Database\Collections\DbOnlineBookingAvailability_V($this->conn);
        $this->onlineBookingReservations    = new \ClubSpeed\Database\Collections\DbOnlineBookingReservations($this->conn);
        $this->screenTemplate               = new \ClubSpeed\Database\Collections\DbScreenTemplate($this->conn);
        $this->screenTemplateDetail         = new \ClubSpeed\Database\Collections\DbScreenTemplateDetail($this->conn);
    }

    public function query($sql, $params = array()) {
        return $this->conn->query($sql, $params); // pass through
    }

    public function exec($sql, $params = array()) {
        return $this->conn->exec($sql, $params); // pass through
    }
}