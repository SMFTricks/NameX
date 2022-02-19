<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms;

if (!defined('SMF'))
	die('No direct access...');

class Buttons
{
	/**
	 * Buttons::normalButtons()
	 *
	 * @param array $buttons It receives the regular buttons for the respetive action.
	 * 
	 * @return void
	 */
	public static function normalButtons(&$buttons)
	{
		global $context, $txt;

		// Notifiy button
		if (isset($buttons['notify']) && !empty($buttons['notify']['sub_buttons']))
		{
			// Simplify the text
			$buttons['notify']['text'] = ($context[(!empty($context['current_topic']) ? 'topic' : 'board') . '_notification_mode'] > 1 ? 'unnotify' : 'notify');

			// Sub-Buttons
			foreach ($buttons['notify']['sub_buttons'] as $key => $sub_notify)
			{
				// Add the status for the button
				$buttons['notify']['sub_buttons'][$key]['notify_status'] = ($sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_1') || $sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_0') ? $txt['notify'] : $txt['unnotify']);

				// Add active status
				if ($sub_notify['text'] === 'notify_' . (!empty($context['current_topic']) ? 'topic_' : 'board_') . $context[(!empty($context['current_topic']) ? 'topic' : 'board') . '_notification_mode'])
					$buttons['notify']['sub_buttons'][$key]['active'] = true;
			}
		}
	}

	/**
	 * Buttons::quickButtons()
	 * 
	 * @param array $output It receives the message information
	 * 
	 * @return void
	 */
	public static function quickButtons(&$output)
	{
		global $context, $scripturl, $txt, $modSettings;

		// Like/Unlike button
		if (!$output['is_ignored'])
		{
			$output['quickbuttons'] = array_merge([
				'likes' => [
					'label' => $output['likes']['you'] ? $txt['unlike'] : $txt['like'],
					'icon' => $output['likes']['you'] ? 'unlike' : 'like',
					'class' => 'smflikebutton',
					'href' => $scripturl . '?action=likes;quickbuttonlike;ltype=msg;sa=like;like=' . $output['id'] . ';' . $context['session_var'] . '=' . $context['session_id'],
					'show' => $output['likes']['can_like'] && !empty($modSettings['enable_likes']),
					'extra_content' => (!empty($output['likes']['count']) ? '
						<span class="amt">
							<a class="buttonlike_count" href="' . $scripturl . '?action=likes;sa=view;ltype=msg;js=1;like=' . $output['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><em style="display: none;">'. $txt['likes'] . '</em>' . $output['likes']['count'] . '</a>
						</span>' : ''),
				],
			], $output['quickbuttons']);
		}
	}
}