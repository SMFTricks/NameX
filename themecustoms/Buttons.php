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
	public function normalButtons(&$buttons)
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
}