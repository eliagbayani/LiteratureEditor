<?php
// namespace php_active_record;
/* Expects: $params */
$type = $params['radio']; //either 'draft' or 'approved'
if($val = @$params['book_title']) $rows = self::list_titles_by_type($type, $val);
else                              $rows = array();
if($rows)
{
    ?>
    <div id="accordion_open">
        <h3>Articles = <?php echo count($rows) . " : &nbsp;&nbsp; <b><u>\"$val\"<u></b>" ?></h3>
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
