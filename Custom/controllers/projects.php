<?php
// namespace php_active_record;

class projects_controller
{
    function __construct($params)
    {
    }


    function project_article_adjustments($params) //when moving files
    {
        //start update project when article is moved while the article is assigned to a project ------------
        if(in_array($params['wiki_status'], array("{Draft}", "{Approved}"))) //meaning an article is being moved, not a project
        {
            echo "<br>goes to aritcle<br>";
            if($project = @$params['projects']) //meaning the article is assigned to a project
            {
                echo " - with project<br>";
                $p = array();
                $p['project'] = $project;
                $p['wiki_title'] = $params['wiki_title'];
                $p['wiki_status'] = $params['wiki_status'];
                self::update_proj_when_article_moves($p); //since this article has projects, really it is only project (1)
            }
        }//end ------------
        else //project is being moved
        {   //start update of article(s) when project is moved while article is attached to it
            // echo "<pre>"; print_r($params); echo "</pre>";
            echo "<br>goes to project<br>";
            // Array $params
            // (
            //     [wiki_title] => Active_Projects:Planet_of_the_Apes
            //     [search_type] => move24harvest
            //     [wiki_status] => {Active}
            //     [token] => 8edb9ed4f5c5ce50d16c543c7218212f57b461a9+\
            //     [articles] => ForHarvesting:16194361_dbd860482d762327211c39ba89f3e58a
            // )
            if($articles = @$params['articles'])
            {
                echo " - with article<br>";
                $p = array();
                $p['articles'] = $articles;
                $p['project'] = $params['wiki_title'];
                $p['wiki_status'] = $params['wiki_status'];
                self::update_articles_when_project_moves($p);
            }
            // exit("<br>project is moving...<br>");
        }
    }

    function update_proj_when_article_moves($params)
    {   /*
        [project] => Active_Projects:project_01
        [wiki_title] => 46306603_f5d670746cdd936d6bc1af1f3cd959a2   --- of the article
        [wiki_status] => {Draft}                                    --- of the article
        */
        $info = bhl_controller::get_wiki_text($params['project']);
        if($wiki_text = $info['content'])
        {
            if($p = bhl_controller::get_void_part($wiki_text))
            {
                // echo "<pre>"; print_r($p); echo "</pre>";
                if($articles = $p['articles'])
                {
                    //start replacing the article's name saved in project with the new moved article name
                    if($params['wiki_status'] == "{Draft}") //put ForHarvesting:
                    {
                        echo "<br>goes 111<br>";
                        $str = str_replace("ForHarvesting:", "", $params['wiki_title']); //should not do this but...
                        $replace = "ForHarvesting:".$str;
                        $replace = str_replace("ForHarvesting:ForHarvesting:", "ForHarvesting:", $replace);
                        
                    }
                    elseif($params['wiki_status'] == "{Approved}") //remove ForHarvesting:
                    {
                        echo "<br>goes 222<br>";
                        $replace = str_replace("ForHarvesting:", "", $params['wiki_title']);
                    }
                    else
                    {
                        echo "<pre>"; print_r($params); echo "</pre>";
                        exit("<br>investigate 001<br>");
                    }
                    
                    
                    $p['articles'] = str_ireplace($params['wiki_title'], $replace, $p['articles']);
                    
                    $p['wiki_title'] = $params['project']; //kind a new, BUT needed since I was not concerned before its value that's saved in the wiki
                    $p['new_article'] = "";
                    $p['remove_article'] = "";
                    bhl_controller::move2wiki_project($p, false); //saving project
                    // exit("<br>-elix-");
                }
            }
        }
        else
        {
            bhl_controller::display_message(array('type' => "error", 'msg' => "Project doesn't exist anymore."));
            echo "<pre>"; print_r($params); echo "</pre>";
            exit("<br>-no wiki text-111");
            return false;
        }
    }
    
    function update_articles_when_project_moves($params)
    {   /*
        [project] => Active_Projects:Planet_of_the_Apes
        [wiki_status] => {Active}
        [articles] => ForHarvesting:16194361_dbd860482d762327211c39ba89f3e58a; other1; other2
        */
        $articles = explode(";", $params['articles']);
        $articles = array_map("trim", $articles);
        $articles = array_filter($articles);
        echo "<pre>"; print_r($articles); echo "</pre>";
        foreach($articles as $article)
        {
            $info = bhl_controller::get_wiki_text($article);
            if($wiki_text = $info['content'])
            {
                if($p = bhl_controller::get_void_part($wiki_text)) //$p is contents of the article
                {
                    // echo "<pre>"; print_r($p); echo "</pre>";
                    if($projects = $p['projects'])
                    {
                        //start replacing the project's name saved in article with the new moved project name
                        if($params['wiki_status'] == "{Active}")        $replace = str_replace("Active_Projects:", "Completed_Projects:", $params['project']);
                        elseif($params['wiki_status'] == "{Completed}") $replace = str_replace("Completed_Projects:", "Active_Projects:", $params['project']);

                        $p['wiki_title'] = $article; //kind a new, BUT needed since I was not concerned before its value that's saved in the wiki
                        $p['projects'] = $replace;
                        $p['new_project'] = "";
                        $p['remove_project'] = "";

                        // echo "<pre>"; print_r($p); echo "</pre>"; print("<br>ditox<br>");
                        
                        bhl_controller::move2wiki($p, false); //saving article
                        // exit("<br>-elix-");
                    }
                }
            }
            else
            {
                bhl_controller::display_message(array('type' => "error", 'msg' => "Article doesn't exist anymore."));
                exit("<br>-no wiki text 222 [$article]-");
                return false;
            }
        }
    }



    function is_eli()
    {
        if($_COOKIE['wiki_literatureeditorUserName'] == "EAgbayani") return true;
        else return false;
    }
    
    
    
    
}
?>
