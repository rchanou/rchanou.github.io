<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed logs.
 */
class LogsLogic extends BaseLogic implements \ClubSpeed\Logging\LogInterface {

    /**
     * Constructs a new instance of the LogsLogic class.
     *
     * The LogsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->logs;
    }

    // note that in addition to editing the underlying logs records with CRUD operations,
    // we also want to extend this 

    public function log($message) {
        return $this->create(array(
              'Message'      => \ClubSpeed\Utility\Convert::toString($message)
            , 'LogDate'      => \ClubSpeed\Utility\Convert::getDate()
            , 'TerminalName' => 'ClubSpeed PHP API'
        ));
    }

    public function debug($message) {
        if (filter_var(@$_REQUEST['debug'], FILTER_VALIDATE_BOOLEAN)) {
            return $this->log("DEBUG :: " . $message);
        }
    }

    public function info($message) {
        return $this->log("INFO :: " . $message);
    }

    public function warn($message) {
        return $this->log("WARNING :: " . $message);
    }

    public function error($message, \Exception $exception = null) {
        if (isset($exception) && $exception instanceof \Exception)
            $message .= ' :: Exception at ' . $exception->getFile() . ':' . $exception->getLine() . ' - ' . $exception->getMessage();
        return $this->log("ERROR :: " . $message); // just use function log for now -- consider sending emails on true errors
    }
}