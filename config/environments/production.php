<?php
/** Production */
ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
/** Disable all file modifications including updates and update notifications */
define('DISALLOW_FILE_MODS', true);
define('GOOGLE_ANALYTICS_PROPERTY_ID', 'UA-61690103-18');
define('ENVIRONMENT', 'production');
