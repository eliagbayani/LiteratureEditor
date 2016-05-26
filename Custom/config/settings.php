<?php

if(!defined('DOWNLOAD_WAIT_TIME')) define('DOWNLOAD_WAIT_TIME', '300000'); //.3 seconds
define('DOWNLOAD_ATTEMPTS', '2');
if(!defined('DOWNLOAD_TIMEOUT_SECONDS')) define('DOWNLOAD_TIMEOUT_SECONDS', '30');

// define('CACHE_PATH', '/Volumes/MacMini_HD2/cache_LiteratureEditor/');    //for mac mini
define('CACHE_PATH', '/var/www/html/cache_LiteratureEditor/');           //for archive


// define('BHL_API_KEY', '8e525086-c464-4298-9431-b815de6c2901'); //Katja's -> used when caching requests
define('BHL_API_KEY', '4ae9b497-37bf-4186-a91c-91f92b2f6e7d'); //Eli's

define('MEDIAWIKI_MAIN_FOLDER', 'LiteratureEditor');
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);

define('DEVELOPER_EMAIL', 'eagbayani@eol.org');
?>
