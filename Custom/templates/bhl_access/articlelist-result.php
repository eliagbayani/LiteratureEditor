<?php
// namespace php_active_record;
/* Expects: $params */
$type = $params['radio']; //either 'draft' or 'approved'
if($val = @$params['book_title'])
{
    $rek = self::list_titles_by_type($type, $val);
    // echo "<pre>"; print_r($rek); echo "</pre>";
    $rows = $rek['recs'];
}
else $rows = array();
if($rows)
{
    ?>
    <div id="accordion_open">
        <h3>Articles = <?php echo count($rows) . " : &nbsp;&nbsp; <b><u>\"$val\"</u></b>" ?></h3>
        <div>
            <?php 
            // echo "<pre>"; print_r($rows); echo "</pre>";
            if($rows) 
            {
                $data = array('group' => 'articles', 'records' => $rows);
                print self::render_template('article-table', array('data' => $data));
            }
            ?>
        </div>
    </div>
    <?php
}
?>
