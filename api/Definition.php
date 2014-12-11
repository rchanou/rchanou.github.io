<?php

use ClubSpeed\Enums\Enums as Enums;

class Definition extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->access['post']   = Enums::API_NO_ACCESS;
        $this->access['get']    = Enums::API_FREE_ACCESS; // or should we use public? we use this to build the drivers
        $this->access['match']  = Enums::API_NO_ACCESS;
        $this->access['filter'] = Enums::API_NO_ACCESS;
        $this->access['put']    = Enums::API_NO_ACCESS;
        $this->access['delete'] = Enums::API_NO_ACCESS;
        $this->access['all']    = Enums::API_NO_ACCESS;
    }

    private static function parseJson($file) {
        return json_decode(file_get_contents($file), true);
    }

    private static function loadData() {
        return self::parseJson(__DIR__.'/data/api.json');
    }

    /**
     * @url GET /
     */
    public function get($request_data = null) {
        $this->validate('get');
        $json = self::loadData();
        return $json;
    }
}