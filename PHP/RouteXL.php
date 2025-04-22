<?php
  
class RouteXL{
    
    var $username = '?????';
    var $password = '?????';

    var $baseURL = 'https://api.routexl.com';
    var $httpResponseCodes = array(
        '401' => 'Authentication problem',
        '403' => 'Too many locations for your subscription',
        '409' => 'No input or no locations found',
        '429' => 'Another route in progress',
        '204' => 'No distance matrix, tour or route was found'
    );
    
    var $logPath = '/logs/routexl/';
    
    function __construct(){
        
    }

    function getFilePath($file){
        if(!file_exists($this->logPath)){
            mkdir($this->logPath,0777,$recursive=true);
        }
        return $this->logPath.$file;
    }
    function loadFile($file){
        return json_decode(file_get_contents(getFilePath($file)),$associative=true);
    }
    function getDatetimeMillis(){
        $now = DateTime::createFromFormat('U.u', microtime(true));
        return $now->format("F d, Y h:i:s.u A");
    }
    function saveRequests($ch, $postBody=''){
        $header = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $output =
        '<u>Header</u>'
        .'<pre>'.htmlentities(print_r($header,true)).'</pre>'
        .'<u>Body</u>'
        .'<pre>'.htmlentities(http_build_query($postBody)).'</pre>'
        .'<u>Decoded Body</u>'
        .'<pre>'.htmlentities(print_r($postBody,true)).'</pre>';

        file_put_contents($this->getFilePath('transactions-log.html'), '<hr><h3>Request</h3><h4>'.$this->getDatetimeMillis().'</h4>'.$output.file_get_contents($this->getFilePath('transactions-log.html')));
    }
    function saveResponses($ch, $response){
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_len);
        $body = substr($response, $header_len);

        $output =
            '<u>Return code</u><br>'
            .curl_getinfo($ch, CURLINFO_HTTP_CODE).'<br><br>'
            .'<u>Header</u>'
            .'<pre>'.htmlentities(print_r($header,true)).'</pre>'
            .'<u>Body</u>'
            .'<pre>'.htmlentities(json_encode(json_decode($body),JSON_PRETTY_PRINT)).'</pre>';

        global $privateFiles;
        file_put_contents($this->getFilePath('transactions-log.html'), '<hr><h3>Response</h3><h4>'.$this->getDatetimeMillis().'</h4>'.$output.file_get_contents($this->getFilePath('transactions-log.html')));
    }
    function curl($url, $headers=[], $method='GET', $postBody=''){
        $ch = curl_init($this->baseURL.$url);
        curl_setopt_array($ch,
            array(
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => $this->username.':'.$this->password,
                CURLOPT_HEADER => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_VERBOSE => 1,
                CURLINFO_HEADER_OUT => 1
            )
        );
        if(isset($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
        }

        $response = curl_exec($ch);
        $this->saveRequests($ch, $postBody);
        $this->saveResponses($ch, $response);

        return $response;
    }
    function getHttpResponseBody($response){
        return substr($response, stripos($response, "\r\n\r\n")+4);
    }
    function prettyPrintResponseBody($response){
        echo '<pre>'.print_r(json_encode(json_decode($this->getHttpResponseBody($response)),JSON_PRETTY_PRINT), true).'</pre>';
    }
    function getHeadersFromCurlResponse($response){
        $headers = array();
        $headerText = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $headerText) as $i => $line){
            if ($i === 0) {
                $headers['http_code'] = $line;
            }
            else{
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
    function tour($addresses){
        foreach($addresses as $address){
            foreach(array('address','lat','lng') as $val){
                if (!isset($address[$val]) || $address[$val] == ''){
                    trigger_error('Incorrect address input: '.var_dump($address));
                }
            }
        }
        
        //[{"address":"The Hague, The Netherlands","lat":"52.05429","lng":"4.248618"},{"address":"The Hague, The Netherlands","lat":"52.076892","lng":"4.26975"},{"address":"Uden, The Netherlands","lat":"51.669946","lng":"5.61852"},{"address":"Sint-Oedenrode, The Netherlands","lat":"51.589548","lng":"5.432482"}]

        $postBody = array(
            'locations' => json_encode($addresses)
        );
        
        $response = $this->curl(
            $url='/tour',
            $headers=array(),
            $method='POST',
            $postBody
        );

        //$this->prettyPrintResponseBody($response);
        return $this->getHttpResponseBody($response);
    }
}  
  
  
?>
