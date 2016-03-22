<?php
// namespace php_active_record;
    /* 
        Expects:
    */
    $total = count(@$xml->Result);
?>
<!--- <h3 class="demoHeaders">Record(s): <?php echo $total ?></h3> --->
<?php if($loop = @$xml->Result)
{
    ?>
    <div id="accordion">
        <?php
        $tabs_count = 0;
        foreach($loop as $Page)
        {
            $tabs_count++;
            ?>
            <div><!-- accordion start -->
                <div id="tabs<?php echo $tabs_count ?>">
                    <ul>
                        <li><a href="#tabs<?php echo $tabs_count ?>-1">Taxon Names</a></li>
                    </ul>

                    <div id="tabs<?php echo $tabs_count ?>-1">
                        Name(s): <?php echo count($Page->Name) ?><br>
                        <?php if(count($Page->Name))
                        {
                            ?>
                            <table>
                                <tr align="left">
                                    <th>NameBankID</th>
                                    <th>EOLID</th>
                                    <th>NameFound</th>
                                    <th>NameConfirmed</th>
                                </tr>
                            <?php 
                            foreach($Page->Name as $Name)
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
