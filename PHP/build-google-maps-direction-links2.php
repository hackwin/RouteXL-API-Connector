<style>
*{
	font-family: courier;
}
</style>
<?php

$stops = array();
for($i=0; $i<100; $i++){
	$stops[$i]['lat'] = 123;
	$stops[$i]['lng'] = 456;
}

$links = buildGoogleDirectionsLinks($stops);
var_dump($links);

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


exit;