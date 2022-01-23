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
|   > Admin Rebuild Counter Functions
|   > Module written by Matt Mecham
|   > Date started: 9th March 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class ad_rebuild {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std, $forums;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		switch($ibforums->input['code'])
		{
			case 'docount':
				$this->docount();
				break;
			case 'doresyncforums':
				$this->resync_forums();
				break;
			case 'doresynctopics':
				$this->resync_topics();
				break;
			case 'doposts':
				$this->rebuild_posts();
				break;
			case 'dopostnames':
				$this->rebuild_post_names();
				break;
			case 'dopostcounts':
				$this->rebuild_post_counts();
				break;
			case 'dothumbnails':
				$this->rebuild_thumbnails();
				break;
			case 'doattachdata':
				$this->rebuild_attachdata();
				break;
			case 'cleanattachments':
				$this->clean_attachments();
				break;
			case 'cleanavatars':
				$this->clean_avatars();
				break;
			case 'cleanphotos':
				$this->clean_photos();
				break;
			//-----------------------------------------
			// Tools
			//-----------------------------------------

			case 'tool_settings':
				$this->tools_dupe_settings();
				break;

			case 'tool_converge':
				$this->tools_converge();
				break;

			case 'tool_bansettings':
				$this->tool_bansettings();
				break;

			case 'tools':
				$this->tools_splash();
				break;

			default:
				$this->rebuild_start();
				break;
		}

	}

	/*-------------------------------------------------------------------------*/
	// TOOLS BAN SETTINGS
	/*-------------------------------------------------------------------------*/

	function tool_bansettings()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Init
		//-----------------------------------------

		$bomb        = array();
		$ban         = array();
		$ip_count    =  0;
		$email_count = 0;
		$name_count  = 0;

		//-----------------------------------------
		// Get current entries
		//-----------------------------------------



		$DB->simple_construct( array( 'select' => '*', 'from' => 'banfilters', 'order' => 'ban_date desc' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ban[ $r['ban_type'] ][ $r['ban_content'] ] = $r;
		}

		//-----------------------------------------
		// Get $INFO (again) ip email name
		//-----------------------------------------

		require( ROOT_PATH."conf_global.php" );

		//-----------------------------------------
		// IP
		//-----------------------------------------

		if ( $INFO['ban_ip'] )
		{
			$bomb = explode( '|', $INFO['ban_ip'] );

			if ( is_array( $bomb ) and count( $bomb ) )
			{
				foreach( $bomb as $bang )
				{
					if ( ! is_array($ban['ip'][ $bang ]) )
					{
						$DB->do_insert( 'banfilters', array( 'ban_type' => 'ip', 'ban_content' => $bang, 'ban_date' => time() ) );

						$ip_count++;
					}
				}
			}
		}

		//-----------------------------------------
		// EMAIL
		//-----------------------------------------

		if ( $INFO['ban_email'] )
		{
			$bomb = explode( '|', $INFO['ban_email'] );

			if ( is_array( $bomb ) and count( $bomb ) )
			{
				foreach( $bomb as $bang )
				{
					if ( ! is_array($ban['email'][ $bang ]) )
					{
						$DB->do_insert( 'banfilters', array( 'ban_type' => 'email', 'ban_content' => $bang, 'ban_date' => time() ) );

						$email_count++;
					}
				}
			}
		}

		//-----------------------------------------
		// EMAIL
		//-----------------------------------------

		if ( $INFO['ban_names'] )
		{
			$bomb = explode( '|', $INFO['ban_names'] );

			if ( is_array( $bomb ) and count( $bomb ) )
			{
				foreach( $bomb as $bang )
				{
					if ( ! is_array($ban['name'][ $bang ]) )
					{
						$DB->do_insert( 'banfilters', array( 'ban_type' => 'name', 'ban_content' => $bang, 'ban_date' => time() ) );

						$name_count++;
					}
				}
			}
		}

		$ibforums->main_msg = "$ip_count IP addresses imported, $email_count email address imported, $name_count names imported.";

		require_once( ROOT_PATH."sources/admin/ad_administration.php");
		$thing = new ad_administration();
		$thing->ban_rebuildcache();

		$this->tools_splash();
	}

	/*-------------------------------------------------------------------------*/
	// TOOLS (UN)CONVERGE
	/*-------------------------------------------------------------------------*/

	function tools_converge()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get all validating members...
		//-----------------------------------------

		$to_unconverge    = array();
		$unconverge_count = 0;

		$DB->simple_construct( array( 'select' => 'id, email, mgroup', 'from' => 'members', 'where' => 'mgroup=1' ) );
		$DB->simple_exec();

		while( $m = $DB->fetch_row() )
		{
			if ( preg_match( "#^{$m['id']}\-#", $m['email'] ) )
			{
				$to_unconverge[] = $m['id'];
			}
		}

		$unconverge_count = intval( count($to_unconverge) );

		if ( $unconverge_count )
		{
			foreach( $to_unconverge as $mid )
			{
				$DB->do_update( 'members'     , array( 'mgroup' => $ibforums->vars['member_group'] ), 'id='.$mid );
				$DB->do_update( 'member_extra', array( 'bio'   => 'dupemail'                       ), 'id='.$mid );
			}
		}

		//-----------------------------------------
		// Time to move on dude
		//-----------------------------------------

		$ibforums->main_msg = "$unconverge_count members found and restored";
		$this->tools_splash();

	}

	/*-------------------------------------------------------------------------*/
	// TOOLS DUPLICATE SETTINGS
	/*-------------------------------------------------------------------------*/

	function tools_dupe_settings()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Remove dupe categories
		//-----------------------------------------

		$title_id_to_keep    = array();
		$title_id_to_delete  = array();
		$title_deleted_count = 0;

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings_titles', 'order' => 'conf_title_id' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			if ( $title_id_to_keep[ $r['conf_title_title'] ] )
			{
				$title_id_to_delete[ $r['conf_title_id'] ] = $r['conf_title_id'];
			}
			else
			{
				$title_id_to_keep[ $r['conf_title_title'] ] = $r['conf_title_id'];
			}
		}

		if ( count( $title_id_to_delete ) )
		{
			$DB->simple_exec_query( array( 'delete' => 'conf_settings_titles', 'where' => 'conf_title_id IN ('.implode( ',', $title_id_to_delete ).')' ) );
		}

		$title_deleted_count = intval( count($title_id_to_delete) );

		//-----------------------------------------
		// Remove dupe settings
		//-----------------------------------------

		$setting_id_to_keep       = array();
		$setting_id_to_delete     = array();
		$setting_id_deleted_count = 0;

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'order' => 'conf_id' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			if ( $setting_id_to_keep[ $r['conf_title'].','.$r['conf_key'] ] )
			{
				$setting_id_to_delete[ $r['conf_id'] ] = $r['conf_id'];
			}
			else
			{
				$setting_id_to_keep[ $r['conf_title'].','.$r['conf_key'] ] = $r['conf_id'];
			}
		}

		if ( count( $setting_id_to_delete ) )
		{
			$DB->simple_exec_query( array( 'delete' => 'conf_settings', 'where' => 'conf_id IN ('.implode( ',', $setting_id_to_delete ).')' ) );
		}

		$setting_deleted_count = intval( count($setting_id_to_delete) );

		//-----------------------------------------
		// Time to move on dude
		//-----------------------------------------

		$ibforums->main_msg = "$title_deleted_count duplicate setting titles deleted and $setting_deleted_count duplicate settings deleted";
		$this->tools_splash();
	}

	/*-------------------------------------------------------------------------*/
	// TOOLS SPLASH
	/*-------------------------------------------------------------------------*/

	function tools_splash()
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_detail = "Please choose which clean-up tool you wish to use.";
		$ibforums->admin->page_title  = "Maintenance Tools";

		//-----------------------------------------
		// STATISTICS
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'tool_settings' ),
												                 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "{none}"    , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Remove Duplicate System Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "After an upgrade from a previous version or import from another board software, you may find that due to running an upgrade tool
																  twice or a time-out you'll end up with some duplicate tools in the System Settings.
																  <br />This tool finds the duplicates which have a greater ID than the original."
														)      );

		$ibforums->html .= $ibforums->adskin->end_form('RUN TOOL');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// STATISTICS (also vali mem)
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'tool_converge' ),
												                 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "{none}"    , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Find & Restore 'Converged' Members" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "After an upgrade from a previous version or import from another board software, you may find that because several of your members have used duplicate
																  email addresses, their account has been moved into the validating group.
																  <br />This tool finds these members and restores them into the default member group and asks them to change their email address."
														)      );

		$ibforums->html .= $ibforums->adskin->end_form('RUN TOOL');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Import old bandana settings
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'tool_bansettings' ),
												                 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "{none}"    , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Find & Restore old IPB Ban Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "After an upgrade from a previous version, you may find that your ban settings are no longer
																  stored.<br />Running this tool attempts to import your old ban settings. Old entries will not overwrite new entries."
														)      );

		$ibforums->html .= $ibforums->adskin->end_form('RUN TOOL');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Print
		//-----------------------------------------

		$ibforums->admin->output();

	}

	/*-------------------------------------------------------------------------*/
	// Clean out photos
	/*-------------------------------------------------------------------------*/

	function clean_photos()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( KERNEL_PATH.'class_upload.php' );

		$upload = new class_upload();

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Pop open the directory and
		// peek inside...
		//-----------------------------------------

		$i = 0;

		$dh = opendir( ROOT_PATH.'uploads' );

 		while ( $file = readdir( $dh ) )
 		{
 			if ( strstr( $file, 'photo-' ) )
 			{
 				$fullfile = ROOT_PATH.'uploads/'.$file;

 				$i++;

 				//-----------------------------------------
 				// Already started?
 				//-----------------------------------------

 				if ( $start > $i )
 				{
 					continue;
 				}

 				//-----------------------------------------
 				// Done for this iteration?
 				//-----------------------------------------

 				if ( $i > $end )
 				{
 					break;
 				}

 				//-----------------------------------------
 				// Try and get attach row
 				//-----------------------------------------

 				$found = $DB->simple_exec_query( array( 'select' => 'id', 'from' => 'member_extra', 'where' => "photo_location='$file'" ) );

 				if ( ! $found['id'] )
 				{
 					@unlink( $fullfile );
 					$output[] = "<span style='color:red'>Removed orphan: $file</span>";
 				}
 				else
 				{
 					$output[] = "<span style='color:gray'>Attached File OK: $file</span>";
 				}
			}
 		}

 		closedir( $dh );

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( $i < $end)
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// Clean out avatars
	/*-------------------------------------------------------------------------*/

	function clean_avatars()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( KERNEL_PATH.'class_upload.php' );

		$upload = new class_upload();

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Pop open the directory and
		// peek inside...
		//-----------------------------------------

		$i = 0;

		$dh = opendir( ROOT_PATH.'uploads' );

 		while ( $file = readdir( $dh ) )
 		{
 			if ( strstr( $file, 'av-' ) )
 			{
 				$fullfile = ROOT_PATH.'uploads/'.$file;

 				$i++;

 				//-----------------------------------------
 				// Already started?
 				//-----------------------------------------

 				if ( $start > $i )
 				{
 					continue;
 				}

 				//-----------------------------------------
 				// Done for this iteration?
 				//-----------------------------------------

 				if ( $i > $end )
 				{
 					break;
 				}

 				//-----------------------------------------
 				// Try and get attach row
 				//-----------------------------------------

 				$found = $DB->simple_exec_query( array( 'select' => 'id', 'from' => 'member_extra', 'where' => "avatar_location='$file' or avatar_location='upload:$file'" ) );

 				if ( ! $found['id'] )
 				{
 					@unlink( $fullfile );
 					$output[] = "<span style='color:red'>Removed orphan: $file</span>";
 				}
 				else
 				{
 					$output[] = "<span style='color:gray'>Attached File OK: $file</span>";
 				}
			}
 		}

 		closedir( $dh );

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( $i < $end)
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// Clean out attachments
	/*-------------------------------------------------------------------------*/

	function clean_attachments()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( KERNEL_PATH.'class_upload.php' );

		$upload = new class_upload();

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Pop open the directory and
		// peek inside...
		//-----------------------------------------

		$i = 0;

		$dh = opendir( ROOT_PATH.'uploads' );

 		while ( $file = readdir( $dh ) )
 		{
 			if ( strstr( $file, 'post-' ) )
 			{
 				$fullfile = ROOT_PATH.'uploads/'.$file;

 				$i++;

 				//-----------------------------------------
 				// Already started?
 				//-----------------------------------------

 				if ( $start > $i )
 				{
 					continue;
 				}

 				//-----------------------------------------
 				// Done for this iteration?
 				//-----------------------------------------

 				if ( $i > $end )
 				{
 					break;
 				}

 				//-----------------------------------------
 				// Try and get attach row
 				//-----------------------------------------

 				$found = $DB->simple_exec_query( array( 'select' => 'attach_id', 'from' => 'attachments', 'where' => "attach_location='$file' OR attach_thumb_location='$file'" ) );

 				if ( ! $found['attach_id'] )
 				{
 					@unlink( $fullfile );
 					$output[] = "<span style='color:red'>Removed orphan: $file</span>";
 				}
 				else
 				{
 					$output[] = "<span style='color:gray'>Attached File OK: $file</span>";
 				}
			}
 		}

 		closedir( $dh );

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( $i < $end)
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD ATTACH DATA
	/*-------------------------------------------------------------------------*/

	function rebuild_attachdata()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( KERNEL_PATH.'class_upload.php' );

		$upload = new class_upload();

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $DB->simple_exec_query( array( 'select' => 'attach_id', 'from' => 'attachments', 'where' => "attach_id > $end" ) );
		$max = intval( $tmp['attach_id'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_id >= $start and attach_id < $end", 'order' => 'attach_id ASC' ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $DB->fetch_row( $outer ) )
		{
			//-----------------------------------------
			// Get ext
			//-----------------------------------------

			$update = array();

			$update['attach_ext'] = $upload->_get_file_extension( $r['attach_file'] );

			if ( $r['attach_location'] )
			{
				if ( file_exists( $ibforums->vars['upload_dir'].'/'.$r['attach_location'] ) )
				{
					$update['attach_filesize'] = @filesize( $ibforums->vars['upload_dir'].'/'.$r['attach_location'] );
				}
			}

			if ( count( $update ) )
			{
				$DB->do_update( 'attachments', $update, 'attach_id='.$r['attach_id'] );
			}

			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD THUMBNAILS
	/*-------------------------------------------------------------------------*/

	function rebuild_thumbnails()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( KERNEL_PATH.'class_image.php' );

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $DB->simple_exec_query( array( 'select' => 'attach_id', 'from' => 'attachments', 'where' => "attach_id > $end" ) );
		$max = intval( $tmp['attach_id'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_id >= $start and attach_id < $end", 'order' => 'attach_id ASC' ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $DB->fetch_row( $outer ) )
		{
			if ( $r['attach_is_image'] )
			{
				if ( $r['attach_thumb_location'] and ( $r['attach_thumb_location'] != $r['attach_location'] ) )
				{
					if ( file_exists( $ibforums->vars['upload_dir'].'/'.$r['attach_thumb_location'] ) )
					{
						if ( ! @unlink( $ibforums->vars['upload_dir'].'/'.$r['attach_thumb_location'] ) )
						{
							$output[] = "Could not remove: ".$r['attach_thumb_location'];
							continue;
						}
					}
				}

				$attach_data           = array();
				$thumb_data            = array();

				$image = new class_image();

				$image->in_type        = 'file';
				$image->out_type       = 'file';
				$image->in_file_dir    = $ibforums->vars['upload_dir'];
				$image->in_file_name   = $r['attach_location'];
				$image->desired_width  = $ibforums->vars['siu_width'];
				$image->desired_height = $ibforums->vars['siu_height'];
				$image->gd_version     = $ibforums->vars['gd_version'];

				$thumb_data = $image->generate_thumbnail();

				$attach_data['attach_thumb_width']    = $thumb_data['thumb_width'];
				$attach_data['attach_thumb_height']   = $thumb_data['thumb_height'];
				$attach_data['attach_thumb_location'] = $thumb_data['thumb_location'];

				if ( count( $attach_data ) )
				{
					$DB->do_update( 'attachments', $attach_data, 'attach_id='.$r['attach_id'] );

					$output[] = "Resized: ".$r['attach_location'];
				}

				unset($image);
			}

			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD POST COUNTS
	/*-------------------------------------------------------------------------*/

	function rebuild_post_counts()
	{
		global $ibforums, $DB, $std, $forums;

		//-----------------------------------------
		// Forums not to count?
		//-----------------------------------------

		$forums = array();

		foreach( $ibforums->cache['forum_cache'] as $id => $data )
		{
			if ( ! $data['inc_postcount'] )
			{
				$forums[] = $data['id'];
			}
		}

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $DB->simple_exec_query( array( 'select' => 'id', 'from' => 'members', 'where' => "id > $end" ) );
		$max = intval( $tmp['id'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "id >= $start and id < $end", 'order' => 'id ASC' ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $DB->fetch_row( $outer ) )
		{
			if ( ! count( $forums ) )
			{
				$count = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'posts', 'where' => 'queued != 1 AND author_id='.$r['id'] ) );
			}
			else
			{
				$DB->query( "SELECT count(p.pid) as count
							 FROM ".SQL_PREFIX."posts p, ".SQL_PREFIX."topics t
							 WHERE p.queued != 1 AND p.author_id={$r['id']}
							 AND t.tid=p.topic_id AND t.forum_id NOT IN (".implode(",",$forums).")" );

				$count = $DB->fetch_row();
			}

			$new_post_count = intval( $count['count'] );

			$DB->do_update( 'members', array( 'posts' => $new_post_count ), 'id='.$r['id'] );

			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD POSTS
	/*-------------------------------------------------------------------------*/

	function rebuild_post_names()
	{
		global $ibforums, $DB, $std, $forums;

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $DB->simple_exec_query( array( 'select' => 'id', 'from' => 'members', 'where' => "id > $end" ) );
		$max = intval( $tmp['id'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "id >= $start and id < $end", 'order' => 'id ASC' ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $DB->fetch_row( $outer ) )
		{
			$DB->do_update( 'contacts'      , array( 'contact_name'     => $r['name'] ), "contact_id="    .$r['id'] );
			$DB->do_update( 'moderator_logs', array( 'member_name'      => $r['name'] ), "member_id="     .$r['id'] );
			$DB->do_update( 'moderators'    , array( 'member_name'      => $r['name'] ), "member_id="     .$r['id'] );
			$DB->do_update( 'posts'         , array( 'author_name'      => $r['name'] ), "author_id="     .$r['id'] );
			$DB->do_update( 'topics'        , array( 'starter_name'     => $r['name'] ), "starter_id="    .$r['id'] );

			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD POSTS
	/*-------------------------------------------------------------------------*/

	function rebuild_posts()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( ROOT_PATH.'sources/lib/post_parser.php' );
		$parser = new post_parser();

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $DB->simple_exec_query( array( 'select' => 'pid', 'from' => 'posts', 'where' => "pid > $end" ) );
		$max = intval( $tmp['pid'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'p.*, t.forum_id', 'from' => 'posts p, ibf_topics t', 'where' => "pid >= $start and pid < $end and p.topic_id=t.tid", 'order' => 'pid ASC' ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $DB->fetch_row( $outer ) )
		{
			$parser->quote_open   = 0;
			$parser->quote_closed = 0;
			$parser->quote_error  = 0;
			$parser->error        = '';
			$parser->image_count  = 0;

			$rawpost = $parser->unconvert( $r['post'] );

			$newpost = $parser->convert( array( 'TEXT'      => $rawpost,
												'SMILIES'   => $r['use_emo'],
												'CODE'      => $ibforums->cache['forum_cache'][ $r['forum_id'] ]['use_ibc'],
									  )       );

			//-----------------------------------------
			// Remove old \' escaping
			//-----------------------------------------

			$newpost = str_replace( "\\'", "'", $newpost );

			//-----------------------------------------
			// Convert old dohtml?
			//-----------------------------------------

			$htmlstate = 0;

			if ( strstr( strtolower($newpost), '[dohtml]' ) )
			{
				//-----------------------------------------
				// Can we use HTML?
				//-----------------------------------------

				if ( $ibforums->cache['forum_cache'][ $r['forum_id'] ]['use_html'] )
				{
					$htmlstate = 2;
				}

				$newpost = preg_replace( "#\[dohtml\]#i" , "", $newpost );
				$newpost = preg_replace( "#\[/dohtml\]#i", "", $newpost );
			}
			else
			{
				$htmlstate = intval( $r['post_htmlstate'] );
			}

			if ( $newpost )
			{
				$DB->do_update( 'posts', array( 'post' => $newpost, 'post_htmlstate' => $htmlstate ), 'pid='.$r['pid'] );
			}

			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>Up to $end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// RESYNCHRONIZE TOPICS
	/*-------------------------------------------------------------------------*/

	function resync_topics()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( ROOT_PATH.'sources/lib/modfunctions.php' );
		$modfunc = new modfunctions();

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'topics', 'where' => "tid > $end" ) );
		$max = intval( $tmp['count'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => "tid >= $start and tid < $end", 'order' => 'tid ASC' ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $DB->fetch_row( $outer ) )
		{
			$modfunc->rebuild_topic($r['tid'], 0);

			if ( $ibforums->input['pergo'] <= 200 )
			{
				$output[] = "Processed topic ".$r['title'];
			}

			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>$end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// RESYNCHRONIZE FORUMS
	/*-------------------------------------------------------------------------*/

	function resync_forums()
	{
		global $ibforums, $DB, $std, $forums;

		require_once( ROOT_PATH.'sources/lib/modfunctions.php' );
		$modfunc = new modfunctions();

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval( $ibforums->input['st'] );
		$end    = intval( $ibforums->input['pergo'] ) ? intval( $ibforums->input['pergo'] ) : 100;
		$end   += $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'forums', 'where' => "id > $end" ) );
		$max = intval( $tmp['count'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forums', 'where' => "id >= $start and id < $end", 'order' => 'id ASC' ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $DB->fetch_row( $outer ) )
		{
			$modfunc->forum_recount( $r['id'] );
			$output[] = "Processed forum ".$r['name'];
			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$text = "<b>Rebuild completed</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild";
			$time = 2;
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------

			$text = "<b>$end processed so far, continuing...</b><br />".implode( "<br />", $output );
			$url  = "act=rebuild&code=".$ibforums->input['code'].'&pergo='.$ibforums->input['pergo'].'&st='.$end;
			$time = 0;
		}

		//-----------------------------------------
		// Bye....
		//-----------------------------------------

		$ibforums->admin->redirect( $url, $text, 0, $time );
	}

	/*-------------------------------------------------------------------------*/
	// DO COUNT - Count the stats
	/*-------------------------------------------------------------------------*/

	function docount()
	{
		global $ibforums, $DB,  $std;

		if ( (! $ibforums->input['posts']) and (! $ibforums->input['members'] ) and (! $ibforums->input['lastreg'] ) )
		{
			$ibforums->admin->error("Nothing to recount!");
		}

		$stats = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='stats'" ) );

		$stats = unserialize(stripslashes($stats['cs_value']));

		if ($ibforums->input['posts'])
		{
			$DB->simple_construct( array( 'select' => 'count(pid) as posts', 'from' => 'posts', 'where' => "queued <> 1" ) );
			$DB->simple_exec();

			$r = $DB->fetch_row();
			$stats['total_replies'] = intval($r['posts']);

			$DB->simple_construct( array( 'select' => 'count(tid) as topics', 'from' => 'topics', 'where' => "approved = 1" ) );
			$DB->simple_exec();

			$r = $DB->fetch_row();
			$stats['total_topics']   = intval($r['topics']);
			$stats['total_replies'] -= $stats['total_topics'];
		}

		if ($ibforums->input['members'])
		{
			$DB->simple_construct( array( 'select' => 'count(id) as members', 'from' => 'members', 'where' => "mgroup <> '".$ibforums->vars['auth_group']."'" ) );
			$DB->simple_exec();

			$r = $DB->fetch_row();
			$stats['mem_count'] = intval($r['members']);
		}

		if ($ibforums->input['lastreg'])
		{
			$DB->simple_construct( array( 'select' => 'id, name',
										  'from'   => 'members',
										  'where'  => "mgroup <> '".$ibforums->vars['auth_group']."'",
										  'order'  => "id DESC",
										  'limit'  => array(0,1) ) );
			$DB->simple_exec();

			$r = $DB->fetch_row();
			$stats['last_mem_name'] = $r['name'];
			$stats['last_mem_id']   = $r['id'];
		}

		if ($ibforums->input['online'])
		{
			$stats['most_date'] = time();
			$stats['most_count'] = 1;
		}

		if ( count($stats) > 0 )
		{
			$DB->simple_exec_query( array( 'delete' => 'cache_store', 'where' => "cs_key='stats'" ) );
			$DB->do_insert( 'cache_store', array( 'cs_array' => 1, 'cs_key' => 'stats', 'cs_value' => addslashes(serialize($stats)) ) );
		}
		else
		{
			$ibforums->admin->error("Nothing to recount!");
		}

		$ibforums->main_msg = 'Statistics Recounted';

		$ibforums->admin->done_screen("Statistics Recounted", "Recount statistics section", "act=rebuild", 'redirect' );

	}

	/*-------------------------------------------------------------------------*/
	// MAIN PAGE
	/*-------------------------------------------------------------------------*/

	function rebuild_start()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "Please choose which statistics to recount.";
		$ibforums->admin->page_title  = "Recount & Rebuild Manager";

		//-----------------------------------------
		// STATISTICS
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'docount' ),
												                 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "Statistic"    , "70%" );
		$ibforums->adskin->td_header[] = array( "Option"       , "30%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Recount Statistics" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Recount total topics and posts",
																 $ibforums->adskin->form_dropdown( 'posts', array( 0 => array( 1, 'Yes'  ), 1 => array( 0, 'No' ) ) )
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Recount Members",
												  $ibforums->adskin->form_dropdown( 'members', array( 0 => array( 1, 'Yes'  ), 1 => array( 0, 'No' ) ) )
										 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Reset last registered member",
												  $ibforums->adskin->form_dropdown( 'lastreg', array( 0 => array( 1, 'Yes'  ), 1 => array( 0, 'No' ) ) )
										 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Reset 'Most online' statistic?",
												  $ibforums->adskin->form_dropdown( 'online', array( 0 => array( 0, 'No'  ), 1 => array( 1, 'Yes' ) ) )
										 )      );

		$ibforums->html .= $ibforums->adskin->end_form('Reset these statistics');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Resynchronise Forums
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doresyncforums' ),
																 2 => array( 'act'   , 'rebuild' ),
														)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Resynchronize Forums" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Resynchronize Forums</b><div style='color:gray'>This will recount topics, posts and the forum last poster for all your forums</div>",
												  		       $ibforums->adskin->form_simple_input( 'pergo', '50', 5 ). "&nbsp;Per Cycle"
										 			  )      );

		$ibforums->html .= $ibforums->adskin->end_form('Resynchronize Forums');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Resynchronise Forums
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doresynctopics' ),
																 2 => array( 'act'   , 'rebuild' ),
														)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Resynchronize Topics" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Resynchronize Topics</b><div style='color:gray'>This will recount replies, attachment count and the topic starter and last poster for all your topics.</div>",
												  		       $ibforums->adskin->form_simple_input( 'pergo', '500', 5 ). "&nbsp;Per Cycle"
										 			  )      );

		$ibforums->html .= $ibforums->adskin->end_form('Resynchronize Topics');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Resynchronise Posts
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doposts' ),
												                 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rebuild Post Content" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rebuild Post Content</b><div style='color:gray'>This will rebuild the post content including BBCode and emoticons. Useful if you've changed a lot of emoticons or the emoticon paths.</div>",
												  		       $ibforums->adskin->form_simple_input( 'pergo', '500', 5 ). "&nbsp;Per Cycle"
										 			  )      );

		$ibforums->html .= $ibforums->adskin->end_form('Rebuild Post Content');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Resynchronise User Names
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dopostnames' ),
												             	 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rebuild User Names" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rebuild User Names</b><div style='color:gray'>This will reset the saved usernames in posts, topics, logs, etc. Useful if you've recently converted or manually changed member's names.</div>",
												  		         $ibforums->adskin->form_simple_input( 'pergo', '500', 5 ). "&nbsp;Per Cycle"
										 		   	    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Rebuild User Names');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Resynchronise User Post Counts
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dopostcounts' ),
												             	 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rebuild User Post Counts" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rebuild User Post Counts</b><div style='color:gray'>This will recount members posts based on CURRENT posts from the database. This will almost certainly REDUCE the post counts for your members as deleted and pruned posts will no longer be counted. This should not be used if you wish to retain your member's current post counts.</div>THERE IS NO UNDO!",
												  		         $ibforums->adskin->form_simple_input( 'pergo', '500', 5 ). "&nbsp;Per Cycle"
										 		   	    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Rebuild User Post Counts');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Rebuild thumbnails
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dothumbnails' ),
												             	 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rebuild Attachment Thumbnails" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rebuild Attachment Thumbnails</b><div style='color:gray'>This will rebuild all your attachment image thumbnails to the current size. This is useful if you've recently changed the thumbnail size and wish to update all current attachments</div>This is moderately resource intensive.",
												  		         $ibforums->adskin->form_simple_input( 'pergo', '20', 5 ). "&nbsp;Per Cycle"
										 		   	    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Rebuild Attachment Thumbnails');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Rebuild attachment data
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doattachdata' ),
												             	 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rebuild Attachment Data" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rebuild Attachment Data</b><div style='color:gray'>This will rebuild all your attachment data such as filesize, location and file extension</div>This is moderately resource intensive.",
												  		         $ibforums->adskin->form_simple_input( 'pergo', '50', 5 ). "&nbsp;Per Cycle"
										 		   	    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Rebuild Attachment Data');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Clean up attachments
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'cleanattachments' ),
												             	 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Remove orphaned attachments" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Remove orphaned attachments</b><div style='color:gray'>This will check and remove all orphaned 'post-' attachments not assigned to a post.</div>This is moderately resource intensive.",
												  		         $ibforums->adskin->form_simple_input( 'pergo', '50', 5 ). "&nbsp;Per Cycle"
										 		   	    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Remove orphaned attachments');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Clean up uploaded avatars
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'cleanavatars' ),
												             	 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Remove orphaned uploaded avatars" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Remove orphaned avatars</b><div style='color:gray'>This will check and remove all orphaned 'av-' avatars not assigned to a member.</div>This is moderately resource intensive.",
												  		         $ibforums->adskin->form_simple_input( 'pergo', '50', 5 ). "&nbsp;Per Cycle"
										 		   	    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Remove orphaned avatars');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Clean up uploaded photos
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'cleanphotos' ),
												             	 2 => array( 'act'   , 'rebuild' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Remove orphaned uploaded photos" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Remove orphaned photos</b><div style='color:gray'>This will check and remove all orphaned 'photo-' photographs not assigned to a member.</div>This is moderately resource intensive.",
												  		         $ibforums->adskin->form_simple_input( 'pergo', '50', 5 ). "&nbsp;Per Cycle"
										 		   	    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Remove orphaned photos');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-------------------------------//

		$ibforums->admin->output();

	}




}


?>