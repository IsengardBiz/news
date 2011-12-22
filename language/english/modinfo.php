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
define("_MI_NEWS_ARTICLES", "Artikel");

// preferences
define("_MI_NEWS_TOPIC_IMAGE_DEFAULT", "Tag-Bild Anzeige-Einstellungen");
define("_MI_NEWS_TOPIC_IMAGE_DEFAULTDSC", "Legt einen Standardwert für das Feld zum Artikeleinsenden fest. 
	Tag Bilder sind nur dann verfügbar, wenn das Sprockets-Module installiert ist.");
define("_MI_NEWS_LEAD_IMAGE_DEFAULT", "Artikel-Bild Anzeige-Einstellungen");
define("_MI_NEWS_LEAD_IMAGE_DEFAULTDSC", "Legt einen Standardwert für das Feld zum Artikeleinsenden fest. 
	Sie können ihn überschreiben.");
define("_MI_NEWS_LEAD_IMAGE_DISPLAY_WIDTH", "Artikel-Bild breite (pixels)");
define("_MI_NEWS_LEAD_IMAGE_DISPLAY_WIDTHDSC", "Mit dieser Breite werden Artikel-Bilder angezeigt. Verändern Sie diesen Wert, werden die bestehenden Bilder automatisch angepasst.");
define("_MI_NEWS_IMAGE_UPLOAD_HEIGHT", "Maximale HÖHE der hochgeladenen Artikel-Bilder (pixels)");
define("_MI_NEWS_IMAGE_UPLOAD_HEIGHTDSC", "Dies ist die maximale Höhe für den Bilderupload der Artikel-Bilder. Vergessen Sie nicht, dass die Bilder automatisch von der größe angepasst werden. Es ist also in Ordnung auch größere Maße zuzulassen.");
define("_MI_NEWS_IMAGE_UPLOAD_WIDTH", "Maximale BREITE der hochgeladenen Artikel-Bilder (pixels)");
define("_MI_NEWS_IMAGE_UPLOAD_WIDTHDSC", "Dies ist die maximale Breite für den Bilderupload der Artikel-Bilder.
	Vergessen Sie nicht, dass die Bilder automatisch von der größe angepasst werden. Es ist also in Ordnung auch größere Maße zuzulassen");
define("_MI_NEWS_IMAGE_FILE_SIZE", "Maximale Dateigröße für hochzuladende Bilder (bytes)");
define("_MI_NEWS_IMAGE_FILE_SIZEDSC", "Maximale Größe (in Bytes) für den Bilderupload. Beachten Sie, dass ihr Server/Webspace diese Größe einschränken kann.
	Wir empfehlen Größen kleiner als 2MB.");
define("_MI_NEWS_ARTICLE_NO", "Nein");
define("_MI_NEWS_ARTICLE_LEFT", "Links");
define("_MI_NEWS_ARTICLE_RIGHT", "Rechts");
define("_MI_NEWS_ARTICLERECENT", "Neuste Artikel");
define("_MI_NEWS_ARTICLERECENTDSC", "Zeigt den neusten Artikel");
define("_MI_NEWS_ARCHIVE", "Archiv");
define("_MI_NEWS_TOPICS_DIRECTORY", "Tags");

// notifications - categories
define("_MI_NEWS_GLOBAL_NOTIFY", "Alle Artikel");
define("_MI_NEWS_GLOBAL_NOTIFY_DSC", "Benachrichtigung bezogen auf Artikel in allen Kategorien.");
define("_MI_NEWS_ARTICLE_NOTIFY", "Artikel");
define("_MI_NEWS_ARTICLE_NOTIFY_DSC", "Benachrichtigungen für einzelne Artikel.");
		  
// notifications - global
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY", "Neuer Artikel wurde veröffentlicht.");
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY_CAP", "Benachrichtige mich, wenn in irgendeiner Kategorie ein neuer Artikel veröffentlicht wird.");
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY_DSC", "Erhalte Benachrichtigungen wenn ein neuer Artikel veröffentlicht ist.");
define("_MI_NEWS_GLOBAL_ARTICLE_PUBLISHED_NOTIFY_SBJ", "Ein neuer Artikel wurde auf {X_SITENAME} veröffentlicht");
define("_MI_NEWS_TEMPLATES", "Templates");

// preferences
define("_MI_NEWS_NUMBER_ARTICLES_PER_PAGE", "Wie viele Artikel sollen auf der Indexseite angezeigt werden?");
define("_MI_NEWS_NUMBER_ARTICLES_PER_PAGEDSC", "Empfohlen werden zwischen 5 to 10 Artikel.");
define("_MI_NEWS_SHOW_TAG_SELECT_BOX", "Anzeigen der Tag-Select-Box auf der Newshauptseite?");
define("_MI_NEWS_SHOW_TAG_SELECT_BOX_DSC", "Tags sind nur verfügbar, wenn das Sprockets-Modul installiert ist.");
define("_MI_NEWS_SHOW_BREADCRUMB", "Anzeigen der Breadcrumb-Navigation?");
define("_MI_NEWS_SHOW_BREADCRUMB_DSC", "Aktivert oder deaktiviert die Breadcrumb-Navigation im Modulheader.");
define("_MI_NEWS_DISPLAY_CREATOR", "Autor anzeigen?");
define("_MI_NEWS_DISPLAY_CREATOR_DSC", "Soll der Name des Autors in den Artikeln erscheinen?.");
define("_MI_NEWS_USE_SUBMITTER_AS_CREATOR", "Soll der Einsender als Autor verwendet werden?");
define("_MI_NEWS_USE_SUBMITTER_AS_CREATOR_DSC", "Sie können sich aussuchen, ob der Einsender auch als Autor angezeigt werden soll 
(verwenden Sie dies, wenn die Admins auch Autoren sind), oder Sie können einstellen, dass Autoren manuell eingestellt werden (Sinnvoll, wenn Admins Artikel für andere Autoren einstellen).");
define("_MI_NEWS_DATE_FORMAT", "Datumsanzeige 
	(<a href=\"http://php.net/manual/en/function.date.php\" target=\"blank\">Siehe Anleitung</a>)");
define("_MI_NEWS_DATE_FORMAT_DSC", "Die Änderung der Datumsanzeige ist analog zum PHP-Datumsformat. Bestimmt ebenfalls die Anzeige im Block für neue Artikel.");
define("_MI_NEWS_DISPLAY_COUNTER", "Besucherzähler anzeigen?");
define("_MI_NEWS_DISPLAY_COUNTER_DSC", "Aktiviert, oder deaktiviert die Anzeige des Besucherzählers.");
define("_MI_NEWS_DISPLAY_PRINTER", "Druckansichts-Icon anzeigen?");
define("_MI_NEWS_NUMBER_RSS_ITEMS", "Anzahl der Artikel im RSS feed");
define("_MI_NEWS_NUMBER_RSS_ITEMS_DSC", "Empfohlen werden zwischen 5 to 10 Artikel.");
define("_MI_NEWS_DISPLAY_RIGHTS", "Urheberrecht anzeigen?");
define("_MI_NEWS_DISPLAY_RIGHTS_DSC", "Schaltet die Artikelbezogene Lizenzrechte-Anzeige ein, oder aus. Dies sollten sie insbesondere dann einschalten, wenn sie Artikel unter verschiedenen Rechten veröffentlichen.
	Veröffentlichen Sie nur unter einer Lizenzart, sollten Sie diese Funktion ausschalten und die Lizenz zum Beispiel im Footer kenntlich machen.");
define("_MI_NEWS_DEFAULT_FEDERATION", "Federate articles by default?");
define("_MI_NEWS_DEFAULT_FEDERATION_DSC", "Sets the default value on the article submission form 
	for your convenience. You can override it.");

//define("", "");