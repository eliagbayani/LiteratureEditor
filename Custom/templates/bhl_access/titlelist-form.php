<?php
// namespace php_active_record;
    /* 
        Expects:
            radio
    */
?>

<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="titlelist">
    <table border="0">
    <tr valign="top">
        <td>
        <div id="radioset">
        <input type="radio" id="radio1" name="radio" value="a" <?php if($radio == 'a') echo " checked=\"checked\""; ?>> <label for="radio1">A</label>
        <input type="radio" id="radio2" name="radio" value="b" <?php if($radio == 'b') echo " checked=\"checked\""; ?>> <label for="radio2">B</label>
        <input type="radio" id="radio3" name="radio" value="c" <?php if($radio == 'c') echo " checked=\"checked\""; ?>> <label for="radio3">C</label>
        <input type="radio" id="radio4" name="radio" value="d" <?php if($radio == 'd') echo " checked=\"checked\""; ?>> <label for="radio4">D</label>
        <input type="radio" id="radio5" name="radio" value="e" <?php if($radio == 'e') echo " checked=\"checked\""; ?>> <label for="radio5">E</label>
        <input type="radio" id="radio6" name="radio" value="f" <?php if($radio == 'f') echo " checked=\"checked\""; ?>> <label for="radio6">F</label>
        <input type="radio" id="radio7" name="radio" value="g" <?php if($radio == 'g') echo " checked=\"checked\""; ?>> <label for="radio7">G</label>
        <input type="radio" id="radio8" name="radio" value="h" <?php if($radio == 'h') echo " checked=\"checked\""; ?>> <label for="radio8">H</label>
        <input type="radio" id="radio9" name="radio" value="i" <?php if($radio == 'i') echo " checked=\"checked\""; ?>> <label for="radio9">I</label>
        <input type="radio" id="radio10" name="radio" value="j" <?php if($radio == 'j') echo " checked=\"checked\""; ?>> <label for="radio10">J</label>
        <input type="radio" id="radio11" name="radio" value="k" <?php if($radio == 'k') echo " checked=\"checked\""; ?>> <label for="radio11">K</label>
        <input type="radio" id="radio12" name="radio" value="l" <?php if($radio == 'l') echo " checked=\"checked\""; ?>> <label for="radio12">L</label>
        <input type="radio" id="radio13" name="radio" value="m" <?php if($radio == 'm') echo " checked=\"checked\""; ?>> <label for="radio13">M</label>
        <input type="radio" id="radio14" name="radio" value="n" <?php if($radio == 'n') echo " checked=\"checked\""; ?>> <label for="radio14">N</label>
        <input type="radio" id="radio15" name="radio" value="o" <?php if($radio == 'o') echo " checked=\"checked\""; ?>> <label for="radio15">O</label>
        <input type="radio" id="radio16" name="radio" value="p" <?php if($radio == 'p') echo " checked=\"checked\""; ?>> <label for="radio16">P</label>
        <input type="radio" id="radio17" name="radio" value="q" <?php if($radio == 'q') echo " checked=\"checked\""; ?>> <label for="radio17">Q</label>
        <input type="radio" id="radio18" name="radio" value="r" <?php if($radio == 'r') echo " checked=\"checked\""; ?>> <label for="radio18">R</label>
        <input type="radio" id="radio19" name="radio" value="s" <?php if($radio == 's') echo " checked=\"checked\""; ?>> <label for="radio19">S</label>
        <input type="radio" id="radio20" name="radio" value="t" <?php if($radio == 't') echo " checked=\"checked\""; ?>> <label for="radio20">T</label>
        <input type="radio" id="radio21" name="radio" value="u" <?php if($radio == 'u') echo " checked=\"checked\""; ?>> <label for="radio21">U</label>
        <input type="radio" id="radio22" name="radio" value="v" <?php if($radio == 'v') echo " checked=\"checked\""; ?>> <label for="radio22">V</label>
        <input type="radio" id="radio23" name="radio" value="w" <?php if($radio == 'w') echo " checked=\"checked\""; ?>> <label for="radio23">W</label>
        <input type="radio" id="radio24" name="radio" value="x" <?php if($radio == 'x') echo " checked=\"checked\""; ?>> <label for="radio24">X</label>
        <input type="radio" id="radio25" name="radio" value="y" <?php if($radio == 'y') echo " checked=\"checked\""; ?>> <label for="radio25">Y</label>
        <input type="radio" id="radio26" name="radio" value="z" <?php if($radio == 'z') echo " checked=\"checked\""; ?>> <label for="radio26">Z</label>
        <!-- <button id="button">Search Title >></button> -->
        <br>
        <input type="radio" id="radio35" name="radio" value="1" <?php if($radio == '1') echo " checked=\"checked\""; ?>> <label for="radio35">1</label>
        <input type="radio" id="radio36" name="radio" value="2" <?php if($radio == '2') echo " checked=\"checked\""; ?>> <label for="radio36">2</label>
        <input type="radio" id="radio37" name="radio" value="3" <?php if($radio == '3') echo " checked=\"checked\""; ?>> <label for="radio37">3</label>
        <input type="radio" id="radio38" name="radio" value="4" <?php if($radio == '4') echo " checked=\"checked\""; ?>> <label for="radio38">4</label>
        <input type="radio" id="radio39" name="radio" value="5" <?php if($radio == '5') echo " checked=\"checked\""; ?>> <label for="radio39">5</label>
        <input type="radio" id="radio40" name="radio" value="6" <?php if($radio == '6') echo " checked=\"checked\""; ?>> <label for="radio40">6</label>
        <input type="radio" id="radio41" name="radio" value="7" <?php if($radio == '7') echo " checked=\"checked\""; ?>> <label for="radio41">7</label>
        <input type="radio" id="radio42" name="radio" value="8" <?php if($radio == '8') echo " checked=\"checked\""; ?>> <label for="radio42">8</label>
        <input type="radio" id="radio43" name="radio" value="9" <?php if($radio == '9') echo " checked=\"checked\""; ?>> <label for="radio43">9</label>

        <input type="radio" id="radio27" name="radio" value="'" <?php if($radio == "'") echo " checked=\"checked\""; ?>> <label for="radio27">'</label>
        <input type="radio" id="radio28" name="radio" value='"' <?php if($radio == '"') echo " checked=\"checked\""; ?>> <label for="radio28">"</label>
        <input type="radio" id="radio29" name="radio" value="(" <?php if($radio == '(') echo " checked=\"checked\""; ?>> <label for="radio29">(</label>
        <input type="radio" id="radio30" name="radio" value="[" <?php if($radio == '[') echo " checked=\"checked\""; ?>> <label for="radio30">[</label>
        <input type="radio" id="radio31" name="radio" value="#" <?php if($radio == '#') echo " checked=\"checked\""; ?>> <label for="radio31">#</label>
        <input type="radio" id="radio34" name="radio" value="$" <?php if($radio == '$') echo " checked=\"checked\""; ?>> <label for="radio34">$</label>
        <input type="radio" id="radio32" name="radio" value="others1" <?php if($radio == 'others1') echo " checked=\"checked\""; ?>> <label for="radio32">“</label>
        <input type="radio" id="radio33" name="radio" value="others2" <?php if($radio == 'others2') echo " checked=\"checked\""; ?>> <label for="radio33">others</label>
        </div>

        </td>
        <td valign="top">
            <button id="button" onClick="spinner_on()">Search Title >></button>
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

<!--
<input type="text" size="20" name="title_id"<?php if($title_id) echo " value=\"$title_id\""; ?>/>
<form style="margin-top: 1em;">
</form>
-->


