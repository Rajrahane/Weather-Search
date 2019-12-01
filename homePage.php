<?php include "apiCalls.php";
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
                	<input type="text" name="latitude" id="latitude" value="-1">
                	<input type="text" name="longitude" id="longitude" value="-1"><br>
                	<button type="submit" class="btn btn-primary" id="searchBtn">Submit</button>
            	</form>
			</div>
		</div>
		<div id="currentTemperatureCard" class="card bg-warning" style="margin:auto;width:40%">
            <div class="card-body">
                <h2 id="currentCityName">Los Angeles</h2>
                <h6	id="currentTimezone">America/LA</h6>
                <h1>
                    <label id="currentTemperature">69.54</label>
                    <sup><img src="https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png" width="10px" height="10px">
                    </sup>F
                </h1>
                <h3 id="currentSummary">Clear</h3><br>
                <div class="container">
                        <div class="row">
                            <div class="col-sm">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-16-512.png" width="20px" height="20px">
                                <br><label id="currentHumidity">0.73</label>
                            </div>
                            <div class="col-sm">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-25-512.png" width="20px" height="20px">
                                <br><label id="currentPressure">1010.5</label>
                            </div>
                            <div class="col-sm">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png" width="20px" height="20px">
                                <br><label id="currentWindSpeed">2.86</label>
                            </div>
                            <div class="col-sm">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-30-512.png" width="20px" height="20px">
                                <br><label id="currentVisibility">7.327</label>
                            </div>
                            <div class="col-sm">
                                <img src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png" width="20px" height="20px">
                                <br><label id="currentCloudCover">0.05</label>
                            </div>
                            <div class="col-sm">
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
		<script>
			window.onload=function(){
				addStates();
				//fetchGeoLocation();
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
					query+="true";
				}else{
					query+="false";
				}
				query=query+"&latitude="+weatherSearchForm.latitude.value
							+"&longitude="+weatherSearchForm.longitude.value;
				if(document.getElementById("currentLocationCheckBox").checked==false){
					query=query+"&street="+weatherSearchForm.street.value
							+"&city="+weatherSearchForm.city.value
							+"&state="+weatherSearchForm.state.value;
				}
				console.log(query);
				xmlHttpRequest.open("post","homePage.php",false);
				xmlHttpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlHttpRequest.send(query);
				var currentWeatherJSONDoc=xmlHttpRequest.responseText;
				console.log(currentWeatherJSONDoc);
				var currentWeatherJSON=JSON.parse(currentWeatherJSONDoc);
				createCurrentWeatherCard(currentWeatherJSON);
				createDailyWeatherPredictionTable(currentWeatherJSON);
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
			function createCurrentWeatherCard(currentWeatherJSON){
				document.getElementById("currentTimezone").innerHTML=currentWeatherJSON.timezone;
				document.getElementById("currentTemperature").innerHTML=currentWeatherJSON.currently.temperature;
				document.getElementById("currentSummary").innerHTML=currentWeatherJSON.currently.summary;
				document.getElementById("currentHumidity").innerHTML=currentWeatherJSON.currently.humidity;
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
						var predictionDate=new Date(dailyWeatherPredictionArray[i].time);
						var column1=createColumn(predictionDate.toDateString(),document,"td");
						var column2=createColumn(dailyWeatherPredictionArray[i].icon,document,"td");
						var column3=createColumn(dailyWeatherPredictionArray[i].summary,document,"td");
						var column4=createColumn(dailyWeatherPredictionArray[i].temperatureHigh,document,"td");
						var column5=createColumn(dailyWeatherPredictionArray[i].temperatureLow,document,"td");
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
			function createColumn(colText,documentName,rowType){
				var column=documentName.createElement(rowType);
				column.style.border="1px solid black";
				var columnText=documentName.createTextNode(colText);
				column.appendChild(columnText);
				return column;
			}
		</script>
		</body>
</html>
