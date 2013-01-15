<?php
/**
 * Handles incoming OAIPMH requests for the News module as per the OAIPMH specification.
 *
 * External metadata harvesters submit OAIPMH queries against this file for processing. The OAIPMH
 * specification outlines a standard vocabulary for requests and responses are defined by the spec's
 * XML schema, handled by an Archive object in the optional Sprockets module. If you don't want to 
 * enable the OAIPMH functionality of this module you can safely remove this file. But it is 
 * probably easier just to turn off OAIPMH functionality in the Sprockets module (option 1: don't 
 * create an Archive object for this module, option 2: each archive object has a kill switch). 
 * XML responses are assembled in a buffer and then flushed in a gzip compressed stream.
 * 
 * For more information visit the Open Archives Initiative, http://www.openarchives.org
 *
 * @copyright	Copyright Isengard.biz 2010, distributed under GNU GPL V2 or any later version
 * @license	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since	1.0
 * @author	Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package	News
 * @version	$Id$
 */

include_once 'header.php';

$xoopsOption['template_main'] = 'news_article.html';
include_once ICMS_ROOT_PATH . '/header.php';

// initialise
$dirty_vars = $allowed_vars = $clean_vars = array();
$verb = $identifier = $identification = $metadataPrefix = $from = $until = $set
	= $resumptionToken = $identification = $getRecord = $listMetadataFormats = $listSets
	= $listRecords = $badVerb = '';

$cursor = 0; // will be overriden if there is a valid $resumptionToken

//////////////////////////////////////////////
////////// BEGIN INPUT SANITISATION //////////
//////////////////////////////////////////////

// whitelist acceptable variables
$allowed_vars = array('verb' => 'plaintext', 'identifier' => 'plaintext',
	'metadataPrefix' => 'plaintext', 'from' => 'plaintext', 'until' => 'plaintext',
	'set' => 'plaintext', 'resumptionToken' => 'plaintext', 'cursor' => 'int');

// OAIPMH spec requires support for both GET and POST requests
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$dirty_vars = $_GET;
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$dirty_vars = $_POST;
}

/*
 *  If there is a resumption token, restore state from that INSTEAD of from GET/POST variables.
 *  This will work so long as *all* the required state information is serialised in the 
 *  resumption token. State is set in the Sprockets module /class/archive/lookup_records()
 */

if (!empty($dirty_vars['resumptionToken']) && ($dirty_vars['verb'] == 'ListIdentifiers' 
		|| $dirty_vars['verb'] == 'ListRecords' || $dirty_vars['verb'] == 'ListSets')) {
	if(get_magic_quotes_gpc()) {
		$dirty_vars = unserialize(stripslashes(urldecode($dirty_vars['resumptionToken'])));
	} else {
		$dirty_vars = unserialize(urldecode($dirty_vars['resumptionToken']));
	}
	$dirty_vars['resumptionToken'] = TRUE;
}

// channel whitelisted variables through the validator function
$clean_vars = news_validate($dirty_vars, $allowed_vars);

// extract the sanitised variables
extract($clean_vars);

//////////////////////////////////////////////////////////
////////// END INPUT SANITISATION ////////////////////////
//////////////////////////////////////////////////////////

// set up the relevant archive and handlers relevant to target object
$newsModule = icms_getModuleInfo(basename(dirname(__FILE__)));
$sprocketsModule = icms_getModuleInfo('sprockets');

if (icms_get_module_status("sprockets"))
{
	$news_article_handler = icms_getModuleHandler('article', $newsModule->getVar('dirname'), 'news');
	$sprockets_archive_handler = icms_getModuleHandler('archive', $sprocketsModule->getVar('dirname'),
		'sprockets');

	$criteria = new icms_db_criteria_Compo();
	$criteria->add(new icms_db_criteria_Item('module_id', $newsModule->getVar('mid')));

	$archive_array = $sprockets_archive_handler->getObjects($criteria);
	$archiveObj = array_shift($archive_array);

	// if no archive object has been created, issue a warning
	if (!$archiveObj) {

		echo _CO_NEWS_ARCHIVE_MUST_CREATE;

	} else {

		// check if this archive is enabled before processing any OAIPMH requests

		if ($archiveObj->getVar('enable_archive', 'e') == 1 ) {

			// IMPORTANT: need to disable the logger because it breaks XML responses	
			icms::$logger->disableLogger();

			////////////////////////////////////////////////////////
			////////// BEGIN OPEN ARCHIVES INITIATIVE API //////////
			////////////////////////////////////////////////////////

			switch ($verb) {

				// retrieve basic information about the archive
				case "Identify":
					$identify_response = $archiveObj->identify();
					$identification = simplexml_load_string($identify_response);
					ob_start("ob_gzhandler");
					header('Content-Type: text/xml');
					print $identification->asXML();
					ob_end_flush();
					exit();
					break;

				// retrieve one record specified by a unique identifier
				case "GetRecord":
					$getRecord = simplexml_load_string($archiveObj->getRecord($news_article_handler,
							$identifier, $metadataPrefix));
					ob_start("ob_gzhandler");
					header('Content-Type: text/xml');
					print $getRecord->asXML();
					ob_end_flush();
					exit();
					break;

				// retrieves record headers rather than full records, time range can be specified
				case "ListIdentifiers":
					if (!empty($from)) {
						if (strlen($from) == 10) {
							$from .= 'T00:00:00Z'; // if granularity is day level, add time to avoid breaking code
						}
					}
					if (!empty($until)) {
						if (strlen($until) == 10) {
							$until .= 'T23:59:59Z'; // if granularity is day level, add time to avoid breaking code
						}
					}
					$listIdentifiers = simplexml_load_string($archiveObj->listIdentifiers($news_article_handler, 
						$metadataPrefix, $from, $until, $set, $resumptionToken, $cursor));
					ob_start("ob_gzhandler");
					header('Content-Type: text/xml');
					print $listIdentifiers->asXML();
					ob_end_flush();
					exit();
					break;

				// list the metadata formats available from this archive
				case "ListMetadataFormats":
					$listMetadataFormats = simplexml_load_string($archiveObj->listMetadataFormats($news_article_handler,
						$identifier));
					ob_start("ob_gzhandler");
					header('Content-Type: text/xml');
					print $listMetadataFormats->asXML();
					ob_end_flush();
					exit();
					break;

				// retrieve multiple records from the repository, time range can be specified
				case "ListRecords":
					if (!empty($from)) {
						if (strlen($from) == 10) {
							$from .= 'T00:00:00Z'; // if granularity is day level, add time to avoid breaking code
						}
					}
					if (!empty($until)) {
						if (strlen($until) == 10) {
							$until .= 'T23:59:59Z'; // if granularity is day level, add time to avoid breaking code
						}
					}
					$listRecords = simplexml_load_string($archiveObj->listRecords($news_article_handler, 
						$metadataPrefix, $from,	$until, $set, $resumptionToken, $cursor));
					ob_start("ob_gzhandler");
					header('Content-Type: text/xml');
					print $listRecords->asXML();
					ob_end_flush();
					exit();
					break;

				// retrieve the set structure of this archive (sets are not implemented)
				case "ListSets":
					$listSets = simplexml_load_string($archiveObj->listSets($resumptionToken, $cursor));
					ob_start("ob_gzhandler");
					header('Content-Type: text/xml');
					print $listSets->asXML();
					ob_end_flush();
					exit();
					break;

				// if we don't know what's going on, throw badVerb error, request is illegal
				default:
					$badVerb = simplexml_load_string($archiveObj->BadVerb());
					ob_start("ob_gzhandler");
					header('Content-Type: text/xml');
					print $badVerb->asXML();
					ob_end_flush();
					exit();
					break;
			}

			//////////////////////////////////////////////////////
			////////// END OPEN ARCHIVES INITIATIVE API //////////
			//////////////////////////////////////////////////////

		} else {

			// archive is disabled, can it
			exit;
		}
	}
	$icmsTpl->assign('archive_module_home', news_getModuleName(TRUE, TRUE));
}
else // Exit if Sprockets module is not installed and active
{
	exit;
}

include_once 'footer.php';