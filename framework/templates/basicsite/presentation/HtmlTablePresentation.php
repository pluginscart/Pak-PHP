<?php

namespace Framework\Templates\BasicSite\Presentation;

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
abstract class HtmlTablePresentation
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
    public function GetTableParameters($table_data)
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
        $movie_data                  = $this->GetRowParameters($table_data);
		/** The table row css values. The css class alternates between the rows **/
        $table_css        = array(
            "CSSlistDARK",
            "CSSlistLIGHT"
        );
        /** The table parameters are returned **/
        $table_parameters = array(
            "header_widths" => $table_width_alignment['header_widths'],
            "header_column_class" => $table_width_alignment['column_css_class'],
            "table_headers" => $table_headers,
            "table_rows" => $movie_data,
            "table_css" => $table_css
        );
        
        return $table_parameters;        
    }

    /** Used to get the html row template parameters for the given table data */
	abstract protected function GetRowParameters($table_data);
	/** Used to get the table sort information */
	abstract protected function GetTableSortInformation();
	/** Used to get the table header links. Used to sort the table columns */
	abstract protected function GetHeaderLinks($table_links);
	/** Used to get the table headers */
	abstract protected function GetTableHeaders($header_links);
	/** Used to get the table header widths and header alignment */
	abstract protected function GetHeaderWidthAlignment();
}