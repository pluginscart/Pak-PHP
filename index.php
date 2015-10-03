<?php
/**
 * The application bootstrap file
 *
 * This file is the main entry point for the application
 * All url requests to the application are handled by this file
 *
 * @link:             http://dev.pakphp.com
 * @since             1.0.0
 * @package           Framework
 * 
 * Description:       Pak PHP example application  
 * Version:           1.0.0
 * Author:            Nadir Latif
 * Author URI:        http://pakjiddat.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       example-pakphp 
 */
namespace Framework;

require("autoload.php");

$argv       = isset($argv)?$argv:array();
$class_name = isset($_REQUEST['module'])?$_REQUEST['module']:'Example';     
$class_name = Utilities\UtilitiesFramework::Factory("string")->Concatenate('\\',$class_name,'\\',"Configuration");

$configuration=$class_name::GetInstance($argv);
$configuration->InitializeApplication($argv);
$configuration->RunApplication();
