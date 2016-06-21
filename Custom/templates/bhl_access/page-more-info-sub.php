<?php
$title_id = self::get_ItemInfo_using_item_id($Page->ItemID, "PrimaryTitleID");

// echo "<hr>";
// print_r($Page);
// echo "<hr>";
// print_r($params);
// echo "<hr>";

?>
<div id="tabs-1"><!--- Page Summary --->
    <table>
    
    <tr>
        <td>PageID</td>
        <td> : <?php echo self::get_url_by_id("page", $Page->PageID) ?>
        <!--
        <ul id="icons" class="ui-widget ui-helper-clearfix">
            <li>: <?php echo $Page->PageID ?></li>
            <li class="ui-state-default ui-corner-all" title="Search this page">
                <span class="ui-icon ui-icon-search"></span>
                <a href="../bhl_access/index.php?page_id=<?php echo $Page->PageID?>&search_type=pagesearch">Search</a>&nbsp;&nbsp;
            </li>
        </ul>
        -->
        </td>
    </tr>
    
    <tr bgcolor="lightyellow"><td>ItemID</td>
    <td>
    <ul id="icons" class="ui-widget ui-helper-clearfix">
        <li>: <?php echo self::get_url_by_id("item", $Page->ItemID) ?></li>
        <li class="ui-state-default ui-corner-all" title="Search this item">
            <span class="ui-icon ui-icon-search"></span>
            <a onClick="spinner_on()" href="../bhl_access/index.php?item_id=<?php echo $Page->ItemID?>&search_type=itemsearch">Search</a>&nbsp;&nbsp;
        </li>
    </ul>
    </td>
    </tr>
    
    <?php
    
    $title = self::get_TitleInfo_using_title_id($title_id, "FullTitle");
    $copyrightstatus = self::get_ItemInfo_using_item_id($Page->ItemID, 'copyrightstatus');
    $license_url = self::get_ItemInfo_using_item_id($Page->ItemID, 'license url');
    
    $licensor = false;
    if(self::is_copyrightstatus_Digitized_With_Permission($copyrightstatus))
    {
        $licensor = self::get_licensor_for_this_title($title);
        if(!$licensor) self::display_message(array('type' => "error", 'msg' => "Please investigate, licensor not found. OR send this message with the Page ID [$Page->PageID] to <a href=\"mailto:" . DEVELOPER_EMAIL . "\">admin</a>."));
    }
    ?>
    
    <tr bgcolor="lightyellow"><td>CopyrightStatus</td><td>: {<?php echo $copyrightstatus ?>}</td></tr>
    <tr bgcolor="lightyellow"><td>LicenseUrl</td><td>: {<?php echo $license_url ?>}</td></tr>
    
    <?php if($licensor)
    {
        ?><tr bgcolor="lightyellow"><td>Licensor</td><td>: {<?php echo $licensor ?>}</td></tr><?php
    }
    ?>
    
    <tr bgcolor="#F0F8FF"><td>TitleID</td><td>: <?php echo self::get_url_by_id("title", $title_id) ?> &nbsp; {<?php echo $title ?>}</td></tr>
    <tr bgcolor="#F0F8FF"><td>BibliographicLevel</td><td>: {<?php echo self::get_TitleInfo_using_title_id($title_id, "BibliographicLevel") ?>}</td></tr>
    
    <tr><td>Volume</td><td>: <?php echo @$Page->Volume ?></td></tr>
    <tr><td>Issue</td><td>: <?php echo @$Page->Issue ?></td></tr>
    <tr><td>Year</td><td>: <?php echo @$Page->Year ?></td></tr>
    <tr><td>PageUrl</td><td>: <?php echo Functions::format_url(@$Page->PageUrl) ?></td></tr>
    <tr><td>ThumbnailUrl</td><td>: <?php echo Functions::format_url(@$Page->ThumbnailUrl) ?></td></tr>
    <tr><td>FullSizeImageUrl</td><td>: <?php echo Functions::format_url(@$Page->FullSizeImageUrl) ?></td></tr>
    <tr><td>OcrUrl</td><td>: <?php echo Functions::format_url(@$Page->OcrUrl) ?></td></tr>
    <tr valign="top"><td>OcrText</td><td>: <?php echo self::string_or_object(@$Page->OcrText) ?></td></tr>
    
    <?php /* working but workflow changed...
    $pass_title = $Page->PageID;
    // $export_url = "../bhl_access/index.php?page_id=" . $Page->PageID . "&item_id=" . $Page->ItemID . "&title_id=" . $title_id . "&pass_title=" . urlencode($pass_title) . "&search_type=move2wiki"; working but not used anymore...
    if($url_params = self::check_if_this_title_has_wiki($pass_title, "v1"))
    {
        $wiki = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/" . $Page->PageID;
        // self::image_with_text(array("text" => "Wiki already exists for this excerpt.", "src" => "../images/wiki-icon.png", "alt_text" => "View Wiki here", "href" => $wiki)); working script
        self::display_message(array('type' => "highlight", 'msg' => "Wiki already exists for this excerpt. <a href='$wiki'>View Wiki</a>"));
        $submit_text = "Proceed overwrite Wiki page";
        // <br><br><a href="$export_url">Proceed overwrite Wiki page</a>
        if(!count($Page_xml->Names->Name)) self::display_message(array('type' => "highlight", 'msg' => "Original excerpt does not have any taxon associated with it."));
    }
    else
    {
        // <!-- <a href="$export_url">Export this to Wiki</a> -->
        $submit_text = "Export this to Wiki";
        if(!count($Page_xml->Names->Name)) self::display_message(array('type' => "error", 'msg' => "This excerpt does not have any taxon associated with it."));
    }
    if(self::is_in_copyright_OR_all_rights_reserved($copyrightstatus))
    {
        self::display_message(array('type' => "highlight", 'msg' => "This is IN COPYRIGHT or ALL RIGHTS RESERVED. We cannot import text into the wiki."));
    }
    else
    {
        ?>
        <tr><td colspan="2" align="center">
        <?php require_once("subject_menu.php"); ?>
        </td></tr>
        <?php
    }
    */
    ?>
    
    </table>
</div>

<div id="tabs-2"><!--- PagesTypes --->
    <table>
    <!-- <?php print_r(@$Page->PageTypes->PageType)  ?> --> <!-- for debug -->
    <?php $total_page_types = count(@$Page->PageTypes->PageType) ?>
    <tr><td colspan="2">Total: <?php echo (string) $total_page_types ?></td></tr>
    <?php
    if($total_page_types == 1)
    {
        foreach($Page->PageTypes as $PageType)
        {
            ?>
            <tr><td>PageTypeName</td><td>: <?php echo (string) @$PageType->PageTypeName ?></td></tr>
            <?php
        }
    }
    elseif($total_page_types > 1)
    {
        foreach($Page->PageTypes->PageType as $PageType)
        {
            ?>
            <tr><td>PageTypeName</td><td>: <?php echo (string) @$PageType->PageTypeName ?></td></tr>
            <?php
        }
    }
    ?>
    </table>
</div>

<div id="tabs-3"><!--- PageNumbers --->
    <table>
    <!-- <?php print_r(@$Page->PageNumbers->PageNumber)  ?> --> <!-- for debug -->
    <?php $total_page_numbers = count(@$Page->PageNumbers->PageNumber) ?>
    <tr><td colspan="2">Total: <?php echo $total_page_numbers ?></td></tr>
    <?php
    if($total_page_numbers == 1)
    {
        foreach($Page->PageNumbers as $PageNumber)
        {
            ?>
            <tr><td>Prefix</td><td>: <?php echo (string) @$PageNumber->Prefix ?></td></tr>
            <tr><td>Number</td><td>: <?php echo (string) @$PageNumber->Number ?></td></tr>
            <?php
        }
    }
    elseif($total_page_numbers > 1)
    {
        foreach($Page->PageNumbers->PageNumber as $PageNumber)
        {
            ?>
            <tr><td>Prefix</td><td>: <?php echo (string) @$PageNumber->Prefix ?></td></tr>
            <tr><td>Number</td><td>: <?php echo self::string_or_object(@$PageNumber->Number) ?></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <?php
        }
    }
    ?>
    </table>
</div>
