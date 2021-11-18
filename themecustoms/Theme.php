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

class Theme
{
	/**
	 * @var string The theme version
	 */
	public $_theme_version = '1.0';

	/**
	 * @var bool Enable avatars on topic list
	 */
	private $_avatars_on_indexes = true;

	/**
	 * @var bool Enable avatars on boardindex
	 */
	private $_avatars_on_boardIndex = true;

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

	/**
	 * @var int The initial order for the css files
	 */
	private $_css_order = -200;

	/**
	 * @var array The theme custom css files
	 */
	private $_css_files = [
		'theme_colors',
		'app',
	];

	/**
	 * @var array The theme custom js files
	 */
	private $_js_files = [];

	/**
	 * @var bool Use bootstrap
	 */
	private $_use_bootstrap = false;

	/**
	 * Theme::__construct()
	 *
	 * Load the theme essentials
	 */
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
		// Theme Modes

		// Add Theme Settings
		add_integration_function('integrate_theme_settings', 'ThemeCustoms\Settings::themeSettings#', false);
	}

	/**
	 * Theme::startSettings()
	 *
	 * Start some of the minimal and default settings
	 */
	protected function startSettings()
	{
		global $settings;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last and the first post on the message index and recent pages.
		$settings['avatars_on_indexes'] = $this->_avatars_on_indexes;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last post on the board index.
		$settings['avatars_on_boardIndex'] = $this->_avatars_on_boardIndex;

		// This defines the formatting for the page indexes used throughout the forum.
		$settings['page_index'] = themecustoms_page_index();

		// Allow css/js files to be disabled for this specific theme.
		// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
		if (!isset($settings['disable_files']))
			$settings['disable_files'] = array();
	}

	/**
	 * Theme::libOptions()
	 *
	 * Load the default libraries and frameworks.
	 * Some can be disabled with theme settings.
	 */
	public function libOptions()
	{
		global $settings;

		$this->_lib_options = [
			// FontAwesome
			'fontawesome' => [
				'include' => !isset($settings['st_disable_fa_icons']) || empty($settings['st_disable_fa_icons']),
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
				'include' => $this->_use_bootstrap,
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
				'include' => !isset($settings['st_disable_theme_effects']) || empty($settings['st_disable_theme_effects']),
				'css' => [
					'minified' => true,
				],
			],
		];
	}

	public function addCSS()
	{
		// Add the css libraries first
		if (!empty($this->_lib_options))
			foreach ($this->_lib_options as $file => $options)
				if (!empty($options['include']) && !empty($options['css']))
					loadCSSFile(
						(!empty($options['css']['file']) ? (!empty($options['css']['external']) ? $options['css']['file'] : ($options['css']['file'] . (!empty($options['css']['minified']) ? '.min' : '') . '.css')) : ($file . (!empty($options['css']['minified']) ? '.min' : '') . '.css')),
						[
							'minimize'  => !empty($options['css']['minimize']),
							'external' => !empty($options['css']['external']),
							'attributes' => !empty($options['css']['attributes']) ? $options['css']['attributes'] : [],
							'order_pos' => !empty($options['css']['order_pos']) ? $options['css']['order_pos'] : $this->_css_order++,
						],
						'smftheme_css_' . $file
					);

		// Now add the theme css files
		if (!empty($this->_css_files))
			foreach ($this->_css_files as $file => $options)
				loadCSSFile(
					$file . '.css',
					[
						'minimize' => !empty($options['minimize']),
						'attributes' => !empty($options['attributes']) ? $options['attributes'] : [],
						'order_pos' => !empty($options['order_pos']) ? $options['order_pos'] : abs($this->_css_order++),
					],
					'smftheme_css_' . $file
				);
	}

	public function addJS()
	{
		// Add the js libraries first
		if (!empty($this->_lib_options))
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

		// Now add the theme js files
		if (!empty($this->_js_files))
			foreach ($this->_js_files as $file)
				loadJavaScriptFile(
					$file . '.js',
					[
						'defer'  =>  false,
						'async'  =>  false,
						'minimize'  =>  false,
					],
					'smftheme_js_' . $file
				);
	}
}