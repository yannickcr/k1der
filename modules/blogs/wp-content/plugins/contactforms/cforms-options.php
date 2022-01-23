<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;


### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die(__('Access Denied','cforms'));
}


// default to 1 & get real #
$FORMCOUNT=get_option('cforms_formcount');


// if all data has been erased quit
if ($FORMCOUNT == ''){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>

	<?php
	die;
}


if(isset($_REQUEST['addbutton'])) {


	$FORMCOUNT=$FORMCOUNT+1;
	$no = $noDISP = $FORMCOUNT;
	
	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_close) ) 
		wp_cache_close();

	update_option('cforms_formcount', (string)($FORMCOUNT));

	add_option('cforms'.$no.'_count_fields', '5');
	add_option('cforms'.$no.'_count_field_1', __('My Fieldset$#$fieldsetstart$#$0$#$0$#$0$#$0', 'cforms'));
	add_option('cforms'.$no.'_count_field_2', __('Your Name|Your Name$#$textfield$#$1$#$0$#$1$#$0', 'cforms'));
	add_option('cforms'.$no.'_count_field_3', __('Email$#$textfield$#$1$#$1$#$0$#$0', 'cforms'));
	add_option('cforms'.$no.'_count_field_4', __('Website|http://$#$textfield$#$0$#$0$#$0$#$0', 'cforms'));
	add_option('cforms'.$no.'_count_field_5', __('Message$#$textarea$#$0$#$0$#$0$#$0', 'cforms'));
	
	add_option('cforms'.$no.'_required', __('(required)', 'cforms'));
	add_option('cforms'.$no.'_emailrequired', __('(valid email required)', 'cforms'));
	
	add_option('cforms'.$no.'_ajax', '1');
	add_option('cforms'.$no.'_confirm', '0');
	add_option('cforms'.$no.'_fname', __('A new form', 'cforms')); 
	add_option('cforms'.$no.'_csubject', __('Re: Your note', 'cforms').'$#$'.__('Re: Submitted form (copy)', 'cforms'));
	add_option('cforms'.$no.'_cmsg', __('Dear {Your Name},', 'cforms') . "\n". __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n");
	add_option('cforms'.$no.'_cmsg_html', __('<div style="color:#ccc; border-bottom:1px solid #ccc"><strong>auto confirmation message, {Date}</strong></div> ', 'cforms') . "\n<br />\n" . __('<p><strong>Dear {Your Name},</strong></p>', 'cforms') . "\n". __('<p>Thank you for your note!</p>', 'cforms') . "\n". __('<p>We will get back to you as soon as possible.</p>', 'cforms') . "\n\n");
	add_option('cforms'.$no.'_email', get_bloginfo('admin_email', 'cforms'));
	add_option('cforms'.$no.'_fromemail', '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>');
	add_option('cforms'.$no.'_bcc', '');
	add_option('cforms'.$no.'_header', __('A new submission (form: "{Form Name}")', 'cforms') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms') . "\r\n" . __('Via: {Page}', 'cforms') . "\r\n" . __('By {IP} (visitor IP)', 'cforms') . ".\r\n" . ".\r\n" );		
	add_option('cforms'.$no.'_header_html', '<p style="background:#fafafa; text-align:center; font:10px arial">' . __('a form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '</p>' );		
	add_option('cforms'.$no.'_formdata', '1111');
	add_option('cforms'.$no.'_space', '30');
	add_option('cforms'.$no.'_noattachments', '0');
	
	add_option('cforms'.$no.'_subject', __('A comment from {Your Name}', 'cforms'));
	add_option('cforms'.$no.'_submit_text', __('Send Comment', 'cforms'));
	add_option('cforms'.$no.'_success', __('Thank you for your comment!', 'cforms'));
	add_option('cforms'.$no.'_failure', __('Please fill in all the required fields.', 'cforms'));
	add_option('cforms'.$no.'_working', __('One moment please...', 'cforms'));
	add_option('cforms'.$no.'_popup', 'nn');
	add_option('cforms'.$no.'_showpos', 'yn');
	
	add_option('cforms'.$no.'_redirect', '0');
	add_option('cforms'.$no.'_redirect_page', 'http://redirect.to.this.page');		
	add_option('cforms'.$no.'_action', '0');
	add_option('cforms'.$no.'_action_page', 'http://');		

	/*file upload*/
	add_option('cforms'.$no.'_upload_dir', ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/attachments');
	add_option('cforms'.$no.'_upload_ext', 'txt,zip,doc,rtf,xls');
	add_option('cforms'.$no.'_upload_size', '1024');

	add_option('cforms'.$no.'_tracking', '');
	
	echo '<div id="message" class="updated fade"><p>'.__('A new form with default fields has been added.', 'cforms').'</p></div>';
	
	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_init) ) 
		wp_cache_init();


} elseif(isset($_REQUEST['dupbutton'])) {


	$noDISP='1'; $no='';
	if( isset($_REQUEST['no']) ) {
		if( $_REQUEST['no']<>'1' )
			$noDISP = $no = $_REQUEST['no'];
	}
	
	$FORMCOUNT=$FORMCOUNT+1;

	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_close) ) 
		wp_cache_close();
	
	update_option('cforms_formcount', (string)($FORMCOUNT));
	
	add_option('cforms'.$FORMCOUNT.'_count_fields', get_option('cforms'.$no.'_count_fields'));
	
	for ( $j=1; $j<=get_option('cforms'.$no.'_count_fields'); $j++)  //delete all extra fields!
		  add_option('cforms'.$FORMCOUNT.'_count_field_'.$j, get_option('cforms'.$no.'_count_field_'.$j));
	
	add_option('cforms'.$FORMCOUNT.'_required', get_option('cforms'.$no.'_required'));
	add_option('cforms'.$FORMCOUNT.'_emailrequired', get_option('cforms'.$no.'_emailrequired'));
	
	add_option('cforms'.$FORMCOUNT.'_ajax', get_option('cforms'.$no.'_ajax'));
	add_option('cforms'.$FORMCOUNT.'_confirm', get_option('cforms'.$no.'_confirm'));
	add_option('cforms'.$FORMCOUNT.'_fname', "Duplicate of form #$noDISP");
	add_option('cforms'.$FORMCOUNT.'_csubject', get_option('cforms'.$no.'_csubject'));
	add_option('cforms'.$FORMCOUNT.'_cmsg', get_option('cforms'.$no.'_cmsg'));
	add_option('cforms'.$FORMCOUNT.'_cmsg_html', get_option('cforms'.$no.'_cmsg_html'));
	add_option('cforms'.$FORMCOUNT.'_email', get_option('cforms'.$no.'_email'));
	add_option('cforms'.$FORMCOUNT.'_bcc', get_option('cforms'.$no.'_bcc'));
	add_option('cforms'.$FORMCOUNT.'_header', get_option('cforms'.$no.'_header'));
	add_option('cforms'.$FORMCOUNT.'_header_html', get_option('cforms'.$no.'_header_html'));
	add_option('cforms'.$FORMCOUNT.'_formdata', get_option('cforms'.$no.'_formdata'));
	add_option('cforms'.$FORMCOUNT.'_space', get_option('cforms'.$no.'_space'));
	add_option('cforms'.$FORMCOUNT.'_noattachments', get_option('cforms'.$no.'_noattachments'));
	
	add_option('cforms'.$FORMCOUNT.'_subject', get_option('cforms'.$no.'_subject'));
	add_option('cforms'.$FORMCOUNT.'_submit_text', get_option('cforms'.$no.'_submit_text'));
	add_option('cforms'.$FORMCOUNT.'_success', get_option('cforms'.$no.'_success'));
	add_option('cforms'.$FORMCOUNT.'_failure', get_option('cforms'.$no.'_failure'));
	add_option('cforms'.$FORMCOUNT.'_working', get_option('cforms'.$no.'_working'));
	add_option('cforms'.$FORMCOUNT.'_popup', get_option('cforms'.$no.'_popup'));
	add_option('cforms'.$FORMCOUNT.'_showpos', get_option('cforms'.$no.'_showpos'));

	add_option('cforms'.$FORMCOUNT.'_redirect', get_option('cforms'.$no.'_redirect'));
	add_option('cforms'.$FORMCOUNT.'_redirect_page', get_option('cforms'.$no.'_redirect_page'));		
	add_option('cforms'.$FORMCOUNT.'_action', get_option('cforms'.$no.'_action'));
	add_option('cforms'.$FORMCOUNT.'_action_page', get_option('cforms'.$no.'_action_page'));		

	add_option('cforms'.$FORMCOUNT.'_upload_dir', get_option('cforms'.$no.'_upload_dir'));
	add_option('cforms'.$FORMCOUNT.'_upload_ext', get_option('cforms'.$no.'_upload_ext'));
	add_option('cforms'.$FORMCOUNT.'_upload_size', get_option('cforms'.$no.'_upload_size'));
		
	add_option('cforms'.$FORMCOUNT.'_tracking', get_option('cforms'.$no.'_tracking'));
	echo '<div id="message" class="updated fade"><p>'.__('The form has been duplicated, you\'re now working on the copy.', 'cforms').'</p></div>';

	//sorry, but WP2.2 doesn quickly enough flush the cache!
	if ( function_exists (wp_cache_init) ) 
		wp_cache_init();
	
	//set $no afterwards: need it to duplicate fields
	$no = $noDISP = $FORMCOUNT;


} elseif( isset($_REQUEST['uploadcformsdata']) ) {  // restore backup


  $file = $_FILES['import'];
	$err = '';
			
	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
			$err = $file['error'];

	// A non-empty file will pass this test.
	if ( !( $file['size'] > 0 ) )
			$err = __('File is empty. Please upload something more substantial.', 'cforms');

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_uploaded_file( $file['tmp_name'] ) )
			$err = __('Specified file failed upload test.', 'cforms');

	if ( $err <> '' ){

	  echo '<div id="message" class="updated fade"><p>'.__('Error:', 'cforms').' '.$err.'</p></div>';

	} else {

	  // current form
	  $noDISP = '1'; $no='';
		if( $_REQUEST['noSub']<>'1' )
			$noDISP = $no = $_REQUEST['noSub'];

		$importdata = file($file['tmp_name']);
		$cf=0;
		if ( !(strpos($importdata[0], 'cf:')===false) ) {
					update_option('cforms'.$no.'_count_fields',substr( trim($importdata[0]), 3) );
					$cf = (int) substr( trim($importdata[0]), 3);
		}
		
		if ( !(strpos($importdata[1], 'ff:')===false) ) {
					$fields = explode( '+++', substr( trim($importdata[1]), 3) );
				  for ( $i=1; $i<=$cf; $i++)  //now delete all fields from last form
							update_option('cforms'.$no.'_count_field_'.$i, $fields[$i-1] );

					//delete the rest until all gone
					while ( get_option( 'cforms'.$no.'_count_field_'.$i++ ) <> "" && $i<100 ) // 100: just to be safe
 							delete_option( 'cforms'.$no.'_count_field_'.($i-1) );
		}

		if ( !(strpos($importdata[2], 'rq:')===false) )
					update_option('cforms'.$no.'_required',substr( trim($importdata[2]), 3) );

		if ( !(strpos($importdata[3], 'er:')===false) )
					update_option('cforms'.$no.'_emailrequired',substr( trim($importdata[3]), 3) );

		if ( !(strpos($importdata[4], 'ac:')===false) )
					update_option('cforms'.$no.'_confirm',substr( trim($importdata[4]), 3) );

		if ( !(strpos($importdata[5], 'jx:')===false) )
					update_option('cforms'.$no.'_ajax',substr( trim($importdata[5]), 3) );

		if ( !(strpos($importdata[6], 'fn:')===false) )
					update_option('cforms'.$no.'_fname',substr( trim($importdata[6]), 3) );

		if ( !(strpos($importdata[7], 'cs:')===false) )
					update_option('cforms'.$no.'_csubject',substr( trim($importdata[7]), 3) );

		if ( !(strpos($importdata[8], 'cm:')===false) )
					update_option('cforms'.$no.'_cmsg', str_replace ('$n$', "\r\n",substr( trim($importdata[8]), 3) ));

		if ( !(strpos($importdata[9], 'em:')===false) )
					update_option('cforms'.$no.'_email',substr( trim($importdata[9]), 3) );

		if ( !(strpos($importdata[10], 'sj:')===false) )
					update_option('cforms'.$no.'_subject',substr( trim($importdata[10]), 3) );

		if ( !(strpos($importdata[11], 'su:')===false) )
					update_option('cforms'.$no.'_submit_text',substr( trim($importdata[11]), 3) );

		if ( !(strpos($importdata[12], 'sc:')===false) )
					update_option('cforms'.$no.'_success',str_replace ('$n$', "\r\n",substr( trim($importdata[12]), 3) ));

		if ( !(strpos($importdata[13], 'fl:')===false) )
					update_option('cforms'.$no.'_failure',str_replace ('$n$', "\r\n",substr( trim($importdata[13]), 3) ));

		if ( !(strpos($importdata[14], 'wo:')===false) )
					update_option('cforms'.$no.'_working',substr( trim($importdata[14]), 3) );

		if ( !(strpos($importdata[15], 'pp:')===false) )
					update_option('cforms'.$no.'_popup',substr( trim($importdata[15]), 3) );

		if ( !(strpos($importdata[16], 'sp:')===false) )
					update_option('cforms'.$no.'_showpos',substr( trim($importdata[16]), 3) );

		if ( !(strpos($importdata[17], 'rd:')===false) )
					update_option('cforms'.$no.'_redirect',substr( trim($importdata[17]), 3) );

		if ( !(strpos($importdata[18], 'rp:')===false) )
					update_option('cforms'.$no.'_redirect_page',substr( trim($importdata[18]), 3) );

		if ( !(strpos($importdata[19], 'hd:')===false) )
					update_option('cforms'.$no.'_header',str_replace ('$n$', "\r\n",substr( trim($importdata[19]), 3) ));

		if ( !(strpos($importdata[20], 'pc:')===false) )
					update_option('cforms'.$no.'_space',substr( trim($importdata[20]), 3) );
					
		if ( !(strpos($importdata[21], 'at:')===false) )
					update_option('cforms'.$no.'_noattachments',substr( trim($importdata[21]), 3) );

		if ( !(strpos($importdata[22], 'ud:')===false) )
					update_option('cforms'.$no.'_upload_dir',substr( trim($importdata[22]), 3) );

		if ( !(strpos($importdata[23], 'ue:')===false) )
					update_option('cforms'.$no.'_upload_ext',substr( trim($importdata[23]), 3) );

		if ( !(strpos($importdata[24], 'us:')===false) )
					update_option('cforms'.$no.'_upload_size',substr( trim($importdata[24]), 3) );

		if ( !(strpos($importdata[25], 'ar:')===false) )
					update_option('cforms'.$no.'_action',substr( trim($importdata[25]), 3) );

		if ( !(strpos($importdata[26], 'ap:')===false) )
					update_option('cforms'.$no.'_action_page',substr( trim($importdata[26]), 3) );
					
		if ( !(strpos($importdata[27], 'bc:')===false) )
					update_option('cforms'.$no.'_bcc',substr( trim($importdata[27]), 3) );

		if ( !(strpos($importdata[28], 'ch:')===false) )
					update_option('cforms'.$no.'_cmsg_html', str_replace ('$n$', "\r\n",substr( trim($importdata[28]), 3) ));

		if ( !(strpos($importdata[29], 'hh:')===false) )
					update_option('cforms'.$no.'_header_html',str_replace ('$n$', "\r\n",substr( trim($importdata[29]), 3) ));

		if ( !(strpos($importdata[30], 'fd:')===false) )
					update_option('cforms'.$no.'_formdata',substr( trim($importdata[30]), 3) );

		if ( !(strpos($importdata[31], 'tr:')===false) )
					update_option('cforms'.$no.'_tracking',substr( trim($importdata[31]), 3) );

		if ( !(strpos($importdata[32], 'fm:')===false) )
					update_option('cforms'.$no.'_fromemail',substr( trim($importdata[32]), 3) );


	echo '<div id="message" class="updated fade"><p>'.__('All form specific settings have been restored from the backup file.', 'cforms').'</p></div>';
	}

	
} elseif(isset($_REQUEST['delbutton']) && $FORMCOUNT>1 && $_REQUEST['no']<>'1') {  // 1..4d...5..6


  // current form
  $noDISP = '1'; $no='';
	if( $_REQUEST['no']<>'1' )
		$noDISP = $no = $_REQUEST['no'];

  for ( $i=(int)$noDISP; $i<$FORMCOUNT; $i++) {  //move all forms "to the left"
  
    for ( $j=1; $j<=get_option('cforms'.$i.'_count_fields'); $j++)  //delete all extra fields!
      delete_option('cforms'.$i.'_count_field_'.$j);
      
    for ( $j=1; $j<=get_option('cforms'.($i+1).'_count_fields'); $j++) { //now delete last form
      $tempo = get_option('cforms'.($i+1).'_count_field_'.$j);
      add_option('cforms'.$i.'_count_field_'.$j,$tempo);
    }
    
    $tempo = get_option('cforms'.($i+1).'_count_fields');
    update_option('cforms'.$i.'_count_fields',$tempo);
    $tempo = get_option('cforms'.($i+1).'_required');
    update_option('cforms'.$i.'_required',$tempo);
    $tempo = get_option('cforms'.($i+1).'_emailrequired');
    update_option('cforms'.$i.'_emailrequired',$tempo);

    $tempo = get_option('cforms'.($i+1).'_confirm');
    update_option('cforms'.$i.'_confirm',$tempo);
    $tempo = get_option('cforms'.($i+1).'_ajax');
    update_option('cforms'.$i.'_ajax',$tempo);
    $tempo = get_option('cforms'.($i+1).'_fname');
    update_option('cforms'.$i.'_fname',$tempo);
    $tempo = get_option('cforms'.($i+1).'_csubject');
    update_option('cforms'.$i.'_csubject',$tempo);
    $tempo = get_option('cforms'.($i+1).'_cmsg');
    update_option('cforms'.$i.'_cmsg',$tempo);
    $tempo = get_option('cforms'.($i+1).'_cmsg_html');
    update_option('cforms'.$i.'_cmsg_html',$tempo);
    $tempo = get_option('cforms'.($i+1).'_email');
    update_option('cforms'.$i.'_email',$tempo);
    $tempo = get_option('cforms'.($i+1).'_fromemail');
    update_option('cforms'.$i.'_fromemail',$tempo);
    $tempo = get_option('cforms'.($i+1).'_bcc');
    update_option('cforms'.$i.'_bcc',$tempo);
    $tempo = get_option('cforms'.($i+1).'_header');
    update_option('cforms'.$i.'_header',$tempo);
    $tempo = get_option('cforms'.($i+1).'_header_html');
    update_option('cforms'.$i.'_header_html',$tempo);
    $tempo = get_option('cforms'.($i+1).'_formdata');
    update_option('cforms'.$i.'_formdata',$tempo);
    $tempo = get_option('cforms'.($i+1).'_space');
    update_option('cforms'.$i.'_space',$tempo);
    $tempo = get_option('cforms'.($i+1).'_noattachments');
    update_option('cforms'.$i.'_noattachments',$tempo);

    $tempo = get_option('cforms'.($i+1).'_upload_dir');
    update_option('cforms'.$i.'_upload_dir',$tempo);
    $tempo = get_option('cforms'.($i+1).'_upload_ext');
    update_option('cforms'.$i.'_upload_ext',$tempo);
    $tempo = get_option('cforms'.($i+1).'_upload_size');
    update_option('cforms'.$i.'_upload_size',$tempo);

    $tempo = get_option('cforms'.($i+1).'_subject');
    update_option('cforms'.$i.'_subject',$tempo);
    $tempo = get_option('cforms'.($i+1).'_submit_text');
    update_option('cforms'.$i.'_submit_text',$tempo);
    $tempo = get_option('cforms'.($i+1).'_success');
    update_option('cforms'.$i.'_success',$tempo);
    $tempo = get_option('cforms'.($i+1).'_failure');
    update_option('cforms'.$i.'_failure',$tempo);
    $tempo = get_option('cforms'.($i+1).'_working');
    update_option('cforms'.$i.'_working',$tempo);
    $tempo = get_option('cforms'.($i+1).'_popup');
    update_option('cforms'.$i.'_popup',$tempo);
    $tempo = get_option('cforms'.($i+1).'_showpos');
    update_option('cforms'.$i.'_showpos',$tempo);
    
    $tempo = get_option('cforms'.($i+1).'_redirect');
    update_option('cforms'.$i.'_redirect',$tempo);
    $tempo = get_option('cforms'.($i+1).'_redirect_page');
    update_option('cforms'.$i.'_redirect_page',$tempo);
    $tempo = get_option('cforms'.($i+1).'_action');
    update_option('cforms'.$i.'_action',$tempo);
    $tempo = get_option('cforms'.($i+1).'_action_page');
    update_option('cforms'.$i.'_action_page',$tempo);

    $tempo = get_option('cforms'.($i+1).'_tracking');
    update_option('cforms'.$i.'_tracking',$tempo);
 }
  
  for ( $i=1; $i<=get_option('cforms'.$FORMCOUNT.'_count_fields'); $i++)  //now delete all fields from last form
    delete_option('cforms'.$FORMCOUNT.'_count_field_'.$i);

  delete_option('cforms'.$FORMCOUNT.'_count_fields');
  delete_option('cforms'.$FORMCOUNT.'_required');
  delete_option('cforms'.$FORMCOUNT.'_emailrequired');

  delete_option('cforms'.$FORMCOUNT.'_confirm');
  delete_option('cforms'.$FORMCOUNT.'_ajax');
  delete_option('cforms'.$FORMCOUNT.'_fname');
  delete_option('cforms'.$FORMCOUNT.'_csubject');
  delete_option('cforms'.$FORMCOUNT.'_cmsg');
  delete_option('cforms'.$FORMCOUNT.'_cmsg_html');
  delete_option('cforms'.$FORMCOUNT.'_email');
  delete_option('cforms'.$FORMCOUNT.'_fromemail');
  delete_option('cforms'.$FORMCOUNT.'_bcc');
  delete_option('cforms'.$FORMCOUNT.'_header');
  delete_option('cforms'.$FORMCOUNT.'_header_html');
  delete_option('cforms'.$FORMCOUNT.'_formdata');
  delete_option('cforms'.$FORMCOUNT.'_space');
  delete_option('cforms'.$FORMCOUNT.'_noattachments');

  delete_option('cforms'.$FORMCOUNT.'_upload_dir');
  delete_option('cforms'.$FORMCOUNT.'_upload_ext');
  delete_option('cforms'.$FORMCOUNT.'_upload_size');

  delete_option('cforms'.$FORMCOUNT.'_subject');
  delete_option('cforms'.$FORMCOUNT.'_submit_text');
  delete_option('cforms'.$FORMCOUNT.'_success');
  delete_option('cforms'.$FORMCOUNT.'_failure');
  delete_option('cforms'.$FORMCOUNT.'_working');
  delete_option('cforms'.$FORMCOUNT.'_popup');
  delete_option('cforms'.$FORMCOUNT.'_showpos');

  delete_option('cforms'.$FORMCOUNT.'_redirect');
  delete_option('cforms'.$FORMCOUNT.'_redirect_page');
  delete_option('cforms'.$FORMCOUNT.'_action');
  delete_option('cforms'.$FORMCOUNT.'_action_page');

  delete_option('cforms'.$FORMCOUNT.'_tracking');

 $FORMCOUNT=$FORMCOUNT-1;
  
  if ( $FORMCOUNT>1 && ((int)$_REQUEST['no'])>1 ) {
  		if( isset($_REQUEST['no']) && (int)$_REQUEST['no']<=$FORMCOUNT) // otherwise stick with the current form
  			$noDISP = $no = $_REQUEST['no'];
    	else
				$no = $noDISP = $FORMCOUNT;
  } else {
    $noDISP = '1';
    $no='';
    }
    
  update_option('cforms_formcount', (string)($FORMCOUNT));
  echo '<div id="message" class="updated fade"><p>'. __('Form deleted', 'cforms').'.</p></div>';


} else {


  // set paramters to default, if not exists
  $noDISP='1';
  $no='';
  if( isset($_REQUEST['switchform']) ) { // only set when hitting form chg buttons
  		if( $_REQUEST['switchform']<>'1' )
  			$noDISP = $no = $_REQUEST['switchform'];
  }
  else if( isset($_REQUEST['go']) ) { // only set when hitting form chg buttons
  		if( $_REQUEST['pickform']<>'1' )
  			$noDISP = $no = $_REQUEST['pickform'];
  }
  else{
  		if( isset($_REQUEST['noSub']) && (int)$_REQUEST['noSub']>1 ) // otherwise stick with the current form
  			$noDISP = $no = $_REQUEST['noSub'];
  }
  
}



//default: $field_count = what's in the DB
$field_count = get_option('cforms'.$no.'_count_fields');

// new field added (will actually be added below!)
if( isset($_REQUEST['AddField']) && isset($_REQUEST['field_count_submit']) )
{
		$field_count = $_REQUEST['field_count_submit'];
		$field_count ++;
		update_option('cforms'.$no.'_count_fields', $field_count);
}

// set to nothing
$usermsg='&nbsp;';

// Update Settings
if( isset($_REQUEST['Submit1']) || isset($_REQUEST['Submit2']) || isset($_REQUEST['Submit3']) || 
    isset($_REQUEST['Submit4']) || isset($_REQUEST['Submit5']) || isset($_REQUEST['Submit6']) || isset($_REQUEST['Submit7']) || 
	isset($_REQUEST['AddField']) || array_search("X", $_REQUEST) ) {

	$verification=false;
	$captcha=false;
	$ccbox=false;
	$emailtobox=false;

	for($i = 1; $i <= $field_count; $i++) {

	  if ($_REQUEST['field_' . $i . '_name']<>''){    // Newly "AddField" does not exist yet!

				$allgood=true;
				$name = str_replace('$#$', '$', $_REQUEST['field_' . $i . '_name']);
				$type = $_REQUEST['field_' . $i . '_type'];
				$required = 0;
				$emailcheck = 0;
				$clear = 0;
				$disabled = 0;

				if( $type=='verification' ){
					$allgood = $verification?false:true;
					$usermsg .= $verification?__('Only one <em>Visitor verification</em> field is permitted!', 'cforms').'<br />':'';
					$verification=true;
				}
				if( $type=='captcha' ){
					$allgood = $captcha?false:true;
					$usermsg .= $captcha?__('Only one <em>captcha</em> field is permitted!', 'cforms').'<br />':'';
					$captcha=true;
				}
				if( $type=='ccbox' ){
					$allgood = $ccbox?false:true;
					$usermsg .= $ccbox?__('Only one <em>CC:</em> field is permitted!', 'cforms').'<br />':'';
					$ccbox=true;
				}
				if( $type=='emailtobox' ){
					$allgood = $emailtobox?false:true;
					$usermsg .= $emailtobox?__('Only one <em>Multiple Recipients</em> field is permitted!'.'<br />', 'cforms'):'';
					$emailtobox=true;
				}
						
				if(isset($_REQUEST['field_' . $i . '_required']) && in_array($type,array('textfield','textarea','checkbox','multiselectbox','selectbox','emailtobox','upload')) ) {
					$required = 1;
				}
				
				if(isset($_REQUEST['field_' . $i . '_emailcheck']) && $type == 'textfield' ){
					$emailcheck = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_clear']) && in_array($type,array('textfield','textarea')) ) {
					$clear = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_disabled']) && in_array($type,array('textarea','textfield','checkbox','checkboxgroup','multiselectbox','selectbox','radiobuttons','upload')) ) {
					$disabled = 1;
				}
				
				if ($allgood)
						update_option('cforms'.$no.'_count_field_' . $i, $name . '$#$' . $type . '$#$' . $required. '$#$'. $emailcheck . '$#$'. $clear . '$#$'. $disabled);

				$all_fields[$i-1]=$name . '$#$' . $type . '$#$' . $required. '$#$' . $emailcheck . '$#$'. $clear . '$#$' . $disabled;
				
		}
	}

	update_option('cforms'.$no.'_upload_dir',  $_REQUEST['cforms_upload_dir']);
	update_option('cforms'.$no.'_upload_ext',  $_REQUEST['cforms_upload_ext']);
	update_option('cforms'.$no.'_upload_size', $_REQUEST['cforms_upload_size']);
	
	update_option('cforms'.$no.'_confirm',    $_REQUEST['cforms_confirm']?'1':'0');
	update_option('cforms'.$no.'_ajax',       $_REQUEST['cforms_ajax']?'1':'0');
	update_option('cforms'.$no.'_popup',     ($_REQUEST['cforms_popup1']?'y':'n').($_REQUEST['cforms_popup2']?'y':'n') );
	update_option('cforms'.$no.'_showpos',   ($_REQUEST['cforms_showposa']?'y':'n').($_REQUEST['cforms_showposb']?'y':'n') );

	update_option('cforms'.$no.'_fname',    preg_replace("/\\\+/", "\\",$_REQUEST['cforms_fname']));	
	update_option('cforms'.$no.'_csubject', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_csubject']).'$#$'.preg_replace("/\\\+/", "\\",$_REQUEST['cforms_ccsubject']) );
	update_option('cforms'.$no.'_cmsg',     preg_replace("/\\\+/", "\\",$_REQUEST['cforms_cmsg']));
	update_option('cforms'.$no.'_cmsg_html',preg_replace("/\\\+/", "\\",$_REQUEST['cforms_cmsg_html']));

  	update_option('cforms'.$no.'_required',      $_REQUEST['cforms_required']);
  	update_option('cforms'.$no.'_emailrequired', $_REQUEST['cforms_emailrequired']);
	update_option('cforms'.$no.'_success',       $_REQUEST['cforms_success']);
	update_option('cforms'.$no.'_failure',       $_REQUEST['cforms_failure']);
	update_option('cforms'.$no.'_working',       $_REQUEST['cforms_working']);

	update_option('cforms'.$no.'_submit_text',   $_REQUEST['cforms_submit_text']);
	update_option('cforms'.$no.'_email',         $_REQUEST['cforms_email']);
	update_option('cforms'.$no.'_fromemail',     $_REQUEST['cforms_fromemail']);
	
	update_option('cforms'.$no.'_bcc',           $_REQUEST['cforms_bcc']);
	update_option('cforms'.$no.'_subject',       $_REQUEST['cforms_subject']);
	update_option('cforms'.$no.'_header',      preg_replace("/\\\+/", "\\",$_REQUEST['cforms_header']));
	update_option('cforms'.$no.'_header_html', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_header_html'])); 

	update_option('cforms'.$no.'_formdata',     ($_REQUEST['cforms_formdata_txt']?'1':'0').($_REQUEST['cforms_formdata_html']?'1':'0').($_REQUEST['cforms_admin_html']?'1':'0').($_REQUEST['cforms_user_html']?'1':'0') );
	
	update_option('cforms'.$no.'_space',         $_REQUEST['cforms_space']);
	update_option('cforms'.$no.'_noattachments', $_REQUEST['cforms_noattachments']?'1':'0');

	update_option('cforms'.$no.'_redirect',      $_REQUEST['cforms_redirect']?'1':'0');
	update_option('cforms'.$no.'_redirect_page', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_redirect_page']));
	update_option('cforms'.$no.'_action',        $_REQUEST['cforms_action']?'1':'0');
	update_option('cforms'.$no.'_action_page',   preg_replace("/\\\+/", "\\",$_REQUEST['cforms_action_page']));
	update_option('cforms'.$no.'_tracking',      preg_replace("/\\\+/", "\\",$_REQUEST['cforms_tracking']));
	
	// did the order of fields change ?
	if(isset($_REQUEST['field_order']) && $_REQUEST['field_order']<>'') {
		$j=0;
		$temp = explode('=',$_REQUEST['field_order']);
		$order = explode(',', $temp[1]);

		//echo $temp[1]."<br>";  // debug
		$tempcount = isset($_REQUEST['AddField'])?($field_count-1):($field_count);
		while($j < $tempcount)
		{
	      $new_f = substr($order[$j],0,-1);
				if ( $j <> $new_f )
						update_option('cforms'.$no.'_count_field_'.($j+1),$all_fields[$new_f]);
					  //echo "$j=$new_f :: ".$all_fields[$j]." == ".$all_fields[$new_f]."<br />";  //debug
		$j++;
		}

	} //if order changed

	echo '<div id="message" class="updated fade"><p>'.__('Form settings updated.', 'cforms').'</p></div>';
}


// delete field if we find one and move the rest up
$deletefound = 0;
if(strlen(get_option('cforms'.$no.'_count_field_' . $field_count)) > 0) {

	$temp_count = 1;
	while($temp_count <= $field_count) {
	
		if(isset($_REQUEST['DeleteField' . $temp_count])) {
			$deletefound = 1;
			update_option('cforms'.$no.'_count_fields', ($field_count - 1));
		}
		
		if($deletefound && $temp_count<$field_count) {
			$temp_val = get_option('cforms'.$no.'_count_field_' . ($temp_count+1));
			update_option('cforms'.$no.'_count_field_' . ($temp_count), $temp_val);
		}
		
		$temp_count++;
	} // while

	if($deletefound == 1) {  //now delete
	  delete_option('cforms'.$no.'_count_field_' . $field_count);
		$field_count--;
	}

} //if

//
// prep drop down box for form selection
//

$formlistbox = ' <select id="pickform" name="pickform">';

for ($i=1; $i<=$FORMCOUNT; $i++){

	$j   = ( $i > 1 )?$i:'';
	$sel = ($noDISP==$i)?' selected="selected"':'';

  $formlistbox .= '<option value="'.$i.'" '.$sel.'>'.get_option('cforms'.$j.'_fname').'</option>';
}
$formlistbox .= '</select><input type="submit" class="allbuttons go" name="go" value="'.__('Go', 'cforms').'"/>';

 
// check for updates that have not set 	add_option('cforms'.$no.'_showpos', 'yn');
if ( get_option('cforms'.$no.'_showpos')=='' ) {
	?>
	
	<div id="message" class="updated fade"><p><strong>
		<?php echo str_replace('[url]','#anchormessage',__('It seems that you have recently upgraded cforms, please check the <a href="[url]">success/failure message settings</a> and >>Show messages<< options below!', 'cforms')) ?>
	</strong></p></div>
	
	<?php

} 


// check for updates that have not set 	add_option('cforms'.$no.'_showpos', 'yn');
if ( get_option('cforms'.$no.'_upload_dir')=='' ) {
	?>
	
	<div id="message" class="updated fade"><p><strong>
		<?php echo str_replace('[url]','#fileupload',__('In case you\'ve recently upgraded, please check the new <a href="[url]">file upload/attachment</a> relevant settings below!', 'cforms')) ?>
	</strong></p></div>
	
	<?php

} 


// check for set email header
if ( get_option('cforms'.$no.'_header')=='' ) {
	?>
	
	<div id="message" class="updated fade">
		<p><strong><?php echo str_replace('[url]','#anchoremail',__('It seems that you have recently upgraded cforms, please check the <a href="[url]">email header settings</a> below!', 'cforms')) ?></strong></p>
		<p><?php _e('If you don\'t know what to insert, here\'s an example:', 'cforms') ?></p>
		<p style="font-family:monospace; padding-left:20px; font-size:11px;"><?php _e('A new form submission ("{Form Name}")<br/ >------------------------------------------------<br/ >Submitted on: {Date}<br/ >Via: {Page}, Visitor IP {IP}<br/ >.<br/ >.', 'cforms') ?></p>
	</div>
	
	<?php

} 


// make sure at least the default FROM: address is set
if ( get_option('cforms'.$no.'_fromemail') == '' ) 
	update_option('cforms'.$no.'_fromemail', '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>');


// check if HTML needs to be enabled
$fd=get_option('cforms'.$no.'_formdata');
if( strlen($fd)<=2 ) {
	$fd .= ( get_option('cforms'.$no.'_header_html')<>''  )?'1':'0';
	$fd .= ( get_option('cforms'.$no.'_cmsg_html')<>'' )?'1':'0';
	update_option('cforms'.$no.'_formdata',$fd);
}


?>

<div class="wrap" id="top">

<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p1-title.jpg" alt=""/>

	<p>
		<?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#inserting',__('This plugin allows you <a href="[url]">to insert</a> one or more custom designed contact forms, which on submission (preferably via Ajax) will send the visitor info via email and optionally stores the feedback in the database.', 'cforms')); ?>
		<?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#guide',__('<a href="[url]">Here</a> is a quick step by step quide to get you up and running quickly.', 'cforms')); ?>
	</p>

	<form name="chgform" method="post" action="#">
			<!-- <span class="bignumber">#'.$noDISP.'</span> -->
			<div class="chgformbox">
				<div class="chgL"><?php _e('Your forms:', 'cforms'); echo $formlistbox; ?></div>
				<div class="chgR">
					<input class="allbuttons addbutton" type="submit" name="addbutton" title="<?php _e('adds a new form with default values', 'cforms'); ?>" value="<?php _e('Add New Form', 'cforms'); ?>"/>&nbsp;&nbsp;
			    	<input class="allbuttons dupbutton" type="submit" name="dupbutton" title="<?php _e('clones the current form', 'cforms'); ?>" value="<?php _e('Duplicate This Form', 'cforms'); ?>"/>&nbsp;&nbsp;
			    	<?php
			      if ( (int)$noDISP > 1)
			        echo '<input class="allbuttons delbutton" title="'.__('This will delete the current form - no warning!', 'cforms').'" type="submit" name="delbutton" value="'.__('Delete THIS Form(!)', 'cforms').'"/>';
			      ?>
				</div>
				<div class="chgM">
					<?php
			    	for ($i=1; $i<=$FORMCOUNT; $i++) {
			    		$j   = ( $i > 1 )?$i:'';
			     		echo '<input title="'.get_option('cforms'.$j.'_fname').'" class="allbuttons chgbutton'.(($i <> $noDISP)?'':'hi').'" type="submit" name="switchform" value="'.$i.'"/>';
		     		}
			    	?>
			 </div>
			</div>

      <input type="hidden" name="no" value="<?php echo $noDISP; ?>"/>
  </form>



	<form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post" action="#anchorfields">
		<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>" />

			<p class="areadesc">
				<?php _e('If you have many different forms, you may give each an optional name to better identify incoming emails:', 'cforms') ?>
			</p>
			<p class="mainoptions">
					<input style="float:right;" type="submit" name="Submit1" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#';" />
					<label for="cforms_fname" class="bignumber"><?php _e('Form Name', 'cforms') ?></label>
						<input id="cforms_fname" name="cforms_fname" class="cforms_fname" size="40" value="<?php echo (get_option('cforms'.$no.'_fname'));  ?>" />
						<input id="cforms_ajax" type="checkbox" class="cforms_ajax" name="cforms_ajax" <?php if(get_option('cforms'.$no.'_ajax')=="1") echo "checked=\"checked\""; ?>/>
					<label for="cforms_ajax" class="bignumber"><?php _e('Ajax enabled', 'cforms') ?></label>
			</p>


			<p class="backup">
					<input type="submit" name="savecformsdata" class="allbuttons backupbutton"  value="<?php _e('Backup This Form', 'cforms'); ?>" onclick="javascript:document.mainform.action='#';" />
					<label for="upload"><?php _e(' or restore previously saved settings:', 'cforms'); ?></label>
					<input type="file" id="upload" name="import" size="25" />
					<input type="submit" name="uploadcformsdata" class="allbuttons restorebutton" value="<?php _e('Restore Settings', 'cforms'); ?>" onclick="javascript:document.mainform.action='#';" />
			</p>


	<fieldset class="cformsoptions" id="anchorfields">
		<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit2" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchorfields';" /><?php _e('Form Input Fields', 'cforms') ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#fields',__('Please see the help section for information on how to deploy the various <a href="[url]">supported fields</a>.', 'cforms')); ?></p>

		<p class="ex"><?php _e('For the <em>auto confirmation</em> feature to work, make sure to mark at least one field <code class="codehighlight">Is Email</code>, otherwise <strong>NO</strong> auto confirmation email will be sent out! If multiple fields are checked "Is Email", only the first in the list will receive a notification.', 'cforms') ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#hfieldsets',__('How to structure your form using <strong>FIELDSETS</strong>? Check out the <a href="[url]">help section</a> for a sample screenshot depicting proper setup.', 'cforms')); ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#single',__('<strong>Default values</strong> can be set for single &amp; multi-line fields. <a href="[url]">See help</a>. In case you\'d like to <strong>auto clear</strong> a default value <em>on field focus</em>, set <code class="codehighlight">Auto Clear</code>.', 'cforms')); ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#regexp',__('Single &amp; Multi line input fields support custom <strong>regular expressions</strong> for specific field validation! Check the <a href="[url]">help</a> section for examples and make sure to check <code class="codehighlight">Is Required</code>.', 'cforms')); ?></p>

		<p id="cformswarning"><?php echo $usermsg; ?></p>

		<ul class="tableheader">
			<li class="field1th"><?php _e('No.', 'cforms'); ?></li>
			<li class="field2th" title="<?php _e('Can be a simple label or a more complex expression. See Help!', 'cforms'); ?>"><span class="abbr"><?php _e('Field Name', 'cforms'); ?></span></li>
			<li class="field3th" title="<?php _e('Pick one of the supported input field types.', 'cforms'); ?>"><span class="abbr"><?php _e('Type', 'cforms'); ?></span></li>
			<li class="field4th" title="<?php _e('Makes an input field required for proper form validation.', 'cforms'); ?>"><span class="abbr"><?php _e('Is Required', 'cforms'); ?></span></li>
			<li class="field5th" title="<?php _e('Makes the field required and verifies the email address.', 'cforms'); ?>"><span class="abbr"><?php _e('Is E-Mail', 'cforms'); ?></span></li>
			<li class="field6th" title="<?php _e('Clears the field (default value if provided) upon focus.', 'cforms'); ?>"><span class="abbr"><?php _e('Auto Clear', 'cforms'); ?></span></li>
			<li class="field7th" title="<?php _e('Grey\'s out a form field (field will be disabled).', 'cforms'); ?>"><span class="abbr"><?php _e('Disabled', 'cforms'); ?></span></li>
		</ul>
			
			<div class="dbx-group" id="cformsfieldsbox">
					
						<?php
						// pre-check for verification field
						$ccboxused=false;
						$emailtoboxused=false;
						$verificationused=false;
						$captchaused=false;
						$fileupload=false; // only for hide/show options
						
						for($i = 1; $i <= $field_count; $i++) {

								$allfields[$i] = get_option('cforms'.$no.'_count_field_' . $i);
								
								if ( strpos($allfields[$i],'verification') )
										$verificationused = true;
								if ( strpos($allfields[$i],'captcha') )
										$captchaused = true;
								if ( strpos($allfields[$i],'emailtobox') )
										$emailtoboxused = true;
								if ( strpos($allfields[$i],'ccbox') )
										$ccboxused = true;
								if ( strpos($allfields[$i],'upload') )
										$fileupload = true;

						}

						$alternate=' rowalt';
						
						for($i = 1; $i <= $field_count; $i++) {
								$field_stat = explode('$#$', $allfields[$i]);
								$field_name = __('New Field', 'cforms');
								$field_type = 'textfield';
								$field_required = '0';
								$field_emailcheck = '0';
								$field_clear = '0';
								$field_disabled = '0';

								if(sizeof($field_stat) >= 3) {
									$field_name = stripslashes(htmlspecialchars($field_stat[0]));
									$field_type = $field_stat[1];
									$field_required = $field_stat[2];
									$field_emailcheck = $field_stat[3];
									$field_clear = $field_stat[4];
									$field_disabled = $field_stat[5];
								}
								else if(sizeof($field_stat) == 1){
									add_option('cforms'.$no.'_count_field_' . $i, __('New Field$#$textfield$#$0$#$0$#$0$#$0', 'cforms'));
								}

            switch ( $field_type ) {
							case 'emailtobox':
										$specialclass = 'style="background:#CBDDFE"';
										break;
							case 'ccbox':
										$specialclass = 'style="background:#D8FFCA"';
										break;
							case 'verification':
							case 'captcha':
										$specialclass = 'style="background:#FFCDCA"';
										break;
							case 'textonly':
										$specialclass = 'style="background:#f0f0f0"';
										break;
							case 'fieldsetstart':
							case 'fieldsetend':
										$specialclass = 'style="background:#ECFEA5"';
										break;
							default:
										$specialclass = '';
										break;
						}
						
						$alternate = $alternate==' rowalt'?'':' rowalt';
						?>

						<div class="dbx-box">
									<a class="dbx-handle" href="javascript:void(0)" style="float:left;">&nbsp;</a>
									<ul class="dbx-cforms<?php echo $alternate?>">
										<li class="fieldno"><?php echo $i; ?></li>
										<li class="fieldname"><input class="inpfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_name" id="field_<?php echo($i); ?>_name" size="30" value="<?php echo ($field_type == 'fieldsetend' || $field_type == 'verification')?'--':$field_name; ?>" /></li>
										<li class="fieldtype">
												<select class="selfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_type" id="field_<?php echo($i); ?>_type">
													<option value="textonly" <?php echo($field_type == 'textonly'?' selected="selected"':''); ?>><?php _e('Text only (no input)', 'cforms'); ?></option>
													<option value="textfield" <?php echo($field_type == 'textfield'?' selected="selected"':''); ?>><?php _e('Single line of text', 'cforms'); ?></option>
													<option value="textarea" <?php echo($field_type == 'textarea'?' selected="selected"':''); ?>><?php _e('Multiple lines of text', 'cforms'); ?></option>
													<option value="checkbox" <?php echo($field_type == 'checkbox'?' selected="selected"':''); ?>><?php _e('Check Box', 'cforms'); ?></option>
													<option value="checkboxgroup" <?php echo($field_type == 'checkboxgroup'?' selected="selected"':''); ?>><?php _e('Check Box Group', 'cforms'); ?></option>
													<option value="selectbox" <?php echo($field_type == 'selectbox'?' selected="selected"':''); ?>><?php _e('Select Box', 'cforms'); ?></option>
													<option value="multiselectbox" <?php echo($field_type == 'multiselectbox'?' selected="selected"':''); ?>><?php _e('Multi Select Box', 'cforms'); ?></option>
													<option value="radiobuttons" <?php echo($field_type == 'radiobuttons'?' selected="selected"':''); ?>><?php _e('Radio Buttons', 'cforms'); ?></option>
													<?php if ( !$ccboxused || $field_type=="ccbox" ) : ?>
													<option value="ccbox" <?php echo($field_type == 'ccbox'?' selected="selected"':''); ?>><?php _e('CC: option for user', 'cforms'); ?></option>
													<?php	endif; ?>
													<?php if ( !$emailtoboxused || $field_type=="emailtobox" ) : ?>
													<option value="emailtobox" <?php echo($field_type == 'emailtobox'?' selected="selected"':''); ?>><?php _e('Multiple Recipients', 'cforms'); ?></option>
													<?php	endif; ?>
													<?php if ( !$verificationused || $field_type=="verification" ) : ?>
														<option value="verification" <?php echo($field_type == 'verification'?' selected="selected"':''); ?>><?php _e('Visitor verification (Q&amp;A)', 'cforms'); ?></option>
													<?php	endif; ?>
													<?php if ( !$captchaused || $field_type=="captcha" ) : ?>
														<option value="captcha" <?php echo($field_type == 'captcha'?' selected="selected"':''); ?>><?php _e('Captcha verification (image)', 'cforms'); ?></option>
													<?php	endif; ?>
													<?php //if ( !$uploadused || $field_type=="upload" ) : ?>
														<option value="upload" <?php echo($field_type == 'upload'?' selected="selected"':''); ?>><?php _e('File Upload Box', 'cforms'); ?></option>
													<?php //endif; ?>
													<option value="fieldsetstart" <?php echo($field_type == 'fieldsetstart'?' selected="selected"':''); ?>><?php _e('New Fieldset', 'cforms'); ?></option>
													<option value="fieldsetend" <?php echo($field_type == 'fieldsetend'?' selected="selected"':''); ?>><?php _e('End Fieldset', 'cforms'); ?></option>
												</select>
						            <?php
						              if($field_count > 1)
									          echo '<input class="xbutton" type="submit" name="DeleteField'.$i.'" value="" title="'.__('Remove input field', 'cforms').'" alt="'.__('Remove input field', 'cforms').'"/>';
						            ?>
										</li>
										<li class="fieldisreq">
											<?php if( in_array($field_type,array('checkboxgroup', 'radiobuttons','fieldsetstart','fieldsetend','ccbox','captcha','verification','textonly')) ) echo '&nbsp;'; else {
													?><input class="chkfld" type="checkbox" name="field_<?php echo($i); ?>_required" value="required"  <?php echo($field_required == '1'?' checked="checked"':''); ?> /><?php }?>
										&nbsp;</li>
										<li class="fieldisemail">
											<?php if( ! in_array($field_type,array('textfield')) ) echo '&nbsp;'; else { ?>
														<input class="chkfld" type="checkbox" name="field_<?php echo($i); ?>_emailcheck" value="required"  <?php echo($field_emailcheck == '1'?' checked="checked"':''); ?> /><?php }?>
										&nbsp;</li>
										<li class="fieldclear">
											<?php if( ! in_array($field_type,array('textarea','textfield')) ) echo '&nbsp;'; else { ?>
														<input class="chkfld" type="checkbox" name="field_<?php echo($i); ?>_clear" value="required"  <?php echo($field_clear == '1'?' checked="checked"':''); ?> /><?php }?>
										&nbsp;</li>
										<li class="fielddisabled">
											<?php if( ! in_array($field_type,array('textarea','textfield','checkbox','selectbox','multiselectbox','radiobuttons','upload')) ) echo '&nbsp;'; else { ?>
														<input class="chkfld" type="checkbox" name="field_<?php echo($i); ?>_disabled" value="required"  <?php echo($field_disabled == '1'?' checked="checked"':''); ?> /><?php }?>
										&nbsp;</li>
									</ul>
						</div> <!--box-->
						
				<?php	}	// for  ?>
			</div> <!--group-->

		<div class="addfieldbox"><input type="submit" name="AddField" class="allbuttons addfield" value="<?php _e('Add another field &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchorfields';" /></div>
		<input type="hidden" name="field_order" value="" />
		<input type="hidden" name="field_count_submit" value="<?php echo($field_count); ?>" />
	</fieldset>

	<p align="center" style="font-size:11px;margin:20px 0 0;"><em><?php _e('Grab the circle to the left of each field to change the order of the fields (drag&amp;drop)', 'cforms'); ?></em></p>



		<fieldset id="fileupload" class="cformsoptions <?php if( !$fileupload) echo "hidden"; ?>">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit5" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#fileupload';" /><?php _e('File Upload Settings', 'cforms') ?></p>

			<p>
				<?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#upload',__('Configure and double-check these settings in case you are adding a "<code class="codehighlight">File Upload Box</code>" to your form (also see the <a href="[url]">Help!</a> for further information).', 'cforms')); ?>
				<?php echo str_replace('[url]','?page='.$plugindir.'/cforms-global-settings.php#upload',__('You may also want to verify the global, file upload specific  <a href="[url]">error messages</a>.', 'cforms')); ?>
			</p>

		    <?php
			if ( !file_exists(get_option('cforms'.$no.'_upload_dir')) ) {
		        echo '<div id="message" class="updated fade"><p>' . __('Can\'t find the specified <strong>Upload Directory</strong> ! Please verify that it exists!', 'cforms' ) . '</p></div>';
		    }
			?>
			
			<div class="optionsbox" style="margin-top:15px;">
				<div class="optionsboxL"><label for="cforms_upload_dir"><strong><?php _e('Upload directory', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" id="cforms_upload_dir" name="cforms_upload_dir" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_dir'))); ?>"/> <?php _e('[make sure the dir exists!]', 'cforms') ?></div>
			</div>

			<div class="optionsbox" style="margin-top:15px;">
				<div class="optionsboxL"><label for="cforms_upload_ext"><strong><?php _e('Allowed file extensions', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" id="cforms_upload_ext" name="cforms_upload_ext" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_ext'))); ?>"/> <?php _e('[empty=all files are allowed]', 'cforms') ?></div>
			</div>

			<div class="optionsbox" style="margin-top:3px;">
				<div class="optionsboxL"><label for="cforms_upload_size"><strong><?php _e('Maximum file size<br />in kilobyte', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" id="cforms_upload_size" name="cforms_upload_size" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_size'))); ?>"/></div>
			</div>

			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_noattachments"><strong><?php _e('Do not email attachments', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_noattachments" name="cforms_noattachments" <?php if(get_option('cforms'.$no.'_noattachments')=="1") echo "checked=\"checked\""; ?>/><br /><?php echo str_replace('[url]','?page='. $plugindir.'/cforms-global-settings.php#tracking',__('<u>Note</u>: Attachments are stored on the server &amp; can be accessed via the <a href="[url]">cforms tracking</a> tables.', 'cforms')); ?></div>
			</div>
			
		</fieldset>
			

		<fieldset class="cformsoptions" id="anchormessage">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit3" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchormessage';" /><?php _e('Redirection, Messages, Text and Button Label', 'cforms') ?></p>

			<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-global-settings.php#visitorv',__('These are the messages displayed to the user on successful (or failed) form submission. These messages are form specific, a general message for entering a wrong <strong>visitor verification code</strong> can be found <a href="[url]">here</a>.', 'cforms')); ?></p>

			<p class="ex"><?php _e('Please <strong>do not use HTML code</strong> in these fields instead, adjust the (cforms.css) stylesheet to your needs. Line breaks and quotes are fine. The actual success &amp; failure message can in addition be shown in a popup <strong>alert box</strong>. This may come handy in case your forms are extremely long and the user can\'t see the submission status at the top anymore.', 'cforms') ?></p>

			<br />
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_working"><strong><?php _e('Waiting message', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><input name="cforms_working" id="cforms_working" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_working')));  ?>" /></div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><label for="cforms_required"><strong><?php _e('"required" label', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_required" id="cforms_required" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_required'))); ?>"/></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_emailrequired"><strong><?php _e('"email required" label', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_emailrequired" id="cforms_emailrequired" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_emailrequired'))); ?>"/></div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><label for="cforms_success"><?php _e('<strong>Success message</strong><br />form filled out correctly', 'cforms'); ?></label></div>
				<div class="optionsboxR">
					<textarea style="float:left" name="cforms_success" id="cforms_success"><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_success'))); ?></textarea>
					<div style="float:left"><input type="checkbox" id="cforms_popup1" name="cforms_popup1" <?php if(substr(get_option('cforms'.$no.'_popup'),0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup1"><?php _e('Opt. Popup Msg', 'cforms'); ?></label></div>
				</div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_failure"><?php _e('<strong>Failure message</strong><br />missing fields or wrong field formats<br />(regular expr.)', 'cforms'); ?></label></div>
					<div class="optionsboxR"><textarea style="float:left" name="cforms_failure" id="cforms_failure" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_failure'))); ?></textarea>
					<div style="float:left"><input type="checkbox" id="cforms_popup2" name="cforms_popup2" <?php if(substr(get_option('cforms'.$no.'_popup'),1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup2"><?php _e('Opt. Popup Msg', 'cforms'); ?></label></div>
				</div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><label for="cforms_showposa"><strong><?php _e('Show messages', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR">
					<input type="checkbox" id="cforms_showposa" name="cforms_showposa" <?php if(substr(get_option('cforms'.$no.'_showpos'),0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposa"><?php _e('Above form', 'cforms'); ?></label><br />
					<input type="checkbox" id="cforms_showposb" name="cforms_showposb" <?php if(substr(get_option('cforms'.$no.'_showpos'),1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposb"><?php _e('Below form', 'cforms'); ?></label>
				</div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><label for="cforms_redirect"><?php _e('<strong>Redirect</strong><br />to page:', 'cforms'); ?></label></div>
				<div class="optionsboxR">
					<input type="checkbox" id="cforms_redirect" name="cforms_redirect" <?php if(get_option('cforms'.$no.'_redirect')) echo "checked=\"checked\""; ?>/><label for="cforms_redirect"><?php _e('Enable alternative success page (redirect)', 'cforms'); ?></label><br />
					<input name="cforms_redirect_page" id="cforms_redirect_page" value="<?php echo (get_option('cforms'.$no.'_redirect_page'));  ?>" />
				</div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><label for="cforms_submit_text"><strong><?php _e('Submit button text', 'cforms'); ?></strong></label></div>
				<div class="optionsboxR"><input name="cforms_submit_text" id="cforms_submit_text" value="<?php echo (get_option('cforms'.$no.'_submit_text'));  ?>" /></div>
			</div>

			<p class="ex" style="margin-top:50px;"><?php _e('If you enable an alternative <strong>form action</strong> you <u>will loose any cforms application logic</u> (spam security, field validation, DB tracking etc.) in non-ajax mode! The settings below are really only for developers that require additional capabilities around forwarding of form data. These settings turn cforms into a front-end "form builder" so to speak.', 'cforms') ?></p>

			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><label for="cforms_action"><?php _e('<strong>Redirect form data</strong> to <br />an alternative page:', 'cforms'); ?></label></div>
				<div class="optionsboxR">
					<input type="checkbox" id="cforms_action" name="cforms_action" <?php if(get_option('cforms'.$no.'_action')) echo "checked=\"checked\""; ?>/><label for="cforms_action"><?php _e('Enable alternative form action!', 'cforms'); ?></label><br />
					<input name="cforms_action_page" id="cforms_action_page" value="<?php echo (get_option('cforms'.$no.'_action_page'));  ?>" />
				</div>
			</div>

		</fieldset>



		<fieldset class="cformsoptions" id="anchoremail">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit4" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchoremail';" /><?php _e('Core Form Admin / Email Options', 'cforms') ?></p>

			<p><?php _e('These settings will be used for the email sent to you. Both "<strong>xx@yy.zz</strong>" and "<strong>abc &lt;xx@yy.zz&gt;</strong>" formats are valid, but check if your mailserver does accept the format of choice!"', 'cforms') ?></p>
			<p><?php _e('The default FROM: address is based on your blog\'s name and the WP default address. It can be changed, but I highly recommend you do not, as it may render the plugin useless. If you do change the FROM: address, triple check if all admin emails are being sent/received! ', 'cforms') ?></p>

			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_fromemail"><strong><?php _e('FROM: email address', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_fromemail" id="cforms_fromemail" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_fromemail'))); ?>" /></div>
			</div>

			<p class="ex"><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#multirecipients',__('More than one "<strong>form admin</strong>"? Simply add additional email addresses separated by a <strong style="color:red">comma</strong>. &nbsp; <em><u>Note:</u></em> &nbsp; If you want the visitor to choose from any of these per select box, you need to add a corresponding "<code class="codehighlight">Multiple Recipients</code>" input field <a href="#anchorfields">above</a> (see the HELP section for <a href="[url]">details</a> on the <em>field name</em> format expected!). If <strong>no</strong> "Multiple Recipients" input field is defined above, the submitted form data will go out to <strong>every email recipient</strong>!', 'cforms')); ?></p>
			<br />
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_email"><strong><?php _e('Admin email address(es)', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_email" id="cforms_email" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_email'))); ?>" /></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_bcc"><strong><?php _e('BCC', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_bcc" id="cforms_bcc" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_bcc'))); ?>" /></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_subject"><strong><?php _e('Subject admin email', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_subject" id="cforms_subject" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_subject'))); ?>" /> <?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('<a href="[url]">Variables</a> allowed.', 'cforms')); ?></div>
			</div>
						
		</fieldset>



		<fieldset class="cformsoptions" id="emailoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit6" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#emailoptions';" /><?php _e('Admin Email Message Options', 'cforms') ?></p>

			<p><?php _e('Emails sent to the admin and the submitting user (CC or auto confirmation) can be both text and HTML formatted. The TXT part is required, HTML optional.', 'cforms'); ?></p>
			<p><?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('Below you\'ll find the settings for both the <strong>TXT part</strong> of the admin email as well as the <strong>optional HTML part</strong> of the message. Both areas permit the use of any of the <strong>pre-defined variables</strong> or <strong>data from input fields</strong>. <a href="[url]">Please see the documentation on the HELP page</a> (including HTML message examples!).', 'cforms')); ?></p>

			<div class="optionsbox" style="margin-top:30px;">
				<div class="optionsboxL"><label for="cforms_header"><?php _e('<strong>Admin TEXT message</strong> part<br />(header)', 'cforms') ?></label></div>
				<div class="optionsboxRxl"><textarea name="cforms_header" id="cforms_header" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_header'))); ?></textarea> <?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('<a href="[url]">Variables</a> allowed.', 'cforms')); ?></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_formdata_txt" name="cforms_formdata_txt" <?php if(substr(get_option('cforms'.$no.'_formdata'),0,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_txt"><?php _e('<strong>Include</strong> <em>pre formatted</em> form input at the bottom of the TXT part', 'cforms') ?></label></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL">&nbsp;</div>
				<div class="optionsboxR"><input type="text" name="cforms_space" id="cforms_space" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_space'))); ?>" /><label for="cforms_space"> &nbsp; <?php _e('(# characters) : spacing between labels &amp; data, for plain txt version only', 'cforms') ?></label></div>
			</div>

			<div class="optionsbox" style="margin-top:20px;">
				<div class="optionsboxL"><label for="cforms_admin_html"><?php _e('<strong>Enable HTML</strong>', 'cforms') ?></label></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_admin_html" name="cforms_admin_html" <?php if($o=substr(get_option('cforms'.$no.'_formdata'),2,1)=='1') echo "checked=\"checked\""; ?>/></div>
			</div>
			
			<div class="optionsbox <?php if( !$o=='1' ) echo "hidden"; ?>">
				<div class="optionsboxL"><label for="cforms_header_html"><?php _e('<strong>Admin HTML message</strong> part<br />(header)', 'cforms') ?></label></div>
				<div class="optionsboxRxl"><textarea name="cforms_header_html" id="cforms_header_html" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_header_html'))); ?></textarea> <?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('<a href="[url]">Variables</a> allowed.', 'cforms')); ?></div>
			</div>
			<div class="optionsbox <?php if( !$o=='1' ) echo "hidden"; ?>"">
				<div class="optionsboxL"></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_formdata_html" name="cforms_formdata_html" <?php if(substr(get_option('cforms'.$no.'_formdata'),1,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_html"><?php _e('<strong>Include</strong> <em>pre formatted</em> form input at the bottom of the HTML part', 'cforms') ?></label></div>
			</div>
			<p class="ex" style="margin-top:30px;"><?php _e('<strong><u>Note</u></strong>: To avoid sending ALL submitted user data (especially of very long forms) to the form admin simply <strong>uncheck</strong> "<em>Include pre formatted form input ...</em>" and instead specify the fields you\'d like to receive via the use of <strong>custom variables</strong>.', 'cforms'); ?></p>
						
		</fieldset>


		<fieldset class="cformsoptions <?php if( !$ccboxused ) echo "hidden"; ?>" id="cc">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><?php _e('CC Settings', 'cforms') ?></p>

			<p><?php _e('This is the subject of the CC email that goes out the user submitting the form and as such requires the <strong>CC:</strong> field in your form definition above.', 'cforms') ?></p>

			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_ccsubject"><strong><?php _e('Subject CC', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_ccsubject" id="cforms_ccsubject" value="<?php $t=explode('$#$',get_option('cforms'.$no.'_csubject')); echo stripslashes(htmlspecialchars($t[1])); ?>" /> <?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('<a href="[url]">Variables</a> allowed.', 'cforms')); ?></div>
			</div>
			
		</fieldset>
		

		<fieldset class="cformsoptions" id="autoconf">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit7" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#autoconf';" /><?php _e('Auto Confirmation', 'cforms') ?></p>

			<p><?php _e('These settings apply to an auto response/confirmation sent to the visitor. If your form includes a "<code class="codehighlight">CC me</code>" field <strong>AND</strong> the visitor selected it, no extra confirmation email is sent!', 'cforms') ?></p>

			<div class="optionsbox" style="margin-top:15px;">
				<div class="optionsboxL"></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_confirm" name="cforms_confirm" <?php if($o=get_option('cforms'.$no.'_confirm')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_confirm"><strong><?php _e('Activate auto confirmation', 'cforms') ?></strong></label></div>
			</div>


			<div class="optionsbox <?php if( !$o=="1" ) echo "hidden"; ?>">
				<div class="optionsboxL"><label for="cforms_csubject"><strong><?php _e('Subject auto confirmation', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" name="cforms_csubject" id="cforms_csubject" value="<?php $t=explode('$#$',get_option('cforms'.$no.'_csubject')); echo stripslashes(htmlspecialchars($t[0])); ?>" /> <?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('<a href="[url]">Variables</a> allowed.', 'cforms')); ?></div>
			</div>
			<div class="optionsbox <?php if( !$o=="1" ) echo "hidden"; ?>" style="margin-top:10px;">
				<div class="optionsboxL"><label for="cforms_cmsg"><?php _e('<strong>TXT message</strong> part', 'cforms') ?></label></div>
				<div class="optionsboxRxl"><textarea name="cforms_cmsg" id="cforms_cmsg" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_cmsg'))); ?></textarea>  <?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('<a href="[url]">Variables</a> allowed.', 'cforms')); ?></div>
			</div>
			<div class="optionsbox <?php if( !$o=="1" ) echo "hidden"; ?>" style="margin-top:15px;">
				<div class="optionsboxL"><label for="cforms_user_html"><?php _e('<strong>Enable HTML</strong>', 'cforms') ?></label></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_user_html" name="cforms_user_html" <?php if($o2=substr(get_option('cforms'.$no.'_formdata'),3,1)=='1') echo "checked=\"checked\""; ?>/></div>
			</div>
			<div class="optionsbox <?php if( !$o=="1" || !$o2=="1") echo "hidden"; ?>">
				<div class="optionsboxL"><label for="cforms_cmsg_html"><?php _e('<strong>HTML message</strong> part', 'cforms') ?></label></div>
				<div class="optionsboxRxl"><textarea name="cforms_cmsg_html" id="cforms_cmsg_html" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_cmsg_html'))); ?></textarea> <?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#variables',__('<a href="[url]">Variables</a> allowed.', 'cforms')); ?></div>
			</div>

		</fieldset>

		<fieldset class="cformsoptions" id="readnotify">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit4" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#autoconf';" /><?php _e('3rd Party Read-Notification Support', 'cforms') ?></p>

			<p><?php _e('If you\'d like to utilize 3rd party email tracking such as <strong>readnotify.com</strong> or <strong>didtheyreadit.com</strong>, add the respective suffix (e.g.: <code class="codehighlight">.readnotify.com</code>) here:', 'cforms') ?></p>

			<div class="optionsbox">
				<div class="optionsboxL"><label for="cforms_tracking"><strong><?php _e('Suffix for email tracking', 'cforms') ?></strong></label></div>
				<div class="optionsboxR"><input type="text" id="cforms_tracking" name="cforms_tracking" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_tracking'))); ?>"/></div>
			</div>

		</fieldset>	
		

		</form>


	<?php cforms_footer(); ?>
</div>
