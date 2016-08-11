<div id="tabs_main">
    <ul>
        <li><a href="#tabs_main-4">Page Search</a></li>
        <li><a href="#tabs_main-2">BHL ID Search</a></li>
        <li><a onClick="tab7_clicked()" href="#tabs_main-7">Articles ››</a></li>
        <!--- <li><a onClick="tab8_clicked()" href="#tabs_main-8">Projects ››</a></li> --->
        <li><a onClick="other_clicked()" href="#tabs_main-6">Other Searches ››</a></li>
        <li><a onClick="tab4_clicked()" href="#tabs_main-5">Back to Wiki ››</a></li>
        <li style="text-align: right;"><?php require_once("html_user.php"); ?></li>
    </ul>
    <div id="tabs_main-1">
        <?php //print $ctrler->render_layout(@$params, 'booksearch-form') 
        ?>
    </div>
    <div id="tabs_main-2">
        <?php 
        print $ctrler->render_layout(@$params, 'titlesearch-form');
        print $ctrler->render_layout(@$params, 'itemsearch-form');
        print $ctrler->render_layout(@$params, 'pagesearch-form');
        print $ctrler->render_layout(@$params, 'pagetaxasearch-form');
        ?>
    </div>
    <div id="tabs_main-3">
        <?php //print $ctrler->render_layout(@$params, 'titlelist-form') 
        ?>
    </div>
    <div id="tabs_main-4">
        <?php 
        print $ctrler->render_layout(@$params, 'pagesearch-form');
        ?>
    </div>
    <div id="tabs_main-5">Loading...</div>
    <div id="tabs_main-6">Loading...</div>
    <div id="tabs_main-7">Loading...</div>
    <!--- <div id="tabs_main-8">Loading...</div> --->
</div>
<?php 
    $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/";
    $other = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/Custom/bhl_access/index.php?search2=";
    $article_list = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/Custom/bhl_access/index.php?article_list=";
    $projects = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/Custom/bhl_access/index.php?projects_menu=";
?>
<script>
function other_clicked() { location.href = '<?php echo $other ?>'; }
function tab7_clicked() { location.href = '<?php echo $article_list ?>'; }
function tab8_clicked() { location.href = '<?php echo $projects ?>'; }

function tab4_clicked() 
{ 
    //alert("session reset");
    <?php
    /* doesn't seem to work as expected... */
    // session_unset(); // remove all session variables
    // session_destroy(); // destroy the session
    ?>
    location.href = '<?php echo $back ?>'; 
}


</script>

