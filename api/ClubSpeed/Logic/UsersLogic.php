<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;
use Clubspeed\Enums\Enums;

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

        $self =& $this;
        $befores = array(
            'create' => array($self, 'validateCreate'),
            'update' => array($self, 'validateUpdate')
        );
        $this->before('uow', function($uow) use ($befores) {
            if (isset($befores[$uow->action]))
                call_user_func($befores[$uow->action], $uow);
        });
    }

    function validateCreate($uow) {
        $user =& $uow->data;
        if (empty($user->UserName) || $user->UserName === Enums::DB_NULL)
            throw new \RequiredArgumentMissingException('Creating a user requires a username! Received: ' . $user->UserName);
        if (empty($user->Password) || $user->Password === Enums::DB_NULL)
            throw new \RequiredArgumentMissingException('Creating a user requires a password!');
        if (empty($user->WebPassword) || $user->WebPassword === Enums::DB_NULL)
            $user->WebPassword = $user->Password;
        if (empty($user->Enabled) || $user->Enabled === Enums::DB_NULL)
            $user->Enabled = 1;
        if (empty($user->Deleted) || $user->Deleted === Enums::DB_NULL)
            $user->Deleted = 0;

        $usernameCount = $this->interface->uow(
            UnitOfWork::build()
                ->table('Users')
                ->action('count')
                ->where(array(
                    "UserName" => $user->UserName
                ))
        )->data;
        if ($usernameCount !== 0)
            throw new \RecordAlreadyExistsException('Attempting to create a user with a username which already exists! Received: ' . $user->UserName);
    }

    function validateUpdate($uow) {
        $user =& $uow->data;
        $existing =& $uow->existing;
        if ($user->UserName === Enums::DB_NULL || (is_string($user->UserName) && empty($user->UserName)))
            throw new \RequiredArgumentMissingException('Cannot update username to an empty value!');
        if ($user->Password === Enums::DB_NULL || (is_string($user->Password) && empty($user->Password)))
            throw new \RequiredArgumentMissingException('Cannot update password to an empty value!');
        if ($user->WebPassword === Enums::DB_NULL || (is_string($user->WebPassword) && empty($user->WebPassword)))
            throw new \RequiredArgumentMissingException('Cannot update web password to an empty value!');

        if (!empty($user->UserName)) {
            $usernameCount = $this->interface->uow(
                UnitOfWork::build()
                    ->table('Users')
                    ->action('count')
                    ->where(array(
                        "UserID" => array('$neq' => $existing->UserID), // allow update from one username to same username.
                        "UserName" => $user->UserName
                    ))
            )->data;
            if ($usernameCount !== 0)
                throw new \RecordAlreadyExistsException('Attempting to update a user with a username which already exists! Received: ' . $user->UserName);
        }
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