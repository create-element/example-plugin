=== Example Plugin ===
Contributors: headwalluk
Donate link: https://power-plugins.com/
Tags: development, boilerplate, composer, psr-4
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.6
License: GPL v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Modern WordPress plugin boilerplate demonstrating best practices with Composer, PSR-4 autoloading, and the Power Plugins Core library.

== Description ==

Example Plugin is a development boilerplate that showcases modern WordPress plugin development practices:

* **Composer Package Management** - Dependencies managed via Composer with PSR-4 autoloading
* **Modern PHP Standards** - PHP 8.1+ with strict types, namespaces, and type declarations
* **Power Plugins Core Library** - Built on the Power Plugins Core component architecture
* **WordPress Coding Standards** - PHPCS configured for WordPress-Core standards
* **Static Analysis** - PHPStan integration for code quality
* **Component Architecture** - Separated admin, public, and settings components
* **Well Documented** - Comprehensive PHPDoc comments and development documentation

This plugin serves as a reference implementation and starting point for developing premium WordPress plugins using modern development practices.

== Developer Features ==

* PSR-4 autoloading via Composer
* Namespaced classes (ExamplePlugin\)
* Component-based architecture
* Settings API integration
* Hook management system
* Development workflow documentation
* Code quality tools (PHPCS, PHPStan)
* Local development with symlinked libraries

== Installation ==

= For Development =

1. Clone the repository to your WordPress plugins directory
2. Install dependencies: `composer install`
3. For local development with pp-core library:
   - Clone pp-core to `/path/to/wp-content/libraries/pp-core`
   - Clone pp-updater to `/path/to/wp-content/libraries/pp-updater`
   - Run `composer install` (will symlink local libraries)
4. Activate the plugin through the WordPress admin

See `docs/04-development-workflow.md` for complete setup instructions.

= For Production Use =

This is a development boilerplate and not intended for production deployment. Use it as a starting point for your own plugin development.

== Frequently Asked Questions ==

= What is this plugin for? =

This plugin is a reference implementation and boilerplate for developing modern WordPress plugins. It demonstrates best practices for plugin architecture, code organization, and development workflow.

= Can I use this in production? =

This is a development example. You should use it as a starting point and customize it for your specific needs before deploying to production.

= What are the requirements? =

* WordPress 6.0 or higher
* PHP 8.1 or higher
* Composer (for dependency management)
* Power Plugins Core library
* Power Plugins Updater library

== Changelog ==

= 1.0.6 =
*Released 25th November 2024*

* Complete rewrite as modern WordPress plugin boilerplate
* Migrated to Composer for dependency management
* Implemented PSR-4 autoloading with namespaces
* Integrated Power Plugins Core library
* Added WordPress Coding Standards (PHPCS)
* Added static analysis (PHPStan)
* Component-based architecture (Plugin, Settings, AdminHooks, PublicHooks)
* Comprehensive documentation and development guides
* GitHub repository established

= 1.0.0 =
*Initial release*

* Basic plugin structure
* Legacy implementation
