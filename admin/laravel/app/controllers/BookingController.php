<?php

require_once(app_path().'/includes/includes.php');

class BookingController extends BaseController
{
    public $image_directory;
    public $image_filenames;
    public $image_paths;
    public $image_urls;

    public function __construct() {

        //Image uploader data
        $this->image_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'booking' . DIRECTORY_SEPARATOR . 'images';
        $this->image_filenames = array('background.jpg','header.jpg');
        $this->image_paths = array();
        $this->image_urls = array();

        //JS and CSS uploader data
        $this->js_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'booking' . DIRECTORY_SEPARATOR . 'js';
        $this->css_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'booking' . DIRECTORY_SEPARATOR . 'css';
        $this->js_path = $this->js_directory . DIRECTORY_SEPARATOR . 'custom-js.js';
        $this->js_url = '/assets/booking/js/' . 'custom-js.js';
        $this->css_path = $this->css_directory . DIRECTORY_SEPARATOR . 'custom-styles.css';
        $this->css_url = '/assets/booking/css/' . 'custom-styles.css';

        foreach($this->image_filenames as $currentFileName)
        {
            $this->image_paths[$currentFileName] = $this->image_directory . DIRECTORY_SEPARATOR . $currentFileName;
            $this->image_urls[$currentFileName] = '/assets/booking/images/' . $currentFileName;
        }

      $standardNote = 'The following can be inserted into this template:<br/><br/>'
                    . '<b>Order #:</b> {{checkId}}<br/>'
                    . '<b>Customer\'s Name:</b> {{customer}}<br/>'
                    . '<b>Your Business Name:</b> {{business}}<br/>'
                    . '<b>Subtotal:</b> {{checkSubtotal}}<br/>'
                    . '<b>Estimated Tax:</b> {{checkTax}}<br/>'
                    . '<b>Gift Card Deduction:</b> {{giftCardTotal}}<br/>'
                    . '<b>Total:</b> {{checkTotal}}<br/><br/>'
                    . 'For an item list, create the HTML for one item and wrap it like so:<br/>'
                    . '<b>&#60;!-- {% for detail in details %} --&#62;<br/>'
                    . '&nbsp;&nbsp;&nbsp;&nbsp;INSERT ITEM HTML HERE<br/>'
                    . '&#60;!-- {% endfor %} --&#62;</b><br/>'
                    . 'You can use the following inside the item block:<br/>'
                    . '<b>Item Description:</b> {{detail.description}}<br/>'
                    . '<b>Item Quantity:</b> {{detail.quantity}}<br/>'
                    . '<b>Item Price:</b> {{detail.price}}';

        $giftCardEmailNote = 'The following can be inserted into this template:<br/><br/>'
            . '<b>Customer\'s Name:</b> {{customer}}<br/>'
            . '<b>Your Business Name:</b> {{business}}<br/>'
            . '<b>eGiftcard Number:</b> {{giftCardNo}}<br/>'
            . '<b>eGiftcard Barcode:</b> {{giftCardImage}}<br/>'
            . '<i class="fa fa-question-circle tip"
                            data-container="body" data-toggle="popover" data-placement="top" data-html="true"
                            data-content="
                                <div class=\'text-center\'><strong>Valid characters</strong></div>
                                <table class=\'table table-condensed table-mini\'>
                                    <thead>
                                        <tr>
                                            <th>Character</th>
                                            <th>Description</th>
                                            <th>Example</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>d</td>
                                            <td>Day of the month, 2 digits with leading zeros</td>
                                            <td>01 to 31</td>
                                        </tr>
                                        <tr>
                                            <td>D</td>
                                            <td>A textual representation of a day, three letters</td>
                                            <td>Mon through Sun</td>
                                        </tr>
                                        <tr>
                                            <td>l (lowercase L)</td>
                                            <td>A full textual representation of the day of the week</td>
                                            <td>Sunday through Saturday</td>
                                        </tr>
                                        <tr>
                                            <td>m</td>
                                            <td>Numeric representation of a month, with leading zeros</td>
                                            <td>01 through 12</td>
                                        </tr>
                                        <tr>
                                            <td>M</td>
                                            <td>A short textual representation of a month, three letters</td>
                                            <td>Jan through Dec</td>
                                        </tr>
                                        <tr>
                                            <td>F</td>
                                            <td>A full textual representation of a month, such as January or March</td>
                                            <td>January through December</td>
                                        </tr>
                                        <tr>
                                            <td>Y</td>
                                            <td>A full numeric representation of a year, 4 digits</td>
                                            <td>Examples: 1999 or 2003</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class=\'table-mini\'>
                                Also usable: Spaces, dashes, commas, and forward slashes.
                                </div>
                            ">
                        </i>'
        . ' <b>Date:</b> {{date:Y-m-d}}';

        $giftCardEmailSubjectNote = 'The following can be inserted into this template:<br/><br/>'
            . '<b>Your Business Name:</b> {{business}}';

      // Booking Templates
      $this->templates = array(
        (object)array(
          'displayName' => 'Online Booking E-mail Receipt (HTML)',
          'templateNamespace' => 'Booking',
          'templateName' => 'receiptEmailBodyHtml',
          'isHtml' => true, // TODO: have template from read setting type in database instead of from this
          'note' => 'Click the pencil icon to edit the source HTML.<br/><br/>' . $standardNote
        ),
        (object)array(
          'displayName' => 'Online Booking E-mail Receipt (TEXT)',
          'templateNamespace' => 'Booking',
          'templateName' => 'receiptEmailBodyText',
          'isHtml' => false, // TODO: have template from read setting type in database instead of from this
          'note' => $standardNote
        ),
        (object)array(
          'displayName' => 'Online Booking E-mail Subject Line',
          'templateNamespace' => 'Booking',
          'templateName' => 'receiptEmailSubject',
          'isHtml' => false, // TODO: have template from read setting type in database instead of from this
          'note' => $standardNote
        ),
        (object)array(
          'displayName' => 'Terms & Conditions',
          'templateNamespace' => 'Booking',
          'templateName' => 'termsAndConditions',
          'isHtml' => true, // TODO: have template from read setting type in database instead of from this
          'note' => ''
        ),
        (object)array(
          'displayName' => 'Gift Card E-mail (HTML)',
          'templateNamespace' => 'Booking',
          'templateName' => 'giftCardEmailBodyHtml',
          'isHtml' => true, // TODO: have template from read setting type in database instead of from this
          'note' => $giftCardEmailNote
        ),
        (object)array(
          'displayName' => 'Gift Card E-mail Subject Line',
          'templateNamespace' => 'Booking',
          'templateName' => 'giftCardEmailSubject',
          'isHtml' => false, // TODO: have template from read setting type in database instead of from this
          'note' => $giftCardEmailSubjectNote
        )
       );
    }

    public function index()
    {
        return View::make('/screens/booking/manage',array(
            'controller' => 'BookingController',
            'currentOnlineBookingState' => $this->currentOnlineBookingState()
        ));
    }

    public function settings()
    {
        $bookingSettings = CS_API::getSettingsFor('Booking');
        if ($bookingSettings === null)
        {
            return Redirect::to('/disconnected');
        }
        $bookingSettingsCheckedData = array();
        $bookingSettingsData = array();
        foreach($bookingSettings->settings as $setting)
        {
            $bookingSettingsCheckedData[$setting->SettingName] = ($setting->SettingValue ? 'checked' : '');
            $bookingSettingsData[$setting->SettingName] = $setting->SettingValue;
        }

        $supportsCacheClearing = CS_API::doesServerSupportCacheClearing();
        $bookingSettingsData['supportsCacheClearing'] = $supportsCacheClearing;

        Session::put('bookingSettings',$bookingSettingsData);

        $supportedCurrencies = array(
            'AUD' => 'Australian dollar',
            'BGN' => 'Bulgarian lev',
            'DKK' => 'Danish krone',
            'EUR' => 'Euro',
            'MXN' => 'Mexican peso',
            'NZD' => 'New Zealand dollar',
            'PLN' => 'Polish złoty',
            'GBP' => 'Pound sterling',
            'RUB' => 'Russian ruble',
            'SEK' => 'Swedish krona',
            'AED' => 'United Arab Emirates dirham',
            'USD' => 'United States dollar'
        );

        $supportedNumberLocales = array(
            'en_US' => 'English (US)',
            'en_GB' => 'English (UK)',
            'en_NZ' => 'English (NZ)',
            'en_AU' => 'English (AU)',
            'en_IE' => 'English (IE)',
            'en_CA' => 'English (CA)',
            'es_MX' => 'Español',
            'es_CR' => 'Español (CR)',
            'es_ES' => 'Castellano',
            'es_PR' => 'Español (PR)',
            'ru_RU' => 'Pусский язык',
            'fr_CA' => 'Français',
            'de_DE' => 'Deutsch',
            'nl_NL' => 'Nederlands',
            'pl_PL' => 'Język polski',
            'da_DK' => 'Dansk',
            'ar_AE' => 'العربية',
            'it_IT' => 'Italiano',
            'bg_BG' => 'български език',
            'sv_SE' => 'Svenska'
        );

				// Massage API country list into Laravel's expected format
				$apiCountries = CS_API::getCountries();
				$apiCountries = is_array($apiCountries) ? $apiCountries : array('' => 'Unable to Load Countries');
				$defaultPaymentCountries = array();
                $defaultPaymentCountries['ZZ'] = 'Please select an option below:';
				foreach($apiCountries as $country) {
				    $defaultPaymentCountries[$country->{'ISO_3166-1_Alpha_2'}] = $country->Name;
				}

        return View::make('/screens/booking/settings',
            array('controller' => 'BookingController',
                  'isChecked' => $bookingSettingsCheckedData,
                  'bookingSettings' => $bookingSettingsData,
                  'currentOnlineBookingState' => $this->currentOnlineBookingState(),
                  'supportedCurrencies' => $supportedCurrencies,
									'defaultPaymentCountries' => $defaultPaymentCountries,
                  'supportedNumberLocales' => $supportedNumberLocales,
                  'background_image_url' => is_file($this->image_paths['background.jpg']) ? $this->image_urls['background.jpg'] : null,
                  'header_image_url' => is_file($this->image_paths['header.jpg']) ? $this->image_urls['header.jpg'] : null,
                  'custom_css_url' => is_file($this->css_path) ? $this->css_url : null,
                  'custom_js_url' => is_file($this->js_path) ? $this->js_url : null,
            ));
    }

    private function currentOnlineBookingState()
    {
        $bookingSettings = CS_API::getSettingsFor('Booking');

        //Check if online booking is disabled
        if ($bookingSettings !== null)
        {
            $bookingSettingsData = array();
            foreach($bookingSettings->settings as $setting)
            {
                $bookingSettingsData[$setting->SettingName] = $setting->SettingValue;
            }
            if (isset($bookingSettingsData['registrationEnabled']) && !$bookingSettingsData['registrationEnabled'])
            {
                return 'disabled_manually';
            }
        }

        //Check if the payment processor is the dummy driver
        if (isset($bookingSettingsData['onlineBookingPaymentProcessorSettings'])
            && $bookingSettingsData['onlineBookingPaymentProcessorSettings'] != null)
        {
            $currentPaymentType = json_decode($bookingSettingsData['onlineBookingPaymentProcessorSettings']);
            if (isset($currentPaymentType->name))
            {
                $currentPaymentType = $currentPaymentType->name;
            }
            else
            {
                $currentPaymentType = "";
            }
            if ($currentPaymentType == "Dummy")
            {
                return 'disabled_dummypayments';
            }
        }

        //Check if the current culture is missing any strings

        $currentCulture = CS_API::getCurrentCultureForOnlineBooking();

        $translations = CS_API::getTranslations('Booking');

        if($currentCulture != 'en-US' && isset($translations['en-US']))
        {
            if (!isset($translations[$currentCulture]) || count($translations['en-US']) > count($translations[$currentCulture]))
            {
                return 'missing_translations';
            }
            foreach($translations[$currentCulture] as $stringName => $stringValue)
            {
                if ($stringValue == '')
                {
                    return 'missing_translations';
                }
            }
        }

        return 'enabled';
    }

    public function updateSettings()
    {
        $input = Input::all();

        //Begin data validation
        $rules = array(
            'reservationTimeout' => 'numeric',
            'bookingAvailabilityWindowBeginningInSeconds' => 'numeric',
            'bookingAvailabilityWindowEndingInSeconds' => 'numeric'
        );
        $messages = array(
            'reservationTimeout.integer' => 'The reservation timeout must be a number.',
            'bookingAvailabilityWindowBeginningInSeconds.integer' => 'The Earliest Booking Time Window must be a number.',
            'bookingAvailabilityWindowEndingInSeconds.integer' => 'The Latest Booking Time Window must be a number.'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('/booking/settings')->withErrors($validator);
        } //End data validation

        //Begin formatting form input for processing - defaults available for any missing settings
        $newSettings = array();
        $newSettings['addressRequired'] = isset($input['addressRequired']) ? 1 : 0;
        $newSettings['addressShown'] = isset($input['addressShown']) ? 1 : 0;
        $newSettings['birthDateRequired'] = isset($input['birthDateRequired']) ? 1 : 0;
        $newSettings['birthDateShown'] = isset($input['birthDateShown']) ? 1 : 0;
        $newSettings['cellRequired'] = isset($input['cellRequired']) ? 1 : 0;
        $newSettings['cellShown'] = isset($input['cellShown']) ? 1 : 0;
        $newSettings['cityRequired'] = isset($input['cityRequired']) ? 1 : 0;
        $newSettings['cityShown'] = isset($input['cityShown']) ? 1 : 0;
        $newSettings['companyRequired'] = isset($input['companyRequired']) ? 1 : 0;
        $newSettings['companyShown'] = isset($input['companyShown']) ? 1 : 0;
        $newSettings['countryRequired'] = isset($input['countryRequired']) ? 1 : 0;
        $newSettings['countryShown'] = isset($input['countryShown']) ? 1 : 0;
        $newSettings['custom1Required'] = isset($input['custom1Required']) ? 1 : 0;
        $newSettings['custom1Shown'] = isset($input['custom1Shown']) ? 1 : 0;
        $newSettings['custom2Required'] = isset($input['custom2Required']) ? 1 : 0;
        $newSettings['custom2Shown'] = isset($input['custom2Shown']) ? 1 : 0;
        $newSettings['custom3Required'] = isset($input['custom3Required']) ? 1 : 0;
        $newSettings['custom3Shown'] = isset($input['custom3Shown']) ? 1 : 0;
        $newSettings['custom4Required'] = isset($input['custom4Required']) ? 1 : 0;
        $newSettings['custom4Shown'] = isset($input['custom4Shown']) ? 1 : 0;
        $newSettings['emailRequired'] = isset($input['emailRequired']) ? 1 : 0;
        $newSettings['emailShown'] = isset($input['emailShown']) ? 1 : 0;
        $newSettings['genderRequired'] = isset($input['genderRequired']) ? 1 : 0;
        $newSettings['genderShown'] = isset($input['genderShown']) ? 1 : 0;
        $newSettings['licenseNumberRequired'] = isset($input['licenseNumberRequired']) ? 1 : 0;
        $newSettings['licenseNumberShown'] = isset($input['licenseNumberShown']) ? 1 : 0;
        $newSettings['racerNameRequired'] = isset($input['racerNameRequired']) ? 1 : 0;
        $newSettings['racerNameShown'] = isset($input['racerNameShown']) ? 1 : 0;
        $newSettings['stateRequired'] = isset($input['stateRequired']) ? 1 : 0;
        $newSettings['stateShown'] = isset($input['stateShown']) ? 1 : 0;
        $newSettings['whereDidYouHearAboutUsRequired'] = isset($input['whereDidYouHearAboutUsRequired']) ? 1 : 0;
        $newSettings['whereDidYouHearAboutUsShown'] = isset($input['whereDidYouHearAboutUsShown']) ? 1 : 0;
        $newSettings['zipRequired'] = isset($input['zipRequired']) ? 1 : 0;
        $newSettings['zipShown'] = isset($input['zipShown']) ? 1 : 0;
        $newSettings['registrationEnabled'] = isset($input['registrationEnabled']) ? 1 : 0;
        $newSettings['enableFacebook'] = isset($input['enableFacebook']) ? 1 : 0;
        $newSettings['forceRegistrationIfAuthenticatingViaThirdParty'] = isset($input['forceRegistrationIfAuthenticatingViaThirdParty']) ? 1 : 0;
        $newSettings['showTermsAndConditions'] = isset($input['showTermsAndConditions']) ? 1 : 0;
        $newSettings['sendReceiptCopyTo'] = isset($input['sendReceiptCopyTo']) ? $input['sendReceiptCopyTo'] : '';
        $newSettings['showLanguageDropdown'] = isset($input['showLanguageDropdown']) ? $input['showLanguageDropdown'] : 0;
        $newSettings['dateDisplayFormat'] = isset($input['dateDisplayFormat']) ? $input['dateDisplayFormat'] : 'Y-m-d';
        $newSettings['timeDisplayFormat'] = isset($input['timeDisplayFormat']) ? $input['timeDisplayFormat'] : 'H:i';
        $newSettings['currency'] = isset($input['currency']) ? $input['currency'] : 'USD';
        $newSettings['numberFormattingLocale'] = isset($input['numberFormattingLocale']) ? $input['numberFormattingLocale'] : 'en_US';
        $newSettings['maxRacersForDropdown'] = isset($input['maxRacersForDropdown']) ? $input['maxRacersForDropdown'] : 50;
        $newSettings['brokerFieldEnabled'] = isset($input['brokerFieldEnabled']) ? $input['brokerFieldEnabled'] : 0;
        $newSettings['brokerSourceInURLEnabled'] = isset($input['brokerSourceInURLEnabled']) ? $input['brokerSourceInURLEnabled'] : 0;
				$newSettings['defaultPaymentCountry'] = isset($input['defaultPaymentCountry']) ? $input['defaultPaymentCountry'] : '';

        if (isset($input['reservationTimeout']))
        {
            $newSettings['reservationTimeout'] = (int)($input['reservationTimeout']*60);
        }
        if (isset($input['bookingAvailabilityWindowBeginningInSeconds']))
        {
            $newSettings['bookingAvailabilityWindowBeginningInSeconds'] = (int)($input['bookingAvailabilityWindowBeginningInSeconds']*60);
        }
        if (isset($input['bookingAvailabilityWindowEndingInSeconds']))
        {
            $newSettings['bookingAvailabilityWindowEndingInSeconds'] = (int)($input['bookingAvailabilityWindowEndingInSeconds']*86400);
        } //End formatting

        //Identify the settings that actually changed and need to be sent to Club Speed
        $currentSettings = Session::get('bookingSettings',array());
        foreach($currentSettings as $currentSettingName => $currentSettingValue)
        {
            if (isset($newSettings[$currentSettingName]))
            {
                if ($newSettings[$currentSettingName] == $currentSettingValue) //If the setting hasn't changed
                {
                    unset($newSettings[$currentSettingName]); //Remove it from the list of new settings
                }
            }
        }

        //Only send settings that already exist in the API due to migrations having already been run
        foreach($newSettings as $newSettingName => $newSettingValue)
        {
            if (!isset($currentSettings[$newSettingName]))
            {
                unset($newSettings[$newSettingName]); //Remove any settings about to be sent that the API doesn't know about yet
            }
        }

        $result = CS_API::updateSettingsFor('Booking',$newSettings);

        if ($result === false)
        {
            return Redirect::to('booking/settings')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('booking/settings')->with( array('message' => 'Settings updated successfully!'));
    }

    public function updateImage()
    {
        // Build the input for our validation
        $input = array('image' => Input::file('image'));
        $filename = Input::get('filename');

        // Within the ruleset, make sure we let the validator know that this
        $rules = array(
            'image' => 'required|max:10000',
        );

        // Now pass the input and rules into the validator
        $validator = Validator::make($input, $rules);

        // Check to see if validation fails or passes
        if ($validator->fails()) {
            // VALIDATION FAILED
            return Redirect::to('booking/settings')->with('error', 'The provided file was not an image');
        } else {
            // SAVE THE FILE...

            // Ensure the directory exists, if not, create it!
            if(!is_dir($this->image_directory)) mkdir($this->image_directory, null, true);

            // Move the file, overwriting if necessary
            Input::file('image')->move($this->image_directory, $filename);

            // Fix permissions on Windows (works on 2003?). This is because by default the uplaoded imaged
            // does not inherit permissions from the folder it is moved to. Instead, it retains the
            // permissions of the temporary folder.
            exec('c:\windows\system32\icacls.exe ' . $this->image_paths[$filename] . ' /inheritance:e');

            return Redirect::to('booking/settings')->with('message', 'Image uploaded successfully!');
        }

    }

    public function updateFile()
    {
        // Build the input for our validation
        $input = array('customfile' => Input::file('customfile'));
        $filename = Input::get('filename');
        $filetype = Input::get('filetype');

        // Within the ruleset, make sure we let the validator know that this
        $rules = array(
            'customfile' => 'required|max:10000',
        );

        // Now pass the input and rules into the validator
        $validator = Validator::make($input, $rules);

        // Check to see if validation fails or passes
        if ($validator->fails()) {
            // VALIDATION FAILED
            return Redirect::to('booking/settings')->with('error', 'The provided file was not accepted.');
        } else {
            // SAVE THE FILE...
            $file_directory = "";
            $file_path = "";
            if ($filetype == 'js')
            {
                $file_directory = $this->js_directory;
                $file_path = $this->js_path;
            }
            else if ($filetype == 'css')
            {
                $file_directory = $this->css_directory;
                $file_path = $this->css_path;
            }
            // Ensure the directory exists, if not, create it!
            if(!is_dir($file_directory)) mkdir($file_directory, null, true);

            // Move the file, overwriting if necessary
            Input::file('customfile')->move($file_directory, $filename);

            // Fix permissions on Windows (works on 2003?). This is because by default the uplaoded imaged
            // does not inherit permissions from the folder it is moved to. Instead, it retains the
            // permissions of the temporary folder.
            exec('c:\windows\system32\icacls.exe ' . $file_path . ' /inheritance:e');

            return Redirect::to('booking/settings')->with('message', 'File uploaded successfully!');
        }

    }

    public function payments()
    {
        $supportedPaymentTypes = CS_API::getSupportedPaymentTypes();
        if ($supportedPaymentTypes === null)
        {
            return Redirect::to('/disconnected');
        }

        $bookingSettings = CS_API::getSettingsFor('Booking');
        if ($bookingSettings === null)
        {
            return Redirect::to('/disconnected');
        }
        $bookingSettingsData = array();
        foreach($bookingSettings->settings as $setting)
        {
            $bookingSettingsData[$setting->SettingName] = $setting->SettingValue;
        }
        if (isset($bookingSettingsData['onlineBookingPaymentProcessorSettings'])
            && $bookingSettingsData['onlineBookingPaymentProcessorSettings'] != null)
        {
            $currentPaymentType = json_decode($bookingSettingsData['onlineBookingPaymentProcessorSettings']);
            if (isset($currentPaymentType->name))
            {
                $currentPaymentType = $currentPaymentType->name;
            }
            else
            {
                $currentPaymentType = "";
            }
        }
        else
        {
            $currentPaymentType = "";
        }

        $currentSavedSettings = array();
        if (isset($bookingSettingsData['onlineBookingPaymentProcessorSavedSettings'])
            && $bookingSettingsData['onlineBookingPaymentProcessorSavedSettings'] != null)
        {
            $currentSavedSettings = json_decode($bookingSettingsData['onlineBookingPaymentProcessorSavedSettings'],true);
        }

        return View::make('/screens/booking/payments',
            array('controller' => 'BookingController',
                  'supportedPaymentTypes' => $supportedPaymentTypes,
                  'currentPaymentType' => $currentPaymentType,
                  'currentSavedSettings' => $currentSavedSettings,
                  'currentOnlineBookingState' => $this->currentOnlineBookingState()
            )
        );
    }

    public function updatePaymentSettings()
    {
        //Get all form input
        $input = Input::all();

        unset($input['_token']); //Removing Laravel's default form value
        $paymentProcessor = $input['paymentProcessor'];
        unset($input['paymentProcessor']); //Extracting paymentProcessor
        $newOptions = $input;

        //Get all supported payment types
        $supportedPaymentTypes = CS_API::getSupportedPaymentTypes();
        if ($supportedPaymentTypes === null)
        {
            return Redirect::to('/disconnected');
        }

        //Get current booking settings
        $bookingSettings = CS_API::getSettingsFor('Booking');
        if ($bookingSettings === null)
        {
            return Redirect::to('/disconnected');
        }
        $bookingSettingsData = array();
        foreach($bookingSettings->settings as $setting)
        {
            $bookingSettingsData[$setting->SettingName] = $setting->SettingValue;
        }

        //Get the current saved booking settings (might be blank to start)
        $currentSavedSettings = array();
        if (isset($bookingSettingsData['onlineBookingPaymentProcessorSavedSettings'])
            && $bookingSettingsData['onlineBookingPaymentProcessorSavedSettings'] != null)
        {
            $currentSavedSettings = json_decode($bookingSettingsData['onlineBookingPaymentProcessorSavedSettings'],true);
        }

        $newSavedSettings = array();

        //For each supportedPaymentType
        foreach($supportedPaymentTypes as $paymentType)
        {
            if (!isset($currentSavedSettings[$paymentType->name])) //If we don't have settings saved for that payment type yet
            {
                //Create a new (blank) entry
                $newSavedSettings[$paymentType->name] =
                                        array('name' => $paymentType->name,
                                              'options' => array()
                                        );

                //Insert their blank starting options
                foreach($paymentType->options as $option)
                {
                    $newSavedSettings[$paymentType->name]['options'][$option] = "";
                }
            }
            else //If we already have settings saved for that payment type
            {
                $newSavedSettings[$paymentType->name] = $currentSavedSettings[$paymentType->name]; //Copy them over!
            }
        }

        //Update the saved settings with what was submitted via the form
        $newSavedSettings[$paymentProcessor]['options'] = array();
        foreach($newOptions as $optionName => $optionValue)
        {
            $newSavedSettings[$paymentProcessor]['options'][$optionName] = $optionValue;
        }

        //Update the saved settings in the database
        $settingsToUpdate = array('onlineBookingPaymentProcessorSavedSettings' => json_encode($newSavedSettings));
        $result = CS_API::updateSettingsFor('Booking',$settingsToUpdate);
        if ($result === false)
        {
            return Redirect::to('booking/payments')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        //Extract the saved setting we're switching to
        $paymentProcessorSettings = $newSavedSettings[$paymentProcessor];

        //Switch to that processor's settings
        $settingsToUpdate = array('onlineBookingPaymentProcessorSettings' => json_encode($paymentProcessorSettings));

        $result = CS_API::updateSettingsFor('Booking',$settingsToUpdate);
        if ($result === false)
        {
            return Redirect::to('booking/payments')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        //Standard success message
        return Redirect::to('booking/payments')->with( array('message' => 'Settings updated successfully!'));
    }


    public function translations()
    {
        $supportedCultures = array(
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
            'fr-FR' => 'Français',
            'fr-CA' => 'Français (CA)',
            'de-DE' => 'Deutsch',
            'nl-NL' => 'Nederlands',
            'pl-PL' => 'Język polski',
            'da-DK' => 'Dansk',
            'ar-AE' => 'العربية',
            'it-IT' => 'Italiano',
            'bg-BG' => 'български език',
            'sv-SE' => 'Svenska',
            'zh-CN' => '中文'
        );

        $currentCulture = CS_API::getCurrentCultureForOnlineBooking();

        $translations = CS_API::getTranslations('Booking');

        return View::make('/screens/booking/translations',
            array('controller' => 'BookingController',
                'currentOnlineBookingState' => $this->currentOnlineBookingState(),
                'supportedCultures' => $supportedCultures,
                'currentCulture' => $currentCulture,
                'translations' => $translations
            )
        );
    }

    public function updateTranslations()
    {
        $input = Input::all();
        unset($input['_token']); //Removing Laravel's default form value
        $cultureKey = $input['cultureKey'];
        unset($input['cultureKey']);

        $input = $input['trans']; //HACK: PHP converts periods to underscores in _GET and _POST. Wrapping input names in an array gets around this behavior.

        //Format the missing string data as expected by Club Speed's API
        $updatedTranslations = array(); //Destined to a PUT
        $newTranslations = array(); //Destined to a POST
        foreach($input as $stringId => $stringValue)
        {
            if (isset($stringId))
            {
                if (!$this->contains($stringId,'new_'))
                {
                    $updatedTranslations[] = array(
                        'translationsId' => str_replace("id_","",$stringId),
                        'value' => $stringValue
                    );
                }
                else if ($stringValue != "")
                {
                    $newTranslations[] = array(
                        'name' => str_replace("id_new_","",$stringId),
                        'namespace' => 'Booking',
                        'value' => $stringValue,
                        'defaultValue' => $stringValue,
                        'culture' => $cultureKey,
                        'comment' => '');
                }
            }
        }

        $result = null;
        if (count($updatedTranslations) > 0)
        {
            $result = CS_API::updateTranslationsBatch($updatedTranslations);

            $updateWasSuccessful = ($result !== null);
            if ($updateWasSuccessful === false)
            {
                return Redirect::to('booking/translations')->with( array('error' => 'One or more translations could not be updated. Please try again.'));
            }
            else if ($updateWasSuccessful === null)
            {
                return Redirect::to('/disconnected');
            }
        }


        $result = null;
        if (count($newTranslations) > 0)
        {
            $result = CS_API::insertTranslationsBatch($newTranslations);

            $insertWasSuccessful = ($result !== null);
            if ($insertWasSuccessful === false)
            {
                return Redirect::to('booking/translations')->with( array('error' => 'One or more translations could not be created. Please try again.'));
            }
            else if ($insertWasSuccessful === null)
            {
                return Redirect::to('/disconnected');
            }
        }

        //Standard success message
        return Redirect::to('booking/translations')->with( array('message' => 'Translations updated successfully!'));

    }

    public function giftCardSales()
    {
        $bookingSettings = CS_API::getSettingsFor('Booking'); //Getting and packaging booking settings
        if ($bookingSettings === null)
        {
            return Redirect::to('/disconnected');
        }
        $bookingSettingsCheckedData = array();
        $bookingSettingsData = array();
        foreach($bookingSettings->settings as $setting)
        {
            $bookingSettingsCheckedData[$setting->SettingName] = ($setting->SettingValue ? 'checked' : '');
            $bookingSettingsData[$setting->SettingName] = $setting->SettingValue;
        }

        Session::put('bookingSettings',$bookingSettingsData);

        $giftCardProducts = CS_API::getGiftCardProducts(); //Get a list of ALL gift card products in Club Speed
        if (isset($giftCardProducts->products))
        {
            $giftCardProducts = $giftCardProducts->products;
        }
        else
        {
            $giftCardProducts = array();
        }

        $giftCardsAvailableForOnlineSale = array(); //Packaging list of gift cards to be made available online
        if (isset($bookingSettingsData['giftCardsAvailableForOnlineSale']))
        {
            $giftCardsAvailableForOnlineSale = $bookingSettingsData['giftCardsAvailableForOnlineSale'];
            $giftCardsAvailableForOnlineSale = json_decode($giftCardsAvailableForOnlineSale);
            if (isset($giftCardsAvailableForOnlineSale->giftCardProductIDs))
            {
                $giftCardsAvailableForOnlineSale = $giftCardsAvailableForOnlineSale->giftCardProductIDs;
            }
            else
            {
                $giftCardsAvailableForOnlineSale = array();
            }
        }

        //Merging the list of existing and enabled gift card products with whether or not they're available online
        $giftCardProductsMerged = array();
        foreach($giftCardProducts as $currentProduct)
        {
            if (isset($currentProduct->enabled) && $currentProduct->enabled == true)
            {
                $currentProductMerged = array(
                  'productId' => $currentProduct->productId,
                  'description' => $currentProduct->description,
                  'availableOnline' => in_array($currentProduct->productId,$giftCardsAvailableForOnlineSale)
                );
                $giftCardProductsMerged[] = $currentProductMerged;
            }
        }

        return View::make('/screens/booking/giftcardsales',
            array('controller' => 'BookingController',
                'currentOnlineBookingState' => $this->currentOnlineBookingState(),
                'isChecked' => $bookingSettingsCheckedData,
                'bookingSettings' => $bookingSettingsData,
                'giftCardProducts' => $giftCardProductsMerged
            )
        );

    }

    public function updateGiftCardSalesSettings()
    {
        $input = Input::all();

        //Begin formatting form input for processing - defaults available for any missing settings
        $newSettings = array();
        $newSettings['giftCardSalesEnabled'] = isset($input['giftCardSalesEnabled']) ? $input['giftCardSalesEnabled'] : false;

        $giftCardsAvailableForOnlineSale = array('giftCardProductIDs' => array());
        foreach($input as $currentInputKey => $currentInputValue)
        {
            if (strpos($currentInputKey,'giftCard_') !== false)
            {
                $giftCardsAvailableForOnlineSale['giftCardProductIDs'][] = str_replace('giftCard_','',$currentInputKey)   ;
            }
        }

        $newSettings['giftCardsAvailableForOnlineSale'] = json_encode($giftCardsAvailableForOnlineSale);

        //'giftCardsAvailableForOnlineSale' => '{"giftCardProductIDs": []}'

        //Identify the settings that actually changed and need to be sent to Club Speed
        $currentSettings = Session::get('bookingSettings',array());
        foreach($currentSettings as $currentSettingName => $currentSettingValue)
        {
            if (isset($newSettings[$currentSettingName]))
            {
                if ($newSettings[$currentSettingName] == $currentSettingValue) //If the setting hasn't changed
                {
                    unset($newSettings[$currentSettingName]); //Remove it from the list of new settings
                }
            }
        }

        $result = CS_API::updateSettingsFor('Booking',$newSettings);

        if ($result === false)
        {
            return Redirect::to('booking/giftcardsales')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('booking/giftcardsales')->with( array('message' => 'Settings updated successfully!'));
    }

    private static function contains(&$haystack, $needle)
    {
        $result = strpos($haystack, $needle);
        return $result !== false;
    }

    public function updateCulture($cultureKey)
    {
        $supportedCultures = array(
            'en-US',
            'en-GB',
            'en-NZ',
            'en-AU',
            'en-IE',
            'en-CA',
            'es-MX',
            'es-CR',
            'es-ES',
            'es-PR',
            'ru-RU',
            'fr-FR',
            'fr-CA',
            'de-DE',
            'nl-NL',
            'pl-PL',
            'da-DK',
            'ar-AE',
            'it-IT',
            'bg-BG',
            'sv-SE',
            'zh-CN'
        );


        if (!in_array($cultureKey,$supportedCultures))
        {
            return Redirect::to('booking/translations')->with( array('error' => 'The desired culture was not recognized and could not be updated. Please contact Club Speed Support.'));
        }

        $result = CS_API::updateSettingsFor('Booking',array('currentCulture' => $cultureKey));
        if ($result != true)
        {
            return Redirect::to('booking/translations')->with( array('error' => 'The current culture could not be updated. Please try again later. If the issue persists, contact Club Speed support.'));
        }

        //Standard success message
        return Redirect::to('booking/translations')->with( array('message' => 'Current culture updated successfully!'));
    }

    public function templates()
    {
        $bookingTemplates = CS_API::getJSON('settings', array('namespace' => 'booking'))->settings;

        // merge this controller's booking template settings with booking template values from API
        // into array to be used to populate editor form
        $templateFormData = array();

        $apiTemplateNames = array_map(
          function($template){
            return $template->name;
          },
          $bookingTemplates
        );

        foreach($this->templates as $id => $template) {
          $matchingApiTemplateKey = array_search($template->templateName, $apiTemplateNames);
          if ($matchingApiTemplateKey !== false){
            $templateToPush = $template;
            $templateToPush->name = $id;  // form looks for name property instead of $id. todo: leave as $id and use $id in form?
            $templateToPush->settingsId = $bookingTemplates[$matchingApiTemplateKey]->settingsId;
            $templateToPush->value = $bookingTemplates[$matchingApiTemplateKey]->value;
            array_push($templateFormData, $templateToPush);
          }
        }

        Session::put('templates', $templateFormData);

        return View::make(
          '/screens/booking/templates',
          array(
            'controller' => 'BookingController',
            'templates' => $templateFormData,
            'currentTemplate' => 0, // unused; will possibly be removed
            'currentOnlineBookingState' => $this->currentOnlineBookingState()
          )
        );
    }

    public function updateTemplates()
    {
      $input = Input::all();
      unset($input['_token']);
      unset($input['_wysihtml5_mode']); // remove this one weird hidden input field used by the wysihtml widget
      $newValues = $input;

      // Make and send API calls to update all changed templates
      $currentTemplates = Session::get('templates', array());

      $result = true; // default case of saving without making any changes is a successful result, so default $result to true
      // if even a single update request fails, reported result becomes false
      foreach($newValues as $id => $newValue){
        if ($currentTemplates[$id]->value != $newValue){
          $thisResult = CS_API::update('settings', $currentTemplates[$id]->settingsId, array('value' => $newValue));
          if (!$thisResult){
            $result = false;
          }
        }
      }

      if ($result === false)
      {
        return Redirect::to('booking/templates')->with( array('error' => 'One or more templates could not be updated. Please try again.'));
      }
      else if ($result === null)
      {
        return Redirect::to('/disconnected');
      }

      return Redirect::to('booking/templates')->with( array('message' => 'Template(s) updated successfully!'));
    }

    public function logs()
    {
      return View::make('/screens/booking/logs', array(
        'controller' => 'BookingController'
      ));
    }

    public function data()
    {
      $params = Input::get();
      $params['model'] = 'logs';
      $params['where']['terminal'] = 'Club Speed Online Booking'; //'Facebook';//

      $data = CS_API::getDataTableData($params);

      return $data;
    }
}
