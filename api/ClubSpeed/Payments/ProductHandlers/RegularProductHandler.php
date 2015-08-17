<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Utility\Convert as Convert;

class RegularProductHandler extends BaseProductHandler {

    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
    }

    public function handle($checkTotal, $metadata = array()) {
        $logPrefix = "Check #" . $checkTotal->CheckID . ": CheckDetail #" . $checkTotal->CheckDetailID . ": ";
        $now = \ClubSpeed\Utility\Convert::getDate();
        return ''; // we don't really need a regular producthandler if we can't book heats this way
    }
}