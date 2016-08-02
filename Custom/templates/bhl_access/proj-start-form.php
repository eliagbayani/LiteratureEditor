<?php
// namespace php_active_record;
    /* Expects: $params */

echo "<pre>"; print_r($params); echo "</pre>";

/*
if(!isset($params['search_type']))
{
    exit("elix");
    $proj_name = "";
}
else //this means a form-submit OR initial page load
{
}
*/

$radio     = $params['radio'];
$proj_name = @$params['proj_name'];
$proj_desc = @$params['proj_desc'];


?>
<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="projectsmenu">
<input type="hidden" name="radio" value="<?php echo $radio ?>">

<table border="1" width="100%">

    <tr><td><b>Project name</b>:</td>
        <td><input size="100" type="text" name="proj_name" value="<?php echo $proj_name; ?>"></td>
    </tr>
    <tr><td><b>Description</b>:</td>
        <td><input size="100" type="text" name="proj_desc" value="<?php echo $proj_desc; ?>"></td>
    </tr>




<tr valign="top">
    <td colspan="2">
    <input type="submit">
    </td>
</tr>
</table>
</form>
