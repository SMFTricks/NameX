<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license GNU GPLv3
 */

namespace ThemeCustoms\Integration;

class Packages
{
	/**
	 * Packages::types()
	 * 
	 * Add the addons to the list of modifications
	 * 
	 * @return void
	 */
	public static function types() : void
	{
		global $context;

		// Your theme comes first
		$context['modification_types'] = array_merge(['themecustoms_addon'], $context['modification_types']);
	}

	/**
	 * Packages::sort()
	 * 
	 * Add the sorting list, which will actually and ultimately add it to the list of modifications
	 * 
	 * @param array $sort_id The type of modification
	 * @return void
	 */
	public static function sort(array &$sort_id) : void
	{
		$sort_id['themecustoms_addon'] = 1;
	}
}