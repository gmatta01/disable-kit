<?php
/**
 * Uninstall script for Disable Kit
 * 
 * @package DisableKit
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Define plugin constants if not already defined
if (!defined('DISABLE_KIT_VERSION')) {
    define('DISABLE_KIT_VERSION', '1.0.0');
}

/**
 * Clean up plugin data on uninstall
 */
class Disable_Kit_Uninstall {
    
    /**
     * Run uninstall process
     */
    public static function uninstall() {
        // Remove plugin options
        delete_option('disable_kit_settings');
        delete_option('wp_strip_settings'); // legacy key from earlier branding
        
        // Remove any transients
        delete_transient('disable_kit_cache');
        
        // Remove user meta if any
        delete_metadata('user', 0, 'disable_kit_dismissed_notices', '', true);
        
        // Flush rewrite rules to clean up any custom rules
        flush_rewrite_rules();
        
        // Clear any cached data
        wp_cache_flush();
        
        // Remove any scheduled events if any were created
        wp_clear_scheduled_hook('disable_kit_cleanup');
        
        // Log uninstall if WP_DEBUG is enabled
        // (intentionally silent in production)
    }
}

// Run the uninstall process
Disable_Kit_Uninstall::uninstall();
