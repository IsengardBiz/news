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

function news_search($queryarray, $andor, $limit, $offset = 0, $userid = 0) {
	
	global $newsConfig, $icmsConfigSearch;
	
	$articlesArray = $ret = array();
	$count = $number_to_process = $pubs_left = '';
	
	$news_article_handler = icms_getModuleHandler('article', basename(dirname(dirname(__FILE__))),
		'news');
	$articlesArray = $news_article_handler->getArticlesForSearch($queryarray, $andor, $limit,
		$offset, $userid);
	
	// Count the number of records
	$count = count($articlesArray);
	
	// The number of records actually containing publication objects is <= $limit, the rest are padding
	// How to figure out how what the actual number of publications is? Important for pagination
	$pubs_left = ($count - ($offset + $icmsConfigSearch['search_per_page']));
	if ($pubs_left < 0) {
		$number_to_process = $icmsConfigSearch['search_per_page'] + $pubs_left; // $pubs_left is negative
	} else {
		$number_to_process = $icmsConfigSearch['search_per_page'];
	}
	
	// Process the actual articles (not the padding)
	for ($i = 0; $i < $number_to_process; $i++) {
			if (is_object($articlesArray[$i])) { // Required to prevent crashing on profile view
			$item['image'] = "images/article.png";
			$item['link'] = $articlesArray[$i]->getItemLink(TRUE);
			$item['title'] = $articlesArray[$i]->getVar('title');
			$item['time'] = $articlesArray[$i]->getVar('date', 'e');
			$item['uid'] = $articlesArray[$i]->getVar('submitter', 'e');
			$ret[] = $item;
			unset($item);
		}
	}
	
	// Restore the padding (required for 'hits' information and pagination controls). The offset
	// must be padded to the left of the results, and the remainder to the right or else the search
	// pagination controls will display the wrong results (which will all be empty).
	// Left padding = -($limit + $offset)
	$ret = array_pad($ret, -($offset + $number_to_process), 1);
	
	// Right padding = $count
	$ret = array_pad($ret, $count, 1);
	
	return $ret;
}