<?php
/*
Plugin Name: OSToolbar
Plugin URI: http://www.ostraining.com/ostoolbar/
Description: This plugin shows training videos inside your WordPress admin panel.
Author: OSTraining.com
Version: 3.0.2
Author URI: http://www.ostraining.com
*/
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('ABSPATH') or die();

if (!defined('OSTOOLBAR_VERSION')) {
    define('OSTOOLBAR_VERSION', '3.0.1');
    define('OSTOOLBAR_BASE', __DIR__);
    define('OSTOOLBAR_LIBRARY', OSTOOLBAR_BASE . '/library');
    define('OSTOOLBAR_ASSETS', OSTOOLBAR_BASE . '/assets');
    define('OSTOOLBAR_IMAGES', OSTOOLBAR_ASSETS . '/images');

    require_once OSTOOLBAR_LIBRARY . '/ostoolbar/Loader.php';
    Ostoolbar\Loader::register('Ostoolbar', OSTOOLBAR_LIBRARY . '/ostoolbar');
}

Ostoolbar\Factory::getApplication()->init();
