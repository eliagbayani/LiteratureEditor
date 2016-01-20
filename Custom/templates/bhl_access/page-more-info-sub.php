<?php
$title_id = self::get_title_id_using_item_id($Page->ItemID);

// echo "<hr>";
// print_r($Page);
// echo "<hr>";
// print_r($params);
// echo "<hr>";

?>
<div id="tabs-1">
    <table>
    
    <tr>
        <td>PageID</td>
        <td> : <?php echo $Page->PageID ?>
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
        <li>: <?php echo $Page->ItemID ?></li>
        <li class="ui-state-default ui-corner-all" title="Search this item">
            <span class="ui-icon ui-icon-search"></span>
            <a href="../bhl_access/index.php?item_id=<?php echo $Page->ItemID?>&search_type=itemsearch">Search</a>&nbsp;&nbsp;
        </li>
    </ul>
    </td>
    </tr>
    
    <?php
    $title = self::get_title_using_title_id($title_id);
    $copyrightstatus = self::get_CopyrightStatus_using_item_id($Page->ItemID, $title);
    $licensor = false;
    if(self::is_copyrightstatus_Digitized_With_Permission($copyrightstatus))
    {
        $licensor = self::get_licensor_for_this_title($title);
    }
    ?>
    
    <tr bgcolor="lightyellow"><td>CopyrightStatus</td>
    <td>: {<?php echo $copyrightstatus ?>}</td></tr>
    
    <?php if($licensor)
    {
        ?>
        <tr bgcolor="lightyellow"><td>Licensor</td>
        <td>: {<?php echo $licensor ?>}</td></tr>
        <?php
    }
    ?>
    
    
    <tr><td>TitleID</td>
    <td>: <?php echo $title_id ?> &nbsp; {<?php echo $title ?>}
    </td>
    </tr>
    
    
    <tr><td>Volume</td><td>: <?php echo @$Page->Volume ?></td></tr>
    <tr><td>Issue</td><td>: <?php echo @$Page->Issue ?></td></tr>
    <tr><td>Year</td><td>: <?php echo @$Page->Year ?></td></tr>
    <tr><td>PageUrl</td><td>: <?php echo Functions::format_url(@$Page->PageUrl) ?></td></tr>
    <tr><td>ThumbnailUrl</td><td>: <?php echo Functions::format_url(@$Page->ThumbnailUrl) ?></td></tr>
    <tr><td>FullSizeImageUrl</td><td>: <?php echo Functions::format_url(@$Page->FullSizeImageUrl) ?></td></tr>
    <tr><td>OcrUrl</td><td>: <?php echo Functions::format_url(@$Page->OcrUrl) ?></td></tr>
    <tr valign="top"><td>OcrText</td><td>: <?php echo @$Page->OcrText ?></td></tr>
    <tr><td colspan="2" align="center">
    
    <?php
    // $title = self::get_title_using_title_id($title_id); -- no longer needed...
    $pass_title = $Page->PageID;
    $export_url = "../bhl_access/index.php?page_id=" . $Page->PageID . "&item_id=" . $Page->ItemID . "&title_id=" . $title_id . "&pass_title=" . urlencode($pass_title) . "&search_type=move2wiki";

    
    if(self::check_if_this_title_has_wiki($pass_title))
    {
        $wiki = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/" . $Page->PageID;
        
        self::image_with_text(array("text" => "Wiki already exists for this excerpt.", "src" => "../images/wiki-icon.png", "alt_text" => "View Wiki here", "href" => $wiki));
        
        $submit_text = "Proceed overwrite Wiki page";
        ?>
        <!--
        <br><br><a href="<?php echo $export_url?>">Proceed overwrite Wiki page</a>
        -->
        <?php
    }
    else
    {
        ?>
        <!--
        <a href="<?php echo $export_url?>">Export this to Wiki</a>
        -->
        <?php
        $submit_text = "Export this to Wiki";
        
    }
    
    require_once("subject_menu.php");
    
    
    ?>
    
    </td></tr>
    </table>
</div>

<div id="tabs-2">
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

<div id="tabs-3">
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
            <tr><td>Number</td><td>: 
            <?php 
            echo self::string_or_object(@$PageNumber->Number);
            ?></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <?php
        }
    }
    ?>
    </table>
</div>
