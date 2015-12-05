var IC_Admin = {	
	/** Indicates the progress of the data import */
	import_progress: 0,
	/** The interval in milliseconds for the data import. The import will be repeated after this interval */
	import_interval: 5000,
	/** The data import timer */
	import_timer: 0,
	/** The current import action */
	import_action: "create_custom_posts_taxonomies",
	/** The current verse. The data import will start at this verse */
	next_verse: 0,
	/** The overlay object. this object is used to create the grayout affect before an ajax call */
	overlay                           : new Object(),
	/** The html id of the element where the grayout effect is created */
	overlay_div_id                    : "#wpbody-content",		
	/**
	 * Used to show/hide the grayout overlay. 
	 * If action parameter is set to show, the overlay is displayed. otherwise the overlay is hidden	 
	 *
	 * @since 2.0.0
	*/ 
	ToggleOverlay: function(action)
	{
	    try {
		    var main_div                                = jQuery(IC_Admin.overlay_div_id);
			if(action=="show")
			    IC_Admin.overlay                        = new ajaxLoader(jQuery(IC_Admin.overlay_div_id));
			else 
			    IC_Admin.overlay.remove();
		}
		catch(err) {
		    alert(IC_L10n.data_fetch_alert);
		}		
	},
    /**
	 * Used to update the narrator dropdown
	 * It is called then the language dropdown is selected
	 * And when the settings page first loads
	 * 
	 * @since 2.0.0
    */
	UpdateNarratorDropdown: function()
	{
	    /** The narrator dropdown is emptied */
		jQuery('#ic_narrator').empty();
		/** The current value of the language dropdown is fetched */
		var ic_language                = jQuery("#ic_language").val();  			
		/** The current value of the extra hidden field is fetched */
		var ic_extra                   = jQuery("#ic_extra").val();
		/** The extra field is split on the '@' character */
		temp_arr                       = ic_extra.split("@");
		/** The first array element which is the narrator language mapping is base64 decoded and then json decoded */	  	
		var narrator_language_mapping  = jQuery.parseJSON(window.atob(temp_arr[0]));
		/** The second array element which is the narrator value is base64 decoded and then json decoded*/
		var ic_narrator                = jQuery.parseJSON(window.atob(temp_arr[1]));
 	
		ic_narrator                    = ic_narrator['narrator'];
			  	
		for(var count=0; count<narrator_language_mapping.length; count++) {		  				
		    var temp_language      = narrator_language_mapping[count]['language'];
            var temp_narrator      = narrator_language_mapping[count]['narrator'];
				  				
			if(ic_language==temp_language) {			  					
                if(ic_narrator == temp_narrator)
				    jQuery('#ic_narrator').append(jQuery('<option SELECTED></option>').val(temp_narrator).html(temp_narrator));											  			
				else
				    jQuery('#ic_narrator').append(jQuery('<option></option>').val(temp_narrator).html(temp_narrator));
		    }
	    }
	},
			
	/**
	 * Formats the admin page
	 *
	 * @since 2.0.0
	 */		 
	FormatAdminPage: function()
	{
	    jQuery('#ic_division_number').parent().parent().hide();
		jQuery('#ic_ayat').parent().parent().hide();	  			
		jQuery('#ic_ruku').parent().parent().hide();		
		jQuery('#ic_sura').parent().parent().hide();
		IC_Admin.UpdateNarratorDropdown();
		jQuery('#ic_form').show();		
	},
	
	/**
	 * Toggles the data source option
	 *
	 * @since 2.0.0
	 */		 
	ToggleDataSourceOption: function()
	{
	    /** 	     
	     * If the data source option is set to remote
	     * Then the data import button and import progress window are both hidden
	     */
	    if(jQuery('#ic_data_source').val() == "Remote") {
	    	jQuery('#ic_import').css("visibility","hidden");
	    	jQuery('#ic_settings_right').css("visibility","hidden");
	    }
	    /** Otherwise both import button and progress window are shown */
	    else {
	    	/** An alert message is shown asking the user to click on data import button */	    	
	    	jQuery('#ic_import').css("visibility","visible");
	    	jQuery('#ic_settings_right').css("visibility","visible");
	    	alert(IC_L10n.data_import_message);
	    }
	},
	
	/**
	 * Checks the import status
	 * 
	 * If the data source option is set to local
	 * And the data import has not completed
	 * Then user is asked to click on Import Data button
	 * 
	 * @since 2.0.0
	 * 
	 * @return boolean cancel_submit used to indicate if the submit action should be cancelled
	 */		 
	CheckImportStatus: function()
	{
		var cancel_submit = false;
	    if (jQuery('#ic_data_source').val() == "Local" && IC_Admin.import_progress < 100) {
	    	alert(IC_L10n.data_import_message);
	    	cancel_submit = true;
	    }
	    else
	        cancel_submit = false;
	        
	   return (!cancel_submit);
	},
	
	/** 
	 * Used to handle an error in the data import process
	 * 
	 * It shows an alert message and clears the import variables
	 * 
	 * @since   2.0.0
	 */
	HandleImportDataError: function() {
		/** In case of error an alert message is shown */
		alert(IC_L10n.data_fetch_alert);		     
		/** The overlay div is hidden */
		IC_Admin.ToggleOverlay("hide");
		/** The timer is cleared */
		clearInterval(IC_Admin.import_timer);
		/** The next verse and progress are set to 0 */
		IC_Admin.next_verse            = 0;	 
	    IC_Admin.import_progress       = 0;
	},
	
	/** 
	 * Used to update the import progress
	 * 
	 * It updates the import variables using the new progress value
	 * 
	 * @since   2.0.0
	 * @param array response the response from the server. it is an array with 2 keys:
	 * result => the result of the data import
	 * text => the details of the data import. it is an array with 3 keys:
	 * next_verse => the next verse at which to start the import
	 * progress => the import progress
	 * import_action => the import action
	 */
	UpdateImportProgress: function(response) {
		/** If the import process is complete */
		if (IC_Admin.import_progress >= 100) {
			IC_Admin.CompleteImportProgress();
			return;
		}
		
		/** The import variables are set */
		IC_Admin.next_verse            = response.text.next_verse;	 
	    IC_Admin.import_progress       = response.text.progress;
		IC_Admin.import_action         = response.text.import_action;
		/** If the progress reported by the server is greater than 100 then the progress is set to 100 */
		if (IC_Admin.import_progress > 100)
		    IC_Admin.import_progress   = 100;
		/** The progress bar is updated */
		drawszlider(100,IC_Admin.import_progress);     
	},
	
	/** 
	 * Used to update the import variables once the import process is complete
	 * 
	 * It resets the import variables and clears the import timer
	 * It also shows an alert message to the user asking the user to save the settings
	 * 
	 * @since   2.0.0
	 */
	CompleteImportProgress: function() {
		/** The import timer is cleared */
		clearInterval(IC_Admin.import_timer);
		/** The import complete message is shown */			
		alert(IC_L10n.data_import_complete);
		/** The import button is hidden */
		jQuery('#ic_import').css("visibility","hidden");
		/** The right section of the settings page is hidden */
	    jQuery('#ic_settings_right').css("visibility","hidden");
	    /** The overlay div is hidden */
	    IC_Admin.ToggleOverlay("hidden");	
	},
	
	/**
	 * Starts the data import
	 * 
	 * It makes ajax calls to the server for importing the data
	 * After each ajax call, it updates the progress bar
	 * 
	 * @since 2.0.0	 	
	 */		 
	ImportData: function()
	{		
		/** The current value of the extra hidden field is fetched */
		var ic_extra                           = jQuery("#ic_extra").val();
		/** The extra field is split on the '@' character */
		temp_arr                               = ic_extra.split("@");		
		/** The ajax nonce. it allows secure ajax calls to WordPress */	
		var ajax_nonce                         = temp_arr[2];
		/** The current action to be carried out */
		var import_action                      = IC_Admin.import_action;
		/** The verse id at which to start the import */
		var next_verse                         = IC_Admin.next_verse;
		/** The parameters for the ajax call */		
		var parameters                         = {			
		    action: "holyqurandataimport",
			plugin_action: import_action,
			security: ajax_nonce,
			next_verse: next_verse,
			plugin: 'IC_HolyQuranDataImport'
		};
				
		jQuery.ajax({
		    type: 'POST',								  
			url: 'admin-ajax.php',
			dataType: 'json',
			data: parameters
		})
		.done(function( response ) {							
		    if(response&&response.result=='success') {
		    	/** The import progress status is updated */
		    	IC_Admin.UpdateImportProgress(response);
		    }
		    else {		      
		    	/** The import error is handled */
		    	IC_Admin.HandleImportDataError();
		    }		 				 
		}).fail(function( result ) {						            
			    /** The import error is handled */
		    	IC_Admin.HandleImportDataError();
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
	
		if(jQuery("#ic_narrator").html()!=undefined) {
			IC_Admin.FormatAdminPage();
		}
		
		jQuery("#ic_language").change(function(){
	     	IC_Admin.UpdateNarratorDropdown();
	    });
		
		jQuery("#ic_data_source").change(function(){
	     	IC_Admin.ToggleDataSourceOption();
	    });
	    
	    jQuery("#ic_submit").click(function(){
	     	return IC_Admin.CheckImportStatus();
	    });
	    
	    jQuery("#ic_import").click(function(){
	    	/** The overlay div is shown over the widget */
	        IC_Admin.ToggleOverlay("show");	
	     	IC_Admin.import_timer = setInterval("IC_Admin.ImportData();",IC_Admin.import_interval);
	    });
	});
})( jQuery );