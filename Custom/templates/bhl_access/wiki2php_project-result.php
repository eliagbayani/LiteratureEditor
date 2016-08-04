<?php
// namespace php_active_record;
    /* 
        Expects: $params
        Array
        (
            [search_type] => wiki2php
            [wiki_title] => 16194405 ee667ff5a5361aedaaa35b2e1e55338e
            [overwrite] => 1
        )
    */
    echo "<pre>"; print_r($params); echo "</pre>";
    $info = self::get_wiki_text($params['wiki_title']);
    $wiki_text = $info['content'];
    if(!$wiki_text)
    {
        if(strpos($params['wiki_title'], "ForHarvesting") !== false) $params['wiki_title'] = str_replace("ForHarvesting:", "", $params['wiki_title']); //string found
        else                                                         $params['wiki_title'] = "ForHarvesting:".$params['wiki_title'];

        $info = self::get_wiki_text($params['wiki_title']);
        $wiki_text = $info['content'];


    }
    
    if($wiki_text) self::parse_wiki_text($wiki_text, $params, true); //true means projects
    else
    {
        ?>
        <div id="accordion_open2">
            <h3>Review Excerpt</h3>
            <div>
            <?php
            self::display_message(array('type' => "error", 'msg' => "You cannot review nor edit wiki that are already for harvesting."));
            self::display_message(array('type' => "error", 'msg' => "An admin must move this to drafts (Main namespace) before you can review/edit it."));
            echo "<br><a href='#' onClick='window.history.back()'>Go Back</a>";
            ?>
            </div>
        </div>
        <?php
    }
?>

