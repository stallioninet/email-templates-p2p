<?php
/*
Plugin Name:P2P Email Templates
Plugin URI: http://www.stallioni.com
Description: Sometimes you want an easy way to edit e-mail templates to send emails to users, now it's as easy as installing this plug-in. And also this plugin made only for the Document Management System Email Template
Author: stallioni Aruljothi
Version: 1.0
*/

global $wpdb, $wp_version;
define("WP_eemail_TABLE", $wpdb->prefix . "eemail_newsletter");
define("WP_eemail_TABLE_SUB", $wpdb->prefix . "eemail_newsletter_sub");
define("WP_eemail_TABLE_SCF", $wpdb->prefix . "gCF");

if ( ! defined( 'EMAIL_PLUGIN_BASENAME' ) )
	define( 'EMAIL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'EMAIL_PLUGIN_NAME' ) )
	define( 'EMAIL_PLUGIN_NAME', trim( dirname( EMAIL_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'EMAIL_PLUGIN_DIR' ) )
	define( 'EMAIL_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . EMAIL_PLUGIN_NAME );

if ( ! defined( 'EMAIL_PLUGIN_URL' ) )
	define( 'EMAIL_PLUGIN_URL', WP_PLUGIN_URL . '/' . EMAIL_PLUGIN_NAME );

function emailnews_plugin_path( $path = '' ) {
	return path_join( FIFO_PLUGIN_DIR, trim( $path, '/' ) );
}

function emailnews_plugin_url( $path = '' ) {
	return plugins_url( $path, EMAIL_PLUGIN_BASENAME );
}

function eemail_install() 
{
	global $wpdb, $wp_version;
	
	add_option('eemail_title', "Email newsletter");
	add_option('eemail_bcc', "0");
	/*add_option('eemail_widget_cap', "Subscribe your email");
	add_option('eemail_widget_txt_cap', "Enter email");
	add_option('eemail_widget_but_cap', "Submit");*/
	
	add_option('eemail_on_homepage', "YES");
	add_option('eemail_on_posts', "YES");
	add_option('eemail_on_pages', "YES");
	add_option('eemail_on_search', "NO");
	add_option('eemail_on_archives', "NO");
	
	add_option('eemail_from_name', "noreply");
	add_option('eemail_from_email', "noreply@mysitename.com");
	
	if($wpdb->get_var("show tables like '". WP_eemail_TABLE . "'") != WP_eemail_TABLE)  
	{
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `". WP_eemail_TABLE . "` (
			  `eemail_id` int(11) NOT NULL auto_increment,
			  `eemail_subject` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
			  `eemail_content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
			  `eemail_status` char(3) NOT NULL default 'YES',
			  `letter_type` varchar(20) NOT NULL default 'all',
			  `eemail_date` datetime NOT NULL default '0000-00-00 00:00:00',
			  `eemail_action` VARCHAR(100) NOT NULL,
			  PRIMARY KEY  (`eemail_id`) )
			");
		
		$sql = "insert into ".WP_eemail_TABLE.""
					. " set `eemail_subject` = '" . 'Sample Subject'
					. "', `eemail_content` = '" . 'This is sample mail content, Can add HTML content here.'
					. "', `eemail_status` = '" . 'YES'
					. "', `letter_type` = '" . 'A'
					. "', `eemail_date` = CURDATE()";
					
		$wpdb->get_results($sql);
	}
	
	if($wpdb->get_var("show tables like '". WP_eemail_TABLE_SUB . "'") != WP_eemail_TABLE_SUB)  
	{
		$wpdb->query("
			CREATE TABLE `". WP_eemail_TABLE_SUB . "` (
				`eemail_id_sub` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`eemail_name_sub` VARCHAR( 250 ) NOT NULL ,
				`eemail_email_sub` VARCHAR( 250 ) NOT NULL ,
				`eemail_status_sub` VARCHAR( 3 ) NOT NULL ,
				`eemail_date_sub` DATE NOT NULL )
			");
	}
	
	// Start v 11.0
	/*$unsubscribelink = get_option('siteurl') . "/wp-content/plugins/email-newsletter/unsubscribe/unsubscribe.php?rand=##rand##&reff=##reff##&user=##user##";
	add_option('eemail_un_option', "Yes");
	add_option('eemail_un_text', "If you do not want to receive any more newsletters, Please <a href='##LINK##'>click here</a>");
	add_option('eemail_un_link', $unsubscribelink);*/
	// End v 11.0
}

function eemail_admin_option() 
{
	echo "<div class='wrap'>";
	include_once("inc/button.php");
	echo "<h2>"; 
	// Start v 7.0
	//echo "Email newsletter";
	// End v 7.0
	echo "</h2>";
	include_once("inc/help.php");
	echo "</div>";
}

function eemail_deactivation() 
{

}

// Start v 11.0
function eemail_get_emailid($Email) 
{
	global $wpdb, $wp_version;
	$cSql = "select eemail_id_sub from ".WP_eemail_TABLE_SUB." where";
	$cSql = $cSql . " eemail_email_sub = '" . mysql_real_escape_string(trim($Email)). "'";
	$cSql = $cSql . " ORDER BY eemail_id_sub LIMIT 0, 1";
	$data = $wpdb->get_results($cSql);
	if ( ! empty($data) ) 
	{
		$data = $data[0];
		$emailid = $data->eemail_id_sub;
	}
	else
	{
		$emailid = "0";
	}
	return $emailid;
}
// End v 11.0

function eemail_send_mail($recipients = array(), $subject = '', $message = '', $type='plaintext', $sender_name='', $sender_email='', $eemail_id_new) 
{
	
	global $wpdb;
	global $user_login , $user_email;
	
	if($sender_email == "" || $sender_name == '')
	{
        get_currentuserinfo();
		$sender_email = $user_email;
		$sender_name = $user_login;
	}
	
	$eemail_from_name = get_option('eemail_from_name');
	if($eemail_from_name!="")
	{
		$sender_name = $eemail_from_name;
	}
	
	$eemail_from_email = get_option('eemail_from_email');
	if($eemail_from_email!="")
	{
		$sender_email = $eemail_from_email;
	}
	
	$num_sent = 0; // return value
	
	if ( (empty($recipients)) ) { return $num_sent; }
	
	if ('' == $message) { return false; }

	$headers  = "From: \"$sender_name\" <$sender_email>\n";
	$headers .= "Return-Path: <" . $sender_email . ">\n";
	$headers .= "Reply-To: \"" . $sender_name . "\" <" . $sender_email . ">\n";
	$headers .= "X-Mailer: PHP" . phpversion() . "\n";

	$subject = stripslashes($subject);
	$message = stripslashes($message);
	
	// Start v 11.0
	$eemail_un_option = get_option('eemail_un_option');
	if($eemail_un_option=="Yes")
	{
		$eemail_un_text = get_option('eemail_un_text');
		$eemail_un_link = get_option('eemail_un_link');
	}
	// End v 11.0
	
	if ('html' == $type) {
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: " . get_bloginfo('html_type') . "; charset=\"". get_bloginfo('charset') . "\"\n";
		$headers .= "Content-type: text/html\r\n"; 
		$mailtext = "<html><head><title>" . $subject . "</title></head><body>" . $message . "</body></html>";
	} else {
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/plain; charset=\"". get_bloginfo('charset') . "\"\n";
		$message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $message);
		$message = preg_replace('|&amp;|', '&', $message);
		$mailtext = wordwrap(strip_tags($message), 80, "\n");
	}
	//$mailtext = str_replace("\r\n", "<br />", $mailtext);
	if(count($recipients) > 0)
	{
		for ($i=0; $i<count($recipients); $i++) 
		{
			@$to = @$recipients[$i];
			if (!eemail_valid_email($to)) 
			{ 
				echo "$to email not valid<br>"; 
				continue; 
			}
			
			// Start v 11.0
		/*	$unsubscribe = "";
			if($eemail_un_option=="Yes")
			{
				$unsubscribemyguid = myguid();
				$emailid = eemail_get_emailid($to);
				//if($emailid <> "0")
				//{
					$unsubscriberand = str_replace("##rand##", $emailid, $eemail_un_link);
					$unsubscribeuser = str_replace("##user##", $to, $unsubscriberand);
					$unsubscribelink = str_replace("##reff##", $unsubscribemyguid, $unsubscribeuser);
					$unsubscribe = str_replace('##LINK##', $unsubscribelink, $eemail_un_text);
					$return_action = get_option('siteurl') . "/wp-content/plugins/email-newsletter/opennew/opennew.php?";
					$opennew = $return_action.'&rand='.$eemail_id_new.'&user='.$to.'&reff='.$unsubscribemyguid;
				//}
				//else
				//{
					//$unsubscribe = "";
				//}
				
			}
			else
			{
				$unsubscribe = "";
			}*/
			/*if ('html' == $type)
			{
				$unsubscribe = '<br><div style="background:#fe7201;border:2px solid #fe7201;width:100%;color:white;font-weight:bold">' . $unsubscribe .'</div>';
				$outputmail = '<br/><br/><div style="background:#fe7201;border:2px solid #fe7201;width:100%;color:white;font-weight:bold">'.'If you\'re having trouble viewing this message Click <a href="'.$opennew.'">here </a> </div><br/><br/>';	
				$newmailtext = $outputmail . $mailtext;
				
			}
			else
			{
				$unsubscribe = '\n' . $unsubscribe;
			}*/
			// End v 11.0
			
			//@$newheaders = $headers . "To: \"" . $to . "\" <" . $to . ">\n" ;
			@wp_mail($to, $subject, $newmailtext . $unsubscribe, $headers);
       		@$num_sent = @$num_sent + 1;
		}
	}
	return $num_sent;
}

// Start v 11.0
function myguid() 
{
	$random_id_length = 60; 
	$rnd_id = crypt(uniqid(rand(),1)); 
	$rnd_id = strip_tags(stripslashes($rnd_id)); 
	$rnd_id = str_replace(".","",$rnd_id); 
	$rnd_id = strrev(str_replace("/","",$rnd_id)); 
	$rnd_id = strrev(str_replace("$","",$rnd_id)); 
	$rnd_id = strrev(str_replace("#","",$rnd_id)); 
	$rnd_id = strrev(str_replace("@","",$rnd_id)); 
	$rnd_id = substr($rnd_id,0,$random_id_length); 
	$rnd_id = strtolower($rnd_id);
	return $rnd_id;
}
// End v 11.0

function eemail_valid_email($email) {
   $regex = '/^[A-z0-9][\w.+-]*@[A-z0-9][\w\-\.]+\.[A-z0-9]{2,6}$/';
   return (preg_match($regex, $email));
}

function eemail_get_max_bcc_recipients() {
	return get_option( 'eemail_bcc' );
}

function eemail_get_email_content($eemail_id) 
{
	global $wpdb;
	$emailrecord = array();
	$data = $wpdb->get_results("select eemail_subject,eemail_content from ".WP_eemail_TABLE." where eemail_id=$eemail_id limit 1");
	if ( !empty($data) ) 
	{
		$data = $data[0];
		$emailrecord["eemail_subject"] = $data->eemail_subject;
		$emailrecord["eemail_content"] = $data->eemail_content;
	}
	return $emailrecord;
}

/*function eemail_show() 
{
	global $wpdb, $wp_version;
	include_once("widget/widget.php");
}

function eemail_widget($args) 
{
	
	if(is_home() && get_option('eemail_on_homepage') == 'YES') { $display = "show";	}
	if(is_single() && get_option('eemail_on_posts') == 'YES') {	$display = "show"; }
	if(is_page() && get_option('eemail_on_pages') == 'YES') { $display = "show"; }
	if(is_archive() && get_option('eemail_on_search') == 'YES') { $display = "show"; }
	if(is_search() && get_option('eemail_on_archives') == 'YES') { $display = "show"; }
	if($display == "show")
	{
		extract($args);
		echo $before_widget;
		echo $before_title;
		echo get_option('eemail_title');
		echo $after_title;
		eemail_show();
		echo $after_widget;
	}
}
	
function eemail_control() 
{
	echo "Email newsletter";
}
*/

function add_admin_menu_email_compose() {
	global $wpdb;
	include_once("email-compose.php");
}
/*function add_admin_menu_assign_templates()
{
	global $wpdb;
	
  $var_email ='<div class="tool-box">
  <h2>Assign Template For ALL users</h2>';
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'all' order by eemail_id desc");
	if (!empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER A! use below form to create!</div>";
		//return;
		 $var_email.='<form name="assigntemplate" id="assigntemplate" action="" method="POST">';
		 $var_email.='Registration confirmation : 
		 <select name="reg_con" id="reg_con">
		 <option name="reg_con" value="">Select the template</option>';
		 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'all' order by eemail_id desc");
		foreach ( $data as $data )
		{ 
			if($data->eemail_status=='YES')
			{
				 $eemail_subject = $data->eemail_subject;
				 $eemail_id=$data->eemail_id;
				 $var_email.='
				  <option name="reg_con" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
			}
			
		}
		 $var_email.='</select><br/>';
		$var_email.='Registration Resetpassword : 
		 <select name="reg_reset" id="reg_con">
		 <option name="reg_reset" value="">Select the template</option>';
		 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'all' order by eemail_id desc");
		foreach ( $data as $data )
		{ 
			if($data->eemail_status=='YES')
			{
				 $eemail_subject = $data->eemail_subject;
				 $eemail_id=$data->eemail_id;
				 $var_email.='
				  <option name="reg_reset" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
			}
			
		}
		 $var_email.='</select><br/>';
		$var_email.='Registration update : 
		 <select name="reg_update" id="reg_con">
		 <option name="reg_update" value="">Select the template</option>';
		 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'all' order by eemail_id desc");
		foreach ( $data as $data )
		{ 
			if($data->eemail_status=='YES')
			{
				 $eemail_subject = $data->eemail_subject;
				 $eemail_id=$data->eemail_id;
				 $var_email.='
				  <option name="reg_update" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
			}
			
		}
		 $var_email.='</select><br/>';
		 $var_email.='<input type="submit" value="Submit All User Templates" name="alluser">';
	
		$var_email.='</form></div>';
		if(isset($_POST['alluser']))
		{
			global $wpdb,$post,$option;
			
			
			
		}
	}
	 $var_email.='<div class="tool-box">
    <h2>Assign Template For Researcher</h2>';
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'researcher' order by eemail_id desc");
	if (!empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER A! use below form to create!</div>";
		//return;
	 $var_email.='<form name="assigntemplate" id="assigntemplate" action="" method="POST">';
	 $var_email.='Receipt For Payment : 
	 <select name="res_pay" id="res_pay">
	 <option name="res_pay" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'researcher' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="reg_con" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	$var_email.='Submission Confirmation Mail : 
	 <select name="res_con" id="res_con">
	 <option name="res_con" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'researcher' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="reg_reset" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	 $var_email.='<input type="submit" value="Submit Researcher Templates" name="researcher">';
	}
	$var_email.='</form></div>';
	if(isset($_POST['researcher']))
	{
		print_r($_POST);
	}
	
	$var_email.='<div class="tool-box">
    <h2>Assign Template For Editor</h2>';
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'editor' order by eemail_id desc");
	if (!empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER A! use below form to create!</div>";
		//return;
	 $var_email.='<form name="assigntemplate" id="assigntemplate" action="" method="POST">';
	 $var_email.='Remind To Complete Edit Template : 
	 <select name="edi_rem" id="edi_rem">
	 <option name="edi_rem" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'editor' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="edi_rem" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	$var_email.='Reminder To Confirm Edit : 
	 <select name="edi_con" id="edi_con">
	 <option name="edi_con" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'researcher' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="edi_con" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	
	$var_email.='Request To Edit: 
	 <select name="edi_req" id="edi_req">
	 <option name="edi_req" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'editor' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="edi_req" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	 
	 $var_email.='Thankyou For Edited Documents: 
	 <select name="edi_thq" id="edi_thq">
	 <option name="edi_thq" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'editor' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="edi_thq" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	 $var_email.='<input type="submit" value="Submit Researcher Templates" name="editor">';
	}
	$var_email.='</form></div>';
	if(isset($_POST['editor']))
	{
		print_r($_POST);
	}
	/********************************* Coorginator Template Assign**************************/
	/*$var_email.='<div class="tool-box">
    <h2>Assign Template For Coorginator</h2>';
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'coorginator' order by eemail_id desc");
	if (!empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER A! use below form to create!</div>";
		//return;
	 $var_email.='<form name="assigntemplate" id="assigntemplate" action="" method="POST">';
	 $var_email.='Remind To Complete Edit Template : 
	 <select name="cog_rem" id="cog_rem">
	 <option name="cog_rem" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'coorginator' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="cog_rem" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	$var_email.='New Order Receive : 
	 <select name="edi_con" id="edi_con">
	 <option name="edi_con" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'coorginator' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="edi_con" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	
	$var_email.='Request To Edit: 
	 <select name="edi_req" id="edi_req">
	 <option name="edi_req" value="">Select the template</option>';
	 $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'coorginator' order by eemail_id desc");
    foreach ( $data as $data )
    { 
		if($data->eemail_status=='YES')
		{
			 $eemail_subject = $data->eemail_subject;
			 $eemail_id=$data->eemail_id;
			 $var_email.='
			  <option name="edi_req" value="'.$eemail_id.'">'. $eemail_subject.'</option>';
		}
		
	}
	 $var_email.='</select><br/>';
	 $var_email.='<input type="submit" value="Submit Researcher Templates" name="coorginator">';
	}
	$var_email.='</form></div>';
	if(isset($_POST['editor']))
	{
		print_r($_POST);
	}
	echo  $var_email;
}
*/

// Start v 7.0
function add_admin_menu_export_csv() {
	//global $wpdb;
	//include_once("export-csv.php");
}
function add_admin_menu_option() 
{
	add_menu_page( __( 'Email Templates', 'email-newsletter' ), __( 'Email Templates', 'email-newsletter' ), 'administrator', 'email-newsletter', 'eemail_admin_option' );
	add_submenu_page('email-newsletter', 'Compose Email', 'Compose Email', 'administrator', 'add_admin_menu_email_compose', 'add_admin_menu_email_compose');
	//add_submenu_page('email-newsletter', 'Assign Template', 'Assign Template', 'administrator', 'add_admin_menu_assign_templates', 'add_admin_menu_assign_templates');
}

add_action('admin_menu', 'add_admin_menu_option');
register_activation_hook(__FILE__, 'eemail_install');
register_deactivation_hook(__FILE__, 'eemail_deactivation');
?>
