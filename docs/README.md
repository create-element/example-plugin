# Power Plugins Composer Migration - Documentation Index

This directory contains comprehensive analysis and migration planning documentation for transitioning Power Plugins Core library from a monolithic `pp-core.php` file to modern Composer-managed packages.

## Documentation Files

### 01-current-architecture-analysis.md

**Purpose:** Deep dive into the existing codebase  
**Contents:**

- Complete breakdown of pp-core.php (2,336 lines)
- Analysis of all 6 abstract classes (Component, Settings_Core, Post, Post_Controller, Term, Term_Controller, Meta_Box)
- Documentation of 40+ utility functions
- Power Plugins Updater (pwpl) library analysis
- Asset management review
- Code quality issues and technical debt
- Current dependencies and integration points

**Use this document to:**

- Understand what exists today
- Identify refactoring opportunities
- Plan class extraction strategy

---

### 02-composer-migration-strategy.md

**Purpose:** Detailed roadmap for Composer migration  
**Contents:**

- Three migration paths (Big Bang, Gradual, Package-Only)
- **Recommended approach:** Path B - Gradual Extraction
- 7-phase implementation plan with timelines
- Proposed package structure (power-plugins/core, power-plugins/updater)
- PSR-4 autoloading strategy
- Backward compatibility wrapper
- Documentation generation plan (phpDocumentor)
- Testing strategy (PHPUnit)
- Effort estimates (~160 hours / 4 weeks)

**Use this document to:**

- Make decisions about migration approach
- Plan team resources and timeline
- Understand technical implementation details
- Set up private Composer repository

---

### 03-quick-reference.md

**Purpose:** Developer cheat sheet for daily migration work  
**Contents:**

- Quick decision matrix (should I use Composer?)
- Class mapping reference (old → new namespaces)
- Function migration cheatsheet (40+ functions)
- Composer command reference
- Common migration patterns with code examples
- Troubleshooting guide
- Plugin compatibility checklist

**Use this document to:**

- Look up how to migrate specific code
- Find the new name for an old function/class
- Troubleshoot common migration issues
- Copy/paste migration patterns

---

### 04-development-workflow.md

**Purpose:** Practical guide for developing Composer libraries alongside WordPress plugins  
**Contents:**

- Three workflow options explained (Library-First, Plugin-First, Monorepo)
- **Recommended: Library-First with Composer path repositories**
- Step-by-step setup instructions
- Daily development cycle with symlinked libraries
- Complete example: adding a feature from library to plugin
- Publishing and versioning strategies
- CI/CD integration examples

**Use this document to:**

- Set up your local development environment
- Understand how to work with library + plugin simultaneously
- Learn when changes require `composer update` (spoiler: not with symlinks!)
- See practical examples of the full development cycle

---

## Key Recommendations

### ✅ Chosen Migration Path: Big Bang Refactor (Path A)

**Decision Made:** November 25, 2025

**Rationale:**

- Existing plugins being rewritten for WordPress Blocks architecture
- Clean slate approach aligns with complete plugin rewrites
- Modern PHP 8.1+ features from day one
- No legacy compatibility burden
- Perfect timing for comprehensive modernization

**Timeline:**

- **Week 1-2:** Set up Composer packages, modern architecture design
- **Week 2-3:** Build core classes with PSR-12, dependency injection
- **Week 3-4:** Implement utility services and WordPress Blocks helpers
- **Week 4-5:** Asset management, React/Gutenberg integration
- **Week 5-6:** Generate comprehensive API documentation
- **Week 6-8:** Build first new plugin with Blocks as proof-of-concept
- **Month 3+:** Rewrite commercial plugins one-by-one with new blocks

**Total Effort:** ~200 hours for core library (4-6 weeks, then ongoing plugin rewrites)

---

## Next Steps

### Immediate Actions

1. **Review documents** with your development team
2. **Choose package hosting:** Satis (self-hosted), Private Packagist ($50/mo), or GitHub Packages (free)
3. **Create GitHub repositories:**
   - `create-element/pp-core` (MIT license)
   - `power-plugins/pwpl` (Proprietary or MIT)
4. **Set up local dev environment** with Composer
5. **Begin Phase 1:** Foundation setup (composer.json, PSR-4 structure)

### Questions to Answer

1. **Package Hosting:** Which option works best for your infrastructure?
2. **PHP Version:** Target PHP 7.4, 8.0, or 8.1+ minimum?
3. **Open Source:** MIT license for Core package?
4. **Testing Priority:** Unit tests or integration tests first?
5. **Timeline:** Sprint approach (4 weeks) or gradual (8 weeks)?

---

## Benefits of Migration

### Code Quality

- ✅ Pass PHP Code Sniffer checks (PSR-12 compliance)
- ✅ Better IDE support (autocomplete, jump-to-definition)
- ✅ Easier to unit test (isolated classes)
- ✅ Reduced file size (2,336 lines → 20+ focused files)

### Developer Experience

- ✅ `composer require power-plugins/core` - simple installation
- ✅ Version locking prevents breaking changes
- ✅ Generated API documentation
- ✅ Clear upgrade path with semantic versioning

### Maintainability

- ✅ Separate concerns (Settings, Post, Term, UI, Utilities)
- ✅ Easier to find and fix bugs
- ✅ Lower risk when adding features
- ✅ Better code reuse across plugins

---

## File Structure Summary

### Current State

```
example-plugin/
├── pp-core.php           (2,336 lines - monolithic)
├── pwpl/                 (updater library)
│   ├── pwpl.php
│   ├── update-checker.php
│   └── ...
└── pp-assets/            (CSS, JS, images)
```

### Future State (Composer)

```
example-plugin/
├── composer.json         (declares dependencies)
├── vendor/               (Composer packages)
│   └── power-plugins/
│       ├── core/         (Settings, Post, Term, etc.)
│       └── updater/      (update checker)
├── example-plugin.php    (loads vendor/autoload.php)
└── includes/             (plugin-specific code)
```

---

## Package Structure Preview

### power-plugins/core

```
src/
├── Component.php
├── Settings/SettingsCore.php
├── Post/Post.php, PostController.php
├── Term/Term.php, TermController.php
├── MetaBox/MetaBox.php
├── UI/FormHelper.php, AssetManager.php
└── Utilities/DateTimeHelper.php, NetworkHelper.php, etc.
```

### power-plugins/updater

```
src/
├── UpdateChecker.php
├── PluginRegistry.php
├── LicenseManager.php
└── Settings/SettingsPage.php
```

---

## Migration Example

### Before (pp-core.php)

```php
namespace My_Plugin;

require_once 'pp-core.php';

class Settings extends Settings_Core
{
  public function save_settings()
  {
    $this->set_bool('enable', $_POST['enable'] ?? false);
  }
}
```

### After (Composer)

```php
namespace My_Plugin;

require_once __DIR__ . '/vendor/autoload.php';

use PowerPlugins\Core\Settings\SettingsCore;

class Settings extends SettingsCore
{
  public function saveSettings()
  {
    $this->setBool('enable', $_POST['enable'] ?? false);
  }
}
```

### composer.json

```json
{
  "require": {
    "power-plugins/core": "^2.0",
    "power-plugins/updater": "^2.0"
  }
}
```

---

## Security Note

The `.htaccess` file in this directory prevents web access to documentation files:

```apache
Order deny,allow
Deny from all
```

This ensures internal planning documents are not publicly accessible via browser.

---

## Contact & Support

- **Email:** hello@power-plugins.com
- **Website:** https://power-plugins.com
- **Documentation:** (to be published after migration)

---

**Last Updated:** November 25, 2025  
**Created For:** Power Plugins internal development team  
**Status:** Planning phase - ready for implementation

---

## Changelog

- **2025-11-25:** Initial documentation created
  - Current architecture analysis
  - Migration strategy with 3 paths
  - Quick reference guide
  - Implementation phases defined
