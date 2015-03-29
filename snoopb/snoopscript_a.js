var bTimeLineChartIntialized = false;
var statusMsgInterval = null;
var clubCount = 0;
var bInputsEnabled = true;
// var bLimitReached  = false;
var bSelClubFresh = true;

var macAddresses			= [];
var macAddressesCounts		= [];
var fakeStartTimes 	= [] ; 
var fakeEndTimes 	= [] ; 

var dataPointFound = false;
var dataPointHits = 0;

var numLoads = 0;
var loadPercent = 0;

var minutesInterval = 1;
var dataPoints = 60; //Samples

//var fakeStartTimes 	=   ['2015-01-21 03:43:00', '2015-01-21 03:44:00', '2015-01-21 03:45:00']; 
//var fakeEndTimes 	=  ['2015-01-21 03:44:00', '2015-01-21 03:45:00', '2015-01-21 03:46:00']; 

var macAddressHighCharts = [];

var fakeMac =  [
                 { key: 'macAddress', val: 'In Time' }
             ];




var timeIndexZ = 0;

$( document ).ready( function()	{
	
	init();
	
	

	$("#get_x_axis").on("click", function () {
	
	//	 alert("Triggered");   
    	 $.post("getxdata.php", 
    				{page:document.title}, addMacAddresses, "json");
    	 
    }); 
	
	    $('#addSeries').on("click", function () {
	    //	alert("Time Index is " +  timeIndexZ + " and about to increase to " + (timeIndexZ + 1));
	    	timeIndexZ++;
	    	getDataPointsFromPost(timeIndexZ);
	    });
	    
	    
	    $('#completeChart').click(function () {
		    
	    	sendMacsToChart();
	    });
	 	
}); 

function init()	{
	

	$("#get_x_axis").css("display", "none");
	$("#addSeries").css("display", "none");
	$("#completeChart").css("display", "none");
	
	$("#dataPointFound").val(dataPointHits);
	
	loadPercent = (numLoads/dataPoints);
	$("#loadPercent").val(loadPercent);
	
	
	setStartEndTimes();
	
	// startChart();
}

function setStartEndTimes()	{	
	var outString  = "";
	//'2015-01-21 03:46:29'

	var pointStartDateTimeNow = new Date(2015, 0, 22, 17, 41, 0, 0);
//	var pointStartDateTimeNow = new Date();
	// alert("CST " + pointStartDateTimeNow );
		pointStartDateTime = new Date(pointStartDateTimeNow .getTime() 
						- dataPoints * minutesInterval * 60000);

		pointJSONStartDateTime = 		pointStartDateTime.toJSON();
	//	alert("CST Point Start is: " +  pointStartDateTime);
		var offset = new Date().getTimezoneOffset();
	//	alert("Time offset in minutes is: " + offset);
		pointEndDateTime = new Date(pointStartDateTime.getTime());
	
	for(i = 0; i<dataPoints; i++){
		
		
	//	var temp			= pointStartDateTime;
	//	fakeStartTimes[i] 	= pointStartDateTime.toJSON();
		
		fakeEndTimes [i]	= pointEndDateTime.toJSON();
		pointJSONLastDateTime  = fakeEndTimes [i];	
	
		pointEndDateTime =  new Date(	pointEndDateTime .getTime() 
		+   minutesInterval * 60000); 
		
	/*	outString 		+= new Date(fakeStartTimes[i]).toLocaleDateString() + ' ' + pointStartDateTime.toLocaleTimeString(); 
		outString		+=  ' - ' ;
		
		outString		+= new Date(fakeEndTimes  [i]).toLocaleDateString() 	+ ' ' + pointEndDateTime.toLocaleTimeString();					 
		outString 		+=  ' **** ';		
	*/	
	/*	pointStartDateTime = new Date(pointStartDateTime .getTime() 
					+ minutesInterval * 60000);  */
	}
	//	outString = "Time is Moving On";

	outString 		+= new Date(pointStartDateTimeNow).toString();
	 $("#currentTime").val(outString); 
 
	 
	
//	 getLogData(); // Draws Table Data
//	 alert("Zulu start " + fakeStartTimes[0] );
	 startChart();
}

function getLogData()	{
	$.post("getlogdata.php", 
			{page:document.title,
			startTime:pointJSONStartDateTime,
			endTime:  pointJSONLastDateTime,			
			}, addRowToLogTable, "json"); 
}

function startChart()	{
	$.post("getactivelogdata.php", 
			{page:document.title,
		startTime:pointJSONStartDateTime,
		endTime:  pointJSONLastDateTime}, 
		addMacAddresses, "json");
	

}



function addTimeSample(chartHeight  )	{

	var ts =  [] ;
	 for(i = 0; i<fakeEndTimes.length; i++)	{
		 date = new Date(fakeEndTimes[i]);
		 ts[i] =/* date.toLocaleDateString() 	+ ' '  + */ date.toLocaleTimeString();
	 } 
	
	$(function () {
	    $('#container').highcharts({
	        
	    		    	
	    	title: {
	            text: 'MACs Found'	          
	        },
	        
	       
	        chart: {
	        	height:chartHeight,
	            borderColor: '#EBBA95',
	            borderWidth: 5,
	            backgroundColor: {
	                linearGradient: [0, 0, 500, 500],
	                stops: [
	                    [0, 'rgb(255, 255, 255)'],
	                    [1, 'rgb(200, 200, 255)']
	                ]
	            },
	            type: 'line',
	            inverted: false
	          	        },
	        
	        plotOptions: {
	            series: {
	            	marker: {
                        enabled: false
                    },  
	            	
	             /*   stacking: 'normal', 	  */            
	                step: 'center' // or '' or 'right' 
	                	
	            
	            } 
	        }, 
	        
	        xAxis: {
	            categories:ts,
	            labels: {
	                rotation: 295
	            }	                
	        }, 
	        

	        yAxis: {
	        	 title:{
	        		 text: 'MACS'
	        	 },
	        	 categories:macAddressHighCharts,
	        	 tickInterval: 2,
	        	 labels: {
	                 align: 'right',
	                 x: 0,
	                 y: -6 
	             },
	        	 floor: 0,
	        	 endOnTick: false
	        }, 
	        
	        tooltip: {
	        	headerFormat: '<small>{}MAC:</small><table>',
	            pointFormat: '{series.name}: <b>{}</b><br/>',	            
	            shared: false
	        },
	        
	       
            
	    });
	}); 

/*	
	$.post("getxdata.php", 
    				{page:document.title}, addMacAddresses, "json");
*/
	
	
	// addMacAddresses(fakeMac);
}	


function addMacAddresses(records)	{
	addTimeSample(records.length * 40);
// alert("# of Mac Recs is "  + records.length);
	var names = ['time1'];
	for(i = 0; i<records.length; i++)	{
		macAddr =  records[i].mac;
//	alert("MAC ADDRESS Series IS: " + macAddr);
	//	macAddresses.push(macAddr);
		
		macAddresses.push(macAddr);
		macAddressHighCharts.push(macAddr);
		macAddressHighCharts.push('   ');
		var detects = new Array();
		var v = 2;		
		$.each(records[i], function(key, value) {
			if(key.substring(0,4) == 'time' )	{
				 v = parseInt(value);
				var theValue = v ; //+ (i * 2);
				
			//	alert("X-Axis value is: " + theValue);
				detects.push(v + (i * 2) );
				if(value > 0) {
					dataPointHits++;
					$("#dataPointFound").val(dataPointHits);
				}
			//	alert( "The key is '" + key + "' and the value is '" + v + "'" );
				//v++;
			}
			
		});	
		
			addMacSeriesToChart(macAddr, detects );
		
	}
//	numLoads++; 
//	loadPercent  =  Math.floor((numLoads / dataPoints) * 100);  
	
	$("#loadPercent").val('50');
	
	getLogData();
	

}


function addMacSeriesToChart(addr, detects){
//	//alert("Mac address " + address + "has a count length of " + countArray.length);
	
//	alert("Seris Name is " + addr);
	var  chart = $('#container').highcharts();	

	chart.addSeries({	
       data: detects.reverse(),
       dashStyle: 'solid',
       name: addr
   });
	

}
	
function addRowToLogTable(records)	{
//	alert("Number of table records is " + records.length);
	for(i = 0; i<records.length; i++)	{
	
		recID		= records[i].rec_id;
		macAddr 	= records[i].mac;
		firstObs	= records[i].time1;
		lastObs 	= records[i].time2;
		beaconCount	= records[i].beacon_count;
		sunC 		= records[i].sunc;
		runID 		= records[i].runID;
	//	$("#movingLog").append("<tr><td>aaaa</td><td>aaaa</td><td>aaaa</td></tr>");
		
		$("#movingLog").append(
				"<tr " +
					"style = 'width:1600px; background-color:transparent' id = " + 
							(recID + i) + ">" +
				/*	"<td style = 'width:50px'>" +
						"<input  type = 'checkbox'>" +	
					"</td>" + */
					"<td style = 'width:150px'>" +	macAddr + "</td>" +
					"<td style = 'width:200px'>" +	firstObs + "</td>" +
					"<td style = 'width:200px'>" +	lastObs + "</td>" +
				"</tr>"
			);
		
	}
	$("#loadPercent").val('100');
}	


