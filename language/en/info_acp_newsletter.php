<?php
/**
*
* Newsletter [English]
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
	'ACP_NEWSLETTER_TITLE'		=> 'Newsletter',
	'ACP_NEWSLETTER_SETTINGS'	=> 'Newsletter settings',

	'ACP_NEWSLETTER_LOG_ENTRY'	=> 'Newsletter sent',
));
