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
	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID'			=> 'Newsletter archive forum',
	'ACP_NEWSLETTER_ARCHIVE_FORUM_ID_EXPLAIN'	=> 'Define the forum-id, in which the newsletter should be posted. If you input ”0“ the function is disabled.',
));
