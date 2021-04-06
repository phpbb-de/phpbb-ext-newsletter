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
	'ACP_NEWSLETTER_SETTINGS_EXPLAIN'			=> 'Auf dieser Einstellungsseite kannst du das Archiv-Forum und die Newsletter Signatur einrichten.',

	'ACP_NEWSLETTER_ARCHIVE_SETTINGS' 			=> 'Newsletter Archiv-Forum einrichten',
	'ACP_NEWSLETTER_ARCHIVE_SETTINGS_EXPLAIN' 	=> 'Du kannst den Newsletter nicht nur per E-Mail versenden, sondern auch in einem Forum als Beitrag abspeichern.',

	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID'			=> 'Newsletter Archiv-Forum',
	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID_EXPLAIN'	=> 'Definiere die Foren-ID, in die der Newsletter abgelegt werden soll. Die Eingabe von „0“ deaktiviert diese Funktion.',

	'ACP_NEWSLETTER_SETTINGS_UPDATED'			=> 'Newsletter Einstellungen wurden aktualisiert.',
	'ACP_NEWSLETTER_SETTINGS_NOT_UPDATED'		=> 'Newsletter Einstellungen wurden aktualisiert. Keine gültige Foren-ID.',
	'ACP_NEWSLETTER_SETTINGS_ARCHIVE_DISABLED'	=> 'Newsletter Einstellungen wurden aktualisiert. Archiv-Forum wird nicht verwendet.',

	'ACP_NEWSLETTER_SIGNATURE_FS'				=> 'Erstelle eine Signatur',
	'ACP_NEWSLETTER_SIGNATURE_FS_EXPLAIN'		=> 'In diesem Eingabefeld kannst du eine Signatur eintragen, die jedem Newsletter den du versendest angehängt wird. Lasse das Feld leer, um keine Signatur zu verwenden.',
	'ACP_NEWSLETTER_SIGNATURE_TA'				=> 'Signatur',
));
