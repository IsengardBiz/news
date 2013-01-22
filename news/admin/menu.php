<?php
/**
* Configuring the amdin side menu for the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

$i = 0;

$adminmenu[$i]['title'] = _MI_NEWS_ARTICLES;
$adminmenu[$i]['link'] = 'admin/article.php';

global $icmsConfig, $newsConfig;

$newsModule = icms_getModuleInfo(basename(dirname(dirname(__FILE__))));

if (isset($newsModule)) {

	$i = 0;
	
	$headermenu[$i]['title'] = _CO_ICMS_GOTOMODULE;
	$headermenu[$i]['link'] = ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)));

	$i++;
	$headermenu[$i]['title'] = _PREFERENCES;
	$headermenu[$i]['link'] = '../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod='
		. $newsModule->getVar('mid');

	$i++;
	$headermenu[$i]['title'] = _MI_NEWS_TEMPLATES;
	$headermenu[$i]['link'] = '../../system/admin.php?fct=tplsets&op=listtpl&tplset='
		. $icmsConfig['template_set'] . '&moddir=' . basename(dirname(dirname(__FILE__)));

	$i++;
	$headermenu[$i]['title'] = _CO_ICMS_UPDATE_MODULE;
	$headermenu[$i]['link'] = ICMS_URL
		. '/modules/system/admin.php?fct=modulesadmin&op=update&module='
		. basename(dirname(dirname(__FILE__)));

	$i++;
	$headermenu[$i]['title'] = _MODABOUT_ABOUT;
	$headermenu[$i]['link'] = ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
		. '/admin/about.php';
}