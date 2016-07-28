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

if(isset($params['page_id']))     { if(substr($params['page_id'],0,4) == "http")     $params['page_id'] = pathinfo($params['page_id'], PATHINFO_FILENAME); }
if(isset($params['ref_page_id'])) { if(substr($params['ref_page_id'],0,4) == "http") $params['ref_page_id'] = pathinfo($params['ref_page_id'], PATHINFO_FILENAME); }
if(isset($params['item_id']))     { if(substr($params['item_id'],0,4) == "http")     $params['item_id'] = pathinfo($params['item_id'], PATHINFO_FILENAME); }
if(isset($params['title_id']))    { if(substr($params['title_id'],0,4) == "http")    $params['title_id'] = pathinfo($params['title_id'], PATHINFO_FILENAME); }

// print_r($params);// exit;

require_once("../config/settings.php");
require_once("../lib/Functions.php");
require_once("../controllers/bhl_access.php");

$ctrler = new bhl_access_controller($params);
if(!$ctrler->user_is_logged_in_wiki()) return;

if(isset($params['search2']) || @$params['search_type'] == 'booksearch'
                             || @$params['search_type'] == 'titlelist') require_once("../templates/bhl_access/layout2.php");
elseif(isset($params['article_list']) 
                             || @$params['search_type'] == 'articlelist'
                             || @$params['search_type'] == 'gen_archive_all'
                             || @$params['search_type'] == 'movebatch') require_once("../templates/bhl_access/layout3.php");
else require_once("../templates/bhl_access/layout.php");
?>

<!--- for spinner effect: http://spin.js.org/ --->
<div id="el"></div>
<script type="text/javascript">
var spinner = new Spinner().spin();
target.appendChild(spinner.el);
$('#el').spin('large'); //start spinning
</script>

<?php
print $ctrler->render_layout(@$params, 'result');

if(isset($params['page_more_info'])) print $ctrler->render_template('page-more-info', array('arr' => @$params['page_more_info']));
if(isset($params['part_more_info'])) print $ctrler->render_template('part-more-info', array('arr' => @$params['part_more_info']));

if(isset($params['search_type']))
{
    if    ($params['search_type'] == "titlelist")     print $ctrler->render_template('titlelist-result', array('letter' => @$params['radio']));
    elseif($params['search_type'] == "articlelist")   print $ctrler->render_template('articlelist-result', array('params' => @$params));
    elseif($params['search_type'] == "move2wiki")     print $ctrler->render_template('move2wiki-result', array('params' => @$params));
    elseif($params['search_type'] == "reviewexcerpt") print $ctrler->render_template('reviewexcerpt-result', array('params' => @$params));
    elseif($params['search_type'] == "wiki2php")      print $ctrler->render_template('wiki2php-result', array('params' => @$params));
    elseif($params['search_type'] == "gen_archive")   print $ctrler->render_template('gen_archive-result', array('params' => @$params));
    elseif($params['search_type'] == "gen_archive_all") print $ctrler->render_template('gen_archive_all-result', array('params' => @$params));
    elseif($params['search_type'] == "move24harvest") print $ctrler->render_template('move24harvest-result', array('params' => @$params));
    elseif($params['search_type'] == "movebatch")     print $ctrler->render_template('movebatch-result', array('params' => @$params));
}

require_once("../config/script-below-entry.html");

//for layout
if    (@$params['search_type'] == 'itemsearch')      print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'titlesearch')     print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'pagetaxasearch')  print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'pagesearch')      print '<script>$( "#tabs_main" ).tabs( "option", "active", 0 );</script>';
elseif(@$params['search_type'] == 'move2wiki')       print '<script>$( "#tabs_main" ).tabs( "option", "active", 0 );</script>';

if(isset($params['part_more_info'])) print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';

//for layout2
if    (@$params['search_type'] == 'booksearch')      print '<script>$( "#tabs_main" ).tabs( "option", "active", 0 );</script>';
elseif(@$params['search_type'] == 'titlelist')       print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';

//for layout3
if    (@$params['search_type'] == 'gen_archive_all') print '<script>$( "#tabs_main" ).tabs( "option", "active", 1 );</script>';
elseif(@$params['search_type'] == 'movebatch')       print '<script>$( "#tabs_main" ).tabs( "option", "active", 0 );</script>';

?>
</body>
</html>
