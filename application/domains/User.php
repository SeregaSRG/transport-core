<?php 
class Domain_User {

    //http://transport-core.microfox.ru/api/user.login?phone=89094294989&password=123
    //http://transport-core.microfox.ru/api/user.register?phone=89094294989&password=123
    public $user;

    public $isRegistered = FALSE;
    public $registeredCode;


    public $isLogged = FALSE;
    public $loggedCode;

    public $isChecked = FALSE;
    public $checkedCode;


    function register() {
        global $pdo;
        $result = [];

        $name			= Parameters::Get('name');
        $surname		= Parameters::Get('surname');
        $password		= Parameters::Get('password');
        $email			= Parameters::Get('email');
        $phone			= Parameters::Get('phone');
        $password_hash	= crypt($password, SALT);

        $isUserQuery = $pdo->prepare(
            "SELECT id FROM `clients` WHERE phone = :phone"
        );
        $isUserQuery->execute([
            ':phone' => $phone
        ]);

        if (!$isUserQuery->rowCount()){
            $addUserQuery = $pdo->prepare(
                "INSERT INTO `clients` (`name`, `surname`, `email`, `phone`, `password`) VALUES (:name, :surname, :email, :phone, :password_hash)"
            );
            $addUserQuery->execute([
                ':name'          => $name,
                ':surname'       => $surname,
                ':email'         => $email,
                ':phone'         => $phone,
                ':password_hash' => $password_hash
            ]);
            if ($addUserQuery){
                $result['isRegistered'] = TRUE;
                $result['registeredCode'] = 100;
            } else {
                $result['isRegistered'] = TRUE;
                $result['registeredCode'] = -2;
            }
        } else {
            $result['isRegistered'] = FALSE;
            $result['registeredCode'] = 101;
        }

        return $result;
    }

    function login() {
        $result = [];
        $password   = Parameters::Get('password');
        $phone      = Parameters::Get('phone');

        $this->user = $this->getUserInfo($phone);

        if ($this->user) {
            if ( $this->checkPassword($password) ) {
                $_SESSION['now_user'] = [
                    'id'         => $this->user->id,
                    'token'      => Token::generate(),
                    'ip'         => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT']
                ];

                Token::insert($_SESSION['now_user'][id], $_SESSION['now_user'][token], $_SESSION['now_user'][ip], $_SESSION['now_user'][user_agent]);

                $result['isLogged'] = true;
                $result['loginCode'] = 200;
            } else {
                $result['isLogged'] = false;
                $result['loginCode'] = 202;
            }
        } else {
            $result['isLogged'] = false;
            $result['loginCode'] = 201;
        }
        return $result;
    }

    function checkLogin() {
        $result = [];
        $token = Parameters::Get('token');
        
        $this->isChecked = Token::checkToken($token, $_SERVER['HTTP_USER_AGENT']);

        if ($this->isChecked) {
            $result['checkedCode'] = '300';
        } else {
            $result['checkedCode'] = '301';
        }

        return $result;
    }

    function checkPassword($password) {
        if ( hash_equals( $this->user->password, crypt($password, SALT)) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getUserInfo($phone) {
        global $pdo;

        $userInfoQuery = $pdo->prepare(
            "SELECT * FROM `clients` WHERE phone = :phone"
        );

        $userInfoQuery->execute([
            ':phone' => $phone
        ]);

        // Возвращает объект или false
        if ($userInfoQuery->rowCount()){
            return $userInfoQuery->fetch();
        } else {
            return FALSE;
        }
    }

    function getUserInfoByToken($token) {
        global $pdo;

        $userInfoQuery = $pdo->prepare(
            "SELECT * FROM `clients` WHERE phone = :phone"
        );

        $userInfoQuery->execute([
            ':phone' => $phone
        ]);

        // Возвращает объект или false
        if ($userInfoQuery->rowCount()){
            return $userInfoQuery->fetch();
        } else {
            return FALSE;
        }
    }
}