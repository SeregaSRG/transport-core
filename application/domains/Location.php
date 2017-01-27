<?php
class Domain_Location extends Domain
{

    public function add()
    {
        global $pdo;

        $token              = Parameters::Get('token');
        $transport_number   = Parameters::Get('transport_number');
        $transport_type     = Parameters::Get('transport_type');
        $lat                = Parameters::Get('lat');
        $lon                = Parameters::Get('lon');
        $speed              = Parameters::Get('speed');
        $time               = Parameters::Get('time');
        $locationId         = Parameters::Get('locationId');
        $accuracy           = Parameters::Get('accuracy');

        $userId             = $_SESSION['now_user']['id'];
        $result             = [];

        if ( Token::checkToken($token, $_SERVER['HTTP_USER_AGENT']) ) {

            $addQuery = $pdo->prepare("
              INSERT INTO `now_map`
                (`client_id`, `transport_number`, `transport_type`, `lat`, `lon`, `speed`, `time_client`, `locationId`, `accuracy`)
              VALUES
                (:client_id, :transport_number, :transport_type, :lat, :lon, :speed, :time_client, :locationId, :accuracy)
              ON DUPLICATE KEY UPDATE
                `lat`= :lat,  `lon`= :lon,   `speed`= :speed,  `closed` = '0'
            ");

            $addQuery->execute([
                ':client_id'        => $userId,
                ':transport_number' => $transport_number,
                ':transport_type'   => $transport_type,
                ':lat'              => $lat,
                ':lon'              => $lon,
                ':speed'            => $speed,
                ':time_client'      => $time,
                ':locationId'       => $locationId,
                ':accuracy'         => $accuracy
            ]);

            if ($addQuery) {
                $result['isAdded']  = true;
                $result['code']     = 400;
            } else {
                $result['isAdded']  = false;
                $result['code']     = -2;
            }
        } else {
            $result['isAdded']  = false;
            $result['code']     = 301;
        }
        
        return $result;
    }

    public function get()
    {
        global $pdo;

        $token  = Parameters::Get('token');
        $result = [];

        if ( Token::checkToken($token, $_SERVER['HTTP_USER_AGENT']) ) {

            $getQuery = $pdo->prepare("
              SELECT 
                * 
              FROM 
                `now_map` 
              WHERE 
                `closed` = '0'
            ");
            $getQuery->execute();

            if ($getQuery) {
                $result['isGot'] = true;
                $result['array'] = $getQuery->fetchAll();
            } else {
                $result['isGot'] = false;
                $result['code'] = -2;
            }
        } else {
            $result['isGot'] = false;
            $result['code'] = 301;
        }

        return $result;
    }
}