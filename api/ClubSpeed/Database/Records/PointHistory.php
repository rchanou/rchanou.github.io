<?php

namespace ClubSpeed\Database\Records;

class PointHistory extends BaseRecord {
    protected static $_definition;

    public $PointHistoryID;
    public $CustID;
    public $CheckID;
    public $UserID;
    public $ReferenceID;
    public $PointAmount;
    public $Type;
    public $PointDate;
    public $PointExpDate;
    public $Notes;
    public $RefPointHistoryID;
    public $IPAddress;
    public $IsManual;
    public $CheckDetailID;
    public $ReservationID;
    public $Username;
    public $ApprovedByUserName;
}