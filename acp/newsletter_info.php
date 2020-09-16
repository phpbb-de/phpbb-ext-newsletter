<?php
/**
*
* Newsletter extension for the phpBB Forum Software package.
*
* @copyright (c) 2020 Crizzo <https://www.phpBB.de>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbbde\newsletter\acp;

class newsletter_info
{
	function module()
	{
		return array(
			'filename'	=> '\phpbbde\newsletter\acp\newsletter_module',
			'title'		=> 'ACP_NEWSLETTER_TITLE',
			'modes'		=> array(
				'settings'	=> array(
						'title' => 'ACP_NEWSLETTER_SETTINGS',
						'auth' => 'ext_phpbbde/newsletter && acl_a_newsletter',
						'cat' => array('ACP_IP_ANONYM_TITLE')
					),
			),
		);
	}
}
