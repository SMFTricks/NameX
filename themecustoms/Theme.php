<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms;

if (!defined('SMF'))
	die('No direct access...');

class Theme
{
	/**
	 * @var bool Enable avatars on topic list
	 */
	private $_avatars_on_indexes = true;

	/**
	 * @var bool Enable avatars on boardindex
	 */
	private $_avatars_on_boardIndex = true;

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
		'theme_colors' => [
			'order_pos' => 100,
		],
		'app',
		'icons'
	];

	/**
	 * @var array The theme custom js files
	 */
	private $_js_files = [
		'main'
	];

	/**
	 * Theme::__construct()
	 *
	 * Load the theme essentials
	 */
	public function __construct()
	{
		// Main Settings
		$this->startSettings();

		// Include any libraries or frameworks
		$this->libOptions();

		// Load any custom templates
		$this->loadTemplates();

		// Load the CSS
		$this->addCSS();

		// Load the JS
		$this->addJS();

		// Theme JS Vars
		$this->addJavaScriptVars();

		// Add any additional hooks for the boards or topics
		$this->hookBoard();

		// Theme Variants
		$this->theme_variants();

		// Theme Modes
		$this->theme_darkmode();

		// Color Changer
		$this->color_changer();
	}

	/**
	 * Theme::startSettings()
	 *
	 * Start some of the minimal and default settings
	 * 
	 * @return void
	 */
	private function startSettings()
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
			$settings['disable_files'] = [];

		// Add any custom attribute to the html tag
		// This is useful for using along the variants, dark mode, etc.
		$settings['themecustoms_html_attributes'] = [];

		// Add inline styles for any setting that requires it
		add_integration_function('integrate_pre_css_output', __NAMESPACE__ . '\Styles::addCss#', false, '$themedir/themecustoms/Styles.php');
	}

	/**
	 * Theme::libOptions()
	 *
	 * Load the default libraries and frameworks.
	 * Some can be disabled with theme settings.
	 * 
	 * @return void
	 */
	private function libOptions()
	{
		global $settings;

		$this->_lib_options = [
			// FontAwesome
			'fontawesome' => [
				'css' => [
					'file' => 'https://use.fontawesome.com/releases/v6.0.0/css/all.css',
					'external' => true,
					'attributes' => [
						'integrity' => 'sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS',
						'crossorigin' => 'anonymous',
					],
				],
			],
			// Bootstrap
			'bootstrap' => [
				'include' => isset(Init::$_settings['include_bootstrap']) && !empty(Init::$_settings['include_bootstrap']),
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
			// jQuery UI
			'jqueryui' => [
				'js' => [
					'file' => 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
					'external' => true,
					'defer' => true,
				]
			],
		];
	}

	/**
	 * Theme::loadTemplates()
	 *
	 * Load the custom templates
	 * 
	 * @return void
	 */
	private function loadTemplates()
	{
		global $context, $board, $topic;

		// Load templates depending on our current action
		if (!empty($context['current_action']))
		{
			switch ($context['current_action'])
			{
				case 'forum':
					loadTemplate('themecustoms/templates/board');
					break;
				default:
					break;
			}
		}
		// Board
		elseif(empty($topic))
			loadTemplate('themecustoms/templates/board');
		// Topic
		// elseif(!empty($topic) && !empty($board))
	}

	/**
	 * Theme::hookBoard()
	 *
	 * Load additional hooks involving board or topic view
	 * 
	 * @return void
	 */
	public function hookBoard()
	{
		global $board, $topic;

		// Topic View
		if (!empty($topic))
		{
			add_integration_function('integrate_display_buttons', __NAMESPACE__ . '\Buttons::normalButtons', false);
			add_integration_function('integrate_prepare_display_context', __NAMESPACE__ . '\Buttons::quickButtons', false);
		}
		// Topic List View
		elseif (!empty($board) && empty($topic))
		{
			add_integration_function('integrate_messageindex_buttons', __NAMESPACE__ . '\Buttons::normalButtons', false);
		}
	}

	/**
	 * Theme::addCSS()
	 *
	 * Add the theme css files
	 * 
	 * @return void
	 */
	private function addCSS()
	{
		// Add the css libraries first
		if (!empty($this->_lib_options))
		{
			foreach ($this->_lib_options as $file => $options)
			{
				if ((!empty($options['include']) || !isset($options['include'])) && !empty($options['css']))
				{
					loadCSSFile(
						(!empty($options['css']['file']) ? (!empty($options['css']['external']) ? $options['css']['file'] : ('custom/' . $options['css']['file'] . (!empty($options['css']['minified']) ? '.min' : '') . '.css')) : ('custom/' . $file . (!empty($options['css']['minified']) ? '.min' : '') . '.css')),
						[
							'minimize'  => !empty($options['css']['minimize']),
							'external' => !empty($options['css']['external']),
							'attributes' => !empty($options['css']['attributes']) ? $options['css']['attributes'] : [],
							'order_pos' => !empty($options['css']['order_pos']) ? $options['css']['order_pos'] : $this->_css_order++,
						],
						'smftheme_css_' . $file
					);
				}
			}
		}

		// Now add the theme css files
		if (!empty($this->_css_files))
		{
			foreach ($this->_css_files as $file => $options)
			{
				loadCSSFile(
					'custom/' . (!is_array($options) ? $options : $file) . '.css',
					[
						'minimize' => !empty($options['minimize']),
						'attributes' => !empty($options['attributes']) ? $options['attributes'] : [],
						'order_pos' => !empty($options['order_pos']) ? $options['order_pos'] : abs($this->_css_order--),
					],
					'smftheme_css_' . (!is_array($options) ? $options : $file)
				);
			}
		}
	}

	/**
	 * Theme::addJS()
	 *
	 * Add the theme js files
	 * 
	 * @return void
	 */
	private function addJS()
	{
		// Add the js libraries first
		if (!empty($this->_lib_options))
		{
			foreach ($this->_lib_options as $file => $options)
			{
				if ((!empty($options['include']) || !isset($options['include'])) && !empty($options['js']))
					loadJavaScriptFile(
						(!empty($options['js']['file']) ? (!empty($options['js']['external']) ? $options['js']['file'] : ('custom/' . $options['js']['file'] . (!empty($options['js']['minified']) ? '.min' : '') . '.js')) : ('custom/' . $file . (!empty($options['js']['minified']) ? '.min' : '') . '.js')),
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

		// Now add the theme js files
		if (!empty($this->_js_files))
		{
			foreach ($this->_js_files as $file => $options)
			{
				loadJavaScriptFile(
					'custom/' . (!is_array($options) ? $options : $file) . '.js',
					[
						'defer'  =>  !empty($options['defer']),
						'async'  =>  !empty($options['async']),
						'minimize'  =>  !empty($options['minimize']),
					],
					'smftheme_js_' . (!is_array($options) ? $options : $file)
				);
			}
		}
	}

	/**
	 * Theme::addJSVars()
	 *
	 * Add the theme js variables
	 * 
	 * @return void
	 */
	private function addJavaScriptVars()
	{
		global $settings;

		// Theme ID
		addJavaScriptVar('smf_theme_id', $settings['theme_id']);

		// News Fader
		addJavaScriptVar('smf_newsfader_time', !empty($settings['newsfader_time']) ? $settings['newsfader_time'] : 5000);
	}

	/**
	 * Theme::unspeakable()
	 *
	 * It does nasty things to the theme footer
	 * 
	 * @return string Surprise!
	 */
	public function unspeakable(&$buffer, $return = false)
	{
		global $settings;

		// Do not remove the copyright without permission!
		$ST = 'Theme by <a href="https://smftricks.com">SMF Tricks</a>';
		// Return it
		if ($return)
			return (!empty($settings['theme_support_information']['theme_link']) ? $settings['theme_support_information']['theme_link'] . ' | ' : '') . $ST;
		// Stick it
		elseif (!isset($settings['theme_remove_copyright']) || empty($settings['theme_remove_copyright']))
			$buffer = preg_replace(
				'~(<li class="smf_copyright">)~',
				'<li>'. $ST . '</li>' . "$1 ",
				$buffer
			);
	}

	/**
	 * Theme::variants()
	 * 
	 * Load the theme variants
	 * 
	 * @return void
	 */
	private function theme_variants()
	{
		global $settings;

		// Variants settings
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
			add_integration_function('integrate_theme_settings', __NAMESPACE__ . '\\Color\Variants::settings#', false, '$themedir/themecustoms/Color/Variants.php');

		// Add the variants to the list of available themes
		add_integration_function('integrate_theme_context', __NAMESPACE__ . '\\Color\Variants::userSelection#', false, '$themedir/themecustoms/Color/Variants.php');

		// Add the theme variants as a theme option too
		add_integration_function('integrate_theme_options', __NAMESPACE__ . '\\Color\Variants::userOptions#', false, '$themedir/themecustoms/Color/Variants.php');
	}

	/**
	 * Theme::theme_darkmode()
	 *
	 * Add the dark mode to the theme
	 * 
	 * @return void
	 */
	private function theme_darkmode()
	{
		global $settings;

		// Insert the variants using the theme settings.
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
			add_integration_function('integrate_theme_settings', __NAMESPACE__ . '\\Color\DarkMode::settings#', false, '$themedir/themecustoms/Color/DarkMode.php');

		// Add the dark mode to the theme variables
		add_integration_function('integrate_theme_context', __NAMESPACE__ . '\\Color\DarkMode::themeVar#', false, '$themedir/themecustoms/Color/DarkMode.php');

		// Add the dark mode as a theme option too
		add_integration_function('integrate_theme_options', __NAMESPACE__ . '\\Color\DarkMode::userOptions#', false, '$themedir/themecustoms/Color/DarkMode.php');
	}

	/**
	 * Theme::color_changer()
	 * 
	 * Add the color changer to the theme
	 * 
	 * @return void
	 */
	private function color_changer()
	{
		global $settings;

		// Add the settings for the color changer
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
			add_integration_function('integrate_theme_settings', __NAMESPACE__ . '\\Color\Changer::settings#', false, '$themedir/themecustoms/Color/Changer.php');

		// Add the color changes
		add_integration_function('integrate_theme_context', __NAMESPACE__ . '\\Color\Changer::colorChanges#', false, '$themedir/themecustoms/Color/Changer.php');
	}
}