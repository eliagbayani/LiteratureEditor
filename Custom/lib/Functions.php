<?php
// namespace php_active_record;

class Functions
{
    // Returns true if $string is valid UTF-8 and false otherwise.
    public static function is_utf8($string) {
        return mb_check_encoding($string, 'UTF-8');
    }
    
    public static function is_ascii($string)
    {
        return preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
        )*$%xs', $string);
    }
    
    public function to_camel_case($str)
    {
        $str = str_replace('_', ' ', $str);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);
        return $str;
    }
    
    public static function array_to_xml($array, $prefix = "  ")
    {
        $xml = "";
        
        foreach($array as $key => $val)
        {
            if(is_int($key)) $key = "value";

            if(is_array($val))
            {
                $xml = $prefix."<".$key.">\n";
                $xml .= self::array_to_xml($val, $prefix."  ");
                $xml .= $prefix."</".$key.">\n";
            }else
            {
                $xml .= $prefix."<".$key.">".htmlspecialchars($val)."</".$key.">\n";
            }
        }
        
        return $xml;
    }
    
    public static function log($string)
    {
        if(isset($GLOBALS['log_file']) && $GLOBALS['ENV_NAME'] != 'test')
        {
            fwrite($GLOBALS['log_file'], date('H:i:s m.d.Y').": $string\n");
        }
    }
    
    public static function file_hours_since_modified($path)
    {
        if(!file_exists($path)) return false;
        
        $stat = stat($path);
        $hours = ceil(((time() - $stat['mtime']) / 60) / 60);
        
        return $hours;
    }
    
    public static function grep_processlist($string)
    {
        $count = 0;
        $ps = ''; // Scope.
        if (defined('PS_LITE_CMD')) {
          @exec(PS_LITE_CMD, $ps);
        } else {
          @exec('ps -ef', $ps);
        }
        foreach($ps as $process)
        {
            if(preg_match("/".preg_quote($string, '/')."/", $process)) $count++;
        }
        return $count;
    }
    
    public static function can_this_connector_run($resource_id)
    {
        if(($count = Functions::grep_processlist("$resource_id.php")) > 1)
        {
          error_log("!! ERROR: This connector [$resource_id.php] is already running. Exiting.");
          return false;
        }
        return true;
    }
    
    public static function get_remote_file($remote_url, $options = array())
    {
        if(!isset($options['download_wait_time'])) $options['download_wait_time'] = DOWNLOAD_WAIT_TIME;
        if(!isset($options['timeout'])) $options['timeout'] = DOWNLOAD_TIMEOUT_SECONDS;
        if(!isset($options['download_attempts'])) $options['download_attempts'] = DOWNLOAD_ATTEMPTS;
        if(!isset($options['delay_in_minutes'])) $options['delay_in_minutes'] = false; /* some servers need a few minutes to be revived */
        
        $remote_url = str_replace(" ", "%20", $remote_url);

        $attempts = 1;
        while($attempts <= $options['download_attempts'])
        {
            $file = @self::fake_user_agent_http_get($remote_url, $options);
            usleep($options['download_wait_time']);
            if($file || strval($file) == "0") // e.g. file is valid with value of '0' http://api.gbif.org/v0.9/occurrence/count?taxonKey=4896414
            {
                return $file;
            }

            //debug("attempt $attempts failed, will try again after " . ($options['download_wait_time']/1000000) . " seconds");
            $attempts++;
            
            if($attempts > $options['download_attempts'])
            {
                if($options['delay_in_minutes'])
                {
                    //debug("Will delay for " . $options['delay_in_minutes'] . " minute(s), then will try again. Number of attempts will be reset.");
                    sleep($options['delay_in_minutes'] . 60);
                    $attempts = 1;
                    $options['delay_in_minutes'] = false;
                }
            }
            
        }

        //debug("failed download file after " . ($attempts-1) . " attempts");
        return false;
    }

    public static function get_string_between($str_left, $str_right, $string)
    {
        if(preg_match("/$str_left(.*?)$str_right/ims", $string, $matches)) return trim($matches[1]);
        return;
    }

    public static function format_url($url)
    {
        if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) //"Not valid";
        {
            return $url;
        }
        else //"VALID";
        {
            if(stripos($url, "biodiversitylibrary.org") !== false) return "<a target='bhl' href=\"$url\">$url</a>"; //string is found
            return "<a href=\"$url\">$url</a>";
        }
    }
    
    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        elseif ($bytes >= 1048576) $bytes = number_format($bytes / 1048576, 2) . ' MB';
        elseif ($bytes >= 1024) $bytes = number_format($bytes / 1024, 2) . ' KB';
        elseif ($bytes > 1) $bytes = $bytes . ' bytes';
        elseif ($bytes == 1) $bytes = $bytes . ' byte';
        else $bytes = '0 bytes';
        return $bytes;
    }
    public static function lookup_with_cache($url, $options = array())
    {
        // default expire time is 30 days
        if(!isset($options['expire_seconds'])) $options['expire_seconds'] = 2592000;
        if(!isset($options['timeout'])) $options['timeout'] = 120;
        if(!isset($options['cache_path'])) $options['cache_path'] = CACHE_PATH; //found in /Custom/config/settings.php

        $md5 = md5($url);
        $cache1 = substr($md5, 0, 2);
        $cache2 = substr($md5, 2, 2);

        if($resource_id = @$options['resource_id'])
        {
            $options['cache_path'] .= "$resource_id/";
            if(!file_exists($options['cache_path'])) mkdir($options['cache_path'], 0777);
        }
        
        if(!file_exists($options['cache_path'] . $cache1))           mkdir($options['cache_path'] . $cache1, 0777);
        if(!file_exists($options['cache_path'] . "$cache1/$cache2")) mkdir($options['cache_path'] . "$cache1/$cache2", 0777);
        $cache_path = $options['cache_path'] . "$cache1/$cache2/$md5.cache";
        if(file_exists($cache_path))
        {
            // echo "-cache available-\n";
            $file_contents = file_get_contents($cache_path);
            $cache_is_valid = true;
            if(@$options['validation_regex'] && !preg_match("/". $options['validation_regex'] ."/ims", $file_contents))
            {
                $cache_is_valid = false;
            }
            if(($file_contents && $cache_is_valid) || (strval($file_contents) == "0" && $cache_is_valid))
            {
                $file_age_in_seconds = time() - filemtime($cache_path);
                if($file_age_in_seconds < $options['expire_seconds']) return $file_contents;
                if($options['expire_seconds'] === false) return $file_contents;
            }
            @unlink($cache_path);
        }
        $file_contents = Functions::get_remote_file($url, $options);
        if($FILE = Functions::file_open($cache_path, 'w')) // normal
        {
            fwrite($FILE, $file_contents);
            fclose($FILE);
        }
        else // can happen when cache_path is from external drive with corrupt dir/file
        {
            if(!($h = Functions::file_open("../temp/cant_delete.txt", 'a'))) return;
            fwrite($h, $cache_path . "\n");
            fclose($h);
        }
        return $file_contents;
    }

    public static function url_already_cached($url, $options = array())
    {
        // default expire time is 30 days
        if(!isset($options['expire_seconds'])) $options['expire_seconds'] = 2592000;
        $md5 = md5($url);
        $cache1 = substr($md5, 0, 2);
        $cache2 = substr($md5, 2, 2);
        
        /*
        if(!file_exists(DOC_ROOT . "tmp/cache/$cache1")) return false;
        if(!file_exists(DOC_ROOT . "tmp/cache/$cache1/$cache2")) return false;
        $cache_path = DOC_ROOT . "tmp/cache/$cache1/$cache2/$md5.cache";
        */

        if(!file_exists(CACHE_PATH . "$cache1"))
        {
            mkdir(CACHE_PATH . $cache1, 0777); //added Nov17
            return false;
        }
        if(!file_exists(CACHE_PATH . "$cache1/$cache2"))
        {
            mkdir(CACHE_PATH . "$cache1/$cache2", 0777); //added Nov17
            return false;
        }
        $cache_path = CACHE_PATH . "$cache1/$cache2/$md5.cache";

        
        if(!file_exists($cache_path)) return false;
        
        if($options['expire_seconds'] === false) {}
        else
        {
            $file_age_in_seconds = time() - filemtime($cache_path);
            if($file_age_in_seconds >= $options['expire_seconds']) return false;
        }
        
        if(@$options['validation_regex'])
        {
            $file_contents = file_get_contents($cache_path);
            if(!preg_match("/". $options['validation_regex'] ."/ims", $file_contents)) return false;
        }
        return true;
    }

    public static function get_remote_file_fake_browser($remote_url, $options = array())
    {
        if(!isset($options['download_wait_time'])) $options['download_wait_time'] = DOWNLOAD_WAIT_TIME;
        if(!isset($options['timeout'])) $options['timeout'] = DOWNLOAD_TIMEOUT_SECONDS;
        if(!isset($options['download_attempts'])) $options['download_attempts'] = DOWNLOAD_ATTEMPTS;

        //debug("Grabbing $remote_url: attempt 1: waiting ". $options['download_wait_time']);
        
        $file = @self::fake_user_agent_http_get($remote_url, $options);
        usleep($options['download_wait_time']);
        
        $attempts = 1;
        while(!$file && $attempts < $options['download_attempts'])
        {
            //debug("Grabbing $remote_url: attempt ".($attempts+1));
            
            $file = @self::fake_user_agent_http_get($remote_url, $options);
            usleep($options['download_wait_time']);
            $attempts++;
        }
        //debug("received file");
        
        return $file;
    }
    
    public static function get_hashed_response($url, $options = array())
    {
        $response = self::get_remote_file($url, $options);
        $hash = simplexml_load_string($response);
        return $hash;
    }
    
    public static function get_hashed_response_fake_browser($url, $options = array())
    {
        $response = self::get_remote_file_fake_browser($url, $options);
        $hash = simplexml_load_string($response);
        return $hash;
    }

    public static function save_remote_file_to_local($url, $options = array())
    {
        if(!isset($options['download_wait_time'])) $options['download_wait_time'] = DOWNLOAD_WAIT_TIME;
        if(!isset($options['timeout'])) $options['timeout'] = DOWNLOAD_TIMEOUT_SECONDS;
        if(!isset($options['download_attempts'])) $options['download_attempts'] = DOWNLOAD_ATTEMPTS;
        $temp_path = "../temp/temp.txt";//temp_filepath();
        if(isset($options['file_extension'])) $temp_path .= "." . $options['file_extension'];
        //debug("\n\n Saving remote file: " . $url);
        //debug("\n\n Temporary file: " . $temp_path);
        if(@$options['cache']) $file_contents = self::lookup_with_cache($url, $options);
        else $file_contents = self::get_remote_file($url, $options);
        if($file_contents)
        {
            if(!($file = Functions::file_open($temp_path, "w"))) return;
            fwrite($file, $file_contents);
            fclose($file);
            return $temp_path;
        }
        return false;
    }

    public static function gzip_resource_xml($resource_id)
    {
        $command_line = "gzip -c " . CONTENT_RESOURCE_LOCAL_PATH . $resource_id . ".xml >" . CONTENT_RESOURCE_LOCAL_PATH . $resource_id . ".xml.gz";
        $output = shell_exec($command_line);
    }
    
    public static function count_resource_tab_files($resource_id, $file_extension = ".tab")
    {
        foreach(glob(CONTENT_RESOURCE_LOCAL_PATH . "/$resource_id/*" . $file_extension) as $filename) self::count_rows_from_text_file(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "/" . pathinfo($filename, PATHINFO_BASENAME));
    }

    public static function remove_resource_working_dir($resource_id = false)
    {
        if(!$resource_id) return;
        $working_dir = CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "_working";
        if(is_dir($working_dir)) recursive_rmdir($working_dir);
    }

    public static function count_rows_from_text_file($file)
    {
        //debug("\n counting: [$file]");
        $i = 0;
        if($handle = Functions::file_open($file, "r"))
        {
            while(!feof($handle))
            {
                if($line = fgets($handle)) $i++;
            }
            fclose($handle);
        }
        //debug("\n total: [$i]\n");
        return $i;
    }

    public static function file_open($file_path, $mode)
    {
        if($handle = fopen($file_path, $mode)) return $handle;
        else
        {
            if(chmod($file_path, 0777))
            {
                if($handle = fopen($file_path, $mode)) return $handle;
            }
        }
        self::debug_line("Couldn't open file: [$file_path]");
        return false;
    }

    public static function file_rename($oldname, $newname)
    {
        // return 'eli is here'; //when debugging...
        if($oldname == $newname) return false;
        if(!self::is_within_folders_where_file_change_is_allowed($oldname)) {
            echo("\nCannot allow file change in this folder ($oldname). Check LiteratureEditor -> Functions lib.\n");
            return false;
        }
        
        if(is_file($newname)) unlink($newname);
        elseif(is_dir($newname)) recursive_rmdir($newname);
        
        if(is_dir($oldname))
        {
            if(self::recursive_copy($oldname, $newname)) recursive_rmdir($oldname);
            else return false;
        }
        elseif(is_file($oldname))
        {
            if(copy($oldname, $newname)) unlink($oldname);
            else return false;
        }
        else
        {
            self::debug_line("Source file does not exist: [$oldname]");
            return false;
        }
        return true;
    }
    
    public static function is_within_folders_where_file_change_is_allowed($file)
    {
        $allowed_folders = array('eol_php_code/tmp/', 'eol_php_code/temp/', 'eol_php_code/public/tmp/', 'eol_php_code/applications/content_server/resources/', 
                                 'eol_php_code/applications/content_server/tmp/', 
                                 'Taxonomic_Validation/', 
                                 'Trait_Data_Import/'
                                ); //allowed folders so far; we can add more.
        foreach($allowed_folders as $folder)
        {
            if(strpos($file, $folder) !== false) return true;
        }
        self::debug_line("File change is not allowed here: [$file]");
        return false;
    }
    
    public static function recursive_copy($source_dir, $destination_dir) //copy entire directory
    {
        if(strpos($source_dir, $destination_dir."/") !== false) return false; //cannot recursive_copy if destination is already within source path
        if(strpos($destination_dir, $source_dir."/") !== false) return false; //cannot recursive_copy if source is already within destination path

        if($dir = opendir($source_dir))
        {
            if(!self::is_within_folders_where_file_change_is_allowed($destination_dir)) return false;
            @mkdir($destination_dir); 
            while(false !== ($file = readdir($dir)))
            { 
                if(($file != '.') && ($file != '..'))
                { 
                    if(is_dir($source_dir . '/' . $file) )
                    {
                        if(!self::recursive_copy($source_dir.'/'.$file, $destination_dir.'/'.$file)) return false;
                    }
                    else
                    {
                        if(!copy($source_dir."/".$file, $destination_dir."/".$file))
                        {
                            self::debug_line("Copy file failed. source:[$source_dir/$file] destination:[$destination_dir/$file]");
                            return false;
                        }
                    }
                } 
            } 
            closedir($dir);
            return true;
        }
        self::debug_line("Permission restriction or filesystem error for: [$source_dir]");
        return false;
    }

    public static function debug_line($msg)
    {
        $callers = array_reverse(debug_backtrace());
        foreach($callers as $caller)
        {
            //debug($caller['file'] . ":" . $caller['line']);
            echo ($caller['file'] . ":" . $caller['line']);
            
        }
        //debug($msg);
        echo ($msg);
        
    }
    
    public static function finalize_dwca_resource($resource_id)
    {
        if(filesize(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "_working/taxon.tab") > 1000)
        {
            if(is_dir(CONTENT_RESOURCE_LOCAL_PATH . $resource_id))
            {
                recursive_rmdir(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "_previous");
                Functions::file_rename(CONTENT_RESOURCE_LOCAL_PATH . $resource_id, CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "_previous");
            }
            Functions::file_rename(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "_working", CONTENT_RESOURCE_LOCAL_PATH . $resource_id);
            Functions::file_rename(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "_working.tar.gz", CONTENT_RESOURCE_LOCAL_PATH . $resource_id . ".tar.gz");
            Functions::set_resource_status_to_force_harvest($resource_id);
            Functions::count_resource_tab_files($resource_id);
            if($undefined_uris = Functions::get_undefined_uris_from_resource($resource_id)) print_r($undefined_uris);
            echo "\nUndefined URIs: " . count($undefined_uris) . "\n";
            require_library('connectors/DWCADiagnoseAPI');
            $func = new DWCADiagnoseAPI();
            $func->check_unique_ids($resource_id);
        }
    }
    
    public static function get_undefined_uris_from_resource($resource_id)
    {
        $undefined_uris = array();
        $defined_uris = self::get_eol_defined_uris();
        // check the measurement_or_fact.tab
        $url = CONTENT_RESOURCE_LOCAL_PATH . $resource_id . "/measurement_or_fact.tab";
        if(!file_exists($url))
        {
            echo "\nFile does not exist: [$url]\n";
            return $undefined_uris;
        }
        $i = 0;
        $exclude = array("http://rs.tdwg.org/dwc/terms/georeferenceRemarks");
        foreach(new FileIterator($url) as $line_number => $temp)
        {
            $temp = explode("\t", $temp);
            $i++;
            if($i == 1) $fields = $temp;
            else
            {
                $rec = array();
                $k = 0;
                if(!$temp) continue;
                foreach($temp as $t)
                {
                    $rec[$fields[$k]] = $t;
                    $k++;
                }
                if($val = @$rec['measurementType'])
                {
                    if(substr($val,0,4) == "http") $uris[$val] = '';
                }
                if($val = @$rec['measurementValue'])
                {
                    if(!in_array(@$rec['measurementType'], $exclude))
                    {
                        if(substr($val,0,4) == "http") $uris[$val] = '';
                    }
                }
            }
        }
        foreach(array_keys($uris) as $uri)
        {
            if(!isset($defined_uris[$uri])) $undefined_uris[$uri] = '';
        }
        return $undefined_uris;
    }

    public static function get_eol_defined_uris($download_options = false)
    {
    }

    // see http://www.php.net/manual/en/function.filesize.php#92462
    public static function remote_file_size($uri)
    {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data === false) return null;
        
        $content_length = null;
        if(preg_match('/Content-Length: (\d+)/', $data, $matches))
        {
            $content_length = ((int) $matches[1]) / 1024;
        }
        
        return $content_length;
    }
    
    public static function ping($uri)
    {
        return (self::remote_file_size($uri) !== null);
    }
    
    public static function curl_post_request($url, $parameters_array = array())
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        if(isset($parameters_array) && is_array($parameters_array)) curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters_array);
        
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        
        //debug("Sending post request to $url with params ".print_r($parameters_array, 1).": only attempt");
        $result = curl_exec($ch);
        
        if(0 == curl_errno($ch))
        {
            curl_close($ch);
            return $result;
        }
        echo "Curl error ($url): " . curl_error($ch);
        return false;
    }
    
    // NOTE - This isn't a fake get, it's a fake user agent.  ;)
    public static function fake_user_agent_http_get($url, $options = array())
    {
        if(!isset($options['timeout'])) $options['timeout'] = DOWNLOAD_TIMEOUT_SECONDS;
        if(substr($url, 0, 1) == "/") $url = "file://" . $url;

        $agent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";
        $ch = curl_init();
        if(isset($options['user_agent'])) curl_setopt($ch, CURLOPT_USERAGENT, $options['user_agent']);
        else curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        if(isset($options['encoding'])) curl_setopt($ch, CURLOPT_ENCODING, $options['encoding']);

        // ignores and just trusts https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        //debug("Sending get request to $url : only attempt");
        $result = curl_exec($ch);
        
        if(0 == curl_errno($ch))
        {
            curl_close($ch);
            return $result;
        }
        //debug("Curl error ($url): " . curl_error($ch));
        return false;
    }
    
    
    public static function cmp_hierarchy_entries($a, $b)
    {
        if ($a->name->string == $b->name->string) return 0;
        return ($a->name->string < $b->name->string) ? -1 : 1;
    }
    
    public static function cmp_references($a, $b)
    {
        if ($a->fullReference == $b->fullReference) return 0;
        
        return ($a->fullReference < $b->fullReference) ? -1 : 1;
    }
    
    public static function cmp_references_arkive($a, $b)
    {
        if ($a->fullReference == $b->fullReference) return 0;
        
        $match1 = $a->fullReference;
        $match2 = $b->fullReference;
        
        if(preg_match("/^([0-9]+)/",$a->fullReference,$arr)) $match1 = $arr[1];
        if(preg_match("/^([0-9]+)/",$b->fullReference,$arr)) $match2 = $arr[1];
        
        return ($match1 < $match2) ? -1 : 1;
    }
    
    public static function cmp_nodes($a, $b)
    {
        if ($a->name == $b->name) return 0;
        
        return ($a->name < $b->name) ? -1 : 1;
    }
    
    public static function canonical_form($string)
    {
        self::sci_parts();
        self::author_parts();
        self::junk_parts();
        
        $string = str_replace('&times;', '×', $string);
        if(preg_match("/^X (.*)$/i",$string,$arr)) $string = $arr[1];
        $string = str_ireplace(" tipo veneto","",$string);
        $string = str_replace("×"," ",$string);
        $string = preg_replace("/[[:space:]]+/", " ", trim($string));
        $string = preg_replace("/[[:space:]]/", " ", trim($string));
        $words = explode(" ", trim($string));
        $num = count($words);
        if(preg_match("/^\??\"?\[?\(?([^\"\[\]\(\)]*)\)?\]?\"?$/",$words[0],$arr)) $words[0] = $arr[1];
        if(preg_match("/^(.*)\?$/",$words[0],$arr)) $words[0] = $arr[1];
        if($words[0]=="Not") return "";
        $words[0] = str_replace("[","",$words[0]);
        $words[0] = str_replace("]","",$words[0]);
        $words[0] = preg_replace("/{\?}/","",$words[0]);
        $words[0] = preg_replace("/\{[0-9\. ]*\}/","",$words[0]);
        if(preg_match("/^[^".UPPER.LOWER."]*([".UPPER.LOWER."]*)[^".UPPER.LOWER."]*$/u",$words[0],$arr)) $words[0] = $arr[1];
        $words[0] = str_replace("[","",$words[0]);
        $words[0] = str_replace("]","",$words[0]);
        $return_string = $words[0];
        if(@preg_match("/^([".LOWER."].*)\)$/u",$words[1],$arr))
        {
            $words[1] = $arr[1];
            if(preg_match("/^(.*)\?$/",$words[1],$arr)) $words[1] = $arr[1];
            if(preg_match("/^[^".UPPER.LOWER."]*([".UPPER.LOWER."]*)[^".UPPER.LOWER."]*$/u",$words[1],$arr)) $words[1] = $arr[1];
            $return_string.=" $words[1]";
            return $return_string;
        }
        
        for($i=1 ; $i<$num ; $i++)
        {
            if(preg_match("/^[".UPPER."\(]/u",$words[$i])) continue;
            if(preg_match("/[0-9]/",$words[$i]) && !preg_match("/^[1-2]?[0-9]?\-?[".LOWER."]+$/u",$words[$i])) continue;
            
            if(preg_match("/^[^0-9".UPPER.LOWER."]*([0-9".UPPER.LOWER."]*)[^0-9".UPPER.LOWER."]*$/u",$words[$i],$arr)) $words[$i] = $arr[1];
            
            if(preg_match("/[".UPPER."]/u",$words[$i])) continue;
            if(preg_match("/\[/u", $words[$i])) continue;
            if(preg_match("/.\../u",$words[$i]) && !preg_match("/st\.-/u",$words[$i])) continue;
            if(!preg_match("/[[:alpha:]]/u",$words[$i])) continue;
            
            if(@$GLOBALS["SCI_PARTS"][$words[$i]] || @$GLOBALS["AUTHOR_PARTS"][$words[$i]] || @$GLOBALS["JUNK_PARTS"][$words[$i]]) continue;
            
            if(strlen($words[$i])>1) $return_string.=" $words[$i]";
        }
        
        unset($string);
        unset($words);
        return trim($return_string);
    }
    
    public static function ranked_canonical_form($string)
    {
        require_library('RubyNameParserClient');
        $string = str_replace('&times;', '×', $string);
        if(!isset($GLOBALS['NAME_PARSER_CLIENT'])) $GLOBALS['NAME_PARSER_CLIENT'] = new RubyNameParserClient();
        return $GLOBALS['NAME_PARSER_CLIENT']->lookup_string($string);
    }
    
    public static function italicized_form($string)
    {
        $canonical_form = self::canonical_form($string);
        
        $words = explode(" ",$canonical_form);
        
        foreach($words as $w)
        {
            $string = preg_replace("/(^|[^0-9".UPPER.LOWER."])".preg_quote($w, "/")."([^0-9".UPPER.LOWER."]|$)/","\\1|-n-|".$w."|-/n-|\\2",$string);
        }
        unset($words);
        
        while(preg_match("/\|-\/n-\| \|-n-\|/",$string,$arr)) $string = str_replace("|-/n-| |-n-|"," ",$string);
        
        $string = str_replace("|-n-|","<i>",$string);
        $string = str_replace("|-/n-|","</i>",$string);
        $string = str_replace("<i><i>", "<i>", $string);
        $string = str_replace("</i></i>", "</i>", $string);
        $string = Functions::fix_italics($string);
        unset($canonical_form);
        return $string;
    }
    
    public static function fix_italics($italicized)
    {
        $modified_italicized = $italicized;
        $modified_italicized = str_replace("<i><i>", "<i>", $modified_italicized);
        $modified_italicized = str_replace("<i><i>", "<i>", $modified_italicized);
        $modified_italicized = str_replace("</i></i>", "</i>", $modified_italicized);
        $modified_italicized = str_replace("</i></i>", "</i>", $modified_italicized);
        if(preg_match_all("/(<i>(.*?))<i>/", $modified_italicized, $matches, PREG_SET_ORDER))
        {
            foreach($matches as $match)
            {
                if(strpos($match[0], "</i>") === false)
                {
                    $modified_italicized = str_replace($match[0], $match[1], $modified_italicized);
                }
            }
        }
        if(preg_match_all("/<\/i>((.*?)<\/i>)/", $modified_italicized, $matches, PREG_SET_ORDER))
        {
            foreach($matches as $match)
            {
                if(strpos($match[0], "<i>") === false)
                {
                    $modified_italicized = str_replace($match[0], $match[1], $modified_italicized);
                }
            }
        }
        return $modified_italicized;
    }
    
    public static function class_name($path)
    {
        if(SYSTEM_OS == "Windows")
        {
            if(preg_match("/\\\([^\\\]+)\.php$/", $path, $arr)) $path = $arr[1];
        }elseif(preg_match("/\/([^\/]+)\.php$/", $path, $arr)) $path = $arr[1];
        return strtolower(preg_replace("/([a-z])([A-Z])/", "\\1_".strtolower("\\2"), $path));
    }
    
    public static function generate_guid()
    {
        return md5(uniqid("eol".rand(), true));
    }
    
    public static function mock_object($class, $params)
    {
        $mock_object = new $class(false);
        foreach($params as $k => $v)
        {
            $mock_object->$k = $v;
        }
        unset($params);
        
        return $mock_object;
    }
    
    public static function get_last_function($index)
    {
        if(!$index) $index = 2;
        $backtrace = debug_backtrace();
        return @$backtrace[$index]['class'].": ".@$backtrace[$index]['function'];
    }
    
    public static function stacktrace()
    {
        return "<pre>".print_r(debug_backtrace(), 1)."</pre>";
    }
    
    
    
    public static function common_names_are_same($cn1, $cn2)
    {
        if(!$cn1 && !$cn2) return true;
        if(!$cn1 || !$cn2) return false;
        
        $common_names_1 = array();
        foreach($cn1 as $k) 
        {
            $common_names_1[] = $k->common_name."|".$k->language_id;
        }
        
        $common_names_2 = array();
        foreach($cn2 as $k)
        {
            $common_names_2[] = $k->common_name."|".$k->language_id;
        }
        
        if (array_diff($common_names_1, $common_names_2)) return false;
        return true;
    }
    
    public static function references_are_same($refs1, $refs2)
    {
        if(!$refs1 && !$refs2) return true;
        if(!$refs1 || !$refs2) return false;
        
        $references_1 = array();
        foreach($refs1 as $k)
        {
            $references_1[] = $k->id;
        }
        
        $references_2 = array();
        foreach($refs2 as $k)
        {
            $references_2[] = $k->id;
        }
        
        if (array_diff($references_1, $references_2)) return false;
        return true;
    }
    
    public static function agents_are_same($agents1, $agents2)
    {
        $agents_1 = array();
        foreach($agents1 as $k)
        {
            $agents_1[] = $k->full_name;
        }
        
        $agents_2 = array();
        foreach($agents2 as $k)
        {
            $agents_2[] = $k->full_name;
        }
        
        if (array_diff($agents_1, $agents_2)) return false;
        return true;
    }

    public static function get_accesspoint_url_if_available($resource_id, $backup_accesspoint_url)
    {
        /* this will use if partner submits an accesspoint_url, othwerwise will use a hard-coded version of it */
        $mysqli =& $GLOBALS['mysqli_connection'];
        $result = $mysqli->query("SELECT accesspoint_url FROM resources WHERE id=" . $resource_id);
        $row = $result->fetch_row();
        $new_resource_path = trim(@$row[0]);
        if($backup_accesspoint_url != $new_resource_path && $new_resource_path) return $new_resource_path;
        else return $backup_accesspoint_url;
    }
    
    public static function create_fixture($table)
    {
        $mysqli =& $GLOBALS['mysqli_connection'];
        
        $return = "";
        
        $i = 1;
        $result = $mysqli->query("SELECT * FROM $table");
        while($result && $row=$result->fetch_assoc())
        {
            $return .= $table."_$i:\n";
            foreach($row as $k => $v)
            {
                if($v != '') $return .= "    $k: $v\n";
            }
            $return .= "\n";
            
            $i++;
        }
        
        return $return;
    }

    public static function load_fixtures($environment = "test")
    {
        if($GLOBALS['ENV_NAME']!=$environment) return false;
        
        $mysqli =& $GLOBALS['mysqli_connection'];
        
        $files = Functions::get_fixture_files();
        
        $fixture_data = (object) array();
        
        $mysqli->begin_transaction();
        foreach($files as $table)
        {
            $fixture_data->$table = (object) array();
            
            $rows = Spyc::YAMLLoad(DOC_ROOT."tests/fixtures/$table.yml");
            foreach($rows as $id => $row)
            {
                $fixture_data->$table->$id = (object) array();
                
                $query = "INSERT INTO $table (`";
                $query .= implode("`, `", array_keys($row));
                $query .= "`) VALUES ('";
                $query .= implode("', '", $row);
                $query .= "')";
                
                $mysqli->insert($query);
                
                foreach($row as $k => $v)
                {
                    $fixture_data->$table->$id->$k = $v;
                }
            }
        }
        $mysqli->end_transaction();
        
        return $fixture_data;
    }
    
    public static function get_files_in_dir($dir)
    {
        $files = array();

        if($handle = opendir($dir))
        {
           while(false !== ($file = readdir($handle)))
           {
               if(substr($file, 0, 1) != "." && $file != '__MACOSX')
               {
                   $files[] = $file;
               }
           }
           closedir($handle);
        }

        return $files;
    }
    
    public static function get_single_xml_file_in_directory($dir)
    {
        $files = Functions::get_files_in_dir($dir);
        $single_xml_path = null;
        foreach($files as $file)
        {
            if($single_xml_path) return null;
            if(preg_match("/\.xml$/i", $file))
            {
                $single_xml_path = $dir."/".$file;
            }else return null;
        }
        return $single_xml_path;
    }
    
    public static function get_fixture_files()
    {
        $files = array();
        
        $dir = DOC_ROOT."tests/fixtures/";
        $files_in_dir = self::get_files_in_dir($dir);
        foreach($files_in_dir as $file)
        {
           if(preg_match("/^(.*)\.yml$/",trim($file), $arr))
           {
               $files[] = $arr[1];
           }
        }
        
        return $files;
    }
    
    public static function require_classes_from_dir($dir, $recursive = false)
    {
        if($handle = opendir($dir))
        {
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != "..")
                {
                    if(preg_match("/\.php$/", trim($file))) require_once($dir . $file);
                    elseif($recursive && !preg_match("/\./", $file) && $file != "modules" && $file != "TDWG") self::require_classes_from_dir($dir . $file . "/", true);
                }
            }
            closedir($handle);
        }
    }
    
    public static function import_decode($string, $remove_shitespace = false, $decode = true)
    {
        if($decode)
        {
            $string = str_replace('&nbsp;',  ' ', $string);
            $string = htmlspecialchars_decode(html_entity_decode($string, ENT_COMPAT, 'UTF-8'));
        }
        
        $string = str_replace(" ", " ", $string);
        //utf-8 0x0A (nobreak space) does not get inserted into mysql properly, we change it back to &nbsp; 
        //$string = str_replace("\xA0", "&nbsp;", $string);
        $string = str_replace("\x0A", "&nbsp;", $string);
        
        if($remove_shitespace) $string = self::remove_whitespace($string);
        return trim($string);
    }
    
    public static function remove_whitespace($string)
    {
        while(preg_match("/  /",$string)) $string = trim(str_replace("  "," ",$string));
        return trim($string);
    }
    
    // from http://www.php.net/manual/en/function.in-array.php#89256
    public static function array_searchi($needle, $haystack)
    {
        if(!is_array($haystack)) return null;
        $needle = strtolower($needle);
        foreach($haystack as $key => $value)
        {
            if(strtolower($value) == $needle) return $key;
        }
        return null;
    }
    
    public static function utf8_to_ascii($nameString)
    {
        // source code at http://us3.php.net/manual/en/function.iconv.php#93609
        
        $r = '';
        $s1 = @iconv('UTF-8', 'ASCII//TRANSLIT', $nameString);
        $j = 0;
        for ($i = 0; $i < strlen($s1); $i++) {
            $ch1 = $s1[$i];
            $ch2 = substr($nameString, $j++, 1);
            if (strstr('`^~\'"', $ch1) !== false) {
                if ($ch1 <> $ch2) {
                    --$j;
                    continue;
                }
            }
            $r .= ($ch1=='?') ? $ch2 : $ch1;
        }
        return $r;
    }
    
    public static function clean_name($name)
    {
        $name = str_replace("."," ",$name);
        $name = str_replace(","," ",$name);
        $name = str_replace(";"," ",$name);
        $name = str_replace("-"," - ",$name);
        $name = str_replace("("," ( ",$name);
        $name = str_replace(")"," ) ",$name);
        $name = str_replace("["," [ ",$name);
        $name = str_replace("]"," ] ",$name);
        $name = str_replace("{"," { ",$name);
        $name = str_replace("}"," } ",$name);
        $name = str_replace(":"," : ",$name);
        $name = str_replace("&"," & ",$name);
        $name = str_replace("?"," ? ",$name);
        $name = str_replace("*"," * ",$name);
        $name = str_replace("&times;"," × ",$name);
        $name = str_replace("×"," × ",$name);
        $name = str_replace(" and "," & ",$name);
        $name = str_replace(" et "," & ",$name);
        while(preg_match("/  /",$name)) $name = str_replace("  "," ",$name);
        $name = preg_replace("/([A-Z])/e","''.strtolower('\\1').''",$name);
        
        $name = str_replace("À","à",$name);
        $name = str_replace("Â","â",$name);
        $name = str_replace("Å","å",$name);
        $name = str_replace("Ã","ã",$name);
        $name = str_replace("Ä","ä",$name);
        $name = str_replace("Á","á",$name);
        $name = str_replace("Æ","æ",$name);
        $name = str_replace("C","c",$name);
        $name = str_replace("Ç","ç",$name);
        $name = str_replace("Č","č",$name);
        $name = str_replace("É","é",$name);
        $name = str_replace("È","è",$name);
        $name = str_replace("Ë","ë",$name);
        $name = str_replace("Í","í",$name);
        $name = str_replace("Ì","ì",$name);
        $name = str_replace("Ï","ï",$name);
        $name = str_replace("Ň","ň",$name);
        $name = str_replace("Ñ","ñ",$name);
        $name = str_replace("Ñ","ñ",$name);
        $name = str_replace("Ó","ó",$name);
        $name = str_replace("Ò","ò",$name);
        $name = str_replace("Ô","ô",$name);
        $name = str_replace("Ø","ø",$name);
        $name = str_replace("Õ","õ",$name);
        $name = str_replace("Ö","ö",$name);
        $name = str_replace("Ú","ú",$name);
        $name = str_replace("Ù","ù",$name);
        $name = str_replace("Ü","ü",$name);
        $name = str_replace("R","r",$name);
        $name = str_replace("Ŕ","ŕ",$name);
        $name = str_replace("Ř","ř",$name);
        $name = str_replace("Ŗ","ŗ",$name);
        $name = str_replace("Š","š",$name);
        $name = str_replace("Š","š",$name);
        $name = str_replace("Ş","ş",$name);
        $name = str_replace("Ž","ž",$name);
        $name = str_replace("Œ","œ",$name);
        return trim($name);
    }
    
    public static function author_parts()
    {
        if(@!$GLOBALS["AUTHOR_PARTS"])
        {
            $array = array();
            $array["d'"]=true;
            $array["der"]=true;
            $array["du"]=true;
            $array["den"]=true;
            $array["le"]=true;
            $array["la"]=true;
            $array["de"]=true;
            $array["da"]=true;
            $array["del"]=true;
            $array["delle"]=true;
            $array["della"]=true;
            $array["des"]=true;
            $array["van"]=true;
            $array["von"]=true;
            $array["y"]=true;
            
            $GLOBALS["AUTHOR_PARTS"] = $array;
        }
    }
    
    public static function sci_parts()
    {
        if(@!$GLOBALS["SCI_PARTS"])
        {
            $array = array();
            $array["unranked"]=true;
            $array["susbsp"]=true;
            $array["lus"]=true;
            $array["sf"]=true;
            $array["subv"]=true;
            $array["susp"]=true;
            $array["pseudosp"]=true;
            $array["subvariety"]=true;
            $array["variety"]=true;
            $array["subspecies"]=true;
            $array["subgroup"]=true;
            $array["group"]=true;
            $array["subfam"]=true;
            $array["spp"]=true;
            $array["convar"]=true;
            $array["forma"]=true;
            $array["fo"]=true;
            $array["form"]=true;
            $array["subforma"]=true;
            $array["subgen"]=true;
            $array["subg"]=true;
            $array["subf"]=true;
            $array["subvar"]=true;
            $array["nothovar"]=true;
            $array["nothosubsp"]=true;
            $array["variant"]=true;
            $array["var"]=true;
            $array["subsp"]=true;
            $array["sp"]=true;
            $array["ssp"]=true;
            $array["subgenus"]=true;
            $array["group"]=true;
            $array["species"]=true;
            $array["generic"]=true;
            $array["genus"]=true;
            $array["genera"]=true;
            $array["complex"]=true;
            $array["section"]=true;
            $array["genus"]=true;
            $array["morph"]=true;
            $array["mstr"]=true;
            $array["notho"]=true;
            $array["chr"]=true;
            $array["mutation"]=true;
            $array["mutatio"]=true;
            $array["biogroup"]=true;
            $array["sec"]=true;
            $array["lato"]=true;
            $array["juvenile"]=true;
            $array["variété"]=true;
            $array["holotype"]=true;
            $array["cross"]=true;
            $array["f"]=true;
            $array["x"]=true;
            
            $GLOBALS["SCI_PARTS"] = $array;
        }
    }
    
    public static function sci_parts1()
    {
        if(@!$GLOBALS["SCI_PARTS1"])
        {
            $array = array();
            $array["unranked"]=true;
            $array["susbsp"]=true;
            $array["lus"]=true;
            $array["sf"]=true;
            $array["subv"]=true;
            $array["susp"]=true;
            $array["pseudosp"]=true;
            $array["subvariety"]=true;
            $array["variety"]=true;
            $array["subspecies"]=true;
            $array["subgroup"]=true;
            $array["group"]=true;
            $array["subfam"]=true;
            $array["spp"]=true;
            $array["convar"]=true;
            $array["fo"]=true;
            $array["form"]=true;
            $array["subforma"]=true;
            $array["subgen"]=true;
            $array["subg"]=true;
            $array["subf"]=true;
            $array["nothovar"]=true;
            $array["nothosubsp"]=true;
            $array["variant"]=true;
            $array["ssp"]=true;
            $array["subgenus"]=true;
            $array["group"]=true;
            $array["species"]=true;
            $array["generic"]=true;
            $array["genus"]=true;
            $array["genera"]=true;
            $array["complex"]=true;
            $array["section"]=true;
            $array["genus"]=true;
            $array["morph"]=true;
            $array["mstr"]=true;
            $array["notho"]=true;
            $array["chr"]=true;
            $array["mutation"]=true;
            $array["mutatio"]=true;
            $array["biogroup"]=true;
            $array["sec"]=true;
            $array["lato"]=true;
            $array["juvenile"]=true;
            $array["variété"]=true;
            $array["holotype"]=true;
            $array["cross"]=true;
            
            $GLOBALS["SCI_PARTS1"] = $array;
        }
    }
    
    public static function sci_parts2()
    {
        if(@!$GLOBALS["SCI_PARTS2"])
        {
            $array = array();
            $array["var"]=true;
            $array["subsp"]=true;
            $array["sp"]=true;
            $array["forma"]=true;
            $array["f"]=true;
            $array["x"]=true;
            $array["subvar"]=true;
            
            $GLOBALS["SCI_PARTS2"] = $array;
        }
    }
    
    public static function junk_parts()
    {
        if(@!$GLOBALS["JUNK_PARTS"])
        {
            $array = array();
            $array["cultiv"]=true;
            $array["enrichment"]=true;
            $array["culture"]=true;
            $array["clone"]=true;
            $array["str"]=true;
            $array["doubtful"]=true;
            $array["dubious"]=true;
            $array["emended"]=true;
            $array["com"]=true;
            $array["auth"]=true;
            $array["sens"]=true;
            $array["partim"]=true;
            $array["fi"]=true;
            $array["indicated"]=true;
            $array["lat"]=true;
            $array["id"]=true;
            $array["ab"]=true;
            $array["loc"]=true;
            $array["and"]=true;
            $array["&"]=true;
            $array["&amp;"]=true;
            $array["corrig"]=true;
            $array["pv"]=true;
            $array["mult"]=true;
            $array["cv"]=true;
            $array["inval"]=true;
            $array["aff"]=true;
            $array["ambig"]=true;
            $array["anon"]=true;
            $array["orth"]=true;
            $array["hyb"]=true;
            $array["gen"]=true;
            $array["nomen"]=true;
            $array["invalid"]=true;
            $array["prep"]=true;
            $array["dela"]=true;
            $array["press"]=true;
            $array["illeg"]=true;
            $array["ssel"]=true;
            $array["hl"]=true;
            $array["ll"]=true;
            $array["super"]=true;
            $array["pro"]=true;
            $array["hybr"]=true;
            $array["plur"]=true;
            $array["nk"]=true;
            $array["as"]=true;
            $array["to"]=true;
            $array["type"]=true;
            $array["nud"]=true;
            $array["et"]=true;
            $array["al"]=true;
            $array["accord"]=true;
            $array["according"]=true;
            $array["orthographic"]=true;
            $array["emend"]=true;
            $array["of"]=true;
            $array["authors"]=true;
            $array["nom"]=true;
            $array["comb"]=true;
            $array["nov"]=true;
            $array["ined"]=true;
            $array["cons"]=true;
            $array["sensu"]=true;
            $array["hort"]=true;
            $array["p.p"]=true;
            $array["not"]=true;
            $array["strain"]=true;
            $array["cf"]=true;
            $array["status"]=true;
            $array["unclear"]=true;
            $array["fide"]=true;
            $array["see"]=true;
            $array["comment"]=true;
            $array["bis"]=true;
            $array["specified"]=true;
            $array["be"]=true;
            $array["filled"]=true;
            $array["fil"]=true;
            $array["questionable"]=true;
            $array["the"]=true;
            $array["arid"]=true;
            $array["acc"]=true;
            $array["region"]=true;
            $array["eul"]=true;
            $array["ms"]=true;
            $array["beauv"]=true;
            $array["prop"]=true;
            $array["nm"]=true;
            $array["fort"]=true;
            $array["mut"]=true;
            $array["stat"]=true;
            $array["plants"]=true;
            $array["nec"]=true;
            $array["given"]=true;
            $array["cited"]=true;
            $array["typ"]=true;
            $array["ign"]=true;
            $array["often"]=true;
            $array["referred"]=true;
            $array["superfl"]=true;
            $array["parte"]=true;
            $array["plants"]=true;
            $array["pl"]=true;
            $array["fig"]=true;
            $array["no"]=true;
            $array["prelo"]=true;
            $array["maly"]=true;
            $array["schneider"]=true;
            $array["apud"]=true;
            $array["sine"]=true;
            $array["typo"]=true;
            $array["abbreviation"]=true;
            $array["recorded"]=true;
            $array["label"]=true;
            $array["on"]=true;
            $array["hybridized"]=true;
            $array["with"]=true;
            $array["unspecified"]=true;
            $array["rke"]=true;
            $array["illegible"]=true;
            $array["biotype"]=true;
            $array["race"]=true;
            $array["biotype"]=true;
            $array["vag"]=true;
            $array["tax"]=true;
            $array["x"]=true;
            $array["west"]=true;
            $array["auctor"]=true;
            $array["toni"]=true;
            $array["assigned"]=true;
            $array["sect"]=true;
            $array["subsect"]=true;
            $array["series"]=true;
            $array["ser"]=true;
            //$array["typus"]=true;
            $array["dos"]=true;
            $array["rn"]=true;
            $array["editor"]=true;
            $array["di"]=true;
            $array["list"]=true;
            $array["pl"]=true;
            $array["applicable"]=true;
            $array["undet"]=true;
            $array["species"]=true;
            $array["col"]=true;
            $array["area"]=true;
            $array["op"]=true;
            $array["cit"]=true;
            $array["ey"]=true;
            $array["zu"]=true;
            $array["und"]=true;
            $array["name"]=true;
            $array["only"]=true;
            $array["excl"]=true;
            $array["syn"]=true;
            $array["or"]=true;
            $array["also"]=true;
            $array["by"]=true;
            $array["ex"]=true;
            $array["in"]=true;
            $array["auct"]=true;
            $array["non"]=true;
            $array["date"]=true;
            $array["inter"]=true;
            $array["before"]=true;
            $array["vel"]=true;
            $array["sep"]=true;
            $array["nat"]=true;
            $array["bekannt"]=true;
            $array["ter"]=true;
            $array["É"]=true;
            $array["nr"]=true;
            $array["aberr"]=true;
            $array["nr"]=true;
            $array["between"]=true;
            $array["rus"]=true;
            $array["ent"]=true;
            $array["synanamorph"]=true;
            $array["anamorph"]=true;
            $array["zur"]=true;
            $array["ul"]=true;
            $array["lu"]=true;
            $array["circa"]=true;
            $array["pls"]=true;
            $array["ante"]=true;
            $array["testa"]=true;
            $array["prior"]=true;
            $array["generic"]=true;
            $array["post"]=true;
            $array["etc"]=true;
            $array["binom"]=true;
            //$array["do"]=true;
            $array["nex"]=true;
            $array["auctt"]=true;
            $array["stricto"]=true;
            $array["das"]=true;
            $array["dates"]=true;
            $array["from"]=true;
            $array["doubtful"]=true;
            $array["dubious"]=true;
            $array["emended"]=true;
            $array["com"]=true;
            $array["partim"]=true;
            $array["fi"]=true;
            $array["indicated"]=true;
            $array["lat"]=true;
            $array["ii"]=true;
            $array["ry"]=true;
            $array["ndez"]=true;
            $array["lez"]=true;
            $array["lc"]=true;
            $array["rskov"]=true;
            $array["nudum"]=true;
            $array["sbsp"]=true;
            $array["morpha"]=true;
            $array["esp"]=true;
            $array["mph"]=true;
            $array["s-sp"]=true;
            $array["subs"]=true;
            $array["variété"]=true;
            $array["forme"]=true;
            $array["subspec"]=true;
            $array["sous-type"]=true;
            $array["inte"]=true;
            $array["subspp"]=true;
            $array["indet"]=true;
            $array["corrected"]=true;
            $array["none"]=true;
            $array["iber"]=true;
            $array["eur"]=true;
            $array["balcan"]=true;
            $array["nonn"]=true;
            $array["fl"]=true;
            $array["cauc"]=true;
            $array["armen"]=true;
            $array["inc"]=true;
            $array["orient"]=true;
            $array["ross"]=true;
            $array["med"]=true;
            $array["germ"]=true;
            $array["boreal"]=true;
            $array["boruss"]=true;
            $array["amer"]=true;
            $array["prol"]=true;
            $array["ca"]=true;
            $array["but"]=true;
            $array["misapplied"]=true;
            $array["subst"]=true;
            $array["for"]=true;
            $array["int"]=true;
            $array["several"]=true;
            $array["error"]=true;
            $array["pers"]=true;
            $array["comm"]=true;
            $array["nudum"]=true;
            $array["errore"]=true;
            $array["incertae"]=true;
            $array["sedis"]=true;
            $array["sic"]=true;
            $array["substit"]=true;
            $array["web"]=true;
            $array["site"]=true;
            $array["viii"]=true;
            $array["oblit"]=true;
            $array["new"]=true;
            $array["xxxx"]=true;
            $array["an"]=true;
            $array["objective"]=true;
            $array["synonym"]=true;
            $array["now"]=true;
            $array["bottom"]=true;
            $array["both"]=true;
            $array["pictures"]=true;
            $array["synonymy"]=true;
            $array["uncertain"]=true;
            $array["substit"]=true;
            $array["under"]=true;
            $array["inc"]=true;
            $array["sed"]=true;
            $array["spelling"]=true;
            $array["brit"]=true;
            $array["irj"]=true;
            $array["mf"]=true;
            $array["subfo"]=true;
            $array["sport"]=true;
            $array["tribe"]=true;
            $array["subtribe"]=true;
            $array["subser"]=true;
            $array["subtrib"]=true;
            $array["trib"]=true;
            $array["sebsp"]=true;
            $array["lusus"]=true;
            $array["sub"]=true;
            $array["gr"]=true;
            $array["oblvar"]=true;
            $array["nra"]=true;
            $array["fam"]=true;
            $array["en"]=true;
            $array["mey"]=true;
            $array["susbp"]=true;
            $array["sre"]=true;
            $array["subtr"]=true;
            $array["subdiv"]=true;
            $array["pars"]=true;
            $array["quad"]=true;
            $array["typum"]=true;
            $array["set"]=true;
            $array["rouy"]=true;
            $array["opiz"]=true;
            $array["agsp"]=true;
            $array["ourk"]=true;
            //$array["proles"]=true;
            $array["liu"]=true;
            $array["ecad"]=true;
            $array["substirps"]=true;
            $array["groupa"]=true;
            $array["groupe"]=true;
            $array["divis"]=true;
            $array["nothosect"]=true;
            $array["nothomorph"]=true;
            $array["em"]=true;
            $array["nsubsp"]=true;
            $array["monstr"]=true;
            $array["rev"]=true;
            $array["basionym"]=true;
            $array["quoad"]=true;
            $array["ven"]=true;
            $array["order"]=true;
            $array["mon"]=true;
            $array["superord"]=true;
            $array["ord"]=true;
            $array["subvars"]=true;
            $array["cm"]=true;
            $array["supertrib"]=true;
            $array["mnstr"]=true;
            $array["ren"]=true;
            $array["subset"]=true;
            $array["subtribus"]=true;
            $array["agg"]=true;
            $array["jr"]=true;
            $array["nothof"]=true;
            $array["nothogen"]=true;
            $array["nothosubgen"]=true;
            $array["individual"]=true;
            //$array["index"]=true;
            $array["supsp"]=true;
            $array["attr"]=true;
            $array["incorrectly"]=true;
            $array["ined;cf"]=true;
            $array["el"]=true;
            $array["various"]=true;
            $array["cultivars"]=true;
            $array["af"]=true;
            $array["valide"]=true;
            $array["publ"]=true;
            $array["class"]=true;
            $array["sufam"]=true;
            $array["xx"]=true;
            $array["xxx"]=true;
            $array["xxxx"]=true;
            $array["quib"]=true;
            $array["ap"]=true;
            $array["subap"]=true;
            $array["grupo"]=true;
            $array["gruppe"]=true;
            $array["oec"]=true;
            $array["prole"]=true;
            $array["nothsect"]=true;
            $array["nssp"]=true;
            $array["nopthosubsp"]=true;
            $array["jun"]=true;
            $array["rx"]=true;
            $array["like"]=true;
            $array["ascribed"]=true;
            $array["included"]=true;
            $array["rejected"]=true;
            $array["segregates"]=true;
            $array["ngstr"]=true;
            $array["nothosubg"]=true;
            $array["subclassis"]=true;
            $array["eds"]=true;
            $array["spec"]=true;
            $array["ty"]=true;
            $array["ed"]=true;
            $array["herb"]=true;
            
            $GLOBALS["JUNK_PARTS"] = $array;
        }
    }
    
    public static function time_elapsed()
    {
        static $a;
        if(!isset($a)) $a = microtime(true);
        return (string) round(microtime(true)-$a, 6);
    }
    
    public static function catch_exception($e)
    {
        echo "Caught exception with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine()."<br>\nStack trace:<br>\n".$e->getTraceAsString()."<br>\n";
    }
    
    public static function print_pre($array, $return = false)
    {
        $str = "<pre>";
        $str .= print_r($array, 1);
        $str .= "</pre>";
        
        if($return) return $str;
        echo $str;
    }
    
    public static function print_r_public($str, $return = false)
    {
        $str = print_r($str, true);
        $regex = "/\n( +)\[[^\]]*?:(private|protected)\] =>.*?(\n\\1\[)/ims";
        while(preg_match($regex, $str)) $str = preg_replace($regex, "\\3", $str);
        
        if($return) return $str;
        echo $str;
    }
    
    /* Given the file path (or just the extension, if $is_extension == true) get the MIME type */
    public static function get_mimetype($file_path, $is_extension=false)
    {
        if ($is_extension)
        {
            $extension = strtolower($file_path);
        } else {
            $path_info = pathinfo($file_path);
            $extension = strtolower(@$path_info['extension']);
        }
        $mimetype = "";
        $mpg = array("mpg", "mpeg");
        if     ($extension === "wmv")        $mimetype = "video/x-ms-wmv";
        elseif ($extension === "avi")        $mimetype = "video/x-msvideo";
        elseif ($extension === "mp4")        $mimetype = "video/mp4";
        elseif ($extension === "mov")        $mimetype = "video/quicktime";
        elseif (in_array($extension, $mpg)) $mimetype = "video/mpeg";
        elseif ($extension === "flv")        $mimetype = "video/x-flv";
        elseif ($extension === "ogv")        $mimetype = "video/ogg";
        elseif ($extension === "webm")       $mimetype = "video/webm";
        elseif ($extension === "bmp")        $mimetype = "image/bmp";
        elseif ($extension === "gif")        $mimetype = "image/gif";
        elseif ($extension === "jpg")        $mimetype = "image/jpeg";
        elseif ($extension === "jpeg")       $mimetype = "image/jpeg";
        elseif ($extension === "png")        $mimetype = "image/png";
        elseif ($extension === "tif")        $mimetype = "image/tiff";
        elseif ($extension === "svg")        $mimetype = "image/svg+xml";
        elseif ($extension === "mp3")        $mimetype = "audio/mpeg";
        elseif ($extension === "wav")        $mimetype = "audio/x-wav";
        elseif ($extension === "ogg")        $mimetype = "audio/ogg";
        elseif ($extension === "oga")        $mimetype = "audio/ogg";
        elseif ($extension === "ogx")        $mimetype = "application/ogg";
        return $mimetype;
    }
    
    public static function language_to_iso_code()
    {
        $iso_639_2_codes = array();
        if(file_exists(DOC_ROOT . 'vendor/wikipedia/iso_639_2.txt'))
        {
            $lines = file(DOC_ROOT . 'vendor/wikipedia/iso_639_2.txt');
            foreach($lines as $line)
            {
                $line = rtrim($line, "\n");
                $parts = explode("\t", $line);
                if(isset($parts[0]) && strlen($parts[0])==2 && isset($parts[1]))
                {
                    $iso_639_2_codes[$parts[1]] = $parts[0];
                }
            }
        }
        return $iso_639_2_codes;
    }

    public static function cardinal_to_ordinal($number)
    {
        switch(substr($number, -1))
        {
            case 1: $number .= "st"; break;
            case 2: $number .= "nd"; break;
            case 3: $number .= "rd"; break;
            default: $number .= "th"; break;
        }
        return $number;
    }

    public static function format_number_with_leading_zeros($num, $padding)
    {
        return str_pad($num, $padding, "0", STR_PAD_LEFT);
    }

    public static function prepare_taxon_params($rec)
    {
        $taxon = array();
        $taxon["identifier"]     = trim(@$rec["identifier"]);
        $taxon["source"]         = @$rec["source"];
        $taxon["kingdom"]        = ucfirst(trim(@$rec["kingdom"]));
        $taxon["phylum"]         = ucfirst(trim(@$rec["phylum"]));
        $taxon["class"]          = ucfirst(trim(@$rec["class"]));
        $taxon["order"]          = ucfirst(trim(@$rec["order"]));
        $taxon["family"]         = ucfirst(trim(@$rec["family"]));
        $taxon["genus"]          = ucfirst(trim(@$rec["genus"]));
        $taxon["scientificName"] = ucfirst(trim(@$rec["sciname"]));
        $taxon["rank"]           = @$rec["rank"];
        //-------------------------------------------------------------------------------------------------
        if(@$rec["commonNames"])
        {
            $taxon["commonNames"] = array();
            foreach($rec["commonNames"] as $comname)
            {
                $taxon["commonNames"][] = new \SchemaCommonName(array("name" => $comname["name"], "language" => $comname["language"]));
            }
        }
        //-------------------------------------------------------------------------------------------------
        if(@$rec["synonyms"])
        {
            $taxon["synonyms"] = array();
            foreach($rec["synonyms"] as $syn)
            {
                $taxon["synonyms"][] = new \SchemaSynonym(array("synonym" => $syn["synonym"], "relationship" => $syn["relationship"]));
            }
        }
        //-------------------------------------------------------------------------------------------------
        $taxon = Functions::prepare_agent_params($rec, $taxon);
        $taxon["created"]  = trim(@$rec["created"]);
        $taxon["modified"] = trim(@$rec["modified"]);
        $taxon = Functions::prepare_reference_params($rec, $taxon);
        $taxon["additionalInformation"] = trim(@$rec["additionalInformation"]);
        //-------------------------------------------------------------------------------------------------
        if(@$rec["data_objects"])
        {
            foreach($rec["data_objects"] as $object)
            {
                if($data_object = Functions::prepare_data_object_params($object)) $taxon["dataObjects"][] = new \SchemaDataObject($data_object);
            }
        }
        //-------------------------------------------------------------------------------------------------
        $taxon_parameters = new \SchemaTaxon($taxon);
        return $taxon_parameters;
    }

    public static function prepare_data_object_params($rec)
    {
        $data_object_parameters = array();
        $data_object_parameters["identifier"] = trim(@$rec["identifier"]);
        $data_object_parameters["dataType"]   = trim(@$rec["dataType"]);
        $data_object_parameters["mimeType"]   = trim(@$rec["mimeType"]);
        $data_object_parameters = Functions::prepare_agent_params($rec, $data_object_parameters);
        $data_object_parameters["created"]               = trim(@$rec["created"]);
        $data_object_parameters["modified"]              = trim(@$rec["modified"]);
        $data_object_parameters["title"]                 = trim(@$rec["title"]);
        $data_object_parameters["language"]              = trim(@$rec["language"]);
        $data_object_parameters["license"]               = trim(@$rec["license"]);
        $data_object_parameters["rights"]                = trim(@$rec["rights"]);
        $data_object_parameters["rightsHolder"]          = trim(@$rec["rightsHolder"]);
        $data_object_parameters["bibliographicCitation"] = trim(@$rec["bibliographicCitation"]);
        //-------------------------------------------------------------------------------------------------
        if(@$rec["audience"])
        {
            $data_object_parameters["audiences"] = array();    
            $audienceParameters = array();  
            foreach(@$rec["audience"] as $audience)
            {
                $audienceParameters["label"] = $audience;
                $data_object_parameters["audiences"][] = new \SchemaAudience($audienceParameters);
            }
        }
        //-------------------------------------------------------------------------------------------------
        $data_object_parameters["source"] = trim(@$rec["source"]);
        //-------------------------------------------------------------------------------------------------
        if(trim(@$rec["subject"]))
        {
            $data_object_parameters["subjects"] = array();
            $subjectParameters = array();
            $subjectParameters["label"] = trim(@$rec["subject"]);
            $data_object_parameters["subjects"][] = new \SchemaSubject($subjectParameters);
        }
        //-------------------------------------------------------------------------------------------------
        $data_object_parameters["description"]  = trim(@$rec["description"]);
        $data_object_parameters["mediaURL"]     = trim(@$rec["mediaURL"]);
        $data_object_parameters["thumbnailURL"] = trim(@$rec["thumbnailURL"]);
        $data_object_parameters["location"]     = trim(@$rec["location"]);
        $data_object_parameters = Functions::prepare_reference_params($rec, $data_object_parameters);
        $data_object_parameters["additionalInformation"] = trim(@$rec["additionalInformation"]);
        //-------------------------------------------------------------------------------------------------
        return $data_object_parameters;
    }

    public static function prepare_reference_params($rec, $taxon_or_data_object_param)
    {
        if(@$rec["reference"])
        {
            $taxon_or_data_object_param["references"] = array();
            $reference = array();
            $attributes = array("bici", "coden", "doi", "eissn", "handle", "isbn", "issn", "lsid", "oclc", "sici", "url", "urn");
            foreach(@$rec["reference"] as $ref)
            {
                $referenceParam = array();
                $referenceParam["fullReference"] = $ref["fullReference"];
                foreach($attributes as $attribute)
                {
                    if(@$ref[$attribute]) $referenceParam["referenceIdentifiers"][] = new \SchemaReferenceIdentifier(array("label" => $attribute, "value" => trim(@$ref[$attribute])));
                }
                $reference[] = new \SchemaReference($referenceParam);
            }
            $taxon_or_data_object_param["references"] = $reference;
        }
        return $taxon_or_data_object_param;
    }

    public static function prepare_agent_params($rec, $taxon_or_data_object_param)
    {
        if(@$rec["agent"])
        {
            $agents = array();
            foreach(@$rec["agent"] as $agent)
            {  
                $agentParameters = array();
                $agentParameters["role"]     = @$agent["role"];
                $agentParameters["homepage"] = @$agent["homepage"];
                $agentParameters["logoURL"]  = @$agent["logoURL"];
                $agentParameters["fullName"] = @$agent["fullName"];
                $agents[] = new \SchemaAgent($agentParameters);
            }
            $taxon_or_data_object_param["agents"] = $agents;
        }
        return $taxon_or_data_object_param;
    }

    public static function set_resource_status_to_force_harvest($resource_id)
    {
        if(file_exists(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . ".xml"))
        {
            if(filesize(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . ".xml") > 600)
            {
                $GLOBALS['db_connection']->update("UPDATE resources SET resource_status_id=" . ResourceStatus::force_harvest()->id . " WHERE id=" . $resource_id);
            }
        }elseif(file_exists(CONTENT_RESOURCE_LOCAL_PATH ."/$resource_id/taxon.tab"))
        {
            if(filesize(CONTENT_RESOURCE_LOCAL_PATH ."/$resource_id/taxon.tab") > 600)
            {
                $GLOBALS['db_connection']->update("UPDATE resources SET resource_status_id=" . ResourceStatus::force_harvest()->id . " WHERE id=" . $resource_id);
            }
        }
    }

    //4 functions for queueing task in connectors
    public static function add_a_task($task, $filename)
    {
        if($READ = Functions::file_open($filename, "a"))
        {
            fwrite($READ, $task);
            fclose($READ);
        }
    }

    public static function get_a_task($filename)
    {
        if($READ = Functions::file_open($filename, "r"))
        {
            $line = fgets($READ);
            fclose($READ);
            return $line;
        }
    }

    public static function delete_a_task($task, $filename)
    {
        if($READ = Functions::file_open($filename, 'r'))
        {
            $task_list = fread($READ, filesize($filename));
            fclose($READ);
            $task_list = str_ireplace($task, "", $task_list);
            //saving
            if(!($OUT = Functions::file_open($filename, "w"))) return;
            fwrite($OUT, $task_list);
            fclose($OUT);
        }
    }

    public static function run_another_connector_instance($resource_id, $times)
    {
        for($i = 1; $i <= $times; $i++)
        {
            print "\n run " . self::cardinal_to_ordinal($i + 1) . " instance--";
            shell_exec(PHP_BIN_PATH . DOC_ROOT . 'update_resources/connectors/' . $resource_id . '_next.php 0 > null &');
            sleep(5);
        }
    }

    public static function process_work_list($class, $batch = null)
    {
        while(true)
        {
            $task = Functions::get_a_task($class->WORK_LIST); //get task to work on
            if($task)
            {
                print "\n Process this: $task";
                Functions::delete_a_task($task, $class->WORK_LIST); //remove a task from task list
                Functions::add_a_task($task, $class->WORK_IN_PROGRESS_LIST);
                print "$task \n";
                $task = str_ireplace("\n", "", $task); //remove carriage return got from text file
                if($class->call_multiple_instance) //call other instances of the connector
                {
                    Functions::run_another_connector_instance($class->resource_id, $class->connectors_to_run);
                    $class->call_multiple_instance = 0;
                }
                $class->get_all_taxa($task, $class->TEMP_FILE_PATH, array($batch, $task)); //main connector body
                print"\n Task $task is done. \n";
                Functions::delete_a_task("$task\n", $class->WORK_IN_PROGRESS_LIST);//remove a task from task list
            }
            else
            {
                print "\n\n [$task] Work list done or list hasn't been created yet " . date('Y-m-d h:i:s a', time());
                break;
            }
        }
    }

    public static function kill_running_connectors($resource_id)
    {
        $myPID = getmypid();
        print "\n myPID: $myPID \n"; //this won't get killed
        $pattern = "update_resources/connectors/" . $resource_id . "|update_resources/connectors/" . $resource_id . "_next";
        $command = "ps -x | egrep " . "'($pattern).php'";
        $output = trim(shell_exec($command));
        $jobs = explode("\n", $output);
        $jobs = array_values($jobs);
        $pids = array();
        foreach($jobs as $job) if($job) $pids[] = substr($job, 0, strpos($job, ' '));
        asort($pids);
        if($pids)
        {
            print_r($jobs);
            foreach($pids as $pid)
            {
                if($pid <> $myPID)
                {
                    print "\n kill $pid ";
                    shell_exec('kill ' . $pid);
                } 
            }
        }
        else print "\n That connector is not running at the moment.";
        print "\n\n";
    }

    public function create_work_list_from_master_file($master_file, $divisor, $destination_folder, $filename_prefix, $work_list)
    {
        if(!($FILE = Functions::file_open($master_file, "r")))
        {
            echo "\n File not found: \n $master_file \n Program will terminate.\n\n";
            return false;
        }
        $i = 0;
        $file_ctr = 0;
        $str = "";
        while(!feof($FILE))
        {
            if($line = fgets($FILE))
            {
                $i++;
                $str .= $line;
                print "\n$i. $line";
                if($i == $divisor)//no. of rows per text file
                {
                    $file_ctr++;
                    $file_ctr_str = Functions::format_number_with_leading_zeros($file_ctr, 3);
                    if(!($OUT = Functions::file_open($destination_folder . $filename_prefix . $file_ctr_str . ".txt", "w"))) return;
                    fwrite($OUT, $str);
                    fclose($OUT);
                    $str = "";
                    $i = 0;
                }
            }
        }
        //last writes
        if($str)
        {
            $file_ctr++;
            $file_ctr_str = Functions::format_number_with_leading_zeros($file_ctr, 3);
            if(!($OUT = Functions::file_open($destination_folder . $filename_prefix . $file_ctr_str . ".txt", "w"))) return;
            fwrite($OUT, $str);
            fclose($OUT);
        }
        //create work_list
        $str = "";
        for($i = 1; $i <= $file_ctr; $i++) $str .= $filename_prefix . Functions::format_number_with_leading_zeros($i, 3) . "\n";
        if($fp = Functions::file_open($work_list, "w"))
        {
            fwrite($fp, $str);
            fclose($fp);
        }
        return $file_ctr; // total number of work tasks
    }

    function combine_all_eol_resource_xmls($resource_id, $files)
    {
        //debug("\n\n Start compiling all XML...");
        if(!($OUT = Functions::file_open(CONTENT_RESOURCE_LOCAL_PATH . $resource_id . ".xml", "w"))) return;
        $str = "<?xml version='1.0' encoding='utf-8' ?>\n";
        $str .= "<response\n";
        $str .= "  xmlns='http://www.eol.org/transfer/content/0.3'\n";
        $str .= "  xmlns:xsd='http://www.w3.org/2001/XMLSchema'\n";
        $str .= "  xmlns:dc='http://purl.org/dc/elements/1.1/'\n";
        $str .= "  xmlns:dcterms='http://purl.org/dc/terms/'\n";
        $str .= "  xmlns:geo='http://www.w3.org/2003/01/geo/wgs84_pos#'\n";
        $str .= "  xmlns:dwc='http://rs.tdwg.org/dwc/dwcore/'\n";
        $str .= "  xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'\n";
        $str .= "  xsi:schemaLocation='http://www.eol.org/transfer/content/0.3 http://services.eol.org/schema/content_0_3.xsd'>\n";
        fwrite($OUT, $str);
        foreach (glob($files) as $filename)
        {
            //debug("\n $filename");
            if(!($READ = Functions::file_open($filename, "r"))) return;
            $contents = fread($READ, filesize($filename));
            fclose($READ);
            if($contents) 
            {
                $pos1 = stripos($contents, "<taxon>");
                $pos2 = stripos($contents, "</response>");
                $str  = substr($contents, $pos1, $pos2-$pos1);
                if($pos1) fwrite($OUT, $str);
            }
            else
            {
                //debug("\n no contents [$filename]");
            }
        }
        fwrite($OUT, "</response>");
        fclose($OUT);
        //debug("\n All XML compiled\n\n");
    }

    function delete_temp_files($file_path, $file_extension = '*')
    {
        if(!$file_path) return;
        foreach (glob($file_path . "*." . $file_extension) as $filename) unlink($filename);
    }

    function last_day_of_month($month, $year)
    {
        return idate('d', mktime(0, 0, 0, ($month + 1), 0, $year));
    }

    function sec2hms($sec, $padHours = false)
    {
        $hms = "";
        $hours = intval(intval($sec) / 3600);
        $hms .= ($padHours)
              ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
              : $hours. ':';
        $minutes = intval(($sec / 60) % 60);
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
        $seconds = intval($sec % 60);
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
        return $hms;
    }

    public function get_google_spreadsheet($options)
    {
        /* This will return an array of $sheet[col][row] values */
        if(!isset($options["spreadsheet_title"]))
        {
            //debug("[spreadsheet_title] is a required paramemter \n");
            return false;
        }
        require_vendor('google_api');
        if(!isset($options["number_of_columns_to_return"])) $options["number_of_columns_to_return"] = "all"; // use this to return the no. of columns from left to right
        if(!isset($options["column_number_to_return"]))     $options["column_number_to_return"] = false; // use this to return a single column
        if(!isset($options["timeout"]))                     $options["timeout"] = 100;
        if(!isset($options["google_username"])) $options["google_username"] = $GLOBALS['GOOGLE_USERNAME'];
        if(!isset($options["google_password"])) $options["google_password"] = $GLOBALS['GOOGLE_PASSWORD'];

        $params = array("timeout" => $options["timeout"]); // parameters for the google_api
        $spreadsheet_tables_api = new \google_api\GoogleSpreadsheetsAPI($options["google_username"], $options["google_password"], @$_SESSION['GOOGLE_AUTH_TOKEN'], '', $params);
        $response = $spreadsheet_tables_api->get_spreadsheets($params);
        foreach($response->entry as $entry)
        {
            if($entry->title == $options["spreadsheet_title"]) // e.g "BOLD image mappings", "SPG Hotlist Official Version"
            {
                $URL_for_spreadsheet = $entry->content['src'];
                $spreadsheet_repsonse = $spreadsheet_tables_api->get_response($URL_for_spreadsheet, $params);
                $sheet_url = $spreadsheet_repsonse->entry->link[0]['href'];
                $worksheet_repsonse = $spreadsheet_tables_api->get_response($sheet_url, $params);
                $cols = array();
                foreach($worksheet_repsonse->entry as $entry) $cols[substr($entry->title,0,1)][substr($entry->title,1,strlen($entry->title)-1)] = $entry->content;
                $letters = array_keys($cols);
                $max_count = 0;
                foreach($letters as $letter) // to get the max_count of rows
                {
                    if(count($cols[$letter]) > $max_count) $max_count = count($cols[$letter]);
                }
                $sheet = array(); // to be returned
                $col_count = 0;
                foreach($letters as $letter)
                {
                    $col_count++;
                    for($i=1; $i<=$max_count; $i++) $sheet[$col_count][$i] = @$cols[$letter][$i];
                    if($options["number_of_columns_to_return"] != "all")
                    {
                        if($col_count >= $options["number_of_columns_to_return"]) return $sheet;
                    }
                }
                if($options["column_number_to_return"]) 
                {
                    return $sheet[$options["column_number_to_return"]];
                }
                return $sheet;
            }
        }
        return false;
    }

}
?>
