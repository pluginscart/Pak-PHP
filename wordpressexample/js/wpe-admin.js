var WPE_Admin = {	
	/**
	 * Used to make a test ajax call
	 * 
	 * It makes a test ajax calls to the server
	 * 
	 * @since 1.0.0	 	
	 */		 
	MakeAjaxCall: function(ajax_nonce)
	{		
		/** The parameters for the ajax call */		
		var parameters                         = {			
		    action: "dashboardwidget",			
			security: ajax_nonce			
		};
				
		jQuery.ajax({
		    type: 'POST',								  
			url: 'admin-ajax.php',
			dataType: 'json',
			data: parameters
		})
		
		.done(function( response ) {							
		    if(response&&response.result=='success') {
		    	/** Success message is shown */
		        alert(WPE_L10n.ajax_success);
		    }
		    else {		      
		    	/** In case of error an alert message is shown */
		        alert(WPE_L10n.ajax_error);		
		    }		 				 
		}).fail(function( result ) {						            
			    /** In case of error an alert message is shown */
		        alert(WPE_L10n.ajax_error);		
		});
	},
	
	/**
	 * Used to validate the settings page form
	 * 
	 * It validates the form on the settings page
	 * 
	 * @since 1.0.0	 	
	 */		 
	ValidateForm: function()
	{		
		/** In case the title field is empty, an alert box is shown */
		if (jQuery("#we_title").val() == "") {
		    alert(WPE_L10n.title_alert);
		    return false;
		}
		
		/** In case the text field is empty, an alert box is shown */
		if (jQuery("#we_text").val() == "") {
		    alert(WPE_L10n.text_alert);
		    return false;
		}
		
		return true;
	}
};