<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar;

defined('ABSPATH') or die();

/**
 * Class Factory
 *
 * @package Ostoolbar
 */
abstract class Factory
{
    protected static $instances = array();

    /**
     * Main application object
     *
     * @return Application
     */
    public static function getApplication()
    {
        return static::getInstance('\Ostoolbar\Application');
    }

    /**
     * @return Configuration
     */
    public static function getConfiguration()
    {
        return static::getInstance('\Ostoolbar\Configuration');
    }

    /**
     * @return Controller
     */
    public static function getController()
    {
        return static::getInstance('\Ostoolbar\Controller');
    }

    /**
     * @return Sanitize
     */
    public static function getSanitize()
    {
        return static::getInstance('\Ostoolbar\Sanitize');
    }

    /**
     * @param $name
     *
     * @return Model
     */
    public static function getModel($name)
    {
        return static::getInstance('\Ostoolbar\Model\\' . $name);
    }

    /**
     * @return Cache\StorageFile
     */
    public static function getCacheStorage()
    {
        return static::getInstance('\Ostoolbar\Cache\StorageFile');
    }

    /**
     * Generic factory method
     *
     * @param $class_name
     *
     * @return mixed
     */
    protected static function getInstance($class_name)
    {
        $key = md5($class_name);
        if (!isset(static::$instances[$key])) {
            static::$instances[$key] = new $class_name();
        }

        return static::$instances[$key];
    }

}
