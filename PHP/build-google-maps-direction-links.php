<style>
*{
	font-family: courier;
}
</style>
<?php

buildGoogleDirectionsLinks(array_fill(0, 100, rand(0,100)));

function buildGoogleDirectionsLinks($stops){
	
	$stopCount = count($stops);
	if($stopCount < 3){
		trigger_error('Stop count must be at least 3');
	}
	//echo 'stopCount: '.$stopCount.'<br>';
	$k = 24;

	do{
		$k--;
		$leftOver = count($stops) % $k;
	}
	while($leftOver < 3);

	$k2 = $k-1;

	//echo 'leftOver: '.$leftOver . '<br>';

	$j = 0;
	$end = 0;

	for($i=0; $i<count($stops); $i++){
		if($i % $k == 0){
			echo '<br>begin ';
		}
		$end = ($i-$j);
		echo sprintf("%02d", $end).' '; 
		if($i == count($stops)-1){
			if($j >= 0){
				for($m=$end+1; $m<$stopCount; $m++){
					echo $m.' ';
				}
				echo 'end2<br>';
			}
		}
		elseif($i % $k == $k2){
			echo ' end3<br>';
			$j++;
		}
	}
	echo '<hr>';
}

exit;