<?php
require_once("reviewproject-result-pre.php");
if($cont_review)
{
    //for project deletion
    if    ($params['wiki_status'] == "{Active}")    $radio = "proj_active";
    elseif($params['wiki_status'] == "{Completed}") $radio = "proj_comp";
?>

<form id="frm_del_proj" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="deletewiki_project">
<input type="hidden" name="wiki_title" value="<?php echo $params['wiki_title'] ?>">
<input type="hidden" name="wiki_status" value="<?php echo $params['wiki_status'] ?>">
<input type="hidden" name="radio" value="<?php echo $radio ?>">
</form>

<form name="" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="fromReview">
<input type="text" name="articles" value="<?php echo @$params['articles'] ?>">
<?php
    // echo"<pre>";print_r($params);echo"</pre>";
    $params['compiler'] = self::cumulatime_compiler($params);
    self::review_excerpt_project($params); //this displays all the fields in HTML
    $fields = array_keys($params);
    $params['search_type'] = "projectsmenu"; //goes to the form
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
    if(@$params['overwrite']) $submit_txt = "Save project (will overwrite existing)";
    else                      $submit_txt = "Save project";
?>
<button onClick="spinner_on();">Edit</button>
<button onClick="document.getElementById('search_type').value='move2wiki_project';spinner_on();"><?php echo $submit_txt ?></button>
</form>

<?php
}
?>
