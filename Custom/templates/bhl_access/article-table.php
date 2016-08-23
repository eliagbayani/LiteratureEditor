<link rel="stylesheet" href="../jquery-datatables/jquery.dataTables.min.css">
<!--- <script src="../jquery-datatables/jquery-1.11.3.min.js"></script> --->
<script src="../jquery-datatables/jquery.dataTables.min.js"></script>
<!-- for smoothness -->
<script src="../jquery-datatables/dataTables.jqueryui.min.js"></script>
<!--- <link rel="stylesheet" href="../jquery-datatables/jquery-ui.css"> --->
<link rel="stylesheet" href="../jquery-datatables/dataTables.jqueryui.min.css">

<?php
if($table_id = @$data['table_id']) {}
else $table_id = "example";
// $table_id = "example";
?>

<style>
body{font: 70% Arial, "Trebuchet MS", sans-serif; /* 62.5% */ /* margin: 50px; */}
tfoot input {
        width: 100%;
        padding: 1px;
        box-sizing: border-box;
    }
#<?php echo $table_id ?> {
    font-family: Arial, Helvetica, sans-serif;
    font-size: small;
    width: 100%;
    border-collapse: collapse;
}
</style>
<?php
/*
The time when the article was added to the queue, with the newest articles being at the top by default.
The title of the book or journal, i.e., FullTitle from the BHL BookSearch API.
Compiler
Subchapter
*/
    $rows = $data['records'];
    $group = $data['group'];
    if($group == "articles") $vars = array('search_type' => "wiki2php",         'js_string' => "Review Excerpt & Metadata");
    else                     $vars = array('search_type' => "wiki2php_project", 'js_string' => "Review Project");
?>
<table id="<?php echo $table_id ?>" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Date</th>
            <?php
            if($group == "articles") echo "<th>Subchapter</th>";
            else                     echo "<th>Project</th>"
            ?>
            <!--- <th>Title</th> --->
            <th>Compiler</th>
            <th style="display:none">Wiki</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Date</th>
            <?php
            if($group == "articles") echo "<th>Subchapter</th>";
            else                     echo "<th>Project</th>"
            ?>
            <!--- <th>Title</th> --->
            <th>Compiler</th>
            <th style="display:none">Wiki</th>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($rows as $r)
        {
            /*
            [timestamp] => 2016-07-01T17:09:43Z
            [compiler] => Contributor one; Eli E. Agbayani
            [subject_type] => Conservation › Threats
            [title] => ForHarvesting:16194405 ae66e9b6f430af7e694cad4cf1d6f295
            [header_title] => Proceedings of the Entomological Society of Washington. v 101 1998
            http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?wiki_title=8611595_8b988c4f572fcada9173c54d5f6a04f8&search_type=wiki2php&overwrite=1
            */
            ?>
                <tr>
                    <td><?php echo $r['timestamp'] ?></td>
                    <?php
                    if($group == "articles") echo '<td>'.$r['subject_type'].'</td>';
                    else                     echo '<td>'.$r['title'].'</td>'
                    ?>
                    <!--- <td><?php echo $r['header_title'] ?></td> --->
                    <td><?php echo strip_tags($r['compiler']) ?></td>
                    
                    <?php
                    if($group == "projects") $r['title'] = str_replace(" ", "_", $r['title']);
                    if($group == "articles") $r['title'] = str_replace(" ", "_", $r['title']);
                    ?>
                    
                    <td style="display:none"><?php echo $r['title'] ?></td>
                </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<form id="myform<?php echo $table_id ?>" action="index.php" method="post" enctype="multipart/form-data" <?php if($group == "articles") echo "target=\"_blank\"" ?>><!---  --->
<input type="hidden" name="search_type" value="<?php echo $vars['search_type'] ?>">
<input type="hidden" name="overwrite"   value="1">
<input type="hidden" name="wiki_title"  value="1" id="wiki_title<?php echo $table_id ?>">
</form>

<script>
<!--- $(document).ready(function() { --->
    // Setup - add a text input to each footer cell
    $('#<?php echo $table_id ?> tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table<?php echo $table_id ?> = $('#<?php echo $table_id ?>').DataTable({
        "iDisplayLength": 25,
        "order": [[ 0, "desc" ]]
    });
 
    // Apply the search
    table<?php echo $table_id ?>.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    
    // to highlight row on click
    $('#<?php echo $table_id ?> tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table<?php echo $table_id ?>.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
 
    $('#button').click( function () {
        table<?php echo $table_id ?>.row('.selected').remove().draw( false );
    } );
    
    // on click event
    $('#<?php echo $table_id ?> tbody').on('click', 'tr', function () {
            var data<?php echo $table_id ?> = table<?php echo $table_id ?>.row( this ).data();
            // alert( 'You clicked on '+data<?php echo $table_id ?>[0]+'\'s row' );
            myFunction<?php echo $table_id ?>(data<?php echo $table_id ?>[3], data<?php echo $table_id ?>[1], data<?php echo $table_id ?>[2]);
        } );
    
<!--- } ); --->

function myFunction<?php echo $table_id ?>(wiki_title, title, subject) 
{
    /* working but dialog box to continue may not be needed anymore...
    var x;
    if (confirm("<?php echo $vars['js_string']?>:\n\n"+title+" - ("+subject+")") == true) 
    {
        document.getElementById("wiki_title<?php echo $table_id ?>").value = wiki_title;
        document.getElementById("myform<?php echo $table_id ?>").submit();
    } else 
    {
        //alert('cancel');
        //x = "You pressed Cancel!";
    }
    //document.getElementById("myform<?php echo $table_id ?>").innerHTML = x;
    */

    // spinner_on();
    document.getElementById("wiki_title<?php echo $table_id ?>").value = wiki_title;
    document.getElementById("myform<?php echo $table_id ?>").submit();
}
</script>
