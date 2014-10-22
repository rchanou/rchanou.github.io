<?php

namespace ClubSpeed\Mappers;

class PrimaryCustomersMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'customers';
        $this->register(array(
              'CustID'          => 'customerId'
            , 'FName'           => 'firstname'
            , 'LName'           => 'lastname'
            , 'EmailAddress'    => 'email'
            , 'BirthDate'       => ''
            , 'ProSkill'        => ''
        ));
    }
}