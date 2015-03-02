<?php

use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Security\Authenticate;
use ClubSpeed\Utility\Convert;

class Tests {

    protected $db;
    protected $logic;

    function __construct() {
        $this->db = $GLOBALS['db'];
        $this->logic = $GLOBALS['logic'];
    }

    /**
     * @url GET /
     */
    public function get() {
        if (!Authenticate::privateAccess())
            throw new RestException(403, "Invalid authorization!");
        // $date = Convert::getDate();
        // $date2 = Convert::toDateForServer($date);
        // pr($date);
        // print_r("\n");
        // pr($date2);
        // print_r("\n");

        // $payment = $this->logic->payment->get(3678);
        // $payment = $payment[0];
        // $payment = (array)$payment;
        // $payment = $this->db->payment->dummy($payment);
        // pr($payment);
        // print_r("\n");

        // $this->logic->payment->update(3678, $payment);

        // $payment = $this->logic->payment->get(3678);
        // $payment = $payment[0];
        // $payment = (array)$payment;
        // pr($payment);
        // print_r("\n");
    }
}