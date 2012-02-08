<?php
/**
* Common functions used by the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * Formats articles for user-side display, prepares them for insertion to templates
 * 
 * @param object $articleObj
 * @return array 
 */
function prepareArticleForDisplay($articleObj, $with_overrides = TRUE) {

	global $newsConfig;
	
	$articleArray = array();
	
	if ($with_overrides) {
		$articleArray = $articleObj->toArray();
	} else {
		$articleArray = $articleObj->toArrayWithoutOverrides();
	}

	// ensure the raw value is used for display_topic_image
	$articleArray['display_topic_image'] = $articleObj->getVar('display_topic_image', 'e');
	
	// create an image tag for the lead image
	$articleArray['lead_image'] = $articleObj->get_lead_image_tag();

	// specify the size of the lead image as per module preferences, for the resized_image plugin
	$articleArray['lead_image_display_width'] = $newsConfig['lead_image_display_width'];
	
	// for some reason IPF inserts some content into dynamic text areas that should be empty
	$articleArray['extended_text'] = trim($articleArray['extended_text']);

	$articleArray['date'] = date($newsConfig['date_format'], $articleObj->getVar('date', 'e'));
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
 * Get module admin link
 *
 * @todo to be move in icms core
 *
 * @param string $moduleName dirname of the module
 * @return string URL of the admin side of the module
 */

function news_getModuleAdminLink($moduleName='news') {
	
	$newsModule = icms_getModuleInfo(basename(dirname(dirname(__FILE__))));
	
	if (!$moduleName && (isset ($newsModule) && is_object($newsModule))) {
		$moduleName = $newsModule->getVar('dirname');
	}
	$ret = '';
	if ($moduleName) {
		$ret = "<a href='" . ICMS_URL . "/modules/$moduleName/admin/index.php'>"
			. _MD_NEWS_ADMIN_PAGE . "</a>";
	}
	return $ret;
}

/**
 * Returns the module name, optionally with link
 *
 * @param bool $withLink
 * @param bool $forBreadCrumb
 * @param string $moduleName
 * @return string 
 */
function news_getModuleName($withLink = TRUE, $forBreadCrumb = FALSE) {
	
	if (!icms_get_module_status("news")) {
		return '';
	}

	if (!$withLink) {
		return icms::$module->getVar('name');
	} else {
		$ret = ICMS_URL . '/modules/' . icms::$module->getVar('dirname') . '/';
		return '<a href="' . $ret . '">' . icms::$module->getVar('name') . '</a>';
	}
}

/**
 * Get URL of previous page
 *
 * @todo to be moved in ImpressCMS 1.2 core
 *
 * @param string $default default page if previous page is not found
 * @return string previous page URL
 */
function news_getPreviousPage($default=FALSE) {
	
	global $impresscms;
	
	if (isset($impresscms->urls['previouspage'])) {
		return $impresscms->urls['previouspage'];
	} elseif($default) {
		return $default;
	} else {
		return ICMS_URL;
	}
}

/**
 * Get month name by its ID
 *
 * @todo to be moved in ImpressCMS 1.2 core
 *
 * @param int $month_id ID of the month
 * @return string month name
 */
function news_getMonthNameById($month_id) {
	return Icms_getMonthNameById($month_id);
}

/**
 * @package SimplyWiki
 * @author Wiwimod: Xavier JIMENEZ
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version $Id
 */

/**
 * Basic validation and sanitation of user input but NOT range checking (calling method should do)
 *
 * This can be expanded for all different types of input: email, URL, filenames, media/mimetypes
 *
 * @param array $input_var Array of user input, gathered from $_GET, $_POST or $_SESSION
 * @param array $valid_vars Array of valid variables and data type (integer, boolean, string,)
 * @return array Array of validated and sanitized variables
 */

function news_validate($input_var, $valid_vars) {
	
	$clean_var = array();

	foreach ($valid_vars as $key => $type) {
		if (empty($input_var[$key])) {
			$input_var[$key] = NULL;
			continue;
		}
		
		switch ($type) {
			case 'int':
			case 'integer':
				$clean_var[$key] = $dirty_int = $clean_int = 0;
				if (filter_var($input_var[$key], FILTER_VALIDATE_INT) == TRUE) {
					$dirty_int = filter_var($input_var[$key], FILTER_SANITIZE_NUMBER_INT);
					$clean_int = mysql_real_escape_string($dirty_int);
					$clean_var[$key] = (int)$clean_int;
				}
				break;

			case 'html': // tolerate (but encode) html tags and entities
				// initialise
				$dirty_html = $clean_html = $clean_var[$key] = '';
				// test for string
				if (is_string($input_var[$key])) {
					// trim fore and aft whitespace
					$dirty_html = trim($input_var[$key]);
					// keep html tags but encode entities and special characters
					$dirty_html = filter_var($dirty_html, FILTER_SANITIZE_SPECIAL_CHARS);
					$clean_html = mysql_real_escape_string($dirty_html);
					$clean_var[$key] = (string)$clean_html;
				}
				break;

			case 'plaintext': // stripped down plaintext with tags removed
				// initialise
				$dirty_text = $clean_text = $clean_var[$key] = '';
				// test for string (in PHP, what isn't??)
				if (is_string($input_var[$key])) {
					// trim fore and aft whitespace
					$dirty_text = trim($input_var[$key]);
					// strip html tags, encode quotes and special characters
					$dirty_text = filter_var($dirty_text, FILTER_SANITIZE_STRING);
					$clean_text = mysql_real_escape_string($dirty_text);
					$clean_var[$key] = (string)$clean_text;
				}
				break;

			case 'name':
				// initialise
				$clean_var[$key] = $clean_name = $dirty_name = '';
				$pattern = '^[a-zA-Z\-\']{1,60}$';
				// test for string + alphanumeric
				if (is_string($input_var[$key]) && preg_match($pattern, $input_var[$key])) {
					// trim fore and aft whitespace
					$dirty_name = trim($input_var[$key]);
					// strip html tags, encode quotes and special characters
					$dirty_name = filter_var($dirty_name, FILTER_SANITIZE_STRING);
					$clean_name = mysql_real_escape_string($dirty_name);
					$clean_var[$key] = (string)$clean_name;
				}
				break;

			case 'email':
				$clean_var[$key] = $dirty_email = $clean_email = '';
				if (filter_var($input_var[$key], FILTER_VALIDATE_EMAIL) == TRUE) {
					$dirty_email = filter_var($input_var[$key], FILTER_SANITIZE_EMAIL);
					$clean_email = mysql_real_escape_string($dirty_email);
					$clean_var[$key] = (string)$clean_email;
				}
				break;

			case 'url':
				// initialise
				$clean_var[$key] = $dirty_url = $clean_url = '';
				// validate and sanitise URL
				if (filter_var($input_var[$key], FILTER_VALIDATE_URL) == TRUE) {
					$dirty_url = filter_var($input_var[$key], FILTER_SANITIZE_URL);
					$clean_url = mysql_real_escape_string($dirty_url);
					$clean_var[$key] = $clean_url;
				}

			case 'float':
			case 'double':
			case 'real':
				// initialise
				$clean_var[$key] = $clean_float = 0;
				// validate and sanitise float
				if (filter_var($input_var[$key], FILTER_VALIDATE_FLOAT) == TRUE) {
					$clean_float = filter_var($input_var[$key], FILTER_SANITIZE_NUMBER_FLOAT);
					$clean_var[$key] = (float)$clean_float;
				}
				break;

			case 'bool':
			case 'boolean':
				$clean_var[$key] = FALSE;
				if (is_bool($input_var[$key])) {
					$clean_var[$key] = (bool) $input_var[$key];
				}
				break;

			case 'binary':/* only PHP6 - for now
				if (is_string($input_var[$key])) {
				$clean_var[$key] = htmlspecialchars(trim($input_var[$key]));
				}*/
				break;

			case 'array': // note: doesn't inspect array *contents*, must be separately sanitised
				if (is_array($input_var[$key]) && !empty($input_var[$key])) {
					$clean_var[$key] = $input_var[$key];
				} else {
					$clean_var[$key] = $input_var[$key];
				}
				break;

			case 'object': // note: doesn't inspect object *properties*, must be treated separately
				if (is_object($input_var[$key])) {
					$clean_var[$key] = (object)$input_var[$key];
				}
				break;
		}
	}
	return $clean_var;
}