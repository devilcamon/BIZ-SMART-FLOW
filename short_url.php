<?php

class short_url
{
	private $_clientID, $_clientSecret, $_genericAccessToken, $_apiKey, $_username, $_tokenAccess;

	public function get_token_access()
	{
		## Write code get token access here.
		$api_url = "https://api-ssl.bitly.com/oauth/access_token";

		$set_header = array();
		$set_header[] = "Content-Type: application/x-www-form-urlencoded";
		$set_header[] = "Authorization: Basic ZGV2aWxjYW1vbkBob3RtYWlsLmNvbTp0YXdhdGNoYWkxMTUw";

		$set_data = array();
		$set_data['client_id'] = $this->_clientID;
		$set_data['client_secret'] = $this->_clientSecret;

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

//		$this->_tokenAccess = "b5ed2a7aa92a429e8fbf990b9e4b38386e264f8d";
		$this->_tokenAccess = $result;
	}

	public function get_short_link($url)
	{
		$api_url = "http://api.bitly.com/v3/shorten";

		$set_header = array();
		$set_header[] = "Content-Type: application/json";

		$set_data = array();
		$set_data['format'] = "json";
		$set_data['apiKey'] = $this->_apiKey;
		$set_data['login'] = $this->_username;
		$set_data['longUrl'] = $url;

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

		$return['status_code'] = $output['status_code'];
		$return['url'] = $output['data']['url'];

		if($output['status_code'] == "200")
		{
			return $return;
		}
		else
		{
			return $return;
		}
	}

	public function set_client_id($value)
	{
		$this->_clientID = $value;
	}

	public function set_client_secret($value)
	{
		$this->_clientSecret = $value;
	}

	public function set_generic_access_token($value)
	{
		$this->_genericAccessToken = $value;
	}

	public function set_api_key($value)
	{
		$this->_apiKey = $value;
	}

	public function set_username($value)
	{
		$this->_username = $value;
	}
}

?>