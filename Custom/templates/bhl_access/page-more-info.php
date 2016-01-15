<?php

// $params =& $_GET;
// print_r($params);
// $page = json_decode($params['page']);
// echo "<hr>";
// print_r($page);

$Page = json_decode($arr);

?>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Page Summary</a></li>
        <li><a href="#tabs-2">PagesTypes</a></li>
        <li><a href="#tabs-3">PageNumbers</a></li>
        <li><a href="#tabs-4">Taxa</a></li>
    </ul>
    <?php require_once("page-more-info-sub.php") ?>
    
    <div id="tabs-4">
        <?php print self::render_layout(array("search_type" => "pagetaxasearch", "page_id" => $Page->PageID), 'result') ?>
    </div>
</div>
