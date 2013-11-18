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
	 * The object will contain the properties `lat`, `lng`, `location`, `formatted_address`, `result_count`, and `raw_results`
	 * lat: latitude for the first returned location
	 * lng: longitude for the first returned location
	 * location: The location that was geocoded
	 * raw: the entire response
	 * formatted_address: formatted address for the first returned location
	 *
	 * If an error occurred it will be returned
	 *
	 * @link http://code.google.com/apis/maps/documentation/geocoding/
	 */


	/** 
	 * @param string $location Location to geocode
	 * @param boolean $simple If true on the lat/lng will be returned
	 * @return StdClass|String
	 */
	public static function geocode( $location, $simple=false ) {
		$url = sprintf("http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false", urlencode( $location ) );

		$response = json_decode( self::scrape( $url ) );

		if ( $response->status != 'OK' ) {
			throw new Exception("API status code : " . $response->status);
		} 
		else {
			$result['lat'] = $api_scrape->results[0]->geometry->location->lat;
			$result['lng'] = $api_scrape->results[0]->geometry->location->lng;
			$result['location'] = $location;
		
			if ( $simple ) {
				return (object)$result;
			}
	
			$result['formatted_address'] = $api_scrape->results[0]->formatted_address;
			$result['result_count'] = count( $api_scrape->results );
			$result['raw_results'] = $api_scrape->results;
	
			return (object)$result;
		}
	}
}
?>
