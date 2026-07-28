# StripBoard

Simply disable unwanted WordPress features from one settings board.

**StripBoard** lets you turn off unused core WordPress (and WooCommerce) features at the load level—hooks, not menu hiding. Every toggle includes plain-English guidance, a risk label, and a scope tag.

No bloat. No page builders. No subscriptions.

---

## Installation

1. Upload the `stripboard` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins screen
3. Open **Settings → StripBoard** to configure features

## Safety

### Kill switch

If a toggle locks you out of admin, add this to `wp-config.php`:

```php
define( 'STRIPBOARD_BYPASS', true );
```

### Other safeguards

- Confirmation dialogs on critical disables
- Risk labels: **high** / **medium** / **low**
- Scope tags: **admin** / **frontend** / **both**
- Parent → child cascade with locked children when a parent is off

---

## Feature catalog

The full machine-readable list lives in [`doc/features.json`](doc/features.json) (129 features).

Semantics: setting **`true` = keep WordPress behavior**; **`false` = disable** at runtime.

---

## Developer API

See [`examples/extend-plugin.php`](examples/extend-plugin.php) for copy-paste patterns.

### Helpers

```php
stripboard_is_feature_enabled( 'comments' ); // true|false|null
Stripboard::is_enabled( 'rest_api' );        // true|false|null
```

### Filters

| Hook | Purpose |
|------|---------|
| `stripboard_features` | Add/modify the feature registry |
| `stripboard_categories` | Add/modify admin category tabs |
| `stripboard_validate_setting` | Filter a value before save |

### Actions

| Hook | Purpose |
|------|---------|
| `stripboard_disable_{$feature_key}` | Run when a feature is being disabled |
| `stripboard_feature_toggled` | After save when a value changes |

### Settings storage

Option: `stripboard_settings`. Legacy `disable_kit_settings` / `wp_strip_settings` migrate automatically.

---

## Requirements

- WordPress 5.9+
- PHP 7.4+
- `manage_options` capability

## Changelog

### 1.0.1

- Rebrand to StripBoard (`stripboard`)
- Removed update-check interference and `DISABLE_WP_CRON` define toggle
- Contributors: gangesh

### 1.0.0

- Initial public release

## License

GPL v2 or later — see [license.txt](license.txt)
