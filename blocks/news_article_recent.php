<?php
/**
 * New products (recent items) block file
 *
 * This file holds the functions needed for the new products block
 *
 * @copyright	http://smartfactory.ca The SmartFactory
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @author		marcan aka Marc-Andre Lanciault <marcan@smartfactory.ca>
 * @author		Madfish
 * @since		1.0
 * @package		news
 * @version		$Id$
 */

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * Prepares the recent news block for display
 *
 * @param array $options
 * @return string 
 */
function news_article_recent_show($options) {
	
	$newsModule = icms_getModuleInfo('news');
	$sprocketsModule = icms_getModuleInfo('sprockets');
	
	include_once(ICMS_ROOT_PATH . '/modules/' . $newsModule->dirname() . '/include/common.php');
	
	$news_article_handler = icms_getModuleHandler('article', $newsModule->dirname(), 'news');

	// retrieve the last XX articles

	if ($sprocketsModule && $options[1]) { // filter by tag
	
		global $xoopsDB;

		$query = $rows = $tag_article_count = '';
		$article_object_array = array();
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->dirname(),
				'sprockets');
		
		$query = "SELECT * FROM " . $news_article_handler->table . ", "
					. $sprockets_taglink_handler->table
					. " WHERE `article_id` = `iid`"
					. " AND `online_status` = '1'"
					. " AND `date` <= '" . time() . "'"
					. " AND `tid` = '" . $options[1] . "'"
					. " AND `mid` = '" . $newsModule->mid() . "'"
					. " AND `item` = 'article'"
					. " ORDER BY `date` DESC"
					. " LIMIT " . '0, ' . $options[0]++;

		$result = $xoopsDB->query($query);

		if (!$result) {
			echo 'Error: Recent articles block';
			exit;

		} else {

			$rows = $news_article_handler->convertResultSet($result);
			foreach ($rows as $key => $row) {
				$article_object_array[$row->getVar('article_id')] = $row;
			}
		}

		$block['recent_news_articles'] = $article_object_array;

	} else { // do not filter by tag

		$criteria = new CriteriaCompo();
		$criteria->setStart(0);
		$criteria->setLimit($options[0] +1); // spotlighted article will not be included in the list
		$criteria->setSort('date');
		$criteria->setOrder('DESC');
		$criteria->add(new Criteria('online_status', true));
		$criteria->add(new Criteria('date', time(), '<'));

		// retrieve the news articles to show in the block
		$block['recent_news_articles'] = $news_article_handler->getObjects($criteria, true, true);
	}

	// check if spotlight mode is active, and if spotlight article has already been retrieved
	if ($options[4] == true && (!empty($block['recent_news_articles']))) {
		if (array_key_exists($options[5], $block['recent_news_articles'])) {
			$spotlightObj = $block['recent_news_articles'][$options[5]];
			unset($block['recent_news_articles'][$options[5]]);
		} elseif ($options[5] == 0) {
			$spotlightObj = array_shift($block['recent_news_articles']);
		} else {
			$spotlightObj = $news_article_handler->get($options[5]);
			$trim = array_pop($block['recent_news_articles']);
		}

		// prepare spotlight for display
		global $newsConfig;
		
		//problem - stylesheet does not seem to get imported when block is cached
		//global $xoTheme;
		//$xoTheme->addStylesheet(ICMS_URL . '/modules/news/module.css');

		$block['recent_news_spotlight_title'] = $spotlightObj->getVar('title');
		if ($spotlightObj->getVar('lead_image')) {
			$block['recent_news_spotlight_display_lead_image'] = $spotlightObj->getVar('display_lead_image', 'e');
			$block['recent_news_spotlight_display_topic_image'] = $spotlightObj->getVar('display_topic_image', 'e');
			$block['recent_news_spotlight_lead_image'] = $spotlightObj->get_lead_image_tag();
			$block['recent_news_lead_image_display_width'] = icms_getConfig('lead_image_display_width', 'news');
		} else {
			$block['recent_news_spotlight_lead_image'] = false;
		}
		$block['recent_news_spotlight_description'] = $spotlightObj->getVar('description');
		$block['recent_news_spotlight_link'] = $spotlightObj->getItemLink(true);
		$block['recent_news_title'] = _MB_NEWS_ARTICLE_SPOTLIGHT_RECENT_ARTICLES;
	}

	// prepare article links for display
	foreach ($block['recent_news_articles'] as &$article) {
		$title = $article->getVar('title');
		
		// trim the title if its length exceeds the block preferences
		if (strlen($title) > $options[3]) {
			$article->setVar('title', substr($title, 0, ($options[3] - 3)) . '...');
		}
		
		// formats timestamp according to the block options
		$date = $article->getVar('date', 'e');
		$date = date($options[2], $date);
		$article = $article->toArrayWithoutOverrides(true);
		$article['date'] = $date;		
	}
	return $block;
}

/**
 * Edit options for the recent news block
 *
 * @param array $options
 * @return string 
 */
function news_article_recent_edit($options) {
	
	$newsModule = icms_getModuleInfo('news');
	include_once(ICMS_ROOT_PATH . '/modules/' . $newsModule->dirname() . '/include/common.php');
	$news_article_handler = icms_getModuleHandler('article', $newsModule->dirname(), 'news');

	// select number of recent articles to display in the block
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_NEWS_ARTICLE_RECENT_LIMIT . '</td>';
	$form .= '<td>' . '<input type="text" name="options[]" value="' . $options[0] . '" /></td>';
	$form .= '</tr>';

	// optionally display results from a single tag - only if sprockets module is installed
	$sprocketsModule = icms_getModuleInfo('sprockets');
	if ($sprocketsModule) {
		$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->dirname(),
				'sprockets');
		$form .= '<tr><td>' . _MB_NEWS_ARTICLE_RECENT_TAG . '</td>';
		// Parameters XoopsFormSelect: ($caption, $name, $value = null, $size = 1, $multiple = false)
		$form_select = new XoopsFormSelect('', 'options[]', $options[1], '1', false);
		$tagList = $sprockets_tag_handler->getList();
		$tagList = array(0 => 'All') + $tagList;
		$form_select->addOptionArray($tagList);
		$form .= '<td>' . $form_select->render() . '</td></tr>';
	}
	
	// customise date format string as per PHP's date() method
	$form .= '<td>' . _MB_NEWS_ARTICLE_DATE_STRING . '</td>';	
	$form .= '<td>' . '<input type="text" name="options[2]" value="' . $options[2]
		. '" /></td></tr>';
	
	// limit title length
	$form .= '<tr><td>' . _MB_NEWS_ARTICLE_TITLE_LENGTH . '</td>';
	$form .= '<td>' . '<input type="text" name="options[3]" value="' . $options[3]
		. '" /></td></tr>';
	
	// activate spotlight feature
	$form .= '<tr><td>' . _MB_NEWS_ARTICLE_ACTIVATE_SPOTLIGHT . '</td>';
	$form .= '<td><input type="radio" name="options[4]" value="1"';
		if ($options[4] == 1) {
			$form .= ' checked="checked"';
		}
		$form .= '/>' . _MB_NEWS_ARTICLE_YES;
		$form .= '<input type="radio" name="options[4]" value="0"';
		if ($options[4] == 0) {
			$form .= 'checked="checked"';
		}
		$form .= '/>' . _MB_NEWS_ARTICLE_NO . '</td></tr>';	
	
	
	// build select box for choosing article to spotlight - need to filter by tag (if set)
	if ($sprocketsModule && $options[1]) {
		
		global $xoopsDB;

		$query = $rows = $tag_article_count = '';
		$article_array = array(0 => _MB_NEWS_ARTICLE_MOST_RECENT_ARTICLE);
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->dirname(),
				'sprockets');
		
		$query = "SELECT * FROM " . $news_article_handler->table . ", "
					. $sprockets_taglink_handler->table
					. " WHERE `article_id` = `iid`"
					. " AND `online_status` = '1'"
					. " AND `date` <= '" . time() . "'"
					. " AND `tid` = '" . $options[1] . "'"
					. " AND `mid` = '" . $newsModule->mid() . "'"
					. " AND `item` = 'article'"
					. " ORDER BY `date` DESC"
					. " LIMIT " . '0, ' . $options[0]++;

		$result = $xoopsDB->query($query);

		if (!$result) {
			echo 'Error: Recent articles block';
			exit;

		} else {

			$rows = $news_article_handler->convertResultSet($result);
			foreach ($rows as $key => $row) {
				$article_array[$row->getVar('article_id')] = $row->title();
			}
		}
		
	} else {
		
		$criteria = new CriteriaCompo();
		$criteria->setStart(0);
		$criteria->setLimit($options[0]+1);
		$criteria->setSort('date');
		$criteria->setOrder('DESC');
		$criteria->add(new Criteria('online_status', true));
		$criteria->add(new Criteria('date', time(), '<'));

		// retrieve the articles
		$article_array = $news_article_handler->getList($criteria);
		$article_array = array(0 => _MB_NEWS_ARTICLE_MOST_RECENT_ARTICLE) + $article_array;
	}
	
	// build a select box of article titles
	$form .= '<tr><td>' . _MB_NEWS_ARTICLE_SPOTLIGHTED_ARTICLE . '</td>';
	// Parameters XoopsFormSelect: ($caption, $name, $value = null, $size = 1, $multiple = false)
	$form_spotlight = new XoopsFormSelect('', 'options[5]', $options[5], '1', false);
	$form_spotlight->addOptionArray($article_array);
	$form .= '<td>' . $form_spotlight->render() . '</td></tr>';
	$form .= '</table>';

	return $form;
}