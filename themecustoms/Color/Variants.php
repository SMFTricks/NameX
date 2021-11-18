<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

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
	 * Variants::init()
	 *
	 * Initializes the theme color variants and
	 * other unnecessary bullshit caused by whoever decided to
	 * only scan the index.template.php for color variants.
	 * 
	 * @param array $theme_variants The defined color variants
	 * 
	 * @return void
	 */
	public static function init($variants)
	{
		// Theme variants...
		self::$_theme_variants = $variants;

		// Insert the variants using the theme settings
		add_integration_function('integrate_theme_settings', __CLASS__ . '::setVariants', false);

		// Add the variants to the list of available themes
		add_integration_function('integrate_theme_context', __CLASS__ . '::userSelection', false);

		// Add the theme variants as a theme option too
		add_integration_function('integrate_theme_options', __CLASS__ . '::userOptions', false);

		// Load the variant CSS
		self::variantCSS();
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
					$context['available_themes'][$id]['selected_variant'] = isset($_GET['vrt']) ? $_GET['vrt'] : (!empty($options['theme_variant'] && in_array($options['theme_variant'], self::$_theme_variants)) ? $options['theme_variant'] : (!empty($settings['default_variant']) ? $settings['default_variant'] : self::$_theme_variants[0]));
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
	 *
	 * @return void
	 */
	public static function userOptions()
	{
		global $context, $txt, $settings;

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
				'default' => true,
				'enabled' => !empty(self::$_theme_variants),
			]
		];

		// Insert the theme options
		$context['theme_options'] = array_merge(self::$_theme_options, $context['theme_options']);
	}

	/**
	 * Variants::variantCSS()
	 *
	 * Loads the variant CSS.
	 *
	 * @return void
	 */
	public static function variantCSS()
	{
		global $context, $settings, $options;

		// Overriding - for previews and that ilk.
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

		if (!empty($context['theme_variant']))
		{
			loadCSSFile('variants/' . $context['theme_variant'] . '.css', array('order_pos' => 300), 'smf_index' . $context['theme_variant']);
			if ($context['right_to_left'])
				loadCSSFile('variants/rtl.' . $context['theme_variant'] . '.css', array('order_pos' => 4200), 'smf_rtl' . $context['theme_variant']);
		}
	}
}