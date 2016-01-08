<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. It is the starting point of the plugin. All requests are sent to this file
 *
 * @link:       	  http://pakjiddat.com
 * @since             1.0.0
 * @package           WordPressExample
 *
 * @wordpress-plugin
 * Plugin Name:       WordPressExample
 * Plugin URI:        http://pakjiddat.com
 * Description:       Example plugin that demonstrates how to write a WordPress plugin using the Pak Php framework
 * Version:           1.0.0
 * Author:            Nadir Latif
 * Author URI:        http://pakjiddat.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-example 
*/

namespace Framework;
/** The autoload.php file is included */
require_once("autoload.php");
/** The application context is determined */
$context = (isset($argc))?"command line":"browser";
/** The application parameters */
$parameters = ($context =="command line")?$argv:$_REQUEST;
/** The application request is handled */
$output  = \Framework\Frameworks\WordPress\Application::HandleRequest($context, $parameters, "WordPressExample");
/** If the output is not suppressed then the application output is echoed back */
if (!defined("NO_OUTPUT"))echo $output;
