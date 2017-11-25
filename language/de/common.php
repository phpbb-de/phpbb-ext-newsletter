<?php
/**
*
* Newsletter [German (Casual Honorifics)]
*
* @package language
* @copyright (c) 2017 phpbb.de
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
	'NEWSLETTER'			=> 'Newsletter',
	'NEWSLETTER_EXPLAIN'	=> 'Mit „Ja“ wird dir regelmäßig der Newsletter zugesendet.',

	'SEND_NEWSLETTER'		=> 'Newsletter versenden',
));
