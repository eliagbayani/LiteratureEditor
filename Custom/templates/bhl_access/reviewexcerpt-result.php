<?php
// namespace php_active_record;
    /* Expects: $params */
    // print_r($params);
    $wiki_status = self::page_status($params['wiki_title']);
?>
<div id="accordion_open2">
    <h3>Review Excerpt <?php echo " - <i>" . $wiki_status . "</i>" ?></h3>
    <div>

    <?php
    if($params['search_type'] == "move2wiki") // from wiki OR from article list
    {
        if($wiki_status == "{Approved}") // you can generate archive
        {
            self::display_message(array('type' => "highlight", 'msg' => "Article approved, you can <a href='index.php?search_type=gen_archive&wiki_title=" . urldecode($params['wiki_title']) . "'>generate the EOL DWC-A</a> for it."));
            echo "<br>";
        }
    }
    ?>

    <form name="" action="index.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="fromReview">
    <?php
        // echo"<pre>";print_r($params);echo"</pre>";
        $params['compiler'] = self::cumulatime_compiler($params);
        self::review_excerpt($params); //this displays all the fields in HTML
        $fields = array_keys($params);
        $params['search_type'] = "pagesearch"; //goes to the form
        foreach($fields as $field)
        {
            if(in_array($field, array("ocr_text", "references", "bibliographicCitation")))
            {
                echo "<textarea name='" . $field . "' style='display:none;'>" . $params[$field] . "</textarea>";
            }
            else
            {
                echo '<input type="hidden" name="' . $field . '" value="' . $params[$field] . '"';
                if($field == "accordion_item") echo ' id="' . $field . '"' ;
                if($field == "search_type")    echo ' id="' . $field . '"' ;
                echo '>';
            }
        }
        
        if(@$params['overwrite']) $submit_txt = "Submit article to EOL (will overwrite existing)";
        else                      $submit_txt = "Submit article to EOL";
        
    ?>
    <button onClick="document.getElementById('accordion_item').value=0;spinner_on();">Edit All</button>
    <button onClick="document.getElementById('search_type').value='move2wiki';spinner_on();"><?php echo $submit_txt ?></button>
    </form>
    </div>
</div>
