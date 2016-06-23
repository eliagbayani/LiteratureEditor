<form name="validator_form" action="index.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="search_type" value="pagesearch">
<table>
    <tr>
        <td>Page ID:</td>
        <td><input type="text" size="20" name="page_id"<?php if($page_id) echo " value=\"$page_id\""; ?>/></td>
        <td><button id="button_search_page" onClick="spinner_on()">Fetch this page</button></td>
    </tr>
</table>
</form>
