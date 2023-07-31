<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Settings;

class Carousel
{
	/**
	 * @var int Number of carousel slides.
	 */
	private int $_slides = 1;

	/**
	 * @var int Slides limit
	 */
	private int $_slides_limit = 5;

	/**
	 * @var array Theme settings.
	 */
	public array $_settings = [];

	/**
	 * Carousel::settings()
	 * 
	 * Add the main carousel settings
	 * 
	 * @return void
	 */
	public function settings() : void
	{
		global $txt, $context;

		// Setting type
		if (!empty($context['st_themecustoms_setting_types']))
		{
			// Add the color setting type
			array_push($context['st_themecustoms_setting_types'], 'carousel');
			// Don't duplicate it if it's already there
			$context['st_themecustoms_setting_types'] = array_unique($context['st_themecustoms_setting_types']);
		}

		// Settings
		$this->_settings = [
			// Enable carousel
			[
				'id' => 'st_enable_carousel',
				'label' => $txt['st_enable_carousel'],
				'description' => $txt['st_enable_carousel_desc'],
				'theme_type' => 'carousel',
			],
			// Carousel only in index
			[
				'id' => 'st_carousel_index',
				'label' => $txt['st_carousel_index'],
				'theme_type' => 'carousel',
			],
			// Link text
			[
				'id' => 'st_carousel_button_text',
				'label' => $txt['st_carousel_link_text'],
				'description' => $txt['st_carousel_link_text_desc'],
				'type' => 'text',
				'theme_type' => 'carousel',
			],
			// Carousel Speed
			[
				'id' => 'st_carousel_speed',
				'label' => $txt['st_carousel_speed'],
				'description' => $txt['st_carousel_speed_desc'],
				'type' => 'number',
				'step' => '250',
				'theme_type' => 'carousel',
			],
		];

		// How many slides, if carousel is enabled...
		if (!empty($settings['st_enable_carousel']))
		{
			// Carousel Number of Slides
			$this->_settings[] = [
				'id' => 'st_carousel_slides',
				'label' => $txt['st_carousel_slides'],
				'description' => $txt['st_carousel_slides_desc'],
				'type' => 'number',
				'min' => 1,
				'max' => $this->_slides_limit,
				'step' => '1',
				'theme_type' => 'carousel',
			];
		}

		// Slides
		$this->slides();

		// Add them to the settings
		$context['theme_settings'] = array_merge($this->_settings, $context['theme_settings']);
	}

	/**
	 * Carousel::slides()
	 * 
	 * Add the slide options based on the number set.
	 * 
	 * @return void
	 */
	public function slides() : void
	{
		global $txt, $settings;

		// Is the carousel enabled?
		if (empty($settings['st_enable_carousel']))
			return;

		// Set the number
		if (!empty($settings['st_carousel_slides']) && $settings['st_carousel_slides'] <= $this->_slides_limit)
			$this->_slides = $settings['st_carousel_slides'];

		// Add the slides settings
		for($i = 1; $i <= $this->_slides; $i++)
		{
			// Title
			$this->_settings[] = [
				'section_title' => sprintf($txt['st_slider_x'], $i),
				'id' => 'st_carousel_title_' . $i,
				'label' => $txt['st_carousel_title'],
				'description' => $txt['st_carousel_title_desc'],
				'type' => 'text',
				'theme_type' => 'carousel',
			];
			// Caption
			$this->_settings[] = [
				'id' => 'st_carousel_text_' . $i,
				'label' => $txt['st_carousel_text'],
				'type' => 'textarea',
				'theme_type' => 'carousel',
			];
			// Link
			$this->_settings[] = [
				'id' => 'st_carousel_link_' . $i,
				'label' => $txt['st_carousel_link'],
				'type' => 'text',
				'theme_type' => 'carousel',
			];
			// Image
			$this->_settings[] = [
				'id' => 'st_carousel_image_url_' . $i,
				'label' => $txt['st_carousel_image_url'],
				'type' => 'text',
				'theme_type' => 'carousel',
			];
		}
	}
}