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
final class Testing extends \Framework\Application\WordPress\Application
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
		$methods[$plugin_prefix.'.AddData']                               = array($this,"AddData");
		$methods[$plugin_prefix.'.AddMetaData']                           = array($this,"AddMetaData");
		
		return $methods;
	}
	
	/**
	 * Used to add ayas data to WordPress custom posts
	 *
	 * It adds ayas to following ayas custom post type
	 * 
	 * @since 2.0.0
	 * @param array $args the user login information. it is an array with following keys:
	 * blog id: the blog id
	 * username: the user name
	 * password: the password
	 */
	public function AddData($args){
		global $wp_xmlrpc_server;
		/** The rpc arguments are escaped */
        $wp_xmlrpc_server->escape($args);
        /** The blog id, user name and password */
        $blog_id           = $args[0];
        $username          = $args[1];
        $password          = $args[2];
		$start_ayat        = $args[3];
		$total_ayat_count  = $args[4];
		$translator        = stripslashes($args[5]);
		$language          = $args[6];
        /** If the login info is not correct then an error is returned */
        if (! $user = $wp_xmlrpc_server->login($username,$password))
            return $wp_xmlrpc_server->error;

		try {
		    /** The suras and authors data is added to WordPress */
		    $this->GetComponent("holyqurandataimport")->AddData($start_ayat,$total_ayat_count,$translator,$language);
		}
		catch(\Exception $e) {
			die($e->getMessage());
		}
        return array("result"=>"success");
	}
	
	/**
	 * Used to add Holy Quran meta data to WordPress
	 *
	 * It adds Holy Quran meta data to the sura and author custom post types
	 * 
	 * @since 2.0.0
	 * @param array $args the user login information. it is an array with following keys:
	 * blog id: the blog id
	 * username: the user name
	 * password: the password
	 */
	public function AddMetaData($args){
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
		    /** The suras and authors data is added to WordPress */
		    $this->GetComponent("holyqurandataimport")->AddMetaData();
		}
		catch(\Exception $e) {
			die($e->getMessage());
		}
        return array("result"=>"success");
	}
}