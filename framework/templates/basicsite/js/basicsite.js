$(document).ready(function()
	{
		if($(".popup_link").length)
			{
				/**		
				 * Called when the input item link is clicked. it displays a popup lightbox with custom content
				 */
				$(".popup_link").click(function()
			  		{
			  			try
			  				{
			  					$(".popup_link").colorbox({iframe:true, width:"80%", height:"75%"});
			  				}
			  			catch(err)
							{
								
							}	
					});
			}	
	});