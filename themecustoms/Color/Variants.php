<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

use ThemeCustoms\Theme;

if (!defined('SMF'))
	die('No direct access...');

class Variants
{
	/**
	 * @var array The theme color variants (red, green, blue, etc)
	 */
	private static $_theme_variants;

	/**
	 * @var array The variant options for user selection
	 */
	private static $_variant_options;

	/**
	 * @var array The theme user options
	 */
	private static $_theme_options;

	/**
	 * @var int Order position for the css variants
	 */
	private static $_order_position = 101;

	/**
	 * @var int Order position for the css rtl variants
	 */
	private static $_order_position_rtl = 4200;

	/**
	 * @var bool Enable a styleswitcher using JS
	 */
	private static $_enable_styleswitcher = true;

	/**
	 * Variants::init()
	 *
	 * Initializes the theme color variants because
	 * SMF scans the index.template.php for the theme variants.
	 * It's nice because it can show the variants when
	 * the theme is NOT selected, but I don't care.
	 * 
	 * @param array $theme_variants The defined color variants
	 * 
	 * @return void
	 */
	public static function init($variants)
	{
		// Theme variants... Add the default style to it just for presentation.
		self::$_theme_variants = array_merge(['default'], $variants);

		// Insert the variants using the theme settings
		add_integration_function('integrate_theme_settings', __CLASS__ . '::setVariants', false);

		// Add the variants to the list of available themes
		add_integration_function('integrate_theme_context', __CLASS__ . '::userSelection', false);

		// Add the theme variants as a theme option too
		add_integration_function('integrate_theme_options', __CLASS__ . '::userOptions', false);

		// Load the variants CSS
		// Set to true when loading all of the variants at once (for styleswitching)
		self::variantCSS(self::$_enable_styleswitcher);

		// Insert the variants JS vars
		if (!empty(self::$_enable_styleswitcher))
			self::addJavaScriptVars();

		// Load the JS file for the variants
		self::variantJS();
	}

	/**
	 * Variants::getVariant()
	 *
	 * Adds the already defined theme color variants to the settings.
	 *
	 * @return void
	 */
	public static function setVariants()
	{
		global $settings;

		// Add the color variants to the settings
		$settings['theme_variants'] = self::$_theme_variants;
	}

	/**
	 * Variants::userSelection()
	 *
	 * Insert the color variants into the available themes list.
	 * This is needed so that the user can actually change the color variant of the theme.
	 *
	 * @return void
	 */
	public static function userSelection()
	{
		global $context, $settings, $options, $txt;

		// Is user selection enabled?
		if (!empty($settings['disable_user_variant']) && !allowedTo('admin_forum'))
			return;

		// Check only for the themes page
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'theme')
		{
			// Add the color variants to this theme
			foreach($context['available_themes'] as $id => $theme)
			{
				// Add the variants to the theme
				if ($theme['id'] == $settings['theme_id'])
				{
					// Add the variants with their label and thumbnail
					foreach(self::$_theme_variants as $variant)
						$context['available_themes'][$id]['variants'][$variant] = [
							'label' => isset($txt['variant_' . $variant]) ? $txt['variant_' . $variant] : $variant,
							'thumbnail' => !file_exists($theme['theme_dir'] . '/images/thumbnail.png') || file_exists($theme['theme_dir'] . '/images/thumbnail_' . $variant . '.png') ? $theme['images_url'] . '/thumbnail_' . $variant . '.png' : ($theme['images_url'] . '/thumbnail.png'),
						];

					// The selected variant
					$context['available_themes'][$id]['selected_variant'] = isset($_GET['vrt']) ? $_GET['vrt'] : (!empty($options['theme_variant']) && in_array($options['theme_variant'], self::$_theme_variants) ? $options['theme_variant'] : (!empty($settings['default_variant']) ? $settings['default_variant'] : self::$_theme_variants[0]));
					if (!isset($context['available_themes'][$id]['variants'][$context['available_themes'][$id]['selected_variant']]['thumbnail']))
						$context['available_themes'][$id]['selected_variant'] = self::$_theme_variants[0];
					// Thumbnail
					$context['available_themes'][$id]['thumbnail_href'] = $context['available_themes'][$id]['variants'][$context['available_themes'][$id]['selected_variant']]['thumbnail'];
					// Allow themes to override the text.
					$context['available_themes'][$id]['pick_label'] = isset($txt['variant_pick']) ? $txt['variant_pick'] : $txt['theme_pick_variant'];
				}
			}
		}
	}

	/**
	 * Variants::userOptions()
	 *
	 * Adds the color variants to the theme options.
	 * No idea why SMF doesn't do this by default.
	 *
	 * @return void
	 */
	public static function userOptions()
	{
		global $context, $txt, $settings, $options;

		// Check if the user is allowed to change color variants
		if (!empty($settings['disable_user_variant']) && !allowedTo('admin_forum'))
			return;

		// Create the variant options, using text strings
		foreach(self::$_theme_variants as $variant)
			self::$_variant_options[$variant] = $txt['variant_' . $variant];

		// Create the theme options
		self::$_theme_options = [
			$txt['st_color_variants'],
			[
				'id' => 'theme_variant',
				'label' => isset($txt['variant_pick']) ? $txt['variant_pick'] : $txt['theme_pick_variant'],
				'options' => self::$_variant_options,
				'default' => false,
				'enabled' => !empty(self::$_theme_variants),
			]
		];

		// Set forum default as the default variant
		if (!isset($options['theme_variant']))
			$options['theme_variant'] = (isset($settings['default_variant']) && !empty($settings['default_variant']) ? $settings['default_variant'] : self::$_theme_variants[0]);

		// Insert the theme options
		$context['theme_options'] = array_merge(self::$_theme_options, $context['theme_options']);
	}

	/**
	 * Variants::variantCSS()
	 *
	 * Loads the variant CSS.
	 * 
	 * @param bool $load_all You can enable this to load all variants at once,
	 * useful for implementing a stylswitcher.
	 *
	 * @return void
	 */
	public static function variantCSS($load_all = false)
	{
		global $context, $settings, $options;

		// Overriding - for previews and that other stuff.
		if (!empty($_REQUEST['variant']))
			$_SESSION['id_variant'] = $_REQUEST['variant'];
		// User selection?
		if (empty($settings['disable_user_variant']) || allowedTo('admin_forum'))
			$context['theme_variant'] = !empty($_SESSION['id_variant']) && in_array($_SESSION['id_variant'], self::$_theme_variants) ? $_SESSION['id_variant'] : (!empty($options['theme_variant']) && in_array($options['theme_variant'], self::$_theme_variants) ? $options['theme_variant'] : '');

		// If not a user variant, select the default.
		if ($context['theme_variant'] == '' || !in_array($context['theme_variant'], self::$_theme_variants))
			$context['theme_variant'] = !empty($settings['default_variant']) && in_array($settings['default_variant'], self::$_theme_variants) ? $settings['default_variant'] : self::$_theme_variants[0];

		// Do this to keep things easier in the templates.
		$context['theme_variant_url'] = $context['theme_variant'] . '/';

		// Add the CSS file for the variant only if it's not the default.
		if (!empty($context['theme_variant']) && $context['theme_variant'] != 'default' && empty($load_all))
		{
			loadCSSFile('variants/' . $context['theme_variant'] . '.css', ['order_pos' => self::$_order_position], 'smf_index_' . $context['theme_variant']);
			if ($context['right_to_left'])
				loadCSSFile('variants/rtl.' . $context['theme_variant'] . '.css', ['order_pos' => self::$_order_position_rtl], 'smf_rtl' . $context['theme_variant']);
		}
		// Load all of the styles
		elseif (!empty($load_all) && (empty($settings['disable_user_variant']) || allowedTo('admin_forum')))
		{
			// For styleswitch we load all of the variants at once.
			foreach (self::$_theme_variants as $variant)
				// Only if it's not the default
				if ($variant != 'default')
				{
					// Load the css file
					loadCSSFile('variants/' . $variant . '.css', ['order_pos' => self::$_order_position++], 'smf_index_' . $variant);
					// RLT?
					if ($context['right_to_left'])
						loadCSSFile('variants/rtl.' . $variant . '.css', ['order_pos' => self::$_order_position_rtl++], 'smf_rtl' . $context['theme_variant']);
				}
		}
	}

	/**
	 * Variants::addJavaScriptVars()
	 *
	 * Loads the variant JS vars.
	 *
	 * @return void
	 */
	public static function addJavaScriptVars()
	{
		global $options, $settings;

		// Theme Variant
		addJavaScriptVar('smf_theme_variant', '\'' . (!empty($_REQUEST['variant']) && in_array($_REQUEST['variant'], self::$_theme_variants) ? $_REQUEST['variant'] : (!empty($options['theme_variant']) && in_array($options['theme_variant'], self::$_theme_variants) ? $options['theme_variant'] : (!empty($settings['default_variant']) ? $settings['default_variant'] : self::$_theme_variants[0]))) . '\'');

		// Obtain a variant from the URL for higher priority
		addJavaScriptVar('smf_request_variant',  (!empty($_REQUEST['variant']) ? 'true' : 'false'));
	}

	/**
	 * Variants::variantJS()
	 *
	 * Loads the variant JS.
	 *
	 * @return void
	 */
	public static function variantJS()
	{
		global $settings;

		// Load the file only if the swtylswitch is enabled and the user can change variants
		if ((!empty(self::$_enable_styleswitcher) && empty($settings['disable_user_variant'])) || allowedTo('admin_forum'))
			loadJavascriptFile(
				'variants.js',
				[
					'minimize' => false,
					'defer' => true,
					'aysnc' => true,
				],
				'smftheme_js_variants'
			);
	}
}