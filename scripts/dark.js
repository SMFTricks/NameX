// Check for dark mode enabled
let darkMode = smf_darkmode;

// Local Mode
let localMode = localStorage.getItem('darkMode');
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
	switchThemeMode(localMode);

// Toggle theme mode
$('.theme-mode-toggle').click(function() {
	// Switch the theme mode
	switchThemeMode(darkMode === 'dark' ? 'light' : 'dark');
});