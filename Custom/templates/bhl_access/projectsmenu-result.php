<?php
// namespace php_active_record;
/* Expects: $params */

switch ($params['radio']) {
    case "proj_my":     $str = "My projects"; break;
    case "proj_active": $str = "Active projects"; break;
    case "proj_comp":   $str = "Completed projects"; break;
    case "proj_start":
        {
            if(isset($params['fromReview'])) $str = "Edit an existing project";
            else                             $str = "Start a new project";
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
    /* working but using cookie is shorter
    // [http://editors.eol.localhost/LiteratureEditor/wiki/User:EAgbayani Eli E. Agbayani]
    if(preg_match("/User:(.*?) /ims", $this->compiler, $arr))
    {
        $username = $arr[1];
        echo "<br>username is: [$username] " . $_COOKIE['wiki_literatureeditorUserName'] . "<br>";
        $rows = self::list_titles_by_type('all_projects', false, true, $username); //2nd param is book_title, 3rd param is boolean $projects, 4th param is logged-in username
        $str .= " = " . count($rows);
    }
    */
    
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
        // echo "<pre>"; print_r($params); echo "</pre>";
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
        elseif(in_array($params['radio'], array("proj_active", "proj_comp", "proj_my")))
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