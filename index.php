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
if(isset($_GET['txt']))
{
	$txt = urldecode($_GET['txt']);

}
	//$txt = "มอบหมายงาน ทดสอบการมอบหมายงานให้คนอื่น ##BizSmartFlow @@NS!!";
echo $txt;
?>
	<form class="form-horizontal" enctype="application/x-www-form-urlencoded">
		<div class="form-group">
			<div class="col-xs-12">
				<textarea name="txt" id="txt" rows="3" class="form-control" ><?php echo $txt; ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-12">
				<button class="btn btn-primary btn-lg">ส่ง</button>
			</div>
		</div>
	</form>
	<?php
$xxx = assign_job_from_line($txt);
print_pre($xxx);

function assign_job_from_line($txt)
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

		$data['assign_project'] = $assign_project;
		$data['assign_to'] = $assign_to;
		$data['assign_detail'] = $assign_detail;

		return $data;
	}
	else
	{
		return false;
	}
}

?>

<br>
<a href="<?php echo $short_url; ?>" target="_blank">Click here.</a>
</div>
<?php include "w_bottom_js.php";?>
<?php include "w_bottom.php";?>
