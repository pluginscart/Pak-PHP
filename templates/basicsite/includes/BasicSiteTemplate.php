<?php

namespace BasicSite;
use \TemplatesFramework\BrowserApplicationTemplate as BrowserApplicationTemplate;
/**
 * This class provides widgets for the Basic Site template
 * 
 * It extends the BrowserApplicationTemplate class
 * It contains functions that allow creating widgets based on Basic Site templates
 * It has only one public method called Render which is declared in the parent class
 * 
 * @category   TemplateWidget
 * @package    BasicSite
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class BasicSiteTemplate extends BrowserApplicationTemplate
	{
/**********************************************************************************************************/
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
		public function Render($option,$parameters)
			{
				try
					{
						$user_interface_html="";
						if($option=="textbox")$user_interface_html=$this->RenderTextBox($parameters);
						else if($option=="html_table")$user_interface_html=$this->RenderHtmlTable($parameters);
						else if($option=="css_js_tags")$user_interface_html=$this->RenderCssJsFileTags($parameters);
						else if($option=="datalist_options")$user_interface_html=$this->RenderDataListOptions($parameters);
						else if($option=="root")$user_interface_html=$this->RenderApplicationTemplate($option);
						else if($option=="alert_confirmation")$user_interface_html=$this->RenderAlertConfirmation($parameters);						
						else if($option=="selectbox")$user_interface_html=$this->RenderSelectbox($parameters);
						else if($option=="base_page")$user_interface_html=$this->RenderBasePage($parameters);
						else if($option=="link")$user_interface_html=$this->RenderLink($parameters);
						
						return $user_interface_html;    
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function Render. Details: ".$e->getMessage(),130,$e);
					}
			}	
/**********************************************************************************************************/			
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
				try
					{												
						/** The select box parameters are initialized **/
						$selectbox_parameters=array();			
						$selectbox_options_parameters=array();
						/** The value to select in the selectbox **/
						$selected_value=$parameters['selected_value'];									
						
						/** The selectbox parameters are formatted **/
						for($count=0;$count<count($parameters['options']);$count++)
							{
								$option=$parameters['options'][$count];
								if($selected_value==$option['value'])$option['selected']="SELECTED";								
								else $option['selected']="";
								/** If the selectbox options are given as a string **/
								if(is_string($option))
									{
										if($option==$selected_value)$selectbox_options_parameters[]=array("text"=>$option,"value"=>$option,"selected"=>"SELECTED");
										else $selectbox_options_parameters[]=array("text"=>$option,"value"=>$option,"selected"=>"");
									}
								/** If the selectbox options are given as an array **/
								else if(isset($option['text'])&&isset($option['value']))$selectbox_options_parameters[]=$option;
								/** If the selectbox options are given in any other format then an Exception is thrown **/
								else throw new \Exception("Invalid parameters given to RenderSelectbox function",130);					
							}						
							
						/** The selectbox options are rendered **/							
						$selectbox_options_html=$this->RenderTemplateFile("selectbox_option.html","basicsite",$selectbox_options_parameters);						
						$selectbox_parameters=array(array("options"=>$selectbox_options_html,"name"=>$parameters['name'],"id"=>$parameters['id'],"onchange"=>$parameters['onchange']));
						/** The selectbox html is rendered **/							
						$selectbox_html=$this->RenderTemplateFile("selectbox.html","basicsite",$selectbox_parameters);
						
						return $selectbox_html;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function RenderSelectbox. Details: ".$e->getMessage(),130,$e);
					}
			}		
/**********************************************************************************************************/
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
				try
					{
						$title=$parameters['title'];
						if(count($parameters['css'])>0)
							{
								$css_file_list=array("file_type"=>"css","file_list"=>array());
								for($count=0;$count<count($parameters['css']);$count++)
								$css_file_list["file_list"][]=$parameters['css'][$count];
								$css_tags=$this->RenderCssJsFileTags($css_file_list);
							}
						else $css_tags="";
						
						if(count($parameters['javascript'])>0)
							{
								$javascript_file_list=array("file_type"=>"javascript","file_list"=>array());
								for($count=0;$count<count($parameters['javascript']);$count++)
								$javascript_file_list["file_list"][]=$parameters['javascript'][$count];
								$javascript_tags=$this->RenderCssJsFileTags($javascript_file_list);
							}
						else $javascript_tags="";
						
						$body=$parameters['body'];
						
						$base_page_parameters=array(array(
						"title"=>$title,
						"css_tags"=>$css_tags,
						"javascript_tags"=>$javascript_tags,
						"body"=>$body));
						
						$page_html=$this->RenderTemplateFile("base_page.html","basicsite",$base_page_parameters);
						
						return $page_html;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function RenderBasePage. Details: ".$e->getMessage(),130,$e);
					}
			}
/**********************************************************************************************************/
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
				try
					{
						$link=$parameters['link'];
						$text=$parameters['text'];
						$id=(isset($parameters['id']))?$parameters['id']:str_replace(" ","_",strtolower($parameters['text']));
						
						/** The link parameters are set **/						
						$link_parameters=array(array("href"=>$link,"text"=>$text,"id"=>$id));
						/** The link template is rendered using the given link parameters **/							
						$link_html=$this->RenderTemplateFile("link.html","basicsite",$link_parameters);
								
						return $link_html;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function RenderLink. Details: ".$e->getMessage(),130,$e);
					}
			}	
/**********************************************************************************************************/
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
				try
					{
						$alert_text=$parameters['alert_text'];
						$optional_javascript=$parameters['optional_javascript'];

						/** The alert message parameters are set **/						
						$alert_confirmation_parameters=array(array("alert_text"=>$alert_text,"optional_javascript"=>$optional_javascript));
						/** The alert confirmation template is rendered using the given alert text and custom javascript **/							
						$alert_confirmation_html=$this->RenderTemplateFile("alert_confirmation.html","basicsite",$alert_confirmation_parameters);
								
						return $alert_confirmation_html;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function RenderAlertConfirmation. Details: ".$e->getMessage(),130,$e);
					}
			}				
/**********************************************************************************************************/
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
				try
					{
						$textbox_list=$parameters['textbox_list'];
						$textbox_value=$parameters['textbox_value'];
						$textbox_name=$parameters['textbox_name'];
						$textbox_css_class=$parameters['textbox_css_class'];
						
						/** The textbox parameters are set **/						
						$textbox_parameters=array(array("textbox_list"=>$textbox_list,"textbox_value"=>$textbox_value,"textbox_name"=>$textbox_name,"textbox_css_class"=>$textbox_css_class));
						/** The textbox html template is rendered using the given textbox list and textbox value **/							
						$textbox_html=$this->RenderTemplateFile("textbox.html","basicsite",$textbox_parameters);
								
						return $textbox_html;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function RenderTextBox. Details: ".$e->getMessage(),130,$e);
					}
			}				
/**********************************************************************************************************/
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
				try
					{															
						$datalist_names=array();
						for($count=0;$count<count($option_text_arr);$count++)
							{
								$file_name=$option_text_arr[$count];
								$option_parameter=array("option_text"=>$file_name);
								$datalist_names[]=$option_parameter;
							}
						$datalist_options=$this->RenderTemplateFile("datalist_option.html","basicsite",$datalist_names);
						return $datalist_options;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function GetDataListOptions. Details: ".$e->getMessage(),130,$e);
					}
			}			
/**********************************************************************************************************/
		/**
		 * Used to get html string for basicsite table_data template
		 * 
		 * It builds the html of the table_data template from the given parameters
		 * The html can be displayed in any template tag
		 * 
		 * @since 1.0.0
		 * @param array $parameters an array of table parameters. it contains 4 key value pairs
		 * table_headers=>an array whoose each element is a text string. each text string is a header element		 
		 * header_widths=>header width of each column header
		 * table_rows=>an array whoose each each element is an array of column values
		 * table_css=>an array with 2 elements. each element is a css class for a table row		
		 * @throws Exception an object of type Exception is thrown if the number of elements in header_width array is not equal to number of elements in header_text array
		 * 
		 * @return string $table_string the html table string containing all the table data		 
		 */	
		private function RenderHtmlTable($parameters)
			{
				try
					{
						if(count($parameters['table_headers'])!=count($parameters['header_widths']))throw new \Exception("Header width array count must match header text array count",130);
		
						/** The table header parameters are generated. each parameter contains a header_width and header_text **/						
						for($count=0;$count<count($parameters['table_headers']);$count++)
							{
								/** The header text **/
								$header_text=$parameters['table_headers'][$count];
								/** The width of a table header column **/
								$header_width=($parameters['header_widths'][$count]);	
								/** The table header params are updated **/					
								$table_header_params[]=array("header_extra_css"=>$header_width,"header_text"=>$header_text);
							}
						/** The table header html string is generated **/								
						$table_header_str=$this->RenderTemplateFile("table_header.html","basicsite",$table_header_params);				
										
						/** The table row html string is generated **/
						$table_rows_params=array();
						for($count1=0;$count1<count($parameters['table_rows']);$count1++)
							{
								/** The table_row_column.html template parameters are initialized **/
								$table_col_params=array();
								/** The table css string is set. The css class repeats after each row **/
								$table_css_string=$parameters['table_css'][$count1%2];
								/** The table_row_column.html template parameters are generated **/
								$table_row_col_text_arr=$parameters['table_rows'][$count1];
								for($count2=0;$count2<count($table_row_col_text_arr);$count2++)
									{
										$table_col_str=$table_row_col_text_arr[$count2];
										$table_col_params[]=array("column_data"=>$table_col_str);
									}
								/** The table_row_column.html template string is generated **/
								$table_col_html_str=$this->RenderTemplateFile("table_row_column.html","basicsite",$table_col_params);
								/** The generated table column html string is added to table_rows.html template parameters **/
								$table_rows_params[]=array("css_class"=>$table_css_string,"table_row_column"=>$table_col_html_str);
							}
						/** The table_rows.html template string is generated **/								
						$table_rows_str=$this->RenderTemplateFile("table_rows.html","basicsite",$table_rows_params);
			
						/** The table_data.html template parameters are generated **/
						$table_data_params=array(array("table_headers"=>$table_header_str,"table_rows"=>$table_rows_str));
								
						/** The table_data.html template string is generated **/
						$table_rows_str=$this->RenderTemplateFile("table_data.html","basicsite",$table_data_params);
						
						return $table_rows_str;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function RenderHtmlTable. Details: ".$e->getMessage(),130,$e);
					}
			}					
/**********************************************************************************************************/
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
				try
					{
						$file_list=$parameters['file_list'];

						$tag_arr=array();						
						for($count=0;$count<count($file_list);$count++)
							{
								$file_name=$file_list[$count];
								$tag_arr[$count]["url"]=$file_name;
							}
							
						if($parameters['file_type']=='css')$script_tags_html=$this->RenderTemplateFile("css_tags.html","basicsite",$tag_arr);
						else if($parameters['file_type']=='javascript')$script_tags_html=$this->RenderTemplateFile("javascript_tags.html","basicsite",$tag_arr);							
						else throw new \Exception("Invalid file type given to RenderCssJsFileTags",130);
						
						return $script_tags_html;
					}
				catch(\Exception $e)
					{
						throw new \Exception("An exception occured in function RenderCssJsFileTags. Details: ".$e->getMessage(),130,$e);
					}
			}						
/**********************************************************************************************************/
	}
?>
 