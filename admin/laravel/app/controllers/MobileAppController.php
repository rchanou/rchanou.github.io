<?php

require_once(app_path().'/includes/includes.php');

class MobileAppController extends BaseController
{
    public function __construct() {
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
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }
        return View::make('/screens/mobileApp/menuItems',array('controller' => 'MobileAppController'));
    }

    public function menuItems()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

        return View::make('/screens/mobileApp/menuItems', array(
          'controller' => 'MobileAppController'
        ));
    }

    public function updateSettings()
    {
        $input = Input::all();

        // TODO: INPUT VALIDATION AND PARSING TO CONSTRUCT API CALL

        $result = true; // TODO: REPLACE WITH API CALL

        if ($result === false)
        {
            return Redirect::to('mobileApp/settings')->with( array('error' => 'One or more menu items could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('mobileApp/settings')->with( array('message' => 'Settings updated successfully!'));
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
}
