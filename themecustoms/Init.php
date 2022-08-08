<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license GNU GPLv3
 */

namespace ThemeCustoms;

use ThemeCustoms\Config\ { Config };

class Init extends Config
{
	/**
	 * @var string Theme Name
	 */
	protected $_theme_name = 'NameX';

	/**
	 * @var string Theme Version
	 */
	protected $_theme_version = '1.0.1';

	/**
	 * @var array Theme Author
	 */
	protected $_theme_author = [
		'name' => 'Diego Andrés',
		'smf_id' => 254071,
	];

	/**
	 * @var array Theme support details
	 */
	protected $_theme_details = [
		'support' => [
			'github_url' => 'https://github.com/SMFTricks/NameX',
			'smf_site_id' => 0,
			'smf_support_topic' => 0,
			'custom_support_url' => 'https://smftricks.com',
		],
	];

	/**
	 * Color Options
	 */
	public static $_color_options = [
		'variants'=> true,
		'darkmode' => true,
	];

	/**
	 * Init::loadHooks()
	 */
	protected function loadHooks() : void
	{
		// Load fonts
		add_integration_function('integrate_pre_css_output', __CLASS__ . '::fonts', false, '$themedir/themecustoms/Init.php');

		// Variants
		add_integration_function('integrate_customtheme_color_variants', __CLASS__ . '::variants', false, '$themedir/themecustoms/Init.php');

		// Dark Mode
		add_integration_function('integrate_customtheme_color_darkmode', __CLASS__ . '::darkMode', false, '$themedir/themecustoms/Init.php');
	}

	/**
	 * Init::fonts()
	 * 
	 * Load some google fonts
	 * 
	 * @param array $assets The assets array
	 */
	public static function fonts() : void
	{
		global $context;

		// Passion One Font
		if (empty($context['header_logo_url_html_safe']))
			loadCSSFile('https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap', ['external' => true, 'order_pos' => -800]);

		// Lato Font
		loadCSSFile('https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap', ['external' => true, 'order_pos' => -800]);
	}

	/**
	 * Init::Variants()
	 * 
	 * Add the theme variants
	 * 
	 * @param array $variants
	 * @return void
	 */
	public static function variants(array &$variants) : void
	{
		$variants = [
			'red',
			'green',
			'blue',
			'yellow',
			'purple',
			'pink',
		];
	}

	/**
	 * Init::darkMode()
	 * 
	 * Enable the Dark Mode?
	 * 
	 * @param bool $darkmode
	 * @return void
	 */
	public static function darkMode(bool &$darkmode) : void
	{
		$darkmode = true;
	}
}