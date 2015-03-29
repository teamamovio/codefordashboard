
$( document ).ready( function()	{
	
	init();

	

	$("#get_x_axis").on("click", function () {
	
		 alert("Triggered");   
    
    });
	   
}); 

function init()	{
	$("#get_x_axis" ).trigger( "click" );


}
