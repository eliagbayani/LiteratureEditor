<?php
// namespace php_active_record;
    /* 
        Expects: $params
        Array
        (
            [search_type] => gen_archive
            [wiki_title] => ForHarvesting:16194405 ee667ff5a5361aedaaa35b2e1e55338e
        )
    */
    // echo "<pre>"; print_r($params); echo "</pre>";
    
    $url = EOL_PHP_CODE . "update_resources/connectors/mediawiki.php?wiki_title=" . urldecode($params['wiki_title']);


?>
    

<div id="accordion_open2">
    <h3>Generate EOL DWC-A</h3>
    <div>
    <?php
    if($val = Functions::lookup_with_cache($url, array('expire_seconds' => true)))
    {
        if(strpos($val, "[SUCCESS]") !== false) //string is found
        {
            self::display_message(array('type' => "highlight", 'msg' => "EOL DWC-A successfully generated."));
            $url = EOL_PHP_CODE . "applications/content_server/resources/" . str_replace(array(":"," "), "_", $params['wiki_title']) . ".tar.gz";

            echo "<br>You can copy the URL below and use it as a resource in an EOL Content Partner resource account (<a href='http://eol.org'>eol.org</a>).";
            echo "<br><br><a href='" . $url . "'>$url</a>";

            $url = EOL_PHP_CODE . "applications/dwc_validator/index.php?file_url=" . $url;
            echo "<br><br>You can also try to validate the archive file <a href='" . $url . "'>here</a>.";
            
            // http://localhost/eol_php_code/applications/dwc_validator/index.php?file_url=http://editors.eol.localhost/eol_php_code/applications/content_server/resources/ForHarvesting_16194405_ae66e9b6f430af7e694cad4cf1d6f295.tar.gz
            
            
        }
        else self::display_message(array('type' => "error", 'msg' => "Process un-successful."));
    }
    else self::display_message(array('type' => "error", 'msg' => "Process un-successful."));

    // echo "<br>[$val]<br>"; //debug
    
    echo "<br><br><a href='#' onClick='window.history.back()'>Go Back</a>";
    ?>
    </div>
</div>
    
    

