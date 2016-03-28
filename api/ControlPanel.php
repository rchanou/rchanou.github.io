<?php

use ClubSpeed\Enums\Enums as Enums;

class ControlPanel extends BaseCompositeApi {

    function __construct() {
        parent::__construct();
        $this->mapper       = new \ClubSpeed\Mappers\ControlPanelMapper();
        $this->interface    = $this->logic->controlPanel;
    }
}
