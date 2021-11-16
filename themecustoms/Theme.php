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
	protected $_lib_options;

	public function __construct()
	{
		// Include any libraries or frameworks
		$this->libOptions();

		// Load the CSS
		$this->addCSS();

		// Load the JS
		$this->addJS();
	}

	public function libOptions()
	{
		$this->_lib_options = [
			// FontAwesome
			'fontawesome' => [
				'include' => true,
				'css' => [
					'file' => 'https://use.fontawesome.com/releases/v5.15.4/css/all.css',
					'external' => true,
					'attributes' => [
						'integrity' => 'sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm',
						'crossorigin' => 'anonymous',
					],
				],
			],
			// Bootstrap
			'bootstrap' => [
				'include' => false,
				'css' => [
					'minified' => true,
				],
				'js' => [
					'file' => 'bootstrap.bundle',
					'minified' => true,
				],
			],
			// Animate
			'animate' => [
				'include' => false,
				'css' => [
					'minified' => true,
				],
			],
		];
	}

	public function addCSS()
	{
		// Add the libraries first
		$order = -200;
		foreach ($this->_lib_options as $file => $options)
		{
			if (!empty($options['include']) && !empty($options['css']))
				loadCSSFile(
					(!empty($options['css']['file']) ? (!empty($options['css']['external']) ? $options['css']['file'] : ($options['css']['file'] . (!empty($options['css']['minified']) ? '.min' : '') . '.css')) : ($file . (!empty($options['css']['minified']) ? '.min' : '') . '.css')),
					[
						'minimize'  => !empty($options['css']['minimize']),
						'external' => !empty($options['css']['external']),
						'attributes' => !empty($options['css']['attributes']) ? $options['css']['attributes'] : [],
						'order_pos' => $order++,
					],
					'smftheme_css_' . $file
				);
		}
	}

	public function addJS()
	{
		// Add the libraries first
		foreach ($this->_lib_options as $file => $options)
		{
			if (!empty($options['include']) && !empty($options['js']))
				loadJavaScriptFile(
					(!empty($options['js']['file']) ? (!empty($options['js']['external']) ? $options['js']['file'] : ($options['js']['file'] . (!empty($options['js']['minified']) ? '.min' : '') . '.js')) : ($file . (!empty($options['js']['minified']) ? '.min' : '') . '.js')),
					[
						'defer'  =>  !empty($options['js']['defer']),
						'async'  =>  !empty($options['js']['async']),
						'minimize'  =>  !empty($options['js']['minimize']),
						'external' => !empty($options['js']['external']),
						'attributes' => !empty($options['js']['attributes']) ? $options['js']['attributes'] : [],
					],
					'smftheme_js_' . $file
				);
		}
	}
}