<?php
// namespace php_active_record;
    /* 
        Expects:
            page_id
    */
?>

<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="pagetaxasearch">
<table>
    <tr>
        <td>Page ID:</td>
        <td><input type="text" size="20" name="page_id"<?php if($page_id) echo " value=\"$page_id\""; ?>/></td>
        <td><button id="button_search_taxa">Show list of taxa</button>
        
        
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/" ?>
        <a href="<?php echo $back?>">Back to Wiki</a>
        </td>
    </tr>
</table>
</form>
