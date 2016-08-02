<?php
// namespace php_active_record;
/* Expects: $params */

?>


<div id="accordion_open">
    <h3>elix</h3>
    <div>
        <?php
        
        echo "<pre>"; print_r($params); echo "</pre>";
        
        if($params['radio'] == "proj_start")
        {
            if(isset($params['proj_name']))
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
