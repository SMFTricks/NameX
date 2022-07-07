/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

$(function() {
	// Drop the og settings
	$('.st_carousel_title_1').hide();
	$('.st_carousel_title_1 + dl.settings').hide();
	$('.st_carousel_title_2').hide();
	$('.st_carousel_title_2 + dl.settings').hide();
	$('.st_carousel_title_3').hide();
	$('.st_carousel_title_3 + dl.settings').hide();
	$('.st_carousel_title_4').hide();
	$('.st_carousel_title_4 + dl.settings').hide();
	$('.st_carousel_title_5').hide();
	$('.st_carousel_title_5 + dl.settings').hide();

	// Add a 'Add New' button inside #settingtype-carousel
	$('#settingtype-carousel').append('<div class="information carousel_new"><a href="#" class="button floatnone">' + carousel_add_new + '</a></div>');

	// When clicking the new button...
	$('.carousel_new a').click(function(e) {
		add_new_setting();
		e.preventDefault();
	});
});

// Create a new function for it 'add_new_setting'
let add_new_setting = () => {
	// Inject the new setting before the 'Add New' button
	carousel_slides_total++;
	$('.carousel_new').before('' +
		'<div class="title_bar tca_carousel_title_' + carousel_slides_total + '">' + 
		'<h3 class="titlebg">' +
			'<label for="tca_delete_' + carousel_slides_total + '">Slider ' + carousel_slides_total + '</label><span class="floatright"><strong><label for="tca_delete_' + carousel_slides_total + '">Delete</label></strong> <input type="checkbox" name="tc_carousel_delete" id="tca_delete_' + carousel_slides_total + '" value="' + carousel_slides_total + '"></span>' + 
		'</h3>' + 
	'</div>' +
	'<dl class="settings">' +
		'<dt>' +
			'<label for="options_tca_carousel_title_' + carousel_slides_total + '"><strong>' + carousel_item_title + '</strong></label>:' +
			'<br>' +
			'<div class="smalltext">' + carousel_item_title_desc + '</div>' +
		'</dt>' +
		'<dd>' +
			'<input type="text" name="options[tca_carousel_title_' + carousel_slides_total + ']" id="options_tca_carousel_title_' + carousel_slides_total + '" value="" size="40">' +
		'</dd>' +
		'<dt>' +
			'<label for="options_tca_carousel_text_' + carousel_slides_total + '"><strong>' + carousel_item_text + '</strong></label>:' +
		'</dt>' +
		'<dd>' +
			'<textarea rows="4" style="width: 95%;" cols="40" name="options[tca_carousel_text_' + carousel_slides_total + ']" id="options_tca_carousel_text_' + carousel_slides_total + '"></textarea>' +
		'</dd>' +
		'<dt>' +
			'<label for="options_tca_carousel_link_' + carousel_slides_total + '"><strong>' + carousel_item_link + '</strong></label>:' +
		'</dt>' +
		'<dd>' +
			'<input type="text" name="options[tca_carousel_link_' + carousel_slides_total + ']" id="options_tca_carousel_link_' + carousel_slides_total + '" value="" size="40">' +
		'</dd>' +
		'<dt>' +
			'<label for="options_tca_carousel_image_' + carousel_slides_total + '"><strong>' + carousel_item_image_upload + '</strong></label>:' +
			'<br>' +
		'</dt>' +
		'<dd>' +
			'<input type="file" name="tca_carousel_image_' + carousel_slides_total + '" id="options_tca_carousel_image_' + carousel_slides_total + '">' +
		'</dd>' +
	'</dl>');
};