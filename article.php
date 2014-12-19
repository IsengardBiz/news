<?php
/**
* Article page
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

include_once 'header.php';

$xoopsOption['template_main'] = 'news_article.html';
include_once ICMS_ROOT_PATH . '/header.php';

global $icmsConfig;

$clean_article_id = $clean_story_id = $clean_tag_id = $clean_start = $articleObj
	= $news_article_handler = $news_tag_name = '';
$articleArray = $tagList = array();

/** Use a naming convention that indicates the source of the content of the variable */
$clean_article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0 ;
$clean_tag_id = isset($_GET['tag_id']) ? intval($_GET['tag_id']) : 0 ;
$clean_start = isset($_GET['start']) ? intval($_GET['start']) : 0;

// support legacy URLs in articles imported from the old XOOPS news module (thanks Pheonyx)
$clean_story_id = isset($_GET['storyid']) ? intval($_GET['storyid']) : 0 ;
if ($clean_story_id) {
	$clean_article_id = $clean_story_id;
}

// get relative path to document root for this ICMS install
// this is required to call the image correctly if ICMS is installed in a subdirectory
$directory_name = basename(dirname(__FILE__));
$script_name = getenv("SCRIPT_NAME");
$document_root = str_replace('modules/' . $directory_name . '/article.php', '', $script_name);

// Optional tagging support (only if Sprockets module installed)
$sprocketsModule = icms::handler("icms_module")->getByDirname("sprockets");

if (icms_get_module_status("sprockets"))
{
	icms_loadLanguageFile("sprockets", "common");
	$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
	$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
	$criteria = icms_buildCriteria(array('label_type' => '0'));
	$sprockets_tag_buffer = $sprockets_tag_handler->getObjects($criteria, TRUE, TRUE);
}

// check if a single article has been requested
$news_article_handler = icms_getModuleHandler('article', basename(dirname(__FILE__)), 'news');
$criteria = icms_buildCriteria(array('online_status' => '1'));
$articleObj = $news_article_handler->get($clean_article_id, TRUE, FALSE, $criteria);

if($articleObj && !$articleObj->isNew()) {
	
	////////////////////////////////////////////////////////////////
	//////////////////// DISPLAY SINGLE ARTICLE ////////////////////
	////////////////////////////////////////////////////////////////
	
	// update hits counter
	if (!icms_userIsAdmin(icms::$module->getVar('dirname')))
	{
		$news_article_handler->updateCounter($articleObj);
	}

	// prepare object for display, unset unwanted fields as per module preferences
	$articleObj->loadTags();

	$edit_item_link = $delete_item_link = '';

	$edit_item_link = $articleObj->getEditItemLink(FALSE, TRUE, FALSE);
	$delete_item_link = $articleObj->getDeleteItemLink(FALSE, TRUE, FALSE);

	$articleArray = $articleObj->prepareArticleForDisplay(TRUE); // with DB overrides

	$articleArray['editItemLink'] = $edit_item_link;
	$articleArray['deleteItemLink'] = $delete_item_link;
	
	// do tag lookups (if sprockets module is installed)
	if (icms_get_module_status("sprockets") && !empty($articleArray['tag'])) {
		
		$tag_icons = $articleTags = array();
		$articleTags = array_flip($articleArray['tag']);
		
		foreach ($articleTags as $key => &$value) {
			if ($key) {
				$value = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(__FILE__))
				. '/article.php?tag_id=' . $sprockets_tag_buffer[$key]->getVar('tag_id', 'e') . '">' 
						. $sprockets_tag_buffer[$key]->getVar('title', 'e') . '</a>';

				// get tag icons, if available
				$icon = $sprockets_tag_buffer[$key]->getVar('icon');
				if (!empty($icon)) {
					$tag_icons[] = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(__FILE__))
						. '/article.php?tag_id=' . $sprockets_tag_buffer[$key]->getVar('tag_id', 'e') . '">'
						. $sprockets_tag_buffer[$key]->getVar('icon') . '</a>';
				}
			}
		}
		$articleArray['tag'] = implode(', ', $articleTags);
		$articleArray['icon'] = $tag_icons;
	}

	// display article rights field?
	if (icms::$module->config['display_rights'] == TRUE) {
		$icmsTpl->assign('news_display_rights', TRUE);
	}
	
	// Display RSS feed? This could probably be consolidated as it is checked twice in this script
	if (icms::$module->config['show_breadcrumb'] == FALSE)
	{
		$icmsTpl->assign('news_rss_link', 'rss.php');
		$icmsTpl->assign('news_rss_title', _CO_NEWS_SUBSCRIBE_RSS);
	}
	
	// Facebook comments integration
	if (icms::$module->config['display_facebook_comments'] == TRUE) {
		$articleArray['display_facebook_comments'] = TRUE;
		$articleArray['facebook_comments_width'] = icms::$module->config['facebook_comments_width'];
	}
	
	// Compatibility with DB templates in legacy installs (create reference, to minimise overhead)
	$articleArray['lead_image'] = &$articleArray['image'];
	$articleArray['lead_image_display_width'] = icms::$module->config['image_display_width'];
	$articleArray['image_display_width'] = &$articleArray['lead_image_display_width'];
	
	// Adjust image path document root (for correct image display in subdirectory installs)
	if ($articleArray['lead_image']) {
		$articleArray['lead_image'] = $document_root . '/' . $articleArray['lead_image'];
	}

	// Display this article
	$icmsTpl->assign('news_article', $articleArray);
	$icmsTpl->assign('news_index_view', FALSE);
	
	// Comments
	if (icms::$module->config['com_rule']) {
		$icmsTpl->assign('news_article_comment', TRUE);
		include_once ICMS_ROOT_PATH . '/include/comment_view.php';
	}
		
	/**
	 * Generating meta information for this page
	 */
	$icms_metagen = new icms_ipf_Metagen($articleObj->getVar('title'),
	$articleObj->getVar('meta_keywords','n'), $articleObj->getVar('meta_description', 'n'));
	$icms_metagen->createMetaTags();
	
} else {
		
	////////////////////////////////////////////////////////////////////
	//////////////////// DISPLAY ARTICLE INDEX PAGE ////////////////////
	////////////////////////////////////////////////////////////////////
	
	if (icms_get_module_status("sprockets")) {

		// initialise
		$form = $news_tag_name = '';
		$tagList = $rights_buffer = $rightsList = array();
		$sprockets_rights_handler = icms_getModuleHandler('rights', 
				$sprocketsModule->getVar('dirname'), 'sprockets');

		// prepare buffers to reduce queries
		$rights_buffer = $sprockets_rights_handler->getObjects(null, TRUE, FALSE);

		// append the tag to the News title
		if (array_key_exists($clean_tag_id, $sprockets_tag_buffer) && ($clean_tag_id !== 0)) {
			$news_tag_name = $sprockets_tag_buffer[$clean_tag_id]->getVar('title', 'e');
			$icmsTpl->assign('news_tag_name', $news_tag_name);
			$icmsTpl->assign('news_category_path', $sprockets_tag_buffer[$clean_tag_id]->getVar('title', 'e'));
		}
		
		// Prepare a tag select box if sprockets module is installed & set in module preferences
		if (icms::$module->config['show_tag_select_box'] == TRUE) {
			// prepare a tag navigation select box
			$tag_select_box = $sprockets_tag_handler->getTagSelectBox('article.php',
					$clean_tag_id, _CO_NEWS_ARTICLE_ALL_TAGS, TRUE, icms::$module->getVar('mid'));
			$icmsTpl->assign('news_tag_select_box', $tag_select_box);
			$icmsTpl->assign('news_show_tag_select_box', TRUE);
		}
	}
	
		// RSS feed including autodiscovery link, which is inserted in the module header
		global $xoTheme;	
		if (icms_get_module_status("sprockets") && $clean_tag_id) {
			$icmsTpl->assign('news_rss_link', 'rss.php?tag_id=' . $clean_tag_id);
			$icmsTpl->assign('news_rss_title', _CO_NEWS_SUBSCRIBE_RSS_ON
					. $sprockets_tag_buffer[$clean_tag_id]->getVar('title', 'e'));
			$rss_attributes = array('type' => 'application/rss+xml', 
				'title' => $icmsConfig['sitename'] . ' - ' . $sprockets_tag_buffer[$clean_tag_id]->getVar('title', 'e'));
			$rss_link = NEWS_URL . 'rss.php?tag_id=' . $clean_tag_id;
		} else {				
				$icmsTpl->assign('news_rss_link', 'rss.php');
				$icmsTpl->assign('news_rss_title', _CO_NEWS_SUBSCRIBE_RSS);
				$rss_attributes = array('type' => 'application/rss+xml', 
					'title' => $icmsConfig['sitename'] . ' - ' .  _CO_NEWS_NEW);
				$rss_link = NEWS_URL . 'rss.php';
		}
		$xoTheme->addLink('alternate', $rss_link, $rss_attributes);

		// list of articles, filtered by tags (if any), pagination and preferences
		$article_object_array = array();

		if ($clean_tag_id && icms_get_module_status("sprockets")) {

			/**
			 * Retrieve a list of articles JOINED to taglinks by article_id/tag_id/module_id/item
			 */

			$query = $rows = $tag_article_count = '';
			$linked_article_ids = array();
			$newsModule = icms_getModuleInfo(basename(dirname(__FILE__)));
			
			// first, count the number of articles for the pagination control
			$group_query = "SELECT count(*) FROM " . $news_article_handler->table . ", "
					. $sprockets_taglink_handler->table
					. " WHERE `article_id` = `iid`"
					. " AND `online_status` = '1'"
					. " AND `date` < '" . time() . "'"
					. " AND `tid` = '" . $clean_tag_id . "'"
					. " AND `mid` = '" . $newsModule->getVar('mid') . "'"
					. " AND `item` = 'article'";
			
			$result = icms::$xoopsDB->query($group_query);

			if (!$result) {
				echo 'Error';
				exit;
				
			} else {
				while ($row = icms::$xoopsDB->fetchArray($result)) {
					foreach ($row as $key => $count) {
						$article_count = $count;
					}
					
				}
			}

			// second, get the articles
			$query = "SELECT * FROM " . $news_article_handler->table . ", "
					. $sprockets_taglink_handler->table
					. " WHERE `article_id` = `iid`"
					. " AND `online_status` = '1'"
					. " AND `date` < '" . time() . "'"
					. " AND `tid` = '" . $clean_tag_id . "'"
					. " AND `mid` = '" . $newsModule->getVar('mid') . "'"
					. " AND `item` = 'article'"
					. " ORDER BY `date` DESC"
					. " LIMIT " . $clean_start . ", " . icms::$module->config['number_of_articles_per_page'];

			$result = icms::$xoopsDB->query($query);

			if (!$result) {
				echo 'Error';
				exit;
				
			} else {

				$rows = $news_article_handler->convertResultSet($result);
				foreach ($rows as $key => $row) {
					$article_object_array[$row->getVar('article_id')] = $row;
				}
			}
			
		} else {

			$criteria = new icms_db_criteria_Compo();

			$criteria->setStart($clean_start);
			$criteria->setLimit(icms::$module->config['number_of_articles_per_page']);
			$criteria->setSort('date');
			$criteria->setOrder('DESC');
			$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
			$criteria->add(new icms_db_criteria_Item('date', time(), '<'));
			
			$article_object_array = $news_article_handler->getObjects($criteria, TRUE, TRUE);
		}

		unset($criteria);

		if (icms_get_module_status("sprockets") && (count($article_object_array) > 0)) {

			// prepare a list of article_ids, this will be used to create a taglink buffer
			// that is used to create tag links for each article
			foreach ($article_object_array as $key => $value) {
				$linked_article_ids[] = $value->getVar('article_id');
			}
			
			$linked_article_ids = '(' . implode(',', $linked_article_ids) . ')';
			
			// prepare multidimensional array of tag_ids with article_id (iid) as key
			$taglink_buffer = $article_tag_id_buffer = array();
			$criteria = new  icms_db_criteria_Compo();
			$criteria->add(new icms_db_criteria_Item('mid', $newsModule->getVar('mid')));
			$criteria->add(new icms_db_criteria_Item('item', 'article'));
			$criteria->add(new icms_db_criteria_Item('iid', $linked_article_ids, 'IN'));
			$taglink_buffer = $sprockets_taglink_handler->getObjects($criteria, TRUE, TRUE);
			unset($criteria);

			foreach ($taglink_buffer as $key => $taglink) {

				if (!array_key_exists($taglink->getVar('iid', 'e'), $article_tag_id_buffer)) {
					$article_tag_id_buffer[$taglink->getVar('iid', 'e')] = array();
				}
				$article_tag_id_buffer[$taglink->getVar('iid', 'e')][] = $taglink->getVar('tid', 'e');
			}
			
			// assign each subarray of tags to the matching article, using the item id as marker
			foreach ($article_tag_id_buffer as $key => $value) {
				$article_object_array[$key]->setVar('tag', $value);
			}
		}
		
		// prepare articles for display
		if (!empty($article_object_array)) {

			foreach($article_object_array as &$article) {
				
				$tag_icons = $edit_item_link = $delete_item_link = '';

				$edit_item_link = $article->getEditItemLink(FALSE, TRUE, FALSE);
				$delete_item_link = $article->getDeleteItemLink(FALSE, TRUE, FALSE);

				$article = $article->prepareArticleForDisplay(FALSE); // without DB overrides

				$article['editItemLink'] = $edit_item_link;
				$article['deleteItemLink'] = $delete_item_link;
				
				// Compatibility with DB templates in legacy installs
				$article['lead_image'] = &$article['image'];
				$article['image_display_width'] = icms::$module->config['image_display_width'];
				$article['lead_image_display_width'] = &$article['image_display_width'];
				
				// Adjust image path document root (for correct image display in subdirectory installs)
				if ($article['lead_image']) {
					$article['lead_image'] = $document_root . '/' . $article['lead_image'];
				}
				
				// only if sprockets installed
				if (icms_get_module_status("sprockets") && !empty($article['tag'])) {
					
					// get tag links and icons, if available
					$articleTags = array_flip($article['tag']);
					foreach ($articleTags as $key => &$value) {
						if ($key) {
							$value = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(__FILE__))
							. '/article.php?tag_id=' . $sprockets_tag_buffer[$key]->getVar('tag_id', 'e') . '">'
							. $sprockets_tag_buffer[$key]->getVar('title', 'e') . '</a>';

							$icon = $sprockets_tag_buffer[$key]->getVar('icon');
							if (!empty($icon)) {
								$tag_icons[] = '<a href="' . ICMS_URL . '/modules/'
								. basename(dirname(__FILE__)) . '/article.php?tag_id='
								. $sprockets_tag_buffer[$key]->getVar('tag_id', 'e') . '">' 
								. $sprockets_tag_buffer[$key]->getVar('icon') . '</a>';
							}
						}
					}
					$article['tag'] = implode(', ', $articleTags);
					$article['icon'] = $tag_icons;
				}
				if (icms_get_module_status("sprockets")) {
					$article['rights'] = $rights_buffer[$article['rights']]['itemLink'];
				}
			}
		}

		$icmsTpl->assign('news_articles_array', $article_object_array);

		// display article rights field?
		if (icms::$module->config['display_rights'] == TRUE) {
			$icmsTpl->assign('news_display_rights', TRUE);
		}
	
		// pagination
		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
		$criteria->add(new icms_db_criteria_Item('date', time(), '<'));

		// adjust for tag, if present
		if (!empty($clean_tag_id)) {
			$extra_arg = 'tag_id=' . $clean_tag_id;
		} else {
			$extra_arg = FALSE;
			$article_count = $news_article_handler->getCount($criteria);
		}
		
		$pagenav = new  icms_view_PageNav($article_count, icms::$module->config['number_of_articles_per_page'],
			$clean_start, 'start', $extra_arg);
		
		$icmsTpl->assign('news_navbar', $pagenav->renderNav());
		$icmsTpl->assign('news_index_view', TRUE);
		
		// Meta
		if (icms_get_module_status("sprockets")) {
			if ($news_tag_name) {
				$icms_metagen = new icms_ipf_Metagen(_CO_NEWS_META_TITLE, FALSE, _CO_NEWS_META_DESCRIPTION 
						. ' ' . strtolower($news_tag_name));
			} else {
				$icms_metagen = new icms_ipf_Metagen(_CO_NEWS_META_TITLE, FALSE, _CO_NEWS_META_DESCRIPTION_INDEX 
						. ' ' . $icmsConfig['sitename']);
			}
		} else {
			$icms_metagen = new icms_ipf_Metagen(_CO_NEWS_META_TITLE, FALSE, _CO_NEWS_META_DESCRIPTION_INDEX 
					. ' ' . $icmsConfig['sitename']);
		}
}

// check if the module's breadcrumb should be displayed
if (icms::$module->config['show_breadcrumb'] == TRUE) {
	$icmsTpl->assign('news_show_breadcrumb', icms::$module->config['show_breadcrumb']);
} else {
	$icmsTpl->assign('news_show_breadcrumb', FALSE);
}

$icmsTpl->assign('news_module_home', news_getModuleName(TRUE, TRUE));

$icms_metagen->createMetaTags();

include_once 'footer.php';