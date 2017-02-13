<?php

namespace ClubSpeed\Mappers;

class CheckEventTasksMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'checkEventTasks';
        $this->register(array(
              'ID' => 'eventTaskId' // intentional mismatch
            , 'CheckID' => 'eventReservationId' // intentional mismatch
            , 'CompletedByUserID' => 'completedBy'
            , 'DateCompleted' => 'completedAt'
            , 'TaskID' => 'eventTaskTypeId'
        ));
    }
}
