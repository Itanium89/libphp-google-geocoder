<?php

if ( isset( $_GET['location'] ) ) {
	require( 'Geocoder.php' );
	$geocoder = new Geocoder;
	$result = $geocoder->geocode( $_GET['location'], $_GET['simple'] );
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Geocoder</title>
</head>
<body>

<h1>Geocoder</h1>

<p>Uses the Google geocoding API</p>

<form action="">
	<label>Enter a location to geocode</label><br>
	<input type="text" name="location"><br>
	<input type="checkbox" name="simple" value="true">
	<label>Simple geocoding (only return location)</label><br>
	<input type="submit" value="Geocode">
</form>

<?php if( isset( $result ) ): ?>
<?php if( $result !== false ): ?>
<h2>Result for <?php echo $_GET['location'] ?></h2>
<pre>
<?php print_r($result) ?>
</pre>
<?php else: ?>
<p>Could not geocode</p>
<?php endif; ?>
<?php endif; ?>
</body>
</html>


