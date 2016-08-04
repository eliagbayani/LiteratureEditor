<?php
// namespace php_active_record;
    /* Expects: radio, book_title, etc. */
?>
<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="projectsmenu">
    <table border="0" width="100%">
    <tr valign="top">
        <td>
        <div id="radioset">
        <input onClick="spinner_on();submit()" type="radio" id="radio1" name="radio" value="proj_my"     <?php if($radio == 'proj_my')     echo " checked=\"checked\""; ?>> <label for="radio1">My projects</label>
        <input onClick="spinner_on();submit()" type="radio" id="radio2" name="radio" value="proj_active" <?php if($radio == 'proj_active') echo " checked=\"checked\""; ?>> <label for="radio2">Active projects</label>
        <input onClick="spinner_on();submit()" type="radio" id="radio3" name="radio" value="proj_comp"   <?php if($radio == 'proj_comp')   echo " checked=\"checked\""; ?>> <label for="radio3">Completed projects</label>
        <input onClick="spinner_on();submit()" type="radio" id="radio4" name="radio" value="proj_start"  <?php if($radio == 'proj_start')  echo " checked=\"checked\""; ?>> <label for="radio4">Start a new project</label>
        </div>
        </td>
    </tr>
    <?php
    /*
    echo "<br>radio:[$radio]<br>";
    if($radio) //one of the radio is clicked
    {}
    if($radio == 'approved') {}
    elseif($radio == 'draft'){}
    */
    ?>
    </table>
</form>
