/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license MIT
 */

// Check for dark mode enabled
let darkMode = smf_darkmode;

// Local Mode
let localMode = localStorage.getItem('darkMode');

// Set mode
let themeMode = null;

let switchThemeMode = (setMode) =>
{
	// Update the dark mode status
	localStorage.setItem('darkMode', setMode);

	// Toggle the theme mode
	document.documentElement.dataset.colormode = setMode;

	// Update the mode in the user settings/options
	smf_setThemeOption('st_theme_mode', setMode, smf_theme_id, smf_session_id, smf_session_var, null);

	// Update the current theme mode
	darkMode = setMode;
	themeMode = true;
}

if (themeMode === null && localMode !== 'null' && localMode !== null)
	switchThemeMode(smf_member_id ? darkMode : localMode);

// Toggle theme mode
$('.theme-mode-toggle').click(function() {
	// Switch the theme mode
	switchThemeMode(darkMode === 'dark' ? 'light' : 'dark');

	return false;
});