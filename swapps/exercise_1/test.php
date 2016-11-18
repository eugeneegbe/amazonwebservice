<?php


$private_key = "y/rUU9SaAYnKp0uzmHPx8LL8F2+t8qYptiE2LM26";
$params = array();
$method = "GET";
$host = "webservices.amazon.com";
$uri = "/onca/xml";

// additional parameters
$params["Service"] = "AWSECommerceService";
$params["Operation"] = "ItemSearch";
$params["AWSAccessKeyId"] = "AKIAIZT5IMI6CLTFZMGQ";
// GMT timestamp
$params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
// API version
$params["Version"] = "2010-05-15";

ksort($params);

// sort the parameters
// create the canonicalized query
$canonicalized_query = array();

foreach ($params as $param => $value) {
    $param = str_replace("%7E", "~", rawurlencode($param));
    $value = str_replace("%7E", "~", rawurlencode($value));
    $canonicalized_query[] = $param . "=" . $value;
}
$canonicalized_query = implode("&", $canonicalized_query);

// create the string to sign
$string_to_sign = $method . "\n" . $host . "\n" . $uri . "\n" . $canonicalized_query;

// calculate HMAC with SHA256 and base64-encoding
$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, True));

// encode the signature for the request
$signature = str_replace("%7E", "~", rawurlencode($signature));

print $signature;

////Catch the response in the $response object
//$response = file_get_contents($request);
//$parsed_xml = simplexml_load_string($response);
//printSearchResults($parsed_xml, $SearchIndex);
//}

