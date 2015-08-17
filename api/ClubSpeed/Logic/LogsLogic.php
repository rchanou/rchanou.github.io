<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;

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
        $this->before('uow', function($uow) {
            switch($uow->action) {
                case 'create':
                    if (is_null($uow->data->LogDate))
                        $uow->data->LogDate = Convert::getDate();
                    if (is_null($uow->data->TerminalName))
                        $uow->data->TerminalName = Enums::NSP_API;
                    break;
                case 'update':
                    // pr('passing through logs update');
                    break;
                case 'all':
                    if (empty($uow->order))
                        $uow->order('LogID DESC');
                    foreach($uow->order as $key => $val) {
                        if ($key === 'Message')
                            unset($uow->order[$key]);
                    }
                    break;
                default:
                    // pr('passing through logs uow for: ' . $uow->action);
                    break;
            }
        });
    }

    // note that in addition to editing the underlying logs records with CRUD operations,
    // we also want to extend this 
    public function log($message, $namespace = null) {
        $data = array(
              'Message'      => Convert::toString($message)
            , 'LogDate'      => Convert::getDate()
            , 'TerminalName' => $namespace ?: Enums::NSP_API
        );
        $uow = UnitOfWork::build($data)->action('create');
        return $this->uow($uow);
    }

    public function debug($message, $namespace = null) {
        if (filter_var(@$_REQUEST['debug'], FILTER_VALIDATE_BOOLEAN)) {
            return $this->log("DEBUG :: " . $message, $namespace);
        }
    }

    public function info($message, $namespace = null) {
        return $this->log("INFO :: " . $message, $namespace);
    }

    public function warn($message, $namespace = null) {
        return $this->log("WARNING :: " . $message, $namespace);
    }

    public function error($message, $namespace = null, \Exception $exception = null) {
        if (isset($exception) && $exception instanceof \Exception)
            $message .= ' :: Exception at ' . $exception->getFile() . ':' . $exception->getLine() . ' - ' . $exception->getMessage();
        return $this->log("ERROR :: " . $message, $namespace); // just use function log for now -- consider sending emails on true errors
    }
}