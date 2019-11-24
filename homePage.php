<html>
	<head>	
	</head>
	<body>
		<h1 style="text-align:center;">Weather Search</h1>
		<form method="POST" name="weatherSearchForm">
			<b>Street</b><input id="street" name="street" required><br>
			<b>City</b><input id="city" name="city" required><br>
			<b>State</b>
				<select name="state" id="state">
				<option value="default">State</option>
				</select><br>
			<input type="checkbox" onclick="toggleCurrentLocationCheckbox()" name="currentLocationCheckBox" id="currentLocationCheckBox" >Current Location<br>
			<input type="submit" onclick="submitForm()" value="Search" id="searchBtn">
			<input type="button" value="Clear">
			<input type="text" name="latitude" id="latitude">
            		<input type="text" name="longitude" id="longitude" >
		</form>
		
		<script>
			window.onload=function(){
				addStates();
				//fetchGeoLocation();
			}
			function submitForm(){
				weatherSearchForm.submit();
			}
			function fetchGeoLocation(){
				var xmlHttpRequest=new XMLHttpRequest();
				xmlHttpRequest.onreadystatechange=function(){
					if(this.readyState==4 && this.status==200){
						var jsonDoc=xmlHttpRequest.responseText;
						window.alert(jsonDoc);
						var locationJSON=JSON.parse(jsonDoc);
						document.getElementById("latitude").value =locationJSON.lat;
                        			document.getElementById("longitude").value =locationJSON.lon;
					}
				};
				xmlHttpRequest.open("GET","http://ip-api.com/json",true);
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
		</script>
		<?php
			//CONSTANTS			
            		define("GOOGLE_GEOCODING_API","https://maps.googleapis.com/maps/api/geocode/json?");
			define("FORECAST_IO_API","https://api.forecast.io/forecast/");
			define("GOOGLE_KEY","AIzaSyDWBtO4XwwiZCwCDr6z2aK8rXZMuO0OTNM");
			define("DARK_SKY_KEY","d815d6a10ae34088a87b7e07ffdc16ec");

			function getGeoLocation(){
				$query=http_build_query([
					'address' => "[".$_POST["street"].",".$_POST["city"].",".$_POST["state"], 
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

			function getCurrentWeather(){
				$params=DARK_SKY_KEY . "/" . $_POST["latitude"] . "," . $_POST["longitude"] . "?";
				$query=http_build_query([
					'exclude' => "minutely,hourly,alerts,flags"	
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
			if(!isset($_POST["currentLocationCheckBox"]) && $_POST["state"]!="default"){
				$locationJSON=json_decode(getGeoLocation(),true);
				$_POST["latitude"] =$locationJSON["results"][0]["geometry"]["location"]["lat"];
				$_POST["longitude"]=$locationJSON["results"][0]["geometry"]["location"]["lng"];
				//print_r($locationJSON);		
				print_r($_POST["latitude"]);
				print_r($_POST["longitude"]);				
			}
			if( isset( $_POST["longitude"] ) && isset( $_POST["latitude"] ) ){
				$currentWeatherJSON=json_decode(getCurrentWeather(),true);
				print_r($currentWeatherJSON);
			}
		?>
	</body>
</html>