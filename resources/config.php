<?php
/*

 */
$config = array(
    "dbconnection" => "host=host port=port dbname=dbname user=username password=password",
);

defined("TEMPLATES_PATH")
    or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/templates'));

ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);


?>