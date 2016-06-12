<?php
$title_id = self::get_ItemInfo_using_item_id($Page->ItemID, "PrimaryTitleID");
$title = self::get_TitleInfo_using_title_id($title_id, "FullTitle");
$subjects = self::get_subjects();
$msgs = self::page_editor_msgs();

$citation_and_authors = self::get_bibliographicCitation($title_id, $Page, $title);
$bibliographicCitation = $citation_and_authors['citation'];
$agents = $citation_and_authors['authors2'];

$page_IDs = self::get_page_IDs($Page->ItemID);
// print_r($page_IDs); exit;

?>

<div id="tabs-0">

    <table><tr><td><big><?php echo $title . " " . @$Page->Volume . " " . @$Page->Year; ?></big></td></tr>
    <tr>
    <td><b>Processing Page <?php echo "<a href='http://biodiversitylibrary.org/page/$Page->PageID'>$Page->PageID</a>" ?></b></td>
    <td>
        <form name="" action="index.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="search_type" value="pagesearch">
        Go to another page:
        <select name="page_id" id="">
            <?php foreach($page_IDs as $page_ID)
            {
                $selected = "";
                if($Page->PageID == $page_ID) $selected = "selected";
                echo '<option value="' . $page_ID . '" ' . $selected . '>' . $page_ID . '</option>';
            }?>
        </select>
        <button id="">Go</button>
        </form>
    </td>
    </tr>

    <tr>
    <td>
        <?php 
        $next_page = $Page->PageID + 1;
        if(in_array($next_page, $page_IDs))
        {
            echo "<a href='index.php?page_id=$next_page&search_type=pagesearch'>Skip to next page</a>";
        }
        ?>
    
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
                    <?php foreach($subjects as $s) echo '<option value="' . $s['url'] . '" ' . '>' . $s['t'] . '</option>'; ?>
                </select>
                </td>
            </tr>
            <tr><td><b>Title</b> (optional):</td>
                <td><input size="100" type="text" name="title"></td>
            </tr>
            <tr><td colspan="2" bgcolor="AliceBlue"><?php echo $msgs["title"] ?></td></tr>
            </table>
        </div>
    
        <h2>Text Excerpt for EOL</h2>
        <div>
            <table>
            <tr><td>
                <textarea id="" rows="5" cols="100" name="ocr_text">
                <?php echo self::string_or_object(@$Page->OcrText); ?>
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
                <?php echo $bibliographicCitation; ?>
                </textarea>
            </td></tr>
            </table>
        </div>
    
    </div>


    

</div>
