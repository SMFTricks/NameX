<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

if (!defined('SMF'))
	die('No direct access...');

class Changer
{
	/**
	 * @var array The color changer master setting
	 */
	private $_color_changer = true;

	/**
	 * @var array The color changer options
	 */
	private $_color_changes = [];

	/**
	 * @var array The color palettes
	 */
	private $_color_palettes = [];

	/**
	 * @var array Root selectors
	 */
	private $_root_selectors = [
		'[data-colormode="dark"]',
	];

	/**
	 * Changer::__construct()
	 * 
	 * Initializes the theme color changer related features
	 * 
	 * @return void
	 */
	public function __construct()
	{
		// Check if color changer is enabled or if theme has any variants or if dark mode is enabled
		if (empty($this->_color_changer) || !empty($GLOBALS['settings']['theme_variants']) || !empty($GLOBALS['context']['theme_variant']))
			return;

		// Load the Color Changer language file
		loadLanguage('ColorChanger');

		// Add the color changes. I was able to insert the color_changes using current_action hook.
		add_integration_function('integrate_current_action', __CLASS__ . '::colorChanges#', false, '$themedir/themecustoms/Color/Changer.php');

		// Add the settings for the color changer
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $GLOBALS['settings']['theme_id'])
			add_integration_function('integrate_theme_settings', __CLASS__ . '::settings#', false, '$themedir/themecustoms/Color/Changer.php');

		// Set the color changes
		$this->setChanges();

		// Add the palettes
		$this->palettes();

		// Add the CSS
		$this->changerCSS();

		// Add the color changer js file
		$this->changerJS();

		// Load the color changer JS vars
		$this->addJavaScriptVars();
	}

	/**
	 * Changer::setChanges()
	 * 
	 * Sets the color changes
	 * 
	 * @return void
	 */
	private function setChanges()
	{
		$this->_color_changes = [
			'background' => [
				['variable' => 'body-bg']
			],
			'primary_color' => [
				['variable' => 'catbg-bg']
			],
		];
	}

	/**
	 * Changer::colorChanges()
	 * 
	 * Adds the color changes array to the theme
	 * 
	 * @return void
	 */

	public function colorChanges()
	{
		global $settings;

		// Add the color changes
		$settings['color_changes'] = $this->_color_changes;

		// Color palettes
		$settings['color_palettes'] = $this->_color_palettes;

		// Add additional root selectors for higher specificity
		// Usually this is how the dark mode works so this won't change here, probably.
		if (!empty($this->_root_selectors))
			$settings['color_changes_root'] = $this->_root_selectors;
	}

	/**
	 * Changer::settings()
	 * 
	 * Adds the color changer settings to the theme
	 * 
	 * @return void
	 */
	public function settings()
	{
		global $context, $txt;

		// Setting type
		if (!empty($context['st_themecustoms_setting_types']))
		{
			// Add the color setting type
			array_push($context['st_themecustoms_setting_types'], 'color');
			// Don't duplicate it if it's already there
			$context['st_themecustoms_setting_types'] = array_unique($context['st_themecustoms_setting_types']);
		}

		// Add the color changes to the settings
		if (!empty($this->_color_changes))
		{
			// Admin only?
			$context['theme_settings'][] = [
				'section_title' => $txt['cc_color_changer'] . (!empty($this->_color_palettes) ? ' <a onclick="return applyColorPalette(\'default\')" id="cc_reset_all">[' . $txt['cc_reset_all'] . ']</a>' : ''),
				'id' => 'cc_admin_only',
				'label' => $txt['cc_admin_only'],
				'description' => $txt['cc_admin_only_help'],
				'theme_type' => 'color',
			];

			// Remove Shadows
			$context['theme_settings'][] = [
				'id' => 'cc_remove_shadows',
				'label' => $txt['cc_remove_shadows'],
				'theme_type' => 'color',
			];

			// Add the color changes as settings
			foreach ($this->_color_changes as $cc_color => $color)
			{
				$context['theme_settings'][] = [
					'id' => 'cc_' . $cc_color,
					'label' => $txt['cc_' . $cc_color],
					'description' => $this->control($cc_color),
					'type' => 'text',
					'size' => 35,
					'theme_type' => 'color',
					'data' => 'data-coloris',
				];
			}
		}
	}

	/**
	 * Changer::control()
	 * 
	 * Set the description based on the palette
	 * 
	 * @param string The possible color id
	 * @return string The description
	 */
	private function control($color_id)
	{
		global $txt;

		// Check if the color is in the palette
		if (!isset($this->_color_palettes['default'][$color_id]))
			return;

		// Return the description
		return '
			<a onclick="$(\'#cc_' . $color_id . '\').attr(\'type\', \'text\').val(\'' . $this->_color_palettes['default'][$color_id] . '\')">
				' . $txt['cc_default_color'] . '
			</a>';
	}

	/**
	 * Changer::changerCSS()
	 * 
	 * Adds the color changer CSS
	 * 
	 * @return void
	 */
	private function changerCSS()
	{
		loadCSSFile('coloris.css', ['minimize' => true], 'smf_coloris');
	}

	/**
	 * Changer::changerJS()
	 * 
	 * Adds the color changer JS files
	 * 
	 * @return void
	 */
	private function changerJS()
	{
		// Load the color changer js
		loadJavaScriptFile('ColorChanger.js', ['minimize' => true, 'defer' => true], 'smf_color_changer');

		// Coloris colorpicker
		loadJavascriptFile('coloris.js', ['minimize' => true, 'defer' => true], 'smftheme_js_coloris');
	}

	/**
	 * Changer::addJavaScriptVars()
	 * 
	 * Adds the color changer JS vars
	 * 
	 * @return void
	 */
	private function addJavaScriptVars()
	{
		global $txt;

		// Check for palettes first...
		if (empty($this->_color_palettes))
			return;

		// Add the vars
		addJavaScriptVar('color_palettes', json_encode($this->_color_palettes));
		addJavaScriptVar('txt_cc_palettes', '\'' . $txt['cc_palettes_title'] . '\'');
	}

	/**
	 * Changer::palettes()
	 * 
	 * Set up some color palettes
	 * 
	 * @return void
	 */
	private function palettes()
	{
		// The default palette
		$this->_color_palettes['default'] = [
			'background' => 'hsl(var(--primary-color-hue), 35%, 80%)',
			'primary_color' => 'hsl(var(--primary-color-hue), 25%, 45%)',
		];
	}
}