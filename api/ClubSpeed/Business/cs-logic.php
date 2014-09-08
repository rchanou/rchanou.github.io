<?php

namespace ClubSpeed\Business;

/**
 * The database interface class
 * for ClubSpeed online booking.
 */
class CSLogic {

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
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSDatabase) {
        $this->db = $CSDatabase;
        $this->_lazy = array();
    }

    // this structure works fine, but requires us to call functions instead of referencing properties
    // this may be bad practice (!!!) make sure we test this --
    // if all else fails, bypass the __get, and call the methods externally instead of the properties
    function __get($prop) {
        switch($prop) {
            case 'booking':                 return $this->booking();
            case 'checks':                  return $this->checks();
            case 'customers':               return $this->customers();
            case 'events':                  return $this->events();
            case 'facebook':                return $this->facebook();
            case 'helpers':                 return $this->helpers();
            case 'passwords':               return $this->passwords();
            case 'replication':             return $this->replication();
            case 'reservations':            return $this->reservations();
            case 'screenTemplate':          return $this->screenTemplate();
            case 'screenTemplateDetail':    return $this->screenTemplateDetail();
            case 'translations':            return $this->translations();
            case 'users':                   return $this->users();
            default:                        throw new \CSException("Attempted to access an invalid CSLogic subclass! Received: " . $prop);
        }
    }


    /**
     * A lazy-loading reference to a instantiated CSBooking class
     * which contains database interface methods for online booking.
     */
    public function booking() {
        if (!isset($this->_lazy['booking'])) {
            require_once(__DIR__.'/cs-booking.php');
            $this->_lazy['booking'] = new \ClubSpeed\Business\CSBooking($this, $this->db);
        }
        return $this->_lazy['booking'];
    }

     /**
     * A lazy-loading reference to the CSChecks class
     * which contains database interface methods for checks.
     */
    public function checks() {
        if (!isset($this->_lazy['checks'])) {
            require_once(__DIR__.'/cs-checks.php');
            $this->_lazy['checks'] = new \ClubSpeed\Business\CSChecks($this, $this->db);
        }
        return $this->_lazy['checks'];
    }

    /**
     * A lazy-loading reference to the CSCustomers class
     * which contains database interface methods for customers.
     */
    public function customers() {
        if (!isset($this->_lazy['customers'])) {
            require_once(__DIR__.'/cs-customers.php');
            $this->_lazy['customers'] = new \ClubSpeed\Business\CSCustomers($this, $this->db);
        }
        return $this->_lazy['customers'];
    }

    /**
     * A lazy-loading reference to the CSEvents class
     * which contains database interface methods for events.
     */
    public function events() {
        if (!isset($this->_lazy['events'])) {
            require_once(__DIR__.'/cs-events.php');
            $this->_lazy['events'] = new \ClubSpeed\Business\CSEvents($this, $this->db);
        }
        return $this->_lazy['events'];
    }

    /**
     * A lazy-loading reference to the CSFacebook class
     * which contains database interface methods for facebook.
     */
    public function facebook() {
        if (!isset($this->_lazy['facebook'])) {
            require_once(__DIR__.'/cs-facebook.php');
            $this->_lazy['facebook'] = new \ClubSpeed\Business\CSFacebook($this, $this->db);
        }
        return $this->_lazy['facebook'];
    }

    /**
     * A lazy-loading reference to the CSHelpers class
     * which contains database interface methods for helper methods.
     */
    public function helpers() {
        if (!isset($this->_lazy['helpers'])) {
            require_once(__DIR__.'/cs-helpers.php');
            $this->_lazy['helpers'] = new \ClubSpeed\Business\CSHelpers($this, $this->db);
        }
        return $this->_lazy['helpers'];
    }

    /**
     * A lazy-loading reference to the CSPasswords class
     * which contains database interface methods for passwords.
     */
    public function passwords() {
        if (!isset($this->_lazy['passwords'])) {
            require_once(__DIR__.'/cs-passwords.php');
            $this->_lazy['passwords'] = new \ClubSpeed\Business\CSPasswords($this, $this->db);
        }
        return $this->_lazy['passwords'];
    }

    /**
     * A lazy-loading reference to the CSReplication class
     * which contains database interface methods for replication.
     */
    public function replication() {
        if (!isset($this->_lazy['replication'])) {
            require_once(__DIR__.'/cs-replication.php');
            $this->_lazy['replication'] = new \ClubSpeed\Business\CSReplication($this, $this->db);
        }
        return $this->_lazy['replication'];
    }

    /**
     * A lazy-loading reference to the CSReservations class
     * which contains database interface methods for reservations.
     */
    public function reservations() {
        if (!isset($this->_lazy['reservations'])) {
            require_once(__DIR__.'/cs-reservations.php');
            $this->_lazy['reservations'] = new \ClubSpeed\Business\CSReservations($this, $this->db);
        }
        return $this->_lazy['reservations'];
    }

    /**
     * A lazy-loading reference to the CSScreenTemplate class
     * which contains database interface methods for screen templates.
     */
    public function screenTemplate() {
        if (!isset($this->_lazy['screenTemplate'])) {
            require_once(__DIR__.'/cs-screen-template.php');
            $this->_lazy['screenTemplate'] = new \ClubSpeed\Business\CSScreenTemplate($this, $this->db);
        }
        return $this->_lazy['screenTemplate'];
    }

    /**
     * A lazy-loading reference to the CSScreenTemplateDetail class
     * which contains database interface methods for screen template details.
     */
    public function screenTemplateDetail() {
        if (!isset($this->_lazy['screenTemplateDetail'])) {
            require_once(__DIR__.'/cs-screen-template-detail.php');
            $this->_lazy['screenTemplateDetail'] = new \ClubSpeed\Business\CSScreenTemplateDetail($this, $this->db);
        }
        return $this->_lazy['screenTemplateDetail'];
    }

    /**
     * A lazy-loading reference to the CSTranslations class
     * which contains database interface methods for translations and resources.
     */
    public function translations() {
        if (!isset($this->_lazy['translations'])) {
            require_once(__DIR__.'/cs-translations.php');
            $this->_lazy['translations'] = new \ClubSpeed\Business\CSTranslations($this, $this->db);
        }
        return $this->_lazy['translations'];
    }

    /**
     * A lazy-loading reference to the CSUsers class
     * which contains database interface methods for users.
     */
    public function users() {
        if (!isset($this->_lazy['users'])) {
            require_once(__DIR__.'/cs-users.php');
            $this->_lazy['users'] = new \ClubSpeed\Business\CSUsers($this, $this->db);
        }
        return $this->_lazy['users'];
    }
}