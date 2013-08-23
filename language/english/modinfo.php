<?php
/**
* English language constants related to module information
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		news
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

// Module Info
define("_MI_NEWS_MD_NAME", "News");
define("_MI_NEWS_MD_DESC", "ImpressCMS Simple Blogging module");
define("_MI_NEWS_ARTICLES", "Articles");

// preferences
define("_MI_NEWS_TOPIC_IMAGE_DEFAULT", "Default tag image display setting");
define("_MI_NEWS_TOPIC_IMAGE_DEFAULTDSC", "Sets the default value in the article submission form. 
	Tag images are only available if the Sprockets module is installed.");
define("_MI_NEWS_IMAGE_DEFAULT", "Default lead image display setting");
define("_MI_NEWS_IMAGE_DEFAULTDSC", "Sets the default value in the article submission form. 
	You can override it.");
define("_MI_NEWS_IMAGE_DISPLAY_WIDTH", "Lead image width (pixels)");
define("_MI_NEWS_IMAGE_DISPLAY_WIDTHDSC", "This is the width at which lead images will be 
	displayed. If you change this value lead images will automatically be rebuilt to the new 
	specification.");
define("_MI_NEWS_IMAGE_UPLOAD_HEIGHT", "Maximum HEIGHT of uploaded lead images (pixels)");
define("_MI_NEWS_IMAGE_UPLOAD_HEIGHTDSC", "This is the maximum height allowed for uploaded
	images. Don't forget that images will be automatically scaled for display, so its ok to allow
	bigger images than you plan to actually use. In fact, it gives your site a bit of flexibility
	should you decide to change the display settings later.");
define("_MI_NEWS_IMAGE_UPLOAD_WIDTH", "Maximum WIDTH of uploaded lead images (pixels)");
define("_MI_NEWS_IMAGE_UPLOAD_WIDTHDSC", "This is the maximum width allowed for uploaded images.
	Don't forget that images will be automatically scaled for display, so its ok to allow bigger
	images than you plan to actually use. In fact it gives your site a bit of flexibility should
	you decide to change the display settings later.");
define("_MI_NEWS_IMAGE_FILE_SIZE", "Maximum image FILE SIZE of uploaded images (bytes)");
define("_MI_NEWS_IMAGE_FILE_SIZEDSC", "This is the maximum size (in bytes) allowed for image
	uploads. Note that your server settings may restrict the maximum size you can upload.
	Recommended (sane) maximum value for general use is < 2MB.");
define("_MI_NEWS_ARTICLE_NO", "No");
define("_MI_NEWS_ARTICLE_LEFT", "Left");
define("_MI_NEWS_ARTICLE_RIGHT", "Right");
define("_MI_NEWS_ARTICLERECENT", "Recent articles");
define("_MI_NEWS_ARTICLERECENTDSC", "Shows the latest news articles");
define("_MI_NEWS_ARCHIVE", "Archive");
define("_MI_NEWS_TOPICS_DIRECTORY", "Tags");

// notifications - categories
define("_MI_NEWS_GLOBAL_NOTIFY", "All articles");
define("_MI_NEWS_GLOBAL_NOTIFY_DSC", "Notifications related to articles in all categories.");
define("_MI_NEWS_ARTICLE_NOTIFY", "Articles");
define("_MI_NEWS_ARTICLE_NOTIFY_DSC", "Notifications related to individual articles.");
		  
// notifications - global
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY", "New article published.");
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY_CAP", "Notify me when a new article is published 
	in any category.");
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY_DSC", "Receive notification when a new article is 
	published.");
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY_SBJ", "New article published at {X_SITENAME}");
define("_MI_NEWS_TEMPLATES", "Templates");

// preferences
define("_MI_NEWS_NUMBER_ARTICLES_PER_PAGE", "Number of article summaries to show on index page");
define("_MI_NEWS_NUMBER_ARTICLES_PER_PAGEDSC", "The recommended (sane) range is 5 to 10.");
define("_MI_NEWS_SHOW_TAG_SELECT_BOX", "Display tag select box on news index page?");
define("_MI_NEWS_SHOW_TAG_SELECT_BOX_DSC", "Tags are only available if the Sprockets module 
	is installed");
define("_MI_NEWS_SHOW_BREADCRUMB", "Display breadcrumb?");
define("_MI_NEWS_SHOW_BREADCRUMB_DSC", "Show or hide the horizontal breadcrumb navigation in the
	module header.");
define("_MI_NEWS_DISPLAY_CREATOR", "Display author field?");
define("_MI_NEWS_DISPLAY_CREATOR_DSC", "Toggle to show or hide the article author on the user
	side.");
define("_MI_NEWS_USE_SUBMITTER_AS_CREATOR", "Use submitter as author?");
define("_MI_NEWS_USE_SUBMITTER_AS_CREATOR_DSC", "You can choose to have the submitter of an article 
	credited as the author on the user side (use this if site admins are also your authors), or you 
	can choose to enter author names manually (use this if your admins are posting articles on 
	behalf of other people).");
define("_MI_NEWS_DATE_FORMAT", "Date format 
	(<a href=\"http://php.net/manual/en/function.date.php\" target=\"blank\">see manual</a>)");
define("_MI_NEWS_DATE_FORMAT_DSC", "You can format the timestamp on your news articles pretty much 
	any way you like by changing the format string as per PHP's date() function. Also governs the 
	date format in the Recent News block.");
define("_MI_NEWS_DISPLAY_COUNTER", "Display hits counter?");
define("_MI_NEWS_DISPLAY_COUNTER_DSC", "Toggle to show or hide the article hits counter on the 
	user side.");
define("_MI_NEWS_DISPLAY_PRINTER", "Display printer-friendly page icon?");
define("_MI_NEWS_NUMBER_RSS_ITEMS", "Number of articles in RSS feeds");
define("_MI_NEWS_NUMBER_RSS_ITEMS_DSC", "Recommended (sane) range is 5-10 articles.");
define("_MI_NEWS_DISPLAY_RIGHTS", "Display intellectual property rights field?");
define("_MI_NEWS_DISPLAY_RIGHTS_DSC", "Toggles the per-article licensing field on or off. If
	you publish articles under a number of different intellectual property licenses, leave it on.
	If your entire site is published under a single kind of license (eg. 'Copyright') its better
	to turn it off and put the notice in your site footer.");
define("_MI_NEWS_DEFAULT_FEDERATION", "Federate articles by default?");
define("_MI_NEWS_DEFAULT_FEDERATION_DSC", "Sets the default value on the article submission form 
	for your convenience. You can override it.");

// New in V1.16
define("_MI_NEWS_DEFAULT_SYNDICATION", "Syndicate (RSS) articles by default?");
define("_MI_NEWS_DEFAULT_SYNDICATION_DSC", "Sets the default value on the article submission form 
	for your convenience. You can override it.");

// New in V1.17
define("_MI_NEWS_DISPLAY_FACEBOOK_COMMENTS", "Enable Facebook comments?");
define("_MI_NEWS_DISPLAY_FACEBOOK_COMMENTSDSC", "You must i) register as a Facebook developer and 
	ii) create a Facebook comments app for this to work. See the instructions in the docs folder of
	this module.");
define("_MI_NEWS_FACEBOOK_COMMENTS_WIDTH", "Width of Facebook comments box (pixels)?");
define("_MI_NEWS_FACEBOOK_COMMENTS_WIDTHDSC", "Set the width you want the Facebook comments box to 
	display at, in pixels.");