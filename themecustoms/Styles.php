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

class Styles
{
	/**
	 * @var array The settings that affect or add styles to the theme.
	 * Add or remove the name of the function to add or remove the style.
	 * Use the name of the setting for each element
	 */
	private $_style_settings = [
		'st_custom_width',
	];

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

		// Fire up the function if the setting is set or enabled
		foreach ($this->_style_settings as $style_setting)
		{
			if (!empty($settings[$style_setting]))
				$this->_css .= $this->$style_setting();
		}

		// Add the css to the theme
		addInlineCss($this->_css);
	}

	/**
	 * Style::st_custom_width()
	 *
	 * It adjusts the forum width to match the setting
	 * Thanks to Sycho for the idea from his Forum Width Mod
	 * https://custom.simplemachines.org/index.php?mod=4223
	 * 
	 * @return void
	 */
	private function st_custom_width()
	{
		global $settings;

		// Adjust the max-width accorrdinly
		return '
			#top_section .inner_wrap, #wrapper, #header, footer .inner_wrap, #nav_wrapper
			{
				max-width: ' . $settings['st_custom_width'] . ';
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