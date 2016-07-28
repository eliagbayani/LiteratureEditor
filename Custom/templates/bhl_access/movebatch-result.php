<?php
// namespace php_active_record;
/*  Expects: $params
Array
(
    [search_type] => movebatch
    [book_title] => Elephant haunts: being a sportsman's narrative of the search for Doctor Livingstone, with scenes of elephant, buffalo, and hippopotamus hunting.
    [wiki_status] => {Draft} or {Approved}
)
*/
// echo "<pre>"; print_r($params); echo "</pre>";
if($params['wiki_status'] == "{Draft}") $str = "For EOL Harvesting";
else                                    $str = "For Review (draft)";

$type = Functions::get_string_between("{", "}", strtolower($params['wiki_status']));

$recs = self::list_titles_by_type($type, $params['book_title']);
// echo "<pre>"; print_r($recs); echo "</pre>";

foreach($recs as $rec)
{
    /* 1 rec
    Array
            (
                [content] =>
                [timestamp] => 2016-07-27T03:34:02Z
                [compiler] => Eli E. Agbayani
                [subject_type] => Overview › Comprehensive Description › General Description
                [title] => 48795816 11def2bad0f05f46c21d190853b2f2af
                [header_title] => Wholesale price list of garden, flower, agricultural, grass and herb seeds for 1901 : to dealers only / 1901
            )
    */

    $p = array();
    $p['wiki_title']  = $rec['title'];
    $p['wiki_status'] = $params['wiki_status'];
    if($p['token'] = self::get_move_token($p['wiki_title']))
    {
        $arr = self::move_file($p);
        // echo "<pre>"; print_r($arr); echo "</pre>"; //debug

        if($msg = @$arr['error']['code']) self::display_message(array('type' => "error", 'msg' => $msg));
        if($msg = @$arr['error']['info']) self::display_message(array('type' => "error", 'msg' => $msg));

        if($new_title = @$arr['move']['to'])
        {
            $wiki_page = "../../wiki/" . $new_title;
        }
    }
    else self::display_message(array('type' => "error", 'msg' => "Move failed. Token creation failed."));
}
?>
<div id="accordion_open2">
    <h3>Move to "<?php echo $str ?>"</h3>
    <div>
    <?php
    $total = count($recs);
    if($total > 1) $words = array("articles", "were");
    else           $words = array("article", "was");
    self::display_message(array('type' => "highlight", 'msg' => $total . " " . $words[0] . " from title <b>'" . $params['book_title'] . "'</b> " . $words[1] . " moved to '$str'."));
    
    if($type == 'approved') $type = 'draft';
    else                    $type = 'approved';
    
    echo "<br><a href=\"index.php?search_type=articlelist&radio=$type&book_title=" . urlencode($params['book_title']) . "\">See new list</a>";
    ?>
    </div>
</div>
