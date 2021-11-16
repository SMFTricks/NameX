<?php

/**
 * @package ST Theme
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

function template_customs_init()
{
	global $settings;

	// Integration hooks, and other fun stuff
	add_integration_function('integrate_load_theme', 'ThemeCustoms\Integration::initialize#', false, $settings['theme_dir'] . '/themecustoms/Integration.php');
}

function themecustoms_header()
{

}

function themecustoms_footer()
{
	
}