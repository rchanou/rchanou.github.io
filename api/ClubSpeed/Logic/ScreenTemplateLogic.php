<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed screen templates.
 */
class ScreenTemplateLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ScreenTemplateLogic class.
     *
     * The ScreenTemplateLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->screenTemplate;

        $this->insertable = array(
              // 'TemplateID'
              'TemplateName'
            , 'ShowScoreboard'
            , 'IdleTime'
            , 'ScoreBoardTrackNo'
            // , 'Deleted'
            , 'StartPosition'
            , 'SizeX'
            , 'SizeY'
        );

    }

    public function create($params = array()) {
        return parent::_create($params, function($screenTemplate) {
            if (is_null($screenTemplate->ShowScoreboard))
                $screenTemplate->ShowScoreboard = false;
            if (is_null($screenTemplate->SizeX))
                $screenTemplate->SizeX = 800;
            if (is_null($screenTemplate->SizeY))
                $screenTemplate->SizeY = 600;
            if (is_null($screenTemplate->StartPosition))
                $screenTemplate->StartPosition = 1;
            $screenTemplate->Deleted = false;

            if ($screenTemplate->ShowScoreboard) {
                if (is_null($screenTemplate->ScoreBoardTrackNo))
                    $screenTemplate->ScoreBoardTrackNo = 1;
            }
            return $screenTemplate;
        });
    }
}