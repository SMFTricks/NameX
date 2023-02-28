<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Config;

use ThemeCustoms\Init;

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
	public function initialize() : void
	{
		// Autoload
		spl_autoload_register(__CLASS__ . '::autoload');

		// Load Theme Strings
		loadLanguage('ThemeCustoms/main');

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
	private function loadSettings() : void
	{
		global $settings;

		// Are we viewing this theme?
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != $settings['theme_id'])
			return;

		// Load the theme settings
		add_integration_function('integrate_theme_settings', 'ThemeCustoms\Settings\Main::settings#', false, '$themedir/themecustoms/Settings/Main.php');
	}

	/**
	 * Integration::autoload()
	 *
	 * Autoloader using SMF function, with theme_dir
	 * @param string $class The fully-qualified class name.
	 */
	protected function autoload($class) : void
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
	 * Load the main hooks
	 * @return void
	 */
	private function loadHooks() : void
	{
		$hooks = [
			'menu_buttons' => 'main_menu',
			'current_action' => 'strip_menu#',
			'actions' => 'hookActions',
			'buffer' => 'hookBuffer#',
			'theme_context' => 'htmlAttributes#',
		];
		foreach ($hooks as $point => $callable)
			add_integration_function('integrate_' . $point, __CLASS__ . '::' . $callable, false,  '$themedir/themecustoms/Config/Integration.php');
	}

	/**
	 * Integration::hookActions()
	 *
	 * Insert any additional hooks needed in very specific cases
	 * @param array $actions An array containing all possible SMF actions. This includes loading different hooks for certain areas.
	 * @return void
	 */
	public static function hookActions() : void
	{
		// Let the action do some work
		if (isset($_REQUEST['action']))
		{
			switch ($_REQUEST['action'])
			{
				// Credits page
				case 'credits': 
					add_integration_function('integrate_credits', __CLASS__ . '::credits#', false,  '$themedir/themecustoms/Config/Integration.php');
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
	public static function main_menu(&$buttons) : void
	{
		global $txt, $scripturl, $settings;

		// Add the theme settings to the admin button
		$current_theme = [
			'title' => $txt['current_theme'],
			'href' => $scripturl . '?action=admin;area=theme;sa=list;th=' . $settings['theme_id'],
			'show' => allowedTo('admin_forum'),
		];
		$buttons['admin']['sub_buttons'] = array_merge([$current_theme], $buttons['admin']['sub_buttons']);

		// Add the community button if it's enabled.
		$temp_buttons = [];
		foreach ($buttons as $k => $v)
		{
			$temp_buttons[$k] = $v;
			if ($k == 'home')
			{
				$temp_buttons['community'] = [
					'title' => $txt['st_community'],
					'href' => $scripturl . (!empty($settings['st_community_forum']) ? '?action=forum' : ''),
					'icon' => 'members',
					'show' => !empty($settings['st_enable_community']),
				];
			}
		}
		$buttons = $temp_buttons;
	}

	/**
	 * Integration::strip_menu()
	 *
	 * Hook our menu icons setting for enabling/disabling.
	 * Will also remove buttons using the provided setting.
	 * This includes some additional checks for portal mods.
	 * 
	 * @return void
	 */
	public function strip_menu(&$current_action) : void
	{
		global $context, $settings, $txt;

		// Check for Ultimate Menu doing witchcraft?
		if (!empty($current_action) && $current_action == 'admin' && isset($_REQUEST['area']) && $_REQUEST['area'] == 'umen')
			return;

		// Remove elements?
		$remove = !empty($settings['st_remove_items']) ? explode(',', $settings['st_remove_items']) : [];

		$current_menu = $context['menu_buttons'];
		foreach ($context['menu_buttons'] as $key => $button)
		{
			// Disable menu icons?
			$current_menu[$key]['icon'] = (isset($settings['st_disable_menu_icons']) && !empty($settings['st_disable_menu_icons']) ? '' : themecustoms_icon('fa fa-' . (isset($txt['lp_forum']) && $key == 'home' ? 'forum' : $key)));

			// Remove the element if it's in the setting.
			// Community shouldn't be removed
			if (in_array($key, $remove) && $key !== 'community')
				unset($current_menu[$key]);
		}
		$context['menu_buttons'] = $current_menu;

		// Community button
		$this->community($current_action);
	}

	
	/**
	 * Integration::community()
	 *
	 * Group menu items inside a 'Community' button.
	 * Excludes the desired items from it.
	 * 
	 * @return void
	 */
	private function community(&$current_action) : void
	{
		global $context, $settings;

		// Is community enabled?
		if (empty($settings['st_enable_community']))
			return;

		// Alright, put into community those that are not outside?
		$temp_menu = [];
		foreach ($context['menu_buttons'] as $action => $button)
		{
			// Don't check for home, community or excluded items
			if ($action == 'home' || $action == 'community' || (!empty($settings['st_not_community']) && in_array($action, explode(',', $settings['st_not_community']))))
				$temp_menu[$action] = $button;
			// Add the buttons?
			elseif (!in_array($action, explode(',', $settings['st_not_community'])))
				$temp_menu['community']['sub_buttons'][$action] = $button;
		}
		$context['menu_buttons'] = $temp_menu;

		// Update the active button
		if (isset($context['menu_buttons']['community']['sub_buttons'][$current_action]) && array_key_exists($current_action, $context['menu_buttons']['community']['sub_buttons']))
			$context['menu_buttons']['community']['active_button'] = true;
	}

	/**
	 * Integration::credits()
	 *
	 * Add a little surprise to the credits page
	 * @return void
	 */
	public function credits() : void
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
	public function hookBuffer($buffer) : string
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
	public function htmlAttributes() : void
	{
		global $settings, $context;

		// Data attributes
		$settings['themecustoms_html_attributes_data'] = (!empty($settings['themecustoms_html_attributes']['data']) && is_array($settings['themecustoms_html_attributes']['data']) ? ' ' . implode(' ', $settings['themecustoms_html_attributes']['data']) : '');

		// Disable the info center?
		if (isset($settings['st_disable_info_center']) && !empty($settings['st_disable_info_center']) && !empty($context['info_center']))
			unset($context['info_center']);
	}
}