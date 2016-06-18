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
        <td><button id="button_search_taxa" onClick="spinner_on()">Show list of taxa</button>
        </td>
        <!--- not needed anymore
        <td>
            <?php $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/" ?>
            <?php self::image_with_text(array("text" => "Back to Wiki", "src" => "../images/Back_icon.png", "alt_text" => "Back to Wiki", "href" => $back));?>
        </td>
        --->
    </tr>
</table>
</form>
