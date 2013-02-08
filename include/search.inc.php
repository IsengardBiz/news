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
	
	$articlesArray = $ret = array();
	$count = '';
	
	$news_article_handler = icms_getModuleHandler('article', basename(dirname(dirname(__FILE__))),
		'news');
	$articlesArray = $news_article_handler->getArticlesForSearch($queryarray, $andor, $limit,
		$offset, $userid);
	
	// Count the number of records
	$count = count($articlesArray);
	
	// Only the first $limit number of records contain publication objects, the rest are padding
	if (!$limit) {
		global $icmsConfigSearch;
		$limit = $icmsConfigSearch['search_per_page'];
	}
	
	// Ensure a value is set for offset as it will be used in calculations later
	if (!$offset) {
		$offset = 0;
	}
		
	// Process the actual publications (not the padding)
	for ($i = 0; $i < $limit; $i++) {
		$item['image'] = "images/article.png";
		$item['link'] = $articlesArray->getItemLink(TRUE);
		$item['title'] = $articlesArray->getVar('title');
		$item['time'] = $articlesArray->getVar('date', 'e');
		$item['uid'] = $articlesArray->getVar('submitter', 'e');
		$ret[] = $item;
		unset($item);
	}
	
	// Restore the padding (required for 'hits' information and pagination controls). The offset
	// must be padded to the left of the results, and the remainder to the right or else the search
	// pagination controls will display the wrong results (which will all be empty).
	// Left padding = -($limit + $offset)
	$ret = array_pad($ret, -($limit + $offset), 1);
	
	// Right padding = $count - ($limit + $offset)
	$ret = array_pad($ret, $count - ($limit + $offset), 1);
	
	return $ret;
}