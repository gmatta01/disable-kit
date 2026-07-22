<?php
/**
 * Admin interface methods for WP Strip
 * 
 * @package WPStrip
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin interface methods (to be added to main class)
 */
trait WP_Strip_Admin {
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('WP Strip', 'wp-strip'),
            __('WP Strip', 'wp-strip'),
            'manage_options',
            'wp-strip',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Initialize admin settings
     */
    public function admin_init() {
        // Register settings
        register_setting(
            'wp_strip_settings',
            $this->options_key,
            array($this, 'sanitize_settings')
        );
        
        // Add settings sections
        $categories = $this->get_feature_categories();
        
        foreach ($categories as $category_key => $category_name) {
            add_settings_section(
                'wp_strip_' . $category_key,
                $category_name,
                array($this, 'section_callback'),
                'wp_strip'
            );
        }
        
        // Add settings fields
        foreach ($this->features as $feature_key => $feature_data) {
            add_settings_field(
                'wp_strip_' . $feature_key,
                $feature_data['name'],
                array($this, 'field_callback'),
                'wp_strip',
                'wp_strip_' . $feature_data['category'],
                array(
                    'feature_key' => $feature_key,
                    'feature_data' => $feature_data
                )
            );
        }
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_wp-strip' !== $hook) {
            return;
        }
        
        wp_enqueue_script(
            'wp-strip-admin',
            WP_STRIP_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            WP_STRIP_VERSION,
            true
        );
        
        wp_enqueue_style(
            'wp-strip-admin',
            WP_STRIP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            WP_STRIP_VERSION
        );
        
        // Localize script
        wp_localize_script('wp-strip-admin', 'wpFeatureManager', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_strip_nonce'),
            'strings' => array(
                'confirmDisable' => __('Are you sure you want to disable this feature? This may affect your site functionality.', 'wp-strip'),
                'confirmEnable' => __('Are you sure you want to enable this feature?', 'wp-strip'),
                'savingChanges' => __('Saving changes...', 'wp-strip'),
                'changesSaved' => __('Changes saved successfully!', 'wp-strip'),
                'errorOccurred' => __('An error occurred. Please try again.', 'wp-strip'),
                'confirmEnableAll' => __('Are you sure you want to enable all features?', 'wp-strip'),
                'confirmDisableAll' => __('Are you sure you want to disable all features? This may break your site functionality.', 'wp-strip'),
                'confirmResetDefaults' => __('Are you sure you want to reset all features to their default state?', 'wp-strip'),
                'unsavedChanges' => __('You have unsaved changes. Are you sure you want to leave?', 'wp-strip'),
                'enabled' => __('Enabled', 'wp-strip'),
                'disabled' => __('Disabled', 'wp-strip'),
                'alreadyDisabled' => __('Already disabled', 'wp-strip'),
                'searchPlaceholder' => __('Search features, descriptions, risks, or scope...', 'wp-strip'),
                'noResults' => __('No features match your search.', 'wp-strip'),
                'sectionEnabled' => __('Section enabled', 'wp-strip'),
                'sectionDisabled' => __('Section disabled', 'wp-strip'),
                'tabListLabel' => __('Feature categories', 'wp-strip'),
                'unsavedIndicator' => __('Unsaved changes', 'wp-strip'),
                'saveChanges' => __('Save Changes', 'wp-strip')
            )
        ));
    }
    
    /**
     * Admin page HTML
     */
    public function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wp-strip'));
        }

        $settings = $this->get_settings();
        $categories = $this->get_feature_categories();
        ?>
        <div class="wrap">
            <div class="wp-strip-shell">
                <header class="wp-strip-topbar">
                    <div class="wp-strip-brand">
                        <span class="wp-strip-logo" aria-hidden="true">FM</span>
                        <div>
                            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                            <p class="description">
                                <?php _e('Turn WordPress features on or off with plain-English guidance, risk labels, and scope tags. Changes affect WordPress at the load level, not just menu visibility.', 'wp-strip'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="wp-strip-summary">
                        <span class="wp-feature-summary-pill"><?php echo esc_html(sprintf(__('Features: %d', 'wp-strip'), count($this->features))); ?></span>
                        <span class="wp-feature-summary-pill wp-feature-summary-pill-risk"><?php _e('Red = high impact', 'wp-strip'); ?></span>
                    </div>
                </header>
            </div>

            <?php $this->show_security_warnings(); ?>

            <form method="post" action="options.php" id="wp-strip-form">
                <?php settings_fields('wp_strip_settings'); ?>

                <div class="wp-strip-layout">
                    <aside class="wp-strip-sidebar" aria-label="<?php echo esc_attr__('Feature categories', 'wp-strip'); ?>">
                        <div class="wp-strip-toolbar">
                            <label class="screen-reader-text" for="wp-feature-search"><?php _e('Search features', 'wp-strip'); ?></label>
                            <input
                                type="search"
                                id="wp-feature-search"
                                class="wp-feature-search"
                                placeholder="<?php echo esc_attr__('Search features, descriptions, risks, or scope...', 'wp-strip'); ?>"
                            >
                            <p class="wp-feature-search-empty" id="wp-feature-search-empty" hidden>
                                <?php _e('No features match your search.', 'wp-strip'); ?>
                            </p>
                        </div>

                        <div class="wp-strip-tabs" role="tablist" aria-label="<?php echo esc_attr__('Feature categories', 'wp-strip'); ?>">
                            <?php $tab_index = 0; ?>
                            <?php foreach ($categories as $category_key => $category_name) : ?>
                                <?php
                                $has_features = false;
                                foreach ($this->features as $feature_data) {
                                    if ($feature_data['category'] === $category_key) {
                                        $has_features = true;
                                        break;
                                    }
                                }

                                if (!$has_features) {
                                    continue;
                                }
                                ?>
                                <button
                                    type="button"
                                    role="tab"
                                    id="tab-<?php echo esc_attr($category_key); ?>"
                                    class="wp-feature-tab<?php echo 0 === $tab_index ? ' is-active' : ''; ?>"
                                    aria-selected="<?php echo 0 === $tab_index ? 'true' : 'false'; ?>"
                                    aria-controls="panel-<?php echo esc_attr($category_key); ?>"
                                    tabindex="<?php echo 0 === $tab_index ? '0' : '-1'; ?>"
                                    data-tab="<?php echo esc_attr($category_key); ?>"
                                >
                                    <?php echo esc_html($category_name); ?>
                                </button>
                                <?php $tab_index++; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="wp-strip-support-card">
                            <h3><?php _e('Need a safe rollback?', 'wp-strip'); ?></h3>
                            <p><?php _e('Use Git commits before large changes so you can quickly restore known-good settings and code.', 'wp-strip'); ?></p>
                        </div>
                    </aside>

                    <main class="wp-strip-content">
                        <div class="wp-strip-actions-bar">
                            <span class="wp-feature-unsaved-indicator" id="wp-feature-unsaved-indicator" hidden>
                                <?php _e('Unsaved changes', 'wp-strip'); ?>
                            </span>
                            <button type="submit" id="wp-strip-submit-top" class="button button-primary">
                                <?php _e('Save Changes', 'wp-strip'); ?>
                            </button>
                        </div>

                        <div class="wp-strip-safety-notice">
                            <h3><?php _e('Safety Information', 'wp-strip'); ?></h3>
                            <p>
                                <?php _e('High-impact features can affect editing, scheduled posts, checkout, feeds, or plugin compatibility. If a feature is already disabled by your theme, another plugin, or WordPress config, its toggle is locked and marked “Already disabled”.', 'wp-strip'); ?>
                            </p>
                            <p>
                                <?php _e('Emergency fallback: if you ever need to bypass this plugin completely, add this line to wp-config.php:', 'wp-strip'); ?>
                                <code>define('DISABLE_WP_STRIP', true);</code>
                            </p>
                        </div>

                                <div class="wp-strip-sections">
                    <?php foreach ($categories as $category_key => $category_name) : ?>
                        <?php
                        $section_features = array();

                        foreach ($this->features as $feature_key => $feature_data) {
                            if ($feature_data['category'] === $category_key) {
                                $section_features[ $feature_key ] = $feature_data;
                            }
                        }

                        if (empty($section_features)) {
                            continue;
                        }

                        $section_all_enabled = true;

                        foreach ($section_features as $section_feature_key => $section_feature_data) {
                            $section_feature_enabled = isset($settings[ $section_feature_key ]) ? (bool) $settings[ $section_feature_key ] : (bool) $section_feature_data['default'];
                            $section_feature_state   = $this->get_feature_runtime_state($section_feature_key, $section_feature_data, $section_feature_enabled);

                            if (!$section_feature_state['locked'] && !$section_feature_enabled) {
                                $section_all_enabled = false;
                            }
                        }
                        ?>
                        <section
                            class="wp-feature-section<?php echo 'writing' === $category_key ? ' is-active' : ''; ?>"
                            role="tabpanel"
                            id="panel-<?php echo esc_attr($category_key); ?>"
                            aria-labelledby="tab-<?php echo esc_attr($category_key); ?>"
                            aria-hidden="<?php echo 'writing' === $category_key ? 'false' : 'true'; ?>"
                            data-category="<?php echo esc_attr($category_key); ?>"
                        >
                            <div class="wp-feature-section-header">
                                <div>
                                    <h2><?php echo esc_html($category_name); ?></h2>
                                    <p><?php echo esc_html($this->get_category_description($category_key)); ?></p>
                                </div>
                                <div class="wp-feature-section-controls">
                                    <span class="wp-feature-section-count"><?php echo esc_html(sprintf(_n('%d feature', '%d features', count($section_features), 'wp-strip'), count($section_features))); ?></span>
                                    <label class="wp-feature-switch wp-feature-switch-section">
                                        <input
                                            type="checkbox"
                                            class="wp-feature-section-toggle"
                                            data-category="<?php echo esc_attr($category_key); ?>"
                                            <?php checked($section_all_enabled); ?>
                                        >
                                        <span class="wp-feature-switch-ui" aria-hidden="true"></span>
                                        <span class="wp-feature-switch-label"><?php _e('Section', 'wp-strip'); ?></span>
                                    </label>
                                </div>
                            </div>

                            <div class="wp-feature-section-content">
                                <?php foreach ($section_features as $feature_key => $feature_data) : ?>
                                    <?php
                                    $is_enabled    = isset($settings[ $feature_key ]) ? (bool) $settings[ $feature_key ] : (bool) $feature_data['default'];
                                    $runtime_state = $this->get_feature_runtime_state($feature_key, $feature_data, $is_enabled);
                                    $effective_on  = $runtime_state['locked'] ? false : $is_enabled;
                                    ?>
                                    <article
                                        class="wp-feature-item wp-feature-risk-<?php echo esc_attr($feature_data['risk']); ?><?php echo $runtime_state['locked'] ? ' is-locked' : ''; ?>"
                                        data-feature-item
                                        data-category="<?php echo esc_attr($category_key); ?>"
                                        data-feature="<?php echo esc_attr($feature_key); ?>"
                                        data-search="<?php echo esc_attr(strtolower($feature_data['name'] . ' ' . $feature_data['description'] . ' ' . $feature_data['risk'] . ' ' . $feature_data['scope'])); ?>"
                                    >
                                        <div class="wp-feature-primary">
                                            <div class="wp-feature-title-row">
                                                <label for="<?php echo esc_attr($feature_key); ?>" class="wp-feature-name">
                                                    <?php echo esc_html($feature_data['name']); ?>
                                                </label>
                                                <div class="wp-feature-meta">
                                                    <span class="wp-feature-badge wp-feature-badge-risk wp-feature-badge-risk-<?php echo esc_attr($feature_data['risk']); ?>">
                                                        <?php echo esc_html($this->get_risk_label($feature_data['risk'])); ?>
                                                    </span>
                                                    <span class="wp-feature-badge wp-feature-badge-scope">
                                                        <?php echo esc_html($this->get_scope_label($feature_data['scope'])); ?>
                                                    </span>
                                                    <?php if ($runtime_state['locked']) : ?>
                                                        <span class="wp-feature-badge wp-feature-badge-locked">
                                                            <?php _e('Already disabled', 'wp-strip'); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <p class="wp-feature-description">
                                                <?php echo esc_html($feature_data['description']); ?>
                                            </p>

                                            <?php if ($runtime_state['reason']) : ?>
                                                <p class="wp-feature-helper-text">
                                                    <?php echo esc_html($runtime_state['reason']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="wp-feature-control">
                                            <input
                                                type="hidden"
                                                name="<?php echo esc_attr($this->options_key); ?>[<?php echo esc_attr($feature_key); ?>]"
                                                value="<?php echo esc_attr($is_enabled ? '1' : '0'); ?>"
                                                class="wp-feature-hidden-value"
                                            >
                                            <label class="wp-feature-switch" for="<?php echo esc_attr($feature_key); ?>">
                                                <input
                                                    type="checkbox"
                                                    id="<?php echo esc_attr($feature_key); ?>"
                                                    class="wp-feature-toggle"
                                                    value="1"
                                                    data-feature="<?php echo esc_attr($feature_key); ?>"
                                                    data-category="<?php echo esc_attr($category_key); ?>"
                                                    <?php checked($effective_on); ?>
                                                    <?php disabled($runtime_state['locked']); ?>
                                                >
                                                <span class="wp-feature-switch-ui" aria-hidden="true"></span>
                                                <span class="screen-reader-text"><?php echo esc_html($feature_data['name']); ?></span>
                                            </label>
                                            <span class="wp-feature-toggle-status">
                                                <?php echo $runtime_state['locked'] ? esc_html__('Already disabled', 'wp-strip') : ($is_enabled ? esc_html__('Enabled', 'wp-strip') : esc_html__('Disabled', 'wp-strip')); ?>
                                            </span>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                        </div>
                    </main>
                </div>

                <?php submit_button(__('Save Changes', 'wp-strip'), 'primary', 'submit', true, array('id' => 'wp-strip-submit', 'style' => 'display:none;')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Get category description.
     *
     * @param string $category_key Category slug.
     * @return string
     */
    private function get_category_description($category_key) {
        $descriptions = array(
            'writing'     => __('Editors, content types, comments, and the tools your team uses to create and manage content.', 'wp-strip'),
            'media'       => __('Embeds, avatars, fonts, images, and browser-facing media behaviour.', 'wp-strip'),
            'speed'       => __('Frontend scripts, CSS, head tags, scheduled tasks, and server-load related features.', 'wp-strip'),
            'security'    => __('Remote access, updates, public registration, and privacy hardening.', 'wp-strip'),
            'admin_ui'    => __('Dashboard cleanup and admin-only screens that can simplify the backend for clients.', 'wp-strip'),
            'feeds'       => __('Syndication formats and cross-site communication features such as RSS and pingbacks.', 'wp-strip'),
            'archives'    => __('Search, date archives, author pages, and attachment pages used for public navigation.', 'wp-strip'),
            'woocommerce' => __('WooCommerce-specific onboarding, admin clutter, checkout behaviour, and storefront scripts.', 'wp-strip'),
        );

        return isset($descriptions[ $category_key ]) ? $descriptions[ $category_key ] : '';
    }

    /**
     * Get display label for risk level.
     *
     * @param string $risk Risk key.
     * @return string
     */
    private function get_risk_label($risk) {
        $labels = array(
            'low'    => __('Safe', 'wp-strip'),
            'medium' => __('Caution', 'wp-strip'),
            'high'   => __('High impact', 'wp-strip'),
        );

        return isset($labels[ $risk ]) ? $labels[ $risk ] : __('Unknown', 'wp-strip');
    }

    /**
     * Get display label for scope.
     *
     * @param string $scope Scope key.
     * @return string
     */
    private function get_scope_label($scope) {
        $labels = array(
            'frontend' => __('Frontend', 'wp-strip'),
            'admin'    => __('Admin', 'wp-strip'),
            'both'     => __('Frontend + Admin', 'wp-strip'),
        );

        return isset($labels[ $scope ]) ? $labels[ $scope ] : __('Mixed', 'wp-strip');
    }

    /**
     * Get runtime state for a feature.
     *
     * @param string $feature_key Feature slug.
     * @param array  $feature_data Feature config.
     * @param bool   $is_enabled Current plugin setting.
     * @return array<string, mixed>
     */
    private function get_feature_runtime_state($feature_key, $feature_data, $is_enabled) {
        $state = array(
            'locked' => false,
            'reason' => '',
        );

        if (!$is_enabled) {
            return $state;
        }

        switch ($feature_key) {
            case 'posts':
                if (!post_type_exists('post')) {
                    $state['locked'] = true;
                    $state['reason'] = __('The Posts content type has already been removed by another plugin or theme.', 'wp-strip');
                }
                break;

            case 'pages':
                if (!post_type_exists('page')) {
                    $state['locked'] = true;
                    $state['reason'] = __('The Pages content type has already been removed by another plugin or theme.', 'wp-strip');
                }
                break;

            case 'attachments':
                if (!post_type_exists('attachment')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Media attachments are already disabled elsewhere.', 'wp-strip');
                }
                break;

            case 'categories':
                if (!taxonomy_exists('category')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Post categories are already disabled elsewhere.', 'wp-strip');
                }
                break;

            case 'tags':
                if (!taxonomy_exists('post_tag')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Post tags are already disabled elsewhere.', 'wp-strip');
                }
                break;

            case 'user_registration':
                if (!get_option('users_can_register')) {
                    $state['locked'] = true;
                    $state['reason'] = __('User registration is already turned off in Settings > General.', 'wp-strip');
                }
                break;

            case 'xmlrpc':
                if (false === apply_filters('xmlrpc_enabled', true)) {
                    $state['locked'] = true;
                    $state['reason'] = __('XML-RPC is already disabled by WordPress config or another plugin.', 'wp-strip');
                }
                break;

            case 'site_editor':
                if (function_exists('wp_is_block_theme') && !wp_is_block_theme()) {
                    $state['locked'] = true;
                    $state['reason'] = __('The Full Site Editor is not available because the active theme is not a block theme.', 'wp-strip');
                }
                break;

            case 'customizer':
                if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
                    $state['locked'] = true;
                    $state['reason'] = __('Block themes use the Site Editor instead of the Theme Customizer.', 'wp-strip');
                }
                break;

            case 'theme_editor':
            case 'plugin_editor':
                if ((defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) || (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)) {
                    $state['locked'] = true;
                    $state['reason'] = __('File editing is already blocked in WordPress config.', 'wp-strip');
                }
                break;

            case 'revisions':
                if (defined('WP_POST_REVISIONS') && false === WP_POST_REVISIONS) {
                    $state['locked'] = true;
                    $state['reason'] = __('Post revisions are already disabled in wp-config.php.', 'wp-strip');
                }
                break;

            case 'comments':
                if (!post_type_supports('post', 'comments') && !post_type_supports('page', 'comments')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Comments are already disabled for the main content types.', 'wp-strip');
                }
                break;
        }

        return $state;
    }
    
    /**
     * Settings section callback
     */
    public function section_callback($args) {
        // Section description can be added here if needed
    }
    
    /**
     * Settings field callback
     */
    public function field_callback($args) {
        // Fields are rendered in admin_page method for better control
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        foreach ($this->features as $feature_key => $feature_data) {
            $sanitized[$feature_key] = isset($input[$feature_key]) ? (bool) $input[$feature_key] : false;
        }
        
        return $sanitized;
    }
    
    /**
     * AJAX handler for toggling features
     */
    public function ajax_toggle_feature() {
        // Verify nonce and check POST keys
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wp_strip_nonce')) {
            wp_die(__('Security check failed.', 'wp-strip'));
        }
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'wp-strip'));
        }
        // Validate and sanitize input
        if (!isset($_POST['feature'], $_POST['enabled'])) {
            wp_send_json_error(__('Missing required data.', 'wp-strip'));
        }
        $feature_key = sanitize_key(wp_unslash($_POST['feature']));
        $enabled = (bool) intval(wp_unslash($_POST['enabled']));
        // Validate feature key
        if (!isset($this->features[$feature_key])) {
            wp_send_json_error(__('Invalid feature.', 'wp-strip'));
        }
        // Update settings
        $settings = $this->get_settings();
        $settings[$feature_key] = $enabled;
        $this->update_settings($settings);
        wp_send_json_success(array(
            'message' => $enabled ? __('Feature enabled.', 'wp-strip') : __('Feature disabled.', 'wp-strip'),
            'feature' => $feature_key,
            'enabled' => $enabled
        ));
    }
    
    /**
     * Show security warning banners for dangerous feature toggles
     */
    public function show_security_warnings() {
        $settings = $this->get_settings();
        $warnings = array();
        
        if (empty($settings['update_checks'])) {
            $warnings[] = array(
                'type' => 'warning',
                'message' => __('<strong>Update checks are disabled.</strong> Your site will not check for WordPress, plugin, or theme updates. This is a security risk — you may miss critical security patches. Consider enabling this or setting up a manual update monitoring process.', 'wp-strip')
            );
        }
        
        if (empty($settings['wp_org_requests'])) {
            $warnings[] = array(
                'type' => 'warning',
                'message' => __('<strong>WordPress.org communication is blocked.</strong> Your site cannot reach WordPress.org for updates, translations, or plugin/theme information. Security patches may not be detected. Consider enabling this or using an alternative update monitoring solution.', 'wp-strip')
            );
        }
        
        foreach ($warnings as $warning) {
            $class = 'notice notice-' . esc_attr($warning['type']) . ' is-dismissible';
            printf('<div class="%s"><p>%s</p></div>', esc_attr($class), wp_kses_post($warning['message']));
        }
    }

    /**
     * Get feature categories
     */
    private function get_feature_categories() {
        return array(
            'writing'     => __('Writing & Content', 'wp-strip'),
            'media'       => __('Media & Embeds', 'wp-strip'),
            'speed'       => __('Site Speed', 'wp-strip'),
            'security'    => __('Security & Privacy', 'wp-strip'),
            'admin_ui'    => __('Admin Interface', 'wp-strip'),
            'feeds'       => __('Feeds & Connections', 'wp-strip'),
            'archives'    => __('Search & Archives', 'wp-strip'),
            'woocommerce' => __('WooCommerce', 'wp-strip')
        );
    }
}
