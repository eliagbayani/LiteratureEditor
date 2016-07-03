<?php
// namespace php_active_record;
    /* 
        Expects:
            book_title
            volume
    */
?>

<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="booksearch">
<table border = "0">

    <tr><td colspan="2">Enter at least one of these three search parameters:</td></tr>
    <tr><td>Book title:</td>            <td><input type="text" size="100" name="book_title"<?php if($book_title) echo " value=\"$book_title\""; ?>/> e.g. Selborne</td></tr>
    <tr><td>Author's last name:</td>   <td><input type="text" size="100" name="lname"<?php if($lname) echo " value=\"$lname\""; ?>/> e.g. White</td></tr>
    <tr><td>Collection ID':</td>        <td><input type="text" size="30" name="collectionid"<?php if($collectionid) echo " value=\"$collectionid\""; ?>/> e.g. 4</td></tr>
    
    <tr><td colspan="2">Here are optional search parameters:</td></tr>

    <tr><td>Volume:</td>    <td><input type="text" size="30" name="volume"<?php if($volume) echo " value=\"$volume\""; ?>/> e.g. 2</td></tr>
    <tr><td>Edition:</td>   <td><input type="text" size="30" name="edition"<?php if($edition) echo " value=\"$edition\""; ?>/> e.g. new</td></tr>
    <tr><td>Year:</td>      <td><input type="text" size="30" name="year"<?php if($year) echo " value=\"$year\""; ?>/> e.g. 1825</td></tr>
    <tr><td>Subject:</td>   <td><input type="text" size="30" name="subject"<?php if($subject) echo " value=\"$subject\""; ?>/></td></tr>
    <tr><td>Language:</td>  <td><input type="text" size="30" name="language"<?php if($language) echo " value=\"$language\""; ?>/> e.g. eng
    &nbsp;&nbsp;&nbsp; <button id="button_search_book" onClick="spinner_on()">Search this book</button>
    </td>
    <td>
        <!---
        <?php $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/" ?>
        <?php self::image_with_text(array("text" => "Back to Wiki", "src" => "../images/Back_icon.png", "alt_text" => "Back to Wiki", "href" => $back));?>
        --->
    </td>
    </tr>
</table>
</form>

