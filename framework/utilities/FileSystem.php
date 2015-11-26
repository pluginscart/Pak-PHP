<?php

namespace Framework\Utilities;

/**
 * Singleton class
 * FileSystem class provides functions related to working with file system
 * 
 * It includes functions such as reading files, writting files fetch remote file contents
 * 
 * @category   FileSystem
 * @package    UtilitiesFramework
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.1
 * @link       N.A
 * @author 	   Nadir Latif <nadir@pakiddat.com>
 */
final class FileSystem
{
    /**
     * Location of upload folder
     */
    private $upload_folder;
    /**
     * Maximum size of file that can be uploaded
     */
    private $max_allowed_file_size;
    /**
     * Contains extensions for each file type that is permitted for uploading
     */
    private $allowed_extensions;
    /**
     * The single static instance
     */
    protected static $instance;
    
    /**
     * Used to return a single instance of the class
     * 
     * Checks if instance already exists
     * If it does not exist then it is created
     * The instance is returned
     * 
     * @since 1.0.0
     * 
     * @return FileSystem static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance($parameters)
    {
        
        if (static::$instance == null) {
            static::$instance = new static($parameters);
        }
        return static::$instance;
        
    }
    
    /**
     * Class constructor.
     *
     * @since 1.0.0	
     * @param array $parameters an array with following keys:    
     * upload_folder=> the location of the upload_folder
     * allowed_extensions=> the file types that are allowed to be uploaded
     * max_allowed_file_size=> the maximum allowed size for uploaded files
     * 
     * @return int returns the number of elements.
     */
    public function __construct($parameters)
    {
        $this->upload_folder           = isset($parameters['upload_folder']) ? $parameters['upload_folder'] : '';
        $this->allowed_extensions      = isset($parameters['allowed_extensions']) ? $parameters['allowed_extensions'] : '';
        $this->max_allowed_file_size   = isset($parameters['max_allowed_file_size']) ? $parameters['max_allowed_file_size'] : '';        
    }
    
    /**
     * Reads the contents of the given folder
     *
     * @since 1.0.0
     * @param string $folder_path absolute path to the folder whoose contents are to be read     
     * 		  
     * @return array $file_list list of files in folder. the . and .. items are removed from the list
     */
    public function GetFolderContents($folder_path)
    {
        
        /** If the folder is not readable then an exception is thrown */
        if (!is_dir($folder_path))
            throw new \Exception("Error in reading folder: " . $folder_path);
        /** The list of files in folder is fetched */
        $file_list = scandir($folder_path);
        /** The . and .. entries are removed from list */
        $file_list = array_slice($file_list, 2);
        /** The file list is returned */
        return $file_list;
        
    }
    
    /**
     * Deletes the given file from local disk
     *
     * @since 1.0.0
     * @param string $file_path the absolute path to the file
     * @throws Exception throws an exception if the file could not be deleted				 			
     */
    public function DeleteLocalFile($file_path)
    {
        
        if (!unlink($file_path))
            throw new \Exception("File could not be deleted");
        
    }
    
    /**
     * Writes the given text to a file on local disk
     *
     * @since 1.0.0
     * @param string $file_text the text that needs to be written to local file
     * @param string $file_path the absolute path to the file
     * @throws Exception throws an exception if the file could not be written	
     * 		  
     * @return string returns the contents of the file
     */
    public function WriteLocalFile($file_text, $file_path)
    {
        $fh = fopen($file_path, "w");
        if (!fwrite($fh, $file_text))
            throw new \Exception("Text could not be written to file");
        else
            fclose($fh);
    }
    
    /**
     * Reads the contents of a file on disk
     *
     * @since 1.0.0
     * @param string $file_path absolute path to the file to be read     
     * 		  
     * @return string returns the contents of the file
     */
    public function ReadLocalFile($file_path)
    {
        $fh       = fopen($file_path, "r");
        $contents = fread($fh, filesize($file_path));
        fclose($fh);
        return $contents;
    }
    
    /**
     * Copies the source file to the target file
     * 
     * It overwrites the destination file
     *
     * @since 1.0.0
     * @param string $source_file_name the source file to copy
     * @param string $target_file_name the target file name        
     */
    public function CopyFile($source_file_name, $target_file_name)
    {
        if (!copy($source_file_name, $target_file_name))
            throw new \Exception("Source file: " . $source_file_name . " could not be copied to target file: " . $target_file_name);
    }
    
    /**
     * Copies as uploaded file to a given location. the location is set in the private class variable
     *
     * @since 1.0.0			 
     * @param array $file_data data for uploaded file.			 		
     * @throws Exception throws an exception if the file size is greater than a limit
     *   or the file extension is not valid or the uploaded file could not be copied.
     *   The upload limit and valid file extensions are specifed in private class variables
     *
     * @return string $path_of_uploaded_file full path to the uploaded file.		
     */
    public function UploadFile($file_data)
    {
        if (!isset($file_data["name"]))
            throw new \Exception("No file to upload");
        
        $max_allowed_file_size = $this->max_allowed_file_size;
        $allowed_extensions    = $this->allowed_extensions;
        $size_of_uploaded_file = ceil($file_data['size'] / 1024);
        $file_name             = $file_data["name"];
        $type_of_uploaded_file = substr($file_name, strrpos($file_name, ".") + 1);
        
        if ($size_of_uploaded_file > $max_allowed_file_size)
            throw new \Exception("Size of file should be less than " . $max_allowed_file_size . " Kb");
         
        //------ Validate the file extension -----
        $allowed_ext = false;
        for ($i = 0; $i < sizeof($allowed_extensions); $i++) {
            if (strcasecmp($allowed_extensions[$i], $type_of_uploaded_file) == 0) {
                $allowed_ext = true;
            }
        }
     
        if (!$allowed_ext)
            throw new \Exception("The uploaded file is not a supported file type. Only the following file types are supported: " . implode(',', $allowed_extensions));
        
        //copy the temp. uploaded file to uploads folder
        $path_of_uploaded_file = $this->upload_folder . DIRECTORY_SEPARATOR . $file_name;
        $tmp_path              = $file_data["tmp_name"];
        
        /** 
         * If the script is running from browser then the file will be checked is_uploaded_file function
         * This checks if file was uploaded by http post
         * Otherwise script will use is_file function
         * 
         */
        if (isset($_SERVER['HTTP_HOST']) && is_uploaded_file($tmp_path) || (!isset($_SERVER['HTTP_HOST']) && is_file($tmp_path))) {
            if (!copy($tmp_path, $path_of_uploaded_file)) {
                throw new \Exception("Error while copying the uploaded file");
            }
        }
        
        return $path_of_uploaded_file;
    }
    
    /**
     * Used to get the contents of a url.
     *
     * @since 1.0.0
     * @param string $url url to be fetched
     * @param string $method optional http method. defaults to "get". http method for the request
     * @param array $parameters optional parameters. the data to be sent to the remote server
     * @param array $request_headers the http headers to include in the url request
     * 			  
     * @return string $file_contents. the contents of the file
     */
    function GetFileContent($url, $method = "GET", $parameters = "", $request_headers = "")
    {
        if ($method == "GET")
            $file_contents = file_get_contents($url);
		else {
		    $count = 0;
		    $ch = curl_init();
		            
		    if ($parameters != "") {
		        curl_setopt($ch, CURLOPT_HEADER, 0);
		        if (is_array($request_headers))
		            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
		        curl_setopt($ch, CURLOPT_POST, true);
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
		    }
		            
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/3.0.0.0");
		    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		            
		    ob_start();
		            
		    curl_exec($ch);
		    curl_close($ch);
		    $file_contents = ob_get_clean();
		}        
		
		return $file_contents;
    }
	
	/**
     * Used to determine if the given url is valid
     *
	 * It checks the http response headers for the given url
	 * If the response headers contain an error code, i.e 4xx, then function returns false
	 * Otherwise the function returns true
	 * See http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	 * 
     * @since 1.0.1
     * @param string $url url to be checked
     * 			  
     * @return boolean $is_valid indicates if the given url is valid or not
     */
    function IsUrlValid($url)
    {
    	/** Used to indicate if the url is valid or not */
    	$is_valid = false;
        /** The http headers for the url are fetched */
        $url_headers      = get_headers($url);
		/** Each header is checked for http error code */
		for ($count = 0; $count < count($url_headers); $count++) {
			/** The http header */
			$http_header  = ($url_headers[$count]);
			/** The http header is checked for 4xx code */
			preg_match("/(http\/1\.[0,1] 4\d\d\s+[a-z]+)/i", $http_header,$matches);
			/** Indicates if the url contains error code or not */
			$is_valid     = (isset($matches[0]) && isset($matches[1]))?false: true;
			/** If the http header contains an error code then no need to check other http headers */
			if (!$is_valid) break;
		}
		
		return $is_valid;
    }
}