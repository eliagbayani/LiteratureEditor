<?php
// namespace php_active_record;
    /* Expects: $params */
    // echo "<pre>"; print_r($params); echo "</pre>"; //exit;
?>
<div id="accordion_open">
    <h3>Move to Wiki</h3>
    <div>
    <?php
        if($new_project = @$params['new_project'])
        {
            if($new_project != $_SESSION['working_proj']) self::display_message(array('type' => "error", 'msg' => "Cannot proceed. Working project no longer exists."));
            else
            {
                $params['projects'] = self::adjust_projects($params);
                /*
                [new_project] => 
                [projects] => Active_Projects:project_01
                [wiki_title] => ForHarvesting:16194361_dbd860482d762327211c39ba89f3e58a
                */
                // echo "<pre>"; print_r($params); echo "</pre>"; exit("<br>add article 2 proj<br>");
                self::add_article_2proj($params);
                self::move2wiki($params);
            }
        }
        elseif(@$params['remove_project'])
        {
            $params['projects'] = self::adjust_projects($params);
            // echo "<pre>"; print_r($params); echo "</pre>"; exit;
            /*
            [new_project] => 
            [remove_project] => 1
            [projects] => Completed_Projects:Project_03; 
            [search_type] => move2wiki
            [overwrite] => 1
            [wiki_title] => 16194406_f4dc920dad6514b1bb210e8e73c71183
            */
            self::remove_article_2proj($params);
            $params['projects'] = "";
            self::move2wiki($params);
        }
        else self::move2wiki($params);
    ?>
    </div>
</div>
