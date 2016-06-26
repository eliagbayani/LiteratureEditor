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
        echo"<pre>";print_r($params);echo"</pre>";
        self::review_excerpt($params);
        $fields = array_keys($params);
        $params['search_type'] = "pagesearch"; //goes to the form
        foreach($fields as $field)
        {
            // echo "<input type='hidden' name='" . $field . "' value='" . $params[$field] . "' ";
            
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
    ?>
    <button onClick="document.getElementById('accordion_item').value=0;spinner_on();">Edit All</button>
    <button onClick="document.getElementById('search_type').value='move2wiki';spinner_on();">Save to Wiki for EOL Ingestion >></button>
    </form>
    </div>
</div>
