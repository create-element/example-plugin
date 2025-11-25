# Example Plugin - Setup Guide

**Status:** Boilerplate plugin ready for Composer-based development  
**Last Updated:** November 25, 2025

---

## Quick Setup

### 1. Create GitHub Repositories

```bash
# On GitHub, create these repos:
# - create-element/pp-core
# - create-element/pp-updater
```

### 2. Clone Libraries to WordPress

```bash
cd /var/www/devx.headwall.tech/web/wp-content/libraries

# Clone the library repositories
git clone git@github.com:create-element/pp-core.git pp-core
git clone git@github.com:create-element/pp-updater.git pp-updater
```

### 3. Install Composer Dependencies

```bash
cd /var/www/devx.headwall.tech/web/wp-content/plugins/example-plugin

# Install dependencies (this creates symlinks to libraries)
composer install

# You should see:
# vendor/power-plugins/core -> ../../libraries/pp-core
# vendor/power-plugins/updater -> ../../libraries/pp-updater
```

### 4. Verify Setup

```bash
# Check symlinks were created
ls -la vendor/power-plugins/

# Should show:
# core -> ../../../../../wp-content/libraries/pp-core
# updater -> ../../../../../wp-content/libraries/pp-updater
```

### 5. Activate Plugin in WordPress

```bash
# Via WP-CLI
wp plugin activate example-plugin

# Or via WordPress admin:
# Navigate to http://localhost/wp-admin/plugins.php
```

---

## Directory Structure After Setup

```
/var/www/devx.headwall.tech/web/
├── wp-content/
│   ├── libraries/                      # Library source of truth
│   │   ├── pp-core/                    # Git: create-element/pp-core
│   │   │   ├── .git/                   # Composer: power-plugins/core
│   │   │   ├── composer.json
│   │   │   └── src/
│   │   └── pp-updater/                 # Git: create-element/pp-updater
│   │       ├── .git/                   # Composer: power-plugins/updater
│   │       ├── composer.json
│   │       └── src/
│   │
│   └── plugins/
│       └── example-plugin/
│           ├── composer.json           # Requires power-plugins/core & updater
│           ├── vendor/                 # Created by composer install
│           │   ├── autoload.php
│           │   └── power-plugins/
│           │       ├── core/          # → symlink to ../../libraries/pp-core
│           │       └── updater/       # → symlink to ../../libraries/pp-updater
│           │
│           ├── docs/                   # Migration documentation
│           ├── includes/               # Plugin classes (PSR-4 autoloaded)
│           │
│           ├── pp-core.php            # REFERENCE ONLY (old monolithic file)
│           ├── pp-assets/             # REFERENCE ONLY (old assets)
│           └── pwpl/                  # REFERENCE ONLY (old updater)
```

---

## Development Workflow

### Editing Library Code

```bash
# 1. Edit library code
cd /var/www/devx.headwall.tech/web/wp-content/libraries/pp-core
vim src/Settings/SettingsCore.php

# 2. Changes are IMMEDIATELY available in plugin (via symlink)
# No need to run composer update!

# 3. Test in WordPress
# Navigate to http://localhost/wp-admin

# 4. Run tests
composer test

# 5. Commit to library repo
git add src/
git commit -m "feat: add new method"
git push origin main
```

### Creating Plugin Features

```bash
cd /var/www/devx.headwall.tech/web/wp-content/plugins/example-plugin

# Create new class in includes/
vim includes/MyFeature.php
```

```php
<?php
namespace ExamplePlugin;

use PowerPlugins\Core\Settings\SettingsCore;

class MyFeature extends SettingsCore
{
  public function __construct()
  {
    parent::__construct('example-plugin', '1.0.6');
  }

  public function saveSettings()
  {
    $this->setBool('my_option', $_POST['my_option'] ?? false);
  }
}
```

---

## Next Steps

### 1. Initialize pp-core Library

See `docs/02-composer-migration-strategy.md` for complete structure.

**Minimal starter structure:**

```bash
cd /var/www/devx.headwall.tech/web/wp-content/libraries/pp-core

# Create directory structure
mkdir -p src/{Settings,Post,Term,UI,Utilities,MetaBox}
mkdir -p tests/Unit

# Create composer.json
cat > composer.json << 'EOF'
{
    "name": "power-plugins/core",
    "description": "Core framework for Power Plugins WordPress plugins",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": ">=8.1"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "squizlabs/php_codesniffer": "^3.7",
        "phpstan/phpstan": "^1.10"
    },
    "autoload": {
        "psr-4": {
            "PowerPlugins\\Core\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "PowerPlugins\\Core\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "phpcs": "phpcs --standard=PSR12 src/",
        "phpstan": "phpstan analyse src/ --level=5"
    }
}
EOF

# Install dependencies
composer install

# Commit initial structure
git add .
git commit -m "chore: initial project structure"
git push origin main
```

### 2. Initialize pp-updater Library

```bash
cd /var/www/devx.headwall.tech/web/wp-content/libraries/pp-updater

# Similar structure to pp-core
mkdir -p src
# ... (see docs for complete setup)
```

### 3. Start Extracting Code

Now you can start extracting classes from the old `pp-core.php` to the new library structure.

See `docs/02-composer-migration-strategy.md` Phase 2 for details.

---

## Troubleshooting

### "Class not found" errors

```bash
# Make sure Composer installed correctly
cd /var/www/devx.headwall.tech/web/wp-content/plugins/example-plugin
composer install

# Check symlinks exist
ls -la vendor/power-plugins/
```

### Admin notice about composer install

If you see an error notice in WordPress admin, run:

```bash
cd /var/www/devx.headwall.tech/web/wp-content/plugins/example-plugin
composer install
```

### Symlinks not working

Check the path in `composer.json` is correct:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../../wp-content/libraries/pp-core"
    }
  ]
}
```

From `/var/www/devx.headwall.tech/web/wp-content/plugins/example-plugin/`, going up two directories (`../../`) should reach `/var/www/devx.headwall.tech/web/`, then `wp-content/libraries/pp-core` reaches the library.

---

## Reference Files

The following files are kept for reference during migration:

- `pp-core.php` - Old monolithic core (2,336 lines) - DO NOT REQUIRE
- `pp-assets/` - Old asset directory - Will be in library packages
- `pwpl/` - Old updater - Will become power-plugins/updater package

**These can be deleted once migration is complete.**

---

## Documentation

- `docs/01-current-architecture-analysis.md` - Analysis of current code
- `docs/02-composer-migration-strategy.md` - Migration plan (Path A: Big Bang)
- `docs/03-quick-reference.md` - Developer cheat sheet
- `docs/04-development-workflow.md` - Development workflow guide

---

**Ready to go! Create those GitHub repos and follow the setup steps above.** 🚀
