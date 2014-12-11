<?php

class Translations extends BaseApi
{

    /**
     * A reference to the globally set CSLogic class.
     */
    public $restler;

    function __construct() {
        parent::__construct();
        $this->logic = isset($GLOBALS['logic']) ? $GLOBALS['logic'] : null;
        $this->mapper = new \ClubSpeed\Mappers\TranslationsMapper();
        $this->interface = $this->logic->translations;
    }

    /**
     * @url POST /batch
     */
    public function batchCreate($request_data = null) {
        $this->validate('post'); // use the same permissions as a single create
        try {
            $in = $this->mapper->in($request_data);
            $mapped = $in->params;
            $batch = $this->interface->batchCreate($mapped);
            $out = $this->mapper->out($batch);
            return $out;
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
     * @url PUT /batch
     */
    public function batchUpdate($request_data = null) {
        $this->validate('put'); // use the same permissions as a single put
        try {
            $in = $this->mapper->in($request_data);
            $mapped = $in->params;
            $batch = $this->interface->batchUpdate($mapped);
            $out = $this->mapper->out($batch);
            return $out;
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}