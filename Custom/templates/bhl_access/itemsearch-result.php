<?php
// namespace php_active_record;
    /* 
        Expects:
    */
    
    $total = count(@$xml->Result);
?>

<!--- <h3 class="demoHeaders">Item(s): <?php echo $total ?></h3> --->
<?php if($loop = @$xml->Result)
{
    ?>
    <div id="accordion_open">
        <?php
        $tabs_count = 0;
        foreach($loop as $item)
        {
            $tabs_count++;
            ?>
            <h3>ItemID: <?php echo $item->ItemID ?></h3>
            <div><!-- accordion start -->
                <div id="tabs<?php echo $tabs_count ?>">
                    <ul>
                        <li><a href="#tabs<?php echo $tabs_count ?>-1">Item Summary</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-2">Pages</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-3">Parts</a></li>
                    </ul>
                    
                    <div id="tabs<?php echo $tabs_count ?>-1">
                    <table>
                    <tr><td>ItemID</td>             <td>: <?php echo self::get_url_by_id("item", $item->ItemID) ?></td></tr>
                    <tr><td>PrimaryTitleID</td>     <td>
                    <ul id="icons" class="ui-widget ui-helper-clearfix">
                        <li>: <?php echo self::get_url_by_id("title", $item->PrimaryTitleID) ?></li>
                        <li class="ui-state-default ui-corner-all" title="Search this title">
                            <span class="ui-icon ui-icon-search"></span>
                            <a onClick="spinner_on()" href="../bhl_access/index.php?title_id=<?php echo $item->PrimaryTitleID ?>&search_type=titlesearch">Search</a>&nbsp;&nbsp;
                        </li>
                        <li>&nbsp; {<?php echo self::get_TitleInfo_using_title_id($item->PrimaryTitleID, "FullTitle") ?>}</li>
                    </ul>
                    
                    </td></tr>
                    <tr><td>ThumbnailPageID</td>    <td>: <?php echo self::get_url_by_id("pagethumb", $item->ThumbnailPageID) ?></td></tr>
                    <tr><td>Source</td>             <td>: <?php echo $item->Source ?></td></tr>
                    <tr><td>SourceIdentifier</td>   <td>: <?php echo $item->SourceIdentifier ?></td></tr>
                    <tr><td>Volume</td>             <td>: <?php echo $item->Volume ?></td></tr>
                    <tr><td>Year</td>               <td>: <?php echo $item->Year ?></td></tr>
                    <tr><td>Contributor</td>        <td>: <?php echo $item->Contributor ?></td></tr>
                    <tr><td>Sponsor</td>            <td>: <?php echo $item->Sponsor ?></td></tr>
                    <tr><td>Language</td>           <td>: <?php echo $item->Language ?></td></tr>
                    <tr><td>LicenseUrl</td>         <td>: <?php echo Functions::format_url($item->LicenseUrl) ?></td></tr>
                    <tr><td>Rights</td>             <td>: <?php echo $item->Rights ?></td></tr>
                    <tr><td>DueDiligence</td>       <td>: <?php echo $item->DueDiligence ?></td></tr>
                    <tr><td>CopyrightStatus</td>    <td>: <?php echo $item->CopyrightStatus ?></td></tr>
                    <tr><td>CopyrightRegion</td>    <td>: <?php echo $item->CopyrightRegion ?></td></tr>
                    <tr><td>ExternalUrl</td>        <td>: <?php echo Functions::format_url($item->ExternalUrl) ?></td></tr>
                    <tr><td>ItemUrl</td>            <td>: <?php echo Functions::format_url($item->ItemUrl) ?></td></tr>
                    <tr><td>TitleUrl</td>           <td>: <?php echo Functions::format_url($item->TitleUrl) ?></td></tr>
                    <tr><td>ItemThumbUrl</td>       <td>: <?php echo Functions::format_url($item->ItemThumbUrl) ?></td></tr>
                    </table>
                    </div>
                    
                    <div id="tabs<?php echo $tabs_count ?>-2">
                        Page(s): <?php echo count($item->Pages->Page) ?><br>
                        <table>
                            <tr align="left">
                                <th width="10"></th>
                                <th>PageID</th>
                                <!--- <th>ItemID</th> --->
                                <th>Volume</th>
                                <th>Year</th>
                                <!--- <th>OcrText</th> working but too long to load --->
                            </tr>
                        <?php 
                        foreach($item->Pages->Page as $Page)
                        {
                            $pagex = json_encode($Page);
                            ?>
                                    <tr valign="top">
                                        <!-- working but commented now since long URI is not allowed
                                        <td><a title="More info on this page" href="index.php?page_more_info=<?php echo urlencode($pagex)?>"><span class="ui-icon ui-icon-search"></span></a></td>
                                        -->
                                        <td><a onClick="spinner_on()" title="More info on this page" href="../bhl_access/index.php?page_id=<?php echo $Page->PageID?>&search_type=pagesearch"><span class="ui-icon ui-icon-search"></span></a></td>
                                        <td><?php echo self::get_url_by_id("page", $Page->PageID) ?></td>
                                        <!--- <td><?php echo self::get_url_by_id("item", $Page->ItemID) ?></td> --->
                                        <td><?php echo $Page->Volume ?></td>
                                        <td><?php echo $Page->Year ?></td>
                                        <!--- <td valign="top"><?php echo $Page->OcrText ?> --->
                                        </td>
                                    </tr>
                            <?php
                        }
                        ?>
                        </table>
                    </div>

                    
                    <div id="tabs<?php echo $tabs_count ?>-3">
                        <?php $total_parts = count($item->Parts->Part) ?>
                        Part(s): <?php echo $total_parts ?><br>
                        
                        <?php if($total_parts)
                        {
                            ?>
                            <table>
                                <tr align="left">
                                    <th></th>
                                    <th>PartID</th>
                                    <th>ItemID</th>
                                    <th>StartPageID</th>
                                    <th>PageRange</th>
                                    <th>Contributor</th>
                                    <th>GenreName</th>
                                    <th>Title</th>
                                    <th>ContainerTitle</th>
                                    <th>Date</th>
                                    <th>PartUrl</th>
                                </tr>
                            <?php 
                            foreach($item->Parts->Part as $Part)
                            {
                                // $partx = utf8_encode(json_encode($Part, JSON_ERROR_UTF8));
                                // $partx = utf8_encode(json_encode($Part, JSON_ERROR_CTRL_CHAR));
                                // $partx = utf8_encode(json_encode($Part, JSON_FORCE_OBJECT));
                                $partx = utf8_encode(json_encode($Part));
                                ?>
                                        <tr valign="top">
                                            <td><a title="More info on this part" href="index.php?part_more_info=<?php echo urlencode($partx) ?>"><span class="ui-icon ui-icon-search" onClick="spinner_on()"></span></a></td>
                                            <td><?php echo $Part->PartID ?></td>
                                            <td><?php echo $Part->ItemID ?></td>
                                            <td><?php echo $Part->StartPageID ?></td>
                                            <td><?php echo $Part->PageRange ?></td>
                                            <td><?php echo $Part->Contributor ?></td>
                                            <td><?php echo $Part->GenreName ?></td>
                                            
                                            <!--
                                            <td valign="top">
                                            <ul id="icons" class="ui-widget ui-helper-clearfix">
                                                <li class="ui-state-default ui-corner-all" title=".ui-icon-info">
                                                    <span class="ui-icon ui-icon-search"></span>
                                                    <a href="index.php?part_more_info=<?php echo urlencode($partx) ?>">More info</a>&nbsp;&nbsp;
                                                </li>
                                                <?php echo $Part->Title ?>
                                            </ul>
                                            </td>
                                            -->

                                            <td><?php echo $Part->Title ?></td>
                                            <td><?php echo $Part->ContainerTitle ?></td>
                                            <td><?php echo $Part->Date ?></td>
                                            <td><?php echo Functions::format_url($Part->PartUrl) ?></td>
                                        </tr>
                                <?php
                            }
                            ?>
                            </table>
                            <?php
                        }
                        ?>
                    </div>
                    
                
                </div><!-- tabs end -->
            </div><!-- accordion end -->
            <?php
        }
        ?>
    </div>
    
    <?php
}
?>
