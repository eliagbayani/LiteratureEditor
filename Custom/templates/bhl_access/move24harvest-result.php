<?php
// namespace php_active_record;
    /*  Expects: $params
        Array
        (
            [wiki_title] => 19369800 1790b123a8a0209e1500fd5be12842f5
            [search_type] => move24harvest
            [wiki_status] => {Draft}
        )
    */
    // echo "<pre>"; print_r($params); echo "</pre>";
    
    if($params['wiki_status'] == "{Draft}") $str = "For EOL Harvesting";
    else                                    $str = "For Review (drafts)";
    
?>
<div id="accordion_open2">
    <h3>Move to "<?php echo $str ?>"</h3>
    <div>
    <?php
    if($params['token'] = self::get_move_token($params['wiki_title']))
    {
        $arr = self::move_file($params);
        // echo "<pre>222"; print_r($arr); echo "</pre>"; //debug

        if($msg = @$arr['error']['code']) self::display_message(array('type' => "error", 'msg' => $msg));
        if($msg = @$arr['error']['info']) self::display_message(array('type' => "error", 'msg' => $msg));
        
        if($new_title = @$arr['move']['to'])
        {
            $wiki_page = "../../wiki/" . $new_title;
            ?>
            <script type="text/javascript">
            location.href = '<?php echo $wiki_page ?>';
            </script>
            <?php
        }
    }
    else self::display_message(array('type' => "error", 'msg' => "Move failed."));
    ?>
    </div>
</div>

