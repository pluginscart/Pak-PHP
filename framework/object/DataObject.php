<?php

namespace Framework\Object;

use \Framework\Configuration\Base as Base;

/**
 * Abstract class. must be implemented by a child class
 * This class provides a base class for Data Objects
 * 
 * Each object of this class represents a single data object. e.g database row or database table
 * It contains functions that help in constructing  data objects
 * 
 * @category   Framework
 * @package    Object
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class DataObject extends Base
{
	/**
     * The field name. used to lookup the data
     * 
     * @since 1.0.0		
     */
    protected $key_field;
    /**
     * Object data
     * 
     * @since 1.0.0		
     */
    protected $data;
	/**
     * Used to indicate that object is read only
     * 
     * @since 1.0.0		
     */
    protected $readonly=true;
	
	/**
     * Used to set the readonly property
     * 
     * It sets the object property that indicated if object data is read only      	
     * 
     * @since 1.0.0
     * @param boolean $readonly used to indicate if object data is read only 
     */
    abstract public function SetReadOnly($readonly);
	
	/**
     * Used to set the field name
     * 
     * It sets the field name of the table          	
     * 
     * @since 1.0.0
     * @param string $key_field the field name of the data 
     */
    abstract public function SetKeyField($key_field);   
	
	/**
     * Used to get the key field
     * 
     * It gets the key field  	
     * 
     * @since 1.0.0
	 * 
     * @return string $key_field the field name of the data 
     */
    abstract public function GetKeyField();
	
    /**
     * Used to set the object data
     * 
     * It sets the object data
     * The data must be suitable for saving to database     		
     * 
     * @since 1.0.0
     * @param array $data the object data     
     */
    abstract public function Load($data);   
		
    /**
     * Used to get the object data
     * 
     * It returns the object data		 
     * 
     * @since 1.0.0
	 * 
     * @return $object_data array the object's data property is returned          	
     */
    abstract public function GetData();
	       
    /**
     * Used to set the object data
     * 
     * It sets the object data		 
     * 
     * @since 1.0.0
     * @param $data array the object data to be set 		 	
     */
    abstract public function SetData($data);
	   
    /**
     * Used to edit the object data
     * 
     * It edits the given property of the data object
     * The user should call save function to save the state of the object
     * 
     * @since 1.0.0		 	
     * @param string $field_name name of the field
     * @param string $field_value new value of the field
     */
    abstract public function Edit($field_name, $field_value);

    /**
     * Used to load the data from database to the data property of the object
     * 
     * It reads data from database and loads it to the $data property of the object
     * It uses the field name value given as parameter     
     * 
     * @since 1.0.0
	 * @param mixed $parameters the parameters used to read the data
     */
    public function Read($parameters){}
	
    /**
     * Used to get the value of required field
     * 
     * It returns the value of the required field		 
     * 
     * @since 1.0.0
     * @throws object Exception an exception is thrown if the field value does not exist
     * 
     * @return string $field_value the value of the given field name 
     */
    abstract public function Get($field_name);   
	
    /**
     * Used to delete the object data
     * 
     * It deletes data from database
     * 
     * @since 1.0.0		 
	 * @throws object Exception an exception is thrown if the object is read only
     * @throws object Exception an exception is thrown if the object could not be deleted
     */
    abstract public function Delete();
	    
    /**
     * Used to indicate if the record already exists in database
     * 
     * It checks if the field name of the record already exists in database
     * If it does then the function returns true
     * Otherwise it returns false
     * 
     * @since 1.0.0
     * 
     * @return boolean $record_exists it is true if the record already exists. it is false otherwise 
     */
    abstract public function RecordExists();
	
    /**
     * Used to save the object data
     * 
     * It saves the object data to database. If the field name of the data contains a value
     * Then the data is updated. Otherwise it is added
     * 
     * @since 1.0.0
	 * @throws object Exception an exception is thrown if the object is read only
	 * 
     * @return int $record_id the value of the field name of the saved row 
     */
    abstract public function Save();
}