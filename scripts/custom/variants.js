/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

// Get the current theme variant
let themeVariant = smf_theme_variant;

// Local Variant
let localVariant = localStorage.getItem('themeVariant');

// Theme Color on reload
let themeColor = null;

// Easy color switching
let switchVariant = (setColor) => 
{
	// Update the theme variant
	localStorage.setItem('themeVariant', setColor);

	// Replace the theme variant
	document.documentElement.dataset.themecolor = setColor;

	// Update user option
	smf_setThemeOption('theme_variant', setColor, smf_theme_id, smf_session_id, smf_session_var, null);

	// Update the theme Variant with the new color
	themeVariant = setColor;
	themeColor = true;
}

// Update the theme variant using the request variant
if (themeColor === null && localVariant !== 'null' && localVariant !== null)
	switchVariant(smf_member_id ? themeVariant : localVariant);

// When someone clicks the button
$(".theme-variant-toggle").click(function() {
	// Get the theme color for the variant
	themeColor = $(this).attr('data-color');
  	// Switch the theme variant with the selected color
	switchVariant(themeColor);

	return false;
});

// Select the active variant
$(function() {
	$('li#user_colorpicker > a').next().find('a').click(function (e) {
		var $obj = $(this);
		// All of the variants are now without the active class if they had it.
		$('ul#colorpicker_menu li a').removeClass('active');
		// Toggle this new selection as active
		$obj.toggleClass('active');
	});
});