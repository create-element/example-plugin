# Composer Migration Strategy for Power Plugins Core

**Date:** November 25, 2025  
**Target:** Migrate `pp-core.php` and `pwpl` to Composer-managed packages  
**Audience:** Power Plugins developers  
**Decision:** Path A (Big Bang Refactor) chosen - existing plugins being rewritten for WordPress Blocks

---

## Table of Contents

1. [Migration Goals](#migration-goals)
2. [Proposed Package Structure](#proposed-package-structure)
3. [Three Migration Paths](#three-migration-paths)
4. [Recommended Approach](#recommended-approach)
5. [Implementation Phases](#implementation-phases)
6. [File Organization](#file-organization)
7. [Backward Compatibility Strategy](#backward-compatibility-strategy)
8. [Documentation Plan](#documentation-plan)
9. [Testing Strategy](#testing-strategy)
10. [Timeline & Effort Estimates](#timeline--effort-estimates)

---

## Migration Goals

### Primary Objectives

1. ✅ **Pass PHP Code Sniffer:** Split monolithic file into manageable components
2. ✅ **Enable Composer:** Use `composer require power-plugins/core` in new plugins
3. ✅ **Generate Documentation:** PHPDoc-based API docs for developers
4. ✅ **Maintain Stability:** Keep existing API working for legacy plugins
5. ✅ **Improve Maintainability:** Separate concerns, add tests

### Secondary Benefits

- PSR-4 autoloading
- Version locking (`~1.14` vs `~2.0`)
- Easy updates (`composer update`)
- Private package repository option
- Better IDE support (autocomplete, jump-to-definition)

---

## Proposed Package Structure

### Two Composer Packages

#### Package 1: `power-plugins/core`

**Repository:** `create-element/pp-core` (private or GitHub)  
**Purpose:** Core framework classes and utilities

```
create-element/pp-core/
├── composer.json
├── README.md
├── LICENSE (MIT)
├── CHANGELOG.md
├── src/
│   ├── Component.php
│   ├── Settings/
│   │   └── SettingsCore.php
│   ├── Post/
│   │   ├── Post.php
│   │   └── PostController.php
│   ├── Term/
│   │   ├── Term.php
│   │   └── TermController.php
│   ├── MetaBox/
│   │   └── MetaBox.php
│   ├── UI/
│   │   ├── FormHelper.php
│   │   ├── SpinnerHelper.php
│   │   └── AssetManager.php
│   ├── Utilities/
│   │   ├── DateTimeHelper.php
│   │   ├── NetworkHelper.php
│   │   ├── StringHelper.php
│   │   ├── ArrayHelper.php
│   │   └── BotDetector.php
│   └── Ajax/
│       ├── PostSearchHandler.php
│       └── TermSearchHandler.php
├── assets/
│   ├── css/
│   │   ├── pp-admin.css
│   │   ├── pp-public.css
│   │   └── select2.min.css
│   ├── js/
│   │   ├── pp-admin.js
│   │   ├── pp-public.js
│   │   └── pp-select2.js
│   └── images/
│       ├── pp-logo.png
│       └── spinner.svg
└── docs/
    ├── api/  (generated)
    └── guides/
        ├── getting-started.md
        ├── settings-api.md
        ├── custom-post-types.md
        └── form-helpers.md
```

#### Package 2: `power-plugins/updater`

**Repository:** `power-plugins/pwpl`  
**Purpose:** Plugin update checker for Power Plugins API

```
power-plugins/pwpl/
├── composer.json
├── README.md
├── LICENSE
├── src/
│   ├── UpdateChecker.php
│   ├── PluginRegistry.php
│   ├── LicenseManager.php
│   ├── ApiClient.php
│   └── Settings/
│       └── SettingsPage.php
├── assets/
│   └── css/
│       └── updater-settings.css
└── docs/
    ├── installation.md
    ├── license-keys.md
    └── api-integration.md
```

---

## Three Migration Paths

### Path A: Big Bang Refactor

**Description:** Complete rewrite with modern architecture

#### Pros

- ✅ Clean slate - no legacy baggage
- ✅ PSR-12 compliant from day one
- ✅ Modern PHP 8.1+ features (enums, attributes, readonly)
- ✅ Dependency injection container
- ✅ 100% test coverage from start

#### Cons

- ❌ High initial effort (4-6 weeks)
- ❌ Breaking changes for all plugins
- ❌ Requires migrating all existing plugins
- ❌ Risk of introducing new bugs
- ❌ Two codebases to maintain during transition

#### Use Case

- Starting fresh with new plugin line
- Willing to update all existing plugins
- Have time for comprehensive rewrite

---

### Path B: Gradual Extraction (Recommended)

**Description:** Extract classes/functions to Composer packages while maintaining compatibility

#### Pros

- ✅ Low risk - incremental changes
- ✅ Existing plugins work unchanged
- ✅ Can use new packages in new plugins immediately
- ✅ Smooth transition (3-4 week initial setup)
- ✅ Deprecation warnings guide migration

#### Cons

- ⚠️ Temporary dual maintenance (legacy file + packages)
- ⚠️ Some code duplication during transition
- ⚠️ Need compatibility shims

#### Use Case

- **Recommended for Power Plugins**
- Maintain existing plugin ecosystem
- Gradual adoption of Composer

#### Implementation

1. Create Composer packages with PSR-4 classes
2. Keep `pp-core.php` as compatibility wrapper
3. New plugins use Composer packages
4. Migrate old plugins at your pace
5. Deprecate `pp-core.php` after 2 years

---

### Path C: Package-Only (No Legacy Support)

**Description:** Create packages, deprecate monolithic file immediately

#### Pros

- ✅ Forces clean break
- ✅ No dual maintenance
- ✅ Faster to implement (2-3 weeks)
- ✅ Modern architecture only

#### Cons

- ❌ Breaks all existing plugins
- ❌ Requires updating 10+ plugins immediately
- ❌ Customer disruption if distributed plugins
- ❌ High coordination cost

#### Use Case

- Internal-only plugins
- Small number of plugins to update
- Coordinated release window

---

## Chosen Approach: Path A (Big Bang Refactor)

### Why Path A?

**Decision Date:** November 25, 2025

1. **Plugin Rewrites:** Existing plugins need rewriting for WordPress Blocks anyway
2. **Clean Architecture:** No legacy baggage, modern PHP 8.1+ from day one
3. **Perfect Timing:** Coordinated migration with block-based architecture
4. **Future-Proof:** Dependency injection, PSR standards, full test coverage
5. **No Dual Maintenance:** One modern codebase, no compatibility layers

### Phase Overview

```
Phase 1: Foundation (Week 1-2)
  ↓ Create packages, CI/CD, docs
Phase 2: Core Classes (Week 2-3)
  ↓ Extract Settings, Post, Term classes
Phase 3: Utilities (Week 3-4)
  ↓ Extract helper functions
Phase 4: Assets & AJAX (Week 4)
  ↓ Asset management, AJAX handlers
Phase 5: Documentation (Week 5)
  ↓ API docs, guides, examples
Phase 6: Pilot Plugin (Week 6-7)
  ↓ Migrate one plugin as proof-of-concept
Phase 7: Rollout (Month 3-12)
  ↓ Gradual migration of remaining plugins
```

---

## Implementation Phases

### Phase 1: Foundation Setup (Week 1-2)

#### 1.1 Create GitHub Repositories

```bash
# Private repos (or public if open-sourcing)
power-plugins/pp-core
power-plugins/pwpl
```

#### 1.2 Set Up `power-plugins/pp-core`

**composer.json:**

```json
{
  "name": "power-plugins/core",
  "description": "Core framework for Power Plugins WordPress plugins",
  "type": "wordpress-plugin-library",
  "license": "MIT",
  "version": "2.0.0",
  "authors": [
    {
      "name": "Power Plugins",
      "email": "hello@power-plugins.com"
    }
  ],
  "require": {
    "php": ">=7.4",
    "composer/installers": "^2.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.5",
    "squizlabs/php_codesniffer": "^3.7",
    "phpstan/phpstan": "^1.10",
    "phpdocumentor/phpdocumentor": "^3.3"
  },
  "autoload": {
    "psr-4": {
      "PowerPlugins\\Core\\": "src/"
    },
    "files": ["src/functions.php"]
  },
  "autoload-dev": {
    "psr-4": {
      "PowerPlugins\\Core\\Tests\\": "tests/"
    }
  },
  "scripts": {
    "test": "phpunit",
    "phpcs": "phpcs --standard=PSR12 src/",
    "phpstan": "phpstan analyse src/ --level=5",
    "docs": "phpdoc -d src/ -t docs/api"
  }
}
```

#### 1.3 Set Up `power-plugins/pwpl`

**composer.json:**

```json
{
  "name": "power-plugins/updater",
  "description": "Plugin update checker for Power Plugins API",
  "type": "wordpress-plugin-library",
  "license": "Proprietary",
  "version": "2.0.0",
  "require": {
    "php": ">=7.4",
    "power-plugins/core": "^2.0"
  },
  "autoload": {
    "psr-4": {
      "PowerPlugins\\Updater\\": "src/"
    }
  }
}
```

#### 1.4 Configure Private Composer Repository

**Option A: Satis (Self-Hosted)**

```bash
# Install Satis
composer create-project composer/satis --stability=dev

# satis.json
{
    "name": "Power Plugins Private Repository",
    "homepage": "https://packages.power-plugins.com",
    "repositories": [
        { "type": "vcs", "url": "git@github.com:create-element/pp-core.git" },
        { "type": "vcs", "url": "git@github.com:create-element/pp-updater.git" }
    ],
    "require-all": true
}

# Build repository
php bin/satis build satis.json public/
```

**Option B: Private Packagist (~$50/month)**

- Upload packages to https://packagist.com/organizations/
- Get authentication token
- Add to plugin projects

**Option C: GitHub Packages (Free for private repos)**

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/create-element/pp-core.git"
    }
  ]
}
```

---

### Phase 2: Extract Core Classes (Week 2-3)

#### 2.1 Create Class Files

**src/Component.php:**

```php
<?php
namespace PowerPlugins\Core;

class Component
{
  protected string $name;
  protected string $version;

  public function __construct(string $name, string $version)
  {
    $this->name = $name;
    $this->version = $version;
    // ... rest of implementation
  }
}
```

**src/Settings/SettingsCore.php:**

```php
<?php
namespace PowerPlugins\Core\Settings;

use PowerPlugins\Core\Component;

abstract class SettingsCore extends Component
{
  // ... extract from pp-core.php lines 1294-1630
}
```

**Similar files:**

- `src/Post/Post.php`
- `src/Post/PostController.php`
- `src/Term/Term.php`
- `src/Term/TermController.php`
- `src/MetaBox/MetaBox.php`

#### 2.2 Add PHPDoc Comments

```php
/**
 * Core component class for Power Plugins framework.
 *
 * This class serves as the base initialization class for all Power Plugins,
 * handling plugin name/version registration and AJAX action setup.
 *
 * @package PowerPlugins\Core
 * @since 2.0.0
 * @author Power Plugins <hello@power-plugins.com>
 */
class Component
{
  /**
   * Plugin name identifier.
   *
   * @var string
   */
  protected string $name;

  // ... etc
}
```

#### 2.3 Create Compatibility Wrapper

**legacy/pp-core.php** (kept in plugin):

```php
<?php
/**
 * Legacy compatibility wrapper for pp-core.php
 *
 * @deprecated 2.0.0 Use Composer package 'power-plugins/core' instead
 */

namespace Pgd_Docs; // Keep original namespace

if (!class_exists('PowerPlugins\Core\Component')) {
  die('Please run: composer require power-plugins/core');
}

// Class aliases for backward compatibility
class_alias('PowerPlugins\Core\Component', __NAMESPACE__ . '\Component');
class_alias('PowerPlugins\Core\Settings\SettingsCore', __NAMESPACE__ . '\Settings_Core');
class_alias('PowerPlugins\Core\Post\Post', __NAMESPACE__ . '\Post');
class_alias('PowerPlugins\Core\Post\PostController', __NAMESPACE__ . '\Post_Controller');
// ... etc

// Function wrappers
function get_next_control_id()
{
  return \PowerPlugins\Core\UI\FormHelper::getNextControlId();
}

// Deprecation notice
if (defined('WP_DEBUG') && WP_DEBUG) {
  trigger_error('pp-core.php is deprecated. Use Composer: composer require power-plugins/core', E_USER_DEPRECATED);
}
```

---

### Phase 3: Extract Utility Functions (Week 3-4)

#### 3.1 Group Functions into Service Classes

**src/Utilities/DateTimeHelper.php:**

```php
<?php
namespace PowerPlugins\Core\Utilities;

use DateTime;

class DateTimeHelper
{
  private static ?DateTime $now = null;

  /**
   * Get current DateTime with WordPress timezone.
   *
   * @return DateTime Current date/time
   */
  public static function getNow(): DateTime
  {
    if (self::$now === null) {
      self::$now = new DateTime('now', wp_timezone());
    }
    return self::$now;
  }

  /**
   * Format current time as human-readable string.
   *
   * @param string $format DateTime format (default: 'Y-m-d H:i:s T')
   * @return string Formatted date string
   */
  public static function getNowFormatted(string $format = 'Y-m-d H:i:s T'): string
  {
    return self::getNow()->format($format);
  }

  /**
   * Get human-readable time span between two dates.
   *
   * @param DateTime|null $reference Reference date
   * @param DateTime|null $now Current date (default: now)
   * @return string Time span string (e.g., "5 days", "> 1 year")
   */
  public static function getTimeSpan(?DateTime $reference, ?DateTime $now = null): string
  {
    // ... implementation
  }
}
```

**src/Utilities/NetworkHelper.php:**

```php
<?php
namespace PowerPlugins\Core\Utilities;

class NetworkHelper
{
  private static ?string $serverIp = null;
  private static ?array $serverIps = null;

  public static function getServerIpAddress(int $protocol = 0): ?string
  {
    /* ... */
  }
  public static function getBrowserIpAddress(): ?string
  {
    /* ... */
  }
  public static function getGeoData(): ?array
  {
    /* ... */
  }
  public static function getCountryCode(): ?string
  {
    /* ... */
  }
}
```

**src/Utilities/StringHelper.php:**

```php
<?php
namespace PowerPlugins\Core\Utilities;

class StringHelper
{
  public static function generateRandomAlphaString(int $length): string
  {
    /* ... */
  }
  public static function generateRandomString(int $length, bool $alphanumeric = false): string
  {
    /* ... */
  }
  public static function parseHtmlTag(string $tag): array
  {
    /* ... */
  }
  public static function endsWith(string $haystack, string $needle): bool
  {
    /* ... */
  }
}
```

**src/Utilities/BotDetector.php:**

```php
<?php
namespace PowerPlugins\Core\Utilities;

class BotDetector
{
  private const BOT_REGEX = '/Unknown Bot|^Ruby|Crawlson|.../';
  private static ?bool $isBot = null;

  public static function isBot(): bool
  {
    /* ... */
  }
}
```

#### 3.2 Legacy Function Wrappers

**src/functions.php** (autoloaded via composer.json):

```php
<?php
/**
 * Global function wrappers for backward compatibility.
 *
 * @deprecated 2.0.0 Use static class methods instead
 */

if (!function_exists('pp_get_now')) {
  function pp_get_now(): DateTime
  {
    return \PowerPlugins\Core\Utilities\DateTimeHelper::getNow();
  }
}

if (!function_exists('pp_is_user_agent_a_bot')) {
  function pp_is_user_agent_a_bot(): bool
  {
    return \PowerPlugins\Core\Utilities\BotDetector::isBot();
  }
}

// ... etc for all 40 functions
```

---

### Phase 4: UI & Asset Management (Week 4)

#### 4.1 Form Helper Class

**src/UI/FormHelper.php:**

```php
<?php
namespace PowerPlugins\Core\UI;

class FormHelper
{
  private static int $controlIndex = 1;

  public static function getNextControlId(): string
  {
    return 'ppctx' . self::$controlIndex++;
  }

  public static function renderSelectList(string $name, string $label, array $options, string $value = '', string $helpText = '', string $classes = ''): string
  {
    // ... implementation from pp_get_select_list_html()
  }

  public static function renderCheckbox(string $name, string $label, bool $checked = false, bool $hasFollowingSection = false, string $classes = ''): string
  {
    // ... implementation
  }

  // ... all other form helpers
}
```

#### 4.2 Asset Manager

**src/UI/AssetManager.php:**

```php
<?php
namespace PowerPlugins\Core\UI;

class AssetManager
{
  private string $pluginName;
  private string $version;
  private string $assetsUrl;
  private bool $adminEnqueued = false;
  private bool $publicEnqueued = false;

  public function __construct(string $pluginName, string $version, string $assetsUrl)
  {
    $this->pluginName = $pluginName;
    $this->version = $version;
    $this->assetsUrl = trailingslashit($assetsUrl);
  }

  public function enqueueAdminAssets(array $extras = []): void
  {
    if ($this->adminEnqueued) {
      return;
    }

    $handle = 'pp-' . $this->pluginName;

    wp_enqueue_style($handle, $this->assetsUrl . 'css/pp-admin.css', null, $this->version);
    wp_enqueue_script($handle, $this->assetsUrl . 'js/pp-admin.js', ['jquery', 'jquery-ui-autocomplete'], $this->version);

    wp_localize_script($handle, 'pwplData', [
      'ajaxUrl' => admin_url('admin-ajax.php')
      // ... etc
    ]);

    if (in_array('select2', $extras)) {
      $this->enqueueSelect2();
    }

    $this->adminEnqueued = true;
  }

  private function enqueueSelect2(): void
  {
    wp_enqueue_script('select2', $this->assetsUrl . 'js/select2.min.js', ['jquery'], '4.1.0-rc.0');
    wp_enqueue_style('select2', $this->assetsUrl . 'css/select2.min.css', [], '4.1.0-rc.0');
  }
}
```

---

### Phase 5: Documentation Generation (Week 5)

#### 5.1 Install phpDocumentor

```bash
composer require --dev phpdocumentor/phpdocumentor
```

#### 5.2 Create Documentation Config

**phpdoc.xml:**

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
    <title>Power Plugins Core API Documentation</title>
    <parser>
        <target>docs/cache</target>
    </parser>
    <transformer>
        <target>docs/api</target>
    </transformer>
    <files>
        <directory>src</directory>
        <ignore>vendor/*</ignore>
    </files>
</phpdocumentor>
```

#### 5.3 Generate Docs

```bash
composer docs
# or
vendor/bin/phpdoc -d src/ -t docs/api --template=clean
```

#### 5.4 Create Developer Guides

**docs/guides/getting-started.md:**

````markdown
# Getting Started with Power Plugins Core

## Installation

```bash
composer require power-plugins/core
```
````

## Basic Usage

```php
use PowerPlugins\Core\Settings\SettingsCore;

class MyPluginSettings extends SettingsCore
{
  public function __construct()
  {
    parent::__construct('my-plugin', '1.0.0');
  }

  public function save_settings()
  {
    $this->set_bool('enable_feature', $_POST['enable_feature'] ?? false);
  }
}
```

````

**docs/guides/custom-post-types.md:**
**docs/guides/form-helpers.md:**
**docs/guides/meta-boxes.md:**

---

### Phase 6: Pilot Plugin Migration (Week 6-7)

#### 6.1 Choose Simple Plugin
Pick a plugin with:
- Low complexity
- Few dependencies
- Active development

#### 6.2 Add Composer to Plugin
**composer.json:**
```json
{
    "name": "power-plugins/example-plugin",
    "require": {
        "power-plugins/core": "^2.0",
        "power-plugins/updater": "^2.0"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.power-plugins.com"
        }
    ]
}
````

#### 6.3 Update Plugin Main File

**Before:**

```php
namespace Plugin_Name;

require_once PP_DIR . 'pp-core.php';
require_once PP_DIR . 'pwpl/pwpl.php';

class My_Settings extends Settings_Core
{
  /* ... */
}
```

**After:**

```php
namespace Plugin_Name;

require_once __DIR__ . '/vendor/autoload.php';

use PowerPlugins\Core\Settings\SettingsCore;
use PowerPlugins\Updater\UpdateChecker;

class My_Settings extends SettingsCore
{
  /* ... */
}

// Initialize updater
$updater = new UpdateChecker('example-plugin', '1.0.6');
```

#### 6.4 Test Migration

```bash
composer install
wp plugin activate example-plugin
# Test all functionality
# Run PHP_CodeSniffer
vendor/bin/phpcs --standard=PSR12 includes/
```

---

## File Organization

### Power Plugins Core Structure

```
src/
├── Component.php                    # Base component class
├── Settings/
│   └── SettingsCore.php            # Settings page framework
├── Post/
│   ├── Post.php                     # Custom post type base
│   └── PostController.php           # CPT management & caching
├── Term/
│   ├── Term.php                     # Taxonomy term base
│   └── TermController.php           # Taxonomy management
├── MetaBox/
│   └── MetaBox.php                  # Meta box framework
├── UI/
│   ├── FormHelper.php               # Form element generators
│   ├── SpinnerHelper.php            # Loading spinners
│   └── AssetManager.php             # CSS/JS enqueuing
├── Utilities/
│   ├── DateTimeHelper.php           # Date/time functions
│   ├── NetworkHelper.php            # IP/geo functions
│   ├── StringHelper.php             # String manipulation
│   ├── ArrayHelper.php              # Array operations
│   └── BotDetector.php              # Bot detection
├── Ajax/
│   ├── PostSearchHandler.php        # Post search AJAX
│   └── TermSearchHandler.php        # Term search AJAX
└── functions.php                    # Legacy function wrappers
```

### Example Plugin Structure (New)

```
example-plugin/
├── composer.json                    # Composer dependencies
├── example-plugin.php               # Main plugin file
├── vendor/                          # Composer packages (gitignored)
│   ├── autoload.php
│   └── power-plugins/
│       ├── core/
│       └── updater/
├── includes/
│   ├── class-settings.php
│   ├── class-custom-post.php
│   └── class-meta-box.php
├── assets/
│   ├── css/
│   └── js/
└── templates/
```

---

## Backward Compatibility Strategy

### For Existing Plugins (Legacy Mode)

#### Option 1: Keep `pp-core.php` Wrapper

```php
// example-plugin.php
require_once PP_DIR . 'pp-core.php'; // Still works!

// pp-core.php now loads Composer and creates aliases
require_once __DIR__ . '/vendor/autoload.php';
class_alias('PowerPlugins\Core\Settings\SettingsCore', 'Settings_Core');
```

#### Option 2: Include Composer in Plugin

```php
// example-plugin.php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
  // New Composer mode
  require_once __DIR__ . '/vendor/autoload.php';
} else {
  // Legacy mode
  require_once __DIR__ . '/pp-core.php';
}
```

### Deprecation Timeline

```
v2.0.0 (2025 Q1): Composer packages released, legacy wrapper works
v2.1.0 (2025 Q2): Deprecation notices in WP_DEBUG mode
v2.2.0 (2025 Q3): Loud deprecation warnings
v3.0.0 (2027 Q1): Remove legacy wrapper (2 years later)
```

---

## Documentation Plan

### 1. API Documentation (Generated)

**Tool:** phpDocumentor  
**Output:** `docs/api/` (HTML)  
**Hosting:** GitHub Pages or `docs.power-plugins.com`

**What it includes:**

- All classes, methods, properties
- Parameter types, return types
- Code examples from docblocks
- Cross-references between classes

### 2. Developer Guides (Manual)

**Format:** Markdown  
**Location:** `docs/guides/`

**Topics:**

- Getting Started (installation, basic usage)
- Settings API (creating settings pages)
- Custom Post Types (extending Post/PostController)
- Taxonomies (extending Term/TermController)
- Meta Boxes (using MetaBox class)
- Form Helpers (UI components)
- Utilities (helper functions)
- AJAX Handlers (post/term search)
- Migration Guide (pp-core.php to Composer)

### 3. README Files

**Power-plugins/core README.md:**

````markdown
# Power Plugins Core

Core framework for building WordPress plugins with Power Plugins best practices.

## Installation

```bash
composer require power-plugins/core
```
````

## Quick Start

```php
use PowerPlugins\Core\Settings\SettingsCore;

class MySettings extends SettingsCore
{
  // ... implementation
}
```

## Documentation

- [API Reference](https://docs.power-plugins.com/core/api/)
- [Developer Guide](https://docs.power-plugins.com/core/guides/)
- [Migration from pp-core.php](https://docs.power-plugins.com/core/guides/migration.html)

## License

MIT - See LICENSE file

```

### 4. Code Examples Repository
**power-plugins/examples:**
```

examples/
├── basic-plugin/ # Minimal plugin using Core
├── custom-post-type/ # CPT example
├── settings-page/ # Settings page example
├── meta-boxes/ # Meta box example
└── ajax-search/ # AJAX search example

```

---

## Testing Strategy

### Unit Tests (PHPUnit)

#### Directory Structure
```

tests/
├── Unit/
│ ├── Settings/
│ │ └── SettingsCoreTest.php
│ ├── Post/
│ │ ├── PostTest.php
│ │ └── PostControllerTest.php
│ ├── Utilities/
│ │ ├── DateTimeHelperTest.php
│ │ └── StringHelperTest.php
│ └── UI/
│ └── FormHelperTest.php
└── Integration/
├── WordPressIntegrationTest.php
└── AssetEnqueueTest.php

````

#### Example Test
**tests/Unit/Utilities/StringHelperTest.php:**
```php
<?php
namespace PowerPlugins\Core\Tests\Unit\Utilities;

use PHPUnit\Framework\TestCase;
use PowerPlugins\Core\Utilities\StringHelper;

class StringHelperTest extends TestCase {
    public function testGenerateRandomAlphaString() {
        $result = StringHelper::generateRandomAlphaString(10);

        $this->assertIsString($result);
        $this->assertEquals(10, strlen($result));
        $this->assertMatchesRegularExpression('/^[a-zA-Z]+$/', $result);
    }

    public function testEndsWith() {
        $this->assertTrue(StringHelper::endsWith('hello.php', '.php'));
        $this->assertFalse(StringHelper::endsWith('hello.php', '.js'));
    }
}
````

### WordPress Integration Tests

**Uses WordPress test framework:**

```bash
# Set up WordPress test environment
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# Run tests
vendor/bin/phpunit --testsuite=integration
```

### Code Quality Tools

```bash
# PHP Code Sniffer
composer phpcs

# PHPStan (static analysis)
composer phpstan

# All checks
composer test && composer phpcs && composer phpstan
```

---

## Timeline & Effort Estimates

### Initial Setup (Weeks 1-5)

| Phase     | Task                       | Hours   | Deliverable                 |
| --------- | -------------------------- | ------- | --------------------------- |
| **1**     | Repository setup           | 4h      | GitHub repos, composer.json |
| **1**     | Private package repository | 8h      | Satis or Private Packagist  |
| **2**     | Extract core classes       | 16h     | 6 class files with PSR-4    |
| **2**     | Add PHPDoc comments        | 8h      | Documented classes          |
| **3**     | Extract utility functions  | 12h     | 5 helper classes            |
| **3**     | Create function wrappers   | 4h      | Legacy compatibility        |
| **4**     | UI & asset management      | 8h      | FormHelper, AssetManager    |
| **4**     | AJAX handlers              | 4h      | PostSearchHandler, etc.     |
| **5**     | Generate API docs          | 4h      | phpDocumentor setup         |
| **5**     | Write developer guides     | 12h     | 5-6 guide documents         |
| **TOTAL** |                            | **80h** | ~2 weeks (1 developer)      |

### Pilot Migration (Weeks 6-7)

| Phase     | Task                         | Hours   | Deliverable           |
| --------- | ---------------------------- | ------- | --------------------- |
| **6**     | Add Composer to pilot plugin | 2h      | composer.json         |
| **6**     | Update plugin code           | 4h      | Use new namespaces    |
| **6**     | Test functionality           | 4h      | QA all features       |
| **6**     | Run code sniffer             | 2h      | PSR-12 compliance     |
| **7**     | Fix issues                   | 4h      | Bug fixes             |
| **7**     | Documentation                | 4h      | Migration notes       |
| **TOTAL** |                              | **20h** | ~1 week (1 developer) |

### Ongoing Migration (Months 3-12)

| Task         | Per Plugin | 10 Plugins |
| ------------ | ---------- | ---------- |
| Add Composer | 1h         | 10h        |
| Update code  | 3h         | 30h        |
| Test & fix   | 2h         | 20h        |
| **Total**    | **6h**     | **60h**    |

### Grand Total: ~160 hours (~4 weeks)

- Initial setup: 80h
- Pilot plugin: 20h
- 10 plugins: 60h

---

## Summary & Recommendations

### Recommended Path Forward

1. **Week 1-2:** Set up Composer packages and private repository
2. **Week 2-3:** Extract core classes with full documentation
3. **Week 3-4:** Extract utilities and create wrappers
4. **Week 4:** Complete UI helpers and asset management
5. **Week 5:** Generate docs and write guides
6. **Week 6-7:** Migrate one pilot plugin as proof-of-concept
7. **Month 3-12:** Gradually migrate remaining plugins

### Immediate Next Steps

1. ✅ **Review this document** with the team
2. ✅ **Decide on package hosting** (Satis, Private Packagist, or GitHub)
3. ✅ **Create GitHub repositories** for `pp-core` and `pwpl`
4. ✅ **Set up local dev environment** with Composer
5. ✅ **Start Phase 1** (Foundation Setup)

### Success Criteria

- ✅ PHP_CodeSniffer passes on all package code
- ✅ API documentation generated and published
- ✅ Pilot plugin migrated and working
- ✅ Existing plugins continue working (no breaks)
- ✅ New plugins can use `composer require power-plugins/core`

### Risk Mitigation

1. **Testing:** Write tests early to catch regressions
2. **Backward Compatibility:** Maintain wrapper for 2 years
3. **Documentation:** Comprehensive migration guide
4. **Pilot Testing:** Thoroughly test first migration
5. **Gradual Rollout:** Don't rush to migrate all plugins

---

## Appendix: Alternative Architectures

### A1: Service Container (Advanced)

For v3.0, consider adding PSR-11 dependency injection:

```php
$container = new \PowerPlugins\Core\Container();
$container->register('settings', fn() => new MySettings());
```

### A2: WordPress Plugin Boilerplate Integration

Align with WPPB structure for familiarity:

```
includes/
├── class-plugin-name-loader.php
├── class-plugin-name-i18n.php
├── class-plugin-name.php
admin/
public/
```

### A3: Namespaced Functions (PHP 8.0+)

For v3.0, use namespaced functions instead of static classes:

```php
namespace PowerPlugins\Core\Utilities;

function generateRandomString(int $length): string
{
  /* ... */
}
```

---

## Questions for Discussion

1. **Package Hosting:** Self-hosted Satis, Private Packagist, or GitHub Packages?
2. **PHP Version:** Require PHP 7.4, 8.0, or 8.1+?
3. **Open Source:** MIT license for Core, proprietary for Updater?
4. **Testing:** Priority for unit tests or integration tests first?
5. **Timeline:** 4-week sprint or slower 8-week rollout?

---

**End of Document**

_For questions, contact: hello@power-plugins.com_
