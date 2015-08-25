<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed screen template detail.
 */
class ScreenTemplateDetailLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ScreenTemplateDetailLogic class.
     *
     * The ScreenTemplateDetailLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->screenTemplateDetail;
    }

    public final function create($params = array()) {
        $db =& $this->db;
        return parent::_create($params, function($screenTemplateDetail) use (&$db) {
            $screenTemplate = $db->screenTemplate->get($screenTemplateDetail->TemplateID);
            if (is_null($screenTemplate) || empty($screenTemplate))
                throw new \CSException("Create screen template detail attempted to use a non-existent screenTemplateId! Received: " . $screenTemplateDetail->TemplateID, 404);
            return $screenTemplateDetail;
        });
    }
}