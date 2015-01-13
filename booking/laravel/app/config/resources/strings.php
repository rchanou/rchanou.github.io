<?php

/**
 * Class Strings
 *
 * This class is used through the website to acquire strings for use.
 * It determines defaults, and pulls translations from Club Speed.
 */
class Strings
{
    private static $defaultEnglish;
    private static $cultureNames;
    private static $errorCodes;

    private function __construct() { }
    private static $initialized = false;

    //Gets the default strings
    public static function getDefaultStrings()
    {
        self::initialize();
        return self::$defaultEnglish;
    }

    //Gets the current strings
    public static function getStrings()
    {
        self::initialize();
        return Session::get('currentStrings');
    }

    //Sets the current strings
    public static function setStrings($newStrings)
    {
        self::initialize();
        return Session::put('currentStrings',$newStrings);
    }

    //Gets culture names
    public static function getCultureNames()
    {
        self::initialize();
        return self::$cultureNames;
    }

    //Gets error code strings
    public static function getErrorCodes()
    {
        self::initialize();
        return self::$errorCodes;
    }

    //Sets default strings, and overwrites them with strings from Club Speed
    private static function initialize()
    {
        if (self::$initialized) return;

        //All string labels must be prefixed with 'str'. Club Speed requirement. I added '_' for readability.
        self::$defaultEnglish = array(

            //Header template strings
            'str_seeTheLineup' => 'See the Lineup',
            'str_chooseARace' => 'Choose a Race',
            'str_reviewYourOrder' => 'Review Your Order',
            'str_checkout' => 'Checkout',
            'str_buyGiftCards' => 'Buy Gift Cards',

            //Disabled template strings
            'str_disabledTitle' => 'Disabled! - Online Booking',
            'str_onlineBookingDisabled' => 'Online Booking Disabled!',
            'str_temporarilyDisabled' => 'Online booking has been temporarily disabled.',
            'str_pleaseTryAgainLater' => 'Please try again later!',

            //Disconnected template strings
            'str_disconnectedTitle' => 'Disconnected! - Online Booking',
            'str_disconnected' => 'Disconnected!',
            'str_unableToReachServer' => "Unable to reach the track's server.",

            //Step 1 template strings
            'str_step1Title' => 'Book a Race - Online Booking',
            'str_chooseYourDate' => 'Choose your date:',
            'str_howManyDrivers' => 'How many drivers?',
            'str_whatTypeOfRace' => 'What type of race?',
            'str_search' => 'Search',

            //Step 2 template strings
            'str_step2Title' => 'Search Results - Online Booking',
            'str_availableRaces' => 'Available Races',
            'str_noRacesFound' => 'No races found. Try another date!',
            'str_spotsAvailableOnline' => 'spot(s) available online',
            'str_drivers' => 'Driver(s)',
            'str_each' => 'each',
            'str_bookIt' => 'Book It!',
            'str_youMustBeLoggedIn' => 'You must be logged in to book a race. Please select one of the following options:',
            'str_createANewAccount' => 'Create A New Account',
            'str_loginWithFacebook' => 'Login with Facebook',
            'str_loginToExistingAccount' => 'Login to Existing Account',
            'str_close' => 'Close',
            'str_emailsMustMatch' => 'Emails must match',
            'str_passwordsMustMatch' => 'Passwords must match',
            'str_accountInformation' => 'Account Information',
            'str_emailAddress' => 'Email Address',
            'str_confirmEmail' => 'Confirm Email',
            'str_iWantToReceiveSpecialOffers' => 'I want to receive race results and special offers via the e-mail provided.',
            'str_password' => 'Password',
            'str_confirmPassword' => 'Confirm Password',
            'str_personalInformation' => 'Personal Information',
            'str_company' => 'Company',
            'str_firstName' => 'First Name',
            'str_lastName' => 'Last Name',
            'str_racerName' => 'Racer Name',
            'str_birthDate' => 'Birth Date',
            'str_gender' => 'Gender',
            'str_male' => 'Male',
            'str_female' => 'Female',
            'str_other' => 'Other',
            'str_whereDidYouHearAboutUs' => 'Where did you hear about us?',
            'str_addressLine1' => 'Address line 1',
            'str_addressLine2' => 'Address line 2',
            'str_city' => 'City',
            'str_state' => 'State/Province/Territory',
            'str_postalCode' => 'Postal Code',
            'str_country' => 'Country',
            'str_cell' => 'Cell',
            'str_licenseNumber' => 'License #',
            //NOTE: Custom field labels still come from an old, cavernous Club Speed setting, and cannot be localized
            'str_createAccount' => 'Create Account',
            'str_loginToYourExistingAccount' => 'Login to Your Existing Account',
            'str_resetPassword' => 'Claim My Account / Reset My Password',
            'str_login' => 'Login',
            'str_alreadyRegisteredTextPart1' => "Already registered at a track but don't have a password, or don't remember your password? No problem! Just head on over to",
            'str_alreadyRegisteredTextPart2' => "and get yourself a new password!",
            'str_thisFieldIsRequired' => 'This field is required.',
            'str_mustBeAValidEmail' => 'Must be a valid e-mail.',

            //Cart template strings
            'str_cartTitle' => 'Shopping Cart - Online Booking',
            'str_shoppingCart' => 'Shopping Cart',
            'str_hasBeenAddedToCart' => 'has been added to your shopping cart.',
            'str_hasBeenRemovedFromCart' => 'has been removed from your shopping cart.',
            'str_unableToAddToCart' => 'Unable to add your item to cart. The item may no longer be available.',
            'str_itemExpired' => 'One or more items in your cart have expired and have been removed.',
            'str_raceName' => 'Race Name',
            'str_racers' => 'Racers',
            'str_startTime' => 'Start Time',
            'str_price' => 'Price',
            'str_subtotal' => 'Subtotal',
            'str_tax' => 'Tax',
            'str_total' => 'Total',
            'str_remove' => 'Remove',
            //NOTE: The names of actual heats and products are not localized
            'str_order' => 'Order',
            'str_proceedToCheckout' => 'Proceed to Checkout',
            'str_yourCartIsEmpty' => 'Your cart is empty.',
            'str_youMustBe' => 'You must be',
            'str_loggedIn' => 'logged in',
            'str_toViewTheCart' => 'to view the cart.',

            //Checkout template strings
            'str_checkoutTitle' => 'Checkout - Online Booking',
            'str_paymentInformation' => 'Payment Information',
            'str_creditCardNumber' => 'Credit Card Number',
            'str_cvv' => 'CVV',
            'str_expirationMonth' => 'Expiration Month',
            'str_expirationYear' => 'Expiration Year',
            'str_phone' => 'Phone',
            'str_email' => 'Email',
            'str_orderSummary' => 'Order Summary',
            'str_termsAndConditions' => 'Terms and Conditions',
            'str_iAgreeToTheTermsAndConditions' => 'I agree to the Terms & Conditions.',
            'str_pleaseAgreeToTheTermsAndConditions' => 'Please agree to the Terms & Conditions',
            'str_makePayment' => 'Make Payment',

            //Footer template strings
            'str_youAreLoggedInAs' => 'You are logged in as',
            'str_logout' => 'Logout',

            //Login template strings
            'str_loginTitle' => 'Login - Online Booking',

            //Login facebook template strings
            'str_loginFacebookTitle' => 'Facebook Login - Online Booking',
            'str_connectingToFacebook' => 'Connecting to Facebook...',
            'str_redirectingInAMoment' => 'Redirecting in a moment...',
            'str_problemConnectingToFacebookPart1' => 'There was a problem connecting to your Facebook account. Please go back and try again, or',
            'str_problemConnectingToFacebookPart2' => 'to book your race',

            //Reset password template strings
            'str_resetPasswordTitle' => 'Reset My Password - Online Booking',
            'str_passwordResetClaimAccount' => 'Password Reset / Claim Account',
            'str_enterEmailToResetPassword' => 'Enter your e-mail address below to reset your password',
            'str_resetMyPassword' => 'Reset My Password',
            'str_successPasswordReset' => 'Success! If your e-mail address has an account, a password reset link has been sent to it. Please open that e-mail and click the link inside to continue.',

            //Reset password form template strings
            'str_resetPasswordFormTitle' => 'Pick A New Password - Online Booking',
            'str_passwordResetForm' => 'Password Reset Form',
            'str_pleaseChooseANewPassword' => 'Please choose a new password',
            'str_newPassword' => 'New Password',
            'str_confirmNewPassword' => 'Confirm New Password',
            'str_successPasswordResetFinal' => 'Success! Your password has been reset.',
            'str_redirectingToSearchPage' => 'Redirecting to the search page.',
            'str_invalidResetLink' => 'The authorization link you used is invalid or expired.',
            'str_please' => 'Please',
            'str_requestANewPasswordLink' => 'request a new password link',
            'str_passwordsDoNotMatch' => 'Passwords do not match',

            //Success template strings
            'str_successTitle' => 'Success! - Online Booking',
            'str_success' => 'Success!',
            'str_thankYouForYourOrder' => 'Thank you for your order',
            'str_weWillSeeYouOnTheTrack' => "We'll see you on the track!",
            'str_yourPaymentConfirmationNumberIs' => 'Your payment confirmation number is',
            'str_pleasePrintThisPageForYourRecords' => 'Please print this page for your records.',

            //CheckoutController strings
            'str_email.required' => 'Your e-mail address is required.',
            'str_email.email' => 'Please enter a valid e-mail address.',
            'str_firstName.required' => 'First name is required.',
            'str_lastName.required' => 'Last name is required.',
            'str_number.required' => 'Your credit card number is required.',
            'str_cvv.required' => 'Your CVV number is required.',
            'str_expiryMonth.required' => 'Expiration month is required.',
            'str_expiryYear.required' => 'Expiration year is required.',
            'str_address1.required' => 'Address line 1 is required.',
            'str_city.required' => 'City is required.',
            'str_state.required' => 'State is required.',
            'str_postcode.required' => 'Postal/zip code is required.',
            'str_country.required' => 'Country is required.',
            'str_oneOrMoreItemsExpiredDuringPayment' => 'One or more items in your cart expired during payment.',
            'str_paymentDeclined' => 'Your payment was declined. Please try again.',

            //CreateAccountController strings
            'str_password.required' => 'The Password field is required.',
            'str_company.required' => 'The Company field is required.',
            'str_racerName.required' => 'The Racer Name field is required.',
            'str_address.required' => 'The Address field is required.',
            'str_phone.required' => 'The Phone field is required.',
            'str_licenseNumber.required' => 'The License Number field is required.',
            'str_genericField.required' => 'Please fill out the missing field(s).',
            'str_birthDate.required' => 'Your birth date is required.',
            'str_emailAlreadyRegistered' => 'This e-mail has already been registered.',
            'str_toResetYourPassword' => 'To reset your password or claim your account,',
            'str_clickHere' => 'click here',
            'str_errorCreatingAccount' => 'This was an error creating your account. Please try again later.',
            'str_errorCreatingAccountUnknown' => 'This was an error creating your account or your e-mail has already been registered.',

            //LoginController strings
            'str_incorrectUsernameOrPassword' => 'Incorrect username or password.',
            'str_accountCreationForced' => 'This track requires you to create an account before using Facebook login. Please create one below using the same e-mail address as your Facebook account.',

            //Step2Controller strings
            'str_pleaseSelectARaceDate' => 'Please select a race date.',

            'str_addMoreRaces' => 'Add More Races',

            //Buy Gift Cards
            'str_giftCardsTitle' => 'Buy Gift Cards',
            'str_chooseAGiftCard' => 'Add a Gift Card to Cart',
            'str_noGiftCardsAvailable' => 'No gift cards are available for sale at this time.<br/> Please try again later.',
            'str_buyIt' => 'Buy it!',
            'str_youMustBeLoggedInGiftCard' => 'You must be logged in to buy a gift card. Please select one of the following options:',

            'str_productName' => 'Product',
            'str_quantity' => 'Quantity',
            'str_noHeatItemsInCart' => 'You currently have no races in your cart.',
            'str_noProductsInCart'  => 'You currently have no products in your cart.',
            'str_races' => 'Races',
            'str_products' => 'Products',
            'str_summary' => 'Summary'

            //'str_' => '',
        );

        //The supported cultures and their display values in their respective languages
        self::$cultureNames = array(
            'en-US' => 'English (US)',
            'en-GB' => 'English (UK)',
            'en-NZ' => 'English (NZ)',
            'en-AU' => 'English (AU)',
            'en-IE' => 'English (IE)',
            'en-CA' => 'English (CA)',
            'es-MX' => 'Español',
            'es-CR' => 'Español (CR)',
            'es-ES' => 'Castellano',
            'es-PR' => 'Español (PR)',
            'ru-RU' => 'Pусский язык',
            'fr-CA' => 'Français',
            'de-DE' => 'Deutsch',
            'nl-NL' => 'Nederlands',
            'pl-PL' => 'Język polski',
            'da-DK' => 'Dansk',
            'ar-AE' => 'العربية',
            'it-IT' => 'Italiano',
            'bg-BG' => 'български език',
            'sv-SE' => 'Svenska'
        );

        //Error codes. Used for sparingly few API calls.
        self::$errorCodes = array(
            'emailAlreadyExists' => 'ERR001'
        );

        self::$defaultEnglish['cultureNames'] = self::$cultureNames;

        if (!Session::has('currentCulture'))
        {
            Session::put('currentStrings',self::$defaultEnglish);
            Session::put('currentCulture','en-US');
        }
        else
        {
            $translations = Session::get('translations');
            $currentCulture = Session::get('currentCulture');
            if (isset($translations[$currentCulture]))
            {
                Session::put('currentStrings',$translations[$currentCulture]);
            }
            else
            {
                Session::put('currentStrings',self::$defaultEnglish);
                Session::put('currentCulture','en-US');
            }
        }
        self::$initialized = true;
    }
}
