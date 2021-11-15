<?php

/**
 * @package ST Theme
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2021, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms;

if (!defined('SMF'))
	die('No direct access...');

class Theme
{
	public function addCSS()
	{
		// Font Awesome
		loadCSSFile('https://use.fontawesome.com/releases/v5.15.4/css/all.css', ['external' => true, 'minimize' => false, 'attributes' => [
			'integrity' => 'sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm',
			'crossorigin' => 'anonymous'
		]]);
	}
}