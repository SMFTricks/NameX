<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Integration;

use ThemeCustoms\Init;

class InfoCenter
{
	/**
	 * @var array The members id 
	 */
	private $_members_id = [];

	/**
	 * InfoCenter::avatars()
	 *
	 * It will load the avatars for the info center
	 * 
	 * @return void
	 */
	public function avatars() : void
	{
		global $settings;

		// Check if the info center is even there...
		if (!empty($settings['st_disable_info_center']))
			return;

		// Recent Posts
		if (!empty($settings['st_enable_avatars_recent']))
			$this->get_latest_posts();

		// Users Online
		if (!empty($settings['st_enable_avatars_online']))
			$this->get_online_users();
		
		// Unique ids
		$this->_members_id = array_unique($this->_members_id);

		// Any members then?
		if (empty($this->_members_id))
			return;

		// Get them!
		loadMemberData($this->_members_id);

		// While we are here, insert avatars in the online list
		if (!empty($settings['st_enable_avatars_online']))
			$this->online_users();

		// And now the recent posts
		if (!empty($settings['st_enable_avatars_recent']))
			$this->recent_posts();
	}

	/**
	 * InfoCenter::get_latest_posts()
	 *
	 * Will get the user ids from the latest posts
	 * 
	 * @return void
	 */
	private function get_latest_posts() : void
	{
		global $context, $settings;

		// Check if there's anything to do
		if (empty($settings['number_recent_posts']) || empty(Init::$_avatar_options) || empty($context['latest_posts']))
			return;

		foreach ($context['latest_posts'] as $post)
			$this->_members_id[] = $post['poster']['id'];
	}

	/**
	 * InfoCenter::get_online_users()
	 *
	 * Will get the user ids from the online list
	 * 
	 * @return void
	 */
	private function get_online_users() : void
	{
		global $context;

		// Check if there's anything to do
		if (empty(Init::$_avatar_options) || empty($context['users_online']))
			return;

		foreach ($context['users_online'] as $user)
				$this->_members_id[] = $user['id'];
	}

	/**
	 * InfoCenter::online_users()
	 *
	 * This will add the avatars to the online list
	 * 
	 * @return void
	 */
	protected function online_users() : void
	{
		global $context, $scripturl, $memberContext, $txt;

		// Need a list of users online...
		if (empty($context['list_users_online']))
			return;

		foreach ($context['users_online'] as $item => $user_online)
		{
			// Search the id in the user link using a regular expression
			if (empty($user_online['id']))
				continue;

			// Check if the user was in our list before
			if (!in_array($user_online['id'], $this->_members_id))
				continue;

			// Add the avatar
			loadMemberContext($user_online['id']);
			$context['list_users_online'][$item] = (
				'<span class="user-online-block' . (!empty($user_online['is_buddy']) ? ' buddy' : '') . '">
					<a class="online-avatar"
						href="' . $scripturl . '?action=profile;u=' . $user_online['id'] . '"
						aria-label="' . sprintf($txt['view_profile_of_username'], $user_online['name']) . '">
						<img src="' . $memberContext[$user_online['id']]['avatar']['href'] . '" alt="' . $user_online['name'] . '" title="' . $user_online['name'] . '" class="avatar' . (!empty($memberContext[$user_online['id']]['group_color']) ? ' group-border" style="border-color: ' . $memberContext[$user_online['id']]['group_color'] . ';"' : '"') . '>
					</a>
					<span class="online-name">' . $user_online['link'] . '</span>
				</span>
				'
			);
		}
	}

	/**
	 * InfoCenter::recent_posts()
	 *
	 * This will add the avatars to the array of recent posts
	 * 
	 * @return void
	 */
	protected function recent_posts() : void
	{
		global $context, $settings, $memberContext;

		// Need a list of recent posts...
		if (empty($context['latest_posts']) || empty($settings['number_recent_posts']))
			return;

		foreach ($context['latest_posts'] as $post_id => $post)
		{
			// Check for guests posts making their way in here
			if (empty($post['poster']['id']))
				continue;

			loadMemberContext($post['poster']['id']);
			$context['latest_posts'][$post_id]['poster']['avatar'] = $memberContext[$post['poster']['id']]['avatar'];
		}
	}
}