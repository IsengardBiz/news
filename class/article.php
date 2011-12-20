<?php

/**
* Classes responsible for managing News article objects
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

// including the IcmsPersistabelSeoObject
include_once ICMS_ROOT_PATH . '/kernel/icmspersistableseoobject.php';
include_once ICMS_ROOT_PATH . '/kernel/icmspersistablecontroller.php';
include_once(ICMS_ROOT_PATH . '/modules/news/include/functions.php');

// Include the common language file of the module if not already present
if(!defined("_CO_NEWS_ARTICLE_TITLE")) {
	icms_loadLanguageFile('news', 'common');
}

class NewsArticle extends IcmsPersistableSeoObject {

	/**
	 * Constructor
	 *
	 * @param object $handler NewsPostHandler object
	 */
	public function __construct(& $handler) {
		global $icmsConfig, $newsConfig, $icmsUser;

		$this->IcmsPersistableObject($handler);

		$this->quickInitVar('article_id', XOBJ_DTYPE_INT, true);
		$this->quickInitVar('title', XOBJ_DTYPE_TXTBOX, true);
		$this->quickInitVar('creator', XOBJ_DTYPE_TXTBOX, false);
		$this->initNonPersistableVar('tag', XOBJ_DTYPE_INT, 'tag', false, false, false, true);
		$this->quickInitVar('display_topic_image', XOBJ_DTYPE_INT, true, false, false,
				$newsConfig['display_topic_image']);
		$this->quickInitVar('description', XOBJ_DTYPE_TXTAREA, true);
		$this->quickInitVar('extended_text', XOBJ_DTYPE_TXTAREA, false);
		$this->quickInitVar('lead_image', XOBJ_DTYPE_IMAGE, false);
		$this->quickInitVar('display_lead_image', XOBJ_DTYPE_INT, true, false, false, 
				$newsConfig['display_lead_image']);
		$this->quickInitVar('rights', XOBJ_DTYPE_INT, false);
		$this->quickInitVar('language', XOBJ_DTYPE_TXTBOX, true, false, false, _LANGCODE);
		$this->quickInitVar('publisher', XOBJ_DTYPE_TXTBOX, true, false, false,
				$icmsConfig['sitename']);
		$this->quickInitVar('type', XOBJ_DTYPE_TXTBOX, true, false, false, 'Text');
		$this->quickInitVar('submitter', XOBJ_DTYPE_INT, true);
		$this->quickInitVar('date', XOBJ_DTYPE_LTIME, true);
		$this->quickInitVar('online_status', XOBJ_DTYPE_INT, true, false, false, 1);
		$this->quickInitVar('federated', XOBJ_DTYPE_INT, true, false, false,
				$newsConfig['default_federation']);
		$this->quickInitVar('oai_identifier', XOBJ_DTYPE_TXTBOX, true, false, false,
				$this->handler->setOaiId());
		$this->initCommonVar('counter');
		$this->initCommonVar('dohtml');
		$this->initCommonVar('dobr');
		$this->initCommonVar('dosmiley');
		$this->initCommonVar('docxode');
		$this->quickInitVar ('article_notification_sent', XOBJ_DTYPE_INT, true, false, false, 0);

		$this->setControl('description', 'dhtmltextarea');
		$this->setControl('extended_text', 'dhtmltextarea');

		// only display the tag and rights fields if the sprockets module is installed
		$sprocketsModule = icms_getModuleInfo('sprockets');
		if ($sprocketsModule) {
			$this->setControl('tag', array(
			'name' => 'select_multi',
			'itemHandler' => 'tag',
			'method' => 'getTags',
			'module' => 'sprockets'));

			$this->setControl('rights', array(
			'name' => 'select',
			'itemHandler' => 'rights',
			'method' => 'getRights',
			'module' => 'sprockets'));
			
			$this->setControl('federated', 'yesno');
		} else {
			$this->hideFieldFromForm('tag');
			$this->hideFieldFromSingleView ('tag');
			$this->hideFieldFromForm('rights');
			$this->hideFieldFromSingleView ('rights');
			$this->hideFieldFromForm('federated');
			$this->hideFieldFromSingleView('federated');
		}

		$this->setControl('display_topic_image', array(
			'name' => 'select',
			'itemHandler' => 'article',
			'method' => 'getTopicImageOptions',
			'module' => 'news'));
		
		$this->setControl('display_lead_image', array(
			'name' => 'select',
			'itemHandler' => 'article',
			'method' => 'getTopicImageOptions',
			'module' => 'news'));
		
		// image path
		$this->setControl('lead_image', array('name' => 'image'));
		$url = ICMS_URL . '/uploads/' . basename(dirname(dirname(__FILE__))) . '/';
		$path = ICMS_ROOT_PATH . '/uploads/' . basename(dirname(dirname(__FILE__))) . '/';
		$this->setImageDir($url, $path);
		
		$this->setControl('submitter', 'user');
		$this->setControl('online_status', 'yesno');
		
		// hidden static fields
		$this->hideFieldFromForm('type');
		$this->hideFieldFromSingleView('type');
		$this->hideFieldFromForm('language');
		$this->hideFieldFromSingleView('language');
		$this->hideFieldFromForm('publisher');
		$this->hideFieldFromSingleView('publisher');
		
		//hide the notification status field, its for internal use only
		$this->hideFieldFromForm('article_notification_sent');
		$this->hideFieldFromSingleView('article_notification_sent');
		
		// the oai_identifier must not be changed
		$this->doMakeFieldreadOnly('oai_identifier');
		
		$this->IcmsPersistableSeoObject();
	}

	/**
	 * Overriding the IcmsPersistableObject::getVar method to assign a custom method on some
	 * specific fields to handle the value before returning it
	 *
	 * @param str $key key of the field
	 * @param str $format format that is requested
	 * @return mixed value of the field that is requested
	 */
	function getVar($key, $format = 's') {
		if ($format == 's' && in_array($key, array ('creator', 'display_topic_image',
				'display_lead_image', 'rights', 'date', 'submitter', 'online_status', 'federated'))) {
			return call_user_func(array ($this,	$key));
		}
		return parent :: getVar($key, $format);
	}
	
	/**
	 * Duplicates the functionality of toArray() but does not execute getVar() overrides that require DB calls
	 * 
	 * Use this function when parsing multiple articles for display. If a getVar() override executes 
	 * a DB query (for example, to lookup a value in another table) then parsing multiple articles 
	 * will trigger that query multiple times. If you are doing this for a multiple fields and a 
	 * large number of articles, this can result in a huge number of queries. It is more efficient
	 * then to build a reference buffer for each such field and then do the lookups in memory 
	 * instead. However, you need to create a reference buffer for each value where you want to 
	 * avoid a DB lookup and manually assign the value in your code
	 *
	 * @return array
	 */
	public function toArrayWithoutOverrides() {
		
		$vars = $this->getVars();
		$do_not_override = array(0 => 'tag', 1 => 'rights');
		$ret = array();
		
		foreach ($vars as $key => $var) {
			if (in_array($key, $do_not_override)) {
				$value = $this->getVar($key, 'e');
			} else {
				$value = $this->getVar($key);
			}
			$ret[$key] = $value;
		}

		if ($this->handler->identifierName != "") {
			$controller = new icms_ipf_Controller($this->handler);
			$ret['itemLink'] = $controller->getItemLink($this);
			$ret['itemUrl'] = $controller->getItemLink($this, true);
			$ret['editItemLink'] = $controller->getEditItemLink($this, false, true);
			$ret['deleteItemLink'] = $controller->getDeleteItemLink($this, false, true);
			$ret['printAndMailLink'] = $controller->getPrintAndMailLink($this);
		}
		
		return $ret;
	}
	
	/**
	 * Converts pipe-delimited creator field to comma separated for user side presentation
	 * 
	 * @return	string
	 */
	public function creator() {
		
		$creator = '';
		$creator = $this->getVar('creator', 'e');
		
		return str_replace("|", ", ",  $creator);
	}
	
	/**
	 * Converts the display_topic_image field to human readable value
	 * 
	 * @return string
	 */
	
	public function display_topic_image() {
		
		$display_topic_image = $this->getVar('display_topic_image', 'e');
		
		switch ($display_topic_image) {
			case "1":
				return _CO_NEWS_ARTICLE_LEFT;
				break;
			
			case "2":
				return _CO_NEWS_ARTICLE_RIGHT;
				break;
			
			default: // case "0"
				return _CO_NEWS_ARTICLE_NO;
		}
	}
	
	/**
	 * Returns an image tag to display the lead image for this article
	 *
	 * @return string 
	 */
	public function get_lead_image_tag() {
		
		$lead_image = $image_tag = '';
		
		$lead_image = $this->getVar('lead_image', 'e');
		if (!empty($lead_image)) {
			$image_tag = '/uploads/' . basename(dirname(dirname(__FILE__))) . '/article/'
				. $lead_image;
		}

		return $image_tag;
	}

	/**
	 * Converts the display_lead_image field to human readable value
	 * 
	 * @return string
	 */
	
	public function display_lead_image() {
		
		$display_lead_image = $this->getVar('display_lead_image', 'e');
		
		switch ($display_lead_image) {
			case "1":
				return _CO_NEWS_ARTICLE_LEFT;
				break;
			
			case "2":
				return _CO_NEWS_ARTICLE_RIGHT;
				break;
			
			default: // case "0"
				return _CO_NEWS_ARTICLE_NO;
		}
	}
	
	/**
	 * Load tags linked to this post
	 *
	 * @return void
	 */
	public function loadTags() {
		
		$ret = '';
		
		$sprocketsModule = icms_getModuleInfo('sprockets');
		if ($sprocketsModule) {
			$sprockets_taglink_handler = icms_getModuleHandler('taglink',
					$sprocketsModule->getVar('dirname'), 'sprockets');
			$ret = $sprockets_taglink_handler->getTagsForObject($this->id(), $this->handler);
			$this->setVar('tag', $ret);
		}
	}

	/**
	 * Converts the rights ID into a human readable value with link
	 *
	 * @return string
	 */
	public function rights() {
		
		$rights_id = $rightsObj = $rights = $sprocketsModule = $sprockets_rights_handler = '';
		
		$rights_id = $this->getVar('rights', 'e');
		
		$sprocketsModule = icms_getModuleInfo('sprockets');
		if ($sprocketsModule) {			
			$sprockets_rights_handler = icms_getModuleHandler('rights',
				$sprocketsModule->getVar('dirname'), 'sprockets');
			$rightsObj = $sprockets_rights_handler->get($rights_id);
			$rights = $rightsObj->getItemLink();
			
		} else {
			
			$rights = false;
		}
		
		return $rights;
	}

	/**
	 * Returns the submitter's username and link to their account
	 * 
	 * @return string
	 */
	public function submitter() {

		return news_getLinkedUnameFromId($this->getVar('submitter', 'e'));
	}

	/**
	 * Converts the date field into a human readable value, using the format specified in preferences
	 *
	 * @global object $newsConfig
	 * @return string
	 */
	public function date() {
		
		global $newsConfig;
		$date = '';
		$date = $this->getVar('date', 'e');
		
		return date($newsConfig['date_format'], $date);
	}

	/**
	 * Converts the online status of an object to a human readable icon with link toggle
	 *
	 * @return string 
	 */
	public function online_status() {
		
		$status = $button = '';
		
		$status = $this->getVar('online_status', 'e');
		$button = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/admin/article.php?article_id=' . $this->getVar('article_id')
				. '&amp;op=changeStatus">';
		if ($status == false) {
			$button .= '<img src="../images/button_cancel.png" alt="' . _CO_NEWS_ARTICLE_ONLINE 
				. '" title="' . _CO_NEWS_ARTICLE_SWITCH_OFFLINE . '" /></a>';
			
		} else {
			
			$button .= '<img src="../images/button_ok.png" alt="' . _CO_NEWS_ARTICLE_OFFLINE
				. '" title="' . _CO_NEWS_ARTICLE_SWITCH_ONLINE . '" /></a>';
		}
		return $button;
	}
	
	/**
	 * Converts the federated field of an object to a human readable icon with link toggle
	 *
	 * @return string 
	 */
	public function federated() {
		
		$status = $button = '';
		
		$status = $this->getVar('federated', 'e');
		$button = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/admin/article.php?article_id=' . $this->getVar('article_id')
				. '&amp;op=changeFederation">';
		
		if ($status == false) {
			$button .= '<img src="../images/button_cancel.png" alt="' . _CO_NEWS_ARTICLE_ONLINE 
				. '" title="' . _CO_NEWS_ARTICLE_DISABLE_FEDERATION . '" /></a>';
			
		} else {
			
			$button .= '<img src="../images/button_ok.png" alt="' . _CO_NEWS_ARTICLE_OFFLINE
				. '" title="' . _CO_NEWS_ARTICLE_ENABLE_FEDERATION . '" /></a>';
		}
		return $button;
	}

	/**
	 * Sends notifications to subscribers when a new article is published, called by afterSave()
	 */
	public function sendNotifArticlePublished() {
		
		$item_id = $module_handler = $module = $notification_handler = '';
		$tags = array();
		
		$item_id = $this->id();
		$module_handler = xoops_getHandler('module');
		$module = $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));
		$module_id = $module->getVar('mid');
		$notification_handler = xoops_getHandler ('notification');

		$tags = array();
		$tags['ITEM_TITLE'] = $this->title();
		$tags['ITEM_URL'] = $this->getItemLink(true);

		// global notification
		$notification_handler->triggerEvent('global', 0, 'article_published', $tags,
			array(), $module_id, 0);
	}
}
	
class NewsArticleHandler extends IcmsPersistableObjectHandler {

	/**
	 * Constructor
	 */
	public function __construct(& $db) {
		$this->IcmsPersistableObjectHandler($db, 'article', 'article_id', 'title', 'description', 'news');

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

		// storing tags
		$sprocketsModule = icms_getModuleInfo('sprockets');
		
		if ($sprocketsModule) {
			$sprockets_taglink_handler = icms_getModuleHandler('taglink', 
					$sprocketsModule->dirname(), 'sprockets', 'sprockets');
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
		
		$notification_handler =& xoops_gethandler('notification');
		$module_handler = xoops_getHandler('module');
		$module = $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));
		$module_id = $module->getVar('mid');
		$category = 'global';
		$item_id = $obj->id();
		
		// delete article bookmarks
		$category = 'article';
		$notification_handler->unsubscribeByItem($module_id, $category, $item_id);
		$notification_handler =& xoops_gethandler('notification');
		$module_handler = xoops_getHandler('module');
		$module = $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));
		$module_id = $module->getVar('mid');
		$category = 'global';
		$item_id = $obj->id();

		// delete article notifications
		$category = 'article';
		$notification_handler->unsubscribeByItem($module_id, $category, $item_id);

		// delete taglinks
		if ($sprocketsModule) {
			$sprockets_taglink_handler = icms_getModuleHandler('taglink',
					$sprocketsModule->dirname(), 'sprockets');
			$sprockets_taglink_handler->deleteAllForObject(&$obj);
		}

		return true;
	}
}