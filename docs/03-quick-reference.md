# Quick Reference Guide

**Date:** November 25, 2025  
**Purpose:** Quick lookup for developers working with Power Plugins migration

---

## Table of Contents

1. [Quick Decision Matrix](#quick-decision-matrix)
2. [Class Mapping Reference](#class-mapping-reference)
3. [Function Migration Cheatsheet](#function-migration-cheatsheet)
4. [Composer Command Reference](#composer-command-reference)
5. [Common Migration Patterns](#common-migration-patterns)
6. [Troubleshooting Guide](#troubleshooting-guide)

---

## Quick Decision Matrix

### Should I Use Composer for This Plugin?

| Scenario                                | Use Composer? | Approach                                  |
| --------------------------------------- | ------------- | ----------------------------------------- |
| **New plugin starting from scratch**    | ✅ YES        | Use `composer require power-plugins/core` |
| **Existing plugin, active development** | ✅ YES        | Migrate during next major version         |
| **Existing plugin, maintenance mode**   | ⚠️ MAYBE      | Only if fixing PHPCS issues               |
| **Legacy plugin, no changes planned**   | ❌ NO         | Keep using pp-core.php wrapper            |
| **Distributed plugin with customers**   | ⚠️ CAREFUL    | Wait for stable v2.0 + docs               |

### Which Migration Path Should I Choose?

**✅ Decision Made: Path A (Big Bang Refactor)**

| Your Situation                              | Recommended Path     | Why                               |
| ------------------------------------------- | -------------------- | --------------------------------- |
| **10+ plugins to maintain**                 | Path B: Gradual      | Low risk, smooth transition       |
| **2-3 internal plugins**                    | Path C: Package-Only | Fast, clean break                 |
| **Starting fresh product line**             | Path A: Big Bang     | Modern architecture from day 1    |
| **Distributed commercial plugins**          | Path B: Gradual      | Customer stability critical       |
| **✅ Power Plugins (Rewriting for Blocks)** | **Path A: Big Bang** | **Clean start, no legacy burden** |

---

## Class Mapping Reference

### Old (pp-core.php) → New (Composer Package)

| Old Namespace Class               | New Composer Class                        | Notes                  |
| --------------------------------- | ----------------------------------------- | ---------------------- |
| `\Your_Namespace\Component`       | `PowerPlugins\Core\Component`             | Base class unchanged   |
| `\Your_Namespace\Settings_Core`   | `PowerPlugins\Core\Settings\SettingsCore` | Method names camelCase |
| `\Your_Namespace\Post`            | `PowerPlugins\Core\Post\Post`             | Same interface         |
| `\Your_Namespace\Post_Controller` | `PowerPlugins\Core\Post\PostController`   | Cache logic improved   |
| `\Your_Namespace\Term`            | `PowerPlugins\Core\Term\Term`             | Same interface         |
| `\Your_Namespace\Term_Controller` | `PowerPlugins\Core\Term\TermController`   | Same interface         |
| `\Your_Namespace\Meta_Box`        | `PowerPlugins\Core\MetaBox\MetaBox`       | Same interface         |

### Method Name Changes (Settings_Core → SettingsCore)

| Old Method (snake_case)    | New Method (camelCase)  | Change Type |
| -------------------------- | ----------------------- | ----------- |
| `get_settings_cap()`       | `getSettingsCap()`      | Naming only |
| `get_settings_page_name()` | `getSettingsPageName()` | Naming only |
| `get_settings_page_url()`  | `getSettingsPageUrl()`  | Naming only |
| `open_wrap()`              | `openWrap()`            | Naming only |
| `close_wrap()`             | `closeWrap()`           | Naming only |
| `get_string()`             | `getString()`           | Naming only |
| `set_string()`             | `setString()`           | Naming only |
| `get_bool()`               | `getBool()`             | Naming only |
| `set_bool()`               | `setBool()`             | Naming only |
| `get_int()`                | `getInt()`              | Naming only |
| `set_int()`                | `setInt()`              | Naming only |

**Note:** For Path B (Gradual Migration), snake_case methods will be aliased for 2 years.

---

## Function Migration Cheatsheet

### Utility Functions: Old → New

#### Date/Time Functions

```php
// OLD (pp-core.php)
$now = pp_get_now();
$formatted = pp_get_now_h('Y-m-d');
$timespan = pp_time_span_h($date);

// NEW (Composer)
use PowerPlugins\Core\Utilities\DateTimeHelper;

$now = DateTimeHelper::getNow();
$formatted = DateTimeHelper::getNowFormatted('Y-m-d');
$timespan = DateTimeHelper::getTimeSpan($date);
```

#### Network/IP Functions

```php
// OLD
$ip = get_this_ip_address();
$browserIp = pp_get_browser_ip_address();
$ips = pp_get_my_ip_addresses();
$geo = get_this_geo_data();
$country = get_this_country();

// NEW
use PowerPlugins\Core\Utilities\NetworkHelper;

$ip = NetworkHelper::getServerIpAddress();
$browserIp = NetworkHelper::getBrowserIpAddress();
$ips = NetworkHelper::getServerIpAddresses();
$geo = NetworkHelper::getGeoData();
$country = NetworkHelper::getCountryCode();
```

#### String Functions

```php
// OLD
$random = pp_generate_random_string(10);
$alpha = pp_generate_random_alpha_string(8);
$endsWith = pp_str_ends_with($haystack, $needle);
$tag = parse_html_tag('<div class="foo">');

// NEW
use PowerPlugins\Core\Utilities\StringHelper;

$random = StringHelper::generateRandomString(10);
$alpha = StringHelper::generateRandomAlphaString(8);
$endsWith = StringHelper::endsWith($haystack, $needle);
$tag = StringHelper::parseHtmlTag('<div class="foo">');
```

#### Array Functions

```php
// OLD
$result = pp_insert_into_array_before_key($array, 'key', $element);
$result = pp_insert_into_array_after_key($array, 'key', $element);

// NEW
use PowerPlugins\Core\Utilities\ArrayHelper;

$result = ArrayHelper::insertBeforeKey($array, 'key', $element);
$result = ArrayHelper::insertAfterKey($array, 'key', $element);
```

#### Bot Detection

```php
// OLD
$isBot = pp_is_user_agent_a_bot();

// NEW
use PowerPlugins\Core\Utilities\BotDetector;

$isBot = BotDetector::isBot();
```

#### URL/Request Functions

```php
// OLD
$url = get_current_request_url();
$isSafe = pp_is_referrer_this_site();
pp_die_if_bad_referrer();

// NEW
use PowerPlugins\Core\Utilities\RequestHelper;

$url = RequestHelper::getCurrentUrl();
$isSafe = RequestHelper::isReferrerThisSite();
RequestHelper::dieIfBadReferrer();
```

### UI Helper Functions: Old → New

#### Form Elements

```php
// OLD
$html = pp_get_text_input_html('field_name', 'Label', 'value');
$html = pp_get_checkbox_toggle_html('field_name', 'Label', true);
$html = pp_get_select_list_html('field_name', 'Label', $options, 'selected');
$html = pp_get_radio_buttons_html('field_name', 'Label', $options);

// NEW
use PowerPlugins\Core\UI\FormHelper;

$html = FormHelper::renderTextInput('field_name', 'Label', 'value');
$html = FormHelper::renderCheckboxToggle('field_name', 'Label', true);
$html = FormHelper::renderSelectList('field_name', 'Label', $options, 'selected');
$html = FormHelper::renderRadioButtons('field_name', 'Label', $options);
```

#### Spinners & Buttons

```php
// OLD
$html = pp_get_spinner_html(true);
$html = pp_get_button_with_spinner_html('Save', 'button-primary');

// NEW
use PowerPlugins\Core\UI\SpinnerHelper;

$html = SpinnerHelper::getSpinner(true);
$html = SpinnerHelper::getButtonWithSpinner('Save', 'button-primary');
```

#### Other UI Elements

```php
// OLD
$html = pp_get_click_to_copy_html('text', 'Copied!', true);
$html = pp_get_header_logo_html($url, 'tooltip');

// NEW
use PowerPlugins\Core\UI\UIHelper;

$html = UIHelper::getClickToCopy('text', 'Copied!', true);
$html = UIHelper::getHeaderLogo($url, 'tooltip');
```

### Asset Management: Old → New

```php
// OLD
pp_enqueue_admin_assets(['select2']);
pp_enqueue_public_assets();

// NEW
use PowerPlugins\Core\UI\AssetManager;

$assets = new AssetManager('plugin-name', '1.0.0', plugin_dir_url(__FILE__) . 'assets');
$assets->enqueueAdminAssets(['select2']);
$assets->enqueuePublicAssets();
```

---

## Composer Command Reference

### Package Installation

```bash
# Install Core package
composer require power-plugins/core

# Install Core + Updater
composer require power-plugins/core power-plugins/updater

# Install specific version
composer require power-plugins/core:^2.0

# Install with dev dependencies (for development)
composer install

# Install without dev dependencies (for production)
composer install --no-dev
```

### Package Updates

```bash
# Update all packages
composer update

# Update only Power Plugins packages
composer update power-plugins/*

# Update to specific version
composer require power-plugins/core:^2.1
```

### Development Commands

```bash
# Dump autoload (after adding new classes)
composer dump-autoload

# Validate composer.json
composer validate

# Show installed packages
composer show

# Show outdated packages
composer outdated
```

### Code Quality Commands

```bash
# Run PHP Code Sniffer
composer phpcs
# or
vendor/bin/phpcs --standard=PSR12 src/

# Run PHPStan
composer phpstan
# or
vendor/bin/phpstan analyse src/ --level=5

# Run PHPUnit tests
composer test
# or
vendor/bin/phpunit

# Run all quality checks
composer phpcs && composer phpstan && composer test
```

### Documentation Generation

```bash
# Generate API docs
composer docs
# or
vendor/bin/phpdoc -d src/ -t docs/api
```

---

## Common Migration Patterns

### Pattern 1: Basic Plugin with Settings

#### Before (pp-core.php):

```php
<?php
/**
 * Plugin Name: My Plugin
 */

namespace My_Plugin;

define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once MY_PLUGIN_DIR . 'pp-core.php';

class Settings extends Settings_Core
{
  public function __construct()
  {
    parent::__construct('my-plugin', '1.0.0');
  }

  public function save_settings()
  {
    $this->set_bool('enable_feature', $_POST['enable'] ?? false);
  }
}

new Settings();
```

#### After (Composer):

```php
<?php
/**
 * Plugin Name: My Plugin
 */

namespace My_Plugin;

// Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use PowerPlugins\Core\Settings\SettingsCore;

define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));

class Settings extends SettingsCore
{
  public function __construct()
  {
    parent::__construct('my-plugin', '1.0.0');
  }

  public function saveSettings()
  {
    $this->setBool('enable_feature', $_POST['enable'] ?? false);
  }
}

new Settings();
```

#### composer.json:

```json
{
  "name": "my-company/my-plugin",
  "require": {
    "power-plugins/core": "^2.0"
  },
  "repositories": [
    {
      "type": "composer",
      "url": "https://packages.power-plugins.com"
    }
  ],
  "autoload": {
    "psr-4": {
      "My_Plugin\\": "includes/"
    }
  }
}
```

---

### Pattern 2: Custom Post Type

#### Before:

```php
namespace My_Plugin;

require_once MY_PLUGIN_DIR . 'pp-core.php';

class Product extends Post
{
  public function get_price()
  {
    return $this->get_float_meta('_price');
  }
}

class Product_Controller extends Post_Controller
{
  protected function create_post_object(int $post_id)
  {
    return new Product($post_id);
  }

  public function manage_posts_columns($columns)
  {
    $columns['price'] = __('Price', 'my-plugin');
    return $columns;
  }

  public function manage_posts_custom_column($column, $post_id)
  {
    if ($column === 'price') {
      $product = $this->get_post_object($post_id);
      echo esc_html($product->get_price());
    }
  }
}

$products = new Product_Controller('product');
```

#### After:

```php
namespace My_Plugin;

use PowerPlugins\Core\Post\Post;
use PowerPlugins\Core\Post\PostController;

class Product extends Post
{
  public function getPrice(): float
  {
    return $this->getFloatMeta('_price');
  }
}

class ProductController extends PostController
{
  protected function createPostObject(int $postId): Post
  {
    return new Product($postId);
  }

  public function managePostsColumns(array $columns): array
  {
    $columns['price'] = __('Price', 'my-plugin');
    return $columns;
  }

  public function managePostsCustomColumn(string $column, int $postId): void
  {
    if ($column === 'price') {
      $product = $this->getPostObject($postId);
      echo esc_html($product->getPrice());
    }
  }
}

$products = new ProductController('product');
```

---

### Pattern 3: Meta Box

#### Before:

```php
namespace My_Plugin;

class Product_Meta_Box extends Meta_Box
{
  public function __construct()
  {
    parent::__construct('product');

    add_action('add_meta_boxes', [$this, 'add_meta_box']);
    add_action('save_post', [$this, 'save_meta_box'], 10, 2);
  }

  public function add_meta_box()
  {
    add_meta_box('product_details', __('Product Details', 'my-plugin'), [$this, 'render'], 'product', 'normal', 'high');
  }

  public function render($post)
  {
    $this->render_nonce_field();

    echo pp_get_text_input_html('product_price', __('Price', 'my-plugin'), get_post_meta($post->ID, '_price', true));
  }

  public function save_meta_box($post_id, $post)
  {
    if (!$this->is_saving_meta_box($post_id, $post)) {
      return;
    }

    update_post_meta($post_id, '_price', floatval($_POST['product_price']));
  }
}

new Product_Meta_Box();
```

#### After:

```php
namespace My_Plugin;

use PowerPlugins\Core\MetaBox\MetaBox;
use PowerPlugins\Core\UI\FormHelper;

class ProductMetaBox extends MetaBox
{
  public function __construct()
  {
    parent::__construct('product');

    add_action('add_meta_boxes', [$this, 'addMetaBox']);
    add_action('save_post', [$this, 'saveMetaBox'], 10, 2);
  }

  public function addMetaBox(): void
  {
    add_meta_box('product_details', __('Product Details', 'my-plugin'), [$this, 'render'], 'product', 'normal', 'high');
  }

  public function render(\WP_Post $post): void
  {
    $this->renderNonceField();

    echo FormHelper::renderTextInput('product_price', __('Price', 'my-plugin'), get_post_meta($post->ID, '_price', true));
  }

  public function saveMetaBox(int $postId, \WP_Post $post): void
  {
    if (!$this->isSavingMetaBox($postId, $post)) {
      return;
    }

    update_post_meta($postId, '_price', floatval($_POST['product_price']));
  }
}

new ProductMetaBox();
```

---

### Pattern 4: AJAX Handler

#### Before:

```php
namespace My_Plugin;

add_action('wp_ajax_my_custom_action', __NAMESPACE__ . '\\handle_ajax');

function handle_ajax()
{
  pp_die_if_bad_nonce_or_cap('my_action', 'edit_posts');

  $post_id = intval($_POST['post_id']);
  $product = new Product($post_id);

  wp_send_json_success([
    'price' => $product->get_price()
  ]);
}
```

#### After:

```php
namespace My_Plugin;

use PowerPlugins\Core\Ajax\AjaxHandler;

class CustomAjaxHandler extends AjaxHandler
{
  public function __construct()
  {
    parent::__construct('my_custom_action', 'edit_posts');
  }

  protected function handle(): void
  {
    $postId = intval($_POST['post_id']);
    $product = new Product($postId);

    $this->sendSuccess([
      'price' => $product->getPrice()
    ]);
  }
}

new CustomAjaxHandler();
```

---

## Troubleshooting Guide

### Common Issues & Solutions

#### Issue 1: "Class not found" error

```
Fatal error: Class 'PowerPlugins\Core\Settings\SettingsCore' not found
```

**Solution:**

```bash
# Make sure Composer packages are installed
composer install

# Check if autoload is included
# In your main plugin file:
require_once __DIR__ . '/vendor/autoload.php'
```

#### Issue 2: PHPCS errors after migration

```
ERROR: Expected 1 space after closing parenthesis; found 2
```

**Solution:**

```bash
# Auto-fix most issues
vendor/bin/phpcbf --standard=PSR12 includes/

# Or manually review
vendor/bin/phpcs --standard=PSR12 includes/
```

#### Issue 3: Assets not loading

```
Console error: 404 on pp-admin.css
```

**Solution:**

```php
// Make sure asset path points to vendor directory
use PowerPlugins\Core\UI\AssetManager;

$assetsUrl = plugins_url('vendor/power-plugins/core/assets', __FILE__);
$assets = new AssetManager('my-plugin', '1.0.0', $assetsUrl);
```

#### Issue 4: Namespace conflicts

```
Fatal error: Cannot redeclare class Settings_Core
```

**Solution:**

```php
// Don't load both pp-core.php and Composer
// Remove this line:
// require_once 'pp-core.php';

// Use only Composer:
require_once __DIR__ . '/vendor/autoload.php';
```

#### Issue 5: Method name changes break code

```
Fatal error: Call to undefined method saveSettings()
```

**Solution:**

```php
// Check method name changed from snake_case to camelCase
// Old: save_settings()
// New: saveSettings()

// Or use compatibility wrapper temporarily
class Settings extends SettingsCore
{
  public function save_settings()
  {
    return $this->saveSettings();
  }
}
```

#### Issue 6: Composer packages not updating

```bash
# Cache might be stale
composer clear-cache
composer update power-plugins/core

# Or force reinstall
rm -rf vendor/
composer install
```

#### Issue 7: Private package authentication

```
Authentication required for packages.power-plugins.com
```

**Solution:**

```bash
# Add authentication to composer
composer config --global --auth http-basic.packages.power-plugins.com username token

# Or add to composer.json
{
    "config": {
        "http-basic": {
            "packages.power-plugins.com": {
                "username": "your-username",
                "password": "your-token"
            }
        }
    }
}
```

---

## Plugin Compatibility Checklist

Use this checklist when migrating a plugin:

### Pre-Migration

- [ ] Plugin has automated tests (or add them first)
- [ ] Create backup/branch: `git checkout -b feature/composer-migration`
- [ ] Document current functionality
- [ ] Check PHP version compatibility (7.4+)

### During Migration

- [ ] Add `composer.json` to plugin
- [ ] Run `composer install`
- [ ] Add `vendor/` to `.gitignore`
- [ ] Replace `require_once 'pp-core.php'` with `require_once 'vendor/autoload.php'`
- [ ] Update class names and namespaces
- [ ] Update method names (snake_case → camelCase)
- [ ] Update function calls to static methods
- [ ] Update asset paths if needed

### Post-Migration

- [ ] Run PHPCS: `composer phpcs`
- [ ] Run tests: `composer test`
- [ ] Test all admin pages
- [ ] Test all frontend functionality
- [ ] Test AJAX handlers
- [ ] Test meta boxes
- [ ] Check asset loading (CSS/JS)
- [ ] Test in different PHP versions (7.4, 8.0, 8.1)
- [ ] Test in different WP versions (5.9+)

### Documentation

- [ ] Update plugin README
- [ ] Add composer.json documentation
- [ ] Document any breaking changes
- [ ] Update version number
- [ ] Update CHANGELOG

---

## Version Compatibility Matrix

| pp-core.php Version | Composer Package        | PHP Requirement | WP Requirement | Migration Path |
| ------------------- | ----------------------- | --------------- | -------------- | -------------- |
| v1.14.5 (current)   | N/A                     | PHP 7.4+        | WP 5.6+        | Legacy mode    |
| v2.0.0 (Composer)   | power-plugins/core:^2.0 | PHP 7.4+        | WP 5.9+        | Path B or C    |
| v3.0.0 (future)     | power-plugins/core:^3.0 | PHP 8.1+        | WP 6.0+        | Full rewrite   |

---

## Quick Links

- [Main Analysis Document](./01-current-architecture-analysis.md)
- [Migration Strategy](./02-composer-migration-strategy.md)
- Power Plugins Core Repo: (to be created)
- Power Plugins Updater Repo: (to be created)
- API Documentation: (to be generated)

---

**End of Quick Reference Guide**

_Last Updated: November 25, 2025_
