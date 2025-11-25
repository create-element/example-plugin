# Example Plugin - Project Tracker

**Goal:** Build a working example plugin to validate and test the Power Plugins Core library and modern development workflow.

**Strategy:** Implement a simple events calendar plugin using a custom Event post type (representing calendar events like conferences, meetups, webinars, etc.), demonstrating all core library features through real-world usage.

**What is "Event"?** 
Event refers to calendar events (e.g., "WordPress Meetup", "Annual Conference", "Product Launch") - NOT system events or cron jobs. This example showcases how to build a small events & calendar plugin using the Power Plugins Core library.

**Why Events Calendar?**
- Demonstrates custom post types with meaningful meta fields (date, location, capacity)
- Shows meta box implementation for event details
- Requires settings (date formats, display options)
- Provides frontend display challenges (date formatting, virtual badges)
- Real-world use case that developers can understand and extend
- Tests Post/PostController caching with date-based queries

---

## Milestone 1: Core Plugin Infrastructure ✅

**Goal:** Set up plugin structure and Composer integration

- [x] Configure Composer with path repositories to pp-core and pp-updater
- [x] Create symlinks to local library development
- [x] Set up PSR-4 autoloading for `ExamplePlugin\` namespace
- [x] Update main plugin file to use Composer autoloader
- [x] Create test script to verify class autoloading
- [x] Verify symlink-based Library-First workflow

---

## Milestone 2: Main Plugin Class ✅

**Goal:** Create `ExamplePlugin\Plugin` class that initializes the plugin

**Status:** COMPLETE - 25 November 2024

### Tasks

- [x] Create `includes/class-plugin.php` with `ExamplePlugin\Plugin` class
- [x] Extend `PowerPlugins\Core\Component` base class
- [x] Initialize plugin in main plugin file (`example-plugin.php`)
- [x] Add plugin activation hook
- [x] Add plugin deactivation hook
- [x] Set up admin and public hooks initialization
- [x] Test plugin activation/deactivation in WordPress admin
- [x] Create `ExamplePlugin\Settings` class extending `SettingsCore`
- [x] Create `ExamplePlugin\AdminHooks` class
- [x] Create `ExamplePlugin\PublicHooks` class
- [x] Add comprehensive PHPDoc comments to all classes
- [x] Configure WordPress Coding Standards (PHPCS)
- [x] Configure PHPStan static analysis
- [x] Set up Composer classmap autoloading
- [x] Update plugin header with all recommended fields
- [x] Add GPL v3 license.txt file
- [x] Create comprehensive readme.txt
- [x] Set up GitHub repository
- [x] Bump version to 1.900.0
- [x] Bump PHP requirement to 8.2

### Validation

- ✅ Plugin activates without errors
- ✅ Plugin name and version passed to Component
- ✅ WordPress Plugins page shows plugin correctly
- ✅ All classes properly documented
- ✅ Code passes WordPress coding standards
- ✅ PHPStan analysis passes
- ✅ Version control established

---

## Milestone 3: Custom Post Type - Event 🎯 CURRENT

**Goal:** Create Event custom post type (calendar events) using Power Plugins Core classes

**Next Session:** Start with Event Post Object implementation

**Event Details:**
- Post type slug: `pp_event`
- Represents calendar events (conferences, meetups, webinars, etc.)
- Meta fields: date, location, capacity, virtual status
- Will demonstrate: custom admin columns, meta boxes, date handling, settings integration

### 3.1: Event Post Object

- [ ] Create `includes/Post/class-event.php`
- [ ] Extend `PowerPlugins\Core\Post\Post`
- [ ] Add custom meta field: `event_date` (DateTime - when the event occurs)
- [ ] Add custom meta field: `event_location` (string - physical address or "Online")
- [ ] Add custom meta field: `event_capacity` (int - maximum attendees)
- [ ] Add custom meta field: `event_is_virtual` (bool - online vs in-person)
- [ ] Implement getter/setter methods using parent class helpers
- [ ] Test meta field storage and retrieval

### 3.2: Event Post Controller

- [ ] Create `includes/Post/class-event-controller.php`
- [ ] Extend `PowerPlugins\Core\Post\PostController`
- [ ] Implement `create_post_object()` to return Event instance
- [ ] Register `pp_event` post type in WordPress
- [ ] Set up post type labels and capabilities
- [ ] Add featured image support
- [ ] Implement `manage_posts_columns()` to customize admin columns
- [ ] Implement `manage_posts_custom_column()` to display:
  - Event date
  - Location
  - Capacity
  - Virtual badge
- [ ] Test post object caching functionality
- [ ] Test creating/editing events in WordPress admin

### 3.3: Event Meta Box

- [ ] Create `includes/MetaBox/class-event-details-meta-box.php`
- [ ] Extend `PowerPlugins\Core\MetaBox\MetaBox`
- [ ] Register meta box with WordPress
- [ ] Render form fields:
  - Date picker for event_date
  - Text input for event_location
  - Number input for event_capacity
  - Checkbox for event_is_virtual
- [ ] Implement save logic using Event object methods
- [ ] Add nonce verification (use parent class helpers)
- [ ] Test meta box saves correctly

### Validation

- Can create/edit/delete events through WordPress admin
- Meta fields save and display correctly
- Admin columns show custom data
- Post object caching reduces database queries
- Meta box nonce verification works

---

## Milestone 4: Settings Page

**Goal:** Create plugin settings page using SettingsCore

### 4.1: Settings Class

- [ ] Create `includes/class-settings.php`
- [ ] Extend `PowerPlugins\Core\Settings\SettingsCore`
- [ ] Implement `save_settings()` method
- [ ] Add option: `example_plugin_default_capacity` (int, default: 50)
- [ ] Add option: `example_plugin_date_format` (string, default: 'Y-m-d')
- [ ] Add option: `example_plugin_show_virtual_badge` (bool, default: true)
- [ ] Add option: `example_plugin_archive_old_events` (bool, default: false)
- [ ] Use parent class getters/setters (get_int, get_string, get_bool)

### 4.2: Settings Page UI

- [ ] Register settings page under Settings menu
- [ ] Use `render_page_title()`, `open_wrap()`, `open_form()` helpers
- [ ] Render form fields for each setting
- [ ] Add submit button
- [ ] Use `close_form()`, `close_wrap()` helpers
- [ ] Hook `maybe_save_settings()` on admin_init
- [ ] Add admin notice on successful save
- [ ] Test settings save and persist correctly

### Validation

- Settings page appears in WordPress admin
- All settings save correctly
- Settings values are retrieved and used throughout plugin
- Nonce verification prevents CSRF

---

## Milestone 5: Frontend Display

**Goal:** Display calendar events on the frontend

### Tasks

- [ ] Create single event template override or use `the_content` filter
- [ ] Display event meta fields (date, location, capacity, virtual status)
- [ ] Use settings to format date display (e.g., "Friday, December 15, 2025")
- [ ] Show/hide virtual badge based on settings (e.g., "🌐 Virtual Event")
- [ ] Create archive template for events listing (calendar view or list)
- [ ] Add basic CSS for event cards/display
- [ ] Enqueue CSS on frontend only
- [ ] Consider upcoming vs past event styling

### Validation

- Individual event pages display correctly
- Event archive page works
- Settings affect frontend display
- CSS loads only on event pages

---

## Milestone 6: Power Plugins Core Library Improvements

**Goal:** Identify and fix issues discovered during example plugin development

### 6.1: API Refinements

- [ ] Document any missing helper methods needed
- [ ] Add missing WordPress function references (if any)
- [ ] Improve PHPDoc comments based on actual usage
- [ ] Add type hints where missing
- [ ] Fix any bugs discovered during development

### 6.2: Utility Functions Migration

- [ ] Extract UI helper functions from pp-core.php (lines 85-1263)
- [ ] Create `src/UI/` directory in pp-core
- [ ] Migrate functions to static methods or helper classes
- [ ] Update SettingsCore to use new UI helpers
- [ ] Test compatibility with existing code

### 6.3: Constants and Configuration

- [ ] Review constants defined in pp-core.php (lines 50-80)
- [ ] Determine which should be configurable vs hardcoded
- [ ] Create configuration class if needed
- [ ] Document configuration options

---

## Milestone 7: Testing and Quality

**Goal:** Ensure code quality and test coverage

### 7.1: Unit Tests - Core Library

- [ ] Set up PHPUnit in pp-core library
- [ ] Write tests for Component class
- [ ] Write tests for SettingsCore (option get/set methods)
- [ ] Write tests for Post class (meta field methods)
- [ ] Write tests for PostController (caching, CRUD)
- [ ] Write tests for Term class
- [ ] Write tests for TermController
- [ ] Write tests for MetaBox class
- [ ] Run tests: `cd libraries/pp-core && composer test`

### 7.2: Integration Tests - Example Plugin

- [ ] Set up WordPress test environment
- [ ] Write integration test: Create event with meta fields
- [ ] Write integration test: Event controller columns
- [ ] Write integration test: Settings save/retrieve
- [ ] Write integration test: Meta box save
- [ ] Run WordPress integration tests

### 7.3: Code Quality

- [ ] Run PHPCS on pp-core: `composer phpcs`
- [ ] Fix PSR-12 violations
- [ ] Run PHPStan on pp-core: `composer phpstan`
- [ ] Fix static analysis issues (level 5)
- [ ] Run PHPCS on example-plugin
- [ ] Fix example plugin code style issues

---

## Milestone 8: Documentation

**Goal:** Document the library and example plugin

### 8.1: API Documentation

- [ ] Generate phpDocumentor docs: `cd libraries/pp-core && composer docs`
- [ ] Review generated API documentation
- [ ] Add missing @param and @return tags
- [ ] Add usage examples in docblocks
- [ ] Commit docs to repository

### 8.2: Developer Guides

- [ ] Write guide: "Creating a Custom Post Type with Power Plugins Core"
- [ ] Write guide: "Building a Settings Page"
- [ ] Write guide: "Working with Meta Boxes"
- [ ] Write guide: "Library-First Development Workflow"
- [ ] Add code examples from example plugin

### 8.3: Example Plugin Documentation

- [ ] Add README.md to example-plugin explaining its purpose
- [ ] Document the Event post type structure
- [ ] Document available settings
- [ ] Add inline code comments

---

## Milestone 9: Power Plugins Updater (Future)

**Goal:** Migrate PWPL updater to modern structure

_Deferred until Core library is stable_

### Tasks

- [ ] Extract updater classes from pwpl/ directory
- [ ] Create PowerPlugins\Updater namespace
- [ ] Implement license key validation
- [ ] Implement remote update checking
- [ ] Test with example plugin
- [ ] Document updater integration

---

## Milestone 10: First Release Preparation

**Goal:** Prepare for v2.0.0-alpha.1 release

### Tasks

- [ ] Review all code for breaking changes
- [ ] Update CHANGELOG.md for both libraries
- [ ] Bump version to 2.0.0-alpha.1
- [ ] Create git tags
- [ ] Push to GitHub
- [ ] Create GitHub releases with notes
- [ ] Test Composer installation from GitHub

---

## Current Status

**Active Milestone:** Milestone 2 (Main Plugin Class)

**Completed:**

- ✅ Milestone 1: Core plugin infrastructure with Composer and symlinks
- ✅ Extracted 7 core classes from pp-core.php to pp-core library
- ✅ Verified autoloading with test script
- ✅ Created CHANGELOGs and READMEs for both libraries

**Next Actions:**

1. Create `ExamplePlugin\Plugin` class
2. Initialize plugin structure
3. Test activation/deactivation

**Blockers:** None

---

## Notes

- **Development Philosophy:** Build real features to validate the library API
- **Testing Strategy:** Use example plugin as integration test suite
- **Documentation:** Write guides based on actual implementation experience
- **Versioning:** Stay in 1.900.x series until API stabilizes, then move to 2.0.0-alpha/beta/rc
