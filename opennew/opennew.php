<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>News Letter</title>
<style>
body
{
line-height: 1;
margin-left: 122px;
background: url('../images/bg1.png') repeat scroll center top transparent;
color:#000000;
font: 14px Arial,Helvetica,sans-serif;
cursor: default !important;
}
h1
{
color:#1a7468;
}
p
{
text-align:justify;
text-indent:15px;
}
#secondary
{
	margin-left: 16px;
}
#primary
{
	margin: 0 -29.4% 0 0;
}
</style>
</head>
<body>
<?php
$referer = @$_SERVER['HTTP_REFERER'];
//if($referer <> "")
//{
	$rand = @$_GET["rand"];
	$user = @$_GET["user"];
	$reff = @$_GET["reff"];
	$eemail_id = @$_GET["rand"];
	if (!is_numeric($rand)) 
	{
    	echo "Unexpected error occurred: 1";
		die;
	}
	
	if ($user == "") 
	{
		echo "Unexpected error occurred: 2";
		die;
	}
	
	if ($reff == "") 
	{
		echo "Unexpected error occurred: 3";
		die;
	}
	
	$eemail_abspath = dirname(__FILE__);
	$eemail_abspath_1 = str_replace('wp-content/plugins/email-newsletter/opennew', '', $eemail_abspath);
	$eemail_abspath_1 = str_replace('wp-content\plugins\email-newsletter\opennew', '', $eemail_abspath_1);
	require_once($eemail_abspath_1 .'wp-config.php');
	
	global $wpdb, $wp_version;
	$data = $wpdb->get_var("select eemail_content from ".WP_eemail_TABLE." where eemail_id=$eemail_id limit 1");
	if ( ! empty($data) ) 
	{
		$message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $data);
		$message = preg_replace('|&amp;|', '&', $message);
		//echo $data->eemail_content;
		$message = stripslashes($message);
		echo $message;
	}
	else
	{
		
		header("Location: ".home_url().'/unsubscribe-error');
	}
//}
//else
//{
//	echo "Unexpected error occurred: 4";
//}
?>
</body>
</html>