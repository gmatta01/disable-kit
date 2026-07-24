=== WP Strip ===
Contributors: gmatta01
Tags: performance, security, disable features, cleanup, woocommerce
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Strip unwanted WordPress features for a leaner, faster, more secure site. 132 toggles with risk labels.

== Description ==

WP Strip gives administrators control over core WordPress (and WooCommerce) subsystems at the load level—using hooks, not menu hiding.

* 132 toggleable features across writing, media, speed, security, admin UI, feeds, archives, and WooCommerce
* Plain-English descriptions, risk labels (high / medium / low), and scope tags (admin / frontend / both)
* Parent → child hierarchy with cascade toggles
* Kill switch via `DISABLE_WP_STRIP` in wp-config.php
* Warnings when stripping update checks, WordPress.org communication, or WP-Cron spawning
* Extensible for developers via documented filters and actions

No bloat. No page builders. No subscriptions.

= Privacy =

WP Strip stores settings in the WordPress options table (`wp_strip_settings`) only. It does not send data to remote servers, create accounts, or collect personal information.

= Developer hooks =

* `wp_strip_features` — modify the feature registry
* `wp_strip_categories` — modify category tabs
* `wp_strip_disable_{$feature}` — implement stripping for custom features
* `wp_strip_feature_toggled` — react when a setting changes
* `wp_strip_validate_setting` — filter values before save
* `wp_strip_is_feature_enabled( $key )` — public helper

See the plugin's `examples/extend-plugin.php` and GitHub README for details.

== Installation ==

1. Upload the `wp-strip` folder to the `/wp-content/plugins/` directory, or install from Plugins → Add New.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings → WP Strip to configure features.

== Frequently Asked Questions ==

= I disabled something and lost admin access. How do I recover? =

Add this line to `wp-config.php` above the “That's all, stop editing!” comment:

`define( 'DISABLE_WP_STRIP', true );`

This bypasses all WP Strip logic until you can re-enable features.

= Does disabling REST API break the block editor? =

The REST toggle blocks **unauthenticated** (guest) REST requests only. Logged-in users and authenticated clients can still use the API.

= Will disabling WP-Cron break scheduled posts? =

It stops WordPress from spawning cron on page loads (`DISABLE_WP_CRON`). You must have a real system cron hitting `wp-cron.php`, or scheduled jobs will stall. A warning is shown in admin when this toggle is off.

= Does this plugin collect data? =

No. Settings are stored locally in `wp_options` only.

== Screenshots ==

1. Settings → WP Strip with feature toggles, risk labels, and category tabs.
2. Search, section controls, and parent/child cascade toggles.

== Changelog ==

= 1.0.0 =
* Initial public release
* 132 WordPress / WooCommerce feature toggles
* Parent–child hierarchy, kill switch, security warnings
* Developer extension API and example file

== Upgrade Notice ==

= 1.0.0 =
Initial release.
