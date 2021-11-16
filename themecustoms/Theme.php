<?php

/**
 * @package ST Theme
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms;

if (!defined('SMF'))
	die('No direct access...');

class Theme
{
	/**
	 * @var string The theme version
	 */
	public $_theme_version = '1.0';

	/**
	 * @var array The theme color variants (red, green, blue, etc)
	 */
	private $_theme_variants = [];

	/**
	 * @var array The theme modes (dark, light, etc.)
	 */
	private $_theme_modes = [];

	/**
	 * @var array The libraries or frameworks to load, populated in libOptions()
	 */
	private $_lib_options = [];

	public function __construct()
	{
		// Main Settings
		$this->startSettings();

		// Load Theme Strings
		loadLanguage('ThemeStrings/');

		// Include any libraries or frameworks
		$this->libOptions();

		// Load the CSS
		$this->addCSS();

		// Load the JS
		$this->addJS();

		/** @TODO */
		// Theme Variants

		/** @TODO */
		// Add Theme Settings (hook)
	}

	protected function startSettings()
	{
		global $settings;

		// The version this template/theme is for. This should probably be the version of SMF it was created for.
		$settings['theme_version'] = $this->_theme_version;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last and the first post on the message index and recent pages.
		$settings['avatars_on_indexes'] = true;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last post on the board index.
		$settings['avatars_on_boardIndex'] = true;

		// This defines the formatting for the page indexes used throughout the forum.
		$settings['page_index'] = themecustoms_page_index();

		// Allow css/js files to be disabled for this specific theme.
		// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
		if (!isset($settings['disable_files']))
			$settings['disable_files'] = array();
	}

	public function libOptions()
	{
		$this->_lib_options = [
			// FontAwesome
			'fontawesome' => [
				'include' => true,
				'css' => [
					'file' => 'https://use.fontawesome.com/releases/v5.15.4/css/all.css',
					'external' => true,
					'attributes' => [
						'integrity' => 'sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm',
						'crossorigin' => 'anonymous',
					],
				],
			],
			// Bootstrap
			'bootstrap' => [
				'include' => false,
				'css' => [
					'minified' => true,
				],
				'js' => [
					'file' => 'bootstrap.bundle',
					'minified' => true,
				],
			],
			// Animate
			'animate' => [
				'include' => false,
				'css' => [
					'minified' => true,
				],
			],
		];
	}

	public function addCSS()
	{
		// Add the libraries first
		$order = -200;
		foreach ($this->_lib_options as $file => $options)
		{
			if (!empty($options['include']) && !empty($options['css']))
				loadCSSFile(
					(!empty($options['css']['file']) ? (!empty($options['css']['external']) ? $options['css']['file'] : ($options['css']['file'] . (!empty($options['css']['minified']) ? '.min' : '') . '.css')) : ($file . (!empty($options['css']['minified']) ? '.min' : '') . '.css')),
					[
						'minimize'  => !empty($options['css']['minimize']),
						'external' => !empty($options['css']['external']),
						'attributes' => !empty($options['css']['attributes']) ? $options['css']['attributes'] : [],
						'order_pos' => $order++,
					],
					'smftheme_css_' . $file
				);
		}
	}

	public function addJS()
	{
		// Add the libraries first
		foreach ($this->_lib_options as $file => $options)
		{
			if (!empty($options['include']) && !empty($options['js']))
				loadJavaScriptFile(
					(!empty($options['js']['file']) ? (!empty($options['js']['external']) ? $options['js']['file'] : ($options['js']['file'] . (!empty($options['js']['minified']) ? '.min' : '') . '.js')) : ($file . (!empty($options['js']['minified']) ? '.min' : '') . '.js')),
					[
						'defer'  =>  !empty($options['js']['defer']),
						'async'  =>  !empty($options['js']['async']),
						'minimize'  =>  !empty($options['js']['minimize']),
						'external' => !empty($options['js']['external']),
						'attributes' => !empty($options['js']['attributes']) ? $options['js']['attributes'] : [],
					],
					'smftheme_js_' . $file
				);
		}
	}
}