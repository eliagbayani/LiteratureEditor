<?php
$pass_title = $Page->PageID;
if($url_params = self::check_if_this_title_has_wiki($pass_title, "v2"))
{
    $wiki = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/" . $Page->PageID;
    self::display_message(array('type' => "highlight", 'msg' => "Wiki already exists for this excerpt. &nbsp; <a href='$wiki'>View Wiki</a>. &nbsp; Or you can proceed here and overwrite existing wiki."));
    $submit_text = "Proceed overwrite Wiki page";
    if(!count($Page_xml->Names->Name)) self::display_message(array('type' => "highlight", 'msg' => "Original excerpt does not have any taxon associated with it."));
}
else
{
    $submit_text = "Export this to Wiki";
    if(!count($Page_xml->Names->Name)) self::display_message(array('type' => "error", 'msg' => "This excerpt does not have any taxon associated with it."));
}
if(self::is_in_copyright_OR_all_rights_reserved($copyrightstatus))
{
    self::display_message(array('type' => "highlight", 'msg' => "This is IN COPYRIGHT or ALL RIGHTS RESERVED. We cannot import text into the wiki."));
}
else
{
    //require_once("page-editor.php");
}
?>
