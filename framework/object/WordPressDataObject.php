<?php

namespace Framework\Object;

/**
 * This class implements the WordPress Object class 
 * 
 * Each object of this class represents a single WordPress item. e.g post, taxonomy, tag etc
 * It contains functions that help in constructing WordPress data objects
 * 
 * @category   Framework
 * @package    Object
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class WordPressDataObject extends DataObject
{
	/**
     * The details of the WordPress data
     * It is an array with following keys:
	 * type => the type of data. currently only type of post is supported
	 * type_name => the name of the data type. e.g name of custom post	
	 * 
     * @since 1.0.0
     */
    private $parameters               = "";
	    
	/** 
	 * Class constructor
	 * Used to set the object parameters
	 * 
	 * It sets the object parameters. i.e the type of object and the name of the object type
	 * It also sets the configuration object
	 * 
	 * @since 1.0.0
	 * @param object $configuration_object the configuration object for the module. it is an array with following keys:
	 * type => the type of data. currently only type of post is supported
	 * type_name => the name of the data type. e.g name of custom post	 
	 */
	public function __construct($configuration_object,$parameters)
	{
		/** The configuration object is set */
		$this->SetConfigurationObject($configuration_object);
		/** The object parameters are set */
		$this->parameters   = $parameters;
		/** An exception is thrown if the type of data is not correct */
    	if ($parameters['type'] !="post")
    	    throw new \Exception("Invalid type given");    
	}
	
	/**
     * Used to load the data from database
     * 
     * It reads data from WordPress and returns it
	 * For object type of post, the custom fields of the post are also returned
     * 
     * @since 1.0.0
	 * @param mixed $parameters used to read the data from database
	 * @throws object Exception an exception is thrown if the type of data is not correct	 
	 * 
	 * @return $data the WordPress object data or false if the data was not found
     */
    final public function LoadWordPressObject($parameters)
    {
    	/** The data that needs to be returned */
    	$data                         = false;
    	/** If the type of the current object is post */
    	if ($this->parameters['type'] == 'post') {
    		/** The parameters for searching for the post */
			$args                     = array("post_type"=>$this->parameters['type_name']);
    		/** The name of the key field is fetched */
    		$key_field                = $this->GetKeyField();
			/** The value of the key field */
			$key_field_value          = $this->data[$key_field];
			/** If the key field is a custom field */
			if (strpos($key_field,"custom_") !== false) {
				/** The custom_ prefix is removed */
				$key_field            = str_replace("custom_", "", $key_field);
				/** The parameters for searching for the post */
			    $args["meta_key"]     = $key_field;
			    $args["meta_value"]   = $key_field_value;
			}
			/** If the key field is not a custom field */
			else {
				/** The parameters for searching for the post */
			    $args[$key_field]     = $key_field_value;
			}
			/** 
			 * If the posts per page attribute is set in data property then it is added to query parameters
			 * Otherwise posts per page is set to 5
			 */
			$args['posts_per_page']   = (isset($this->data['posts_per_page']))?$this->data['posts_per_page']:5;
			/** The post is searched */
			$posts                    = get_posts($args);
			/** If the post was found */
			if (isset($posts[0])) {
				/** The custom fields for each post is fetched */
				for ($count = 0; $count < count($posts); $count++) {
					/** The object is converted to an array */
					$post             = get_object_vars($posts[$count]);
					/** The post id */
					$post_id          = $post['ID'];
					/** The custom fields of the post are fetched */
					$custom_fields    = get_post_custom($post_id);
					/** The custom fields are added to the post */
					$post             = array_merge($post,$custom_fields);
					/** The post is added to the $data variable */
				    $data[]           = $post;						
				}				
			}			
    	}
		
		return $data;		
    }  

    /**
     * Used to load the data from database to the data object property
     * 
     * It reads data from WordPress and loads it to the $data property of the object
	 * The type of WordPress object to load
	 * For example custom post or category is given in the parameters argument of the constructor    
     * 
     * @since 1.0.0
	 * @param mixed $parameters used to read the data from database
	 * @throws object Exception an exception is thrown if the type of data is not correct	 
	 * 
	 * @return $is_valid used to indicate that data was found in database
     */
    final public function Read($parameters)
    {
    	/** Used to indicate if the record exists */
    	$is_valid                     = false;
		/** The WordPress object is loaded */
    	$data                         = $this->LoadWordPressObject($parameters);
		/** If the object was found */
		if ($data) {
		    /** then it is set to the data property of the current object */ 
			$this->data               = $data;
			/** is_valid is set to true */
			$is_valid                 = true;
		}
		return $is_valid;		
    }      
	
	/**
     * Used to indicate if the WordPress object already exists in database
     * 
     * If the current WordPress object is a post, then the method will check if
	 * A post with the value in the key_field object property exists
     * 
     * @since 1.0.0
     * 
     * @return boolean $record_exists it is true if the record already exists. it is false otherwise 
     */
    public function RecordExists()
    {
    	/** Used to indicate if the record exists */
    	$record_exists                = false;
    	/** The WordPress object is loaded */
    	$data                         = $this->LoadWordPressObject(array());
		/** If the data was found then record_exists is set to true */
		$record_exists                = ($data)?true:false;
		
        return $record_exists;
    }
	
    /**
     * Used to delete the WordPress data
     * 
     * It deletes data from WordPress
     * 
     * @since 1.0.0		 
	 * @throws object Exception an exception is thrown if the object is read only
     * @throws object Exception an exception is thrown if the object could not be deleted
     */
    final public function Delete()
    {
    	/** If the current object is set to read only then an exception is thrown */
    	if ($this->readonly) throw new \Exception("Cannot delete readonly object");        
        
		/** An exception is thrown if the type, type_name or id parameters are not given */
    	if (!isset($this->parameters['type']) || !isset($this->parameters['type_name']) || !isset($this->parameters['id'])) {
    		throw new \Exception("Invalid parameters were given");
    	}
		
		/** An exception is thrown if the type of data is not correct */
    	if ($this->parameters['type'] !="post") {
    		throw new \Exception("Invalid type given");
    	}
    }


    /**
     * Used to save the object data
     * 
     * It saves the object data to a WordPress object. e.g post
     * 
     * @since 1.0.0
	 * @throws object Exception an exception is thrown if the object is read only
	 * @throws object Exception an exception is thrown if the object's data could not be saved
     */
    final public function Save()
    {
    	/** If the current object is set to read only then an exception is thrown */
    	if ($this->readonly) throw new \Exception("Cannot save readonly object");
		/** If the data needs to be saved to a post */
		if ($this->parameters['type'] == "post") {
			/** The WordPress object is loaded */
    	    $data                                    = $this->LoadWordPressObject(array());
			/** If the post already exists, then it is deleted */
			if (isset($data[0]['ID']))
			    wp_delete_post($data[0]['ID'], true);
			/** If the post_status is not set then it is set to publish */
		    $this->data['post_status']               = (isset($this->data['post_status']))?$this->data['post_status']:'publish';
			/** If the post_type is not set then it is set to the type_name parameter */
		    $this->data['post_type']                 = (isset($this->data['post_type']))?$this->data['post_type']:$this->parameters['type_name'];
			/** If the post_type is empty then it is set to post */
			$this->data['post_type']                 = ($this->data['post_type'] == '')?'post':$this->data['post_type'];
		    /** If the post title is not set then it is set to the string 'title' */
		    $this->data['post_title']                = (isset($this->data['post_title']))?$this->data['post_title']:'title';		   
		    /** If the post content is not set then it is set to the post title */
		    $this->data['post_content']              = (isset($this->data['post_content']))?$this->data['post_content']:$this->data['post_title'];		    
            /** If the post excerpt is not set then it is set to the post title */
		    $this->data['post_excerpt']              = (isset($this->data['post_title']))?$this->data['post_title']:' ';
			/** The post is added to database */
			$insert_post_result                      = wp_insert_post($this->data,true);
			/** If the result of adding the new post is a WP_Error object then an exception is thrown */
		    if (is_wp_error($insert_post_result)) {
			    throw new \Exception("New post could not be added. Details: ".$insert_post_result->get_error_message());
		    }
			/** If the post was successfully added then the post id is set */
			else $post_id                             = $insert_post_result;		
			/** Each data field is checked */
			foreach ($this->data as $field_name => $field_value) {
				/** If the field name starts with custom_ then it is considered to be a custom field */
				if (strpos($field_name, "custom_") !==false) {					
					/** The custom_ prefix is removed from field name */
					$field_name                      = str_replace("custom_", "", $field_name);
					/** The custom field is added to the last added post */
					add_post_meta($post_id,$field_name,$field_value,true);		
				}
			}
		}
       /** Otherwise an exception is thrown */
	   else
	       throw new \Exception("Invalid type given");
    }
}