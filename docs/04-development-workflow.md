# Development Workflow: Composer Library + WordPress Plugin

**Date:** November 25, 2025  
**Purpose:** Explain how to develop a Composer library alongside WordPress plugins

---

## The Core Challenge

You want to:

1. Develop a reusable Composer library (`power-plugins/core`)
2. Use it in WordPress plugins during development
3. Test changes in real WordPress environment
4. Publish library updates to Composer
5. Update plugins to use new library versions

**Key Question:** Where does the "source of truth" live?

---

## Three Workflow Options

### Option 1: Library-First (Recommended for Big Bang Refactor)

**Source of Truth:** Separate library repository  
**Best For:** Clean architecture, multiple plugins using same library

#### Repository Structure

```
/var/www/projects/
├── pp-core/                          # Library repo (source of truth)
│   ├── .git/                         # GitHub: create-element/pp-core
│   ├── composer.json                 # Composer: power-plugins/core
│   ├── src/
│   │   ├── Component.php
│   │   ├── Settings/
│   │   ├── Post/
│   │   └── ...
│   ├── tests/
│   └── docs/
│
├── pp-updater/                       # Updater library repo
│   ├── .git/                         # GitHub: create-element/pp-updater
│   ├── composer.json                 # Composer: power-plugins/updater
│   └── src/
│
└── plugins/
    ├── example-plugin/               # Test/boilerplate plugin
    │   ├── .git/
    │   ├── composer.json             # requires power-plugins/core
    │   ├── vendor/                   # symlinked during dev
    │   └── example-plugin.php
    │
    └── commercial-plugin-1/
        ├── .git/
        ├── composer.json
        └── ...
```

#### ⚡ Important: GitHub vs Composer Naming

**GitHub Repository Names:**

- `create-element/pp-core` ✅ (GitHub path)
- `create-element/pp-updater` ✅ (GitHub path)

**Composer Package Names (inside composer.json):**

- `power-plugins/core` ✅ (Composer package - simpler!)
- `power-plugins/updater` ✅ (Composer package)

**Why Different?**

1. **GitHub:** Repository names can be anything (`pp-core`, `wordpress-plugin`, `foo`)
2. **Composer:** Package names MUST be in `vendor/package` format
   - Vendor = `power-plugins` (your namespace, must match across all packages)
   - Package = `core`, `updater` (unique within your vendor namespace)

**The vendor name (`power-plugins`) acts as your namespace in Composer**, so:

- ✅ `power-plugins/core` - No conflicts with anyone else's `core` package
- ✅ `acme/core` - Someone else can have this without conflict
- ✅ `symfony/console` - Symfony's vendor namespace
- ✅ `laravel/framework` - Laravel's vendor namespace

**You do NOT need globally unique package names!** Just unique within `power-plugins/*`

**Recommendation:**

- GitHub repos: `pp-core`, `pp-updater` (shorter, prefixed for your org)
- Composer packages: `power-plugins/core`, `power-plugins/updater` (cleaner, vendor namespace provides uniqueness)

#### Development Workflow

**Step 1: Initial Setup**

```bash
# Clone library repository (GitHub name: pp-core)
cd /var/www/projects/
git clone git@github.com:create-element/pp-core.git pp-core
cd pp-core

# Inside composer.json, the package name is different:
# {
#     "name": "power-plugins/core",    ← Composer package name (cleaner!)
#     ...
# }

# Install dependencies
composer install

# Run tests to verify setup
composer test
```

**Step 2: Link Library to Plugin (Development Mode)**

```bash
# In your plugin directory
cd /var/www/projects/plugins/example-plugin

# Option A: Use Composer path repository (RECOMMENDED)
# Edit composer.json:
{
    "repositories": [
        {
            "type": "path",
            "url": "../../pp-core",              ← Points to GitHub repo directory
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "power-plugins/core": "@dev"            ← Uses Composer package name
    }
}

# Note: GitHub repo name (pp-core) ≠ Composer package name (power-plugins/core)
# The path URL points to the directory, Composer reads the package name from composer.json inside it

# Install - this creates symlink to local development copy
composer install

# Now vendor/power-plugins/core -> ../../pp-core
#         ↑ Composer package path    ↑ GitHub repo directory
```

**Option B: Use Composer global symlink (Alternative)**

```bash
# In library directory
cd /var/www/projects/pp-core
composer link

# In plugin directory
cd /var/www/projects/plugins/example-plugin
composer link power-plugins/core
```

**Step 3: Development Cycle**

```bash
# 1. Make changes in library
cd /var/www/projects/pp-core
vim src/Settings/SettingsCore.php

# 2. Run library tests
composer test
composer phpcs

# 3. Changes immediately available in plugin (via symlink)
cd /var/www/projects/plugins/example-plugin

# 4. Test in WordPress
# Navigate to http://localhost/wp-admin and test plugin

# 5. If changes work, commit to library
cd /var/www/projects/pp-core
git add src/Settings/SettingsCore.php
git commit -m "feat: add new settings method"
git push origin main

# 6. Tag release
git tag v2.0.1
git push origin v2.0.1
```

**Step 4: Publishing to Composer**

**Option A: Private Satis Repository**

```bash
# On your package server
cd /var/www/packages.power-plugins.com
php bin/satis build satis.json public/

# Plugins can now install released version
composer require power-plugins/core:^2.0.1
```

**Option B: GitHub Packages (Free)**

```bash
# Automatic - GitHub creates package from tags
# In plugin composer.json:
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/create-element/pp-core"
        }
    ],
    "require": {
        "power-plugins/core": "^2.0.1"
    }
}
```

**Step 5: Update Plugins to Use Released Version**

```bash
cd /var/www/projects/plugins/example-plugin

# Remove dev symlink, use packaged version
# Edit composer.json - remove "path" repository
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.power-plugins.com"
        }
    ],
    "require": {
        "power-plugins/core": "^2.0.1"
    }
}

# Install from package repository
rm -rf vendor/
composer install
```

---

### Option 2: Plugin-First with Extraction

**Source of Truth:** Plugin repository initially, then extract to library  
**Best For:** Evolving existing plugin into library

#### Repository Structure

```
/var/www/projects/
└── example-plugin/                   # Development happens here
    ├── .git/
    ├── composer.json
    ├── lib/                          # Local library code (not in vendor/)
    │   ├── src/
    │   │   ├── Component.php
    │   │   └── ...
    │   └── composer.json             # Defines library structure
    ├── vendor/
    └── example-plugin.php
```

#### Workflow

```bash
# 1. Develop library code inside plugin
cd /var/www/projects/example-plugin
vim lib/src/Settings/SettingsCore.php

# 2. Plugin autoloads from lib/ during development
# In example-plugin.php:
require_once __DIR__ . '/lib/vendor/autoload.php' # or custom autoloader

# 3. When ready to extract, copy to separate repo
cd /var/www/projects/
git init pp-core
cp -r example-plugin/lib/* pp-core/
cd pp-core
git add .
git commit -m "Initial library extraction"
git remote add origin git@github.com:create-element/pp-core.git
git push -u origin main

# 4. Update plugin to use Composer package
cd /var/www/projects/example-plugin
# Edit composer.json to require power-plugins/core
composer require power-plugins/core:@dev
rm -rf lib/ # Remove local copy
```

**Pros:**

- Start development quickly in plugin context
- Extract when library is stable

**Cons:**

- Have to migrate code to separate repo later
- Risk of plugin-specific coupling
- One-time migration effort

---

### Option 3: Monorepo with Subtree Split

**Source of Truth:** Single repository with automatic package splitting  
**Best For:** Advanced teams, automated CI/CD

#### Repository Structure

```
power-plugins-monorepo/
├── .git/
├── packages/
│   ├── core/                         # Becomes power-plugins/core
│   │   ├── composer.json
│   │   └── src/
│   └── updater/                      # Becomes power-plugins/updater
│       ├── composer.json
│       └── src/
└── plugins/
    ├── example-plugin/
    └── commercial-plugin-1/
```

#### Workflow with GitHub Actions

```yaml
# .github/workflows/split-packages.yml
name: Split Packages
on:
  push:
    tags:
      - 'v*'

jobs:
  split:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2

      # Split packages/core to create-element/pp-core repo
      - uses: symplify/monorepo-split-github-action@v2
        with:
          package-directory: 'packages/core'
          split-repository-organization: 'create-element'
          split-repository-name: 'pp-core'
          tag: ${GITHUB_REF#refs/tags/}
```

**Pros:**

- Single repository for all development
- Automated package publishing
- Easy cross-package refactoring

**Cons:**

- Complex setup
- Requires CI/CD infrastructure
- Overkill for 2-3 packages

---

## Recommended Workflow for Power Plugins

### Phase 1: Initial Development (Weeks 1-6)

**Use Option 1 (Library-First) with local symlinks**

```bash
# Setup (one time)
mkdir -p /var/www/projects/{libraries,plugins}

# Clone/create library repos
cd /var/www/projects/libraries
git init pp-core
cd pp-core
# Create initial structure
mkdir -p src/{Settings,Post,Term,UI,Utilities}
# Create composer.json
composer init --name=power-plugins/core

# Clone example-plugin
cd /var/www/projects/plugins
git clone <your-repo>/example-plugin
cd example-plugin

# Link library for development
# Add to composer.json:
{
    "repositories": [
        {
            "type": "path",
            "url": "../../libraries/pp-core",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "power-plugins/core": "@dev"
    }
}

composer install
# vendor/power-plugins/core is now symlinked to ../../libraries/pp-core
```

**Daily Development:**

```bash
# 1. Edit library code
vim /var/www/projects/libraries/pp-core/src/Settings/SettingsCore.php

# 2. Test in WordPress immediately (changes are live via symlink)
# Visit http://localhost/wp-admin

# 3. Run library tests
cd /var/www/projects/libraries/pp-core
composer test

# 4. Commit to library
git add src/
git commit -m "feat: add new feature"
git push origin main
```

---

### Phase 2: First Release (Week 6-7)

**Publish to package repository**

```bash
cd /var/www/projects/libraries/pp-core

# 1. Update version in composer.json
vim composer.json
# "version": "2.0.0"

# 2. Update CHANGELOG.md
vim CHANGELOG.md

# 3. Commit and tag
git add .
git commit -m "chore: release v2.0.0"
git tag v2.0.0
git push origin main --tags

# 4. Build Satis repository (if using)
cd /var/www/packages.power-plugins.com
php bin/satis build satis.json public/

# Or GitHub Packages automatically picks up the tag
```

---

### Phase 3: Production Use (Ongoing)

**Update plugin to use released version**

```bash
cd /var/www/projects/plugins/example-plugin

# Switch from symlink to packaged version
vim composer.json
# Remove "path" repository, add package repository
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.power-plugins.com"
        }
    ],
    "require": {
        "power-plugins/core": "^2.0.0"  # Changed from @dev
    }
}

# Reinstall
rm -rf vendor/
composer install

# Commit updated composer.lock
git add composer.json composer.lock
git commit -m "chore: use packaged power-plugins/core v2.0.0"
```

---

## Development Tools & Tips

### Composer Path Repository (Best for Local Dev)

**composer.json:**

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../../../libraries/pp-core",
      "options": {
        "symlink": true
      }
    }
  ],
  "require": {
    "power-plugins/core": "@dev"
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

**How it works:**

- Composer creates symlink: `vendor/power-plugins/core -> ../../../libraries/pp-core`
- Changes to library are immediately visible in plugin
- No need to run `composer update` after library changes
- Perfect for active development

### Composer Link (Alternative)

```bash
# One-time global link
cd /var/www/projects/libraries/pp-core
composer link

# Use in any project
cd /var/www/projects/plugins/some-plugin
composer link power-plugins/core

# Unlink when done
composer unlink power-plugins/core
```

### Git Submodules (Not Recommended)

```bash
# Avoid this approach - more complex than path repositories
cd /var/www/projects/plugins/example-plugin
git submodule add https://github.com/create-element/pp-core.git lib/pp-core
```

**Why not recommended:**

- Harder to manage than Composer path repos
- Doesn't integrate with Composer workflows
- Submodule update confusion

---

## Practical Example: Adding a New Feature

Let's walk through adding a new method to `SettingsCore`:

### Step 1: Edit Library Code

```bash
cd /var/www/projects/libraries/pp-core
vim src/Settings/SettingsCore.php
```

```php
<?php
namespace PowerPlugins\Core\Settings;

abstract class SettingsCore extends Component
{
  // ... existing code ...

  /**
   * Get JSON-encoded option.
   *
   * @param string $optionName Option key
   * @param array $default Default value if not found
   * @return array Decoded JSON as array
   */
  public function getJson(string $optionName, array $default = []): array
  {
    $value = get_option($optionName, json_encode($default));

    $decoded = json_decode($value, true);
    if (!is_array($decoded)) {
      return $default;
    }

    return $decoded;
  }

  /**
   * Set JSON-encoded option.
   *
   * @param string $optionName Option key
   * @param array $value Value to encode and store
   * @return void
   */
  public function setJson(string $optionName, array $value = []): void
  {
    if (empty($value)) {
      delete_option($optionName);
    } else {
      update_option($optionName, json_encode($value));
    }
  }
}
```

### Step 2: Add Test

```bash
vim tests/Unit/Settings/SettingsCoreTest.php
```

```php
public function testGetSetJson(): void {
    $settings = new ConcreteSettings('test', '1.0.0');

    // Test setting
    $data = ['key' => 'value', 'number' => 42];
    $settings->setJson('test_json', $data);

    // Test getting
    $retrieved = $settings->getJson('test_json');
    $this->assertEquals($data, $retrieved);

    // Test default
    $default = ['default' => true];
    $missing = $settings->getJson('nonexistent', $default);
    $this->assertEquals($default, $missing);
}
```

### Step 3: Run Tests

```bash
cd /var/www/projects/libraries/pp-core
composer test
```

### Step 4: Test in WordPress Plugin

```bash
cd /var/www/projects/plugins/example-plugin
```

```php
// In includes/class-settings.php
use PowerPlugins\Core\Settings\SettingsCore;

class Settings extends SettingsCore
{
  public function saveSettings()
  {
    // Use new method immediately!
    $data = [
      'enabled' => $_POST['enabled'] ?? false,
      'options' => $_POST['options'] ?? []
    ];

    $this->setJson('plugin_config', $data);
  }

  public function getConfig(): array
  {
    return $this->getJson('plugin_config', ['enabled' => false]);
  }
}
```

Test in WordPress:

```bash
# Navigate to http://localhost/wp-admin/options-general.php?page=example-plugin
# Changes are live immediately (via symlink)!
```

### Step 5: Commit to Library

```bash
cd /var/www/projects/libraries/pp-core
git add src/Settings/SettingsCore.php tests/Unit/Settings/SettingsCoreTest.php
git commit -m "feat: add getJson/setJson methods to SettingsCore"
git push origin main
```

### Step 6: Release New Version (When Ready)

```bash
# After several features are ready
cd /var/www/projects/libraries/pp-core

# Update CHANGELOG
vim CHANGELOG.md
# ## [2.1.0] - 2025-11-30
# ### Added
# - `getJson()` and `setJson()` methods in SettingsCore

# Tag and push
git tag v2.1.0
git push origin v2.1.0

# Update package repository
# (Automatic for GitHub Packages, or rebuild Satis)
```

### Step 7: Update Plugin to Use New Version

```bash
cd /var/www/projects/plugins/example-plugin

# Update requirement
vim composer.json
# "power-plugins/core": "^2.1.0"

# Update and test
composer update power-plugins/core
composer test

# Commit
git add composer.json composer.lock
git commit -m "chore: update power-plugins/core to v2.1.0"
```

---

## Directory Structure for Development

### Recommended Setup

```
/var/www/
├── projects/
│   ├── libraries/                    # Library source of truth
│   │   ├── pp-core/
│   │   │   ├── .git/
│   │   │   ├── composer.json
│   │   │   ├── src/
│   │   │   ├── tests/
│   │   │   ├── docs/
│   │   │   └── README.md
│   │   └── pp-updater/
│   │       └── ...
│   │
│   └── plugins/                      # WordPress plugins
│       ├── example-plugin/
│       │   ├── .git/
│       │   ├── composer.json         # Uses path repo during dev
│       │   ├── vendor/
│       │   │   └── power-plugins/
│       │   │       └── core/         # -> symlink to ../../libraries/pp-core
│       │   └── example-plugin.php
│       │
│       └── commercial-plugin-1/
│           └── ...
│
├── packages.power-plugins.com/      # Package repository (optional)
│   ├── satis.json
│   └── public/
│       └── packages.json
│
└── devx.headwall.tech/              # WordPress installation
    └── web/
        └── wp-content/
            └── plugins/
                ├── example-plugin/   # Symlink to /var/www/projects/plugins/example-plugin
                └── ...
```

### Setup Commands

```bash
# 1. Create directory structure
mkdir -p /var/www/projects/{libraries,plugins}

# 2. Create library
cd /var/www/projects/libraries
git init pp-core
cd pp-core
composer init --name=power-plugins/core

# 3. Create example plugin
cd /var/www/projects/plugins
git clone < your-repo > /example-plugin
cd example-plugin

# 4. Link library to plugin
# Edit composer.json (add path repository)
composer install

# 5. Symlink plugin to WordPress
ln -s /var/www/projects/plugins/example-plugin \
  /var/www/devx.headwall.tech/web/wp-content/plugins/example-plugin

# 6. Activate in WordPress
wp plugin activate example-plugin
```

---

## CI/CD Integration

### GitHub Actions for Library

**`.github/workflows/test.yml`:**

```yaml
name: Test Library

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php: ['7.4', '8.0', '8.1', '8.2']

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: mbstring, xml
          coverage: xdebug

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run tests
        run: composer test

      - name: Run PHPCS
        run: composer phpcs

      - name: Run PHPStan
        run: composer phpstan
```

### Auto-publish to Satis

**`.github/workflows/publish.yml`:**

```yaml
name: Publish to Package Repository

on:
  push:
    tags:
      - 'v*'

jobs:
  publish:
    runs-on: ubuntu-latest
    steps:
      - name: Trigger Satis rebuild
        run: |
          curl -X POST https://packages.power-plugins.com/webhook/rebuild \
               -H "Authorization: Bearer ${{ secrets.SATIS_TOKEN }}"
```

---

## Summary: Recommended Workflow

### For Power Plugins (Big Bang Refactor):

1. **Setup:**

   - Create separate `pp-core` and `pp-updater` repositories
   - Use Composer path repositories with symlinks during development
   - Set up private package repository (Satis or GitHub Packages)

2. **Development:**

   - Edit code in library repository
   - Changes immediately visible in linked plugins via symlink
   - Run tests in library repo
   - Test in real WordPress environment via linked plugin

3. **Release:**

   - Tag library releases with semantic versioning
   - Publish to package repository (automatic with GitHub Packages)
   - Update plugins to use specific versions

4. **Benefits:**
   - Clean separation of concerns
   - Professional development workflow
   - Easy to reuse library across plugins
   - Standard Composer practices

### Quick Start Commands

```bash
# Library development
cd /var/www/projects/libraries/pp-core
vim src/SomeClass.php
composer test

# Plugin testing (changes are live immediately)
# Visit WordPress: http://localhost/wp-admin

# Release
git tag v2.1.0 && git push origin v2.1.0

# Update plugin
cd /var/www/projects/plugins/example-plugin
composer update power-plugins/core
```

---

## Naming Convention: GitHub vs Composer

### Quick Reference

| What                | GitHub                          | Composer                       | Example                                  |
| ------------------- | ------------------------------- | ------------------------------ | ---------------------------------------- |
| **Core library**    | `create-element/pp-core`        | `power-plugins/core`           | `composer require power-plugins/core`    |
| **Updater library** | `create-element/pp-updater`     | `power-plugins/updater`        | `composer require power-plugins/updater` |
| **Example plugin**  | `create-element/example-plugin` | `power-plugins/example-plugin` | (plugins usually match)                  |

### Why Keep GitHub and Composer Names Different?

**GitHub Naming:**

- Company organization: `create-element` (your parent company)
- Can use prefixes for organization: `pp-core`, `pp-updater`
- Helps identify related repos in your GitHub org
- Can be more descriptive: `wordpress-plugin-core`, `payment-gateway-helpers`

**Composer Naming:**

- Vendor provides the namespace: `power-plugins/`
- Package names should be clean and simple: `core`, `updater`, `helpers`
- Users type this in `composer require` - shorter is better!

### Real-World Examples

**Your Setup (Power Plugins):**

- GitHub: `create-element/pp-core` (company organization)
- Composer: `power-plugins/core` (product brand namespace)

**Symfony:**

- GitHub: `symfony/symfony` (monorepo)
- Composer: `symfony/console`, `symfony/http-foundation`, `symfony/routing`

**Laravel:**

- GitHub: `laravel/framework`
- Composer: `laravel/framework` (matches in this case)

**WordPress:**

- GitHub: `WordPress/WordPress`
- Composer: `roots/wordpress` (different vendor entirely - maintained by Roots)

### Your composer.json Files

**In `pp-core/composer.json`:**

```json
{
  "name": "power-plugins/core",
  "description": "Core framework for Power Plugins WordPress plugins",
  "type": "library"
}
```

**In `pp-updater/composer.json`:**

```json
{
  "name": "power-plugins/updater",
  "description": "Plugin update checker for Power Plugins API",
  "type": "library"
}
```

**In `example-plugin/composer.json`:**

```json
{
  "name": "power-plugins/example-plugin",
  "type": "wordpress-plugin",
  "require": {
    "power-plugins/core": "^2.0",
    "power-plugins/updater": "^2.0"
  }
}
```

### Path Repositories Handle the Mapping

When you use a path repository, Composer reads the `name` from the target's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../../pp-core"          ← Directory name (GitHub repo name)
        }
    ],
    "require": {
        "power-plugins/core": "@dev"       ← Package name (from pp-core/composer.json)
    }
}
```

Composer looks in `../../pp-core/composer.json` and finds `"name": "power-plugins/core"`, then creates the symlink:

```
vendor/power-plugins/core -> ../../pp-core
```

---

## Questions?

- **Do I need to run `composer update` after changing library code?**  
  No! With symlinked path repository, changes are immediate.

- **When do I switch from symlink to packaged version?**  
  For production deployment or when sharing with other developers.

- **Can multiple plugins use the same symlinked library?**  
  Yes! Each plugin's `vendor/power-plugins/core` symlinks to the same library source.

- **What if I need different library versions in different plugins?**  
  Use Git branches in library repo, or use packaged versions instead of symlinks.

- **Should my GitHub repo name match my Composer package name?**  
  Not required! GitHub: `pp-core`, Composer: `power-plugins/core` works perfectly.
  The vendor name (`power-plugins`) provides uniqueness, not the repo name.

---

**This workflow gives you the best of both worlds: easy development with immediate feedback, and professional package management for production.**
