<?php
// namespace php_active_record;
    /* 
        Expects: $params
    */
?>
<div id="accordion_open2">
    <h3>Review Excerpt</h3>
    <div>
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
        
        if(@$params['overwrite']) $submit_txt = "Overwrite existing Wiki - for EOL Ingestion";
        else                      $submit_txt = "Save to Wiki - for EOL Ingestion";
        
    ?>
    <button onClick="document.getElementById('accordion_item').value=0;spinner_on();">Edit All</button>
    <button onClick="document.getElementById('search_type').value='move2wiki';spinner_on();"><?php echo $submit_txt ?> >></button>
    </form>
    </div>
</div>
