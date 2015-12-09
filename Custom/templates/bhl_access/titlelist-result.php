<?php
// namespace php_active_record;
    /* 
        Expects: $letter
    */
    $rows = self::list_titles_by_letter($letter);
    $i = 0;
?>
<div id="accordion_open">
<h3>Titles = <?php echo count($rows) ?></h3>
<div>
<table id="customers">
    <tr>
        <th>ID</th>
        <th>Title
            <!-- <a href="../bhl_access/titles.php?letter=all">All</a> too big to load as DataTables-->
        </th>
        <th align="right"><a href="../bhl_access/titles.php?letter=<?php echo $letter ?>">View as DataTable</a></th>
    </tr>
    <?php foreach($rows as $r)
    {
        $i++;
        if(!$r[0]) continue;
        if (($i % 2) == 1) echo '<tr class="alt">';
        else echo '<tr>';
        ?>
            <td><a href="../bhl_access/index.php?search_type=titlesearch&title_id=<?php echo $r[0] ?>"><?php echo @$r[0]?></a></td>
            <td colspan="2"><?php echo $r[1] ?></td>
            <!--
            <td><?php echo $r[0] ?></td>
            <td><a href="../bhl_access/index.php?search_type=titlesearch&title_id=<?php echo $r[0] ?>"><?php echo @$r[1]?></a></td>
            -->
        </tr>
        <?php
    }
    ?>
</table>
</div>
</div>
