<?php
// namespace php_active_record;

class api_reader_controller //extends ControllerBase
{
    function __construct($type, $params)
    {
        // exit("\n[$value]\n");
        if($type == 'usercontrib')
        {
            $namespace['ForReview'] = 5000;
            $namespace['Published'] = 0;
            
            $url = $params['server'] . "/StudentContributions/api.php?action=query&list=usercontribs&ucuser=" . $params['user'] . "&uclimit=100&ucdir=older&format=json&ucnamespace=" . $namespace[$params['article_type']] . "&ucshow=top";
            $json = Functions::lookup_with_cache($url, array('expire_seconds' => 0));
            $arr = json_decode($json);
            $titles = array();
            foreach($arr->query->usercontribs as $item)
            {
                $titles[] = array('page_title' => $item->title, 'server' => $params['server']);
            }
            $this->body = implode(array_map('api_reader_controller::render_page_row', $titles));
        }
    }
    
    
    public static function index()
    {
    }

    function render_template($filename, $variables) 
    {
        extract($variables); //makes the array index value to become a variable e.g. array("a" => "dog") becomes $a = "dog";
        ob_start();
        require('../templates/api_reader/' . $filename . '.php');
        $contents = ob_get_contents(); 
        ob_end_clean();
        return $contents;
    }

    function render_layout($title, $body) 
    {
        return api_reader_controller::render_template('layout', array('title' => $title, 'body' => $body));
    }

    function render_page_row($title)
    {
        return api_reader_controller::render_template('page-row', $title); //$title here is an array value for $titles above
    }

    function render_article_summary($article)
    {
        return api_reader_controller::render_template('article-summary', $article);
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
