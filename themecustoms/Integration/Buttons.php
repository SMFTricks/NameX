<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license GNU GPLv3
 */

namespace ThemeCustoms\Integration;

use ThemeCustoms\Init;

class Buttons
{
	/**
	 * Buttons::normalButtons()
	 *
	 * @param array $buttons It receives the regular buttons for the respetive action.
	 * 
	 * @return void
	 */
	public static function normalButtons(&$buttons) : void
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
				$buttons['notify']['sub_buttons'][$key]['button_status'] = ($sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_1') || $sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_0') ? $txt['notify'] : $txt['unnotify']);

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
	public static function quickButtons(&$output) : void
	{
		global $context, $scripturl, $txt, $modSettings;

		// Like/Unlike button
		// It doesn't make sense to me that you'd like their post if it's ignored, even if you decide to see it.
		if (!$output['is_ignored'] && !empty($modSettings['enable_likes']) && !empty(Init::$_likes_quickbutton))
		{
			$output['quickbuttons'] = array_merge([
				'likes' => [
					'label' => $output['likes']['you'] ? $txt['unlike'] : $txt['like'],
					'icon' => $output['likes']['you'] ? 'unlike' : 'like',
					'class' => 'post_like_button',
					'id' => 'msg_' . $output['id'] . '_quicklikes',
					'href' => $scripturl . '?action=likes;quickbuttonlike;ltype=msg;sa=like;like=' . $output['id'] . ';' . $context['session_var'] . '=' . $context['session_id'],
					'show' => $output['likes']['can_like'],
					'extra_content' => (!empty($output['likes']['count']) ? '
						<span class="amt">
							<a class="buttonlike_count" href="' . $scripturl . '?action=likes;sa=view;ltype=msg;js=1;like=' . $output['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><em style="display: none;">'. $txt['likes'] . '</em>' . $output['likes']['count'] . '</a>
						</span>' : ''),
				],
			], $output['quickbuttons']);
		}
	}
}