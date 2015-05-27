<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar;

defined('ABSPATH') or die();

abstract class Request
{
    protected static $hostUrl = 'https://www.ostraining.com/';
    public static    $isTrial = false;

    public static function getHostUrl()
    {
        $trial = static::$isTrial ? '_trial' : '';

        $vars = array(
            'option' => 'com_api',
            'v'      => 'wp' . $trial
        );

        return static::$hostUrl . 'index.php?' . http_build_query($vars);
    }

    public static function makeRequest($data)
    {
        $apikey = get_option('ostoolbar_apikey');

        $staticData = array(
            'format' => 'json',
            'key'    => $apikey
        );

        if (!isset($data['app'])) {
            $data['app'] = 'tutorials';
        }
        $data = array_merge($data, $staticData);

        $response = Rest\Request::send(
            static::getHostUrl(),
            $data,
            'POST',
            array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            )
        );

        if ($body = $response->getBody()) {
            $response->setBody(json_decode($body));
        }

        if ($response->hasError()) {
            $body = $response->getBody();
            if (isset($body->code)) {
                $response->setErrorCode($body->code);
            }
            if (isset($body->message)) {
                $response->setErrorMsg($body->message);
            }
        }

        return $response;
    }

    public static function filter($text)
    {
        $split  = explode('index.php', static::getHostUrl());
        $ostUrl = $split[0];

        $text = preg_replace('#(href|src)="([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#', '$1="' . $ostUrl . '$2$3', $text);

        return $text;
    }
}
