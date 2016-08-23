<?php
if($val = @$params['articles']) //semi-colon separated titles
{
    $rek = self::prepare_proj_articles_list($val, $wiki_status);
    $rows = $rek['recs'];
    // echo "<pre>"; print_r($rows); echo "</pre>";
    $data = array('group' => 'articles', 'records' => $rows, 'table_id' => $table_id);
    print self::render_template('article-table', array('data' => $data));
}
?>
