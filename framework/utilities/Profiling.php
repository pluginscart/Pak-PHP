<?php

namespace Framework\Utilities;

/**
 * Singleton class
 * Profiling class provides functions related to profiling
 * 
 * It includes functions for getting the function execution time,
 * stack trace, cpu and memory usage and code coverage data
 * 
 * @category   Framework
 * @package    Utilities
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0 
 * @author 	   Nadir Latif <nadir@pakiddat.com>
 */
final class Profiling
{    
    /**
     * The single static instance
     */
    protected static $instance;
    /**
     * The start execution time
     */
    private $start_time;    
    /**
     * Used to return a single instance of the class
     * 
     * Checks if instance already exists
     * If it does not exist then it is created
     * The instance is returned
     * 
     * @since 1.0.0
     * @return Utilities static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance()
    {        
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;        
    }
	
	/**
     * Main logging function
	 * It is used to log the given data
     * 
     * It logs the data to the given destination     
     * 
     * @since 1.0.0		 
     * @param string $required_data [execution_time] the profiling data that is required
     */
    public function StartProfiling($required_data)
    {
    	/** If the execution time is required */
		if (strpos($required_data, "execution_time") !== false) {
			$this->start_time            = microtime(true);
		}
    }
	
	/**
     * Used to get the total execution time	 
     * 
     * It gets the difference between the current time and the start time
	 * The time difference is returned     
     * 
     * @since 1.0.0		 
     * @return int $execution_time the total execution time
     */
    public function GetExecutionTime()
    {
    	/** The total execution time */
    	$execution_time       = (microtime(true) - $this->start_time);
		
		return $execution_time;		
    }	
}