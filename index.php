<?php
include "w_top.php";
echo "Hello World.";

?>

<?php
$api_url = "https://api-ssl.bitly.com/oauth/access_token";

$set_header = array();
$set_header[] = "Content-Type: application/x-www-form-urlencoded";
$set_header[] = "Authorization: Basic ZGV2aWxjYW1vbkBob3RtYWlsLmNvbTp0YXdhdGNoYWkxMTUw";

$set_data = array();
$set_data['client_id'] = "f4c11aa908316736555b8fadd2c0c63bf05f078e";
$set_data['client_secret'] = "6bdedd6d4efb71673da697925f8366a392c27127";

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
	echo $result;
}
else
{
	echo "N";
}
?>

<a href="<?php echo $short_url; ?>" target="_blank">Click here.</a>

<?php include "w_bottom_js.php";?>
<?php include "w_bottom.php";?>