<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'/cs-booking.php');
require_once(__DIR__.'/cs-checks.php');
require_once(__DIR__.'/cs-customers.php');
require_once(__DIR__.'/cs-events.php');
require_once(__DIR__.'/cs-exceptions.php');
require_once(__DIR__.'/cs-facebook.php');
require_once(__DIR__.'/cs-helpers.php');
require_once(__DIR__.'/cs-passwords.php');
require_once(__DIR__.'/cs-replication.php');
require_once(__DIR__.'/cs-reservations.php');
require_once(__DIR__.'/cs-screen-template.php');
require_once(__DIR__.'/cs-screen-template-detail.php');
require_once(__DIR__.'/cs-users.php');

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
     * A reference to a instantiated CSBooking class,
     * which contains database interface methods for online booking.
     */
    public $booking;

    /**
     * A reference to a instantiated CSChecks class,
     * which contains database interface methods for checks.
     */
    public $checks;

    /**
     * A reference to a instantiated CSCustomers class,
     * which contains database interface methods for customers.
     */
    public $customers;

    /**
     * A reference to a instantiated CSEvents class,
     * which contains database interface methods for events.
     */
    public $events;

    /**
     * A reference to a instantiated CSFacebook class,
     * which contains database interface methods for facebook customers.
     */
    public $facebook;

    /**
     * A reference to a instantiated CSHelpers class,
     * which contains database interface methods for helper functions.
     */
    public $helpers;

    /**
     * A reference to a instantiated CSPasswords class,
     * which contains database interface methods for passwords.
     */
    public $passwords;

    /**
     * A reference to a instantiated CSReplication class,
     * which contains database interface methods for replication.
     */
    public $replication;

    /**
     * A reference to a instantiated CSReservations class,
     * which contains database interface methods for online booking reservations.
     */
    public $reservations;

    /**
     * A reference to a instantiated CSScreenTemplate class,
     * which contains database interface methods for screen templates.
     */
    public $screenTemplate;

    /**
     * A reference to a instantiated CSScreenTemplateDetail class,
     * which contains database interface methods for screen template detail.
     */
    public $screenTemplateDetail;

    /**
     * A reference to a instantiated CSUsers class,
     * which contains database interface methods for users.
     */
    public $users;
    
    /**
     * Constructs a new instance of the CSBooking class.
     *
     * The CSBooking constructor requires an instantiated CSConnection class for injection.
     *
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSDatabase) {
        $this->db                   = $CSDatabase;
        $this->booking              = new \ClubSpeed\Business\CSBooking($this, $this->db);
        $this->checks               = new \ClubSpeed\Business\CSChecks($this, $this->db);
        $this->customers            = new \ClubSpeed\Business\CSCustomers($this, $this->db);
        $this->events               = new \ClubSpeed\Business\CSEvents($this, $this->db);
        $this->facebook             = new \ClubSpeed\Business\CSFacebook($this, $this->db);
        $this->helpers              = new \ClubSpeed\Business\CSHelpers($this, $this->db);
        $this->passwords            = new \ClubSpeed\Business\CSPasswords($this, $this->db);
        $this->replication          = new \ClubSpeed\Business\CSReplication($this, $this->db);
        $this->reservations         = new \ClubSpeed\Business\CSReservations($this, $this->db);
        $this->screenTemplate       = new \ClubSpeed\Business\CSScreenTemplate($this, $this->db);
        $this->screenTemplateDetail = new \ClubSpeed\Business\CSScreenTemplateDetail($this, $this->db);
        $this->users                = new \ClubSpeed\Business\CSUsers($this, $this->db);
    }
}