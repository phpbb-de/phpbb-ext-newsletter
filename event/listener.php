<?php
/**
 *
 * @package phpBB.de External Images as link
 * @copyright (c) 2015-2016 phpBB.de
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace phpbbde\newsletter\event;

/**
* @ignore
*/
use phpbb\config\config;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	const ID_NEWSLETTER = 72;

	/** @var config */
	protected $config;

	/** @var string */
	protected $phpbb_root_path;

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
	 * @param config		$config
	 * @param template		$template
	 * @param user			$user
	 */
	public function __construct(config $config, request_interface $request, template $template, user $user, $phpbb_root_path, $users_table)
	{
		$this->config = $config;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->users_table = $users_table;

		//$this->user->add_lang_ext('phpbbde/externalimgaslink', 'extimgaslink');
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_email_modify_sql'		=> 'get_newsletter_users',
			'core.acp_email_send_before'	=> array(
				'modify_email_template',
				'post_newsletter_archive', // Workaround since there is no _after event
			),
			'core.acp_email_display'		=> 'modify_acp_email_template',
		);
	}

	/**
	 * Adds settings for this extension to the ACP
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
	 * Adds settings for this extension to the ACP
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
	 * Adds settings for this extension to the ACP
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

		// TODO: modify $template_data
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

		include_once($this->phpbb_root_path . 'includes/functions_posting.php');

		$subject = $event['subject'];
		$message = $this->request->variable('message', '', true);
		$bbcode_uid = $bbcode_bitfield = $flags = '';

		generate_text_for_storage($message, $bbcode_uid, $bbcode_bitfield, $flags, true, true, true);

		$poll_data = array();
		$post_data = array(
			'forum_id'		=> self::ID_NEWSLETTER,
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

		submit_post('post', $subject, $this->user->data['username'], POST_NORMAL, $poll_data, $post_data);

		$event['generate_log_entry'] = false;
		if (!empty($event['usernames']))
		{
			// TODO: log with implode(', ', utf8_normalize_nfc($usernames));
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
				$group_name = $this->user->lang['ALL_USERS'];
			}

			// TODO: log with $group_name
		}
	}

	/**
	 * Adds settings for this extension to the ACP
	 *
	 * @param \phpbb\event\data $event
	 * @return null
	 * @access public
	 */
	public function acp_add_config($event)
	{
		/*if ($event['mode'] !== 'post')
		{
			return;
		}

		$own_vars = array(
			'extimgaslink_config'	=> array(
				'lang' => 'EXTIMGASLINK_CONFIG',
				'validate' => 'int:0',
				'type' => 'select',
				'function' => array($this->helper, 'extimgaslink_config_select'),
				'params' => array('{CONFIG_VALUE}'),
				'explain' => true,
			),
		);

		$vars = $event['display_vars'];
		$vars['vars'] = helper::array_insert($vars['vars'], 'allow_post_links', $own_vars);
		$event['display_vars'] = $vars;*/
	}


}
