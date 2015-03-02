<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;

/**
 * The business logic class
 * for ClubSpeed users.
 */
class UsersLogic extends BaseLogic {

    /**
     * Constructs a new instance of the UsersLogic class.
     *
     * The UsersLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->users;
    }

    public final function login($data) {

        $username = @$data['username'];
        $password = @$data['password'];
        if (empty($username) || !is_string($username))
            throw new \InvalidArgumentException("User login requires username to be a non-empty string! Received: " . $username);
        if (empty($password) || !is_string($password))
            throw new \InvalidArgumentException("User login requires password to be a non-empty string!");
        // note, we actually want to use WebPassword for login purposes
        $data = array(
              'UserName'    => $username
            , 'WebPassword' => $password
            , 'Enabled'     => true
            , 'Deleted'     => false
        );
        $uow = UnitOfWork::build()
            ->table('Users')
            ->action('all') // extend to allow exists to work with $where clauses
            ->where($data)
            ->select(array(
                  'UserID'
                , 'UserName'
            ));
        $this->interface->uow($uow);
        $uow->data = Arrays::first($uow->data);
        return $uow;
    }
}