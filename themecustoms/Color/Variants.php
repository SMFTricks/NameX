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
	private static $_theme_variants = [];

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
		
		// Set the variants... Again
		self::setVariants();

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
					$context['available_themes'][$id]['selected_variant'] = isset($_GET['vrt']) ? $_GET['vrt'] : (!empty($options['theme_variant'] && in_array($options['theme_variant'], self::$_theme_variants)) ? $options['theme_variant'] : (!empty($settings['default_variant']) ? $settings['default_variant'] : $settings['theme_variants'][0]));
					if (!isset($context['available_themes'][$id]['variants'][$context['available_themes'][$id]['selected_variant']]['thumbnail']))
						$context['available_themes'][$id]['selected_variant'] = $settings['theme_variants'][0];
					// Thumbnail
					$context['available_themes'][$id]['thumbnail_href'] = $context['available_themes'][$id]['variants'][$context['available_themes'][$id]['selected_variant']]['thumbnail'];
					// Allow themes to override the text.
					$context['available_themes'][$id]['pick_label'] = isset($txt['variant_pick']) ? $txt['variant_pick'] : $txt['theme_pick_variant'];
				}
			}
		}
	}
}