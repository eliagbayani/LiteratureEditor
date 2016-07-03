<link rel="stylesheet" href="../jquery-datatables/jquery.dataTables.min.css">
<!--- <script src="../jquery-datatables/jquery-1.11.3.min.js"></script> --->
<script src="../jquery-datatables/jquery.dataTables.min.js"></script>
<!-- for smoothness -->
<script src="../jquery-datatables/dataTables.jqueryui.min.js"></script>
<!--- <link rel="stylesheet" href="../jquery-datatables/jquery-ui.css"> --->
<link rel="stylesheet" href="../jquery-datatables/dataTables.jqueryui.min.css">
<style>
body{font: 70% Arial, "Trebuchet MS", sans-serif; /* 62.5% */ /* margin: 50px; */}
tfoot input {
        width: 100%;
        padding: 1px;
        box-sizing: border-box;
    }
#example {
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
?>
<table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Subchapter</th>
            <th>Compiler</th>
            <th style="display:none">Wiki</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Subchapter</th>
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
                    <td><?php echo $r['header_title'] ?></td>
                    <td><?php echo $r['subject_type'] ?></td>
                    <td><?php echo $r['compiler'] ?></td>
                    <td style="display:none"><?php echo $r['title'] ?></td>
                </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<form id="myform" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="wiki2php">
<input type="hidden" name="overwrite"   value="1">
<input type="hidden" name="wiki_title"  value="1" id="wiki_title">
</form>

<script>
<!--- $(document).ready(function() { --->
    // Setup - add a text input to each footer cell
    $('#example tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#example').DataTable({
        "iDisplayLength": 50
    });
 
    // Apply the search
    table.columns().every( function () {
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
    $('#example tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
 
    $('#button').click( function () {
        table.row('.selected').remove().draw( false );
    } );    
    
    // on click event
    $('#example tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            //alert( 'You clicked on '+data[0]+'\'s row' );
            myFunction(data[4], data[1]);
        } );
    
<!--- } ); --->

function myFunction(wiki_title, title) {
    var x;
    if (confirm("Proceed to Title: "+title) == true) 
    {
        //alert('ok '+title_id);
        document.getElementById("wiki_title").value = wiki_title;
        document.getElementById("myform").submit();
        //x = "You pressed OK!";
    } else 
    {
        //alert('cancel');
        //x = "You pressed Cancel!";
    }
    //document.getElementById("myform").innerHTML = x;
}
</script>
