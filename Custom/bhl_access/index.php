<!doctype html>
<html lang="us">
<head>
    <title>BHL API Search Interface</title>
    <?php require_once("../config/head-entry.html") ?>
</head>
<body>

<?php
// namespace php_active_record;

// require_once(dirname(__FILE__) ."/../../config/environment.php");
// $mysqli = $GLOBALS['db_connection'];
// $GLOBALS['ENV_DEBUG'] = false;

// if(@$_FILES['dwca_upload']) $_POST['dwca_upload'] = $_FILES['dwca_upload'];
// $parameters =& $_GET;
// if(!$parameters) $parameters =& $_POST;

// http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=BookSearch&title=Selborne&lname=White&volume=2&edition=new&year=1825&subject=&collectionid=4&language=eng&apikey=8e525086-c464-4298-9431-b815de6c2901

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$params =& $_GET;
if(!$params) $params =& $_POST;

// print_r($params);// exit;

require_once("../config/settings.php");
require_once("../lib/Functions.php");
require_once("../controllers/bhl_access.php");

// $articles = array(
//   array('title' => 'Article #1', 'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut malesuada ut massa nec iaculis.'),
//   array('title' => 'Article #2', 'text' => 'Mauris pharetra aliquam mauris eu condimentum. Duis egestas nunc elit, bibendum imperdiet libero gravida non.'),
// );


$ctrler = new bhl_access_controller($params);
// $body = $ctrler->body;


require_once("../templates/bhl_access/layout.php");

// print $ctrler->render_layout(@$params, 'booksearch-form');
// print $ctrler->render_layout(@$params, 'itemsearch-form');
// print $ctrler->render_layout(@$params, 'titlesearch-form');
// print $ctrler->render_layout(@$params, 'pagetaxasearch-form');
// print $ctrler->render_layout(@$params, 'pagesearch-form');
// print $ctrler->render_layout(@$params, 'titlelist-form');


print $ctrler->render_layout(@$params, 'result');

if(isset($params['page_more_info'])) print $ctrler->render_template('page-more-info', array('arr' => @$params['page_more_info']));
if(isset($params['part_more_info'])) print $ctrler->render_template('part-more-info', array('arr' => @$params['part_more_info']));

if(isset($params['search_type']))
{
    if($params['search_type'] == "titlelist") print $ctrler->render_template('titlelist-result', array('letter' => @$params['radio']));
    if($params['search_type'] == "move2wiki") print $ctrler->render_template('move2wiki-result', array('params' => @$params));
}

?>

<?php require_once("../config/script-below-entry.html") ?>

<?php
if    (@$params['search_type'] == 'booksearch')      print '<script>$( "#tabs_main" ).tabs( "option", "active", 0 );</script>';
elseif(@$params['search_type'] == 'itemsearch')      print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'titlesearch')     print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'pagetaxasearch')  print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'pagesearch')      print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'titlelist')       print '<script>$( "#tabs_main" ).tabs( "option", "active", 2 );</script>';
elseif(@$params['search_type'] == 'move2wiki')       print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
?>


</body>
</html>
