<?php

namespace Framework\Templates\BasicSite\Presentation;

use \Framework\WebApplication\Configuration as Configuration;

/**
 * This class provides functions for rendering Basic Site templates
 * 
 * It extends the TemplateEngine class
 * It contains functions that allow creating html objects based on Basic Site templates
 * It has only one public method called Render which is declared in the abstract parent class
 * 
 * @category   Framework
 * @package    Templates
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class BasicSiteTemplate extends \Framework\WebApplication\TemplateEngine
{
    /**
     * Used to render the required user interface template
     * 
     * It renders the required user interface template using the given parameters
     * 
     * @since 1.0.0
     * @param string $option the user interface element name. e.g textbox or html_table
     * @param array $parameters the parameters used to render the user interface item 	 		 		 		 
     * 
     * @return string $user_interface_html the html string of the user interface item. e.g table html string
     */
    public function Render($option, $parameters)
    {
        $user_interface_html = "";
        if ($option == "textbox")
            $user_interface_html = $this->RenderTextBox($parameters);
        else if ($option == "html_table")
            $user_interface_html = $this->RenderHtmlTable($parameters);
        else if ($option == "css_js_tags")
            $user_interface_html = $this->RenderCssJsFileTags($parameters);
        else if ($option == "datalist_options")
            $user_interface_html = $this->RenderDataListOptions($parameters);
        else if ($option == "root")
            $user_interface_html = $this->RenderApplicationTemplate($option);
        else if ($option == "alert_confirmation")
            $user_interface_html = $this->RenderAlertConfirmation($parameters);
        else if ($option == "selectbox")
            $user_interface_html = $this->RenderSelectbox($parameters);
        else if ($option == "base_page")
            $user_interface_html = $this->RenderBasePage($parameters);
        else if ($option == "link")
            $user_interface_html = $this->RenderLink($parameters);
        else if ($option == "span")
            $user_interface_html = $this->RenderSpan($parameters);
		
        return $user_interface_html;
    }
    
    /**
     * Used to render select box
     * 
     * It displays a selectbox using the given options		 
     * 
     * @since 1.0.0
     * @param array $parameters an array with following keys:
     * name=> name of the selectbox
     * id=> id of the selectbox
     * selected_value=> value of selectbox
     * onchange=> the javascript to run when the user selects an option
     * options=> selectbox options. the selectbox options. each array element contains the option text
     * it can optionally contain an array with 2 keys. text=> the text of the option and value=> the value of the option	 		 		 		 
     * @throws Exception an object of type Exception is thrown if the parameters are not in correct format
     * 
     * @return string $selectbox_html the textbox html string
     */
    private function RenderSelectbox($parameters)
    {
        /** The select box parameters are initialized */
        $selectbox_parameters         = array();
        $selectbox_options_parameters = array();
        /** The value to select in the selectbox */
        $selected_value               = $parameters['selected_value'];
        /** The selectbox parameters are formatted */
        for ($count = 0; $count < count($parameters['options']); $count++) {
            $option = $parameters['options'][$count];
            if ($selected_value == $option['value'])
                $option['selected'] = "SELECTED";
            else
                $option['selected'] = "";
            /** If the selectbox options are given as a string */
            if (is_string($option)) {
                if ($option == $selected_value)
                    $selectbox_options_parameters[] = array(
                        "text" => $option,
                        "value" => $option,
                        "selected" => "SELECTED"
                    );
                else
                    $selectbox_options_parameters[] = array(
                        "text" => $option,
                        "value" => $option,
                        "selected" => ""
                    );
            }
            /** If the selectbox options are given as an array */
            else if (isset($option['text']) && isset($option['value']))
                $selectbox_options_parameters[] = $option;
            /** If the selectbox options are given in any other format then an Exception is thrown */
            else
                throw new \Exception("Invalid parameters given to RenderSelectbox function");
        }
        
        /** The selectbox options are rendered */
        /** The full path to the template file */
        $template_file_path     =  Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."selectbox_option.html";        
        $selectbox_options_html =  \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $selectbox_options_parameters);
                
        $selectbox_parameters   = array(
            array(
                "options" => $selectbox_options_html,
                "name" => $parameters['name'],
                "id" => $parameters['id'],
                "onchange" => $parameters['onchange']
            )
        );
        /** The selectbox html is rendered */
        /** The full path to the template file */
        $template_file_path     = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."selectbox.html";
        $selectbox_html         = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $selectbox_parameters);
        
        return $selectbox_html;
    }
    
    /**
     * Used to render html string inside the base_page.html template
     * 
     * It displays html content in the base_page.html template
     * 
     * @since 1.0.0
     * @param array $parameters the parameters containing the html content. it is an array with following keys:
     * title=> the page title
     * css=> the css file names
     * javascript=> the javascript file names
     * body=> the body html contents		 		 		 		 		 
     * 
     * @return string $page_html the base page html string
     */
    private function RenderBasePage($parameters)
    {
        $title = $parameters['title'];
        if (count($parameters['css']) > 0) {
            $css_file_list = array(
                "file_type" => "css",
                "file_list" => array()
            );
            for ($count = 0; $count < count($parameters['css']); $count++)
                $css_file_list["file_list"][] = $parameters['css'][$count];
            $css_tags = $this->RenderCssJsFileTags($css_file_list);
        } else
            $css_tags = "";
        
        if (count($parameters['javascript']) > 0) {
            $javascript_file_list = array(
                "file_type" => "javascript",
                "file_list" => array()
            );
            for ($count = 0; $count < count($parameters['javascript']); $count++)
                $javascript_file_list["file_list"][] = $parameters['javascript'][$count];
            $javascript_tags = $this->RenderCssJsFileTags($javascript_file_list);
        } else
            $javascript_tags = "";
        
        $body = $parameters['body'];
        
        $base_page_parameters = array(
            array(
                "title" => $title,
                "css_tags" => $css_tags,
                "javascript_tags" => $javascript_tags,
                "body" => $body
            )
        );
		
		/** The full path to the template file */
        $template_file_path     = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."base_page.html";
        $page_html              = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $base_page_parameters);
		
        return $page_html;
    }
    
	/**
     * Used to render a span tag
     * 
     * It renders a span tag using the given parameters		 
     * 
     * @since 1.0.4
     * @param array $parameters the parameters containing the span tag text and css class
     * class=> the css class
     * text=> the inner html of the span tag    	 		 		
     * 
     * @return string $link_html the hyperlink html string
     */
    private function RenderSpan($parameters)
    {
        /** The full path to the template file */
        $template_file_path     = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."span.html";
		/** The span template is rendered using the given span parameters */
        $span_html              = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $parameters);        
        
        return $span_html;
    }
	
    /**
     * Used to render a hyperlink
     * 
     * It returns a hyperlink string using given parameters		 
     * 
     * @since 1.0.0
     * @param array $parameters the parameters containing the hyperlink text and link
     * link=> the hyperlink
     * text=> the link text
     * name=> the link name
     * id=> the link id 		 		 		 
     * 
     * @return string $link_html the hyperlink html string
     */
    private function RenderLink($parameters)
    {
        $link = $parameters['link'];
        $text = $parameters['text'];
        $id   = (isset($parameters['id'])) ? $parameters['id'] : str_replace(" ", "_", strtolower($parameters['text']));
        
        /** The link parameters are set */
        $link_parameters = array(
            array(
                "href" => $link,
                "text" => $text,
                "id" => $id
            )
        );        
        /** The full path to the template file */
        $template_file_path     = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."link.html";
		/** The link template is rendered using the given link parameters */
        $link_html              = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $link_parameters);        
        
        return $link_html;
    }
    
    /**
     * Used to render an alert box
     * 
     * It displays a javascript alert box
     * And then runs custom javascript 
     * 
     * @since 1.0.0
     * @param array $parameters the parameters containing the alert text and custom javascript
     * alert_text=> the text in the alert box
     * optional_javascript=> the custom javascript text		 		 		 		 		 
     * 
     * @return string $textbox_html the textbox html string
     */
    private function RenderAlertConfirmation($parameters)
    {
        $alert_text          = $parameters['alert_text'];
        $optional_javascript = $parameters['optional_javascript'];
        
        /** The alert message parameters are set */
        $alert_confirmation_parameters = array(
            array(
                "alert_text" => $alert_text,
                "optional_javascript" => $optional_javascript
            )
        );
        
        /** The full path to the template file */
        $template_file_path            = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."alert_confirmation.html";
		/** The alert confirmation template is rendered using the given alert text and custom javascript */
        $alert_confirmation_html       = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $alert_confirmation_parameters);        
        
		
        return $alert_confirmation_html;
    }
    
    /**
     * Used to render a html text box
     * 
     * It displays a html text box	 
     * 
     * @since 1.0.0
     * @param array $parameters the parameters used to render the textbox. It is an array with following keys
     * textbox_list=> the textbox list name
     * textbox_value=> the textbox value
     * textbox_name=> the textbox name
     * textbox_css_class=> the css class of the textbox		 		 		 		 
     * 
     * @return string $textbox_html the textbox html string
     */
    private function RenderTextBox($parameters)
    {
        
        $textbox_list      = $parameters['textbox_list'];
        $textbox_value     = $parameters['textbox_value'];
        $textbox_name      = $parameters['textbox_name'];
		$textbox_id        = $parameters['textbox_id'];
        $textbox_css_class = $parameters['textbox_css_class'];
        $textbox_onchange  = $parameters['textbox_onchange'];
		
        /** The textbox parameters are set */
        $textbox_parameters = array(
            array(
                "textbox_list" => $textbox_list,
                "textbox_value" => $textbox_value,
                "textbox_name" => $textbox_name,
                "textbox_id" => $textbox_id,
                "textbox_css_class" => $textbox_css_class,
                "textbox_onchange" => $textbox_onchange
            )
        );

		/** The full path to the textbox template file */
        $template_file_path = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."textbox.html";
		/** The textbox template is rendered using the given textbox parameters */
        $textbox_html       = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $textbox_parameters);        
        
        return $textbox_html;
        
    }
    
    /**
     * Used to get the datalist options string
     * 
     * It renders the datalist_options.html template using the given option text array
     * 
     * @since 1.0.0
     * @param array $option_text list of option text values
     * 
     * @return string $datalist_options_str the datalist options html string
     */
    private function RenderDataListOptions($option_text_arr)
    {
        $datalist_names = array();
        for ($count = 0; $count < count($option_text_arr); $count++) {
            $file_name        = $option_text_arr[$count];
            $option_parameter = array(
                "option_text" => $file_name
            );
            $datalist_names[] = $option_parameter;
        }

		/** The full path to the template file */
        $template_file_path = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."datalist_option.html";
		/** The datalist options template is rendered using the given data list names */
        $datalist_options   = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $datalist_names);        
        		
        return $datalist_options;
        
    }
    
    /**
     * Used to get html string for basicsite table_data template
     * 
     * It builds the html of the table_data template from the given parameters
     * The html can be displayed in any template tag
     * 
     * @since 1.0.0
     * @param array $parameters an array of table parameters. it contains 5 key value pairs
     * table_headers=>an array whoose each element is a text string. each text string is a header element		 
     * header_widths=>header width of each column header
     * table_rows=>an array whoose each each element is an array of column values
     * table_css=>an array with 2 elements. each element is a css class for a table row	
     * css_class=>an array whoose each element is a css class that should be applied to each row column	
	 * cell_attributes_callback=>a callback function that gives the cell attributes for given table row and column
     * @throws Exception an object of type Exception is thrown if the number of elements in header_width array is not equal to number of elements in header_text array
     * 
     * @return string $table_string the html table string containing all the table data		 
     */
    private function RenderHtmlTable($parameters)
    {       
        if (count($parameters['table_headers']) != count($parameters['header_widths']))
            throw new \Exception("Header width array count must match header text array count");
        
        /** The table header parameters are generated. each parameter contains a header_width and header_text */
        for ($count = 0; $count < count($parameters['table_headers']); $count++) {
            /** The header text */
            $header_text                        = $parameters['table_headers'][$count];
            /** The width of a table header column */
            $header_width                       = ($parameters['header_widths'][$count]);
            /** The header column css class */
            $header_column_class                = $parameters['header_column_class'][$count];
            /** The table header params are updated */
            $table_header_params[]              = array(
                "header_extra_css" => $header_width,
                "header_text" => $header_text,
                "header_column_class" => $header_column_class
            );
        }

        /** The full path to the template file */
        $template_file_path                     = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."table_header.html";
		/** The table header template is rendered using the table header parameters */
        $table_header_text                      = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $table_header_params);
        /** The table row html string is generated */
        $table_rows_params = array();
        for ($count1 = 0; $count1 < count($parameters['table_rows']); $count1++) {
            /** The table_row_column.html template parameters are initialized */
            $table_col_params                   = array();
            /** The row css class is set. The css class repeats after each row */
            $row_css_class                      = $parameters['table_css'][$count1 % 2];
            /** The table_column.html template parameters are generated */
            $table_row_col_text_arr             = $parameters['table_rows'][$count1];
            for ($count2 = 0; $count2 < count($table_row_col_text_arr); $count2++) {            	
                $column_text                    = $table_row_col_text_arr[$count2];                
				$cell_attributes_callback       = $parameters['cell_attributes_callback'];
				/** If the cell attributes callback function is callable then it is called */
				if (is_callable($cell_attributes_callback)){
				    $cell_attributes_callback_params  = array($count1, $count2);				    
				    $cell_attributes            = call_user_func_array($cell_attributes_callback, $cell_attributes_callback_params);
					$column_css_class           = $cell_attributes['css_class'];
					$column_span                = $cell_attributes['column_span'];
				}
				else if($cell_attributes_callback!="")
					throw new \Exception("Invalid table columnspan callback");				
				else {
					$column_span                = 1;
					$column_css_class           = "";
				}
				
                $table_col_params[] = array(
                    "column_data" => $column_text,
                    "css_class" => $column_css_class,
                    "column_span" => $column_span
                );
            }
			
			/** The full path to the template file */
            $template_file_path                = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."table_column.html";
		    /** The table_column.html template string is generated */
            $table_col_text                    = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $table_col_params);        
                   
            /** The generated table column html string is added to table_row.html template parameters */
            $table_rows_params[] = array(
                "css_class" => $row_css_class,
                "table_column" => $table_col_text
            );
        }

		/** The full path to the template file */
        $template_file_path                   = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."table_row.html";
		/** The table_row_column.html template string is generated */
        $table_rows_text                      = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $table_rows_params);        
 
        /** The table_data.html template parameters are generated */
        $table_data_params = array(
            array(
                "table_headers" => $table_header_text,
                "table_rows" => $table_rows_text
            )
        );
        
		/** The full path to the template file */
        $template_file_path                  = Configuration::GetConfig("path","template_path").DIRECTORY_SEPARATOR."table_data.html";
		/** The table_row_column.html template string is generated */
        $table_rows_str                      = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_file_path, $table_data_params);        

        return $table_rows_str;
        
    }
    
    /**
     * Used to get css and javascript tag values
     * 
     * It returns css and javascript file tags for the given css and javascript files	 
     * 
     * @since 1.0.0
     * @param array $parameters the parameters used to render the css and javascript tags. It is an array with following keys:
     * file_type=> the type of file to render. i.e css or javascript
     * file_list=> an array where each element is an absolute path of a css file			
     * 
     * @return string $script_tags_html html string containing the css or javascript tags
     */
    private function RenderCssJsFileTags($parameters)
    {
        /** The path to the application template folder is fetched */
        $template_folder_path = Configuration::GetConfig("path","template_path");
        $file_list            = $parameters['file_list'];
        
        $tag_arr = array();
        for ($count = 0; $count < count($file_list); $count++) {
            $file_name              = $file_list[$count];
            $tag_arr[$count]["url"] = $file_name;
        }
        
		/** If the file type is css then the css template file is rendered */
        if ($parameters['file_type'] == 'css')
            $script_tags_html = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_folder_path . DIRECTORY_SEPARATOR . "css_tags.html", $tag_arr);
		/** If the file type is javascript then the javascript template file is rendered */
        else if ($parameters['file_type'] == 'javascript')
            $script_tags_html = \Framework\Utilities\UtilitiesFramework::Factory("template")->RenderTemplateFile($template_folder_path . DIRECTORY_SEPARATOR . "javascript_tags.html", $tag_arr);
        else
            throw new \Exception("Invalid file type given to RenderCssJsFileTags");
        
        return $script_tags_html;
        
    }
}