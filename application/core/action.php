<?php
class Action {

    public $domain;
    public $responder;

    function __construct() {
    }
    
    static function loadDomain($name) {
        if( file_exists(HOME_DIR.'/application/domains/'.$name.'.php') ) {
            require_once HOME_DIR.'/application/domains/'.$name.'.php';
        } else {
            //TODO исключение для подключения домена
        }
    }
    
}