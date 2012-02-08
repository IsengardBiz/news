<?php

/**
* Class representing News Article Handler object
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

class NewsArticleHandler extends icms_ipf_Handler {

	/**
	 * Constructor
	 */
	public function __construct(& $db) {
		parent::__construct($db, 'article', 'article_id', 'title', 'description', 'news');

		global $newsConfig;

		// enable lead image upload. This should use the core mimetype manager when it is improved.
		$mimetypes = array('image/jpeg', 'image/png', 'image/gif');
		$this->enableUpload($mimetypes, $newsConfig['lead_image_file_size'],
			$newsConfig['lead_image_upload_width'], $newsConfig['lead_image_upload_height']);
	}
	
	public function getTopicImageOptions() {
		return array('0' => _CO_NEWS_ARTICLE_NO, '1' => _CO_NEWS_ARTICLE_LEFT,
			'2' => _CO_NEWS_ARTICLE_RIGHT);
	}
	
	/**
	 * Used to assemble a unique identifier for a record as per the OAIPMH specs
	 * 
	 * The identifier takes the form oai:domain.com:timestamp
	 *
	 * @return string
	 */
	public function setOaiId() {
		
		$id = $prefix = $namespace = $timestamp = '';
		
		$prefix = $this->getMetadataPrefix();
		$namespace = $this->getNamespace();
		$timestamp = time();
		$id = $prefix . ":" . $namespace . ":" . $timestamp;
		
		return $id;
	}
	
	/**
	 * Returns the available metadata prefixes for this archive (currently only 'oai')
	 *
	 * @return string 
	 */
	public function getMetadataPrefix() {
		
		$metadataPrefix = '';
		
		$metadataPrefix = 'oai';
		return $metadataPrefix;
	}


	/**
	 * Used to assemble a unique oai_identifier for a record as per the OAIPMH specification
	 *
	 * @return string
	 */
	public function getNamespace() {
		
		$namespace = '';
		
		$namespace = ICMS_URL;
		$namespace = str_replace('http://', '', $namespace);
		$namespace = str_replace('https://', '', $namespace);
		$namespace = str_replace('www.', '', $namespace);
		
		return $namespace;
	}
	
	/**
	 * Toggles an article on or offline
	 *
	 * @param int $article_id
	 * @param str $field
	 * @return int $visibility
	 */
	public function changeOnlineStatus($article_id, $field) {
		
		$visibility = $articleObj = '';
		
		$articleObj = $this->get($article_id);
		if ($articleObj->getVar($field, 'e') == true) {
			$articleObj->setVar($field, 0);
			$visibility = 0;
		} else {
			$articleObj->setVar($field, 1);
			$visibility = 1;
		}
		$this->insert($articleObj, true);
		
		return $visibility;
	}
		
	/**
	 * Provides the global search functionality for the News module
	 *
	 * @param array $queryarray
	 * @param string $andor
	 * @param int $limit
	 * @param int $offset
	 * @param int $userid
	 * @return array 
	 */
	public function getArticlesForSearch($queryarray, $andor, $limit, $offset, $userid) {
		
		$criteria = new icms_db_criteria_Compo();
		$criteria->setStart($offset);
		$criteria->setLimit($limit);
		$criteria->setSort('date');
		$criteria->setOrder('DESC');

		if ($userid != 0) {
			$criteria->add(new icms_db_criteria_Item('submitter', $userid));
		}
		
		if ($queryarray) {
			$criteriaKeywords = new icms_db_criteria_Compo();
			for ($i = 0; $i < count($queryarray); $i++) {
				$criteriaKeyword = new icms_db_criteria_Compo();
				$criteriaKeyword->add(new icms_db_criteria_Item('title', '%' . $queryarray[$i] . '%',
					'LIKE'), 'OR');
				$criteriaKeyword->add(new icms_db_criteria_Item('description', '%' . $queryarray[$i]
					. '%', 'LIKE'), 'OR');
				$criteriaKeywords->add($criteriaKeyword, $andor);
				unset ($criteriaKeyword);
			}
			$criteria->add($criteriaKeywords);
		}
		
		$criteria->add(new icms_db_criteria_Item('online_status', true));
		$criteria->add(new icms_db_criteria_Item('date', time(), '<'));
		
		return $this->getObjects($criteria, true, true);
	}
	
	// ADMIN TABLE FILTERS
	
	/**
	 * Adds a filter for online status to the articles table on the admin side
	 *
	 * @return  string
	 */
	public function online_status_filter() {
		
		return array(0 =>  _CO_NEWS_ARTICLE_OFFLINE, 1 =>  _CO_NEWS_ARTICLE_ONLINE);
	}
	
	/**
	 * Adds a filter for federation status to the articles table on the admin side
	 *
	 * @return  string
	 */
	public function federation_filter() {
		
		return array(0 =>  _CO_NEWS_ARTICLE_FEDERATION_DISABLED, 
			1 =>  _CO_NEWS_ARTICLE_FEDERATION_ENABLED);
	}
	
	/**
	 * Creates a rights filter for the article admin display table
	 *
	 * @return array
	 */

	public function rights_filter() {
		
		$rights_array = array();
		$sprockets_rights_handler = '';
		
		$sprockets_rights_handler = icms_getModuleHandler('rights', 'sprockets', 'sprockets');
		$rights_array = $sprockets_rights_handler->getList();
		
		return $rights_array;
	}

	/**
	 * Updates comments
	 *
	 * @param int $article_id
	 * @param int $total_num
	 */
	public function updateComments($article_id, $total_num) {
			
		$articleObj = '';
		
		$articleObj = $this->get($article_id);
		if ($articleObj && !$articleObj->isNew()) {
			$articleObj->setVar('post_comments', $total_num);
			$this->insert($articleObj, true);
		}
	}
	
	/**
	 * Triggers notifications, called when an article is inserted or updated
	 *
	 * @param object $obj NewsArticle object
	 * @return bool
	 */
	protected function afterSave(& $obj) {
		
		$sprockets_taglink_handler = '';
		
		// triggers notification event for subscribers
		if($obj->getVar('date') < time()) {
			if (!$obj->getVar('article_notification_sent') && $obj->getVar ('online_status', 'e') == 1) {
			$obj->sendNotifArticlePublished();
			$obj->setVar('article_notification_sent', true);
			$this->insert ($obj);
			}
		}

		// Only update the taglinks if the object is being updated from the add/edit form (POST).
		// The taglinks should *not* be updated during a GET request (ie. when the toggle buttons
		// are used to change the completion status or online status). Attempting to do so will 
		// trigger an error, as the database should not be updated during a GET request.
		$sprocketsModule = icms::handler("icms_module")->getByDirname("sprockets");
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && icms_get_module_status("sprockets")) {
			$sprockets_taglink_handler = icms_getModuleHandler('taglink', 
					$sprocketsModule->getVar('dirname'), 'sprockets', 'sprockets');
			$sprockets_taglink_handler->storeTagsForObject($obj);
		}
	
		return true;
	}
	
	/**
	 * Deletes notification subscriptions, called when an article is deleted
	 *
	 * @param object $obj NewsArticle object
	 * @return bool
	 */
	protected function afterDelete(& $obj) {
		
		$sprocketsModule = $notification_handler = $module_handler = $module = $module_id
				= $category = $item_id = '';
		
		$sprocketsModule = icms_getModuleInfo('sprockets');
		
		$notification_handler = icms::handler('icms_data_notification');
		$module_handler = icms::handler("icms_module");
		$module = $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));
		$module_id = $module->getVar('mid');
		$category = 'global';
		$item_id = $obj->getVar('article_id');
		
		// delete article bookmarks
		$category = 'article';
		$notification_handler->unsubscribeByItem($module_id, $category, $item_id);
		$notification_handler = icms::handler('icms_data_notification');
		$module_handler = icms::handler("icms_module");
		$module = $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));
		$module_id = $module->getVar('mid');
		$category = 'global';
		$item_id = $obj->getVar('article_id');

		// delete article notifications
		$category = 'article';
		$notification_handler->unsubscribeByItem($module_id, $category, $item_id);

		// delete taglinks
		if (icms_get_module_status("sprockets")) {
			$sprockets_taglink_handler = icms_getModuleHandler('taglink',
					$sprocketsModule->getVar('dirname'), 'sprockets');
			$sprockets_taglink_handler->deleteAllForObject($obj);
		}

		return true;
	}
}