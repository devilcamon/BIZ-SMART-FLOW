<?php
/*
 * API Ref.
 * POST /oauth/access_token HTTP/1.1
Host: api-ssl.bitly.com
Content-Type: application/x-www-form-urlencoded

client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&code=CODE&redirect_uri=REDIRECT_URI
 */

$api_url = "api-ssl.bitly.com";

$set_header = array();
$set_header[] = "application/x-www-form-urlencoded";

$set_data = array();
$set_data['client_id'] = "f4c11aa908316736555b8fadd2c0c63bf05f078e";
$set_data['client_secret'][0]['type'] = "6bdedd6d4efb71673da697925f8366a392c27127";
$set_data['code'][0]['text'] = "5d249ec8bc1230a16edfcdc89ed70ca940bdd409";
$set_data['redirect_uri'][0]['text'] = " http://103.208.27.224/workflow_master4/register?line=U3842f58385e2fa1ed44500796e3ec2de";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $set_header);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($set_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

var_dump($result);
?>