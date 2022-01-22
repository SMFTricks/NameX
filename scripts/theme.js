$(function() {
	$('ul.quickbuttons').superfish({delay : 250, speed: 100, sensitivity : 8, interval : 50, timeout : 1});

	// tooltips
	$('.preview').SMFtooltip();

	// find all nested linked images and turn off the border
	$('a.bbc_link img.bbc_img').parent().css('border', '0');
});

// The purpose of this code is to fix the height of overflow: auto blocks, because some browsers can't figure it out for themselves.
function smf_codeBoxFix()
{
	var codeFix = $('code');
	$.each(codeFix, function(index, tag)
	{
		if (is_webkit && $(tag).height() < 20)
			$(tag).css({height: ($(tag).height() + 20) + 'px'});

		else if (is_ff && ($(tag)[0].scrollWidth > $(tag).innerWidth() || $(tag).innerWidth() == 0))
			$(tag).css({overflow: 'scroll'});

		// Holy conditional, Batman!
		else if (
			'currentStyle' in $(tag) && $(tag)[0].currentStyle.overflow == 'auto'
			&& ($(tag).innerHeight() == '' || $(tag).innerHeight() == 'auto')
			&& ($(tag)[0].scrollWidth > $(tag).innerWidth() || $(tag).innerWidth == 0)
			&& ($(tag).outerHeight() != 0)
		)
			$(tag).css({height: ($(tag).height + 24) + 'px'});
	});
}

// Add a fix for code stuff?
if (is_ie || is_webkit || is_ff)
	addLoadEvent(smf_codeBoxFix);

// Toggles the element height and width styles of an image.
function smc_toggleImageDimensions()
{
	$('.postarea .bbc_img.resized').each(function(index, item)
	{
		$(item).click(function(e)
		{
			$(item).toggleClass('original_size');
		});
	});
}

// Add a load event for the function above.
addLoadEvent(smc_toggleImageDimensions);

function smf_addButton(stripId, image, options)
{
	$('#' + stripId).append(
		'<a href="' + options.sUrl + '" class="button last" ' + ('sCustom' in options ? options.sCustom : '') + ' ' + ('sId' in options ? ' id="' + options.sId + '_text"' : '') + '>'
			+ options.sText +
		'</a>'
	);
}

// Some theme bits
$(function() {

	// Settings tabs
	$( '#st_settings_tabs').tabs();
	// Info center tabs
	$( '#info_center_blocks').tabs();

	// Change the behaviour of the notify button
	$('.normal_button_strip_notify').next().find('a').click(function (e) {
		var $obj = $(this);
		// All of the sub buttons are now without the active class if they had it.
		$('.notify_dropdown .viewport .overview a').removeClass('active');
		// Toggle this new selection as active
		$obj.toggleClass('active');
		e.preventDefault();
		ajax_indicator(true);
		$.get($obj.attr('href') + ';xml', function () {
			ajax_indicator(false);
			$('.normal_button_strip_notify > span').text($obj.find('em').text());
		});

		return false;
	});

	// Likes on quickbuttons
	$(document).on('click', 'ul.quickbuttons li.smflikebutton > a', function(event){
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
		return reqOverlayDiv(url, title, 'post/thumbup.png');
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
});