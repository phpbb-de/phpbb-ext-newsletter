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

	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID'			=> 'Newsletter archive forum',
	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID_EXPLAIN'	=> 'Define the forum-id, in which the newsletter should be posted. If you input ”0“ the function is disabled.',

	'ACP_NEWSLETTER_SETTINGS_UPDATED'		=> 'Newsletter settings were updated.',
	'ACP_NEWSLETTER_SETTINGS_NOT_UPDATED'	=> 'Newsletter settings were not updated. No valid forum id.',
	'ACP_NEWSLETTER_SETTINGS_ARCHIVE_DISABLED'	=> 'Newsletter settings were updated. Archive forum will not be used.'
));
