<?php

function route(){
	if('POST' == $_SERVER['REQUEST_METHOD']){
		$routeXL = new RouteXL();
		
		$locations = json_decode($_POST['locations'],$associative=true); // [{"address":"", "lat":"","lng":""},...] 
		$results = $routeXL->tour($locations);
		$results = json_decode($results, $associative=true);
		
		foreach($results['route'] as $key => $stop){
			foreach($locations as $location){
				if($stop['name'] == $location['address']){
					$results['route'][$key]['lat'] = $location['lat'];
					$results['route'][$key]['lng'] = $location['lng'];
				}
			}
		}
		
		$this->viewVars['stops'] = $results['route'];
		$this->viewVars['googleLinks'] = $this->buildGoogleDirectionsLinks($this->viewVars['stops']);
		//var_dump($this->viewVars['googleLinks']); exit;
		$this->assign($this->viewVars);
		return $this->display($template='organize_route.tpl');
	}
}

function buildGoogleDirectionsLinks($stops){
	$links = array();
	$begin = 'https://www.google.com/maps/dir/';
	$stopCount = count($stops);
	if($stopCount < 3){
		trigger_error('Stop count must be at least 3');
	}
	$k = 24;

	do{
		$k--;
		$leftOver = count($stops) % $k;
	}
	while($leftOver < 3);

	$k2 = $k-1;

	$j = 0;
	$end = 0;

	for($i=0; $i<count($stops); $i++){
		if($i % $k == 0){
			$link = $begin;
		}
		$end = ($i-$j);
		$link .= $stops[$end]['lat'].','.$stops[$end]['lng'].'/'; 
		if($i == count($stops)-1){
			if($j >= 0){
				for($m=$end+1; $m<$stopCount; $m++){
					$link .= $stops[$m]['lat'].','.$stops[$m]['lng'].'/';
				}
				$links[] = $link;
				//echo 'end2<br>';
			}
		}
		elseif($i % $k == $k2){
			$links[] = $link;
			//echo '<br>';
			$j++;
		}
	}
	return $links;
	//echo '<hr>';
}