<?php
$access_token = 'VGO54TpsjKQPB2fpcY02n2SbfETsnV6bNxZPdaeLgohtqwi7wnNl6xF+9zgA5xiv8xZhkUTBjg1Hgog0E23gvI86et1O1YHqbjJZw7FEzScidVC3J7no8vS6U0oFeeuYFei0IxF1tWcOFpTxJb5z5AdB04t89/1O/w1cDnyilFU=';

$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);

$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$access_token}";

if($arrJson['events'][0]['message']['text'] == "สวัสดี"){
	$arrPostData = array();
	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	$arrPostData['messages'][0]['type'] = "text";
	$arrPostData['messages'][0]['text'] = "สวัสดี ID คุณคือ ".$arrJson['events'][0]['source']['userId'];
}else if($arrJson['events'][0]['message']['text'] == "ชื่ออะไร"){
	$arrPostData = array();
	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	$arrPostData['messages'][0]['type'] = "text";
	$arrPostData['messages'][0]['text'] = "ฉันยังไม่มีชื่อนะ";
}else if($arrJson['events'][0]['message']['text'] == "ทำอะไรได้บ้าง"){
	$arrPostData = array();
	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	$arrPostData['messages'][0]['type'] = "text";
	$arrPostData['messages'][0]['text'] = "ฉันทำอะไรไม่ได้เลย คุณต้องสอนฉันอีกเยอะ";
}else{
	$arrPostData = array();
	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	$arrPostData['messages'][0]['type'] = "text";
	$arrPostData['messages'][0]['text'] = "ฉันไม่เข้าใจคำสั่ง";
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$strUrl);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close ($ch);


$strUrl = "https://api.line.me/v2/bot/message/push";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$access_token}";

$arrPostData = array();
$arrPostData['to'] = "U3842f58385e2fa1ed44500796e3ec2de";
$arrPostData['messages'][0]['type'] = "text";
$arrPostData['messages'][0]['text'] = "นี้คือการทดสอบ Push Message";


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$strUrl);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close ($ch);

?>

