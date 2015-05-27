<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar;

defined('ABSPATH') or die();

class Model
{
    protected $state  = array();
    protected $errors = array();

    public function setState($key, $value)
    {
        $this->state[$key] = $value;

        return true;
    }

    public function getState($key)
    {
        if (isset($this->state[$key])) {
            return $this->state[$key];
        } else {
            return false;
        }
    }

    public function setError($msg)
    {
        $this->errors[] = $msg;
    }

    public function getError($all = false)
    {
        if (empty($this->errors)) {
            return false;
        }

        if ($all) {
            return $this->errors;
        }

        $last = count($this->errors) - 1;

        return $this->errors[$last];
    }
}
