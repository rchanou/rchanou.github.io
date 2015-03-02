<?php

namespace ClubSpeed\Mappers;
use ClubSpeed\Utility\Arrays as Arrays;

class UserTasksMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'userTasks';
        $this->register(array(
              'UserID'          => 'userId'
            , 'UserName'        => 'username'
            , 'EmailAddress'    => 'email'
            , 'RoleID'          => 'roleId'
            , 'RoleName'        => 'roleName'
            , 'TaskID'          => 'taskId'
            , 'TaskDescription' => 'taskDescription'
        ));
    }
}