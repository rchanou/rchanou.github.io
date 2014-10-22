<?php

namespace ClubSpeed\Mappers;

class FacebookMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'facebook';
        $this->register(array(
              'FB_CustId'    => 'facebookId' // different name? need to make sure we know it's the table's id
            , 'CustId'       => 'customerId'
            , 'UId'          => 'uid'
            , 'Access_token' => 'accessToken'
            , 'AllowEmail'   => ''
            , 'AllowPost'    => ''
            , 'Enabled'      => ''
        ));
    }
}