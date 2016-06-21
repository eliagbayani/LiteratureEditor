<?php 
$subjects = self::get_subjects();
$subject_type = "http://rs.tdwg.org/ontology/voc/SPMInfoItems#GeneralDescription"; //default value
if(isset($url_params['subject_type']))
{
    if($val = $url_params['subject_type']) $subject_type = $val;
}
//====================================================
$audiences = array(
    array("value" => "Everyone",        "t" => "Everyone"),
    array("value" => "General public",  "t" => "General public"),
    array("value" => "Expert users",    "t" => "Expert users"),
    array("value" => "Children",        "t" => "Children"));
$audience_type = "Everyone"; //default value
if(isset($url_params['audience_type']))
{
    if($val = $url_params['audience_type']) $audience_type = $val;
}
//====================================================
$licenses = self::get_licenses();
$license_type = self::get_license_type($license_url, $copyrightstatus); //default is based on specs from mapping doc.
$license_type = self::get_license_value($license_type);
if(isset($url_params['license_type']))
{
    if($val = $url_params['license_type']) $license_type = $val;
}
// echo "<br>[$license_type]</br>";
//====================================================
$citation_and_authors = self::get_bibliographicCitation($title_id, $Page, $title);
$bibliographicCitation = $citation_and_authors['citation'];
$agents = $citation_and_authors['authors2'];

if(isset($url_params['agents']))
{
    if($val = $url_params['agents']) $agents = $val;
}
//====================================================
$taxon_names = self::get_taxa_list($Page->PageID);
if(isset($url_params['taxon_names']))
{
    if($val = $url_params['taxon_names']) $taxon_names = $val;
}
//====================================================
if(isset($params))
{
    if($val = @$params['subject_type']) $subject_type = $val;
    if($val = @$params['audience_type']) $audience_type = $val;
    if($val = @$params['license_type']) $license_type = $val;
    if($val = @$params['agents']) $agents = $val;
}
//====================================================
?>
<form action="../bhl_access/index.php">
    <input type="hidden" name="page_id" value="<?php echo $Page->PageID ?>">
    <input type="hidden" name="item_id" value="<?php echo $Page->ItemID ?>">
    <input type="hidden" name="title_id" value="<?php echo $title_id ?>">
    <input type="hidden" name="pass_title" value="<?php echo urlencode($pass_title) ?>">
    <input type="hidden" name="search_type" value="move2wiki">
    <input type="hidden" name="licensor" value="<?php echo $licensor ?>">
    
    <table border="0">
        <tr>
            <td>Taxa found in page:</td>
            <td><input size="100" type="text" name="taxon_names" value="<?php echo $taxon_names ?>"> <i><small>semi-colon separated taxon names</small></i></td>
        </tr>
    
        <tr>
            <td>Bibliographic Citation:</td>
            <td>
            <input type="text" size="100" name="bibliographicCitation" value="<?php echo $bibliographicCitation ?>">
            </td>
        </tr>
        <tr>
            <td>Authors:</td>
            <td><input size="100" type="text" name="agents" value="<?php echo $agents ?>"> <i><small>semi-colon separated authors</small></i></td>
        </tr>
        
        <tr>
            <td>Subject:</td>
            <td>
                <select name="subject_type" id="selectmenu">
                    <?php foreach($subjects as $s)
                    {
                        $selected = "";
                        if($subject_type == $s['url']) $selected = "selected";
                        echo '<option value="' . $s['url'] . '" ' . $selected . '>' . $s['t'] . '</option>';
                    }?>
                </select>
            </td>
        </tr>
        
        <tr>
            <td>License:</td>
            <td>
                <select name="license_type" id="selectmenu_3">
                    <option></option>
                    <?php foreach($licenses as $s)
                    {
                        $selected = "";
                        if($license_type == $s['value']) $selected = "selected";
                        echo '<option value="' . $s['value'] . '" ' . $selected . '>' . $s['t'] . '</option>';
                    }?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Audience:</td>
            <td>
                <select name="audience_type" id="selectmenu_2">
                    <?php foreach($audiences as $s)
                    {
                        $selected = "";
                        if($audience_type == $s['value']) $selected = "selected";
                        echo '<option value="' . $s['value'] . '" ' . $selected . '>' . $s['t'] . '</option>';
                    }?>
                </select>
            </td>
        </tr>

        <tr><td colspan="2" align="center"><button id="button_move2wiki"><?php echo $submit_text ?></button></td></tr>
    </table>
    
</form>
