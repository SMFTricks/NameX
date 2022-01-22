// Check for dark mode enabled
let darkMode = smf_darkmode;

// Local Mode
let localMode = localStorage.getItem('darkMode');

// When we reload the page we want to use the value from the user
let themeMode = null;

let switchThemeMode = (setMode) =>
{
	// Update the dark mode status
	localStorage.setItem('darkMode', setMode);
	// Define the var again cuz... idk actually
	localMode = localStorage.getItem('darkMode');
	// Toggle the theme mode
	if (setMode === 'light')
		document.documentElement.classList.remove('dark-mode');
	else
		document.documentElement.classList.add('dark-mode');

	// Update the mode in the user settings/options
	if (themeMode !== null || (themeMode === null && localMode !== darkMode))
	{
		// Update user option
		smf_setThemeOption('st_theme_mode', setMode, smf_theme_id, smf_session_id, smf_session_var, null);
		// Update the theme Variant with the new color
		darkMode = setMode;
	}
}

if (themeMode === null)
	switchThemeMode((localMode === 'null' || localMode === null) ? darkMode : localMode);

// Toggle theme mode
$('.theme-mode-toggle').click(function() {
	// Switch the theme mode
	switchThemeMode(darkMode === 'dark' ? 'light' : 'dark');
});