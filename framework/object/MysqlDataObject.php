<?php

namespace Framework\Object;

use \Framework\Configuration\Base as Base;

/**
 * This class implements the base DataObject class 
 * 
 * Each object of this class represents a single object. e.g database row or database table
 * It contains functions that help in constructing  data objects
 * 
 * @category   Framework
 * @package    Object
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class DataObject Base
{
    /**
     * MySQL table key field
	 * Used to lookup the data in database
     * 
     * @since 1.0.0		
     */
    public $key_field;
    /**
     * MySQL table name
     * 
     * @since 1.0.0		
     */
    public $table_name;
    /**
     * Object data
     * 
     * @since 1.0.0		
     */
    protected $data;    
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
    function Load($data)
    {        
        $this->data = $data;       
    }    
    /**
     * Used to get the  object data
     * 
     * It returns the object data		 
     * 
     * @since 1.0.0
	 * 
     * @return $object_data array the  object's data property is returned          	
     */
    function GetData()
    {        
        /** The  object data is returned */
        return $this->data;        
    }    
    /**
     * Used to set the  object data
     * 
     * It sets the buisness object data		 
     * 
     * @since 1.0.0
     * @param $data array the object data to be set 		 	
     */
    function SetData($data)
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
    function Edit($field_name, $field_value)
    {        
        /** The field value is added to the $data property */
        $this->data[$field_name] = $field_value;       
    }    
    /**
     * Used to load the data from database to the data property of the object
     * 
     * It reads data from database and loads it to the $data property of the object
     * It uses the key field value given as parameter
     * The current object corresponds to a single database row 
     * 
     * @since 1.0.0
     * @param int $id the key field value of the row
     * @throws Exception an object of type Exception is thrown if the data does not exist or could not be read
     */
    function Read($id)
    {        
        /** The data array is initialized */
        $this->data = array();
        /** The $db_functions object is initialized and cleared */
        $this->configuration->GetComponent("database")->df_initialize();
        /** The select query is built */
        $main_query             = array();
        $main_query[0]['field'] = "*";
        
        $where_clause = array();
        
        $where_clause                 = array();
        $where_clause[0]['field']     = $this->$key_field;
        $where_clause[0]['value']     = $id;
        $where_clause[0]['table']     = $this->table_name;
        $where_clause[0]['operation'] = '=';
        $where_clause[0]['operator']  = '';
        /** The data is fetched from database */
        $query                        = $this->configuration->GetComponent("database")->df_build_query($main_query, $where_clause, 's');
        $db_rows                      = $this->configuration->GetComponent("database")->df_all_rows($query);
        
        /** If no data was returned by select query then function returns */
        if (!isset($db_rows[0]))
            return;
        
        foreach ($db_rows[0] as $field_name => $field_value) {
            $this->data[$field_name] = $field_value;
        }       
    }    
    /**
     * Used to get the value of required field
     * 
     * It returns the value of the required field		 
     * 
     * @since 1.0.0
     * @throws Exception an object of type Exception is thrown if the field value does not exist
     * 
     * @return string $field_value the value of the given field name 
     */
    function Get($field_name)
    {        
        if (!isset($this->data[$field_name]))
            throw new \Exception("Value for the field: " . $field_name . " does not exist", 30);
        
        /** The field value is fetched from $data property */
        $field_value = $this->data[$field_name];
        
        return $field_value;       
    }    
    /**
     * Used to delete the object data
     * 
     * It deletes data from database
     * 
     * @since 1.0.0		 
     * @throws Exception an object of type Exception is thrown if the object could not be deleted
     */
    function Delete()
    {        
        /** The $db_functions object is initialized and cleared */
        $this->configuration->GetComponent("database")->df_initialize();
        /** The where clause of the database query is created */
        $counter      = 0;
        $where_clause = array();
        
        foreach ($this->data as $field_name => $field_value) {
            $where_clause[$counter]['field']     = $field_name;
            $where_clause[$counter]['value']     = $field_value;
            $where_clause[$counter]['table']     = $this->table_name;
            $where_clause[$counter]['operation'] = '=';
            $where_clause[$counter]['operator']  = 'AND';
            $counter++;
        }
        
        $where_clause[$counter - 1]['operator'] = '';
        /** The database query is built */
        $query                                  = $this->configuration->GetComponent("database")->df_build_query(array(), $where_clause, 'd');
        /** The database query is executed. An exception is thrown if the data could not be deleted */
        if (!$this->configuration->GetComponent("database")->df_execute($query))
            throw new \Exception("Data could not be deleted", 30);        
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
    function RecordExists()
    {        
        /** The $db_functions object is initialized and cleared */
        $this->configuration->GetComponent("database")->df_initialize();
        
        $main_query             = array();
        $main_query[0]['field'] = $this->key_field;
        $main_query[0]['table'] = $this->table_name;
        
        $counter      = 0;
        $where_clause = array();
        foreach ($this->data as $field_name => $field_value) {
            $where_clause[$counter]['field']     = $field_name;
            $where_clause[$counter]['value']     = $field_value;
            $where_clause[$counter]['table']     = $this->table_name;
            $where_clause[$counter]['operation'] = '=';
            $where_clause[$counter]['operator']  = 'AND';
            $counter++;
        }
        
        $where_clause[$counter - 1]['operator'] = '';
        
        $query   = $this->configuration->GetComponent("database")->df_build_query($main_query, $where_clause, 's');
        $db_rows = $this->configuration->GetComponent("database")->df_all_rows($query);
        
        if (isset($db_rows[0][$this->key_field]))
            $record_exists = true;
        else
            $record_exists = false;
        
        return $record_exists;       
    }    
    /**
     * Used to save the object data
     * 
     * It saves the object data to database. If the key field of the data contains a value
     * Then the data is updated. Otherwise it is added
     * 
     * @since 1.0.0
     * @return int $record_id the value of the key field of the saved row 
     */
    function Save()
    {        
        /** The $record_id variable is initialized */
        $record_id = '-1';
        /** The $db_functions object is initialized and cleared */
        $this->configuration->GetComponent("database")->df_initialize();
        /** If the $data contains key field information then it is updated */
        if (isset($this->data[$this->key_field])) {
            /** The update query fields are added to the database object */
            foreach ($this->data as $field_name => $field_value)
                $this->configuration->GetComponent("database")->df_add_update_field($field_name, $this->table_name, $field_value, true);
            /** The where clause of the update query is set */
            $this->configuration->GetComponent("database")->df_build_where_clause($this->key_field, $this->data[$this->key_field], true, $this->table_name, '=', '', '');
            /** The update query is fetched */
            $query_str = $this->configuration->GetComponent("database")->df_get_query_string('u');
            /** The update query is run */
            $this->configuration->GetComponent("database")->df_execute($query_str);
            /** The key field value for the data */
            $record_id = $this->data[$this->key_field];
        }
        /** If the $data does not contain key field information then it is added */
        else {
            /** The insert query fields are added to the database object */
            foreach ($this->data as $field_name => $field_value)
                $this->configuration->GetComponent("database")->df_build_insert_query($field_name, $field_value, true, $this->table_name);
            /** The insert query is fetched */
            $query_str = $this->configuration->GetComponent("database")->df_get_query_string('i');
            /** The insert query is run */
            $this->configuration->GetComponent("database")->df_execute($query_str);
            /** The id of the last added record */
            $record_id = $this->configuration->GetComponent("database")->df_last_insert_id();
        }
        return $record_id;
        
    }
}