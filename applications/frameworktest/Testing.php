<?php

namespace FrameworkTest;

use \TestingFramework\BrowserApplicationTesting;
use \ApplicationConfigurationFramework\ApplicationConfiguration as ApplicationConfiguration;

/**
 * This class implements the unit tests for the FrameworkTest application
 * It extends the BrowserApplicationTest class
 * 
 * It is used to test the FrameworkTest application
 * It tests the framework utility libraries
 * 
 * @category   BrowserApplication
 * @package    FrameworkTest
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
class Testing extends BrowserApplicationTesting
{
    /** 
     * Authentication function test
     * Used to test http digest authentication		 
     */
    public function TestAuthentication()
    {
        /** List of valid user credentials. used to test the http digest authentication **/
        $credentials              = array(
            array(
                "user_name" => "admin",
                "password" => "admin"
            ),
            array(
                "user_name" => "manager",
                "password" => "manager"
            )
        );
        /** The custom text to use in the authentication box that shows in the browser **/
        $authentication_box_title = "Protected Area!";
        /** The authentication object is fetched **/
        $authentication           = \UtilitiesFramework\UtilitiesFramework::Factory("authentication");
        /** 
         * If the user presses the cancel button then the function returns false
         * If the user entered the wrong credentials then he will be asked to login again
         */
        $cancel_pressed=(!$authentication->AuthenticateUser($credentials, $authentication_box_title));            
        $this->AssertTrue(true);
    }
    
    /** 
     * Caching function test
     * Used to test function caching
     */
    public function TestCaching()
    {
        /** The database object is fetched. enter your database credentials **/
        $database_parameters = array(
            "host" => "localhost",
            "user" => "nadir",
            "password" => "kcbW5eFSCbPXbJGLHvUGG8T8",
            "database" => "dev_pakphp",
            "debug" => "1"
        );
			
        $database_obj        = \UtilitiesFramework\UtilitiesFramework::Factory("database", $database_parameters);
        $db_link             = $database_obj->df_get_id();
        /** The prefix of the table. e.g if prefix name is example_ then table name is example_cached_data **/
        $table_prefix        = "example_";
        /** The caching object is fetched with given parameters **/
        $caching_parameters  = array(
            "db_link" => $database_obj->df_get_id(),
            "table_prefix" => $table_prefix
        );
        $caching_obj         = \UtilitiesFramework\UtilitiesFramework::Factory("caching", $caching_parameters);
        /** The data is saved to cache **/
        $caching_obj->SaveDataToCache("TestFunction", array(
            "parameter 1",
            "parameter 2"
        ), "test data");
        /** The data is fetched from cache **/
        $cached_data = $caching_obj->GetCachedData("TestFunction", array(
            "parameter 1",
            "parameter 2"
        ));
		
        $this->AssertEqual($cached_data,"test data");
    }
    
    /**
     * Encryption testing
     * Used to test encryption and decryption of text
     */
    public function TestEncryption()
    {
        /** The encryption object is fetched **/
        $encryption_obj = \UtilitiesFramework\UtilitiesFramework::Factory("encryption");
        /** The text to be encrypted **/
        $original_text  = "test encryption";
        /** The original text is encrypted **/
        $encrypted_text = $encryption_obj->EncryptText($original_text);
        /** The encrypted text is decrypted **/
        $decrypted_text = $encryption_obj->DecryptText($encrypted_text);
        /** If the original text matches the decrypted text then following message is shown **/
        $this->AssertEqual($original_text,$decrypted_text);            
    }
    
    /**
     * Database testing
     * Used to test database abstraction class
     */
    public function TestDatabase()
    {
        /** The database object is fetched **/
        $database_parameters = array(
            "host" => "localhost",
            "user" => "nadir",
            "password" => "kcbW5eFSCbPXbJGLHvUGG8T8",
            "database" => "dev_pakphp",
            "debug" => "1"
        );
        $database_obj        = \UtilitiesFramework\UtilitiesFramework::Factory("database", $database_parameters);
        /** The $database_obj is initialized and cleared **/
        $database_obj->df_initialize();
        /** The select query is built **/
        $main_query               = array();
        $main_query[0]['field']   = "*";
        /** The where clause of the query is built **/
        $where_clause             = array();
        $where_clause[0]['field'] = "function_name";
        $where_clause[0]['value'] = "TestFunction";
        $where_clause[0]['table'] = "example_cached_data";
        
        $query   = $database_obj->df_build_query($main_query, $where_clause, 's');
        $db_rows = $database_obj->df_all_rows($query);
		
        $this->AssertEqual($db_rows[0]['function_name'],"TestFunction");     
    }
    
    /**
     * Excel file testing
     * Used to test excel utility object functions
     */
    public function TestExcel()
    {
        /** The PhpExcel library is included **/
        $excel_file_path = DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "www" . DIRECTORY_SEPARATOR . "html" . DIRECTORY_SEPARATOR . "dev_pakphp" . DIRECTORY_SEPARATOR . "vendors" . DIRECTORY_SEPARATOR . 'phpexcel' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'IOFactory.php';
        include_once($excel_file_path);
        /** The Excel class object is fetched **/
        $excel_obj = \UtilitiesFramework\UtilitiesFramework::Factory("excel");
        $tmp_folder_path=ApplicationConfiguration::GetConfig("tmp_folder_path");
        $data_arr  = $excel_obj->ReadExcelFile($tmp_folder_path.DIRECTORY_SEPARATOR."TOP 30 KW 31.xls", "A2", "C13");
        $this->AssertEqual($data_arr[6]['A'],"Platz");
    }
    
    /**
     * Email testing
     * Used to test email function
     * It support html content and attachments
     */
    public function TestEmail()
    {
        /** 
         * The Email class object is fetched 
         * The Email class requires Mail and Mail_Mime pear package
         * Change the from and to emails to your email address
         */
        include_once("Mail.php");
        include_once("Mail/mime.php");
        
        $from_email = "nadir@pakjiddat.com";
        $to_email   = "nadir@pakjiddat.com";
        
        $email_obj = \UtilitiesFramework\UtilitiesFramework::Factory("email");
        $is_sent   = $email_obj->SendEmail(array(
            "test.xls"
        ), $from_email, $to_email, "Utilitiesframework Test", "<h3>testing html content</h3>");
		
        $this->AssertTrue($is_sent);
    }
    
    /**
     * String function testing
     * Used to test relative to absolute conversion function
     */
    public function TestString()
    {
        /** The String class object is fetched **/
        $string_obj = \UtilitiesFramework\UtilitiesFramework::Factory("string");
        $main_url   = "https://pear.php.net/manual/en/";
        $rel_url    = "package.mail.mail.send.php";
        /** The relative link is converted to absolute link **/
        $abs_url    = $string_obj->ConvertRelUrlToAbsUrl($main_url, $rel_url);
         /** The validation results are displayed **/
        $this->AssertEqual($abs_url,$main_url.$rel_url);
    }
}