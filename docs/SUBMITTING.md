# Submitting StripBoard to WordPress.org

Prepared artifacts for directory submission.

## Before you submit

1. Confirm the plugin slug `stripboard` is available: search https://wordpress.org/plugins/
2. Create / log in to a WordPress.org account (contributor slug should match `readme.txt` → `Contributors: gmatta01` or update that field)
3. Whitelist `plugins@wordpress.org` in email

## Zip for review

Built package:

`dist/stripboard-1.0.0.zip`

Excluded from the zip:

- `.git/`
- `.pi-subagents/`
- `dist/`
- `.wordpress-org/` (upload these to SVN `assets/` after approval)
- internal `doc/*.html` analysis

Included:

- Plugin PHP/JS/CSS, `readme.txt`, `license.txt`, `README.md`, `ROADMAP.md`
- `examples/extend-plugin.php`
- `languages/stripboard.pot`
- `doc/features.json`

Directory graphics live in `.wordpress-org/` (banners, icons, screenshots) for SVN `assets/` after approval.

## Submit

1. Open https://wordpress.org/plugins/developers/add/
2. Upload `dist/stripboard-1.0.0.zip`
3. Add a short overview (lean feature governor; 132 toggles; kill switch; no remote calls)
4. Wait for plugin review (often 1–10 days)

## After approval (SVN)

```bash
svn co https://plugins.svn.wordpress.org/stripboard stripboard-svn
# Copy plugin files into trunk/
# Copy .wordpress-org/* into svn assets/ (sibling of trunk)
svn add trunk/* assets/*
svn ci -m "Initial release 1.0.0"
# Tag
svn cp trunk tags/1.0.0
svn ci -m "Tag 1.0.0"
```

Validate readme anytime: https://wordpress.org/plugins/developers/readme-validator/

## Smoke test checklist

- [ ] Activate on clean WP 6.8+
- [ ] Settings → StripBoard loads; save toggles
- [ ] Disable emoji / generator; confirm frontend change
- [ ] Disable cron; confirm admin warning
- [ ] With WooCommerce active, Woo tab appears
- [ ] `define('STRIPBOARD_BYPASS', true);` restores defaults behavior
- [ ] Custom feature via `examples/extend-plugin.php` patterns works
