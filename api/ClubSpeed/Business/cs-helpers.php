<?php

namespace ClubSpeed\Business;

/**
 * The business logic class
 * for ClubSpeed helper methods.
 */
class CSHelpers {

    /**
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSHelpers class.
     *
     * The CSHelpers constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param CSLogic $CSLogic The parent CSLogic container.
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSLogic, &$CSDatabase) {
        $this->logic = $CSLogic;
        $this->db = $CSDatabase;
    }

    /**
     * Creates a new check in the database.
     *
     * @param string        $terminalName   (optional)  The name of the check.
     * @param string[mixed] $settingNames               The list of setting names to collect.
     *
     * @return mixed[string] An associative array containing the requested settings as key value pairs.
     */
    public function getControlPanelSettings($terminalName = 'MainEngine', $settingNames = array()) {
        $paramsValues = $settingNames; // NOTE! PHP makes a COPY by default, not a reference (!!!)
        array_unshift($paramsValues, $terminalName);
        $paramsPlaces = array_fill(0, count($paramsValues) - 1, '?'); // needs to be one fewer than paramsValues count, due to TerminalName
        $sql = "SELECT"
            ."\n    cp.SettingName"
            ."\n    , cp.SettingValue"
            ."\nFROM"
            ."\n    dbo.ControlPanel cp"
            ."\nWHERE"
            ."\n    cp.TerminalName = ?"
            ."\n    AND ("
            ."\n        cp.SettingName IN (" . implode(", ", $paramsPlaces) . ")"
            ."\n    )"
            ;
        $results = $this->db->query($sql, $paramsValues);
        $settings = array();
        foreach($results as $result) {
            // mutate the key/value response from sql server into a php array
            $settings[$result['SettingName']] = $result['SettingValue'];
        }
        return $settings;
    }

    /**
     * Collects the Text and Subject from the MailTemplate in the database.
     *
     * @return string[string] An associative array containing the Text and the Subject for the MailTemplate.
     */
    public function getMailTemplate() {
        $sql = (
                "SELECT"
            ."\n   m.Text"
            ."\n   , m.Subject"
            ."\nFROM"
            ."\n   dbo.MailTemplate m"
        );
        $result = $this->db->query($sql);
        if (!empty($result) && isset($result[0])) {
            return $result[0];
        }
        return array();
    }
}