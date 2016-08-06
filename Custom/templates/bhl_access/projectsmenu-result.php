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
                exist("<br>goes here...<br>");
                self::move2wiki_project($params);
            }
            else
            {
                require_once("../templates/bhl_access/proj-start-form.php"); //print self::render_layout(@$params, 'proj-start-form')
            }
        }
        elseif($params['radio'] == "proj_active")
        {
            // $wiki_status = "{Active}"; not needed
            $rows = self::list_titles_by_type('active', false, true); //2nd param is book_title, 3rd param is boolean $projects
        }
        elseif($params['radio'] == "proj_comp")
        {
            // $wiki_status = "{Completed}"; not needed
            $rows = self::list_titles_by_type('completed', false, true); //2nd param is book_title, 3rd param is boolean $projects
        }
        
        if(in_array($params['radio'], array("proj_active", "proj_comp")))
        {
            if($rows)
            {
                // echo "<pre>"; print_r($rows); echo "</pre>";
                
                $data = array('group' => 'projects', 'records' => $rows);
                print self::render_template('article-table', array('data' => @$data));
            }
        }

        ?>
    </div>
</div>
