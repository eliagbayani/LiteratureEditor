<?php
// namespace php_active_record;
    /* 
        Expects: $params
    */
    $wiki_text = self::get_wiki_text($params['wiki_title']);
    self::parse_wiki_text($wiki_text, $params);
?>
