<?php
/**
* English language constants commonly used in the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

define("_CO_NEWS_MD_NAME", "News");

// article
define("_CO_NEWS_ARTICLE_TITLE", "Title");
define("_CO_NEWS_ARTICLE_TITLE_DSC", " The headline of your news story.");
define("_CO_NEWS_ARTICLE_CREATOR", "Author");
define("_CO_NEWS_ARTICLE_CREATOR_DSC", " Separate multiple authors with a pipe &#039;|&#039;
	 character.");
define("_CO_NEWS_ARTICLE_TAG", "Tag(s)");
define("_CO_NEWS_ARTICLE_TAG_DSC", "You can apply multiple tags to an article. Tags are used as 
	navigational links and filters.");
define("_CO_NEWS_ARTICLE_DISPLAY_TOPIC_IMAGE", "Display tag image?");
define("_CO_NEWS_ARTICLE_DISPLAY_TOPIC_IMAGE_DSC", " Tag images are only displayed if there is no 
	spotlight images.");
define("_CO_NEWS_ARTICLE_LEAD_IMAGE", "Lead image");
define("_CO_NEWS_ARTICLE_LEAD_IMAGE_DSC", "A feature image that will be displayed in the 
	introduction (description field) of the article, and also in the spotlight block. Display size 
	is specified in module preferences and can be changed at any time (the images will be 
	rebuilt). Images are restricted to .jpg, .png and .gif");
define("_CO_NEWS_ARTICLE_DISPLAY_LEAD_IMAGE", "Display lead image?");
define("_CO_NEWS_ARTICLE_DISPLAY_LEAD_IMAGEDSC", "Float the image to the left, right or hide it.");
define("_CO_NEWS_ARTICLE_DESCRIPTION", "Description");
define("_CO_NEWS_ARTICLE_DESCRIPTION_DSC", " Enter the lead or &#039;teaser&#039; text for your 
	article here. This will be shown on the news index page.");
define("_CO_NEWS_ARTICLE_EXTENDED_TEXT", "Extended text");
define("_CO_NEWS_ARTICLE_EXTENDED_TEXT_DSC", " Enter the rest of the story (beyond the teaser) 
	here.");
define("_CO_NEWS_ARTICLE_LANGUAGE", "Language");
define("_CO_NEWS_ARTICLE_RIGHTS", "Rights");
define("_CO_NEWS_ARTICLE_RIGHTS_DSC", " The intellectual property rights license that this article 
	is distributed under.");
define("_CO_NEWS_ARTICLE_ATTACHMENT", "Attachment");
define("_CO_NEWS_ARTICLE_ATTACHMENT_DSC", " You can upload an attachment to accompany your story.");
define("_CO_NEWS_ARTICLE_SUBMITTER", "Submitter");
define("_CO_NEWS_ARTICLE_SUBMITTER_DSC", " The person responsible for managing this article.");
define("_CO_NEWS_ARTICLE_DATE", "Publication date");
define("_CO_NEWS_ARTICLE_DATE_DSC", " You can schedule the publication of this article until a 
	later date/time.");
define("_CO_NEWS_ARTICLE_ONLINE_STATUS", "Online?");
define("_CO_NEWS_ARTICLE_ONLINE_STATUS_DSC", " Toggle the article on or offline.");
define("_CO_NEWS_ARTICLE_FEDERATED", "Federated?");
define("_CO_NEWS_ARTICLE_FEDERATED_DSC", "Syndicate this soundtrack's metadata with other
    sites (cross site search) via the Open Archives Initiative Protocol for Metadata Harvesting.");
define("_CO_NEWS_ARTICLE_OAI_IDENTIFIER", "OAI Identifier");
define("_CO_NEWS_ARTICLE_OAI_IDENTIFIER_DSC", " Unique identifier for use with the Open Archives 
	Initiative Protocol for Metadata Harvesting. Reserved for future use.");
define("_CO_NEWS_ARTICLE_YES", "Yes");
define("_CO_NEWS_ARTICLE_NO", "No");
define("_CO_NEWS_ARTICLE_LEFT", "Left");
define("_CO_NEWS_ARTICLE_RIGHT", "Right");
define("_CO_NEWS_ARTICLE_POSTED_BY", "Posted by");
define("_CO_NEWS_ARTICLE_POSTED_ON", "Posted on");
define("_CO_NEWS_ARTICLE_READS", "reads");
define("_CO_NEWS_ARTICLE_READ_MORE", "Read more...");
define("_CO_NEWS_ARTICLE_TAGS", "Tags: ");
define("_CO_NEWS_ARTICLE_ALL_TAGS", "-- All articles --");
define("_CO_NEWS_ALL", "All news");
define("_CO_NEWS_SUBSCRIBE_RSS", "Subscribe to our newsfeed");
define("_CO_NEWS_SUBSCRIBE_RSS_ON", "Subscribe to our newsfeed on: ");
define("_CO_NEWS_ARTICLE_SWITCH_OFFLINE", "Article switched offline.");
define("_CO_NEWS_ARTICLE_SWITCH_ONLINE", "Article switched online.");
define("_CO_NEWS_ARTICLE_OFFLINE", "Offline");
define("_CO_NEWS_ARTICLE_ONLINE", "Online");
define("_CO_NEWS_ARTICLE_FEDERATION_ENABLED", "Enabled");
define("_CO_NEWS_ARTICLE_FEDERATION_DISABLED", "Disabled");
define("_CO_NEWS_ARTICLE_ENABLE_FEDERATION", "Enable OAIPMH federation");
define("_CO_NEWS_ARTICLE_DISABLE_FEDERATION", "Disable OAIPMH federation");
define("_CO_NEWS_NEW", "Recent news");
define("_CO_NEWS_NEW_DSC", "The latest news stories from ");
define("_CO_NEWS_META_TITLE", "News");
define("_CO_NEWS_META_DESCRIPTION", "The latest news on ");
define("_CO_NEWS_META_DESCRIPTION_INDEX", "The latest news from ");

// archive
define("_CO_NEWS_ARCHIVES", "News archives");
define("_CO_NEWS_ARCHIVE", "Archive");
define("_CO_NEWS_ARCHIVE_DESCRIPTION", "Archived news articles sorted by month.");
define("_CO_NEWS_ARCHIVE_ARTICLES", "Articles");
define("_CO_NEWS_ARCHIVE_ACTIONS", "Actions");
define("_CO_NEWS_ARCHIVE_DATE", "Date");
define("_CO_NEWS_ARCHIVE_VIEWS", "Views");
define("_CO_NEWS_ARCHIVE_PRINTERFRIENDLY", "Printer friendly view");
define("_CO_NEWS_ARCHIVE_SENDSTORY", "Send story to a friend");
define("_CO_NEWS_ARCHIVE_TAGS", "Tags");
define("_CO_NEWS_ARCHIVE_TAGS_DESCRIPTION", "View news articles sorted by tag.");
define("_CO_NEWS_ARCHIVE_THEREAREINTOTAL", "There are a total of ");
define("_CO_NEWS_ARCHIVE_ARTICLES_LOWER", " articles:");
define("_CO_NEWS_ARCHIVE_NOT_CONFIGURED", "Sprockets is currently configured to refuse incoming
    OAIPMH requests, sorry");
define("_CO_NEWS_ARCHIVE_MUST_CREATE", "Error: An archive object must be created before OAIPMH
    requests can be handled. Please create one via the Open Archive tab in Sprockets administration.");
define("_CO_NEWS_NO_ARCHIVE", "Sorry there are no articles to display yet.");

// calendar
define("_CO_NEWS_CAL_JANUARY", "January");
define("_CO_NEWS_CAL_FEBRUARY", "February");
define("_CO_NEWS_CAL_MARCH", "March");
define("_CO_NEWS_CAL_APRIL", "April");
define("_CO_NEWS_CAL_MAY", "May");
define("_CO_NEWS_CAL_JUNE", "June");
define("_CO_NEWS_CAL_JULY", "July");
define("_CO_NEWS_CAL_AUGUST", "August");
define("_CO_NEWS_CAL_SEPTEMBER", "September");
define("_CO_NEWS_CAL_OCTOBER", "October");
define("_CO_NEWS_CAL_NOVEMBER", "November");
define("_CO_NEWS_CAL_DECEMBER", "December");

// topics
define("_CO_NEWS_NO_TOPICS_TO_DISPLAY", "There are currently no topics to display.");
define("_CO_NEWS_NEWS_TOPICS", "News tags");

// New in V1.16
define("_CO_NEWS_ARTICLE_SYNDICATED", "Syndicated?");
define("_CO_NEWS_ARTICLE_SYNDICATED_DSC", "Include this article in RSS feeds?");
define("_CO_NEWS_ARTICLE_ENABLE_SYNDICATION", "Enable RSS federation");
define("_CO_NEWS_ARTICLE_DISABLE_SYNDICATION", "Disable RSS federation");