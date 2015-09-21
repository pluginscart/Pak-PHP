<?php

namespace Framework\Utilities;

/**
 * Singleton class
 * Email class provides functions related to email
 * 
 * It includes functions such as sending email with attachment
 * It uses pear Mail_Mime package (https://pear.php.net/package/Mail_Mime/)
 * And Mail package (https://pear.php.net/package/Mail/) 
 * 
 * @category   Framework
 * @package    Utilities
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 * @author 	   Nadir Latif <nadir@pakiddat.com>
 */
class Email
{
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
     * @return Utilities static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance($parameters)
    {
        try {
            if (static::$instance == null) {
                static::$instance = new static($parameters);
            }
            return static::$instance;
        }
        catch (\Exception $e) {
            throw new \Exception("Error in function GetInstance. Details: " . $e->getMessage(), 60, $e);
        }
    }
	
    /**
     * Sends an email
     *
     * @since 1.0.0	
     * @param array $attachment_files an array containing files to be attached with email.
     * @param string $from the sender of the email.
     * @param string $to the reciever of the email.
     * @param string $subject the subject of the email.
     * @param string $message the message of the email.			 			  			  					 
     * @throws Exception throws an exception if the file size is greater than a limit or the file extension is not valid or the uploaded file could not be copied
     * 
     * @return boolean $is_sent used to indicate if the email was sent.
     */
    public function SendEmail($attachment_files, $from, $to, $subject, $text)
    {
        try {
            $processed = htmlentities($text);
            if ($processed == $text)
                $is_html = false;
            else
                $is_html = true;
            
            $message = new \Mail_mime();
            if (!$is_html)
                $message->setTXTBody($text);
            else
                $message->setHTMLBody($text);
            for ($count = 0; $count < count($attachment_files); $count++) {
                $path_of_uploaded_file = $attachment_files[$count];
                if ($path_of_uploaded_file != "")
                    $message->addAttachment($path_of_uploaded_file);
            }
            
            $body = $message->get();
            
            $extraheaders = array(
                "From" => $from,
                "Subject" => $subject,
                "Reply-To" => $from
            );
            $headers      = $message->headers($extraheaders);
            
            $mail    = new \Mail("mail");
            $is_sent = $mail->send($to, $headers, $body);
            
            if (!$is_sent)
                throw new \Exception("Email could not be sent. Details: " . $e->getMessage(), 110);
            else
                return true;
        }
        catch (\Exception $e) {
            throw new \Exception("Email could not be sent. Details: " . $e->getMessage(), 110, $e);
        }
    }
}