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
|   > UserCP functions
|   > Module written by Matt Mecham
|   > Date started: 14th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Thu 20 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class usercp
{
    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";

    var $member     = array();
    var $m_group    = array();

    var $jump_html  = "";
    var $parser     = "";

    var $links      = array();

    var $bio        = "";
    var $notes      = "";
    var $size       = "m";

    var $email      = "";
    var $md5_check  = "";

    var $modules    = "";

    var $lib;

    /*-------------------------------------------------------------------------*/
    // Run!
    /*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	//-----------------------------------------
    	// Load parser
    	//-----------------------------------------

    	require ROOT_PATH."sources/lib/usercp_functions.php";

    	require ROOT_PATH."sources/lib/post_parser.php";

        $this->parser = new post_parser();

        //-----------------------------------------
        // Prep form check
        //-----------------------------------------

        $this->md5_check = $std->return_md5_check();

        //-----------------------------------------
    	// Get the emailer module
		//-----------------------------------------

		require ROOT_PATH."sources/classes/class_email.php";

		$this->email = new emailer();

		//-----------------------------------------
    	// Get the sync module
		//-----------------------------------------

		if ( USE_MODULES == 1 )
		{
			require ROOT_PATH."modules/ipb_member_sync.php";

			$this->modules = new ipb_member_sync();
		}

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

    	$ibforums->lang = $std->load_words($ibforums->lang, 'lang_post'  , $ibforums->lang_id );
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_ucp'   , $ibforums->lang_id );

    	$this->html = $std->load_template('skin_ucp');

    	$ibforums->base_url_nosess = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}";

    	//-----------------------------------------
    	// Check viewing permissions, etc
		//-----------------------------------------

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

		$this->links = $ibforums->member['links'];
		$this->notes = $ibforums->member['notes'];
		$this->size  = $ibforums->member['ta_size'] ? $ibforums->member['ta_size'] : $this->size;

		//-----------------------------------------
    	// Print the top button menu
    	//-----------------------------------------

    	$menu_html = $this->html->Menu_bar($ibforums->base_url);

    	//-----------------------------------------
    	// If no messenger, remove the links!
    	//-----------------------------------------

    	if ( $ibforums->member['g_use_pm'])
        {
        	//-----------------------------------------
    		// Print folder links
    		//-----------------------------------------

    		$folder_links = "";

			if (empty($ibforums->member['vdirs']))
			{
				$ibforums->member['vdirs'] = "in:Inbox|sent:Sent Items";
			}

			foreach( explode( "|", $ibforums->member['vdirs'] ) as $dir )
			{
				list ($id  , $data)  = explode( ":", $dir );
				list ($real, $count) = explode( ";", $data );

				if ( ! $id )
				{
					continue;
				}

				if ( $count )
				{
					$real .= " ({$count})";
				}

				$folder_links .= $this->html->menu_bar_msg_folder_link($id, $real);
			}

			if ( $folder_links != "" )
			{
				$menu_html = str_replace( "<!--IBF.FOLDER_LINKS-->", $folder_links, $menu_html );
			}
        }

		//-----------------------------------------
    	// Using Sub Manager?
    	//-----------------------------------------

		if ( @is_dir( ROOT_PATH.'/modules/subsmanager' ) )
		{
			$url  = $ibforums->base_url."act=module&amp;module=subscription&amp;CODE=index";
			$name = $ibforums->lang['new_sub_link'];

			$menu_html = str_replace( "<!--IBF.OPTION_LINKS-->", $this->html->menu_bar_new_link( $url, $name ), $menu_html );
		}

    	$print->add_output( $menu_html );

    	$this->lib    = new usercp_functions(&$this);

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['CODE'])
    	{
    		case '00':
    			$this->splash();
    			break;
    		case '01':
    			$this->personal();
    			break;
    		//-----------------------------------------
    		case '02':
    			$this->email_settings();
    			break;
    		case '03':
    			$this->lib->do_email_settings();
    			break;
    		//-----------------------------------------
    		case '04':
    			$this->board_prefs();
    			break;
    		case '05':
    			$this->lib->do_board_prefs();
    			break;
    		//-----------------------------------------
    		case '08':
    			$this->email_change();
    			break;
    		case '09':
    			$this->do_email_change();
    			break;
    		//-----------------------------------------
    		case '21':
    			$this->lib->do_profile();
    			break;
    		case '20':
    			$this->update_notepad();
    			break;
    		//-----------------------------------------
    		case '22':
    			$this->signature();
    			break;
    		case '23':
    			$this->lib->do_signature();
    			break;
    		//-----------------------------------------
    		case '24':
    			$this->avatar();
    			break;
    		case '25':
    			$this->lib->do_avatar();
    			break;
    		//-----------------------------------------
    		case '26':
    			$this->tracker();
    			break;
    		case '27':
    			$this->do_update_tracker();
    			break;
    		//-----------------------------------------
    		case '28':
    			$this->pass_change();
    			break;
    		case '29':
    			$this->do_pass_change();
    			break;
    		//-----------------------------------------
    		case '50':
    			$this->forum_tracker();
    			break;
    		case '51':
    			$this->remove_forum_tracker();
    			break;
    		//-----------------------------------------
    		case 'ignore':
    			$this->ignore_user_splash();
    			break;
    		case 'ignoreadd':
    			$this->lib->ignore_user_add();
    			break;
    		case 'ignoreremove':
    			$this->lib->ignore_user_remove();
    			break;
    		//-----------------------------------------
    		case 'show_image':
    			$this->show_image();
    			break;
    		case 'photo':
    			$this->photo();
    			break;
    		case 'dophoto':
    			$this->lib->do_photo();
    			break;
    		case 'getgallery':
    			$this->avatar_gallery();
    			break;
    		case 'setinternalavatar':
    			$this->lib->set_internal_avatar();
    			break;
    		case 'attach':
    			$this->attachments();
    			break;
    		//-----------------------------------------
    		// Mod tools
    		//-----------------------------------------
    		case 'iptool':
    			$this->mod_ip_tool_start();
    			break;
    		case 'doiptool':
    			$this->mod_ip_tool_complete();
    			break;
    		case 'memtool':
    			$this->mod_find_user_start();
    			break;
    		case 'domemtool':
    			$this->mod_find_user_complete();
    			break;
    		case 'announce_start':
    			$this->mod_announce_start();
    			break;
    		case 'announce_add':
    			$this->mod_announce_form('add');
    			break;
    		case 'announce_save':
    			$this->mod_announce_save();
    			break;
    		case 'announce_edit':
    			$this->mod_announce_form('edit');
    			break;
    		case 'announce_delete':
    			$this->mod_announce_delete();
    			break;
    		//-----------------------------------------
    		// Subs
    		//-----------------------------------------
    		case 'start_subs':
    			$this->lib->subs_choose();
    			break;
    		case 'end_subs':
    			$this->lib->subs_choose('save');
    			break;
    		default:
    			$this->splash();
    			break;
    	}

    	//-----------------------------------------
    	// If we have any HTML to print, do so...
    	//-----------------------------------------

    	$fj = $std->build_forum_jump();
		$fj = preg_replace( "!#Forum Jump#!", $ibforums->lang['forum_jump'], $fj);

		$this->output .= $this->html->CP_end();

		$this->output .= $this->html->forum_jump($fj, $links);

    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 1, NAV => $this->nav ) );
 	}

 	/*-------------------------------------------------------------------------*/
 	// ANNOUNCEMENTS: DELETE
 	/*-------------------------------------------------------------------------*/

 	function mod_announce_delete()
	{
		global $std, $ibforums, $DB, $forums;

		if ( ! $ibforums->member['g_is_supmod'] )
		{
			$this->output .= $this->html->dead_section();
 			return;
		}

		$id = intval( $ibforums->input['id'] );

		if ( $id )
		{
			$DB->simple_exec_query( array( 'delete' => 'announcements', 'where' => 'announce_id='.$ibforums->input['id'] ) );
		}

		//-----------------------------------------
		// Update cache
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/announcements.php' );
		$announcements = new announcements();
		$announcements->announce_recache();

		$this->mod_announce_start();
	}

 	/*-------------------------------------------------------------------------*/
 	// ANNOUNCEMENTS: SAVE (new/edit)
 	/*-------------------------------------------------------------------------*/

 	function mod_announce_save()
	{
		global $std, $ibforums, $DB, $forums;

		$type = $ibforums->input['type'];
		$forums_to_save = "";

		if ( ! $ibforums->member['g_is_supmod'] )
		{
			$this->output .= $this->html->dead_section();
 			return;
		}

		//-----------------------------------------
		// check...
		//-----------------------------------------

		if ( ! $ibforums->input['announce_title'] or ! $ibforums->input['announce_post'] )
		{
			$this->mod_announce_form( $type, $ibforums->lang['announce_error_title'] );
			return;
		}

		//-----------------------------------------
		// Get forums to add announce in
		//-----------------------------------------

		if ( is_array( $_POST['announce_forum'] ) and count( $_POST['announce_forum'] ) )
		{
			if ( in_array( '*', $_POST['announce_forum'] ) )
			{
				$forums_to_save = '*';
			}
			else
			{
				$forums_to_save = implode( ",", $_POST['announce_forum'] );
			}
		}

		if ( ! $forums_to_save )
		{
			$this->mod_announce_form( $type, $ibforums->lang['announce_error_forums'] );
			return;
		}

		//-----------------------------------------
		// check dates
		//-----------------------------------------

		$start_date = 0;
		$end_date   = 0;

		if ( strstr( $ibforums->input['announce_start'], '-' ) )
		{
			$start_array = explode( '-', $ibforums->input['announce_start'] );

			if ( $start_array[0] and $start_array[1] and $start_array[2] )
			{
				if ( ! checkdate( $start_array[0], $start_array[1], $start_array[2] ) )
				{
					$this->mod_announce_form( $type, $ibforums->lang['announce_error_date'] );
					return;
				}
			}

			$start_date = $std->date_gmmktime( 0, 0, 1, $start_array[0], $start_array[1], $start_array[2] );
		}

		if ( strstr( $ibforums->input['announce_end'], '-' ) )
		{
			$end_array = explode( '-', $ibforums->input['announce_end']  );

			if ( $end_array[0] and $end_array[1] and $end_array[2] )
			{
				if ( ! checkdate( $end_array[0], $end_array[1], $end_array[2] ) )
				{
					$this->mod_announce_form( $type, $ibforums->lang['announce_error_date'] );
					return;
				}
			}

			$end_date = $std->date_gmmktime( 23, 59, 59, $end_array[0], $end_array[1], $end_array[2] );
		}

		//-----------------------------------------
		// Build save array
		//-----------------------------------------

		$save_array = array( 'announce_title'        => $ibforums->input['announce_title'],
							 'announce_post'         => $ibforums->input['announce_post'],
							 'announce_active'       => $ibforums->input['announce_active'],
							 'announce_forum'        => $forums_to_save,
							 'announce_html_enabled' => $ibforums->input['announce_html_enabled'],
							 'announce_start'        => $start_date,
							 'announce_end'          => $end_date
						   );

		//-----------------------------------------
		// Save..
		//-----------------------------------------

		if ( $type == 'add' )
		{
			$save_array['announce_member_id'] = $ibforums->member['id'];

			$DB->do_insert( 'announcements', $save_array );
		}
		else
		{
			if ( $ibforums->input['id'] )
			{
				$DB->do_update( 'announcements', $save_array, 'announce_id='.intval($ibforums->input['id']) );
			}
		}

		//-----------------------------------------
		// Update cache
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/announcements.php' );
		$announcements = new announcements();
		$announcements->announce_recache();

		$this->mod_announce_start();
		return;
	}

 	/*-------------------------------------------------------------------------*/
 	// ANNOUNCEMENTS: FORM (new/edit)
 	/*-------------------------------------------------------------------------*/

 	function mod_announce_form($type='add', $msg="")
	{
		global $std, $ibforums, $DB, $forums;

		if ( ! $ibforums->member['g_is_supmod'] )
		{
			$this->output .= $this->html->dead_section();
 			return;
		}

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		if ( $type == 'add' )
		{
			$button   = $ibforums->lang['announce_button_add'];
			$announce = array( 'announce_active' => 1 );

		}
		else
		{
			$ibforums->input['id'] = intval($ibforums->input['id']);
			$button                = $ibforums->lang['announce_button_edit'];
			$announce              = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'announcements', 'where' => 'announce_id='.$ibforums->input['id'] ) );
			$announce['announce_forum'] = explode( ",", $announce['announce_forum'] );
			$announce['announce_start'] = $announce['announce_start'] ? gmdate( 'm-d-Y', $announce['announce_start'] ) : '';
			$announce['announce_end']   = $announce['announce_end']   ? gmdate( 'm-d-Y', $announce['announce_end'] ) : '';
		}

		//-----------------------------------------
		// Do we have _POST?
		//-----------------------------------------

		foreach( array( 'announce_title', 'announce_post', 'announce_start', 'announce_end', 'announce_forum', 'announce_active' ) as $bit )
		{
			if ( $_POST[$bit] )
			{
				$announce[$bit] = $_POST[$bit];
			}
		}

		//-----------------------------------------
		// Forums
		//-----------------------------------------

		$forum_html = "<option value='*'>{$ibforums->lang['announce_form_allforums']}</option>" . $forums->forums_forum_jump();

		//-----------------------------------------
		// Save forums?
		//-----------------------------------------

		if ( is_array( $announce['announce_forum'] ) and count( $announce['announce_forum'] ) )
		{
			foreach( $announce['announce_forum'] as $f )
			{
				$forum_html = preg_replace( "#option\s+value=[\"'](".preg_quote($f,'#').")[\"']#i", "option value='\\1' selected='selected'", $forum_html );
			}
		}

		if ( $announce['announce_active'] )
		{
			$announce['announce_active_checked'] = 'checked="checked"';
		}

		if ( $announce['announce_html_enabled'] )
		{
			$announce['announce_html_enabled'] = 'checked="checked"';
		}

		$announce['announce_post'] = $std->my_br2nl( $announce['announce_post'] );

		$this->output .= $this->html->ucp_announce_form($announce, $button, $forum_html, $type, $msg);

		$this->page_title = $ibforums->lang['menu_announcements'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 		$this->nav[]      = $ibforums->lang['menu_announcements'];
	}

 	/*-------------------------------------------------------------------------*/
 	// ANNOUNCEMENTS: START (Show current)
 	/*-------------------------------------------------------------------------*/

 	function mod_announce_start()
	{
		global $std, $ibforums, $DB, $forums;

		if ( ! $ibforums->member['g_is_supmod'] )
		{
			$this->output .= $this->html->dead_section();
 			return;
		}

		//-----------------------------------------
		// Get announcements
		//-----------------------------------------

		$DB->cache_add_query( 'ucp_get_all_announcements', array() );
		$DB->cache_exec_query();

		$content = "";

		while ( $r = $DB->fetch_row() )
		{
			if ( $r['announce_start'] )
			{
				$r['announce_starts_converted'] = gmdate( 'M-d-Y', $r['announce_start'] );
			}
			else
			{
				$r['announce_starts_converted'] = '-';
			}

			if ( $r['announce_end'] )
			{
				$r['announce_end_converted'] = gmdate( 'M-d-Y', $r['announce_end'] );
			}
			else
			{
				$r['announce_end_converted'] = '-';
			}

			if ( $r['announce_forum'] == '*' )
			{
				$r['announce_forum_show'] = $ibforums->lang['announce_page_allforums'];
			}
			else
			{
				$tmp_forums = explode(",",$r['announce_forum']);

				if ( is_array( $tmp_forums ) and count($tmp_forums) )
				{
					if ( count($tmp_forums) > 5 )
					{
						$r['announce_forum_show'] = count($tmp_forums).' '.$ibforums->lang['announce_page_numforums'];
					}
					else
					{
						$tmp2 = array();

						foreach( $tmp_forums as $id )
						{
							$tmp2[] = "<a href='{$ibforums->base_url}showforum={$id}'>{$forums->forum_by_id[ $id ]['name']}</a>";
						}

						$r['announce_forum_show'] = implode( "<br />", $tmp2 );
					}
				}
			}

			if ( ! $r['announce_active'] )
			{
				$r['announce_inactive'] = "<span class='desc'>{$ibforums->lang['announce_page_disabled']}</span>";
			}

			$content .= $this->html->ucp_announce_manage_row( $r );
		}

		$this->output .= $this->html->ucp_announce_manage($content);

		$this->page_title = $ibforums->lang['menu_announcements'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 		$this->nav[]      = $ibforums->lang['menu_announcements'];
	}

 	/*-------------------------------------------------------------------------*/
 	// MEMBER TOOL: START
 	/*-------------------------------------------------------------------------*/

 	function mod_find_user_start($msg="")
	{
		global $std, $ibforums, $DB;

		if ( ! $ibforums->member['g_is_supmod'] )
		{
			$this->output .= $this->html->dead_section();
 			return;
		}

		$this->output .= $this->html->mod_find_user($msg);
	}

	/*-------------------------------------------------------------------------*/
 	// MEMBER TOOL: COMPLETE
 	/*-------------------------------------------------------------------------*/

	function mod_find_user_complete()
	{
		global $std, $ibforums, $DB, $print;

		$ibforums->input['name'] = trim(strtolower($ibforums->input['name']));

		if ( $ibforums->input['name'] == "" )
		{
			$this->mod_find_user_start($ibforums->lang['cp_no_matches']);
			return;
		}

		//-----------------------------------------
		// Query the DB for possible matches
		//-----------------------------------------

		$this->start_val = intval($ibforums->input['st'] );

		$sql = "lower(name) LIKE '{$ibforums->input['name']}%'";

		$DB->simple_construct( array( 'select' => 'count(id) as max', 'from' => 'members', 'where' => $sql ) );
		$DB->simple_exec();

		$total_possible = $DB->fetch_row();

		if ($total_possible['max'] < 1)
		{
			$this->mod_find_user_start( $ibforums->lang['cp_no_matches'] );
			return;
		}

		$pages = $std->build_pagelinks( array( 'TOTAL_POSS'  => $total_possible['max'],
											   'PER_PAGE'    => 20,
											   'CUR_ST_VAL'  => $this->start_val,
											   'L_SINGLE'    => $ibforums->lang['single_page_forum'],
											   'L_MULTI'     => $ibforums->lang['multi_page_forum'],
											   'BASE_URL'    => $ibforums->base_url."act=usercp&CODE=domemtool&name={$ibforums->input['name']}",
											 )
									  );

		$content = "";

		$DB->simple_construct( array( 'select' => 'name, id, ip_address, posts, joined, mgroup',
									  'from'   => 'members',
									  'where'  => $sql,
									  'order'  => "joined DESC",
									  'limit'  => array( $this->start_val,20 ) ) );
		$DB->simple_exec();

		while( $row = $DB->fetch_row() )
		{
			$row['joined']    = $std->get_date( $row['joined'], 'JOINED' );
			$row['groupname'] = $ibforums->cache['group_cache'][ $row['mgroup'] ]['prefix']
							  . $ibforums->cache['group_cache'][ $row['mgroup'] ]['g_title']
							  . $ibforums->cache['group_cache'][ $row['mgroup'] ]['suffix'];

			if ( ($ibforums->member['mgroup'] != $ibforums->vars['admin_group']) and ($row['mgroup'] == $ibforums->vars['admin_group']) )
			{
				$row['ip_address'] = '--';
			}

			$content .= $this->html->mod_ip_member_row( $row, $std->return_md5_check() );
		}

		$this->mod_find_user_start( $this->html->mod_ip_member_results($pages, $content) );
	}

 	/*-------------------------------------------------------------------------*/
 	// IP TOOL: Start
 	/*-------------------------------------------------------------------------*/

 	function mod_ip_tool_start($msg="")
 	{
		global $ibforums, $DB, $std, $forums;

 		if ( ! $ibforums->member['g_is_supmod'] )
 		{
 			$this->output .= $this->html->dead_section();
 			return;
 		}

 		$this->output .= $this->html->mod_ip_start_form($ibforums->input['ip'], $msg);

 		$this->page_title = $ibforums->lang['menu_ipsearch'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 		$this->nav[]      = $ibforums->lang['menu_ipsearch'];
 	}

 	/*-------------------------------------------------------------------------*/
 	// IP TOOL: Complete
 	/*-------------------------------------------------------------------------*/

 	function mod_ip_tool_complete()
 	{
		global $ibforums, $DB, $std, $forums;

 		if ( ! $ibforums->member['g_is_supmod'] )
 		{
 			$this->output .= $this->html->dead_section();
 			return;
 		}

 		//-----------------------------------------
		// Remove trailing periods
		//-----------------------------------------

		$exact_match     = 1;
		$final_ip_string = trim( $ibforums->input['ip'] );
		$this->start_val = intval($ibforums->input['st'] );

		if ( strstr( $final_ip_string, '*' ) )
		{
			$exact_match = 0;

			$final_ip_string = preg_replace( "/^(.+?)\*(.+?)?$/", "\\1", $final_ip_string ).'%';
		}

		//-----------------------------------------
		// H'okay, what have we been asked to do?
		// (that's a metaphorical "we" in a rhetorical question)
		//-----------------------------------------

		if ($ibforums->input['iptool'] == 'resolve')
		{
			$resolved = @gethostbyaddr($final_ip_string);

			if ($resolved == "")
			{
				$this->mod_ip_tool_start( $ibforums->lang['cp_safe_fail'] );
				return;
			}
			else
			{
				$this->mod_ip_tool_start( sprintf($ibforums->lang['ip_resolve_result'], $final_ip_string, $resolved) );
			}
		}
		else if ($ibforums->input['iptool'] == 'members')
		{
			if ($exact_match == 0)
			{
				$sql = "ip_address LIKE '$final_ip_string'";
			}
			else
			{
				$sql = "ip_address='$final_ip_string'";
			}

			$DB->simple_construct( array( 'select' => 'count(id) as max', 'from' => 'members', 'where' => $sql ) );
			$DB->simple_exec();

			$total_possible = $DB->fetch_row();

			if ($total_possible['max'] < 1)
			{
				$this->mod_ip_tool_start( $ibforums->lang['cp_no_matches'] );
				return;
			}

			$pages = $std->build_pagelinks( array( 'TOTAL_POSS'  => $total_possible['max'],
												   'PER_PAGE'    => 20,
												   'CUR_ST_VAL'  => $this->start_val,
												   'L_SINGLE'    => $ibforums->lang['single_page_forum'],
												   'L_MULTI'     => $ibforums->lang['multi_page_forum'],
												   'BASE_URL'    => $ibforums->base_url."act=usercp&CODE=doiptool&iptool=members&ip={$ibforums->input['ip']}",
												 )
										  );

			$content = "";

			if ( $ibforums->member['mgroup'] != $ibforums->vars['admin_group'] )
			{
				$sql .= "AND mgroup != {$ibforums->vars['admin_group']}";
			}

			$DB->simple_construct( array( 'select' => 'name, id, ip_address, posts, joined, mgroup',
										  'from'   => 'members',
										  'where'  => $sql,
										  'order'  => "joined DESC",
										  'limit'  => array( $this->start_val,20 ) ) );
			$DB->simple_exec();

			while( $row = $DB->fetch_row() )
			{
				$row['joined']    = $std->get_date( $row['joined'], 'JOINED' );
				$row['groupname'] = $ibforums->cache['group_cache'][ $row['mgroup'] ]['prefix']
								  . $ibforums->cache['group_cache'][ $row['mgroup'] ]['g_title']
								  . $ibforums->cache['group_cache'][ $row['mgroup'] ]['suffix'];
				$content .= $this->html->mod_ip_member_row( $row, $std->return_md5_check() );
			}

			$this->mod_ip_tool_start( $this->html->mod_ip_member_results($pages, $content) );
		}
		else
		{
			// Find posts then!

			if ($exact_match == 0)
			{
				$sql = "ip_address LIKE '$final_ip_string'";
			}
			else
			{
				$sql = "ip_address='$final_ip_string'";
			}

			// Get forums we're allowed to view

			$aforum = array();

			foreach( $forums->forum_by_id as $id => $data )
			{
				if ( $std->check_perms($data['read_perms']) == TRUE )
				{
					$aforum[] = $data['id'];
				}
			}

			if ( count($aforum) < 1)
			{
				$this->mod_ip_tool_start( $ibforums->lang['cp_no_matches'] );
				return;
			}

			$the_forums = implode( ",", $aforum);

			$DB->cache_add_query( 'ucp_mod_ip_tool_one', array( 'the_forums' => $the_forums, 'sql' => $sql ) );
			$DB->cache_exec_query();

			$max_hits = $DB->get_num_rows();

			$posts  = "";

			while ($row = $DB->fetch_row() )
			{
				$posts .= $row['pid'].",";
			}

			$posts  = preg_replace( "/,$/", "", $posts );

			//-----------------------------------------
			// Do we have any results?
			//-----------------------------------------

			if ($posts == "")
			{
				$this->mod_ip_tool_start( $ibforums->lang['cp_no_matches'] );
				return;
			}

			//-----------------------------------------
			// If we are still here, store the data into the database...
			//-----------------------------------------

			$unique_id = md5(uniqid(microtime(),1));

			$DB->do_insert( 'search_results', array (
													  'id'         => $unique_id,
													  'search_date'=> time(),
													  'post_id'    => $posts,
													  'post_max'   => $max_hits,
													  'sort_key'   => 'p.post_date',
													  'sort_order' => 'desc',
													  'member_id'  => $ibforums->member['id'],
													  'ip_address' => $ibforums->input['IP_ADDRESS'],
											 )        );

			$this->mod_ip_tool_start( $this->html->mod_ip_post_results($unique_id, $max_hits) );

			return TRUE;
		}
	}

 	/*-------------------------------------------------------------------------*/
 	// Attachments
 	/*-------------------------------------------------------------------------*/

 	function attachments()
 	{
		global $ibforums, $DB, $std, $forums;

 		$info     = array();
 		$start    = intval($ibforums->input['st']);
 		$perpage  = 15;

 		$sort_key = "";

 		switch ($ibforums->input['sort'])
 		{
 			case 'date':
 				$sort_key = 'a.attach_date ASC';
 				$info['date_order'] = 'rdate';
 				$info['size_order'] = 'size';
 				break;
 			case 'rdate':
 				$sort_key = 'a.attach_date DESC';
 				$info['date_order'] = 'date';
 				$info['size_order'] = 'size';
 				break;
 			case 'size':
 				$sort_key = 'a.attach_filesize DESC';
 				$info['date_order'] = 'date';
 				$info['size_order'] = 'rsize';
 				break;
 			case 'rsize':
 				$sort_key = 'a.attach_filesize ASC';
 				$info['date_order'] = 'date';
 				$info['size_order'] = 'size';
 				break;
 			default:
 				$sort_key = 'a.attach_date DESC';
 				$info['date_order'] = 'date';
 				$info['size_order'] = 'size';
 				break;
 		}

 		$this->page_title = $ibforums->lang['m_attach'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 		//-----------------------------------------
 		// Get the ID's to delete
 		//-----------------------------------------

 		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^attach_(\d+)$/", $key, $match ) )
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
 			$DB->cache_add_query( 'usercp_get_to_delete', array( 'mid' => $ibforums->member['id'], 'aid_array' => $ids ) );

    		$o = $DB->cache_exec_query();

			while ( $killmeh = $DB->fetch_row( $o ) )
			{
				if ( $killmeh['attach_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_location'] );
				}
				if ( $killmeh['attach_thumb_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_thumb_location'] );
				}

				if ( $killmeh['topic_id'] )
				{
					$DB->simple_construct( array( 'update' => 'topics', 'set' => 'topic_hasattach=topic_hasattach-1', 'where' => 'tid='.$killmeh['topic_id'] ) );
					$DB->simple_shutdown_exec();
				}
			}

			$DB->simple_exec_query( array( 'delete' => 'attachments', 'where' => 'attach_id IN ('.implode(",",$ids).') and attach_member_id='.$ibforums->member['id'] ) );
 		}

 		//-----------------------------------------
 		// Get some stats...
 		//-----------------------------------------

 		$maxspace = intval($ibforums->member['g_attach_max']);

 		if ( $ibforums->member['g_attach_max'] == -1 )
 		{
 			$std->Error( array( 'MSG' => 'no_permission', 'LEVEL' => 1 ) );
 		}

 		//-----------------------------------------
 		// Limit by forums
 		//-----------------------------------------

 		$stats = $DB->simple_exec_query( array( 'select' => 'count(*) as count, sum(attach_filesize) as sum',
 												'from'   => 'attachments',
 												'where'  => 'attach_member_id='.$ibforums->member['id'] ) );

 		if ( $maxspace > 0 )
 		{
			//-----------------------------------------
			// Figure out percentage used
			//-----------------------------------------

			$info['has_limit']    = 1;
			$info['full_percent'] = $stats['sum'] ? sprintf( "%.0f", ( ( $stats['sum'] / ($maxspace * 1024) ) * 100) ) : 0;

			if ( $info['full_percent'] > 100 )
			{
				$info['full_percent'] = 100;
			}

			$info['img_width']    = $info['full_percent'] > 0 ? intval($info['full_percent']) * 2.4 : 1;

			if ($info['img_width'] > 235)
			{
				$info['img_width'] = 235;
			}

			$ibforums->lang['attach_space_count'] = sprintf( $ibforums->lang['attach_space_count'], $stats['count'], $info['full_percent'] );
			$ibforums->lang['attach_space_used']  = sprintf( $ibforums->lang['attach_space_used'] , $std->size_format(intval($stats['sum'])), $std->size_format($maxspace * 1024) );
 		}
 		else
 		{
 			$info['has_limit'] = 0;
 			$ibforums->lang['attach_space_used']  = sprintf( $ibforums->lang['attach_space_unl'] , $std->size_format(intval($stats['sum'])) );
 		}

 		//-----------------------------------------
 		// Pages
 		//-----------------------------------------

 		$pages = $std->build_pagelinks( array( 'TOTAL_POSS'  => $stats['count'],
											   'PER_PAGE'    => $perpage,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "",
											   'L_MULTI'     => $ibforums->lang['msg_pages'],
											   'BASE_URL'    => $ibforums->base_url."act=usercp&amp;CODE=attach&amp;sort={$ibforums->input['sort']}",
									  )      );

 		//-----------------------------------------
 		// Get attachments...
 		//-----------------------------------------

 		$DB->cache_add_query( 'usercp_get_attachments', array( 'mid' => $ibforums->member['id'], 'order' => $sort_key, 'limit_a' => $start, 'limit_b' => $perpage ) );

    	$DB->cache_exec_query();

    	$temp_html = "";

		$ibforums->lang = $std->load_words($ibforums->lang,'lang_topic', $ibforums->lang_id );

		while ( $row = $DB->fetch_row() )
		{
			if ( $std->check_perms($forums->forum_by_id[ $row['forum_id'] ]['read_perms']) != TRUE )
			{
				$row['title'] = $ibforums->lang['attach_topicmoved'];
			}

			//-----------------------------------------
			// Full attachment thingy
			//-----------------------------------------

			if ( $row['attach_pid'] )
			{
				$row['_type'] = 'post';
			}
			else
			{
				$row['_type'] = 'msg';
				$row['title'] = $ibforums->lang['attach_inpm'];
			}

			$row['image']       = $ibforums->cache['attachtypes'][ $row['attach_ext'] ]['atype_img'];

			$row['short_name']  = $std->txt_truncate( $row['attach_file'], 30 );

			$row['attach_date'] = $std->get_date( $row['attach_date'], 'SHORT' );

			$row['real_size']   = $std->size_format( $row['attach_filesize'] );

			$temp_html .= $this->html->attachments_row( $row );
		}

    	$this->output .= $this->html->attachments_top($info, $pages, $temp_html);

 	}

 	/*-------------------------------------------------------------------------*/
 	// Ignore user.
 	/*-------------------------------------------------------------------------*/

 	function ignore_user_splash($msg="")
 	{
		global $ibforums, $DB, $std;

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 		$final_users = array();
 		$temp_users  = array();

 		//-----------------------------------------
 		// Do we have a MESSAGE FRROM GAWD???!@
 		//-----------------------------------------

 		if ( $msg )
 		{
 			$this->output .= $this->html->ucp_message( $ibforums->lang['mi5_error'], $msg );
 		}

 		//-----------------------------------------
 		// Do we have incoming?
 		//-----------------------------------------

 		if ( intval($ibforums->input['uid']) )
 		{
 			$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "id=".intval($ibforums->input['uid']) ) );
			$DB->simple_exec();

 			$newmem = $DB->fetch_row();

 			$ibforums->input['newbox_1'] = $newmem['name'];
 		}

 		//-----------------------------------------
 		// Stored as userid,userid,userid
 		//-----------------------------------------

 		$ignored_users = explode( ',', $ibforums->member['ignored_users'] );

 		//-----------------------------------------
 		// Get members and check to see if they've
 		// since been moved into a group that cannot
 		// be ignored
 		//-----------------------------------------

 		foreach( $ignored_users as $id )
 		{
 			if ( intval($id) )
 			{
 				$temp_users[] = $id;
 			}
 		}

 		if ( count($temp_users) )
 		{
 			$DB->simple_construct( array( 'select' => 'id, name, mgroup, posts',
										  'from'   => 'members',
										  'where'  => "id IN (".implode(",",$temp_users).")"
								 )      );

			$DB->simple_exec();

 			while ( $m = $DB->fetch_row() )
 			{
 				$m['g_title'] = $ibforums->cache['group_cache'][ $m['mgroup'] ]['g_title'];
 				$m['prefix']  = $ibforums->cache['group_cache'][ $m['mgroup'] ]['prefix'];
 				$m['suffix']  = $ibforums->cache['group_cache'][ $m['mgroup'] ]['suffix'];

 				if ( $ibforums->vars['cannot_ignore_groups'] )
				{
					if ( strstr( $ibforums->vars['cannot_ignore_groups'], ','.$m['mgroup'].',' ) )
					{
						continue;
					}
 				}

 				$final_users[ $m['id'] ] = $m;
 			}
 		}

 		$this->output .= $this->html->iu_start();

 		foreach( $final_users as $id => $member )
 		{
 			$this->output .= $this->html->iu_populated_row($member);
 		}

 		$this->output .= $this->html->iu_add_new();

 	}


 	/*-------------------------------------------------------------------------*/
 	// Photo:
 	//
 	// Change / Add / Edit Users Photo
 	/*-------------------------------------------------------------------------*/

 	function photo()
 	{
		global $ibforums, $DB, $std;

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 		if ( $ibforums->member['g_photo_max_vars'] == "" or $ibforums->member['g_photo_max_vars'] == "::" )
 		{
 			// Nothing set up yet...

 			$this->output .= $this->html->dead_section();
 			return;
 		}

 		//-----------------------------------------
 		// SET DIMENSIONS
 		//-----------------------------------------

 		list($p_max, $p_width, $p_height) = explode( ":", $ibforums->member['g_photo_max_vars'] );

 		if ( $p_max )
 		{
 			$ibforums->lang['pph_max']  = sprintf( $ibforums->lang['pph_max'], $p_max );
 			$ibforums->lang['pph_max'] .= sprintf( $ibforums->lang['pph_max2'], $p_width, $p_height );
 		}
 		else
 		{
 			$ibforums->lang['pph_max'] = sprintf( $ibforums->lang['pph_max2'], $p_width, $p_height );
 		}

 		list($p_w, $p_h) = explode( ",", $ibforums->member['photo_dimensions'] );

 		$cur_photo = $ibforums->lang['pph_none'];
 		$cur_type  = "";
 		$url_photo = "";

 		$width  = ( $p_w ) ? "width='$p_w'"  : "";
 		$height = ( $p_h ) ? "height='$p_h'" : "";

 		$show_size = str_replace( ",", " X ", $ibforums->member['photo_dimensions'] );

 		//-----------------------------------------
 		// TYPE?
 		//-----------------------------------------

 		if ( $ibforums->member['photo_type'] == 'upload' )
 		{
 			$cur_type  = $ibforums->lang['pph_t_upload'];
 			$cur_photo = "<img src=\"".$ibforums->vars['upload_url']."/".$ibforums->member['photo_location']."\" $width $height alt='Photo' />";
 		}
 		else if ( $ibforums->member['photo_type'] == 'url' )
 		{
 			$cur_type  = $ibforums->lang['pph_t_url'];
 			$cur_photo = "<img src=\"".$ibforums->member['photo_location']."\" $width $height alt='Photo' />";
 			$url_photo = $ibforums->member['photo_location'];
 		}

 		//-----------------------------------------
 		// SHOW THE FORM
 		//-----------------------------------------

 		$this->output .= $this->html->photo_page($cur_photo, $cur_type, $url_photo, $show_size, $this->md5_check);

 		if ($p_max)
 		{
 			$this->output = str_replace( "<!--IPB.UPLOAD-->", $this->html->photo_page_upload( 500000 ), $this->output );
 		}

 		$size_html = $ibforums->vars['disable_ipbsize'] ? $this->html->photo_page_mansize() : $this->html->photo_page_autosize();

 		$this->output = str_replace( "<!--IPB.SIZE-->", $size_html, $this->output );

 	}


 	/*-------------------------------------------------------------------------*/
 	// Forum tracker
 	//
 	// What, you need a definition with that title?
 	// What are you doing poking around in the code for anyway?
 	/*-------------------------------------------------------------------------*/

 	function remove_forum_tracker()
 	{
		global $ibforums, $std, $DB;

 		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^id-(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$ids[] = $match[1];
 				}
 			}
 		}

 		$allowed = array( 'none', 'immediate', 'delayed', 'daily', 'weekly' );

 		//-----------------------------------------
 		// what we doing?
 		//-----------------------------------------

 		if ( count($ids) > 0 )
 		{
 			if ( $ibforums->input['trackchoice'] == 'unsubscribe' )
 			{
 				$DB->simple_exec_query( array( 'delete' => 'forum_tracker', 'where' => "member_id={$ibforums->member['id']} and forum_id IN (".implode( ",", $ids ).")" ) );
 			}
 			else if ( in_array( $ibforums->input['trackchoice'], $allowed ) )
 			{
 				$DB->do_update( 'forum_tracker', array( 'forum_track_type' => $ibforums->input['trackchoice'] ), "member_id={$ibforums->member['id']} and forum_id IN (".implode( ",", $ids ).")" );
 			}
 		}

 	    $std->boink_it($ibforums->base_url."act=UserCP&CODE=50");

 	}

 	function forum_tracker()
 	{
		global $ibforums, $DB, $std, $print, $forums;

 		//-----------------------------------------
 		// Remap...
 		//-----------------------------------------

 		$remap = array( 'none'      => 'subs_none_title',
						'immediate' => 'subs_immediate',
						'delayed'   => 'subs_delayed',
						'daily'     => 'subs_daily',
						'weekly'    => 'subs_weekly'
					  );

 		$this->output .= $this->html->forum_subs_header();

 		//-----------------------------------------
 		// Query the DB for the subby toppy-ics - at the same time
 		// we get the forum and topic info, 'cos we rule.
 		//-----------------------------------------

 		$DB->cache_add_query( 'ucp_get_forum_tracker', array( 'mid' => $ibforums->member['id'] ) );
		$DB->cache_exec_query();

 		if ( $DB->get_num_rows() )
 		{
 			while( $forum = $DB->fetch_row() )
 			{
 				$forum['folder_icon'] = $forums->forums_new_posts($forum);

 				$forum['last_post'] = $std->get_date($forum['last_post'], 'LONG');

				$forum['last_topic'] = $ibforums->lang['f_none'];

 				$forum['last_title'] = str_replace( "&#33;" , "!", $forum['last_title'] );
				$forum['last_title'] = str_replace( "&quot;", "\"", $forum['last_title'] );

				if (strlen($forum['last_title']) > 30)
				{
					$forum['last_title'] = substr($forum['last_title'],0,27) . "...";
					$forum['last_title'] = preg_replace( '/&(#(\d+;?)?)?\.\.\.$/', '...', $forum['last_title'] );
				}

				if ($forum['password'] != "")
				{
					$forum['last_topic'] = $ibforums->lang['f_none'];
				}
				else
				{
					$forum['last_topic'] = "<a href='{$ibforums->base_url}showtopic={$forum['last_id']}&view=getlastpost'>{$forum['last_title']}</a>";
				}


				if ( isset($forum['last_poster_name']))
				{
					$forum['last_poster'] = $forum['last_poster_id'] ? "<a href='{$ibforums->base_url}showuser={$forum['last_poster_id']}'>{$forum['last_poster_name']}</a>"
																	 : $forum['last_poster_name'];
				}
				else
				{
					$forum['last_poster'] = $ibforums->lang['f_none'];
				}

				$this->output .= $this->html->forum_subs_row($forum, $remap[ $forum['forum_track_type'] ]);
			}

		}
		else
		{
			$this->output .= $this->html->forum_subs_none();
		}

		$this->output .= $this->html->forum_subs_end();

		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}


 	/*-------------------------------------------------------------------------*/
 	// pass change:
 	//
 	// Change the users password.
 	/*-------------------------------------------------------------------------*/

 	function pass_change()
 	{
		global $ibforums, $DB, $std;

 		$this->output    .= $this->html->pass_change();
 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}

 	/*-------------------------------------------------------------------------*/
 	// PASSWORD CHAGE COMPLETE
 	/*-------------------------------------------------------------------------*/

 	function do_pass_change()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( $_POST['current_pass'] == "" or empty($_POST['current_pass']) )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
 		}

 		//-----------------------------------------
 		// Check and trim
 		//-----------------------------------------

 		$cur_pass = trim($ibforums->input['current_pass']);
 		$new_pass = trim($ibforums->input['new_pass_1']);
 		$chk_pass = trim($ibforums->input['new_pass_2']);

 		if ( ( empty($new_pass) ) or ( empty($chk_pass) ) )
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
 		}

 		if ($new_pass != $chk_pass)
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'pass_no_match' ) );
 		}

 		//-----------------------------------------
 		// Check password...
 		//-----------------------------------------

 		$ibforums->converge->converge_load_member($ibforums->member['email']);

 		if ( $ibforums->converge->converge_authenticate_member( md5($cur_pass) ) != TRUE )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'wrong_pass' ) );
		}

 		//-----------------------------------------
 		// Create new password...
 		//-----------------------------------------

 		$md5_pass = md5($new_pass);

 		//-----------------------------------------
 		// Update the DB
 		//-----------------------------------------

 		$ibforums->converge->converge_update_password( $md5_pass, $ibforums->member['email'] );

 		//-----------------------------------------
 		// Update members log in key...
 		//-----------------------------------------

 		$key  = $ibforums->converge->generate_auto_log_in_key();
 		$DB->do_update( 'members', array( 'member_login_key' => $key ), 'id='.$ibforums->member['id'] );

 		//-----------------------------------------
 		// Use sync module?
 		//-----------------------------------------

 		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);
    		$this->modules->on_pass_change($ibforums->member['id'], $new_pass);
   		}

 		//-----------------------------------------
 		// Redirect...
 		//-----------------------------------------

 		$print->redirect_screen( $ibforums->lang['pass_redirect'], 'act=UserCP&CODE=00' );
 	}


 	/*-------------------------------------------------------------------------*/
 	// email change:
 	//
 	// Change the users email address
 	/*-------------------------------------------------------------------------*/

 	function email_change($msg="")
 	{
		global $ibforums, $DB, $std;

 		$txt = $ibforums->lang['ce_current'].$ibforums->member['email'];

 		if ($ibforums->vars['reg_auth_type'])
 		{
 			$txt .= $ibforums->lang['ce_auth'];
 		}

 		if ($ibforums->vars['bot_antispam'])
 		{
			//-----------------------------------------
			// Set up security code
			//-----------------------------------------

			// Set a new ID for this reg request...

			$regid = md5( uniqid(microtime()) );

			// Set a new 6 character numerical string

			mt_srand ((double) microtime() * 1000000);

			$reg_code = mt_rand(100000,999999);

			// Insert into the DB

			$DB->do_insert( 'reg_antispam', array (
													 'regid'      => $regid,
													 'regcode'    => $reg_code,
													 'ip_address' => $ibforums->input['IP_ADDRESS'],
													 'ctime'      => time(),
										 )       );
		}

 		$this->output    .= $this->html->email_change($txt, $ibforums->lang[$msg]);

 		if ($ibforums->vars['bot_antispam'])
 		{

			if ($ibforums->vars['bot_antispam'] == 'gd')
			{
				$this->output = str_replace( "<!--ANTIBOT-->", $this->html->email_change_gd($regid), $this->output );
			}
			else
			{
				$this->output = str_replace( "<!--ANTIBOT-->", $this->html->email_change_img($regid), $this->output );
			}

 		}

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 	}

 	/*-------------------------------------------------------------------------*/
 	// COMPLETE EMAIL ADDRESS CHANGE
 	/*-------------------------------------------------------------------------*/

 	function do_email_change()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Check input
 		//-----------------------------------------

 		if ($_POST['in_email_1'] == "")
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
 		}

 		if ($_POST['in_email_2'] == "")
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
 		}

 		//-----------------------------------------
 		// Authorizing?
 		//-----------------------------------------

 		if ($ibforums->member['mgroup'] == $ibforums->vars['auth_group'])
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'email_change_v' ) );
 		}

 		//-----------------------------------------
 		// Check password...
 		//-----------------------------------------

 		$ibforums->converge->converge_load_member($ibforums->member['email']);

 		if ( $ibforums->converge->converge_authenticate_member( md5($ibforums->input['password']) ) != TRUE )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'wrong_pass' ) );
		}

 		//-----------------------------------------
 		// Test email addresses
 		//-----------------------------------------

 		$email_one    = strtolower( trim($ibforums->input['in_email_1']) );
 		$email_two    = strtolower( trim($ibforums->input['in_email_2']) );

 		if ($email_one != $email_two)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'email_addy_mismatch' ) );
		}

		$email_one = $std->clean_email($email_one);

		if ( $email_one == "" )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_email' ) );
		}

		//-----------------------------------------
		// Is this email addy taken?
		//-----------------------------------------

		if ( $ibforums->converge->converge_check_for_member_by_email( $email_one ) == TRUE )
		{
			$std->Error( array( LEVEL => 1, MSG => 'email_exists' ) );
		}

		//-----------------------------------------
		// Check in banned list
		//-----------------------------------------

		if ($ibforums->vars['ban_email'])
		{
			$ips = explode( "|", $ibforums->vars['ban_email'] );

			foreach ($ips as $ip)
			{
				$ip = preg_replace( "/\*/", '.*' , $ip );

				if ( preg_match( "/$ip/", $email_one ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'banned_email' ) );
				}
			}
		}

		//-----------------------------------------
		// Anti bot flood...
		//-----------------------------------------

		if ($ibforums->vars['bot_antispam'])
 		{
			//-----------------------------------------
			// Check the security code:
			//-----------------------------------------

			if ($ibforums->input['regid'] == "")
			{
				$this->email_change('err_security_code');
				return "";
			}

			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'reg_antispam',
										  'where'  => "regid='".trim(addslashes($ibforums->input['regid']))."'"
								 )      );

			$DB->simple_exec();

			if ( ! $row = $DB->fetch_row() )
			{
				$this->email_change('err_security_code');
				return "";
			}

			if ( trim( intval($ibforums->input['reg_code']) ) != $row['regcode'] )
			{
				$this->email_change('err_security_code');
				return "";
			}

			$DB->simple_construct( array( 'delete' => 'reg_antispam',
										  'where'  => "regid='".trim(addslashes($ibforums->input['regid']))."'"
								 )      );

			$DB->simple_exec();
		}

		//-----------------------------------------
		// Update converge...
		//-----------------------------------------

		$ibforums->converge->converge_update_member( $ibforums->member['email'], $email_one );

		//-----------------------------------------
		// Update dupemail
		//-----------------------------------------

		if ( $ibforums->member['bio'] == 'dupemail' )
		{
			$DB->do_update( 'member_extra', array( 'bio' => '' ), 'id='.$ibforums->member['id'] );
		}

		//-----------------------------------------
 		// Use sync module?
 		//-----------------------------------------

 		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);
    		$this->modules->on_email_change($ibforums->member['id'], $email_one);
   		}

		//-----------------------------------------
		// Require new validation? NON ADMINS ONLY
		//-----------------------------------------

		if ($ibforums->vars['reg_auth_type'] AND ! $ibforums->member['g_access_cp'] )
		{
			$validate_key = md5( $std->make_password() . time() );

			//-----------------------------------------
			// Update the new email, but enter a validation key
			// and put the member in "awaiting authorisation"
			// and send an email..
			//-----------------------------------------

			$db_str = array(
							'vid'         => $validate_key,
							'member_id'   => $ibforums->member['id'],
							'real_group'  => $ibforums->member['mgroup'],
							'temp_group'  => $ibforums->vars['auth_group'],
							'entry_date'  => time(),
							'coppa_user'  => 0,
							'email_chg'   => 1,
							'ip_address'  => $ibforums->input['IP_ADDRESS']
						   );

			$DB->do_insert( 'validating', $db_str );

			$DB->do_update( 'members' , array(
												'mgroup' => $ibforums->vars['auth_group'],
												'email'  => $email_one,
											 ), 'id='.$ibforums->member['id']
						  );

			//-----------------------------------------
			// Update their session with the new member group
			//-----------------------------------------

			if ( $ibforums->session_id )
			{
				$DB->do_update( 'sessions', array( 'member_name'  => '',
												   'member_id'    => 0,
												   'member_group' => $ibforums->vars['guest_group']
												 ), "member_id=".$ibforums->member['id']." and id='".$ibforums->session_id."'"
							  );
			}

 			//-----------------------------------------
 			// Kill the cookies to stop auto log in
 			//-----------------------------------------

 			$std->my_setcookie( 'pass_hash'  , '-1', 0 );
 			$std->my_setcookie( 'member_id'  , '-1', 0 );
 			$std->my_setcookie( 'session_id' , '-1', 0 );

 			//-----------------------------------------
 			// Dispatch the mail, and return to the activate form.
 			//-----------------------------------------

 			$this->email->get_template("newemail");

			$this->email->build_message( array(
												'NAME'         => $ibforums->member['name'],
												'THE_LINK'     => $ibforums->base_url_nosess."?act=Reg&CODE=03&type=newemail&uid=".$ibforums->member['id']."&aid=".$validate_key,
												'ID'           => $ibforums->member['id'],
												'MAN_LINK'     => $ibforums->base_url_nosess."?act=Reg&CODE=07",
												'CODE'         => $validate_key,
											  )
										);

			$this->email->subject = $ibforums->lang['lp_subject'].' '.$ibforums->vars['board_name'];
			$this->email->to      = $email_one;

			$this->email->send_mail();

			$print->redirect_screen( $ibforums->lang['ce_redirect'], 'act=Reg&CODE=07' );
		}
		else
		{
			//-----------------------------------------
			// No authorisation needed, change email addy and return
			//-----------------------------------------

			$DB->do_update( 'members', array( 'email' => $email_one ), 'id='.$ibforums->member['id'] );

			$print->redirect_screen( $ibforums->lang['email_changed_now'], 'act=UserCP&CODE=00' );
		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// tracker:
 	//
 	// Print the subscribed topics listings
 	/*-------------------------------------------------------------------------*/

 	function tracker()
 	{
		global $ibforums, $DB, $std, $print;

 		$this->output .= $this->html->subs_header();

 		//-----------------------------------------
 		// Remap...
 		//-----------------------------------------

 		$remap = array( 'none'      => 'subs_none_title',
						'immediate' => 'subs_immediate',
						'delayed'   => 'subs_delayed',
						'daily'     => 'subs_daily',
						'weekly'    => 'subs_weekly'
					  );

 		//-----------------------------------------
 		// Get forums module
 		//-----------------------------------------

 		require_once( ROOT_PATH.'sources/forums.php' );
 		$this->forums = new forums();
 		$this->forums->init();

 		//-----------------------------------------
 		// Are we checking for auto-prune?
 		//-----------------------------------------

 		$auto_explain = $ibforums->lang['no_auto_prune'];

 		if ($ibforums->vars['subs_autoprune'] > 0)
 		{
			$auto_explain = sprintf( $ibforums->lang['auto_prune'], $ibforums->vars['subs_autoprune'] );
 		}

 		//-----------------------------------------
 		// Do we have an incoming date cut?
 		//-----------------------------------------

 		$date_cut   = intval($ibforums->input['datecut']) != "" ? intval($ibforums->input['datecut']) : 30;

 		$date_query = $date_cut != 1000 ? " AND t.last_post > '".(time() - ($date_cut*86400))."' " : "";

 		//-----------------------------------------
 		// Query the DB for the subby toppy-ics - at the same time
 		// we get the forum and topic info, 'cos we rule.
 		//-----------------------------------------

 		$DB->cache_add_query( 'ucp_get_topic_tracker', array( 'mid' => $ibforums->member['id'], 'date_query' => $date_query ) );
		$DB->cache_exec_query();

 		if ( $DB->get_num_rows() )
 		{
 			$last_forum_id = -1;

 			while( $topic = $DB->fetch_row() )
 			{
 				if ($last_forum_id != $topic['forum_id'])
 				{
 					$last_forum_id = $topic['forum_id'];

 					$this->output .= $this->html->subs_forum_row($topic['forum_id'], $topic['forum_name']);
 				}

				$topic['last_post_date']  = $std->get_date( $topic['last_post'], 'LONG' );

				if ( $topic['description'] )
				{
					$topic['description'] .= "<br />";
				}

				$topic['track_started'] = $std->get_date( $topic['track_started'], 'LONG' );

				$topic = $this->forums->parse_data($topic);

				$this->output .= $this->html->subs_row($topic, $remap[ $topic['topic_track_type'] ]);
			}

		}
		else
		{
			$this->output .= $this->html->subs_none();
		}

		// Build date box

		$date_box = "<option value='1'>".$ibforums->lang['subs_today']."</option>\n";

		foreach( array( 1,7,14,21,30,60,90,365 ) as $day )
		{
			$selected = $day == $date_cut ? ' selected="selected"' : '';

			$date_box .= "<option value='$day'$selected>".sprintf( $ibforums->lang['subs_day'], $day )."</option>\n";
		}

		$date_box .= "<option value='1000'>".$ibforums->lang['subs_all']."</option>\n";

		$this->output .= $this->html->subs_end($auto_explain, $date_box);

		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}

 	/*-------------------------------------------------------------------------*/
 	// UPDATE TRACKER
 	/*-------------------------------------------------------------------------*/

 	function do_update_tracker()
 	{
		global $ibforums, $std, $DB;

 		//-----------------------------------------
 		// Get the ID's to delete
 		//-----------------------------------------

 		if ($ibforums->input['request_method'] != 'post')
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'poss_hack_attempt' ) );
 		}

 		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^id-(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$ids[] = $match[1];
 				}
 			}
 		}

 		$allowed = array( 'none', 'immediate', 'delayed', 'daily', 'weekly' );

 		//-----------------------------------------
 		// what we doing?
 		//-----------------------------------------

 		if ( count($ids) > 0 )
 		{
 			if ( $ibforums->input['trackchoice'] == 'unsubscribe' )
 			{
 				$DB->simple_exec_query( array( 'delete' => 'tracker', 'where' => "member_id='".$ibforums->member['id']."' and trid IN (".implode( ",", $ids ).")" ) );
 			}
 			else if ( in_array( $ibforums->input['trackchoice'], $allowed ) )
 			{
 				$DB->do_update( 'tracker', array( 'topic_track_type' => $ibforums->input['trackchoice'] ), "trid IN (".implode( ",", $ids ).")" );
 			}
 		}

 	    $std->boink_it($ibforums->base_url."act=UserCP&CODE=26");

 	}


 	/*-------------------------------------------------------------------------*/
 	// BOARD PREFS:
 	//
 	// Set up view avatar, sig, time zone, etc.
 	/*-------------------------------------------------------------------------*/

 	function board_prefs()
 	{
		global $ibforums, $DB, $std, $print;

 		$time = $std->get_date( time(), 'LONG' );

 		// Do we have a user stored offset, or use the board default:

 		$offset = ( $ibforums->member['time_offset'] != "" ) ? $ibforums->member['time_offset'] : $ibforums->vars['time_offset'];

 		$time_select = "<select name='u_timezone' class='forminput'>";

 		// Loop through the langauge time offsets and names to build our
 		// HTML jump box.

 		foreach( $ibforums->lang as $off => $words )
 		{
 			if ( preg_match("/^time_(-?[\d\.]+)$/", $off, $match))
 			{
				$time_select .= $match[1] == $offset ? "<option value='{$match[1]}' selected='selected'>$words</option>\n"
												     : "<option value='{$match[1]}'>$words</option>\n";
 			}
 		}

 		$time_select .= "</select>";

 		// Print out the header..

 		if ($ibforums->member['dst_in_use'])
 		{
 			$dst_check = 'checked';
 		}
 		else
 		{
 			$dst_check = '';
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

 		list($post_page, $topic_page) = explode( "&", $ibforums->member['view_prefs'] );

 		if ($post_page == "")
 		{
 			$post_page = -1;
 		}
 		if ($topic_page == "")
 		{
 			$topic_page = -1;
 		}

 		$pp_a = array();
 		$tp_a = array();
 		$post_select  = "";
 		$topic_select = "";

 		$pp_a[] = array( '-1', $ibforums->lang['pp_use_default'] );
 		$tp_a[] = array( '-1', $ibforums->lang['pp_use_default'] );

 		foreach( explode( ',', $ibforums->vars['postpage_contents'] ) as $n )
 		{
 			$n      = intval(trim($n));
 			$pp_a[] = array( $n, $n );
 		}

 		foreach( explode( ',', $ibforums->vars['topicpage_contents'] ) as $n )
 		{
 			$n      = intval(trim($n));
 			$tp_a[] = array( $n, $n );
 		}

 		//-----------------------------------------

 		foreach( $pp_a as $id => $data )
 		{
 			$post_select .= ($data[0] == $post_page) ? "<option value='{$data[0]}' selected='selected'>{$data[1]}\n" : "<option value='{$data[0]}'>{$data[1]}\n";
 		}

 		foreach( $tp_a as $id => $data )
 		{
 			$topic_select .= ($data[0] == $topic_page) ? "<option value='{$data[0]}' selected='selected'>{$data[1]}\n" : "<option value='{$data[0]}'>{$data[1]}\n";
 		}


 		//-----------------------------------------

 		$this->output .= $this->html->settings_header($this->member, $time_select, $time, $dst_check, $this->md5_check);

 		$hide_sess   = $std->my_getcookie('hide_sess');

 		$open_qreply = $std->my_getcookie("open_qr");

 		if ( $open_qreply == FALSE )
 		{
 			$open_qreply = 0;
 		}

 		// View avatars, signatures and images..

 		$view_ava  = "<select name='VIEW_AVS' class='forminput'>";
 		$view_sig  = "<select name='VIEW_SIGS' class='forminput'>";
 		$view_img  = "<select name='VIEW_IMG' class='forminput'>";
 		$view_pop  = "<select name='DO_POPUP' class='forminput'>";
 		$html_sess = "<select name='HIDE_SESS' class='forminput'>";
 		$html_qr   = "<select name='OPEN_QR' class='forminput'>";

 		$view_ava .= $ibforums->member['view_avs'] ? "<option value='1' selected='selected'>".$ibforums->lang['yes']."</option>\n<option value='0'>".$ibforums->lang['no']."</option>"
 											   : "<option value='1'>".$ibforums->lang['yes']."</option>\n<option value='0' selected='selected'>".$ibforums->lang['no']."</option>";

 		$view_sig .= $ibforums->member['view_sigs'] ? "<option value='1' selected='selected'>".$ibforums->lang['yes']."</option>\n<option value='0'>".$ibforums->lang['no']."</option>"
 											   : "<option value='1'>".$ibforums->lang['yes']."</option>\n<option value='0' selected='selected'>".$ibforums->lang['no']."</option>";

 		$view_img .= $ibforums->member['view_img'] ? "<option value='1' selected='selected'>".$ibforums->lang['yes']."</option>\n<option value='0'>".$ibforums->lang['no']."</option>"
 											   : "<option value='1'>".$ibforums->lang['yes']."</option>\n<option value='0' selected='selected'>".$ibforums->lang['no']."</option>";

 		$view_pop .= $ibforums->member['view_pop'] ? "<option value='1' selected='selected'>".$ibforums->lang['yes']."</option>\n<option value='0'>".$ibforums->lang['no']."</option>"
 											   : "<option value='1'>".$ibforums->lang['yes']."</option>\n<option value='0' selected='selected'>".$ibforums->lang['no']."</option>";

 		$html_sess .= $hide_sess == 1          ? "<option value='1' selected='selected'>".$ibforums->lang['yes']."</option>\n<option value='0'>".$ibforums->lang['no']."</option>"
 											   : "<option value='1'>".$ibforums->lang['yes']."</option>\n<option value='0' selected='selected'>".$ibforums->lang['no']."</option>";

 		$html_qr   .= $open_qreply == 1        ? "<option value='1' selected='selected'>".$ibforums->lang['yes']."</option>\n<option value='0'>".$ibforums->lang['no']."</option>"
 											   : "<option value='1'>".$ibforums->lang['yes']."</option>\n<option value='0' selected='selected'>".$ibforums->lang['no']."</option>";




 		$this->output .= $this->html->settings_end( array ( 'IMG'  => $view_img."</select>",
 															'SIG'  => $view_sig."</select>",
 															'AVA'  => $view_ava."</select>",
 															'POP'  => $view_pop."</select>",
 															'SESS' => $html_sess."</select>",
 															'QR'   => $html_qr."</select>",
 															'TPS'  => $topic_select,
 															'PPS'  => $post_select,
 												  )       );

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}


 	/*-------------------------------------------------------------------------*/
 	// EMAIL SETTINGS:
 	//
 	// Set up the email stuff.
 	/*-------------------------------------------------------------------------*/

 	function email_settings()
 	{
		global $ibforums, $DB, $std, $print;

 		// PM_REMINDER: First byte = Email PM when received new
 		//   			Second byte= Show pop-up when new PM received


 		$info = array();

 		foreach ( array(hide_email, allow_admin_mails, email_full, email_pm, auto_track) as $k )
 		{
 			if (!empty($ibforums->member[ $k ]))
 			{
 				$info[$k] = 'checked';
 			}
 		}

 		$info['key'] = $this->md5_check;

 		$this->output .= $this->html->email($info);

 		//-----------------------------------------
 		// Update select box
 		//-----------------------------------------

 		$this->output = str_replace( "<option value=\"{$ibforums->member['auto_track']}\">", "<option value='{$ibforums->member['auto_track']}' selected='selected'>", $this->output );

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}


 	/*-------------------------------------------------------------------------*/
 	// custom sort routine:
 	//
 	// Like wot is seys on the tin
 	/*-------------------------------------------------------------------------*/

 	function sort_avatars($a, $b)
 	{
 		$aa = strtolower($a[1]);
 		$bb = strtolower($b[1]);

 		if ( $aa == $bb ) return 0;

 		return ( $aa > $bb ) ? 1 : -1;
 	}


 	/*-------------------------------------------------------------------------*/
 	// AVATAR:
 	//
 	// Displays the avatar choices
 	/*-------------------------------------------------------------------------*/

 	function avatar_gallery()
 	{
		global $ibforums, $DB, $std, $print;

 		$avatar_gallery    = array();
 		$av_categories     = array( 0 => array( "root", $ibforums->lang['av_root'] ) );

 		$av_cat_selected   = preg_replace( "/[^\w\s_\-]/", "", $ibforums->input['av_cat'] );
 		$av_cat_found      = FALSE;
 		$av_human_readable = "";

 		if ($av_cat_selected == 'root')
 		{
 			$av_cat_selected   = "";
 			$av_human_readable = $ibforums->lang['av_root'];
 		}

 		//-----------------------------------------
 		// Get the avatar categories
 		//-----------------------------------------

 		$dh = opendir( CACHE_PATH.'style_avatars' );

 		while ( $file = readdir( $dh ) )
 		{
			if ( is_dir( CACHE_PATH.'style_avatars'."/".$file ) )
			{
				if ( $file != "." && $file != ".." )
				{
					if ( $file == $av_cat_selected )
					{
						$av_cat_found      = TRUE;
						$av_human_readable = str_replace( "_", " ", $file );
					}

					$av_categories[] = array( $file, str_replace( "_", " ", $file ) );
				}
			}
 		}

 		closedir( $dh );

 		//-----------------------------------------
 		// SORT IT OUT YOU MUPPET!!
 		//-----------------------------------------

 		usort( $av_categories, array( 'UserCP', 'sort_avatars' ) );
 		reset( $av_categories );

 		//-----------------------------------------
 		// Did we find the directory?
 		//-----------------------------------------

 		if ($av_cat_selected)
 		{
 			if ( $av_cat_found != TRUE )
 			{
 				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'av_no_gallery' ) );
 			}

 			$av_cat_real = "/".$av_cat_selected;
 		}

 		//-----------------------------------------
 		// Get the avatar images for this category
 		//-----------------------------------------

 		$dh = opendir( CACHE_PATH.'style_avatars'.$av_cat_real);

 		while ( $file = readdir( $dh ) )
 		{
 			if ( ! preg_match( "/^..?$|^index|^\.ds_store|^\.htaccess/i", $file ) )
 			{
 				if ( is_file( CACHE_PATH.'style_avatars'.$av_cat_real."/".$file) )
 				{
 					if ( preg_match( "/\.(gif|jpg|jpeg|png|swf)$/i", $file ) )
 					{
 						$av_gall_images[] = $file;
 					}
 				}
 			}
 		}

 		//-----------------------------------------
 		// SORT IT OUT YOU PLONKER!!
 		//-----------------------------------------

 		if ( is_array($av_gall_images) and count($av_gall_images) )
 		{
 			natcasesort($av_gall_images);
 			reset($av_gall_images);
 		}

 		//-----------------------------------------
 		// Render drop down box..
 		//-----------------------------------------

 		$av_gals = "<select name='av_cat' class='forminput'>\n";

 		foreach( $av_categories as $cat )
 		{
 			$av_gals .= "<option value='".$cat[0]."'>".$cat[1]."</option>\n";
 		}

 		$av_gals .= "</select>\n";

 		closedir( $dh );

 		$gal_cols = $ibforums->vars['av_gal_cols'] == "" ? 5 : $ibforums->vars['av_gal_cols'];
 		$gal_rows = $ibforums->vars['av_gal_rows'] == "" ? 3 : $ibforums->vars['av_gal_rows'];

 		$gal_found = count($av_gall_images);

 		//-----------------------------------------
 		// Produce the avatar gallery sheet
 		//-----------------------------------------

 		$this->output .= $this->html->avatar_gallery_start_table($av_human_readable,$av_gals,urlencode($av_cat_selected), $this->md5_check);

 		$c = 0;

 		if ( is_array($av_gall_images) and count($av_gall_images) )
 		{
			foreach( $av_gall_images as $img )
			{
				$c++;

				if ($c == 1)
				{
					$this->output .= $this->html->avatar_gallery_start_row();
				}

				$this->output .= $this->html->avatar_gallery_cell_row(
																	  $av_cat_real."/".$img,
																	  str_replace( "_", " ", preg_replace( "/^(.*)\.\w+$/", "\\1", $img ) ),
																	  urlencode($img)
																	);


				if ($c == $gal_cols)
				{
					$this->output .= $this->html->avatar_gallery_end_row();

					$c = 0;
				}

			}
 		}

 		if ($c != $gal_cols)
 		{
			for ($i = $c ; $i < $gal_cols ; ++$i)
			{
				$this->output .= $this->html->avatar_gallery_blank_row();
			}

			$this->output .= $this->html->avatar_gallery_end_row();
		}

 		$this->output .= $this->html->avatar_gallery_end_table();

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}

 	/*-------------------------------------------------------------------------*/
 	// SHOW AVATAR
 	/*-------------------------------------------------------------------------*/

 	function avatar()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
 		// Organise the dimensions
 		//-----------------------------------------

 		list( $ibforums->member['AVATAR_WIDTH'] , $ibforums->member['AVATAR_HEIGHT']  ) = explode ("x", $ibforums->member['avatar_size']);
 		list( $ibforums->vars['av_width']       , $ibforums->vars['av_height']        ) = explode ("x", $ibforums->vars['avatar_dims']);
 		list( $w, $h ) = explode ( "x", $ibforums->vars['avatar_def'] );

 		//-----------------------------------------
 		// Get the users current avatar to display
 		//-----------------------------------------

 		$my_avatar = $std->get_avatar( $ibforums->member['avatar_location'], 1, $ibforums->member['avatar_size'], $ibforums->member['avatar_type'] );

 		$my_avatar = $my_avatar ? $my_avatar : 'noavatar';

 		//-----------------------------------------
 		// Get the avatar gallery
 		//-----------------------------------------

 		$avatar_gallery = array();
 		$av_categories  = array( 0 => array( "root", $ibforums->lang['av_root'] ) );

 		//-----------------------------------------
 		// Get the avatar categories
 		//-----------------------------------------

 		$dh = opendir( CACHE_PATH.'style_avatars' );

 		while ( $file = readdir( $dh ) )
 		{
			if ( is_dir( CACHE_PATH.'style_avatars'."/".$file ) )
			{
				if ( $file != "." && $file != ".." )
				{
					if ( $file == $av_cat_selected )
					{
						$av_cat_found = TRUE;
					}

					$av_categories[] = array( $file, str_replace( "_", " ", $file ) );
				}
			}
 		}

 		closedir( $dh );

 		usort( $av_categories, array( 'UserCP', 'sort_avatars' ) );
 		reset( $av_categories );

 		//-----------------------------------------
 		// Get the avatar gallery selected
 		//-----------------------------------------

 		$url_avatar = "http://";

 		$avatar_type = "na";

 		if ( ($ibforums->member['avatar_location'] != "") and ($ibforums->member['avatar_location'] != "noavatar") )
 		{
 			if ( ! $ibforums->member['avatar_type'] )
 			{
				if ( preg_match( "/^upload:/", $ibforums->member['avatar'] ) )
				{
					$avatar_type = "upload";
				}
				else if ( ! preg_match( "/^http/i", $ibforums->member['avatar'] ) )
				{
					$avatar_type = "local";
				}
				else
				{
					$url_avatar = $ibforums->member['avatar'];
					$avatar_type = "url";
				}
			}
			else
			{
				switch ($ibforums->member['avatar_type'])
				{
					case 'upload':
						$avatar_type = 'upload';
						break;
					case 'url':
						$avatar_type = 'url';
						$url_avatar  = $ibforums->member['avatar_location'];
						break;
					default:
						$avatar_type = 'local';
						break;
				}
			}
 		}

 		//-----------------------------------------
 		// Render drop down box..
 		//-----------------------------------------

 		$av_gals = "<select name='av_cat' class='forminput'>\n";

 		foreach( $av_categories as $cat )
 		{
 			$av_gals .= "<option value='".$cat[0]."'>".$cat[1]."</option>\n";
 		}

 		$av_gals .= "</select>\n";


 		//-----------------------------------------
 		// Rest of the form..
 		//-----------------------------------------

 		$formextra   = "";
 		$hidden_field = "";

 		if ($ibforums->member['g_avatar_upload'] == 1)
 		{
 			$formextra    = " enctype='multipart/form-data'";
			$hidden_field = "<input type='hidden' name='MAX_FILE_SIZE' value='9000000' />";
		}

 		$this->output .= $this->html->avatar_main( array (
															'MEMBER'               => $this->member,
															'avatar_galleries'     => $av_gals,
															'current_url_avatar'   => $url_avatar,
															'current_avatar_image' => $my_avatar,
															'current_avatar_type'  => $ibforums->lang['av_t_'.$avatar_type],
															'current_avatar_dims'  => $ibforums->member['avatar_size'] == "x" ? "" : $ibforums->member['avatar_size'],
												 )  , $formextra, $hidden_field, $this->md5_check     );

		//-----------------------------------------
 		// Autosizing or manual sizing?
 		//-----------------------------------------

		$size_html = $ibforums->vars['disable_ipbsize'] ? $this->html->avatar_mansize() : $this->html->avatar_autosize();

		//-----------------------------------------
 		// Can we use a URL avatar?
 		//-----------------------------------------

 		if ($ibforums->vars['avatar_url'])
 		{
 			$this->output = str_replace( "<!--IBF.EXTERNAL_TITLE-->",  $this->html->avatar_external_title(), $this->output );
 			$this->output = str_replace( "<!--IBF.URL_AVATAR-->",  $this->html->avatar_url_field($url_avatar), $this->output );
 			$this->output = str_replace( "<!--IPB.SIZE-->", $size_html, $this->output );
 			$ibforums->lang['av_text_url'] = sprintf( $ibforums->lang['av_text_url'], $ibforums->vars['av_width'], $ibforums->vars['av_height'] );
 		}
 		else
 		{
 			$ibforums->lang['av_text_url'] = "";
 		}

 		//-----------------------------------------
 		// Can we use an uploaded avatar?
 		//-----------------------------------------

		if ($ibforums->member['g_avatar_upload'] == 1)
		{
			$this->output = str_replace( "<!--IBF.EXTERNAL_TITLE-->",  $this->html->avatar_external_title(), $this->output );
			$this->output = str_replace( "<!--IBF.UPLOAD_AVATAR-->", $this->html->avatar_upload_field($text), $this->output );
			$this->output = str_replace( "<!--IPB.SIZE-->", $size_html, $this->output );
			$ibforums->lang['av_text_upload'] = sprintf( $ibforums->lang['av_text_upload'], $ibforums->vars['avup_size_max'] );
		}
		else
		{
			$ibforums->lang['av_text_upload'] = "";
		}

		//-----------------------------------------
 		// If yes, show little thingy at top
 		//-----------------------------------------

 		$ibforums->lang['av_allowed_files'] = sprintf($ibforums->lang['av_allowed_files'], implode (' .', explode( "|", $ibforums->vars['avatar_ext'] ) ) );

 		if ( $ibforums->vars['allow_flash'] != 1 )
		{
			$ibforums->lang['av_allowed_files'] = str_replace( ".swf", "", $ibforums->lang['av_allowed_files'] );
		}

		$this->output = str_replace( "<!--IBF.LIMITS_AVATAR-->", $this->html->avatar_limits(), $this->output );


 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}


 	/*-------------------------------------------------------------------------*/
 	// SIGNATURE
 	/*-------------------------------------------------------------------------*/

 	function signature()
 	{
		global $ibforums, $DB, $std, $print;

		$t_sig = $this->parser->unconvert( $ibforums->member['signature'], $ibforums->vars['sig_allow_ibc'], $ibforums->vars['sig_allow_html'] );

		$ibforums->lang['the_max_length'] = $ibforums->vars['max_sig_length'] ? $ibforums->vars['max_sig_length'] : 0;

		if ( $ibforums->vars['sig_allow_html'] == 1 )
		{
			$this->parser->pp_do_html = 1;
		}

 		$ibforums->member['signature'] = $this->parser->post_db_parse($ibforums->member['signature']);

 		$this->output .= $this->html->signature($ibforums->member['signature'], $t_sig, $std->return_md5_check());

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

 	}


 	/*-------------------------------------------------------------------------*/
 	// PROFILE
 	/*-------------------------------------------------------------------------*/

 	function personal()
 	{
		global $ibforums, $DB, $std, $print;

 		//-----------------------------------------
		// Check to make sure that we can edit profiles..
		//-----------------------------------------

		if ( empty($ibforums->member['g_edit_profile']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cant_use_feature' ) );
		}

		//-----------------------------------------
		// Format the birthday drop boxes..
		//-----------------------------------------

		$date = getdate();

		$day  = "<option value='0'>--</option>";
		$mon  = "<option value='0'>--</option>";
		$year = "<option value='0'>--</option>";

		for ( $i = 1 ; $i < 32 ; $i++ )
		{
			$day .= "<option value='$i'";

			$day .= $i == $ibforums->member['bday_day'] ? "selected='selected'>$i</option>" : ">$i</option>";
		}

		for ( $i = 1 ; $i < 13 ; $i++ )
		{
			$mon .= "<option value='$i'";

			$mon .= $i == $ibforums->member['bday_month'] ? "selected='selected'>{$ibforums->lang['month'.$i]}</option>" : ">{$ibforums->lang['month'.$i]}</option>";
		}

		$i = $date['year'] - 1;
		$j = $date['year'] - 100;

		for ( $i ; $j < $i ; $i-- )
		{
			$year .= "<option value='$i'";

			$year .= $i == $ibforums->member['bday_year'] ? "selected='selected'>$i</option>" : ">$i</option>";
		}

		//-----------------------------------------
    	// Get custom profile information
    	//-----------------------------------------

    	$required_output = "";
		$optional_output = "";

    	require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->member_id   = $ibforums->member['id'];
    	$fields->mem_data_id = $ibforums->member['id'];
    	$fields->cache_data  = $ibforums->cache['profilefields'];
    	$fields->admin       = intval($ibforums->member['g_access_cp']);
    	$fields->supmod      = intval($ibforums->member['g_is_supmod']);

    	$fields->init_data();
    	$fields->parse_to_edit();

    	foreach( $fields->out_fields as $id => $data )
    	{
    		if ( $fields->cache_data[ $id ]['pf_not_null'] == 1 )
			{
				$ftype = 'required_output';
			}
			else
			{
				$ftype = 'optional_output';
			}

    		if ( $fields->cache_data[ $id ]['pf_type'] == 'drop' )
			{
				$form_element = $this->html->field_dropdown( 'field_'.$id, $data );
			}
			else if ( $fields->cache_data[ $id ]['pf_type'] == 'area' )
			{
				$form_element = $this->html->field_textarea( 'field_'.$id, $data );
			}
			else
			{
				$form_element = $this->html->field_textinput( 'field_'.$id, $data );
			}

			${$ftype} .= $this->html->field_entry( $fields->field_names[ $id ], $fields->field_desc[ $id ], $form_element );
    	}

		//-----------------------------------------
		// Format the interest / location boxes
		//-----------------------------------------

		$ibforums->member['location']  = $this->parser->unconvert( $ibforums->member['location']  );
 		$ibforums->member['interests'] = $this->parser->unconvert( $ibforums->member['interests'] );

 		$ibforums->member['key']       = $this->md5_check;

		//-----------------------------------------
		// Suck up the HTML and swop some tags if need be
		//-----------------------------------------

		$this->output .= $this->html->personal_panel($ibforums->member);

		if ( ($ibforums->vars['post_titlechange']) and ($ibforums->member['posts'] > $ibforums->vars['post_titlechange']) )
		{
			$t_html = $this->html->member_title($ibforums->member['title']);
			$this->output = preg_replace( "/<!--\{MEMBERTITLE\}-->/", $t_html, $this->output );
		}

		$t_html = $this->html->birthday($day, $mon, $year);

		$this->output = preg_replace( "/<!--\{BIRTHDAY\}-->/", $t_html, $this->output );

		//-----------------------------------------
		// Add in the custom fields if we need to.
		//-----------------------------------------

		if ($required_output != "")
		{
			$this->output = str_replace( "<!--{REQUIRED.FIELDS}-->", $this->html->required_title()."\n".$required_output.$this->html->required_end(), $this->output );
		}

		if ($optional_output != "")
		{
			$this->output = str_replace( "<!--{OPTIONAL.FIELDS}-->", "\n".$optional_output, $this->output );
		}

		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );

	}


 	/*-------------------------------------------------------------------------*/
 	// SPLASH (no, not the movie starring Tom Hanks)
 	/*-------------------------------------------------------------------------*/

 	function splash()
 	{
		global $ibforums, $DB, $std, $print, $forums;

		//-----------------------------------------
		// Format the basic data
		//-----------------------------------------

		$info['member_email']    = $ibforums->member['email'];
		$info['date_registered'] = $std->get_date( $ibforums->member['joined'], 'LONG' );
		$info['member_posts']    = $ibforums->member['posts'];

		$info['daily_average']   = $ibforums->lang['no_posts'];

		if ($ibforums->member['posts'] > 0 )
		{
			$diff = time() - $ibforums->member['joined'];
			$days = ($diff / 3600) / 24;
			$days = $days < 1 ? 1 : $days;
			$info['daily_average']  = sprintf('%.2f', ($ibforums->member['posts'] / $days) );
		}

		//-----------------------------------------
		// Grab the last 5 read topics
		//-----------------------------------------

		$topic_array = array();
		$final_array = array();

		$topics = $std->my_getcookie( 'topicsread' );
		$topics = unserialize(stripslashes( $topics ) );

		$tmp = $ibforums->vars['db_topic_read_cutoff'];
		$ibforums->vars['db_topic_read_cutoff'] = 0;

		if ( is_array( $topics ) and count( $topics ) )
		{
			arsort($topics);

			$topic_array = array_slice( array_keys( $topics ), 0, 5 );

			if ( count( $topic_array ) )
			{
				//-----------------------------------------
				// Grab libraries
				//-----------------------------------------

				require_once( ROOT_PATH."sources/forums.php" );
				$this->forums = new forums();
				$this->forums->init();

				$DB->simple_construct( array( 'select' => '*',
											  'from'   => 'topics',
											  'where'  => 'tid IN ('.implode(",",$topic_array).')',
											  'limit'  => array(0,5) ) );

				$DB->simple_exec();

				while ( $row = $DB->fetch_row() )
				{
					if ( $forums->forum_by_id[ $row['forum_id'] ] )
					{
						$topic = $this->forums->parse_data( $row );
						$final_array[ $row['tid'] ] = $this->forums->html->render_forum_row( $topic );
					}
				}

				foreach( $topic_array as $tid )
				{
					$info['topic_html'] .= $final_array[ $tid ];
				}
			}
		}

 		$ibforums->vars['db_topic_read_cutoff'] = $tmp;

 		//-----------------------------------------
		// Grab the last 5 attachments
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*',
								      'from'   => 'attachments',
									  'where'  => 'attach_member_id='.$ibforums->member['id'],
									  'order'  => 'attach_date desc',
									  'limit'  => array( 0, 5 ) ) );

		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{
			$this->topic_html = $std->load_template( 'skin_topic' );

			$ibforums->lang = $std->load_words($ibforums->lang,'lang_topic', $ibforums->lang_id );

			while ( $row = $DB->fetch_row() )
			{
				//-----------------------------------------
				// Full attachment thingy
				//-----------------------------------------

				$method = $row['attach_msg'] ? 'msg' : 'post';

				$info['attach_html'] .= $this->topic_html->Show_attachments( array (
																			   'hits'  => $row['attach_hits'],
																			   'image' => $ibforums->cache['attachtypes'][ $row['attach_ext'] ]['atype_img'],
																			   'name'  => $row['attach_file'],
																			   'pid'   => $row['attach_pid'],
																			   'id'    => $row['attach_id'],
																			   'method'=> $method,
																			   'size'  => $std->size_format( $row['attach_filesize'] ),
																	 )  	  );
			}
		}

		//-----------------------------------------
		// Write the data..
		//-----------------------------------------

		$s_array = array( 's' => 5 ,
						  'm' => 7 ,
						  'l' => 15
						);

		$info['NOTES'] = $this->notes ? $this->notes : $ibforums->lang['note_pad_empty'];

		$info['SIZE']  = $s_array[$this->size];

		$info['SIZE_CHOICE'] = "";

		//-----------------------------------------
		// If someone has cheated, fix it now.
		//-----------------------------------------

		if ( empty($info['SIZE']) )
		{
			$info['SIZE'] = '5';
		}

		//-----------------------------------------
		// Make the choice HTML.
		//-----------------------------------------

		foreach ($s_array as $k => $v)
		{
			if ($v == $info['SIZE'])
			{
				$info['SIZE_CHOICE'] .= "<option value='$k' selected='selected'>{$ibforums->lang['ta_'.$k]}</option>";
			}
			else
			{
				$info['SIZE_CHOICE'] .= "<option value='$k'>{$ibforums->lang['ta_'.$k]}</option>";
			}
		}

 		$info['NOTES'] = $std->my_br2nl( $info['NOTES'] );

 		$this->output .= $this->html->splash($info);

 		$this->page_title = $ibforums->lang['t_welcome'];
 		$this->nav        = array( "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>" );
 	}






 	/*-------------------------------------------------------------------------*/
 	// UPDATE_NOTEPAD:
 	//
 	// Displays the intro screen
 	/*-------------------------------------------------------------------------*/

 	function update_notepad()
 	{
		global $ibforums, $DB, $std;

 		// Do we have an entry for this member?

 		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}


 		$DB->simple_construct( array( 'select' => 'id', 'from' => 'member_extra', 'where' => "id=".$ibforums->member['id'] ) );
		$DB->simple_exec();

 		if ( $DB->get_num_rows() )
 		{
 			$DB->do_update( 'member_extra', array( 'notes' => $ibforums->input['notes'], 'ta_size' => $ibforums->input['ta_size'] ), 'id='.$ibforums->member['id'] );
 		}
 		else
 		{
 			$DB->do_insert( 'member_extra',  array( 'notes' => $ibforums->input['notes'], 'ta_size' => $ibforums->input['ta_size'], 'id' => $ibforums->member['id'] ) );
 		}

 		$std->boink_it($ibforums->base_url."act=UserCP&CODE=00");
 	}


 	function show_image()
	{
		global $ibforums, $DB, $std;


		if ( $ibforums->input['rc'] == "" )
		{
			return false;
		}

		// Get the info from the db

		$row = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'reg_antispam', 'where' => "regid='".trim(addslashes($ibforums->input['rc']))."'" ) );

		if ( ! $row['regid'] )
		{
			return false;
		}

		//-----------------------------------------
		// Using GD?
		//-----------------------------------------

		if ( $ibforums->vars['bot_antispam'] == 'gd' )
		{
			$std->show_gd_img($row['regcode']);
		}
		else
		{

			//-----------------------------------------
			// Using normal then, check for "p"
			//-----------------------------------------

			if ( $ibforums->input['p'] == "" )
			{
				return false;
			}

			$p = intval($ibforums->input['p']) - 1; //substr starts from 0, not 1 :p

			$this_number = substr( $row['regcode'], $p, 1 );

			$std->show_gif_img($this_number);
		}

	}

}

?>