<?php
/**
 * Example: Extending WP Strip
 * 
 * This file demonstrates how to extend the WP Strip
 * with custom features and functionality.
 * 
 * @package WPStrip
 */

// This file is for demonstration only - do not include in production

/**
 * Example 1: Adding custom features
 */
add_filter('wp_strip_features', 'add_custom_features');

function add_custom_features($features) {
    // Add WooCommerce features if WooCommerce is installed
    if (class_exists('WooCommerce')) {
        $features['woocommerce_cart'] = array(
            'name' => __('WooCommerce Cart', 'wp-strip'),
            'description' => __('Enable/disable WooCommerce cart functionality.', 'wp-strip'),
            'category' => 'ecommerce',
            'default' => true,
            'priority' => 1
        );
        
        $features['woocommerce_checkout'] = array(
            'name' => __('WooCommerce Checkout', 'wp-strip'),
            'description' => __('Enable/disable WooCommerce checkout process.', 'wp-strip'),
            'category' => 'ecommerce',
            'default' => true,
            'priority' => 1
        );
    }
    
    // Add custom post type feature
    $features['portfolio'] = array(
        'name' => __('Portfolio Post Type', 'wp-strip'),
        'description' => __('Enable/disable custom portfolio post type.', 'wp-strip'),
        'category' => 'custom',
        'default' => false,
        'priority' => 1
    );
    
    return $features;
}

/**
 * Example 2: Adding custom categories
 */
add_filter('wp_strip_categories', 'add_custom_categories');

function add_custom_categories($categories) {
    $categories['ecommerce'] = __('E-commerce', 'wp-strip');
    $categories['custom'] = __('Custom Features', 'wp-strip');
    return $categories;
}

/**
 * Example 3: Custom feature implementation
 */
add_action('wp_strip_disable_portfolio', 'disable_portfolio_feature');

function disable_portfolio_feature() {
    // Remove portfolio post type
    add_action('init', function() {
        unregister_post_type('portfolio');
    }, 20);
    
    // Remove portfolio from admin menu
    add_action('admin_menu', function() {
        remove_menu_page('edit.php?post_type=portfolio');
    });
}

/**
 * Example 4: Programmatic feature checking
 */
function check_feature_status_example() {
    if (class_exists('WP_Strip')) {
        $feature_manager = WP_Strip::get_instance();
        
        // Check if comments are enabled
        if ($feature_manager->is_feature_enabled('comments')) {
            // Comments are enabled, do something
            add_action('wp_head', 'add_comment_styles');
        }
        
        // Check if REST API is enabled
        if (!$feature_manager->is_feature_enabled('rest_api')) {
            // REST API is disabled, maybe show a notice
            add_action('admin_notices', function() {
                echo '<div class="notice notice-warning"><p>REST API is disabled by WP Strip.</p></div>';
            });
        }
        
        // Get all disabled features
        $disabled = $feature_manager->get_disabled_features();
        if (!empty($disabled)) {
            // Log disabled features for debugging
            error_log('Disabled features: ' . implode(', ', $disabled));
        }
    }
}
add_action('init', 'check_feature_status_example');

/**
 * Example 5: Custom admin notice based on feature status
 */
add_action('admin_notices', 'feature_manager_custom_notices');

function feature_manager_custom_notices() {
    if (!class_exists('WP_Strip')) {
        return;
    }
    
    $feature_manager = WP_Strip::get_instance();
    
    // Warn if critical features are disabled
    $critical_features = array('posts', 'pages', 'rest_api');
    $disabled_critical = array();
    
    foreach ($critical_features as $feature) {
        if (!$feature_manager->is_feature_enabled($feature)) {
            $disabled_critical[] = $feature;
        }
    }
    
    if (!empty($disabled_critical)) {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>WP Strip Warning:</strong> Critical features are disabled: ' . implode(', ', $disabled_critical) . '</p>';
        echo '</div>';
    }
}

/**
 * Example 6: Custom feature toggle hook
 */
add_action('wp_strip_feature_toggled', 'handle_feature_toggle', 10, 3);

function handle_feature_toggle($feature_key, $old_value, $new_value) {
    // Log feature changes
    error_log(sprintf(
        'Feature "%s" changed from %s to %s',
        $feature_key,
        $old_value ? 'enabled' : 'disabled',
        $new_value ? 'enabled' : 'disabled'
    ));
    
    // Flush rewrite rules when certain features change
    $flush_features = array('posts', 'pages', 'archives');
    if (in_array($feature_key, $flush_features)) {
        flush_rewrite_rules();
    }
    
    // Clear cache when REST API status changes
    if ($feature_key === 'rest_api') {
        wp_cache_flush();
    }
}

/**
 * Example 7: Custom bulk actions
 */
add_action('wp_strip_bulk_actions', 'add_custom_bulk_actions');

function add_custom_bulk_actions() {
    ?>
    <button type="button" onclick="enableContentFeatures()" class="button">
        <?php _e('Enable All Content Features', 'wp-strip'); ?>
    </button>
    <button type="button" onclick="disableApiFeatures()" class="button">
        <?php _e('Disable All API Features', 'wp-strip'); ?>
    </button>
    
    <script>
    function enableContentFeatures() {
        if (confirm('Enable all content-related features?')) {
            jQuery('input[data-category="content"]').prop('checked', true).trigger('change');
        }
    }
    
    function disableApiFeatures() {
        if (confirm('Disable all API features? This may affect integrations.')) {
            jQuery('input[data-category="apis"]').prop('checked', false).trigger('change');
        }
    }
    </script>
    <?php
}

/**
 * Example 8: Feature dependencies
 */
add_filter('wp_strip_validate_setting', 'validate_feature_dependencies', 10, 3);

function validate_feature_dependencies($is_valid, $feature_key, $new_value) {
    // If trying to enable tags but posts are disabled
    if ($feature_key === 'tags' && $new_value) {
        $feature_manager = WP_Strip::get_instance();
        if (!$feature_manager->is_feature_enabled('posts')) {
            add_settings_error(
                'wp_strip_settings',
                'dependency_error',
                __('Cannot enable tags when posts are disabled.', 'wp-strip'),
                'error'
            );
            return false;
        }
    }
    
    return $is_valid;
}
