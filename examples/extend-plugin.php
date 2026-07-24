<?php
/**
 * Example: Extending Disable Kit
 *
 * Demonstration only — do not load this file in production.
 * Copy the patterns you need into your own theme or plugin.
 *
 * Feature metadata shape:
 * - name (string, required)
 * - description (string, required)
 * - category (string, required — must exist in disable_kit_categories)
 * - risk (string: low|medium|high)
 * - scope (string: frontend|admin|both)
 * - default (bool)
 * - priority (int)
 * - children (array of feature keys, optional)
 *
 * Custom features appear in the UI and settings. To actually strip them,
 * hook `disable_kit_disable_{$feature_key}` (see Example 3).
 *
 * @package DisableKit
 */

// This file is for demonstration only - do not include in production.

/**
 * Example 1: Adding custom features
 */
add_filter('disable_kit_features', 'disable_kit_example_add_custom_features');

function disable_kit_example_add_custom_features($features) {
    $features['portfolio'] = array(
        'name'        => __('Portfolio Post Type', 'disable-kit'),
        'description' => __('Enable/disable a custom portfolio post type.', 'disable-kit'),
        'category'    => 'custom',
        'risk'        => 'medium',
        'scope'       => 'both',
        'default'     => true,
        'priority'    => 1,
    );

    return $features;
}

/**
 * Example 2: Adding custom categories
 */
add_filter('disable_kit_categories', 'disable_kit_example_add_custom_categories');

function disable_kit_example_add_custom_categories($categories) {
    $categories['custom'] = __('Custom Features', 'disable-kit');
    return $categories;
}

/**
 * Example 3: Custom feature disable implementation
 *
 * Required for any custom feature key. Without this action, the toggle
 * saves but does not change runtime behavior.
 */
add_action('disable_kit_disable_portfolio', 'disable_kit_example_disable_portfolio');

function disable_kit_example_disable_portfolio() {
    add_action('init', function () {
        unregister_post_type('portfolio');
    }, 20);

    add_action('admin_menu', function () {
        remove_menu_page('edit.php?post_type=portfolio');
    });
}

/**
 * Example 4: Programmatic feature checking
 */
function disable_kit_example_check_feature_status() {
    // Preferred helper
    if (true === disable_kit_is_feature_enabled('comments')) {
        // Comments are enabled.
    }

    // Or via the class
    if (class_exists('Disable_Kit') && true === Disable_Kit::is_enabled('rest_api')) {
        // Unauthenticated REST access is still allowed.
    }
}
add_action('init', 'disable_kit_example_check_feature_status');

/**
 * Example 5: React when a setting changes
 *
 * Args: $feature_key, $new_value, $old_value
 */
add_action('disable_kit_feature_toggled', 'disable_kit_example_handle_feature_toggle', 10, 3);

function disable_kit_example_handle_feature_toggle($feature_key, $new_value, $old_value) {
    $flush_features = array('posts', 'pages', 'archives');
    if (in_array($feature_key, $flush_features, true)) {
        flush_rewrite_rules();
    }

    if ('rest_api' === $feature_key) {
        wp_cache_flush();
    }
}

/**
 * Example 6: Validate / force a setting value before save
 *
 * Return the (bool) value that should be stored.
 */
add_filter('disable_kit_validate_setting', 'disable_kit_example_validate_feature_dependencies', 10, 3);

function disable_kit_example_validate_feature_dependencies($value, $feature_key, $settings) {
    // Keep tags disabled when posts are being disabled in the same save.
    if ('tags' === $feature_key && $value) {
        $posts_enabled = isset($settings['posts']) ? (bool) $settings['posts'] : true;
        if (!$posts_enabled) {
            add_settings_error(
                'disable_kit_settings',
                'dependency_error',
                __('Cannot enable tags when posts are disabled.', 'disable-kit'),
                'error'
            );
            return false;
        }
    }

    return (bool) $value;
}
