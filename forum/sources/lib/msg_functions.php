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
|   > Msg Func module
|   > Module written by Matt Mecham
|   > Date started: 14th February 2002
|
|   > Module Version 1.0.0
|   > DBA Checked: Fri 21 May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class msg_functions
{

	var $postlib      = "";
	var $class        = "";
	var $output       = "";
	var $can_upload   = 0;
	var $form_extra   = "";
	var $hidden_field = "";
	var $redirect_url = "";
	var $redirect_lang= "";

	var $force_pm     = 0;

	function register_class( $class )
	{
		$this->class = &$class;
	}

	/*-------------------------------------------------------------------------*/
	// Initiate
	/*-------------------------------------------------------------------------*/

	function init()
	{
		global $DB, $ibforums, $forums, $std, $print, $skin_universal;

		//-----------------------------------------
		// Get post stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/post.php' );
		$this->postlib = new post();
		$this->postlib->init();

		if ( $ibforums->member['g_attach_max'] != -1 and $ibforums->member['g_can_msg_attach'] )
		{
			$this->can_upload   = 1;
			$this->form_extra   = " enctype='multipart/form-data'";
			$this->hidden_field = "<input type='hidden' name='MAX_FILE_SIZE' value='".($ibforums->member['g_attach_max']*1024)."' />";
		}

		$this->postlib->can_upload = $this->can_upload;
	}

	/*-------------------------------------------------------------------------*/
	// Send form stuff
	/*-------------------------------------------------------------------------*/

	function send_form($preview=0, $errors="")
 	{
		global $ibforums, $DB, $std, $print;

 		$this->form_mid     = intval($ibforums->input['MID']);
 		$this->form_orig_id = intval($ibforums->input['MSID']);

 		//-----------------------------------------
 		// Fix up errors
 		//-----------------------------------------

 		$errors = preg_replace( "/^<br>/", "", $errors );

    	//-----------------------------------------
    	// Preview post?
    	//-----------------------------------------

    	if ( $preview )
    	{
    		$old_msg = $this->postlib->parser->convert( array( 'TEXT'    => $std->remove_tags($ibforums->input['Post']),
															   'SMILIES' => 1,
															   'CODE'    => $ibforums->vars['msg_allow_code'],
															   'HTML'    => $ibforums->vars['msg_allow_html']
													  )     );

			$this->postlib->parser->pp_do_html  = 0;
			$this->postlib->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
			$this->postlib->parser->pp_nl2br    = 1;

			$old_msg = $this->postlib->parser->post_db_parse( $old_msg );

			$this->output .= $this->class->html->preview($old_msg);

    	}

    	if ($errors != "")
    	{
    		$this->output .= $this->class->html->pm_errors($errors);
    		$preview = 1;
    	}

    	//-----------------------------------------
 		// Load the contacts
 		//-----------------------------------------

 		$contacts = $this->build_contact_list();

 		$name_to_enter = "";
 		$old_message   = "";
 		$old_title     = "";

    	//-----------------------------------------
 		// Did we come from a button with a user ID?
 		//-----------------------------------------

		if ( $this->form_mid )
		{
			$DB->simple_construct( array( 'select' => 'name, id', 'from' => 'members', 'where' => "id=".$this->form_mid ) );
			$DB->simple_exec();

			$name = $DB->fetch_row();

			if ($ibforums->input['fwd'] != 1)
			{
				if ($name['id'])
				{
					$name_to_enter = $name['name'];
				}
			}
		}
		else
		{
			$name_to_enter = $ibforums->input['entered_name'];
		}

 		//-----------------------------------------
 		// Are we quoting an old message?
 		//-----------------------------------------

 		if ( $preview or $this->class->show_form )
 		{
 			$old_message = $std->txt_htmlspecialchars($std->txt_stripslashes($_POST['Post']));
 			$old_title   = preg_replace( "/'/", "&#39;", $std->txt_stripslashes($_POST['msg_title']) );

 		}
 		else if ( $this->form_orig_id )
 		{
 			$DB->cache_add_query( 'msg_get_saved_msg', array( 'msgid' => $this->form_orig_id, 'mid' => $ibforums->member['id'] ) );
 			$DB->simple_exec();

 			$old_msg = $DB->fetch_row();

 			if ($old_msg['mt_title'])
 			{
 				if ( $this->class->edit_saved )
				{
					$name_to_enter         = $old_msg['name'];
					$cc_text               = $old_msg['msg_cc_users'];
					$old_title             = $old_msg['mt_title'];
					$old_message           = $std->my_br2nl( $old_msg['msg_post'] );
					$this->class->post_key = $old_msg['msg_post_key'];
					$ibforums->input['OID']= $old_msg['mt_id'];
				}
 				else if ($ibforums->input['fwd'] == 1)
 				{
 					$old_title     = "Fwd:".$old_msg['mt_title'];
 					$old_title     = preg_replace( "/^(?:Fwd\:){1,}/i", "Fwd:", $old_title );
 					$old_message   = '[QUOTE]'.sprintf($ibforums->lang['vm_forward_text'], $name['name'])."\n\n".$old_msg['msg_post'].'[/QUOTE]'."\n";
 					$old_message   = $std->my_br2nl( $old_message );
 				}
 				else
 				{
 					$old_title   = "Re:".$old_msg['mt_title'];
 					$old_title   = preg_replace( "/^(?:Re\:){1,}/i", "Re:", $old_title );
 					$old_message = '[QUOTE]'.$old_msg['msg_post'].'[/QUOTE]'."\n";
 					$old_message = $std->my_br2nl( $old_message );
 				}
 			}
 		}

 		//-----------------------------------------
 		// Build up the HTML for the send form
 		//-----------------------------------------

 		$this->output .= $this->postlib->html->get_javascript();

 		$this->output .= $this->class->html->Send_form( array (
																'CONTACTS'  => $contacts,
																'MEMBER'    => $this->member,
																'N_ENTER'   => $name_to_enter,
																'O_TITLE'   => $old_title,
																'OID'       => $ibforums->input['OID'], // Old unsent msg id for restoring saved msg - used to delete saved when sent
																'post_key'  => $this->class->post_key,
																'form_extra'=> $this->form_extra,
																'upload'    => $this->hidden_field,

													  )       );

 		$ibforums->lang['the_max_length'] = $ibforums->vars['max_post_length'] * 1024;

 		$this->output .= $this->postlib->html->pm_postbox_buttons($old_message);

 		if ($this->can_upload)
		{
			$this->output .= $this->postlib->html_build_uploads($this->class->post_key,'msg');
		}

 		$this->output .= $this->class->html->send_form_footer();

 		//-----------------------------------------
 		// Add in the smilies box
 		//-----------------------------------------

 		$this->output = $this->postlib->html_add_smilie_box($this->output);

		$this->class->page_title = $ibforums->lang['t_welcome'];
 		$this->class->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

		//-----------------------------------------
 		// Do we have permission to mass PM peeps?
 		//-----------------------------------------

 		if ($ibforums->member['g_max_mass_pm'] > 0)
 		{
 			$ibforums->lang['carbon_copy_desc'] = sprintf( $ibforums->lang['carbon_copy_desc'], $ibforums->member['g_max_mass_pm'] );

 			if ( isset($_POST['carbon_copy']) or $cc_text )
 			{
 				$cc_text = $cc_text ? $cc_text : $std->txt_htmlspecialchars($_POST['carbon_copy']);

 				$cc_box = preg_replace( "#</textarea>#i", "", $std->txt_stripslashes($cc_text) );
 			}

 			$this->output = str_replace( "<!--IBF.MASS_PM_BOX-->", $this->class->html->mass_pm_box($cc_box), $this->output );
 		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// Build contact listy poos
 	/*-------------------------------------------------------------------------*/

 	function build_contact_list()
	{
		global $DB, $std, $ibforums;

		$contacts = "";

 		$DB->simple_construct( array( 'select' => '*', 'from' => 'contacts', 'where' => "member_id=".$ibforums->member['id'], 'order' => "contact_name") );
 		$DB->simple_exec();

 		if ( $DB->get_num_rows() )
 		{
 			$contacts = "<select name='from_contact' class='forminput'><option value='-'>".$ibforums->lang['other']."</option>\n<option value='-'>--------------------</option>\n";

 			while ( $entry = $DB->fetch_row() )
 			{
 				$selected = ( $ibforums->input['from_contact'] == $entry['contact_id'] ) ? ' selected="selected"' : '';

 				$contacts .= "<option value='".$entry['contact_id']."'{$selected}>".$entry['contact_name']."</option>\n";
 			}

 			$contacts .= "</select>\n";
 		}
 		else
 		{
 			$contacts = $ibforums->lang['address_list_empty'];
 		}

 		return $contacts;
 	}


 	//-----------------------------------------
 	// API for deleting messages
 	//-----------------------------------------

 	function delete_messages($ids, $owner_id, $extra="")
 	{
		global $DB, $ibforums, $std, $print;

 		//-----------------------------------------
 		// Basic WHERE
 		//-----------------------------------------

 		if ( ! $extra )
 		{
 			$extra = "mt_owner_id=$owner_id";
 		}

 		$id_string = "";

 		if ( is_array( $ids ) )
 		{
 			if ( ! count($ids) )
 			{
 				return;
 			}

 			$id_string = 'IN ('.implode( ",", $ids ).')';
 		}
 		else
 		{
 			if ( ! $ids )
 			{
 				return;
 			}

 			$id_string = '='.$ids;
 		}

 		//-----------------------------------------
 		// Are these our messages?
 		//-----------------------------------------

 		$DB->simple_construct( array( 'select' => 'mt_id, mt_msg_id', 'from' => 'message_topics', 'where' => "$extra AND mt_id $id_string" ) );
 		$DB->simple_exec();

 		$final_ids = array();
 		$final_mts = array();

 		while ( $i = $DB->fetch_row() )
 		{
 			$final_ids[ $i['mt_id'] ] = $i['mt_msg_id'];
 			$final_mts[] = $i['mt_id'];
 		}

 		//-----------------------------------------
 		// Delete MT topics
 		//-----------------------------------------

 		if ( count($final_mts) )
 		{
 			$DB->simple_construct( array( 'delete' => 'message_topics', 'where' => "mt_id IN (".implode( ',',$final_mts ).")" ) );
 			$DB->simple_exec();
 		}

 		//-----------------------------------------
 		// Update delete count
 		//-----------------------------------------

 		if ( count($final_ids) )
 		{
 			$DB->simple_construct( array( 'update' => 'message_text', 'set' => "msg_deleted_count=msg_deleted_count+1", 'where' => "msg_id IN (".implode( ',',$final_ids ).")" ) );
 			$DB->simple_exec();
 		}

 		//-----------------------------------------
 		// Run through and delete dead msgs
 		//-----------------------------------------

 		$deleted_ids = array();
 		$attach_ids  = array();

 		$DB->simple_construct( array( 'select' => 'msg_id', 'from' => 'message_text', 'where' => 'msg_deleted_count >= msg_sent_to_count' ) );
 		$DB->simple_exec();

 		while ( $r = $DB->fetch_row() )
 		{
 			$deleted_ids[] = $r['msg_id'];
 		}

 		if ( count($deleted_ids) )
 		{
 			$DB->simple_construct( array( 'delete' => 'message_text', 'where' => "msg_id IN (".implode( ',',$deleted_ids ).")") );
 			$DB->simple_exec();

 			$DB->simple_construct( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_msg IN (".implode( ',',$deleted_ids ).")") );
 			$DB->simple_exec();

 			while ( $a = $DB->fetch_row() )
 			{
 				$attach_ids[] = $a['attach_id'];

 				if ( $a['attach_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$a['attach_location'] );
				}
				if ( $a['attach_thumb_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$a['attach_thumb_location'] );
				}
 			}

 			if ( count($attach_ids) )
 			{
 				$DB->simple_construct( array( 'delete' => 'attachments', 'where' => "attach_id IN (".implode( ',',$attach_ids ).")") );
 				$DB->simple_exec();
 			}
 		}
 	}

 	/*-------------------------------------------------------------------------*/
	// Send form stuff
	/*-------------------------------------------------------------------------*/

	function send_pm( $opts=array() )
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// INIT some vars
 		//-----------------------------------------

 		if ( ! $this->to and $this->to_by_id )
 		{
 			//-----------------------------------------
 			// Just an id...
 			//-----------------------------------------

 			$tmp = $DB->simple_exec_query( array( 'select' => 'name', 'from' => 'members', 'where' => 'id='.$this->to_by_id ) );

 			$this->to = $tmp['name'];
 		}

 		$this->to = strtolower(str_replace( '|', '&#124;', $this->to) );

 		$DB->cache_add_query( 'msg_get_cc_users', array( 'name_array' => array( 0 => "'".$this->to."'" ) ) );
		$DB->simple_exec();

 		if ( ! $this->send_to_member = $DB->fetch_row() )
 		{
 			$this->error = $ibforums->lang['err_no_such_member'];
 			return;
 		}

 		$this->error = "";
 		$this->save_only      = $opts['save_only'];
 		$this->orig_id        = $opts['orig_id'];
 		$this->preview        = $opts['preview'];
 		$this->add_tracking   = $opts['track'];
 		$this->add_sent       = $opts['add_sent'];
 		$this->hide_cc        = $opts['hide_cc'];

 		//-----------------------------------------
 		// Are we simply saving this for later?
 		//-----------------------------------------

 		$this->_process_save_only();

 		if ( $this->redirect_url )
 		{
 			return;
 		}

 		if ( $this->force_pm != 1 )
 		{
 			//-----------------------------------------
			// Can the reciepient use the PM system?
			//-----------------------------------------

			$DB->cache_add_query( 'msg_get_msg_poster', array( 'mid' => $this->send_to_member['id'] ) );
			$DB->simple_exec();

			$to_msg_stats = $DB->fetch_row();

			if ($to_msg_stats['g_use_pm'] != 1)
			{
				$ibforums->input['MID'] = $this->send_to_member['id'];
				$this->error = $ibforums->lang['no_usepm_member'];
				return;
			}

			//-----------------------------------------
			// Does the target member have enough room
			// in their inbox for a new message?
			//-----------------------------------------

			$to_msg_stats = $this->_get_real_allowance( $to_msg_stats );

			if ( (($to_msg_stats['msg_total']) >= $to_msg_stats['g_max_messages']) and ($to_msg_stats['g_max_messages'] > 0) )
			{
				$this->error = $ibforums->lang['no_usepm_member'];
				return;
			}

			//-----------------------------------------
			// Has the reciepient blocked us?
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => 'contact_id, allow_msg', 'from' => 'contacts', 'where' => "contact_id=".$this->from_member['id']." AND member_id=".$this->send_to_member['id'] ) );
			$DB->simple_exec();

			$can_msg = $DB->fetch_row();

			if ( (isset($can_msg['contact_id'])) and ($can_msg['allow_msg'] != 1) )
			{
				$ibforums->input['MID'] = $this->send_to_member['id'];
				$this->error = $ibforums->lang['msg_blocked'];
				return;
			}

			//-----------------------------------------
			// Do we have enough room to store a
			// saved copy?
			//-----------------------------------------

			if ($ibforums->input['add_sent'] and ($ibforums->member['g_max_messages'] > 0) )
			{
				if ( ($this->msg_stats['msg_total'] + 1) >= $ibforums->member['g_max_messages'] )
				{
					$this->error = $ibforums->lang['max_message_from'];
					return;
				}
			}
 		}

 		//-----------------------------------------
 		// CC PM stuff
 		//-----------------------------------------

 		$this->can_mass_pm = 0;

 		if ( $ibforums->member['g_max_mass_pm'] > 0 or $this->force_pm )
 		{
 			$cc_array = $this->_process_cc();
 		}

 		if ( $this->error != "" )
 		{
 			return;
 		}

 		//-----------------------------------------
 		// Add our original ID
 		//-----------------------------------------

 		$cc_array[ $this->send_to_member['id'] ] = $this->send_to_member;

 		unset($to_member);

 		//-----------------------------------------
 		// Insert the message body
 		//-----------------------------------------

 		$count = count( $cc_array );

 		if ( $this->add_sent )
 		{
 			// we're storing a copy locally, so
 			// add 1 to the "sent_to_count"

 			$count++;
 		}

 		$DB->do_insert( 'message_text', array(
											   'msg_date'	       => time(),
											   'msg_post'          => $std->remove_tags($this->msg_post),
											   'msg_cc_users'      => $this->cc_users,
											   'msg_sent_to_count' => $count,
											   'msg_post_key'      => $this->class->post_key,
											   'msg_author_id'     => $this->from_member['id']
									  )      );


		$msg_id = $DB->get_insert_id();

		//-----------------------------------------
		// Make attachments permanent
		//-----------------------------------------

		$no_attachments = $this->postlib->pf_make_attachments_permanent( $this->class->post_key, "", "", $msg_id );

		//-----------------------------------------
		// If we have an original ID - delete it and 'move'
		// attachments
		//-----------------------------------------

		if ( $this->orig_id )
		{
			$DB->cache_add_query( 'msg_get_saved_msg', array( 'mid' => $this->from_member['id'], 'msgid' => $this->orig_id ) );
			$DB->simple_exec();

			if( $old = $DB->fetch_row() )
			{
				//-----------------------------------------
				// Update attachments
				//-----------------------------------------

				$DB->simple_construct( array( 'update' => 'attachments', 'set' => "attach_post_key='{$this->class->post_key}', attach_msg=$msg_id", 'where' => "attach_msg={$old['msg_id']}" ) );
				$DB->simple_exec();

				$DB->simple_construct( array( 'delete' => 'message_topics', 'where' => "mt_id={$old['mt_id']}" ) );
				$DB->simple_exec();

				$DB->simple_construct( array ( 'update' => 'message_text', 'set' => 'msg_deleted_count=msg_deleted_count-1', 'where' => "msg_id={$old['msg_id']}" ) );
				$DB->simple_exec();

				$no_attachments = $old['mt_hasattach'];
			}
		}

 		//-----------------------------------------
 		// loop....
 		//-----------------------------------------

 		foreach ($cc_array as $user_id => $to_member)
 		{
			//-----------------------------------------
			// Sort out tracking and pop us status
			//-----------------------------------------

			$show_popup =  $to_member['view_pop'];

			//-----------------------------------------
			// Enter the info into the DB
			// Target user side.
			//-----------------------------------------

			$DB->do_insert( 'message_topics', array(
													 'mt_msg_id'     => $msg_id,
													 'mt_date'       => time(),
													 'mt_title'      => $this->msg_title,
													 'mt_from_id'    => $this->from_member['id'],
													 'mt_to_id'      => $to_member['id'],
													 'mt_vid_folder' => 'in',
													 'mt_hide_cc'    => 0,
													 'mt_tracking'   => $this->add_tracking,
													 'mt_hasattach'  => intval($no_attachments),
													 'mt_owner_id'   => $to_member['id'],
													 'mt_hide_cc'    => $this->hide_cc,
									       )      );


			$mt_id = $DB->get_insert_id();

			//-----------------------------------------
			// Update profile
			//-----------------------------------------

			$inbox_count = $this->_get_dir_count( $to_member['vdirs'], 'in' );

			$new_vdir = $this->rebuild_dir_count( $to_member['id'],
												  "",
												  'in',
												  $inbox_count + 1,
												  'save',
												  "msg_total=msg_total+1,new_msg=new_msg+1,show_popup={$show_popup}"
												);

			//-----------------------------------------
			// Has this member requested a PM email nofity?
			//-----------------------------------------

			if ($to_member['email_pm'] == 1)
			{
				$to_member['language'] = $to_member['language'] == "" ? 'en' : $to_member['language'];

				$this->postlib->email->get_template("pm_notify", $to_member['language']);

				$this->postlib->email->build_message( array(
													'NAME'   => $to_member['name'],
													'POSTER' => $this->from_member['name'],
													'TITLE'  => $this->msg_title,
													'LINK'   => "?act=Msg&CODE=03&VID=in&MSID=$mt_id",
													)       );

				$this->postlib->email->subject = $ibforums->lang['pm_email_subject'];
				$this->postlib->email->to      = $to_member['email'];
				$this->postlib->email->send_mail();

			}
		}

 		//-----------------------------------------
 		// Add the data to the current members DB if we are
 		// adding it to our "sent items" folder
 		//-----------------------------------------

 		if ( $this->add_sent )
 		{
 			$sent_count = $this->_get_dir_count( $this->from_member['vdirs'], 'sent' );

			$this->rebuild_dir_count( $this->from_member['id'],
									  "",
									  'sent',
									  $sent_count + 1,
									  'save',
									  "msg_total=msg_total+1"
									);

			$DB->do_insert( 'message_topics', array(
													 'mt_msg_id'     => $msg_id,
													 'mt_date'       => time(),
													 'mt_title'      => $this->msg_title,
													 'mt_from_id'    => $this->from_member['id'],
													 'mt_to_id'      => $this->send_to_member['id'],
													 'mt_vid_folder' => 'sent',
													 'mt_hide_cc'    => 0,
													 'mt_tracking'   => 0,
													 'mt_hasattach'  => intval($no_attachments),
													 'mt_owner_id'   => $this->from_member['id'],
													 'mt_hide_cc'    => $this->hide_cc,
									       )      );

		}

		$this->to_by_id = "";
		$this->to       = "";
 	}


 	/*-------------------------------------------------------------------------*/
	// Rebuild DIR count
	/*-------------------------------------------------------------------------*/

	function rebuild_dir_count($mid, $vdir, $cur_dir, $new_count, $nosave='save', $extra="")
	{
		global $DB, $std, $ibforums, $forums;

		$rebuild = array();

		if ( ! $vdir )
		{
			$DB->simple_construct( array( "select" => 'vdirs', 'from' => 'member_extra', 'where' => 'id='.$mid ) );
			$DB->simple_exec();

			$mem = $DB->fetch_row();

			$vdir = $mem['vdirs'] ? $mem['vdirs'] : 'in:Inbox;0|sent:Sent Items;0';
		}

		foreach( explode( "|", $vdir ) as $dir )
    	{
    		list ($id  , $data)  = explode( ":", $dir );
    		list ($real, $count) = explode( ";", $data );

    		if ( ! $id )
    		{
    			continue;
    		}

    		if ( $id == $cur_dir )
    		{
    			$count = $new_count;
    			$count = $count < 1 ? 0 : $count;
    		}

    		$rebuild[$id] = $id.':'.$real.';'.intval($count);
    	}

    	$final = implode( '|', $rebuild );

    	if ( $nosave != 'nosave' )
    	{
			$DB->simple_construct( array( 'update' =>  'member_extra', 'set' => 'vdirs="'.$final.'"', 'where' => 'id='.$mid ) );
			$DB->simple_exec();

			if ( $extra )
			{
				$DB->simple_construct( array( 'update' =>  'members', 'set' => $extra, 'where' => 'id='.$mid ) );
				$DB->simple_exec();
			}
    	}

    	return $final;

	}


 	/*-------------------------------------------------------------------------*/
	// POST PROCESS CC
	/*-------------------------------------------------------------------------*/

	function format_cc_string($cc_users, $mid )
	{
		global $DB, $std, $ibforums;

		$cc_array = array();
		$final    = array();

		$cc_array = $this->get_cc_array( $cc_users );

		foreach( $cc_array as $id => $data )
		{
			if ( $id == $mid )
			{
				continue;
			}

			$final[] = $std->make_profile_link( $data['name'], $data['id'] );
		}

		return implode( ", ", $final );

	}

	/*-------------------------------------------------------------------------*/
	// PROCESS CC
	/*-------------------------------------------------------------------------*/

	function get_cc_array($cc_users)
	{
		global $DB, $std, $ibforums, $forums;

 		$cc_array = array();

		$cc_users = strtolower(str_replace( '|', '&#124;', $cc_users) );

		if ( $cc_users )
		{
			//-----------------------------------------
			// Sort out the array
			//-----------------------------------------

			$cc_users = str_replace(  "<br><br>", "<br>" , trim($cc_users) );
			$cc_users = preg_replace( "/^(<br>){1}/", "" , $cc_users );
			$cc_users = preg_replace( "/(<br>){1}$/", "" , $cc_users );
			$cc_users = preg_replace( "/<br>\s+/",  ","  , $cc_users );

			$temp_array = explode( "<br>", $cc_users );

			//-----------------------------------------
			// Make SQL'able
			//-----------------------------------------

			if ( is_array($temp_array) and count($temp_array) > 0 )
			{
				$new_array = array();

				foreach( $temp_array as $name )
				{
					$name  = "'".trim(strtolower($name))."'";

					if (in_array( $name, $new_array ) )
					{
						continue;
					}

					$new_array[] = $name;
				}
			}

			//-----------------------------------------
			// SQL it
			//-----------------------------------------

			if ( is_array($new_array) and count($new_array) > 0 )
			{
				$array_count = count($new_array);

				$DB->cache_add_query( 'msg_get_cc_users', array( 'name_array' => $new_array ) );
				$DB->simple_exec();

				while( $r = $DB->fetch_row() )
				{
					$cc_array[$r['id']] = $r;
				}
			}
		}

		return $cc_array;
	}


	/*-------------------------------------------------------------------------*/
	// PROCESS CC
	/*-------------------------------------------------------------------------*/

	function _process_cc()
	{
		global $DB, $std, $ibforums, $forums;

		$this->can_mass_pm = 1;
 		$cc_array = array();

		$this->cc_users = strtolower(str_replace( '|', '&#124;', $this->cc_users) );

		if (isset($this->cc_users) and $this->cc_users != "")
		{
			//-----------------------------------------
			// Sort out the array
			//-----------------------------------------

			$this->cc_users = str_replace(  "<br>", "<br />" , trim($this->cc_users) );
			$this->cc_users = str_replace(  "<br /><br />", "<br />" , trim($this->cc_users) );
			$this->cc_users = preg_replace( "#^(<br />){1}#", "" , $this->cc_users );
			$this->cc_users = preg_replace( "#(<br />){1}$#", "" , $this->cc_users );
			$this->cc_users = preg_replace( "#<br />\s+#",  ","  , $this->cc_users );

			$temp_array = explode( "<br />", $this->cc_users );

			//-----------------------------------------
			// Make SQL'able
			//-----------------------------------------

			if ( is_array($temp_array) and count($temp_array) > 0 )
			{
				$new_array = array();

				foreach( $temp_array as $name )
				{
					$name  = "'".trim(strtolower($name))."'";

					if (in_array( $name, $new_array ) )
					{
						continue;
					}

					$new_array[] = $name;
				}
			}

			//-----------------------------------------
			// SQL it
			//-----------------------------------------

			if ( is_array($new_array) and count($new_array) > 0 )
			{
				$array_count = count($new_array);

				$DB->cache_add_query( 'msg_get_cc_users', array( 'name_array' => $new_array ) );
				$DB->simple_exec();

				if ( ! $DB->get_num_rows() )
				{
					$this->error = $ibforums->lang['pme_no_cc_user'];
					return;
				}
				else
				{
					while( $r = $DB->fetch_row() )
					{
						$cc_array[$r['id']] = $r;
					}

					//-----------------------------------------

					if ( $this->force_pm != 1 )
					{
						if ( count($cc_array) > $ibforums->member['g_max_mass_pm'])
						{
							$ibforums->input['MID'] = $this->send_to_member['id'];
							$this->error = $ibforums->lang['pme_too_many'];
							return;
						}
					}

					//-----------------------------------------
					// Names exist?
					//-----------------------------------------

					$cc_error = "";

					if ( count($cc_array) != $array_count )
					{
						foreach( $new_array as $n )
						{
							$seen = 0;

							foreach( $cc_array as $idx => $cc_user )
							{
								$tmp = "'".strtolower($cc_user['name'])."'";

								if ($tmp == $n)
								{
									$seen = 1;
								}
							}

							if ($seen != 1)
							{
								$cc_error .= "<br>".sprintf( $ibforums->lang['pme_failed_nomem'], $n, $n );
							}
						}
					}

					if ($cc_error != "")
					{
						$ibforums->input['MID'] = $this->send_to_member['id'];
						$this->error = $cc_error;
						return;
					}

					//-----------------------------------------
					// Can use PM system?
					//-----------------------------------------

					$cc_error   = "";
					$cc_id_array = array();

					foreach($cc_array as $idx => $cc_user)
					{
						if ($cc_user['g_use_pm'] != 1)
						{
							$cc_error .= "<br>".sprintf( $ibforums->lang['pme_failed_nopm'], $cc_user['name'], $cc_user['name'] );
						}

						$cc_user = $this->_get_real_allowance($cc_user);

						if ($cc_user['g_max_messages'] > 0 and ($cc_user['msg_total'] + 1 > $cc_user['g_max_messages']) )
						{
							$cc_error .= "<br>".sprintf( $ibforums->lang['pme_failed_maxed'], $cc_user['name'], $cc_user['name'] );
						}

						$cc_id_array[] = $cc_user['id'];
					}

					if ( $this->force_pm != 1 )
					{
						if ($cc_error != "")
						{
							$ibforums->input['MID'] = $this->send_to_member['id'];
							$this->error = $cc_error;
							return;
						}
					}

					//-----------------------------------------
					// Check the block list..
					//-----------------------------------------

					$DB->cache_add_query( 'msg_get_cc_blocked', array( 'mid' => $this->from_member['id'], 'cc_array' => $cc_id_array ) );
					$DB->simple_exec();

					while ( $c = $DB->fetch_row() )
					{
						if ($c['allow_msg'] != 1)
						{
							$cc_error .= "<br>".sprintf( $ibforums->lang['pme_failed_block'], $c['name'], $c['name'] );
						}
					}

					if ( $this->force_pm != 1 )
					{
						if ($cc_error != "")
						{
							$ibforums->input['MID'] = $this->send_to_member['id'];
							$this->error = $cc_error;
							return;
						}
					}
				}
			}
		}

		return $cc_array;
	}

	/*-------------------------------------------------------------------------*/
	// SAVE stuff
	/*-------------------------------------------------------------------------*/

	function _process_save_only()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( $this->save_only )
 		{

			$raw = array(
						  'msg_date'	      => time(),
						  'msg_post'          => $std->remove_tags($this->msg_post),
						  'msg_cc_users'      => $this->cc_users,
						  'msg_sent_to_count' => 1,
						  'msg_post_key'      => $this->class->post_key,
						  'msg_author_id'     => $this->from_member['id']
						);

			$saved = 0;

			if ( $this->orig_id )
			{
				//-----------------------------------------
				// We have an OID which means that this message
				// is already from the unsent folder, lets check that
				// and if true, update rather than create a new unsent
				// row
				//-----------------------------------------

				$DB->simple_construct( array( 'select' => 'mt_id, mt_msg_id', 'from' => 'message_topics', 'where' => "mt_id=".$this->orig_id." AND mt_owner_id=".$this->from_member['id']." AND mt_vid_folder='unsent'" ) );
				$DB->simple_exec();

				if ( $omsg = $DB->fetch_row() )
				{
					$saved = 1;

					$DB->do_update( 'message_text', $raw, "msg_id=".$omsg['mt_msg_id'] );

					//-----------------------------------------
					// Make attachments permanent
					//-----------------------------------------

					$no_attachments = $this->postlib->pf_make_attachments_permanent( $this->class->post_key, "", "", $omsg['mt_msg_id'] );

					$DB->simple_construct( array( 'update' => 'message_topics', 'set' => "mt_hasattach=$no_attachments, mt_date=".time(), 'where' => 'mt_owner_id='.$ibforums->member['id'].' AND mt_id='.$omsg['mt_id'] ) );
					$DB->simple_exec();
				}
			}

			if ($saved == 0)
			{
				$DB->do_insert( 'message_text', $raw);

				$msg_id = $DB->get_insert_id();

				//-----------------------------------------
				// Make attachments permanent
				//-----------------------------------------

				$no_attachments = $this->postlib->pf_make_attachments_permanent( $this->class->post_key, "", "", $msg_id );

				$DB->do_insert( 'message_topics', array(
														 'mt_msg_id'     => $msg_id,
														 'mt_date'       => time(),
														 'mt_title'      => $this->msg_title,
														 'mt_from_id'    => $this->from_member['id'],
														 'mt_to_id'      => $this->send_to_member['id'],
														 'mt_vid_folder' => 'unsent',
														 'mt_hide_cc'    => 0,
														 'mt_tracking'   => 0,
														 'mt_hasattach'  => intval($no_attachments),
														 'mt_owner_id'   => $this->from_member['id'],
											   )      );
			}

			$this->redirect_url  = "&act=Msg&CODE=01";
			$this->redirect_lang = $ibforums->lang['pms_redirect'];
 		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// Return count of current VDIR (quickly)
 	/*-------------------------------------------------------------------------*/

 	function _get_dir_count( $vdir, $vid )
 	{
 		preg_match( "#(?:^|\|)$vid:.+?;(\d+)(?:\||$)#i", $vdir, $match );

		return intval($match[1]);
 	}

 	/*-------------------------------------------------------------------------*/
 	// Get real allowance based on multi-groups
 	/*-------------------------------------------------------------------------*/

 	function _get_real_allowance( $member )
 	{
		global $ibforums, $DB, $std;

 		$groups_id = explode( ',', $member['mgroup_others'] );

 		if ( count( $groups_id ) )
		{
			foreach( $groups_id as $pid )
			{
				if ( ! $ibforums->cache['group_cache'][ $pid ]['g_id'] )
				{
					continue;
				}

				if ( $ibforums->cache['group_cache'][ $pid ]['g_max_messages'] > $member['g_max_messages'] )
				{
					$member['g_max_messages'] = $ibforums->cache['group_cache'][ $pid ]['g_max_messages'];
				}
			}
		}

		return $member;
 	}


}

?>