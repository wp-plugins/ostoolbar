<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar;

defined('ABSPATH') or die();

class Loader
{
    /**
     * Associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected static $prefixes = array();

    /**
     * @var loader
     */
    protected static $instance = null;

    protected static $wordPresPrefixes = array();

    protected static function registerLoader($method)
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        spl_autoload_register(array(static::$instance, $method));
    }

    /**
     * Register a psr4 namespace
     *
     * @param string $prefix   The namespace prefix.
     * @param string $path     A base directory for class files in the
     *                         namespace.
     * @param bool   $prepend  If true, prepend the base directory to the stack
     *                         instead of appending it; this causes it to be searched first rather
     *                         than last.
     *
     * @return void
     */
    public static function register($prefix, $path, $prepend = false)
    {
        if (count(static::$prefixes) == 0) {
            // Register function on first call
            static::registerLoader('loadClass');
        }

        // normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        // normalize the base directory with a trailing separator
        $path = rtrim($path, '\\/') . '/';

        // initialise the namespace prefix array
        if (empty(static::$prefixes[$prefix])) {
            static::$prefixes[$prefix] = array();
        }

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift(static::$prefixes[$prefix], $path);
        } else {
            array_push(static::$prefixes[$prefix], $path);
        }
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     *
     * @return bool|string The mapped file name on success, or boolean false on failure.
     */
    protected function loadClass($class)
    {
        $prefixes   = explode('\\', $class);
        $class_name = '';
        while ($prefixes) {
            $class_name = array_pop($prefixes) . $class_name;
            $prefix     = join('\\', $prefixes) . '\\';

            if ($file_path = $this->loadMappedFile($prefix, $class_name)) {
                return $file_path;
            }
            $class_name = '\\' . $class_name;
        }

        // never found a mapped file
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and class.
     *
     * @param string $prefix     The namespace prefix.
     * @param string $class_name The relative class name.
     *
     * @return bool|string false if no mapped file can be loaded | path that was loaded
     */
    protected function loadMappedFile($prefix, $class_name)
    {
        // are there any base directories for this namespace prefix?
        if (isset(static::$prefixes[$prefix]) === false) {
            return false;
        }

        // look through base directories for this namespace prefix
        foreach (static::$prefixes[$prefix] as $path) {
            $path = $path . str_replace('\\', '/', $class_name) . '.php';

            if ($this->requireFile($path)) {
                return $path;
            }
        }

        // never found it
        return false;
    }

    /**
     * Register a folder for WordPress coding conventions
     *
     * @param string  $prefix
     * @param string  $path
     * @param boolean $prepend
     */
    public static function registerWordPress($prefix, $path, $prepend = false)
    {
        if (count(static::$wordPresPrefixes) == 0) {
            // Register function on first call
            static::registerLoader('loadWordPressClass');
        }

        // initialise the prefix array
        if (empty(static::$wordPresPrefixes[$prefix])) {
            static::$wordPresPrefixes[$prefix] = array();
        }

        // retain the base directory for the prefix
        if ($prepend) {
            array_unshift(static::$wordPresPrefixes[$prefix], $path);
        } else {
            array_push(static::$wordPresPrefixes[$prefix], $path);
        }
    }

    /**
     * Handles autoloading of WordPress classes
     *
     * @param string $class
     *
     * @return bool|string
     */
    protected function loadWordPressClass($class)
    {
        $parts = explode('_', $class);
        if (count($parts) > 0 && array_key_exists($parts[0], static::$wordPresPrefixes)) {
            $prefix    = array_shift($parts);
            $file_name = 'class-' . join('-', $parts);
            $path      = static::$wordPresPrefixes[$prefix] . '/' . $file_name . '.php';
            if ($this->requireFile($path)) {
                return $path;
            }
        }

        return false;
    }

    /**
     * Require the selected file path
     *
     * @param string $path
     *
     * @return bool
     */
    protected function requireFile($path)
    {
        $success = is_file($path);
        if ($success) {
            require_once $path;
        }

        return $success;
    }
}
