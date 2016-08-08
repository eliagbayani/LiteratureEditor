<?php
// namespace php_active_record;
    /* Expects: radio, book_title, etc. */
?>
<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="articlelist">
    <table border="0" width="100%">
    <tr valign="top">
        <td>
        <div id="radioset">
        <input onClick="spinner_on();submit()" type="radio" id="radio1" name="radio" value="approved" <?php if($radio == 'approved') echo " checked=\"checked\""; ?>> <label for="radio1">For EOL Harvesting</label>
        <input onClick="spinner_on();submit()" type="radio" id="radio2" name="radio" value="draft"    <?php if($radio == 'draft') echo " checked=\"checked\""; ?>>    <label for="radio2">For Review (draft)</label>
        <!---
        <input onClick="spinner_on();submit()" type="radio" id="radio3" name="radio" value="all"      <?php if($radio == 'all') echo " checked=\"checked\""; ?>>      <label for="radio3">All</label>
        --->
        </div>
        </td>
        <!---
        <td valign="top">
             <button id="button" onClick="spinner_on()">Search Title >></button>
        </td>
        --->
    </tr>
    <?php
    if($radio) //one of the radio is clicked
    {
        $book_titles = self::get_unique_book_titles($radio);
        if($total_books = count($book_titles))
        {
            // echo "<pre>"; print_r($book_titles); echo "</pre>";
            ?>
            <tr>
                <td>
                <select name="book_title" id="" style="width: 1000px;" onChange="spinner_on();submit()">
                    <option>-- Choose a title --</option>
                    <?php foreach($book_titles as $book)
                    {
                        $selected = "";
                        if($book_title == $book) $selected = "selected";
                        echo '<option value="' . $book . '" ' . $selected . '>' . $book . '</option>';
                    }?>
                </select>
                <?php echo "Titles = " . $total_books ?>
                </td>
            </tr>
            <?php
        }
        else 
        {
            ?>
            <tr><td>
            <?php self::display_message(array('type' => "highlight", 'msg' => "No $radio articles.")); ?>
            </td></tr>
            <?php
        }
    }

    if($radio == 'approved')
    {
        /* moved to its separate menu item
        $archive_id = "BHL_lit_" . str_replace(array("-",":"," "), "_", date('Y-m-d H:i:s'));
        ?>
        <tr><td><a href='index.php?search_type=gen_archive_all&archive_id=<?php echo $archive_id ?>'>Generate EOL DWC-A for all articles in 'For EOL Harvesting'.</a></td></tr>
        <?php
        */
        $str = "For Review (draft)";
    }
    elseif($radio == 'draft')
    {
        $str = "For EOL Harvesting";
    }

    $wiki_status = "{" . ucfirst($radio) . "}";
    // if($book_title != '-- Choose a title --' && $book_title != "" && in_array($book_title, $book_titles))
    if($radio)
    {
        if(in_array($book_title, $book_titles))
        {
            if($total_books)
            {
                ?>
                <tr><td>
                <a onClick="spinner_on();" href='index.php?search_type=movebatch&wiki_status=<?php echo urlencode($wiki_status) ?>&book_title=<?php echo urlencode($book_title) ?>'><b>Move ALL</b> articles from this title to <b>'<?php echo $str ?>'</b>.</a>
                </td></tr>
                <?php
            }
        }
    }
    ?>
    </table>
</form>
