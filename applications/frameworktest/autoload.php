<?php

namespace ApplicationFramework;
error_reporting(E_ALL);
       ini_set('display_errors', true);
            ini_set('display_startup_errors', true);   
/**
 * Function used to auto load the framework classes
 * 
 * It imlements PSR-0 and PSR-4 autoloading standards
 * The required class name should be prefixed with a namespace
 * This lowercaser of the namespace should match the folder name of the class 
 * 
 * @since 1.0.0
 * @param string $class_name name of the class that needs to be included
 */
function autoload_framework_classes($class_name)
{    
    /** If the required class is in the global namespace then no need to autoload the class **/
    if (strpos($class_name, "\\") === false)
        return false;
    /** The class name is split into namespace and short class name **/
    list($namespace, $class_name) = explode("\\", $class_name);
    /** The namespace is converted to lower case **/
    $namespace_folder        = strtolower($namespace);
	/** .php is added to class name **/
	$class_name=$class_name.".php";
    /** The applications folder name **/
    $application_folder_path = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "applications";
    /** The templates folder path **/
    $templates_folder_path   = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $namespace_folder . DIRECTORY_SEPARATOR . "includes";
    /** The path to the frameworks libraries folder **/
    $frameworks_folder_path  = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR ."includes" . DIRECTORY_SEPARATOR . $namespace_folder;
    
    /** The application folder is checked for file name **/
    $file_name = $application_folder_path . DIRECTORY_SEPARATOR . $namespace_folder . DIRECTORY_SEPARATOR . $class_name;
	
    if (is_file($file_name))
        include_once($file_name);
    else {
        /** The templates folder is checked for file name **/
        $file_name = $templates_folder_path . DIRECTORY_SEPARATOR . $class_name;
        if (is_file($file_name))
            include_once($file_name);
        else {
            /** The framework libraries folder is checked for file name **/
            $file_name = $frameworks_folder_path . DIRECTORY_SEPARATOR . $class_name;
			           
            if (is_file($file_name))
                include_once($file_name);
        }
    }
}

/**
 * Registers the autoload function
 */
spl_autoload_register("\ApplicationFramework\autoload_framework_classes", true, true);

?>