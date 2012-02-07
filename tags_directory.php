<?php

/**
* Topics page - displays a list of tags available in the Sprockets module (if installed)
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

include_once 'header.php';

$xoopsOption['template_main'] = 'news_topics.html';
include_once ICMS_ROOT_PATH . '/header.php';

// check if the Sprockets module is installed, because otherwise there are no tags
$sprocketsModule = icms_getModuleInfo('sprockets');
if (icms_get_module_status("sprockets")) {
	
	// initialise
	$tagList = '';
	$have_tags = false;
	$article_list = $article_ids = $tag_list = $tag_ids = $tag_array = $taglink_object_array
		= $taglink_iid_list = array();
	
	$newsModule = icms_getModuleInfo(basename(dirname(__FILE__)));
	$news_article_handler = icms_getModuleHandler('article', basename(dirname(__FILE__)), 'news');
	$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
	$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'),
		'sprockets');

	// get a list of tags containing online articles using a JOIN between article and taglink tables

	global $xoopsDB;

	$query = $rows = $tag_article_count = '';

	$query = "SELECT DISTINCT `tid` FROM " . $news_article_handler->table . ", "
			. $sprockets_taglink_handler->table
			. " WHERE `article_id` = `iid`"
			. " AND `online_status` = '1'"
			. " AND `date` < '" . time() . "'"
			. " AND `mid` = '" . $newsModule->getVar('mid') . "'"
			. " AND `item` = 'article'";

	$result = $xoopsDB->query($query);

	if (!$result) {
		echo 'Error';
		exit;

	} else {

		$rows = $sprockets_taglink_handler->convertResultSet($result);
		foreach ($rows as $key => $row) {
			$tag_ids[] = $row->getVar('tid');
		}
	}
	
	if (count($tag_ids) > 0) {

		// convert tag_ids to string for use as search criteria
		$tag_ids = "('" . implode("','", $tag_ids) . "')";

		// retrieve relevant tags
		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('tag_id', $tag_ids, 'IN'));
		$criteria->setSort('title');
		$criteria->setOrder('ASC');

		// create a list of tags with links
		$tag_array = $sprockets_tag_handler->getList($criteria);

		foreach($tag_array as $key => $tag) {
			$tag_list[] = '<a href="' . ICMS_URL . '/modules/' . $newsModule->getVar('dirname')
					. '/article.php?tag_id=' . $key . '" title="' . $tag . '">' . $tag . '</a>';
		}
		$have_tags = true;
	}
	
	// assign the results to the template
	if ($have_tags) {
		$icmsTpl->assign('news_topics_list', $tag_list);
	} else {
		$icmsTpl->assign('news_topics_list', false);
	}
}

// check if the module's breadcrumb should be displayed
if ($newsConfig['show_breadcrumb'] == true) {
	$icmsTpl->assign('news_show_breadcrumb', $newsConfig['show_breadcrumb']);
} else {
	$icmsTpl->assign('news_show_breadcrumb', false);
}

$icmsTpl->assign('news_module_home', news_getModuleName(true, true));
$icmsTpl->assign('news_category_path', _CO_NEWS_ARCHIVE_TAGS);
$icms_metagen = new icms_ipf_Metagen(_CO_NEWS_ARCHIVE_TAGS, false, _CO_NEWS_ARCHIVE_TAGS_DESCRIPTION);
$icms_metagen->createMetaTags();

include_once 'footer.php';