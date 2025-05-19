<?php
$startTime = microtime(true);

$chs = array();
$mh = curl_multi_init();
curl_multi_setopt($mh, CURLMOPT_MAX_HOST_CONNECTIONS, 10);
curl_multi_setopt($mh, CURLMOPT_MAX_TOTAL_CONNECTIONS, 10);
curl_multi_setopt($mh, CURLMOPT_MAX_CONCURRENT_STREAMS, 10);

$addresses = <<<txt
# street, city, state, country
# street, city, state, country
# street, city, state, country
txt;

$addresses = explode("\r\n", $addresses);

// your mapbox API key here
$apiKey = 'pk.???';

foreach($addresses as $address){
	$url = "https://api.mapbox.com/search/geocode/v6/forward?q=".urlencode($address)."&proximity=ip&access_token=".$apiKey;
	$chs[] = curl_init($url);
	curl_setopt($chs[count($chs)-1], CURLOPT_RETURNTRANSFER, 1);
}

foreach($chs as $ch){
	curl_multi_add_handle($mh, $ch);
}

// execute all queries simultaneously, and continue when all are complete
$running = null;
do {
	curl_multi_exec($mh, $running);
} while ($running);
  
//close the handles
foreach($chs as $ch){
	curl_multi_remove_handle($mh, $ch);
}

curl_multi_close($mh);

foreach($chs as $ch){
	$response = curl_multi_getcontent($ch);
	curl_close($ch);
	var_dump($response);
}

echo 'Time taken: '.number_format(microtime(true)-$startTime, 3);