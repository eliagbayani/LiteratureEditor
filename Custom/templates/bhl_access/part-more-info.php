<?php
$Part = json_decode($arr, true); //converts it to array() instead of object
// $Part = json_decode($arr);

// echo "<pre>"; print_r($Part); echo "</pre>"; //debug

?>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Part Summary</a></li>
        <li><a href="#tabs-2">Authors</a></li>
    </ul>
    <div id="tabs-1">
        <table>
        <tr><td>PartUrl</td><td>:               <?php echo Functions::format_url(self::check_arr(@$Part['PartUrl'])) ?></td></tr>
        <tr><td>PartID</td><td>:                <?php echo self::check_arr(@$Part['PartID']) ?></td></tr>
        <tr><td>ItemID</td><td>:                <?php echo self::check_arr(@$Part['ItemID']) ?></td></tr>
        <tr><td>StartPageID</td><td>:           <?php echo self::check_arr(@$Part['StartPageID']) ?></td></tr>
        <tr><td>SequenceOrder</td><td>:         <?php echo self::check_arr(@$Part['SequenceOrder']) ?></td></tr>
        <tr><td>Contributor</td><td>:           <?php echo self::check_arr(@$Part['Contributor']) ?></td></tr>
        <tr><td>ContributorID</td><td>:         <?php echo self::check_arr(@$Part['ContributorID']) ?></td></tr>
        <tr><td>GenreName</td><td>:             <?php echo self::check_arr(@$Part['GenreName']) ?></td></tr>
        <tr><td>Title</td><td>:                 <?php echo self::check_arr(@$Part['Title']) ?></td></tr>
        <tr><td>TranslatedTitle</td><td>:       <?php echo self::check_arr(@$Part['TranslatedTitle']) ?></td></tr>
        <tr><td>ContainerTitle</td><td>:        <?php echo self::check_arr(@$Part['ContainerTitle']) ?></td></tr>
        <tr><td>PublicationDetails</td><td>:    <?php echo self::check_arr(@$Part['PublicationDetails']) ?></td></tr>
        <tr><td>PublisherName</td><td>:         <?php echo self::check_arr(@$Part['PublisherName']) ?></td></tr>
        <tr><td>PublisherPlace</td><td>:        <?php echo self::check_arr(@$Part['PublisherPlace']) ?></td></tr>
        <tr><td>Notes</td><td>:                 <?php echo self::check_arr(@$Part['Notes']) ?></td></tr>
        <tr><td>Volume</td><td>:                <?php echo self::check_arr(@$Part['Volume']) ?></td></tr>
        <tr><td>Series</td><td>:                <?php echo self::check_arr(@$Part['Series']) ?></td></tr>
        <tr><td>Issue</td><td>:                 <?php echo self::check_arr(@$Part['Issue']) ?></td></tr>
        <tr><td>Date</td><td>:                  <?php echo self::check_arr(@$Part['Date']) ?></td></tr>
        <tr><td>PageRange</td><td>:             <?php echo self::check_arr(@$Part['PageRange']) ?></td></tr>
        <tr><td>StartPageNumber</td><td>:       <?php echo self::check_arr(@$Part['StartPageNumber']) ?></td></tr>
        <tr><td>EndPageNumber</td><td>:         <?php echo self::check_arr(@$Part['EndPageNumber']) ?></td></tr>
        <tr><td>Language</td><td>:              <?php echo self::check_arr(@$Part['Language']) ?></td></tr>
        <tr><td>ExternalUrl</td><td>:           <?php echo Functions::format_url(self::check_arr(@$Part['ExternalUrl'])) ?></td></tr>
        <tr><td>DownloadUrl</td><td>:           <?php echo Functions::format_url(self::check_arr(@$Part['DownloadUrl'])) ?></td></tr>
        <tr><td>RightsStatus</td><td>:          <?php echo self::check_arr(@$Part['RightsStatus']) ?></td></tr>
        <tr><td>RightsStatement</td><td>:       <?php echo self::check_arr(@$Part['RightsStatement']) ?></td></tr>
        <tr><td>LicenseName</td><td>:           <?php echo self::check_arr(@$Part['LicenseName']) ?></td></tr>
        <tr><td>LicenseUrl</td><td>:            <?php echo Functions::format_url(self::check_arr(@$Part['LicenseUrl'])) ?></td></tr>
        <tr><td>Doi</td><td>:                   <?php echo self::check_arr(@$Part['Doi']) ?></td></tr>
        </table>
    </div>

    <div id="tabs-2">
        <table>
        <?php //print_r(@$Part['Authors']['Creator'])  //debug ?>
        
        <?php if(isset($Part['Authors']['Creator'][0]))
        {
            $total_authors = count(@$Part['Authors']['Creator']);
            ?>
            <tr><td colspan="2">Total: <?php echo $total_authors ?></td></tr>
            <?php
            if($total_authors)
            {
                foreach(@$Part['Authors']['Creator'] as $Creator)
                {
                    ?>
                    <tr><td>CreatorID</td><td>:     <?php echo self::check_arr(@$Creator['CreatorID']) ?></td></tr>
                    <tr><td>Name</td><td>:          <?php echo self::check_arr(@$Creator['Name']) ?></td></tr>
                    <tr><td>Numeration</td><td>:    <?php echo self::check_arr(@$Creator['Numeration']) ?></td></tr>
                    <tr><td>Unit</td><td>:          <?php echo self::check_arr(@$Creator['Unit']) ?></td></tr>
                    <tr><td>Title</td><td>:         <?php echo self::check_arr(@$Creator['Title']) ?></td></tr>
                    <tr><td>Location</td><td>:      <?php echo self::check_arr(@$Creator['Location']) ?></td></tr>
                    <tr><td>FullerForm</td><td>:    <?php echo self::check_arr(@$Creator['FullerForm']) ?></td></tr>
                    <tr><td>Dates</td><td>:         <?php echo self::check_arr(@$Creator['Dates']) ?></td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <?php
                }
            }
        }
        else
        {
            $total_authors = count(@$Part['Authors']);
            ?>
            <tr><td colspan="2">Total: <?php echo $total_authors ?></td></tr>
            <?php
            if($total_authors)
            {
                foreach(@$Part['Authors'] as $Creator)
                {
                    ?>
                    <tr><td>CreatorID</td><td>:     <?php echo self::check_arr(@$Creator['CreatorID']) ?></td></tr>
                    <tr><td>Name</td><td>:          <?php echo self::check_arr(@$Creator['Name']) ?></td></tr>
                    <tr><td>Numeration</td><td>:    <?php echo self::check_arr(@$Creator['Numeration']) ?></td></tr>
                    <tr><td>Unit</td><td>:          <?php echo self::check_arr(@$Creator['Unit']) ?></td></tr>
                    <tr><td>Title</td><td>:         <?php echo self::check_arr(@$Creator['Title']) ?></td></tr>
                    <tr><td>Location</td><td>:      <?php echo self::check_arr(@$Creator['Location']) ?></td></tr>
                    <tr><td>FullerForm</td><td>:    <?php echo self::check_arr(@$Creator['FullerForm']) ?></td></tr>
                    <tr><td>Dates</td><td>:         <?php echo self::check_arr(@$Creator['Dates']) ?></td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <?php
                }
            }
        }
        ?>
        
        </table>
    </div>
    
    
</div>

