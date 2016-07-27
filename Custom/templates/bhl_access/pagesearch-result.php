<?php
// namespace php_active_record;
    /* Expects: */
    $total = count(@$xml->Result);
    // print_r(@$xml->Result);
    // print_r($params);
?>
<!--- <h3 class="demoHeaders">Page(s): <?php echo $total ?></h3> --->
<?php if($loop = @$xml->Result)
{
    ?>
    <div id="accordion_open">
        <?php
        $tabs_count = 0;
        foreach($loop as $Page)
        {
            $Page_xml = $Page;
            $Page = json_decode(json_encode($Page)); //converting SimpleXMLElement Object to stdClass Object
            $tabs_count++;
            ?>
            <h3>PageID: <?php echo $Page->PageID ?></h3>
            <div><!-- accordion start -->
                <div id="tabs">
                    <ul>
                        <li><a href="#tabs-0">Page Editor</a></li>
                        <li><a href="#tabs-1">PageSummary</a></li>
                        <li><a href="#tabs-2">PageTypes</a></li>
                        <li><a href="#tabs-3">PageNumbers</a></li>
                        <li><a href="#tabs-4">Taxa</a></li>
                    </ul>
                    <?php require_once("page-editor.php") ?>
                    <?php require_once("page-more-info-sub.php") ?> <!--- for PageSummary, PageTypes, PageNumbers  --->
                    <div id="tabs-4">
                        Name(s): <?php echo count($Page_xml->Names->Name) ?><br>
                        <?php if(count($Page_xml->Names->Name))
                        {
                            ?>
                            <table>
                                <tr align="left">
                                    <th>NameBankID</th>
                                    <th>EOLID</th>
                                    <!--- <th></th> --->
                                    <th>NameFound</th>
                                    <th>NameConfirmed</th>
                                </tr>
                            <?php 
                            foreach($Page_xml->Names->Name as $Name)
                            {
                                ?>
                                    <tr>
                                        <td><?php echo $Name->NameBankID ?></td>
                                        <td><?php echo self::get_url_by_id("eol", $Name->EOLID) ?></td>
                                        <td><?php echo $Name->NameFound ?></td>
                                        <td><?php echo $Name->NameConfirmed ?></td>
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
