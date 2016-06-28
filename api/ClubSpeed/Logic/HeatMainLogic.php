<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Database\Helpers\UnitOfWork;

/**
 * The business logic class
 * for ClubSpeed heats.
 */
class HeatMainLogic extends BaseLogic {

    /**
     * Constructs a new instance of the HeatMainLogic class.
     *
     * The HeatMainLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->heatMain;

        $self =& $this;
        $afters = array(
            'create' => array($self, 'clearCache'),
            'update' => array($self, 'clearCache'),
            'delete' => array($self, 'clearCache')
        );
        $this->after('uow', function($uow) use (&$afters) {
            if (isset($afters[$uow->action]))
                call_user_func($afters[$uow->action], $uow);
        });

        $this->before('uow', function($uow) use ($db) {
            switch($uow->action) {
                case 'create':
                    if (!empty($uow->data)) {
                        $heat =& $uow->data;
                        if (empty($heat->HeatStatus))
                            $heat->HeatStatus = Enums::HEAT_STATUS_OPEN;
                        if (empty($heat->NumberOfReservation))
                            $heat->NumberOfReservation = 0;
                        if (empty($heat->NumberOfCadetReservation))
                            $heat->NumberOfCadetReservation = 0;
                        if (empty($heat->TrackNo))
                            $heat->TrackNo = 1; // safe?

                        // use HeatType as a lookup for default information
                        if (empty($heat->HeatTypeNo))
                            throw new \RequiredArgumentMissingException('Creating a heat requires a heat type! Received: ' . $heat->HeatTypeNo);
                        $heatType = null;
                        try {
                            $heatTypeUow = UnitOfWork::build()
                                ->action('get')
                                ->table('HeatTypes')
                                ->table_id($heat->HeatTypeNo);
                            $db->heatTypes->uow($heatTypeUow);
                            $heatType = $heatTypeUow->data;
                        }
                        catch(\RecordNotFoundException $e) {
                            // safe? consider reworking dbcollection to not throw exceptions by default.
                            throw new \InvalidArgumentValueException('Creating a heat received an invalid heat type id! Received: ' . $heat->HeatTypeNo);
                        }
                        // just other exceptions get thrown to the top

                        // allow explicit overrides for most HeatType fields,
                        // but override any empty fields with defaults
                        if (empty($heat->RacersPerHeat))
                            $heat->RacersPerHeat = $heatType->RacersPerHeat;
                        if (empty($heat->LapsOrMinutes))
                            $heat->LapsOrMinutes = $heatType->LapsOrMinutes;
                        if (empty($heat->WinBy))
                            $heat->WinBy = $heatType->WinBy;
                        if (empty($heat->RaceBy))
                            $heat->RaceBy = $heatType->RaceBy;
                        if (empty($heat->SpeedLevel))
                            $heat->SpeedLevel = $heatType->SpeedLevel;
                        if (empty($heat->ScheduleDuration))
                            $heat->ScheduleDuration = $heatType->ScheduleDuration;
                        if (empty($heat->CadetsPerHeat))
                            $heat->CadetsPerHeat = $heatType->CadetsPerHeat;
                        if (empty($heat->PointsNeeded) && $heat->PointsNeeded !== 0)
                            $heat->PointsNeeded = $heatType->Cost;
                    }
                    break;
                case 'delete':
                    $id = $uow->table_id;
                    $heatDetails = $db->heatdetails->match(array(
                        "HeatNo" => $id
                    ));
                    if (!empty($heatDetails))
                        throw new \CSException('Attempted to delete heat #' . $id . ', but heat details still exist for that heat!');
                    break;
            }
        });
    }

    function clearCache($uow) {
        $GLOBALS['webapi']->clearCache();
    }
}
