<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > UserCP functions library
|   > Module written by Matt Mecham
|   > Date started: 20th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Fri 21 May 2004
+--------------------------------------------------------------------------
*/


class usercp_functions {

	var $class;
	var $image;

	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function usercp_functions($class)
	{
		$this->class = &$class;

		require_once( KERNEL_PATH.'class_image.php' );
		$this->image = new class_image();
	}

	/*-------------------------------------------------------------------------*/
	// HANDLE SUBSCRIPTION START
	/*-------------------------------------------------------------------------*/

	function subs_choose($save="")
	{
		global $ibforums, $DB, $std, $print, $forums;

		//-----------------------------------------
		// Topic - forum - what?
		//-----------------------------------------

		$method = $ibforums->input['method'] == 'forum' ? 'forum' : 'topic';
		$tid    = intval($ibforums->input['tid']);
		$fid    = intval($ibforums->input['fid']);

		if ( $method == 'topic' )
		{
			//-----------------------------------------
			// Get the details from the DB (TOPIC)
			//-----------------------------------------

			$topic = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.$tid ) );

			if ( ! $topic['tid'] )
			{
				$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
			}

			$forum = $forums->forum_by_id[ $topic['forum_id'] ];
		}
		else
		{
			//-----------------------------------------
			// Get the details (FORUM)
			//-----------------------------------------

			$forum = $forums->forum_by_id[ $fid ];
		}

		//-----------------------------------------
		// Permy check
		//-----------------------------------------

		if ( $std->check_perms( $forums->forum_by_id[ $forum['id'] ]['read_perms'] ) != TRUE )
		{
			$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
		}

		//-----------------------------------------
		// Passy check
		//-----------------------------------------

		if ( $forum['password'] != "" )
		{
			if ( $forums->forums_compare_password( $forum['id'] ) != TRUE )
			{
				$std->Error( array( LEVEL => 1, MSG => 'forum_no_access') );
			}
		}

		//-----------------------------------------
		// Have we already subscribed?
		//-----------------------------------------

		if ( $method == 'forum' )
		{
			$tmp = $DB->simple_exec_query( array( 'select' => 'frid as tmpid',
												  'from'   => 'forum_tracker',
												  'where'  => "forum_id={$fid} AND member_id=".$ibforums->member['id'] ) );
		}
		else
		{
			$tmp = $DB->simple_exec_query( array( 'select' => 'trid as tmpid',
												  'from'   => 'tracker',
												  'where'  => "topic_id={$tid} AND member_id=".$ibforums->member['id'] ) );
		}

		if ( $tmp['tmpid'] )
		{
			$std->Error( array( LEVEL => 1, MSG => 'already_sub') );
		}

		//-----------------------------------------
		// What to do...
		//-----------------------------------------

		if ( ! $save )
		{
			//-----------------------------------------
			// Okay, lets do the HTML
			//-----------------------------------------

			$this->class->output .= $this->class->html->subs_show_choice_page( $forum, $topic, $method, $this->class->md5_check );
		}
		else
		{
			//-----------------------------------------
			// Method..
			//-----------------------------------------

			switch ($ibforums->input['emailtype'])
			{
				case 'immediate':
					$this->method = 'immediate';
					break;
				case 'delayed':
					$this->method = 'delayed';
					break;
				case 'none':
					$this->method = 'none';
					break;
				case 'daily':
					$this->method = 'daily';
					break;
				case 'weekly':
					$this->method = 'weekly';
					break;
				default:
					$this->method = 'delayed';
					break;
			}

			//-----------------------------------------
			// Add it to the DB
			//-----------------------------------------

			if ( $method == 'forum' )
			{

				$DB->do_insert( 'forum_tracker', array (
														 'member_id'        => $ibforums->member['id'],
														 'forum_id'         => $fid,
														 'start_date'       => time(),
														 'forum_track_type' => $this->method,
											  )       );

				$print->redirect_screen( $ibforums->lang['sub_added'], "showforum=$fid" );

			}
			else
			{
				$DB->do_insert( 'tracker',  array (
												   'member_id'        => $ibforums->member['id'],
												   'topic_id'         => $tid,
												   'start_date'       => time(),
												   'topic_track_type' => $this->method,
										)       );

				$print->redirect_screen( $ibforums->lang['sub_added'], "showtopic=$tid&st={$ibforums->input['st']}" );

			}
		}

		$this->class->page_title = $ibforums->lang['t_welcome'];
 		$this->class->nav        = array( "<a href='".$ibforums->base_url."act=usercp&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
	}


	/*-------------------------------------------------------------------------*/
	// HANDLE PHOTO OP'S
	/*-------------------------------------------------------------------------*/

	function do_photo()
	{
		global $ibforums, $DB, $std, $print;

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['auth_key'] != $this->class->md5_check )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
		}

		//-----------------------------------------
		// Did we press "remove"?
		//-----------------------------------------

		if ( $ibforums->input['remove'] )
		{
			$this->bash_uploaded_photos($ibforums->member['id']);

			$DB->simple_construct( array( 'select' => 'id', 'from' => 'member_extra', 'where' => "id=".$ibforums->member['id'] ) );
			$DB->simple_exec();

			if ( $DB->get_num_rows() )
			{
				$DB->do_update( 'member_extra', array( 'photo_location'   => '',
													   'photo_type'       => '',
													   'photo_dimensions' => '',
													 ), 'id='.$ibforums->member['id'] );
			}
			else
			{
				$DB->do_insert( 'member_extra', array( 'photo_location'   => '',
													   'photo_type'       => '',
													   'photo_dimensions' => '',
													   'id'               => $ibforums->member['id']
													 )  );
			}

			$print->redirect_screen( $ibforums->lang['photo_c_up'], "act=UserCP&CODE=photo" );
		}

		//-----------------------------------------
		// NO? CARRY ON!!
		//-----------------------------------------

		list($p_max, $p_width, $p_height) = explode( ":", $ibforums->member['g_photo_max_vars'] );

		//-----------------------------------------
		// Check to make sure we don't just have
		// http:// in the URL box..
		//-----------------------------------------

		if ( preg_match( "/^http:\/\/$/i", $ibforums->input['url_photo'] ) )
		{
			$ibforums->input['url_photo'] = "";
		}

		if ( empty($ibforums->input['url_photo']) )
		{
			//-----------------------------------------
			// Lets check for an uploaded photo..
			//-----------------------------------------

			if ($_FILES['upload_photo']['name'] != "" and ($_FILES['upload_photo']['name'] != "none") )
			{
				//-----------------------------------------
				// Are we allowed to upload this avatar?
				//-----------------------------------------

				if ( $p_max < 0 )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_av_upload' ) );
				}

				//-----------------------------------------
				// Remove any uploaded photos...
				//-----------------------------------------

				$this->bash_uploaded_photos($ibforums->member['id']);

				$real_name = 'photo-'.$ibforums->member['id'];
				$real_type = 'upload';

				//-----------------------------------------
				// Load the library
				//-----------------------------------------

				require_once( KERNEL_PATH.'class_upload.php' );
				$upload = new class_upload();

				//-----------------------------------------
				// Set up the variables
				//-----------------------------------------

				$upload->out_file_name     = 'photo-'.$ibforums->member['id'];
				$upload->out_file_dir      = $ibforums->vars['upload_dir'];
				$upload->max_file_size     = ($p_max * 1024) * 8;  // Allow xtra for compression
				$upload->upload_form_field = 'upload_photo';

				//-----------------------------------------
				// Populate allowed extensions
				//-----------------------------------------

				if ( is_array( $ibforums->cache['attachtypes'] ) and count( $ibforums->cache['attachtypes'] ) )
				{
					foreach( $ibforums->cache['attachtypes'] as $idx => $data )
					{
						if ( $data['atype_photo'] )
						{
							$upload->allowed_file_ext[] = $data['atype_extension'];
						}
					}
				}

				//-----------------------------------------
				// Upload...
				//-----------------------------------------

				$upload->upload_process();

				//-----------------------------------------
				// Error?
				//-----------------------------------------

				if ( $upload->error_no )
				{
					switch( $upload->error_no )
					{
						case 1:
							// No upload
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_failed' ) );
						case 2:
							// Invalid file ext
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_av_type' ) );
						case 3:
							// Too big...
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_to_big') );
						case 4:
							// Cannot move uploaded file
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_failed' ) );
					}
				}

				//-----------------------------------------
				// Still here?
				//-----------------------------------------

				$real_name = $upload->parsed_file_name;

				//-----------------------------------------
				// Check image size...
				//-----------------------------------------

				if ( ! $ibforums->vars['disable_ipbsize'] )
				{
					$this->image->in_type        = 'file';
					$this->image->out_type       = 'file';
					$this->image->in_file_dir    = $ibforums->vars['upload_dir'];
					$this->image->in_file_name   = $real_name;
					$this->image->out_file_name  = 'photos-'.$ibforums->member['id'];
					$this->image->desired_width  = $p_width;
					$this->image->desired_height = $p_height;

					$return = $this->image->generate_thumbnail();

					$im['img_width']  = $return['thumb_width'];
					$im['img_height'] = $return['thumb_height'];

					//-----------------------------------------
					// Do we have an attachment?
					//-----------------------------------------

					if ( strstr( $return['thumb_location'], 'photos-' ) )
					{
						//-----------------------------------------
						// Kill old and rename new...
						//-----------------------------------------

						@unlink( $ibforums->vars['upload_dir']."/".$real_name );

						$real_name = 'photo-'.$ibforums->member['id'].'.'.$this->image->file_extension;

						@rename( $ibforums->vars['upload_dir']."/".$return['thumb_location'], $ibforums->vars['upload_dir']."/".$real_name );
						@chmod(  $ibforums->vars['upload_dir']."/".$real_name, 0777 );
					}
				}
				else
				{
					$w = intval($ibforums->input['man_width'])  ? intval($ibforums->input['man_width'])  : $p_width;
					$h = intval($ibforums->input['man_height']) ? intval($ibforums->input['man_height']) : $p_height;
					$im['img_width']  = $w > $p_width  ? $p_width  : $w;
					$im['img_height'] = $h > $p_height ? $p_height : $h;
				}

				//-----------------------------------------
				// Check the file size (after compression)
				//-----------------------------------------

				if ( filesize( $ibforums->vars['upload_dir']."/".$real_name ) > ( $p_max * 1024 ) )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$real_name );

					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_to_big' ) );
				}

				$final_location  = $real_name;
				$final_type      = 'upload';
				$final_dimension = $im['img_width'].','.$im['img_height'];
			}
			else
			{
				//-----------------------------------------
				// URL field and upload field left blank.
				//-----------------------------------------

				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_photo_selected' ) );
			}
		}
		else
		{
			//-----------------------------------------
			// It's an entered URL 'ting man
			//-----------------------------------------

			if ( empty($ibforums->vars['allow_dynamic_img']) )
			{
				if ( preg_match( "/[?&;]/", $ibforums->input['url_photo'] ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'not_url_photo' ) );
				}
			}

			//-----------------------------------------
			// Check extension
			//-----------------------------------------

			$ext = explode ( ",", $ibforums->vars['photo_ext'] );
			$checked = 0;
			$av_ext = preg_replace( "/^.*\.(\S+)$/", "\\1", $ibforums->input['url_photo'] );

			foreach ($ext as $v )
			{
				if (strtolower($v) == strtolower($av_ext))
				{
					$checked = 1;
				}
			}

			if ($checked != 1)
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'photo_invalid_ext' ) );
			}

			//-----------------------------------------
			// Check image size...
			//-----------------------------------------

			$im = array();

			if ( ! $ibforums->vars['disable_ipbsize'] )
			{
				if ( ! $img_size = @GetImageSize( $ibforums->input['url_photo'] ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'not_url_photo' ) );
				}

				$im = $std->scale_image( array(
												'max_width'  => $p_width,
												'max_height' => $p_height,
												'cur_width'  => $img_size[0],
												'cur_height' => $img_size[1]
									   )      );
			}
			else
			{
				$w = intval($ibforums->input['man_width'])  ? intval($ibforums->input['man_width'])  : $p_width;
				$h = intval($ibforums->input['man_height']) ? intval($ibforums->input['man_height']) : $p_height;
				$im['img_width']  = $w > $p_width  ? $p_width  : $w;
				$im['img_height'] = $h > $p_height ? $p_height : $h;
			}

			//-----------------------------------------
			// Remove any uploaded images..
			//-----------------------------------------

			$this->bash_uploaded_photos($ibforums->member['id']);

			$final_location  = $ibforums->input['url_photo'];
			$final_type      = 'url';
			$final_dimension = $im['img_width'].','.$im['img_height'];
		}

		// Do we have an entry?

		$DB->simple_construct( array( 'select' => 'id', 'from' => 'member_extra', 'where' => "id=".$ibforums->member['id'] ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{
			$DB->do_update( 'member_extra', array( 'photo_location'   => $final_location,
												   'photo_type'       => $final_type,
												   'photo_dimensions' => $final_dimension,
												 ), 'id='.$ibforums->member['id'] );
		}
		else
		{
			$DB->do_insert( 'member_extra', array( 'photo_location'   => $final_location,
												   'photo_type'       => $final_type,
												   'photo_dimensions' => $final_dimension,
												   'id'               => $ibforums->member['id']
												 )  );
		}

		$print->redirect_screen( $ibforums->lang['photo_c_up'], "act=UserCP&CODE=photo" );

	}


	/*-------------------------------------------------------------------------*/
	// REMOVE UPLOADED PICCIES
	/*-------------------------------------------------------------------------*/

	function bash_uploaded_photos($id)
	{
		global $ibforums, $DB, $std, $print;

		foreach( array( 'swf', 'jpg', 'jpeg', 'gif', 'png' ) as $ext )
		{
			if ( @file_exists( $ibforums->vars['upload_dir']."/photo-".$id.".".$ext ) )
			{
				@unlink( $ibforums->vars['upload_dir']."/photo-".$id.".".$ext );
			}
		}
	}

	function bash_uploaded_avatars($id)
	{
		global $ibforums, $DB, $std, $print;

		foreach( array( 'swf', 'jpg', 'jpeg', 'gif', 'png' ) as $ext )
		{
			if ( @file_exists( $ibforums->vars['upload_dir']."/av-".$id.".".$ext ) )
			{
				@unlink( $ibforums->vars['upload_dir']."/av-".$id.".".$ext );
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// SAVE SKIN/LANG PREFS
	/*-------------------------------------------------------------------------*/

	function do_skin_langs()
	{
		global $ibforums, $DB, $std, $print, $HTTP_POST_VARS;

		// Check input for 1337 h/\x0r nonsense

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['auth_key'] != $this->class->md5_check )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
		}

		//-----------------------------------------

		if ( preg_match( "/\.\./", $ibforums->input['u_skin'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}
		//-----------------------------------------
		if ( preg_match( "/\.\./", $ibforums->input['u_language'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}

		//-----------------------------------------

		if ($ibforums->vars['allow_skins'] == 1)
		{

			$DB->query("SELECT sid FROM ibf_skins WHERE hidden <> 1 AND sid='".$ibforums->input['u_skin']."'");

			if (! $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'skin_not_found' ) );
			}

			$db_string = $DB->compile_db_update_string(  array (
																  'language'    => $ibforums->input['u_language'],
																  'skin       ' => $ibforums->input['u_skin'],
													  )         );
		}
		else
		{
			$db_string = $DB->compile_db_update_string(  array (
																  'language'    => $ibforums->input['u_language'],
													  )         );
		}

		//-----------------------------------------



		$DB->query("UPDATE ibf_members SET $db_string WHERE id='".$ibforums->member['id']."'");

		$print->redirect_screen( $ibforums->lang['set_updated'], "act=UserCP&CODE=06" );

	}

	/*-------------------------------------------------------------------------*/
	// Board prefs
	/*-------------------------------------------------------------------------*/

	function do_board_prefs()
	{
		global $ibforums, $DB, $std, $print, $HTTP_POST_VARS;

		// Check the input for naughties :D

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['auth_key'] != $this->class->md5_check )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
		}

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}
		//-----------------------------------------
		if ( ! preg_match( "/^[\-\d\.]+$/", $ibforums->input['u_timezone'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}
		//-----------------------------------------
		if ( ! preg_match( "/^\d+$/", $ibforums->input['VIEW_IMG'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}
		//-----------------------------------------
		if ( ! preg_match( "/^\d+$/", $ibforums->input['VIEW_SIGS'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}
		//-----------------------------------------
		if ( ! preg_match( "/^\d+$/", $ibforums->input['VIEW_AVS'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}
		//-----------------------------------------
		if ( ! preg_match( "/^\d+$/", $ibforums->input['DO_POPUP'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}

		/*if ( ! preg_match( "/^\d+$/", $ibforums->input['HIDE_SESS'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}*/

		if ( ! preg_match( "/^\d+$/", $ibforums->input['OPEN_QR'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
		}

		//-----------------------------------------

		if ($ibforums->vars['postpage_contents'] == "")
		{
			$ibforums->vars['postpage_contents'] = '5,10,15,20,25,30,35,40';
		}

		if ($ibforums->vars['topicpage_contents'] == "")
		{
			$ibforums->vars['topicpage_contents'] = '5,10,15,20,25,30,35,40';
		}

		$ibforums->vars['postpage_contents']  .= ",-1,";
		$ibforums->vars['topicpage_contents'] .= ",-1,";

		if (! preg_match( "/(^|,)".$ibforums->input['postpage'].",/", $ibforums->vars['postpage_contents'] ) )
		{
			$ibforums->input['postpage'] = '-1';
		}

		//-----------------------------------------

		if (! preg_match( "/(^|,)".$ibforums->input['topicpage'].",/", $ibforums->vars['topicpage_contents'] ) )
		{
			$ibforums->input['topicpage'] = '-1';
		}

		//-----------------------------------------

		$DB->do_update( 'members',  array (  'time_offset'  => $ibforums->input['u_timezone'],
											 'view_avs'     => $ibforums->input['VIEW_AVS'],
											 'view_sigs'    => $ibforums->input['VIEW_SIGS'],
											 'view_img'     => $ibforums->input['VIEW_IMG'],
											 'view_pop'     => $ibforums->input['DO_POPUP'],
											 'dst_in_use'   => $ibforums->input['DST'],
											 'view_prefs'   => $ibforums->input['postpage']."&".$ibforums->input['topicpage'],
								 ) , 'id='.$ibforums->member['id']  );

		if ($ibforums->input['OPEN_QR'] == 1)
		{
			$std->my_setcookie('open_qr', '1');
		}
		else
		{
			$std->my_setcookie('open_qr', '0');
		}

		$print->redirect_screen( $ibforums->lang['set_updated'], "act=UserCP&CODE=04" );

	}


	/*-------------------------------------------------------------------------*/
	// Complete email settings
	/*-------------------------------------------------------------------------*/

	function do_email_settings()
	{
		global $ibforums, $DB, $std, $print, $HTTP_POST_VARS;

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['auth_key'] != $this->class->md5_check )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
		}

		//-----------------------------------------

		//check and set the rest of the info

		foreach ( array('hide_email', 'admin_send', 'send_full_msg', 'pm_reminder', 'auto_track') as $v )
		{
			$ibforums->input[ $v ] = $std->is_number( $ibforums->input[ $v ] );

			if ( $ibforums->input[ $v ] < 1 )
			{
				$ibforums->input[ $v ] = 0;
			}
		}

		if ( $ibforums->input['auto_track'] )
		{
			$allowed = array( 'none', 'immediate', 'delayed', 'daily', 'weekly' );

 			if ( in_array( $ibforums->input['trackchoice'], $allowed ) )
 			{
 				$ibforums->input['auto_track'] = $ibforums->input['trackchoice'];
 			}
 		}

		$DB->do_update( 'members', array ( 'hide_email'         => $ibforums->input['hide_email'],
										   'email_full'         => $ibforums->input['send_full_msg'],
										   'email_pm'           => $ibforums->input['pm_reminder'],
										   'allow_admin_mails'  => $ibforums->input['admin_send'],
										   'auto_track'         => $ibforums->input['auto_track'],
					  )  ,'id='.$ibforums->member['id']       );

		$print->redirect_screen( $ibforums->lang['email_c_up'], "act=UserCP&CODE=02" );

	}

	/*-------------------------------------------------------------------------*/
	// Set gallery avatar
	/*-------------------------------------------------------------------------*/

	function set_internal_avatar()
	{
		global $ibforums, $DB, $std, $print, $HTTP_POST_VARS;

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['auth_key'] != $this->class->md5_check )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
		}

		//-----------------------------------------

		$real_choice = 'noavatar';
		$real_dims   = '';
		$real_dir    = "";
		$save_dir    = "";

		//-----------------------------------------
		// Check incoming..
		//-----------------------------------------

		$current_folder  = preg_replace( "/[^\s\w_-]/"             , "", urldecode($ibforums->input['current_folder']) );
		$selected_avatar = preg_replace( "/[^\s\w\._\-\[\]\(\)]/"  , "", urldecode($ibforums->input['avatar']) );

		//-----------------------------------------
		// Are we in a folder?
		//-----------------------------------------

		if ($current_folder == 'root')
		{
			$current_folder = "";
		}

		if ($current_folder != "")
		{
			$real_dir = "/".$current_folder;
			$save_dir = $current_folder."/";
		}

		//-----------------------------------------
		// Check it out!
		//-----------------------------------------

		$avatar_gallery = array();

		$dh = opendir( CACHE_PATH.'style_avatars'.$real_dir );

		while ( $file = readdir( $dh ) )
		{
			if ( !preg_match( "/^..?$|^index/i", $file ) )
			{
				$avatar_gallery[] = $file;
			}
		}
		closedir( $dh );

		if (!in_array( $selected_avatar, $avatar_gallery ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_avatar_selected' ) );
		}

		$final_string = $save_dir.$selected_avatar;

		// Update the DB

		$DB->do_update( 'member_extra', array( 'avatar_location' => $final_string, 'avatar_type' => 'local' ), 'id='.$ibforums->member['id'] );

		$print->redirect_screen( $ibforums->lang['av_c_up'], "act=UserCP&CODE=24" );

	}


	/*-------------------------------------------------------------------------*/
	// Save avatar
	/*-------------------------------------------------------------------------*/

	function do_avatar()
	{
		global $ibforums, $DB, $std, $print;

		//-----------------------------------------
		// Got attachment types?
		//-----------------------------------------

		if ( ! is_array( $ibforums->cache['attachtypes'] ) )
		{
			$ibforums->cache['attachtypes'] = array();

			$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
			}
		}

		$real_type = "";

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['auth_key'] != $this->class->md5_check )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
		}

		//-----------------------------------------
		// Did we press "remove"?
		//-----------------------------------------

		if ( $ibforums->input['remove'] )
		{
			$this->bash_uploaded_avatars($ibforums->member['id']);

			$DB->do_update( 'member_extra', array( 'avatar_location' => '',
												   'avatar_size'     => '',
												   'avatar_type'     => '',
												 ), 'id='.$ibforums->member['id'] );

			$print->redirect_screen( $ibforums->lang['av_c_up'], "act=UserCP&CODE=24" );

		}

		//-----------------------------------------
		// NO? CARRY ON!!
		//-----------------------------------------

		list($p_width, $p_height) = explode( "x", $ibforums->vars['avatar_dims'] );

		//-----------------------------------------
		// Check to make sure we don't just have
		// http:// in the URL box..
		//-----------------------------------------

		if ( preg_match( "/^http:\/\/$/i", $ibforums->input['url_avatar'] ) )
		{
			$ibforums->input['url_avatar'] = "";
		}

		if ( empty($ibforums->input['url_avatar']) )
		{
			//-----------------------------------------
			// Lets check for an uploaded photo..
			//-----------------------------------------

			if ($_FILES['upload_avatar']['name'] != "" and ($_FILES['upload_avatar']['name'] != "none") )
			{
				//-----------------------------------------
				// Are we allowed to upload this avatar?
				//-----------------------------------------

				if ( ($ibforums->member['g_avatar_upload'] != 1) or ($ibforums->vars['avup_size_max'] < 1) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_av_upload' ) );
				}

				//-----------------------------------------
				// Remove any uploaded avatars..
				//-----------------------------------------

				$this->bash_uploaded_avatars($ibforums->member['id']);

				$real_name = 'av-'.$ibforums->member['id'];
				$real_type = 'upload';

				//-----------------------------------------
				// Load the library
				//-----------------------------------------

				require_once( KERNEL_PATH.'class_upload.php' );
				$upload = new class_upload();

				//-----------------------------------------
				// Set up the variables
				//-----------------------------------------

				$upload->out_file_name     = 'av-'.$ibforums->member['id'];
				$upload->out_file_dir      = $ibforums->vars['upload_dir'];
				$upload->max_file_size     = ($ibforums->vars['avup_size_max'] * 1024) * 8;  // Allow xtra for compression
				$upload->upload_form_field = 'upload_avatar';

				//-----------------------------------------
				// Populate allowed extensions
				//-----------------------------------------

				if ( is_array( $ibforums->cache['attachtypes'] ) and count( $ibforums->cache['attachtypes'] ) )
				{
					foreach( $ibforums->cache['attachtypes'] as $idx => $data )
					{
						if ( $data['atype_photo'] )
						{
							$upload->allowed_file_ext[] = $data['atype_extension'];
						}
					}
				}

				//-----------------------------------------
				// Upload...
				//-----------------------------------------

				$upload->upload_process();

				//-----------------------------------------
				// Error?
				//-----------------------------------------

				if ( $upload->error_no )
				{
					switch( $upload->error_no )
					{
						case 1:
							// No upload
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_failed' ) );
						case 2:
							// Invalid file ext
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_av_type' ) );
						case 3:
							// Too big...
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_to_big') );
						case 4:
							// Cannot move uploaded file
							$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_failed' ) );
					}
				}

				if ( ( $upload->file_extension == 'swf' ) AND ($ibforums->vars['allow_flash'] != 1) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_flash_av' ) );
				}

				//-----------------------------------------
				// Still here?
				//-----------------------------------------

				$real_name = $upload->parsed_file_name;

				if ( ! $ibforums->vars['disable_ipbsize'] and $upload->file_extension != '.swf' )
				{
					$this->image->in_type        = 'file';
					$this->image->out_type       = 'file';
					$this->image->in_file_dir    = $ibforums->vars['upload_dir'];
					$this->image->in_file_name   = $real_name;
					$this->image->out_file_name  = 'avs-'.$ibforums->member['id'];
					$this->image->desired_width  = $p_width;
					$this->image->desired_height = $p_height;

					$return = $this->image->generate_thumbnail();

					$im['img_width']  = $return['thumb_width'];
					$im['img_height'] = $return['thumb_height'];

					//-----------------------------------------
					// Do we have an attachment?
					//-----------------------------------------

					if ( strstr( $return['thumb_location'], 'avs-' ) )
					{
						//-----------------------------------------
						// Kill old and rename new...
						//-----------------------------------------

						@unlink( $ibforums->vars['upload_dir']."/".$real_name );

						$real_name = 'av-'.$ibforums->member['id'].'.'.$this->image->file_extension;

						@rename( $ibforums->vars['upload_dir']."/".$return['thumb_location'], $ibforums->vars['upload_dir']."/".$real_name );
						@chmod(  $ibforums->vars['upload_dir']."/".$real_name, 0777 );
					}
				}
				else
				{
					$w = intval($ibforums->input['man_width'])  ? intval($ibforums->input['man_width'])  : $p_width;
					$h = intval($ibforums->input['man_height']) ? intval($ibforums->input['man_height']) : $p_height;
					$im['img_width']  = $w > $p_width  ? $p_width  : $w;
					$im['img_height'] = $h > $p_height ? $p_height : $h;
				}

				//-----------------------------------------
				// Check the file size (after compression)
				//-----------------------------------------

				if ( filesize( $ibforums->vars['upload_dir']."/".$real_name ) > ($ibforums->vars['avup_size_max']*1024))
				{
					@unlink( $ibforums->vars['upload_dir']."/".$real_name );

					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_to_big' ) );
				}

				//-----------------------------------------
				// Set the "real" avatar..
				//-----------------------------------------

				$real_choice = $real_name;
				$real_dims   = $im['img_width'].'x'.$im['img_height'];
			}
			else
			{
				//-----------------------------------------
				// URL field and upload field left blank.
				//-----------------------------------------

				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_avatar_selected' ) );

			}
		}
		else
		{
			//-----------------------------------------
			// It's an entered URL 'ting man
			//-----------------------------------------

			$ibforums->input['url_avatar'] = trim($ibforums->input['url_avatar']);

			if ( empty($ibforums->vars['allow_dynamic_img']) )
			{
				if ( preg_match( "/[?&;]/", $ibforums->input['url_avatar'] ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'avatar_invalid_url' ) );
				}
			}

			//-----------------------------------------
			// Check extension
			//-----------------------------------------

			$ext = explode ( ",", $ibforums->vars['avatar_ext'] );
			$checked = 0;
			$av_ext = preg_replace( "/^.*\.(\S+)$/", "\\1", $ibforums->input['url_avatar'] );

			foreach ($ext as $v )
			{
				if (strtolower($v) == strtolower($av_ext))
				{
					if ( ( $v == 'swf' ) AND ($ibforums->vars['allow_flash'] != 1) )
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_flash_av' ) );
					}

					$checked = 1;
				}
			}

			if ($checked != 1)
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'avatar_invalid_ext' ) );
			}

			//-----------------------------------------
			// Check image size...
			//-----------------------------------------

			$im = array();

			if ( ! $ibforums->vars['disable_ipbsize'] )
			{
				if ( ! $img_size = @GetImageSize( $ibforums->input['url_avatar'] ) )
				{
					$img_size[0] = $p_width;
					$img_size[1] = $p_height;
				}

				$im = $std->scale_image( array(
												'max_width'  => $p_width,
												'max_height' => $p_height,
												'cur_width'  => $img_size[0],
												'cur_height' => $img_size[1]
									   )      );
			}
			else
			{
				$w = intval($ibforums->input['man_width'])  ? intval($ibforums->input['man_width'])  : $p_width;
				$h = intval($ibforums->input['man_height']) ? intval($ibforums->input['man_height']) : $p_height;
				$im['img_width']  = $w > $p_width  ? $p_width  : $w;
				$im['img_height'] = $h > $p_height ? $p_height : $h;
			}

			//-----------------------------------------
			// Remove any uploaded images..
			//-----------------------------------------

			$this->bash_uploaded_avatars($ibforums->member['id']);

			$real_choice = $ibforums->input['url_avatar'];
			$real_dims   = $im['img_width'].'x'.$im['img_height'];
			$real_type   = 'url';
		}

		//-----------------------------------------
		// Update the DB
		//-----------------------------------------

		$DB->do_update( 'member_extra', array( 'avatar_location' => $real_choice, 'avatar_size' => $real_dims, 'avatar_type' => $real_type ), 'id='.$ibforums->member['id'] );

		$print->redirect_screen( $ibforums->lang['av_c_up'], "act=UserCP&CODE=24" );

	}


	function do_profile()
	{
		global $ibforums, $DB, $std, $print;

		//-----------------------------------------
		// Check for bad entry
		//-----------------------------------------

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['auth_key'] != $this->class->md5_check )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
		}

		//-----------------------------------------
		// Custom profile field stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->member_id   = $ibforums->member['id'];
    	$fields->mem_data_id = $ibforums->member['id'];
    	$fields->cache_data  = $ibforums->cache['profilefields'];
    	$fields->admin       = intval($ibforums->member['g_access_cp']);
    	$fields->supmod      = intval($ibforums->member['g_is_supmod']);

    	$fields->init_data();
    	$fields->parse_to_save();

		//-----------------------------------------
		// Check...
		//-----------------------------------------

		if ( count( $fields->error_fields['empty'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form', 'EXTRA' => $fields->error_fields['empty'][0]['pf_title'] ) );
		}

		if ( count( $fields->error_fields['invalid'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form', 'EXTRA' => $fields->error_fields['invalid'][0]['pf_title'] ) );
		}

		if ( count( $fields->error_fields['toobig'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cf_to_long', 'EXTRA' => $fields->error_fields['toobig'][0]['pf_title'] ) );
		}

		//-----------------------------------------

		if ( (strlen($_POST['Interests']) > $ibforums->vars['max_interest_length']) and ($ibforums->vars['max_interest_length']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'int_too_long' ) );
		}
		//-----------------------------------------
		if ( (strlen($_POST['Location']) > $ibforums->vars['max_location_length']) and ($ibforums->vars['max_location_length']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'loc_too_long' ) );
		}
		//-----------------------------------------
		if (strlen($_POST['WebSite']) > 150)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'web_too_long' ) );
		}
		//-----------------------------------------
		if (strlen($_POST['Photo']) > 150)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'photo_too_long' ) );
		}
		//-----------------------------------------
		if ( ($_POST['ICQNumber']) && (!preg_match( "/^(?:\d+)$/", $_POST['ICQNumber'] ) ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'not_icq_number' ) );
		}


		//-----------------------------------------
		// make sure that either we entered
		// all calendar fields, or we left them
		// all blank
		//-----------------------------------------

		$c_cnt = 0;

		foreach ( array('day','month','year') as $v )
		{
			if (!empty($ibforums->input[$v]))
			{
				$c_cnt++;
			}
		}

		if ( ($c_cnt > 0) and ($c_cnt != 3) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'calendar_not_all' ) );
		}

		if ( ! preg_match( "#^http://#", $ibforums->input['WebSite'] ) )
		{
			$ibforums->input['WebSite'] = 'http://'.$ibforums->input['WebSite'];
		}

		//-----------------------------------------
		// Start off our array
		//-----------------------------------------

		$set = array(
					   'bday_day'    => $ibforums->input['day'],
					   'bday_month'  => $ibforums->input['month'],
					   'bday_year'   => $ibforums->input['year'],
					);

		$bet = array(  'website'     => $ibforums->input['WebSite'],
					   'icq_number'  => $ibforums->input['ICQNumber'],
					   'aim_name'    => $ibforums->input['AOLName'],
					   'yahoo'       => $ibforums->input['YahooName'],
					   'msnname'     => $ibforums->input['MSNName'],
					   'location'    => $ibforums->input['Location'],
					   'interests'   => $ibforums->input['Interests'],
					);

		//-----------------------------------------
		// check to see if we can enter a member title
		// and if one is entered, update it.
		//-----------------------------------------

		if ( (isset($ibforums->input['member_title'])) and ( isset($ibforums->vars['post_titlechange']) ) and ( $ibforums->member['posts'] >= $ibforums->vars['post_titlechange']) )
		{
			$set['title'] = $ibforums->input['member_title'];
		}

		//-----------------------------------------
		// Update the DB
		//-----------------------------------------

		$DB->do_update( 'members'     , $set, 'id='.$ibforums->member['id'] );
		$DB->do_update( 'member_extra', $bet, 'id='.$ibforums->member['id'] );


		//-----------------------------------------
		// Save the profile stuffy wuffy
		//-----------------------------------------

		if ( count( $fields->out_fields ) )
		{
			//-----------------------------------------
			// Do we already have an entry in
			// the content table?
			//-----------------------------------------

			$test = $DB->simple_exec_query( array( 'select' => 'member_id', 'from' => 'pfields_content', 'where' => 'member_id='.$ibforums->member['id'] ) );

			if ( $test['member_id'] )
			{
				//-----------------------------------------
				// We have it, so simply update
				//-----------------------------------------

				$DB->do_update( 'pfields_content', $fields->out_fields, 'member_id='.$ibforums->member['id'] );
			}
			else
			{
				$fields->out_fields['member_id'] = $ibforums->member['id'];

				$DB->do_insert( 'pfields_content', $fields->out_fields );
			}
		}

		//-----------------------------------------
 		// Use sync module?
 		//-----------------------------------------

 		if ( USE_MODULES == 1 )
		{
			$set['id'] = $ibforums->member['id'];
			$this->class->modules->register_class(&$this);
    		$this->class->modules->on_profile_update($set, $custom_fields);
   		}

		// Return us!

		$print->redirect_screen( $ibforums->lang['profile_edited'], "act=UserCP&CODE=01" );

	}

	function do_signature()
	{
		global $ibforums, $DB, $std, $print;

		//-----------------------------------------
		// Check for bad entry
		//-----------------------------------------

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		if ( (strlen($_POST['Post']) > $ibforums->vars['max_sig_length']) and ($ibforums->vars['max_sig_length']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'sig_too_long' ) );
		}

		if ( $_POST['key'] != $std->return_md5_check() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post' ) );
		}

		//-----------------------------------------
		// Check for valid IB CODE
		//-----------------------------------------
		//
		// For efficiency, we convert the IBF code into HTML and store it in the DB
		// Otherwise we'll have to parse the siggies each time we view a post - that
		// gets boring after a while.
		//
		// We will adjust raw HTML on the fly, as some admins may allow it until it's abused
		// then switch it off. If we pre-compile HTML in siggies, we'd have to edit everyones
		// siggies to remove it. We don't want that.
		//
		// I'm going to stick my neck out again and say that most admins will allow IBF Code
		// in siggies, so it's not much of a bother.

		$ibforums->input['Post'] = $this->class->parser->convert(  array( 'TEXT'      => $ibforums->input['Post'],
																		  'SMILIES'   => 0,
																		  'CODE'      => $ibforums->vars['sig_allow_ibc'],
																		  'HTML'      => $ibforums->vars['sig_allow_html'],
																		  'SIGNATURE' => 1
																)       );

		if ($this->class->parser->error != "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => $this->class->parser->error) );
		}

		//-----------------------------------------
		// Write it to the DB.
		//-----------------------------------------

		if ( $mem = $DB->simple_exec_query( array( 'select' => 'id', 'from' => 'member_extra', 'where' => 'id='.$ibforums->member['id'] ) ) )
		{
			$DB->do_update( 'member_extra', array( 'signature' => $ibforums->input['Post'] ), 'id='.$ibforums->member['id'] );
		}
		else
		{
			$DB->do_insert( 'member_extra', array( 'id' => $ibforums->member['id'], 'signature' => $ibforums->input['Post'] ) );
		}


		//-----------------------------------------
		// Member sync?
		//-----------------------------------------

		if ( USE_MODULES == 1 )
 		{
  			$this->class->modules->register_class(&$this);
     		$this->class->modules->on_signature_update($ibforums->member, $ibforums->input['Post']);
    	}

		//-----------------------------------------
		// Buh BYE:
		//-----------------------------------------

		$std->boink_it($ibforums->base_url."act=UserCP&CODE=22");
	}



	function ignore_user_add()
	{
		global $ibforums, $DB, $std;

		$temp_users = array();

		//-----------------------------------------
 		// Stored as userid,userid,userid
 		//-----------------------------------------

 		$ignored_users = explode( ',', $ibforums->member['ignored_users'] );

 		foreach( $ignored_users as $id )
 		{
 			if ( intval($id) )
 			{
 				$temp_users[] = $id;
 			}
 		}

 		$final_string = ",".implode( ',', $temp_users ).",";

 		$final_string = preg_replace( "/,{2,}/", ",", str_replace( " ", "", $final_string ) );

 		$lookup_meh_pants = array();

 		if ( $ibforums->input['newbox_1'] )
 		{
 			$lookup_meh_pants[] = "'".strtolower(str_replace( '|', '&#124;', $ibforums->input['newbox_1']))."'";
 		}
 		if ( $ibforums->input['newbox_2'] )
 		{
 			$lookup_meh_pants[] = "'".strtolower(str_replace( '|', '&#124;', $ibforums->input['newbox_2']))."'";
 		}
 		if ( $ibforums->input['newbox_3'] )
 		{
 			$lookup_meh_pants[] = "'".strtolower(str_replace( '|', '&#124;', $ibforums->input['newbox_3']))."'";
 		}

		if ( count($lookup_meh_pants) )
		{
			//-----------------------------------------
			// See if we have any MEMBRES IN THE DB
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => 'id, name, mgroup', 'from' => 'members', 'where' => "lower(name) IN (".implode(",", $lookup_meh_pants ).")" ) );
			$DB->simple_exec();

			while( $s = $DB->fetch_row() )
			{
				if ( strstr( $ibforums->vars['cannot_ignore_groups'], ','.$s['mgroup'].',' ) )
				{
					continue;
				}

				if ( strstr( $final_string, ','.$s['id'].',') )
				{
					continue;
				}

				if ( $s['id'] != $ibforums->member['id'] )
				{
					$members[ $s['name'] ] = $s['id'];
				}
			}

			if ( count($members ) )
			{
				foreach( $members as $name => $id )
				{
					foreach( array( 1,2,3 ) as $hehe )
					{
						if ( strtolower($name) == strtolower($ibforums->input[ 'newbox_'.$hehe ]) )
						{
							$ibforums->input[ 'newbox_'.$hehe ] = "";
						}
					}

					$final_string .= $id.",";
				}

				$DB->do_update( 'members', array( 'ignored_users' => $final_string ), 'id='.$ibforums->member['id'] );
			}

			$cant_find = array();

			foreach( array( 1,2,3 ) as $hehe )
			{
				if ( $ibforums->input[ 'newbox_'.$hehe ] != "" )
				{
					$cant_find[] = $ibforums->input[ 'newbox_'.$hehe ];
				}
			}

			if ( count($cant_find) )
			{
				$ibforums->member['ignored_users'] = $final_string;

				$this->class->ignore_user_splash( sprintf( $ibforums->lang['mi5_cantfind'], implode( ",", $cant_find ) ) );
				return;
			}
		}

		$ibforums->member['ignored_users'] = $final_string;

		$this->class->ignore_user_splash();
		return;

	}


	function ignore_user_remove()
	{
		global $ibforums, $DB, $std;

		$temp_users = array();

		//-----------------------------------------
 		// Stored as userid,userid,userid
 		//-----------------------------------------

 		$ignored_users = explode( ',', $ibforums->member['ignored_users'] );

 		foreach( $ignored_users as $id )
 		{
 			if ( intval($id) and ( $id != $ibforums->input['id'] ) )
 			{
 				$temp_users[] = $id;
 			}
 		}

 		$final_string = ",".implode( ',', $temp_users ).",";

 		$final_string = preg_replace( "/,{2,}/", ",", str_replace( " ", "", $final_string ) );

 		$DB->do_update( 'members', array( 'ignored_users' => $final_string ), 'id='.$ibforums->member['id'] );

 		$ibforums->member['ignored_users'] = $final_string;

		$this->class->ignore_user_splash();
		return true;

	}
}



?>