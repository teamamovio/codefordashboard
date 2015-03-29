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
var dataPoints = 30;

//var fakeStartTimes 	=   ['2015-01-21 03:43:00', '2015-01-21 03:44:00', '2015-01-21 03:45:00']; 
//var fakeEndTimes 	=  ['2015-01-21 03:44:00', '2015-01-21 03:45:00', '2015-01-21 03:46:00']; 
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

//	var pointStartDateTimeNow = new Date(2015, 0, 1, 20, 42, 0, 0);
	var pointStartDateTimeNow = new Date();
		pointStartDateTime = new Date(pointStartDateTimeNow .getTime() 
						- dataPoints * minutesInterval * 60000);
		//alert(pointStartDateTimeNow);
		//alert(pointStartDateTime);
		pointJSONStartDateTime = pointStartDateTime.toJSON();
	//	pointJSONLastDateTime  = new Date(2015, 0, 1, 20, 30, 0, 0).toJSON(); // pointStartDateTime.toJSON(); 
	for(i = 0; i<dataPoints; i++){
		pointEndDateTime = 	new Date(pointStartDateTime .getTime() 
							+  minutesInterval * 60000);
		
		fakeStartTimes[i] 	= pointStartDateTime.toJSON();
		fakeEndTimes [i]	= pointEndDateTime.toJSON();
		pointJSONLastDateTime  = fakeEndTimes [i];	
	
	/*	outString 		+= new Date(fakeStartTimes[i]).toLocaleDateString() + ' ' + pointStartDateTime.toLocaleTimeString(); 
		outString		+=  ' - ' ;
		
		outString		+= new Date(fakeEndTimes  [i]).toLocaleDateString() 	+ ' ' + pointEndDateTime.toLocaleTimeString();					 
		outString 		+=  ' **** ';		
	*/	
		pointStartDateTime = new Date(pointStartDateTime .getTime() 
					+ minutesInterval * 60000);  
	}
	//	outString = "Time is Moving On";
	outString 		+= new Date(pointStartDateTimeNow).toString();
	 $("#currentTime").val(outString);
 
	 
//	$.post("getlogdata.php", 
//				{page:document.title}, addRowToLogTable, "json");
		
	$.post("getlogdata.php", 
			{page:document.title,
			startTime:pointJSONStartDateTime,
			endTime:  pointJSONLastDateTime,			
			}, addRowToLogTable, "json"); 
			
}

function startChart()	{
	addTimeSample();
}



function addTimeSample(  )	{
	
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
	            borderColor: '#EBBA95',
	            borderWidth: 5,
	            backgroundColor: {
	                linearGradient: [0, 0, 500, 500],
	                stops: [
	                    [0, 'rgb(255, 255, 255)'],
	                    [1, 'rgb(200, 200, 255)']
	                ]
	            },
	            type: 'line'
	        },	        
	        
	        xAxis: {
	            categories:ts,
	            labels: {
	                rotation: 295
	            }	                
	        }        
            
	    });
	}); 

	
	$.post("getxdata.php", 
    				{page:document.title}, addMacAddresses, "json");
	
}	


function addMacAddresses(records)	{
	// alert("# of Recs is "  + records.length);
	
	for(i = 0; i<records.length; i++)	{
		macAddr = records[i].macAddress;
	//	alert("MAC ADDRESS Series IS: " + macAddr);
	//	macAddresses.push(macAddr);
		
		macAddresses.push(macAddr);
		array = new Array();
		macAddressesCounts.push(array);
	} 
	// Now get Data Points
	
	
	//sendMacsToChart();
	getDataPointsFromPost(0);
	
	//getDataPointsFromPost(1);
	// sendMacsToChart();
}

function getDataPointsFromPost(timeIndex){
	 $.post("getmcount.php", 
				{page:document.title,
				startTime:fakeStartTimes[timeIndex],
				endTime:  fakeEndTimes[timeIndex],
				timeIndex: timeIndex
				}, addMacAddressesCounts, "json");
}

function addMacAddressesCounts(records)	{
	//alert("Do That Stuff ");
	// alert("Detect Count Rec Length is: " + records.length);
	for(i = 0; i<records.length; i++)	{
		
		macCountArray = macAddressesCounts[i];
		
		mac			= records[i].mac;
		detectCount = records[i].detectCount;
		
		macCountArray.push(detectCount);
		
		if(detectCount > 0) {
			dataPointHits++;
			$("#dataPointFound").val(dataPointHits);
		}
		
		 
		
		
	/*	alert("MacAddressArray " + macAddresses[i]
				+ " records " + i + "Detect Count is: " 
				+ detectCount + 
				" for time index " +  timeIndexZ); */

	//	addMacSeriesToChart(records[i].mac, counts);
	}
	
	numLoads++; 
	loadPercent  =  Math.floor((numLoads / dataPoints) * 100);  
	
	$("#loadPercent").val(loadPercent);
	
	if(timeIndexZ < fakeStartTimes.length - 1)
		$("#addSeries" ).trigger( "click" );
	else	{		
		sendMacsToChart();
	}
		
	
	/*
	switch(timeIndexZ)	{
		
		case 0: $("#addSeries" ).trigger( "click" ); break;
		case 1: $("#addSeries" ).trigger( "click" ); break;
		case 2: sendMacsToChart(); break;
		default: ; // Do Nothing;
	}
	*/
		
}



function sendMacsToChart()	{
//	alert("MAC Addressess count is "+ macAddresses.length);
	for(i = 0; i<macAddresses.length; i++)	{
		mac = macAddresses[i];
		macCountArray = macAddressesCounts[i];
		//	alert("Mac Address " + mac + " count 0 is " + macCountArray[0] + " count 1 is " + macCountArray[1] );
		isDetected  = false;
		for(i2 = 0; i2 < macCountArray.length; i2++){
			if(macCountArray[i2]>0)
				isDetected  = true;
				
		}
		if(isDetected)	{
			dataPointFound = true;
			addMacSeriesToChart(mac , macCountArray);
			
		}
	}
	
	
	
	if(!dataPointFound)
		alert("No Data Points Found");
	
}


function addMacSeriesToChart(address, countArray){
//	//alert("Mac address " + address + "has a count length of " + countArray.length);
	var  chart = $('#container').highcharts();	

	chart.addSeries({	
       data: countArray,
       dashStyle: 'longdash',
      name: address
   });
	

}
	
function addRowToLogTable(records)	{
//	alert("Let life" + records[0].rec_id);
	for(i = 0; i<records.length; i++)	{
	
		recID		= records[i].rec_id;
		macAddr 	= records[i].mac;
		firstObs	= records[i].time1;
		secondObs 	= records[i].time2;
		secondObs 	= records[i].beacon_count;
		secondObs 	= records[i].sunc;
		secondObs 	= records[i].runID;
	//	$("#movingLog").append("<tr><td>aaaa</td><td>aaaa</td><td>aaaa</td></tr>");
		
		$("#movingLog").append(
				"<tr " +
					"style = 'width:1600px; background-color:transparent' id = " + 
							(recID + i) + ">" +
					"<td style = 'width:50px'>" +
						"<input  type = 'checkbox'>" +	
					"</td>" +
					"<td style = 'width:150px'>" +	macAddr + "</td>" +
					"<td style = 'width:200px'>" +	firstObs + "</td>" +
				"</tr>"
			);
		
	}
	startChart();
}	


