<?php
include "short_url.php";

$access_token = 'VGO54TpsjKQPB2fpcY02n2SbfETsnV6bNxZPdaeLgohtqwi7wnNl6xF+9zgA5xiv8xZhkUTBjg1Hgog0E23gvI86et1O1YHqbjJZw7FEzScidVC3J7no8vS6U0oFeeuYFei0IxF1tWcOFpTxJb5z5AdB04t89/1O/w1cDnyilFU=';

$content = file_get_contents('php://input');
$json = json_decode($content, true);

$api_url = "https://api.line.me/v2/bot/message/reply";

$set_header = array();
$set_header[] = "Content-Type: application/json";
$set_header[] = "Authorization: Bearer {$access_token}";

if($json['events'][0]['message']['text'] == "สวัสดี"){
	$set_data = array();
	$set_data['replyToken'] = $json['events'][0]['replyToken'];
	$set_data['messages'][0]['type'] = "text";
	$set_data['messages'][0]['text'] = "สวัสดี ID คุณคือ ?";
}
elseif($json['events'][0]['message']['text'] == "ชื่ออะไร")
{
	$set_data = array();
	$set_data['replyToken'] = $json['events'][0]['replyToken'];
	$set_data['messages'][0]['type'] = "text";
	$set_data['messages'][0]['text'] = "ฉันยังไม่มีชื่อนะ";
}
elseif($json['events'][0]['message']['text'] == "ลงทะเบียน")
{
	## Setup api for get short url
	$url = new short_url();
	$url->set_client_id('f4c11aa908316736555b8fadd2c0c63bf05f078e');
	$url->set_client_secret('6bdedd6d4efb71673da697925f8366a392c27127');
	$url->set_generic_access_token('5d249ec8bc1230a16edfcdc89ed70ca940bdd409');
	$url->set_username('devilcamon');
	$url->set_api_key('R_184e3c7406bd4f1fb18ccd8ce3515c6e');

	## get token access
	$url->get_token_access();
	## get short url
	$short_url = $url->get_short_link('http://103.208.27.224/workflow_master4/register?line='.$json['events'][0]['source']['userId']);

	$set_data = array();
	$set_data['replyToken'] = $json['events'][0]['replyToken'];
	$set_data['messages'][0]['type'] = "text";
//	$set_data['messages'][0]['text'] = "ลงทะเบียน Line ID กับ BizSmartFlow ได้ที่นี่  http://103.208.27.224/workflow_master4/register?line=".$json['events'][0]['source']['userId'];
	$set_data['messages'][0]['text'] = "ลงทะเบียน Line ID กับ BizSmartFlow ได้ที่นี่  ".$short_url;
}
else
{
	$set_data = array();
	$set_data['replyToken'] = $json['events'][0]['replyToken'];
	$set_data['messages'][0]['type'] = "text";
	$set_data['messages'][0]['text'] = "ฉันไม่เข้าใจคำสั่ง";
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$api_url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $set_header);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($set_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close ($ch);


?>

