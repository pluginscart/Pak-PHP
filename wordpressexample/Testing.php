<?php

namespace WordPressExample;

/**
 * This class implements the main test class
 * It contains functions that extend the WordPress XML-RPC interface
 * 
 * It allows unit testing the plugin
 * 
 * @category   WordPressExample
 * @package    Testing
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
final class Testing extends \Framework\Testing\Testing
{
	/**
	 * Used to register functions that extend the WordPress XML-RPC interface
	 *
	 * It adds functions to the WordPress XML-RPC interface
	 * 
	 * @since 1.0.0
	 * @param array $methods the list of xml-rpc methods provided by WordPress
	 * 
	 * @return array $methods the list updated WordPress XML-RPC methods
	 */
	public function RegisterXmlRpcMethods($methods){
		/** The WordPress plugin prefix */
		$plugin_prefix                                                    = $this->GetConfig("wordpress","plugin_prefix");
	
		/** The custom xml rpc functions are added */		
		$methods[$plugin_prefix.'.TestFunction']                          = array($this,"TestFunction");		
		
		return $methods;
	}
		
	/**
	 * Used to add a test function to the WordPress XML-RPC interface
	 *
	 * It adds a test function to the WordPress XML-RPC interface
	 * It can be used to unit test WordPress plugins
	 * 
	 * @since 1.0.0
	 * @param array $args the user login information. it is an array with following keys:
	 * blog id: the blog id
	 * username: the user name
	 * password: the password
	 */
	public function TestFunction($args){
		global $wp_xmlrpc_server;
		/** The rpc arguments are escaped */
        $wp_xmlrpc_server->escape($args);
        /** The blog id, user name and password */
        $blog_id  = $args[0];
        $username = $args[1];
        $password = $args[2];
        /** If the login info is not correct then an error is returned */
        if (! $user = $wp_xmlrpc_server->login($username,$password))
            return $wp_xmlrpc_server->error;

		try {
		    /** Success response is returned */
			return array("result"=>"success");
		}
		catch(\Exception $e) {
			die($e->getMessage());
		}        
	}
	
	/**
	 * Used to register custom post types and custom taxonomies with WordPress
	 *
	 * It adds following custom post types: websites
	 * 
	 * @since 1.0.0
	 */
	public function AddCustomPostTypes()
	{
		/** The default arguments for the new custom post types */
		$default_args                  = array(
											'public'             => true,
											'publicly_queryable' => true,
											'show_ui'            => true,
											'show_in_menu'       => true,
											'query_var'          => true,
											'menu_position'      => 5,											
											'supports'           => array( 'title', 'custom-fields')
        );			
        /** The ayat custom post type is added */
        $this->GetComponent("application")->AddNewCustomPostType("Websites","Website",array(),$default_args);		
	}
}