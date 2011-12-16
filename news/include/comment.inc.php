<?php
/**
* Comment include file
*
* File holding functions used by the module to hook with the comment system of ImpressCMS
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

/**
 * Updates news comments
 *
 * @param int $article_id
 * @param int $total_num 
 */
function news_com_update($article_id, $total_num) {
    $news_article_handler = icms_getModuleHandler('article', basename(dirname(dirname(__FILE__))),
		'news');
    $news_article_handler->updateComments($article_id, $total_num);
}

function news_com_approve(&$comment) {
    // notification mail here
}