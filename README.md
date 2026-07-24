# Disable Kit

Disable unwanted WordPress features for a leaner, faster, more secure site.

**Disable Kit** gives administrators control over **132 core WordPress (and WooCommerce) features** at the load level—hooks, not menu hiding. Every toggle includes plain-English guidance, a risk label, and a scope tag.

No bloat. No page builders. No subscriptions.

---

## Installation

1. Upload the `disable-kit` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins screen
3. Open **Settings → Disable Kit** to configure features

## Safety

### Kill switch

If a toggle locks you out of admin, add this to `wp-config.php`:

```php
define( 'DISABLE_KIT_BYPASS', true );
```

### Other safeguards

- Confirmation dialogs on critical disables
- Persistent warnings when update checks, WordPress.org communication, or WP-Cron spawning are turned off
- Risk labels: **high** / **medium** / **low**
- Scope tags: **admin** / **frontend** / **both**
- Parent → child cascade with locked children when a parent is off

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

Semantics: setting **`true` = keep WordPress behavior**; **`false` = disable** at runtime.

---

## Developer API

See [`examples/extend-plugin.php`](examples/extend-plugin.php) for copy-paste patterns.

### Helpers

```php
disable_kit_is_feature_enabled( 'comments' ); // true|false|null
Disable_Kit::is_enabled( 'rest_api' );        // true|false|null
```

Unknown keys return `null` (they are not silently treated as enabled).

### Filters

| Hook | Purpose |
|------|---------|
| `disable_kit_features` | Add/modify the feature registry |
| `disable_kit_categories` | Add/modify admin category tabs |
| `disable_kit_validate_setting` | Filter a value before save (`$value, $key, $input`) |

### Actions

| Hook | Purpose |
|------|---------|
| `disable_kit_disable_{$feature_key}` | Run when a feature is being disabled (required for custom features) |
| `disable_kit_feature_toggled` | After save when a value changes (`$key, $new, $old`) |

### Custom feature contract

```php
$features['portfolio'] = array(
    'name'        => 'Portfolio Post Type',
    'description' => '…',
    'category'    => 'custom', // must exist via disable_kit_categories
    'risk'        => 'medium', // low|medium|high
    'scope'       => 'both',   // frontend|admin|both
    'default'     => true,
    'priority'    => 1,
    'children'    => array(),  // optional
);
```

Register a matching `disable_kit_disable_portfolio` action or the toggle will save with no runtime effect.

### Settings storage

Single option: `disable_kit_settings` (associative array of feature key => bool).

Legacy installs may still have `wp_strip_settings`; Disable Kit migrates that option automatically on first load.

---

## Requirements

- WordPress 5.9+
- PHP 7.4+
- `manage_options` capability

## Privacy

Disable Kit stores settings in the WordPress options table only. It does not phone home, create accounts, or collect personal data.

## Changelog

### 1.0.0

- Initial public release as Disable Kit
- 132 toggleable WordPress / WooCommerce features
- Parent–child hierarchy with cascade and locks
- Developer hooks: `disable_kit_features`, `disable_kit_categories`, `disable_kit_disable_*`, `disable_kit_feature_toggled`, `disable_kit_validate_setting`
- Kill switch, risk/scope labels, security warnings

## License

GPL v2 or later — see [license.txt](license.txt)
