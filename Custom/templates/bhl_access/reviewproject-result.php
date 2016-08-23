<?php
// namespace php_active_record;
    /* Expects: $params */
    $params['wiki_status'] = self::page_status($params['wiki_title'], true); //true means project
?>
<div id="accordion_open2">
    <h3>Review Project <?php echo " - <i>" . $params['wiki_status'] . "</i>" ?></h3>
    <div>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Details</a></li>
                <li><a href="#tabs-2">Articles {Draft}</a></li>
                <li><a href="#tabs-3">Articles {Approved}</a></li>
            </ul>
            <div id="tabs-1"><?php require_once("project_info_details.php") ?></div>
            <div id="tabs-2"><?php 
                $wiki_status = "{Draft}";
                $table_id = "draft";
                require("project_info_articles.php")
            ?></div>
            <div id="tabs-3"><?php 
                $wiki_status = "{Approved}";
                $table_id = "approved";
                require("project_info_articles.php")
            ?></div>
        </div>
    </div>
</div>
<script>
function confirm_project_delete()
{
    if (confirm("Are you sure to DELETE?") == true)
    {
        document.getElementById("frm_del_proj").submit();
    }
}
</script>
