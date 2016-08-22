<?php
// namespace php_active_record;
/* Expects: $params */
/*
$radio     = $params['radio'];
$proj_name = @$params['proj_name'];
$proj_desc = @$params['proj_desc'];
*/
?>
<!---
<form id="frm" name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" id="search_type" name="search_type" value="projectsmenu">
<input type="hidden" name="radio" value="<?php echo $radio ?>">
<table border="0" width="100%">
    <tr><td><b>Project name</b>:</td>
        <td><input size="100" type="text" id="proj_name" name="proj_name" value="<?php echo $proj_name; ?>"></td>
    </tr>
    <tr><td><b>Description</b>:</td>
        <td><input size="100" type="text" name="proj_desc" value="<?php echo $proj_desc; ?>"></td>
    </tr>
<tr valign="top">
    <td colspan="2">
    <input type="button" onClick="submit_onclick()" value="Review Project">
    </td>
</tr>
</table>
</form>
--->

<?php
echo "<pre>"; print_r($params); echo "</pre>";

$radio     = $params['radio'];

if(!isset($params['proj_name']))
{
    $search_type = "projectsmenu";
    $overwrite = 0;
    $wiki_title = "";
    $compiler = "";

    // $title_form = "";
    // $taxon_asso = "";
    
    $proj_name = '';
    $proj_desc = '';
}
else //this means a form-submit
{
    $overwrite      = $params['overwrite'];
    $wiki_title      = @$params['wiki_title'];
    $compiler      = $params['compiler'];
    $search_type    = $params['search_type'];

    // $title_form     = $params['title_form'];
    // $taxon_asso     = $params['taxon_asso'];

    $proj_name     = $params['proj_name'];
    $proj_desc     = $params['proj_desc'];
}

?>
<div id="tabs-0">
    <?php
    /*
    require_once("proj-start-form-pre.php");
    if($cont_editor)
    */
    if(true)
    {
        ?>
        <!--- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ --->
            <form id="frm" action="index.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="search_type" id="search_type" value="projectsmenu">
            <input type="hidden" name="radio" value="<?php echo $radio ?>">
            <input type="hidden" name="overwrite" value="<?php echo $overwrite ?>">
            <input type="hidden" name="wiki_title" value="<?php echo $wiki_title ?>" size="200">
            <input type="hidden" name="compiler" value="<?php echo $compiler ?>">

            <input type="text" name="articles" value="<?php echo @$params['articles'] ?>">

            <table border="0" width="100%">
                <tr><td><b>Project name</b>:</td>
                    <?php
                    /* working well if we don't want Completed Projects name be editable
                    if(self::project_is_completed(@$params['wiki_title']))
                    {
                        ?>
                        <td><?php echo $proj_name; ?><input size="100" type="hidden" id="proj_name" name="proj_name" value="<?php echo $proj_name; ?>"></td>
                        <?php
                    }
                    else
                    {
                        ?>
                        <td><input size="100" type="text" id="proj_name" name="proj_name" value="<?php echo $proj_name; ?>"></td>
                        <?php
                    }
                    */
                    if(@$params['articles'])
                    {
                        ?>
                        <td><input size="100" type="hidden" id="proj_name" name="proj_name" value="<?php echo $proj_name; ?>"><?php echo $proj_name ?></td>
                        <?php
                    }
                    else
                    {
                        ?>
                        <td><input size="100" type="text" id="proj_name" name="proj_name" value="<?php echo $proj_name; ?>"></td>
                        <?php
                    }
                    ?>
                    
                </tr>
                <tr><td><b>Description</b>:</td>
                    <td><input size="100" type="text" name="proj_desc" value="<?php echo $proj_desc; ?>"></td>
                </tr>
            <tr valign="top">
                <td colspan="2">
                <input type="button" onClick="submit_onclick()" value="Review Project">
                </td>
            </tr>
            </table>

            </form>
        <!--- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ --->
        <?php
    }
    ?>
</div>



<script>
function submit_onclick()
{
    if(document.getElementById('proj_name').value == "")
    {
        alert("Project name - cannot be blank.");
        return true;
    }
    document.getElementById('search_type').value='reviewproject';
    // spinner_on();
    document.getElementById('frm').submit();
}
</script>
