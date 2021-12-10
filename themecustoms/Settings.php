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
	 * @var array The theme settings
	 */
	private static $_settings;

	/**
	 * @var array Setting types. Will allow to separate the settings if needed
	 * No type means the setting is either a default setting or a main setting of the theme.
	 */
	private static $_setting_types = [
		'carousel',
		'color',
		'social',
	];

	/**
	 * @var array Unwanted settings from the default theme (or custom theme even).
	 */
	private static $_remove_settings = [
		'site_slogan',
		'enable_news',
		'forum_width',
	];

	/**
	 * Settings::__construct()
	 *
	 * It handles the settings side of the theme
	 */
	public function __construct()
	{
		// Hook the theme settings
		add_integration_function('integrate_theme_settings', __CLASS__ . '::themeSettings', false);

		// Remove the values from those undesired settings
		self::undoSettings();
	}

	/**
	 * Settings::themeSettings()
	 *
	 * The monstrous theme settings array. New settings are added in here.
	 */
	public function themeSettings()
	{
		// Create the custom settings
		self::createSettings();

		// Remove unwanted settings
		self::removeSettings();

		// Add theme settings
		self::addSettings();
	}

	/**
	 * Settings::addSettings()
	 *
	 * Inserts the theme settings in the array
	 */
	private static function addSettings()
	{
		global $context;

		// I need to add a line break because later on I might do silly things
		if (!empty(self::$_settings))
			$context['theme_settings'][] = '';

		// Add the setting types
		$context['st_setting_types'] = self::$_setting_types;

		// Insert the new theme settings in the array
		$context['theme_settings'] = array_merge($context['theme_settings'], self::$_settings);
	}

	/**
	 * Settings::removeSettings()
	 *
	 * Remove any unwanted settingss
	 */
	private static function removeSettings()
	{
		global $context;

		// Remove Settings
		if (!empty(self::$_remove_settings))
			foreach ($context['theme_settings'] as $key => $theme_setting)
				if (isset($theme_setting['id']) && in_array($theme_setting['id'], self::$_remove_settings))
					unset($context['theme_settings'][$key]);
	}

	/**
	 * Settings::createSettings()
	 *
	 * Adds custom settings to the theme
	 */
	private static function createSettings()
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

		// Theme Custom Settings
		self::$_settings = [
			[
				'id' => 'st_disable_theme_effects',
				'label' => $txt['st_disable_theme_effects'],
				'description' => $txt['st_disable_theme_effects_desc'],
				'type' => 'checkbox'
			],
			[
				'id' => 'st_enable_avatars_boards',
				'label' => $txt['st_enable_avatars_boards'],
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

		// Add compatibility for the 'disable icon' setting
		if (!isset($settings['disable_menu_icons']))
			self::$_settings[] = [
				'id' => 'st_disable_menu_icons',
				'label' => $txt['st_disable_menu_icons'],
				'type' => 'checkbox',
			];
	}

	/**
	 * Settings::undoSettings()
	 *
	 * Prevents undesired settings from affecting the forum.
	 * It obviously doesn't remove any setting from the database, just "disables" them.
	 * 
	 * @return void
	 */
	private static function undoSettings()
	{
		global $settings;

		// Good riddance!
		if (!empty(self::$_remove_settings))
			foreach (self::$_remove_settings as $remove_setting)
				unset($settings[$remove_setting]);
	}

	/**
	 * Settings::admin_areas()
	 *
	 * Mainly I'll just use this one to hook back the news files,
	 * because for some odd reason they cause issues in the template.
	 * 
	 * @param array $areas. The admin areas
	 * 
	 * @return void
	 */
	public function admin_areas(&$areas)
	{
		global $modSettings, $scripturl;

		// Disable the smf_js so it doesn't do silly things when loading the admin page
		$modSettings['disable_smf_js'] = true;

		// The below functions include all the scripts needed from the simplemachines.org site.
		// The language and format are passed for internationalization.
		if (!empty($modSettings['disable_smf_js']))
		echo '
					<script src="', $scripturl, '?action=viewsmfile;filename=current-version.js"></script>
					<script src="', $scripturl, '?action=viewsmfile;filename=latest-news.js"></script>';
	}
}