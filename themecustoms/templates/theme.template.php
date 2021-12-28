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
	global $scripturl, $context, $settings;

	themecustoms_colorpicker();

	echo '
	<header>
		<div id="header">
			<h1 class="forumtitle">
				<a id="top" href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? '<span class="theme-logo"><span>' . substr_replace($settings['theme_real_name'], '', -1) . '</span><span>' . substr_replace($settings['theme_real_name'], '', 0, 4). '</span></span>' : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name_html_safe'] . '">', '</a>
			</h1>';

			// Theme Variants
			themecustoms_search();

	echo '
		</div>
	</header>';
}

function themecustoms_footer()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
	<footer>
		<div class="inner_wrap">
			<div class="footer_links">
				<ul class="copyright">
					<li class="smf_copyright">', theme_copyright(), '</li>
				</ul>
				<div class="footer-other">
					<a href="', $scripturl, '">', $context['forum_name'], ' &copy; ', date('Y'), '</a>
					<span class="help-links">
						<a href="', $scripturl, '?action=help">', $txt['help'], '</a>', (!empty($modSettings['requireAgreement'])) ? '
						<a href="' . $scripturl . '?action=agreement">' . $txt['terms_and_rules'] . '</a>' : '', '
						<a href="#top_section">', $txt['go_up'], ' ', themecustoms_icon('fa fa-arrow-up'), '</a>
					</span>
					', themecustoms_socials(), '
				</div>
			</div>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
			<p>
				', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '
			</p>';

	echo '
		</div>
	</footer>';
}

function themecustoms_userarea()
{
	global $context, $scripturl, $maintenance, $txt;

	// If the user is logged in, display some things that might be useful.
	if ($context['user']['is_logged'])
	{
		// Firstly, the user's menu
		echo '
			<ul id="top_info">
				<li>
					<a href="', $scripturl, '?action=profile"', !empty($context['self_profile']) || $context['current_action'] == 'unread'  || $context['current_action'] == 'unreadreplies' ? ' class="active"' : '', ' id="profile_menu_top" onclick="return false;">
						', $context['user']['avatar']['image'], '
					</a>
					<div id="profile_menu" class="top_menu"></div>
				</li>';

		// Secondly, PMs if we're doing them
		if ($context['allow_pm'])
			echo '
				<li>
					<a href="', $scripturl, '?action=pm"', !empty($context['self_pm']) ? ' class="active"' : '', ' id="pm_menu_top">', themecustoms_icon('fa fa-envelope'), !empty($context['user']['unread_messages']) ? ' <span class="amt">' . $context['user']['unread_messages'] . '</span>' : '', '</a>
					<div id="pm_menu" class="top_menu scrollable"></div>
				</li>';

		// Thirdly, alerts
		echo '
				<li>
					<a href="', $scripturl, '?action=profile;area=showalerts;u=', $context['user']['id'], '"', !empty($context['self_alerts']) ? ' class="active"' : '', ' id="alerts_menu_top">', themecustoms_icon('fa fa-bell'), !empty($context['user']['alerts']) ? ' <span class="amt">' . $context['user']['alerts'] . '</span>' : '', '</a>
					<div id="alerts_menu" class="top_menu scrollable"></div>
				</li>';

		// A logout button for people without JavaScript.
		echo '
				<li id="nojs_logout">
					<a href="', $scripturl, '?action=logout;', $context['session_var'], '=', $context['session_id'], '">', $txt['logout'], '</a>
					<script>document.getElementById("nojs_logout").style.display = "none";</script>
				</li>';

		// And now we're done.
		echo '
			</ul>';
	}
	// Otherwise they're a guest. Ask them to either register or login.
	/**
	 * @todo use our own login overlay (if needed) because the ajax one breaks the variants
	 */
	elseif (empty($maintenance))
		echo '
			<ul class="floatleft welcome">
				<li>', sprintf($txt[$context['can_register'] ? 'welcome_guest_register' : 'welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return reqOverlayDiv(this.href, ' . JavaScriptEscape($txt['login']) . ');', $scripturl . '?action=signup'), '</li>
			</ul>';
	else
		// In maintenance mode, only login is allowed and don't show OverlayDiv
		echo '
			<ul class="floatleft welcome">
				<li>', sprintf($txt['welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return true;'), '</li>
			</ul>';
}

function themecustoms_socials()
{
	global $settings;

	echo '
	<div class="social_icons">';

	// Facebook
	if (!empty($settings['st_facebook']))
		echo '
		<a href="https://facebook.com/' . $settings['st_facebook'] . '" target="_blank" rel="noopener" class="facebook">', themecustoms_icon('fab fa-facebook-f'), '</a>';

	// Twitter
	if (!empty($settings['st_twitter']))
		echo '
		<a href="https://twitter.com/' . $settings['st_twitter'] . '" target="_blank" rel="noopener" class="twitter">', themecustoms_icon('fab fa-twitter'), '</a>';

	// Instagram
	if (!empty($settings['st_instagram']))
		echo '
		<a href="https://instagram.com/' . $settings['st_instagram'] . '" target="_blank" rel="noopener" class="instagram">', themecustoms_icon('fab fa-instagram'), '</a>';

	// Youtube
	if (!empty($settings['st_youtube']))
		echo '
		<a href="' . $settings['st_youtube'] . '" target="_blank" rel="noopener" class="youtube">', themecustoms_icon('fab fa-youtube'), '</a>';

	// Twitch
	if (!empty($settings['st_twitch']))
		echo '
		<a href="https://twitch.tv/' . $settings['st_twitch'] . '" target="_blank" rel="noopener" class="twitch">', themecustoms_icon('fab fa-twitch'), '</a>';

	// Discord
	if (!empty($settings['st_discord']))
		echo '
		<a href="' . $settings['st_discord'] . '" target="_blank" rel="noopener" class="discord">', themecustoms_icon('fab fa-discord'), '</a>';

	echo '
	</div>';
}

function themecustoms_icon($icon)
{
	return '<i class="' . $icon . '"></i>';
}

function themecustoms_colorpicker()
{
	global $settings, $txt;

	if (!empty($settings['theme_variants']) && empty($settings['disable_user_variant']))
	{
		echo '
		<div class="st_styleswitcher">';
		
		// Theme variants
		foreach ($settings['theme_variants'] as $variant)
			echo '
			<button class="theme-variant-toggle" data-color="', $variant, '">
				', $txt['variant_'. $variant], '
			</button>';

		echo '
		</div>';
	}
}

function themecustoms_search()
{
	global $txt, $context, $scripturl;

	if ($context['allow_search'])
	{
		echo '
			<form class="custom_search" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
				<input type="search" name="search" value="" placeholder="', $txt['search'], '">
				<button><i class="fas fa-search"></i></button>';

		// Using the quick search dropdown?
		$selected = !empty($context['current_topic']) ? 'current_topic' : (!empty($context['current_board']) ? 'current_board' : 'all');

		echo '
				<select name="search_selection">
					<option value="all"', ($selected == 'all' ? ' selected' : ''), '>', $txt['search_entireforum'], ' </option>';

		// Can't limit it to a specific topic if we are not in one
		if (!empty($context['current_topic']))
			echo '
					<option value="topic"', ($selected == 'current_topic' ? ' selected' : ''), '>', $txt['search_thistopic'], '</option>';

		// Can't limit it to a specific board if we are not in one
		if (!empty($context['current_board']))
			echo '
					<option value="board"', ($selected == 'current_board' ? ' selected' : ''), '>', $txt['search_thisboard'], '</option>';

		// Can't search for members if we can't see the memberlist
		if (!empty($context['allow_memberlist']))
			echo '
					<option value="members"', ($selected == 'members' ? ' selected' : ''), '>', $txt['search_members'], ' </option>';

		echo '
				</select>';

		// Search within current topic?
		if (!empty($context['current_topic']))
			echo '
				<input type="hidden" name="sd_topic" value="', $context['current_topic'], '">';

		// If we're on a certain board, limit it to this board ;).
		elseif (!empty($context['current_board']))
			echo '
				<input type="hidden" name="sd_brd" value="', $context['current_board'], '">';

		echo '
				<input type="hidden" name="advanced" value="0">
			</form>';
	}
}

function themecustoms_languageselector()
{
	global $modSettings, $context, $txt;

	if (!empty($modSettings['userLanguage']) && !empty($context['languages']) && count($context['languages']) > 1)
	{
		echo '
			<form id="languages_form" method="get">
				<select id="language_select" name="language" onchange="this.form.submit()">';

		foreach ($context['languages'] as $language)
			echo '
					<option value="', $language['filename'], '"', isset($context['user']['language']) && $context['user']['language'] == $language['filename'] ? ' selected="selected"' : '', '>', str_replace('-utf8', '', $language['name']), '</option>';

		echo '
				</select>
				<noscript>
					<input type="submit" value="', $txt['quick_mod_go'], '">
				</noscript>
			</form>';
	}
}

function themecustoms_avatar($avatar, $memID =  0, $pos = 'left')
{
	global $scripturl, $context;

	if (empty($avatar))
		return;

	return
		(!empty($memID) ? '<a class="avatar" href="' . $scripturl . '?action=profile;u='. $memID . '"' . ($pos == 'right' ? ' style="'. ($context['right_to_left'] ? 'order: 0;' : 'order: 1;') . '"' : ($context['right_to_left'] ? 'order: 1;' : '')) . '>' : '') . 
		'<img class="avatar_dot' . (!empty($class) ? ' '. $class : '') . '" src="' . $avatar . '" alt=""' . ($pos == 'right' ? ' style="'. ($context['right_to_left'] ? 'order: 0;' : 'order: 1;') . '"' : ($context['right_to_left'] ? 'order: 1;' : '')) . ' />' . 
		(!empty($memID) ? '</a>' : '');
}