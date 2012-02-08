<?php
/**
* Generating an RSS feed
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

/** Include the module's header for all pages */
include_once 'header.php';
include_once ICMS_ROOT_PATH.'/header.php';

/**
 * Encodes entities to ensure feed content complies with the RSS specification
 *
 * @param string $field
 * @return string
 */
function encode_entities($field) {
	$field = htmlspecialchars(html_entity_decode($field, ENT_QUOTES, 'UTF-8'),
		ENT_NOQUOTES, 'UTF-8');
	return $field;
}

$newsModule = icms_getModuleInfo(basename(dirname(__FILE__)));
$clean_tag_id = $sort_order = '';

$clean_tag_id = isset($_GET['tag_id']) ? intval($_GET['tag_id']) : false;

include_once ICMS_ROOT_PATH . '/modules/' . basename(dirname(__FILE__))
	. '/class/icmsfeed.php';
$news_feed = new IcmsFeed();
$news_article_handler = icms_getModuleHandler('article', basename(dirname(__FILE__)), 'news');

$sprocketsModule = icms_getModuleInfo('sprockets');
if (icms_get_module_status("sprockets")) {
	$sprockets_taglink_handler = icms_getModuleHandler('taglink',
			$sprocketsModule->getVar('dirname'), 'sprockets');
	$sprockets_tag_handler = icms_getModuleHandler('tag',
			$sprocketsModule->getVar('dirname'), 'sprockets');
}

// generates a feed of recent news articles across all tags
if (empty($clean_tag_id) || !icms_get_module_status("sprockets")) {
	$feed_title = _CO_NEWS_NEW;
	$site_name = encode_entities($icmsConfig['sitename']);
	$tag_title = _CO_NEWS_ALL;

	$news_feed->title = $site_name . ' - ' . $feed_title;
	$news_feed->url = NEWS_URL . 'article.php';
	$news_feed->description = _CO_NEWS_NEW_DSC . $site_name . '.';
	$news_feed->language = _LANGCODE;
	$news_feed->charset = _CHARSET;
	$news_feed->category = $newsModule->getVar('name');

	$url = ICMS_URL . '/images/logo.gif';
	$news_feed->image = array('title' => $news_feed->title, 'url' => $url,
			'link' => $news_feed->url);
	$news_feed->width = 144;
	$news_feed->atom_link = '"' . NEWS_URL . 'rss.php"';

	$criteria = new icms_db_criteria_Compo();
	$criteria->add(new icms_db_criteria_Item('online_status', true));
	$criteria->add(new icms_db_criteria_Item('date', time(), '<'));
	$criteria->setStart(0);
	$criteria->setLimit($newsModule->config['number_rss_items']);

	$criteria->setSort('date');
	$criteria->setOrder('DESC');

	$articleArray = $news_article_handler->getObjects($criteria);
	
} else { // generates tag-specific feeds
	
	// need to remove html tags and problematic characters to meet RSS spec
	$tagObj = $sprockets_tag_handler->get($clean_tag_id);
	$site_name = encode_entities($icmsConfig['sitename']);
	$tag_title = encode_entities($tagObj->getVar('title'));
	$tag_description = strip_tags($tagObj->getVar('description'));
	$tag_description = encode_entities($tag_description);

	$news_feed->title = $site_name . ' - ' . $tag_title;
	$news_feed->url = NEWS_URL . 'article.php?tag_id=' . $tagObj->getVar('id');
	$news_feed->description = $tag_description;
	$news_feed->language = _LANGCODE;
	$news_feed->charset = _CHARSET;
	$news_feed->category = $newsModule->getVar('name');

	// if there's a tag icon, use it as the feed image
	if ($tagObj->getVar('icon', 'e')) {
		$url = $tagObj->getImageDir() . $tagObj->getVar('icon', 'e');
	} else {
		$url = ICMS_URL . 'images/logo.gif';
	}
	$news_feed->image = array('title' => $news_feed->title, 'url' => $url,
			'link' => NEWS_URL . 'rss.php?tag_id='
			. $tagObj->getVar('id'));
	$news_feed->width = 144;
	$news_feed->atom_link = '"' . NEWS_URL . 'rss.php?tag_id=' . $tagObj->getVar('id') . '"';
	
	// retrieve articles relevant to this tag using a JOIN to the taglinks table

	$query = $rows = $tag_article_count = '';

	$query = "SELECT * FROM " . $news_article_handler->table . ", "
			. $sprockets_taglink_handler->table
			. " WHERE `article_id` = `iid`"
			. " AND `online_status` = '1'"
			. " AND `date` < '" . time() . "'"
			. " AND `tid` = '" . $clean_tag_id . "'"
			. " AND `mid` = '" . $newsModule->getVar('mid') . "'"
			. " AND `item` = 'article'"
			. " ORDER BY `date` DESC"
			. " LIMIT " . $newsModule->config['number_rss_items'];

	$result = icms::$xoopsDB->query($query);

	if (!$result) {
		echo 'Error';
		exit;

	} else {

		$rows = $news_article_handler->convertResultSet($result);
		foreach ($rows as $key => $row) {
			$articleArray[$row->getVar('article_id')] = $row;
		}
	}
}

// prepare an array of articles
foreach($articleArray as $article) {
	$flattened_article = $article->toArray();

	// check if creator or submitter should be designated as author
	if ($newsModule->config['display_creator'] == false) {
		$creator = $site_name;
	} else {
		if ($newsModule->config['use_submitter_as_creator'] == true) {
			$member_handler = icms::handler('icms_member');
			$user = & $member_handler->getUser($article->getVar('submitter', 'e'));
			$creator = $user->getVar('uname');
		} else {
			$creator = $article->getVar('creator', 'e');
			$creator = explode('|', $creator);
			foreach ($creator as &$individual) {
				$individual = encode_entities($individual);
			}
		}
	}
	$creator = encode_entities($creator);

	// check if there is an extended text
	$description = encode_entities($flattened_article['description']);
	$title = encode_entities($flattened_article['title']);
	$link = encode_entities($flattened_article['itemUrl']);

	$news_feed->feeds[] = array (
		'title' => $title,
		'link' => $link,
		'description' => $description,
		'author' => $creator,
		// pubdate must be a RFC822-date-time EXCEPT with 4-digit year or the feed won't validate
		'pubdate' => date(DATE_RSS, $article->getVar('date', 'e')),
		'guid' => $link,
		'category' => $tag_title
		);
}

$news_feed->render();