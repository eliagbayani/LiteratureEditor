<?php require_once("../config/settings.php"); ?>
<?php exit("<hr>Needs system maintenance.<hr>Contact Eli Agbayani eagbayani@eol.org.<hr>"); ?>
<!doctype html>
<html lang="us">
<head>
    <div id="loadOverlay" style="background-color:#333; position:absolute; top:0px; left:0px; width:100%; height:100%; z-index:2000; color:white; font-size:120%;">Loading, please wait ...</div>
    <title>BHL API Search Interface</title>
    <?php require_once("../config/head-entry.html") ?>
</head>
<body>

<?php
$params =& $_GET;
if(!$params) $params =& $_POST;

if(isset($params['page_id']))     { if(substr($params['page_id'],0,4) == "http")     $params['page_id'] = pathinfo($params['page_id'], PATHINFO_FILENAME); }
if(isset($params['ref_page_id'])) { if(substr($params['ref_page_id'],0,4) == "http") $params['ref_page_id'] = pathinfo($params['ref_page_id'], PATHINFO_FILENAME); }
if(isset($params['item_id']))     { if(substr($params['item_id'],0,4) == "http")     $params['item_id'] = pathinfo($params['item_id'], PATHINFO_FILENAME); }
if(isset($params['title_id']))    { if(substr($params['title_id'],0,4) == "http")    $params['title_id'] = pathinfo($params['title_id'], PATHINFO_FILENAME); }

// print_r($params);// exit;

require_once("../lib/Functions.php");
require_once("../controllers/projects.php");
require_once("../controllers/bhl.php");
$ctrler = new bhl_controller($params);
?>
<script type="text/javascript">
$(window).load(function () { $("#loadOverlay").css("display","none"); });
</script>
<?php

if(!$ctrler->user_is_logged_in_wiki()) return;

//start assignment ------------------------------------------
if(isset($params['assign'])) $ctrler->make_working_proj($params['wiki_title']);

// http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?wiki_title=Completed_Projects:Planet_of_the_Apes&search_type=move24harvest&wiki_status={Completed}&articles=
if($val = @$params['search_type'])
{
    if($val == "move24harvest")
    {
        if($params['wiki_title'] == $_SESSION['working_proj']) $_SESSION['working_proj'] = false;
    }
}
//end ------------------------------------------

if(isset($params['search2']) || @$params['search_type'] == 'booksearch'
                             || @$params['search_type'] == 'titlelist') require_once("../templates/bhl_access/layout2.php");
elseif(isset($params['article_list']) 
                             || @$params['search_type'] == 'articlelist'
                             || @$params['search_type'] == 'gen_archive_all'
                             || @$params['search_type'] == 'deletewiki'
                             || @$params['search_type'] == 'movebatch') require_once("../templates/bhl_access/layout3.php");
                             
elseif(isset($params['projects_menu']) || in_array(@$params['search_type'], array("projectsmenu", "wiki2php_project", "reviewproject", "deletewiki_project"))) 
{
    if(@$params['projects_menu'] == "deactivate_proj") $_SESSION['working_proj'] = false;
    require_once("../templates/bhl_access/layout4.php");    
}
else require_once("../templates/bhl_access/layout.php"); //this includes pagesearch, reviewexcerpt, etc.
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
    elseif($params['search_type'] == "projectsmenu")  print $ctrler->render_template('projectsmenu-result', array('params' => @$params));

    elseif($params['search_type'] == "move2wiki")     print $ctrler->render_template('move2wiki-result', array('params' => @$params));
    elseif($params['search_type'] == "reviewexcerpt") print $ctrler->render_template('reviewexcerpt-result', array('params' => @$params));

    elseif($params['search_type'] == "deletewiki")         print $ctrler->render_template('deletewiki-result', array('params' => @$params));
    elseif($params['search_type'] == "deletewiki_project") print $ctrler->render_template('deletewiki-result', array('params' => @$params));

    elseif($params['search_type'] == "move2wiki_project") print $ctrler->render_template('move2wiki_project-result', array('params' => @$params));
    elseif($params['search_type'] == "reviewproject")     print $ctrler->render_template('reviewproject-result', array('params' => @$params));

    elseif($params['search_type'] == "wiki2php")      print $ctrler->render_template('wiki2php-result', array('params' => @$params));
    elseif($params['search_type'] == "wiki2php_project")      print $ctrler->render_template('wiki2php_project-result', array('params' => @$params));
    
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

// for layout4
if    (@$params['search_type'] == 'projectsmenu') print '<script>$( "#tabs_main" ).tabs( "option", "active", 0 );</script>';
?>
</body>
</html>
