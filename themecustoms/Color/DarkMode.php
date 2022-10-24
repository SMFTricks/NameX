<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

class DarkMode
{
	/**
	 * @var bool The dark mode master setting
	 */
	private $_darkmode = false;

	/**
	 * @var int Order position for the dark mode file
	 */
	private $_order_position = 150;

	/**
	 * DarkMode::__construct()
	 *
	 * Initializes the theme dark mode related features
	 * 
	 * @return void
	 */
	public function __construct()
	{
		// Init the dark mode
		$this->initDarkMode();
	}

	/**
	 * DarkMode::initDarkMode()
	 * 
	 * Initializes the theme dark mode
	 * 
	 * @return void
	 */
	private function initDarkMode()
	{
		global $settings;

		// Set the dark mode?
		call_integration_hook('integrate_customtheme_color_darkmode', [&$this->_darkmode]);

		// Add the dark mode to the variables
		$settings['customtheme_darkmode'] = $this->_darkmode;
	}

	/**
	 * DarkMode::themeVar()
	 * 
	 * Add a variable for theme settings to have more control over the dark mode
	 * 
	 * @return void
	 */
	public function themeVar()
	{
		global $settings;

		// Load the dark mode... Again?
		$settings['customtheme_darkmode'] = $this->_darkmode;

		// Dark mode is enabled?
		if (empty($settings['customtheme_darkmode']))
			return;

		// Load dark mode CSS
		$this->darkCSS();

		// Load the javascript
		$this->darkJS();
	}

	/**
	 * DarkMode::setting()
	 *
	 * Inserts the theme settings for the dark mode
	 * 
	 * @return void
	 */
	public function settings()
	{
		global $context, $txt, $settings;

		// Dark mode is enabled?
		if (empty($settings['customtheme_darkmode']))
			return;

		// Setting type
		if (!empty($context['st_themecustoms_setting_types']))
		{
			// Add the color setting type
			array_push($context['st_themecustoms_setting_types'], 'color');
			// Don't duplicate it if it's already there
			$context['st_themecustoms_setting_types'] = array_unique($context['st_themecustoms_setting_types']);
		}

		// Master setting
		$context['theme_settings'][] = [
			'section_title' => $txt['st_dark_mode'],
			'id' => 'st_theme_mode_default',
			'label' => $txt['st_theme_mode_default'],
			'description' => $txt['st_theme_mode_default_desc'],
			'options' => [
				'light' => $txt['st_light_mode'],
				'dark' => $txt['st_dark_mode'],
				'auto' => $txt['st_auto_mode'],
			],
			'type' => 'list',
			'default' => 'light',
			'theme_type' => 'color',
		];

		// Allow users to select mode?
		$context['theme_settings'][] = [
			'id' => 'st_enable_dark_mode',
			'label' => $txt['st_enable_dark_mode'],
			'type' => 'checkbox',
			'theme_type' => 'color',
		];
	}

	/**
	 * DarkMode::userOptions()
	 *
	 * Adds the mode selection to the theme options
	 *
	 * @return void
	 */
	public function userOptions()
	{
		global $context, $txt, $settings;

		// Dark mode is enabled?
		if (empty($settings['customtheme_darkmode']))
			return;

		// Insert the theme options
		$context['theme_options'] = array_merge(
			[
				$txt['st_theme_mode'],
				[
					'id' => 'st_theme_mode',
					'label' => $txt['st_theme_mode_select'],
					'options' => [
						'light' => $txt['st_light_mode'],
						'dark' => $txt['st_dark_mode'],
						'auto' => $txt['st_auto_mode'],
					],
					'default' => isset($settings['st_theme_mode_default']) && !empty($settings['st_theme_mode_default']) ? $settings['st_theme_mode_default'] : 'light',
					'enabled' => !empty($settings['st_enable_dark_mode']),
				],
			],
			$context['theme_options']
		);
	}

	/**
	 * DarkMode::darkCSS()
	 *
	 * Loads the dark CSS.
	 *
	 * @return void
	 */
	private function darkCSS()
	{
		global $settings, $options;

		// Do we need dark mode?
		if (empty($settings['st_enable_dark_mode']) && (!isset($settings['st_theme_mode_default']) ||$settings['st_theme_mode_default'] === 'light'))
			return;

		// Add the HTML data attribute for color mode
		$settings['themecustoms_html_attributes']['data']['darkmode'] = (!empty($settings['st_enable_dark_mode']) ? ('data-colormode="' . (isset($options['st_theme_mode']) && $options['st_theme_mode'] === 'dark' ? 'dark' : 'light') . '"') : 'data-colormode="dark"');

		// Load the dark CSS
		loadCSSFile('custom/dark.css', ['order_pos' => $this->_order_position], 'smf_darkmode');
	}

	/**
	 * DarkMode::darkJS()
	 *
	 * Loads the dark mode JS.
	 *
	 * @return void
	 */
	private function darkJS()
	{
		global $options, $settings;

		// Do we need dark mode?
		if (empty($settings['st_enable_dark_mode']) && (!isset($settings['st_theme_mode_default']) ||$settings['st_theme_mode_default'] === 'light'))
			return;

		// Theme Mode
		addJavaScriptVar('smf_darkmode', '\'' . (isset($options['st_theme_mode']) && !empty($settings['st_enable_dark_mode']) ? $options['st_theme_mode'] : $settings['st_theme_mode_default']) . '\'');

		// Load the javascript file
		loadJavascriptFile(
			'custom/dark.js',
			[
				'minimize' => true,
				'defer' => true,
				'async' => true,
			],
			'smftheme_js_darkmode'
		);
	}
}