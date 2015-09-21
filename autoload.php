<?php

namespace Framework;
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
	/** The namespace seperator is replaced with directory seperator **/
	$class_name=str_replace("\\",DIRECTORY_SEPARATOR,$class_name);
    /** The class name is split into namespace and short class name **/
    $path_info = explode(DIRECTORY_SEPARATOR, $class_name);
	/** The namepsace is extracted **/
	$namespace = implode(DIRECTORY_SEPARATOR,array_slice($path_info, 0, count($path_info)-1));
	/** The class name is extracted **/
	$class_name = $path_info[count($path_info)-1];
    /** The namespace is converted to lower case **/
    $namespace_folder        = trim(strtolower($namespace),DIRECTORY_SEPARATOR);
	/** .php is added to class name **/
	$class_name=$class_name.".php";
	 /** The applications folder name **/
    $framework_folder_path = realpath(dirname(__FILE__));    
    /** The templates folder path **/
    $templates_folder_path   = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $namespace_folder . DIRECTORY_SEPARATOR . "includes";
    /** The path to the frameworks libraries folder **/
    $libraries_folder_path  = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."includes" . DIRECTORY_SEPARATOR . $namespace_folder;
    
    /** The application folder is checked for file name **/
    $file_name = $framework_folder_path . DIRECTORY_SEPARATOR . $namespace_folder . DIRECTORY_SEPARATOR . $class_name;
	
    if (is_file($file_name))
        include_once($file_name);   
}

/**
 * Registers the autoload function
 */
spl_autoload_register("\Framework\autoload_framework_classes", true, true);

?>