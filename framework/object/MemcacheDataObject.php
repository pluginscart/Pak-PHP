<?php

namespace Framework\Object;

/**
 * This class implements the Memcache Object class 
 * 
 * Each object of this class represents a single Memcache data item. e.g a memcache key/value pair
 * It contains functions that help in constructing Memcache data objects
 * 
 * @category   Framework
 * @package    Object
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class MemcacheDataObject extends DataObject
{
	/** 
	 * Class constructor
	 * Used to add all the memcache servers to the memcache object
	 * 
	 * It adds each memcache server given in application configuration
	 * It also sets the configuration object
	 * 
	 * @since 1.0.0
	 * @param object $configuration_object the configuration object for the module	  
	 */
	public function __construct($configuration_object)
	{
		/** The configuration object is set */
		$this->SetConfigurationObject($configuration_object);
		/** Ip address of memcache server */
		$memcache_server        = $this->GetConfig("general","memcache_server");	
	    /** The memcache object connects to the memcache server */
	    $this->GetComponent("memcache")->pconnect($memcache_server);
	}
	
    /**
     * Used to set the object data
     * 
     * It sets the object data
     * The data must be suitable for saving to database
     * The field names in the $data array should correspond to database table field names		 
     * 
     * @since 1.0.0
     * @param array $data the object data     
     */
    final public function Load($data)
    {        
        $this->data = $data;       
    }
	
	/**
     * Used to get the field name
     * 
     * It gets the field name
	 * 
     * @since 1.0.0     
	 * 
	 * @return string $field_name the field name for the object
     */
    final public function GetKeyField()
    {        
        $field_name = $this->field_name;
		
		return $field_name; 
    }
	
	/**
     * Used to set the key field
     * 
     * It sets the key field
	 * 
     * @since 1.0.0
     * @param string $field_name key field of the MySQL database table from which the data is loaded     
     */
    final public function SetKeyField($field_name)
    {        
        $this->field_name = $field_name; 
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
    final public function SetData($data)
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
   final public function Edit($field_name, $field_value)
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
    final public function SetReadonly($readonly)
	{
		/** The readonly property of the underlying data object is called */
    	$this->readonly = $readonly;
	}
	
    /**
     * Used to load the data from memcache to the data property of the object
     * 
     * It reads data from memcache and loads it to the $data property of the object
     * It uses the object's field_name property as the memcache key
     * The current object corresponds to a memcache value that corresponds to a single memcache key 
     * 
     * @since 1.0.0
	 * @param mixed $parameters used to read the data from memcache
	 * 
	 * @return $is_valid used to indicate that data was found in memcache
     */
    final public function Read($parameters)
    {
    	/** The value is fetched from memcache */
    	$value                =  $this->GetComponent("memcache")->get($this->field_name);
		/** If the value exists then it is unserialized */
		if($value) {
			$this->data       = unserialize($value);
			$this->data       = array_values($this->data);
			$this->data       = $this->data[0];
		}
		/** Otherwise the result is set to false */
		else $this->data      = false;
		/** Used to indicate if the data was found by memcache or not */
		$is_valid             = ($this->data)?true:false;
		
		return $is_valid;
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
        if (!isset($this->data[$field_name]))
            throw new \Exception("Value for the field: " . $field_name . " does not exist");
        
        /** The field value is fetched from $data property */
        $field_value = $this->data[$field_name];
        
        return $field_value;       
    }
	
    /**
     * Used to delete the memcache key data
     * 
     * It deletes data from memcache
     * 
     * @since 1.0.0		 
	 * @throws object Exception an exception is thrown if the object is read only
     * @throws object Exception an exception is thrown if the object could not be deleted
     */
    final public function Delete()
    {
    	/** If the current object is set to read only then an exception is thrown */
    	if ($this->readonly) throw new \Exception("Cannot delete readonly object.");        
        /** The value is deleted from memcache */
    	$is_deleted                =  $this->GetComponent("memcache")->delete($this->field_name);
		/** An exception is thrown if the value could not be deleted */
		if (!$is_deleted) throw new \Exception("Memcache value for the key: ".$this->field_name." could not be deleted");
    }

    /**
     * Used to indicate if the record already exists in memcache
     * 
     * It checks if the key field of the record already exists in memcache
     * If it does then the function returns true
     * Otherwise it returns false
     * 
     * @since 1.0.0
     * 
     * @return boolean $record_exists it is true if the record already exists. it is false otherwise 
     */
    final public function RecordExists()
    {        
        /** The value is fetched from memcache */
    	$value                   =  $this->GetComponent("memcache")->get($this->field_name);
		/** If the value exists then $record_exists is set to true. Otherwise it is set to false */
		$record_exists           = ($value !==false)?true:false;		
		
		return $record_exists;
    }

    /**
     * Used to save the object data
     * 
     * It saves the object data to memcache
     * 
     * @since 1.0.0
	 * @throws object Exception an exception is thrown if the object is read only
	 * @throws object Exception an exception is thrown if the object's data could not be saved
     */
    final public function Save()
    {
    	/** If the current object is set to read only then an exception is thrown */
    	if ($this->readonly) throw new \Exception("Cannot save readonly object.");
		/** The data stored in memache. It is serialized before storing */
		$value                     = serialize($this->data); 
        /** The value is saved to memcache */
    	$is_saved                  =  $this->GetComponent("memcache")->set($this->field_name,$value,0,0);
		/** An exception is thrown if the value could not be saved */
		if (!$is_saved) throw new \Exception("Memcache value for the key: ".$this->field_name." could not be saved");
    }
}