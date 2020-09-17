<?php
/**
*
* Newsletter [German (Casual Honorifics)]
*
* @package language
* @copyright (c) 2020 phpBB.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_NEWSLETTER_SETTINGS_EXPLAIN'			=> 'On this settings page you can define the forum-id for the archive forum and the newsletter credits footer.',

	'ACP_NEWSLETTER_ARCHIVE_SETTINGS' 			=> 'Newsletter forum archive',
	'ACP_NEWSLETTER_ARCHIVE_SETTINGS_EXPLAIN' 	=> 'You can post the newsletter parallely to the sending via email into a newsletter forum.',

	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID'			=> 'Newsletter Archiv-Forum',
	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID_EXPLAIN'	=> 'Definiere die Foren-ID, in die der Newsletter abgelegt werden soll. Die Eingabe von „0“ deaktiviert diese Funktion.',

	'ACP_NEWSLETTER_SETTINGS_UPDATED'		=> 'Newsletter Einstellungen wurden aktualisiert.',
	'ACP_NEWSLETTER_SETTINGS_NOT_UPDATED'	=> 'Newsletter Einstellungen wurden aktualisiert. Keine gültige Foren-ID.',
));
