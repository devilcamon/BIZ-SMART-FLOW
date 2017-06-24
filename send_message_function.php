<?php
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
$access_token = $_REQUEST['token_access'];

if($_REQUEST['process'] == "Y")
{
	## Call API line push message
	$api_url = "https://api.line.me/v2/bot/message/push";

	$set_header = array();
	$set_header[] = "Content-Type: application/json";
	$set_header[] = "Authorization: Bearer {$access_token}";

	$set_data = array();
	$set_data['to'] = $_REQUEST['user_id'];						// User id for receiver
	$set_data['messages'][0]['type'] = "text";					// Message type
	$set_data['messages'][0]['text'] = $_REQUEST['message'];		// Message

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

	if($result)
	{
		echo "Y";
	}
	else
	{
		echo "N";
	}
}
?>