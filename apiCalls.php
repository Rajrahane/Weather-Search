<?php
			//CONSTANTS
			//error_reporting(0);	
			//print_r($_REQUEST);		
            define("GOOGLE_GEOCODING_API","https://maps.googleapis.com/maps/api/geocode/json?");
			define("FORECAST_IO_API","https://api.forecast.io/forecast/");
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