<?php

/**
* Class representing News article objects
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

class NewsArticle extends icms_ipf_seo_Object {
	
	/**
	 * Constructor
	 *
	 * @param object $handler NewsPostHandler object
	 */
	public function __construct(& $handler) {
		
		global $icmsConfig, $newsConfig;

		parent::__construct($handler);

		$this->quickInitVar('article_id', XOBJ_DTYPE_INT, TRUE);
		$this->quickInitVar('title', XOBJ_DTYPE_TXTBOX, TRUE);
		$this->quickInitVar('creator', XOBJ_DTYPE_TXTBOX, FALSE);
		$this->initNonPersistableVar('tag', XOBJ_DTYPE_INT, 'tag', FALSE, FALSE, FALSE, TRUE);
		$this->quickInitVar('display_topic_image', XOBJ_DTYPE_INT, TRUE, FALSE, FALSE,
				$newsConfig['display_topic_image']);
		$this->quickInitVar('description', XOBJ_DTYPE_TXTAREA, TRUE);
		$this->quickInitVar('extended_text', XOBJ_DTYPE_TXTAREA, FALSE);
		$this->quickInitVar('image', XOBJ_DTYPE_IMAGE, FALSE);
		$this->quickInitVar('display_image', XOBJ_DTYPE_INT, TRUE, FALSE, FALSE, 
				$newsConfig['display_image']);
		$this->quickInitVar('rights', XOBJ_DTYPE_INT, FALSE);
		$this->quickInitVar('language', XOBJ_DTYPE_TXTBOX, TRUE, FALSE, FALSE, _LANGCODE);
		$this->quickInitVar('publisher', XOBJ_DTYPE_TXTBOX, TRUE, FALSE, FALSE,
				$icmsConfig['sitename']);
		$this->quickInitVar('type', XOBJ_DTYPE_TXTBOX, TRUE, FALSE, FALSE, 'Text');
		$this->quickInitVar('submitter', XOBJ_DTYPE_INT, TRUE);
		$this->quickInitVar('date', XOBJ_DTYPE_LTIME, TRUE);
		$this->quickInitVar('online_status', XOBJ_DTYPE_INT, TRUE, FALSE, FALSE, 1);
		$this->quickInitVar('syndicated', XOBJ_DTYPE_INT, TRUE, FALSE, FALSE,
				$newsConfig['default_syndication']);
		$this->quickInitVar('federated', XOBJ_DTYPE_INT, TRUE, FALSE, FALSE,
				$newsConfig['default_federation']);
		$this->quickInitVar('oai_identifier', XOBJ_DTYPE_TXTBOX, TRUE, FALSE, FALSE,
				$this->handler->setOaiId());
		$this->initCommonVar('counter');
		$this->initCommonVar('dohtml');
		$this->initCommonVar('dobr');
		$this->initCommonVar('dosmiley');
		$this->initCommonVar('docxode');
		$this->quickInitVar ('article_notification_sent', XOBJ_DTYPE_INT, TRUE, FALSE, FALSE, 0);

		$this->setControl('description', 'dhtmltextarea');
		$this->setControl('extended_text', 'dhtmltextarea');
		$this->setControl('syndicated', 'yesno');

		// only display the tag and rights fields if the sprockets module is installed
		$sprocketsModule = icms_getModuleInfo('sprockets');
		if (icms_get_module_status("sprockets")) {
			$this->setControl('tag', array(
			'name' => 'selectmulti',
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
		
		$this->setControl('display_image', array(
			'name' => 'select',
			'itemHandler' => 'article',
			'method' => 'getTopicImageOptions',
			'module' => 'news'));
		
		// image path
		$this->setControl('image', array('name' => 'image'));
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
				'display_image', 'rights', 'date', 'submitter', 'online_status', 'syndicated', 
			'federated'))) {
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
			$ret['itemUrl'] = $controller->getItemLink($this, TRUE);
			$ret['editItemLink'] = $controller->getEditItemLink($this, FALSE, TRUE);
			$ret['deleteItemLink'] = $controller->getDeleteItemLink($this, FALSE, TRUE);
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
	 * Customise object itemLink to append the SEO-friendly string.
	 */
	public function getItemLinkWithSEOString()
	{
		$short_url = $this->short_url();
		if (!empty($short_url))
		{
			$seo_url = '<a href="' . $this->getItemLink(TRUE) . '&amp;title=' . $this->short_url() 
					. '">' . $this->getVar('title', 'e') . '</a>';
		}
		else
		{
			$seo_url = $this->getItemLink(FALSE);
		}
		
		return $seo_url;
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
	public function get_image_tag() {
		
		$image = $image_tag = '';
		
		$image = $this->getVar('image', 'e');
		if (!empty($image)) {
			$image_tag = '/uploads/' . basename(dirname(dirname(__FILE__))) . '/article/'
				. $image;
		}

		return $image_tag;
	}

	/**
	 * Converts the display_image field to human readable value
	 * 
	 * @return string
	 */
	
	public function display_image() {
		
		$display_image = $this->getVar('display_image', 'e');
		
		switch ($display_image) {
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
		if (icms_get_module_status("sprockets")) {
			$sprockets_taglink_handler = icms_getModuleHandler('taglink',
					$sprocketsModule->getVar('dirname'), 'sprockets');
			$ret = $sprockets_taglink_handler->getTagsForObject($this->id(), $this->handler, 0);
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
		if (icms_get_module_status("sprockets")) {			
			$sprockets_rights_handler = icms_getModuleHandler('rights',
				$sprocketsModule->getVar('dirname'), 'sprockets');
			$rightsObj = $sprockets_rights_handler->get($rights_id);
			$rights = $rightsObj->getItemLink();
			
		} else {
			
			$rights = FALSE;
		}
		
		return $rights;
	}

	/**
	 * Returns the submitter's username and link to their account
	 * 
	 * @return string
	 */
	public function submitter() {
		return icms_member_user_Handler::getUserLink($this->getVar('submitter', 'e'));
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
		if ($status == FALSE) {
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_cancel.png" alt="' 
				. _CO_NEWS_ARTICLE_ONLINE . '" title="' . _CO_NEWS_ARTICLE_SWITCH_OFFLINE . '" /></a>';
			
		} else {
			
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_ok.png" alt="'
					. _CO_NEWS_ARTICLE_OFFLINE . '" title="' . _CO_NEWS_ARTICLE_SWITCH_ONLINE . '" /></a>';
		}
		return $button;
	}
	
	/**
	 * Converts the syndicated field of an object to a human readable icon with link toggle
	 *
	 * @return string 
	 */
	public function syndicated() {
		
		$status = $button = '';
		
		$status = $this->getVar('syndicated', 'e');
		$button = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/admin/article.php?article_id=' . $this->getVar('article_id')
				. '&amp;op=changeSyndication">';
		
		if ($status == FALSE) {
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_cancel.png" alt="' 
					. _CO_NEWS_ARTICLE_ONLINE . '" title="' . _CO_NEWS_ARTICLE_DISABLE_SYNDICATION 
					. '" /></a>';
			
		} else {
			
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_ok.png" alt="' 
					. _CO_NEWS_ARTICLE_OFFLINE . '" title="' . _CO_NEWS_ARTICLE_ENABLE_SYNDICATION 
					. '" /></a>';
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
		
		if ($status == FALSE) {
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_cancel.png" alt="' 
					. _CO_NEWS_ARTICLE_ONLINE . '" title="' . _CO_NEWS_ARTICLE_DISABLE_FEDERATION 
					. '" /></a>';
			
		} else {
			
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_ok.png" alt="' 
					. _CO_NEWS_ARTICLE_OFFLINE . '" title="' . _CO_NEWS_ARTICLE_ENABLE_FEDERATION 
					. '" /></a>';
		}
		return $button;
	}
	
	/**
	 * Formats articles for user-side display, prepares them for insertion to templates
	 * 
	 * @param object $articleObj
	 * @return array 
	 */
	function prepareArticleForDisplay($with_overrides = TRUE) {

		global $newsConfig;

		$articleArray = array();

		if ($with_overrides) {
			$articleArray = $this->toArray();
		} else {
			$articleArray = $this->toArrayWithoutOverrides();
		}

		// Add SEO friendly string to URL
		if (!empty($articleArray['short_url']))
		{
			$articleArray['itemLink'] = $this->getItemLinkWithSEOString();
		}

		// ensure the raw value is used for display_topic_image
		$articleArray['display_topic_image'] = $this->getVar('display_topic_image', 'e');

		// create an image tag for the lead image
		$articleArray['image'] = $this->get_image_tag();

		// specify the size of the lead image as per module preferences, for the resized_image plugin
		$articleArray['image_display_width'] = $newsConfig['image_display_width'];

		// for some reason IPF inserts some content into dynamic text areas that should be empty
		$articleArray['extended_text'] = trim($articleArray['extended_text']);

		$articleArray['date'] = date($newsConfig['date_format'], $this->getVar('date', 'e'));
		if ($newsConfig['display_creator'] == FALSE) {
			unset($articleArray['creator']);
		} else {
			if ($newsConfig['use_submitter_as_creator'] == TRUE) {
				$articleArray['creator'] = $articleArray['submitter'];
			}
		}
		if ($newsConfig['display_counter'] == FALSE) {
			unset($articleArray['counter']);
		} else {
			$articleArray['counter']++;
		}
		return $articleArray;
	}

	/**
	 * Sends notifications to subscribers when a new article is published, called by afterSave()
	 */
	public function sendNotifArticlePublished() {
		
		$item_id = $module_handler = $module = $notification_handler = '';
		$tags = array();
		
		$item_id = $this->id();
		$module_handler = icms::handler('icms_module');
		$module = $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));
		$module_id = $module->getVar('mid');
		$notification_handler = icms::handler( 'icms_data_notification' );

		$tags = array();
		$tags['ITEM_TITLE'] = $this->getVar('title');
		$tags['ITEM_URL'] = $this->getItemLink(TRUE);

		// global notification
		$notification_handler->triggerEvent('global', 0, 'article_published', $tags,
			array(), $module_id, 0);
	}
	
	/**
	* Return a linked username or full name for a specific $userid
	*
	* @TODO: This functionality might be available from the core; if so should use that and get rid of this
	*
	* @param integer $userid uid of the related user
	* @param bool $name TRUE to return the fullname, FALSE to use the username; if TRUE and the user does not have fullname, username will be used instead
	* @param array $users array already containing icms::$user objects in which case we will save a query
	* @param bool $withContact TRUE if we want contact details to be added in the value returned (PM and email links)
	* @return string name of user with a link on his profile
	*/
	function getLinkedUnameFromId($userid, $name = FALSE, $users = array (), $withContact = FALSE)
	{
		if (!is_numeric($userid)) {
			return $userid;
		}

		$user = $fullname = $fullname2 = $linked_user = '';
		$userid = intval($userid);

		if ($userid > 0) {

			if ($users == array())
			{
				//fetching users
				$user = icms::handler('icms_member')->getUser($userid);

			} else {

				if (!isset($users[$userid])) {return $GLOBALS['icmsConfig']['anonymous'];}
				$user = & $users[$userid];
			}

			if (is_object($user)) {

				$username = $user->getVar('uname');
				$fullname = '';
				$fullname2 = $user->getVar('name');

				if (($name) && !empty($fullname2)) {
					$fullname = $user->getVar('name');
				}

				if (!empty ($fullname)) {

					$linkeduser = "$fullname [<a href='" . ICMS_URL
					. "/userinfo.php?uid=" . $userid . "'>" . $ts->htmlSpecialChars($username) . "</a>]";

				} else {

					$linkeduser = "<a href='" . ICMS_URL."/userinfo.php?uid=" . $userid . "'>"
					.  icms_core_DataFilter::htmlSpecialchars($username) . "</a>";
				}

				// add contact info : email + PM
				if ($withContact) {

					$linkeduser .= '<a href="mailto:'.$user->getVar('email') 
						.'"><img style="vertical-align: middle;" src="' . ICMS_URL
						. '/images/icons/email.gif' . '" alt="' . _US_SEND_MAIL . '" title="'
						. _US_SEND_MAIL.'"/></a>';
					$js = "javascript:openWithSelfMain('" . ICMS_URL . '/pmlite.php?send2=1&to_userid='
						. $userid . "', 'pmlite',450,370);";
					$linkeduser .= '<a href="' . $js . '"><img style="vertical-align: middle;" src="'
						. ICMS_URL . '/images/icons/pm.gif' . '" alt="' . _US_SEND_PM . '" title="'
						. _US_SEND_PM . '"/></a>';
				}

				return $linkeduser;
			}
		}
		return $GLOBALS['icmsConfig']['anonymous'];
	}
}