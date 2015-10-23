<?php

namespace Framework\Object;

use \Framework\Configuration\Base as Base;

/**
 * This class implements the base UiObject class 
 * 
 * Abstract class. must be extended by child class
 * It contains functions that help in constructing objects with user interfaces
 * Such as data tables
 * 
 * @category   Framework
 * @package    Object
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class UiObject extends Base
{	
    /**
     * Sub items of the UiObject instance
     * 
     * @since 1.0.0		
     */
    protected $sub_items;
    /**
     * Data of the UiObject instance     
     * 
     * @since 1.0.0		
     */
    protected $data;
    /**
     * Presentation object of the UiObject instance
     * It is used to present the data
     * 
     * @since 1.0.0		
     */
    protected $presentation_object;
	/**
     * ExcelFile database reader object
	 * It is used to read the excel data from database
     */
    protected $database_reader;
 	/**
     * Used to load the object with data
     * 
     * It loads the data from database to the object. It must be implemented by a child class
     * 
     * @since 1.0.0		 
	 * @param array $data optional data used to read from database          
     */
    function Read($data=""){}
    /**
     * Used to load the object with data
     * 
     * It loads the data to the object. It must be implemented by a child class
     * 
     * @since 1.0.0		 
     * @param array $data array containing data for the object and the sub items
	 * @param array $data optional data to be loaded into the current object
     */
    function Load($data=""){}
    /**
     * Used to save the data in the object
     * 
     * It saves the data in the object to database. It must be implemented by child class
     * 
     * @since 1.0.0     
     */
    function Save(){}
    /**
     * Used to display the data of the object in a template 
     * 
     * It renders the data in the object to a template. It must be implemented by child class		 
     * 
     * @since 1.0.0    
     */
    abstract function Display();
    /**
     * Used to delete the given object
     * 
     * It deletes the current object. It must be implemented by child class		 
     * 
     * @since 1.0.0     
     */
    function Delete(){}
	/**
     * Used to set the presentation object
     * 
     * It sets the current presentation object
     * 
     * @since 1.0.0		 
     * @param object $presentation_object the presentation object for the class
	 *     
     */
    final public function SetPresentationObject($presentation_object)
    {
    	$this->presentation_object = $presentation_object;
    }
	/**
     * Used to set the database reader object of the excel file
     * 
     * It sets the database reader object
	 * The database reader object is used to read the excel file data from database
     * 
     * @since 1.0.0
     * 	
     * @return void 
     */
    final public function SetDatabaseReader($database_reader)
    {        
       /** The database reader object is set */
       $this->database_reader = $database_reader;
    }
}