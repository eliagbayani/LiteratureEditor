<?php
// namespace php_active_record;
    /* Expects: radio, book_title, etc. */
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
    <?php
    if($radio == 'approved')
    {
        date_default_timezone_set('America/New_York');
        $archive_id = "BHL_lit_" . str_replace(array("-",":"," "), "_", date('Y-m-d H:i:s'));
        ?>
        <tr><td><a href='index.php?search_type=gen_archive_all&archive_id=<?php echo $archive_id ?>'>Generate EOL DWC-A for these articles.</a></td></tr>
        <?php
    }
    elseif($radio == 'draft') {}
    if($radio) //one of the radio is clicked
    {
        $book_titles = self::get_unique_book_titles($radio);
        // echo "<pre>"; print_r($book_titles); echo "</pre>";
        ?>
        <tr>
            <td>
            <select name="book_title" id="" style="width: 1000px;" onChange="spinner_on();submit()">
                <option>-- Pick a title --</option>
                <?php foreach($book_titles as $book)
                {
                    $selected = "";
                    if($book_title == $book) $selected = "selected";
                    echo '<option value="' . $book . '" ' . $selected . '>' . $book . '</option>';
                }?>
            </select>
            </td>
        </tr>
        <?php
    }
    ?>
    </table>
</form>
