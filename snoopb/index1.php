
 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html >
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
		<title>TABC Reports</title>
		<script src="../jquery-1.11.1.js">	</script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="http://code.highcharts.com/highcharts.js"></script>			
		<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		 <link rel="stylesheet" href="/resources/demos/style.css">
		
		<script>
			<?php 	if($_SERVER['HTTP_HOST'] != 'phoenix')	{ ?>
					$.getScript("snoopscript1.js");
			<?php 	}
				else { ?>
					$.getScript("snoopscript1.js");
			<?php 	}				
				?>	 
		</script>
	
		
		
</head>   
    
<body>
	
<div style = "height: 900px; width:1024px; background-color:yellow">	
	<button id = "get_x_axis" >Get MACS</button>	


	<div id="container" style="float:left; width:100%; height:auto; background-color:transparent">
	        </div>	

</div>
</body>
</html>
