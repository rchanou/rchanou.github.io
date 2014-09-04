<?php

/**
 * An extended class representing an exception
 * which is expected to be thrown pending
 * failed ClubSpeed business logic.
 */
class CSException extends \Exception {}

/**
 * A ClubSpeed business logic exception
 * signifying that the email provided
 * for a login attempt was invalid.
 */
class InvalidEmailException extends \CSException {}

/**
 * A ClubSpeed business logic exception
 * signifying that the password provided
 * for a login attempt was invalid.
 */
class InvalidPasswordException extends \CSException {}

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
class RecordNotFoundException extends \CSException{}

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
 * signifying that a user attempted to 
 * execute an invalid db operation
 * (such as updating a record in a view).
 */
class InvalidDbOperationException extends \CSException {}