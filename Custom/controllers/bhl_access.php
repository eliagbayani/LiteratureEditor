<?php
// namespace php_active_record;

class bhl_access_controller //extends ControllerBase
{
    function __construct($params)
    {
        /*
        if($type == 'usercontrib')
        {
            $namespace['ForReview'] = 5000;
            $namespace['Published'] = 0;
            
            $url = $params['server'] . "/LiteratureEditor/api.php?action=query&list=usercontribs&ucuser=" . $params['user'] . "&uclimit=100&ucdir=older&format=json&ucnamespace=" . $namespace[$params['article_type']] . "&ucshow=top";
            $json = Functions::lookup_with_cache($url, array('expire_seconds' => 0));
            $arr = json_decode($json);
            $titles = array();
            foreach($arr->query->usercontribs as $item)
            {
                $titles[] = array('page_title' => $item->title, 'server' => $params['server']);
            }
            $this->body = implode(array_map('bhl_access_controller::render_page_row', $titles));
        }
        */

        $this->bhl_api_service['booksearch']  = "http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=BookSearch&apikey=" . BHL_API_KEY;
        $this->bhl_api_service['itemsearch']  = "http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=GetItemMetadata&pages=t&ocr=t&parts=t&apikey=" . BHL_API_KEY;
        $this->bhl_api_service['titlesearch'] = "http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=GetTitleMetadata&items=t&apikey=" . BHL_API_KEY;
        $this->bhl_api_service['pagetaxasearch']  = "http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=GetPageNames&apikey=" . BHL_API_KEY;
        $this->bhl_api_service['pagesearch']  = "http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=GetPageMetadata&ocr=t&names=t&apikey=" . BHL_API_KEY;
        
        $this->download_options = array('download_timeout_seconds' => 4800, 'download_wait_time' => 300000, 'expire_seconds' => false);
        
    }
    
    
    public static function index()
    {
    }

    function render_template($filename, $variables)
    {
        extract($variables); //makes the array index value to become a variable e.g. array("a" => "dog") becomes $a = "dog";
        ob_start();
        require('../templates/bhl_access/' . $filename . '.php');
        $contents = ob_get_contents(); 
        ob_end_clean();
        return $contents;
    }

    function render_layout($p, $template)
    {
        if($template == 'result')
        {
            if(in_array(@$p['search_type'], array('booksearch', 'itemsearch', 'titlesearch', 'pagetaxasearch', 'pagesearch')))
            {
                $xml = self::search_bhl($p);
                if(    @$p['search_type'] == 'booksearch')      echo self::render_template('booksearch-result', array('xml' => $xml));
                elseif(@$p['search_type'] == 'itemsearch')      echo self::render_template('itemsearch-result', array('xml' => $xml));
                elseif(@$p['search_type'] == 'titlesearch')     echo self::render_template('titlesearch-result', array('xml' => $xml));
                elseif(@$p['search_type'] == 'pagetaxasearch')  echo self::render_template('pagetaxasearch-result', array('xml' => $xml));
                elseif(@$p['search_type'] == 'pagesearch')      echo self::render_template('pagesearch-result', array('xml' => $xml, 'params' => $p));
            }
            else
            {
                // print_r($p);
                // exit("<br>investigate pls.[$template]<br>");
            }
        }
        
        return self::render_template($template, array('book_title' => @$p['book_title'], 'volume' => @$p['volume'], 'lname' => @$p['lname'], 'collectionid' => @$p['collectionid'], 'edition' => @$p['edition'], 'year' => @$p['year'], 'subject' => @$p['subject'], 'language' => @$p['language'],
                                                      'item_id' => @$p['item_id'],
                                                      'title_id' => @$p['title_id'],
                                                      'page_id' => @$p['page_id'],
                                                      'radio' => @$p['radio'],
                                                      'search_type' => @$p['search_type'],
                                                      'pass_title' => @$p['pass_title']
                                                      ));
    }
    
    function search_bhl($p)
    {
        if($val = @$p['search_type']) $url = $this->bhl_api_service[$val];
        else return;
        
        if(@$p['search_type'] == 'booksearch')
        {
            if($val = $p['book_title'])     $url .= "&title=$val";
            if($val = $p['volume'])         $url .= "&volume=$val";
            if($val = $p['lname'])          $url .= "&lname=$val";
            if($val = $p['collectionid'])   $url .= "&collectionid=$val";
            if($val = $p['edition'])        $url .= "&edition=$val";
            if($val = $p['year'])           $url .= "&year=$val";
            if($val = $p['subject'])        $url .= "&subject=$val";
            if($val = $p['language'])       $url .= "&language=$val";
            // $url .= "&format=json";
        }
        elseif(@$p['search_type'] == 'itemsearch')
        {
            if($val = $p['item_id']) $url .= "&itemid=$val";
        }
        elseif(@$p['search_type'] == 'titlesearch')
        {
            if($val = $p['title_id']) $url .= "&titleid=$val";
        }
        elseif(@$p['search_type'] == 'pagetaxasearch')
        {
            if($val = $p['page_id']) $url .= "&pageid=$val";
        }
        elseif(@$p['search_type'] == 'pagesearch')
        {
            if($val = $p['page_id']) $url .= "&pageid=$val";
        }

        
        /*
        $json = Functions::lookup_with_cache($url, array('expire_seconds' => false));
        $arr = json_decode($json, true);
        if($qry_type == 'booksearch') echo self::render_template('booksearch', array('arr' => $arr));
        */

        $xml = Functions::lookup_with_cache($url, array('expire_seconds' => false, 'download_timeout_seconds' => 300)); //timesout in 5 mins. = 300 secs.
        $xml = simplexml_load_string($xml);
        // print_r($xml);
        return $xml;

        /* moved this in render_layout()
        if(@$p['search_type'] == 'booksearch')      echo self::render_template('booksearch-result', array('xml' => $xml));
        if(@$p['search_type'] == 'itemsearch')      echo self::render_template('itemsearch-result', array('xml' => $xml));
        if(@$p['search_type'] == 'titlesearch')     echo self::render_template('titlesearch-result', array('xml' => $xml));
        if(@$p['search_type'] == 'pagetaxasearch')  echo self::render_template('pagetaxasearch-result', array('xml' => $xml));
        if(@$p['search_type'] == 'pagesearch')      echo self::render_template('pagesearch-result', array('xml' => $xml));
        */
    }
    
    function image_with_text($options)
    {
        echo self::render_template('display-image', array('options' => $options));
    }
    
    function get_title_id_using_item_id($item_id)
    {
        $p['search_type'] = 'itemsearch';
        $p['item_id'] = $item_id;
        $xml = self::search_bhl($p);
        return (string) $xml->Result->PrimaryTitleID;
    }
    
    function check_arr($arr)
    {
        if(is_array($arr))
        {
            if($val = $arr)
            {
                print_r($arr);
                return "is arr, not empty, investigate this one...";
            }
            else return ""; //"is arr but empty"; //"is arr but empty";
        }
        else return $arr; //"is not arr";
        
        
    }
    
    function list_titles_by_letter($letter)
    {
        $filename = "../temp/exported_titles/$letter" . "_titles.txt";
        $rows = array();
        if($FILE = Functions::file_open($filename, 'r'))
        {
            while(!feof($FILE))
            {
                $cols = explode("\t", fgets($FILE));
                if(@$cols[0]) $rows[] = $cols;
            }
            fclose($FILE);
        }
        
        $title = array();
        foreach ($rows as $key => $row)
        {
            $title[$key] = @$row[1];
        }
        array_multisort($title, SORT_ASC, $rows);
        return $rows;
    }
    
    
    
    // function render_result($book_title) 
    // {
    //     return bhl_access_controller::render_template('result', array('book_title' => $book_title));
    // }
    

    // function render_page_row($title)
    // {
    //     return bhl_access_controller::render_template('page-row', $title); //$title here is an array value for $titles above
    // }
    // 
    // function render_article_summary($article)
    // {
    //     return bhl_access_controller::render_template('article-summary', $article);
    // }

    function move2wiki($params)
    {
        $filename = "../temp/wiki/" . $params['page_id'] . ".wiki";
        if($file = Functions::file_open($filename, 'w'))
        {
            $go_top = "|+ style=\"caption-side:right;\"|[[Image:arrow-up icon.png|link=#top|Go top]]";
            
            $p['search_type'] = 'pagesearch';
            $p['page_id']     = $params['page_id'];
            $xml = self::search_bhl($p);
            self::write_page_info($xml, $file, $params, $go_top);

            $p['search_type'] = 'itemsearch';
            $p['item_id']     = $params['item_id'];
            $xml = self::search_bhl($p);
            self::write_item_info($xml, $file, $go_top);

            $p['search_type'] = 'titlesearch';
            $p['title_id']     = $params['title_id'];
            $xml = self::search_bhl($p);
            self::write_title_info($xml, $file, $go_top);
            
            fclose($file);
        }
        
        
        $temp_wiki_file = DOC_ROOT . MEDIAWIKI_MAIN_FOLDER . "/Custom/temp/wiki/" . $p['page_id'] . ".wiki";
        // $cmdline = "php -q " . DOC_ROOT . MEDIAWIKI_MAIN_FOLDER . "/maintenance/edit.php -s 'Quick edit' -m " . $p['page_id'] . " < " . $temp_wiki_file;
        $cmdline = "php -q " . DOC_ROOT . MEDIAWIKI_MAIN_FOLDER . "/maintenance/edit.php -s 'BHL data to Wiki' -m " . $params['pass_title'] . " < " . $temp_wiki_file;

        $status = shell_exec($cmdline . " 2>&1");
        $status = str_ireplace("done", "done. ", $status);
        // echo "<br>$cmdline<br>";

        echo "[$status]<br>";
        
        
        /* working also
        $wiki_page = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/" . $p['page_id'];
        echo "<br><a href=\"$wiki_page\">Wiki for Page ID: " . $p['page_id'] . " </a><br>";
        */
        
        $wiki_page = "../../wiki/" . $params['pass_title'];
        echo "<br><a href=\"$wiki_page\">See generated Wiki for Page: " . $params['pass_title'] . " </a><br>";

        // echo "<br>getcwd() = " . getcwd();
        // echo "<br>doc_root = " . $_SERVER['DOCUMENT_ROOT'];
        // echo "<br>doc_root = " . DOC_ROOT;
        // echo "<br>script = " . $_SERVER['SCRIPT_FILENAME'];
        // echo "<br>server = " . $_SERVER['SERVER_NAME'];
    }
    
    function write_page_info($xml, $file, $params, $go_top)
    {
        // http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?page_id=42194842&search_type=pagesearch
        $back = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/Custom/bhl_access/index.php?page_id=" . $xml->Result->PageID . "&search_type=pagesearch";
        $back .= "&subject_type=" . urlencode($params['subject_type']);
        $back .= "&audience_type=" . urlencode($params['audience_type']);
        
       
        fwrite($file, "<span class=\"plainlinks\">[$back Back to BHL API result page]</span>[[Image:Back icon.png|link=$back|Back to BHL API result page]]\n");

        // fwrite($file, "[[Contributing User::{{subst:REVISIONUSER}}]]\n");
        // fwrite($file, "[[Contributing User::{{subst:USERNAME}}]]\n");
        // fwrite($file, "[[Contributing User::{{subst:CURRENTUSER}}]]\n");
        
        $wiki_user = "";
        if(isset($_COOKIE['wiki_literatureeditorUserName'])) $wiki_user = $_COOKIE['wiki_literatureeditorUserName'];
        
        $agent_url = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/User:{$wiki_user}";
        fwrite($file, "<br /><span class=\"plainlinks\">Contributing User: [$agent_url <b>{$wiki_user}</b>]</span>\n");
        
        /*
        fwrite($file, "===Table of Contents===" . "\n");
        fwrite($file, "{| class=\"wikitable\"" . "\n");
        fwrite($file, "| [[#Page Summary|Page Summary]]" . "\n");
        fwrite($file, "|-" . "\n");
        fwrite($file, "| [[#OCR Text|OCR Text]]" . "\n");
        fwrite($file, "|-" . "\n");
        fwrite($file, "| [[#Taxa Found in Page|Taxa Found in Page]]" . "\n");
        fwrite($file, "|-" . "\n");
        fwrite($file, "|}" . "\n");
        */

        
        if($loop = @$xml->Result)
        {
            foreach($loop as $Page)
            {
                $Page_xml = $Page;
                $Page = json_decode(json_encode($Page)); //converting SimpleXMLElement Object to stdClass Object

                //User-defined Title
                fwrite($file, "===User-defined Title (optional)===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"color:green; background-color:#ffffcc;\" name=\"User-defined Title\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . "''enter title here''" . "\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");
                
                if(@$params['licensor'])
                {
                    //Licensor
                    fwrite($file, "===Licensor===\n");
                    fwrite($file, "{| class=\"wikitable\" style=\"color:green; background-color:#ffffcc;\" name=\"Licensor\"\n");
                    fwrite($file, "$go_top\n");
                    fwrite($file, "|" . self::format_wiki($params['licensor'])."\n");
                    fwrite($file, "|-\n");
                    fwrite($file, "|}\n");
                }

                //Subject Type
                fwrite($file, "===Subject Type===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"color:green; background-color:#ffffcc;\" name=\"Subject Type\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($params['subject_type'])."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //Audience Type
                fwrite($file, "===Audience Type===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"color:green; background-color:#ffffcc;\" name=\"Audience Type\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($params['audience_type'])."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //References
                fwrite($file, "===User-defined References (optional)===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"color:green; background-color:#ffffcc;\" name=\"User-defined References\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . "\n");
                fwrite($file, "<ref name=\"ref1\">John's Handbook, Third Edition, Doe-Roe Co., 1972.</ref><!-- Put your reference here or leave it as is. This sample won't be imported -->" . "\n");
                fwrite($file, "<ref name=\"ref2\">[http://www.eol.org Link text], my 2nd sample reference.</ref><!-- Put your reference here or leave it as is. This sample won't be imported -->" . "\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");
                // fwrite($file, "<references/><!-- Put this line \"<references/>\" elsewhere to display the reference or footnote list in that part of the wiki. -->\n");
                fwrite($file, "<!--\n");
                fwrite($file, "Then, if you want to add citation points at any part of your wiki, just enter: e.g.\n\n");
                fwrite($file, "<ref name=\"ref1\"/>\n\n...and this will display the auto-numbered superscripts as link text in that part of the wiki.\n");
                fwrite($file, "-->\n");
                fwrite($file, "<br>\n");
                
                
                
                //page summary
                fwrite($file, "===Page Summary===\n");
                fwrite($file, "{| class=\"wikitable\" name=\"Page Summary\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PageID\n");             fwrite($file, "| $Page->PageID\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ItemID\n");             fwrite($file, "| $Page->ItemID\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Volume\n");             fwrite($file, "| " . @$Page->Volume."\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Issue\n");              fwrite($file, "| " . @$Page->Issue."\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Year\n");               fwrite($file, "| " . @$Page->Year."\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PageUrl\n");            fwrite($file, "| " . @$Page->PageUrl."\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ThumbnailUrl\n");       fwrite($file, "| " . @$Page->ThumbnailUrl."\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| FullSizeImageUrl\n");   fwrite($file, "| " . @$Page->FullSizeImageUrl."\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| OcrUrl\n");             fwrite($file, "| " . @$Page->OcrUrl."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //OCR Text
                fwrite($file, "===OCR Text===\n");
                fwrite($file, "{| class=\"wikitable\" name=\"OCR Text\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($Page->OcrText)."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //taxa
                fwrite($file, "===Taxa Found in Page===\n");
                $total_taxa = count($Page_xml->Names->Name);
                fwrite($file, "Name(s): " . $total_taxa . "<br />\n");
                if($total_taxa)
                {
                    fwrite($file, "{| class=\"wikitable\" name=\"Taxa Found in Page\"\n");
                    fwrite($file, "$go_top\n");
                    fwrite($file, "|-\n");
                    fwrite($file, "! scope=\"col\"|NameBankID   ||! scope=\"col\"|EOLID ||! scope=\"col\"|NameFound ||! scope=\"col\"|NameConfirmed\n");
                    if(count($Page_xml->Names->Name))
                    {
                        foreach($Page_xml->Names->Name as $Name)
                        {
                            fwrite($file, "|-\n");
                            fwrite($file, "|$Name->NameBankID   ||$Name->EOLID  ||$Name->NameFound  ||$Name->NameConfirmed\n");
                        }
                    }
                    fwrite($file, "|-\n");
                    fwrite($file, "|}\n");
                }
                
                //Page Types
                fwrite($file, "===Page Types===\n");
                $total_page_types = count(@$Page->PageTypes->PageType);
                if($total_page_types)
                {
                    fwrite($file, "{| class=\"wikitable\" name=\"Page Types\"\n");
                    fwrite($file, "$go_top\n");
                    fwrite($file, "|-\n");
                    fwrite($file, "! scope=\"col\"|PageTypeName\n");
                    if($total_page_types == 1)
                    {
                        foreach($Page->PageTypes as $PageType)
                        {
                            fwrite($file, "|-\n");
                            fwrite($file, "|" . (string) @$PageType->PageTypeName . "\n");
                        }
                    }
                    elseif($total_page_types > 1)
                    {
                        foreach($Page->PageTypes->PageType as $PageType)
                        {
                            fwrite($file, "|-\n");
                            fwrite($file, "|" . (string) @$PageType->PageTypeName . "\n");
                        }
                    }
                    fwrite($file, "|-\n");
                    fwrite($file, "|}\n");
                }
                
                //Page Numbers
                fwrite($file, "===Page Numbers===\n");
                $total_page_numbers = count(@$Page->PageNumbers->PageNumber);
                if($total_page_numbers)
                {
                    fwrite($file, "{| class=\"wikitable\" name=\"Page Numbers\"\n");
                    fwrite($file, "$go_top\n");
                    fwrite($file, "|-\n");
                    fwrite($file, "! scope=\"col\"|Prefix   ||! scope=\"col\"|Number\n");
                    
                    if($total_page_numbers == 1)
                    {
                        foreach($Page->PageNumbers as $PageNumber)
                        {
                            fwrite($file, "|-\n");
                            fwrite($file, "|" . @$PageNumber->Prefix . "   ||" . @$PageNumber->Number . "\n");
                        }
                    }
                    elseif($total_page_numbers > 1)
                    {
                        foreach($Page->PageNumbers->PageNumber as $PageNumber)
                        {
                            fwrite($file, "|-\n");
                            fwrite($file, "|" . @$PageNumber->Prefix . "   ||" . self::string_or_object(@$PageNumber->Number) . "\n");
                        }
                    }
                    fwrite($file, "|-\n");
                    fwrite($file, "|}\n");
                }
                
            }//foreach loop ends
        }
    }
    
    function write_item_info($xml, $file, $go_top)
    {
        if($loop = @$xml->Result)
        {
            foreach($loop as $item)
            {
                fwrite($file, "===Item Summary===\n");
                fwrite($file, "{| class=\"wikitable\" name=\"Item Summary\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ItemID\n");             fwrite($file, "| $item->ItemID\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PrimaryTitleID\n");     fwrite($file, "| $item->PrimaryTitleID\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ThumbnailPageID\n");    fwrite($file, "| $item->ThumbnailPageID\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Source\n");             fwrite($file, "| $item->Source\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| SourceIdentifier\n");   fwrite($file, "| $item->SourceIdentifier\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Volume\n");             fwrite($file, "| $item->Volume\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Year\n");               fwrite($file, "| $item->Year\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Contributor\n");        fwrite($file, "| $item->Contributor\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Sponsor\n");            fwrite($file, "| $item->Sponsor\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Language\n");           fwrite($file, "| $item->Language\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| LicenseUrl\n");         fwrite($file, "| $item->LicenseUrl\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Rights\n");             fwrite($file, "| $item->Rights\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| DueDiligence\n");       fwrite($file, "| $item->DueDiligence\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| CopyrightStatus\n");    fwrite($file, "| $item->CopyrightStatus\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| CopyrightRegion\n");    fwrite($file, "| $item->CopyrightRegion\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ExternalUrl\n");        fwrite($file, "| $item->ExternalUrl\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ItemUrl\n");            fwrite($file, "| $item->ItemUrl\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| TitleUrl\n");           fwrite($file, "| $item->TitleUrl\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ItemThumbUrl\n");       fwrite($file, "| $item->ItemThumbUrl\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");
            }
        }
    }

    function write_title_info($xml, $file, $go_top)
    {
        if($loop = @$xml->Result)
        {
            foreach($loop as $title)
            {
                fwrite($file, "===Title Summary===\n");
                fwrite($file, "{| class=\"wikitable\" name=\"Title Summary\"\n");
                fwrite($file, "$go_top\n");    
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| TitleID\n");                fwrite($file, "| $title->TitleID\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| BibliographicLevel\n");     fwrite($file, "| $title->BibliographicLevel\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| FullTitle\n");              fwrite($file, "| $title->FullTitle\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| ShortTitle\n");             fwrite($file, "| $title->ShortTitle\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| SortTitle\n");              fwrite($file, "| $title->SortTitle\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PartNumber\n");             fwrite($file, "| $title->PartNumber\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PartName\n");               fwrite($file, "| $title->PartName\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| CallNumber\n");             fwrite($file, "| $title->CallNumber\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| Edition\n");                fwrite($file, "| $title->Edition\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PublisherPlace\n");         fwrite($file, "| $title->PublisherPlace\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PublisherName\n");          fwrite($file, "| $title->PublisherName\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PublicationDate\n");        fwrite($file, "| $title->PublicationDate\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| PublicationFrequency\n");   fwrite($file, "| $title->PublicationFrequency\n");
                fwrite($file, "|-\n");  fwrite($file, "! scope=\"row\"| TitleUrl\n");               fwrite($file, "| $title->TitleUrl\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");
            }
        }
    }



    function format_wiki($wiki)
    {
        $wiki = str_replace(array("\n"), "", $wiki);
        return $wiki;
    }
    
    function check_if_this_title_has_wiki($title)
    {
        // http://editors.eol.localhost/LiteratureEditor/api.php?action=query&titles=9407451&format=json
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/api.php?action=query&prop=revisions&rvprop=content&titles=" . urlencode($title) . "&format=json";
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/api.php?action=query&titles=" . urlencode($title) . "&format=json";
        $json = Functions::lookup_with_cache($url, array('expire_seconds' => true)); //this expire_seconds should always be true
        $arr = json_decode($json, true);
        // echo"<pre>";print_r(@$arr['query']['pages'][-1]); print"</pre>";
        if(@$arr['query']['pages']['-1']) return false;
        else return true;
    }
    
    function get_title_using_title_id($title_id)
    {
        $p['search_type'] = 'titlesearch';
        $p['title_id']     = $title_id;
        $xml = self::search_bhl($p);
        if($val = @$xml->Result->FullTitle) return $val;
        elseif($val = @$xml->Result->ShortTitle) return $val;
    }
    
    function get_CopyrightStatus_using_item_id($item_id, $title)
    {
        $p['search_type'] = 'itemsearch';
        $p['item_id']     = $item_id;
        $xml = self::search_bhl($p);
        if($val = @$xml->Result->CopyrightStatus) return $val;
        
    }
    
    function is_copyrightstatus_Digitized_With_Permission($status)
    {
        if($status == "In copyright. Digitized with the permission of the rights holder.") return true;
        elseif($status == "In copyright. Digitized with the permission of the rights holder") return true;
        else return false;
    }
    
    function get_licensor_for_this_title($title)
    {
        $title_without_ending_period = self::remove_ending_period($title);
        // echo "<br>[$title_without_ending_period]<br>"; exit;
        $licensors = self::generate_licensor_title_list();
        foreach($licensors as $titulo => $licensor) //$licensor is the new copyrightstatus
        {
            if(strtolower($title_without_ending_period) == strtolower($titulo)) return $licensor;
            if(strtolower($title) == strtolower($titulo))                                               return $licensor;
        }
        
        //2nd case: {Copenhagen decisions on zoological nomenclature : additions to, and modifications of, the Règles internationales de la nomenclature zoologique /}
        $temp = explode("/", $title);
        if(count($temp) > 1)
        {
            $partial_title = trim($temp[0]);
            foreach($licensors as $titulo => $licensor) //$licensor is the new copyrightstatus
            {
                if(stripos($titulo, $partial_title) !== false) //string is found
                {
                    return $licensor;
                }
            }
        }
        
        //3rd case: {International code of zoological nomenclature = Code international de nomenclature zoologique /}
        $temp = explode(" = ", $title);
        if(count($temp) > 1)
        {
            $partial_title = trim($temp[0]);
            foreach($licensors as $titulo => $licensor) //$licensor is the new copyrightstatus
            {
                if(stripos($titulo, $partial_title) !== false) //string is found
                {
                    return $licensor;
                }
            }
        }
        
        //4th case: removing first word "The " e.g. {The Journal of the East Africa and Uganda Natural History Society. }
        if(substr($title, 0, 4) == "The ")
        {
            $title2 = trim(substr($title, 4, strlen($title)));
            $title_without_ending_period = self::remove_ending_period($title2);
            
            foreach($licensors as $titulo => $licensor) //$licensor is the new copyrightstatus
            {
                if(strtolower($title2) == strtolower($titulo)) return $licensor;
                if(strtolower($title_without_ending_period) == strtolower($titulo)) return $licensor;
            }
            
        }
        
        //5th case: {Brigham Young University science bulletin.} vs [BYU Science Bulletin, V. 1-20]
        $title2 = str_ireplace("Brigham Young University", "BYU", $title);
        $temp_licensor = explode("Bulletin,", $title2);
        if(count($temp_licensor) > 1)
        {
            $partial_licensor = trim($temp_licensor[0]." Bulletin");
            foreach($licensors as $titulo => $licensor) //$licensor is the new copyrightstatus
            {
                if(stripos($licensor, $partial_licensor) !== false) //string is found
                {
                    return $licensor;
                }
            }
        }
        
        
    }
    
    function generate_licensor_title_list()
    {
        $recs = array();
        $url = "https://docs.google.com/spreadsheets/u/1/d/1ExBu0Q9yLXsYVNzXdIrDYt2Go6blwftAEEb5kJk-dfk/pub?output=html";
        $html = Functions::lookup_with_cache($url, array('expire_seconds' => 86400, 'download_wait_time' => 1000000)); //expires every 24 hours
        if(preg_match_all("/<tr style\=\'height\:1px\;\'>(.*?)<\/tr>/ims", $html, $arr))
        {
            foreach($arr[1] as $t)
            {
                if(preg_match_all("/<td (.*?)<\/td>/ims", $t, $arr2))
                {
                    $a = $arr2[1];
                    $temp1 = explode(">", $a[1]);
                    $temp2 = explode(">", $a[2]);
                    $recs[$temp2[1]] = $temp1[1];
                }
            }
        }
        // echo "\n" . count($recs) . "\n";
        return $recs;
    }
    
    function remove_ending_period($str)
    {
        $str = trim($str);
        if(substr($str,-1) == ".") return trim(substr($str, 0, strlen($str)-1));
        return $str;
    }
    
    function string_or_object($value)
    {
        if(is_string($value)) return (string) $value;
        elseif(is_object($value))
        {
            echo "<br>Investigate:<br>";
            print_r($value);
            return "";
        }
    }
    
}


/*
class dwc_validator_controller //extends ControllerBase
{
    public static function index($parameters)
    {
        extract($parameters);
        
        $errors = array();
        $eol_errors = array();
        $eol_warnings = array();
        $stats = array();
        if(!isset($format)) $format = 'html';
        
        
        $dwca_file = @trim($file_url);
        $suffix = null;
        if(@$dwca_upload['tmp_name'])
        {
            $dwca_file = $dwca_upload['tmp_name'];
            if(preg_match("/\.([^\.]+)$/", $dwca_upload['name'], $arr)) $suffix = strtolower(trim($arr[1]));
        }
        if($dwca_file)
        {
            $validation_hash = ContentArchiveValidator::validate_url($dwca_file, $suffix);
            $errors = isset($validation_hash['errors']) ? $validation_hash['errors'] : null;
            $structural_errors = isset($validation_hash['structural_errors']) ? $validation_hash['structural_errors'] : null;
            $warnings = isset($validation_hash['warnings']) ? $validation_hash['warnings'] : null;
            $stats = isset($validation_hash['stats']) ? $validation_hash['stats'] : null;
        }
        if($format == 'json')
        {
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            $json = array();
            if($structural_errors) $json['status'] = 'invalid';
            elseif($errors) $json['status'] = 'partially valid';
            else $json['status'] = 'valid';
            dwc_validator_controller::add_errors_to_json($structural_errors, $json, 'errors');
            dwc_validator_controller::add_errors_to_json($errors, $json, 'errors');
            dwc_validator_controller::add_errors_to_json($warnings, $json, 'warnings');
            $json['stats'] = $stats;
            echo json_encode($json);
            return;
        }else
        {
            render_template("validator/index", array("file_url" => @$file_url, "file_upload" => @$dwca_upload['name'], "errors" => @$errors, "structural_errors" => @$structural_errors, "warnings" => @$warnings, "stats" => $stats));
        }
    }

    private static function add_errors_to_json($errors, &$json, $index)
    {
        if($errors)
        {
            if(!isset($json[$index])) $json[$index] = array();
            foreach($errors as $error)
            {
                 $error_hash = (array) $error;
                 if(isset($error_hash['line']))
                 {
                     $error_hash['lines'] = explode(",", str_replace(" ", "", $error_hash['line']));
                     unset($error_hash['line']);
                 }
                 $json[$index][] = $error_hash;
            }
        }
    }
}
*/
?>
