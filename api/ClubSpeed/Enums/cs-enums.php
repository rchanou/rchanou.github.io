<?php

/**
 * The enumeration container for ClubSpeed specific constants
 * designed to improve readability with internal functions.
 */
class CSEnums {

    /**
     * The code which signifies a customer was added from a point of sale.
     */
    const RULE_ADD_CUSTOMER_FROM_POS = 1;

    /**
     * The code which signifies a customer was added from a registration terminal.
     */
    const RULE_ADD_CUSTOMER_FROM_REGISTRATION_TERMINAL = 2;

    /**
     * The code which signifies a customer was added from online registration.
     */
    const RULE_ADD_CUSTOMER_FROM_ONLINE_REGISTRATION = 3;

    /**
     * The code which signifies a customer was added from online registration to an event queue.
     */
    const RULE_ADD_EVENT_CUSTOMER_FROM_ONLINE_REGISTRATION = 4;

    /**
     * The code which signifies a customer has signed a primary waiver.
     */
    const RULE_SIGN_PRIMARY_WAIVER = 5;

    /**
     * The code which signifies a customer has signed a secondary waiver.
     */
    const RULE_SIGN_SECONDARY_WAIVER = 6;

    /**
     * The code which signifies a customer has successfully been auto billed. (???)
     */
    const RULE_AUTO_BILL_SUCCESSFUL = 7; // note that vb has this misspelled as "Succesfull"

    /**
     * The code which signifies a customer has not been successfully auto billed. (???)
     */
    const RULE_AUTO_BILL_FAILED = 8;

    /**
     * The constant signifying an explicitly set DB_NULL
     */
    const DB_NULL = '\0';
}