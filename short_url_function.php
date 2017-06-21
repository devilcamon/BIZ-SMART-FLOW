<?php
/*
 * API Ref.
 * POST /oauth/access_token HTTP/1.1
Host: api-ssl.bitly.com
Content-Type: application/x-www-form-urlencoded

client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&code=CODE&redirect_uri=REDIRECT_URI
 */

//$api_url = "http://api.bitly.com/v3/shorten?format=json&apiKey=R_184e3c7406bd4f1fb18ccd8ce3515c6e&login=devilcamon&longUrl=http%3A%2F%2F203.150.225.80%2Fbizpotential%2Fworkflow%2Fworkflow.php%3FW%3D242%26search%3DY%26PROBLEM_SYSTEM%3D%26WFR_ID%3D%26PROBLEM%3D%26WF_DET_NEXT%3D304";
$api_url = "http://api.bitly.com/v3/shorten";

$set_header = array();
$set_header[] = "Content-Type: application/json";

$set_data = array();
$set_data['format'] = "json";
$set_data['apiKey'] = "R_184e3c7406bd4f1fb18ccd8ce3515c6e";
$set_data['login'] = "devilcamon";
$set_data['longUrl'] = "http://203.150.225.80/bizpotential/workflow/workflow.php?W=242%26search=Y%26PROBLEM_SYSTEM=%26WFR_ID=%26PROBLEM=%26WF_DET_NEXT=304";

$build_data = array();
foreach($set_data as $_key => $_val)
{
	array_push($build_data, $_key."=".$_val);
}
$implode_data = implode('&', $build_data);
$api_url .= "?".$implode_data;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $set_header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

$output = json_decode($result, true);

echo '<pre>';
print_r($output);
?>