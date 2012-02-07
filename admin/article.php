<?php
/**
* Admin page to manage articles
*
* List, add, edit and delete article objects
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

/**
 * Edit an Article
 *
 * @param int $article_id Articleid to be edited
*/
function editarticle($article_id = 0)
{
	global $news_article_handler, $icmsAdminTpl;
	
	$articleObj = $newsModule = $sform = '';
	
	$newsModule = icms_getModuleInfo(basename(dirname(dirname(__FILE__))));
	$articleObj = $news_article_handler->get($article_id);

	if (!$articleObj->isNew()){
		$articleObj->loadTags();
		$newsModule->displayAdminMenu(0, _AM_NEWS_ARTICLES . " > " . _CO_ICMS_EDITING);
		$sform = $articleObj->getForm(_AM_NEWS_ARTICLE_EDIT, 'addarticle');
		$sform->assign($icmsAdminTpl);

	} else {
		$newsModule->displayAdminMenu(0, _AM_NEWS_ARTICLES . " > " . _CO_ICMS_CREATINGNEW);
		$articleObj->setVar('submitter', icms::$user->getVar('uid'));
		// Reduce the date field by 10 minutes to compensate for the submission form jumping forward
		// to the next 10 minute increment. This ensures that the publication date is in the past
		// (unless the user changes it), thereby preventing the article from being embargoed
		// for several minutes after submission, which is annoying and confusing.
		$articleObj->setVar('date', (time() - 600));
		$sform = $articleObj->getForm(_AM_NEWS_ARTICLE_CREATE, 'addarticle');
		$sform->assign($icmsAdminTpl);

	}
	$icmsAdminTpl->display('db:news_admin_article.html');
}

include_once("admin_header.php");

// initialise
$clean_article_id = $clean_tag_id = $clean_op = $valid_op = '';
$news_article_handler = icms_getModuleHandler('article', basename(dirname(dirname(__FILE__))),
	'news');

/** Create a whitelist of valid values, be sure to use appropriate types for each value
 * Be sure to include a value for no parameter, if you have a default condition
 */
$valid_op = array ('mod','changedField','addarticle','del','view','changeStatus',
	'changeFederation',	'');

if (isset($_GET['op'])) $clean_op = htmlentities($_GET['op']);
if (isset($_POST['op'])) $clean_op = htmlentities($_POST['op']);

$clean_article_id = isset($_GET['article_id']) ? (int) $_GET['article_id'] : 0 ;
$clean_tag_id = isset($_GET['tag_id']) ? intval($_GET['tag_id']) : 0 ;

if (in_array($clean_op,$valid_op,true)){
  switch ($clean_op) {
  	case "mod":
  	case "changedField":
  		icms_cp_header();
  		editarticle($clean_article_id);
		
  		break;
	
  	case "addarticle":	
        $controller = new icms_ipf_Controller($news_article_handler);
		$controller->storeFromDefaultForm(_AM_NEWS_ARTICLE_CREATED, _AM_NEWS_ARTICLE_MODIFIED);
		
  		break;

  	case "del":
		$controller = '';
        $controller = new icms_ipf_Controller($news_article_handler);
  		$controller->handleObjectDeletion();

  		break;

  	case "view" :
  		$articleObj = $news_article_handler->get($clean_article_id);
  		icms_cp_header();
  		$articleObj->displaySingleObject();
		
  		break;
	
	case "changeStatus":
			$status = $ret = '';
			$status = $news_article_handler->changeOnlineStatus($clean_article_id, 'online_status');
			$ret = '/modules/' . basename(dirname(dirname(__FILE__))) . '/admin/article.php';
			if ($status == 0) {
				redirect_header(ICMS_URL . $ret, 2, _AM_NEWS_ARTICLE_OFFLINE);
			} else {
				redirect_header(ICMS_URL . $ret, 2, _AM_NEWS_ARTICLE_ONLINE);
			}
			
		break;
		
	case "changeFederation":
			$status = $ret = '';
			$status = $news_article_handler->changeOnlineStatus($clean_article_id, 'federated');
			$ret = '/modules/' . basename(dirname(dirname(__FILE__))) . '/admin/article.php';
			if ($status == 0) {
				redirect_header(ICMS_URL . $ret, 2, _AM_NEWS_ARTICLE_FEDERATION_DISABLED);
			} else {
				redirect_header(ICMS_URL . $ret, 2, _AM_NEWS_ARTICLE_FEDERATION_ENABLED);
			}
			
		break;
		
  	default:

  		icms_cp_header();

  		$newsModule->displayAdminMenu(0, _AM_NEWS_ARTICLES);
		
		// if no op is set, but there is a (valid) soundtrack_id, display a single object
		if ($clean_article_id) {
			$articleObj = $news_article_handler->get($clean_article_id);
			if ($articleObj->getVar('article_id')) {
				$lead_image = $articleObj->getVar('lead_image', 'e');
				if ($lead_image) {
					$lead_image = '<img src="/uploads/' . basename(dirname(dirname(__FILE__))) 
						. '/article/' . $lead_image . '" alt="' . $articleObj->getVar('title') . '" />';
					$articleObj->setVar('lead_image', $lead_image);
				}
				$articleObj->displaySingleObject();
			}
		}
		
		// display a tag select filter (if the Sprockets module is installed)
		$newsModule = icms_getModuleInfo(basename(dirname(dirname(__FILE__))));
		$sprocketsModule = icms_getModuleInfo('sprockets');
		
		if (icms_get_module_status("sprockets")) {
			
			$tag_select_box = '';
			$taglink_array = $tagged_article_list = array();
			$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'),
				'sprockets');
			$sprockets_taglink_handler = icms_getModuleHandler('taglink',
					$sprocketsModule->getVar('dirname'), 'sprockets');
			
			$tag_select_box = $sprockets_tag_handler->getTagSelectBox('article.php', $clean_tag_id,
				_AM_NEWS_ARTICLE_ALL_ARTICLES, TRUE, icms::$module->getVar('mid'));
			if (!empty($tag_select_box)) {
				echo '<h3>' . _AM_NEWS_ARTICLE_FILTER_BY_TAG . '</h3>';
				echo $tag_select_box;
			}
			
			if ($clean_tag_id) {
				
				// get a list of article IDs belonging to this tag
				$criteria = new icms_db_criteria_Compo();
				$criteria->add(new icms_db_criteria_Item('tid', $clean_tag_id));
				$criteria->add(new icms_db_criteria_Item('mid', $newsModule->getVar('mid')));
				$criteria->add(new icms_db_criteria_Item('item', 'article'));
				$taglink_array = $sprockets_taglink_handler->getObjects($criteria);
				foreach ($taglink_array as $taglink) {
					$tagged_article_list[] = $taglink->getVar('iid');
				}
				$tagged_article_list = "('" . implode("','", $tagged_article_list) . "')";
				
				// use the list to filter the persistable table
				$criteria = new icms_db_criteria_Compo();
				$criteria->add(new icms_db_criteria_Item('article_id', $tagged_article_list, 'IN'));
			}
		}

		if (empty($criteria)) {
			$criteria = null;
		}

  		$objectTable = new icms_ipf_view_Table($news_article_handler, $criteria);
		
		$objectTable->addQuickSearch('title');
		$objectTable->addColumn(new icms_ipf_view_Column('online_status', 'center', true));
  		$objectTable->addColumn(new icms_ipf_view_Column('title'));
		$objectTable->addColumn(new icms_ipf_view_Column('creator'));
		$objectTable->addColumn(new icms_ipf_view_Column('counter'));
		$objectTable->addColumn(new icms_ipf_view_Column('date'));
		$objectTable->setDefaultSort('date');
		$objectTable->setDefaultOrder('DESC');
		if (icms_get_module_status("sprockets")) {
			$objectTable->addColumn(new icms_ipf_view_Column('federated'));
			$objectTable->addFilter('federated', 'federation_filter');
		}
		$objectTable->addFilter('online_status', 'online_status_filter');
		if (icms_get_module_status("sprockets")) {$objectTable->addFilter('rights', 'rights_filter');}
  		$objectTable->addIntroButton('addarticle', 'article.php?op=mod', _AM_NEWS_ARTICLE_CREATE);
		
  		$icmsAdminTpl->assign('news_article_table', $objectTable->fetch());
  		$icmsAdminTpl->display('db:news_admin_article.html');
		
  		break;
  }
  icms_cp_footer();
}
/**
 * If you want to have a specific action taken because the user input was invalid,
 * place it at this point. Otherwise, a blank page will be displayed
 */