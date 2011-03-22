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
	 * The object will contain the properties `lat`, `lng`, `formatted_address`, `viewport` `bounds` and `raw`
	 * lat: latitude for the first returned location
	 * lng: longitude for the first returned location
	 * viewport: viewport for the first returned location
	 * bounds: bounds for the first returned location
	 * raw: the entire response
	 * formatted_address: formatted address for the first returned location
	 *
	 * If an error occurred false will be returned
	 *
	 * @link http://code.google.com/apis/maps/documentation/geocoding/
	 *
	 * @param string $location
	 * @return mixed
	 */
	public function geocode( $location, $simple=false ) {
		$api_scrape = $this->scrapeAPI( $location );

		if ( !$api_scrape ) {
			return false;
		}

		$result['lat'] = $api_scrape->results[0]->geometry->location->lat;
		$result['lng'] = $api_scrape->results[0]->geometry->location->lng;
	
		if ( $simple ) {
			return $result;
		}

		$result['formatted_address'] = $api_scrape->results[0]->formatted_address;
		$result['raw'] = $api_scrape->results;

		$result['viewport']['southwest']['lat'] = $api_scrape->results[0]->geometry->viewport->southwest->lat;
		$result['viewport']['southwest']['lng'] = $api_scrape->results[0]->geometry->viewport->southwest->lng;
		$result['viewport']['northeast']['lat'] = $api_scrape->results[0]->geometry->viewport->northeast->lat;
		$result['viewport']['northeast']['lng'] = $api_scrape->results[0]->geometry->viewport->northeast->lng;

		$result['bounds']['southwest']['lat'] = $api_scrape->results[0]->geometry->bounds->southwest->lat;
		$result['bounds']['southwest']['lng'] = $api_scrape->results[0]->geometry->bounds->southwest->lng;
		$result['bounds']['northeast']['lat'] = $api_scrape->results[0]->geometry->bounds->northeast->lat;
		$result['bounds']['northeast']['lng'] = $api_scrape->results[0]->geometry->bounds->northeast->lng;
		return $result;
	}

	/*
	 * Scrape
	 *
	 * @param string $url URL to scrape
	 * @return mixed String or false
	 */
	public function scrape( $url ) {
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
	 * @return string|false a GeocodeError on error, LatLng on success.
	 */
	private function scrapeAPI( $location ) {
		$url = sprintf( "http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false", urlencode( $location ) );
		$response = json_decode( $this->scrape( $url ) );
		if ( $response->status != 'OK' ) {
			return false;
		}
		return $response;
	}

}