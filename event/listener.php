<?php
/**
 *
 * @package phpBB.de Newsletter
 * @copyright (c) 2017 phpBB.de
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var string */
	protected $php_ext;

	/** @var \phpbb\log\log_interface; */
	protected $phpbb_log;

	/** @var string */
	protected $phpbb_root_path;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $users_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config			$config				Config object
	 * @param \phpbb\config\db_text			$config_text		Config text object
	 * @param \phpbb\log\log_interface					$phpbb_log			phpBB log system
	 * @param \phpbb\language\language		$language			Language object
	 * @param \phpbb\request\request		$request			Request object
	 * @param \phpbb\template\template 		$template			Template object
	 * @param \phpbb\user     				$user				User object
	 * @param string						$php_ext			PHP extension
	 * @param string						$phpbb_root_path	phpBB root path
	 * @param string						$users_table
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\log\log_interface $phpbb_log,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$php_ext,
		$phpbb_root_path,
		$users_table)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->php_ext = $php_ext;
		$this->phpbb_log = $phpbb_log;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->users_table = $users_table;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @access public
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_email_display'				=> 'modify_acp_email_template',
			'core.acp_email_modify_sql'				=> 'get_newsletter_users',
			//'core.acp_email_send_after'			=> 'post_newsletter_archive',
			'core.acp_email_send_before'			=> array(
				array('modify_email_template', 0),
				array('post_newsletter_archive', 50), // Workaround for event since there is no _after event
			),
			'core.ucp_prefs_modify_common'				=> 'modify_ucp_prefs',
			'core.ucp_prefs_personal_update_data'		=> 'update_ucp_newsletter',
			'core.permissions'							=> 'add_permissions',
			'core.acp_users_prefs_modify_data'			=> 'acp_users_newsletter_settings_get',
			'core.acp_users_prefs_modify_template_data'	=> 'acp_profile_newsletter_template',
			'core.acp_users_prefs_modify_sql'			=> 'acp_profile_newsletter_set',
		);
	}

	/**
	 * Add the extension's permissions to phpBB
	 *
	 * @param \phpbb\event\data $event
	 * @access public
	 */
	public function add_permissions($event)
	{
		// TODO
	}

	/**
	 * Modify SQL to get users which enabled newsletter
	 *
	 * @param \phpbb\event\data $event
	 * @access public
	 */
	public function get_newsletter_users($event)
	{
		if (!$this->request->is_set_post('newsletter'))
		{
			return;
		}

		$sql_ary = $event['sql_ary'];

		$prefix = '';

		if (isset($sql_ary['FROM'][$this->users_table]) && $sql_ary['FROM'][$this->users_table] === 'u')
		{
			$prefix = 'u.';
		}

		$sql_ary['WHERE'] .= " AND {$prefix}user_allow_newsletter = 1";
		$event['sql_ary'] = $sql_ary;
	}

	/**
	 * Modify template data for mass email sending.
	 *
	 * @param \phpbb\event\data $event
	 * @access public
	 */
	public function modify_acp_email_template($event)
	{
		$this->language->add_lang('common' , 'phpbbde/newsletter');

		$event['template_data'] += array(
			'S_SEND_NEWSLETTER'	=> $this->request->is_set_post('newsletter'),
		);
	}

	/**
	 * Modify email template data.
	 *
	 * @param \phpbb\event\data $event
	 * @access public
	 */
	public function modify_email_template($event)
	{
		if (!$this->request->is_set_post('newsletter'))
		{
			return;
		}

		$event['email_template'] = '@phpbbde_newsletter/admin_send_newsletter';

		$event['template_data'] = array_merge($event['template_data'], [
			'U_REMIND'		=> generate_board_url() . "/ucp.{$this->php_ext}?mode=sendpassword",
			'SIGNATURE'		=> $this->config_text->get('phpbbde_newsletter_signature_text'),
		]);
	}

	/**
	 * Modify the UCP template to display newsletter settings
	 *
	 * @param \phpbb\event\data $event
	 * @access public
	 */
	public function modify_ucp_prefs($event)
	{
		if ($event['mode'] !== 'personal')
		{
			return;
		}

		$this->language->add_lang('common' , 'phpbbde/newsletter');

		$this->template->assign_vars(array(
			'NEWSLETTER'	=> $this->user->data['user_allow_newsletter'],
		));
	}

	/**
	 * Adds settings for this extension to the ACP
	 *
	 * @param \phpbb\event\data $event
	 * @access public
	 */
	public function post_newsletter_archive($event)
	{
		if (!$this->request->is_set_post('newsletter'))
		{
			return;
		}

		include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);

		$subject = $event['subject'];
		$message = $this->request->variable('message', '', true);
		$bbcode_uid = $bbcode_bitfield = $flags = '';

		generate_text_for_storage($message, $bbcode_uid, $bbcode_bitfield, $flags, true, true, true);
		$forum_id = $this->config['phpbbde_newsletter_archive_forum'];
		$poll_data = array();
		$post_data = array(
			'forum_id'		=> $forum_id,
			'topic_id'		=> 0,
			'icon_id'		=> false,

			'enable_bbcode'		=> true,
			'enable_smilies'	=> true,
			'enable_urls'		=> true,
			'enable_sig'		=> true,

			'message'			=> $message,
			'message_md5'		=> md5($message),

			'bbcode_bitfield'	=> $bbcode_bitfield,
			'bbcode_uid'		=> $bbcode_uid,

			'post_edit_locked'	=> 0,
			'topic_title'		=> $subject,

			'notify_set'		=> false,
			'notify'			=> false,

			'enable_indexing'	=> true,

			'force_approved_state'	=> true,
		);

		// Only post in a board if the forum_id is set and greater than 0
		if ($post_data['forum_id'] > 0)
		{
			submit_post('post', $subject, $this->user->data['username'], POST_NORMAL, $poll_data, $post_data);
		}

		$event['generate_log_entry'] = false;
		if (!empty($event['usernames']))
		{
			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_NEWSLETTER', false, array(implode(', ', utf8_normalize_nfc($event['usernames']))));
		}
		else
		{
			if ($event['group_id'])
			{
				$group_name = get_group_name($event['group_id']);
			}
			else
			{
				// Not great but the logging routine doesn't cope well with localising on the fly
				$group_name = $this->language->lang('ALL_USERS');
			}

			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_NEWSLETTER', false, array($group_name));
		}
	}

	/**
	 * Update the user's settings for the newsletter
	 *
	 * @param \phpbb\event\data $event
	 * @access public
	 */
	public function update_ucp_newsletter($event)
	{
		$event['sql_ary'] += array(
			'user_allow_newsletter'		=> $this->request->variable('newsletter', (bool) $this->user->data['user_allow_newsletter']),
		);
	}

	public function acp_users_newsletter_settings_get($event)
	{
		$data = $event['data'];
		$user_row = $event['user_row'];
		$data = array_merge($data, array(
			'user_allow_newsletter'		=> $this->request->variable('newsletter', (bool) $user_row['user_allow_newsletter']),
		));
		$event['data'] = $data;
	}

	/**
	 * Assign template data in the ACP
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function acp_profile_newsletter_template($event)
	{
		$this->user->add_lang_ext('phpbbde/newsletter', 'common');
		$data = $event['data'];
		$user_prefs_data = $event['user_prefs_data'];
		$user_prefs_data = array_merge($user_prefs_data, array(
			'NEWSLETTER'				=> $data['user_allow_newsletter'],
		));
		$event['user_prefs_data'] = $user_prefs_data;
	}

	/**
	 * Add user options' state into the sql_array
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function acp_profile_newsletter_set($event)
	{
		$data = $event['data'];
		$sql_ary = $event['sql_ary'];
		$sql_ary = array_merge($sql_ary, array(
			'user_allow_newsletter'		=> $this->request->variable('newsletter', (bool) $event['data']['user_allow_newsletter']),
		));
		$event['sql_ary'] = $sql_ary;
	}
}
