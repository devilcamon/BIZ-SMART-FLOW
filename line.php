<?php
include "w_top.php";
/*
 * curl -X POST \
-H 'Content-Type:application/json' \
-H 'Authorization: Bearer {ENTER_ACCESS_TOKEN}' \
-d '{
    "to": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "messages":[
        {
            "type":"text",
            "text":"Hello, world1"
        },
        {
            "type":"text",
            "text":"Hello, world2"
        }
    ]
 */

## Token from line business
$access_token = 'VGO54TpsjKQPB2fpcY02n2SbfETsnV6bNxZPdaeLgohtqwi7wnNl6xF+9zgA5xiv8xZhkUTBjg1Hgog0E23gvI86et1O1YHqbjJZw7FEzScidVC3J7no8vS6U0oFeeuYFei0IxF1tWcOFpTxJb5z5AdB04t89/1O/w1cDnyilFU=';

if($_POST['process'] == "Y")
{
	## Call API line push message
	$strUrl = "https://api.line.me/v2/bot/message/push";

	$arrHeader = array();
	$arrHeader[] = "Content-Type: application/json";
	$arrHeader[] = "Authorization: Bearer {$access_token}";

	$arrPostData = array();
	/*$arrPostData['to'] = "U3842f58385e2fa1ed44500796e3ec2de";
	$arrPostData['messages'][0]['type'] = "text";
	$arrPostData['messages'][0]['text'] = "นี้คือการทดสอบ Push Message";*/
	$arrPostData['to'] = $_POST['user_id'];							// User id for receiver
	$arrPostData['messages'][0]['type'] = "text";					// Message type
	$arrPostData['messages'][0]['text'] = $_POST['message'];		// Message

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $strUrl);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($ch);
	curl_close($ch);

}
?>

<form method="post">
	<input type="hidden" name="process" id="process" value="Y">
	User id:
	<input type="text" name="user_id" id="user_id" value="U3842f58385e2fa1ed44500796e3ec2de" size="50">
	<br>
	Message:
	<textarea name="message" id="message" rows="3" cols="60"></textarea>
	<br>
	<button type="submit">ส่ง</button>
</form>

<?php include "w_bottom_js.php";?>
<?php include "w_bottom.php";?>