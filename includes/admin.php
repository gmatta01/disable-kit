<?php
/**
 * Admin interface methods for Disable Kit
 * 
 * @package DisableKit
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin interface methods (to be added to main class)
 */
trait Disable_Kit_Admin {
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Disable Kit', 'disable-kit'),
            __('Disable Kit', 'disable-kit'),
            'manage_options',
            'disable-kit',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Initialize admin settings
     */
    public function admin_init() {
        // Register settings
        register_setting(
            'disable_kit_settings',
            $this->options_key,
            array($this, 'sanitize_settings')
        );
        
        // Add settings sections
        $categories = $this->get_feature_categories();
        
        foreach ($categories as $category_key => $category_name) {
            add_settings_section(
                'disable_kit_' . $category_key,
                $category_name,
                array($this, 'section_callback'),
                'disable_kit'
            );
        }
        
        // Add settings fields
        foreach ($this->features as $feature_key => $feature_data) {
            add_settings_field(
                'disable_kit_' . $feature_key,
                $feature_data['name'],
                array($this, 'field_callback'),
                'disable_kit',
                'disable_kit_' . $feature_data['category'],
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
        if ('settings_page_disable-kit' !== $hook) {
            return;
        }
        
        wp_enqueue_script(
            'disable-kit-admin',
            DISABLE_KIT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            DISABLE_KIT_VERSION,
            true
        );
        
        wp_enqueue_style(
            'disable-kit-admin',
            DISABLE_KIT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            DISABLE_KIT_VERSION
        );
        
        // Localize script
        wp_localize_script('disable-kit-admin', 'disableKit', array(
            'strings' => array(
                'confirmDisable' => __('Are you sure you want to disable this feature? This may affect your site functionality.', 'disable-kit'),
                'savingChanges' => __('Saving changes...', 'disable-kit'),
                'changesSaved' => __('Changes saved successfully!', 'disable-kit'),
                'unsavedChanges' => __('You have unsaved changes. Are you sure you want to leave?', 'disable-kit'),
                'enabled' => __('Enabled', 'disable-kit'),
                'disabled' => __('Disabled', 'disable-kit'),
                'saveChanges' => __('Save Changes', 'disable-kit'),
                'dismissNotice' => __('Dismiss this notice.', 'disable-kit')
            )
        ));
    }
    
    /**
     * Admin page HTML
     */
    public function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'disable-kit'));
        }

        $settings = $this->get_settings();
        $categories = $this->get_feature_categories();
        ?>
        <div class="wrap">
            <div class="disable-kit-shell">
                <header class="disable-kit-topbar">
                    <div class="disable-kit-topbar-row">
                        <div class="disable-kit-brand">
                            <img
                                class="disable-kit-logo"
                                src="<?php echo esc_url(DISABLE_KIT_PLUGIN_URL . 'assets/images/icon-128x128.png'); ?>"
                                alt=""
                                width="50"
                                height="50"
                                decoding="async"
                            >
                            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                        </div>
                        <a
                            class="button disable-kit-review-button"
                            href="<?php echo esc_url('https://wordpress.org/support/plugin/disable-kit/reviews/#new-post'); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <?php esc_html_e('Leave a review', 'disable-kit'); ?>
                        </a>
                    </div>
                </header>
            </div>

            <?php $this->show_security_warnings(); ?>

            <form method="post" action="options.php" id="disable-kit-form">
                <?php settings_fields('disable_kit_settings'); ?>

                <div class="disable-kit-layout">
                    <aside class="disable-kit-sidebar" aria-label="<?php echo esc_attr__('Feature categories', 'disable-kit'); ?>">
                        <div class="disable-kit-toolbar">
                            <label class="screen-reader-text" for="wp-feature-search"><?php esc_html_e('Search features', 'disable-kit'); ?></label>
                            <input
                                type="search"
                                id="wp-feature-search"
                                class="wp-feature-search"
                                placeholder="<?php echo esc_attr__('Search features, descriptions, risks, or scope...', 'disable-kit'); ?>"
                            >
                            <p class="wp-feature-search-empty" id="wp-feature-search-empty" hidden>
                                <?php esc_html_e('No features match your search.', 'disable-kit'); ?>
                            </p>
                        </div>

                        <div class="disable-kit-tabs" role="tablist" aria-label="<?php echo esc_attr__('Feature categories', 'disable-kit'); ?>">
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

                        <div class="disable-kit-safety-notice">
                            <h3><?php esc_html_e('Safety Information', 'disable-kit'); ?></h3>
                            <p>
                                <?php esc_html_e('High-impact features can affect editing, scheduled posts, checkout, feeds, or plugin compatibility. If a feature is already disabled by your theme, another plugin, or WordPress config, its toggle is locked and marked “Already disabled”.', 'disable-kit'); ?>
                            </p>
                            <p>
                                <?php esc_html_e('Emergency fallback: if you ever need to bypass this plugin completely, add this line to wp-config.php:', 'disable-kit'); ?>
                                <code>define('DISABLE_KIT_BYPASS', true);</code>
                            </p>
                        </div>
                    </aside>

                    <main class="disable-kit-content">
                        <div class="disable-kit-actions-bar">
                            <div class="disable-kit-summary">
                                <span class="wp-feature-summary-pill"><?php
                                /* translators: %d: number of features available in the plugin. */
                                echo esc_html(sprintf(__('Features: %d', 'disable-kit'), count($this->features)));
                                ?></span>
                                <span class="wp-feature-summary-pill wp-feature-summary-pill-risk"><?php esc_html_e('Red = high impact', 'disable-kit'); ?></span>
                            </div>
                            <div class="disable-kit-actions-bar-end">
                                <span class="wp-feature-unsaved-indicator" id="wp-feature-unsaved-indicator" hidden>
                                    <?php esc_html_e('Unsaved changes', 'disable-kit'); ?>
                                </span>
                                <button type="submit" id="disable-kit-submit-top" class="button button-primary">
                                    <?php esc_html_e('Save Changes', 'disable-kit'); ?>
                                </button>
                            </div>
                        </div>

                                <div class="disable-kit-sections">
                    <?php
                    // Build global child-key set so children never appear as top-level features
                    $global_child_keys = array();
                    foreach ($this->features as $f_key => $f_data) {
                        if (!empty($f_data['children'])) {
                            foreach ($f_data['children'] as $child) {
                                $global_child_keys[$child] = true;
                            }
                        }
                    }
                    ?>
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

                        // Alphabetical order by display name for top-level rows in this section.
                        uasort(
                            $section_features,
                            static function ($a, $b) {
                                return strcasecmp($a['name'], $b['name']);
                            }
                        );

                        // Use the global child-key set built above
                        $all_child_keys = $global_child_keys;

                        // Adjust section display count: exclude children that render under a parent
                        $section_display_count = count($section_features);
                        foreach ($section_features as $sf_key => $sf_data) {
                            if (isset($all_child_keys[$sf_key])) {
                                $section_display_count--;
                            }
                        }

                        $section_all_enabled = true;

                        foreach ($section_features as $section_feature_key => $section_feature_data) {
                            // Skip children in the main toggle check — they follow their parent
                            if (isset($all_child_keys[$section_feature_key])) {
                                continue;
                            }
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
                                    <span class="wp-feature-section-count"><?php
                                    /* translators: %d: number of features in the current category section. */
                                    echo esc_html(sprintf(_n('%d feature', '%d features', $section_display_count, 'disable-kit'), $section_display_count));
                                    ?></span>
                                    <label class="wp-feature-switch wp-feature-switch-section">
                                        <input
                                            type="checkbox"
                                            class="wp-feature-section-toggle"
                                            data-category="<?php echo esc_attr($category_key); ?>"
                                            <?php checked($section_all_enabled); ?>
                                        >
                                        <span class="wp-feature-switch-ui" aria-hidden="true"></span>
                                        <span class="wp-feature-switch-label"><?php esc_html_e('Section', 'disable-kit'); ?></span>
                                    </label>
                                </div>
                            </div>

                            <div class="wp-feature-section-content">
                                <?php foreach ($section_features as $feature_key => $feature_data) : ?>
                                    <?php
                                    // Skip children — they are rendered inline after their parent
                                    if (isset($all_child_keys[$feature_key])) {
                                        continue;
                                    }

                                    $is_enabled    = isset($settings[ $feature_key ]) ? (bool) $settings[ $feature_key ] : (bool) $feature_data['default'];
                                    $runtime_state = $this->get_feature_runtime_state($feature_key, $feature_data, $is_enabled);
                                    $effective_on  = $runtime_state['locked'] ? false : $is_enabled;
                                    $has_children  = !empty($feature_data['children']);
                                    ?>
                                    <article
                                        class="wp-feature-item wp-feature-risk-<?php echo esc_attr($feature_data['risk']); ?><?php echo $runtime_state['locked'] ? ' is-locked' : ''; ?><?php echo $has_children ? ' wp-feature-item-parent' : ''; ?>"
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
                                                            <?php esc_html_e('Already disabled', 'disable-kit'); ?>
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
                                                <?php echo $runtime_state['locked'] ? esc_html__('Already disabled', 'disable-kit') : ($is_enabled ? esc_html__('Enabled', 'disable-kit') : esc_html__('Disabled', 'disable-kit')); ?>
                                            </span>
                                        </div>
                                    </article>

                                    <?php
                                    // Render children immediately after parent, alphabetical by name.
                                    if ($has_children) :
                                        $sorted_children = $feature_data['children'];
                                        usort(
                                            $sorted_children,
                                            function ($a, $b) {
                                                $name_a = isset($this->features[ $a ]['name']) ? $this->features[ $a ]['name'] : $a;
                                                $name_b = isset($this->features[ $b ]['name']) ? $this->features[ $b ]['name'] : $b;
                                                return strcasecmp($name_a, $name_b);
                                            }
                                        );
                                        foreach ($sorted_children as $child_key) :
                                            if (!isset($this->features[$child_key])) {
                                                continue;
                                            }
                                            $child_data      = $this->features[$child_key];
                                            $child_enabled   = isset($settings[ $child_key ]) ? (bool) $settings[ $child_key ] : (bool) $child_data['default'];
                                            $child_state     = $this->get_feature_runtime_state($child_key, $child_data, $child_enabled);
                                            $child_effective = $child_state['locked'] ? false : $child_enabled;
                                    ?>
                                    <article
                                        class="wp-feature-item wp-feature-item-child wp-feature-risk-<?php echo esc_attr($child_data['risk']); ?><?php echo $child_state['locked'] ? ' is-locked' : ''; ?>"
                                        data-feature-item
                                        data-category="<?php echo esc_attr($category_key); ?>"
                                        data-feature="<?php echo esc_attr($child_key); ?>"
                                        data-search="<?php echo esc_attr(strtolower($feature_data['name'] . ' ' . $child_data['name'] . ' ' . $child_data['description'] . ' ' . $child_data['risk'] . ' ' . $child_data['scope'])); ?>"
                                    >
                                        <div class="wp-feature-primary">
                                            <div class="wp-feature-title-row">
                                                <label for="<?php echo esc_attr($child_key); ?>" class="wp-feature-name">
                                                    <span class="wp-feature-child-arrow" aria-hidden="true">&#8627;</span>
                                                    <?php echo esc_html($child_data['name']); ?>
                                                </label>
                                                <div class="wp-feature-meta">
                                                    <span class="wp-feature-badge wp-feature-badge-risk wp-feature-badge-risk-<?php echo esc_attr($child_data['risk']); ?>">
                                                        <?php echo esc_html($this->get_risk_label($child_data['risk'])); ?>
                                                    </span>
                                                    <span class="wp-feature-badge wp-feature-badge-scope">
                                                        <?php echo esc_html($this->get_scope_label($child_data['scope'])); ?>
                                                    </span>
                                                    <?php if ($child_state['locked']) : ?>
                                                        <span class="wp-feature-badge wp-feature-badge-locked">
                                                            <?php esc_html_e('Already disabled', 'disable-kit'); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <p class="wp-feature-description">
                                                <?php echo esc_html($child_data['description']); ?>
                                            </p>

                                            <?php if ($child_state['reason']) : ?>
                                                <p class="wp-feature-helper-text">
                                                    <?php echo esc_html($child_state['reason']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="wp-feature-control">
                                            <input
                                                type="hidden"
                                                name="<?php echo esc_attr($this->options_key); ?>[<?php echo esc_attr($child_key); ?>]"
                                                value="<?php echo esc_attr($child_enabled ? '1' : '0'); ?>"
                                                class="wp-feature-hidden-value"
                                            >
                                            <label class="wp-feature-switch" for="<?php echo esc_attr($child_key); ?>">
                                                <input
                                                    type="checkbox"
                                                    id="<?php echo esc_attr($child_key); ?>"
                                                    class="wp-feature-toggle"
                                                    value="1"
                                                    data-feature="<?php echo esc_attr($child_key); ?>"
                                                    data-category="<?php echo esc_attr($category_key); ?>"
                                                    <?php checked($child_effective); ?>
                                                    <?php disabled($child_state['locked']); ?>
                                                >
                                                <span class="wp-feature-switch-ui" aria-hidden="true"></span>
                                                <span class="screen-reader-text"><?php echo esc_html($child_data['name']); ?></span>
                                            </label>
                                            <span class="wp-feature-toggle-status">
                                                <?php echo $child_state['locked'] ? esc_html__('Already disabled', 'disable-kit') : ($child_enabled ? esc_html__('Enabled', 'disable-kit') : esc_html__('Disabled', 'disable-kit')); ?>
                                            </span>
                                        </div>
                                    </article>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                        </div>
                    </main>
                </div>

                <?php submit_button(esc_html__('Save Changes', 'disable-kit'), 'primary', 'submit', true, array('id' => 'disable-kit-submit', 'style' => 'display:none;')); ?>
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
            'writing'     => __('Editors, content types, comments, and the tools your team uses to create and manage content.', 'disable-kit'),
            'media'       => __('Embeds, avatars, fonts, images, and browser-facing media behaviour.', 'disable-kit'),
            'speed'       => __('Frontend scripts, CSS, head tags, scheduled tasks, and server-load related features.', 'disable-kit'),
            'security'    => __('Remote access, updates, public registration, and privacy hardening.', 'disable-kit'),
            'admin_ui'    => __('Dashboard cleanup and admin-only screens that can simplify the backend for clients.', 'disable-kit'),
            'feeds'       => __('Syndication formats and cross-site communication features such as RSS and pingbacks.', 'disable-kit'),
            'archives'    => __('Search, date archives, author pages, and attachment pages used for public navigation.', 'disable-kit'),
            'woocommerce' => __('WooCommerce-specific onboarding, admin clutter, checkout behaviour, and storefront scripts.', 'disable-kit'),
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
            'low'    => __('Safe', 'disable-kit'),
            'medium' => __('Caution', 'disable-kit'),
            'high'   => __('High impact', 'disable-kit'),
        );

        return isset($labels[ $risk ]) ? $labels[ $risk ] : __('Unknown', 'disable-kit');
    }

    /**
     * Get display label for scope.
     *
     * @param string $scope Scope key.
     * @return string
     */
    private function get_scope_label($scope) {
        $labels = array(
            'frontend' => __('Frontend', 'disable-kit'),
            'admin'    => __('Admin', 'disable-kit'),
            'both'     => __('Frontend + Admin', 'disable-kit'),
        );

        return isset($labels[ $scope ]) ? $labels[ $scope ] : __('Mixed', 'disable-kit');
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
                    $state['reason'] = __('The Posts content type has already been removed by another plugin or theme.', 'disable-kit');
                }
                break;

            case 'pages':
                if (!post_type_exists('page')) {
                    $state['locked'] = true;
                    $state['reason'] = __('The Pages content type has already been removed by another plugin or theme.', 'disable-kit');
                }
                break;

            case 'attachments':
                if (!post_type_exists('attachment')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Media attachments are already disabled elsewhere.', 'disable-kit');
                }
                break;

            case 'categories':
                if (!taxonomy_exists('category')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Post categories are already disabled elsewhere.', 'disable-kit');
                }
                break;

            case 'tags':
                if (!taxonomy_exists('post_tag')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Post tags are already disabled elsewhere.', 'disable-kit');
                }
                break;

            case 'user_registration':
                if (!get_option('users_can_register')) {
                    $state['locked'] = true;
                    $state['reason'] = __('User registration is already turned off in Settings > General.', 'disable-kit');
                }
                break;

            case 'xmlrpc':
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter.
                if (false === apply_filters('xmlrpc_enabled', true)) {
                    $state['locked'] = true;
                    $state['reason'] = __('XML-RPC is already disabled by WordPress config or another plugin.', 'disable-kit');
                }
                break;

            case 'site_editor':
                if (!$this->is_feature_enabled('design_system')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Locked because Design System is disabled. Enable Design System to manage the Full Site Editor separately.', 'disable-kit');
                } elseif (!post_type_exists('wp_template')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Block template support has already been removed by another plugin or theme.', 'disable-kit');
                } elseif (function_exists('wp_is_block_theme') && !wp_is_block_theme()) {
                    $state['locked'] = true;
                    $state['reason'] = __('The Full Site Editor is not available because the active theme is not a block theme.', 'disable-kit');
                }
                break;

            case 'customizer':
                if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
                    $state['locked'] = true;
                    $state['reason'] = __('Block themes use the Site Editor instead of the Theme Customizer.', 'disable-kit');
                }
                break;

            case 'theme_editor':
            case 'plugin_editor':
                if ((defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) || (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)) {
                    $state['locked'] = true;
                    $state['reason'] = __('File editing is already blocked in WordPress config.', 'disable-kit');
                }
                break;

            case 'revisions':
                if (defined('WP_POST_REVISIONS') && false === WP_POST_REVISIONS) {
                    $state['locked'] = true;
                    $state['reason'] = __('Post revisions are already disabled in wp-config.php.', 'disable-kit');
                }
                break;

            case 'comments':
                if (!post_type_supports('post', 'comments') && !post_type_supports('page', 'comments')) {
                    $state['locked'] = true;
                    $state['reason'] = __('Comments are already disabled for the main content types.', 'disable-kit');
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
     *
     * @param array $input Raw submitted settings.
     * @return array
     */
    public function sanitize_settings($input) {
        $old_settings = $this->get_settings();
        $sanitized = array();
        $input = is_array($input) ? $input : array();

        foreach ($this->features as $feature_key => $feature_data) {
            $value = isset($input[$feature_key]) ? (bool) $input[$feature_key] : false;

            /**
             * Filter a feature setting before it is saved.
             *
             * @param bool   $value       Proposed enabled state.
             * @param string $feature_key Feature slug.
             * @param array  $input       Full submitted settings array.
             */
            $value = (bool) apply_filters('disable_kit_validate_setting', $value, $feature_key, $input);
            $sanitized[$feature_key] = $value;

            $old_value = isset($old_settings[$feature_key])
                ? (bool) $old_settings[$feature_key]
                : (bool) $feature_data['default'];

            if ($old_value !== $value) {
                /**
                 * Fires when a feature toggle value changes on save.
                 *
                 * @param string $feature_key Feature slug.
                 * @param bool   $value       New enabled state.
                 * @param bool   $old_value   Previous enabled state.
                 */
                do_action('disable_kit_feature_toggled', $feature_key, $value, $old_value);
            }
        }

        return $sanitized;
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
                'message' => __('<strong>Automatic updates and background update checks are disabled.</strong> Security patches will not install themselves, and WordPress will not schedule core/plugin/theme update checks. Consider re-enabling this or monitoring updates another way.', 'disable-kit')
            );
        }
        
        if (empty($settings['wp_org_requests'])) {
            $warnings[] = array(
                'type' => 'warning',
                'message' => __('<strong>WordPress.org communication is blocked.</strong> Your site cannot reach WordPress.org for updates, translations, or plugin/theme information. Security patches may not be detected. Consider enabling this or using an alternative update monitoring solution.', 'disable-kit')
            );
        }

        if (isset($settings['cron']) && empty($settings['cron'])) {
            $warnings[] = array(
                'type' => 'warning',
                'message' => __('<strong>WP-Cron spawning is disabled.</strong> Scheduled posts and plugin jobs will not run unless your host triggers <code>wp-cron.php</code> via a real system cron. Re-enable this toggle or configure system cron before leaving it off.', 'disable-kit')
            );
        }
        
        foreach ($warnings as $warning) {
            $class = 'notice notice-' . esc_attr($warning['type']) . ' is-dismissible';
            printf('<div class="%s"><p>%s</p></div>', esc_attr($class), wp_kses_post($warning['message']));
        }
    }

    /**
     * Get feature categories
     *
     * @return array<string, string>
     */
    private function get_feature_categories() {
        $categories = array(
            'writing'     => __('Writing & Content', 'disable-kit'),
            'media'       => __('Media & Embeds', 'disable-kit'),
            'speed'       => __('Site Speed', 'disable-kit'),
            'security'    => __('Security & Privacy', 'disable-kit'),
            'admin_ui'    => __('Admin Interface', 'disable-kit'),
            'feeds'       => __('Feeds & Connections', 'disable-kit'),
            'archives'    => __('Search & Archives', 'disable-kit'),
            'woocommerce' => __('WooCommerce', 'disable-kit')
        );

        // Drop categories that currently have no registered features (e.g. Woo when inactive).
        $used = array();
        foreach ($this->features as $feature_data) {
            if (!empty($feature_data['category'])) {
                $used[$feature_data['category']] = true;
            }
        }
        $categories = array_intersect_key($categories, $used);

        /**
         * Filter the feature category labels shown in the admin UI.
         *
         * @param array<string, string> $categories Category slug => label.
         */
        return apply_filters('disable_kit_categories', $categories);
    }
}
