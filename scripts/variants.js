// Get the current theme variant
let themeVariant = smf_theme_variant;

// Perhaps the user loaded a variant using the URL, this one has a higher priority
let requestVariant = smf_request_variant;

// Local Variant
let localVariant = localStorage.getItem('themeVariant');

// When reloading the page we don't have an actual color... yet
let themeColor = null;

// Easy color switching
let switchVariant = (setColor) => 
{
	// Replace the theme variant currently in use
	document.documentElement.classList.remove('theme-' + themeVariant, 'theme-' + localVariant);

	// Update the local Variant
	localStorage.setItem('themeVariant', setColor);
	// Define the var again cuz... idk actually
	localVariant = localStorage.getItem('themeVariant');
	// Add the selected theme variant
	document.documentElement.classList.toggle('theme-' + localVariant);



	// Update the variant in the user settings/options
	if (themeColor !== null || (themeColor === null && localVariant !== themeVariant))
	{
		// Update user option
		smf_setThemeOption('theme_variant', setColor, smf_theme_id, smf_session_id, smf_session_var, null);
		// Update the theme Variant with the new color
		themeVariant = setColor;
	}
}

// Since we are loading all variants at once for the styleswitcher, we need to initialize the current variant
if (themeColor === null)
	switchVariant((localVariant === 'null' || localVariant === null || requestVariant) ? themeVariant  : localVariant);

// When someone clicks the button
$(".theme-variant-toggle").click(function() {
	// Get the theme color for the variant
	themeColor = $(this).attr('data-color');
  	// Switch the theme variant with the selected color
	switchVariant(themeColor);

	return false;
});

// Some bits for the color picker, borrowed from the buttonlist stuff
$(function() {
	$('#colorpicker_menu').each(function(index, item) {
		$(item).prev().click(function(e) {
			e.stopPropagation();
			e.preventDefault();

			if ($(item).is(':visible')) {
				$(item).css('display', 'none');
				return true;
			}
			$(item).css('display', 'block');
			$(item).css('top', $(this).offset().top + $(this).height());
		});
		$(document).click(function() {
			$(item).css('display', 'none');
		});
	});

	$('li#user_colorpicker > a').next().find('a').click(function (e) {
		var $obj = $(this);
		// All of the variants are now without the active class if they had it.
		$('ul#colorpicker_menu li a').removeClass('active');
		// Toggle this new selection as active
		$obj.toggleClass('active');
	});
});