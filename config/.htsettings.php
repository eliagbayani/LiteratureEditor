<?php
//=== for archive ===
// /*
$conf['wgServer']           = "https://editors.eol.org";

// database
$conf['wgDBuser']           = "root";
$conf['wgDBpassword']       = "-secret-";

// cache
$conf['wgMainCacheType']    = CACHE_ACCEL;
$conf['wgMemCachedServers'] = array();

// images
$conf['images_folder']      = "/var/www/html/LiteratureEditor_images";
$conf['wgUploadDirectory']  = $conf['images_folder'];               //where MediaWiki uploades images
$conf['wgUploadPath']       = "/LiteratureEditor_images";           //where MediaWiki views images
$conf['wgDeletedDirectory'] = $conf['images_folder']."/deleted";
$conf['wgTmpDirectory']     = $conf['images_folder']."/temp";

// ImageMagick
$conf['wgUseImageMagick']               = true;
$conf['wgImageMagickConvertCommand']    = "/usr/bin/convert";

// mail
$conf['use_smtp'] = false;
// */

//==========================================================================

//=== for mac mini ===
/*
$conf['wgServer']       = "http://editors.eol.localhost";

// database
$conf['wgDBuser']       = "root";
$conf['wgDBpassword']   = "m173";

// cache
$conf['wgMainCacheType']    = CACHE_MEMCACHED; //CACHE_ACCEL; //CACHE_MEMCACHED;                used in MW 1.25.2
$conf['wgMemCachedServers'] = array( '127.0.0.1:11211' ); //array();     //array( '127.0.0.1:11211' );     used in MW 1.25.2

// images
$conf['images_folder']      = "/Library/WebServer/Documents/LiteratureEditor_images";
$conf['wgUploadDirectory']  = $conf['images_folder'];        //where MediaWiki uploades images
$conf['wgUploadPath']       = "/LiteratureEditor_images";    //where MediaWiki views images
$conf['wgDeletedDirectory'] = $conf['images_folder']."/deleted";
$conf['wgTmpDirectory']     = $conf['images_folder']."/temp";

// ImageMagick
$conf['wgUseImageMagick']               = true;
$conf['wgImageMagickConvertCommand']    = "/usr/local/bin/convert";

// mail
$conf['use_smtp'] = true;
*/
?>
