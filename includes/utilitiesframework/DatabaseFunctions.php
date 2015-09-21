<?php
namespace UtilitiesFramework;
/**
 * Build and execute MySQL database queries
 * 
 * This class is a wrapper around mysqli functions
 * It is specially usefully for executing very long insert and update queries
 * It can be used to generate select,insert,update and delete queries
 * 
 * @category   Utilities
 * @package    UtilitiesFramework
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.1.0
 * @link       N.A
 */
class DatabaseFunctions
{

    /**
     * Used to indicate start of sub where clause
     */
    const START_SUB_WHERE = 100;
    /**
     * Used to indicate end of sub where clause
     */
    const END_SUB_WHERE = 101;
    
    /**
     * MySQL Database connection resource
     */
    private $Id;
    /**
     * Used to indicate the query debug. i.e 0=silent, 1=normal, 2=debug, 3=trace		
     */
    private $debug;
    /**
     * The complete MySQL query
     */
    private $query = '';
    /**
     * The complete MySQL where clause
     */
    private $where_clause = '';
    /**
     * The type of the MySQL query. u=>update,s=>select,i=>insert,d=>delete
     */
    private $query_type = '';
    /**
     * The list of MySQL tables used in the query
     */
    private $table_list = array();
    /**
     * The list of field names used in the MySQL query
     */
    private $field_list = array();
    /**
     * The list of field values used in the MySQL query
     */
    private $value_list = array();
    /**
     * The list of display fields used in the MySQL query
     */
    private $display_fields = array();
    /**
     * The start value in LIMIT clause of MySQL query
     */
    private $start = 0;
    /**
     * The end value in LIMIT clause of MySQL query
     */
    private $end = 0;
    /**
     * The MySQL query log information
     */
    private $query_log = array();
    /**
     * The sort field using in ORDER BY clause of MySQL
     */
    private $sort_by = "";
    /**
     * The order by field using in ORDER BY clause of MySQL
     */
    private $order_by = "";
    /**
     * Used to indicate if the current application is being run from a browser
     */
    private $is_browser_application = true;

    /**
     * Class constructor
     * 
     * Initializes object variables. connects to database server
     * Calls the relevant constructor function depending on the parameters passed
     * 
     * @since 1.0.0     
     * @param array $parameters database server connection information contains following keys:
     * host=> database server host
     * user=> database user
     * password=> database
     * database=> name of the database
     * debug=> 0 implies no debugging. 1 implies logging sql queries. 2 implies logging query execution time		
     * @throws Exception object if database connection could not be established or an error occured
     */
    function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if ($i == 1)
            $this->DatabaseFunctionsFactory($a[0]);
    }

    /**
     * Class constructor
     * 
     * Initializes object variables. connects to database server
     * 
     * @since 1.0.0     
     * @param array $parameters database server connection information OR
     * @throws Exception object if database connection could not be established or an error occured
     */
    public function DatabaseFunctionsFactory($parameters)
    {
        $this->id = 0;
        /** The object variables are cleared **/
        $this->df_initialize();
        /** Used to determine if application is running from browser or command line **/
        $this->is_browser_application = (isset($_SERVER['HTTP_HOST'])) ? true : false;
        $this->debug                  = (isset($parameters['debug'])) ? $parameters['debug'] : 0; // 0=silent, 1=normal, 2=debug, 3=trace       	
        // Try to connect when instance is created
        if ($parameters['host'] !== '') {
            $this->id = $this->internal_df_connect($parameters['host'], $parameters['user'], $parameters['password'], $parameters['database']);
            if (!$this->id)
                throw new \Exception("Error in establishing database server connection. Details: " . mysqli_error($this->id), 80);
        }        
    }

    /**
     * Class constructor
     * 
     * Initializes object variables. connects to database server
     * Should be called by a factory class that accepts variable arguments
     * 
     * @since 1.0.0     
     * @param string $srv database server host name
     * @param string $uid database user name
     * @param string $pwd database password
     * @param string $db database name
     * @param string $debug used to allow debugging of the sql queries
     * @param string $type type of database query. u=>update,s=>select,i=>insert,d=>delete
     * @throws Exception object if database connection could not be established or an error occured
     */
    public function DatabaseFunctionsDirect($srv = '', $uid = '', $pwd = '', $db = '', $debug = 0, $type = 's')
    {        
        $this->id = 0;
        $this->df_initialize();
        $this->debug = $debug;
        // Try to connect when instance is created
        if ($srv !== '') {
            $this->id = $this->internal_df_connect($srv, $uid, $pwd, $db);
            if (!$this->id)
                throw new \Exception("Error in establishing database server connection. Details: " . mysqli_error($this->id), 80);
        }       
    }

    /**
     * Connects to mysql server
     * 
     * Initializes object variables. connects to database server
     * 
     * @since 1.0.0     
     * @param string $srv database server host name
     * @param string $uid database user name
     * @param string $pwd database password
     * @param string $db database name
     * @param string $debug used to allow debugging of the sql queries
     * @param string $type type of database query. u=>update,s=>select,i=>insert,d=>delete     
     * @throws Exception object if database connection could not be established or an error occured
	 * 
	 * @return boolean $is_valid returns true if database connection succeeded. throws exeption otherwise 
     */
    public function df_connect($srv, $uid, $pwd, $db, $debug = 0, $type = 's')
    {        
        $this->query_type = $type;
        $this->debug      = $debug;
        $this->id         = $this->internal_df_connect($srv, $uid, $pwd, $db);
        if ($this->id === false)
            throw new \Exception("Error in establishing database server connection. Details: " . mysqli_error($this->id), 80, $e);
        return true;       
    }

    /**
     * Used to close the mysql connection
     * 
     * @since 1.0.0
     * 
     * @return void     
     */
    public function df_close()
    {        
        if ($this->id !== false)
            $this->internal_df_close();       
    }

    /**
     * Used to execute the given sql query
     *
	 * @since 1.0.0 
     * @param string $sql sql query that needs to be executed
	 * 
     * @return boolean true if sql query was successfully executed. throws an exception otherwise     
     */
    public function df_execute($sql)
    {        
        $rsid = $this->internal_df_open($sql);
        return true;       
    }

    /**
     * Used to fetch the first row of the select query results
     * 
     * @since 1.0.0     
     * @param string $sql sql query for which the data needs to be fetched
	 * 
     * @return array first row of the select query result    
     */
    public function df_first_row($sql)
    {        
        $rsid = $this->internal_df_open($sql);
        $x    = $this->internal_df_fetch($rsid);
        return $x;       
    }

    /**
     * Used to fetch all the rows of the select query results
     * 
     * @since 1.0.0     
     * @param string $sql sql query for which the data needs to be fetched
	 * 
     * @return array all the rows of the select query result     
     */
    public function df_all_rows($sql)
    {        
        $rsid = $this->internal_df_open($sql);
        $x    = array();
        while ($r = $this->internal_df_fetch($rsid)) {
            $x[] = $r;
        }
        
        return $x;       
    }

    /**
     * Used to get the number of rows affected by the last query
     * 
     * @since 1.0.0
     * 
     * @return int number of rows affected by last database query     
     */
    public function df_affected_rows()
    {        
        return mysqli_affected_rows($this->id);        
    }

    /**
     * Used to get the names of all the table fields
     * 
     * @since 1.0.0     
     * @param string $table_name the name of the table		 
     * 
     * @return array $field_names the names of all the table fields
     */
    public function df_get_field_names($table_name)
    {        
        $field_names = array();
        $query       = "SHOW COLUMNS FROM " . $table_name;
        $result      = mysqli_query($this->id, $query);
        
        while ($row = mysqli_fetch_assoc($result))
            $field_names[] = $row['Field'];
        
        return $field_names;       
    }

    /**
     * Used to get the row id of the last row that was added to database
     * 
     * @since 1.0.0
     * 
     * @return int row id of last row added to database     
     */
    public function df_last_insert_id()
    {        
        return mysqli_insert_id($this->id);       
    }

    /**
     * Used to set the sort order used in select query
     * 
     * @since 1.0.0     
     * @param string $table_name the name of the table that contains the sort field
     * @param string $field_name the name of the field to sort by
     * @param string $sort_order the sort order. i.e asc or desc     
     * 
     * @return void		 
     */
    public function df_set_order_by($table_name, $field_name, $sort_order)
    {        
        $this->sort_by  = $table_name . "." . $field_name;
        $this->order_by = $sort_order;       
    }

    /**
     * Used to set the limit parameters used in select query
     * 
     * @since 1.0.0
     * 
     * @param int $start start number of the row
     * @param int $end end number of the row
	 *  
     * @return void     
     */
    public function df_set_limits($start, $end)
    {        
        $this->start = $start;
        $this->end   = $end;       
    }

    /**
     * Used to build the sql query string
     * 
     * @since 1.0.0     
     * @param array $main_query 2d array. the array has three columns used to hold the field name,table name and field value
     * @param array $where_clause 2d array. the array has five columns used to hold the field name,table name,field value,operation (AND,OR) and operator (<,=,>,>=,>=)
     * @param string $query_type type should be i,s,u or d     
     * 
     * @return string sql query string created from the data		 
     */
    public function df_build_query($main_query, $where_clause, $query_type)
    {        
        $this->df_set_query_type($query_type);
        
        for ($count = 0; $count < count($main_query); $count++) {
            $table_name = (isset($main_query[$count]['table'])) ? $main_query[$count]['table'] : "";
            if ($query_type == 's')
                $this->df_add_select_field($main_query[$count]['field'], $table_name);
            else if ($query_type == 'i')
                $this->df_build_insert_query($main_query[$count]['field'], $main_query[$count]['value'], true, $table_name);
            else if ($query_type == 'u')
                $this->internal_df_build_update_query($main_query[$count]['field'], $main_query[$count]['value'], true, true, $table_name, "", "");
        }
        
        if ($where_clause != '') {
            for ($count = 0; $count < count($where_clause); $count++) {
                $operation = (isset($where_clause[$count]['operation'])) ? $where_clause[$count]['operation'] : "=";
                $operator  = (isset($where_clause[$count]['operator'])) ? $where_clause[$count]['operator'] : "";
                $this->df_build_where_clause($where_clause[$count]['field'], $where_clause[$count]['value'], true, $where_clause[$count]['table'], $operation, $operator, false);
            }
        }
        
        return ($this->df_get_query_string());        
    }

    /**
     * Used to build an insert query
     * 
     * @since 1.0.0    
     * @param string $field table field name
     * @param string $value field value
     * @param boolean $is_string used to indicate if value is string
     * @param string $table name of the database table		      
     * 
     * @return void
     */
    public function df_build_insert_query($field, $value, $is_string, $table = '')
    {        
        $value = mysqli_escape_string($this->id, $value);
        
        if (count($this->table_list) == 0 && $table != '')
            $this->table_list[] = $table;
        
        $this->field_list[] = $field;
        
        if ($is_string)
            $this->value_list[] = "'" . $value . "'";
        else
            $this->value_list[] = $value;        
    }

    /**
     * Used to build a delete query
     * 
     * @since 1.0.0     
     * @param string $field table field name
     * @param string $value field value
     * @param boolean $is_string used to indicate if value is string
     * @param string $table name of the database table
     * @param string $operator the comparision operator. e.g <,>,=,!=,LIKE etc		      
     * 
     * @return void
     */
    public function df_build_delete_query($field, $value, $is_string, $table, $operator)
    {       
        $value = mysqli_escape_string($this->id, $value);
        
        if (!in_array($table, $this->table_list))
            $this->table_list[] = $table;
        if ($is_string)
            $this->where_clause .= $table . "." . $field . $operator . "'" . $value . "',";
        else
            $this->where_clause .= $table . "." . $field . $operator . $value . "',";        
    }

    /**
     * Used to return the where clause of the sql query
     * 
     * @since 1.0.0		 		    
     * 
     * @return string $where_clause 
     */
    public function df_get_where_clause()
    {        
        return $this->where_clause;       
    }

    /**
     * Used to clear the query log
     * 
     * @since 1.0.0		 		     
     *
     * @return void 
     */
    public function df_clear_query_log()
    {       
        $this->query_log = array();       
    }

    /**
     * Used to display the query log
     * 
     * @since 1.0.0		 		     
     *
     * @return void 
     */
    public function df_display_query_log()
    {        
        if ($this->is_browser_application)
            $line_break = "<br/>";
        else
            $line_break = "\n";
        
        for ($count = 0; $count < count($this->query_log); $count++) {
            $query = $this->query_log[$count];
            if ($this->debug > 1)
                echo ($count + 1) . ") time taken: " . $query['time_taken'] . " sec" . $line_break . "query: " . $query['sql'] . $line_break . $line_break;
            else if ($this->debug == 1)
                echo ($count + 1) . ") query: " . $query['sql'] . $line_break . $line_break;
        }        
    }

    /**
     * Used to fetch the final query string
     * 
     * @since 1.0.0     
     * @param string $type type of sql query. e.g i=>insert,d=>delete,u=>update,s=>select		      
     * 
     * @return string $sql the sql query
     */
    public function df_get_query_string($type = '')
    {        
        if ($type != '')
            $this->query_type = $type;
        if (strtolower($this->query_type) == 'i') {
            $this->query = "INSERT INTO " . $this->table_list[0] . "(";
            
            for ($count = 0; $count < count($this->field_list); $count++)
                $this->query .= ($this->field_list[$count] . ",");
            
            $this->query = trim($this->query, ',');
            
            $this->query .= ") VALUES(";
            
            for ($count = 0; $count < count($this->value_list); $count++)
                $this->query .= ($this->value_list[$count] . ",");
            
            $this->query = trim($this->query, ',');
            
            $this->query .= ")";
        } else if (strtolower($this->query_type) == 's') {
            if ($this->where_clause != "")
                $this->where_clause = trim($this->where_clause, ',');
            
            $this->query = "SELECT ";
            
            for ($count = 0; $count < count($this->display_fields); $count++)
                $this->query .= ($this->display_fields[$count] . ",");
            
            $this->query = trim($this->query, ',') . " FROM ";
            
            for ($count = 0; $count < count($this->table_list); $count++)
                $this->query .= ($this->table_list[$count] . ",");
            
            $this->query = trim($this->query, ',');
            
            if ($this->where_clause != "")
                $this->query .= " WHERE " . $this->where_clause;
            
            if ($this->sort_by != "" && $this->order_by != "")
                $this->query .= " ORDER BY " . $this->sort_by . " " . $this->order_by;
            
            if ($this->start < $this->end)
                $this->query .= " LIMIT " . $this->start . "," . $this->end;
        } else if (strtolower($this->query_type) == 'u') {
            if ($this->where_clause != "")
                $this->where_clause = trim($this->where_clause, ',');
            
            $this->query = "UPDATE ";
            
            for ($count = 0; $count < count($this->table_list); $count++)
                $this->query .= ($this->table_list[$count] . ",");
            
            $this->query = trim($this->query, ',');
            
            $this->query .= " SET ";
            
            for ($count = 0; $count < count($this->field_list); $count++)
                $this->query .= ($this->field_list[$count] . "=" . $this->value_list[$count] . ",");
            
            $this->query = trim($this->query, ',');
            
            if ($this->where_clause != "")
                $this->query .= " WHERE " . $this->where_clause;
        } else if (strtolower($this->query_type) == 'd') {
            if ($this->where_clause != "")
                $this->where_clause = trim($this->where_clause, ',');
            
            $this->query = "DELETE FROM ";
            
            $this->query .= ($this->table_list[0] . ",");
            
            $this->query = trim($this->query, ',');
            
            if ($this->where_clause != "")
                $this->query .= " WHERE " . $this->where_clause;
        } else
            throw new \Exception("Invalid query type given", 20);
        
        return $this->query;        
    }

    /**
     * Used to get the mysql query link
     * 
     * @since 1.0.0		      
     * 
     * @return int $Id the mysql query link resource 
     */
    public function df_get_id()
    {        
        return $this->id;        
    }

    /**
     * Used to set the query type of the query
     * 
     * @since 1.0.0
     * @param string $type type of sql query. e.g i,s,u or d     
     */
    function df_set_query_type($type)
    {       
        $this->query_type = $type;        
    }

    /**
     * Used to initialize object variables
     *  
     * @since 1.0.0		 		      
     * 
     * @return void 
     */
    public function df_initialize()
    {        
        $this->query          = '';
        $this->where_clause   = '';
        $this->query_type     = '';
        $this->sort_by        = '';
        $this->order_by       = '';
        $this->table_list     = array();
        $this->field_list     = array();
        $this->value_list     = array();
        $this->display_fields = array();
    }

    /**
     * Used to set the table name
     *  
     * @since 1.0.0		 		 
     * @param string $table_name the name of the database table     
     * 
     * @return void 
     */
    public function df_set_table($table_name)
    {
        $this->table_list[] = $table_name;       
    }

    /**
     * Used to add the display fields of a select query
     * 
     * @since 1.0.0		 
     * @param string $field name of the table field
     * @param string $table name of the database table     
     */
    public function df_add_select_field($field, $table)
    {        
        $this->internal_df_build_select_query($field, "", true, "", $table, "", "", false);        
    }

    /**
     * Used to add the update fields of an update query
     * 
     * @since 1.0.0		 
     * @param string $field name of the table field
     * @param string $table name of the database table
     * @param string $value field value
     * @param boolean $is_string used to indicate if the value is a string     
     */
    public function df_add_update_field($field, $table, $value, $is_string)
    {        
        $value = mysqli_escape_string($this->id, $value);
        
        $this->internal_df_build_update_query($field, $value, true, $is_string, $table, '', '');       
    }

    /**
     * Used to add the where clause of a select,update query or delete query
     * 
     * @since 1.0.0
     * 
     * @param string $field name of the table field
     * @param string $value field value
     * @param boolean $is_string used to indicate if the value is a string
     * @param string $table name of the database table
     * @param string $operation the comparision operator. e.g <,>,=,!=,LIKE etc
     * @param string $operator the operation to be performed. e.g AND, OR, NOT 
     * @param int    $options an integer value that indicates an extra option. e.g start of sub where clause. i.e '('. following options are supported: (,)		      
     * 
     * @return void
     */
    public function df_build_where_clause($field, $value, $is_string, $table, $operation, $operator, $options)
    {        
        $value = mysqli_escape_string($this->id, $value);
        
        $this->internal_df_build_select_query($field, $value, false, $is_string, $table, $operation, $operator, $options);        
    }

    /**
     * Commits the current transaction
     * 
     * MySQL commit only works with transactional table types like innodb
     * It does not support MyISAM table type
     * Once the transaction is commited the changes are written to database		 
     * 
     * @since 1.0.0		 
     * @throws Exception object if transaction could not be commited
     * 
     * @return void		 
     */
    public function df_commit()
    {        
        if (!mysqli_commit($this->id))
            throw new \Exception("Error in commiting transaction. Details: " . mysqli_error($this->id), 80, $e);        
    }

    /**
     * Rolls back the current transaction
     * 
     * MySQL rollback only works with transactional table types like innodb
     * It does not support MyISAM table type
     * Once the transaction is rolledback it cannot be saved to database
     * 
     * @since 1.0.0		 
     * @throws Exception object if transaction could not be commited
     * 
     * @return void		 
     */
    public function df_rollback()
    {        
        if (!mysqli_rollback($this->id))
            throw new \Exception("Error in rolling back transaction. Details: " . mysqli_error($this->id), 80, $e);        
    }

    /**
     * Turns on or off auto commit for MySQL queries
     * 
     * MySQL autocommit only works with transactional table types like innodb
     * It does not support MyISAM table type
     * Autocommit should be turned off if queries need to be run as part of a transaction
     * 
     * @since 1.0.0
     * @param boolean $is_enable is true if autocommit needs to be enabled. its false if it needs to be disabled
     * @throws Exception object if autocommit could not be turned off
     * 
     * @return void		 
     */
    public function df_toggle_autocommit($is_enable)
    {        
        if (!mysqli_autocommit($this->id, $is_enable))
            throw new \Exception("Error in changing autocommit value. Details: " . mysqli_error($this->id), 80, $e);        
    }

    /**
     * Used to connect to the mysql database server
     * 
     * @since 1.0.0
     * 
     * @param string $srv database server host name
     * @param string $uid database user name
     * @param string $pwd database password
     * @param string $db database name		 
     * @throws Exception object if an error occured
     * 
     * @return boolean $is_valid returns true if database connection succeeded. throws exeption otherwise 
     */
    private function internal_df_connect($srv, $uid, $pwd, $db)
    {        
        $Id = @mysqli_connect($srv, $uid, $pwd);
        if (($Id !== false) and ($db !== '')) {
            if (!@mysqli_select_db($Id, $db))
                throw new \Exception("Error in establishing database server connection. Details: " . mysqli_error($Id), 20);
        }
        return $Id;        
    }

    /**
     * Used to close the connection to the mysqld database
     * 
     * @since 1.0.0		 		     
     *
     * @return void 
     */
    private function internal_df_close()
    {        
        @mysqli_close($this->id);        
    }

    /**
     * Used to open a connection to a mysql database
     * 
     * @since 1.0.0
     * 
     * @param string $sql sql string that needs be executed		 
     * @throws Exception object if an error occured
     * 
     * @return int $rsid mysql query result 
     */
    private function internal_df_open($sql)
    {        
        /** If debugging mode is set to 1 or greater then the start time is noted **/
        if ($this->debug >= 1)
            $start_time = microtime(true);
        
        $rsid = @mysqli_query($this->id, $sql);
        
        if ($this->debug >= 1) {
            /** If debugging mode is set to 1 or greater then the end time is noted and the query is logged along with time taken **/
            $end_time        = microtime(true);
            /** The logging information is saved to array **/
            $sql_log         = array(
                array(
                    "sql" => $sql,
                    "time_taken" => number_format(($end_time - $start_time), 4)
                )
            );
            $this->query_log = array_merge($this->query_log, $sql_log);
            
        }
        
        if ($rsid)
            return $rsid;
        else
            throw new \Exception("Error in executing mysql query. Details: " . mysqli_error($this->id) . " sql: " . $sql, 80);        
    }

    /**
     * Used to fetch the results of mysql query
     * 
     * @since 1.0.0
     * 
     * @param int $rsid mysql resource id of the data that needs to be fetched     
     * 
     * @return array $row one row of the tables data
     */
    private function internal_df_fetch($rsid)
    {        
        $row = mysqli_fetch_assoc($rsid);
        return $row;        
    }

    /**
     * Used to build a select query
     * 
     * @since 1.0.0
     * 
     * @param string $field name of the table field
     * @param string $value field value
     * @param boolean $is_display_field used to indicate if the field is a display field or is used in where clause
     * @param boolean $is_string used to indicate if the value is a string
     * @param string $table name of the database table
     * @param string $operation the operation to be performed. e.g AND, OR, NOT
     * @param string $operator the comparision operator. e.g <,>,=,!=,LIKE etc 
     * @param int    $options an integer value that indicates an extra option. e.g start of sub where clause. i.e '('. following options are supported: (,)		      
     * 
     * @return void
     */
    private function internal_df_build_select_query($field, $value, $is_display_field, $is_string, $table, $operation, $operator, $options)
    {        
        $value = mysqli_escape_string($this->id, $value);
        if ($table != "" && !in_array($table, $this->table_list))
            $this->table_list[] = $table;
        
        if ($is_display_field) {
            if ($table != "")
                $this->display_fields[] = $table . "." . $field;
            else
                $this->display_fields[] = $field;
        } else {
            if ($options == DatabaseFunctions::START_SUB_WHERE)
                $this->where_clause .= "(";
            if ($table != "")
                $field_name = $table . "." . $field;
            else
                $field_name = $field;
            
            if ($is_string)
                $this->where_clause .= $field_name . $operation . "'" . $value . "'";
            else
                $this->where_clause .= $field_name . $operation . $value;
            if ($options == DatabaseFunctions::END_SUB_WHERE)
                $this->where_clause .= ")";
            
            $this->where_clause .= " " . $operator . " ";
        }        
    }

    /**
     * Used to build an update query
     * 
     * @since 1.0.0
     * 
     * @param string $field name of the table field
     * @param string $value field value
     * @param boolean $is_update_field used to indicate if the field is an update field or is used in where clause
     * @param boolean $is_string used to indicate if the value is a string
     * @param string $table name of the database table
     * @param string $operation the operation to be performed. e.g AND, OR, NOT
     * @param string $operator the comparision operator. e.g <,>,=,!=,LIKE etc 		      
     * 
     * @return void
     */
    private function internal_df_build_update_query($field, $value, $is_update_field, $is_string, $table, $operation, $operator)
    {
       $value = mysqli_escape_string($this->id, $value);
            
       if ($is_update_field) {
           if (!in_array($table, $this->table_list))
               $this->table_list[] = $table;
                
           $this->field_list[] = $field;
                
           if ($is_string)
               $this->value_list[] = "'" . $value . "'";
           else
               $this->value_list[] = $value;
        } else {
            if ($is_string)
                $this->where_clause .= $table . "." . $field . $operation . "'" . $value . "',";
            else
                $this->where_clause .= $table . "." . $field . $operation . $value . "',";
        }      
    }

}