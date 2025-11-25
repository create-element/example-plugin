# Example Plugin - Coding Standards Guide

**Last Updated:** 25 November 2025  
**PHP Version:** 8.1+  
**Standards:** PSR-12

---

## Overview

This plugin follows modern PHP coding standards to ensure maintainability, readability, and consistency across the codebase.

## Standards We Follow

### 1. PSR-12: Extended Coding Style

We strictly follow [PSR-12](https://www.php-fig.org/psr/psr-12/) which extends PSR-1 and PSR-2.

**Key Points:**

- 4 spaces for indentation (no tabs)
- Opening braces for classes and methods on the next line
- Control structure braces on the same line
- One statement per line
- Line length soft limit: 120 characters
- Strict types declaration when beneficial
- Namespace and use statements properly organized

### 2. PSR-4: Autoloading

We follow [PSR-4](https://www.php-fig.org/psr/psr-4/) for autoloading:

**Namespace Structure:**

```
ExamplePlugin\           → includes/
ExamplePlugin\Post\      → includes/Post/
ExamplePlugin\Term\      → includes/Term/
ExamplePlugin\MetaBox\   → includes/MetaBox/
```

**File Naming:**

- Classes: `class-plugin.php` (lowercase with hyphens)
- Interfaces: `interface-cacheable.php`
- Traits: `trait-singleton.php`
- Class names match filename: `class-event.php` → `class Event`

### 3. Type Declarations

**Use strict typing where possible:**

```php
declare(strict_types=1);

public function get_capacity(): int {
    return $this->capacity;
}

public function set_location( string $location ): void {
    $this->location = $location;
}
```

**Type Hints:**

- Always use parameter type hints
- Always use return type hints (including `void`)
- Use nullable types when appropriate: `?string`, `?int`
- Use union types for PHP 8+: `string|int`

### 4. Documentation

**PHPDoc Blocks:**

```php
/**
 * Short description (one line)
 *
 * Longer description if needed. Can span multiple lines
 * and provide more detail about the functionality.
 *
 * @since 1.0.0
 * @param string $name The event name.
 * @param int    $capacity Maximum capacity.
 * @return Event The event object.
 * @throws \InvalidArgumentException If capacity is negative.
 */
public function create_event( string $name, int $capacity ): Event {
    // Implementation
}
```

**File Headers:**

```php
<?php
/**
 * Event Post Type
 *
 * @package ExamplePlugin\Post
 * @since 1.0.0
 */

namespace ExamplePlugin\Post;

use PowerPlugins\Core\Post\Post;
```

### 5. Naming Conventions

**Classes:**

- PascalCase: `EventController`, `EventDetailsMetaBox`
- Descriptive names that indicate purpose

**Methods:**

- snake_case: `get_event_date()`, `save_settings()`
- Follows WordPress convention for compatibility

**Variables:**

- snake_case: `$event_date`, `$max_capacity`

**Constants:**

- UPPER_SNAKE_CASE: `PP_EXP_VERSION`, `MAX_EVENTS_PER_PAGE`
- Defined with `define()` or `const`

**Hooks:**

- Prefix with plugin name: `example_plugin_before_save`
- Use snake_case: `example_plugin_event_created`

### 6. Code Organization

**Class Structure Order:**

1. Constants
2. Static properties
3. Instance properties (private → protected → public)
4. Constructor
5. Static methods
6. Public methods
7. Protected methods
8. Private methods

**Property Visibility:**

- Default to `private` unless there's a reason for `protected`
- Avoid `public` properties - use getters/setters
- Use readonly properties in PHP 8.1+

---

## Tools & Automation

### PHP_CodeSniffer (PHPCS)

**Configuration:** `phpcs.xml`

**Check code:**

```bash
composer phpcs
```

**Auto-fix violations:**

```bash
composer phpcbf
```

**Check specific file:**

```bash
vendor/bin/phpcs includes/class-plugin.php
```

### PHPStan

**Configuration:** `phpstan.neon`

**Run static analysis:**

```bash
composer phpstan
```

**Analysis Level:** 5 (0-9, higher = stricter)

**Ignored Patterns:**

- WordPress functions (they're not in our codebase)
- Legacy class references during migration

### Running All Checks

```bash
# Run all quality checks
composer phpcs && composer phpstan
```

---

## Code Review Checklist

Before committing code, ensure:

- [ ] All PHPCS violations fixed (`composer phpcs`)
- [ ] PHPStan passes with no errors (`composer phpstan`)
- [ ] All methods have type hints and return types
- [ ] All public methods have PHPDoc blocks
- [ ] File header with @package and @since tags
- [ ] No `var_dump()` or `print_r()` in code
- [ ] Error logging uses `error_log()` only in development mode
- [ ] Security: All output escaped (`esc_html`, `esc_attr`, `esc_url`)
- [ ] Security: All input validated and sanitized
- [ ] Database queries use wpdb prepared statements
- [ ] Nonce verification for form submissions

---

## WordPress-Specific Standards

### Security

**Escape output:**

```php
echo esc_html($event->get_title());
echo '<a href="' . esc_url($url) . '">' . esc_html($text) . '</a>';
```

**Validate and sanitize input:**

```php
$capacity = absint($_POST['capacity']);
$location = sanitize_text_field($_POST['location']);
$date = sanitize_text_field($_POST['event_date']);
```

**Verify nonces:**

```php
if (!wp_verify_nonce($_POST['_wpnonce'], 'save_event')) {
  wp_die('Invalid nonce');
}
```

### Database Queries

**Use wpdb prepared statements:**

```php
global $wpdb;
$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}events WHERE capacity > %d", $min_capacity));
```

### Hooks and Filters

**Naming:**

```php
// Actions
do_action('example_plugin_event_created', $event);
do_action('example_plugin_before_save_event', $event_id, $data);

// Filters
$capacity = apply_filters('example_plugin_max_capacity', 100, $event_id);
$format = apply_filters('example_plugin_date_format', 'Y-m-d');
```

---

## Migration Notes

### Legacy Code

Files being migrated from old standards:

- `includes/class-admin-hooks.php` - Uses old naming
- `includes/class-public-hooks.php` - Uses old naming
- `includes/class-settings.php` - Uses old naming

**Current Exclusions in phpstan.neon:**

```yaml
excludePaths:
  - includes/class-admin-hooks.php
  - includes/class-public-hooks.php
  - includes/class-settings.php
```

These will be refactored to follow new standards in Milestone 4.

---

## Resources

- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [PSR-4: Autoloading Standard](https://www.php-fig.org/psr/psr-4/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- [PHP_CodeSniffer Documentation](https://github.com/squizlabs/PHP_CodeSniffer/wiki)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
