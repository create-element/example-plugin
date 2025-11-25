#!/usr/bin/env php
<?php
/**
 * Test script to verify Power Plugins Core classes can be autoloaded
 *
 * Run from plugin directory: php test-autoload.php
 */

// Load Composer autoloader
$autoload_path = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoload_path)) {
    echo "❌ ERROR: Composer autoloader not found at: $autoload_path\n";
    echo "   Run 'composer install' first.\n";
    exit(1);
}

require $autoload_path;

echo "Testing Power Plugins Core class autoloading...\n\n";

$classes_to_test = [
    'PowerPlugins\Core\Component' => 'Component (Base)',
    'PowerPlugins\Core\Settings\SettingsCore' => 'Settings Core',
    'PowerPlugins\Core\Post\Post' => 'Post',
    'PowerPlugins\Core\Post\PostController' => 'Post Controller',
    'PowerPlugins\Core\Term\Term' => 'Term',
    'PowerPlugins\Core\Term\TermController' => 'Term Controller',
    'PowerPlugins\Core\MetaBox\MetaBox' => 'Meta Box'
];

$all_passed = true;

foreach ($classes_to_test as $class_name => $description) {
    if (class_exists($class_name)) {
        echo "✅ $description ($class_name)\n";
    } else {
        echo "❌ $description ($class_name) - NOT FOUND\n";
        $all_passed = false;
    }
}

echo "\n";

if ($all_passed) {
    echo "✅ All classes loaded successfully!\n";
    echo "\nNamespace structure verified:\n";
    echo "  PowerPlugins\\Core\\Component\n";
    echo "  PowerPlugins\\Core\\Settings\\SettingsCore\n";
    echo "  PowerPlugins\\Core\\Post\\{Post, PostController}\n";
    echo "  PowerPlugins\\Core\\Term\\{Term, TermController}\n";
    echo "  PowerPlugins\\Core\\MetaBox\\MetaBox\n";
    exit(0);
} else {
    echo "❌ Some classes failed to load.\n";
    exit(1);
}

