<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

 /**
 * Initialize the customs template along with the theme version and the custom files
 */
function template_customs_init()
{
	global $settings;

	// The version this template/theme is for. This should probably be the version of SMF it was created for.
	// SMF cries if it's not defined around here.
	$settings['theme_version'] = '2.1';

	// Integration hooks, and other fun stuff
	add_integration_function('integrate_load_theme', 'ThemeCustoms\Integration::initialize#', false, $settings['theme_dir'] . '/themecustoms/Integration.php');
}

function themecustoms_page_index()
{
	global $txt;

	return array(
		'extra_before' => '<span class="pages">' . $txt['pages'] . '</span>',
		'previous_page' => '<span class="main_icons previous_page"></span>',
		'current_page' => '<span class="current_page">%1$d</span> ',
		'page' => '<a class="nav_page" href="{URL}">%2$s</a> ',
		'expand_pages' => '<span class="expand_pages" onclick="expandPages(this, {LINK}, {FIRST_PAGE}, {LAST_PAGE}, {PER_PAGE});"> ... </span>',
		'next_page' => '<span class="main_icons next_page"></span>',
		'extra_after' => '',
	);
}

function themecustoms_header()
{
}

function themecustoms_footer()
{
}