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
	 * @var array The color changer options
	 */
	private $_color_changes;

	/**
	 * @var array The color palettes
	 */
	private $_color_palettes = [];

	/**
	 * @var array Root selectors
	 * The exptected dark mode is added by default
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
		// Load the color changes
		$this->setChanges();
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
		global $settings;

		// Hook the color changes
		call_integration_hook('integrate_customtheme_color_changes', [&$this->_color_changes, &$this->_color_palettes, &$this->_root_selectors]);

		$settings['color_changes'] = $this->_color_changes;
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

		// Add the color changes... Again?
		$settings['color_changes'] = $this->_color_changes;

		// Color palettes
		if (!empty($this->_color_palettes))
			$settings['color_palettes'] = $this->_color_palettes;
		
		// Add additional root selectors for higher specificity
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
		global $context, $txt, $settings;

		// Do nothing when there are variants or no color changes
		if (!empty($settings['theme_variants']) || empty($settings['color_changes']))
			return;

		// Load the Color Changer language file
		loadLanguage('ColorChanger');

		// Setting type
		if (!empty($context['st_themecustoms_setting_types']))
		{
			// Add the color setting type
			array_push($context['st_themecustoms_setting_types'], 'color');
			// Don't duplicate it if it's already there
			$context['st_themecustoms_setting_types'] = array_unique($context['st_themecustoms_setting_types']);
		}

		// Add the color changes to the settings
		if (!empty($this->_color_changes) && is_array($this->_color_changes))
		{
			// Admin only?
			$context['theme_settings'][] = [
				'section_title' => $txt['cc_color_changer'] . (!empty($this->_color_palettes['default']) ? ' <a onclick="return applyColorPalette(\'default\')" id="cc_reset_all">[' . $txt['cc_reset_all'] . ']</a>' : ''),
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

		// Add coloris
		$this->coloris();

		// Any color palettes?
		if (!empty($this->_color_palettes))
			$this->changerJS();
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
	 * Changer::coloris()
	 * 
	 * Load Coloris
	 * 
	 * @return void
	 */
	private function coloris()
	{
		// CSS
		loadCSSFile('custom/coloris.css', ['minimize' => true], 'smf_coloris');
		// Javascript
		loadJavascriptFile('custom/coloris.js', ['minimize' => true, 'defer' => true], 'smftheme_js_coloris');
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
		global $txt;

		// Load the color changer js... Sometimes is not loaded
		loadJavaScriptFile('ColorChanger.js', ['minimize' => true, 'defer' => true], 'smf_color_changer');

		// Add the vars
		addJavaScriptVar('color_palettes', json_encode($this->_color_palettes));
		addJavaScriptVar('txt_cc_palettes', '\'' . $txt['cc_palettes_title'] . '\'');
	}
}