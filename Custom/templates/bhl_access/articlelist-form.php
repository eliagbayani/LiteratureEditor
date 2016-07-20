<?php
// namespace php_active_record;
    /* Expects: radio */
?>

<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="articlelist">
    <table border="0">
    <tr valign="top">
        <td>
        <div id="radioset">
        <input onClick="spinner_on();submit()" type="radio" id="radio1" name="radio" value="approved" <?php if($radio == 'approved') echo " checked=\"checked\""; ?>> <label for="radio1">For EOL Harvesting</label>
        <input onClick="spinner_on();submit()" type="radio" id="radio2" name="radio" value="draft"    <?php if($radio == 'draft') echo " checked=\"checked\""; ?>>    <label for="radio2">For Review (drafts)</label>
        <!---
        <input onClick="spinner_on();submit()" type="radio" id="radio3" name="radio" value="all"      <?php if($radio == 'all') echo " checked=\"checked\""; ?>>      <label for="radio3">All</label>
        --->
        </div>
        </td>
        <td valign="top">
            <!--- <button id="button" onClick="spinner_on()">Search Title >></button> --->
        </td>
        <td>
        </td>
    </tr>
    </table>
</form>
