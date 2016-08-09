<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$params =& $_GET;
if(!$params) $params =& $_POST;

require_once("../config/settings.php");
require_once("../lib/Functions.php");
require_once("../controllers/bhl_access.php");

$ctrler = new bhl_access_controller($params);

//==================cmd line

// $options = getopt("p:q:");
// var_dump($options);
// print_r($options);

// $ctrler->cron_task();

// $cmdline = "php -q " . "//MacMini_HD/Library/WebServer/Documents/LiteratureEditor/Custom/bhl_access/index.php?search_type=articlelist&radio=approved";
$cmdline = "php -q " . "index.php?search_type=articlelist&radio=approved";

$status = shell_exec($cmdline . " 2>&1");
echo $status;


//==================

?>
