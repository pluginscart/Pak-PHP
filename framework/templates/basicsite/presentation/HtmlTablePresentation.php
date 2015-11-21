<?php

namespace Framework\Templates\BasicSite\Presentation;

use Framework\Configuration\Base as Base;

/**
 * Abstract class that defines a presentation class for html tables
 * 
 * Contains functions that are used to render html tables
 * 
 * @category   Framework
 * @package    Templates
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
abstract class HtmlTablePresentation extends Base
{	
	/**
     * Used to format the data so its suitable for displaying in html table
     * 
     * It extracts the data and formats it so it can be displayed in a html table
     * 
     * @since 1.0.0
     * @param array $table_data an array of objects containing table data
     * 
     * @return array $table_parameters the formatted data. it is an array with 3 keys:
     * header_widths=> the width of the table headers
     * table_headers=> the list of table headers
     * table_rows=> the table row data
     */
    final public function GetTableParameters($table_data)
    {    	
    	/** The alignment and width information of the table */        
        $table_width_alignment       = $this->GetHeaderWidthAlignment();		
		/** Used to get the table sort information */		 
		$sort_information            = $this->GetTableSortInformation();
		/** The table column header links */
		$table_links                 = $this->GetHeaderLinks($sort_information);
        /** The table headers */
        $table_headers               = $this->GetTableHeaders($table_links);
		/** Used to get html table data */
        $table_rows                  = $this->GetRowParameters($table_data);
		/** Used to get html table css class */
        $table_css_class             = $this->GetTableCssClass($table_data);
		/** The table row css values. The css class alternates between the rows **/
        $table_row_css        = array(
            "CSSlistDARK",
            "CSSlistLIGHT"
        );
        /** The table parameters are returned **/
        $table_parameters = array(
            "header_widths" => $table_width_alignment['header_widths'],
            "header_column_class" => $table_width_alignment['column_css_class'],
            "table_headers" => $table_headers,
            "table_rows" => $table_rows,
            "table_row_css" => $table_row_css,
            "table_css_class" => $table_css_class
        );
        
        return $table_parameters;        
    }

    /**
     * Used to get the movie data
     * 
     * It returns an array containing the table data
	 * Each array element is an array that represents a table row
     * Each element in this array is a column for a given row
	 * 
     * @since 1.0.0
	 * @param array $table_data an array containing table data
	 * 
     * @return array $table_data each element in the array contains the data for a row
     */
	protected function GetRowParameters($table_data){return $table_data;}	
	/**
     * Used to get the table sort state
     * 
     * It returns an array containing the table sort state
	 * 
     * @since 1.0.0
     * 
     * @return array $table_data an array the table sort information
     */
	protected function GetTableSortInformation(){return array();}
	/**
     * Used to get the movie table column links
     * 
     * It returns an array containing the header links
	 * The links allow data to be sorted by these columns
     * 
	 * 
     * @since 1.0.0
     * @param array $sort_information an array containing sort information for the column
	 * 
     * @return array $column_links an array containing column link information
     */
	protected function GetHeaderLinks($sort_information){return array();}
	/**
     * Used to get the table header
     * 
     * It returns an array containing the header text for each table header    	
	 * 
     * @since 1.0.0
     * @param array $table_links the table header links
	 * 
     * @return array $table_header each element in the array contains the text for a header column
     */
	protected function GetTableHeaders($header_links){return array();}
	/**
     * Used to get the table header widths and column alignments
     * 
     * It gets the header widths and header alignments
	 * So they can be used to display html table
	 * 
     * @since 1.0.0
     * 
     * @return array $table_data an array with two keys:
	 * header_widths => the width of each table column
	 * column alignment => the alignment of each table column 
     */
	protected function GetHeaderWidthAlignment(){return array();}
	/**
     * Used to get the table css class
     * 
     * It returns the table css class
	 * 
     * @since 1.0.0
	 * 
     * @return string $table_css_class the table css class
     */
	protected function GetTableCssClass(){return "";}
}