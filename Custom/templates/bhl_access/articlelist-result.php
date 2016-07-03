<?php
// namespace php_active_record;
    /* Expects: $type */
    $rows = self::list_titles_by_type($type);
?>
<div id="accordion_open">
    <h3>Titles = <?php echo count($rows) ?></h3>
    <div>
        <?php 
        // echo "$type";
        // echo "<pre>"; print_r($rows); echo "</pre>";
        if($rows)
        {
            print self::render_template('article-table', array('rows' => @$rows));
        }
        ?>
    </div>
</div>

