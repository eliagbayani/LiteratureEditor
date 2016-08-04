<?php
// namespace php_active_record;
/* Expects: $params */

switch ($params['radio']) {
    case "proj_my":     $str = "My projects"; break;
    case "proj_active": $str = "Active projects"; break;
    case "proj_comp": $str = "Completed projects"; break;
    case "proj_start": $str = "Start a new project"; break;
    // default:
    //     code to be executed if n is different from all labels;
}
?>
<div id="accordion_open">
    <h3><?php echo $str ?></h3>
    <div>
        <?php
        // echo "<pre>xxx"; print_r($params); echo "yyy</pre>";
        if($params['radio'] == "proj_start")
        {
            if(isset($params['proj_name']) && !isset($params['overwrite']))
            {
                self::move2wiki_project($params);
            }
            else
            {
                require_once("../templates/bhl_access/proj-start-form.php"); //print self::render_layout(@$params, 'proj-start-form')
            }
        }
        ?>
    </div>
</div>
