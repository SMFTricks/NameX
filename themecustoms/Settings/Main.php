<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Settings;

use ThemeCustoms\Init;

class Main
{
	/**
	 * @var array The common theme settings
	 */
	private $_settings = [];

	/**
	 * Will allow to separate the settings if needed.
	 * No type means the setting is either a default setting or a main setting.
	 * @var array Setting types.
	 */
	private $_setting_types = [];

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
	private $_custom_settings = [];

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
		global $txt, $context;

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

		// Theme Settings
		$this->_settings = [
			// Fonts
			[
				'section_title' => $txt['st_cdn_source'],
				'id' => 'st_fonts_source',
				'label' => $txt['st_fonts'],
				'description' => $txt['st_fonts_desc'],
				'type' => 'list',
				'options' => [
					0 => $txt['st_cdn_local'],
					1 => $txt['st_cdn_google'],
				]
			],
			// jQuery UI CDN
			[
				'id' => 'st_jquery_ui_source',
				'label' => $txt['st_jqueryui'],
				'description' => $txt['st_cdn_source_desc'],
				'type' => 'list',
				'options' => [
					0 => $txt['st_cdn_local'],
					1 => $txt['st_cdn_google'],
				]
			],
			// Font Awesome CDN
			[
				'id' => 'st_fontawesome_source',
				'label' => $txt['st_fontawesome'],
				'description' => $txt['st_cdn_source_desc'],
				'type' => 'list',
				'options' => [
					0 => $txt['st_cdn_local'],
					1 => $txt['st_cdn_cloudflare'],
				]
			],
			// Menu Settings
			[
				'id' => 'st_loginlogout_menu',
				'label' => sprintf($txt['st_loginlogout_menu'], $txt['logout'], $txt['login'], $txt['register']),
			],
			[
				'section_title' => $txt['st_menu_settings'],
				'id' => 'st_disable_menu_icons',
				'label' => $txt['st_disable_menu_icons'],
				'description' => $txt['st_disable_menu_icons_desc'],
				'type' => 'checkbox',
			],
			[
				'id' => 'st_remove_items',
				'label' => $txt['st_remove_items'],
				'description' => $txt['st_remove_items_desc'],
				'type' => 'text',
			],
			[
				'id' => 'st_enable_community',
				'label' => $txt['st_enable_community'],
				'description' => $txt['st_enable_community_desc'],
			],
			[
				'id' => 'st_not_community',
				'label' => $txt['st_not_community'],
				'description' => $txt['st_not_community_desc'],
				'type' => 'text',
			],
			[
				'id' => 'st_community_forum',
				'label' => $txt['st_community_forum'],
				'description' => $txt['st_community_forum_desc'],
			],
			// Additional settings
			[
				'section_title' => $txt['st_additional_settings'],
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
		];
		
		/** Socials */
		$this->socials();
		
		/** Avatars */
		$this->avatars();

		/** Custom Links */
		$this->links();

		// Any custom changes?
		call_integration_hook('integrate_customtheme_settings', [&$this->_custom_settings, &$this->_setting_types, &$this->_remove_settings]);

		// Add any custom settings
		if (!empty($this->_custom_settings) && is_array($this->_custom_settings))
			$this->_settings = array_merge($this->_settings, $this->_custom_settings);
	}

	/**
	 * Main::socials()
	 * 
	 * Add settings for socials
	 * 
	 * @return void
	 */
	private function socials() : void
	{
		global $txt, $scripturl;

		// Add the type
		$this->_setting_types[] = 'social';

		// Social settings
		array_push($this->_settings, 
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
				'id' => 'st_tiktok',
				'label' => $txt['st_tiktok_username'],
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
				'id' => 'st_steam',
				'label' => $txt['st_steam_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_github',
				'label' => $txt['st_github_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_linkedin',
				'label' => $txt['st_linkedin_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_rss_url',
				'label' => $txt['st_rss_url'],
				'description' => sprintf($txt['st_rss_url_desc'], $scripturl) . '<br>' . $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			]
		);
	}

	/**
	 * Main::avatars()
	 * 
	 * Add settings for avatars
	 * 
	 * @return void
	 */
	private function avatars() : void
	{
		global $txt;

		// Are avatars enabled?
		if (empty(Init::$_avatar_options))
			return;

		// Boards
		$this->_settings[] = [
			'section_title' => $txt['st_avatar_settings'],
			'id' => 'st_enable_avatars_boards',
			'label' => $txt['st_enable_avatars_boards'],
			'type' => 'checkbox',
		];
		// Topics
		$this->_settings[] = [
			'id' => 'st_enable_avatars_topics',
			'label' => $txt['st_enable_avatars_topics'],
			'type' => 'checkbox',
		];
		// Recent Posts
		$this->_settings[] = [
			'id' => 'st_enable_avatars_recent',
			'label' => $txt['st_enable_avatars_recent'],
			'type' => 'checkbox',
		];
		// Users Online
		$this->_settings[] = [
			'id' => 'st_enable_avatars_online',
			'label' => $txt['st_enable_avatars_online'],
			'type' => 'checkbox',
		];
	}

	/**
	 * Main::links()
	 * 
	 * Add custom links settings
	 * 
	 * @return void
	 */
	private function links() : void
	{
		global $txt;

		// Adding links settings?
		if (empty(Init::$_settings['custom_links_limit']))
			return;

		// Add the type
		$this->_setting_types[] = 'custom_links';

		// Enable custom links
		$this->_settings[] = [
			'id' => 'st_custom_links_enabled',
			'label' => $txt['st_custom_links_enabled'],
			'theme_type' => 'custom_links',
		];

		// Add the links settings
		for ($link = 1; $link <= Init::$_settings['custom_links_limit']; $link++)
		{
			// Title
			$this->_settings[] = [
				'id' => 'st_custom_link' . $link. '_title',
				'label' => $txt['st_custom_link_title'],
				'type' => 'text',
				'theme_type' => 'custom_links',
			];
			// Link
			$this->_settings[] = [
				'id' => 'st_custom_link' . $link,
				'label' => sprintf($txt['st_custom_link'], $link),
				'description' => $txt['st_custom_link_url'],
				'type' => 'text',
				'theme_type' => 'custom_links',
			];
		}
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