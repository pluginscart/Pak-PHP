var IC_Holy_Quran_Dashboard_Widget    = {
    /** the overlay object. this object is used to create the grayout affect before an ajax call */
	overlay                           : new Object(),
	/** the html id of the element where the grayout effect is created */
	overlay_div_id                    : "#holy-quran-dashboard-widget",		
	/**
	 * Used to show/hide the grayout overlay. 
	 * If action parameter is set to show, the overlay is displayed. otherwise the overlay is hidden	 
	 *
	 * @since    2.0.0
	*/ 
	ToggleOverlay: function(action)
	{
	    try {
		    var main_div                                = jQuery(IC_Holy_Quran_Dashboard_Widget.overlay_div_id);
			if(action=="show")
			    IC_Holy_Quran_Dashboard_Widget.overlay  = new ajaxLoader(jQuery(IC_Holy_Quran_Dashboard_Widget.overlay_div_id));
			else 
			    IC_Holy_Quran_Dashboard_Widget.overlay.remove();
		}
		catch(err) {
		    alert(IC_L10n.data_fetch_alert);
		}		
	},
			
	/**
	 * Gets the text selected by the user	 
	 *
	 * @since    2.0.0
	 */
	GetSelectionText: function()
	{
	    var text 						= "";
		if (window.getSelection)
	        text                        = window.getSelection().toString();
		else if (document.selection && document.selection.type != "Control")
		    text                        = document.selection.createRange().text;
				    		
	    return text;
	},
				
	/**
	 * Opens the dictionary url so user can check definition of word
	 *
	 * @since    2.0.0
	 */		
	OpenDictionaryURL: function(dictionary_url)
	{
	    var selected_text               = IC_Holy_Quran_Dashboard_Widget.GetSelectionText();
		if(selected_text=="")
		    alert(IC_L10n.selected_text_alert);
		else {
		    dictionary_url              = dictionary_url.replace("{word}",selected_text.toLowerCase());
			window.open(dictionary_url);
		}
	},
		
	/**
	 * Used to register the event handlers for the Holy Quran Dashboard Widget
	 * 
	 * @since 2.0.0 
	 */
	RegisterEventHandlers: function()
	{
		jQuery("#ic_division_number").change(function(){
	     	IC_Holy_Quran_Dashboard_Widget.FetchVerseData("division_number_box");
	    });
		
		jQuery("#ic_sura").change(function(){
	     	IC_Holy_Quran_Dashboard_Widget.FetchVerseData("sura_box");
	    });
	    
	    jQuery("#ic_ruku").change(function(){
	     	IC_Holy_Quran_Dashboard_Widget.FetchVerseData("ruku_box");
	    });
	    
	    jQuery("#next").click(function(){
	     	IC_Holy_Quran_Dashboard_Widget.FetchVerseData("next");
	    });
	    
	    jQuery("#prev").click(function(){
	     	IC_Holy_Quran_Dashboard_Widget.FetchVerseData("prev");
	    });
	},
	
    /**
	 * Fetches verse data from the server	 
	 *
	 * @since    2.0.0
	 */
	FetchVerseData: function(navigator_action)
	{	
		/** The ajax nonce. it allows secure ajax calls to WordPress */	
		var ajax_nonce                   = IC_Holy_Quran_Dashboard_Widget.ic_ajax_nonce;
		/** If sura was selected from sura box */
		if(navigator_action=="sura_box") {
		    jQuery('#ic_ruku_number_box').val("1");
		}
		/** If division number was selected from division number box */
		else if(navigator_action=="division_number_box") {
		    jQuery('#ic_ruku_number_box').val("1");
			jQuery('#ic_sura_box').val("1");
		}
		/** The state of the Holy Quran Widget. it contains the current selected data */
		var navigator_state              = {
			division_number: jQuery('#ic_division_number').val(),
			sura: jQuery('#ic_sura').val(),
			ruku: jQuery('#ic_ruku').val(),			
			action: navigator_action
		};
		/** The parameters for the ajax call */		
		var parameters                   = {			
		    action: "holyqurandashboardwidget",
			plugin_action: "fetch_navigator_data",
			security: ajax_nonce,
			state: navigator_state,
			plugin: 'IC_HolyQuranDashboardWidget'
		};
		/** The overlay div is shown over the widget */
	    IC_Holy_Quran_Dashboard_Widget.ToggleOverlay("show");					
		jQuery.ajax({
		    type: 'POST',								  
			url: 'admin-ajax.php',
			dataType: 'json',
			data: parameters
		})
		.done(function( response ) {							
		    if(response&&response.result=='success') {
		        jQuery("#ic-quran-dashboard-widget-text").html(response.text);
		        IC_Holy_Quran_Dashboard_Widget.RegisterEventHandlers();
		    }
		    else {
		        alert(IC_L10n.data_fetch_alert);		     
		        IC_Holy_Quran_Dashboard_Widget.ToggleOverlay("hide");
		    }		 				 
	    }).fail(function( result ) {					
	            IC_Holy_Quran_Dashboard_Widget.ToggleOverlay("hide");
			    alert(IC_L10n.data_fetch_alert);				
		});
    } 		
};

(function( jQuery ) {
	'use strict';
	/**
	 * All of the code for your Dashboard-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the jQuery function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * jQuery(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * jQuery( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */	 
	 jQuery( window ).load(function() {		 
		IC_Holy_Quran_Dashboard_Widget.RegisterEventHandlers();		
	});
})( jQuery );