<?php
class Route {
    static function start() {
        $method = NULL;
        $action = NULL;

        $addressWithoutGet = explode('?',htmlspecialchars($_SERVER['REQUEST_URI'],ENT_QUOTES));
        $routes = explode('/', htmlspecialchars($addressWithoutGet[0]),ENT_QUOTES);


        switch($routes[1]){
            case 'api':
                $actionandmethod = explode('.',htmlspecialchars($routes[2],ENT_QUOTES));
                $action = mb_convert_case($actionandmethod[0], MB_CASE_TITLE, "UTF-8");
                $method = $actionandmethod[1];
                break;

            case 'start':
                require_once HOME_DIR.'/first_start.php';
                exit();
                break;

            default:
                Route::ErrorPage404();
                break;
        }


        // добавляем префиксы
        $action_class_name = 'Action_'.$action;

        // подцепляем файлы
        if( file_exists(HOME_DIR.'/application/actions/'.$action.'.php') ) {
            require_once HOME_DIR.'/application/actions/'.$action.'.php';
        } else {
            Route::ErrorPage404();
        }

        if( file_exists(HOME_DIR.'/application/domains/'.$action.'.php') ) {
            require_once HOME_DIR.'/application/domains/'.$action.'.php';
        } else {
            Route::ErrorPage404();
        }

        if( file_exists(HOME_DIR.'application/responder/'.$action.'.php') ) {
            require_once HOME_DIR.'application/responder/'.$action.'.php';
        }
        
        // Вызываем action и method, переданные в запросе.
        $action = new $action_class_name;
        if ( method_exists($action,$method) ) {
            $action = new $action_class_name;
            $action->$method();
        } else {
            Route::ErrorPage404();
        }
    }

    static function ErrorPage404() {
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:'.$host.'404');
        exit();
    }
}