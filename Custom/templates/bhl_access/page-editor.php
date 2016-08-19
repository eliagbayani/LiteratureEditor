<?php
// if(isset($Page))

$accordion_item = '';
$scientists = '';
$public = '';
$children = '';

$save_status[0] = '';
$save_status[1] = '';
$save_status[2] = '';
$save_status[3] = '';
$save_status[4] = '';

if(!isset($params['header_title']))
{
    $PageID = $Page->PageID;
    $search_type = "pagesearch";

    $overwrite = 0;
    $wiki_title = "";
    $compiler = "";

    $title_form = "";
    
    $title_id = self::get_ItemInfo_using_item_id($Page->ItemID, "PrimaryTitleID");
    $title = self::get_TitleInfo_using_title_id($title_id, "FullTitle");
    $header_title = $title . " " . self::get_ItemInfo_using_item_id($Page->ItemID, "volume");
    
    $copyrightstatus = self::get_ItemInfo_using_item_id($Page->ItemID, 'copyrightstatus');
    $license_url = self::get_ItemInfo_using_item_id($Page->ItemID, 'license url');
    $license_type = self::get_license_type($license_url, $copyrightstatus); //default is based on specs from mapping doc.
    $license_type = self::get_license_value($license_type);
    
    $citation_and_authors = self::get_bibliographicCitation($title_id, $Page, $title);
    $bibliographicCitation = $citation_and_authors['citation'];
    $agents                = $citation_and_authors['authors']; // Authors
    $agents2               = $citation_and_authors['authors2']; // Authors
    
    if(!$agents) $agents = "";
    
    $ItemID = $Page->ItemID;
    $ocr_text = self::string_or_object(@$Page->OcrText);

    $recently_added = $PageID;
    $label_added = "";
    $label_added_ref = "";
    
    /* should not be here, just for testing...
    $new_ocr = self::get_PageInfo_using_page_id($PageID, "ocr_text");
    echo "<br>new ocr: [$new_ocr]<br>";
    */
    
    $taxon_asso = "";
    $separated_names = self::get_separated_names($Page->Names);
    $separated_names = array_filter($separated_names); //removes blank array values
    
    // $subject_type = "http://rs.tdwg.org/ontology/voc/SPMInfoItems#GeneralDescription"; //default value
    $language = '';
    $rightsholder = '';
    $contributor = '';
    
    $references = '';
    
    $next_page = $recently_added + 1;
}
else //this means a form-submit
{
    $agents2 = ""; //don't want to query this at this time, maybe when requested
    $accordion_item = $params['accordion_item'];
    $overwrite      = $params['overwrite'];
    $wiki_title      = @$params['wiki_title'];
    $compiler      = $params['compiler'];
    
    
    $PageID         = $params['PageID'];
    $search_type    = $params['search_type'];
    $title_form     = $params['title_form'];
    $header_title   = $params['header_title'];
    
    $copyrightstatus = $params['copyrightstatus'];
    
    $subject_type   = $params['subject_type'];

    $ItemID         = $params['ItemID'];
    $ocr_text       = trim($params['ocr_text']);
    $references     = $params['references'];

    $language       = $params['language'];
    $rightsholder   = $params['rightsholder'];
    $contributor   = $params['contributor'];

    $bibliographicCitation   = $params['bibliographicCitation'];
    
    $license_type   = $params['license_type'];  //has default values above
    $agents         = $params['agents'];        //has default values above
    
    if(isset($params['scientists'])) $scientists = "checked";
    if(isset($params['public']))     $public     = "checked";
    if(isset($params['children']))   $children   = "checked";

    $taxon_asso     = $params['taxon_asso'];

    if($params['AddPage'] == 1)
    {
        $recently_added = $params['recently_added'];
        $label_added = $params['label_added'];
        $label_added .= " $recently_added";

        //now get the ocr_text of added page
        $new_ocr = trim(self::get_PageInfo_using_page_id($recently_added, "ocr_text"));
        $ocr_text .= "\n" . $this->parag_separator . "\n" . $new_ocr;
        
        //first get recently added page its taxa names:
        $Page_Names = self::get_PageInfo_using_page_id($recently_added, "taxa_names");
        $Page_Names = json_decode(json_encode($Page_Names)); //converting SimpleXMLElement Object to stdClass Object
        $separated_names = self::get_separated_names($Page_Names);
        $separated_names = array_filter($separated_names); //removes blank array values

        //then append the old list from from-submitted to it
        $old_list = $params['separated_names'];
        $old_list = explode("|", $old_list);
        $separated_names = array_merge($old_list, $separated_names);
        $separated_names = array_unique($separated_names); //make unique
        
        $next_page = $recently_added + 1;
    }
    else
    {
        $recently_added = $params['recently_added'];
        $next_page = $recently_added;
        
        $label_added = $params['label_added'];
        
        $old_list = $params['separated_names'];
        $separated_names = explode("|", $old_list);
    }
    
    if(!isset($params['fromReview'])) $save_status[$params['accordion_item']] = '<span id="memo">Saved OK</span>';
    
    //for references
    $label_added_ref = $params['label_added_ref'];
    if($params['ref_prioritized'] == 1)
    {
        if($val = $params['ref_page_id'])
        {
            if(stripos($label_added_ref, $val) !== false) //this string is found - page already added
            {
                self::display_message(array('type' => "error", 'msg' => "Page already added: [$val]"));
                $save_status[2] = '';
            }
            else
            {
                //now get the ocr_text of added page
                if($new_ref = trim(self::get_PageInfo_using_page_id($val, "ocr_text")))
                {
                    $references .= "\n" . $this->parag_separator . "\n" . $new_ref;
                    $label_added_ref .= " $val";
                }
                else
                {
                    self::display_message(array('type' => "error", 'msg' => "Invalid Page ID: [$val]"));
                    $save_status[2] = '';
                }
            }
        }
        else $save_status[2] = '';
    }

}

$page_IDs = self::get_page_IDs($ItemID);
$subjects = self::get_subjects();
$languages = self::get_languages();
$licenses = self::get_licenses();
$msgs = self::page_editor_msgs();
?>
<div id="tabs-0">
    <?php require_once("page-editor-pre.php");
    if($cont_editor)
    {
        ?>
        <!--- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ --->
            <table><tr><td colspan="2"><big><?php echo $header_title ?></big></td></tr>
            <tr>
            <td><b>Processing Page <?php echo self::get_url_by_id("page", $PageID) ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>
                <form name="" action="index.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="search_type" value="pagesearch">
                Go to another page:
                <select name="page_id" id="">
                    <?php foreach($page_IDs as $page_ID)
                    {
                        $selected = "";
                        if($PageID == $page_ID) $selected = "selected";
                        echo '<option value="' . $page_ID . '" ' . $selected . '>' . $page_ID . '</option>';
                    }?>
                </select>
                <button id="" onClick="spinner_on()">Go</button>
                </form>
            </td>
            </tr>
            <form id="frm" action="index.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="search_type" id="search_type" value="pagesearch">
            <input type="hidden" name="overwrite" value="<?php echo $overwrite ?>">
            <input type="hidden" name="wiki_title" value="<?php echo $wiki_title ?>" size="200">
            <input type="hidden" name="page_id" value="<?php echo $PageID ?>"> <!--- this may not be needed --->
            <input type="hidden" name="PageID" value="<?php echo $PageID ?>">
            <input type="hidden" name="recently_added" value="<?php echo $next_page ?>">
            <input type="hidden" name="label_added" value="<?php echo $label_added ?>">
            <input type="hidden" name="label_added_ref" value="<?php echo $label_added_ref ?>">
            <input type="hidden" name="header_title" value="<?php echo $header_title ?>">
            <input type="hidden" name="copyrightstatus" value="<?php echo $copyrightstatus ?>">
            <input type="hidden" name="next_page" value="<?php echo $next_page ?>">
            <input type="hidden" name="ItemID" value="<?php echo $ItemID ?>">
            <input type="hidden" name="AddPage" id="AddPage">
            <input type="hidden" name="accordion_item" id="accordion_item" value="<?php echo $accordion_item ?>">
            <input type="hidden" name="compiler" value="<?php echo $compiler ?>">

            <input type="text" name="projects" value="<?php echo @$params['projects'] ?>">
            
            <tr>
            <td colspan="2">
                <!--- working ok but commented by Katja
                <?php 
                if(in_array($next_page, $page_IDs)) echo "You can <a href='index.php?page_id=" . ($PageID + 1) . "&search_type=pagesearch'>skip to next page</a> to remove the current text excerpt and replace it with the content of the next page.";
                else                                echo "No more succeeding page.";
                ?> 
                --->
                <!-- working but a copy was moved to the 'Text Excerpt' section
                &nbsp;&nbsp; or &nbsp;&nbsp;<button id="" onClick="document.getElementById('AddPage').value=1;spinner_on();">Add a page</button>
                -->
                <?php if($label_added) echo "<i>Page added: $label_added</i>"; ?>
            </td>
            </tr>
            <!-- working
            <tr><td colspan="2" bgcolor="AliceBlue"><?php echo $msgs["intro"] ?></td></tr>
            -->
            </table>

            <div id="accordion_open2">
                <h2>Title & Subchapter</h2>
                <div>
                    <table>
                    <tr><td><b>EOL subchapter</b>:</td>
                        <td>
                        <select name="subject_type" id="selectmenu_4"><option>Choose a subchapter</option>
                            <?php 
                            foreach($subjects as $s)
                            {
                                $selected = "";
                                if($subject_type == $s['url']) $selected = "selected";
                                echo '<option value="' . $s['url'] . '" ' . $selected . '>' . $s['t'] . '</option>';
                            }
                            ?>
                        </select>
                        </td>
                    </tr>
                    <tr><td><b>Title</b> (optional):</td>
                        <td><input size="100" type="text" name="title_form" value="<?php echo $title_form; ?>"></td>
                    </tr>
                    <tr><td colspan="2" bgcolor="AliceBlue"><?php echo $msgs["title"] ?></td></tr>
                    <tr><td><button id="" onClick="document.getElementById('accordion_item').value=0;spinner_on();">Save</button> <i><?php echo $save_status[0] ?></i></td></tr>
                    </table>
                </div>

                <h2>Text Excerpt for EOL</h2>
                <div>
                    <table>
                    <tr><td><button id="" onClick="document.getElementById('AddPage').value=1;document.getElementById('accordion_item').value=1;spinner_on();">Add a page</button> &nbsp;&nbsp;<?php if($label_added) echo "<i>Page added: $label_added</i>"; ?>
                    </td></tr>

                    <tr><td bgcolor="AliceBlue">
                    <?php echo $msgs["text_excerpt_pre"] ?>
                    </td></tr>

                    <tr><td>
                        <textarea id="ocr_text" rows="15" cols="100" name="ocr_text"><?php echo self::format_wiki($ocr_text); ?></textarea> 
                    </td></tr>
                    <tr><td bgcolor="AliceBlue"><?php echo $msgs["text_excerpt"] ?></td></tr>
                    <tr><td><button id="" onClick="document.getElementById('accordion_item').value=1;spinner_on();">Save</button> <i><?php echo $save_status[1] ?></i></td></tr>
                    </table>
                </div>

                <h2>References</h2>
                <div>
                    <table>
                    <tr><td><b>Fetch References from Page</b>: <input type="text" name="ref_page_id"> 
                    <button id="" onClick="document.getElementById('accordion_item').value=2;document.getElementById('ref_prioritized').value=1;spinner_on();">Add a page</button>
                    &nbsp;&nbsp;<?php if($label_added_ref) echo "<i>Page added: $label_added_ref</i>"; ?>
                    <input type="hidden" name="ref_prioritized" id="ref_prioritized">
                    </td></tr>

                    <tr><td>
                        <textarea id="" rows="15" cols="100" name="references"><?php echo self::format_wiki($references); ?></textarea>
                    </td></tr>
                    <tr><td bgcolor="AliceBlue"><?php echo $msgs["references"] ?></td></tr>
                    <tr><td><button id="" onClick="document.getElementById('accordion_item').value=2;spinner_on();">Save</button> <i><?php echo $save_status[2] ?></i></td></tr>
                    </table>
                </div>

                <h2>Taxon Associations</h2>
                <div>
                    <table>
                    <tr><td>
                        <b>Taxon associations for this excerpt</b>:
                        <input size="100" type="text" id="taxon_asso" name="taxon_asso" value="<?php echo $taxon_asso; ?>"> <button id="" onClick="document.getElementById('accordion_item').value=3;spinner_on();">Save</button> <i><?php echo $save_status[3] ?></i>
                    </td></tr>
                    <tr><td bgcolor="AliceBlue"><?php echo $msgs["taxon_asso"] ?></td></tr>
                    <tr><td>
                        <?php
                        $separated_names = array_map("trim", $separated_names);
                        $separated_names = array_filter($separated_names);
                        echo "n=" . count($separated_names) . "<br>";
                        foreach($separated_names as $names) echo "$names<br>"; ?>
                        <input type="hidden" name="separated_names" value="<?php echo implode("|", $separated_names); ?>">
                    </td></tr>
                    </table>
                </div>

                <h2>Excerpt Metadata</h2>
                <div>
                <table>
                <tr><td colspan="2" bgcolor="AliceBlue"><?php echo $msgs["excerpt_meta"] ?></td></tr>
                <tr><td width="95"><b>Language</b>:</td>
                    <td>
                        <select name="language" id="">
                            <?php 
                            foreach($languages as $s)
                            {
                                $selected = "";
                                if($language == $s['abb']) $selected = "selected";
                                echo '<option value="' . $s['abb'] . '" ' . $selected . '>' . $s['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><b>License</b>:</td>
                    <td>
                        <select name="license_type" id="selectmenu_3">
                            <!--- <option></option> --->
                            <?php foreach($licenses as $s)
                            {
                                $selected = "";
                                if($license_type == $s['value']) $selected = "selected";
                                echo '<option value="' . $s['value'] . '" ' . $selected . '>' . $s['t'] . '</option>';
                            }?>
                        </select>
                    </td>
                </tr>
                <tr><td><b>Rights holder</b>:</td>
                    <td><input size="100" type="text" name="rightsholder" value="<?php echo $rightsholder; ?>"></td>
                </tr>
                <tr>
                    <td><b>Authors</b>:</td>
                    <td><input size="100" type="text" name="agents" value="<?php echo $agents ?>"><small><?php echo $agents2 ?></small></td>
                </tr>
                <tr>
                    <td><b>Bibliographic citation</b>:</td>
                    <td><textarea id="" rows="4" cols="100" name="bibliographicCitation"><?php echo $bibliographicCitation; ?></textarea></td>
                </tr>
                <tr><td><b>Contributor</b>:</td>
                    <td><input size="100" type="text" name="contributor" value="<?php echo $contributor; ?>"></td>
                </tr>
                <tr><td><b>Audience</b>:</td>
                    <td><input type="checkbox" name="scientists" <?php echo $scientists; ?>> scientists &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="public"     <?php echo $public; ?>> public &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="children"   <?php echo $children; ?>> children
                    </td>
                </tr>
                <tr><td colspan="2"><button id="" onClick="document.getElementById('accordion_item').value=4;spinner_on();">Save</button> <i><?php echo $save_status[4] ?></i></td></tr>
                </table>
                </div>

            </div>
            <!--- <button onClick="submit_onclick()">Review Excerpt & Metadata</button> --->
            <input type="button" onClick="submit_onclick()" value="Review Excerpt & Metadata">
            <!--- <button type="button" class="close">Collapse all</button> --->
            </form>
        <!--- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ --->
        <?php
    }
    ?>
</div>
<?php
if(isset($params['accordion_item'])) print '<script>$("#accordion_open2").accordion({ active: ' . $params['accordion_item'] . ', heightStyle: "content" });</script>';
?>
<script>
function submit_onclick()
{
    if(document.getElementById('selectmenu_4').selectedIndex == 0)
    {
        alert("Please choose an EOL subchapter.");
        disable_accordion_panels(0);
        return true;
    }
    if(document.getElementById('ocr_text').value == "")
    {
        alert("Text Excerpt for EOL - cannot be blank.");
        disable_accordion_panels(1);
        return true;
    }
    if(document.getElementById('taxon_asso').value == "")
    {
        alert("Taxon Associations - cannot be blank.");
        disable_accordion_panels(3);
        return true;
    }
    document.getElementById('search_type').value='reviewexcerpt';
    spinner_on();
    document.getElementById('frm').submit();
}

function disable_accordion_panels(panel)
{
    // $("#accordion_open2").accordion({active: false});
    $("#accordion_open2").accordion({active: false , collapsible: true});
    $("#accordion_open2").accordion("refresh");
    $("#accordion_open2").accordion( "option", "active", panel ); 
}
</script>

