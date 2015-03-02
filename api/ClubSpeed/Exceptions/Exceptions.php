<?php

// todo: rework to a structure that an AutoLoader can use

/**
 * An extended class representing an exception
 * which is expected to be thrown pending
 * failed ClubSpeed business logic.
 */
class CSException extends \Exception {}

/**
 * A ClubSpeed business logic exception
 * signifying that the token provided
 * for a password reset attempt was invalid.
 */
class InvalidTokenException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the email provided
 * for a login attempt already exists 
 * in the database.
 */
class EmailAlreadyExistsException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the event id provided to 
 * a function could not be found in the database.
 *
 * Note that this exception is used to simulate
 * typical foreign key exceptions, as the database
 * structure does not have foreign keys set up.
 */
class EventNotFoundException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the customer id provided to 
 * a function could not be found in the database.
 *
 * Note that this exception is used to simulate
 * typical foreign key exceptions, as the database
 * structure does not have foreign keys set up.
 */
class CustomerNotFoundException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the user id provided to 
 * a function could not be found in the database.
 *
 * Note that this exception is used to simulate
 * typical foreign key exceptions, as the database
 * structure does not have foreign keys set up.
 */
class UserNotFoundException extends \CSException {}

/**
 * A ClubSpeed exception signifying that 
 * a required record was attempted to be found
 * but none matching the criteria was available.
 */
class RecordNotFoundException extends \CSException {
    public function __construct($message = null, $code = 404, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class BadRequestException extends \CSException {
    public function __construct($message = null, $code = 400, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class UnauthorizedException extends \CSException {
    public function __construct($message = null, $code = 401, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class ForbiddenException extends \CSException {
    public function __construct($message = null, $code = 403, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * A ClubSpeed business logic exception
 * signifying that the email provided
 * for a login attempt was invalid.
 */
class InvalidEmailException extends \UnauthorizedException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the password provided
 * for a login attempt was invalid.
 */
class InvalidPasswordException extends \UnauthorizedException {}

/**
 * Clubspeed exception signifying that
 * a unique record is already considered to exist,
 * either by unique id or combination of other columns.
 */
class RecordAlreadyExistsException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the user id provided to 
 * a function could not be found in the database.
 *
 * Note that this exception is being used on top
 * of typical foreign key exceptions, as the OnlineBooking
 * information needs to be accessed before an insert is attempted.
 */
class OnlineBookingsNotFoundException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that an invalid reservation quantity
 * was attempted to be used for online booking.
 */
class OnlineBookingsQuantityException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the an argument was required
 * but was either not set or empty.
 */
class RequiredArgumentMissingException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the an argument was supplied
 * but fails business logic or pre-database validation checks.
 */
class InvalidArgumentValueException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the an argument was supplied
 * but was the wrong argument type.
 */
class InvalidArgumentTypeException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that a user attempted to 
 * execute an invalid db operation
 * (such as updating a record in a view).
 */
class InvalidDbOperationException extends \CSException {}