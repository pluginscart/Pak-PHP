<?php

namespace Framework\Object;

/**
 * This class implements the MySQL Database Object class 
 * 
 * Each object of this class represents a single MySQL database object. e.g database row or database table
 * It contains functions that help in constructing MySQL data objects
 * 
 * @category   Framework
 * @package    Object
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class MysqlDataObject extends DataObject
{
	/**
     * Name of the field by which to order the data
     * 
     * @since 1.0.0
     */
    private $order_by               = "";
	/**
     * Sort order
     * 
     * @since 1.0.0
     */
    private $order_by_direction     = "ASC";
	/**
     * The table name
     * 
     * @since 1.0.0		
     */
    protected $table_name;
	   
	/**
     * Used to set the order by field
     * 
     * It sets the order by field
	 * 
     * @since 1.0.0
	 * @param string $table_name name of the MySQL database table
     * @param string $field_name sort field of the MySQL database table
     */
    final public function SetOrderBy($table_name,$field_name)
    {        
        $this->order_by = $table_name . "." . $field_name; 
    }
	
	/**
     * Used to get the table name
     * 
     * It gets the table name
	 * 
     * @since 1.0.0     
	 * 
	 * @return string $table_name the table name for the object     
     */
    final public function GetTableName()
    {        
        $table_name = $this->table_name;
		
		return $table_name; 
    }
	
	/**
     * Used to set the table name
     * 
     * It sets the table name
	 * 
     * @since 1.0.0
     * @param string $table_name name of the MySQL database table from which the data is loaded     
     */
    final public function SetTableName($table_name)
    {        
        $this->table_name = $table_name; 
    }
	        
    /**
     * Used to load the data from database to the data property of the object
     * 
     * It reads data from database and loads it to the $data property of the object
     * It uses the key field value given as parameter
     * The current object corresponds to a single database row 
     * 
     * @since 1.0.0
	 * @param mixed $parameters the parameters used to fetched the data. for relational data, it should have following keys:
	 * fields => list of field names to fetch
     * condition => the condition used to fetch the data from database
	 * It can be a single string or integer value. in this case the previously set field name and table name are used 
	 * Or an array with following fields: field,value,table,operation and operator
	 * read_all => used to indicate if all data should be returned
	 * In case of non relational data, it can be empty
	 * 
	 * @return $is_valid used to indicate that data was found in database
     */
    final public function Read($parameters)
    {
    	/** The list of fields to fetch */
    	$fields                                                        = $parameters['fields'];
		/** The condition for fetching the data */
    	$condition                                                     = $parameters['condition'];
		/** Used to indicate if all data should be fetched */
    	$read_all                                                      = $parameters['read_all'];
    	/** Used to indicate that data was found in database */
    	$data_found                                                    = true;
        /** The data array is initialized */
        $this->data                                                    = array();		
        /** The database object is initialized and cleared */
        $this->GetComponent("database")->df_initialize();
        /** The select query is built */
        $main_query                                                    = array();
		/** If the given field is a string then it is split on comma */        
		if (is_string($fields)) {
		    $fields                                                    = explode(",",$fields);
		}
		/** The given field data is converted to an array */
		if(is_array($fields)) {
		    /** The field names are added to the select query */
		    for ($count = 0; $count < count($fields); $count++) {
			    $main_query[$count]['field']                           = $fields[$count];
		    }
		}
        /** The where clause used to fetch data from database */
        $where_clause                                                  = array();	
        /** If the given condition is a string then it should be value of key field */        
		if (is_string($condition) || is_int($condition)) {			
            $where_clause[0]['field']                                  = $this->key_field;
            $where_clause[0]['value']                                  = $condition;
            $where_clause[0]['table']                                  = $this->table_name;
            $where_clause[0]['operation']                              = '=';
            $where_clause[0]['operator']                               = '';			
		}
		else if(is_array($condition)){
			/** The updated where clause containing default values */
			$updated_condition                                         = array();
			for ($count =0; $count<count($condition); $count++) {
				$updated_condition[$count]                             = $condition[$count];				
				/** If the table name is not set then the default table name is set */
				$updated_condition[$count]['table']                    = (!isset($condition[$count]['table']))?$this->table_name:$updated_condition[$count]['table'];
			}
			$where_clause                                              = $updated_condition;
		}
		else if (!$condition) {				
		    $where_clause                                              = '';
			$this->GetComponent("database")->df_set_table_name($this->table_name);
		}
		/** If the order by field is given then the data is sorted by this field */
		if ($this->order_by) {
		    list($order_by_table_name,$order_by)                       = explode(".",$this->order_by);
			$this->GetComponent("database")->df_set_order_by($order_by_table_name, $order_by, $this->order_by_direction);
		}
		
        /** The data is fetched from database */
        $query                                                         = $this->GetComponent("database")->df_build_query($main_query, $where_clause, 's');			
        $db_rows                                                       = $this->GetComponent("database")->df_all_rows($query);
        
        /** If no data was returned by select query then function returns false */
        if (!isset($db_rows[0]))
            $data_found                                                = false;
      
		if ($data_found) {
		    if(!$read_all) {			
                foreach ($db_rows[0] as $field_name => $field_value) {
                    $this->data[$field_name]                           = $field_value;
                }   
		    }
		    else {
		        $this->data                                            = $db_rows;	
		    }
 		}		
		
		return $data_found;    
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
    	/** If the current object is set to read only then an exception is thrown */
    	if ($this->readonly) throw new \Exception("Cannot delete readonly object.");        
        /** The database object is initialized and cleared */
        $this->GetComponent("database")->df_initialize();
        /** The where clause of the database query is created */
        $counter                                 = 0;
        $where_clause                            = array();
        
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
        $query                                   = $this->GetComponent("database")->df_build_query(array(), $where_clause, 'd');
        /** The database query is executed. An exception is thrown if the data could not be deleted */
        if (!$this->GetComponent("database")->df_execute($query))
            throw new \Exception("Data could not be deleted");        
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
        /** The database object is initialized and cleared */
        $this->GetComponent("database")->df_initialize();
        
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
        
        $query   = $this->GetComponent("database")->df_build_query($main_query, $where_clause, 's');
        $db_rows = $this->GetComponent("database")->df_all_rows($query);
        
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
	 * @throws object Exception an exception is thrown if the object is read only
	 * 
     * @return int $record_id the value of the key field of the saved row 
     */
    final public function Save()
    {
    	/** If the current object is set to read only then an exception is thrown */
    	if ($this->readonly) throw new \Exception("Cannot save readonly object.");        
        /** The $record_id variable is initialized */
        $record_id = '-1';
        /** The database object is initialized and cleared */
        $this->GetComponent("database")->df_initialize();
        /** If the $data contains key field information then it is updated */
        if (isset($this->data[$this->key_field])) {
            /** The update query fields are added to the database object */
            foreach ($this->data as $field_name => $field_value)
                $this->GetComponent("database")->df_add_update_field($field_name, $this->table_name, $field_value, true);
            /** The where clause of the update query is set */
            $this->GetComponent("database")->df_build_where_clause($this->key_field, $this->data[$this->key_field], true, $this->table_name, '=', '', '');
            /** The update query is fetched */
            $query_str = $this->GetComponent("database")->df_get_query_string('u');
            /** The update query is run */
            $this->GetComponent("database")->df_execute($query_str);
            /** The key field value for the data */
            $record_id = $this->data[$this->key_field];
        }
        /** If the $data does not contain key field information then it is added */
        else {
            /** The insert query fields are added to the database object */
            foreach ($this->data as $field_name => $field_value)
                $this->GetComponent("database")->df_build_insert_query($field_name, $field_value, true, $this->table_name);
            /** The insert query is fetched */
            $query_str = $this->GetComponent("database")->df_get_query_string('i');
            /** The insert query is run */
            $this->GetComponent("database")->df_execute($query_str);
            /** The id of the last added record */
            $record_id = $this->GetComponent("database")->df_last_insert_id();
        }
        return $record_id;
        
    }
		
	/**
     * Used to return the data table for the given data type
     * 
     * It returns the data table name from application configuration
     * 
     * @since 1.0.0
	 * @param string $data_type the type of the data
	 * 
	 * @return string $data_table_name name of the MySQL table for the given data type
     */
    final public function GetDatabaseTableName($data_type)
	{
		/** The data table names are fetched from application configuration */
		$mysql_table_names           = $this->GetConfig("general","mysql_table_names");
		$data_table_name             = $mysql_table_names[$data_type];
		
		return $data_table_name;
	}	
		
	/**
     * Used to set the meta information for the given data type	 
     * 
     * It sets the table name and field name for the given data type	
     * 
     * @since 1.0.0
	 * @param mixed $meta_information the meta information to set
	 * it is an array with following keys:
	 * data_type => the type of the data. e.g author
	 * field_name => the name of field to be used in the search
     */
    final public function SetMetaInformation($meta_information)
	{
		/** The database table name for the data type */
		$data_type_table   = $this->GetDatabaseTableName($meta_information['data_type']);			
		/** The table name for the current object is set */		    				   
		$this->SetTableName($data_type_table);		    
		/** The field name for the current object is set */				    
		$this->SetKeyField($meta_information['field_name']);		    
	}
}