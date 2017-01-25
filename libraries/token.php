e<?php
class Token {
    static function generate() {
        return hash('sha512', uniqid(rand(), true));
    }

    static function insert($id, $token, $ip, $userAgent) {
        global $pdo;
        
        $QueryById = $pdo->prepare(
            "UPDATE tokens SET `closed`='1' WHERE `user_id`= :id"
        );
        $QueryById->execute([
            ':id' => $id
        ]);
         
        $insertToken = $pdo->prepare(
            "INSERT INTO `tokens` (`user_id`,`token`,`user_ip`, `user_agent`) VALUES (:id, :token, :ip, :user_agent)"
        );
        $insertToken->execute([
            ':id' => $id,
            ':token' => $token,
            ':ip' => $ip,
            ':user_agent' => $userAgent
        ]);
    }

    static function checkToken($token, $userAgent) {
        global $pdo;

        $QueryByToken = $pdo->prepare(
            "SELECT `user_agent` FROM `tokens` WHERE `token` = :token AND `closed` = '0'"
        );
        $QueryByToken->execute([
            ':token' => $token
        ]);

        if ($QueryByToken) {
            if ($QueryByToken->rowCount()) {
                if ($QueryByToken->fetch()->user_agent == $userAgent) {
                    return TRUE;
                } else {
                    Token::close($token);
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            // TODO Обработка ошибок базы данных
            print_r($QueryByToken->errorInfo());
            return FALSE;
        }
    }

    static function close($token) {
        global $pdo;

        $QueryById = $pdo->prepare(
            "UPDATE tokens SET `closed`='1' WHERE `token`= :token"
        );
        $QueryById->execute([
            ':token' => $token
        ]);
    }
    
    static function getId ($token){
        global $mysqli;
        $QueryByToken = $mysqli->query("SELECT * FROM `tokens` WHERE token='".htmlspecialchars($token, ENT_QUOTES)."' AND closed='0'");
        if(!$QueryByToken -> num_rows){
            Response::error('-1');
            exit();
        }
        $byTokenObj = $QueryByToken->fetch_object();
        return $byTokenObj->user_id;
    }
    
    /*
    static function isClosed($token) {
        global $mysqli;
        $QueryByToken = $mysqli->query("SELECT * FROM `tokens` WHERE token='".htmlspecialchars($token, ENT_QUOTES)."'");
        if(!$QueryByToken -> num_rows){
            Response::error('-1');
            exit();
        }
        $byTokenObj = $QueryByToken->fetch_object();
        return $byTokenObj->closed;
    }*/
}