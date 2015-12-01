<?php
/**
 * The application bootstrap file
 *
 * This file is the main entry point for the application
 * All url requests to the application are handled by this file
 *
 * @link:       	  http://pakjiddat.com
 * @since             1.0.0
 * @package           Example
 * 
 * Description:       Example project
 * Version:           1.0.12
 * Author:            Nadir Latif
 * Author URI:        http://pakjiddat.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       example
 */
namespace Framework;

/** The autoload.php file is included */
require("autoload.php");
/** The application context is determined */
$context = (isset($argc))?"command line":"browser";
/** The application parameters */
$parameters = ($context =="command line")?$argv:$_REQUEST;
/** The application request is handled */
$output  = \Framework\Application\WordPress\Application::HandleRequest($context, $parameters, "Example");
/** If the output is not suppressed then the application output is echoed back */
if (!defined("NO_OUTPUT"))echo $output;