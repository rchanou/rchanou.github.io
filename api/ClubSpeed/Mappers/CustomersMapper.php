<?php

namespace ClubSpeed\Mappers;

class CustomersMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'customers';
        $this->register(array(
              'CustID'               => 'customerId'
            , 'Company'              => ''
            , 'FName'                => 'firstname'
            , 'LName'                => 'lastname'
            , 'RacerName'            => 'racername'
            , 'BirthDate'            => 'birthdate'
            , 'IgnoreDOB'            => ''
            , 'Gender'               => 'gender'
            , 'EmailAddress'         => 'email'
            , 'SourceID'             => 'howdidyouhearaboutus'
            , 'Hotel'                => ''
            , 'IndustryID'           => ''
            , 'RefID'                => ''
            , 'DoNotMail'            => 'donotemail'
            , 'Address'              => 'Address'
            , 'Address2'             => 'Address2'
            , 'City'                 => 'City'
            , 'State'                => 'State'
            , 'Zip'                  => 'Zip'
            , 'Country'              => 'Country'
            , 'PhoneNumber'          => ''
            , 'PhoneNumber2'         => ''
            , 'Cell'                 => 'mobilephone'
            , 'Fax'                  => ''
            , 'LicenseNumber'        => 'LicenseNumber'
            , 'IssuedBy'             => ''
            , 'Waiver'               => ''
            , 'Waiver2'              => ''
            , 'CrdID'                => 'cardId' // do we want to expose this? note that a customer having this id in their possession is like owning the gift card
            , 'RPM'                  => 'proskill'
            , 'AccountCreated'       => ''
            , 'LastVisited'          => ''
            , 'TotalVisits'          => ''
            , 'TotalRaces'           => ''
            , 'MembershipStatus'     => ''
            , 'MembershipText'       => ''
            , 'MemberShipTextLong'   => ''
            , 'PriceLevel'           => ''
            , 'PromotionCode'        => ''
            , 'IsGiftCard'           => ''
            , 'WebUserName'          => ''
            , 'Password'             => '' // we need to be able to insert this, but not select it.
            , 'Award1'               => ''
            , 'Award2'               => ''
            , 'Custom1'              => 'Custom1'
            , 'Custom2'              => 'Custom2'
            , 'Custom3'              => 'Custom3'
            , 'Custom4'              => 'Custom4'
            , 'Privacy1'             => ''
            , 'Privacy2'             => ''
            , 'Privacy3'             => ''
            , 'Privacy4'             => ''
            , 'Status1'              => ''
            , 'Status2'              => ''
            , 'Status3'              => ''
            , 'Status4'              => ''
            , 'Deleted'              => ''
            , 'IsEmployee'           => ''
            , 'OriginalID'           => ''
            , 'CreditLimit'          => ''
            , 'CreditOnHold'         => ''
            // , 'LastUnSubscribedDate' => ''
            , 'GeneralNotes'         => ''
        ));

        // allow password to be set on incoming data
        // but NOT on outgoing data.
        // consider moving to logic class, but note 
        // that this would affect internal use of the logic class.
        // using the mapper will only affect the return from the API.
        $this->restrict('client', array(
              'password'
            // , 'cardId'
        ));
    }
}