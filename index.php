<?php
			//CONSTANTS
			//error_reporting(0);	
			//print_r($_REQUEST);		
            define("GOOGLE_GEOCODING_API","https://maps.googleapis.com/maps/api/geocode/json?");
			define("FORECAST_IO_API","https://api.forecast.io/forecast/");
			define("DARKSKY_NET_API","https://api.darksky.net/forecast/");
			define("GOOGLE_KEY","AIzaSyDWBtO4XwwiZCwCDr6z2aK8rXZMuO0OTNM");
			define("DARK_SKY_KEY","d815d6a10ae34088a87b7e07ffdc16ec");
			$longitude;
			if(isset($_POST["longitude"]))
				$longitude=$_POST["longitude"];
			$latitude;
			if(isset($_POST["latitude"]))
				$latitude=$_POST["latitude"];
			if(isset($_POST["currentLocationCheckBox"])){
				$isCurrentLocCheckBoxEnabled=$_POST["currentLocationCheckBox"];
			}
			else{
				$isCurrentLocCheckBoxEnabled="false";
			}
			$state="";
			if(isset($_POST["state"])){
				$state=$_POST["state"];
			}
			$street="";
			if(isset($_POST["street"])){
				$state=$_POST["street"];
			}
			$city="";
			if(isset($_POST["city"])){
				$state=$_POST["city"];
			}
			$time="";
			if(isset($_POST["time"])){
				$time=$_POST["time"];
				function getDailyWeather($latitude,$longitude,$time){
					$params=DARK_SKY_KEY . "/" . $latitude . "," . $longitude . "," . $time . "?";
					$query=http_build_query([
						'exclude' => "minutely",	
					]);	
					$arrContextOptions=array(
						"ssl" =>array(
							"verify_peer"=>false,
							"verify_peer_name"=>false,	
						)
					);
					return file_get_contents(DARKSKY_NET_API.$params.$query,false,
						stream_context_create($arrContextOptions)				
					);
				}
				$dailyWeatherJSONDoc=json_decode(getDailyWeather($latitude,$longitude,$time));
				header('Content-type:application/json');
                echo json_encode($dailyWeatherJSONDoc);                
				exit();
			}
			if($isCurrentLocCheckBoxEnabled=="false"){
				//print_r("inside getGeoloc");
				function getGeoLocation($street,$city,$state){
					$query=http_build_query([
						'address' => "[".$street.",".$city.",".$state, 
						'key' => GOOGLE_KEY,
					]);
					$arrContextOptions=array(
						"ssl" =>array(
							"verify_peer"=>false,
							"verify_peer_name"=>false,
						)
					);
					return file_get_contents(GOOGLE_GEOCODING_API.$query,false,
						stream_context_create($arrContextOptions)
					);
				}
				if($state!="default"){					
					$locationJSON=json_decode(getGeoLocation($street,$city,$state),true);
					if(isset($locationJSON["results"][0]))
					$latitude =$locationJSON["results"][0]["geometry"]["location"]["lat"];
					if(isset($locationJSON["results"][0]))
					$longitude=$locationJSON["results"][0]["geometry"]["location"]["lng"];
				}
			}
			
			if(isset($latitude) && 	isset($longitude)){
				//print_r("both set");print_r($latitude);print_r($longitude);
				function getCurrentWeather($latitude,$longitude){
					$params=DARK_SKY_KEY . "/" . $latitude . "," . $longitude . "?";
					$query=http_build_query([
						'exclude' => "minutely,hourly,alerts,flags",	
					]);	
					$arrContextOptions=array(
						"ssl" =>array(
							"verify_peer"=>false,
							"verify_peer_name"=>false,	
						)
					);
					return file_get_contents(FORECAST_IO_API.$params.$query,false,
						stream_context_create($arrContextOptions)				
					);
                }			
                $currentWeatherJSONDoc=json_decode(getCurrentWeather($latitude,$longitude));
                header('Content-type:application/json');
                echo json_encode($currentWeatherJSONDoc);
                die();
				//$currentWeatherJSONDoc=;		
				//print_r($currentWeatherJSONDoc);		
				//echo $currentWeatherJSONDoc;
			}
			
			//print_r("printing");print_r($_REQUEST["currentLocationCheckBox"]);
			/*if(!isset($_REQUEST["currentLocationCheckBox"]) && $_REQUEST["state"]!="default"){
				print_r("in here");
				//print_r($locationJSON);		
				print_r($_REQUEST["latitude"]);
				print_r($_REQUEST["longitude"]);				
			}*/
			//print_r($_POST);
			/*if( isset( $longitude ) && isset( $latitude ) ){
				print_r("decoding");
				print_r("lat=");
				print_r($latitude);
				//$currentWeatherJSON=json_decode(currentWeatherJSONDoc);	
				print_r("encoding");		
			}*/
		
?>
<html>
	<head>
	<style>
		.card{
			box-shadow: 0px 0px 20px 4px orange;
			color:white;
			margin:auto;
			padding:10px;
			max-width:40%;
		}
	</style>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
 		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="jumbotron">
           		<h1 class="text-center">Weather Search</h1>
            	<form method="post" name="weatherSearchForm" onsubmit="event.preventDefault();submitForm();" class="was-validated">
                	<div class="form-group">
                    	<label for="streetName">Street:</label><br>
                    	<input type="text" class="form-control" style="width:30%" id="street" placeholder="Street Name" required>
                    	<div class="valid-feedback">Valid</div>
                    	<div class="invalid-feedback">Enter Street Name</div>
                	</div>
                	<div class="form-group">
                        <label for="cityName">City:</label><br>
                        <input type="text" class="form-control" style="width:30%" id="city" placeholder="City Name" required>
                        <div class="valid-feedback">Valid</div>
                        <div class="invalid-feedback">Enter City Name</div>
                	</div>
                	<div class="form-group">
                        <label>State:</label>
                        <select required class="form-control" style="width:30%" name="state" id="state">
							<option value="">State</option>
						</select>
						<div class="valid-feedback">Valid</div>
						<div class="invalid-feedback">Select State</div>
                	</div>
                	<div class="form-check">
                    	<label class="form-check-label">
                        	<input type="checkbox" class="form-check-input" value="" onclick="toggleCurrentLocationCheckbox()" name="currentLocationCheckBox" id="currentLocationCheckBox">
                        	Current Location
                    	</label>
                	</div>
                	<input type="hidden" name="latitude" id="latitude" value="-1">
                	<input type="hidden" name="longitude" id="longitude" value="-1"><br>
                	<button type="submit" class="btn btn-primary" id="searchBtn">Submit</button>
            	</form>
			</div>
		</div>
		<div id="currentTemperatureCard" class="card bg-warning" style="margin:auto;width:40%;display:none" >
            <div class="card-body">
                <h2 id="currentCityName">Los Angeles</h2>
                <h6	id="currentTimezone">America/LA</h6>
                <h1>
                    <label id="currentTemperature">69.54</label>
                    <sup><img src="https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png" width="10px" height="10px">
                    </sup>C
                </h1>
                <h3 id="currentSummary">Clear</h3><br>
                <div class="container">
                        <div class="row">
                            <div class="col-sm" title="Humidity">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-16-512.png" width="20px" height="20px">
                                <br><label id="currentHumidity">73</label>%
                            </div>
                            <div class="col-sm" title="Pressure">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-25-512.png" width="20px" height="20px">
                                <br><label id="currentPressure">1010.5</label>
                            </div>
                            <div class="col-sm" title="Wind Speed">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png" width="20px" height="20px">
                                <br><label id="currentWindSpeed">2.86</label>
                            </div>
                            <div class="col-sm" title="Visibility">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-30-512.png" width="20px" height="20px">
                                <br><label id="currentVisibility">7.327</label>
                            </div>
                            <div class="col-sm" title="Cloud Cover">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png" width="20px" height="20px">
                                <br><label id="currentCloudCover">0.05</label>
                            </div>
                            <div class="col-sm" title="Ozone">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-24-512.png" width="20px" height="20px">
                                <br><label id="currentOzone">287.2</label>
                            </div>
                        </div>
                </div>
            </div>
		</div>
		<br>
		<div class="container">
			<table class="table table-hover" id="searchTable"></table>
		</div>
		<div id="dailyTemperatureCard" class="card bg-success" style="margin:auto;width:40%;display:none">
			<div class="card-body">
				<h1 id="dailyWeatherDate">some date</h1>
				<div class="container">
					<div class="row">
						<div class="col-sm" style="margin:auto">							
							<h1 id="dailySummary">Clear</h1><br>
							<h1>								
                    			<label id="dailyTemperature">69</label>
                    			<sup>
									<img src="https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png" width="10px" height="10px">
                    			</sup>C
                			</h1>
						</div>
						<div class="col-sm">
                                <img id="dailyWeatherIcon" style="margin-left:auto;margin-right:auto;display:block" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-24-512.png" width="60%" height="60%">							
						</div>
					</div>
				</div>
				<h6 style="text-align:center;">
					Precipitation:<label id="dailyPrecipitation">N/A</label>
				<br>
					Chances of Rain:<label id="dailyRain">N/A</label>
					%
				<br>
					WindSpeed:<label id="dailyWindSpeed">N/A</label>
					mph
				<br>
					Humidity:<label id="dailyHumidity">N/A</label>
					%
				<br>
					Visibility:<label id="dailyVisibility">N/A</label>
					mi
				<br>
					Sunrise/Sunset: <label id="dailySun">N/A</label>
				</p>
			</div>
		</div>
		<br>
		<script>
			var iconMap,isIconMapSet=false,iconURL;
			var dailyIconMap,isDailyIconMapSet=false,dailyIconURL;
			window.onload=function(){
				addStates();
				//fetchGeoLocation();
			}
			function mapDailyIconURL(){
				dailyIconURL=[
				"https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png",
				"https://cdn3.iconfinder.com/data/icons/weather-344/142/rain-512.png",
				"https://cdn3.iconfinder.com/data/icons/weather-344/142/snow-512.png",
				"https://cdn3.iconfinder.com/data/icons/weather-344/142/lightning-512.png",
				"https://cdn4.iconfinder.com/data/icons/the-weather-is-nice-today/64/weather_10-512.png",
				"https://cdn3.iconfinder.com/data/icons/weather-344/142/cloudy-512.png",
				"https://cdn3.iconfinder.com/data/icons/weather-344/142/cloud-512.png",
				"https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png"
				];
				var weatherTypes=["clear-day","rain","snow","sleet","wind","fog","cloudy","partly-cloudy-day"];
				dailyIconMap=new Map();
				dailyIconMap.set("clear-night",dailyIconURL[0]);
				dailyIconMap.set("partly-cloudy-night",dailyIconURL[7]);
				for(var i=0;i<8;i++){
					dailyIconMap.set(weatherTypes[i],dailyIconURL[i]);
				}
			}
			function mapIconURL(){
				iconURL=[
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png",
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-04-512.png",
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-19-512.png",
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-07-512.png",
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png",
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png",
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-01-512.png",
				"https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png"
				];
				var weatherTypes=["clear-day","rain","snow","sleet","wind","fog","cloudy","partly-cloudy-day"];
				iconMap=new Map();
				iconMap.set("clear-night",iconURL[0]);
				iconMap.set("partly-cloudy-night",iconURL[7]);
				for(var i=0;i<8;i++){
					iconMap.set(weatherTypes[i],iconURL[i]);
				}
			}
			function submitForm(){
				var xmlHttpRequest=new XMLHttpRequest();
				/*xmlHttpRequest.onreadystatechange=function(){
					if(this.readyState==4 && this.status==200){
						var currentWeatherJSONDoc=xmlHttpRequest.responseText;
						//window.alert("hi there");
						window.alert(currentWeatherJSONDoc);
						//window.alert("here in submit response");
						var currentWeatherJSON=JSON.parse(currentWeatherJSONDoc);
						window.alert(currentWeatherJSONDoc);
						document.getElementById("output").innerHTML=currentWeatherJSON.latitude;
						//createCurrentWeatherCard(currentWeatherJSONDoc);
					}
				};*/
				query="currentLocationCheckBox=";
				if(currentLocationCheckBox.checked==false){
					document.getElementById("currentCityName").innerHTML=city.value;
					query+="false";
				}else{
					query+="true";
				}
				query=query+"&latitude="+weatherSearchForm.latitude.value
							+"&longitude="+weatherSearchForm.longitude.value;
				if(document.getElementById("currentLocationCheckBox").checked==false){
					query=query+"&street="+weatherSearchForm.street.value
							+"&city="+weatherSearchForm.city.value
							+"&state="+weatherSearchForm.state.value;
				}
				console.log(query);
				xmlHttpRequest.open("post","index.php",false);
				xmlHttpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlHttpRequest.send(query);
				var currentWeatherJSONDoc=xmlHttpRequest.responseText;
				console.log(currentWeatherJSONDoc);
				var currentWeatherJSON=JSON.parse(currentWeatherJSONDoc);
				document.getElementById("latitude").value =currentWeatherJSON.latitude;
                document.getElementById("longitude").value =currentWeatherJSON.longitude;
				document.getElementById("currentTemperatureCard").style.display="block";
				document.getElementById("dailyTemperatureCard").style.display="none";
				createCurrentWeatherCard(currentWeatherJSON);
				clearTemperaturePredictionTable();
				createDailyWeatherPredictionTable(currentWeatherJSON);
			}
			function fetchDailyWeatherDescription(){
					console.log(this.value);
					var xmlHttpRequest=new XMLHttpRequest();
					xmlHttpRequest.open("post","index.php",false);
					xmlHttpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					query="time="+this.value;					
					query=query+"&latitude="+weatherSearchForm.latitude.value
						+"&longitude="+weatherSearchForm.longitude.value;
					xmlHttpRequest.send(query);
					var dailyWeatherDescriptionJSONDoc=xmlHttpRequest.responseText;
					console.log(dailyWeatherDescriptionJSONDoc);
					var dailyWeatherDescriptionJSON=JSON.parse(dailyWeatherDescriptionJSONDoc);
					createDailyWeatherCard(dailyWeatherDescriptionJSON);
			}
			function clearTemperaturePredictionTable(){
				document.getElementById("searchTable").innerHTML="";
			}
			function fahrenheitToCelsius(f){
				var celsiusValue=(f-32)*5/9;
				return celsiusValue.toPrecision(2);
			}
			function fetchGeoLocation(){
				var xmlHttpRequest=new XMLHttpRequest();
				xmlHttpRequest.onreadystatechange=function(){
					if(this.readyState==4 && this.status==200){
						var jsonDoc=xmlHttpRequest.responseText;
						console.log(jsonDoc);
						var locationJSON=JSON.parse(jsonDoc);
						document.getElementById("latitude").value =locationJSON.lat;
                        document.getElementById("longitude").value =locationJSON.lon;
						document.getElementById("currentCityName").innerHTML=locationJSON.city;
					}
				};
				xmlHttpRequest.open("get","http://ip-api.com/json",true);
				xmlHttpRequest.send();
			}
			function addStates(){
				var stateNames=["Alabama","Alaska","Arizona","Arkansas","California","Colorado",
						"Connecticut","Delaware","District Of Columbia","Florida","Georgia",
						"Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky",
						"Louisiana","Maine","Maryland","Massachusetts","Michigan"
				];
				var selectState=document.getElementById("state");
				for(var i in stateNames){
					var option=document.createElement("option");
					option.text=stateNames[i];
					selectState.add(option);
				}
			}
			function toggleCurrentLocationCheckbox(){
				var checkBox=document.getElementById("currentLocationCheckBox");
				var street=document.getElementById("street");
				var city=document.getElementById("city");
				var state=document.getElementById("state");

				if(checkBox.checked==true){
					street.value="";street.disabled=true;
					city.disabled=true;city.value="";
					state.disabled=true;state.value="default";
					fetchGeoLocation();
				}else{
					street.disabled=false;city.disabled=false;
					state.disabled=false;
				}
			}
			function getPrecipitationType(value){
				if(value<=0.001) return "None";
				else if(value<=0.015) return "Very Light";
				else if(value<=0.05) return "Light";
				else if(value<=0.1) return "Moderate";
				else return "Heavy";
			}
			function createDailyWeatherCard(dailyWeatherDescriptionJSON){
				var dailyTemperatureCard=document.getElementById("dailyTemperatureCard");
				if(dailyTemperatureCard.style.display=="none"){
					dailyTemperatureCard.style.display="block";
				}
				if(!isDailyIconMapSet){
					mapDailyIconURL();
					isDailyIconMapSet=true;
				}
				var dailyWeatherDate=new Date(dailyWeatherDescriptionJSON.currently.time*1000);
				document.getElementById("dailyWeatherDate").innerHTML
					=dailyWeatherDate.toDateString();
				document.getElementById("dailyWeatherIcon").src
					=dailyIconMap.get(dailyWeatherDescriptionJSON.currently.icon);
				document.getElementById("dailySummary").innerHTML
					=dailyWeatherDescriptionJSON.currently.summary;
				document.getElementById("dailyTemperature").innerHTML
					=fahrenheitToCelsius(dailyWeatherDescriptionJSON.currently.temperature);
					
				
				document.getElementById("dailyPrecipitation").innerHTML
					=getPrecipitationType(dailyWeatherDescriptionJSON.currently.precipIntensity);
				document.getElementById("dailyRain").innerHTML
					=(dailyWeatherDescriptionJSON.currently.precipProbability*100).toPrecision(2);
				document.getElementById("dailyWindSpeed").innerHTML
					=dailyWeatherDescriptionJSON.currently.windSpeed;
				document.getElementById("dailyHumidity").innerHTML
					=(dailyWeatherDescriptionJSON.currently.humidity*100).toPrecision(2);
				document.getElementById("dailyVisibility").innerHTML
					=dailyWeatherDescriptionJSON.currently.visibility;
				var sunRiseTime=new Date(dailyWeatherDescriptionJSON.daily.data[0].sunriseTime*1000);
				var sunSetTime=new Date(dailyWeatherDescriptionJSON.daily.data[0].sunsetTime*1000);
				
				document.getElementById("dailySun").innerHTML
					=getFormattedSuntime(sunRiseTime,sunSetTime);
			}
			function getFormattedSuntime(sunRiseTime,sunSetTime){
				var suntime="";
				if(sunRiseTime.getHours()<10)
					suntime+="0";
				suntime+=sunRiseTime.getHours();
				if(sunRiseTime.getMinutes()<10)
					suntime+="0";
				suntime+=sunRiseTime.getMinutes()+"hrs / ";
				if(sunSetTime.getHours()<10)
					suntime+="0";
				suntime+=sunSetTime.getHours();
				if(sunSetTime.getMinutes()<10)
					suntime+="0";
				suntime+=sunSetTime.getMinutes()+"hrs";
				return suntime;
			}
			function createCurrentWeatherCard(currentWeatherJSON){
				document.getElementById("currentTimezone").innerHTML=currentWeatherJSON.timezone;
				document.getElementById("currentTemperature").innerHTML
					=fahrenheitToCelsius(currentWeatherJSON.currently.temperature);
				document.getElementById("currentSummary").innerHTML=currentWeatherJSON.currently.summary;
				document.getElementById("currentHumidity").innerHTML=(currentWeatherJSON.currently.humidity*100).toPrecision(2);
				document.getElementById("currentPressure").innerHTML=currentWeatherJSON.currently.pressure;
				document.getElementById("currentWindSpeed").innerHTML=currentWeatherJSON.currently.windSpeed;
				document.getElementById("currentVisibility").innerHTML=currentWeatherJSON.currently.visibility;
				document.getElementById("currentCloudCover").innerHTML=currentWeatherJSON.currently.cloudCover;
				document.getElementById("currentOzone").innerHTML=currentWeatherJSON.currently.ozone;
			}
			function createDailyWeatherPredictionTable(currentWeatherJSON){
				var searchTable=document.getElementById("searchTable");
				searchTable.style.display="table";
				var dailyWeatherPredictionArray=currentWeatherJSON.daily.data;
				var headRow=document.createElement("tr");
				if(dailyWeatherPredictionArray.length==0){
					var headRowColumn1=createColumn("No Results to Display",document,"th");
					headRow.appendChild(headRowColumn1);
				}else{
					if(!isIconMapSet){
						mapIconURL();
						isIconMapSet=true;
					}
					var headerName=["Date","Status","Summary","Temperature High","Temperature Low","Wind Speed"];
					for(k in headerName){
						var column=createColumn(headerName[k],document,"th");
						headRow.append(column);
					}
				}
				var searchTableHead=document.createElement("thead");
				searchTableHead.classList.add("thead-dark");
				searchTableHead.appendChild(headRow);
				searchTable.appendChild(searchTableHead);
				if(dailyWeatherPredictionArray.length!=0){
					var searchTableBody=document.createElement("tbody");
					for(i in dailyWeatherPredictionArray){
						var predictionDate=new Date(dailyWeatherPredictionArray[i].time*1000);
						var column1=createColumn(predictionDate.toDateString(),document,"td");
						var column2=createImageColumn(iconMap.get(dailyWeatherPredictionArray[i].icon),document,"td");
						var column3=createAnchorColumn(dailyWeatherPredictionArray[i].summary,dailyWeatherPredictionArray[i].time,document,"td");
						var column4=createColumn(fahrenheitToCelsius(dailyWeatherPredictionArray[i].temperatureHigh),document,"td");
						var column5=createColumn(fahrenheitToCelsius(dailyWeatherPredictionArray[i].temperatureLow),document,"td");
						var column6=createColumn(dailyWeatherPredictionArray[i].windSpeed,document,"td");
						var weatherRow=document.createElement("tr");
						weatherRow.appendChild(column1);weatherRow.appendChild(column2);
						weatherRow.appendChild(column3);weatherRow.appendChild(column4);
						weatherRow.appendChild(column5);weatherRow.appendChild(column6);
						searchTableBody.appendChild(weatherRow);
					}
					searchTable.appendChild(searchTableBody);
				}
			}
			function createAnchorColumn(colText,colValue,documentName,rowType){
				var column=documentName.createElement(rowType);
				column.style.border="1px solid black";
				var anchorTag=documentName.createElement("a");
				anchorTag.value=colValue;
				anchorTag.title="View Summary";
				anchorTag.onclick=fetchDailyWeatherDescription;
				anchorTag.appendChild(document.createTextNode(colText));
				column.appendChild(anchorTag);
				return column;
			}
			function createColumn(colText,documentName,rowType){
				var column=documentName.createElement(rowType);
				column.style.border="1px solid black";
				var columnText=documentName.createTextNode(colText);
				column.appendChild(columnText);
				return column;
			}
			function createImageColumn(imgSrc,documentName,rowType){
				var column=documentName.createElement(rowType);
				column.style.border="1px solid black";
				var imgTag=documentName.createElement("img");
				imgTag.setAttribute("src",imgSrc);
				imgTag.setAttribute("align","center");
				imgTag.setAttribute("width","50px");
				imgTag.setAttribute("height","50px");
				column.appendChild(imgTag);	
				return column;
			}
		</script>
		</body>
</html>
