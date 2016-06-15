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

        $this->id_url['page'] = "http://biodiversitylibrary.org/page/";
        $this->id_url['title'] = "http://www.biodiversitylibrary.org/bibliography/";
        $this->id_url['item'] = "http://www.biodiversitylibrary.org/item/";
        $this->id_url['pagethumb'] = "http://www.biodiversitylibrary.org/pagethumb/";
        $this->id_url['eol'] = "http://www.eol.org/pages/";
        $this->id_url['creator'] = "http://www.biodiversitylibrary.org/creator/";
        
    }

    function user_is_logged_in_wiki()
    {
        $session_cookie = 'wiki_literatureeditor_session';
        if(!isset($_COOKIE[$session_cookie])) return false;

        $path = "http://" . $_SERVER['SERVER_NAME'];
        $url  = $path . "/LiteratureEditor/api.php?action=query&meta=userinfo&format=json";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_COOKIE, $session_cookie . '=' . $_COOKIE[$session_cookie]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $string = curl_exec($ch);
        curl_close($ch);

        /* string possible values:
        Array ( [wiki_literatureeditor_session] => qm1skkoagkoke0pejoke12uti2 ) {"query":{"userinfo":{"id":0,"name":"127.0.0.1","anon":""}}}
        Array ( [wiki_literatureeditor_session] => q1hjhuk9108ufr6l1c6jfmli06 ) {"query":{"userinfo":{"id":1,"name":"EAgbayani"}}}
        */
        if(stripos($string, "\"anon\"") !== false) //string is found
        {
            echo "<h3><p>Cannot proceed.<p>";
            echo "<a href='" . $path . "/LiteratureEditor/wiki/Special:UserLogin'>You must login from the wiki first</a></h3>";
            return false;
        }
        else
        {
            // echo "<p>Login OK.<p>";
            return true;
        }
    }
    
    /*
    public function isLoggedIn()
    {
        // clearstatcache();
        // http://editors.eol.localhost/LiteratureEditor/api.php?action=query&meta=userinfo&format=json
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/LiteratureEditor/api.php?action=query&meta=userinfo&format=json";
        $url = "http://editors.eol.localhost"      . "/LiteratureEditor/api.php?action=query&meta=userinfo&format=json&assert=bot";
        // $url = "http://editors.eol.localhost"      . "/LiteratureEditor/api.php?action=query&meta=userinfo&uiprop=rights|hasmsg&format=json";
        $url = "http://editors.eol.localhost/LiteratureEditor/api.php?action=parse&page=WhoIsLoggedIn&prop=text";
        $url = "http://editors.eol.localhost/LiteratureEditor/api.php?action=query&meta=userinfo&format=json&assert=bot";
        // $url = "http://editors.eol.localhost/LiteratureEditor/wiki/WhoIsLoggedIn";
        // $json = Functions::lookup_with_cache($url, array('expire_seconds' => 0));

        // clearstatcache();
        $json = file_get_contents($url);
        // clearstatcache();
        print($json); exit;
        clearstatcache();
        
        $arr = json_decode($json, true);
        echo "<br>$url<br>";
        print_r($arr);
    }
    */

    public function get_url_by_id($type, $id)
    {
        return "<a href='" . $this->id_url[$type] . "$id'>$id</a>";
    }
    
    public static function index(){}

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
                                                      'pass_title' => @$p['pass_title'],
                                                      'use_cache' => @$p['use_cache']
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
        if(!$xml) $xml = Functions::lookup_with_cache($url, array('expire_seconds' => 1, 'download_timeout_seconds' => 300)); //try again
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
        $status = str_ireplace("done", "done. &nbsp;", $status);
        if(stripos($status, "Your edit was ignored because no change was made to the text") !== false) 
        {
            $status = "Your edit was ignored because no change was made to the text."; //string is found
            $status2 = "See Wiki for Page ID:";
        }
        else $status2 = "See newly generated Wiki for Page ID:";
        
        // echo "<br>$cmdline<br>";
        self::display_message(array('type' => "highlight", 'msg' => $status));
        
        /* working also
        $wiki_page = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/" . $p['page_id'];
        echo "<br><a href=\"$wiki_page\">Wiki for Page ID: " . $p['page_id'] . " </a><br>";
        */
        
        $wiki_page = "../../wiki/" . $params['pass_title'];
        
        self::display_message(array('type' => "highlight", 'msg' => "$status2 <a href=\"$wiki_page\">" . $params['pass_title'] . " </a>"));

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
        $back .= "&license_type=" . urlencode($params['license_type']);
        $back .= "&agents=" . urlencode($params['agents']);
        $back .= "&taxon_names=" . urlencode($params['taxon_names']);
        
        
        fwrite($file, "<span class=\"plainlinks\">[$back Back to BHL API result page]</span>[[Image:Back icon.png|link=$back|Back to BHL API result page]]\n");

        // fwrite($file, "[[Contributing User::{{subst:REVISIONUSER}}]]\n");
        // fwrite($file, "[[Contributing User::{{subst:USERNAME}}]]\n");
        // fwrite($file, "[[Contributing User::{{subst:CURRENTUSER}}]]\n");
        
        $wiki_user = "";
        if(isset($_COOKIE['wiki_literatureeditorUserName'])) $wiki_user = $_COOKIE['wiki_literatureeditorUserName'];
        
        $agent_url = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/wiki/User:{$wiki_user}";
        fwrite($file, "<br /><span class=\"plainlinks\">Contributing User: [$agent_url <b>{$wiki_user}</b>]</span>\n");
        
        $color_green = "color:green; background-color:#ffffcc;";
        
        if($loop = @$xml->Result)
        {
            foreach($loop as $Page)
            {
                $Page_xml = $Page;
                $Page = json_decode(json_encode($Page)); //converting SimpleXMLElement Object to stdClass Object

                // print_r($Page); exit;

                //User-defined Title
                fwrite($file, "===User-defined Title (optional)===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"User-defined Title\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . "''enter title here''" . "\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //Taxa List
                fwrite($file, "===Taxa Found in Page===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"Taxa Found in Page\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . $params['taxon_names']."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");
                
                if(@$params['licensor'])
                {
                    //Licensor
                    fwrite($file, "===Licensor===\n");
                    fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"Licensor\"\n");
                    fwrite($file, "$go_top\n");
                    fwrite($file, "|" . self::format_wiki($params['licensor'])."\n");
                    fwrite($file, "|-\n");
                    fwrite($file, "|}\n");
                }

                //Subject Type
                fwrite($file, "===Subject Type===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"Subject Type\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($params['subject_type'])."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //License Type
                fwrite($file, "===License Type===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"License Type\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($params['license_type'])."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //Bibliographic Citation
                fwrite($file, "===Bibliographic Citation===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"Bibliographic Citation\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($params['bibliographicCitation'])."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //Authors
                fwrite($file, "===Authors===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"Authors\"\n");
                fwrite($file, "$go_top\n");
                $agents = explode("; ", @$params['agents']);
                foreach($agents as $agent)
                {
                    fwrite($file, "|" . self::format_wiki($agent)."\n");
                    fwrite($file, "|-\n");
                }
                fwrite($file, "|}\n");


                //Audience Type
                fwrite($file, "===Audience Type===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"Audience Type\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($params['audience_type'])."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //References
                fwrite($file, "===User-defined References (optional)===\n");
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"User-defined References\"\n");
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
                fwrite($file, "{| class=\"wikitable\" style=\"" . $color_green . "\" name=\"OCR Text\"\n");
                fwrite($file, "$go_top\n");
                fwrite($file, "|" . self::format_wiki($Page->OcrText)."\n");
                fwrite($file, "|-\n");
                fwrite($file, "|}\n");

                //taxa
                fwrite($file, "===Taxa Found in Page (tabular)===\n");
                $total_taxa = count($Page_xml->Names->Name);
                fwrite($file, "Name(s): " . $total_taxa . "<br />\n");
                // if($total_taxa)
                // {
                    fwrite($file, "{| class=\"wikitable\" name=\"Taxa Found in Page (tabular)\"\n");
                    fwrite($file, "$go_top\n");
                    fwrite($file, "|-\n");
                    fwrite($file, "! scope=\"col\"|NameBankID   ||! scope=\"col\"|EOLID ||! scope=\"col\"|NameFound ||! scope=\"col\"|NameConfirmed\n");
                    if($total_taxa)
                    {
                        foreach($Page_xml->Names->Name as $Name)
                        {
                            fwrite($file, "|-\n");
                            fwrite($file, "|$Name->NameBankID   ||$Name->EOLID  ||$Name->NameFound  ||$Name->NameConfirmed\n");
                        }
                    }
                    // else
                    // {
                        /*
                        fwrite($file, "|-\n");
                        fwrite($file, "|NameBankID1   ||EOLID1  ||NameFound1  ||NameConfirmed1 <!-- This is just sample entry, will be ignored. Overwrite to add taxon. -->\n");
                        fwrite($file, "|-\n");
                        fwrite($file, "|NameBankID2   ||EOLID2  ||NameFound2  ||NameConfirmed2 <!-- This is just sample entry, will be ignored. Overwrite to add taxon. -->\n");
                        */
                    // }
                    
                    fwrite($file, "|-\n");
                    fwrite($file, "|}\n");
                    
                    /*
                    fwrite($file, "<!-- Only the field NameConfirmed is required. The other three fields (NameBankID, EOLID, NameFound) are optional. -->" . "\n");
                    */
                    
                // }
                
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
        /* works but only replaces the first char dash.
        if(substr($wiki, 0, 1) == "-") $wiki = "&ndash;" . trim(substr($wiki, 1, strlen($wiki))); //replace first char to &dash; if it is "-" dash.
        */
        $wiki = str_replace("-", "&ndash;", $wiki);
        
        $wiki = str_replace(array("\n"), "", $wiki);
        return $wiki;
    }
    
    function check_if_this_title_has_wiki($title)
    {
        // http://editors.eol.localhost/LiteratureEditor/api.php?action=query&titles=9407451&format=json
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/api.php?action=query&prop=revisions&rvprop=content&titles=" . urlencode($title) . "&format=json";
        // $url = "http://" . $_SERVER['SERVER_NAME'] . "/" . MEDIAWIKI_MAIN_FOLDER . "/api.php?action=query&titles=" . urlencode($title) . "&format=json";
        $json = Functions::lookup_with_cache($url, array('expire_seconds' => true)); //this expire_seconds should always be true
        $arr = json_decode($json, true);
        // echo "<pre>";print_r(@$arr); print_r($json); print"</pre>";
        if(@$arr['query']['pages']['-1']) return false;
        else
        {
            return self::get_url_params_from_wiki(@$arr['query']['pages']);
            return true;
        }
    }
    
    private function get_url_params_from_wiki($arr)
    {
        if(!$arr) return array(); //bit of a hack, should be checked later
        foreach($arr as $rec)
        {
            $str = $rec['revisions'][0]['*'];
            if(preg_match("/\[(.*?) Back to BHL API result page/ims", $str, $arr))
            {
                $str = urldecode($arr[1]);
                if(preg_match("/subject_type=(.*?)\&/ims", $str, $arr))         $final['subject_type']  = $arr[1];
                if(preg_match("/audience_type=(.*?)\&/ims", $str, $arr))        $final['audience_type'] = $arr[1];
                if(preg_match("/license_type=(.*?)\&/ims", $str, $arr))         $final['license_type']  = $arr[1];
                if(preg_match("/agents=(.*?)\&/ims", $str, $arr))               $final['agents']        = $arr[1];
                if(preg_match("/taxon_names=(.*?)xxx/ims", $str."xxx", $arr))   $final['taxon_names']   = $arr[1];

                // echo"<pre>"; print_r($final); echo "</pre>"; //debug
                return $final;
            }
        }
    }
    
    // function get_title_id_using_item_id($item_id) --- obsolete, can be deleted...
    // {
    //     $p['search_type'] = 'itemsearch';
    //     $p['item_id']     = $item_id;
    //     $xml = self::search_bhl($p);
    //     return (string) $xml->Result->PrimaryTitleID;
    // }

    // function get_title_using_title_id($title_id) --- obsolete, can be deleted...
    // {
    //     $p['search_type'] = 'titlesearch';
    //     $p['title_id']     = $title_id;
    //     $xml = self::search_bhl($p);
    //     if($val = @$xml->Result->FullTitle) return $val;
    //     elseif($val = @$xml->Result->ShortTitle) return $val;
    // }

    function get_TitleInfo_using_title_id($title_id, $sought_field)
    {
        $p['search_type'] = 'titlesearch';
        $p['title_id']     = $title_id;
        $xml = self::search_bhl($p);
        
        if($sought_field == "FullTitle")
        {
            if($val = @$xml->Result->FullTitle) return $val;
            elseif($val = @$xml->Result->ShortTitle) return $val;
        }
        if($sought_field == "BibliographicLevel") {if($val = @$xml->Result->BibliographicLevel) return $val;}
        if($sought_field == "all")                {if($val = @$xml->Result) return $val;}
    }
    
    function get_ItemInfo_using_item_id($item_id, $sought_field)
    {
        $p['search_type'] = 'itemsearch';
        $p['item_id']     = $item_id;
        $xml = self::search_bhl($p);
        
        if($sought_field == "copyrightstatus") {if($val = @$xml->Result->CopyrightStatus) return $val;}
        if($sought_field == "license url")     {if($val = @$xml->Result->LicenseUrl) return trim($val);}
        if($sought_field == "PrimaryTitleID")  {if($val = @$xml->Result->PrimaryTitleID) return trim($val);}
        if($sought_field == "pages")           {if($val = @$xml->Result->Pages) return $val;}
        if($sought_field == "all")             {if($val = @$xml->Result) return $val;}
        if($sought_field == "volume")          {if($val = @$xml->Result->Volume) return trim($val);}
    }

    function get_PageInfo_using_page_id($page_id, $sought_field)
    {
        $p['search_type'] = 'pagesearch';
        $p['page_id']     = $page_id;
        $xml = self::search_bhl($p);
        
        // print_r($xml);
        // echo "<br>" . @$xml->Result->OcrText . "<br>";
        
        if($sought_field == "ocr_text")     {if($val = @$xml->Result->OcrText) return (string) $val;}
        if($sought_field == "taxa_names")   {if($val = @$xml->Result->Names) return $val;}
        if($sought_field == "all")          {if($val = @$xml->Result) return $val;}
    }

    function get_separated_names($Names)
    {
        $string = array();
        if(isset($Names->Name->NameConfirmed))
        {
            if($val = self::string_or_object($Names->Name->NameConfirmed)) $string[trim($val)] = '';
        }
        else
        {
            foreach($Names->Name as $Name)
            {
                if($val = self::string_or_object($Name->NameConfirmed)) $string[trim($val)] = '';
            }
        }
        return array_keys($string);
    }

    function get_page_IDs($item_id)
    {
        $page_IDs = array();
        if($xml = self::get_ItemInfo_using_item_id($item_id, "pages"))
        {
            foreach($xml->Page as $Page) $page_IDs[] = (int) $Page->PageID;
            $page_IDs = array_unique($page_IDs);
        }
        return $page_IDs;
    }

    private function get_PartInfo_using_item_id($item_id, $sought_field)
    {
        if($item_info = self::get_ItemInfo_using_item_id($item_id, "all"))
        {
            foreach($item_info as $item)
            {
                return $item->Parts;
            }
        }
    }
    
    private function get_taxa_list($page_id)
    {
        $p['search_type'] = 'pagetaxasearch';
        $p['page_id']     = $page_id;
        $xml = self::search_bhl($p);
        $names = array();
        if($loop = @$xml->Result)
        {
            foreach($loop as $Page)
            {
                if(count($Page->Name))
                {
                    foreach($Page->Name as $Name) $names[(string) $Name->NameConfirmed] = ''; // $Name->NameBankID $Name->EOLID $Name->NameFound
                }
            }
        }
        $names = array_keys($names);
        return implode("; ", $names);
    }
    
    function get_bibliographicCitation($title_id, $Page, $title)
    {
        $item_id     = $Page->ItemID;
        $page_id     = $Page->PageID;
        $PageNumbers = @$Page->PageNumbers;
        
        /* IF the BibliographicLevel of the title is either "Monograph/Item" or "Monographic component part," 
        we should be able to construct the BibliographicCitation from the GetTitleMetadata API like this:   <Authors:Creator, start with the first name and list them all, separating individual names 
        with semicolons>. <PublicationDate>. <FullTitle>. <PublisherName>, <PublisherPlace>.  
        
        Unfortunately, BHL does not provide enough information for an appropriate bibliographic citation for most journal articles (BibliographicLevel of the title is "Serial" or "Serial component part).  
        It looks like the only exceptions to this are articles that are indexed in BioStor.  Data about those appear to be listed in the <Parts> section of the GetItemMetadata response, 
        e.g., GetItemMetadata&itemid=25335 has some. If a wiki excerpt can be tied to one of the articles listed in the <Parts> section, we can use the data there to construct the 
        Bibliographic Citation like this:  <Authors:Creator, start with the first name and list them all, separating individual names with semicolons>. <Date>. <Title>. 
        <ContainerTitle> <Volume>  <Series> (<Issue>):<PageRange>.  
        
        For journal articles that are not covered in the <Parts> section, we'll have to puzzle together a preliminary citation based on the Title and Item metadata, and editors will then have to 
        add article level information manually. Let's do the following.  Text in square brackets is instructions to editors: [Please add authors].  
        <GetItemMetadata:Page:Year>. [Please add article title]. <GetTitleMetadata:FullTitle> <GetItemMetadata:Page:Volume>: [please add page range]. 
        */
        $citation = "";
        $authors = array();  //this is for the authors in bibliographicCitation
        $authors2 = array(); //this is for the list of authors/agents
        
        $rec = self::get_TitleInfo_using_title_id($title_id, "all");
        if(self::bibliographic_level_is_monograph($rec->BibliographicLevel)) // 1st option -- e.g. page_id = 16059324
        {
            foreach($rec->Authors->Creator as $Creator) 
            {
                $authors[] = self::remove_ending_char($Creator->Name);
                $authors2[] = self::remove_ending_char($Creator->Name) . " {" . $Creator->CreatorID . "}";
            }
            $authors = trim(implode("; ", $authors));
            $authors2 = trim(implode("; ", $authors2));
            $citation = self::format_citation_part($authors);
            if($val = @$rec->PublicationDate) $citation .= self::format_citation_part($val);
            if($val = @$rec->FullTitle)       $citation .= self::format_citation_part($val);
            if($val = @$rec->PublisherName)   $citation .= self::format_citation_part($val);
            if($val = @$rec->PublisherPlace)  $citation .= self::format_citation_part($val);
        }
        elseif(self::bibliographic_level_is_journal($rec->BibliographicLevel)) // 2nd option -- e.g. page_id = 6705456
        {
            $page_nos = self::get_page_nos($PageNumbers);
            $part_info = self::get_part_info_for_this_page($page_id, $item_id, $page_nos);

            //these next two lines are taken from 2 respective pages from /templates/:
            $partx = utf8_encode(json_encode($part_info));                                  //taken from itemsearch-result.php
            $Part = json_decode($partx, true); //converts it to array() instead of object   //taken from part-more-info.php
            
            if(isset($Part['Authors']['Creator'][0]))
            {
                $total_authors = count(@$Part['Authors']['Creator']);
                if($total_authors)
                {
                    foreach(@$Part['Authors']['Creator'] as $Creator) 
                    {
                        $authors[] = self::remove_ending_char(self::check_arr(@$Creator['Name']));
                        $authors2[] = self::remove_ending_char(self::check_arr(@$Creator['Name'])) . " {" . $Creator['CreatorID'] . "}";
                    }
                }
            }
            else
            {
                $total_authors = count(@$Part['Authors']);
                if($total_authors)
                {
                    foreach(@$Part['Authors'] as $Creator) 
                    {
                        $authors[] = self::remove_ending_char(self::check_arr(@$Creator['Name']));
                        $authors2[] = self::remove_ending_char(self::check_arr(@$Creator['Name']))  . " {" . $Creator['CreatorID'] . "}";
                    }
                }
            }
            /* <Authors:Creator, start with the first name and list them all, separating individual names with semicolons>. <Date>. <Title>. <ContainerTitle> <Volume>  <Series> (<Issue>):<PageRange>. */
            $authors = trim(implode("; ", $authors));
            $authors2 = trim(implode("; ", $authors2));
            $citation = self::format_citation_part($authors);
            if($val = self::check_arr(@$Part['Date']))           $citation .= self::format_citation_part($val);
            if($val = self::check_arr(@$Part['Title']))          $citation .= self::format_citation_part($val);
            if($val = self::check_arr(@$Part['ContainerTitle'])) $citation .= trim($val)." ";
            if($val = self::check_arr(@$Part['Volume']))         $citation .= trim($val)." ";
            if($val = self::check_arr(@$Part['Series']))         $citation .= trim($val)." ";
            if($val = self::check_arr(@$Part['Issue']))          $citation .= "(".trim($val).")";
            if($val = self::check_arr(@$Part['PageRange']))      $citation .= ":".self::format_citation_part($val);
            
            
            //start of 3rd option -- page_id = 6705246
            $citation = trim($citation);
            if($citation == "." || $citation == "")
            {   /* [Please add authors]. <GetItemMetadata:Page:Year>. [Please add article title]. <GetTitleMetadata:FullTitle> <GetItemMetadata:Page:Volume>: [please add page range]. */
                $citation = "";
                $citation .= "[Please add authors]. ";
                if($val = @$Page->Year) $citation .= self::format_citation_part($val);
                $citation .= "[Please add article title]. ";
                if($val = $title) $citation .= self::format_citation_part($val);
                if($val = @$Page->Volume) $citation .= self::format_citation_part($val);
                $citation .= ": [Please add page range].";
            }
            
        }
        return array("citation" => $citation, "authors" => $authors, "authors2" => $authors2);
    }
    
    private function get_page_nos($PageNumbers)
    {
        $final_page_nos = array();
        
        $valid_page_prefix = array("Page", "Article, Page"); //there can be more in this array, will add as soon as others are discovered...
        $total_page_numbers = count(@$PageNumbers->PageNumber);
        if($total_page_numbers == 1)
        {
            foreach($PageNumbers as $PageNumber)
            {
                foreach($valid_page_prefix as $prefix)
                {
                    if($prefix == (string) @$PageNumber->Prefix) $final_page_nos[(string) @$PageNumber->Number] = '';
                }
            }
        }
        elseif($total_page_numbers > 1)
        {
            foreach($PageNumbers->PageNumber as $PageNumber)
            {
                foreach($valid_page_prefix as $prefix)
                {
                    if($prefix == (string) @$PageNumber->Prefix) $final_page_nos[self::string_or_object(@$PageNumber->Number)] = '';
                }
            }
        }
        $final_page_nos = array_keys($final_page_nos);
        return $final_page_nos;
    }
    
    private function get_part_info_for_this_page($page_id, $item_id, $page_nos)
    {
        $parts                    = array();
        $page_ids_with_parts      = array();
        $part_ids_with_page_range = array();
        $part_info = self::get_PartInfo_using_item_id($item_id, "all");
        foreach($part_info->Part as $Part)
        {
            if($val = (string) $Part->StartPageID) $page_ids_with_parts[$val] = (string) $Part->PartID;
            if($val = (string) $Part->PageRange) $part_ids_with_page_range[(string) $Part->PartID] = $val;
            $parts[(string) $Part->PartID] = $Part;
        }
        // echo "<pre>"; print_r($page_ids_with_parts); print_r($part_ids_with_page_range); print_r($page_nos); echo "</pre>"; //good for debugging
        $final_part_id = false;
        if($val = @$page_ids_with_parts[$page_id]) $final_part_id = $val;
        else
        {
            foreach($part_ids_with_page_range as $part_id => $page_range)
            {
                $range = explode("--", $page_range);
                foreach($page_nos as $page_no)
                {
                    if(in_array($page_no, range($range[0], $range[1]))) $final_part_id = $part_id;
                }
            }
        }
        // echo "<br>this page belongs to part id = [$final_part_id]<br>";
        if($val = $final_part_id) return $parts[$val];
        return false;
    }
    
    private function format_citation_part($part)
    {
        $part = trim($part);
        $chars = array(":", ",", ";", "/", "-"); //remove these ending chars
        foreach($chars as $char)
        {
            if(substr($part, -1) == $char)
            {
                $part = trim(substr($part, 0, strlen($part)-1));
                break;
            }
        }
        //add period if ending char is not period.
        if(substr($part, -1) != ".") $part .= ". ";
        else                         $part .= " ";
        return $part;
    }

    private function remove_ending_char($part)
    {
        $part = trim($part);
        $chars = array(":", ",", ";", "/", "-"); //remove these ending chars
        foreach($chars as $char)
        {
            if(substr($part, -1) == $char)
            {
                $part = trim(substr($part, 0, strlen($part)-1));
                break;
            }
        }
        return $part;
    }
    
    private function bibliographic_level_is_monograph($level) //meaning level is non-journal
    {
        if($level == "Monograph/Item")                              return true;
        if(stripos($level, "Monographic component part") !== false) return true; //string is found
        return false;
    }

    private function bibliographic_level_is_journal($level)
    {
        if($level == "Serial")                                 return true;
        if(stripos($level, "Serial component part") !== false) return true; //string is found
        return false;
    }
    
    function get_license_type($license_url, $copyrightstatus)
    {   /* BHL-GetItemMetadata:LicenseUrl   --  http://ns.adobe.com/xap/1.0/rights/UsageTerms
        Still need to figure out possible values for this. There are some items in BHL that we cannot take, e.g., things that are "in copyright" or "all rights reserved." 
        People should not be able to import text from these items into the wiki. 
        
        Also, I know there are a bunch of items that lack LicenseUrl information.  
        For those, we should use "no known copyright restrictions" as the license, UNLESS their CopyrightStatus is "NOT_IN_COPYRIGHT" in which case we should use "public domain." 
        Can you scope out all possible values of LicenseUrl?
        */
        if(!$license_url)
        {
            if(self::is_not_in_copyright($copyrightstatus)) return "http://creativecommons.org/licenses/publicdomain/";
            else return "no known copyright restrictions";
        }
        else return $license_url;
    }
    
    function is_copyrightstatus_Digitized_With_Permission($status)
    {
        $status = trim($status);
        $sought_status = "In copyright. Digitized with the permission of the rights holder";
        if(stripos($status, $sought_status) !== false) return true; //string is found
        elseif(stripos($status, $sought_status.".") !== false) return true; //string is found
        else return false;
    }
    
    function get_licensor_for_this_title($title)
    {
        //manual
        $title = str_ireplace("Journal / Entomological Exchange and Correspondence Club.", "Journal of the Entomological Exchange and Correspondence Club", $title);
        
        
        $title_without_ending_period = self::remove_ending_period($title);
        // echo "<br>[$title_without_ending_period]<br>"; exit;
        $licensors = self::generate_licensor_title_list();
        foreach($licensors as $titulo => $licensor) //$licensor is the new copyrightstatus
        {
            if(strtolower($title_without_ending_period) == strtolower($titulo)) return $licensor;
            if(strtolower($title) == strtolower($titulo))                       return $licensor;
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
        //6th case: {Newsletter - Hawaiian Botanical Society.} vs [Newsletter -- Hawaii Botanical Society]
        $title2 = false;
        if(stripos($title, "Brigham Young University") !== false) $title2 = str_ireplace("Brigham Young University", "BYU", $title);
        if(stripos($title, " - Hawaiian ") !== false)             $title2 = str_ireplace(" - Hawaiian ", " -- Hawaii ", $title);
        
        if($title2)
        {
            $title_without_ending_period = self::remove_ending_period($title2);
            // echo "<br>[$title][$title2][$title_without_ending_period]<br>";
            foreach($licensors as $titulo => $licensor) //$licensor is the new copyrightstatus
            {
                if(stripos($titulo, $title2) !== false) return $licensor;
                if(stripos($titulo, $title_without_ending_period) !== false) return $licensor;
            }
        }
        
        //manual specific
        if(stripos($title, "Madroño") !== false) return "California Botanical Society";
        
        return false;
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
            if(!$value)
            {
                echo "<br>Investigate:<br>";
                print_r($value);
            }
            return "";
        }
    }
    
    function display_message($options)
    {   //displays Highlight or Error messages
        if($options['type'] == "highlight")
        {
            echo'<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="margin-top: 0px; padding: 0 .7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span><strong>Info:</strong>&nbsp; ' . $options['msg'] . '</p></div></div>';
        }
        elseif($options['type'] == "error")
        {
            echo'<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>&nbsp; ' . $options['msg'] . '</p></div></div>';
        }
    }
    
    function is_in_copyright_OR_all_rights_reserved($status)
    {
        /* Still need to figure out possible values for this. There are some items in BHL that we cannot take, e.g., things that are "in copyright" or "all rights reserved." 
        People should not be able to import text from these items into the wiki. Also, I know there are a bunch of items that lack LicenseUrl information.  
        For those, we should use "no known copyright restrictions" as the license, UNLESS their CopyrightStatus is "NOT_IN_COPYRIGHT" in which case we should use "public domain." 
        Can you scope out all possible values of LicenseUrl?
        */
        $status = strtolower(trim($status));
        $lists = array("in copyright", "all rights reserved");
        foreach($lists as $a) $lists[] = str_replace(" ", "_", $a);
        foreach($lists as $a) $lists[] = $a . ".";
        if(in_array($status, $lists)) return true;
        else return false;
    }
    
    function is_not_in_copyright($status)
    {
        $status = strtolower(trim($status));
        if(!$status) return true; //blank status means NOT IN COPYRIGHT.
        $lists = array("not in copyright", "no longer under copyright", "no copyright restriction");
        foreach($lists as $a) $lists[] = str_replace(" ", "_", $a);
        foreach($lists as $a) $lists[] = $a . ".";
        // print_r($lists);

        //1st test
        if(in_array($status, $lists)) return true;
        
        //2nd test
        foreach($lists as $item)
        {
            if(stripos($status, $item) !== false) return true; //string found
        }
        return false;
    }
    
    function page_editor_msgs()
    {
        return array("intro" => "Use the <b>Skip to next page</b> link to remove the current text excerpt and replace it with the content of the next page.",
        
        "title" => "Please select an <a href='http://eol.org/info/98'>EOL subchapter</a> for the excerpt. You can also enter a title to specify the scope of the excerpt. For example if you map
        an excerpt to the <b>Morphology</b> subchapter, you may want to use <b>Larvae</b> or <b>Morphology of Larvae</b> as the title if the excerpt focuses on the morphology of the larvae. Also, if the
        excerpt represents the original description, please map it to the <b>Diagnostic Description</b> subchapter and add <b>Original Description</b> as the title. Don't use the subchapter title as the title
        since this would lead to title duplication on EOL pages.",
        
        "text_excerpt_pre" => "If your excerpt spans several pages, you can append the content of subsequent pages to the text excerpt using the <b>Add a page</b> button.
        Each time you click this button, the text of the next page in the volume will be added. Please note that, in general,
        excerpts for EOL should be brief. If you have text spanning more than a couple of pages, you should consider breaking it down into multiple excerpts.",
        
        "text_excerpt" => "Remove text that is not part of the targeted excerpt, proofread the remaining text, and fix OCR errors. <b>Please do not change the original text.</b> The excerpt
        should be a faithful transcription of the original work. If you spot an error (e.g. a misspelling) in the original text, you can draw attention to it by adding [sic]
        after the problematic passage. Please do clean up the text by removing page numbers, headers, footers, and other elements that are not part of the targeted
        excerpt. It's also a good idea to dehyphenate the text, i.e., to remove word breaks due to typesetting. You can use HTML to replicate the original text format,
        but please use it sparingly and with caution. Creative use of HTML may lead to display problems when the text is imported to EOL or other applications.
        Recommended tags are &lt;p&gt;&lt;/p&gt; to mark up paragraphs and &lt;m&gt;&lt;/m&gt; to mark up italics.",
        "references" => "If there are any references cited in the excerpt, please add the full bibliographic citations for these works here. Separate individual references by blank lines.
        Unfortunately, there is no way for us to get the references automatically, so you will have to track down the References section of the original work and fetch the relevant references from there.",
        
        "taxon_asso" => "Add a list of taxon names, separated by semicolons. The excerpt will then be placed on these EOL taxon pages. You can copy names from the list below. but
        be aware that not all names found on a page are suitable for taxon associations. Please only enter names if the excerpt provides substantial information that 
        would be a valuable addition to the taxon page. If relevant taxon names are missing, please add them. Also, the names listed in the text may be outdated. If
        EOL has good synonym coverage for a given taxon, outdated names are not a problem, but you may want to check EOL to make sure the names you enter
        are recognized. If not, you may want to do a bit of research to figure out the best names for your taxon associations. While you should not update taxon 
        names in the original text, it's a good idea to do so for the taxon associations.",
        
        "excerpt_meta" => "Most of these fields are automatically populated from information provided by BHL, but for some fields this is difficult or impossible. Please double-check to make sure
        everything looks correct.");
    }
    
    function get_licenses()
    {
        return array(
        array("value" => "http://creativecommons.org/licenses/by/3.0/",       "t" => "CC BY"),
        array("value" => "http://creativecommons.org/licenses/by-nc/3.0/",    "t" => "CC BY NC"),
        array("value" => "http://creativecommons.org/licenses/by-sa/3.0/",    "t" => "CC BY SA"),
        array("value" => "http://creativecommons.org/licenses/by-nc-sa/3.0/", "t" => "CC BY NC SA"),
        array("value" => "http://creativecommons.org/licenses/publicdomain/", "t" => "Puclic Domain"),
        array("value" => "no known copyright restrictions",                   "t" => "no known copyright restrictions"));
    }
    
    function get_languages()
    {
        return array(
        array("name" => "English",           "abb" => "en"),
        array("name" => "Espa&#241;ol",      "abb" => "es"),
        array("name" => "Fran&#231;ais",     "abb" => "fr"),
        array("name" => "German",            "abb" => "de"),
        array("name" => "Portugus-Brasil",   "abb" => "br"),
        array("name" => "Portugus-Portugal", "abb" => "pt"),);
    }
    
    function get_subjects()
    {
        return array(
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#TaxonBiology", "t" => "Overview › Brief Summary"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Description", "t" => "Overview › Comprehensive Description > Description"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#GeneralDescription", "t" => "Overview › Comprehensive Description > General Description"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Biology", "t" => "Overview › Comprehensive Description > Biology"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Distribution", "t" => "Overview › Distribution"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Morphology", "t" => "Physical Description › Morphology"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Size", "t" => "Physical Description › Size"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#DiagnosticDescription", "t" => "Physical Description › Diagnostic Description"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#LookAlikes", "t" => "Physical Description › Look Alikes"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#Development", "t" => "Physical Description › Development"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Habitat", "t" => "Ecology › Habitat"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Migration", "t" => "Ecology › Migration"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Dispersal", "t" => "Ecology › Dispersal"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#TrophicStrategy", "t" => "Ecology › Trophic Strategy"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Associations", "t" => "Ecology › Associations"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Diseases", "t" => "Ecology › Diseases and Parasites"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#PopulationBiology", "t" => "Ecology › Population Biology"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Ecology", "t" => "Ecology › General Ecology"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Behaviour", "t" => "Life History and Behavior › Behavior"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Cyclicity", "t" => "Life History and Behavior › Cyclicity"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#LifeCycle", "t" => "Life History and Behavior › Life Cycle"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#LifeExpectancy", "t" => "Life History and Behavior › Life Expectancy"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Reproduction", "t" => "Life History and Behavior › Reproduction"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Growth", "t" => "Life History and Behavior › Growth"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Evolution", "t" => "Evolution and Systematics › Evolution"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#FossilHistory", "t" => "Evolution and Systematics › Fossil History"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#SystematicsOrPhylogenetics", "t" => "Evolution and Systematics › Systematics or Phylogenetics"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#FunctionalAdaptations", "t" => "Evolution and Systematics › Functional Adaptations"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Physiology", "t" => "Physiology and Cell Biology › Physiology"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Cytology", "t" => "Physiology and Cell Biology › Cell Biology"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Genetics", "t" => "Molecular Biology and Genetics › Genetics"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#Genome", "t" => "Molecular Biology and Genetics › Genome"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#MolecularBiology", "t" => "Molecular Biology and Genetics › Molecular Biology"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#Barcode", "t" => "Molecular Biology and Genetics › Molecular Biology > Barcode"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#ConservationStatus", "t" => "Conservation › Conservation Status"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Conservation", "t" => "Conservation › Conservation"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Trends", "t" => "Conservation › Trends"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Procedures", "t" => "Conservation › Threats > Procedures"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Threats", "t" => "Conservation › Threats"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Management", "t" => "Conservation › Management"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Use", "t" => "Relevance to Humans and Ecosystems › Benefits"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#RiskStatement", "t" => "Relevance to Humans and Ecosystems › Risks"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#Notes", "t" => "Notes"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#Taxonomy", "t" => "Names and Taxonomy › Taxonomy"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#TypeInformation", "t" => "Names and Taxonomy › Type Information"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#EducationResources", "t" => "Education Resources"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#Education", "t" => "Education"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#CitizenScience", "t" => "Citizen Science Links"), 
        array("url" => "http://rs.tdwg.org/ontology/voc/SPMInfoItems#Key", "t" => "Identification Resources > Key"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#IdentificationResources", "t" => "Identification Resources"), 
        array("url" => "http://eol.org/schema/eol_info_items.xml#NucleotideSequences", "t" => "Nucleotide Sequences"));
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
