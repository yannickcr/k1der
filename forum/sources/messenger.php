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
|   > Messenger functions
|   > Module written by Matt Mecham
|   > Date started: 26th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class messenger
{
    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";
    var $email      = "";

    var $msg_stats  = array();
    var $prefs      = "";

    var $member     = array();
    var $m_group    = array();

    var $to_mem     = array();

    var $jump_html  = "";

    var $vid        = "in";
    var $mem_groups = array();
    var $mem_titles = array();

    var $topiclib   = "";
    var $postlib    = "";

    var $parser     = "";

    var $cp_html    = "";
    var $edit_saved = "";

    /*-------------------------------------------------------------------------*/
    // Auto-run
    /*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_msg', $ibforums->lang_id);
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_ucp', $ibforums->lang_id);

    	//-----------------------------------------

    	$this->html = $std->load_template('skin_msg');

    	//-----------------------------------------

    	$this->cp_html = $std->load_template('skin_ucp');

    	//-----------------------------------------

    	require_once( ROOT_PATH.'sources/lib/msg_functions.php' );

 		$this->lib = new msg_functions();

    	//-----------------------------------------

    	$this->base_url        = $ibforums->base_url;
    	$this->base_url_nosess = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}";

    	//-----------------------------------------
    	// Check viewing permissions, etc
		//-----------------------------------------

		if ( ! $ibforums->member['g_use_pm'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_use_messenger' ) );
		}

		if ( ! $ibforums->member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

    	//-----------------------------------------
		// Get all member info..
    	//-----------------------------------------

    	$DB->cache_add_query( 'generic_get_all_member', array( 'mid' => $ibforums->member['id'] ) );
		$DB->cache_exec_query();

    	if ( ! $member = $DB->fetch_row() )
    	{
    		$DB->do_insert( 'member_extra', array( 'id' => $ibforums->member['id'] ) );
    	}
    	else
    	{
    		$ibforums->member = array_merge( $member, $ibforums->member );
    	}

    	//-----------------------------------------
    	// Do a little set up, do a litle dance, get
    	// down tonight! *boogie*
    	//-----------------------------------------

    	$this->jump_html = "<select name='VID' class='forminput'>\n";

    	$ibforums->member['dir_data'] = array();

    	//-----------------------------------------
    	// Do we have VID?
    	// No, it's just the way we walk! Haha, etc.
    	//-----------------------------------------

    	if ($ibforums->input['VID'])
    	{
    		$this->vid = $ibforums->input['VID'];
    	}

    	if ( ! $ibforums->member['vdirs'] )
    	{
    		$ibforums->member['vdirs'] = "in:Inbox;0|sent:Sent Items;0";
    	}

    	$folder_links = "";

    	foreach( explode( "|", $ibforums->member['vdirs'] ) as $dir )
    	{
    		list ($id  , $data)  = explode( ":", $dir );
    		list ($real, $count) = explode( ";", $data );

    		if ( ! $id )
    		{
    			continue;
    		}

    		$ibforums->member['dir_data'][$id] = array( 'id' => $id, 'real' => $real, 'count' => $count );

    		if ($this->vid == $id)
    		{
    			$ibforums->member['current_dir'] = $real;
    			$ibforums->member['current_id']  = $id;
    			$this->jump_html .= "<option value='$id' selected='selected'>$real</option>\n";
    		}
    		else
    		{
    			$this->jump_html .= "<option value='$id'>$real</option>\n";
    		}

    		if ( $count )
    		{
    			$real .= " ({$count})";
    		}

    		$folder_links .= $this->cp_html->menu_bar_msg_folder_link($id, $real);

    	}

    	$this->jump_html .= "<!--EXTRA--></select>\n\n";

    	$menu_html = $this->cp_html->Menu_bar($this->base_url);

    	if ( $folder_links != "" )
		{
			$menu_html = str_replace( "<!--IBF.FOLDER_LINKS-->", $folder_links, $menu_html );
		}

		//-----------------------------------------
    	// Using Sub Manager?
    	//-----------------------------------------

		if ( @is_dir( ROOT_PATH.'/modules/subsmanager' ) )
		{
			$url  = $ibforums->base_url."act=module&amp;module=subscription&amp;CODE=index";
			$name = $ibforums->lang['new_sub_link'];

			$menu_html = str_replace( "<!--IBF.OPTION_LINKS-->", $this->cp_html->menu_bar_new_link( $url, $name ), $menu_html );
		}

    	$print->add_output( $menu_html );

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['CODE'])
    	{
    		case '01':
    			$this->msg_list();
    			break;
    		case '02':
    			$this->contact();
    			break;
    		case '03':
    			$this->view_msg();
    			break;
    		case '04';
    			$this->send();
    			break;
    		case '05':
    			$this->delete();
    			break;
    		case '06':
    			$this->multi_act();
    			break;
    		case '07':
    			$this->prefs();
    			break;
    		case '08':
    			$this->do_prefs();
    			break;
    		case '09':
    			$this->add_member();
    			break;
    		case '10':
    			$this->del_member();
    			break;
    		case '11':
    			$this->edit_member();
    			break;
    		case '12':
    			$this->do_edit();
    			break;
    		case '14':
    			$this->archive();
    			break;
    		case '15':
    			$this->do_archive();
    			break;

    		case '20':
    			$this->view_saved();
    			break;

    		case '21':
    			$this->edit_saved = 1;
    			$this->send();
    			break;

    		case '30':
    			$this->show_tracking();
    			break;

    		case '31':
    			$this->end_tracking();
    			break;

    		case '32':
    			$this->del_tracked();
    			break;

    		case 'delete':
    			$this->start_empty_folders();
    			break;
    		case 'dofolderdelete':
    			$this->end_empty_folders();
    			break;

    		default:
    			$this->msg_list();
    			break;
    	}

    	// If we have any HTML to print, do so...

    	$fj = $std->build_forum_jump();
		$fj = preg_replace( "!#Forum Jump#!", $ibforums->lang['forum_jump'], $fj);

		$this->output .= $this->cp_html->CP_end();

		$this->output .= $this->cp_html->forum_jump($fj);

    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
 	}

 	/*-------------------------------------------------------------------------*/
 	// Empty PM folders:
 	//
 	// Interface for removing PM's on a folder by folder basis
 	/*-------------------------------------------------------------------------*/

 	function start_empty_folders()
 	{
		global $ibforums, $DB, $std, $print;

 		$this->output .= $this->html->empty_folder_header();

 		//-----------------------------------------
 		// Get the PM count - 1 query?
 		//-----------------------------------------

 		$count = array( 'unsent' => 0 );
 		$names = array( 'unsent' => $ibforums->lang['fd_unsent'] );

 		foreach( $ibforums->member['dir_data'] as $k => $v )
 		{
 			$count[ $v['id'] ] = 0;
 			$names[ $v['id'] ] = $v['real'];
 		}

 		$DB->simple_construct( array( 'select' => 'mt_id, mt_vid_folder, mt_msg_id', 'from' => 'message_topics', 'where' => 'mt_owner_id='.$ibforums->member['id'] ) );
 		$DB->simple_exec();

 		while( $r = $DB->fetch_row() )
 		{
 			if ( $r['mt_vid_folder'] == "" )
 			{
 				$count['in']++;
 			}
 			else
 			{
 				$count[ $r['mt_vid_folder'] ]++;
 			}
 		}

 		foreach( $names as $vid => $name )
 		{
 			$this->output .= $this->html->empty_folder_row( $name, $vid, $count[$vid] );
 		}

 		$this->output .= $this->html->empty_folder_save_unread();
 		$this->output .= $this->html->empty_folder_footer();

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}

 	/*-------------------------------------------------------------------------*/
 	// DELETE emptied PMS
 	/*-------------------------------------------------------------------------*/

 	function end_empty_folders()
 	{
		global $ibforums, $DB, $std, $print;

 		$names = array( 'unsent' => $ibforums->lang['fd_unsent'] );
 		$ids   = array();
 		$qe = "";

 		foreach( $ibforums->member['dir_data'] as $k => $v )
 		{
 			$names[ $v['id'] ] = $v['real'];
 		}

 		//-----------------------------------------
 		// Did we check any boxes?
 		//-----------------------------------------

 		foreach( $names as $vid => $name )
 		{
 			if ( $ibforums->input['its_'.$vid] == 1 )
 			{
 				$ids[] = $vid;
 			}
 		}

 		if ( count($ids) < 1 )
 		{
 			$std->Error( array(  'LEVEL' => 1, 'MSG' => 'fd_noneselected' ) );
 		}

 		//-----------------------------------------
 		// Delete em!
 		//-----------------------------------------

 		if ( $ibforums->input['save_unread'] )
 		{
 			$qe = ' AND mt_read=1';
 		}

 		$mtids = array();

 		$DB->simple_construct( array( 'select' => 'mt_id', 'from' => 'message_topics', 'where' => 'mt_owner_id='.$ibforums->member['id']." AND mt_vid_folder IN('".implode("','", $ids)."')".$qe ) );
 		$DB->simple_exec();

 		while( $d = $DB->fetch_row() )
 		{
 			$mtids[] = $d['mt_id'];
 		}

 		$this->lib->delete_messages( $mtids, $ibforums->member['id'] );

 		$DB->simple_construct( array ( 'select' => 'COUNT(*) as msg_total', 'from' => 'message_topics', 'where' => "mt_owner_id=".$ibforums->member['id']." AND mt_vid_folder <> 'unsent'" ) );
 		$DB->simple_exec();

 		$total = $DB->fetch_row();

 		$total['msg_total'] = intval($total['msg_total']);

 		$DB->simple_construct( array ( 'update'=> 'members', 'set' => "msg_total=".$total['msg_total'], 'where' => "id=".$ibforums->member['id'] ) ) ;
 		$DB->simple_exec();

 		$std->boink_it($ibforums->base_url."act=Msg&CODE=delete");
 	}


 	/*-------------------------------------------------------------------------*/
 	// ARCHIVE:
 	//
 	// Allows a user to archive and email a HTML file
 	/*-------------------------------------------------------------------------*/

 	function archive()
 	{
		global $ibforums, $DB, $std, $print;

 		$this->jump_html = preg_replace("/<!--EXTRA-->/", "<option value='all'>".$ibforums->lang['all_folders']."</option>", $this->jump_html );

 		$this->output .= $this->html->archive_form( $this->jump_html );

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}

 	/*-------------------------------------------------------------------------*/
 	// Process archive
 	/*-------------------------------------------------------------------------*/

 	function do_archive()
 	{
		global $ibforums, $DB, $std, $print;

 		require ROOT_PATH."sources/classes/class_email.php";

		$this->email = new emailer();

 		//-----------------------------------------
 		// Did we specify a folder, or choose all?
 		//-----------------------------------------

 		$folder_query  = "";
 		$msg_ids       = array();
 		$older_newer   = '>';

 		if ( $ibforums->input['oldnew'] == 'older' )
 		{
 			$older_newer = '<';
 		}

 		if ($ibforums->input['VID'] != 'all')
 		{
 			$folder_query = " AND mt.mt_vid_folder='".$ibforums->input['VID']."'";
 		}

 		if ( $ibforums->input['dateline'] == 'all' )
 		{
 			$time_cut    = 0;
 			$older_newer = '>';
 		}
 		else
 		{
 			$time_cut = time() - ($ibforums->input['dateline'] * 60 * 60 *24);
 		}

 		//-----------------------------------------
 		// Check the input...
 		//-----------------------------------------

 		$ibforums->input['number'] = intval( $ibforums->input['number'] );

 		if ($ibforums->input['number'] < 5)
 		{
 			$ibforums->input['number'] = 5;
 		}

 		if ($ibforums->input['number'] > 50)
 		{
 			$ibforums->input['number'] = 50;
 		}

 		$type      = 'html';
 		$file_name = "pm_archive.html";
 		$ctype     = "text/html";

 		if ($ibforums->input['type'] == 'xls')
 		{
 			$type      = 'xls';
 			$file_name = "xls_importable.txt";
 			$ctype     = "text/plain";
 		}

 		$output = "";

 		//-----------------------------------------
 		// Start the datafile..
 		//-----------------------------------------

 		if ($type == 'html')
 		{
 			$output .= $this->html->archive_html_header();
 		}

 		require ROOT_PATH."sources/lib/post_parser.php";

        $this->parser = new post_parser();

 		//-----------------------------------------
 		// Get the messages...
 		//-----------------------------------------

 		$DB->cache_add_query( 'msg_get_msg_archive', array( 'mid' => $ibforums->member['id'], 'limit_b' => $ibforums->input['number'], 'older_newer' => $older_newer, 'time_cut' => $time_cut, 'folder_query' => $folder_query ) );
 		$DB->cache_exec_query();

 		//-----------------------------------------
 		// Repeat after me..
 		//-----------------------------------------

 		if ( $DB->get_num_rows() )
 		{
 			while ( $r = $DB->fetch_row() )
 			{
 				$info = array();

 				$msg_ids[] = $r['mt_id'];

 				$info['msg_date']    = $std->get_date( $r['mt_date'], 'LONG' );
 				$info['msg_title']   = $r['mt_title'];
 				$info['msg_sender']  = $r['name'];
				$info['msg_content'] = $this->parser->convert( array( 'TEXT'    => $r['msg_post'],
																	  'SMILIES' => 0,
																	  'CODE'    => $ibforums->vars['msg_allow_code'],
																	  'HTML'    => $ibforums->vars['msg_allow_html']
																	)
															 );

 				if ($type == 'xls')
 				{
 					$output .= '"'.$this->strip_quotes($info['msg_title']).'","'.$this->strip_quotes($info['msg_date']).'","'.$this->strip_quotes($info['msg_sender']).'","'.$this->strip_quotes($info['msg_content']).'"'."\r";
 				}
 				else
 				{
 					if ( $r['vid'] == 'sent' )
 					{
 						$info['msg_sender']  = $r['rec_name'];
 						$output .= $this->html->archive_html_entry_sent($info);
 					}
 					else
 					{
 						$output .= $this->html->archive_html_entry($info);
 					}
 				}
 			}

 			if ($type == 'html')
			{
				$output .= $this->html->archive_html_footer();
			}

			$num_msg = count( $msg_ids );

			//-----------------------------------------
			// Delete?
			//-----------------------------------------

			if ($ibforums->input['delete'] == 'yes')
			{
				$this->lib->delete_messages( $msg_ids, $ibforums->member['id'] );

				$DB->simple_construct( array ( 'select' => 'COUNT(*) as msg_total', 'from' => 'message_topics', 'where' => "mt_owner_id=".$ibforums->member['id']." AND mt_vid_folder <> 'unsent'" ) );
				$DB->simple_exec();

				$total = $DB->fetch_row();

				$total['msg_total'] = intval($total['msg_total']);

				$DB->simple_construct( array ( 'update'=> 'members', 'set' => "msg_total=".$total['msg_total'], 'where' => "id=".$ibforums->member['id'] ) ) ;
				$DB->simple_exec();
			}

			//-----------------------------------------
			// Process & Print
			//-----------------------------------------

			$output = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $output );

			$this->email->get_template("pm_archive");

			$this->email->build_message( array( 'NAME' => $ibforums->member['name'] ) );

			$this->email->subject = $ibforums->lang['arc_email_subject'];
			$this->email->to      = $ibforums->member['email'];
			$this->email->add_attachment( $output, $file_name, $ctype );
			$this->email->send_mail();

			//-----------------------------------------
			// Done..
			//-----------------------------------------

			$ibforums->lang['arc_complete'] = str_replace( "<#NUM#>", $num_msg, $ibforums->lang['arc_complete'] );

			$this->output .= $this->html->archive_complete();

			$this->page_title = $ibforums->lang['t_welcome'];
			$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

		}
		else
		{
			$std->Error( array(  'LEVEL' => 1, 'MSG' => 'no_archive_messages' ) );
		}
 	}

	/*-------------------------------------------------------------------------*/
	// Strip Quotes
	/*-------------------------------------------------------------------------*/

	function strip_quotes($text) {

 		return preg_replace( "/\"/", '\\\"', $text );
 	}

 	/*-------------------------------------------------------------------------*/
 	// PREFS:
 	//
 	// Create/delete/edit messenger folders
 	/*-------------------------------------------------------------------------*/

 	function prefs()
 	{
		global $ibforums, $DB, $std, $print;

 		$this->output .= $this->html->prefs_header();

 		$max = 1;

 		foreach( $ibforums->member['dir_data'] as $k => $v )
 		{
 			$extra = "";
 			if ($v['id'] == 'in' or $v['id'] == 'sent')
 			{
 				$extra = "&nbsp;&nbsp;( ".$v['real']." - ".$ibforums->lang['cannot_remove']." )";
 			}

 			$this->output .= $this->html->prefs_row( array( 'ID' => $v['id'], 'REAL' => $v['real'], 'EXTRA' => $extra ) );

 			if ( stristr( $v['id'], 'dir_' ) )
 			{
 				$max = intval( str_replace( 'dir_', "", $v['id'] ) ) + 1;
 			}
 		}

 		$count = $max + 1;

 		$this->output .= $this->html->prefs_add_dirs();

 		for ($i = $count; $i < $count+3; $i++)
 		{
 			$this->output .= $this->html->prefs_row( array( 'ID' => 'dir_'.$i, 'REAL' => '', 'EXTRA' => '' ) );
 		}

 		$this->output .= $this->html->prefs_footer();

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}

 	/*-------------------------------------------------------------------------*/
 	// SAVE FOLDERS
 	/*-------------------------------------------------------------------------*/

 	function do_prefs()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Check to ensure than we've not tried to
 		// remove the inbox and sent items directories.
 		//-----------------------------------------

 		if ( ($ibforums->input['sent'] == "") or ($ibforums->input['in'] == "") )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cannot_remove_dir' ) );
 		}

 		$cur_dir = array();

 		foreach( explode( "|", $ibforums->member['vdirs'] ) as $dir )
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

    		$cur_dir[$id] = intval($count);
    	}

 		$v_dir = 'in:'.$ibforums->input['in'].';'.intval($cur_dir['in']).'|sent:'.$ibforums->input['sent'].';'.intval($cur_dir['sent']);

 		//-----------------------------------------
 		// Fetch the rest of the dirs
 		//-----------------------------------------

 		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^dir_(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$count = intval( $cur_dir[ $match[0] ] );

 					$v_dir .= '|'.$match[0].':'.trim(str_replace( '|', '', str_replace( ";", "", $ibforums->input[$match[0]] ) ) ).';'.$count;
 				}
 			}
 		}

 		$DB->simple_construct( array('update' => 'member_extra', 'set' => "vdirs='$v_dir'", 'where' => 'id='.$ibforums->member['id']) );
 		$DB->simple_exec();

 		$std->boink_it($ibforums->base_url."act=Msg&CODE=07");
 	}

 	/*-------------------------------------------------------------------------*/
 	// DELETE_MEMBER:
 	//
 	// Removes a member from address book.
 	/*-------------------------------------------------------------------------*/

 	function del_member()
 	{
		global $ibforums, $DB, $std, $print;

 		if (!$ibforums->input['MID'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		if (! preg_match( "/^(\d+)$/", $ibforums->input['MID'] ) )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		$DB->simple_exec_query( array( 'delete' => 'contacts', 'where' => "member_id={$ibforums->member['id']} AND contact_id={$ibforums->input['MID']}" ) );

 		$std->boink_it($this->base_url."act=Msg&CODE=02");
	}

	/*-------------------------------------------------------------------------*/
 	// EDIT_MEMBER:
 	//
 	// Edit a member from address book.
 	/*-------------------------------------------------------------------------*/

 	function edit_member()
 	{
		global $ibforums, $DB, $std, $print;

 		if (!$ibforums->input['MID'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		if (! preg_match( "/^(\d+)$/", $ibforums->input['MID'] ) )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		$DB->simple_construct( array( 'select' => '*', 'from' => 'contacts', 'where' => "member_id={$ibforums->member['id']} AND contact_id={$ibforums->input['MID']}" ) );
		$DB->simple_exec();

 		$memb = $DB->fetch_row();

 		if (!$memb['contact_id'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		$html = "<select name='allow_msg' class='forminput'>";

 		if ($memb['allow_msg'])
 		{
 			$html .= "<option value='yes' selected>{$ibforums->lang['yes']}</option><option value='no'>{$ibforums->lang['no']}";
 		}
 		else
 		{
 			$html .= "<option value='yes'>{$ibforums->lang['yes']}</option><option value='no' selected>{$ibforums->lang['no']}";
 		}

 		$html .= "</select>";

 		$this->output .= $this->html->address_edit( array( 'SELECT' => $html, 'MEMBER' => $memb ) );


 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."&amp;act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>",
 								   "<a href='".$this->base_url."act=Msg&CODE=02'>".$ibforums->lang['t_book']."</a>"  );
 	}

	/*-------------------------------------------------------------------------*/
 	// DO_EDIT_MEMBER:
 	//
 	// Edit a member from address book.
 	/*-------------------------------------------------------------------------*/

 	function do_edit()
 	{
		global $ibforums, $DB, $std, $print;

 		if (!$ibforums->input['MID'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		if (! preg_match( "/^(\d+)$/", $ibforums->input['MID'] ) )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		$ibforums->input['allow_msg'] = $ibforums->input['allow_msg'] == 'yes' ? 1 : 0;

 		$DB->simple_construct( array( 'select' => '*', 'from' => 'contacts', 'where' => "member_id={$ibforums->member['id']} AND contact_id={$ibforums->input['MID']}" ) );
		$DB->simple_exec();

 		$memb = $DB->fetch_row();

 		if (!$memb['contact_id'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		$DB->do_update( 'contacts', array( 'contact_desc' => $ibforums->input['mem_desc'],
 										   'allow_msg'    => $ibforums->input['allow_msg'],
 										 ), 'id='.$memb['id'] );

 		$std->boink_it($this->base_url."act=Msg&CODE=02");
 	}

 	/*-------------------------------------------------------------------------*/
 	// CONTACT:
 	//
 	// Shows the address book.
 	/*-------------------------------------------------------------------------*/

 	function contact()
 	{
		global $ibforums, $DB, $std, $print;

 		$this->output .= $this->html->Address_header();

 		$DB->simple_construct( array( 'select' => '*',
 									  'from'   => 'contacts',
 									  'where'  => "member_id={$ibforums->member['id']}",
 									  'order'  =>  "contact_name ASC" ) );
		$DB->simple_exec();

 		if ( $DB->get_num_rows() )
 		{

 			$this->output .= $this->html->Address_table_header();
 			while ( $row = $DB->fetch_row() )
 			{
 				$row['text'] = $row['allow_msg']
 							 ? $ibforums->lang['can_contact']
 							 : $ibforums->lang['cannot_contact'];

 				$this->output .= $this->html->render_address_row($row);
 			}

 			$this->output .= $this->html->end_address_table();
 		}
 		else
 		{
 			$this->output .= $this->html->Address_none();

 		}

 		//-----------------------------------------
 		// Do we have a name to enter?
 		//-----------------------------------------

 		$name_to_enter = "";

 		if ($ibforums->input['MID'])
 		{
 			if ( preg_match( "/^(\d+)$/", $ibforums->input['MID'] ) )
 			{
 				$DB->simple_construct( array( 'select' => 'name,id', 'from' => 'members', 'where' => "id={$ibforums->input['MID']}" ) );
				$DB->simple_exec();

 				$memb = $DB->fetch_row();

 				if ($memb['id'])
 				{
 					$name_to_enter = $memb['name'];
 				}
 			}
 		}

 		$this->output .= $this->html->address_add($name_to_enter);

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 	}

 	/*-------------------------------------------------------------------------*/
 	// ADD MEMBER:
 	//
 	// Adds a member to the addy book.
 	/*-------------------------------------------------------------------------*/

 	function add_member()
 	{
		global $ibforums, $DB, $std, $print;

 		if (! $ibforums->input['mem_name'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		$DB->simple_construct( array( 'select' => 'name,id', 'from' => 'members', 'where' => "LOWER(name)='".$ibforums->input['mem_name']."'" ) );
		$DB->simple_exec();

 		$memb = $DB->fetch_row();

 		if (! $memb['id'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user' ) );
 		}

 		//-----------------------------------------
 		// Do we already have this member in our
 		// address book?
 		//-----------------------------------------

 		$DB->simple_construct( array( 'select' => '*', 'from' => 'contacts', 'where' => "member_id={$ibforums->member['id']} AND contact_id={$memb['id']}" ) );
		$DB->simple_exec();

 		if ( $DB->get_num_rows() )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'member_in_add_book' ) );
 		}

 		//-----------------------------------------
 		// Insert it into the DB
 		//-----------------------------------------

 		$ibforums->input['allow_msg'] = $ibforums->input['allow_msg'] == 'yes' ? 1 : 0;

 		$DB->do_insert( 'contacts', array(
										  'member_id'      => $ibforums->member['id'],
										  'contact_name'   => $memb['name'],
										  'allow_msg'      => $ibforums->input['allow_msg'],
										  'contact_desc'   => $ibforums->input['mem_desc'],
										  'contact_id'     => $memb['id']
								 )      );

		$std->boink_it($this->base_url."act=Msg&CODE=02");
	}

 	/*-------------------------------------------------------------------------*/
 	// Mutli Act:
 	//
 	// Removes or moves messages.
 	/*-------------------------------------------------------------------------*/

 	function multi_act()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Get the ID's to delete
 		//-----------------------------------------

 		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^msgid_(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$ids[] = $match[1];
 				}
 			}
 		}

 		$affected_ids = count($ids);

 		if ( $affected_ids > 0 )
 		{
 			$id_string = implode( ",", $ids );

 			if ($ibforums->input['delete'])
 			{
 				$this->lib->delete_messages( $ids, $ibforums->member['id'] );

 				if ($ibforums->input['saved'])
 				{
 					//-----------------------------------------
 					// Did we delete from the saved folder? If so, don't update the msg stats and
 					// redirect back to the saved folder.
 					//-----------------------------------------

 					$std->boink_it($this->base_url."act=Msg&CODE=20");
 				}
 				else
 				{
 					$this->lib->rebuild_dir_count( $ibforums->member['id'],
												   $ibforums->member['vdirs'],
												   $this->vid,
												   $ibforums->member['dir_data'][ $this->vid ]['count'] - $affected_ids,
												   'save',
												   "msg_total=msg_total-$affected_ids"
												 );

					$std->boink_it($this->base_url."act=Msg&CODE=01&VID={$this->vid}");

 				}

 			}
 			else if ($ibforums->input['move'])
 			{
 				$DB->simple_construct( array( 'update' => 'message_topics', 'set' => "mt_vid_folder='{$this->vid}', mt_to_id={$ibforums->member['id']}", 'where' => "mt_vid_folder != '{$this->vid}' AND mt_owner_id=".$ibforums->member['id']." AND mt_id IN ($id_string)" ) );
 				$DB->simple_exec();

				if ( $DB->get_affected_rows() )
				{
					$this->lib->rebuild_dir_count( $ibforums->member['id'],
												   $this->lib->rebuild_dir_count( $ibforums->member['id'],
																				  $ibforums->member['vdirs'],
																				  $ibforums->input['curvid'],
																				  $ibforums->member['dir_data'][ $ibforums->input['curvid'] ]['count'] - $affected_ids,
																				  'nosave'
																				),
												   $this->vid,
												   $ibforums->member['dir_data'][ $this->vid ]['count'] + $affected_ids,
												   'save'
												 );
				}

 				$std->boink_it($this->base_url."act=Msg&CODE=01&VID={$this->vid}");

 			}
 			else
 			{
 				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_msg_chosen' ) );
 			}
 		}
 		else
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_msg_chosen' ) );
 		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// END TRACKING
 	//
 	// Removes read tracked messages
 	/*-------------------------------------------------------------------------*/

 	function end_tracking()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Get the ID's to delete
 		//-----------------------------------------

 		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^msgid_(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$ids[] = $match[1];
 				}
 			}
 		}

 		$affected_ids = count($ids);

 		if ( $affected_ids > 0 )
 		{
 			$id_string = implode( ",", $ids );

 			$DB->simple_construct( array( 'update' => 'message_topics', 'set' => 'mt_tracking=0', 'where' => "mt_tracking=1 AND mt_read=1 AND mt_from_id={$ibforums->member['id']} AND mt_id IN ($id_string)" ) );
 			$DB->simple_exec();

 			$std->boink_it($this->base_url."act=Msg&CODE=30");
 		}
 		else
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_msg_chosen' ) );
 		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// Delete tracked messages
 	/*-------------------------------------------------------------------------*/

 	function del_tracked()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Get the ID's to delete
 		//-----------------------------------------

 		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^msgid_(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$ids[] = $match[1];
 				}
 			}
 		}

 		$affected_ids = count($ids);

 		if ( $affected_ids > 0 )
 		{
 			$id_string = implode( ",", $ids );

 			$this->lib->delete_messages( $ids, $ibforums->member['id'], "mt_read=0 and mt_tracking=1 AND mt_from_id=".$ibforums->member['id'] );

 			$std->boink_it($this->base_url."act=Msg&CODE=30");
 		}
 		else
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_msg_chosen' ) );
 		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// DELETE MESSAGE:
 	/*-------------------------------------------------------------------------*/

 	function delete()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// check for a msg ID
 		//-----------------------------------------

 		$ibforums->input['MSID'] = intval($ibforums->input['MSID']);

 		if ( ! $ibforums->input['MSID'] )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_msg_chosen' ) );
 		}

 		//-----------------------------------------
 		// Delete it from the DB
 		//-----------------------------------------

 		$this->lib->delete_messages( $ibforums->input['MSID'], $ibforums->member['id'] );

 		$this->lib->rebuild_dir_count( $ibforums->member['id'],
 									   $ibforums->member['vdirs'],
 									   $this->vid,
 									   $ibforums->member['dir_data'][ $this->vid ]['count'] - 1,
 									   'save',
 									   "msg_total=msg_total-1"
 									 );

 		$std->boink_it($this->base_url."act=Msg&CODE=01&VID={$this->vid}");
 	}

 	/*-------------------------------------------------------------------------*/
 	// VIEW MESSAGE:
 	//
 	// Views a message, thats it. No, it doesn't do anything else
 	// I don't know why. It just does. Accept it and move on dude.
 	/*-------------------------------------------------------------------------*/

 	function view_msg()
 	{
		global $ibforums, $DB, $std, $print, $skin_universal;

 		//-----------------------------------------
 		// Get extra LIBBIES
 		//-----------------------------------------

 		require_once( ROOT_PATH.'sources/topics.php' );

 		$this->topiclib = new topics();
 		$this->topiclib->topic_init();

 		//-----------------------------------------
 		// check for a msg ID
 		//-----------------------------------------

 		$ibforums->input['MSID'] = intval($ibforums->input['MSID']);

 		if (! $ibforums->input['MSID'] )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_msg' ) );
 		}

 		$DB->cache_add_query( 'msg_get_msg_to_show', array( 'msgid' => $ibforums->input['MSID'], 'mid' => $ibforums->member['id'] ) );
 		$DB->simple_exec();

 		if ( ! $msg = $DB->fetch_row() )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_msg' ) );
 		}

 		//-----------------------------------------
 		// Did we read this in the pop up?
 		// If so, reduce new count by 1 (this msg)
 		// 'cos if we went via inbox, we'd have
 		// no new msg
 		//-----------------------------------------

 		if ($ibforums->member['new_msg'] >= 1)
 		{
 			$DB->simple_construct( array( 'update' => 'members', 'set' => "new_msg=new_msg-1", 'where' => "id=".$ibforums->member['id'] ) );
 			$DB->simple_exec();
 		}

		//-----------------------------------------
 		// Is this an unread message?
 		//-----------------------------------------

 		if ($msg['mt_read'] < 1)
 		{
 			$DB->simple_construct( array( 'update' => 'message_topics', 'set' => "mt_read=1, mt_user_read=".time(), 'where' => "mt_id=".$ibforums->input['MSID'] ) );
 			$DB->simple_exec();
 		}

 		//-----------------------------------------
		// Remove potential [attachmentid= tag in title
		//-----------------------------------------

		$msg['mt_title'] = str_replace( '[attachmentid=', '&#91;attachmentid=', $msg['mt_title'] );

 		$msg['msg_date'] = $std->get_date( $msg['msg_date'], 'LONG' );


 		$member = $this->topiclib->parse_member( $msg );

 		$msg['msg_post'] = $this->topiclib->parser->convert( array( 'TEXT'    => $msg['msg_post'],
																	'SMILIES' => 1,
																	'CODE'    => $ibforums->vars['msg_allow_code'],
																	'HTML'    => $ibforums->vars['msg_allow_html']
														   )      );

		$this->topiclib->parser->pp_do_html  = intval($ibforums->vars['msg_allow_html']);
		$this->topiclib->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
		$this->topiclib->parser->pp_nl2br    = 1;

		$msg['msg_post'] = $this->topiclib->parser->post_db_parse($msg['msg_post']);

		if ( $ibforums->member['view_sigs'] and $member['signature'] )
		{
			$member['signature'] = $this->topiclib->parser->convert( array( 'TEXT'    => $member['signature'],
																			'SMILIES' => 0,
																			'CODE'    => $ibforums->vars['sig_allow_ibc'],
																			'HTML'    => $ibforums->vars['sig_allow_html'],
																			'SIGNATURE'=> 1,
																   )      );

			$this->topiclib->parser->pp_do_html  = intval($ibforums->vars['sig_allow_html']);
			$this->topiclib->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
			$this->topiclib->parser->pp_nl2br    = 1;

			$member['signature'] = $this->topiclib->parser->post_db_parse($member['signature']);
			$member['signature'] = $ibforums->skin_global->signature_separator($member['signature']);
		}
		else
		{
			$member['signature'] = "";
		}

		$member['VID'] = $ibforums->member['current_id'];

		//-----------------------------------------
		// To , CC, etc?
		//-----------------------------------------

		if ( ! $msg['mt_hide_cc'] )
		{
			$cc_users = $this->lib->format_cc_string( $msg['msg_cc_users'], $ibforums->member['id'] );

			if ( $cc_users )
			{
				$msg['show_cc_users'] = $this->html->render_msg_show_cc( $cc_users );
			}
		}

		$html = $this->html->Render_msg( $msg, $member, $this->jump_html );

		//-----------------------------------------
		// Attachments?
		//-----------------------------------------

		if ( $msg['mt_hasattach'] )
		{
			$html = $this->topiclib->parse_attachments( $html, array( $msg['msg_id'] ), 'attach_msg', 'msg', 'msg' );
		}

		$this->output .= $html;

		$this->page_title = $ibforums->lang['t_welcome'];

		$this->nav        = array( "<a href='".$this->base_url."&amp;act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>",
								   "<a href='".$this->base_url."act=Msg&CODE=01&VID={$member['VID']}'>".$ibforums->member['current_dir']."</a>",
								   $msg['mt_title']
								 );
 	}

 	/*-------------------------------------------------------------------------*/
 	// SEND MESSAGE:
 	//
 	// Sends a message. Yes, it's that simple. Why so much code?
 	// Because typing "send a message to member X" doesnt actually
 	// do anything.
 	/*-------------------------------------------------------------------------*/

 	function send()
 	{
		global $ibforums, $DB, $std, $forums;

 		//-----------------------------------------
 		// Set up and stuff
 		//-----------------------------------------

 		$this->post_key = $ibforums->input['post_key'] ? $ibforums->input['post_key'] : md5(microtime());

 		$this->lib->init();
 		$this->lib->register_class( &$this );

 		$show_form = 0;

 		//-----------------------------------------
		// Did we remove an attachment?
		//-----------------------------------------

		if ( $ibforums->input['removeattachid'] )
		{
			if ( $ibforums->input[ 'removeattach_'. $ibforums->input['removeattachid'] ] )
			{
				$this->lib->postlib->pf_remove_attachment( intval($ibforums->input['removeattachid']), $this->post_key );
				$this->show_form = 1;
			}
		}

		//-----------------------------------------
		// Did we add an attachment?
		//-----------------------------------------

		if ( $ibforums->input['attachgo'] )
		{
			$this->upload_id = $this->lib->postlib->process_upload();
			$this->show_form = 1;
		}

		//-----------------------------------------
		// Did we preview?
		//-----------------------------------------

		if ($ibforums->input['preview'] != "")
 		{
 			$this->show_form = 1;
 		}

 		//-----------------------------------------
 		// Show form or...
 		//-----------------------------------------

 		if ( $ibforums->input['MODE'] and $this->show_form != 1 )
 		{
 			$this->send_msg();
 		}
 		else
 		{
 			$this->lib->send_form($ibforums->input['preview']);

 			$this->output .= $this->lib->output;
 		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// SEND MESSAGE
 	/*-------------------------------------------------------------------------*/

 	function send_msg()
 	{
		global $ibforums, $DB, $std, $print;

 		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_error'   , $ibforums->lang_id);

 		$ibforums->input['from_contact'] = $ibforums->input['from_contact'] ? $ibforums->input['from_contact'] : '-';

 		//-----------------------------------------
 		// Error checking
 		//-----------------------------------------

 		if ( strlen($ibforums->input['msg_title']) < 2 )
 		{
 			$this->lib->send_form( 0, $ibforums->lang['err_no_title'] );
 			$this->output .= $this->lib->output;
 			return;
 		}

 		if ( strlen($_POST['Post']) < 2 )
 		{
 			$this->lib->send_form( 0, $ibforums->lang['err_no_msg'] );
 			$this->output .= $this->lib->output;
 			return;
 		}

 		if ($ibforums->input['from_contact'] == '-' and $ibforums->input['entered_name'] == "")
 		{
 			$this->lib->send_form( 0, $ibforums->lang['err_no_chosen_member'] );
 			$this->output .= $this->lib->output;
 			return;
 		}

 		//-----------------------------------------
 		// TO:
 		//-----------------------------------------

 		if ($ibforums->input['from_contact'] == '-')
 		{
 			$this->lib->to = $ibforums->input['entered_name'];
 		}
 		else
 		{
 			$this->lib->to_by_id = $ibforums->input['from_contact'];
 		}

 		//-----------------------------------------
 		// SEND
 		//-----------------------------------------

 		$this->upload_id        = $this->lib->postlib->process_upload();
 		$this->lib->cc_users    = $std->clean_value( $std->txt_UNhtmlspecialchars($_POST['carbon_copy']) );
 		$this->lib->from_member = $ibforums->member;
 		$this->lib->msg_title   = $ibforums->input['msg_title'];
 		$this->lib->msg_post    = $ibforums->input['Post'];

 		$this->lib->send_pm( array( 'save_only' => $ibforums->input['save'],
							        'orig_id'   => intval($ibforums->input['OID']),
							        'preview'   => $ibforums->input['preview'],
							        'track'     => $ibforums->input['add_tracking'],
							        'add_sent'  => $ibforums->input['add_sent'],
							        'hide_cc'   => $ibforums->input['mt_hide_cc']
						   )     );

 		if ( $this->lib->error != "" )
 		{
 			$this->lib->send_form( 0,$this->lib->error );
 			$this->output .= $this->lib->output;
 			return;
 		}

		if ( $this->lib->redirect_url )
		{
			$print->redirect_screen( $this->lib->redirect_lang, $this->lib->redirect_url );
		}

		//-----------------------------------------
		// Swap and serve...
		//-----------------------------------------

		$text = preg_replace( "/<#FROM_MEMBER#>/"   , $ibforums->member['name'] , $ibforums->lang['sent_text'] );
		$text = preg_replace( "/<#MESSAGE_TITLE#>/" , $ibforums->input['msg_title'], $text );

		$print->redirect_screen( $text , "&act=Msg&CODE=01" );
 	}

 	/*-------------------------------------------------------------------------*/
 	// MSG LIST:
 	//
 	// Views the inbox / folder of choice
 	/*-------------------------------------------------------------------------*/

 	function msg_list()
 	{
		global $ibforums, $DB, $std, $print;

 		$sort_key = "";

 		switch ($ibforums->input['sort'])
 		{
 			case 'rdate':
 				$sort_key = 'mt_date ASC';
 				break;
 			case 'title':
 				$sort_key = 'mt_title ASC';
 				break;
 			case 'name':
 				$sort_key = 'mt_from_id ASC';
 				break;
 			default:
 				$sort_key = 'mt_date DESC';
 				break;
 		}

 		//-----------------------------------------
 		// Get the number of messages we have in total.
 		//-----------------------------------------

 		$DB->simple_construct( array ( 'select' => 'COUNT(*) as msg_total', 'from' => 'message_topics', 'where' => "mt_owner_id=".$ibforums->member['id']." AND mt_vid_folder != 'unsent'" ) );
 		$DB->simple_exec();

 		$total = $DB->fetch_row();

 		$total['msg_total'] = intval($total['msg_total']);

 		if ( $total['msg_total'] != $ibforums->member['msg_total'] )
 		{
 			$DB->simple_construct( array ( 'update'=> 'members', 'set' => "msg_total=".$total['msg_total'], 'where' => "id=".$ibforums->member['id'] ) ) ;
 			$DB->simple_exec();
 		}

 		//-----------------------------------------
 		// Get the number of messages in our curr folder.
 		//-----------------------------------------

 		$DB->simple_construct( array ( 'select' => 'COUNT(*) as msg_total', 'from' => 'message_topics', 'where' => "mt_owner_id=".$ibforums->member['id']." AND mt_vid_folder='{$this->vid}'" ) );
 		$DB->simple_exec();

 		$total_current = $DB->fetch_row();

 		$total_current['msg_total'] = intval($total_current['msg_total']);

 		if ( $total_current['msg_total'] != $ibforums->member['dir_data'][ $this->vid ]['count'] )
 		{
 			$this->lib->rebuild_dir_count( $ibforums->member['id'], $ibforums->member['vdirs'], $this->vid, $total_current['msg_total'] );
 		}

 		//-----------------------------------------
 		// Make sure we've not exceeded our alloted allowance.
 		//-----------------------------------------

 		$info['full_messenger'] = "<br />";
 		$info['full_text']      = "";
 		$info['total_messages'] = $total['msg_total'];
 		$info['img_width']      = 1;
 		$info['vid']            = $this->vid;
 		$info['date_order']     = $sort_key == 'm.msg_date DESC' ? 'rdate' : 'msg_date';

 		$amount_info            = sprintf( $ibforums->lang['pmpc_info_string'], $total['msg_total'] ,$ibforums->lang['pmpc_unlimited'] );

 		if ($ibforums->member['g_max_messages'] > 0)
 		{
 			$amount_info          = sprintf( $ibforums->lang['pmpc_info_string'], $total['msg_total'] ,$ibforums->member['g_max_messages'] );

 			$info['full_percent'] = $total['msg_total'] ? sprintf( "%.0f", ( ($total['msg_total'] / $ibforums->member['g_max_messages']) * 100) ) : 0;
 			$info['img_width']    = $info['full_percent'] > 0 ? intval($info['full_percent']) * 2.4 : 1;

 			if ($info['img_width'] > 300)
 			{
 				$info['img_width'] = 300;
 			}

 			if ($total['msg_total'] >=$ibforums->member['g_max_messages'])
 			{
 				$info['full_messenger'] = "<span class='highlight'>".$ibforums->lang['folders_full']."</span>";
 			}
 			else
 			{
 				$info['full_messenger'] = str_replace( "<#PERCENT#>", $info['full_percent'], $ibforums->lang['pmpc_full_string'] );
 			}
 		}

 		//-----------------------------------------
 		// Generate Pagination
 		//-----------------------------------------

 		$start = intval($ibforums->input['st']) > 0 ? intval($ibforums->input['st']) : 0;
 		$p_end = $ibforums->vars['show_max_msg_list'] > 0 ? $ibforums->vars['show_max_msg_list'] : 50;


 		$pages = $std->build_pagelinks( array( 'TOTAL_POSS'  => $total_current['msg_total'],
											   'PER_PAGE'    => $p_end,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "",
											   'L_MULTI'     => $ibforums->lang['msg_pages'],
											   'BASE_URL'    => $ibforums->base_url."act=Msg&amp;CODE=1&amp;VID=".$this->vid."&amp;sort=".$ibforums->input['sort'],
									  )      );

 		//-----------------------------------------
 		// Print the header
 		//-----------------------------------------

 		if ($this->vid == 'sent')
 		{
 			$ibforums->lang['message_from'] = $ibforums->lang['message_to'];

 			$DB->cache_add_query( 'msg_get_sent_list', array( 'mid' => $ibforums->member['id'], 'vid' => $this->vid, 'sort' => $sort_key, 'limita' => $start, 'limitb' => $p_end ) );
 			$DB->simple_exec();
  		}
 		else
 		{
 			$DB->cache_add_query( 'msg_get_folder_list', array( 'mid' => $ibforums->member['id'], 'vid' => $this->vid, 'sort' => $sort_key, 'limita' => $start, 'limitb' => $p_end ) );
 			$DB->simple_exec();
  		}

 		$this->output .= $this->html->inbox_table_header( $ibforums->member['current_dir'], $info, $this->jump_html, $pages, $this->vid );

 		//-----------------------------------------
 		// Get the messages
 		//-----------------------------------------

 		if ( $DB->get_num_rows() )
 		{
 			while( $row = $DB->fetch_row() )
 			{
 				if ( $row['mt_hasattach'] )
				{
					$row['attach_img'] = '<{ATTACH_ICON}>';
				}

 				if ($this->vid == 'sent')
 				{
 					$row['icon'] = "<{M_READ}>";
 				}
 				else
 				{
 					$row['icon'] = $row['mt_read'] == 1 ? "<{M_READ}>" : "<{M_UNREAD}>";
 				}

 				$row['date'] = $std->get_date( $row['mt_date'] , 'LONG' );

 				if ($this->vid != 'sent')
 				{
 					$row['add_to_contacts'] = "[ <a href='{$ibforums->base_url}act=Msg&amp;CODE=02&amp;MID={$row['from_id']}'>{$ibforums->lang[add_to_book]}</a> ]";
 				}
 				else
 				{
 					//$row['from_id'] = $row['mt_from_id'];
 				}

 				$this->output .= $this->html->inbox_row( $row );
 			}
 		}
 		else
 		{
 			$this->output .= $this->html->No_msg_inbox();
 		}


 		$this->output .= $this->html->end_inbox($this->jump_html, $amount_info, $pages);

 		//-----------------------------------------
 		// Update the message stats if we have to
 		//-----------------------------------------

 		if ($ibforums->member['current_id'] == 'in' and $ibforums->member['new_msg'] > 0 )
 		{
 			$DB->simple_construct( array( 'update' => 'members', 'set' => 'new_msg=0', 'where' => 'id='.$ibforums->member['id'] ) );
 			$DB->simple_exec();
 		}

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 	}

	/*-------------------------------------------------------------------------*/
 	// VIEW SAVED:
 	//
 	// View the saved folder stuff.
 	/*-------------------------------------------------------------------------*/

 	function view_saved()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Print the header
 		//-----------------------------------------

 		$this->output .= $this->html->unsent_table_header();

 		$DB->cache_add_query( 'msg_get_sent_list', array( 'mid' => $ibforums->member['id'], 'vid' => 'unsent', 'sort' => 'mt_date DESC', 'limita' => 0, 'limitb' => 5000 ) );
 		$DB->simple_exec();

 		//-----------------------------------------
 		// Get the messages
 		//-----------------------------------------

 		if ( $DB->get_num_rows() )
 		{
 			while( $row = $DB->fetch_row() )
 			{
 				if ( $row['mt_hasattach'] )
				{
					$row['attach_img'] = '<{ATTACH_ICON}>';
				}

 				$row['icon']     = "<{M_READ}>";
 				$row['date']     = $std->get_date( $row['mt_date'] , 'LONG' );
 				$row['cc_users'] = $row['cc_users'] == "" ? $ibforums->lang['no'] : $ibforums->lang['yes'];

 				$d_array = array( 'msg' => $row, 'member' => $ibforums->member );

 				$this->output .= $this->html->unsent_row( $d_array );
 			}
 		}
 		else
 		{
 			$this->output .= $this->html->No_msg_inbox();
 		}

 		$this->output .= $this->html->unsent_end();

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 	}

 	/*-------------------------------------------------------------------------*/
 	// SHOW TRACKED MESSAGE
 	/*-------------------------------------------------------------------------*/

 	function show_tracking()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Get all tracked and read messages
 		//-----------------------------------------

 		$DB->cache_add_query( 'msg_get_tracking', array( 'mid' => $ibforums->member['id'] ) );
		$DB->simple_exec();

		$read = array();
		$unread = array();

		while ( $r = $DB->fetch_row() )
		{
			if ( $r['mt_read'] )
			{
				$read[ $r['mt_user_read'].','.$r['mt_id'] ] = $r;
			}
			else
			{
				$unread[ $r['mt_user_read'].','.$r['mt_id'] ] = $r;
			}
		}

		krsort( $read );
		krsort( $unread );

		//-----------------------------------------
 		// READ MESSAGES
 		//-----------------------------------------

 		$this->output .= $this->html->trackread_table_header();

 		if ( count($read) )
 		{
 			foreach( $read as $id => $row )
 			{
 				$row['icon']     = "<{M_READ}>";
 				$row['date']     = $std->get_date( $row['mt_user_read'] , 'LONG' );
 				$this->output .= $this->html->trackread_row( $row );
 			}
 		}
 		else
 		{
 			$this->output .= $this->html->No_msg_inbox();
 		}

 		$this->output .= $this->html->trackread_end();

 		//-----------------------------------------
 		// UNREAD MESSAGES
 		//-----------------------------------------

 		$this->output .= $this->html->trackUNread_table_header();

 		if ( count($unread) )
 		{
 			foreach( $unread as $id => $row )
 			{
 				$row['icon']     = "<{M_UNREAD}>";
 				$row['date']     = $std->get_date( $row['mt_date'] , 'LONG' );
 				$this->output .= $this->html->trackUNread_row( $row );
 			}
 		}
 		else
 		{
 			$this->output .= $this->html->No_msg_inbox();
 		}

 		$this->output .= $this->html->trackUNread_end();

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$this->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 	}

}

?>