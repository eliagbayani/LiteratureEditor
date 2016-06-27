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
    $wiki_text = self::get_wiki_text($params['wiki_title']);
    self::parse_wiki_text($wiki_text, $params);
?>
