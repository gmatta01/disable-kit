<?php
/**
 * Test file for WP Strip
 * 
 * This file helps test the plugin functionality
 * 
 *  WPStrip
 */

// This file is for testing only - do not use in production

/**
 * Test plugin functionality
 */
function wp_strip_test() {
    if (!class_exists('WP_Strip')) {
        return 'Plugin not loaded';
    }
    
    $feature_manager = WP_Strip::get_instance();
    $results = array();
    
    // Test 1: Check if plugin is loaded
    $results['plugin_loaded'] = class_exists('WP_Strip') ? 'PASS' : 'FAIL';
    
    // Test 2: Check settings
    $settings = $feature_manager->get_settings();
    $results['settings_accessible'] = is_array($settings) ? 'PASS' : 'FAIL';
    
    // Test 3: Check feature status
    $posts_enabled = $feature_manager->is_feature_enabled('posts');
    $results['posts_status_check'] = is_bool($posts_enabled) ? 'PASS' : 'FAIL';
    
    // Test 4: Check if post type exists when enabled
    if ($posts_enabled) {
        $post_type_exists = post_type_exists('post');
        $results['post_type_exists_when_enabled'] = $post_type_exists ? 'PASS' : 'FAIL';
    } else {
        $post_type_exists = post_type_exists('post');
        $results['post_type_disabled'] = !$post_type_exists ? 'PASS' : 'FAIL';
    }
    
    // Test 5: Check Gutenberg status
    $gutenberg_enabled = $feature_manager->is_feature_enabled('gutenberg');
    $results['gutenberg_status'] = is_bool($gutenberg_enabled) ? 'PASS' : 'FAIL';
    
    // Test 6: Check if block editor is disabled when Gutenberg is disabled
    if (!$gutenberg_enabled) {
        $block_editor_disabled = !use_block_editor_for_post_type('post');
        $results['block_editor_disabled'] = $block_editor_disabled ? 'PASS' : 'FAIL';
    }
    
    // Test 7: Check REST API status
    $rest_api_enabled = $feature_manager->is_feature_enabled('rest_api');
    $results['rest_api_status'] = is_bool($rest_api_enabled) ? 'PASS' : 'FAIL';
    
    // Test 8: Check comments status
    $comments_enabled = $feature_manager->is_feature_enabled('comments');
    $results['comments_status'] = is_bool($comments_enabled) ? 'PASS' : 'FAIL';
    
    return $results;
}

/**
 * Display test results - DISABLED
 */
function wp_strip_display_test_results() {
    // Test results display disabled per user request
    return;
}

// Add test results to admin if WP_DEBUG is enabled
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('admin_notices', 'wp_strip_display_test_results');
}

/**
 * Debug function to show current feature states
 */
function wp_strip_debug_info() {
    if (!current_user_can('manage_options') || !isset($_GET['wp_feature_debug'])) {
        return;
    }
    
    if (!class_exists('WP_Strip')) {
        wp_die('WP Strip not loaded');
    }
    
    $feature_manager = WP_Strip::get_instance();
    $settings = $feature_manager->get_settings();
    
    echo '<h2>WP Strip Debug Info</h2>';
    echo '<h3>Current Settings:</h3>';
    echo '<pre>' . print_r($settings, true) . '</pre>';
    
    echo '<h3>Post Types:</h3>';
    $post_types = get_post_types(array('public' => true), 'names');
    echo '<pre>' . print_r($post_types, true) . '</pre>';
    
    echo '<h3>Taxonomies:</h3>';
    $taxonomies = get_taxonomies(array('public' => true), 'names');
    echo '<pre>' . print_r($taxonomies, true) . '</pre>';
    
    echo '<h3>Block Editor Status:</h3>';
    echo 'Block editor for posts: ' . (use_block_editor_for_post_type('post') ? 'Enabled' : 'Disabled') . '<br>';
    echo 'Block editor for pages: ' . (use_block_editor_for_post_type('page') ? 'Enabled' : 'Disabled') . '<br>';
    
    wp_die();
}
add_action('init', 'wp_strip_debug_info');

/**
 * Add debug link to admin bar
 */
function wp_strip_debug_link($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'wp-strip-debug',
        'title' => 'WP Strip Debug',
        'href' => admin_url('?wp_feature_debug=1'),
        'meta' => array('target' => '_blank')
    ));
}
add_action('admin_bar_menu', 'wp_strip_debug_link', 999);
