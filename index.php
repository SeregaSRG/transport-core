<?php
header("Access-Control-Allow-Origin:*");
header('Content-type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
session_start();

define('HOME_DIR',dirname(__FILE__));
require_once HOME_DIR.'/route.php';
require_once HOME_DIR.'/config.php';
require_once HOME_DIR.'/libraries/token.php';
require_once HOME_DIR.'/libraries/parameters.php';
require_once HOME_DIR.'/libraries/database.php';
require_once HOME_DIR.'/application/core/action.php';
require_once HOME_DIR.'/application/core/domain.php';
require_once HOME_DIR.'/application/core/responder.php';

$pdo = Database::connect();

Route::start();
