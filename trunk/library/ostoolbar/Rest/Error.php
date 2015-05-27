<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar\Rest;

defined('ABSPATH') or die();

/**
 * Class Error
 *
 * Holds CURL error information
 *
 * @package Ostoolbar\Rest
 */
class Error
{
    public $code    = null;
    public $message = null;
}
