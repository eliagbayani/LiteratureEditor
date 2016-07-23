<?php
// namespace php_active_record;
    /* 
        Expects:
    */
    
    $total = count(@$xml->Result);
?>

<!--- <h3 class="demoHeaders">Title(s): <?php echo $total ?></h3> --->
<?php if($loop = @$xml->Result)
{
    ?>
    <div id="accordion_open">
        <?php
        $tabs_count = 0;
        foreach($loop as $title)
        {
            $tabs_count++;
            ?>
            <h3>TitleID: <?php echo $title->TitleID ?></h3>
            <div><!-- accordion start -->
                <div id="tabs<?php echo $tabs_count ?>">
                    <ul>
                        <li><a href="#tabs<?php echo $tabs_count ?>-1">Title Summary</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-2">Authors</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-3">Subjects</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-4">Identifiers</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-5">Variants</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-6">Items</a></li>
                        <li><a href="#tabs<?php echo $tabs_count ?>-7">Notes</a></li>
                    </ul>
                    <div id="tabs<?php echo $tabs_count ?>-1">
                    <table>
                    <tr><td>TitleID</td>                <td>: <?php echo self::get_url_by_id("title", $title->TitleID) ?></td></tr>
                    <tr><td>BibliographicLevel</td>     <td>: <?php echo $title->BibliographicLevel ?></td></tr>
                    <tr><td>FullTitle</td>              <td>: <?php echo $title->FullTitle ?></td></tr>
                    <tr><td>ShortTitle</td>             <td>: <?php echo $title->ShortTitle ?></td></tr>
                    <tr><td>SortTitle</td>              <td>: <?php echo $title->SortTitle ?></td></tr>
                    <tr><td>PartNumber</td>             <td>: <?php echo $title->PartNumber ?></td></tr>
                    <tr><td>PartName</td>               <td>: <?php echo $title->PartName ?></td></tr>
                    <tr><td>CallNumber</td>             <td>: <?php echo $title->CallNumber ?></td></tr>
                    <tr><td>Edition</td>                <td>: <?php echo $title->Edition ?></td></tr>
                    <tr><td>PublisherPlace</td>         <td>: <?php echo $title->PublisherPlace ?></td></tr>
                    <tr><td>PublisherName</td>          <td>: <?php echo $title->PublisherName ?></td></tr>
                    <tr><td>PublicationDate</td>        <td>: <?php echo $title->PublicationDate ?></td></tr>
                    <tr><td>PublicationFrequency</td>   <td>: <?php echo $title->PublicationFrequency ?></td></tr>
                    <tr><td>TitleUrl</td>               <td>: <?php echo Functions::format_url($title->TitleUrl) ?></td></tr>
                    </table>
                    </div>
                    
                    <div id="tabs<?php echo $tabs_count ?>-2">
                        Author(s): <?php echo count($title->Authors->Creator) ?><br>
                        <table>
                        <?php 
                        foreach($title->Authors->Creator as $Creator)
                        {
                            ?>
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
                                <tr><td>&nbsp;</td></tr>
                            <?php
                        }
                        ?>
                        </table>
                    </div>

                    <div id="tabs<?php echo $tabs_count ?>-3">
                        Subject(s): <?php echo count($title->Subjects->Subject) ?><br>
                        <table>
                        <?php 
                        foreach($title->Subjects->Subject as $Subject)
                        {
                            ?>
                                <tr><td>SubjectText</td><td>: <?php echo $Subject->SubjectText ?></td></tr>
                            <?php
                        }
                        ?>
                        </table>
                    </div>

                    <div id="tabs<?php echo $tabs_count ?>-4">
                        Identifier(s): <?php echo count($title->Identifiers->TitleIdentifier) ?><br>
                        <table>
                            <tr>
                                <th>IdentifierName</th>
                                <th>IdentifierValue</th>
                            </tr>
                        <?php 
                        foreach($title->Identifiers->TitleIdentifier as $TitleIdentifier)
                        {
                            ?>
                                <tr>
                                    <td><?php echo $TitleIdentifier->IdentifierName ?></td>
                                    <td><?php echo $TitleIdentifier->IdentifierValue ?></td>
                                </tr>
                            <?php
                        }
                        ?>
                        </table>
                    </div>

                    <div id="tabs<?php echo $tabs_count ?>-5">
                        Variant(s): <?php echo count($title->Variants->TitleVariant) ?><br>
                        <?php if(count($title->Variants->TitleVariant))
                        {
                            ?>
                            <table>
                                <tr align="left">
                                    <th>Variant Type</th>
                                    <th>Title</th>
                                </tr>
                            <?php 
                            foreach($title->Variants->TitleVariant as $TitleVariant)
                            {
                                ?>
                                    <tr>
                                        <td><?php echo $TitleVariant->TitleVariantTypeName ?></td>
                                        <td><?php echo $TitleVariant->Title ?></td>
                                    </tr>
                                <?php
                            }
                            ?>
                            </table>                            
                            <?php
                        }
                        ?>
                    </div>

                    <div id="tabs<?php echo $tabs_count ?>-6">
                        Item(s): <?php echo count($title->Items->Item) ?><br>
                        <table>
                            <tr align="left">
                                <th></th>
                                <th>ItemID</th>
                                <th>PrimaryTitleID</th>
                                <th>ThumbnailPageID</th>
                                <th>Source</th>
                                <th>SourceIdentifier</th>
                                <th>Volume</th>
                                <th>Year</th>
                                <th>Contributor</th>
                                <th>Sponsor</th>
                                <th>Language</th>
                                <th>Rights</th>
                                <th>DueDiligence</th>
                                <th>CopyrightStatus</th>
                                <th>CopyrightRegion</th>
                                <th>URLs</th>
                            </tr>
                        <?php 
                        foreach($title->Items->Item as $Item)
                        {
                            ?>
                                <tr>
                                    <td><a onClick="spinner_on()" title="More info on this item" href="../bhl_access/index.php?item_id=<?php echo $Item->ItemID?>&search_type=itemsearch"><span class="ui-icon ui-icon-search"></span></a></td>

                                    <!--- <td><a href="<?php echo $Item->ItemUrl ?>"><?php echo $Item->ItemID ?></a></td> --->
                                    <td><?php echo self::get_url_by_id("item", $Item->ItemID) ?></td>

                                    <!--- <td><a href="<?php echo $Item->TitleUrl ?>"><?php echo $Item->PrimaryTitleID ?></a></td> --->
                                    <td><?php echo self::get_url_by_id("title", $Item->PrimaryTitleID) ?></td>
                                    
                                    <!--- <td><a href="<?php echo $Item->ItemThumbUrl ?>"><?php echo $Item->ThumbnailPageID ?></a></td> --->
                                    <td><?php echo self::get_url_by_id("pagethumb", $Item->ThumbnailPageID) ?></td>
                                    
                                    <td><?php echo $Item->Source ?></td>
                                    <td><?php echo $Item->SourceIdentifier ?></td>
                                    <td><?php echo $Item->Volume ?></td>
                                    <td><?php echo $Item->Year ?></td>
                                    <td><?php echo $Item->Contributor ?></td>
                                    <td><?php echo $Item->Sponsor ?></td>
                                    <td><?php echo $Item->Language ?></td>
                                    <td><?php echo $Item->Rights ?></td>
                                    <td><?php echo $Item->DueDiligence ?></td>
                                    <td><?php echo $Item->CopyrightStatus ?></td>
                                    <td><?php echo $Item->CopyrightRegion ?></td>
                                    <td>
                                        LicenseUrl: <?php echo $Item->LicenseUrl ?><br>
                                        ExternalUrl: <?php echo $Item->ExternalUrl ?>
                                    </td>
                                </tr>
                            <?php
                        }
                        ?>
                        </table>
                    </div>

                    <div id="tabs<?php echo $tabs_count ?>-7">
                        Note(s): <?php echo count($title->Notes->TitleNote) ?><br>
                        <?php if(count($title->Notes->TitleNote))
                        {
                            ?>
                            <table>
                                <tr align="left">
                                    <th>NoteText</th>
                                    <th>NoteSequence</th>
                                    <th>NoteTypeName</th>
                                </tr>
                            <?php 
                            foreach($title->Notes->TitleNote as $TitleNote)
                            {
                                ?>
                                    <tr>
                                        <td><?php echo $TitleNote->NoteText ?></td>
                                        <td><?php echo $TitleNote->NoteSequence ?></td>
                                        <td><?php echo $TitleNote->NoteTypeName ?></td>
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
