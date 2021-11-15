<?php

namespace ThemeCustoms;

if (!defined('SMF'))
	die('No direct access...');

class Theme
{
	public function addCSS()
	{
		loadCSSFile('fontawesome.min.css', ['minimize' => false]);
	}
}