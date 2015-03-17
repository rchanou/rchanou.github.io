<?php

namespace ClubSpeed\Database\Records;

class OnlineBookingAvailability_V extends BaseRecord {
    protected static $_definition;

    public $HeatDescription;
    public $HeatEndsAt;
    public $HeatNo;
    public $HeatSpotsAvailableCombined;
    public $HeatSpotsAvailableOnline;
    public $HeatSpotsTotalActual;
    public $HeatStartsAt;
    public $HeatTypeNo;
    public $IsPublic;
    public $OnlineBookingsID;
    public $Price1;
    public $ProductType;
    public $ProductDescription;
    public $ProductsID;
    public $ProductSpotsAvailableOnline;
    public $ProductSpotsTotal;
    public $ProductSpotsUsed;
}