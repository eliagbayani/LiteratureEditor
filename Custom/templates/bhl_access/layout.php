<div id="tabs_main">
    <ul>
        <li><a href="#tabs_main-4">Page Search</a></li>
        <li><a href="#tabs_main-2">BHL ID Search</a></li>
        <!--- working
        <li><a href="#tabs_main-1">Book Search</a></li>
        <li><a href="#tabs_main-3">Pick A Title</a></li>
        --->
        <li><a onClick="other_clicked()" href="#tabs_main-6">Other Searches >></a></li>
        <li><a onClick="tab4_clicked()" href="#tabs_main-5">Back to Wiki</a></li>
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
    <div id="tabs_main-6">Loading other searches...</div>
    <div id="tabs_main-5">
    Loading wiki...
    </div>
</div>
<?php 
    $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/";
    $other = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/Custom/bhl_access/index.php?search2=";
?>
<script>
function other_clicked() { location.href = '<?php echo $other ?>'; }
function tab4_clicked() { location.href = '<?php echo $back ?>'; }
</script>

