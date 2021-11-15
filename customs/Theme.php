<?php

namespace ThemeCustoms;

if (!defined('SMF'))
	die('No direct access...');

class Theme
{
	public function addCSS()
	{
		// Font Awesome
		loadCSSFile('fontawesome.min.css', ['minimize' => false]);
	}
}