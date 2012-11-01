<?php
/**
 * @package OSToolbar
 * @version 2.3
 */
/*
Plugin Name: OSToolbar
Plugin URI: http://www.ostraining.com/joomla-ostoolbar/
Description: This plugin shows training videos inside your WordPress admin panel.
Author: OSTraining.com
Version: 2.2
Author URI: http://www.ostraining.com
*/


require_once(dirname(__FILE__).'/libraries/factory.php');
require_once(dirname(__FILE__).'/libraries/configuration.php');
require_once(dirname(__FILE__).'/libraries/application.php');
require_once(dirname(__FILE__).'/libraries/model.php');
require_once(dirname(__FILE__).'/libraries/cache.php');
require_once(dirname(__FILE__).'/libraries/request.php');
require_once(dirname(__FILE__).'/libraries/rest.php');
require_once(dirname(__FILE__).'/models/tutorial.php');
require_once(dirname(__FILE__).'/models/tutorials.php');
require_once(dirname(__FILE__).'/models/help.php');
require_once(dirname(__FILE__).'/models/helppage.php');
require_once(dirname(__FILE__).'/controller.php');


$app = OST_Factory::getInstance('OST_Application');
$app->init();