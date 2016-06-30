<?php
# This file was automatically generated by the MediaWiki 1.25.2
# installer. If you make manual changes, please keep track in case you
# need to recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {exit;}

$wgShowExceptionDetails = true;

// $wgReadOnly = 'This wiki is currently being upgraded to a newer software version.'; //debug - readonly status when doing maintenance

// start by eli =====================
// Define constants for my additional namespaces.
define("NS_ForHarvesting", 5000); // This MUST be even.
define("NS_ForHarvesting_TALK", 5001); // This MUST be the following odd integer.
// end by eli =====================


## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = "EoL: Literature Editor";
$wgMetaNamespace = "Project";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "/LiteratureEditor"; // $wgScriptPath = "/w";
$wgArticlePath = "/LiteratureEditor/wiki/$1";
$wgScriptExtension = ".php";

require_once("config/.htsettings.php");

## The protocol and server name to use in fully-qualified URLs
$wgServer = $conf['wgServer']; //"http://editors.eol.localhost";


## The relative URL path to the skins directory
$wgStylePath = "$wgScriptPath/skins";
$wgResourceBasePath = $wgScriptPath;

## The relative URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
// $wgLogo = "$wgResourceBasePath/resources/assets/wiki.png";
// $wgLogo = "$wgResourceBasePath/images/three_balls.png";
// $wgLogo = "$wgResourceBasePath/resources/assets/eol black bg.png";
$wgLogo = "$wgResourceBasePath/resources/assets/eol_literature_logo.png";

## UPO means: this is also a user preference option


## Database settings
$wgDBtype = "mysql";
$wgDBserver = "localhost";
$wgDBname = "wiki_literatureeditor";
$wgDBuser       = $conf['wgDBuser'];//"root";
$wgDBpassword   = $conf['wgDBpassword'];//"m173";


# MySQL specific settings
$wgDBprefix = "";

# MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";


# Experimental charset support for MySQL 5.0.
$wgDBmysql5 = false;

## Shared memory settings
$wgMainCacheType = $conf['wgMainCacheType']; //CACHE_MEMCACHED;;
$wgMemCachedServers = $conf['wgMemCachedServers']; //array( '127.0.0.1:11211' );

/* 
Make sure that XCache is installed with the new version of PHP. The most likely cause is the new version of PHP simply doesn't have xcache installed. You can do this by creating a php file with just the code <?php phpinfo(); and then viewing it in a web browser.
Setting $wgMainCacheType = CACHE_NONE; will disable all caching, which would prevent the error, but make things slow.

$wgMainCacheType = CACHE_ACCEL;
$wgMemCachedServers = array();
*/


## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick               = $conf['wgUseImageMagick']; //true;
$wgImageMagickConvertCommand    = $conf['wgImageMagickConvertCommand']; //"/usr/local/bin/convert";

/*
all these folders must have write permissions
e.g.
    mkdir /images/temp
    chmod 0777 /images/temp
*/

/* working
$images_folder      = $IP . "_images";
$wgUploadDirectory  = $images_folder;               //where MediaWiki uploades images
$wgUploadPath       = $wgScriptPath . "_images";    //where MediaWiki views images
$wgDeletedDirectory = "$images_folder/deleted";
$wgTmpDirectory     = "$images_folder/temp";
*/

$images_folder      = $conf['images_folder'];
$wgUploadDirectory  = $conf['wgUploadDirectory'];   //where MediaWiki uploades images
$wgUploadPath       = $conf['wgUploadPath'];        //where MediaWiki views images
$wgDeletedDirectory = $conf['wgDeletedDirectory'];
$wgTmpDirectory     = $conf['wgTmpDirectory'];


# InstantCommons allows wiki to use images from http://commons.wikimedia.org
$wgUseInstantCommons = true;


## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "en_US.UTF-8";


## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
#$wgHashedUploadDirectory = false;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/Names.php
$wgLanguageCode = "en";


$wgSecretKey = "47400f156bed4391d3c3769bfffc69efabb980485a40b448e787eb2c742de032";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = "b4800f578748ee57";


## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-sa/3.0/";
$wgRightsText = "Creative Commons Attribution-ShareAlike";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-sa.png";



# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

# The following permissions were set based on your choice in the installer
    //initialize false rights
    $rights = array('createaccount', 'createpage', 'edit', 'move', 'writeapi', 'upload', 'changetags', 'applychangetags', 'minoredit', 'move-categorypages', 'movefile', 'move', 'move-subpages', 'move-rootuserpages', 'reupload-shared', 'reupload', 'purge', 'sendemail');
    foreach(array('*', 'user') as $eoe_user)
    {
        foreach($rights as $right) $wgGroupPermissions[$eoe_user][$right] = false;
    }
    //initialize allowed rights
    $rights = array('read', 'createtalk', 'editmyoptions', 'editmyprivateinfo', 'editmyusercss', 'editmyuserjs', 'editmywatchlist', 'viewmyprivateinfo', 'viewmywatchlist');
    foreach(array('EoL_Contributor', 'EoL_Administrator') as $eoe_user) //'EoE_Member'
    {
        foreach($rights as $right) $wgGroupPermissions[$eoe_user][$right] = true;
    }
    //initialize basic rights
    $rights = array('createpage', 'edit', 'delete', 'undelete', 'upload', 'sendemail'); //, 'apihighlimits', 'writeapi', 'bot'
    foreach(array('EoL_Contributor', 'EoL_Administrator') as $eoe_user) //'EoE_Administrator'
    {
        foreach($rights as $right) $wgGroupPermissions[$eoe_user][$right] = true;
    }
    //initialize special rights
    $rights = array('createaccount', 'userrights', 'move', 'suppressredirect', 'confirmaccount', 'sendbatchemail', 'sendemail');
    foreach(array('EoL_Administrator') as $eoe_user) //'EoE_Administrator'
    {
        foreach($rights as $right) $wgGroupPermissions[$eoe_user][$right] = true;
    }

    /*
    Public - can request to be a member, but the application needs to be approved.
    Administrator - does the approving and setting up the account. 

    Literature Editor project groups:
        Public
        EoL Data Provider
        EoL Administrator

    MediaWiki default groups
        administrator   
        bureaucrat      
        user            
    */


## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = "vector";

# Enabled skins.
# The following skins were automatically enabled:
wfLoadSkin( 'CologneBlue' );
wfLoadSkin( 'Modern' );
wfLoadSkin( 'MonoBook' );
wfLoadSkin( 'Vector' );


# Enabled Extensions. Most extensions are enabled by including the base extension file here
# but check specific extension documentation for more details
# The following extensions were automatically enabled:

wfLoadExtension( 'Cite' );
wfLoadExtension( 'CiteThisPage' );
require_once "$IP/extensions/ConfirmEdit/ConfirmEdit.php";
wfLoadExtension( 'Gadgets' );
wfLoadExtension( 'ImageMap' );
wfLoadExtension( 'InputBox' );
wfLoadExtension( 'Interwiki' );
wfLoadExtension( 'LocalisationUpdate' );
wfLoadExtension( 'Nuke' );
wfLoadExtension( 'ParserFunctions' );
wfLoadExtension( 'PdfHandler' );
wfLoadExtension( 'Poem' );
wfLoadExtension( 'Renameuser' );
wfLoadExtension( 'SpamBlacklist' );
wfLoadExtension( 'SyntaxHighlight_GeSHi' );
wfLoadExtension( 'TitleBlacklist' );
wfLoadExtension( 'WikiEditor' );
require_once "$IP/extensions/CreatePage/CreatePage.php";            //added by Eli Sep 8
require_once "$IP/extensions/Lockdown/Lockdown.php";                //added by Eli Sep 8
require_once("$IP/extensions/TalkRight/TalkRight.php");             //ver 1.5.1 -> This makes EoE_Member write to Talk/Discussion pages but readonly to regular pages
require_once "$IP/extensions/ConfirmAccount/ConfirmAccount.php";    //added by Eli Sep 9
require_once("$IP/extensions/EmailUsers/EmailUsers.php");           //added by Eli Oct 20
wfLoadExtension( 'EmbedVideo' );
require_once "$IP/extensions/UserFunctions/UserFunctions.php";      //added 8-Jun-2016

# End of automatically generated settings.
# Add more configuration options below.

//================================================= UserFunctions: https://www.mediawiki.org/wiki/Extension:UserFunctions
$wgUFEnablePersonalDataFunctions = true;
$wgUFAllowedNamespaces = array(NS_MAIN => true, NS_USER => true);
// $wgUFEnableSpecialContexts = false;
//================================================= 

$wgFileExtensions = array_merge($wgFileExtensions, explode(" ", "pdf xls xlsx txt doc png ppt ods jp2 webp PDF XLS XLSX TXT DOC PNG PPT ODS JP2 WEBP svg png jpg jpeg gif bmp SVG PNG JPG JPEG GIF BMP")); //e.g. array('txt', 'pdf', 'doc') by Eli
$wgFileExtensions = array_unique($wgFileExtensions); 
// print_r($wgFileExtensions);exit;

//================================================= EoL: Literature Editor
if($conf['use_smtp'])
{
    $wgSMTP = array('host'      => 'ssl://smtp.gmail.com',
                    'IDHost'    => 'gmail.com',
                    'port'      => 465,
                    'username'  => 'eagbayanieol@gmail.com', //turn on the radio here for this account to work: https://www.google.com/settings/u/1/security/lesssecureapps
                    'password'  => 'erjaeol1309',
                    'auth'      => true);
}

$wgEmailAuthentication  = true;
$wgEnableEmail          = true;
$wgAllowHTMLEmail       = true;
$wgEnableUserEmail      = true;
$wgEmergencyContact     = "eagbayani@eol.org";
$wgPasswordSender       = "eagbayani@eol.org";
$wgEnotifUserTalk       = true;
$wgEnotifWatchlist      = true;

// echo "\n" . $( '#t-emailuser' ).length ? true : false;
//=================================================

//from CreatePage
$wgCreatePageEditExisting = true;
$wgCreatePageUseVisualEditor = true;
//=================================================

//from TalkRight
$wgGroupPermissions['EoL_Contributor']['talk']      = true;
$wgGroupPermissions['EoL_Administrator']['talk'] = true;
//=================================================

// Add namespaces.
$wgExtraNamespaces[NS_ForHarvesting]      = "ForHarvesting";
$wgExtraNamespaces[NS_ForHarvesting_TALK] = "ForHarvesting_talk"; // Note underscores in the namespace name.

//=================================================from Lockdown

// $wgActionLockdown['history'] = array('user'); //only logged-in users can view history - working but used below instead
$wgActionLockdown['history'] = array('EoL_Contributor', 'EoL_Administrator');
/* use this to disable viewing of source:
https://www.mediawiki.org/wiki/Extension:ProtectSource
*/

$wgSpecialPageLockdown['*']         = array('EoL_Contributor', 'EoL_Administrator');
$wgSpecialPageLockdown['BlockList'] = array('EoL_Contributor', 'EoL_Administrator');
$wgSpecialPageLockdown['Export']    = array('EoL_Contributor', 'EoL_Administrator');
// $wgSpecialPageLockdown['Version'] = array('bureaucrat', 'sysop'); not sure yet what it does "Version"

/* orig
$wgNamespacePermissionLockdown[NS_ForHarvesting]['*']      = array('EoL_Contributor', 'EoL_Administrator');
*/
$wgNamespacePermissionLockdown[NS_ForHarvesting]['read']      = array('EoL_Contributor');
$wgNamespacePermissionLockdown[NS_ForHarvesting]['edit']      = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_ForHarvesting]['move']      = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_ForHarvesting]['delete']    = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_ForHarvesting]['protect']   = array('');


$wgNamespacePermissionLockdown[NS_ForHarvesting_TALK]['*'] = array('EoL_Contributor', 'EoL_Administrator');

// $wgNamespacePermissionLockdown[NS_MAIN]['*']             = array('EoL_Contributor', 'EoL_Administrator'); //new - comment since no one should edit NS_MAIN
$wgNamespacePermissionLockdown[NS_TALK]['*']             = array('EoL_Contributor', 'EoL_Administrator');
$wgNamespacePermissionLockdown[NS_MAIN]['read']          = array('developer', 'script', 'maintenance script', 'maintenance', 'user', 'bot', 'sysop', 'administrator', 'bureaucrat', 'api', 'user', 'autoconfirmed');
$wgNamespacePermissionLockdown[NS_MAIN]['read']          = array('*');
$wgNamespacePermissionLockdown[NS_MAIN]['edit']          = array(''); //no one can edit //new - comment since no one should edit NS_MAIN


/* To modify NS_MEDIAWIKI & NS_MEDIAWIKI_TALK user must be both 'administrator' and 'EoL_Administrator' */
$spaces = array(NS_MEDIAWIKI, NS_MEDIAWIKI_TALK);
foreach($spaces as $space)
{
    $wgNamespacePermissionLockdown[$space]['edit']         = array('EoL_Administrator');
    $wgNamespacePermissionLockdown[$space]['createpage']   = array('EoL_Administrator');
    $wgNamespacePermissionLockdown[$space]['delete']       = array(''); //no one can delete
    $wgNamespacePermissionLockdown[$space]['undelete']     = array(''); //no one can undelete
    $wgNamespacePermissionLockdown[$space]['move']         = array(''); //no one can move
}


$wgNamespacePermissionLockdown[NS_MAIN]['edit']         = array('EoL_Administrator'); //new - comment since no one should edit NS_MAIN
$wgNamespacePermissionLockdown[NS_MAIN]['move']         = array('EoL_Administrator'); //new - comment since no one should edit NS_MAIN
$wgNamespacePermissionLockdown[NS_MAIN]['protect']      = array(''); //new - comment since no one should edit NS_MAIN

$wgNamespacePermissionLockdown[NS_MAIN]['createpage']   = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_MAIN]['delete']       = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_MAIN]['undelete']     = array('EoL_Administrator');

$wgNamespacePermissionLockdown[NS_TALK]['edit']         = array('EoL_Contributor', 'EoL_Administrator');
$wgNamespacePermissionLockdown[NS_TALK]['createpage']   = array('EoL_Contributor', 'EoL_Administrator');
$wgNamespacePermissionLockdown[NS_TALK]['delete']       = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_TALK]['undelete']     = array('EoL_Administrator');

$wgNamespacePermissionLockdown[NS_PROJECT]['edit']         = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_PROJECT]['createpage']   = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_PROJECT]['delete']       = array('EoL_Administrator');
$wgNamespacePermissionLockdown[NS_PROJECT]['undelete']     = array('EoL_Administrator');


/*
0 	: 	NS_MAIN 	
1 	Talk: 	NS_TALK 	
2 	User: 	NS_USER 	
3 	User_Talk: 	NS_USER_TALK 	
4 	Project: 	NS_PROJECT 	
5 	Project_Talk: 	NS_PROJECT_TALK 	
6 	File: 	NS_FILE 	
7 	File_Talk: 	NS_FILE_TALK 	
8 	MediaWiki: 	NS_MEDIAWIKI 	
9 	MediaWiki_Talk: 	NS_MEDIAWIKI_TALK 	
10 	Template: 	NS_TEMPLATE 	
11 	Template_Talk: 	NS_TEMPLATE_TALK 	
12 	Help: 	NS_HELP 	
13 	Help_Talk: 	NS_HELP_TALK 	
14 	Category: 	NS_CATEGORY 	
15 	Category_Talk: 	NS_CATEGORY_TALK
*/


//=================================================

//from ConfirmAccount

//set to minimun requirements:
$wgMakeUserPageFromBio = false;
$wgAutoWelcomeNewUsers = false;
$wgConfirmAccountRequestFormItems = array(  'UserName'        => array( 'enabled' => true ),
                                            'RealName'        => array( 'enabled' => true ),
                                            'Biography'       => array( 'enabled' => true, 'minWords' => 0 ),
                                            'AreasOfInterest' => array( 'enabled' => true ),
                                            'CV'              => array( 'enabled' => false ),
                                            'Notes'           => array( 'enabled' => true ),
                                            'Links'           => array( 'enabled' => true ),
                                            'TermsOfService'  => array( 'enabled' => true ));


$wgConfirmAccountRequestFormItems['Biography']['minWords'] = 0;
// $wgGroupPermissions['sysop']['createaccount'] = false; //do this to disable sysop from creating accounts
$wgConfirmAccountContact = 'eagbayani@eol.org'; // a beaurocrat or EoL_Administrator or EoE_Managing_Editor



$wgFileStore['accountreqs']['directory']       = "$IP/images/accountreqs";
$wgFileStore['accountreqs']['url'] = null; 
$wgFileStore['accountreqs']['hash'] = 3;

$wgFileStore['accountcreds']['directory']       = "$IP/images/accountcreds";
$wgFileStore['accountcreds']['url'] = null; 
$wgFileStore['accountcreds']['hash'] = 3;

$wgFileStore['deleted']['directory'] = "$IP/images/imagesDeleted";
$wgFileStore['deleted']['url'] = null; 
$wgFileStore['deleted']['hash'] = 3;


$wgAccountRequestThrottle  = 100;
//=================================================
//WikiEditor settings
# Enables use of WikiEditor by default but still allows users to disable it in preferences
$wgDefaultUserOptions['usebetatoolbar'] = 1;

# Enables link and table wizards by default but still allows users to disable them in preferences
$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;

# Displays the Preview and Changes tabs
$wgDefaultUserOptions['wikieditor-preview'] = 1;

# Displays the Publish and Cancel buttons on the top right side
$wgDefaultUserOptions['wikieditor-publish'] = 0;

//================================================
// $wgSpamBlacklistFiles = array(
//    "[[m:Spam blacklist]]",
//    "https://en.wikipedia.org/wiki/MediaWiki:Spam-blacklist"
// );
// print_r($wgSpamBlacklistFiles);


//================================================ from extension EmailUsers
/*
Configuration parameters

$wgEmailUsersMaxRecipients  : Defines the max number of recipients
$wgEmailUsersUseJobQueue    : Use Manual:Job queue when sending mails
User rights                 : sendbatchemail
                              Allows users to use this extension. General rights for sending mails is also required.
*/
$wgEmailUsersMaxRecipients  = 5;    //: Defines the max number of recipients
$wgEmailUsersUseJobQueue = true;    //: Use Manual:Job queue when sending mails

//================================================
$wgShowIPinHeader = false;
//==================================================
// $wgReadOnly = 'Upgrading to MediaWiki 1.26.2'; //uncomment this line everytime we upgrade to have database-readonly access.
//================================================

// echo "\n" . $wgUser->getName() . "\n"; ---deprecated already
// echo "\n" . $_COOKIE['wiki_literatureeditorUserName'] . "\n"; //was used in /Custom/controllers/bhl_access.php;
// var_dump(ini_get('include_path')); //just to see the include_path

/* not needed at this point
$wgEnableAPI = true;
$wgEnableWriteAPI = true;
*/

/* helpful cookie variable
print_r($_COOKIE);
*/