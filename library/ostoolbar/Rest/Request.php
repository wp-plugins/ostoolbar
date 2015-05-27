<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar\Rest;

defined('ABSPATH') or die();

class Request
{
    /**
     * @param string       $url
     * @param string|array $data
     * @param string       $method
     * @param array        $curlOptions
     *
     * @return Response
     */
    public static function send($url, $data = null, $method = 'GET', $curlOptions = array())
    {
        $data   = static::prepareData($data);
        $handle = curl_init();

        static::setCurlOption($handle, CURLOPT_RETURNTRANSFER, true, $curlOptions);

        switch ($method) {
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                }
                break;

            case 'GET':
                $divider = strpos($url, '?') === false ? '?' : '&';
                $url .= $divider . $data;
                break;
        }
        curl_setopt($handle, CURLOPT_URL, $url);

        if (!empty($curlOptions)) {
            foreach ($curlOptions as $option => $value) {
                curl_setopt($handle, $option, $value);
            }
        }

        $receiveHeaders = isset($curlOptions[CURLOPT_HEADER]);
        $response       = new Response($handle, $receiveHeaders);

        return $response;
    }

    /**
     * @param resource $handle
     * @param int      $name
     * @param mixed    $value
     * @param array    $curlOptions
     *
     * @return bool
     */
    protected static function setCurlOption($handle, $name, $value, $curlOptions = array())
    {
        $options = array_keys($curlOptions);
        if (in_array($name, $options)) {
            return false;
        } else {
            curl_setopt($handle, $name, $value);

            return true;
        }
    }

    /**
     * @param string|array $data
     *
     * @return string
     */
    protected static function prepareData($data)
    {
        if (is_array($data)) {
            $data = http_build_query($data);
        }

        return $data;
    }
}
