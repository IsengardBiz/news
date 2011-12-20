<?php
/**
* Archive page - displays a list of all published articles by month
*
* @copyright	(c) The Xoops Project - www.xoops.org
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @author Xoops Modules Dev Team
* @author		Madfish 28/6/2011
* @since		1.0
* @package		news
* @version		$Id$
*/

######################################################################
# Original version:
# [11-may-2001] Kenneth Lee - http://www.nexgear.com/
######################################################################

include_once 'header.php';
$xoopsOption['template_main'] = 'news_archive.html';
include_once ICMS_ROOT_PATH . '/header.php';

global $icmsConfig, $newsConfig;

$news_article_handler = icms_getModuleHandler('article', basename(dirname(__FILE__)), 'news');

$lastyear = 0;
$lastmonth = 0;

$months_arr = array(1 => _CO_NEWS_CAL_JANUARY, 2 => _CO_NEWS_CAL_FEBRUARY, 3 => _CO_NEWS_CAL_MARCH,
	4 => _CO_NEWS_CAL_APRIL, 5 => _CO_NEWS_CAL_MAY, 6 => _CO_NEWS_CAL_JUNE, 7 => _CO_NEWS_CAL_JULY,
	8 => _CO_NEWS_CAL_AUGUST, 9 => _CO_NEWS_CAL_SEPTEMBER, 10 => _CO_NEWS_CAL_OCTOBER,
	11 => _CO_NEWS_CAL_NOVEMBER, 12 => _CO_NEWS_CAL_DECEMBER);

$fromyear = (isset($_GET['year'])) ? intval ($_GET['year']): 0;
$frommonth = (isset($_GET['month'])) ? intval($_GET['month']) : 0;

$pgtitle = '';
if ($fromyear && $frommonth) {
	$pgtitle = sprintf(" - %d - %d",$fromyear,$frommonth);
}

$dateformat=$newsConfig['date_format'];
if ($dateformat == '') {
	$dateformat = 'm';
}

$icmsTpl->assign('xoops_pagetitle', icms_core_DataFilter::htmlSpecialchars(_CO_NEWS_ARCHIVES) . $pgtitle);

$useroffset = '';
if (is_object($xoopsUser)) {
	$timezone = $xoopsUser->getVar("timezone_offset");
	if (isset($timezone)) {
		$useroffset = $xoopsUser->getVar("timezone_offset");
	} else {
		$useroffset = $xoopsConfig['default_TZ'];
	}
}

global $xoopsDB;

$sql = "SELECT `date` FROM " . $news_article_handler->table . " WHERE (`date` > '0' AND `date` <= '"
	. time() . "') ORDER BY `date` DESC";

$rows = $news_article_handler->query($sql, null);

if (!$rows) {
	echo _CO_NEWS_NO_ARCHIVE;
} else {
	$years = array();
	$months = array();
	$i = 0;
	
	foreach ($rows as $row) {
		$time = $row['date'];
		$time = formatTimestamp($time, 'mysql', $useroffset);
		
		// do not insert a line break or you will break the regex!
			if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $time, $datetime)) {
				$this_year  = intval($datetime[1]);
				$this_month = intval($datetime[2]);
			if (empty($lastyear)) {
				$lastyear = $this_year;
			}
			if ($lastmonth == 0) {
				$lastmonth = $this_month;
				$months[$lastmonth]['string'] = $months_arr[$lastmonth];
				$months[$lastmonth]['number'] = $lastmonth;
			}
			if ($lastyear != $this_year) {
				$years[$i]['number'] = $lastyear;
				$years[$i]['months'] = $months;
				$months = array();
				$lastmonth = 0;
				$lastyear = $this_year;
				$i++;
			}
			if ($lastmonth != $this_month) {
				$lastmonth = $this_month;
				$months[$lastmonth]['string'] = $months_arr[$lastmonth];
				$months[$lastmonth]['number'] = $lastmonth;
			}
		}
	}

	$years[$i]['number'] = $this_year;
	$years[$i]['months'] = $months;
	$icmsTpl->assign('years', $years);
}

if ($fromyear != 0 && $frommonth != 0) {
	
	$icmsTpl->assign('show_articles', true);
	$icmsTpl->assign('lang_articles', _CO_NEWS_ARCHIVE_ARTICLES);
	$icmsTpl->assign('currentmonth', $months_arr[$frommonth]);
	$icmsTpl->assign('currentyear', $fromyear);
	$icmsTpl->assign('lang_actions', _CO_NEWS_ARCHIVE_ACTIONS);
	$icmsTpl->assign('lang_date', _CO_NEWS_ARCHIVE_DATE);
	$icmsTpl->assign('lang_views', _CO_NEWS_ARCHIVE_VIEWS);

	// must adjust the selected time to server timestamp
	$timeoffset = $useroffset - $icmsConfig['server_TZ'];
	$monthstart = mktime(0 - $timeoffset, 0, 0, $frommonth, 1, $fromyear);
	$monthend = mktime(23 - $timeoffset, 59, 59, $frommonth + 1, 0, $fromyear);
	$monthend = ($monthend > time()) ? time() : $monthend;

	$count=0;
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('date', $monthstart, '>'));
	$criteria->add(new Criteria('date', $monthend, '<'));
	$criteria->add(new Criteria('online_status', true));
	$criteria->setSort('date');
	$criteria->setOrder('DESC');
	$storyarray = $news_article_handler->getObjects($criteria, true);

	$count=count($storyarray);
	if (is_array($storyarray) && $count>0) {
		
		// if Sprockets is installed, prepare tag buffers to reduce database lookups
		$sprocketsModule = icms_getModuleInfo('sprockets');
		if ($sprocketsModule) {
			$newsModule = icms_getModuleInfo(basename(dirname(__FILE__)));
			$article_ids = array_keys($storyarray);
			$article_tags_multi_array = array();
			$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->dirname(),
					'sprockets');
			$sprockets_taglink_handler = icms_getModuleHandler('taglink', 
					$sprocketsModule->dirname(), 'sprockets');
			
			// only get taglinks relevant to the articles being listed
			$article_ids = "('" . implode("','", $article_ids) . "')";
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('mid', $newsModule->mid()));
			$criteria->add(new Criteria('item', 'article'));
			$criteria->add(new Criteria('iid', $article_ids, 'IN'));

			$tag_buffer = $sprockets_tag_handler->getObjects(null, true);
			$taglink_buffer = $sprockets_taglink_handler->getObjects($criteria, true, false);
			
			// prepare a multidimensional array holding the tags for each story
			foreach ($taglink_buffer as $taglink) {
				if (!array_key_exists($taglink['iid'], $article_tags_multi_array)) {
					$article_tags_multi_array[$taglink['iid']] = array();				
				}
				$article_tags_multi_array[$taglink['iid']][] = '<a href="' . ICMS_URL
					. '/modules/' . basename(dirname(__FILE__)) . '/article.php?tag_id='
					. $taglink['tid'] . '" title="' . $tag_buffer[$taglink['tid']]->title() . '">'
					. $tag_buffer[$taglink['tid']]->title() . '</a>';
			}
		}
		
		foreach ($storyarray as $article) {
	    	$htmltitle = '';
			$story = array();			
			
	    	$story['title'] = $article->getItemLink();
	    	$story['counter'] = $article->getVar('counter');
			if ($sprocketsModule) {
				// use the article_id to extract the array of tags relevant to this article
				$story['tags'] = implode(', ', $article_tags_multi_array[$article->id()]);
			} else {
				$story['tags'] = false;
			}
	    	$story['date'] = formatTimestamp($article->getVar('date', 'e'),$dateformat,$useroffset);
	    	$icmsTpl->append('stories', $story);
		}
	}
	$icmsTpl->assign('lang_tags', _CO_NEWS_ARCHIVE_TAGS);
	$icmsTpl->assign('lang_storytotal', _CO_NEWS_ARCHIVE_THEREAREINTOTAL . $count
		. _CO_NEWS_ARCHIVE_ARTICLES_LOWER);
} else {
    $icmsTpl->assign('show_articles', false);
}

$icmsTpl->assign('lang_newsarchives', _CO_NEWS_ARCHIVES);

// check if the module's breadcrumb should be displayed
if ($newsConfig['show_breadcrumb'] == true) {
	$icmsTpl->assign('news_show_breadcrumb', $newsConfig['show_breadcrumb']);
} else {
	$icmsTpl->assign('news_show_breadcrumb', false);
}

$icmsTpl->assign('news_module_home', news_getModuleName(true, true));
$icmsTpl->assign('news_category_path', _CO_NEWS_ARCHIVE);

/**
 * Generating meta information for this page
 */
$icms_metagen = new icms_ipf_Metagen(_CO_NEWS_ARCHIVES, false, _CO_NEWS_ARCHIVE_DESCRIPTION);
$icms_metagen->createMetaTags();

include_once 'footer.php';