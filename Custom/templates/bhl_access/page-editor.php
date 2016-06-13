<?php
// if(isset($Page))
if(!isset($params['header_title']))
{
    $PageID = $Page->PageID;
    $search_type = "pagesearch";
    $title_form = "";
    
    $title_id = self::get_ItemInfo_using_item_id($Page->ItemID, "PrimaryTitleID");
    $title = self::get_TitleInfo_using_title_id($title_id, "FullTitle");
    $header_title = $title . " " . self::get_ItemInfo_using_item_id($Page->ItemID, "volume");
    
    $citation_and_authors = self::get_bibliographicCitation($title_id, $Page, $title);
    $bibliographicCitation = $citation_and_authors['citation'];
    $references            = $citation_and_authors['citation'];
    
    $agents = $citation_and_authors['authors2'];
    
    $ItemID = $Page->ItemID;
    $ocr_text = self::string_or_object(@$Page->OcrText);
}
else
{
    $PageID         = $params['PageID'];
    $search_type    = $params['search_type'];
    $title_form     = $params['title_form'];
    $header_title     = $params['header_title'];
    
    $subject_type   = $params['subject_type'];

    $ItemID         = $params['ItemID'];
    $ocr_text       = $params['ocr_text'];
    $references     = $params['references'];
    
    // echo "<pre>"; print_r($params); echo "</pre>"; exit;
}

$page_IDs = self::get_page_IDs($ItemID);
$subjects = self::get_subjects();
$msgs = self::page_editor_msgs();
$next_page = $PageID + 1;

// print_r($page_IDs); exit;
?>
<div id="tabs-0">

    <table><tr><td><big><?php echo $header_title ?></big></td></tr>
    <tr>
    <td><b>Processing Page <?php echo "<a href='http://biodiversitylibrary.org/page/$PageID'>$PageID</a>" ?></b></td>
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
        <button id="">Go</button>
        </form>
    </td>
    </tr>
    
    <form name="" action="index.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="search_type" value="pagesearch">
    <input type="hidden" name="page_id" value="<?php echo $PageID ?>">
    <input type="hidden" name="PageID" value="<?php echo $PageID ?>">
    
    <input type="hidden" name="header_title" value="<?php echo $header_title ?>">
    <input type="hidden" name="next_page" value="<?php echo $next_page ?>">
    <input type="hidden" name="ItemID" value="<?php echo $ItemID ?>">
    
    <tr>
    <td>
        <?php 
        if(in_array($next_page, $page_IDs))
        {
            echo "You can also <a href='index.php?page_id=$next_page&search_type=pagesearch'>Skip to next page</a>";
        }
        else echo "No more succeeding page.";
        ?>
        
        &nbsp;&nbsp; or &nbsp;&nbsp;<button id="">Add a page</button>
        
    </td>
    <td>
    </td>
    </tr>

    <tr><td colspan="2" bgcolor="AliceBlue"><?php echo $msgs["intro"] ?></td></tr>
    
    
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
            </table>
        </div>
    
        <h2>Text Excerpt for EOL</h2>
        <div>
            <table>
            <tr><td>
                <textarea id="" rows="5" cols="100" name="ocr_text">
                <?php echo $ocr_text; ?>
                </textarea>
            </td></tr>
            <tr><td bgcolor="AliceBlue"><?php echo $msgs["text_excerpt"] ?></td></tr>
            </table>
        </div>
    
        <h2>References</h2>
        <div>
            <table>
            <tr><td>
                <textarea id="" rows="5" cols="100" name="references">
                <?php echo $references; ?>
                </textarea>
            </td></tr>
            </table>
        </div>
        
        <h2>Taxon Associations</h2>
        <div></div>
        <h2>Excerpt Metadata</h2>
        <div></div>
        
    
    </div>
    </form>
    
</div>
