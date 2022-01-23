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
|   > Warning Module
|   > Module written by Matt Mecham
|   > Date started: 16th May 2003
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class  warn {

    var $output    = "";
    var $topic     = array();
    var $forum     = array();
    var $topic_id  = "";
    var $forum_id  = "";
    var $moderator = "";
    var $modfunc   = "";
    var $mm_data   = "";
    var $parser    = "";

    var $can_ban      = 0;
    var $can_mod_q    = 0;
    var $can_rem_post = 0;
    var $times_a_day  = 0;
    var $type         = 'mod';

    var $warn_member  = "";

    //-----------------------------------------
	// @constructor (no, not bob the builder)
	//-----------------------------------------

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

        //-----------------------------------------
        // Load modules...
        //-----------------------------------------

        $ibforums->lang  = $std->load_words($ibforums->lang, 'lang_mod', $ibforums->lang_id);

 		$this->html      = $std->load_template('skin_mod');

        require( ROOT_PATH.'sources/lib/post_parser.php');

        $this->parser  = new post_parser();

        //-----------------------------------------
        // Make sure we're a moderator...
        //-----------------------------------------

        $pass = 0;

        if ($ibforums->member['id'])
        {
        	if ( $ibforums->member['g_access_cp'] )
			{
				$pass               = 1;
				$this->can_ban      = 1;
    			$this->can_mod_q    = 1;
    			$this->can_rem_post = 1;
    			$this->times_a_day  = -1;
				$this->type = 'admin';
			}
        	else if ($ibforums->member['g_is_supmod'] == 1)
        	{
        		$pass               = 1;
        		$this->can_ban      = $ibforums->vars['warn_gmod_ban'];
    			$this->can_mod_q    = $ibforums->vars['warn_gmod_modq'];
    			$this->can_rem_post = $ibforums->vars['warn_gmod_post'];
    			$this->times_a_day  = intval($ibforums->vars['warn_gmod_day']);
    			$this->type         = 'supmod';
        	}
        	else if ($ibforums->member['is_mod'])
        	{

        		$DB->simple_construct( array( 'select' => '*',
											  'from'   => 'moderators',
											  'where'  => "(member_id='".$ibforums->member['id']."' OR (is_group=1 AND group_id='".$ibforums->member['mgroup']."'))" ) );

				$DB->simple_exec();

				if ( $this->moderator = $DB->fetch_row() )
				{
					if ( $this->moderator['allow_warn'] )
					{
						$pass               = 1;
						$this->can_ban      = $ibforums->vars['warn_mod_ban'];
						$this->can_mod_q    = $ibforums->vars['warn_mod_modq'];
						$this->can_rem_post = $ibforums->vars['warn_mod_post'];
						$this->times_a_day  = intval($ibforums->vars['warn_mod_day']);
						$this->type         = 'mod';
    				}
				}
        	}
        	else if ( $ibforums->vars['warn_show_own'] and $ibforums->member['id'] == $ibforums->input['mid'] )
        	{
        		$pass               = 1;
        		$this->can_ban      = 0;
    			$this->can_mod_q    = 0;
    			$this->can_rem_post = 0;
    			$this->times_a_day  = 0;
    			$this->type         = 'member';
        	}
        	else
        	{
        		$pass = 0;
        	}
        }

        if ($pass == 0)
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_permission') );
        }

        if ( ! $ibforums->vars['warn_on'] )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_permission') );
        }

        //-----------------------------------------
        // Ensure we have a valid member id
        //-----------------------------------------

        $mid = intval($ibforums->input['mid']);

        if ( $mid < 1 )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_such_user') );
        }

        $DB->cache_add_query( 'generic_get_all_member', array( 'mid' => $mid ) );
		$DB->cache_exec_query();

        $this->warn_member = $DB->fetch_row();

        if ( ! $this->warn_member['id'] )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_such_user') );
        }

        if ( $ibforums->input['CODE'] == "" OR $ibforums->input['CODE'] == "dowarn" )
        {
			//-----------------------------------------
			// Protected member? Really? o_O
			//-----------------------------------------

			if ( strstr( ','.$ibforums->vars['warn_protected'].',', ','.$this->warn_member['mgroup'].',' ) )
			{
				$std->Error( array( LEVEL => 1, MSG => 'protected_user') );
			}

			//-----------------------------------------
			// I've already warned you!!
			//-----------------------------------------

			if ( $this->times_a_day > 0 )
			{
				$time_to_check = time() -  86400;

				$DB->simple_construct( array( 'select' => '*', 'from' => 'warn_logs', 'where' => "wlog_mid={$this->warn_member['id']} AND wlog_date > $time_to_check" ) );
				$DB->simple_exec();

				if ( $DB->get_num_rows() >= $this->times_a_day )
				{
					$std->Error( array( LEVEL => 1, MSG => 'warned_already') );
				}
			}
        }

        //-----------------------------------------
        // Bouncy, bouncy!
        //-----------------------------------------

		switch ($ibforums->input['CODE'])
		{
        	case 'dowarn':
        		$this->do_warn();
        		break;

        	case 'view':
        		$this->view_log();
        		break;

        	default:
        		$this->show_form();
        		break;
        }

		if ( count($this->nav) < 1 )
		{
			$this->nav[] = $ibforums->lang['w_title'];
		}

		if (! $this->page_title )
		{
			$this->page_title = $ibforums->lang['w_title'];
		}

    	$print->add_output( $this->output );
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 1, 'NAV' => $this->nav ) );

	}



	//-----------------------------------------
	// Show logs
	//-----------------------------------------

	function view_log()
	{
		global $std, $ibforums, $DB, $print;

		//-----------------------------------------
		// Protected member? Really? o_O
		//-----------------------------------------

		if ( stristr( $ibforums->vars['warn_protected'], ','.$this->warn_member['mgroup'].',' ) )
		{
			$std->Error( array( LEVEL => 1, MSG => 'protected_user') );
		}

		$perpage = 50;

		$start   = intval($ibforums->input['st']);

		$DB->simple_construct( array( 'select' => 'count(*) as cnt', 'from' => 'warn_logs', 'where' => "wlog_mid={$this->warn_member['id']}" ) );
		$DB->simple_exec();

		$row = $DB->fetch_row();

		$links = $std->build_pagelinks( array(
											'TOTAL_POSS'  => $row['cnt'],
											'PER_PAGE'    => $perpage,
											'CUR_ST_VAL'  => $ibforums->input['st'],
											'L_SINGLE'    => "",
											'L_MULTI'     => $ibforums->lang['w_v_pages'],
											'BASE_URL'    => $ibforums->base_url."act=warn&amp;CODE=view&amp;mid={$this->warn_member['id']}",
									  )      );

		$this->output .= $this->html->warn_view_header($this->warn_member['id'], $this->warn_member['name'], $links);

		if ( $row['cnt'] < 1 )
		{
			$this->output .= $this->html->warn_view_none();
		}
		else
		{
			$DB->cache_add_query( 'warn_get_data', array( 'mid' => $this->warn_member['id'], 'limit_a' => $start, 'limit_b' => $perpage ) );
			$DB->cache_exec_query();

			while ( $r = $DB->fetch_row() )
			{
				$date = $std->get_date( $r['wlog_date'], 'LONG' );

				$raw = preg_match( "#<content>(.+?)</content>#is", $r['wlog_notes'], $match );

				$content   = $this->parser->convert( array(
															'TEXT'    => $match[1],
															'SMILIES' => 1,
															'CODE'    => 1,
															'HTML'    => 0
												   )      );

				$puni_name = $std->make_profile_link( $r['punisher_name'], $r['punisher_id'] );

				if ( $r['wlog_type'] == 'pos' )
				{
					$this->output .= $this->html->warn_view_positive_row($date, $content, $puni_name);
				}
				else
				{
					$this->output .= $this->html->warn_view_negative_row($date, $content, $puni_name);
				}

			}
		}


		$this->output .= $this->html->warn_view_footer();

		$print->pop_up_window( "WARN", $this->output );

	}



	//-----------------------------------------
	// Do the actual warny-e-poos
	//-----------------------------------------

	function do_warn()
	{
		global $std, $ibforums, $DB, $print;

		require "./sources/classes/class_email.php";
		$this->email = new emailer();

		$save = array();

		if ( $this->type == 'member' )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_permission') );
        }

        $err = "";

        if ( ! $ibforums->vars['warn_past_max'] )
        {
        	$ibforums->vars['warn_min'] = $ibforums->vars['warn_min'] ? $ibforums->vars['warn_min'] : 0;
        	$ibforums->vars['warn_max'] = $ibforums->vars['warn_max'] ? $ibforums->vars['warn_max'] : 10;

			$warn_level = intval($this->warn_member['warn_level']);

			if ( $ibforums->input['level'] == 'add' )
			{
				if ( $warn_level >= $ibforums->vars['warn_max'] )
				{
					$err = 1;
				}
			}
			else
			{
				if ( $warn_level <= $ibforums->vars['warn_min'] )
				{
					$err = 1;
				}
			}

			if ( $err == 1 )
			{
				$std->Error( array( LEVEL => '1', MSG => 'no_warn_max' ) );
			}
        }

		//-----------------------------------------
		// Check security fang
		//-----------------------------------------

		if ( $ibforums->input['key'] != $std->return_md5_check() )
		{
			$std->Error( array( LEVEL => '1', MSG => 'del_post') );
		}

		//-----------------------------------------
		// As Celine Dion once squawked, "Show me the reason"
		//-----------------------------------------

		if ( trim($ibforums->input['reason']) == "" )
		{
			$this->show_form('we_no_reason');
			return;
		}

		//-----------------------------------------
		// Plussy - minussy?
		//-----------------------------------------

		$save['wlog_type'] = ( $ibforums->input['level'] == 'add' ) ? 'neg' : 'pos';
		$save['wlog_date'] = time();

		//-----------------------------------------
		// Contacting the member?
		//-----------------------------------------

		if ( $ibforums->input['contact'] != "" )
		{
			$save['wlog_contact']         = $ibforums->input['contactmethod'];
			$save['wlog_contact_content'] = "<subject>{$ibforums->input['subject']}</subject><content>{$ibforums->input['contact']}</content>";

			if ( trim($ibforums->input['subject']) == "" )
			{
				$this->show_form('we_no_subject');
				return;
			}

			if ( $ibforums->input['contactmethod'] == 'email' )
			{
				//-----------------------------------------
				// Send the email
				//-----------------------------------------

				$this->email->get_template("email_member");

				$this->email->build_message( array(
													'MESSAGE'     => str_replace( "<br>", "\n", str_replace( "\r", "",  $ibforums->input['contact'] ) ),
													'MEMBER_NAME' => $this->warn_member['name'],
													'FROM_NAME'   => $ibforums->member['name']
												  )
											);

				$this->email->subject = $ibforums->input['subject'];
				$this->email->to      = $this->warn_member['email'];
				$this->email->from    = $ibforums->member['email'];
				$this->email->send_mail();
			}
			else
			{
				//-----------------------------------------
				// PM :o
				//-----------------------------------------

				require_once( ROOT_PATH.'sources/lib/msg_functions.php' );

 				$this->lib = new msg_functions();
 				$this->lib->init();

				$this->lib->to_by_id    = $this->warn_member['id'];
 				$this->lib->from_member = $ibforums->member;
 				$this->lib->msg_title   = $ibforums->input['subject'];
 				$this->lib->msg_post    = $std->remove_tags($ibforums->input['contact']);
				$this->lib->force_pm    = 1;

				$this->lib->send_pm();

				if ( $this->lib->error )
				{
					print $this->error;
					exit();
				}
			}
		}

		//-----------------------------------------
		// Right - is we banned or wha?
		//-----------------------------------------

		$restrict_post = '';
		$mod_queue     = '';
		$susp          = '';

		$save['wlog_notes']  = "<content>{$ibforums->input['reason']}</content>";
		$save['wlog_notes'] .= "<mod>{$ibforums->input['mod_value']},{$ibforums->input['mod_unit']},{$ibforums->input['mod_indef']}</mod>";
		$save['wlog_notes'] .= "<post>{$ibforums->input['post_value']},{$ibforums->input['post_unit']},{$ibforums->input['post_indef']} </post>";
		$save['wlog_notes'] .= "<susp>{$ibforums->input['susp_value']},{$ibforums->input['susp_unit']}</susp>";

		if ( $ibforums->input['mod_indef'] == 1 )
		{
			$mod_queue = 1;
		}
		elseif ( $ibforums->input['mod_value'] > 0 )
		{
			$mod_queue = $std->hdl_ban_line( array( 'timespan' => intval($ibforums->input['mod_value']), 'unit' => $ibforums->input['mod_unit']  ) );
		}


		if ( $ibforums->input['post_indef'] == 1 )
		{
			$restrict_post = 1;
		}
		elseif ( $ibforums->input['post_value'] > 0 )
		{
			$restrict_post = $std->hdl_ban_line( array( 'timespan' => intval($ibforums->input['post_value']), 'unit' => $ibforums->input['post_unit']  ) );
		}

		if ( $ibforums->input['susp_value'] > 0 )
		{
			$susp = $std->hdl_ban_line( array( 'timespan' => intval($ibforums->input['susp_value']), 'unit' => $ibforums->input['susp_unit']  ) );
		}

		$save['wlog_mid']     = $this->warn_member['id'];
		$save['wlog_addedby'] = $ibforums->member['id'];

		//-----------------------------------------
		// Enter into warn loggy poos (eeew - poo)
		//-----------------------------------------

		$DB->do_insert( 'warn_logs', $save );

		//-----------------------------------------
		// Update member
		//-----------------------------------------

		$warn_level = intval($this->warn_member['warn_level']);

		if ( $ibforums->input['level'] == 'add' )
		{
			$warn_level++;
		}
		else
		{
			$warn_level--;
		}

		if ( $warn_level > $ibforums->vars['warn_max'] )
		{
			$warn_level = $ibforums->vars['warn_max'];
		}

		if ( $warn_level < intval($ibforums->vars['warn_min']) )
		{
			$warn_level = 0;
		}

		$DB->do_update( 'members', array (
										  'mod_posts'     => $mod_queue,
										  'restrict_post' => $restrict_post,
										  'temp_ban'      => $susp,
										  'warn_level'    => $warn_level,
										  'warn_lastwarn' => time(),
					  ) , "id={$this->warn_member['id']}"  );

		//-----------------------------------------
		// Now what? Show success screen, that's what!!
		//-----------------------------------------

		$ibforums->lang['w_done_te'] = sprintf( $ibforums->lang['w_done_te'], $this->warn_member['name'] );

		$this->output .= $this->html->warn_success();

		// Did we have a topic? eh! eh!! EH!

		$tid = intval($ibforums->input['t']);

		if ( $tid > 0 )
		{
			$DB->cache_add_query( 'warn_get_forum', array( 'tid' => $tid ) );
			$DB->cache_exec_query();

			$topic = $DB->fetch_row();

			$this->output = str_replace( "<!--IBF.FORUM_TOPIC-->", $this->html->warn_success_forum( $topic['id'], $topic['name'], $topic['tid'], $topic['title'], intval($ibforums->input['st']) ), $this->output );
		}
	}

	//-----------------------------------------
	// Show form
	//-----------------------------------------

	function show_form($errors="")
	{
		global $std, $ibforums, $DB, $print;

		if ( $this->type == 'member' )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_permission') );
        }

		$key = $std->return_md5_check();

		if ( $errors != "" )
		{
			$this->output .= $this->html->warn_errors($ibforums->lang[$errors]);
		}

		$type = array( 'minus' => "", 'add' => "" );

		if ( $ibforums->input['type'] == 'minus' )
		{
			$type['minus'] = 'checked="checked"';
		}
		else
		{
			$type['add'] = 'checked="checked"';
		}

		$this->output .= $this->html->warn_header(
													$this->warn_member['id'],
													$this->warn_member['name'],
													intval($this->warn_member['warn_level']),
													$ibforums->vars['warn_min'],
													$ibforums->vars['warn_max'],
													$key,
													intval($ibforums->input['t']),
													intval($ibforums->input['st']),
													$type
												 );

		if ( $this->can_mod_q )
		{
			$mod_tick = 0;
			$mod_arr  = array();

			if ( $this->warn_member['mod_posts'] == 1 )
			{
				$mod_tick = 'checked';
			}
			elseif ($this->warn_member['mod_posts'] > 0)
			{
				$mod_arr = $std->hdl_ban_line($this->warn_member['mod_posts'] );

				$hours  = ceil( ( $mod_arr['date_end'] - time() ) / 3600 );

				if ( $hours > 24 and ( ($hours / 24) == ceil($hours / 24) ) )
				{
					$mod_arr['days']     = 'selected="selected"';
					$mod_arr['timespan'] = $hours / 24;
				}
				else
				{
					$mod_arr['hours']    = 'selected="selected"';
					$mod_arr['timespan'] = $hours;
				}

				$mod_extra = $this->html->warn_restricition_in_place();
			}

			$this->output .= $this->html->warn_mod_posts($mod_tick, $mod_arr, $mod_extra);
		}

		if ( $this->can_rem_post )
		{

			$post_tick = 0;
			$post_arr  = array();

			if ( $this->warn_member['restrict_post'] == 1 )
			{
				$post_tick = 'checked';
			}
			else if ( $this->warn_member['restrict_post'] > 0 )
			{
				$post_arr = $std->hdl_ban_line( $this->warn_member['restrict_post'] );

				$hours  = ceil( ( $post_arr['date_end'] - time() ) / 3600 );

				if ( $hours > 24 and ( ($hours / 24) == ceil($hours / 24) ) )
				{
					$post_arr['days']     = 'selected="selected"';
					$post_arr['timespan'] = $hours / 24;
				}
				else
				{
					$post_arr['hours']    = 'selected="selected"';
					$post_arr['timespan'] = $hours;
				}

				$post_extra = $this->html->warn_restricition_in_place();
			}

			$this->output .= $this->html->warn_rem_posts($post_tick, $post_arr, $post_extra);

		}

		if ( $this->can_ban )
		{
			$ban_arr  = array();

			if ( $this->warn_member['temp_ban'] )
			{
				$ban_arr = $std->hdl_ban_line( $this->warn_member['temp_ban'] );

				$hours  = ceil( ( $ban_arr['date_end'] - time() ) / 3600 );

				if ( $hours > 24 and ( ($hours / 24) == ceil($hours / 24) ) )
				{
					$ban_arr['days']     = 'selected="selected"';
					$ban_arr['timespan'] = $hours / 24;
				}
				else
				{
					$ban_arr['hours']    = 'selected="selected"';
					$ban_arr['timespan'] = $hours;
				}

				$post_extra = $this->html->warn_restricition_in_place();
			}

			$this->output .= $this->html->warn_suspend($ban_arr, $ban_extra);

		}

		$this->output .= $this->html->warn_footer();

	}



}

?>