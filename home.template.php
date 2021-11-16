<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines https://www.simplemachines.org
 * @copyright 2021 Simple Machines and individual contributors
 * @license https://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.1 RC4
 */

/**
 * The upper part of the main template layer. This is the stuff that shows above the main forum content.
 */
function template_theme_header_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings, $maintenance;

	// Wrapper div now echoes permanently for better layout options. h1 a is now target for "Go up" links.
	echo '
	Este es el header';
}

function template_theme_header_below() {


	echo 'below the header';
}

/**
 * The stuff shown immediately below the main content, including the footer
 */


function template_theme_footer_above() {}


function template_theme_footer_below()
{

	echo '
		Este es el footer';
}