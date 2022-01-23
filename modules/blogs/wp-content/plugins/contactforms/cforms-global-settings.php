<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';


// SMPT sever configured?
$smtpsettings=explode('$#$',get_option('cforms_smtp'));

### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die(__('Access Denied','cforms'));
}



// if all data has been erased quit
if ( get_option('cforms_formcount') == '' ){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>
	<?php
	die;
}


if( isset($_REQUEST['deleteall']) ) {  // erase all cforms data

	for ( $z=1; $z<= (int) get_option('cforms_formcount'); $z++ ) {
	
	    $j = ($z==1)?'':$z;
	
		  for ( $i=1; $i<=get_option('cforms'.$j.'_count_fields'); $i++)  //now delete all fields from last form
			delete_option('cforms'.$j.'_count_field_'.$i);
			
			delete_option('cforms'.$j.'_count_fields');
			delete_option('cforms'.$j.'_required');
			delete_option('cforms'.$j.'_emailrequired');
			
			delete_option('cforms'.$j.'_confirm');
			delete_option('cforms'.$j.'_tracking');
			delete_option('cforms'.$j.'_ajax');
			delete_option('cforms'.$j.'_fname');
			delete_option('cforms'.$j.'_csubject');
			delete_option('cforms'.$j.'_cmsg');
			delete_option('cforms'.$j.'_cmsg_html');
			delete_option('cforms'.$j.'_email');
			delete_option('cforms'.$j.'_bcc');
			delete_option('cforms'.$j.'_header');
			delete_option('cforms'.$j.'_header_html');
			delete_option('cforms'.$j.'_formdata');
			
			delete_option('cforms'.$j.'_subject');
			delete_option('cforms'.$j.'_submit_text');
			delete_option('cforms'.$j.'_success');
			delete_option('cforms'.$j.'_failure');
			delete_option('cforms'.$j.'_working');
			delete_option('cforms'.$j.'_popup');

			delete_option('cforms'.$j.'_redirect');
			delete_option('cforms'.$j.'_redirect_page');
			delete_option('cforms'.$j.'_action');
			delete_option('cforms'.$j.'_action_page');
			
			delete_option('cforms'.$j.'_upload_dir');
			delete_option('cforms'.$j.'_upload_ext');
			delete_option('cforms'.$j.'_upload_size');
		
  	}
	
		delete_option('cforms_css');
		delete_option('cforms_labelID');
		delete_option('cforms_linklove');

		delete_option('cforms_sec_qa');
		delete_option('cforms_show_quicktag');
    	delete_option('cforms_codeerr');
      	delete_option('cforms_database');
      	delete_option('cforms_smtp');
		
		delete_option('cforms_upload_err1');
		delete_option('cforms_upload_err2');
		delete_option('cforms_upload_err3');
		delete_option('cforms_upload_err4');
		delete_option('cforms_upload_err5');

		delete_option('cforms_formcount');
		
		$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
		$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");

	?>
	<div id="message" class="updated fade"><p><strong><?php _e('All cforms related data has been deleted.', 'cforms') ?></strong></p></div>

	<div class="wrap">
	<h2><?php _e('Thank you for using cforms.', 'cforms') ?></h2>
	<p><?php _e('You can go directly to your <strong>Plugins</strong> tab and disable the plugin!', 'cforms') ?></p>
	</div>
	<?php
	
	die;


} else if ( isset($_REQUEST['deletetables']) ) {

	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");

	update_option('cforms_database', '0');

	?>
	<div id="message" class="updated fade"><p><strong><?php _e('cforms tracking tables (<code>cformssubmissions</code> & <code>cformsdata</code>) have been deleted.<br />Please backup/clean-up your upload directory, chances are that when you turn tracking back on, existing (older) attachments may be <u>overwritten</u>!<br /><small>(provided your form includes a file upload field)</small>', 'cforms') ?></strong></p></div>
	<?php

}



// Update Settings
if( isset($_REQUEST['Submit1']) || isset($_REQUEST['Submit2']) || isset($_REQUEST['Submit3']) || isset($_REQUEST['Submit4']) || isset($_REQUEST['Submit5']) ) {

//	update_option('cforms_linklove', $_REQUEST['cforms_linklove']?'1':'0');
	update_option('cforms_show_quicktag', $_REQUEST['cforms_show_quicktag']?'1':'0');
	update_option('cforms_sec_qa', $_REQUEST['cforms_sec_qa'] );
	update_option('cforms_codeerr', $_REQUEST['cforms_codeerr']);
	update_option('cforms_database', $_REQUEST['cforms_database']?'1':'0');

	$smtpsettings[0]=$_REQUEST['cforms_smtp_onoff']?'1':'0';
	$smtpsettings[1]=$_REQUEST['cforms_smtp_host'];
	$smtpsettings[2]=$_REQUEST['cforms_smtp_user'];
	if ( !preg_match('/^\*+$/',$_REQUEST['cforms_smtp_pass']) ) {
		$smtpsettings[3]=$_REQUEST['cforms_smtp_pass'];
		}
	update_option('cforms_smtp', implode('$#$',$smtpsettings) );

	update_option('cforms_upload_err1', $_REQUEST['cforms_upload_err1']);
	update_option('cforms_upload_err2', $_REQUEST['cforms_upload_err2']);
	update_option('cforms_upload_err3', $_REQUEST['cforms_upload_err3']);
	update_option('cforms_upload_err4', $_REQUEST['cforms_upload_err4']);
	update_option('cforms_upload_err5', $_REQUEST['cforms_upload_err5']);

	// Setup database tables ?
	if ( isset($_REQUEST['cforms_database']) && $_REQUEST['cforms_database_new']=='true' ) {
	
		if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") <> $wpdb->cformssubmissions ){
			
			$sql = "CREATE TABLE " . $wpdb->cformssubmissions . " (
					  id int(11) unsigned auto_increment,
					  form_id varchar(3) default '',
					  sub_date timestamp,
					  email varchar(40) default '', 
					  ip varchar(15) default '', 
					  PRIMARY KEY  (id) );";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
			
			$sql = "CREATE TABLE " . $wpdb->cformsdata . " (
					  f_id int(11) unsigned auto_increment primary key, 
					  sub_id int(11) unsigned NOT NULL, 
					  field_name varchar(100) NOT NULL default '', 
					  field_val text);";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);

			?>
			<div id="message" class="updated fade"><p><strong><?php _e('cforms tracking tables (<code>cformssubmissions</code> & <code>cformsdata</code>) have been created.', 'cforms') ?></strong></p></div>
			<?php
		} else {

			$sets = $wpdb->get_var("SELECT count(id) FROM $wpdb->cformssubmissions");
			?>
			<div id="message" class="updated fade"><p><strong><?php _e('Found existing cforms tracking tables with', 'cforms') ?>
				<?php echo $sets; ?> <?php _e('records.', 'cforms') ?></strong></p></div>
			<?php	
		}
	}
	
}


?>

<div class="wrap" id="top">
<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p2-title.jpg" alt=""/>

	<p><?php _e('All settings and configuration options on this page apply to all forms.', 'cforms') ?></p>

	<form id="cformsdata" name="mainform" method="post" action="">
	 <input type="hidden" name="cforms_database_new" value="<?php if(get_option('cforms_database')=="0") echo 'true'; ?>"/>

		<fieldset id="smtp" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit5" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#smtp';"/><?php _e('SMTP Server Settings', 'cforms') ?></p>

			<p><?php _e('In case your web hosting provider doesn\'t support the <strong>native PHP mail()</strong> command feel free to configure cforms to utilize an SMTP mail server to deliver the emails.', 'cforms') ?></p>

			<p class="ex"><?php echo str_replace('[url]','http://phpmailer.sourceforge.net/',__('This requires either the latest WP code (coming with the <strong>phpmailer class</strong>) or the <a href="[url]">respective files</a> to be copied to your wp-include/ directory. Further, due to the <u>limitations</u> of <em>phpmailer</em> neither <strong>SSL</strong> nor <strong>TLS</strong> are supported for authentication, simply spoken this option may or may not work for your specific SMTP server.', 'cforms')) ?></p>

			<?php
				if ( $smtpsettings[0]=='1' ) {
					if ( !file_exists(ABSPATH . WPINC . '/class-phpmailer.php') )
						echo '<div id="message" class="updated fade"><p>'.__('<strong>ERROR</strong>: Can\'t find "<strong>class-phpmailer.php</strong>" in your WP include directory!<br/>If you intend to use an specific STMP server, please make sure that your WP installation is up-to-date and supports the <i>phpmailer</i> class.', 'cforms').'</p></div>';
				}
			?>
			
			<div class="optionsbox">
				<div class="optionsboxL"></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_smtp_onoff" name="cforms_smtp_onoff" <?php if($smtpsettings[0]=="1") echo "checked=\"checked\""; ?>/><label for="cforms_smtp_onoff"><?php _e('Enable specific SMTP server for relaying emails.', 'cforms') ?></label></div>
			</div>

			<div class="optionsbox" style="margin-top:15px;">
				<div class="optionsboxL"><label for="cforms_smtp_host"><strong><?php _e('SMTP server address', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><input type="text" id="cforms_smtp_host" name="cforms_smtp_host" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[1])); ?>"/></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_smtp_user"><strong><?php _e('Username', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><input type="text" id="cforms_smtp_user" name="cforms_smtp_user" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[2])); ?>"/></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_smtp_pass"><strong><?php _e('Password', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><input type="text" id="cforms_smtp_pass" name="cforms_smtp_pass" value="<?php echo str_repeat('*',strlen($smtpsettings[3])); ?>"/><p style="width:280px"><?php _e('Please note that in a normal WP environment you do not need to configure these settings!', 'cforms') ?></p></div>
			</div>
			
		</fieldset>


		<fieldset id="upload" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit3" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#upload';"/><?php _e('Global File Upload Settings', 'cforms') ?></p>

			<p>
				<?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#upload',__('Configure and double-check these settings in case you are adding a "<code class="codehighlight">File Upload Box</code>" to your form (also see the <a href="[url]">Help!</a> for further information).', 'cforms')); ?>
				<?php echo str_replace('[url]','?page='.$plugindir.'/cforms-options.php#fileupload',__('Form specific settings (directory path etc.) have been moved to <a href="[url]">here</a>.', 'cforms')); ?>
			</p>

			<p class="ex"><?php _e('Also, note that by adding a <em>File Upload Box</em> to your form, the Ajax (if enabled) submission method will (automatically) <strong>gracefully degrade</strong> to the standard method, due to general HTML limitations.', 'cforms') ?></p>

			<p style="padding-top:15px;"><?php _e('Specify the error messages shown in case something goes awry:', 'cforms') ?></p>

			<div class="optionsbox" style="margin-top:15px;">
				<div class="optionsboxL"><label for="cforms_upload_err5"><strong><?php _e('File type not allowed', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><textarea class="errmsgbox" name="cforms_upload_err5" id="cforms_upload_err5" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err5'))); ?></textarea></div>
			</div>

			<div class="optionsbox" style="margin-top:3px;">
				<div class="optionsboxL"><label for="cforms_upload_err1"><strong><?php _e('Generic (unknown) error', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><textarea class="errmsgbox" name="cforms_upload_err1" id="cforms_upload_err1" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err1'))); ?></textarea></div>
			</div>
			
			<div class="optionsbox" style="margin-top:3px;">
				<div class="optionsboxL"><label for="cforms_upload_err2"><strong><?php _e('File is empty', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><textarea class="errmsgbox" name="cforms_upload_err2" id="cforms_upload_err2" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err2'))); ?></textarea></div>
			</div>

			<div class="optionsbox" style="margin-top:3px;">
				<div class="optionsboxL"><label for="cforms_upload_err3"><strong><?php _e('File size too big', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><textarea class="errmsgbox" name="cforms_upload_err3" id="cforms_upload_err3" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err3'))); ?></textarea></div>
			</div>

			<div class="optionsbox" style="margin-top:3px;">
				<div class="optionsboxL"><label for="cforms_upload_err4"><strong><?php _e('Error during upload', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><textarea class="errmsgbox" name="cforms_upload_err4" id="cforms_upload_err4" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err4'))); ?></textarea></div>
			</div>

		</fieldset>


		<fieldset id="wpeditor" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><?php _e('WP Editor Button support', 'cforms') ?></p>

			<p><?php _e('If you would like to use editor buttons to insert your cforms please enable them below.', 'cforms') ?></p>
	
			<div class="optionsbox">
				<div class="optionsboxL"><img src="<?php echo $cforms_root; ?>/images/button.gif" /></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_show_quicktag" name="cforms_show_quicktag" <?php if(get_option('cforms_show_quicktag')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_show_quicktag"><strong><?php _e('Enable TinyMCE', 'cforms') ?></strong> <?php _e('& Code editor buttons', 'cforms') ?></label></div>
			</div>
		</fieldset>


		<fieldset id="visitorv" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit1" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#wpeditor';"/><?php _e('Visitor Verification Settings (Q&A)', 'cforms') ?></p>

			<p><?php _e('Getting a lot of <strong>SPAM</strong>? Use these Q&A\'s to counteract spam and ensure it\'s a human submitting the form. To use in your form, add the corresponding input field "<code class="codehighlight">Visitor verification</code>" preferably in its own FIELDSET!', 'cforms') ?></p>
			<p class="ex"><?php _e('<strong><u>Note:</u></strong> The below error/failure message is also used for <strong>captcha</strong> verification!', 'cforms') ?></p>

			<div class="optionsbox" style="margin-top:25px;">
				<div class="optionsboxL"><label for="cforms_codeerr"><?php _e('<strong>Failure message</strong><br />(for a wrong answer)', 'cforms'); ?></label></div>
				<div class="optionsboxR"><textarea name="cforms_codeerr" id="cforms_codeerr" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_codeerr'))); ?></textarea></div>
			</div>

			<?php $qa = stripslashes(htmlspecialchars(get_option('cforms_sec_qa'))); ?>
	
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_sec_qa"><?php _e('<strong>Questions & Answers</strong><br />format: Q=A', 'cforms') ?></label></div>
				<div class="optionsboxR"><textarea name="cforms_sec_qa" id="cforms_sec_qa" ><?php echo $qa; ?></textarea></div>
			</div>

			<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#captcha',__('Depending on your personal preferences and level of SPAM security you\'d like to put in place, you can also use <a href="[url]">cforms\' CAPTCHA feature</a>!', 'cforms')); ?></p>
	
		</fieldset>


		
		<fieldset id="tracking" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit2" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#';"/><?php _e('Database Input Tracking', 'cforms') ?></p>

				<p><?php _e('If you like to track your form submissions also via the database, please enable this feature below. If required, this will create two new tables and you\'ll see a new sub tab "<strong>Tracking</strong>" under the cforms menu.', 'cforms') ?></p>
		
				<p><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#autoconf',__('If you\'ve enabled the <a href="[url]">auto confirmation message</a> feature or have included a <code class="codehighlight">CC: me</code> input field, you can optionally configure the subject line/message of the email to include the form tracking ID by using the variable <code class="codehighlight">{ID}</code>.', 'cforms')); ?></p>
		
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_database"><span class="abbr" title="<?php _e('Will create two new tables in your WP database.', 'cforms') ?>"><strong><?php _e('Enable Database Tracking', 'cforms') ?></strong></span></label></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_database" name="cforms_database" <?php if(get_option('cforms_database')=="1") echo "checked=\"checked\""; ?>/></div>
				</div>
				
				<?php if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions ) :?>
				<div class="optionsbox" style="margin-top:25px;">
					<div class="optionsboxL"><label for="deletetables"><?php _e('<strong>Wipe out</strong> all collected cforms submission data and drop tables.', 'cforms') ?></label></div>
					<div class="optionsboxR"><input type="submit" title="<?php _e('Be careful with this one!', 'cforms') ?>" name="deletetables" class="allbuttons delbutton" value="<?php _e('Delete cforms Tracking Tables', 'cforms') ?>" onclick="return confirm('<?php _e('Do you really want to erase all collected data?', 'cforms') ?>');"/></div>
				</div>
				<?php endif; ?>

		</fieldset>

		

		<fieldset class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><?php _e('Uninstalling / Removing cforms', 'cforms') ?></p>

				<p><?php _e('Generally, deactivating the plugin does <strong>not</strong> erase any of its data, if you\'d like to quit using cforms for good, please erase all data before deactivating the plugin.', 'cforms') ?></p>

				<p><?php _e('This erases <strong>all</strong> cforms data (form & plugin settings). <strong>This is irrevocable!</strong> Be careful.', 'cforms') ?>&nbsp;&nbsp;&nbsp;
					 <input type="submit" name="deleteall" title="<?php _e('Are you sure you want to do this?!', 'cforms') ?>" class="allbuttons deleteall" value="<?php _e('DELETE *ALL* CFORMS DATA', 'cforms') ?>" onclick="return confirm('<?php _e('Do you really want to erase all of the plugin config data?', 'cforms') ?>');"/>
				</p>
		</fieldset>

	</form>


	<?php cforms_footer(); ?>
</div>
