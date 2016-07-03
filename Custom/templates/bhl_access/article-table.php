<link rel="stylesheet" href="../jquery-datatables/jquery.dataTables.min.css">
<!--- <script src="../jquery-datatables/jquery-1.11.3.min.js"></script> --->
<script src="../jquery-datatables/jquery.dataTables.min.js"></script>
<!-- for smoothness -->
<script src="../jquery-datatables/dataTables.jqueryui.min.js"></script>
<!--- <link rel="stylesheet" href="../jquery-datatables/jquery-ui.css"> --->
<link rel="stylesheet" href="../jquery-datatables/dataTables.jqueryui.min.css">
<style>
body{
    font: 70% Arial, "Trebuchet MS", sans-serif; /* 62.5% */
    /* margin: 50px; */
}

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
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Subchapter</th>
            <th>Compiler</th>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($rows as $r)
        {
            ?>
                <tr>
                    <td><?php echo $r['timestamp'] ?></td>
                    <td><?php echo $r['header_title'] ?></td>
                    <td><?php echo $r['subject_type'] ?></td>
                    <td><?php echo $r['compiler'] ?></td>
                </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<form id="myform" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="titlesearch">
<input id="input_id" type="hidden"   name="title_id">
</form>

<script>
<!--- $(document).ready(function() { --->
    // Setup - add a text input to each footer cell
    $('#example tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#example').DataTable();
 
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
            myFunction(data[0], data[1]);
        } );
    
<!--- } ); --->


function myFunction(title_id, title) {
    var x;
    if (confirm("Proceed to Title: "+title) == true) 
    {
        //alert('ok '+title_id);
        document.getElementById("input_id").value = title_id;
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
