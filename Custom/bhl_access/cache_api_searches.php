<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../config/settings.php");
require_once("../lib/Functions.php");
require_once("../controllers/bhl_access.php");

$ctrler = new bhl_access_controller(array());

// cache_title_search($ctrler, $text_file);

/*
$text_file = "http://localhost/cp/MediaWiki/BHL/titleidentifier.txt";
list_cached_title_searches($ctrler, $text_file);
*/

// /*
$text_file = "http://localhost/cp/MediaWiki/BHL/title.txt";
generate_text_files($text_file);
// */

function cache_title_search($ctrler, $text_file)
{
    if($temp_path = Functions::save_remote_file_to_local($text_file, array('cache' => 1, 'download_timeout_seconds' => 4800, 'download_wait_time' => 300000, 'expire_seconds' => false)))
    {
        $file = Functions::file_open($temp_path, "r");
        $first_row = true;
        $i = 0;
        while(!feof($file))
        {
            $cols = explode("\t", fgets($file));
            $title_id = trim($cols[0]);
            if($first_row) 
            {
                $first_row = false;
                continue;
            }
            
            
            $i++;
            // /* breakdown when caching - up to 5 simultaneous connectors
            $m = 50000;
            $cont = false;
            // if($i >=  1    && $i < $m)    $cont = true;
            // if($i >=  $m   && $i < $m*2)  $cont = true;
            // if($i >=  $m*2 && $i < $m*3)  $cont = true;
            if($i >=  $m*3 && $i < $m*4)  $cont = true;
            if(!$cont) continue;
            // */
            
            
            
            echo "\n - [$title_id]";
            
            //search title_id
            $url = $ctrler->bhl_api_service['titlesearch'] . "&titleid=$title_id";
            $xml = Functions::lookup_with_cache($url, array('expire_seconds' => false, 'download_wait_time' => 1000000));
            $xml = simplexml_load_string($xml);
            
            //search item_id
            // if(!in_array($title_id, array(359, 970, 822, 727, 598))) cache_item_ids($xml);
            // continue;
            
            if(!@$xml->Result) continue;
            
            $titles = array();
            $titles[] = trim($xml->Result->FullTitle);
            $titles[] = trim($xml->Result->ShortTitle);
            $titles = array_unique($titles);
            $searched_titles = array();
            // print_r($titles);

            //search book_title
            foreach($titles as $title)
            {
                $arr = explode(" ...", $title);
                $title = trim($arr[0]);
                if(!$title) continue;
                echo "\nsearching [$title]...";
                $searched_titles[] = md5($title);
                $url = $ctrler->bhl_api_service['booksearch'] . "&title=$title";
                $xml = Functions::lookup_with_cache($url, array('expire_seconds' => false, 'download_wait_time' => 500000, 'delay_in_minutes' => 0.5, 'download_attempts' => 1));
                $xml = simplexml_load_string($xml);
                if(@$xml->Result->Title)
                {
                    echo "OK\n";
                    break;
                }
                else
                {
                    echo "failed...try again 1\n";
                    $arr = explode(" /", $title);
                    $title = trim($arr[0]);
                    if(!in_array(md5($title), $searched_titles))
                    {
                        $searched_titles[] = md5($title);
                        
                        if(!$title) continue;
                        echo "\nsearching [$title]...";
                        $url = $ctrler->bhl_api_service['booksearch'] . "&title=$title";
                        $xml = Functions::lookup_with_cache($url, array('expire_seconds' => false, 'download_wait_time' => 500000, 'delay_in_minutes' => 0.5, 'download_attempts' => 1));
                        $xml = simplexml_load_string($xml);
                        if(@$xml->Result->Title)
                        {
                            echo "OK\n";
                            break;
                        }
                        else
                        {
                            echo "failed...try again 2\n";
                            $arr = explode(" :", $title);
                            $title = trim($arr[0]);
                            if(!in_array(md5($title), $searched_titles))
                            {
                                $searched_titles[] = md5($title);
                                
                                if(!$title) continue;
                                echo "\nsearching [$title]...";
                                $url = $ctrler->bhl_api_service['booksearch'] . "&title=$title";
                                $xml = Functions::lookup_with_cache($url, array('expire_seconds' => false, 'download_wait_time' => 500000, 'delay_in_minutes' => 0.5, 'download_attempts' => 1));
                                $xml = simplexml_load_string($xml);
                                if(@$xml->Result->Title)
                                {
                                    echo "OK\n";
                                    break;
                                }
                                else echo "failed\n";
                            }
                            
                        }
                    }

                }
            }
            //search item_id
        }
        fclose($file);
    }
}

function cache_item_ids($xml)
{
    foreach($xml->Result->Items->Item as $Item)
    {
        $url = "http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=GetItemMetadata&pages=t&ocr=t&parts=t&apikey=" . BHL_API_KEY . "&itemid=$Item->ItemID";
        echo "\nsearching [$url]...";
        $xml = Functions::lookup_with_cache($url, array('expire_seconds' => false, 'download_wait_time' => 500000, 'delay_in_minutes' => 0.5, 'download_attempts' => 1));
        $xml = simplexml_load_string($xml);
        if(@$xml->Result->ItemID) echo "OK\n";
        else echo "failed\n";
    }
}


function list_cached_title_searches($ctrler, $text_file)
{
    if($temp_path = Functions::save_remote_file_to_local($text_file, array('cache' => 1, 'download_timeout_seconds' => 4800, 'download_wait_time' => 300000, 'expire_seconds' => false)))
    {
        $folder = "cached_titles";
        $folder = "cached_titles";
        initialize_text_files($folder);
        
        $file = Functions::file_open($temp_path, "r");
        $first_row = true;
        $i = 0;
        while(!feof($file))
        {
            $cols = explode("\t", fgets($file));
            $title_id = trim($cols[0]);
            if($first_row) 
            {
                $first_row = false;
                continue;
            }
            
            
            $i++;
            /* breakdown when caching - up to 5 simultaneous connectors
            $m = 50000;
            $cont = false;
            if($i >=  1    && $i < $m)    $cont = true;
            // if($i >=  $m   && $i < $m*2)  $cont = true;
            // if($i >=  $m*2 && $i < $m*3)  $cont = true;
            // if($i >=  $m*3 && $i < $m*4)  $cont = true;
            if(!$cont) continue;
            */
            
            // echo "\n - [$title_id]"; continue;
            
            //search title_id
            echo "[$title_id]";
            $url = $ctrler->bhl_api_service['titlesearch'] . "&titleid=$title_id";
            $xml = Functions::lookup_with_cache($url, array('expire_seconds' => false, 'download_wait_time' => 1000000));
            $xml = simplexml_load_string($xml);
            
            //search item_id
            // if(!in_array($title_id, array(359, 970, 822, 727, 598))) cache_item_ids($xml);
            // continue;
            
            if(!@$xml->Result) continue;
            
            $titles = array();
            $titles[] = trim($xml->Result->FullTitle);
            $titles[] = trim($xml->Result->ShortTitle);
            $titles = array_unique($titles);
            $searched_titles = array();
            // print_r($titles);

            //search book_title
            foreach($titles as $title)
            {
                $arr = explode(" ...", $title);
                $title = trim($arr[0]);
                if(!$title) continue;
                // echo "\nsearching [$title]...";
                $searched_titles[] = md5($title);
                $url = $ctrler->bhl_api_service['booksearch'] . "&title=$title";
                if(Functions::url_already_cached($url, array('expire_seconds' => false, 'download_wait_time' => 500000, 'delay_in_minutes' => 0.5, 'download_attempts' => 1))) save_title_to_text($title_id, $title, $folder);
                else
                {
                    // echo "failed...try again 1\n";
                    $arr = explode(" /", $title);
                    $title = trim($arr[0]);
                    if(!in_array(md5($title), $searched_titles))
                    {
                        $searched_titles[] = md5($title);
                        if(!$title) continue;
                        // echo "\nsearching [$title]...";
                        $url = $ctrler->bhl_api_service['booksearch'] . "&title=$title";
                        if(Functions::url_already_cached($url, array('expire_seconds' => false, 'download_wait_time' => 500000, 'delay_in_minutes' => 0.5, 'download_attempts' => 1))) save_title_to_text($title_id, $title, $folder);
                        else
                        {
                            // echo "failed...try again 2\n";
                            $arr = explode(" :", $title);
                            $title = trim($arr[0]);
                            if(!in_array(md5($title), $searched_titles))
                            {
                                $searched_titles[] = md5($title);
                                
                                if(!$title) continue;
                                // echo "\nsearching [$title]...";
                                $url = $ctrler->bhl_api_service['booksearch'] . "&title=$title";
                                if(Functions::url_already_cached($url, array('expire_seconds' => false, 'download_wait_time' => 500000, 'delay_in_minutes' => 0.5, 'download_attempts' => 1))) save_title_to_text($title_id, $title, $folder);
                                // else echo "failed\n";
                            }
                        }
                    }
                }
            }
        }
        fclose($file);
    }
}

function save_title_to_text($title_id, $title, $folder, $all_titles = FALSE)
{
    if(!$all_titles)
    {
        $title = trim($title);
        $path1 = "../temp/$folder/";
        $path2 = "_titles.txt";
        $first_letter = strtolower(substr($title, 0, 1));
        $filename = $path1 . $first_letter . $path2;
    }
    else
    {
        $path1 = "../temp/$folder/";
        $path2 = "all_titles.txt";
        $filename = $path1 . $path2;
    }
    
    if($FILE = Functions::file_open($filename, 'a'))
    {
        fwrite($FILE, $title_id."\t".$title . "\n");
        fclose($FILE);
    }
    echo "...cached [$title_id]\n";
}



function initialize_text_files($folder)
{
    $path = "../temp/$folder/";
    $filename = "_titles.txt";
    $letters = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
    $letters = explode(",", $letters);
    foreach($letters as $letter)
    {
        if($FILE = Functions::file_open($path . $letter . $filename, 'w')) fclose($FILE);
    }
}


function generate_text_files($text_file)
{
    if($temp_path = Functions::save_remote_file_to_local($text_file, array('cache' => 1, 'download_timeout_seconds' => 4800, 'download_wait_time' => 300000, 'expire_seconds' => false)))
    {
        $folder = "exported_titles";
        initialize_text_files($folder);
        
        $file = Functions::file_open($temp_path, "r");
        $first_row = true;
        $i = 0;
        while(!feof($file))
        {
            $cols = explode("\t", fgets($file));
            $title_id = trim(@$cols[0]);
            $title    = trim(@$cols[3]);
            
            if(!$title) continue;
            
            if($first_row) 
            {
                $first_row = false;
                continue;
            }
            
            // print_r($cols); exit;
            echo "[$title_id]";
            save_title_to_text($title_id, $title, $folder);
            save_title_to_text($title_id, $title, $folder, true);
            
        }
        fclose($file);
    }
}




/*
function cache_book_search()
{
    $letters = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
    $letters = explode(",", $letters);
    print_r($letters);
    foreach($letters as $letter)
    {
        $url = "http://www.biodiversitylibrary.org/browse/titles/" . $letter . "#/titles";
        $url = "http://www.biodiversitylibrary.org/browse/titles/b#/titles";
        // $url = "http://eol.org";
        echo "\n$url";
        $html = Functions::lookup_with_cache($url, array('download_timeout_seconds' => 4800, 'download_wait_time' => 30000000, 'expire_seconds' => false));
        echo "\n111\n";
        echo $html;
        echo "\n222\n";
        //Titles</span> beginning with "A" (6624)
        if(preg_match("/Titles<\/span> beginning with \"$letter\" \((.*?)\)/ims", $html, $arr))
        {
            echo "\n" . $arr[1];
        }
        exit("\n-stop-\n");
    }
    
}
*/

?>