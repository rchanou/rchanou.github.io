<?php

namespace ClubSpeed\Mappers;

class TasksForCheckEventMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'tasksForCheckEvents';
        $this->register(array(
              'TaskID' => 'eventTaskId' // intentional mismatch
            , 'Deleted' => 'deleted'
            , 'Seq' => 'seq'
            , 'TaskDescription' => 'description'
            , 'TaskName' => 'name'
        ));
    }
}
