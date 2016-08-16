<?php
// namespace php_active_record;
    /* Expects: $params */
    // print_r($params);
    $params['wiki_status'] = self::page_status($params['wiki_title']);
?>
<div id="accordion_open2">
    <h3>Review Excerpt <?php echo " - <i>" . $params['wiki_status'] . "</i>" ?></h3>
    <div>

    <?php
    //for article deletion
    if($params['wiki_status'] == "{Approved}") $radio = "approved";
    elseif($params['wiki_status'] == "{Draft}") $radio = "draft";
    ?>
    <form id="frm_del_article" action="index.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="search_type" value="deletewiki">
    <input type="hidden" name="wiki_title" value="<?php echo $params['wiki_title'] ?>">
    <input type="hidden" name="wiki_status" value="<?php echo $params['wiki_status'] ?>">
    <input type="hidden" name="radio" value="<?php echo $radio ?>">
    </form>

    <form id="submit_article" name="" action="index.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="fromReview">
    
    <input id="new_project" type="hidden" name="new_project">
    <input id="remove_project" type="hidden" name="remove_project">
    
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
            elseif($field == "new_project") {} //just ignore
            elseif($field == "remove_project") {} //just ignore
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
<script>
function confirm_article_delete()
{
    if (confirm("Are you sure to DELETE?") == true)
    {
        document.getElementById("frm_del_article").submit();
    }
}
function assign_project(project)
{
    document.getElementById("new_project").value = project;
    document.getElementById('search_type').value = 'move2wiki';
    spinner_on();
    document.getElementById("submit_article").submit();
}
function remove_project()
{
    document.getElementById("remove_project").value = 1;
    document.getElementById('search_type').value = 'move2wiki';
    spinner_on();
    document.getElementById("submit_article").submit();
}

</script>
