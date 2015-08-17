<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed races containing facebook customers.
 */
class MailTemplateLogic extends BaseReadOnlyLogic {
    
    /**
     * Constructs a new instance of the MailTemplateLogic class.
     *
     * The MailTemplateLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->mailTemplate;
    }
}