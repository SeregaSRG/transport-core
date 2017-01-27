<?php 
class Domain_User
{
    public $user;

    public function register() 
    {
        global $pdo;
        $result = [];

        $name			= Parameters::Get('name');
        $surname		= Parameters::Get('surname');
        $password		= Parameters::Get('password');
        $email			= Parameters::Get('email');
        $phone			= Parameters::Get('phone');
        $password_hash	= crypt($password, SALT);

        $isUserQuery = $pdo->prepare("
          SELECT 
          id 
          FROM 
          `clients` 
          WHERE 
          phone = :phone
        ");
        
        $isUserQuery->execute([
            ':phone' => $phone
        ]);

        if (!$isUserQuery->rowCount()){
            $addUserQuery = $pdo->prepare("
              INSERT INTO 
                `clients` 
                (`name`, `surname`, `email`, `phone`, `password`)
              VALUES 
                (:name, :surname, :email, :phone, :password_hash)
            ");
            
            $addUserQuery->execute([
                ':name'          => $name,
                ':surname'       => $surname,
                ':email'         => $email,
                ':phone'         => $phone,
                ':password_hash' => $password_hash
            ]);
            
            if ($addUserQuery){
                $result['isRegistered'] = true;
                $result['registeredCode'] = 100;
            } else {
                $result['isRegistered'] = false;
                $result['registeredCode'] = -2;
            }
        } else {
            $result['isRegistered'] = false;
            $result['registeredCode'] = 101;
        }

        return $result;
    }

    public function login() 
    {
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

                Token::insert(
                    $_SESSION['now_user']['id'], 
                    $_SESSION['now_user']['token'], 
                    $_SESSION['now_user']['ip'], 
                    $_SESSION['now_user']['user_agent']
                );

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

    public function checkLogin() {
        $result = [];
        $token = Parameters::Get('token');
        
        if ( Token::checkToken($token, $_SERVER['HTTP_USER_AGENT']) ) {
            $result['checkedCode'] = '300';
        } else {
            $result['checkedCode'] = '301';
        }

        return $result;
    }

    public function checkPassword($password) {
        if ( hash_equals( $this->user->password, crypt($password, SALT)) ) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserInfo($phone) {
        global $pdo;

        $userInfoQuery = $pdo->prepare("
          SELECT 
          * 
          FROM 
          `clients` 
          WHERE 
          phone = :phone
        ");
        
        $userInfoQuery->execute([
            ':phone' => $phone
        ]);
        
        if ($userInfoQuery->rowCount()){
            return $userInfoQuery->fetch();
        } else {
            return false;
        }
    }
}