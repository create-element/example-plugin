# Power Plugins Core - Current Architecture Analysis

**Date:** November 25, 2025  
**Version Analyzed:** PP-Core v1.14.5  
**Purpose:** Document the current state of `pp-core.php` and `pwpl` libraries for refactoring to Composer-based architecture

---

## Executive Summary

The Power Plugins Core (`pp-core.php`) is a monolithic 2,336-line PHP file that provides a comprehensive framework for WordPress plugin development. While functional and stable, it suffers from:

1. **Single-file architecture** - Makes it difficult to maintain, test, and pass PHP Code Sniffer checks
2. **Namespace coupling** - Uses the parent plugin's namespace (`Pgd_Docs` in the example)
3. **No dependency management** - Manual inclusion via `require_once`
4. **Limited documentation** - Inline comments only, no generated API docs
5. **Mixed concerns** - Utilities, classes, and WordPress hooks all in one file

---

## File Structure Overview

### Core Library (`pp-core.php`)

**Location:** `/pp-core.php`  
**Lines:** 2,336  
**License:** MIT  
**Current Namespace:** Uses parent plugin's namespace (e.g., `Pgd_Docs`)

### Updater Library (`pwpl/`)

**Location:** `/pwpl/`  
**Purpose:** Plugin update checker for Power Plugins repository
**Files:**

- `pwpl.php` - Entry point
- `constants.php` - Configuration constants
- `functions.php` - Helper functions
- `settings.php` - Settings interface
- `update-checker.php` - Main update logic (328 lines)

### Assets (`pp-assets/`)

**Location:** `/pp-assets/`  
**Contents:**

- CSS files: `pp-admin.css`, `pp-public.css`, `select2.min.css`
- JavaScript files: `pp-admin.js`, `pp-public.js`, `pp-select2.js`, `select2.min.js`
- Images: `pp-logo.png`, `spinner.svg`
- Security: `index.php` (prevents directory listing)

---

## Core Components Breakdown

### 1. Constants and Configuration (Lines 1-77)

```php
const PP_CORE_NAME = 'pwpl';
const PP_CORE_VERSION = '1.14.5';
const PP_CORE_DATE = '2025-09-24';
```

**Purpose:** Version tracking, asset paths, default values
**Issues:** Hardcoded in parent namespace

### 2. Utility Functions (Lines 84-760)

**Count:** ~40 utility functions

**Categories:**

- **UI Generation:** Control IDs, spinners, buttons, form elements
- **String Manipulation:** Random strings, HTML tag parsing, string helpers
- **Date/Time:** DateTime helpers, time span formatting
- **Network:** IP address detection, geolocation
- **WordPress Helpers:** URL helpers, referrer checks, bot detection
- **Array Manipulation:** Insert before/after key operations

**Example Functions:**

- `get_next_control_id()` - Generate unique control IDs
- `pp_is_user_agent_a_bot()` - Bot detection with regex
- `pp_get_now()` - Get current DateTime with WP timezone
- `get_this_ip_address()` - Server IP detection with caching
- `pp_get_click_to_copy_html()` - UI component generator

### 3. HTML Form Helpers (Lines 760-950)

**Purpose:** Generate WordPress admin form elements

**Functions:**

- `pp_get_select_list_html()` - Dropdown/select fields
- `pp_get_radio_buttons_html()` - Radio button groups (text/image)
- `pp_get_checkbox_toggle_html()` - Toggle switches
- `pp_get_text_input_html()` - Text input fields
- `pp_get_item_chooser_html()` - Post/term picker with autocomplete
- `pp_get_button_with_spinner_html()` - Loading buttons

### 4. Asset Management (Lines 950-1000)

**Functions:**

- `pp_enqueue_public_assets()` - Frontend CSS/JS
- `pp_enqueue_admin_assets()` - Admin CSS/JS with extras (Select2)
- Asset URL/path constants

**Features:**

- jQuery UI autocomplete integration
- Select2 dropdown enhancement
- Dashicons dependency
- Localized JavaScript data for AJAX

### 5. AJAX Handlers (Lines 1000-1200)

**Purpose:** Admin-side AJAX endpoints

**Actions:**

- `pp_search_posts_or_terms()` - Search posts by type or taxonomy terms
- `pp_get_post_and_term_metas()` - Retrieve post/term metadata
- `pp_die_if_bad_nonce_or_cap()` - Security helper

**Security:** Nonce verification, capability checks

### 6. Abstract Classes (Lines 1264-2336)

#### a. `Component` Class (Line 1264)

**Purpose:** Base initialization class
**Responsibilities:**

- Plugin name/version storage
- AJAX action registration
- Constants definition (`PP_HOST_PLUGIN_NAME`, `PP_SEARCH_POSTS_OR_TERMS_ACTION`)

#### b. `Settings_Core` Class (Line 1294)

**Purpose:** Settings page framework
**Methods:** 336 lines of settings management

- Option getters/setters (string, int, float, bool, array, color, date, datetime)
- Form rendering (open/close wrap, nonce fields)
- Hook option management
- Sanitization framework

**Supported Data Types:**

- `get/set_string()`, `get/set_int()`, `get/set_float()`
- `get/set_bool()`, `get/set_array()`
- `get/set_colour_hex()` - Color picker values
- `get/set_datetime_string()`, `get/set_date_string()`
- `get/set_hook_option()` - WordPress hook configuration

#### c. `Post` Class (Line 1630)

**Purpose:** Custom Post Type abstraction
**Methods:** ~130 lines

- Title/slug/content getters/setters
- Thumbnail management
- Meta field handling (bool, int, string)
- CSV column storage (comma-separated meta values)

**Features:**

- Type-safe meta field access
- Automatic serialization for arrays
- Thumbnail ID management

#### d. `Post_Controller` Class (Line 1760)

**Purpose:** Post type management and caching
**Methods:** ~256 lines

- Post object factory with LRU cache
- Bulk operations (`get_all()`, `get_all_ids()`)
- Query by meta, slug, or ID
- Admin columns integration
- Cache management (configurable size, default 20 objects)

**Features:**

- Object caching to reduce DB queries
- Abstract methods for column management
- Post creation with `create_new_post_object()`

#### e. `Term` Class (Line 2016)

**Purpose:** Taxonomy term abstraction
**Methods:** ~90 lines

- Title/slug getters
- Thumbnail management (term meta)
- Meta field handling (bool, int, string)

#### f. `Term_Controller` Class (Line 2106)

**Purpose:** Taxonomy management
**Methods:** ~160 lines

- Term object factory (no caching)
- Query by meta, slug, or ID
- Post terms retrieval
- Admin columns integration

#### g. `Meta_Box` Class (Line 2267)

**Purpose:** Meta box framework
**Methods:** ~70 lines

- Multi-post-type support
- Nonce generation/verification
- Save detection with capability checks
- Autosave prevention

---

## Dependencies

### WordPress Core

- `wp_enqueue_script()`, `wp_enqueue_style()`
- `get_posts()`, `get_terms()`, `wp_insert_post()`, `wp_insert_term()`
- `get_post_meta()`, `update_post_meta()`, `get_term_meta()`, `update_term_meta()`
- `wp_nonce_field()`, `wp_verify_nonce()`, `current_user_can()`
- `get_transient()`, `set_transient()` (caching)

### Optional WordPress

- **WooCommerce detection:** `is_woocommerce_available()` checks for `WC()` function
- **jQuery UI:** Autocomplete for post/term pickers
- **Dashicons:** Icon font

### PHP Extensions

- **cURL:** Used for IP detection and geolocation
- **DateTime:** Timezone-aware date handling
- **JSON:** Encoding/decoding for AJAX and caching

### Third-Party Libraries

- **Select2 4.1.0-rc.0:** Enhanced dropdowns (bundled in `pp-assets/`)

---

## Key Design Patterns

### 1. Abstract Factory Pattern

- `Post_Controller` creates `Post` objects
- `Term_Controller` creates `Term` objects
- Plugins extend these to create domain-specific models

### 2. Template Method Pattern

- Abstract classes define structure
- Subclasses implement specifics (e.g., `save_settings()`, `create_post_object()`)

### 3. Singleton-like Globals

```php
global $pp_is_a_bot; // Bot detection cache
global $pp_control_index; // Control ID counter
global $pwpl_this_ip_address; // IP address cache
```

### 4. Transient Caching

- IP addresses: 12-hour cache
- Geolocation data: 1-week cache
- Plugin update info: Configurable TTL

### 5. Object Caching (LRU)

- `Post_Controller`: 20-object default cache
- `Term_Controller`: No caching (could be added)

---

## Code Quality Issues

### 1. PHP Code Sniffer Failures

**Likely violations:**

- **File length:** 2,336 lines exceeds typical limits (300-500 lines)
- **Function length:** Some functions exceed 50-100 line limits
- **Cyclomatic complexity:** Nested conditionals in utility functions
- **Missing docblocks:** Limited PHPDoc comments
- **Naming conventions:** Mix of snake_case and camelCase
- **Inline HTML:** String concatenation for HTML generation

### 2. Namespace Pollution

- 40+ functions in parent plugin's namespace
- Risk of naming conflicts
- Hard to track function origins

### 3. Mixed Responsibilities

- Utilities, UI, AJAX, classes all in one file
- No clear separation of concerns

### 4. Testing Challenges

- Difficult to unit test monolithic file
- Global state dependencies
- WordPress function dependencies (hard to mock)

### 5. Documentation Gaps

- No generated API documentation
- Limited inline comments
- No usage examples
- No changelog

---

## Current Usage Pattern

### In Plugin Main File

```php
namespace Plugin_Name;

require_once PP_EXP_DIR . 'pp-core.php'; // Loads into current namespace

class My_Settings extends Settings_Core
{
  public function save_settings()
  {
    $this->set_bool('my_option', $_POST['my_checkbox'] ?? false);
  }
}

class My_Post extends Post
{
  public function get_custom_field()
  {
    return $this->get_string_meta('_custom_field');
  }
}
```

**Issues:**

1. No version pinning
2. Namespace inherited from parent
3. No autoloading
4. Asset paths hardcoded

---

## Power Plugins Updater (`pwpl/`) Analysis

### Structure

**Entry Point:** `pwpl/pwpl.php`

- Loads constants, functions, settings, and update checker
- Defines `PWPLUC_NAME`, `PWPLUC_VERSION` (1.1.1)

### Update Checker (`pwpl/update-checker.php`)

**Lines:** 328  
**Purpose:** Check for plugin updates from `power-plugins.com`

**Key Functions:**

- `update_power_plugins()` - Filter `update_plugins_power-plugins.com`
- `custom_plugins_api_result()` - Plugin information modal
- `alert_for_missing_licence_keys()` - Admin notices
- `get_available_power_plugins()` - Fetch from API with transient cache
- `flush_power_plugins_cache()` - Clear cache on upgrades

**Features:**

- Transient caching (configurable TTL)
- License key authentication
- Download URL generation with tokens
- Admin settings page integration

### Dependencies

- WordPress HTTP API (`wp_remote_get()`)
- WordPress Transients API
- Plugin Update API hooks

---

## Asset Management

### Admin Assets (`pp-admin.css`, `pp-admin.js`)

**Purpose:** Admin UI styling and JavaScript
**Features:**

- Post/term autocomplete pickers
- Click-to-copy functionality
- Toggle switches
- Loading spinners
- Quick popups

**JavaScript Globals:**

```javascript
window.pwplData = {
  ajaxUrl: admin_url('admin-ajax.php'),
  quickPopupTtl: 1000,
  searchPostOrTerms: { action, nonce },
  getPostAndTermMeta: { action, nonce },
};
```

### Public Assets (`pp-public.css`, `pp-public.js`)

**Purpose:** Frontend styling and behavior
**Dependencies:** jQuery, Dashicons

### Select2 Integration (`pp-select2.js`)

**Purpose:** Initialize Select2 on `pp-select2` class elements
**Version:** 4.1.0-rc.0 (bundled)

---

## Security Considerations

### Nonce Verification

- All AJAX handlers verify nonces
- Settings pages use action/nonce pairs
- Meta boxes have dedicated nonce fields

### Capability Checks

- Admin functions check `current_user_can()`
- Configurable capabilities (e.g., `manage_options`, `edit_posts`)

### Input Sanitization

- Text fields: `sanitize_text_field()`, `trim()`
- URLs: `esc_url()`, `esc_attr()`
- HTML output: `esc_html()`
- Colors: `sanitize_hex_color()`

### SQL Injection Prevention

- Uses WordPress APIs (`get_posts()`, `get_terms()`)
- No raw SQL queries

### XSS Prevention

- All output escaped
- HTML generation uses `sprintf()` with `esc_*()` functions

---

## Performance Considerations

### Caching Strategies

1. **Object Cache:** Post objects (20 items default)
2. **Transient Cache:** IP addresses (12 hours), geolocation (1 week), plugin updates
3. **WordPress Object Cache:** Compatible with Redis/Memcached

### Optimization Techniques

- `wp_suspend_cache_addition()` for bulk queries
- Lazy loading of globals
- Early returns in conditionals

### Potential Bottlenecks

- cURL requests for IP/geolocation (blocking)
- Large result sets without pagination
- No query result caching for terms

---

## Integration Points

### WordPress Hooks Used

- `admin_notices` - License key alerts
- `upgrader_process_complete` - Cache flushing
- `update_plugins_{$hostname}` - Update checks
- `plugins_api_result` - Plugin info modal
- `wp_ajax_{$action}` - AJAX handlers
- `manage_{$post_type}_posts_columns` - Admin columns
- `manage_{$taxonomy}_custom_column` - Term columns

### Filters Provided

- `pp_is_client_a_bot` - Override bot detection
- `pp_spinner_url` - Custom spinner image

---

## Strengths

1. **Comprehensive:** Covers most WordPress plugin needs
2. **Stable:** "Rarely needs changes" per developer notes
3. **Reusable:** Used across multiple Power Plugins
4. **MIT Licensed:** Flexible for distribution
5. **Type Hints:** Modern PHP 7.4+ type declarations
6. **Timezone Aware:** Uses `wp_timezone()` for dates

---

## Weaknesses

1. **Monolithic:** 2,336-line file
2. **No Autoloading:** Manual `require_once`
3. **No Tests:** No unit or integration tests
4. **Hard to Version:** No Composer/package manager
5. **Namespace Coupling:** Uses parent plugin namespace
6. **Limited Docs:** No API documentation
7. **Asset Coupling:** Hardcoded paths
8. **Global State:** Multiple global variables

---

## Maintenance Burden

### Adding Features

- Risk of file size growth
- Hard to find code in 2,336 lines
- Merge conflicts in collaborative development

### Fixing Bugs

- Limited test coverage
- Difficult to isolate issues
- Changes affect all plugins using core

### Code Standards

- Fails modern PHP_CodeSniffer rules
- Mixed coding styles
- Inconsistent documentation

---

## Migration Readiness

### Good News

1. **Well-defined classes:** Easy to extract into files
2. **Logical grouping:** Functions naturally cluster
3. **Stable API:** Few breaking changes needed
4. **Type hints:** Already using modern PHP

### Challenges

1. **Namespace migration:** Need to move to `PowerPlugins\Core`
2. **Asset paths:** Need dynamic resolution
3. **Global functions:** Need namespacing or static methods
4. **Backward compatibility:** Existing plugins depend on current API

---

## Recommendations Summary

**Immediate Actions:**

1. Create Composer package structure
2. Split classes into individual files
3. Group utilities into service classes
4. Generate API documentation

**Medium-term:**

1. Add PHPUnit tests
2. Implement PSR-4 autoloading
3. Version lock in Composer
4. Create migration guide

**Long-term:**

1. Deprecate global functions
2. Add dependency injection
3. Create plugin scaffold CLI
4. Publish to Packagist

---

## Next Steps

See **02-composer-migration-strategy.md** for detailed refactoring plan.
