<?php
$debug = true; // must be set to false in production!
if ($debug === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
define("CODE_SECRET", "thisIsACustomKey458SecretA");