# WP Strip

Strip unwanted WordPress features for a leaner, faster, more secure site.

**WP Strip** gives administrators control over **132 core WordPress (and WooCommerce) features** at the load level—hooks, not menu hiding. Every toggle includes plain-English guidance, a risk label, and a scope tag.

No bloat. No page builders. No subscriptions.

---

## Installation

1. Upload the `wp-strip` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins screen
3. Open **Settings → WP Strip** to configure features

## Safety

### Kill switch

If a toggle locks you out of admin, add this to `wp-config.php`:

```php
define( 'DISABLE_WP_STRIP', true );
```

### Other safeguards

- Confirmation dialogs on critical disables
- Persistent warnings when update checks, WordPress.org communication, or WP-Cron spawning are stripped
- Risk labels: **high** / **medium** / **low**
- Scope tags: **admin** / **frontend** / **both**
- Parent → child cascade with locked children when a parent is off

### Debug tools (optional)

```php
define( 'WP_STRIP_DEBUG_TOOLS', true );
```

---

## Feature catalog

The full machine-readable list (keys, risk, scope, children) lives in [`doc/features.json`](doc/features.json) (132 features). Categories:

| Category | Examples |
|----------|----------|
| Writing & Content | Gutenberg, posts, pages, comments, design system |
| Media & Embeds | oEmbed, emoji, lazy load, WebP |
| Site Speed | Heartbeat, jQuery Migrate, head tags, block CSS |
| Security & Privacy | Unauthenticated REST, XML-RPC, user enumeration |
| Admin Interface | Dashboard widgets, Customizer, file editors, nags |
| Feeds & Connections | RSS, RDF, pingbacks |
| Search & Archives | Search, date/author archives, attachment pages |
| WooCommerce | Notices, cart fragments, checkout blocks (when WC active) |

Semantics: setting **`true` = keep WordPress behavior**; **`false` = strip/disable** at runtime.

---

## Developer API

See [`examples/extend-plugin.php`](examples/extend-plugin.php) for copy-paste patterns.

### Helpers

```php
wp_strip_is_feature_enabled( 'comments' ); // true|false|null
WP_Strip::is_enabled( 'rest_api' );        // true|false|null
```

Unknown keys return `null` (they are not silently treated as enabled).

### Filters

| Hook | Purpose |
|------|---------|
| `wp_strip_features` | Add/modify the feature registry |
| `wp_strip_categories` | Add/modify admin category tabs |
| `wp_strip_validate_setting` | Filter a value before save (`$value, $key, $input`) |

### Actions

| Hook | Purpose |
|------|---------|
| `wp_strip_disable_{$feature_key}` | Run when a feature is being stripped (required for custom features) |
| `wp_strip_feature_toggled` | After save when a value changes (`$key, $new, $old`) |

### Custom feature contract

```php
$features['portfolio'] = array(
    'name'        => 'Portfolio Post Type',
    'description' => '…',
    'category'    => 'custom', // must exist via wp_strip_categories
    'risk'        => 'medium', // low|medium|high
    'scope'       => 'both',   // frontend|admin|both
    'default'     => true,
    'priority'    => 1,
    'children'    => array(),  // optional
);
```

Register a matching `wp_strip_disable_portfolio` action or the toggle will save with no runtime effect.

### Settings storage

Single option: `wp_strip_settings` (associative array of feature key => bool).

---

## Requirements

- WordPress 5.0+
- PHP 7.4+
- `manage_options` capability

## Privacy

WP Strip stores settings in the WordPress options table only. It does not phone home, create accounts, or collect personal data.

## Changelog

### 1.0.0

- Initial public release as WP Strip
- 132 toggleable WordPress / WooCommerce features
- Parent–child hierarchy with cascade and locks
- Developer hooks: `wp_strip_features`, `wp_strip_categories`, `wp_strip_disable_*`, `wp_strip_feature_toggled`, `wp_strip_validate_setting`
- Kill switch, risk/scope labels, security warnings
- Debug tools gated behind `WP_STRIP_DEBUG_TOOLS`

## License

GPL v2 or later — see [license.txt](license.txt)
