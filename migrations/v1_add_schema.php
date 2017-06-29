<?php
/**
 *
 * @package phpBB.de Newsletter
 * @copyright (c) 2017 phpBB.de
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace phpbbde\newsletter\migrations;

class v1_add_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'user_allow_newsletter');
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_allow_newsletter'		=> array('BOOL', '1', 'after' => 'user_actkey'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_allow_newsletter',
				),
			),
		);
	}
}
