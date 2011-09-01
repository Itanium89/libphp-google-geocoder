<?php

/**
 * Geocoder class for getting lat/lng coordinates of a location
 *
 * This uses Google's geocoding API
 * @link http://code.google.com/apis/maps/documentation/geocoding/
 */

class Geocoder {

	/**
	 * Geocodes a location
	 * 
	 * This will return a Stdclass object if the location is successfully geocoded
	 * The object will contain the properties `lat`, `lng`, `location`, `formatted_address`, `result_count`, `viewport` `bounds` and `raw_results`
	 * lat: latitude for the first returned location
	 * lng: longitude for the first returned location
	 * location: The location that was geocoded
	 * viewport: viewport for the first returned location
	 * bounds: bounds for the first returned location
	 * raw: the entire response
	 * formatted_address: formatted address for the first returned location
	 *
	 * If an error occurred it will be returned
	 *
	 * @link http://code.google.com/apis/maps/documentation/geocoding/
	 *
	 * @param string $location Location to geocode
	 * @param boolean $simple If true on the lat/lng will be returned
	 * @return StdClass|String
	 */
	public function geocode( $location, $simple=false ) {
		$api_scrape = $this->scrapeAPI( $location );

		if ( !$api_scrape ) {
			return false;
		}

		$result['lat'] = $api_scrape->results[0]->geometry->location->lat;
		$result['lng'] = $api_scrape->results[0]->geometry->location->lng;
		$result['location'] = $location;
	
		if ( $simple ) {
			return (object)$result;
		}

		$result['formatted_address'] = $api_scrape->results[0]->formatted_address;
		$result['result_count'] = count( $response->results );
		$result['raw_results'] = $response->results;

		$result['viewport']['southwest']['lat'] = $api_scrape->results[0]->geometry->viewport->southwest->lat;
		$result['viewport']['southwest']['lng'] = $api_scrape->results[0]->geometry->viewport->southwest->lng;
		$result['viewport']['northeast']['lat'] = $api_scrape->results[0]->geometry->viewport->northeast->lat;
		$result['viewport']['northeast']['lng'] = $api_scrape->results[0]->geometry->viewport->northeast->lng;

		$result['bounds']['southwest']['lat'] = $api_scrape->results[0]->geometry->bounds->southwest->lat;
		$result['bounds']['southwest']['lng'] = $api_scrape->results[0]->geometry->bounds->southwest->lng;
		$result['bounds']['northeast']['lat'] = $api_scrape->results[0]->geometry->bounds->northeast->lat;
		$result['bounds']['northeast']['lng'] = $api_scrape->results[0]->geometry->bounds->northeast->lng;
		return (object)$result;
	}

	/*
	 * Scrape
	 *
	 * @param string $url URL to scrape
	 * @return String|False
	 */
	private function scrape( $url ) {
		if ( ini_get( 'allow_url_fopen' ) ) {
			return file_get_contents( $url );
		}
		elseif ( function_exists( 'curl_init' ) ) {
			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curl, CURLOPT_HEADER, 0);
			return curl_exec( $curl );
		}
		else {
				return FALSE;
		}
	
	}

	/**
	 * Scrape the API
	 *
	 * @param string $location Location to geocode
	 * @return StdClass|String
	 */
	private function scrapeAPI( $location ) {
		$url = sprintf( "http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false", urlencode( $location ) );
		$response = json_decode( $this->scrape( $url ) );
		if ( $response->status != 'OK' ) {
			return $response->status;
		}
		return $response;
	}

}