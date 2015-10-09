<?php

namespace Framework\Object;

/**
 * This class implements the base UiObject class 
 * 
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
abstract class UiObject
{
    /**
     * Sub items of the UiObject instance
     * 
     * @since 1.0.0		
     */
    protected $sub_items;
    /**
     * Data object of the UiObject instance
     * It allows saving/loading/deleting the instance from MySQL database
     * 
     * @since 1.0.0		
     */
    protected $data_object;
    /**
     * Presentation object of the UiObject instance
     * It is used to present the data
     * 
     * @since 1.0.0		
     */
    protected $presentation_object;
 	/**
     * Used to load the object with data
     * 
     * It loads the data from database to the object. It must be implemented by a child class
     * 
     * @since 1.0.0		           
     */
    abstract function Read();
    /**
     * Used to load the object with data
     * 
     * It loads the data to the object. It must be implemented by a child class
     * 
     * @since 1.0.0		 
     * @param array $data array containing data for the object and the sub items     
     */
    abstract function Load($data);
    /**
     * Used to save the data in the object
     * 
     * It saves the data in the object to database. It must be implemented by child class
     * 
     * @since 1.0.0     
     */
    abstract function Save();
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
    abstract function Delete();
	/**
     * Used to set the presentation object
     * 
     * It sets the current presentation object
     * 
     * @since 1.0.0		 
     * @param object $presentation_object the presentation object for the class
	 *     
     */
    public function SetPresentationObject($presentation_object) {
    	$this->presentation_object = $presentation_object;
    }	
}