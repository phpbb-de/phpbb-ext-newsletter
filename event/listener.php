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
use phpbb\config\config;
use phpbb\log\log_interface;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var string */
	protected $php_ext;

	/** @var log_interface */
	protected $phpbb_log;

	/** @var string */
	protected $phpbb_root_path;

	/* @var \phpbb\language\language */
	protected $language;

	/** @var request_interface */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var string */
	protected $users_table;

	/**
	 * Constructor
	 *
	 * @param config				$config
	 * @param log_interface			$phpbb_log
	 * @param \phpbb\language\language	$language
	 * @param request_interface		$request
	 * @param template				$template
	 * @param user					$user
	 * @param string				$php_ext
	 * @param string				$phpbb_root_path
	 * @param string				$users_table
	 */
	public function __construct(
		\phpbb\config\config $config,
		log_interface $phpbb_log,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$php_ext,
		$phpbb_root_path,
		$users_table)
	{
		$this->config = $config;
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
			'core.ucp_prefs_modify_common'			=> 'modify_ucp_prefs',
			'core.ucp_prefs_personal_update_data'	=> 'update_ucp_newsletter',
			'core.permissions'						=> 'add_permissions',
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
			'U_IMPRINT'		=> generate_board_url(true) . "/go/impressum", // TODO: Make this changeable
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
		$this->language->add_lang('common' , 'phpbbde/newsletter');

		if (!$this->request->is_set_post('newsletter'))
		{
			return;
		}

		include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);

		$subject = $event['subject'];
		$message = $this->request->variable('message', '', true);
		$bbcode_uid = $bbcode_bitfield = $flags = '';

		generate_text_for_storage($message, $bbcode_uid, $bbcode_bitfield, $flags, true, true, true);

		$poll_data = array();
		$post_data = array(
			'forum_id'		=> $this->config['phpbbde_newsletter_archive_forum'],
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
}
