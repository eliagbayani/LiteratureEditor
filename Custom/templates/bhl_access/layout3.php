<div id="tabs_main">
    <ul>
        <li><a href="#tabs_main-1">Article List</a></li>
        <li><a onClick="tab2_clicked()" href="#tabs_main-2">BHL ID Search ››</a></li>
        <li><a onClick="tab3_clicked()" href="#tabs_main-3">Back to Wiki ››</a></li>
    </ul>
    <div id="tabs_main-1">
        <?php 
          print $ctrler->render_layout(@$params, 'articlelist-form')
        ?>
    </div>
    <div id="tabs_main-2">Loading...</div>
    <div id="tabs_main-3">Loading...</div>
</div>
<?php $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/";
      $other = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/Custom/bhl_access/index.php";
?>
<script>
function tab2_clicked() { location.href = '<?php echo $other ?>'; }
function tab3_clicked() { location.href = '<?php echo $back ?>'; }
</script>
