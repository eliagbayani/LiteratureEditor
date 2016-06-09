<!doctype html>
<html lang="us">
<head>
    <title>BHL API Search Interface</title>
    <?php require_once("../config/head-entry.html") ?>
</head>
<body>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$params =& $_GET;
if(!$params) $params =& $_POST;

// print_r($params);// exit;

require_once("../config/settings.php");
require_once("../lib/Functions.php");
require_once("../controllers/bhl_access.php");

$ctrler = new bhl_access_controller($params);
if(!$ctrler->user_is_logged_in_wiki()) return;

require_once("../templates/bhl_access/layout.php");

print $ctrler->render_layout(@$params, 'result');

if(isset($params['page_more_info'])) print $ctrler->render_template('page-more-info', array('arr' => @$params['page_more_info']));
if(isset($params['part_more_info'])) print $ctrler->render_template('part-more-info', array('arr' => @$params['part_more_info']));

if(isset($params['search_type']))
{
    if($params['search_type'] == "titlelist") print $ctrler->render_template('titlelist-result', array('letter' => @$params['radio']));
    if($params['search_type'] == "move2wiki") print $ctrler->render_template('move2wiki-result', array('params' => @$params));
}

require_once("../config/script-below-entry.html");

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
