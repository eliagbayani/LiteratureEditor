<?php
// namespace php_active_record;

class projects_controller
{
    function __construct($params)
    {
    }

    function is_eli()
    {
        if($_COOKIE['wiki_literatureeditorUserName'] == "EAgbayani") return true;
        else return false;
    }
}
?>
