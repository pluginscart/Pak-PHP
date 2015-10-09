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
 * Version:           1.0.0
 * Author:            Nadir Latif
 * Author URI:        http://pakjiddat.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       example
 */
namespace Framework;
/** The autoload.php file is included */
require("autoload.php");

/** The command line arguments */
$argv                = isset($argv)?$argv:array();
/** The current module name is determined */
$class_name          = isset($_REQUEST['module'])?$_REQUEST['module']:'Example';
$class_name          = Utilities\UtilitiesFramework::Factory("string")->Concatenate('\\',$class_name,'\\',"Configuration");
/** An instance of the required module is created */
$configuration       = $class_name::GetInstance($argv);
/** The application is initialized */
$configuration->InitializeApplication($argv);
/** The application is run */
$configuration->RunApplication();