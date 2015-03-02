<?php

require_once(app_path().'/includes/includes.php');

class MobileAppController extends BaseController
{
    public $image_directory;
    public $image_filenames;
    public $image_paths;
    public $image_urls;

    public function __construct() {
			//Image uploader data
      if (getenv('SERVER_ADDR') == '192.168.111.205'){
        // Ronnie's debugging directory
        $this->image_directory = '\\\\192.168.111.122\\c$\\clubspeedapps\\assets\\MobileApp\\icons';
      } else {
        $this->image_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'mobileApp' . DIRECTORY_SEPARATOR . 'icons';
      }

      $this->image_paths = array();
      $this->image_urls = array();

      //JS and CSS uploader data
      $this->js_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'mobileApp' . DIRECTORY_SEPARATOR . 'js';
      $this->css_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'mobileApp' . DIRECTORY_SEPARATOR . 'css';
      $this->js_path = $this->js_directory . DIRECTORY_SEPARATOR . 'custom-js.js';
      $this->js_url = '/assets/mobileApp/js/' . 'custom-js.js';
      $this->css_path = $this->css_directory . DIRECTORY_SEPARATOR . 'custom-styles.css';
      $this->css_url = '/assets/mobileApp/css/' . 'custom-styles.css';

      $standardNote = 'The following can be inserted into this template:<br/><br/>'
                    . '<b>Order #:</b> {{checkId}}<br/>'
                    . '<b>Customer\'s First Name:</b> {{customer}}<br/>'
                    . '<b>Your Business Name:</b> {{business}}<br/>'
                    . '<b>Item Description:</b> {{detail.description}}<br/>'
                    . '<b>Item Quantity:</b> {{detail.quantity}}<br/>'
                    . '<b>Item Price:</b> {{detail.price}}<br/>'
                    . '<b>Subtotal:</b> {{checkSubtotal}}<br/>'
                    . '<b>Estimated Tax:</b> {{checkTax}}<br/>'
                    . '<b>Gift Card Deduction:</b> {{giftCardTotal}}<br/>'
                    . '<b>Total:</b> {{checkTotal}}';

      // Mobile App Templates
      $this->templates = array(
        (object)array(
          'displayName' => 'Track Info (HTML)',
          'templateNamespace' => 'MobileApp',
          'templateName' => 'trackInfoHtml',
          'isHtml' => true,
          'note' => $standardNote
        )
       );
    }

    public function index()
    {
        return View::make('/screens/mobileApp/menuItems',array('controller' => 'MobileAppController'));
    }

    public function menuItems()
    {
        return View::make('/screens/mobileApp/menuItems', array(
          'controller' => 'MobileAppController'
        ));
    }


    public function settings()
    {
        $mobileSettings = CS_API::getSettingsFromNewTableFor('MobileApp');
        if ($mobileSettings === null)
        {
            return Redirect::to('/disconnected');
        }
        $mobileSettingsCheckedData = array();
        $mobileSettingsData = array();
        $mobileSettingsIds = array();
        foreach($mobileSettings->settings as $setting)
        {
            $mobileSettingsCheckedData[$setting->name] = ($setting->value ? 'checked' : '');
            $mobileSettingsData[$setting->name] = $setting->value;
            $mobileSettingsIds[$setting->name] = $setting->settingsId;
        }

        $listOfTracks = CS_API::getListOfTracks();

        Session::put('mobileSettings',$mobileSettingsData);
        Session::put('mobileSettingsIds',$mobileSettingsIds);

        return View::make('/screens/mobileApp/settings',
            array('controller' => 'MobileAppController',
                'isChecked' => $mobileSettingsCheckedData,
                'mobileSettings' => $mobileSettingsData,
                'listOfTracks' => $listOfTracks
            ));
    }

    public function updateSettings()
    {
        $input = Input::all();

        //Begin formatting form input for processing - defaults available for any missing settings
        $newSettings = array();
        $newSettings['enableFacebook'] = isset($input['enableFacebook']) ? 1 : 0;
        $newSettings['forceLogin'] = isset($input['forceLogin']) ? 1 : 0;
        $newSettings['defaultApiKey'] = isset($input['defaultApiKey']) ? $input['defaultApiKey'] : '';
        $newSettings['defaultTrack'] = isset($input['defaultTrack']) ? $input['defaultTrack'] : 1;
        //End formatting

        //Identify the settings that actually changed and need to be sent to Club Speed
        $currentSettings = Session::get('mobileSettings',array());
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

        $mobileSettingsIds = Session::get('mobileSettingsIds',array());
        $result = CS_API::updateSettingsInNewTableFor('mobileApp',$newSettings,$mobileSettingsIds);

        if ($result === false)
        {
            return Redirect::to('mobileApp/settings')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('mobileApp/settings')->with( array('message' => 'Settings updated successfully!'));
    }

    public function templates()
    {
        $mobileAppTemplates = CS_API::getJSON('settings', array('namespace' => 'mobileApp'))->settings;

        // merge this controller's mobileApp template settings with mobileApp template values from API
        // into array to be used to populate editor form
        $templateFormData = array();

        $apiTemplateNames = array_map(
          function($template){
            return $template->name;
          },
          $mobileAppTemplates
        );

        foreach($this->templates as $id => $template) {
          $matchingApiTemplateKey = array_search($template->templateName, $apiTemplateNames);
          if ($matchingApiTemplateKey !== false){
            $templateToPush = $template;
            $templateToPush->name = $id;  // form looks for name property instead of $id. todo: leave as $id and use $id in form?
            $templateToPush->settingsId = $mobileAppTemplates[$matchingApiTemplateKey]->settingsId;
            $templateToPush->value = $mobileAppTemplates[$matchingApiTemplateKey]->value;
            array_push($templateFormData, $templateToPush);
          }
        }

        Session::put('templates', $templateFormData);

        return View::make(
          '/screens/mobileApp/templates',
          array(
            'controller' => 'MobileAppController',
            'templates' => $templateFormData,
            'currentTemplate' => 0 // unused; will possibly be removed
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

      $result = true; // default case of saving without making any changes is a successful result, so init $result to true
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
        return Redirect::to('mobileApp/templates')->with( array('error' => 'One or more templates could not be updated. Please try again.'));
      }
      else if ($result === null)
      {
        return Redirect::to('/disconnected');
      }

      return Redirect::to('mobileApp/templates')->with( array('message' => 'Template(s) updated successfully!'));
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
        return Response::json(array('error' => 'The provided file was not an image.'), 412);
      } else {
        // SAVE THE FILE...

        // Ensure the directory exists, if not, create it!
        if(!is_dir($this->image_directory)) mkdir($this->image_directory, null, true);

        // Move the file, overwriting if necessary
        Input::file('image')->move($this->image_directory, $filename);

        // Fix permissions on Windows (works on 2003?). This is because by default the uploaded imaged
        // does not inherit permissions from the folder it is moved to. Instead, it retains the
        // permissions of the temporary folder.
        exec('c:\windows\system32\icacls.exe ' . $this->image_directory . DIRECTORY_SEPARATOR . $filename . ' /inheritance:e');

        return Response::json(array('message' => 'Image uploaded successfully!'), 200);
      }
    }
}
