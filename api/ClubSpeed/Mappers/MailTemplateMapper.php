<?php

namespace ClubSpeed\Mappers;

class MailTemplateMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'races';
        $this->register(array(
              'EmailTemplateID' => 'mailTemplateId'
            , 'Text'            => ''
            , 'Subject'         => ''
        ));
    }
}