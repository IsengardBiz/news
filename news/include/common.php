<?php
/**
* Common file of the module included on all pages of the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

if(!defined("NEWS_DIRNAME")) define("NEWS_DIRNAME", $modversion['dirname'] = 
	basename(dirname(dirname(__FILE__))));
if(!defined("NEWS_URL")) define("NEWS_URL", ICMS_URL.'/modules/' . NEWS_DIRNAME . '/');
if(!defined("NEWS_ROOT_PATH")) define("NEWS_ROOT_PATH", ICMS_ROOT_PATH . '/modules/'
	. NEWS_DIRNAME . '/');
if(!defined("NEWS_IMAGES_URL")) define("NEWS_IMAGES_URL", NEWS_URL . 'images/');
if(!defined("NEWS_ADMIN_URL")) define("NEWS_ADMIN_URL", NEWS_URL . 'admin/');

// Include the common language file of the module
icms_loadLanguageFile('news', 'common');

include_once(NEWS_ROOT_PATH . "include/functions.php");

// Creating the module object to make it available throughout the module
$newsModule = icms_getModuleInfo(NEWS_DIRNAME);

if (is_object($newsModule)){
	$news_moduleName = $newsModule->getVar('name');
}

// Find if the user is admin of the module and make this info available throughout the module
$news_isAdmin = icms_userIsAdmin(NEWS_DIRNAME);

// Creating the module config array to make it available throughout the module
$newsConfig = icms_getModuleConfig(NEWS_DIRNAME);

// creating the icmsPersistableRegistry to make it available throughout the module
global $icmsPersistableRegistry;
$icmsPersistableRegistry = IcmsPersistableRegistry::getInstance();