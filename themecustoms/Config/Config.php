<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Config;

abstract class Config
{
	/**
	 * @var string Theme Name
	 */
	protected $_theme_name;

	/**
	 * @var string Theme Version
	 */
	protected $_theme_version;

	/**
	 * @var array Theme Author
	 */
	protected $_theme_author = [
		'name' => '',
		'smf_id' => 0,
	];

	/**
	 * @var array Theme support details
	 */
	protected $_theme_details = [
		'support' => [
			'github_url' => '',
			'smf_site_id' => 0,
			'smf_support_topic' => 0,
			'custom_support_url' => '',
		],
	];

	/**
	 * @var array Additional settings and configuration
	 */
	public static $_settings = [
		'include_bootstrap' => false,
	];

	/**
	 * @var array Avatar options
	 */
	public static $_avatar_options = [
		// Enable avatars on topic list
		'topics_list' => true,
		// Enable avatars on board list
		'boards' => true,
		// Enable avatars on recent posts (info center)
		'recent_posts' => true,
		// Enable avatars on users online list
		'users_online' => true,
	];

	/**
	 * @var array Color Options
	 */
	public static $_color_options = [
		// Theme Variants
		'variants' => false,
		// Theme Dark Mode
		'darkmode' => false,
		// Theme Color Changer
		'colorchanger' => false,
	];

	public static $_likes_quickbutton = true;

	/**
	 * Load the custom hooks for the theme
	 */
	abstract protected function loadHooks(); 

	/**
	 * Config the custom theme
	 */
	public function __construct()
	{
		// Load the theme details
		$this->loadThemeDetails();

		// Load the custom theme hooks
		$this->loadHooks();
	}

	/**
	 * Config::loadThemeDetails()
	 * 
	 * Load the theme details
	 * 
	 * @return void
	 */
	protected function loadThemeDetails() : void
	{
		global $settings;

		// Theme Name
		$settings['theme_name'] = $this->_theme_name;

		// Theme Version
		$settings['theme_real_version'] = $this->_theme_version;

		// Theme Author
		$settings['theme_author'] = $this->_theme_author;

		// Theme Support
		$settings['theme_support'] = $this->_theme_details['support'];
	}
}