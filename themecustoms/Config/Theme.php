<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Config;

use ThemeCustoms\Color\DarkMode;
use ThemeCustoms\Color\Variants;
use ThemeCustoms\Init;

class Theme
{
	/**
	 * @var const Font Awesome version
	 */
	const FONTAWESOME_VERSION = '6.3.0';

	/**
	 * @var const jQuery UI version
	 */
	const JQUERYUI_VERSION = '1.13.2';

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
		'custom_edits',
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

		// Addons?
		$this->addons();

		// Carousel
		$this->theme_carousel();

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
	private function startSettings() : void
	{
		global $settings;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last and the first post on the message index and recent pages.
		$settings['avatars_on_indexes'] = Init::$_avatar_options;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last post on the board index.
		$settings['avatars_on_boardIndex'] = Init::$_avatar_options;

		// Set the following variable to true if this theme wants to display the login and register buttons in the main forum menu.
		$settings['login_main_menu'] = $settings['st_loginlogout_menu'] ?? false;

		// Allow css/js files to be disabled for this specific theme.
		// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
		if (!isset($settings['disable_files']))
			$settings['disable_files'] = [];

		// Add any custom attribute to the html tag
		// This is useful for using along the variants, dark mode, etc.
		$settings['themecustoms_html_attributes'] = [];

		// Define the total amount of custom links to use.
		$settings['st_custom_links_limit'] = Init::$_settings['custom_links_limit'] ?? 0;

		// Add inline styles for any setting that requires it
		add_integration_function('integrate_pre_css_output', 'ThemeCustoms\Settings\Styles::addCss#', false, '$themedir/themecustoms/Settings/Styles.php');
	}

	/**
	 * Theme::libOptions()
	 *
	 * Load the default libraries and frameworks.
	 * Some can be disabled with theme settings.
	 * 
	 * @return void
	 */
	private function libOptions() : void
	{
		global $settings;

		$this->_lib_options = [
			// FontAwesome
			'fontawesome' => [
				'css' => [
					'file' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . Theme::FONTAWESOME_VERSION . '/css/all.min.css',
					'file' => (!empty($settings['st_fontawesome_source']) ? 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . Theme::FONTAWESOME_VERSION . '/css/all.min.css' : 'all'),
					'external' => !empty($settings['st_fontawesome_source']),
					'minified' => empty($settings['st_fontawesome_source']),
				],
			],
			// jQuery UI
			'jqueryui' => [
				'js' => [
					'file' => (!empty($settings['st_jquery_ui_source']) ? 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . Theme::JQUERYUI_VERSION . '/jquery-ui.min.js' : 'jquery-ui'),
					'external' => !empty($settings['st_jquery_ui_source']),
					'defer' => true,
					'minified' => empty($settings['st_jquery_ui_source']),
				]
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
		];
	}

	/**
	 * Theme::loadTemplates()
	 *
	 * Load the custom templates
	 * 
	 * @return void
	 */
	private function loadTemplates() : void
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
				case 'profile':
					loadTemplate('themecustoms/templates/profile');
					break;
				default:
					break;
			}
		}
		// Board
		elseif (empty($topic))
			loadTemplate('themecustoms/templates/board');
	}

	/**
	 * Theme::hookBoard()
	 *
	 * Load additional hooks involving board or topic view
	 * 
	 * @return void
	 */
	public function hookBoard() : void
	{
		global $board, $topic, $context;

		// Topic View
		if (!empty($topic))
		{
			add_integration_function('integrate_display_buttons', 'ThemeCustoms\Integration\Buttons::normalButtons', false, '$themedir/themecustoms/Integration/Buttons.php');
			add_integration_function('integrate_prepare_display_context', 'ThemeCustoms\Integration\Buttons::quickButtons', false, '$themedir/themecustoms/Integration/Buttons.php');
		}
		// Topic List View
		elseif (!empty($board) && empty($topic))
		{
			add_integration_function('integrate_messageindex_buttons', 'ThemeCustoms\Integration\Buttons::normalButtons', false, '$themedir/themecustoms/Integration/Buttons.php');
		}
		// BoardIndex
		elseif ((empty($board) && empty($topic)) || $context['current_action'] == 'forum')
		{
			// Info Center shenanigans
			add_integration_function('integrate_mark_read_button', 'ThemeCustoms\Integration\InfoCenter::avatars#', false, '$themedir/themecustoms/Integration/InfoCenter.php');
		}
	}

	/**
	 * Theme::addons()
	 *
	 * Load the types of addons that are available
	 * Normally, just theme addons, but who knows in the future.
	 * 
	 * @return void
	 */
	private function addons() : void
	{
		add_integration_function('integrate_modification_types', 'ThemeCustoms\Integration\Packages::types#', false, '$themedir/themecustoms/Integration/Packages.php');
		add_integration_function('integrate_packages_sort_id', 'ThemeCustoms\Integration\Packages::sort#', false, '$themedir/themecustoms/Integration/Packages.php');
	}

	/**
	 * Theme::addCSS()
	 *
	 * Add the theme css files
	 * 
	 * @return void
	 */
	private function addCSS() : void
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
							'minimize' => !isset($options['css']['minimize']) ? true : !empty($options['css']['minimize']),
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
						'minimize' => !isset($options['minimize']) ? true : !empty($options['minimize']),
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
	private function addJS() : void
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
							'defer'  => !empty($options['js']['defer']),
							'async'  => !empty($options['js']['async']),
							'minimize'  => !isset($options['js']['minimize']) ? true : !empty($options['js']['minimize']),
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
						'defer' => !empty($options['defer']),
						'async' => !empty($options['async']),
						'minimize' => !isset($options['minimize']) ? true : !empty($options['minimize']),
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
	private function addJavaScriptVars() : void
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
	 * @return void|string Surprise!
	 */
	public function unspeakable(&$buffer, $return = false)
	{
		global $settings;

		// Do not remove the copyright without permission!
		$ST = 'Theme by <a href="https://smftricks.com">SMF Tricks</a>';
		// Return it
		if ($return)
			return (!empty($settings['theme_name']) ? $settings['theme_name'] . ' | ' : '') . $ST;
		// Stick it
		elseif (!isset($settings['theme_remove_copyright']) || empty($settings['theme_remove_copyright']))
			$buffer = preg_replace(
				'~(<li class="smf_copyright">)~',
				'<li>'. $ST . '</li>' . "$1 ",
				$buffer
			);
	}

	/**
	 * Theme:carousel()
	 * 
	 * Load the theme carousel
	 * 
	 * @return void
	 */
	private function theme_carousel() : void
	{
		global $settings;

		// Carousel enabled?
		if (empty(Init::$_settings['addons']['carousel']))
			return;

		// Carousel language file
		loadLanguage('ThemeCustoms/carousel');

		// Carousel template file
		loadTemplate('themecustoms/templates/carousel');

		// Carousel settings
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
			add_integration_function('integrate_theme_settings', 'ThemeCustoms\Settings\Carousel::settings#', false, '$themedir/themecustoms/Settings/Carousel.php');
	}

	/**
	 * Theme::variants()
	 * 
	 * Load the theme variants
	 * 
	 * @return void
	 */
	private function theme_variants() : void
	{
		global $settings, $context, $maintenance, $modSettings, $user_info;

		// Theme Variants enabled?
		if (!isset(Init::$_color_options['variants']) || empty(Init::$_color_options['variants']))
			return;

		// Maintenance Mode or Kicking guests?
		if ((!empty($maintenance) && !allowedTo('admin_forum')) || (empty($modSettings['allow_guestAccess']) && $user_info['is_guest']))
		{
			$context['theme_variant'] = $settings['default_variant'] ?? Init::$_color_options[0];
			$theme_variants = new Variants(false);
			$theme_variants->variantCSS();

			return;
		}

		// Variants settings
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
			add_integration_function('integrate_theme_settings', 'ThemeCustoms\Color\Variants::settings#', false, '$themedir/themecustoms/Color/Variants.php');

		// Add the variants to the list of available themes
		add_integration_function('integrate_theme_context', 'ThemeCustoms\Color\Variants::userSelection#', false, '$themedir/themecustoms/Color/Variants.php');

		// If we are in the admin area, check we are editing the current theme
		if (!empty($context['current_action']) && $context['current_action'] == 'admin' && isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != $settings['theme_id'])
			return;

		// Add the theme variants as a theme option too
		add_integration_function('integrate_theme_options', 'ThemeCustoms\Color\Variants::userOptions#', false, '$themedir/themecustoms/Color/Variants.php');

		// Style sceditor cuz it's dumb and stupid
		add_integration_function('integrate_sceditor_options', 'ThemeCustoms\Color\Variants::sceditor#', false, '$themedir/themecustoms/Color/Variants.php');
	}

	/**
	 * Theme::theme_darkmode()
	 *
	 * Add the dark mode to the theme
	 * 
	 * @return void
	 */
	private function theme_darkmode() : void
	{
		global $settings, $context, $maintenance, $modSettings, $user_info, $options;

		// Theme Dark Mode enabled?
		if (!isset(Init::$_color_options['darkmode']) || empty(Init::$_color_options['darkmode']))
			return;

		// Maintenance Mode or Kicking guests?
		if ((!empty($maintenance) && !allowedTo('admin_forum')) || (empty($modSettings['allow_guestAccess']) && $user_info['is_guest']))
		{
			$settings['st_enable_dark_mode'] = false;
			$theme_darkmode = new DarkMode(false);
			$theme_darkmode->darkCSS();

			return;
		}

		// Insert the variants using the theme settings.
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
			add_integration_function('integrate_theme_settings', 'ThemeCustoms\Color\DarkMode::settings#', false, '$themedir/themecustoms/Color/DarkMode.php');

		// Add the dark mode to the theme variables
		add_integration_function('integrate_theme_context', 'ThemeCustoms\Color\DarkMode::themeVar#', false, '$themedir/themecustoms/Color/DarkMode.php');

		// If we are in the admin area, check we are editing the current theme
		if (!empty($context['current_action']) && $context['current_action'] == 'admin' && isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != $settings['theme_id'])
			return;

		// Add the dark mode as a theme option too
		add_integration_function('integrate_theme_options', 'ThemeCustoms\Color\DarkMode::userOptions#', false, '$themedir/themecustoms/Color/DarkMode.php');

		// Style sceditor cuz it's dumb and stupid
		add_integration_function('integrate_sceditor_options', 'ThemeCustoms\Color\DarkMode::sceditor#', false, '$themedir/themecustoms/Color/DarkMode.php');
	}

	/**
	 * Theme::color_changer()
	 * 
	 * Add the color changer to the theme
	 * 
	 * @return void
	 */
	private function color_changer() : void
	{
		global $settings, $maintenance, $modSettings, $user_info;

		// Maintenance Mode or Kicking guests?
		if ((!empty($maintenance) && !allowedTo('admin_forum')) || (empty($modSettings['allow_guestAccess']) && $user_info['is_guest']))
			return;

		// Theme Color Changer enabled?
		if (!isset(Init::$_color_options['colorchanger']) || empty(Init::$_color_options['colorchanger']))
			return;

		// Add the settings for the color changer
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
			add_integration_function('integrate_theme_settings', 'ThemeCustoms\Color\Changer::settings#', false, '$themedir/themecustoms/Color/Changer.php');

		// Add the color changes
		add_integration_function('integrate_theme_context', 'ThemeCustoms\Color\Changer::colorChanges#', false, '$themedir/themecustoms/Color/Changer.php');
	}
}