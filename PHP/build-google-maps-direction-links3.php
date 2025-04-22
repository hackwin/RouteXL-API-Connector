<style>
*{
	font-family: courier;
	font-size: 14px;
}
</style>
<?php

if('POST' == $_SERVER['REQUEST_METHOD']){
	$stops = array();
	for($i=0; $i < $_REQUEST['count']; $i++){
		$stops[] = array('lat'=>$i,'lng'=>$i);
	}
	buildGoogleDirectionsLinks($stops);
}

function buildGoogleDirectionsLinks($stops){
	
	$stopCount = count($stops);
	
	$links = array();
	
	if($stopCount < 3){
		trigger_error('Stop count must be at least 3');
	}

	// 0 to 24
	for($i=0; $i<25 && $i<$stopCount; $i++){ 
		$links[0][] = $stops[$i];
	}

	// 24 to 49	
	if($stopCount > 25){ 
		for($i=24; $i<49 && $i<$stopCount; $i++){
			$links[1][] = $stops[$i];
		}
	}
	// 48 to 72
	if($stopCount > 48){ 
		for($i=48; $i<73 && $i<$stopCount; $i++){
			$links[2][] = $stops[$i];
		}
	}
	// 72 to 96
	if($stopCount > 72){ 
		for($i=72; $i<97 && $i<$stopCount; $i++){
			$links[3][] = $stops[$i];
		}
	}
	// 97 to 121
	if($stopCount > 97){ 
		for($i=96; $i<122 && $i<$stopCount; $i++){
			$links[4][] = $stops[$i];
		}
	}
	// 121 to 145
	if($stopCount > 121){ 
		for($i=121; $i<146 && $i<$stopCount; $i++){
			$links[5][] = $stops[$i];
		}
	}
	// 145 to 169
	if($stopCount > 145){ 
		for($i=145; $i<170 && $i<$stopCount; $i++){
			$links[6][] = $stops[$i];
		}
	}
	// 169 to 193
	if($stopCount > 169){ 
		for($i=169; $i<194 && $i<$stopCount; $i++){
			$links[7][] = $stops[$i];
		}
	}
	// 193 to 199
	if($stopCount > 193){ 
		for($i=193; $i<200 && $i<count($stops); $i++){
			$links[8][] = $stops[$i];
		}
	}

	$googleLinks = array();
	foreach($links as $key => $group){
		$googleLinks[$key] = 'https://www.google.com/maps/dir/';
		foreach($group as $stop){
			$googleLinks[$key] .= $stop['lat'].','.$stop['lng'].'/';
		}
	}
	
	
	echo '<pre style="white-space: pre-wrap; word-wrap: break-word;">'.print_r($googleLinks,true).'</pre><hr>';
	
	//return $googleLinks;
	
}

?>

<h2>For RouteXL.com - Divide routes into several Google Maps 25-stop routes</h2>
<form method="POST">
Stops: <input name="count" value="<?php echo (isset($_REQUEST['count'])?$_REQUEST['count']:50); ?>"> (3 to 200 stops)
<input type="submit">
</form>