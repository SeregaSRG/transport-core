<?php
class Action_User extends Action 
{

    function __construct() 
    {
        $this->domain = new Domain_User();
        $this->responder = new Responder();
    }

    function register() 
    {
        $result = $this->domain->register();

        if ($result['isRegistered']) {
            Responder::send([
                'code' => $result['registeredCode']
            ]);
        } else {
            Responder::error(
                $result['registeredCode']
            );
            session_destroy();
        }

        exit();
    }

    function login() 
    {
        $result = $this->domain->login();

        if ($result['isLogged']) {
            Responder::send([
                'token' => $_SESSION['now_user']['token'],
                'name'  => $this->domain->user->name,
                'surname'  => $this->domain->user->surname,
                'email'  => $this->domain->user->email
            ]);
        } else {
            Responder::error(
                $result['loggedCode']
            );
            session_destroy();
        }

        exit();
    }

    function checkLogin() 
    {
        $result = $this->domain->checkLogin();
            
        if ($this->domain->isChecked) {
            Responder::send([
                'code' => $result['checkedCode']
            ]);
        } else {
            Responder::error([
                'code' => $result['checkedCode']
            ]);
            session_destroy();
        }

        exit();
    }
}
