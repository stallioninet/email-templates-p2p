<div class="wrap">
  <?php
  	include_once("inc/button.php");
  	global $wpdb;
    $mainurl = get_option('siteurl')."/wp-admin/admin.php?page=add_admin_menu_email_compose";
    $DID=@$_GET["DID"];
    $AC=@$_GET["AC"];
    $submittext = "Insert Message";
	if($AC <> "DEL" and trim(@$_POST['eemail_subject']) <>"")
    {
			if($_POST['eemail_id'] == "" )
			{
					$sql = "insert into ".WP_eemail_TABLE.""
					. " set `eemail_subject` = '" . mysql_real_escape_string(trim($_POST['eemail_subject']))
					. "', `eemail_content` = '" . mysql_real_escape_string(trim($_POST['eemail_content']))
					. "', `eemail_status` = '" . mysql_real_escape_string(trim($_POST['eemail_status']))
					. "', `letter_type` = '" . mysql_real_escape_string(trim($_POST['lettertype']))
					. "', `eemail_action` = '" . mysql_real_escape_string(trim($_POST['select_template']))
					. "', `eemail_date` = CURDATE()";
			}
			else
			{
					$sql = "update ".WP_eemail_TABLE.""
					. " set `eemail_subject` = '" . mysql_real_escape_string(trim($_POST['eemail_subject']))
					. "', `eemail_content` = '" . mysql_real_escape_string(trim($_POST['eemail_content']))
					. "', `eemail_status` = '" . mysql_real_escape_string(trim($_POST['eemail_status']))
					. "', `letter_type` = '" . mysql_real_escape_string(trim($_POST['lettertype']))
					. "', `eemail_action` = '" . mysql_real_escape_string(trim($_POST['select_template']))
					. "' where `eemail_id` = '" . $_POST['eemail_id'] 
					. "'";	
			}

			$wpdb->get_results($sql);
    }
    
    if($AC=="DEL" && $DID > 0)
    {
        $wpdb->get_results("delete from ".WP_eemail_TABLE." where eemail_id=".$DID);
    }
    
    if($DID<>"" and $AC <> "DEL")
    {
        $data = $wpdb->get_results("select * from ".WP_eemail_TABLE." where eemail_id=$DID limit 1");
        if ( empty($data) ) 
        {
           echo "<div id='message' class='error'><p>No data available! use below form to create!</p></div>";
           return;
        }
        $data = $data[0];
        if ( !empty($data) ) $eemail_id_x = htmlspecialchars(stripslashes($data->eemail_id)); 
		if ( !empty($data) ) $eemail_subject_x = htmlspecialchars(stripslashes($data->eemail_subject)); 
        if ( !empty($data) ) $eemail_content_x = htmlspecialchars(stripslashes($data->eemail_content));
		if ( !empty($data) ) $eemail_status_x = htmlspecialchars(stripslashes($data->eemail_status));
		if ( !empty($data) ) $letter_type_x = htmlspecialchars(stripslashes($data->letter_type));
		if ( !empty($data) ) $action_status = htmlspecialchars(stripslashes($data->eemail_action));
        $submittext = "Update Message";
    }
    ?>
  <h2>Email Templates(Compose email)</h2>
  <script language="JavaScript" src="<?php echo emailnews_plugin_url('inc/setting.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/email-templates/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript">
    tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "visual",
        theme : "advanced",
        theme_advanced_disable : "styleselect",
        relative_urls : false,
        remove_script_host : false,
        theme_advanced_buttons3: "forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_resizing : true,
        theme_advanced_statusbar_location: "bottom",
        document_base_url : "<?php echo get_option('home'); ?>/",
        //content_css : "<?php echo get_option('blogurl'); ?>/wp-content/plugins/newsletter/editor.css?" + new Date().getTime()
		content_css : "<?php echo get_option('blogurl'); ?>//wp-content/plugins/email-templates/tiny_mce/editor.css?" + new Date().getTime()
    });
</script>

  <form name="form_eemail" method="post" action="<?php echo @$mainurl; ?>" onsubmit="return eemail_submit()"  >
    <table width="100%">
      <tr>
        <td colspan="2" align="left" valign="middle">Enter email subject:</td>
      </tr>
      <tr>
        <td colspan="2" align="left" valign="middle"><input name="eemail_subject" type="text" id="eemail_subject" value="<?php echo @$eemail_subject_x; ?>" size="90" /></td>
      </tr>
      <tr>
        <td colspan="2" align="left" valign="middle">Enter email body (You use HTML content)</td>
      </tr>
      <tr>
      	<td colspan="2" align="left" valign="middle"><?php echo '<h3>Shortcodes</h3>  
	    <strong>Researcher codes :</strong> %salutation%   %last-name%  %user-name%  %user-email%  %manuscript-name%  %field%<br/>
		<strong>Coordinator codes :</strong>  %c-salutation%   %c-last-name%  %coordinate-name%   %manuscript-name%  %field% <br/>
		<strong>Editor codes :</strong> %e-salutation%   %e-last-name% %e-user-name%   %manuscript-name%  %field% <br/>
		'?></td>
      </tr>
      <tr>
	   <?php //the_editor(@$eemail_content_x);
	  //$nc->editor('eemail_content'); ?>
	 <?php //wp_editor( $ewd_skills, 'ewd_skills_description' ); ?>
	 <?php //echo apply_filters('the_content', $description); ?>
        <td colspan="2" align="left" valign="middle"><textarea name="eemail_content" cols="140" rows="25" id="eemail_content" class="visual" ><?php echo @$eemail_content_x; ?></textarea></td>
      </tr
      ><tr>
        <td align="left" valign="middle">Display Status:</td>
        <td align="left" valign="middle">Email user Type</td>
        
      </tr>
      <tr>
        <td width="22%" align="left" valign="middle"><select name="eemail_status" id="eemail_status">
            <option value="">Select</option>
            <option value='YES' <?php if(@$eemail_status_x=='YES') { echo 'selected' ; } ?>>Yes</option>
            <option value='NO' <?php if(@$eemail_status_x=='NO') { echo 'selected' ; } ?>>No</option>
          </select>        </td>
        <td width="78%" align="left" valign="middle">
		<input type="radio" name="lettertype"  id="lettertype" value="all" <?php if($letter_type_x == 'all')echo "checked=checked";else echo "checked=checked"; ?>/>All User
		<input type="radio" name="lettertype" id="lettertype" value="editor" <?php if($letter_type_x == 'editor')echo "checked=checked";?>/>Editor
        <input type="radio" name="lettertype" id="lettertype" value="coordinator" <?php if($letter_type_x == 'coordinator')echo "checked=checked";?>/>coordinator
        <input type="radio" name="lettertype" id="lettertype" value="researcher" <?php if($letter_type_x == 'researcher')echo "checked=checked";?>/>Researcher
		</td>
      </tr>
      <tr>
      <tr>
     
		 <td align="left" valign="middle">Select the Template Actions: </td>
		<td align="left" valign="middle">
		<select name="select_template" id="select_template">
	    <option name="select_template" value="">Select Action</option>
	 <optgroup label="All User">
		<option name="select_template" value="Registration confirmation" <?php if($action_status == 'Registration confirmation')echo "selected=selected";else echo ""; ?>>Registration confirmation</option>
		<option name="select_template" value="Registration Resetpassword" <?php if($action_status == 'Registration Resetpassword')echo "selected=selected";else echo ""; ?>>Registration Resetpassword</option>
		<option name="select_template" value="Registration update" <?php if($action_status == 'Registration update')echo "selected=selected";else echo ""; ?>>Registration update</option>
  	 </optgroup>
	  <optgroup label="Researcher">
		<option name="select_template" value="Receipt For Payment" <?php if($action_status == 'Receipt For Payment')echo "selected=selected";else echo ""; ?>>Receipt For Payment</option>
		<option name="select_template" value="Submission Confirmation Mail" <?php if($action_status == 'Submission Confirmation Mail')echo "selected=selected";else echo ""; ?>>Submission Confirmation Mail</option>
		<option name="select_template" value="Close Order after completes" <?php if($action_status == 'Close Order after completes')echo "selected=selected";else echo ""; ?>>Close Order after completes</option>
	  </optgroup>
	  <optgroup label="Editor">
		<option name="select_template" value="Reminder To Complete Uploaded Document" <?php if($action_status == 'Reminder To Complete Uploaded Document')echo "selected=selected";else echo ""; ?>> Reminder To Complete Uploaded Document</option>
		<option name="select_template" value="Reminder To Confirm Edit" <?php if($action_status == 'Reminder To Confirm Edit')echo "selected=selected";else echo ""; ?>>Reminder To Confirm Edit</option>
		<option name="select_template" value="Sends Cancellation Email to Assigned Editor" <?php if($action_status == 'Sends Cancellation Email to Assigned Editor')echo "selected=selected";else echo ""; ?>> Sends Cancellation Email to Assigned Editor</option>
		<option name="select_template" value="Thankyou For Edited Documents" <?php if($action_status == 'Thankyou For Edited Documents')echo "selected=selected";else echo ""; ?>>  Thankyou For Edited Documents</option>
        <option name="select_template" value="Re-edit the Edited Document Template" <?php if($action_status == 'Re-edit the Edited Document Template')echo "selected=selected";else echo ""; ?>>Re-edit the Edited Document Template</option>
	  </optgroup>
	  <optgroup label="Coordinator">
		<option name="select_template" value="New Order Receive Template" <?php if($action_status == 'New Order Receive Template')echo "selected=selected";else echo ""; ?>>New Order Receive Template</option>
        <option name="select_template" value="Editor Reject the Assigned Document" <?php if($action_status == 'Editor Reject the Assigned Document')echo "selected=selected";else echo ""; ?>>Editor Reject the Assigned Document</option>
        <option name="select_template" value="Editor Uploaded Completed Document Template" <?php if($action_status == 'Editor Uploaded Completed Document Template')echo "selected=selected";else echo ""; ?>>Editor Uploaded Completed Document Template</option>
	  </optgroup>
	  </select></td>
    
      </tr>
        <td height="35" colspan="2" align="left" valign="bottom"><table width="100%">
            <tr>
              <td width="50%" align="left">
			  	<input name="publish" lang="publish" class="button-primary" value="<?php echo @$submittext?>" type="submit" />
                <input name="publish" lang="publish" class="button-primary" onclick="_eemail_redirect()" value="Cancel" type="button" />              
			  </td>
              <td width="50%" align="right">&nbsp;</td>
            </tr>
          </table></td>
      </tr>
      <input name="eemail_id" id="eemail_id" type="hidden" value="<?php echo @$eemail_id_x; ?>">
    </table>
  </form>
  <div class="tool-box">
  <h2>Template For ALL users</h2>
    <?php
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'all' order by eemail_id desc");
	if ( empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER A! use below form to create!</div>";
		//return;
	}
	?>
    <form name="frm_eemail_display" method="post" action="">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th width="57%" align="left" scope="col">Subject
              </th>
              <th width="20%" align="left" scope="col">Assigned To
              </th>
            <th width="9%" align="left" scope="col">Status </th>
            <th width="14%" align="left" scope="col">Action </th>
                  </tr>
        </thead>
        <?php 
        $i = 0;
        foreach ( $data as $data ) { 
		if($data->eemail_status=='YES') { $displayisthere="True"; }
        ?>
        <tbody>
          <tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_subject)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_action)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_status)); ?></td>
			<td align="left" valign="middle"><a href="admin.php?page=add_admin_menu_email_compose&DID=<?php echo($data->eemail_id); ?>">Edit</a> &nbsp; <a onClick="javascript:_eemail_delete('<?php echo($data->eemail_id); ?>')" href="javascript:void(0);">Delete</a> </td>
          </tr>
        </tbody>
        <?php $i = $i+1; } ?>
        <?php if($displayisthere<>"True") { ?>
        <tr>
          <td colspan="3" align="center" style="color:#FF0000" valign="middle">No message available with display status 'Yes'!' </td>
        </tr>
        <?php } ?>
      </table>
    </form>
  </div>
  <div style="clear:both;"></div>
    <div class="tool-box">
 	 <h2>Template For Editor</h2>
    <?php
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'editor' order by eemail_id desc");
	if ( empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER B! use below form to create!</div>";
		//return;
	}
	?>
   
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
           <th width="57%" align="left" scope="col">Subject
              </th>
              <th width="20%" align="left" scope="col">Assigned To
              </th>
            <th width="9%" align="left" scope="col">Status </th>
            <th width="14%" align="left" scope="col">Action </th>         </tr>
        </thead>
        <?php 
        $i = 0;
        foreach ( $data as $data ) { 
		if($data->eemail_status=='YES') { $displayisthere="True"; }
        ?>
        <tbody>
          <tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_subject)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_action)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_status)); ?></td>
			<td align="left" valign="middle"><a href="admin.php?page=add_admin_menu_email_compose&DID=<?php echo($data->eemail_id); ?>">Edit</a> &nbsp; <a onClick="javascript:_eemail_delete('<?php echo($data->eemail_id); ?>')" href="javascript:void(0);">Delete</a> </td>
          </tr>
        </tbody>
        <?php $i = $i+1; } ?>
        <?php if($displayisthere<>"True") { ?>
        <tr>
          <td colspan="3" align="center" style="color:#FF0000" valign="middle">No message available with display status 'Yes'!' </td>
        </tr>
        <?php } ?>
      </table>
    </form>
  </div>
  
  <div class="tool-box">
 	 <h2>Template For  Coordinator</h2>
    <?php
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'coordinator' order by eemail_id desc");
	if ( empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER B! use below form to create!</div>";
		//return;
	}
	?>
   
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th width="57%" align="left" scope="col">Subject
              </th>
              <th width="20%" align="left" scope="col">Assigned To
              </th>
            <th width="9%" align="left" scope="col">Status </th>
            <th width="14%" align="left" scope="col">Action </th>        </tr>
        </thead>
        <?php 
        $i = 0;
        foreach ( $data as $data ) { 
		if($data->eemail_status=='YES') { $displayisthere="True"; }
        ?>
        <tbody>
          <tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_subject)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_action)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_status)); ?></td>
			<td align="left" valign="middle"><a href="admin.php?page=add_admin_menu_email_compose&DID=<?php echo($data->eemail_id); ?>">Edit</a> &nbsp; <a onClick="javascript:_eemail_delete('<?php echo($data->eemail_id); ?>')" href="javascript:void(0);">Delete</a> </td>
          </tr>
        </tbody>
        <?php $i = $i+1; } ?>
        <?php if($displayisthere<>"True") { ?>
        <tr>
          <td colspan="3" align="center" style="color:#FF0000" valign="middle">No message available with display status 'Yes'!' </td>
        </tr>
        <?php } ?>
      </table>
    </form>
  </div>
  
  <div class="tool-box">
 	 <h2>Template For  Researcher</h2>
    <?php
	$data = $wpdb->get_results("select * from ".WP_eemail_TABLE." WHERE letter_type = 'researcher' order by eemail_id desc");
	if ( empty($data) ) 
	{ 
		//echo "<div id='message' class='error'>No data available in NEWS LETTER B! use below form to create!</div>";
		//return;
	}
	?>
    
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th width="57%" align="left" scope="col">Subject
              </th>
              <th width="20%" align="left" scope="col">Assigned To
              </th>
            <th width="9%" align="left" scope="col">Status </th>
            <th width="14%" align="left" scope="col">Action </th>        </tr>
        </thead>
        <?php 
        $i = 0;
        foreach ( $data as $data ) { 
		if($data->eemail_status=='YES') { $displayisthere="True"; }
        ?>
        <tbody>
          <tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_subject)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_action)); ?></td>
            <td align="left" valign="middle"><?php echo(stripslashes($data->eemail_status)); ?></td>
			<td align="left" valign="middle"><a href="admin.php?page=add_admin_menu_email_compose&DID=<?php echo($data->eemail_id); ?>">Edit</a> &nbsp; <a onClick="javascript:_eemail_delete('<?php echo($data->eemail_id); ?>')" href="javascript:void(0);">Delete</a> </td>
          </tr>
        </tbody>
        <?php $i = $i+1; } ?>
        <?php if($displayisthere<>"True") { ?>
        <tr>
          <td colspan="3" align="center" style="color:#FF0000" valign="middle">No message available with display status 'Yes'!' </td>
        </tr>
        <?php } ?>
      </table>
    </form>
  </div>

</div>
<div style="clear:both;"></div>