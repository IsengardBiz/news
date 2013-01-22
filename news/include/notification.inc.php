<?php
/**
* Common functions used by the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/
/**
 * Notification lookup function
 *
 * This function is called by the notification process to get an array contaning information
 * about the item for which there is a notification
 *
 * @param string $category category of the notification
 * @param int $item_id id f the item related to this notification
 *
 * @return array containing 'name' and 'url' of the related item
 */
function news_notify_iteminfo($category, $item_id){
	
	$item = array();
	
    if ($category == 'global') {
        $item['name'] = '';
        $item['url'] = '';
        return $item;
    }
	
	if ($category == 'publication') {

		$news_article_handler = icms_getModuleHandler('article',
			basename(dirname(dirname(__FILE__))), 'news');
		$articleObj = $news_article_handler->get($item_id);
		if ($articleObj) {
			$item['name'] = $articleObj->title();
			$item['url'] = ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/article.php?article_id=' . intval($item_id);
			return $item;
		} else {
			return null;
		}
	}
}