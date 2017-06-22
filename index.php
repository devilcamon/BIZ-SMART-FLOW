<?php
include "w_top.php";
?>
<div class="container">
	<?php
echo "Hello World.<br>";

function print_pre($txt)
{
	echo "<pre>";
	print_r($txt);
	echo "</pre>";
}

echo $txt = "มอบหมายงาน ทดสอบการมอบหมายงานให้คนอื่น ##BizSmartFlow @@NS!!";
echo "<br>";
$find_assign = strpos($txt, 'มอบหมายงาน');

if($find_assign !== false)
{
	echo $assign = iconv_substr($txt, 10, iconv_strlen($txt, 'utf-8'), 'utf-8');

	preg_match_all("/(##)([a-zA-Z0-9_]+)( )/", $assign, $match_project, PREG_SET_ORDER);
	preg_match_all("/(@@)([a-zA-Z0-9_]+)(!!)/", $assign, $match_to, PREG_SET_ORDER);

	$assign_project = $match_project[0][0];
	$assign_to = $match_to[0][0];

	$array_search = array($assign_project, $assign_to);
	$array_replace = array('', '');
	echo "<br>";
	echo $assign_detail = trim(str_replace($array_search, $array_replace, $assign));
}

?>

<br>
<a href="<?php echo $short_url; ?>" target="_blank">Click here.</a>
</div>
<?php include "w_bottom_js.php";?>
<?php include "w_bottom.php";?>
