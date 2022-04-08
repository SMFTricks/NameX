<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Settings;

class Styles
{
	/**
	 * @var array The settings that affect or add styles to the theme.
	 * Use the name of the setting for each element
	 */
	private $_style_settings;

	/**
	 * @var string The inline CSS output
	 */
	private $_css;

	/**
	 * Style::__construct()
	 *
	 * Load each setting function
	 * 
	 * @return void
	 */
	public function __construct()
	{
		global $settings;

		// Set the css to empty
		$this->_css = '';

		// Load the settings
		$this->style_settings();

		// Fire up the function if the setting is set or enabled
		foreach ($this->_style_settings as $style_setting => $style_function)
			if (!empty($settings[$style_setting]))
				$this->_css .= (empty($style_function) ? $this->$style_setting($settings[$style_setting]) : call_user_func($style_function));
	}

	/**
	 * Style::style_settings()
	 * 
	 * Build the style settings array
	 * 
	 * @return void
	 */
	public function style_settings() : void
	{
		// Settings
		$this->_style_settings = [
			'st_custom_width' => false,
		];
		call_integration_hook('integrate_customtheme_style_settings', array(&$this->_style_settings));
	}

	/**
	 * Style::addCss()
	 * 
	 * Output the inline CSS to the theme
	 * 
	 * @return void
	 */
	public function addCss() : void
	{
		addInlineCss($this->_css);
	}

	/**
	 * Style::st_custom_width()
	 *
	 * It adjusts the forum width to match the setting
	 * Thanks to Sycho for the idea from his Forum Width Mod
	 * https://custom.simplemachines.org/index.php?mod=4223
	 * 
	 * @param string $setting The setting to use
	 * @return string The CSS output
	 */
	public function st_custom_width($setting) : string
	{
		// Adjust the max-width accorrdinly
		return '
			#top_section .inner_wrap, #wrapper, #header, footer .inner_wrap, #nav_wrapper
			{
				max-width: ' . $setting. ';
				width: unset;
			}
			@media screen and (max-width: 991px)
			{
				#top_section .inner_wrap, #wrapper, #header, footer .inner_wrap, #nav_wrapper
				{
					max-width: 95%;
					width: 100%;
				}
			}';
	}
}