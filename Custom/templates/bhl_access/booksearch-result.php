<?php
// namespace php_active_record;
    /* 
        Expects:
    */
    $total = count(@$xml->Result->Title);
?>
<b>Book(s): <?php echo $total ?></b>
<?php if($loop = @$xml->Result->Title)
{
    ?>
    <div id="accordion">
        <?php
        $tabs_count = 0;
        foreach($loop as $Title)
        {
            $tabs_count++;
            ?>
            <h3>FullTitle: <?php echo $Title->FullTitle ?></h3>
            <div><!-- accordion start -->
                <div id="tabs<?php echo $tabs_count ?>">
                    <ul>
                        <li><a href="#tabs<?php echo $tabs_count ?>-1">Book Summary</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-2">Authors</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-3">Items</a></li>
                    </ul>
                    
                    <div id="tabs<?php echo $tabs_count ?>-1">
                        <table>
                        <tr><td>TitleID</td>
                        <td>
                        <ul id="icons" class="ui-widget ui-helper-clearfix">
                            <li>: <?php echo self::get_url_by_id("title", $Title->TitleID) ?></li>
                            <li class="ui-state-default ui-corner-all" title="More info on this book">
                                <span class="ui-icon ui-icon-search"></span>
                                <a onClick="spinner_on()" href="../bhl_access/index.php?title_id=<?php echo $Title->TitleID?>&search_type=titlesearch">Search</a>&nbsp;&nbsp;
                            </li>
                        </ul>
                        </td></tr>
                        <tr><td>BibliographicLevel</td>    <td>: <?php echo $Title->BibliographicLevel ?></td></tr>
                        <tr><td>PartNumber</td>            <td>: <?php echo $Title->PartNumber ?></td></tr>
                        <tr><td>PartName</td>              <td>: <?php echo $Title->PartName ?></td></tr>
                        <tr><td>Edition</td>               <td>: <?php echo $Title->Edition ?></td></tr>
                        <tr><td>PublisherPlace</td>        <td>: <?php echo $Title->PublisherPlace ?></td></tr>
                        <tr><td>PublisherName</td>         <td>: <?php echo $Title->PublisherName ?></td></tr>
                        <tr><td>PublicationDate</td>       <td>: <?php echo $Title->PublicationDate ?></td></tr>
                        <tr><td>TitleUrl</td>              <td>: <?php echo Functions::format_url($Title->TitleUrl) ?></td></tr>
                        </table>
                    </div>
                    
                    <div id="tabs<?php echo $tabs_count ?>-2">
                        Creator(s): <?php echo count($Title->Authors->Creator) ?><br>
                        <?php 
                        foreach($Title->Authors->Creator as $Creator)
                        {
                            ?>
                                <table>
                                    <tr><td>CreatorID</td>      <td>: <?php echo self::get_url_by_id("creator", $Creator->CreatorID) ?></td></tr>
                                    <tr><td>Name</td>           <td>: <?php echo $Creator->Name ?></td></tr>
                                    <tr><td>Role</td>           <td>: <?php echo $Creator->Role ?></td></tr>
                                    <tr><td>Numeration</td>     <td>: <?php echo $Creator->Numeration ?></td></tr>
                                    <tr><td>Unit</td>           <td>: <?php echo $Creator->Unit ?></td></tr>
                                    <tr><td>Title</td>          <td>: <?php echo $Creator->Title ?></td></tr>
                                    <tr><td>Location</td>       <td>: <?php echo $Creator->Location ?></td></tr>
                                    <tr><td>FullerForm</td>     <td>: <?php echo $Creator->FullerForm ?></td></tr>
                                    <tr><td>Relationship</td>   <td>: <?php echo $Creator->Relationship ?></td></tr>
                                    <tr><td>TitleOfWork</td>    <td>: <?php echo $Creator->TitleOfWork ?></td></tr>
                                    <tr><td>Dates</td>          <td>: <?php echo $Creator->Dates ?></td></tr>
                                </table>
                                <br>
                            <?php
                        }
                        ?>
                    </div>
                    <div id="tabs<?php echo $tabs_count ?>-3">
                        Item(s): <?php echo count($Title->Items->Item) ?><br>
                        <?php
                        foreach($Title->Items->Item as $Item)
                        {
                            ?>
                                <table>
                                    <tr><td>ItemID</td>
                                    <td>
                                    <ul id="icons" class="ui-widget ui-helper-clearfix">
                                        <li>: <?php echo self::get_url_by_id("item", $Item->ItemID) ?></li>
                                        <li class="ui-state-default ui-corner-all" title=".ui-icon-search">
                                            <span class="ui-icon ui-icon-search"></span>
                                            <a onClick="spinner_on()" href="../bhl_access/index.php?item_id=<?php echo $Item->ItemID?>&search_type=itemsearch">Search</a>&nbsp;&nbsp;
                                        </li>
                                    </ul>
                                    </td></tr>
                                    <tr><td>PrimaryTitleID</td>     <td>: <?php echo self::get_url_by_id("title", $Item->PrimaryTitleID) ?></td></tr>
                                    <tr><td>ThumbnailPageID</td>    <td>: <?php echo self::get_url_by_id("pagethumb", $Item->ThumbnailPageID) ?></td></tr>
                                    <tr><td>Volume</td>             <td>: <?php echo $Item->Volume ?></td></tr>
                                    <tr><td>Contributor</td>        <td>: <?php echo $Item->Contributor ?></td></tr>
                                    <tr><td>ItemUrl</td>            <td>: <?php echo Functions::format_url($Item->ItemUrl) ?></td></tr>
                                </table>
                                <br>
                                <?php
                                $collections_count = count($Item->Collections->Collection);
                                ?>
                                Collection(s): <?php echo $collections_count ?><br>
                                <?php
                                if($collections_count)
                                {
                                    foreach($Item->Collections->Collection as $Collection)
                                    {
                                        ?>
                                            <table>
                                                <tr><td>CollectionID</td>   <td>: <?php echo $Collection->CollectionID ?></td></tr>
                                                <tr><td>CollectionName</td> <td>: <?php echo $Collection->CollectionName ?></td></tr>
                                                <tr><td>CanContainTitles</td> <td>: <?php echo $Collection->CanContainTitles ?></td></tr>
                                                <tr><td>CanContainItems</td> <td>: <?php echo $Collection->CanContainItems ?></td></tr>
                                            </table>
                                            <br>
                                        <?php
                                    }
                                }
                                ?>
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
    if($total > 5)
    {
        echo "<script>";
        for ($i = 6; $i <= $total; $i++) 
        {
            echo '$( "#tabs' . $i . '" ).tabs();';
        }
        echo "</script>";
    }
}
?>
