<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms;

if (!defined('SMF'))
	die('No direct access...');

class Settings
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
	 * Settings::themeSettings()
	 *
	 * The monstrous theme settings array.
	 * New settings are added in here.
	 */
	public function themeSettings()
	{
		// Create the theme settings
		$this->createSettings();

		// Remove unwanted settings
		$this->removeSettings();

		// Add theme settings
		$this->addSettings();

		// Remove the values from undesired settings
		$this->undoSettings();
	}

	/**
	 * Settings::createSettings()
	 *
	 * Adds settings to the theme
	 */
	private function createSettings()
	{
		global $txt, $settings, $context;

		// Insert forum width setting at the beginning
		$context['theme_settings'] = array_merge([
			[
				'id' => 'st_custom_width',
				'label' => $txt['st_custom_width'],
				'description' => $txt['st_custom_width_desc'],
				'type' => 'text',
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
				'id' => 'st_separate_sticky_locked',
				'label' => $txt['st_separate_sticky_locked'],
				'description' => $txt['st_separate_sticky_locked_desc'],
				'type' => 'checkbox'
			],
			[
				'section_title' => $txt['st_avatar_settings'],
				'id' => 'st_enable_avatars_boards',
				'label' => $txt['st_enable_avatars_boards'],
				'type' => 'checkbox',
			],
			[
				'id' => 'st_enable_avatars_topics',
				'label' => $txt['st_enable_avatars_topics'],
				'type' => 'checkbox',
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
				'description' => $txt['st_rss_url_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
		];

		// Any custom changes?
		call_integration_hook('integrate_customtheme_settings', [&$this->_custom_settings, &$this->_setting_types, &$this->_remove_settings]);

		// Add any custom settings
		if (!empty($this->_custom_settings) && is_array($this->_custom_settings))
			$this->_settings = array_merge($this->_settings, $this->_custom_settings);
	}

	/**
	 * Settings::removeSettings()
	 *
	 * Remove any unwanted settingss
	 */
	private function removeSettings()
	{
		global $context;

		// Remove Settings
		if (!empty($this->_remove_settings))
			foreach ($context['theme_settings'] as $key => $theme_setting)
				if (isset($theme_setting['id']) && in_array($theme_setting['id'], $this->_remove_settings))
					unset($context['theme_settings'][$key]);
	}

	/**
	 * Settings::addSettings()
	 *
	 * Inserts the theme settings in the array
	 */
	private function addSettings()
	{
		global $context;

		// Add the setting types
		if (!empty($this->_setting_types))
			$context['st_themecustoms_setting_types'] = array_merge(['main'], $this->_setting_types);

		// Insert the new theme settings in the array
		$context['theme_settings'] = array_merge($context['theme_settings'], $this->_settings);
	}

	/**
	 * Settings::undoSettings()
	 *
	 * Prevents undesired settings from affecting the forum.
	 * It obviously doesn't remove any setting from the database, just "disables" them.
	 * 
	 * @return void
	 */
	private function undoSettings()
	{
		global $settings;

		// Good riddance!
		if (!empty($this->_remove_settings))
			foreach ($this->_remove_settings as $remove_setting)
				unset($settings[$remove_setting]);
	}
}