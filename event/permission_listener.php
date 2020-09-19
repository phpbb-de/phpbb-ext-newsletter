<?php
/**
 *
 * Newsletter extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020 Crizzo <https://www.phpBB.de>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbde\newsletter\event;

/**
* @ignore
*/

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/

class permission_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth		$auth
	 */
	public function __construct(\phpbb\auth\auth $auth)
	{
		$this->auth = $auth;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions' => 'permissions',
		);
	}

	/**
	 * Add permissions
	 *
	 * @param	object	$event	The event object
	 * @return	null
	 * @access	public
	 */
	public function permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions['a_newsletter'] = array('lang' => 'ACL_A_NEWSLETTER', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}
}
