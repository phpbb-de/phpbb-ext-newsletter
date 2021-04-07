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

/**
* @ignore
*/


/**
* @package acp
*/
class newsletter_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	protected $config;
	protected $config_text;
	protected $db;
	protected $language;
	protected $request;
	protected $template;
	protected $user;

	public function main($id, $mode)
	{

		global $config, $template, $request, $phpbb_container, $db, $user;

		/** @var \phpbb\language\language $language */
		$language = $phpbb_container->get('language');
		$config_text = $phpbb_container->get('config_text');

		$this->config = $config;
		$this->config_text = $config_text;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;

		// Mode switch start
		switch ($mode)
		{
			// Settings
			case 'settings':
				// Add the newsletter ACP lang file
				$this->language->add_lang('acp', 'phpbbde/newsletter');

				$this->tpl_name = 'acp_newsletter_settings';
				$this->page_title = ('ACP_NEWSLETTER_SETTINGS');

				$form_name = 'PHPBBDE_NEWSLETTER_SETTINGS';
				add_form_key($form_name);
				$error = '';

				// Build an array of all available forum ids
				$sql = 'SELECT forum_id
				FROM ' . FORUMS_TABLE . '
				WHERE forum_type = 1 ';
				$result = $this->db->sql_query($sql);
				$rows = $this->db->sql_fetchrowset($result);
				$forum_ids = array_column($rows, 'forum_id');
				$this->db->sql_freeresult($result);

				if ($this->request->is_set_post('submit'))
				{
					if (!check_form_key($form_name))
					{
						$error = $this->language->lang('FORM_INVALID');
					}

					// Read forum ID input
					$forum_id_user_input = (int) $this->request->variable('phpbbde_newsletter_archive_forum', 0);

					// Read signature input
					$signature_user_input = (string) $this->request->variable('phpbbde_newsletter_signature_text', '', true);

					if (empty($error) && in_array($forum_id_user_input, $forum_ids))
					{
						$this->config->set('phpbbde_newsletter_archive_forum', $forum_id_user_input);

						$this->config_text->set('phpbbde_newsletter_signature_text', $signature_user_input);

						trigger_error($this->language->lang('ACP_NEWSLETTER_SETTINGS_UPDATED') . adm_back_link($this->u_action));
					}
					else if (empty($error) && $forum_id_user_input == 0)
					{
						$this->config->set('phpbbde_newsletter_archive_forum', $forum_id_user_input);

						$this->config_text->set('phpbbde_newsletter_signature_text', $signature_user_input);

						trigger_error($this->language->lang('ACP_NEWSLETTER_SETTINGS_ARCHIVE_DISABLED') . adm_back_link($this->u_action));
					}
					else
					{
						trigger_error($this->language->lang('ACP_NEWSLETTER_SETTINGS_NOT_UPDATED') . adm_back_link($this->u_action), E_USER_WARNING );
					}
				}

				$this->template->assign_vars(array(
					'ERRORS'								=> $error,
					'U_ACTION'								=> $this->u_action,

					'PHPBBDE_NEWSLETTER_ARCHIVE_FORUM_ID'	=> $this->config['phpbbde_newsletter_archive_forum'],
					'ACP_NEWSLETTER_SIGNATURE_TEXT'			=> $this->config_text->get('phpbbde_newsletter_signature_text'),
				));
			break;
		}
	}
}
