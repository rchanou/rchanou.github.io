<?php
require_once(app_path().'/includes/includes.php');

class LoginController extends BaseController
{
    public function loginStart()
    {
        if(Session::get('authenticated'))
        {
            return Redirect::to('/dashboard');
        }
        return View::make('/login');
    }

    public function loginSubmit()
    {
        $input = Input::all();
        $this->validateLoginInput($input); //TODO: Live validation

        $username = $input['username'];
        $password = $input['password'];

        $loginResult = CS_API::login($username, $password);

				if($loginResult['result'] === true) {
						Session::put('authenticated', true);
						Session::put('user', $loginResult['user']->username);
						
						$userPermissions = CS_API::getUserTasks(array(
								'where' => array(
										'userId' => array('$eq' => $loginResult['user']->userId))
										)
								);

						$permissions = array('allowedTasks' => array(), 'allowedRoles' => array());
						foreach($userPermissions as $task) {
								if(!empty($task->taskDescription)) $permissions['allowedTasks'][str_replace(':', '|', $task->taskDescription)] = true;  // Laravel uses a colon (:) internally to specify filter params
								if(!empty($task->roleId)) $permissions['allowedRoles'][$task->roleId] = true;
						}
						Session::put('permissions', $permissions);

						// Redirect back to intended location (if set)
						$session = Session::all();
						if (isset($session["redirectTo"]))
						{
								Session::forget("redirectTo");
								return Redirect::to($session["redirectTo"]);
						}
						return Redirect::to('/dashboard');
				} else {
						$messages = new Illuminate\Support\MessageBag;
						$messages->add('errors', $loginResult['message']);

						//Redirect to the previous page with an appropriate error message
						return Redirect::to('/login')->withErrors($messages)->withInput();
				}
        return View::make('/login');
    }

    public function logout()
    {
        $username = Session::get('user');
				Session::flush();
        Session::regenerate();
        CS_API::log("INFO :: Successful logout for \"{$username}\"", 'Club Speed Admin Panel');
				return Redirect::to('/login');
    }

    //TODO: I don't think this pattern works in Laravel. The redirect may never happen as it needs to chain up.
    private function validateLoginInput($input)
    {
        $rules = array(
            'username' => 'required',
            'password' => 'required',
        );
        $messages = array(
            'username.required' => 'Please enter your username.',
            'password.required' => 'Please enter your password.',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('/login')->withErrors($validator)->withInput();
        }
    }
} 