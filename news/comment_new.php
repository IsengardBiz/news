<?php
/**
* New comment form
*
* This file holds the configuration information of this module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

include_once 'header.php';

$com_itemid = isset($_GET['com_itemid']) ? intval($_GET['com_itemid']) : 0;

if ($com_itemid > 0) {
	
	$news_article_handler = icms_getModuleHandler('article', basename(dirname(__FILE__)), 'news');
	$articleObj = $news_article_handler->get($com_itemid);
	
	if ($articleObj && !$articleObj->isNew()) {
		$bodytext = $articleObj->getVar('description');
		if ($bodytext != '') {
			$com_replytext = '<br /><br />' . $bodytext . '';
		}
		$com_replytitle = $articleObj->getVar('title');
		include_once ICMS_ROOT_PATH . '/include/comment_new.php';
	}
}