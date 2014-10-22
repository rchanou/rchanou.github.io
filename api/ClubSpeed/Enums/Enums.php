<?php

namespace ClubSpeed\Enums;

/**
 * The enumeration container for ClubSpeed specific constants
 * designed to improve readability with internal functions.
 */
class Enums {

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
     * The constant signifying an explicitly set database null.
     */
    const DB_NULL = '\0';

    /**
     * The constant signifying an API call requires at least private level access.
     */
    const API_PRIVATE_ACCESS = 0;

    /**
     * The constant signifying an API call requires at least public level access.
     */
    const API_PUBLIC_ACCESS = 1;

    /**
     * The constant signifying an API call should not be exposed to any level access.
     */
    const API_NO_ACCESS = -1;


    // See UserDataType.vb at Enum ProductType
    const PRODUCT_TYPE_REGULAR = 1;
    const PRODUCT_TYPE_POINT = 2;
    const PRODUCT_TYPE_FOOD = 3;
    const PRODUCT_TYPE_RESERVATION = 4;
    const PRODUCT_TYPE_GAME_CARD = 5;
    const PRODUCT_TYPE_MEMBERSHIP = 6;
    const PRODUCT_TYPE_GIFT_CARD = 7;
    const PRODUCT_TYPE_ENTITLE = 8;

    // See UserDataType.vb at Enum PayType
    const PAY_TYPE_CASH = 1;
    const PAY_TYPE_CREDIT_CARD = 2;
    const PAY_TYPE_EXTERNAL = 3;
    const PAY_TYPE_GIFT_CARD = 4;
    const PAY_TYPE_VOUCHER = 5;
    const PAY_TYPE_COMPLIMENTARY = 6;
    const PAY_TYPE_CHECK = 7;
    const PAY_TYPE_GAME_CARD = 8;
    const PAY_TYPE_DEBIT = 9;
    const PAY_TYPE_SAGE_PAY = 10;
    const PAY_TYPE_PAY_PAL_PAY_FLOW_PRO = 11;

    // See UserDataType.vb at Enum PayStatus
    const PAY_STATUS_PAID = 1;
    const PAY_STATUS_VOID = 2;
}