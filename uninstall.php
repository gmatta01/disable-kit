<?php
/**
 * Uninstall script for WP Strip
 * 
 * @package WPStrip
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Define plugin constants if not already defined
if (!defined('WP_STRIP_VERSION')) {
    define('WP_STRIP_VERSION', '1.0.0');
}

/**
 * Clean up plugin data on uninstall
 */
class WP_Strip_Uninstall {
    
    /**
     * Run uninstall process
     */
    public static function uninstall() {
        // Remove plugin options
        delete_option('wp_strip_settings');
        
        // Remove any transients
        delete_transient('wp_strip_cache');
        
        // Remove user meta if any
        delete_metadata('user', 0, 'wp_strip_dismissed_notices', '', true);
        
        // Flush rewrite rules to clean up any custom rules
        flush_rewrite_rules();
        
        // Clear any cached data
        wp_cache_flush();
        
        // Remove any scheduled events if any were created
        wp_clear_scheduled_hook('wp_strip_cleanup');
        
        // Log uninstall if WP_DEBUG is enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Strip: Plugin uninstalled and data cleaned up.');
        }
    }
}

// Run the uninstall process
WP_Strip_Uninstall::uninstall();
