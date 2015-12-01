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
     * Used to get the key field
     *
     * It gets the name of the key field
	 * The key field is used to search for the data
	 * 
     * @since 1.0.0     
	 * 
	 * @return string $field_name the field name for the object
     */
    public function GetKeyField()
    {        
        $key_field = $this->key_field;
		
		return $key_field; 
    }
	
	/**
     * Used to set the key field
     * 
     * It sets the key field
	 * 
     * @since 1.0.0
     * @param string $key_field key field used to search for the data     
     */
    public function SetKeyField($key_field)
    {        
        $this->key_field = $key_field; 
    }
	
    /**
     * Used to get the object data
     * 
     * It returns the object data		 
     * 
     * @since 1.0.0
	 * 
     * @return $object_data array the object's data property is returned          	
     */
    public function GetData()
    {        
        /** The object data is returned */
        return $this->data;        
    }
	
    /**
     * Used to set the object data
     * 
     * It sets the object data		 
     * 
     * @since 1.0.0
     * @param $data array the object data to be set 		 	
     */
    public function SetData($data)
    {        
        /** The  object data is set */
        $this->data = $data;        
    }
	
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
   public function Edit($field_name, $field_value)
   {        
        /** The field value is added to the $data property */
        $this->data[$field_name] = $field_value;       
   }

	/**
     * Used to set the readonly property
     * 
     * It sets the object property that indicated if object data is read only    
	 * It calls the SetReadOnly function of the underlying data object  	
     * 
     * @since 1.0.0
     * @param boolean $readonly used to indicate if object data is read only 
     */
    public function SetReadonly($readonly)
	{
		/** The readonly property of the underlying data object is called */
    	$this->readonly = $readonly;
	}

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
    public function Get($field_name)
    {        
        if (!isset($this->data[$field_name]))
            throw new \Exception("Value for the field: " . $field_name . " does not exist");
        
        /** The field value is fetched from $data property */
        $field_value = $this->data[$field_name];
        
        return $field_value;       
    }
	
    /**
     * Used to load the data from database to the data property of the object
     * 
     * It reads data from database and loads it to the $data property of the object
     * It uses the field name value given as parameter     
     * 
     * @since 1.0.0
	 * @param mixed $parameters the parameters used to read the data
     */
    abstract public function Read($parameters);
	
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
    public function RecordExists(){}
	
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