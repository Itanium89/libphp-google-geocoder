#Geocoder

Geocoder using Google's geocoding API

Simple...

	require( 'Geocoder.php' );
	$geocoder = new Geocoder;
	$result = $geocoder->geocode( 'Las Vegas, NV' );
	if ( $result instanceof StdClass ) {
	    // Successful geocode
	}
	else {
		// $result is the error return by google
	}
There's also a second option, that if true, will return only the lat/lng