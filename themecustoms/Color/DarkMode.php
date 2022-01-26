<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

if (!defined('SMF'))
	die('No direct access...');

class DarkMode
{
	/**
	 * @var array The dark mode setting
	 */
	private $_darkmode = true;

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
		// Check if darkmode is enabled
		if (empty($this->_darkmode))
			return;

		// Insert the variants using the theme settings.
		add_integration_function('integrate_theme_settings', __CLASS__ . '::setting', false);

		// Add the theme variants as a theme option too
		add_integration_function('integrate_theme_options', __CLASS__ . '::userOptions', false);

		// Load dark mode CSSS
		$this->darkCSS();

		// Insert the variants JS vars
		$this->addJavaScriptVars();

		// Load the JS file for the variants
		$this->darkJS();
	}

	/**
	 * DarkMode::setting()
	 *
	 * Inserts the theme settings for the dark mode
	 * 
	 * @return void
	 */
	public static function setting()
	{
		global $context, $txt;

		// Add the setting type
		$context['st_themecustoms_setting_types'][] = 'color';

		// Master setting
		$context['theme_settings'][] = [
			'section_title' => $txt['st_dark_mode'],
			'id' => 'st_theme_mode_default',
			'label' => $txt['st_theme_mode_default'],
			'description' => $txt['st_theme_mode_default_desc'],
			'options' => [
				'light' => $txt['st_light_mode'],
				'dark' => $txt['st_dark_mode'],
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
	protected function darkCSS()
	{
		global $settings, $options;

		// Do we need dark mode?
		if (empty($settings['st_enable_dark_mode']) && (!isset($settings['st_theme_mode_default']) ||$settings['st_theme_mode_default'] !== 'dark'))
			return;

		// Add the HTML data attribute for color mode
		$settings['themecustoms_html_attributes']['data'][] = (!empty($settings['st_enable_dark_mode']) ? ('data-colormode="' . (isset($options['st_theme_mode']) && $options['st_theme_mode'] === 'dark' ? 'dark' : 'light') . '"') : 'data-colormode="dark"');

		// Load the dark CSS
		loadCSSFile('dark.css', ['order_pos' => $this->_order_position], 'smf_darkmode');
	}

	/**
	 * DarkMode::addJavaScriptVars()
	 *
	 * Loads the dark mode JS vars.
	 *
	 * @return void
	 */
	protected function addJavaScriptVars()
	{
		global $options, $settings;

		// Theme Mode
		if (!empty($settings['st_enable_dark_mode']))
			addJavaScriptVar('smf_darkmode', '\'' . (isset($options['st_theme_mode']) && $options['st_theme_mode'] === 'dark' ? 'dark' : 'light') . '\'');
	}

	/**
	 * DarkMode::darkJS()
	 *
	 * Loads the dark mode JS.
	 *
	 * @return void
	 */
	protected function darkJS()
	{
		global $settings;

		// Load the file only if the swtylswitch is enabled and the user can change variants
		if (!empty($settings['st_enable_dark_mode']))
			loadJavascriptFile(
				'dark.js',
				[
					'minimize' => false,
					'defer' => true,
				],
				'smftheme_js_darkmode'
			);
	}
}