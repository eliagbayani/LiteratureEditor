<?php
// namespace php_active_record;

// require_once(dirname(__FILE__) ."/../../config/environment.php");
// $mysqli = $GLOBALS['db_connection'];
// $GLOBALS['ENV_DEBUG'] = false;

// if(@$_FILES['dwca_upload']) $_POST['dwca_upload'] = $_FILES['dwca_upload'];
// $parameters =& $_GET;
// if(!$parameters) $parameters =& $_POST;


// $GLOBALS['ENV_DEBUG'] = true;

$params = & $_GET;

// print_r($params); 
// exit;

require_once("../config/settings.php");
require_once("../lib/Functions.php");
require_once("../controllers/api_reader.php");

$articles = array(
  array('title' => 'Article #1', 'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut malesuada ut massa nec iaculis.'),
  array('title' => 'Article #2', 'text' => 'Mauris pharetra aliquam mauris eu condimentum. Duis egestas nunc elit, bibendum imperdiet libero gravida non.'),
);

// $ctrler = new api_reader_controller($articles);

$ctrler = new api_reader_controller('usercontrib', $params);

$body = $ctrler->body;

print $ctrler->render_layout('List of Articles', $body);

?>