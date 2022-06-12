/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

// Some theme bits
$(function() {

	// Settings tabs
	$( '#st_settings_tabs').tabs();
	// Info center tabs
	$( '#info_center_blocks').tabs();

	// Replace stats icon
	$("img[src=\'"+smf_images_url+"/icons/stats_info.png\']").replaceWith("<i class=\'main_icons stats\'></i>");

	// Change the behaviour of the notify button
	$('.normal_button_strip_notify').next().find('a').click(function (e) {
		var $obj = $(this);
		// All of the sub buttons are now without the active class if they had it.
		$('.notify_dropdown .viewport .overview a').removeClass('active');
		// Toggle this new selection as active
		$obj.toggleClass('active');
		e.preventDefault();
		ajax_indicator(true);
		// New Text
		var new_text = $obj.find('em').text();
		var new_text_lCase = new_text.toLowerCase();
		$.get($obj.attr('href') + ';xml', function () {
			ajax_indicator(false);
			$('.normal_button_strip_notify > span').text(new_text);
			$('.normal_button_strip_notify i.fa').removeClass();
			$('.normal_button_strip_notify i').addClass('fa fa-' + new_text_lCase);
		});

		return false;
	});

	// Likes on quickbuttons
	$(document).on('click', 'ul.quickbuttons li.post_like_button > a', function(event){
		var obj = $(this);
		event.preventDefault();
		ajax_indicator(true);
		$.ajax({
			type: 'GET',
			url: obj.attr('href') + ';js=1',
			headers: {
				"X-SMF-AJAX": 1
			},
			xhrFields: {
				withCredentials: typeof allow_xhjr_credentials !== "undefined" ? allow_xhjr_credentials : false
			},
			cache: false,
			dataType: 'html',
			success: function(html){
				obj.parent().replaceWith($(html).first('li'));
			},
			error: function (html){
			},
			complete: function (){
				ajax_indicator(false);
			}
		});

		return false;
	});

	// Likes count for messages.
	$(document).on('click', '.buttonlike_count', function(e){
		e.preventDefault();
		var title = $(this).find('em').text();
			url = $(this).attr('href') + ';js=1';
		return reqOverlayDiv(url, title, 'like');
	});

	// Color Picker Menu and Theme Mode
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

	// Fixing the other popups because of the flexbox stuff...
	$('#profile_menu, #pm_menu, #alerts_menu').each(function(index, item) {
		$(item).prev().click(function(e) {
			$(item).css('top', $(this).offset().top + $(this).height());
		});
	});

	// Linktree toggler
	$(document).on('click', '.navigate_section ul li.trigger a', function(e){
		$('.navigate_section ul li').toggleClass('show');
	});

	// Menu improvements
	$('.mobile_user_menu').click(function() {
		if ($('#mobile_user_menu').is(':visible') == true) {
			$(document).mouseup(function (e) {
				if ($('#mobile_user_menu').has(e.target).length === 0)
					$('#mobile_user_menu').hide();
			}).keyup(function(e){
				if (e.keyCode == 27)
					$('#mobile_user_menu').hide();
			});
		}
	});

	// Mobile actions
	$('.mobile_act').click(function() {
		if ($('#mobile_action').is(':visible') == true) {
			$(document).mouseup(function (e) {
				if ($('#mobile_action').has(e.target).length === 0)
					$('#mobile_action').hide();
			}).keyup(function(e){
				if (e.keyCode == 27)
					$('#mobile_action').hide();
			});
		}
	});

	// Mobile mod
	$('.mobile_mod').click(function() {
		if ($('#mobile_moderation').is(':visible') == true) {
			$(document).mouseup(function (e) {
				if ($('#mobile_moderation').has(e.target).length === 0)
					$('#mobile_moderation').hide();
			}).keyup(function(e){
				if (e.keyCode == 27)
					$('#mobile_moderation').hide();
			});
		}
	});
});