<?php

namespace ClubSpeed\Database\Records;

class PrimaryCustomers_V extends BaseRecord {

    // public static $table      = 'dbo.PrimaryCustomers_V';

    public static $table = <<<EOS

    (SELECT
          c.CustID
        , c.FName
        , c.LName
        , c.BirthDate
        , c.EmailAddress
        , c.ProSkill
    FROM (
        SELECT
            ROW_NUMBER() OVER (
                PARTITION BY
                    c.FName
                    , c.LName
                    , c.BirthDate
                ORDER BY
                    CASE WHEN (c.Password IS NULL OR LEN(LTRIM(RTRIM(c.Password))) = 0) THEN 1 ELSE 0 END
                    , Points DESC
                    , c.TotalRaces DESC
                    , c.LastVisited DESC
                    , c.RPM DESC
            ) AS Rank
            , c.CustID
            , c.FName
            , c.LName
            , c.BirthDate
            , ISNULL(c.EmailAddress, '') AS EmailAddress
            , c.RPM AS ProSkill
        FROM CUSTOMERS c
        LEFT OUTER JOIN (
            SELECT
                p.CustID
                , SUM(ISNULL(p.PointAmount, 0)) as Points
            FROM POINTHISTORY p
            WHERE
                p.PointExpDate IS NULL
                OR p.PointExpDate >= GETDATE()
            GROUP BY p.CustID
        ) AS p ON p.CustID = c.CustID
        WHERE
            c.Deleted = 0
    ) c
    WHERE c.Rank = 1)
EOS;
    public static $tableAlias = 'pc';
    public static $key        = 'CustID';

    public $CustID;
    public $FName;
    public $LName;
    public $BirthDate;
    public $EmailAddress;
    public $ProSkill;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CustID']))         $this->CustID       = \ClubSpeed\Utility\Convert::toNumber          ($data['CustID']);
                    if (isset($data['FName']))          $this->FName        = \ClubSpeed\Utility\Convert::toString          ($data['FName']);
                    if (isset($data['LName']))          $this->LName        = \ClubSpeed\Utility\Convert::toString          ($data['LName']);
                    if (isset($data['BirthDate']))      $this->BirthDate    = \ClubSpeed\Utility\Convert::toDateForServer   ($data['BirthDate']);
                    if (isset($data['EmailAddress']))   $this->EmailAddress = \ClubSpeed\Utility\Convert::toString          ($data['EmailAddress']);
                    if (isset($data['ProSkill']))       $this->ProSkill     = \ClubSpeed\Utility\Convert::toNumber          ($data['ProSkill']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}