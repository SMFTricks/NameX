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

class Integration
{
	protected $_load_theme;

	public function initialize()
	{	
		// Autoload
		spl_autoload_register(__CLASS__ . '::autoload');

		// Theme Settings
		$this->_load_theme = new Theme;

		// Main hooks
		$this->loadHooks();
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

		// Do any third-party scripts want in on the fun?
		call_integration_hook('integrate_customtheme_autoload', array(&$classMap));
	
		foreach ($classMap as $prefix => $dirName)
		{
			// does the class use the namespace prefix?
			$len = strlen($prefix);
			if (strncmp($prefix, $class, $len) !== 0)
			{
				continue;
			}
	
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
	protected function loadHooks()
	{
		$hooks = [
			'menu_buttons' => 'main_menu',
			'current_action' => 'current_action',
			'actions' => 'hookActions',
		];
		foreach ($hooks as $point => $callable)
			add_integration_function('integrate_' . $point, __CLASS__ . '::' . $callable, false);
	}

	/**
	 * Integration::hookActions()
	 *
	 * Insert any additional hooks needed in very specific cases
	 * @param array $actions An array containing all possible SMF actions. This includes loading different hooks for certain areas.
	 * @return void
	 */
	public static function hookActions(&$actions)
	{
		// Add some hooks by action
		if (isset($_REQUEST['action']))
			switch ($_REQUEST['action'])
			{
				// Admin News
				case 'admin':
					add_integration_function('integrate_admin_areas', __NAMESPACE__ . '\Settings::admin_areas#', false);
					break;
			}
	}

	/**
	 * Integration::main_menu()
	 *
	 * Add or change menu buttons
	 * @param array $buttons
	 * @return void
	 */
	public function main_menu(&$buttons)
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
	 * Integration::current_action()
	 *
	 * Hook our menu icons setting for enabling/disabling.
	 * It's done just in case users haven't updated their forums to the final version 
	 * Or for whatever reason they are missing the setting.
	 * 
	 * @return void
	 */
	public function current_action()
	{
		global $context, $settings;

		// Disable menu icons?
		if (isset($settings['st_disable_menu_icons']) && !empty($settings['st_disable_menu_icons']))
		{
			$current_menu = $context['menu_buttons'];
			foreach ($context['menu_buttons'] as $key => $button)
			{
				$current_menu[$key]['icon'] = '';
			}
			$context['menu_buttons'] = $current_menu;
		}
	}
}