<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

/**
 * Carousel main layout
 */
function themecustoms_carousel($carousel = false)
{
	global $settings, $txt, $context, $topic, $board;

	// Is the carousel enabled?
	if (empty($settings['st_enable_carousel']))
		return;

	// Always enabled?
	if (empty($settings['st_carousel_index']) || (empty($context['current_action']) && empty($board)))
		$carousel = true;

	// Check if the carousel should be displayed
	if (!$carousel)
		return;

	// Language
	loadLanguage('ThemeCustoms/carousel');

	// Normal carousel
	if (empty($context['st_carousel_full']))
		carousel_settings();

	// Check if there are at least 2 slides
	if (empty($context['st_carousel_items']) || count($context['st_carousel_items']) < 2)
		return;

	echo '
	<div id="themecustoms-carousel" class="carousel slide st_carousel"', (!empty($settings['st_carousel_speed']) ? ' data-bs-ride="carousel"' : ''), '>
		<div class="carousel-indicators">';

	// Carousel indicators
	foreach ($context['st_carousel_items'] as $number => $item)
	{
		// Remove it if there's no title
		if (empty($item['title']))
			continue;

		echo '
			<a role="button" data-bs-target="#themecustoms-carousel" data-bs-slide-to="', ($number - 1), '"', (!empty($item['active']) ? ' class="active"' : ''), ' aria-current="true" aria-label="', $txt['st_slide'], ' ', $number, '">
				<span class="title">', $item['title'], '</span>
			</a>';
	}

	echo  '
		</div>
		<div class="carousel-inner">';

	// Carousel slides
	foreach ($context['st_carousel_items'] as $number => $slide)
	{
		// Remove it if there's no title
		if (empty($slide['title']))
			continue;

		echo '
			<div class="carousel-item', (!empty($slide['active']) ? ' active' : ''), '" ', (!empty($settings['st_carousel_speed']) ? ' data-bs-interval="' . $settings['st_carousel_speed'] . '"' : ''), (!empty($slide['image']) ? ' style="background-repeat: no-repeat; background-size: cover; background-image: url(' . $slide['image'] . ');"' : ''), '>
				<div class="carousel-block">
					<div class="carousel-caption">
						<h5>', $slide['title'], '</h5>
						', !empty($slide['text']) ? '<p>' . $slide['text'] . '</p>' : '', '
						', (!empty($slide['link']) ? '<a class="button" href="' . $slide['link'] . '">' . (!empty($settings['st_carousel_button_text']) ? $settings['st_carousel_button_text'] : $txt['st_carousel_go_to_link']) . '</a>' : ''), '
					</div>
				</div>
			</div>';
	}

	echo '
		</div>
		<a role="button" class="carousel-control carousel-control-prev" type="button" data-bs-target="#themecustoms-carousel" data-bs-slide="prev">
			<span class="fa fa-chevron-left" aria-hidden="true"></span>
			<span class="visually-hidden">', $txt['st_previous'], '</span>
		</a>
		<a role="button" class="carousel-control carousel-control-next" type="button" data-bs-target="#themecustoms-carousel" data-bs-slide="next">
			<span class="fa fa-chevron-right" aria-hidden="true"></span>
			<span class="visually-hidden">', $txt['st_next'], '</span>
		</a>
	</div>';
}

/**
 * Build the carousel settings for the normal carousel
 */
function carousel_settings()
{
	global $settings, $context;

	$carousel_items = [];
	$context['carousel_items'] = [];
	foreach ($settings as $key => $value)
	{
		// Skip if they don't match 'st_carousel_'
		if (strpos($key, 'st_carousel_') !== 0)
			continue;

		// Title?
		if (strpos($key, 'st_carousel_title_') !== false)
			$carousel_items[str_replace('st_carousel_title_', '', $key)]['title'] = $value;
		// Text?
		elseif (strpos($key, 'st_carousel_text_') !== false)
			$carousel_items[str_replace('st_carousel_text_', '', $key)]['text'] = $value;
		// Link?
		elseif (strpos($key, 'st_carousel_link_') !== false)
			$carousel_items[str_replace('st_carousel_link_', '', $key)]['link'] = $value;
		// Image?
		elseif (strpos($key, 'st_carousel_image_url_') !== false)
			$carousel_items[str_replace('st_carousel_image_url_', '', $key)]['image'] = $value;
	}

	// Sort them correctly
	ksort($carousel_items);

	// Loop through it again just to make sure
	$slides = 1;
	foreach ($carousel_items as $item)
	{
		// Skip it if there's no title
		if (empty($item['title']))
			continue;

		// Set the active one
		$context['st_carousel_items'][$slides] = $item;

		// Set the active one
		if ($slides == 1)
			$context['st_carousel_items'][$slides]['active'] = true;
		$slides++;
	}
}