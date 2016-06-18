<?php
// namespace php_active_record;
    /* 
        Expects:
            item_id
    */
?>

<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="itemsearch">
<table>
    <tr>
        <td>Item ID:</td>
        <td><input type="text" size="20" name="item_id"<?php if($item_id) echo " value=\"$item_id\""; ?>/></td>
        <td><button id="button_search_item" onClick="spinner_on()">Search this item</button></td>
    </tr>
</table>
</form>
