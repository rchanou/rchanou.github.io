<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'../../Utility/Convert.php');

/**
 * The business logic class
 * for ClubSpeed screen templates.
 */
class CSScreenTemplate {

    /**
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSReservations class.
     *
     * The CSReservations constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param CSLogic $CSLogic The parent CSLogic container.
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSLogic, &$CSDatabase) {
        $this->logic = $CSLogic;
        $this->db = $CSDatabase;
    }

    /**
     * Document: TODO
     */
    public final function create($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->screenTemplate->map('server', $params);
        $screenTemplateId = $this->db->screenTemplate->create($mapped);
        return array(
            "screenTemplateId" => $screenTemplateId
        );
    }

    public final function all() {
        $all = $this->db->screenTemplate->all();
        $compressed = $this->db->screenTemplate->compress($all);
        return $compressed;
    }

    public final function get($screenTemplateId) {
        $get = $this->db->screenTemplate->get($screenTemplateId);
        $compressed = $this->db->screenTemplate->compress($get);
        return $compressed;
    }

    public final function find($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->screenTemplate->map('server', $params);
        $find = $this->db->screenTemplate->find($mapped);
        $compressed = $this->db->screenTemplate->compress($find);
        return $compressed;
    }

    /**
     * Document: TODO
     */
    public final function update($screenTemplateId, $params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $screenTemplate = $this->db->screenTemplate->get($screenTemplateId);
        if (is_null($screenTemplate))
            throw new \RecordNotFoundException("Attempted to update a non-existent screen template! Received screenTemplateId: " . $screenTemplateId);
        $screenTemplate = $this->db->screenTemplate->blank();
        $screenTemplate->load($screenTemplateId);
        $mapped = $this->db->screenTemplate->map('server', $params);
        $screenTemplate->load($mapped);
        return $this->db->screenTemplate->update($screenTemplate);
    }

    /**
     * Document: TODO
     */
    public final function delete($screenTemplateId) {
        return $this->db->screenTemplate->delete($screenTemplateId);
    }
}