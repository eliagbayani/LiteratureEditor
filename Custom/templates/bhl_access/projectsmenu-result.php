<?php
// namespace php_active_record;
/* Expects: $params */

switch ($params['radio']) {
    case "proj_my":     $str = "My projects"; break;
    case "proj_active": $str = "Active projects"; break;
    case "proj_comp":   $str = "Completed projects"; break;
    case "proj_start":
        {
            if(isset($params['fromReview']))
            {
                if(@$params['overwrite'] == 1) $str = "Edit an existing project";
                else                           $str = "Edit new project";
            }
            else                               $str = "Start a new project";
            break;
        }
    default: $str = ""; break;
    // code to be executed if n is different from all labels;
}

if($params['radio'] == "proj_active")
{
    // $wiki_status = "{Active}"; not needed
    $rek = self::list_titles_by_type('active', false, true); //2nd param is book_title, 3rd param is boolean $projects
    $rows = $rek['recs'];
    $str .= " = " . count($rows);
}
elseif($params['radio'] == "proj_comp")
{
    // $wiki_status = "{Completed}"; not needed
    $rek = self::list_titles_by_type('completed', false, true); //2nd param is book_title, 3rd param is boolean $projects
    $rows = $rek['recs'];
    $str .= " = " . count($rows);
}
elseif($params['radio'] == "proj_my")
{
    $username = $_COOKIE[MW_DBNAME.'UserName'];
    $rek = self::list_titles_by_type('all_projects', false, true, $username); //2nd param is book_title, 3rd param is boolean $projects, 4th param is logged-in username
    $rows = $rek['recs'];
    $str .= " = " . count($rows);
}
?>
<div id="accordion_open">
    <h3><?php echo $str ?></h3>
    <div>
        <?php
        if(in_array($params['radio'], array("proj_active", "proj_comp", "proj_my")))
        {
            if($rows)
            {
                // echo "<pre>"; print_r($rows); echo "</pre>";
                $data = array('group' => 'projects', 'records' => $rows);
                print self::render_template('article-table', array('data' => @$data));
            }
        }
        elseif($params['radio'] == "proj_start")
        {
            ?>
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Details</a></li>
                    <!--- <li><a href="#tabs-2">Articles</a></li> --->
                </ul>
                <div id="tabs-1">
                    <?php
                    if(isset($params['proj_name']) && !isset($params['overwrite'])) //does not go here anymore...
                    {
                        exit("<br>goes here...<br>");
                        self::move2wiki_project($params);
                    }
                    else
                    {
                        require_once("../templates/bhl_access/proj-start-form.php"); //print self::render_layout(@$params, 'proj-start-form')
                    }
                    ?>
                </div>
                <!--- <div id="tabs-2"></div> --->
            </div>
            <?php
        }
        ?>
    </div>
</div>
