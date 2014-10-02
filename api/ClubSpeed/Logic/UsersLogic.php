<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed users.
 */
class UsersLogic extends BaseLogic {

    /**
     * Constructs a new instance of the CheckDetailsLogic class.
     *
     * The CheckDetailsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->users;

        // todo - make a real mapper class, deprecate the static mapper register below
        $this->namespace = lcfirst($this->db->users->table);
        \ClubSpeed\Utility\JsonMapper::register($this->namespace, array(
              "UserID"          => "userId"
            , "FName"           => "firstName"
            , "LName"           => "lastName"
            , "UserName"        => "username"
            , "Password"        => "password"
            , "CrdID"           => "cardId"
            , "Enabled"         => "enabled"
            , "EmailAddress"    => "email"
            , "PhoneNumber"     => "phone"
            , "Deleted"         => "deleted"
            , "MaxHrsPerWeek"   => "maxHoursPerWeek"
            , "MaxHoursPerDay"  => "maxHoursPerDay"
            , "MondayOn"        => "mondayOn"
            , "TuesdayOn"       => "tuesdayOn"
            , "WednesdayOn"     => "wednesdayOn"
            , "ThursdayOn"      => "thursdayOn"
            , "FridayOn"        => "fridayOn"
            , "SaturdayOn"      => "saturdayOn"
            , "SundayOn"        => "sundayOn"
            , "MondayStart"     => "mondayStart"
            , "MondayEnd"       => "mondayEnd"
            , "TuesdayStart"    => "tuesdayStart"
            , "TuesdayEnd"      => "tuesdayEnd"
            , "WednesdayStart"  => "wednesdayStart"
            , "WednesdayEnd"    => "wednesdayEnd"
            , "ThursdayStart"   => "thursdayStart"
            , "ThursdayEnd"     => "thursdayEnd"
            , "FridayStart"     => "fridayStart"
            , "FridayEnd"       => "fridayEnd"
            , "SaturdayStart"   => "saturdayStart"
            , "SaturdayEnd"     => "saturdayEnd"
            , "SundayStart"     => "sundayStart"
            , "SundayEnd"       => "sundayEnd"
            , "EmpStartDate"    => "employeeStartDate"
            , "WebPassword"     => "webPassword"
            , "SystemUsers"     => "systemUsers"
        ));
    }

    /**
     * Validates a username password combination against the users table.
     *
     * @param string    $username   The username to validate.
     * @param string    $password   The password to validate.
     *
     * @return boolean True if the username and password combination match, false if not.
     *
     * @throws InvalidArgumentException     if $username is not a string.
     * @throws InvalidArgumentException     if $password is not a string.
     */
    public final function validate($username, $password) {
        if (!isset($username) || !is_string($username))
            throw new \InvalidArgumentException("User validate requires username to be a string! Received: " . $username);
        if (!isset($password) || !is_string($password))
            throw new \InvalidArgumentException("User validate requires password to be a string! Received: " . $password);

        $sql = array(
              "SELECT "
            , "    CASE WHEN EXISTS ("
            , "        SELECT *"
            , "        FROM dbo.USERS u"
            , "        LEFT OUTER JOIN dbo.USERROLES ur"
            , "            ON u.UserID = ur.UserID"
            , "        WHERE"
            , "                ur.RoleID = 1" // admin role
            , "            AND u.[Enabled] = 1"
            , "            AND u.Deleted = 0"
            , "            AND u.UserName = ?"
            , "            AND u.[Password] = ?"
            , "    )"
            , "    THEN 1"
            , "    ELSE 0"
            , "    END AS IsValid"
        );
        $sql = implode("\n", $sql);
        $params = array($username, $password);
        $result = $this->db->query($sql, $params);
        $isValid = $result[0]['IsValid'];
        return $isValid;
    }

    /**
     * Validates the existence of an userId in the database.
     * Note: This should be done by foreign keys, but since we don't have any
     *       for users, we must do our existence validation at some point.
     *
     * @param int $userId The user id to check for existence.
     * @return boolean If the userId is found in dbo.Users then true, else false.
     * @throws InvalidArgumentException If the userId parameter is not set or a non-integer.
     */
    public final function user_exists($userId) {
        if (!isset($userId) || !is_int($userId))
            throw new \InvalidArgumentException("User exists requires userId to be an integer! Received: $userId");

        $sql = "SELECT"
            ."\n    CASE WHEN EXISTS ("
            ."\n        SELECT u.*"
            ."\n        FROM dbo.USERS u"
            ."\n        WHERE u.UserID = ?"
            ."\n    )"
            ."\n    THEN 1"
            ."\n    ELSE 0"
            ."\n    END AS UserExists";
        $params = array($userId);
        $results = $this->db->query($sql, $params);
        $userExists = \ClubSpeed\Utility\Convert::toBoolean($results[0]['UserExists']);
        return $userExists;
    }
}