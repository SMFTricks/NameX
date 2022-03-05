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
	 * @var array Any additional settings to set
	 */
	public static $_settings = [
		'include_bootstrap' => false,
	];

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
	protected function loadThemeDetails()
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