<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Database\Helpers\UnitOfWork;

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
        $this->on('uow', function($uow) { // we could really just use an abstract function for this. doubt we'll need an array of callbacks.
            // note that we can't use &$uow due to silly PHP issues.
            // CHECK FOR REFERENCE/VALUE PROBLEMS (!!!)
            // we may still be safe, since $uow is an object (I think...)
            switch($uow->action) {
                case 'create':
                    if (is_null($uow->data->LogDate))
                        $uow->data->LogDate = Convert::getDate();
                    if (is_null($uow->data->TerminalName))
                        $uow->data->TerminalName = 'ClubSpeed PHP API';
                    break;
                case 'update':
                    // pr('passing through logs update');
                    break;
                case 'all':
                    if (empty($uow->order))
                        $uow->order('LogID DESC');
                    break;
                default:
                    // pr('passing through logs uow for: ' . $uow->action);
                    break;
            }
        });
    }

    // note that in addition to editing the underlying logs records with CRUD operations,
    // we also want to extend this 

    public function log($message) {
        $data = array(
              'Message'      => Convert::toString($message)
            , 'LogDate'      => Convert::getDate()
            , 'TerminalName' => 'ClubSpeed PHP API'
        );
        $uow = UnitOfWork::build($data)->action('create');
        return $this->uow($uow);

        // return $this->create(array(
        //       'Message'      => \ClubSpeed\Utility\Convert::toString($message)
        //     , 'LogDate'      => \ClubSpeed\Utility\Convert::getDate()
        //     , 'TerminalName' => 'ClubSpeed PHP API'
        // ));
    }

    // here is the other 
    protected final function beforeCreate(&$uow) {

    }

    protected final function beforeUpdate(&$uow) {

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