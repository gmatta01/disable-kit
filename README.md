# WP Strip

Strip unwanted WordPress features for a leaner, faster, more secure site.

**WP Strip** is a comprehensive WordPress plugin that gives you administrative control over **110+ core WordPress features** at the load level. Toggle features on or off with plain-English guidance, risk labels, and scope tags.

No bloat. No page builders. No subscriptions. Just clean WordPress controls.

---

## Features at a Glance

WP Strip can **remove** or **disable** these WordPress subsystems:

### Writing & Content

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `gutenberg` | Block Editor (Gutenberg) | high | admin |
| `classic_editor` | Classic Editor (TinyMCE) | medium | admin |
| `block_widgets` | Block Widgets Editor | medium | admin |
| `site_editor` | Full Site Editor | high | admin |
| `posts` | Blog Posts | high | both |
| `pages` | Pages | high | both |
| `attachments` | Media Attachments | medium | both |
| `categories` | Post Categories | high | both |
| `tags` | Post Tags | medium | both |
| `comments` | Comments System | high | both |
| `revisions` | Post Revision History | medium | admin |
| `autosave` | Auto-Save While Editing | medium | admin |
| `capital_p_dangit` | Auto-correct "WordPress" Spelling | low | both |
| `wptexturize` | Smart Punctuation (wptexturize) | low | both |
| `convert_smilies` | Text Smilies to Images | low | both |
| `post_formats` | Post Formats | low | admin |
| `link_manager` | Link Manager (Blogroll) | low | admin |
| `block_directory` | Block Directory | medium | admin |
| `font_library` | Font Library | low | admin |
| `comment_cookies` | Comment Author Cookies | low | frontend |
| `comment_threading` | Threaded Comment Replies | low | frontend |
| `comment_url_field` | Website Field in Comment Form | low | frontend |
| `comment_avatars` | Comment Avatars Only | low | frontend |
| `comment_html` | Allowed HTML in Comments | low | frontend |

### Media & Embeds

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `embeds` | Automatic Link Previews (oEmbed) | medium | both |
| `emoji` | WordPress Emoji Support | low | both |
| `gravatars` | Gravatar Profile Images | low | both |
| `dns_prefetch` | Browser DNS Pre-loading | low | frontend |
| `google_fonts` | Google Fonts Loading | medium | frontend |
| `disable_lazy_load` | Native Image Lazy Loading | medium | both |
| `disable_auto_scaling_images` | Auto-Scale Oversized Images | medium | both |
| `responsive_images` | Responsive Images (srcset) | medium | frontend |
| `webp_uploads` | WebP Conversion on Upload | medium | both |
| `pdf_thumbnails` | PDF Thumbnail Generation | low | admin |

### Site Speed (Scripts, Styles & Head Tags)

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `heartbeat` | Background Auto-Sync (Heartbeat API) | medium | both |
| `cron` | Scheduled Tasks (WP-Cron) | high | both |
| `jquery_migrate` | jQuery Migrate Script | medium | both |
| `jquery_migrate_admin` | jQuery Migrate in Admin | medium | admin |
| `jquery_core_frontend` | jQuery Core on Frontend | high | frontend |
| `wp_embed_script` | WordPress Embed Script | medium | frontend |
| `comment_reply_script` | Comment Reply Script | low | frontend |
| `admin_bar_script` | Admin Bar Frontend Script | low | frontend |
| `backbone_underscore` | Legacy JavaScript Libraries | medium | frontend |
| `wp_util_script` | WordPress Helper Scripts | medium | frontend |
| `jquery_ui_scripts` | Interactive UI Scripts (jQuery UI) | medium | frontend |
| `masonry_script` | Photo Grid Layout Scripts | low | frontend |
| `wp_mediaelement` | Audio/Video Player Scripts | medium | frontend |
| `wp_accessibility` | Accessibility Scripts | medium | frontend |
| `version_strings` | Hide WordPress Version Number | low | frontend |
| `disable_wlwmanifest` | Windows Live Writer Link | low | frontend |
| `disable_wp_shortlink` | WordPress Shortlink Tag | low | both |
| `disable_rest_api_links` | REST API Discovery Tags | low | both |
| `disable_rss_feed_links` | RSS Feed Discovery Tags | low | frontend |
| `remove_query_strings` | Cache-Friendly Asset URLs | low | frontend |
| `disable_legacy_css` | Unused Legacy Styles | low | frontend |
| `remove_block_library_css` | Block Editor CSS on Frontend | medium | frontend |
| `dashicons_guests` | Dashicons for Logged-Out Visitors | low | frontend |
| `global_styles_inline_css` | Global Styles Inline CSS | medium | frontend |
| `svg_duotone_filters` | SVG Duotone Filters Output | low | frontend |
| `adjacent_posts_links` | Adjacent Post Links in Head | low | frontend |
| `disable_rsd_link` | RSD Link Tag | low | frontend |
| `comment_feeds` | Comment Feed Endpoints | low | frontend |
| `wp_sitemaps` | Built-In WordPress Sitemaps | medium | both |
| `remote_block_patterns` | Remote Block Pattern Loading | low | admin |
| `core_block_patterns` | Core Block Patterns | medium | admin |
| `block_editor_assets_non_editors` | Block Editor Assets for Non-Editors | low | admin |
| `disable_auto_trash_empty` | Scheduled Trash Cleanup | low | admin |
| `interactivity_api` | Interactivity API Scripts | medium | frontend |
| `canonical_links` | Canonical Link Tags | low | frontend |
| `wp_resource_hints` | Resource Hints (dns-prefetch, preconnect) | low | frontend |
| `generator_meta_rss` | Generator Tag in RSS Feeds | low | frontend |

### Security & Privacy

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `rest_api` | Remote App Connections (REST API) | high | both |
| `xmlrpc` | Legacy Remote Publishing (XML-RPC) | medium | both |
| `wp_org_requests` | WordPress.org Communication | high | admin |
| `update_checks` | Automatic Updates | high | admin |
| `user_registration` | Public User Registration | medium | both |
| `user_enumeration` | Hide Usernames from Public | low | both |
| `application_passwords` | Application Passwords | medium | both |
| `login_language_selector` | Login Page Language Selector | low | frontend |
| `lost_password` | Lost Password Flow | high | both |

### Admin Interface

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `dashboard_widgets` | Dashboard Widgets | low | admin |
| `admin_bar` | Admin Toolbar | medium | both |
| `customizer` | Theme Customizer | medium | admin |
| `theme_editor` | Theme File Editor | low | admin |
| `plugin_editor` | Plugin File Editor | low | admin |
| `welcome_panel` | Welcome Panel | low | admin |
| `wp_news_dashboard` | WordPress Events & News Widget | low | admin |
| `admin_email_verification` | Admin Email Verification Screen | low | admin |
| `command_palette` | Command Palette | low | admin |
| `privacy_policy_guide` | Privacy Policy Guide | low | admin |
| `health_check` | Site Health | low | admin |
| `export_erase_personal_data` | Export / Erase Personal Data Tools | low | admin |
| `browser_update_nag` | Browser Update Nag | low | admin |
| `php_update_nag` | PHP Version Update Nag | low | admin |

### Feeds & Connections

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `rss_feeds` | RSS / Atom Feeds | medium | frontend |
| `rdf_feed` | RDF Feed (Legacy Syndication) | low | frontend |
| `pingbacks` | Cross-Site Link Notifications (Pingbacks) | low | both |

### Search & Archives

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `search` | Site Search | high | frontend |
| `archives` | Date Archive Pages | low | frontend |
| `attachment_pages` | Media Attachment Pages | low | frontend |
| `author_archives` | Author Archive Pages | low | frontend |

### WooCommerce (only when WooCommerce is active)

| Key | Feature | Risk | Scope |
|-----|---------|------|-------|
| `wc_marketing_hub` | WooCommerce Marketing Hub | low | admin |
| `wc_marketplace_suggestions` | WooCommerce Extension Suggestions | low | admin |
| `wc_admin_notices` | WooCommerce Promotional Notices | low | admin |
| `wc_setup_wizard` | WooCommerce Setup Wizard | low | admin |
| `wc_home_screen` | WooCommerce Home Screen | low | admin |
| `wc_store_alerts` | WooCommerce Store Alert Banners | low | admin |
| `wc_usage_tracking` | WooCommerce Usage Tracking | low | both |
| `wc_checkout_blocks` | WooCommerce Checkout & Cart Blocks | high | both |
| `wc_block_styles` | WooCommerce Block Styles | medium | frontend |
| `wc_cart_fragments` | WooCommerce Cart Counter Update | high | frontend |
| `wc_password_strength` | Password Strength Meter | medium | both |
| `wc_conditional_assets` | WooCommerce Assets Only on Store Pages | medium | frontend |
| `wc_reviews` | Product Reviews & Ratings | medium | both |

---

## Installation

1. Upload the `wp-strip` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **Settings → WP Strip** to configure features

## Safety Features

### Kill Switch

If you accidentally disable critical features and cannot access the admin panel, add this line to your `wp-config.php`:

```php
define('DISABLE_WP_STRIP', true);
```

This completely bypasses all plugin functionality and restores access to your site.

### Confirmation Dialogs

Critical features (Posts, Pages, REST API, updates, etc.) show a confirmation dialog before disabling to prevent accidents.

### Security Warnings

The plugin displays persistent warnings when you disable security-sensitive features like update checks or WordPress.org communication.

### Risk Labels

Every feature has a risk label:
- **High** — May break navigation, content editing, or security
- **Medium** — Impacts specific admin interfaces or frontend features
- **Low** — Safe for most sites with minimal impact

### Scope Tags

Each feature shows where it applies:
- **admin** — Admin dashboard only
- **frontend** — Public-facing pages
- **both** — Both admin and frontend

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Administrator privileges

## Changelog

### 1.0.0

- Initial release as WP Strip (forked from WP Feature Manager)
- 110+ toggleable WordPress features
- Content & text processing controls (capital_P_dangit, wptexturize, smilies)
- Media optimization (responsive images, WebP, PDF thumbnails)
- Frontend asset control (Interactivity API, canonical links, resource hints)
- Security & access controls (application passwords, login language, lost password)
- Admin & dashboard cleanup (health check, command palette, privacy guide, export tools)
- Block Editor controls (block directory, font library)
- Granular comment controls (cookies, threading, URL field)
- Harden existing features per architecture review
- Split theme/plugin editor controls with distinct constants
- Debug tools gated behind `WP_STRIP_DEBUG_TOOLS` constant
- WooCommerce admin notices targetted (no more global remove_all_actions)
- Security warning banners for update_checks and wp_org_requests

## Technical Implementation

Features are disabled at the WordPress load level using hooks — not just hidden from menus. The plugin uses:

- `init` — For post type and taxonomy unregistration
- `admin_menu` — For menu and submenu removal
- `wp_enqueue_scripts` / `admin_enqueue_scripts` — For script/style deregistration
- `wp_head` — For head tag removal
- `template_redirect` — For archive/search/page disabling
- Various WordPress and WooCommerce-specific hooks

Settings are stored in a single WordPress option (`wp_strip_settings`) for performance.

### Filter Reference

- `wp_strip_features` — Modify the list of manageable features
- `wp_strip_categories` — Modify feature categories

## License

GPL v2 or later
