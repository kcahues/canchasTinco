<?php
//session_start();
// Database configuration   
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'u340286682_adminTinco');
define('DB_PASSWORD', '=Uj03A?*');
define('DB_NAME', 'u340286682_canchas_tinco');

// Google API configuration
define('GOOGLE_CLIENT_ID', '13541218880-kdultqf57nnvtlbm0paeki2r557c9mvk.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_OAUTH_SCOPE', 'https://www.googleapis.com/auth/calendar');
define('REDIRECT_URI', 'https://canchastinco.com/admin/mantenimientos/google_calendar_event_sync.php');

// Start session
if(!session_id()) session_start();

// Google OAuth URL
$googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode(GOOGLE_OAUTH_SCOPE) . '&redirect_uri=' . REDIRECT_URI . '&response_type=code&client_id=' . GOOGLE_CLIENT_ID . '&access_type=online';

?>