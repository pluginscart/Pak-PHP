$(document).ready(function()
	{
		if($("#upload_file_link").length)
			{
				/**		
				 * Called when the input item link is clicked. it displays a popup lightbox with custom content
				 */
				$("#upload_file_link").click(function()
			  		{
			  			try
			  				{
			  					$("#upload_file_link").colorbox({iframe:true, width:"80%", height:"75%"});
			  				}
			  			catch(err)
							{
								
							}	
					});
			}	
	});