<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Integration;

class Packages
{
	/**
	 * @var array The types of packages
	 */
	private $_package_types = [
		'themecustoms_addon'
	];

	/**
	 * Packages::types()
	 * 
	 * Add the addons to the list of modifications
	 * 
	 * @return void
	 */
	public function types() : void
	{
		global $context;

		// Your theme comes first
		$context['modification_types'] = array_merge($this->_package_types, $context['modification_types']);
	}

	/**
	 * Packages::sort()
	 * 
	 * Add the sorting list, which will actually and ultimately add it to the list of modifications
	 * 
	 * @param array $sort_id The type of modification
	 * @return void
	 */
	public function sort(array &$sort_id) : void
	{
		foreach ($this->_package_types as $type)
			$sort_id[$type] = 1;
	}
}