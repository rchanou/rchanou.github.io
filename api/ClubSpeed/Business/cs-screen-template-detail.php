<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'../../Utility/Convert.php');

/**
 * The business logic class
 * for ClubSpeed screen template detail.
 */
class CSScreenTemplateDetail {

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
        $mapped = $this->db->screenTemplateDetail->map('server', $params);
        $detail = $this->db->screenTemplateDetail->blank();
        $detail->load($mapped);
        $screen = $this->db->screenTemplate->get($detail->TemplateID);
        if (is_null($screen))
            throw new \RecordNotFoundException("Create screen template detail attempted to use a non-existent screenTemplateId! Received: " . $detail->TemplateID);
        $screenTemplateDetailId = $this->db->screenTemplateDetail->create($detail);
        return array(
            "screenTemplateDetailId" => $screenTemplateDetailId
        );
    }

    public final function all() {
        $all = $this->db->screenTemplateDetail->all();
        $compressed = $this->db->screenTemplateDetail->compress($all);
        return $compressed;
    }

    public final function get($screenTemplateDetailId) {
        $get = $this->db->screenTemplateDetail->get($screenTemplateDetailId);
        $compressed = $this->db->screenTemplateDetail->compress($get);
        return $compressed;
    }

    public final function find($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->screenTemplateDetail->map('server', $params);
        $find = $this->db->screenTemplateDetail->find($mapped);
        $compressed = $this->db->screenTemplateDetail->compress($find);
        return $compressed;
    }

    /**
     * Document: TODO
     */
    public final function update($screenTemplateDetailId, $params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $reservation = $this->db->screenTemplateDetail->get($screenTemplateDetailId);
        if (is_null($reservation))
            throw new \RecordNotFoundException("Attempted to update a non-existent online booking reservation! Received screenTemplateDetailId: " . $screenTemplateDetailId);
        $reservation = $this->db->screenTemplateDetail->blank();
        $reservation->load($screenTemplateDetailId);
        $mapped = $this->db->screenTemplateDetail->map('server', $params);
        $reservation->load($mapped);
        return $this->db->screenTemplateDetail->update($reservation);
    }

    /**
     * Document: TODO
     */
    public final function delete($screenTemplateDetailId) {
        return $this->db->screenTemplateDetail->delete($screenTemplateDetailId);
        // if (is_null($screenTemplateDetailId))
        //     throw new \InvalidArgumentException("Delete reservation for online booking requires an screenTemplateDetailId!");

        // $sql = "DELETE obr"
        //     ."\nFROM dbo.screenTemplateDetail obr"
        //     ."\nWHERE obr.screenTemplateDetailID = :screenTemplateDetailID"
        //     ;
        // $params = array(
        //     ":screenTemplateDetailID" => $screenTemplateDetailId
        // );
        // $affected = $this->db->exec($sql, $params); // check for a single delete
    }
}