<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Settings;

use ThemeCustoms\Init;

class Main
{
	/**
	 * @var array The common theme settings
	 */
	private $_settings;

	/**
	 * @var array Setting types. Will allow to separate the settings if needed.
	 * No type means the setting is either a default setting or a main setting.
	 */
	private $_setting_types = [
		'social',
	];

	/**
	 * @var array Unwanted settings from the default theme (or custom theme even).
	 */
	private $_remove_settings = [
		'site_slogan',
		'enable_news',
		'forum_width',
	];

	/**
	 * @var array The custom settings that are not listed here
	 */
	private $_custom_settings;

	/**
	 * Main::settings()
	 *
	 * The monstrous theme settings array.
	 * New settings are added in here.
	 */
	public function settings() : void
	{
		// Create the theme settings
		$this->create();

		// Remove unwanted settings
		$this->remove();

		// Add theme settings
		$this->add();

		// Remove the values from undesired settings
		$this->undo();
	}

	/**
	 * Main::create()
	 *
	 * Adds settings to the theme
	 */
	private function create() : void
	{
		global $txt, $settings, $context;

		// Insert forum width setting at the beginning
		$context['theme_settings'] = array_merge([
			[
				'id' => 'st_custom_width',
				'label' => $txt['st_custom_width'],
				'description' => $txt['st_custom_width_desc'],
				'type' => 'text',
			],
			[
				'id' => 'st_site_color',
				'label' => $txt['st_site_color'],
				'description' => $txt['st_site_color_desc'],
				'type' => 'color',
			]
		], $context['theme_settings']);

		// Add compatibility for the 'disable icon' setting
		if (!isset($settings['disable_menu_icons']))
			$context['theme_settings'] = array_merge([
				[
					'id' => 'st_disable_menu_icons',
					'label' => $txt['st_disable_menu_icons'],
					'description' => $txt['st_disable_menu_icons_desc'],
					'type' => 'checkbox',
				]
			], $context['theme_settings']);

		// Theme Settings
		$this->_settings = [
			[
				'section_title' => $txt['st_additional_settings'],
				'id' => 'st_remove_items',
				'label' => $txt['st_remove_items'],
				'description' => $txt['st_remove_items_desc'],
				'type' => 'text',
			],		
			[
				'id' => 'st_separate_sticky_locked',
				'label' => $txt['st_separate_sticky_locked'],
				'description' => $txt['st_separate_sticky_locked_desc'],
				'type' => 'checkbox'
			],
			[
				'id' => 'st_disable_info_center',
				'label' => $txt['st_disable_info_center'],
				'description' => $txt['st_disable_info_center_desc'],
				'type' => 'checkbox'
			],	
			[
				'id' => 'st_facebook',
				'label' => $txt['st_facebook_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_twitter',
				'label' => $txt['st_twitter_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_instagram',
				'label' => $txt['st_instagram_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_youtube',
				'label' => $txt['st_youtube_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_twitch',
				'label' => $txt['st_twitch_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_discord',
				'label' => $txt['st_discord'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_rss_url',
				'label' => $txt['st_rss_url'],
				'description' => $txt['st_rss_url_desc'] . '<br>' . $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
		];

		/** Avatars */
		// Boards
		if (!empty(Init::$_avatar_options['boards']))
			// Boards
			$this->_settings[] = [
				'section_title' => $txt['st_avatar_settings'],
				'id' => 'st_enable_avatars_boards',
				'label' => $txt['st_enable_avatars_boards'],
				'type' => 'checkbox',
			];
		// Topics
		if (!empty(Init::$_avatar_options['topics_list']))
			$this->_settings[] = [
				'id' => 'st_enable_avatars_topics',
				'label' => $txt['st_enable_avatars_topics'],
				'type' => 'checkbox',
			];
		// Recent Posts
		if (!empty(Init::$_avatar_options['recent_posts']))
			$this->_settings[] = [
				'id' => 'st_enable_avatars_recent',
				'label' => $txt['st_enable_avatars_recent'],
				'type' => 'checkbox',
			];
		// Users Online
		if (!empty(Init::$_avatar_options['users_online']))
			$this->_settings[] = [
				'id' => 'st_enable_avatars_online',
				'label' => $txt['st_enable_avatars_online'],
				'type' => 'checkbox',
			];

		// Any custom changes?
		call_integration_hook('integrate_customtheme_settings', [&$this->_custom_settings, &$this->_setting_types, &$this->_remove_settings]);

		// Add any custom settings
		if (!empty($this->_custom_settings) && is_array($this->_custom_settings))
			$this->_settings = array_merge($this->_settings, $this->_custom_settings);
	}

	/**
	 * Main::remove()
	 *
	 * Remove any unwanted settingss
	 */
	private function remove() : void
	{
		global $context;

		// Remove Settings
		if (!empty($this->_remove_settings))
			foreach ($context['theme_settings'] as $key => $theme_setting)
				if (isset($theme_setting['id']) && in_array($theme_setting['id'], $this->_remove_settings))
					unset($context['theme_settings'][$key]);
	}

	/**
	 * Main::add()
	 *
	 * Inserts the theme settings in the array
	 */
	private function add() : void
	{
		global $context;

		// Add the setting types
		if (!empty($this->_setting_types))
			$context['st_themecustoms_setting_types'] = array_merge(['main'], $this->_setting_types);

		// Insert the new theme settings in the array
		$context['theme_settings'] = array_merge($context['theme_settings'], $this->_settings);
	}

	/**
	 * Main::undo()
	 *
	 * Prevents undesired settings from affecting the forum.
	 * It obviously doesn't remove any setting from the database, just "disables" them.
	 * 
	 * @return void
	 */
	private function undo() : void
	{
		global $settings;

		// Good riddance!
		if (!empty($this->_remove_settings))
			foreach ($this->_remove_settings as $remove_setting)
				unset($settings[$remove_setting]);
	}
}