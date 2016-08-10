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
    // echo "<pre>"; print_r($params); echo "</pre>";
    $info = self::get_wiki_text($params['wiki_title']);
    $wiki_text = $info['content'];
    if(!$wiki_text)
    {
        if(strpos($params['wiki_title'], "ForHarvesting") !== false) $params['wiki_title'] = str_replace("ForHarvesting:", "", $params['wiki_title']); //string found
        else                                                         $params['wiki_title'] = "ForHarvesting:".$params['wiki_title'];
    }
    $info = self::get_wiki_text($params['wiki_title']);
    $wiki_text = $info['content'];
    if($wiki_text) self::parse_wiki_text($wiki_text, $params);
    else
    {
        ?>
        <div id="accordion_open2">
            <h3>Review Excerpt</h3>
            <div>
            <?php
            self::display_message(array('type' => "error", 'msg' => "Error occurred. Record may not exist anymore."));
            echo "<br><a href='#' onClick='window.history.back()'>Go Back</a>";
            ?>
            </div>
        </div>
        <?php
    }
?>

