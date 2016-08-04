<?php
$pass_title = $Page->PageID;
// if($url_params = self::check_if_this_title_has_wiki($pass_title, "v2")) //very old implementation
$cont_editor = true;
// if(!isset($params['continue']))
if(count(array_keys($params)) == 2)
{
    if($titles = self::check_if_this_title_has_wiki_v2($pass_title, "5000|0")) //2nd param are pipe-delimited namespaces
    {
        $mwiki = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/";
        $wiki = $mwiki . $Page->PageID;
        // echo "<pre>"; print_r($titles);echo "</pre>";
        // echo "<pre>"; print_r($params);echo "</pre>";
        if($titles)
        {
            $cont_editor = false;
            if(count($titles > 1)) $str = "excerpts have";
            else                   $str = "excerpt has";
            self::display_message(array('type' => "error", 'msg' => "Page $Page->PageID has already been processed. &nbsp;The following $str been created:"));
            foreach($titles as $r)
            {
                //get subject from wiki
                if($info = self::get_wiki_text($r->title))
                {
                    $wiki_text = $info['content'];
                    $p = self::get_void_part($wiki_text);
                    $post = self::page_status($r->title);
                    echo "<br>" . self::get_subject_desc(@$p['subject_type']) . " - <a href='index.php?search_type=wiki2php&wiki_title=$r->title&overwrite=1'>view</a> &nbsp; <i>$post</i><br>";
                }
            }
            echo "<p><br><a href='index.php?search_type=pagesearch&page_id=$Page->PageID&continue'>Create new excerpt for this page</a>";
            //Overview › Brief Summary - view
            //Ecology › Associations - view
        }
        $submit_text = "Proceed overwrite Wiki page";
    }
    else $submit_text = "Export this to Wiki";
}
if(!count($Page_xml->Names->Name)) self::display_message(array('type' => "highlight", 'msg' => "This excerpt does not have any taxon associated with it."));
if(self::is_in_copyright_OR_all_rights_reserved($copyrightstatus))
{
    self::display_message(array('type' => "highlight", 'msg' => "This is IN COPYRIGHT or ALL RIGHTS RESERVED. We cannot import text into the wiki."));
}
else
{
    //require_once("page-editor.php");
}
?>

