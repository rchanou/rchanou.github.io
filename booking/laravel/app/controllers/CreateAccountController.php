<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class CreateAccountController
 *
 * This controller handles the creation of a new Club Speed account.
 * This is done in the context of a user creating an account while trying to book a specific heat.
 * Upon account creation, the user is logged in and the desired heat is immediately added to cart.
 */
class CreateAccountController extends BaseController
{
    public function entry()
    {
        $input = Input::all();
        $strings = Strings::getStrings();

        $heatId = isset($input['heatId']) ? $input['heatId'] : null; //Heat that the user intends to book after creating the account
        $productId = isset($input['productId']) ? $input['productId'] : null; //Product that the user intends to book after creating the account
        $source = isset($input['source']) ? $input['source'] : 'step1';

        $settings = Session::get('settings');

        //DATA VALIDATION
        $rules = array();
        $messages = array();

        if ($settings['emailShown'])
        {
            if ($settings['emailRequired'])
            {
                $rules['EmailAddress'] = 'required|email';
                $rules['EmailAddressConfirmation']  = 'required|email';
                $messages['EmailAddress.required'] = $strings['str_email.required'];
                $messages['EmailAddress.email'] = $strings['str_email.email'];
                $messages['EmailAddressConfirmation.required'] = $strings['str_email.required'];
                $messages['EmailAddressConfirmation.email'] = $strings['str_email.email'];
            }
            else
            {
                $rules['EmailAddress'] = 'email';
                $rules['EmailAddressConfirmation']  = 'email';
                $messages['EmailAddress.email'] = $strings['str_email.required'];
                $messages['EmailAddressConfirmation.email'] = $strings['str_email.email'];
            }
        }
        if ($settings['passwordRequired'])
        {
            $rules['Password'] = 'required';
            $rules['PasswordConfirmation'] = 'required';
            $messages['Password.required'] = $strings['str_password.required'];
            $messages['PasswordConfirmation.required'] = $strings['str_password.required'];

        }
        if ($settings['companyRequired'])
        {
            $rules['Company'] = 'required';
            $messages['Company.required'] = $strings['str_company.required'];
        }
        if ($settings['firstNameRequired'])
        {
            $rules['FName'] = 'required';
            $messages['FName.required'] = $strings['str_firstName.required'];
        }
        if ($settings['lastNameRequired'])
        {
            $rules['LName'] = 'required';
            $messages['LName.required'] = $strings['str_lastName.required'];
        }
        if ($settings['racerNameRequired'])
        {
            $rules['RacerName'] = 'required';
            $messages['RacerName.required'] = $strings['str_racerName.required'];
        }
        if ($settings['addressRequired'])
        {
            $rules['Address'] = 'required';
            $messages['Address.required'] = $strings['str_address.required'];
        }
        if ($settings['cityRequired'])
        {
            $rules['City'] = 'required';
            $messages['City.required'] = $strings['str_city.required'];
        }
        if ($settings['stateRequired'])
        {
            $rules['State'] = 'required';
            $messages['State.required'] = $strings['str_state.required'];
        }
        if ($settings['zipRequired'])
        {
            $rules['Zip'] = 'required';
            $messages['Zip.required'] = $strings['str_postcode.required'];
        }
        if ($settings['countryRequired'])
        {
            $rules['Country'] = 'required';
            $messages['Country.required'] = $strings['str_country.required'];
        }
        if ($settings['cellRequired'])
        {
            $rules['Cell'] = 'required';
            $messages['Cell.required'] = $strings['str_phone.required'];
        }
        if ($settings['licenseNumberRequired'])
        {
            $rules['LicenseNumber'] = 'required';
            $messages['LicenseNumber.required'] = $strings['str_licenseNumber.required'];
        }
        if ($settings['custom1Required'])
        {
            $rules['Custom1'] = 'required';
            $messages['Custom1.required'] = $strings['str_genericField.required'];
        }
        if ($settings['custom2Required'])
        {
            $rules['Custom2'] = 'required';
            $messages['Custom2.required'] = $strings['str_genericField.required'];
        }
        if ($settings['custom3Required'])
        {
            $rules['Custom3'] = 'required';
            $messages['Custom3.required'] = $strings['str_genericField.required'];
        }
        if ($settings['custom4Required'])
        {
            $rules['Custom4'] = 'required';
            $messages['Custom4.required'] = $strings['str_genericField.required'];
        }

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails())
        {
            if (array_key_exists('source',$input) && $input['source'] == "step2")
            {
                $createAccountErrors = array();
                $createAccountErrors[$input['heatId']] = $validator->errors()->all();
                return Redirect::to("/step2?create=$heatId#$heatId")->with(array('createAccountErrors' => $createAccountErrors));
            }
            else if (array_key_exists('source',$input) && $input['source'] == "login")
            {
                return Redirect::to('/login')->withErrors($validator);
            }
            else
            {
                return Redirect::to('/step1')->withErrors($validator);
            }
        }

        $customerData = $this->convertCustomerDataToClubSpeedFormat($input);

        $createAccountErrors = array();
        $createAccountErrors[$heatId] = array();

        if ($customerData['birthdate'] == "") //Enforce birthdate requirement
        {
            $createAccountErrors[$heatId][] = $strings['str_birthDate.required'];

            if (array_key_exists('source',$input) && $input['source'] == "step2")
            {
                return Redirect::to("/step2?create=$heatId#$heatId")->with(array('createAccountErrors' => $createAccountErrors));
            }
            else if (array_key_exists('source',$input) && $input['source'] == "login")
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_birthDate.required']);
                return Redirect::to('/login')->withErrors($messages);
            }
            else
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_birthDate.required']);
                return Redirect::to('/step1')->withErrors($messages);
            }
        }
        if ($input['Password'] != $input['PasswordConfirmation'] ) //Enforce password matching requirement
        {
            $createAccountErrors[$heatId][] = $strings['str_passwordsDoNotMatch'];

            if (array_key_exists('source',$input) && $input['source'] == "step2")
            {
                return Redirect::to("/step2?create=$heatId#$heatId")->with(array('createAccountErrors' => $createAccountErrors));
            }
            else if (array_key_exists('source',$input) && $input['source'] == "login")
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_passwordsDoNotMatch']);
                return Redirect::to('/login')->withErrors($messages);
            }
            else
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_passwordsDoNotMatch']);
                return Redirect::to('/step1')->withErrors($messages);
            }
        }
        if ($input['EmailAddress'] != $input['EmailAddressConfirmation'] ) //Enforce password matching requirement
        {
            $createAccountErrors[$heatId][] = $strings['str_emailsMustMatch'];

            if (array_key_exists('source',$input) && $input['source'] == "step2")
            {
                return Redirect::to("/step2?create=$heatId#$heatId")->with(array('createAccountErrors' => $createAccountErrors));
            }
            else if (array_key_exists('source',$input) && $input['source'] == "login")
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_emailsMustMatch']);
                return Redirect::to('/login')->withErrors($messages);
            }
            else
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_emailsMustMatch']);
                return Redirect::to('/step1')->withErrors($messages);
            }
        }

        //If we've made it this far, the form data has passed validation

        $response = CS_API::createClubSpeedAccount($customerData); //Create the customer account

        if ($response === null) //Redirect to the Disconnected page on error
        {
            return Redirect::to('/disconnected');
        }
        else if (!is_numeric($response)) //If we received an error message
        {
            $errorCodes = Strings::getErrorCodes();
            $createAccountErrors = array();
            $createAccountErrors[$heatId] = array();

            if (str_contains($response,$errorCodes['emailAlreadyExists']) || str_contains($response,'Precondition Failed: Customer create found an email which already exists!')) //If the creation failed because an account already existed, let the user know
            {
                $createAccountErrors[$heatId][] = $strings['str_emailAlreadyRegistered'] . ' <br/>' . $strings['str_toResetYourPassword'] . ' ' . link_to('resetpassword',$strings['str_clickHere']) . '.';
            }
            else
            {
                $createAccountErrors[$heatId][] = $strings['str_errorCreatingAccount'];

            }
            if ($source == "step2")
            {
                return Redirect::to("/step2?create=$heatId#$heatId")->with(array('createAccountErrors' => $createAccountErrors));
            }
            else
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_errorCreatingAccountUnknown'] . ' <br/>' . $strings['str_toResetYourPassword'] . ' ' . link_to('resetpassword',$strings['str_clickHere']) . '.');
                return Redirect::to('/login')->withErrors($messages);
            }
        }

        $customerId = $response; //If creation succeeded, record the customerId

        //Set the user as logged in
        Session::put('authenticated',$customerId);
        Session::put('authenticatedEmail',$customerData['email']);

        $quantity = isset($input['numberOfParticipants']) ? $input['numberOfParticipants'] : 1;
        if ($source == "step2") //Direct them to the URL that will add that item to their cart
        {
            return Redirect::to("/cart?action=add&heatId=$heatId&quantity=$quantity");
        }
        else if ($source == "login")
        {
            if ($heatId != null)
            {
                return Redirect::to("/cart?action=add&heatId=$heatId&quantity=$quantity"); //TODO: Will this redirect back to a bad place?
            }
            else if ($productId != null)
            {
                return Redirect::to("/cart?action=add&productId=$productId&quantity=$quantity"); //TODO: Will this redirect back to a bad place?
            }
            else
            {
                return Redirect::to('/step1');
            }
        }

    }

    /**
     * This function converts the customer creation form input to the format that the Club Speed API expects.
     * It fills in any missing fields as empty strings and/or default values.
     * @param $originalCustomerData
     * @return array
     */
    private function convertCustomerDataToClubSpeedFormat($originalCustomerData)
    {
        $clubSpeedCustomerData = array(
            "email" => isset($originalCustomerData["EmailAddress"]) ? $originalCustomerData["EmailAddress"] : "",
            "password" => isset($originalCustomerData["Password"]) ? $originalCustomerData["Password"] : "",
            "donotemail" => isset($originalCustomerData["ConsentToMail"]) ? 0 : 1, //Reverse logic!
            "Company" => isset($settings["Company"]) ? $settings["Company"] : "",
            "firstname" => isset($originalCustomerData["FName"]) ? $originalCustomerData["FName"] : "",
            "lastname" => isset($originalCustomerData["LName"]) ? $originalCustomerData["LName"] : "",
            "racername" => isset($originalCustomerData["RacerName"]) ? $originalCustomerData["RacerName"] : "",
            "birthdate" => isset($originalCustomerData["BirthDate"]) ? $originalCustomerData["BirthDate"] : "",
            "gender" => isset($originalCustomerData["Gender"]) ? $originalCustomerData["Gender"] : 0,
            "howdidyouhearaboutus" => isset($originalCustomerData["SourceID"]) ? $originalCustomerData["SourceID"] : "",
            "Address" => isset($originalCustomerData["Address"]) ? $originalCustomerData["Address"] : "",
            "Address2" => isset($originalCustomerData["Address2"]) ? $originalCustomerData["Address2"] : "",
            "City" => isset($originalCustomerData["City"]) ? $originalCustomerData["City"] : "",
            "State" => isset($originalCustomerData["State"]) ? $originalCustomerData["State"] : "",
            "Zip" => isset($originalCustomerData["Zip"]) ? $originalCustomerData["Zip"] : "",
            "Country" => isset($originalCustomerData["Country"]) ? $originalCustomerData["Country"] : "",
            "mobilephone" => isset($originalCustomerData["Cell"]) ? $originalCustomerData["Cell"] : "",
            "LicenseNumber" => isset($originalCustomerData["LicenseNumber"]) ? $originalCustomerData["LicenseNumber"] : "",
            "Custom1" => isset($originalCustomerData["Custom1"]) ? $originalCustomerData["Custom1"] : "",
            "Custom2" => isset($originalCustomerData["Custom2"]) ? $originalCustomerData["Custom2"] : "",
            "Custom3" => isset($originalCustomerData["Custom3"]) ? $originalCustomerData["Custom3"] : "",
            "Custom4" => isset($originalCustomerData["Custom4"]) ? $originalCustomerData["Custom4"] : ""
        );

        return $clubSpeedCustomerData;
    }
}