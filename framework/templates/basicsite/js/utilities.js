var Utilities = {
	/** The overlay object. This object is used to create the grayout affect before an ajax call */
	overlay: new Object(),
	/** The html id of the element where the grayout effect is created */
	overlay_div_id: "#main",
/**********************************************************************************************************************/
	/**	 
	 Used to make an ajax call. this function accepts following parameters: 
	 url => url used in ajax request. method=>possible values are GET and POST.
	 arguments => the parameters sent with ajax call.
	 callaback => the function that is called after successfull response from server is recieved.
	 error_call_back => the function that is called when an error response from server is received.
	 is_overlay => possible values are true and false. if set to true then a grayout overlay is displayed on the screen untill the ajax response is recieved from server
	*/
	MakeAjaxCall: function(url,method,arguments,callback,error_call_back,is_overlay)
		{
			try
				{
					if(is_overlay)Utilities.ToggleOverlay("show");
					/** The parameters used to make the ajax call */
					var ajax_parameters = {
							type: method,								  
							url: url,
							dataType: 'json',
							data: ''
					}
					/** If the arguments were given then they are sent with the ajax call */
					if(arguments) {						
						ajax_parameters['data'] = {parameters : arguments};
					}
						
					$.ajax(ajax_parameters)
					.done(function( result ) {
						if(is_overlay)Utilities.ToggleOverlay("hide");
						if(result.result!="success"&&error_call_back)error_call_back(result);
						else if(callback)callback(result);						    
					}).fail(function( result ) {
						if(is_overlay)Utilities.ToggleOverlay("hide");			
						Callbacks.ErrorCallBack(result);   
					});
				}
			catch(err)
				{
					Callbacks.ErrorCallBack();
				}	
	  },
/**********************************************************************************************************************/
	/** 
	 * Used to show/hide the grayout overlay.
	 * If action parameter is set to show, the overlay is displayed. otherwise the overlay is hidden
	*/ 
	ToggleOverlay: function(action)
	  	{
	  		try
	  			{
			  		var main_div=$(Utilities.overlay_div_id);
			  		if(action=="show")Utilities.overlay=new ajaxLoader($(Utilities.overlay_div_id));
					else Utilities.overlay.remove();
				}
			catch(err)
				{
					Callbacks.ErrorCallBack();
				}		
	  	}
/************************************************************************************************************************/	  	
};