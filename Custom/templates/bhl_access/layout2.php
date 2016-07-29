<div id="tabs_main">
    <ul>
        <li><a href="#tabs_main-1">Book Search</a></li>
        <li><a href="#tabs_main-3">Pick A Title</a></li>
        <li><a onClick="other_clicked()" href="#tabs_main-6">Page Search ››</a></li>
        <li><a onClick="tab4_clicked()" href="#tabs_main-5">Back to Wiki ››</a></li>
    </ul>
    <div id="tabs_main-1">
        <?php print $ctrler->render_layout(@$params, 'booksearch-form') ?>
    </div>
    <div id="tabs_main-3">
        <?php print $ctrler->render_layout(@$params, 'titlelist-form') ?>
    </div>
    <div id="tabs_main-5">Loading...</div>
    <div id="tabs_main-6">Loading...</div>
</div>
<?php $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/";
      $other = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/Custom/bhl_access/index.php";
?>
<script>
function tab4_clicked() { location.href = '<?php echo $back ?>'; }
function other_clicked() { location.href = '<?php echo $other ?>'; }
</script>

