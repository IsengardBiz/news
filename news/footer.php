<?php
/**
* Footer page included at the end of each page on user side of the mdoule
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

$icmsTpl->assign("news_adminpage", news_getModuleAdminLink());
$icmsTpl->assign("news_is_admin", $news_isAdmin);
$icmsTpl->assign('news_url', NEWS_URL);
$icmsTpl->assign('news_images_url', NEWS_IMAGES_URL);

$xoTheme->addStylesheet(NEWS_URL . 'module'.(( defined("_ADM_USE_RTL") && _ADM_USE_RTL )?'_rtl':'')
	. '.css');

include_once(ICMS_ROOT_PATH . '/footer.php');