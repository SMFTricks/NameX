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

class Integration
{
	/**
	 * @var object The theme main file
	 */
	protected $_theme;

	/**
	 * @var object The theme config file
	 */
	protected $_config;

	/**
	 * Integration::initialize()
	 *
	 * initiallize the custom theme configuration
	 */
	public function initialize()
	{
		// Autoload
		spl_autoload_register(__CLASS__ . '::autoload');

		// Load Theme Strings
		loadLanguage('ThemeStrings/');

		// Theme Settings
		$this->loadSettings();

		// Theme Init
		$this->_theme = new Theme;

		// Custom Theme Config
		$this->_config = new Init;

		// Main hooks
		$this->loadHooks();
	}

	/**
	 * Integration::loadSettings()
	 * 
	 * Load the main theme settings using hooks
	 * 
	 * @return void
	 */
	private function loadSettings()
	{
		global $settings;

		// Are we viewing this theme?
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != $settings['theme_id'])
			return;

		// Load the theme settings
		add_integration_function('integrate_theme_settings', __NAMESPACE__ . '\Settings::themeSettings#', false, '$themedir/themecustoms/Settings.php');
	}

	/**
	 * Integration::autoload()
	 *
	 * Autoloader using SMF function, with theme_dir
	 * @param string $class The fully-qualified class name.
	 */
	protected function autoload($class)
	{
		global $settings;

		$classMap = array(
			'ThemeCustoms\\' => 'themecustoms/',
		);
		call_integration_hook('integrate_customtheme_autoload', array(&$classMap));
	
		foreach ($classMap as $prefix => $dirName)
		{
			// does the class use the namespace prefix?
			$len = strlen($prefix);
			if (strncmp($prefix, $class, $len) !== 0)
				continue;
	
			$relativeClass = substr($class, $len);
			$fileName = $dirName . strtr($relativeClass, '\\', '/') . '.php';
	
			// if the file exists, require it
			if (file_exists($fileName = $settings['theme_dir'] . '/' . $fileName))
			{
				require_once $fileName;
				return;
			}
		}
	}

	/**
	 * Integration::loadHooks()
	 *
	 * Load hooks quietly
	 * @return void
	 */
	private function loadHooks()
	{
		$hooks = [
			'menu_buttons' => 'main_menu',
			'current_action' => 'disable_icons',
			'actions' => 'hookActions',
			'buffer' => 'hookBuffer#',
			'theme_context' => 'htmlAttributes#',
		];
		foreach ($hooks as $point => $callable)
			add_integration_function('integrate_' . $point, __CLASS__ . '::' . $callable, false,  '$themedir/themecustoms/Integration.php');
	}

	/**
	 * Integration::hookActions()
	 *
	 * Insert any additional hooks needed in very specific cases
	 * @param array $actions An array containing all possible SMF actions. This includes loading different hooks for certain areas.
	 * @return void
	 */
	public static function hookActions()
	{
		// Let the action do some work
		if (isset($_REQUEST['action']))
		{
			switch ($_REQUEST['action'])
			{
				// Credits page
				case 'credits': 
					add_integration_function('integrate_credits', __CLASS__ . '::credits#', false,  '$themedir/themecustoms/Integration.php');
					break;
			}
		}
	}

	/**
	 * Integration::main_menu()
	 *
	 * Add or change menu buttons
	 * @param array $buttons
	 * @return void
	 */
	public static function main_menu(&$buttons)
	{
		global $txt, $scripturl, $settings;

		// Add the theme settings to the admin button
		$current_theme = [
			'title' => $txt['current_theme'],
			'href' => $scripturl . '?action=admin;area=theme;sa=list;th=' . $settings['theme_id'],
			'show' => allowedTo('admin_forum'),
		];
		$buttons['admin']['sub_buttons'] = array_merge([$current_theme], $buttons['admin']['sub_buttons']);
	}

	/**
	 * Integration::disable_icons()
	 *
	 * Hook our menu icons setting for enabling/disabling.
	 * It's done just in case users haven't updated their forums to the final version 
	 * Or for whatever reason they are missing the setting.
	 * This includes a dirty fix for the home button whenever light portal is installed.
	 * 
	 * @return void
	 */
	public static function disable_icons()
	{
		global $context, $settings, $txt;

		// Disable menu icons?
		$current_menu = $context['menu_buttons'];
		foreach ($context['menu_buttons'] as $key => $button)
		{
			$current_menu[$key]['icon'] = (isset($settings['st_disable_menu_icons']) && !empty($settings['st_disable_menu_icons']) ? '' : themecustoms_icon('fa fa-' . (isset($txt['lp_forum']) && $key == 'home' ? 'forum' : $key)));
		}
		$context['menu_buttons'] = $current_menu;
	}

	/**
	 * Integration::credits()
	 *
	 * Add a little surprise to the credits page
	 * @return void
	 */
	public function credits()
	{
		global $context;

		// Theme copyright
		$copyright = true;

		// Lelelelele?
		$context['copyrights']['mods'][] = $this->_theme->unspeakable($copyright, true);
	}

	/**
	 * Integration::hookBuffer()
	 *
	 * Do some black magic with the buffer hook
	 * @param string $buffer The current content
	 * @return string $buffer The changed content
	 */
	public function hookBuffer($buffer)
	{
		// Do unspeakable things to the footer
		$this->_theme->unspeakable($buffer);

		// Return the buffer
		return $buffer;
	}

	/**
	 * Integration::htmlAttributes()
	 *
	 * Add the global data attributes
	 * 
	 * @return void
	 */
	public function htmlAttributes()
	{
		global $settings;

		// Data attributes
		$settings['themecustoms_html_attributes_data'] = (!empty($settings['themecustoms_html_attributes']['data']) && is_array($settings['themecustoms_html_attributes']['data']) ? ' ' . implode(' ', $settings['themecustoms_html_attributes']['data']) : '');
	}
}