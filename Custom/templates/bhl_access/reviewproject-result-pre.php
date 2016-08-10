<?php
$pass_title = ucfirst(str_replace(" ", "_", $params['proj_name']));
$pass_title = $params['proj_name'];

$cont_review = true;
echo "<pre>"; print_r($params); echo "</pre>";

if($params['overwrite'] != 1)
{
    if(count(array_keys($params)) == 8)
    {
        if($title = self::project_exists($pass_title))
        {
            $cont_review = false;
            self::display_message(array('type' => "error", 'msg' => "'$pass_title' - Project name already exists."));
            $post = self::page_status($title, true); //true means projects
            echo "<br>" . "<a href='index.php?search_type=wiki2php_project&wiki_title=$title&overwrite=1'>View existing project</a> &nbsp; <i>$post</i><br>";
            echo "<br>OR<br><br><a href='javascript:history.go(-1)'>Go back and edit</a>";
        }
        /*
        if($titles = self::check_if_this_title_has_wiki_v2($pass_title, "5002|5004"))
        {
            echo "<pre>"; print_r($titles);echo "</pre>"; //exit("<br>stop muna<br>");
            // echo "<pre>"; print_r($params);echo "</pre>";
            if($titles)
            {
                $cont_review = false;
                if(count($titles > 1)) $str = "excerpts have";
                else                   $str = "excerpt has";
                self::display_message(array('type' => "error", 'msg' => "Project name already exists."));
                foreach($titles as $r)
                {
                    $post = self::page_status($r->title, true); //true means projects
                    echo "<br>" . " - <a href='index.php?search_type=wiki2php_project&wiki_title=$r->title&overwrite=1'>view</a> &nbsp; <i>$post</i><br>";
                }
                // http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?wiki_title=Active_Projects:project_1&search_type=wiki2php_project&overwrite=1
                // echo "<p><br><a href='index.php?search_type=pagesearch&page_id=$Page->PageID&continue'>Create new excerpt for this page</a>";
            }
            $submit_text = "Proceed overwrite Wiki page";
        }
        else $submit_text = "Export this to Wiki";
        */
    }
}
else
{
    $arr = explode(":", $params['wiki_title']);
    $old_title = str_replace("_", " ", $arr[1]);
    
    //adjust for display
    $old_title = ucfirst($old_title);
    $pass_title = ucfirst($pass_title);
    
    if($pass_title != $old_title)
    {
        if($title = self::project_exists($pass_title))
        {
            $cont_review = false;
            self::display_message(array('type' => "error", 'msg' => "'$pass_title' - Project name already exists 2. [$old_title]"));
            $post = self::page_status($title, true); //true means projects
            echo "<br>" . "<a href='index.php?search_type=wiki2php_project&wiki_title=$title&overwrite=1'>View existing project</a> &nbsp; <i>$post</i><br>";
            echo "<br>OR<br><br><a href='javascript:history.go(-1)'>Go back and edit</a>";
            
            
        }
    }
}
?>
