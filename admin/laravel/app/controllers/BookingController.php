<?php

require_once(app_path().'/includes/includes.php');

class BookingController extends BaseController
{
    public function __construct() {
      $standardNote = 'The following can be inserted into this template:<br/><br/>'
                    . '<b>Order #:</b> {{checkId}}<br/>'
                    . '<b>Customer\'s First Name:</b> {{customer}}<br/>'
                    . '<b>Your Business Name:</b> {{business}}<br/>'
                    . '<b>Item Description:</b> {{detail.name}}<br/>'
                    . '<b>Item Quantity:</b> {{detail.quantity}}<br/>'
                    . '<b>Item Price:</b> {{detail.price}}<br/>'
                    . '<b>Subtotal:</b> {{checkSubtotal}}<br/>'
                    . '<b>Estimated Tax:</b> {{checkTax}}<br/>'
                    . '<b>Gift Card Deduction:</b> {{giftCardTotal}}<br/>'
                    . '<b>Total:</b> {{checkTotal}}';
      
      // Booking Templates   
      $this->templates = array(
        (object)array(
          'displayName' => 'Online Booking E-mail Receipt (HTML)',
          'templateNamespace' => 'Booking',
          'templateName' => 'receiptEmailBodyHtml',
          'isHtml' => true,
          'note' => $standardNote
        ),
        (object)array(
          'displayName' => 'Online Booking E-mail Receipt (TEXT)',
          'templateNamespace' => 'Booking',
          'templateName' => 'receiptEmailBodyText',
          'isHtml' => false,
          'note' => $standardNote
        ),
        (object)array(
          'displayName' => 'Online Booking E-mail Subject Line',
          'templateNamespace' => 'Booking',
          'templateName' => 'receiptEmailSubject',
          'isHtml' => false,
          'note' => $standardNote
        ),
        (object)array(
          'displayName' => 'Terms & Conditions',
          'templateNamespace' => 'Booking',
          'templateName' => 'termsAndConditions',
          'isHtml' => true,
          'note' => ''
        )
       );
    }

    public function index()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }
        return View::make('/screens/booking/manage',array(
            'controller' => 'BookingController',
            'currentOnlineBookingState' => $this->currentOnlineBookingState()
        ));
    }

    public function settings()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

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

        Session::put('bookingSettings',$bookingSettingsData);

        return View::make('/screens/booking/settings',
            array('controller' => 'BookingController',
                  'isChecked' => $bookingSettingsCheckedData,
                  'bookingSettings' => $bookingSettingsData,
                  'currentOnlineBookingState' => $this->currentOnlineBookingState()
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

        return 'enabled';
    }

    public function updateSettings()
    {
        $input = Input::all();

        //Begin data validation
        $rules = array(
            'reservationTimeout' => 'integer',
            'bookingAvailabilityWindowBeginningInSeconds' => 'integer',
            'bookingAvailabilityWindowEndingInSeconds' => 'integer'
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

        //Begin formatting form input for processing
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

        if (isset($input['reservationTimeout']))
        {
            $newSettings['reservationTimeout'] = $input['reservationTimeout'];
        }
        if (isset($input['bookingAvailabilityWindowBeginningInSeconds']))
        {
            $newSettings['bookingAvailabilityWindowBeginningInSeconds'] = $input['bookingAvailabilityWindowBeginningInSeconds'];
        }
        if (isset($input['bookingAvailabilityWindowEndingInSeconds']))
        {
            $newSettings['bookingAvailabilityWindowEndingInSeconds'] = $input['bookingAvailabilityWindowEndingInSeconds'];
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

    public function payments()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

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

        //TODO: Just for test purposes
        /*$currentSavedSettings = '{"Dummy": {"name": "Dummy","options": {}}, "SagePay_Direct": {"name":"SagePay_Direct", "options": {"vendor":"clubspeed3","simulatorMode":"true","testMode":"false"}} }';
        $currentSavedSettings = json_decode($currentSavedSettings,true);
        $currentPaymentType = "SagePay_Direct";*/

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

    public function templates()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

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
      foreach($newValues as $id => $newValue)
      {
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
}