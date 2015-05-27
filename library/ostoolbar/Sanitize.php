<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar;

defined('ABSPATH') or die();

class Sanitize
{
    /**
     * @param string   $key
     * @param string   $hash
     * @param callable $function
     *
     * @return mixed|null
     */
    public function get($key, $function = null, $hash = null)
    {
        if ($hash === null) {
            $hash = $_GET;
        }

        $value = null;
        if (is_array($hash) && isset($hash[$key])) {
            $value = $hash[$key];

            if (is_string($function) && function_exists($function)) {
                $value = $function($value);

            } elseif (is_callable($function)) {
                $value = call_user_func($function, $value);
            }
        }

        return $value;
    }

    public function getEmail($key, $hash = null)
    {
        return $this->get($key, 'sanitize_email', $hash);
    }

    public function getFileName($key, $hash = null)
    {
        return $this->get($key, 'sanitize_file_name', $hash);
    }

    public function getHtmlClass($key, $hash = null)
    {
        return $this->get($key, 'sanitize_html_class', $hash);
    }

    public function getInt($key, $hash = null)
    {
        return $this->get($key, 'intval', $hash);
    }

    public function getKey($key, $hash = null)
    {
        return $this->get($key, 'sanitize_key', $hash);
    }

    public function getMeta($key, $hash = null)
    {
        return $this->get($key, 'sanitize_meta', $hash);
    }

    public function getMimeType($key, $hash = null)
    {
        return $this->get($key, 'sanitize_mime_type', $hash);
    }

    public function getOption($key, $hash = null)
    {
        return $this->get($key, 'sanitize_option', $hash);
    }

    public function getSqlOrderby($key, $hash = null)
    {
        return $this->get($key, 'sanitize_sql_orderby', $hash);
    }

    public function getTextField($key, $hash = null)
    {
        return $this->get($key, 'sanitize_text_field', $hash);
    }

    public function getTitle($key, $hash = null)
    {
        return $this->get($key, 'sanitize_title', $hash);
    }

    public function getTitleForQuery($key, $hash = null)
    {
        return $this->get($key, 'sanitize_title_for_query', $hash);
    }

    public function getTitleWithDashes($key, $hash = null)
    {
        return $this->get($key, 'sanitize_title_with_dashes', $hash);
    }

    public function getUser($key, $hash = null)
    {
        return $this->get($key, 'sanitize_user', $hash);
    }
}
