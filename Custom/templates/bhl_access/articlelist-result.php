<?php
// namespace php_active_record;
    /* Expects: $params */
    if($val = @$params['book_title']) $rows = self::list_titles_by_type($params['radio'], $val);
    else $rows = array();

if($rows)
{
    ?>
    <div id="accordion_open">
        <h3>Articles = <?php echo count($rows) ?></h3>
        <div>
            <?php 
            // echo "<pre>"; print_r($rows); echo "</pre>";
            if($rows) print self::render_template('article-table', array('rows' => @$rows));
            ?>
        </div>
    </div>
    <?php
}
?>

