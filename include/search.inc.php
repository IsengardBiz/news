<?php
/**
* News version infomation
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

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * Provides search functionality for the news module
 *
 * @param array $queryarray
 * @param string $andor
 * @param int $limit
 * @param int $offset
 * @param int $userid
 * @return array 
 */

function news_search($queryarray, $andor, $limit, $offset, $userid) {
	
	global $newsConfig;
	
	$news_article_handler = icms_getModuleHandler('article', basename(dirname(dirname(__FILE__))),
		'news');
	$articlesArray = $news_article_handler->getArticlesForSearch($queryarray, $andor, $limit,
		$offset, $userid);

	$ret = array();

	foreach ($articlesArray as $articleArray) {
		$item['image'] = "images/article.png";
		$item['link'] = $articleArray->getItemLink(TRUE);
		$item['title'] = $articleArray->getVar('title');
		$item['time'] = $articleArray->getVar('date', 'e');
		$item['uid'] = $articleArray->getVar('submitter', 'e');
		$ret[] = $item;
		unset($item);
	}
	return $ret;
}