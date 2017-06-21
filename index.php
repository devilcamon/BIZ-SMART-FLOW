<?php
include "w_top.php";
echo "Hello World.";

include "short_url.php";

$url = new short_url();
$url->set_client_id('f4c11aa908316736555b8fadd2c0c63bf05f078e');
$url->set_client_secret('6bdedd6d4efb71673da697925f8366a392c27127');
$url->set_generic_access_token('5d249ec8bc1230a16edfcdc89ed70ca940bdd409');
$url->set_username('devilcamon');
$url->set_api_key('R_184e3c7406bd4f1fb18ccd8ce3515c6e');

//$url->get_token_access();
//$short_url = $url->get_short_link('http://203.150.225.80/bizpotential/workflow/workflow.php?W=242&search=Y&PROBLEM_SYSTEM=&WFR_ID=&PROBLEM=&WF_DET_NEXT=304');

//echo $short_url;
?>

<a href="<?php echo $short_url; ?>" target="_blank">Click here.</a>

<?php include "w_bottom_js.php";?>
<?php include "w_bottom.php";?>