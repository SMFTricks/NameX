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
	private $_theme_settings;

	/**
	 * @var array Setting types. Will allow to separate the settings if needed
	 * No type means the setting is either a default setting or a main setting of the theme.
	 */
	private $_setting_types = [
		'carousel',
		'color',
		'social',
	];

	/**
	 * @var array Unwanted settings from the default theme (or custom theme even).
	 */
	public static $_remove_settings = [
		'site_slogan',
		'enable_news',
		'forum_width',
	];

	/**
	 * Settings::themeSettings()
	 *
	 * The monstrous theme settings array. New settings are added in here.
	 */
	public function themeSettings()
	{
		// Create the custom settings
		$this->createSettings();

		// Add theme settings
		$this->addSettings();

		// Remove unwanted settings
		$this->removeSettings();
	}

	/**
	 * Settings::addSettings()
	 *
	 * Inserts the theme settings in the array
	 */
	private function addSettings()
	{
		global $context;

		// I need to add a break line because later on I might do silly things
		if (!empty($this->_theme_settings))
			$context['theme_settings'][] = '';

		// Add the setting types
		$context['st_setting_types'] = $this->_setting_types;

		// Insert the new theme settings in the array
		$context['theme_settings'] = array_merge($context['theme_settings'], $this->_theme_settings);
	}

	/**
	 * Settings::removeSettings()
	 *
	 * Remove any unwanted settingss
	 */
	private function removeSettings()
	{
		global $context, $settings;

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

		// Theme Custom Settings
		$this->_theme_settings = [
			[
				'id' => 'st_disable_fa_icons',
				'label' => $txt['st_disable_fa_icons'],
				'description' => $txt['st_disable_fa_icons_desc'],
				'type' => 'checkbox',
			],
			[
				'id' => 'st_disable_theme_effects',
				'label' => $txt['st_disable_theme_effects'],
				'description' => $txt['st_disable_theme_effects_desc'],
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
				'description' => $txt['st_rss_url_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
		];

		// Add compatibility for the 'disable icon' setting
		if (!isset($settings['disable_menu_icons']))
			$this->_theme_settings[] = [
				'id' => 'st_disable_menu_icons',
				'label' => $txt['st_disable_menu_icons'],
				'type' => 'checkbox',
			];
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