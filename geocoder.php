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
	private $application = 'libphp-google-geocoder';

	function __construct() {
		openlog($this->application, LOG_PID | LOG_PERROR, LOG_LOCAL0);
		try {
			if (extension_loaded('newrelic')) { 
				newrelic_set_appname($this->application);
			}
			else {
				throw new Exception("Newrelic not installed");
			}
		}
		catch (Exception $e) {
			syslog(LOG_NOTICE, '('.get_current_user().') ['. __FILE__.':'.$e->getLine().'] '. $e->getMessage());
		}
	}

	/** 
	 * @param string $location Location to geocode
	 * @param boolean $simple If true on the lat/lng will be returned
	 * @return StdClass|String
	 */
	public static function geocode( $location, $simple=false ) {
		$api_scrape = self::scrapeAPI( $location );

		if ( !$api_scrape instanceof StdClass ) {
			return false;
		}

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

	/*
	 * Scrape
	 *
	 * @param string $url URL to scrape
	 * @return String|False
	 */
	private static function scrape( $url ) {
		try {
			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curl, CURLOPT_HEADER, 0);
			$data = curl_exec($curl);
			if (curl_errno($curl) != 0) {
				return FALSE;
				throw new Exception(curl_error($curl));
			}
			else {
				return $data;
			}
		}
		catch (Exception $e) {
			syslog(LOG_WARNING, '('.get_current_user().') ['. __FILE__.':'.$e->getLine().'] '. $e->getMessage());
		}
	}

	/**
	 * Scrape the API
	 *
	 * @param string $location Location to geocode
	 * @return StdClass|String
	 */
	private static function scrapeAPI( $location ) {
		$url = sprintf("http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false", urlencode( $location ) );
		try {
			$response = json_decode( self::scrape( $url ) );
			if ( $response->status != 'OK' ) {
				throw new Exception("API status code : " . $response->status);
				return $response->status;
			}
			else {
				return $response;
			}
		}
		catch(Exception $e) {
			syslog(LOG_WARNING, '('.get_current_user().') ['. __FILE__.':'.$e->getLine().'] '. $e->getMessage());
		}
	}

	function __destruct() {
		closelog();
	}
}
?>
