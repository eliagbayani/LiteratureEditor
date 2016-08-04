<?php
// namespace php_active_record;
/* Expects: $params */

?>


<div id="accordion_open">
    <h3>elix</h3>
    <div>
        <?php
        
        echo "<pre>xxx"; print_r($params); echo "yyy</pre>";
        
        if($params['radio'] == "proj_start")
        {
            if(isset($params['proj_name']) && !isset($params['overwrite']))
            {
                self::move2wiki_project($params);
            }
            else
            {
                echo "<br>goes to form<br>";
                require_once("../templates/bhl_access/proj-start-form.php"); //print self::render_layout(@$params, 'proj-start-form')
            }
        }
        
        ?>
    </div>
</div>
