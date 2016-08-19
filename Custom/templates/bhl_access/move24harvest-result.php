<?php
// namespace php_active_record;
    /*  Expects: $params
        Array - past ??
        (
            [wiki_title] => 19369800 1790b123a8a0209e1500fd5be12842f5
            [search_type] => move24harvest
            [wiki_status] => {Draft}
        )
        
        Array - as of Aug 17
        (
            [wiki_title] => 46306603_f5d670746cdd936d6bc1af1f3cd959a2
            [search_type] => move24harvest
            [wiki_status] => {Draft}
            [projects] => Active_Projects:Project_02
        )
        
    */
    echo "<pre>"; print_r($params); echo "</pre>";

    //for articles, last two for projects
    if($params['wiki_status'] == "{Draft}")         $str = "For EOL Harvesting";
    elseif($params['wiki_status'] == "{Approved}")  $str = "For Review (draft)";
    elseif($params['wiki_status'] == "{Active}")    $str = "Completed Projects";
    elseif($params['wiki_status'] == "{Completed}") $str = "Active Projects";
?>
<div id="accordion_open2">
    <h3>Moved to "<?php echo $str ?>"</h3>
    <div>
    <?php
        self::start_move($params);
    ?>
    </div>
</div>
