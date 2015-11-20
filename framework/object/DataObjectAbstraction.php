<?php

namespace Framework\Object;

use \Framework\Configuration\Base as Base;

/**
 * Abstract class. must be implemented by a child class
 * It provides an abstract class that functions as an abstraction layer
 *  
 * It functions as an abstraction layer between the user data object class and the underlying data object class
 * The underlying data object class allows access to a certain data source. e.g MySQL or WordPress
 * Each object of this class represents a single data object
 * Each function of this class simply calls the function of the underlying data source class 
 * 
 * @category   Framework
 * @package    Object
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class DataObjectAbstraction extends Base
{
	/** 
	 * The mapping between the data source name and the data source class
	 * 
     * @since 1.0.0		
	 */    
    protected $data_source_class_mapping = array("wordpress"=>"WordpressDataObject","mysql"=>"MysqlDataObject");
	/** 
	 * The name of the underlying data source
	 * 
     * @since 1.0.0		
	 */    
    protected $data_source_name;
	
	/** 
	 * The object of the underlying data source class
	 * 
     * @since 1.0.0		
	 */    
    protected $data_source_object;
	    
	/**
	 * Class constructor
	 * Used to initialize the object
	 * 
	 * It sets the data source name
	 * It creates an instance of the relavant data source class	 
	 */
	public function __construct()
	{
		/** The application configuration is fetched */
		$configuration                = $this->GetConfigurationObject();		
		/** The data source name is set */
		$this->data_source_name       = $this->GetConfig("general","database_type");
		/** The data source class name */
		$this->data_source_class_name = '\Framework\Object\\'.$this->data_source_class_mapping[$this->data_source_name];
		/** The data source class object is created */		
		$this->data_source_object     = new $this->data_source_class_name();
		/** The application configuration object is set */
		$this->data_source_object->SetConfigurationObject($configuration);
	}
	
	/**
     * Used to get the table name
     * 
     * It gets the table name
	 * 
     * @since 1.0.0
     * @param string $table_name name of the database table from which the data is loaded
	 * 
	 * @return string $table_name the table name for the object     
     */
    final public function GetTableName($table_name)
    {        
        /** The table name is fetched from the underlying data source object */
    	$this->data_source_object->GetTableName($table_name);
    }
	
	/**
     * Used to set the table name
     * 
     * It sets the table name
	 * 
     * @since 1.0.0
     * @param string $table_name name of the database table from which the data is loaded     
     */
    final public function SetTableName($table_name)
    {        
        /** The table name is set in the underlying data source object */
    	$this->data_source_object->SetTableName($table_name);
    }      
	
	/**
     * Used to get the key field name
     * 
     * It gets the key field name
	 * 
     * @since 1.0.0     
	 * 
	 * @return string $key_field the key field for the object
     */
    final public function GetKeyField()
    {
    	/** The key field is fetched from the underlying data source */ 
        $key_field = $this->data_source_object->GetKeyField();
		
		return $key_field; 
    }
	
	/**
     * Used to set the key field
     * 
     * It sets the key field
	 * 
     * @since 1.0.0
     * @param string $key_field key field of the database table from which the data is loaded     
     */
    final public function SetKeyField($key_field)
    {
    	/** The key field is set in the underlying data source */ 
    	$this->data_source_object->SetKeyField($key_field);        
    }
	
    /**
     * Used to set the object data
     * 
     * It sets the object data
     * The data must be suitable for saving to database     		
     * 
     * @since 1.0.0
     * @param array $data the object data     
     */
    final public function Load($data)
    {
    	/** The data is loaded from the underlying data source object */
    	$this->data_source_object->Load($data);
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
    final public function GetData()
    {
    	/** The data is fetched from the underlying data source object */
    	$object_data = $this->data_source_object->GetData();
		
		return $object_data;
    }
	       
    /**
     * Used to set the object data
     * 
     * It sets the object data		 
     * 
     * @since 1.0.0
     * @param $data array the object data to be set 		 	
     */
    final public function SetData($data)
    {
    	/** The data is saved to the underlying data source object */
    	$this->data_source_object->SetData($data);
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
    final public function SetReadOnly($readonly)
	{
		/** The readonly property of the underlying data object is called */
    	$this->data_source_object->SetReadOnly($readonly);
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
    final public function Edit($field_name, $field_value)
    {
    	/** The data of the underlying data source object is edited */
    	$this->data_source_object->Edit($field_name, $field_value);
    }

    /**
     * Used to load the data from database to the data property of the object
     * 
     * It reads data from database and loads it to the $data property of the object
     * It uses the key field value given as parameter     
     * 
     * @since 1.0.0
	 * @param array $fields list of field names to fetch	  
     * @param mixed $condition the condition used to fetch the data from database
	 * @param boolean $read_all used to indicate if all data should be returned
     */
    final public function Read($fields,$condition,$read_all)
    {
    	/** The data is read from the underlying data source object */
    	$this->data_source_object->Read($fields,$condition,$read_all);
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
    final public function Get($field_name)
	{
		/** The field value is fetched from the underlying data source object */
		$field_value = $this->data_source_object->Get($field_name);
		
		return $field_value;
	}   
	
    /**
     * Used to delete the object data
     * 
     * It deletes data from database
     * 
     * @since 1.0.0		 
	 * @throws object Exception an exception is thrown if the object is read only
     * @throws object Exception an exception is thrown if the object could not be deleted
     */
    final public function Delete()
	{
		/** 
		 * The object data is deleted from the underlying data source object
		 * The data is deleted from the data source
		 * The data should have been previously loaded from the data source
		 */
		$this->data_source_object->Delete();
	}
	    
    /**
     * Used to indicate if the record already exists in database
     * 
     * It checks if the key field of the record already exists in database
     * If it does then the function returns true
     * Otherwise it returns false
     * 
     * @since 1.0.0
     * 
     * @return boolean $record_exists it is true if the record already exists. it is false otherwise 
     */
    final public function RecordExists()
	{
		/** It checks if the underlying data source contains the data */
		$this->data_source_object->RecordExists();
	}
	
    /**
     * Used to save the object data
     * 
     * It saves the object data to database. If the key field of the data contains a value
     * Then the data is updated. Otherwise it is added
     * 
     * @since 1.0.0
	 * @throws object Exception an exception is thrown if the object is read only
	 * 
     * @return int $record_id the value of the key field of the saved row 
     */
    final public function Save()
	{
		/** It saves the underlying data source class object's data */
		$this->data_source_object->Save();
	}
	
		
	/**
     * Used to set the meta information for the given data type
	 * For example for database type of MySQL, the table name and key field is set	 
     * 
     * It sets the meta information for the current object
	 * It calls the SetMetaInformation function of the underlying data source class object
     * 
     * @since 1.0.0
	 * @param array $meta_information the meta information to set	 
     */
    public function SetMetaInformation($meta_information)
	{
		/** If MySQL is to be used then the MySQL table name and field name are set */
		if ($this->GetConfig("general","database_type") == "mysql") {
			/** The SetMetaInformation function of the underlying data source class object is called */
			$this->data_source_object->SetMetaInformation($meta_information);		    		   
		}
	}
}