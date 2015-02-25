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

		// enable lead image upload. This should use the core mimetype manager when it is improved.
		$mimetypes = array('image/jpeg', 'image/png', 'image/gif');
		$this->enableUpload($mimetypes, icms_getConfig('image_file_size', 'news'),
			icms_getConfig('image_upload_width', 'news'),
			icms_getConfig('image_upload_height', 'news'));
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
		if ($articleObj->getVar($field, 'e') == TRUE) {
			$articleObj->setVar($field, 0);
			$visibility = 0;
		} else {
			$articleObj->setVar($field, 1);
			$visibility = 1;
		}
		$this->insert($articleObj, TRUE);
		
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
		
		$count = $results = '';
		$criteria = new icms_db_criteria_Compo();

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
				$criteriaKeyword->add(new icms_db_criteria_Item('extended_text', '%' . $queryarray[$i]
					. '%', 'LIKE'), 'OR');
				$criteriaKeyword->add(new icms_db_criteria_Item('creator', '%' . $queryarray[$i]
					. '%', 'LIKE'), 'OR');
				$criteriaKeywords->add($criteriaKeyword, $andor);
				unset ($criteriaKeyword);
			}
			$criteria->add($criteriaKeywords);
		}
		
		$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
		$criteria->add(new icms_db_criteria_Item('date', time(), '<'));
		
		/*
		 * Improving the efficiency of search
		 * 
		 * The general search function is not efficient, because it retrieves all matching records
		 * even when only a small subset is required, which is usually the case. The full records 
		 * are retrieved so that they can be counted, which is used to display the number of 
		 * search results and also to set up the pagination controls. The problem with this approach 
		 * is that a search generating a very large number of results (eg. > 650) will crash out. 
		 * Maybe its a memory allocation issue, I don't know.
		 * 
		 * A better approach is to run two queries: The first a getCount() to find out how many 
		 * records there are in total (without actually wasting resources to retrieve them), 
		 * followed by a getObjects() to retrieve the small subset that are actually needed. 
		 * Due to the way search works, the object array needs to be padded out 
		 * with the number of elements counted in order to preserve 'hits' information and to construct
		 * the pagination controls. So to minimise resources, we can just set their values to '1'.
		 * 
		 * In the long term it would be better to (say) pass the count back as element[0] of the 
		 * results array, but that will require modification to the core and will affect all modules.
		 * So for the moment, this hack is convenient.
		 */
		
		$criteria->setStart($offset);
		$criteria->setSort('date');
		$criteria->setOrder('DESC');
		
		// Count the number of search results WITHOUT actually retrieving the objects
		$count = $this->getCount($criteria);
		
		// Retrieve the subset of results that are actually required
		if (!$limit) {
			global $icmsConfigSearch;
			$limit = $icmsConfigSearch['search_per_page'];
		}
		
		$criteria->setLimit($limit);
		$results = $this->getObjects($criteria, FALSE, TRUE);
		
		// Pad the results array out to the counted length to preserve 'hits' and pagination controls.
		// This approach is not ideal, but it greatly reduces the load for queries with large result sets
		$results = array_pad($results, $count, 1);
		
		return $results;
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
	 * Adds a filter for syndication status to the articles table on the admin side
	 *
	 * @return  string
	 */
	public function syndication_filter() {
		
		return array(0 =>  _CO_NEWS_ARTICLE_SYNDICATION_DISABLED, 
			1 =>  _CO_NEWS_ARTICLE_SYNDICATION_ENABLED);
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
			$this->insert($articleObj, TRUE);
		}
	}
	
	/**
	 * Flush the cache for the News module after adding, editing or deleting an event.
	 * 
	 * Ensures that the index/block/single view cache is kept updated if caching is enabled.
	 * 
	 * @global array $icmsConfig
	 * @param type $obj 
	 */
	protected function clear_cache(& $obj)
	{
		global $icmsConfig;
		$cache_status = $icmsConfig['module_cache'];
		$module = icms::handler("icms_module")->getByDirname("news", TRUE);
		$module_id = $module->getVar("mid");
			
		// Check if caching is enabled for this module. The cache time is stored in a serialised 
		// string in config table (module_cache), and is indicated in seconds. Uncached = 0.
		if ($cache_status[$module_id] > 0)
		{			
			// As PHP's exec() function is often disabled for security reasons
			try 
			{	
				// Index pages
				exec("find " . ICMS_CACHE_PATH . "/" . "news^%2Fmodules%2Fnews%2Farticle.php^* -delete &");
				exec("find " . ICMS_CACHE_PATH . "/" . "news^%2Fmodules%2Fnews%2Farticle.php%3Fstart* -delete &");
				// Archive pages
				exec("find " . ICMS_CACHE_PATH . "/" . "news^%2Fmodules%2Fnews%2Farchive.php* -delete &");
				// Blocks
				exec("find " . ICMS_CACHE_PATH . "/" . "blk_news* -delete &");
				if (!$obj->isNew())
				{
					exec("find " . ICMS_CACHE_PATH . "/" . "news^%2Fmodules%2Fnews%2Farticle.php%3Farticle_id%3D" 
							. $obj->getVar('article_id', 'e') . "%26* -delete &");
					exec("find " . ICMS_CACHE_PATH . "/" . "news^%2Fmodules%2Fnews%2Farticle.php%3Farticle_id%3D" 
							. $obj->getVar('article_id', 'e') . "^* -delete &");
				}				
			}
			catch(Exception $e)
			{
				$obj->setErrors($e->getMessage());
			}
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
			$obj->setVar('article_notification_sent', TRUE);
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
			$sprockets_taglink_handler->storeTagsForObject($obj, 'tag', 0);
		}
		
		// Clear cache
		$this->clear_cache($obj);
	
		return TRUE;
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
		
		// delete global notifications
		$notification_handler = icms::handler('icms_data_notification');
		$newsModule = icms::handler("icms_module")->getByDirname("news");
		$module_id = $newsModule->getVar('mid');
		$category = 'global';
		$item_id = $obj->getVar('article_id');
		$notification_handler->unsubscribeByItem($module_id, $category, $item_id);
		
		// delete article bookmarks
		$category = 'article';
		$notification_handler->unsubscribeByItem($module_id, $category, $item_id);

		// delete taglinks
		if (icms_get_module_status("sprockets")) {
			$sprockets_taglink_handler = icms_getModuleHandler('taglink',
					$sprocketsModule->getVar('dirname'), 'sprockets');
			$sprockets_taglink_handler->deleteAllForObject($obj);
		}
		
		// Clear cache
		$this->clear_cache($obj);	

		return TRUE;
	}
}