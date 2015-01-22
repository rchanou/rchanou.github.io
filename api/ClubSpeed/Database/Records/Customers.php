<?php

namespace ClubSpeed\Database\Records;

class Customers extends BaseRecord {

    public static $table = 'dbo.Customers';
    public static $tableAlias = 'c';
    public static $key = 'CustID';

    public $CustID;
    public $Company;
    public $FName;
    public $LName;
    public $RacerName;
    public $BirthDate;
    public $IgnoreDOB;
    public $Gender;
    public $EmailAddress;
    public $SourceID;
    public $Hotel;
    public $IndustryID;
    public $RefID;
    public $DoNotMail;
    public $Address;
    public $Address2;
    public $City;
    public $State;
    public $Zip;
    public $Country;
    public $PhoneNumber;
    public $PhoneNumber2;
    public $Cell;
    public $Fax;
    public $LicenseNumber;
    public $IssuedBy;
    public $Waiver;
    public $Waiver2;
    public $CrdID;
    public $RPM;
    public $AccountCreated;
    public $LastVisited;
    public $TotalVisits;
    public $TotalRaces;
    public $MembershipStatus;
    public $MembershipText;
    public $MemberShipTextLong;
    public $PriceLevel;
    public $PromotionCode;
    public $IsGiftCard;
    public $WebUserName;
    public $Password;
    public $Award1;
    public $Award2;
    public $Custom1;
    public $Custom2;
    public $Custom3;
    public $Custom4;
    public $Privacy1;
    public $Privacy2;
    public $Privacy3;
    public $Privacy4;
    public $Status1;
    public $Status2;
    public $Status3;
    public $Status4;
    public $Deleted;
    public $IsEmployee;
    public $OriginalID;
    public $CreditLimit;
    public $CreditOnHold;
    // public $LastUnSubscribedDate;
    public $GeneralNotes;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CustID']))                 $this->CustID               = \ClubSpeed\Utility\Convert::toNumber          ($data['CustID']);
                    if (isset($data['Company']))                $this->Company              = \ClubSpeed\Utility\Convert::toString          ($data['Company']);
                    if (isset($data['FName']))                  $this->FName                = \ClubSpeed\Utility\Convert::toString          ($data['FName']);
                    if (isset($data['LName']))                  $this->LName                = \ClubSpeed\Utility\Convert::toString          ($data['LName']);
                    if (isset($data['RacerName']))              $this->RacerName            = \ClubSpeed\Utility\Convert::toString          ($data['RacerName']);
                    if (isset($data['BirthDate']))              $this->BirthDate            = \ClubSpeed\Utility\Convert::toDateForServer   ($data['BirthDate']);
                    if (isset($data['IgnoreDOB']))              $this->IgnoreDOB            = \ClubSpeed\Utility\Convert::toBoolean         ($data['IgnoreDOB']);
                    if (isset($data['Gender']))                 $this->Gender               = \ClubSpeed\Utility\Convert::toNumber          ($data['Gender']);
                    if (isset($data['EmailAddress']))           $this->EmailAddress         = \ClubSpeed\Utility\Convert::toString          ($data['EmailAddress']);
                    if (isset($data['SourceID']))               $this->SourceID             = \ClubSpeed\Utility\Convert::toNumber          ($data['SourceID']);
                    if (isset($data['Hotel']))                  $this->Hotel                = \ClubSpeed\Utility\Convert::toString          ($data['Hotel']);
                    if (isset($data['IndustryID']))             $this->IndustryID           = \ClubSpeed\Utility\Convert::toNumber          ($data['IndustryID']);
                    if (isset($data['RefID']))                  $this->RefID                = \ClubSpeed\Utility\Convert::toNumber          ($data['RefID']);
                    if (isset($data['DoNotMail']))              $this->DoNotMail            = \ClubSpeed\Utility\Convert::toBoolean         ($data['DoNotMail']);
                    if (isset($data['Address']))                $this->Address              = \ClubSpeed\Utility\Convert::toString          ($data['Address']);
                    if (isset($data['Address2']))               $this->Address2             = \ClubSpeed\Utility\Convert::toString          ($data['Address2']);
                    if (isset($data['City']))                   $this->City                 = \ClubSpeed\Utility\Convert::toString          ($data['City']);
                    if (isset($data['State']))                  $this->State                = \ClubSpeed\Utility\Convert::toString          ($data['State']);
                    if (isset($data['Zip']))                    $this->Zip                  = \ClubSpeed\Utility\Convert::toString          ($data['Zip']);
                    if (isset($data['Country']))                $this->Country              = \ClubSpeed\Utility\Convert::toString          ($data['Country']);
                    if (isset($data['PhoneNumber']))            $this->PhoneNumber          = \ClubSpeed\Utility\Convert::toString          ($data['PhoneNumber']);
                    if (isset($data['PhoneNumber2']))           $this->PhoneNumber2         = \ClubSpeed\Utility\Convert::toString          ($data['PhoneNumber2']);
                    if (isset($data['Cell']))                   $this->Cell                 = \ClubSpeed\Utility\Convert::toString          ($data['Cell']);
                    if (isset($data['Fax']))                    $this->Fax                  = \ClubSpeed\Utility\Convert::toString          ($data['Fax']);
                    if (isset($data['LicenseNumber']))          $this->LicenseNumber        = \ClubSpeed\Utility\Convert::toString          ($data['LicenseNumber']);
                    if (isset($data['IssuedBy']))               $this->IssuedBy             = \ClubSpeed\Utility\Convert::toString          ($data['IssuedBy']);
                    if (isset($data['Waiver']))                 $this->Waiver               = \ClubSpeed\Utility\Convert::toNumber          ($data['Waiver']);
                    if (isset($data['Waiver2']))                $this->Waiver2              = \ClubSpeed\Utility\Convert::toNumber          ($data['Waiver2']);
                    if (isset($data['CrdID']))                  $this->CrdID                = \ClubSpeed\Utility\Convert::toNumber          ($data['CrdID']);
                    if (isset($data['RPM']))                    $this->RPM                  = \ClubSpeed\Utility\Convert::toNumber          ($data['RPM']);
                    if (isset($data['AccountCreated']))         $this->AccountCreated       = \ClubSpeed\Utility\Convert::toDateForServer   ($data['AccountCreated']);
                    if (isset($data['LastVisited']))            $this->LastVisited          = \ClubSpeed\Utility\Convert::toDateForServer   ($data['LastVisited']);
                    if (isset($data['TotalVisits']))            $this->TotalVisits          = \ClubSpeed\Utility\Convert::toNumber          ($data['TotalVisits']);
                    if (isset($data['TotalRaces']))             $this->TotalRaces           = \ClubSpeed\Utility\Convert::toNumber          ($data['TotalRaces']);
                    if (isset($data['MembershipStatus']))       $this->MembershipStatus     = \ClubSpeed\Utility\Convert::toNumber          ($data['MembershipStatus']);
                    if (isset($data['MembershipText']))         $this->MembershipText       = \ClubSpeed\Utility\Convert::toString          ($data['MembershipText']);
                    if (isset($data['MemberShipTextLong']))     $this->MemberShipTextLong   = \ClubSpeed\Utility\Convert::toString          ($data['MemberShipTextLong']);
                    if (isset($data['PriceLevel']))             $this->PriceLevel           = \ClubSpeed\Utility\Convert::toNumber          ($data['PriceLevel']);
                    if (isset($data['PromotionCode']))          $this->PromotionCode        = \ClubSpeed\Utility\Convert::toString          ($data['PromotionCode']);
                    if (isset($data['IsGiftCard']))             $this->IsGiftCard           = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsGiftCard']);
                    if (isset($data['WebUserName']))            $this->WebUserName          = \ClubSpeed\Utility\Convert::toString          ($data['WebUserName']);
                    if (isset($data['Password']))               $this->Password             = \ClubSpeed\Utility\Convert::toString          ($data['Password']);
                    if (isset($data['Award1']))                 $this->Award1               = \ClubSpeed\Utility\Convert::toNumber          ($data['Award1']);
                    if (isset($data['Award2']))                 $this->Award2               = \ClubSpeed\Utility\Convert::toNumber          ($data['Award2']);
                    if (isset($data['Custom1']))                $this->Custom1              = \ClubSpeed\Utility\Convert::toString          ($data['Custom1']);
                    if (isset($data['Custom2']))                $this->Custom2              = \ClubSpeed\Utility\Convert::toString          ($data['Custom2']);
                    if (isset($data['Custom3']))                $this->Custom3              = \ClubSpeed\Utility\Convert::toString          ($data['Custom3']);
                    if (isset($data['Custom4']))                $this->Custom4              = \ClubSpeed\Utility\Convert::toString          ($data['Custom4']);
                    if (isset($data['Privacy1']))               $this->Privacy1             = \ClubSpeed\Utility\Convert::toBoolean         ($data['Privacy1']);
                    if (isset($data['Privacy2']))               $this->Privacy2             = \ClubSpeed\Utility\Convert::toBoolean         ($data['Privacy2']);
                    if (isset($data['Privacy3']))               $this->Privacy3             = \ClubSpeed\Utility\Convert::toBoolean         ($data['Privacy3']);
                    if (isset($data['Privacy4']))               $this->Privacy4             = \ClubSpeed\Utility\Convert::toBoolean         ($data['Privacy4']);
                    if (isset($data['Status1']))                $this->Status1              = \ClubSpeed\Utility\Convert::toNumber          ($data['Status1']);
                    if (isset($data['Status2']))                $this->Status2              = \ClubSpeed\Utility\Convert::toNumber          ($data['Status2']);
                    if (isset($data['Status3']))                $this->Status3              = \ClubSpeed\Utility\Convert::toNumber          ($data['Status3']);
                    if (isset($data['Status4']))                $this->Status4              = \ClubSpeed\Utility\Convert::toNumber          ($data['Status4']);
                    if (isset($data['Deleted']))                $this->Deleted              = \ClubSpeed\Utility\Convert::toBoolean         ($data['Deleted']);
                    if (isset($data['IsEmployee']))             $this->IsEmployee           = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsEmployee']);
                    if (isset($data['OriginalID']))             $this->OriginalID           = \ClubSpeed\Utility\Convert::toNumber          ($data['OriginalID']);
                    if (isset($data['CreditLimit']))            $this->CreditLimit          = \ClubSpeed\Utility\Convert::toNumber          ($data['CreditLimit']);
                    if (isset($data['CreditOnHold']))           $this->CreditOnHold         = \ClubSpeed\Utility\Convert::toNumber          ($data['CreditOnHold']);
                    // if (isset($data['LastUnSubscribedDate']))   $this->LastUnSubscribedDate = \ClubSpeed\Utility\Convert::toDateForServer   ($data['LastUnSubscribedDate']);
                    if (isset($data['GeneralNotes']))           $this->GeneralNotes         = \ClubSpeed\Utility\Convert::toString          ($data['GeneralNotes']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    public function validate($type) {
        switch (strtolower($type)) {
            case 'insert':
                
                break;
        }
    }
}