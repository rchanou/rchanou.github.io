<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;

/**
 * The business logic class
 * for ClubSpeed races containing facebook customers.
 */
class FacebookRacesLogic extends BaseReadOnlyLogic {
    
    /**
     * Constructs a new instance of the FacebookRacesLogic class.
     *
     * The FacebookRacesLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->facebookRaces_V;
        $this->before('uow', function($uow) {
            switch($uow->action) {
                case 'all':
                    if (empty($uow->order))
                        $uow->order('Finish');
                    if (!empty($uow->where)) {
                        if (isset($uow->where['Finish'])) {
                            $finish =& $uow->where['Finish'];
                            if (is_array($finish) && Arrays::isAssociative($finish)) {
                                foreach($finish as $key => $val)
                                    $finish[$key] = Convert::toDateForServer($val);
                            }
                            else
                                $finish = Convert::toDateForServer($val);
                        }
                    }
                    break;
            }
        });
    }
}