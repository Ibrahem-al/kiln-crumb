#!/usr/bin/env bash
# Sets up the demo site on a fresh WordPress install with this theme.
# Run from the WordPress root:   bash /path/to/scripts/setup-content.sh
# Needs WP-CLI (`wp`). In Local (localwp.com): right-click the site -> "Open site shell".
set -euo pipefail
WP="${WP:-wp}"

$WP theme activate kiln-crumb
$WP rewrite structure '/%postname%/' --hard
$WP eval 'require_once get_theme_file_path( "inc/demo-content.php" ); kc_create_demo_content();'

echo "Done. Visit the home page, /wholesale/, /llms.txt and /wp-sitemap.xml."
