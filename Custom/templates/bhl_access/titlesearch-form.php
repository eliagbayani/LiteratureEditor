<?php
// namespace php_active_record;
    /* 
        Expects:
            title_id
    */
?>

<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="titlesearch">
<table>
    <tr>
        <td>Title ID:</td>
        <td><input type="text" size="20" name="title_id"<?php if($title_id) echo " value=\"$title_id\""; ?>/></td>
        
        <!---
        <td><input type="checkbox" name="use_cache"<?php if($use_cache) echo " checked"; ?>/>
        <?php echo "[$use_cache]" ?>
        --->
        
        </td>
        
        <td><button id="button_search_title" onClick="spinner_on()">Search this title</button></td>
    </tr>
</table>
</form>
