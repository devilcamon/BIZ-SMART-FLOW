<?php

function assign_job($txt)
{
	$find_assign = strpos($txt, 'มอบหมายงาน');

	if($find_assign !== false)
	{
		$assign = iconv_substr($txt, 10, iconv_strlen($txt, 'utf-8'), 'utf-8');

		preg_match_all("/(##)([a-zA-Z0-9_]+)( )/", $assign, $match_project, PREG_SET_ORDER);
		preg_match_all("/(@@)([a-zA-Z0-9_]+)(!!)/", $assign, $match_to, PREG_SET_ORDER);

		$raw_assign_project = $match_project[0][0];
		$raw_assign_to = $match_to[0][0];
		$assign_project = $match_project[0][2];
		$assign_to = $match_to[0][2];

		$array_search = array($raw_assign_project, $raw_assign_to);
		$array_replace = array('', '');
		$assign_detail = trim(str_replace($array_search, $array_replace, $assign));

		if($assign_to == "NS")
		{
			$line_to = "U3842f58385e2fa1ed44500796e3ec2de";
		}
		elseif($assign_to == "TU")
		{
			$line_to = "U3842f58385e2fa1ed44500796e3ec2de";
		}
		elseif($assign_to == "TEST")
		{
			$line_to = "Uf19e365fcf20f66d473ae895de0ef4bc";
		}

		if($line_to != "")
		{

			$line_token = "VGO54TpsjKQPB2fpcY02n2SbfETsnV6bNxZPdaeLgohtqwi7wnNl6xF+9zgA5xiv8xZhkUTBjg1Hgog0E23gvI86et1O1YHqbjJZw7FEzScidVC3J7no8vS6U0oFeeuYFei0IxF1tWcOFpTxJb5z5AdB04t89/1O/w1cDnyilFU=";
			$line_message = "คุณได้รับงานใหม่: ".$assign_detail;

			$a_data['process'] = "Y";
			$a_data['token_access'] = $line_token;
			$a_data['user_id'] = $line_to;
			$a_data['message'] = $line_message;

			$url = 'https://bizsmartflow.herokuapp.com/send_message_function.php';
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $a_data);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$send_result = curl_exec($curl);
			curl_close($curl);

			return "มอบหมายงานให้ ".$assign_to." สำเร็จ";
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function send_to_receive($txt, $user_id)
{
	$find_assign = strpos($txt, 'มอบหมายงาน');

	if($find_assign !== false)
	{
		$line_token = "VGO54TpsjKQPB2fpcY02n2SbfETsnV6bNxZPdaeLgohtqwi7wnNl6xF+9zgA5xiv8xZhkUTBjg1Hgog0E23gvI86et1O1YHqbjJZw7FEzScidVC3J7no8vS6U0oFeeuYFei0IxF1tWcOFpTxJb5z5AdB04t89/1O/w1cDnyilFU=";

		$a_data['line_token'] = $line_token;
		$a_data['line_user_id'] = $user_id;
		$a_data['line_message'] = $txt;

		$url = 'http://103.208.27.224/workflow_master4/receive/receive.php';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $a_data);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$send_result = curl_exec($curl);
		curl_close($curl);

		return true;
	}
	else
	{
		return false;
	}
}
?>