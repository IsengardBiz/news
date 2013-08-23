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
define("_CO_NEWS_ARTICLE_TITLE", "Titel");
define("_CO_NEWS_ARTICLE_TITLE_DSC", " Überschrift deines neuen Artikels.");
define("_CO_NEWS_ARTICLE_CREATOR", "Autor");
define("_CO_NEWS_ARTICLE_CREATOR_DSC", " Trenne verschiedene Autoren mit einem Strich: &#039;|&#039;.");
define("_CO_NEWS_ARTICLE_TAG", "Tag(s)");
define("_CO_NEWS_ARTICLE_TAG_DSC", "Sie können mehrere Tags für einen Artikel bestimmen. Tags werden als Filter und Links eingesetzt.");
define("_CO_NEWS_ARTICLE_DISPLAY_TOPIC_IMAGE", "Tag-Bild anzeigen?");
define("_CO_NEWS_ARTICLE_DISPLAY_TOPIC_IMAGE_DSC", " Tag-Bilder werden nur angezeigt, wenn kein Spotlight-Bild ausgewählt ist.");
define("_CO_NEWS_ARTICLE_IMAGE", "Artikel-Bild");
define("_CO_NEWS_ARTICLE_IMAGE_DSC", "Artikel-Bilder werden in der Einleitung, sowie im Spotlight-Block des Artikels angezeigt. 
Die Anzeigegröße ist in den Moduleinstellungen festgelegt und kann dort jeder Zeit angepasst werden (Das Bild wird danach neu berechnet).
Es werden die Dateiformate .jpg, .png und .gif unterstützt.");
define("_CO_NEWS_ARTICLE_DISPLAY_IMAGE", "Artikel-Bild anzeigen?");
define("_CO_NEWS_ARTICLE_DISPLAY_IMAGEDSC", "Richtet das Artikel-Bild links, oder rechts aus, oder macht es unsichtbar.");
define("_CO_NEWS_ARTICLE_DESCRIPTION", "Einleitung");
define("_CO_NEWS_ARTICLE_DESCRIPTION_DSC", " Tragen Sie hier den Einleitungstext für Ihren Artikel ein. Dieser wird auf der Newsseite dargestellt.");
define("_CO_NEWS_ARTICLE_EXTENDED_TEXT", "Hauptteil");
define("_CO_NEWS_ARTICLE_EXTENDED_TEXT_DSC", " Führen Sie hier Ihren Artikel fort.");
define("_CO_NEWS_ARTICLE_LANGUAGE", "Sprache");
define("_CO_NEWS_ARTICLE_RIGHTS", "Rechte");
define("_CO_NEWS_ARTICLE_RIGHTS_DSC", " Diese Artikel wird unter den geistigen Eigentumsrechten veröffentlicht:");
define("_CO_NEWS_ARTICLE_ATTACHMENT", "Anhang");
define("_CO_NEWS_ARTICLE_ATTACHMENT_DSC", " Sie können dem Artikel einen begleitenden Anhang beifügen.");
define("_CO_NEWS_ARTICLE_SUBMITTER", "Einsender");
define("_CO_NEWS_ARTICLE_SUBMITTER_DSC", " Person, welche für diesen Artikel verantwortlich ist.");
define("_CO_NEWS_ARTICLE_DATE", "Veröffentlichungsdatum");
define("_CO_NEWS_ARTICLE_DATE_DSC", " Sie können diesen Artikel zu einem späteren Zeitpunkt automatisch veröffentlichen lassen:");
define("_CO_NEWS_ARTICLE_ONLINE_STATUS", "Online?");
define("_CO_NEWS_ARTICLE_ONLINE_STATUS_DSC", " Schalten Sie diesen Artikel on- oder offline.");
define("_CO_NEWS_ARTICLE_FEDERATED", "Federated?");
define("_CO_NEWS_ARTICLE_FEDERATED_DSC", "Syndicate this soundtrack's metadata with other
    sites (cross site search) via the Open Archives Initiative Protocol for Metadata Harvesting.");
define("_CO_NEWS_ARTICLE_OAI_IDENTIFIER", "OAI Identifier");
define("_CO_NEWS_ARTICLE_OAI_IDENTIFIER_DSC", " Einzigartiger Identifizierer für den gebrauch des Open-Archives-Initiative-Protokolls für Metadata Harvesting. Reserviert für zukünftigen Gebrauch.");
define("_CO_NEWS_ARTICLE_YES", "Ja");
define("_CO_NEWS_ARTICLE_NO", "Nein");
define("_CO_NEWS_ARTICLE_LEFT", "Links");
define("_CO_NEWS_ARTICLE_RIGHT", "Rechts");
define("_CO_NEWS_ARTICLE_POSTED_BY", "Verfasst von");
define("_CO_NEWS_ARTICLE_POSTED_ON", "Posted on");
define("_CO_NEWS_ARTICLE_READS", "Leser");
define("_CO_NEWS_ARTICLE_READ_MORE", "Weiter lesen...");
define("_CO_NEWS_ARTICLE_TAGS", "Tags: ");
define("_CO_NEWS_ARTICLE_ALL_TAGS", "-- Alle Artikel --");
define("_CO_NEWS_ALL", "Alle News");
define("_CO_NEWS_SUBSCRIBE_RSS", "Subscribe unseren Newsfeed");
define("_CO_NEWS_SUBSCRIBE_RSS_ON", "Subscribe unseren Newsfeed auf: ");
define("_CO_NEWS_ARTICLE_SWITCH_OFFLINE", "Artikel wurde offline geschaltet.");
define("_CO_NEWS_ARTICLE_SWITCH_ONLINE", "Artikel wurde online geschaltet.");
define("_CO_NEWS_ARTICLE_OFFLINE", "Offline");
define("_CO_NEWS_ARTICLE_ONLINE", "Online");
define("_CO_NEWS_ARTICLE_FEDERATION_ENABLED", "Aktiviert");
define("_CO_NEWS_ARTICLE_FEDERATION_DISABLED", "Deaktiviert");
define("_CO_NEWS_ARTICLE_ENABLE_FEDERATION", "Aktiviere OAIPMH federation");
define("_CO_NEWS_ARTICLE_DISABLE_FEDERATION", "Deaktiviere OAIPMH federation");
define("_CO_NEWS_NEW", "Aktuelle News");
define("_CO_NEWS_NEW_DSC", "Die neuste News von ");
define("_CO_NEWS_META_TITLE", "News");
define("_CO_NEWS_META_DESCRIPTION", "Die neuste News auf ");

// archive
define("_CO_NEWS_ARCHIVES", "News Archiv");
define("_CO_NEWS_ARCHIVE", "Archiv");
define("_CO_NEWS_ARCHIVE_DESCRIPTION", "Archivierte Artikel sortiert nach Monat.");
define("_CO_NEWS_ARCHIVE_ARTICLES", "Artikel");
define("_CO_NEWS_ARCHIVE_ACTIONS", "Actions");
define("_CO_NEWS_ARCHIVE_DATE", "Datum");
define("_CO_NEWS_ARCHIVE_VIEWS", "Gelesen");
define("_CO_NEWS_ARCHIVE_PRINTERFRIENDLY", "Druckansicht");
define("_CO_NEWS_ARCHIVE_SENDSTORY", "Artikel einem Freund senden");
define("_CO_NEWS_ARCHIVE_TAGS", "Tags");
define("_CO_NEWS_ARCHIVE_TAGS_DESCRIPTION", "Zeige Artikel nach Tags sortiert.");
define("_CO_NEWS_ARCHIVE_THEREAREINTOTAL", "Es sind insgesamt: ");
define("_CO_NEWS_ARCHIVE_ARTICLES_LOWER", " Artikel:");
define("_CO_NEWS_ARCHIVE_NOT_CONFIGURED", "Sprockets ist so eingestellt, dass es ankommende OAIPMH-Anfragen ablehnt");
define("_CO_NEWS_ARCHIVE_MUST_CREATE", "Fehler: Ein Archivobjekt muss zunächst erstellt werden, bevor OAIPMH-Anfragen bearbeitet werden können.
Bitte erstellen Sie ein Archivobjekt über den Open Archive Karteireiter in der Sprockets Administration.");
define("_CO_NEWS_NO_ARCHIVE", "Sorry, es gibt noch keine Artikel zum anzeigen.");

// calendar
define("_CO_NEWS_CAL_JANUARY", "Januar");
define("_CO_NEWS_CAL_FEBRUARY", "Februar");
define("_CO_NEWS_CAL_MARCH", "März");
define("_CO_NEWS_CAL_APRIL", "April");
define("_CO_NEWS_CAL_MAY", "Mai");
define("_CO_NEWS_CAL_JUNE", "Juni");
define("_CO_NEWS_CAL_JULY", "Juli");
define("_CO_NEWS_CAL_AUGUST", "August");
define("_CO_NEWS_CAL_SEPTEMBER", "September");
define("_CO_NEWS_CAL_OCTOBER", "Oktober");
define("_CO_NEWS_CAL_NOVEMBER", "November");
define("_CO_NEWS_CAL_DECEMBER", "Dezember");

// topics
define("_CO_NEWS_NO_TOPICS_TO_DISPLAY", "Zur Zeit gibt es keine Themen zum anzeigen.");
define("_CO_NEWS_NEWS_TOPICS", "News tags");
//define("", "");

// some tag constants that should be pulled from Sprockets, but for whatever reason, aren't
define("_CO_SPROCKETS_TAG_TAG", "Tag");
define("_CO_SPROCKETS_TAG_CATEGORY", "Kategorien");
define("_CO_SPROCKETS_TAG_BOTH", "Beide");
define("_CO_SPROCKETS_TAG_SWITCH_ONLINE", "Online stellen");
define("_CO_SPROCKETS_TAG_SWITCH_OFFLINE", "Offline stellen");
define("_CO_SPROCKETS_TAG_ONLINE", "Online");
define("_CO_SPROCKETS_TAG_OFFLINE", "Offline");
define("_CO_SPROCKETS_TAG_YES", "Ja");
define("_CO_SPROCKETS_TAG_NO", "Nein");
define("_CO_SPROCKETS_TAG_NAVIGATION_ENABLE", "Als Navigationelement aktivieren");
define("_CO_SPROCKETS_TAG_NAVIGATION_DISABLE", "Als Navigationelement anzeigen");
define("_CO_SPROCKETS_TAG_NAVIGATION_ENABLED", "Navigationelement aktiviert");
define("_CO_SPROCKETS_TAG_NAVIGATION_DISABLED", "Navigationelement deaktiviert");