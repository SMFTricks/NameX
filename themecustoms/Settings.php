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
	 * @var array Unwanted settings from the default theme (or custom theme even)
	 */
	private $_remove_settings = [
		'site_slogan',
		'enable_news',
		'forum_width',
	];

	/**
	 * @var array Setting types. Will allow to separate the settings if needed
	 * No type means the setting is either a default setting or a main setting of the theme.
	 */
	public $_setting_types = [
		'carousel',
		'color',
		'social',
	];

	/**
	 * Settings::themeSettings()
	 *
	 * The monstrous theme settings array. New settings are added in here.
	 */
	public function themeSettings()
	{
		global $txt;

		// Theme Settings
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

		// Insert the new theme settings in the array
		$context['theme_settings'] = array_merge($context['theme_settings'], $this->_theme_settings);
	}

	/**
	 * Settings::removeSettings()
	 *
	 * Remove any unwanted settings from the array
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
}