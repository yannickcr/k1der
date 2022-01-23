<?php
		/*file upload*/
		add_option('cforms_upload_dir', ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/attachments');
		add_option('cforms_upload_ext', 'txt,zip,doc,rtf,xls');
		add_option('cforms_upload_size', '1024');
		
		add_option('cforms_upload_err1', __('Generic file upload error. Please try again', 'cforms'));
		add_option('cforms_upload_err2', __('File is empty. Please upload something more substantial.', 'cforms'));
		add_option('cforms_upload_err3', __('Sorry, file is too large. You may try to zip your file.', 'cforms'));
		add_option('cforms_upload_err4', __('File upload failed. Please try again or contact the blog admin.', 'cforms'));
		add_option('cforms_upload_err5', __('File not accepted, file type not allowed.', 'cforms'));

		/*for default form*/
		add_option('cforms_count_field_1', __('My Fieldset$#$fieldsetstart$#$0$#$0$#$0$#$0', 'cforms'));
		add_option('cforms_count_field_2', __('Your Name|Your Name$#$textfield$#$1$#$0$#$1$#$0', 'cforms'));
		add_option('cforms_count_field_3', __('Email$#$textfield$#$1$#$1$#$0$#$0', 'cforms'));
		add_option('cforms_count_field_4', __('Website|http://$#$textfield$#$0$#$0$#$0$#$0', 'cforms'));
		add_option('cforms_count_field_5', __('Message$#$textarea$#$0$#$0$#$0$#$0', 'cforms'));

		/*form verification questions*/
		add_option('cforms_sec_qa', __('What color is snow?=white', 'cforms'). "\r\n" . __('The color of grass is=green', 'cforms'). "\r\n" . __('Ten minus five equals=five', 'cforms'));
		add_option('cforms_formcount', '1');
		add_option('cforms_show_quicktag', '1');
		add_option('cforms_count_fields', '5');
		add_option('cforms_required', __('(required)', 'cforms'));
		add_option('cforms_emailrequired', __('(valid email required)', 'cforms'));

		add_option('cforms_confirm', '0');
		add_option('cforms_ajax', '1');
		add_option('cforms_fname', __('Your default form', 'cforms'));
		add_option('cforms_csubject', __('Re: Your note', 'cforms').'$#$'.__('Re: Submitted form (copy)', 'cforms'));
		add_option('cforms_cmsg', __('Dear {Your Name},', 'cforms') . "\n" . __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n");
		add_option('cforms_cmsg_html',  __('<div style="color:#ccc; border-bottom:1px solid #ccc"><strong>auto confirmation message, {Date}</strong></div> ', 'cforms') . "\n<br />\n" . __('<p><strong>Dear {Your Name},</strong></p>', 'cforms') . "\n". __('<p>Thank you for your note!</p>', 'cforms') . "\n". __('<p>We will get back to you as soon as possible.</p>', 'cforms') . "\n\n");
		add_option('cforms_email', get_bloginfo('admin_email'));
		add_option('cforms_fromemail', '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>' );
		add_option('cforms_bcc', '');

		add_option('cforms_header', __('A new submission (form: "{Form Name}")', 'cforms') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms') . "\r\n" . __('Via: {Page}', 'cforms') . "\r\n" . __('By {IP} (visitor IP)', 'cforms') . ".\r\n" . ".\r\n" );		
		add_option('cforms_header_html', '<p style="background:#fafafa; text-align:center; font:10px arial">' . __('a form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '</p>' );		
		add_option('cforms_formdata', '1111');
		add_option('cforms_space', '30');
		add_option('cforms_noattachments', '0');

		add_option('cforms_subject', __('A comment from {Your Name}', 'cforms'));
		add_option('cforms_submit_text', __('Send Comment', 'cforms'));
		add_option('cforms_success', __('Thank you for your comment!', 'cforms'));
		add_option('cforms_failure', __('Please fill in all the required fields.', 'cforms'));
		add_option('cforms_codeerr', __('Please double-check your verification code.', 'cforms'));
		add_option('cforms_working', __('One moment please...', 'cforms'));
		add_option('cforms_popup', 'nn');
		add_option('cforms_showpos', 'yn');
		add_option('cforms_database', '0');

		add_option('cforms_css', 'cforms.css');
		add_option('cforms_labelID', '0');
		
		add_option('cforms_redirect', '0');
		add_option('cforms_redirect_page', 'http://redirect.to.this.page');		
		add_option('cforms_action', '0');
		add_option('cforms_action_page', 'http://');		
		
		add_option('cforms_tracking', '');
		
		// updates existing tracking db
		if ( $wpdb->get_var("show tables like '$wpdb->cformsdata'") == $wpdb->cformsdata ) {
			// fetch table column structure from the database
			$tablefields = $wpdb->get_results("DESCRIBE {$wpdb->cformsdata};");

            $afield = array();
			foreach($tablefields as $field)
                array_push ($afield,$field->Field); 
            
            if ( !in_array('f_id', $afield) ) {
    			$sql = "ALTER TABLE " . $wpdb->cformsdata . " 
    					  ADD f_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    					  CHANGE field_name field_name varchar(100) NOT NULL default '';";
    			$wpdb->query($sql);
              	echo '<div id="message" class="updated fade"><p><strong>' . __('Existing cforms Tracking Tables updated.', 'cforms') . '</strong></p></div>';
            }            
        }
?>
