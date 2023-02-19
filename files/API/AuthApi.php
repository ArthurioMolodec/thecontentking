<?php

class AuthApi
{
    /** @var DBHelper $dbHelper */
    private $dbHelper;

    function __construct($dbHelper = dbHelper)
    {
        $this->dbHelper = $dbHelper;
    }

    public function login($form)
    {
        $errors = [];

        if (!isset($form['email']) || $form['email'] == '') {
            $errors[] = 'Email is required';
        }

        if (!isset($form['pass']) || $form['pass'] == '') {
            $errors[] = 'Password is required';
        }

        if (count($errors)) {
            return [
                'status' => false,
                'errors' => $errors,
            ];
        }

        $auth = $this->dbHelper->loginWithEmailPass($form['email'], $form['pass']);

        if ($auth !== true) {
            return [
                'status' => false,
                'errors' => $auth,
            ];
        }

        return [
            'status' => true,
            'result' => dbHelper->getAuthData(),
        ];
    }

    public function registration()
    {
        $errors = [];

        if (($resultCaptcha = validateCaptcha()) !== true) {
            $errors = array_merge($errors, $resultCaptcha);
        }

        list($input, $errorsValidation) = validateFields(['email', 'pass', 'pass_confirmation', 'first_name', 'last_name'], []);
        $errors = array_merge($errors, $errorsValidation);

        if (isset($input['pass']) && isset($input['pass_confirmation']) && $input['pass_confirmation'] != $input['pass']) {
            $errors[] = 'Password and Password confirmation must be the same';
        }

        if (count($errors)) {
            return [
                'status' => false,
                'errors' => $errors,
            ];
        }
        $userData = [
            // 'phone' => $input['phone'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'type' => ACCOUNT_TYPE_DEFAULT,
        ];
        $auth = dbHelper->signUpWithEmailPass($input['email'], $input['pass'], $userData);
        if ($auth !== true) {
            return [
                'status' => false,
                'errors' => $auth,
            ];
        }

        return [
            'status' => true,
        ];
    }
}



$authApi = new AuthApi();

switch($_REQUEST['action']) {
    case 'login':
        echo(json_encode($authApi->login($_REQUEST)));
        break;
    case 'registration':
        echo(json_encode($authApi->registration($_REQUEST)));
        break;
    }