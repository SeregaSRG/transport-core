<?php
class Parameters {
    static function Post($name) {
        return htmlspecialchars($_POST[$name],ENT_QUOTES);
    }

    static function Get($name) {
        return htmlspecialchars($_GET[$name],ENT_QUOTES);
    }
}