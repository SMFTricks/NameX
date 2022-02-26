<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms;

use ThemeCustoms\Color\Variants;
use ThemeCustoms\Color\DarkMode;

if (!defined('SMF'))
	die('No direct access...');

class Theme
{
	/**
	 * @var string The theme version
	 */
	public $_theme_version = '1.0';

	/**
	 * @var string The theme name
	 */
	public $_theme_name = 'NameX';

	/**
	 * @var array The theme author and the SMF id's
	 */
	public $_theme_author = ['Diego Andrés', 254071];

	/**
	 * @var int The theme's site id
	 */
	public $_smf_site_id = 0;

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
		'customs'
	];

	/**
	 * @var object The theme color variants
	 */
	public $_theme_variants;

	/**
	 * @var object Enable dark/light mode
	 */
	public $_theme_darkmode;

	/**
	 * @var object Inline CSS styles
	 */
	public $_css_inline;

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

		// Add inline styles for any setting that requires it
		$this->_css_inline = new Styles;

		// Add any additional hooks for the boards or topics
		$this->hookBoard();

		// Theme Variants
		$this->_theme_variants = new Variants;

		// Theme Modes
		$this->_theme_darkmode = new DarkMode;
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

		// The actual version of the theme
		$settings['theme_real_version'] = $this->_theme_version;

		// The theme name
		$settings['theme_real_name'] = $this->_theme_name;

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
			// Passion One Font
			'notosansfont' => [
				'include' => empty($context['header_logo_url_html_safe']),
				'css' => [
					'file' => 'https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap',
					'external' => true,
				]
			],
			// Lato Font
			'latofont' => [
				'css' => [
					'file' => 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap',
					'external' => true,
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
						(!empty($options['css']['file']) ? (!empty($options['css']['external']) ? $options['css']['file'] : ($options['css']['file'] . (!empty($options['css']['minified']) ? '.min' : '') . '.css')) : ($file . (!empty($options['css']['minified']) ? '.min' : '') . '.css')),
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
					(!is_array($options) ? $options : $file) . '.css',
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

		// Now add the theme js files
		if (!empty($this->_js_files))
		{
			foreach ($this->_js_files as $file => $options)
			{
				loadJavaScriptFile(
					(!is_array($options) ? $options : $file) . '.js',
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
	public function unspeakable(&$buffer)
	{
		global $settings;

		// Do not remove the copyright without permission!
		if (!isset($settings['theme_remove_copyright']) || empty($settings['theme_remove_copyright']))
			$buffer = preg_replace(
				'~(<li class="smf_copyright">)~',
				'<li>Theme by <a href="https://smftricks.com">SMF Tricks</a></li>' . "$1 ",
				$buffer
			);
	}
}