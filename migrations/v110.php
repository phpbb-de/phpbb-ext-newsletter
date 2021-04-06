<?php
/**
*
* Newsletter extension for the phpBB Forum Software package.
*
* @copyright (c) 2020 Crizzo <https://www.phpBB.de>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbbde\newsletter\migrations;

class v110 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\phpbbde\newsletter\migrations\v1_add_schema',
		);
	}

	public function update_data()
	{
		$data = array(
				// Add ACP module
				array('module.add', array(
					'acp',
					'ACP_CAT_DOT_MODS',
					'ACP_NEWSLETTER_TITLE'
				)),
				array('module.add', array(
					'acp',
					'ACP_NEWSLETTER_TITLE',
					array(
						'module_basename'	=> '\phpbbde\newsletter\acp\newsletter_module',
						'modes'				=> array('settings'),
					),
				)),
				// Add config values
				array('config.add', array('phpbbde_newsletter_archive_forum', 0)),
                // Add config_text value for signature
                array('config_text.add', array('phpbbde_newsletter_signature_text', '')),
				// Add permissions
				array('permission.add', array('a_newsletter', true)),
			);

			// Check if admin standard role exists and assign permission to it
			if ($this->role_exists('ROLE_ADMIN_STANDARD'))
			{
				$data[] = array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_newsletter', 'role', true));
			}

			// Check if admin full role exists and assign permission to it
			if ($this->role_exists('ROLE_ADMIN_FULL'))
			{
				$data[] = array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_newsletter', 'role', true));
			}

		return $data;
	}

	/**
	 * Checks whether the given role does exist or not.
	 *
	 * @param String $role the name of the role
	 * @return true if the role exists, false otherwise
	 * Source: https://github.com/paul999/mention/
	 */
	private function role_exists($role)
	{
		$sql = 'SELECT role_id
			FROM ' . ACL_ROLES_TABLE . "
			WHERE role_name = '" . $this->db->sql_escape($role) . "'";
		$result = $this->db->sql_query_limit($sql, 1);
		$role_id = $this->db->sql_fetchfield('role_id');
		$this->db->sql_freeresult($result);
		return $role_id;
	}
}
