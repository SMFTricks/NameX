<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

 /**
 * Initialize the customs template along with the theme version and the custom files
 */
function template_customs_init()
{
	global $settings;

	// The version this template/theme is for. This should probably be the version of SMF it was created for.
	$settings['theme_version'] = '2.1';

	// This defines the formatting for the page indexes used throughout the forum.
	$settings['page_index'] = themecustoms_page_index();

	// Integration hooks are always fun.
	add_integration_function('integrate_load_theme', 'ThemeCustoms\Config\Integration::initialize#', false, '$themedir/themecustoms/Config/Integration.php');
}

/** 
 * The pagination style
 */
function themecustoms_page_index()
{
	global $txt;

	return array(
		'extra_before' => '<span class="pagination_container"><span class="pages">' . $txt['pages'] . '</span>',
		'previous_page' => themecustoms_icon('fa fa-angle-left'),
		'current_page' => '<span class="current_page">%1$d</span> ',
		'page' => '<a class="nav_page" href="{URL}">%2$s</a> ',
		'expand_pages' => '<span class="expand_pages" onclick="expandPages(this, {LINK}, {FIRST_PAGE}, {LAST_PAGE}, {PER_PAGE});"> ... </span>',
		'next_page' => themecustoms_icon('fa fa-angle-right'),
		'extra_after' => '</span>',
	);
}

/**
 * Social icons
 */
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

	// TikTok
	if (!empty($settings['st_tiktok']))
		echo '
		<a href="https://tiktok.com/@' . $settings['st_tiktok'] . '" target="_blank" rel="noopener" class="tiktok">', themecustoms_icon('fab fa-tiktok'), '</a>';

	// Twitch
	if (!empty($settings['st_twitch']))
		echo '
		<a href="https://twitch.tv/' . $settings['st_twitch'] . '" target="_blank" rel="noopener" class="twitch">', themecustoms_icon('fab fa-twitch'), '</a>';

	// Discord
	if (!empty($settings['st_discord']))
		echo '
		<a href="' . $settings['st_discord'] . '" target="_blank" rel="noopener" class="discord">', themecustoms_icon('fab fa-discord'), '</a>';

	// Steam
	if (!empty($settings['st_steam']))
		echo '
		<a href="' . $settings['st_steam'] . '" target="_blank" rel="noopener" class="steam">', themecustoms_icon('fab fa-steam-symbol'), '</a>';

	// GitHub
	if (!empty($settings['st_github']))
		echo '
		<a href="' . $settings['st_github'] . '" target="_blank" rel="noopener" class="github">', themecustoms_icon('fab fa-github'), '</a>';

	// LinkedIn
	if (!empty($settings['st_linkedin']))
		echo '
		<a href="' . $settings['st_linkedin'] . '" target="_blank" rel="noopener" class="linkedin">', themecustoms_icon('fab fa-linkedin'), '</a>';

	// RSS
	if (!empty($settings['st_rss_url']))
		echo '
		<a href="' . $settings['st_rss_url'] . '" target="_blank" rel="noopener" class="rss">', themecustoms_icon('fas fa-rss'), '</a>';

	echo '
	</div>';
}

/**
 * Icons
 */
function themecustoms_icon($icon)
{
	return '<i class="' . $icon . '"></i>';
}

/**
 * Search form
*/
function themecustoms_search()
{
	global $txt, $context, $scripturl;

	if ($context['allow_search'])
	{
		echo '
			<form class="custom_search" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
				<input type="search" name="search" value="" placeholder="', $txt['search'], '">
				<button>', themecustoms_icon('fas fa-search'), '</button>';

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

/**
 * Language selector form
 */
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

/**
 * Avatar display
 */
function themecustoms_avatar($avatar, $memID =  0)
{
	global $scripturl;

	// No avatar? Go away
	if (empty($avatar))
		return;

	$user_avatar = '';

	// Build a link?
	if (!empty($memID))
		$user_avatar .= '<a class="avatar" href="' . $scripturl . '?action=profile;u=' . $memID . '">';

	// Show the image
	$user_avatar .= '<img class="avatar avatar_dot" src="' . $avatar . '" alt="">';

	// Close the link
	if (!empty($memID))
		$user_avatar .= '</a>';

	return
		$user_avatar;
}

/**
 * Theme info
 */
function themecustoms_themeinfo()
{
	global $settings, $txt;

	echo '
		<div class="st-theme-information"><!-- Theme information -->
			<div class="block"><!-- Theme Block -->
				<h4>', $txt['st_themeinfo_details'], '</h4>
				<div class="block-content">
					<div class="icon">
						', themecustoms_icon('fas fa-code-merge'), '
					</div>
					<div class="details">
						<strong>', $txt['st_themeinfo_name'], ':</strong>
						<span>', (empty($settings['theme_support']['smf_site_id']) ? 
							$settings['theme_name'] : 
							'<a href="https://custom.simplemachines.org/index.php?theme=' . $settings['theme_support']['smf_site_id'] . '">' . $settings['theme_name'] . '</a>'), '
						</span><br>

						<strong>', $txt['st_themeinfo_author'], ':</strong>
						<span>', (empty($settings['theme_author']['smf_id']) ? 
							$settings['theme_author']['name'] : 
							'<a href="https://simplemachines.org/community/index.php?action=profile;u=' . $settings['theme_author']['smf_id'] . '">' . $settings['theme_author']['name'] . '</a>'), '
						</span><br>

						<strong>', $txt['st_themeinfo_version'] , ':</strong>
						<span>', $settings['theme_real_version'], '</span><br>

						<strong>', $txt['st_themeinfo_smfversion'], ':</strong>
						<span>', $settings['theme_version'], '</span>
					</div>
				</div>
			</div><!-- Theme Block -->';

		// Check for SMF link or External link
		if (!empty($settings['theme_support']['smf_support_topic']) || !empty($settings['theme_support']['custom_support_url']))
		{
				echo '
			<div class="block"><!-- Theme Block -->
				<h4>', $txt['st_themeinfo_support'], '</h4>
				<div class="block-content">
					<div class="icon">
						', themecustoms_icon('fas fa-question'), '
					</div>
					<div class="details">';

				// Support topic
				if (!empty($settings['theme_support']['smf_support_topic']))
					echo '
						<strong>', $txt['st_themeinfo_support_topic'], ':</strong>
						<a href="https://simplemachines.org/community/index.php?topic='. $settings['theme_support']['smf_support_topic']. '.0">', $txt['st_themeinfo_support_topic_desc'], '</a><br>';

				// Review Link
				if (!empty($settings['theme_support']['smf_site_id']))
					echo '
						<strong>', $txt['st_themeinfo_review'], ':</strong>
						<a href="https://custom.simplemachines.org/index.php?theme=' . $settings['theme_support']['smf_site_id'] . '">', $txt['st_themeinfo_review_desc'], '</a><br>';

				// External
				if (!empty($settings['theme_support']['custom_support_url']))
					echo '
						<strong>', $txt['st_themeinfo_support_board'], ':</strong>
						<a href="', $settings['theme_support']['custom_support_url'], '">', $txt['st_themeinfo_support_board_desc'], '</a><br>';

				// GitHub
				if (!empty($settings['theme_support']['github_url']))
					echo '
						<strong>', $txt['st_themeinfo_github'] , ':</strong>
						<a href="', $settings['theme_support']['github_url'], '">', $txt['st_themeinfo_github_desc'], '</a><br>';
				echo '
					</div>
				</div>
			</div><!-- Theme Block -->';
		}

	echo '
		</div>';
}

/**
 * Custom Links
 */
function themecustoms_customlinks()
{
	global $settings;

	echo '
	<div class="st_custom_links">';

	// Link 1
	if (!empty($settings['st_custom_link1']) && !empty($settings['st_custom_link1_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link1'], '" target="_blank" rel="noopener">', $settings['st_custom_link1_title'], '</a>
		</span>';

	// Link 2
	if (!empty($settings['st_custom_link2']) && !empty($settings['st_custom_link2_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link2'], '" target="_blank" rel="noopener">', $settings['st_custom_link2_title'], '</a>
		</span>';

	// Link 3
	if (!empty($settings['st_custom_link3']) && !empty($settings['st_custom_link3_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link3'], '" target="_blank" rel="noopener">', $settings['st_custom_link3_title'], '</a>
		</span>';

	// Link 4
	if (!empty($settings['st_custom_link4']) && !empty($settings['st_custom_link4_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link4'], '" target="_blank" rel="noopener">', $settings['st_custom_link4_title'], '</a>
		</span>';

	// Link 5
	if (!empty($settings['st_custom_link5']) && !empty($settings['st_custom_link5_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link5'], '" target="_blank" rel="noopener">', $settings['st_custom_link5_title'], '</a>
		</span>';

	// Link 6
	if (!empty($settings['st_custom_link6']) && !empty($settings['st_custom_link6_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link6'], '" target="_blank" rel="noopener">', $settings['st_custom_link6_title'], '</a>
		</span>';

	// Link 7
	if (!empty($settings['st_custom_link7']) && !empty($settings['st_custom_link7_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link7'], '" target="_blank" rel="noopener">', $settings['st_custom_link7_title'], '</a>
		</span>';

	// Link 8
	if (!empty($settings['st_custom_link8']) && !empty($settings['st_custom_link8_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link8'], '" target="_blank" rel="noopener">', $settings['st_custom_link8_title'], '</a>
		</span>';

	// Link 9
	if (!empty($settings['st_custom_link9']) && !empty($settings['st_custom_link9_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link9'], '" target="_blank" rel="noopener">', $settings['st_custom_link9_title'], '</a>
		</span>';

	// Link 10
	if (!empty($settings['st_custom_link10']) && !empty($settings['st_custom_link10_title']))
		echo '
		<span>
			<a href="', $settings['st_custom_link10'], '" target="_blank" rel="noopener">', $settings['st_custom_link10_title'], '</a>
		</span>';

	echo '
	</div>';
}