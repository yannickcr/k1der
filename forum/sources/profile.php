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
|   > User Profile functions
|   > Module written by Matt Mecham
|   > Date started: 28th February 2002
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

class profile
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

    var $show_photo = "";
    var $show_width = "";
    var $show_height = "";
    var $show_name  = "";

    var $photo_member = "";

    var $has_photo   = FALSE;

    var $lib;

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	require ROOT_PATH."sources/lib/post_parser.php";

        $this->parser = new post_parser();
        $this->parser->check_caches();

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

    	$ibforums->lang = $std->load_words($ibforums->lang, 'lang_profile'  , $ibforums->lang_id );

    	$this->html = $std->load_template('skin_profile');

    	$ibforums->base_url_nosess = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}";

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['CODE'])
    	{
    		case '03':
    			$this->view_profile();
    			break;

    		case 'showphoto':
    			$this->show_photo();
    			break;

    		case 'showcard':
    			$this->show_card();

    		//-----------------------------------------
    		default:
    			$this->view_profile();
    			break;
    	}

    	// If we have any HTML to print, do so...

    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 1, NAV => $this->nav ) );
 	}

 	/*-------------------------------------------------------------------------*/
 	// VIEW CONTACT CARD:
 	/*-------------------------------------------------------------------------*/

 	function show_card()
 	{
		global $ibforums, $DB, $std, $print;

 		$info = array();

 		if ($ibforums->member['g_mem_info'] != 1)
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    	}

 		//-----------------------------------------
    	// Check input..
    	//-----------------------------------------

    	$id = intval($ibforums->input['MID']);

    	if ( empty($id) )
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'incorrect_use' ) );
    	}

    	$DB->cache_add_query( 'generic_get_all_member', array( 'mid' => $id ) );
		$DB->cache_exec_query();

    	$member = $DB->fetch_row();

    	$member['website'] = $member['website'] == 'http://' ? '' : $member['website'];

    	$info['aim_name']    = $member['aim_name']   ? $member['aim_name']   : $ibforums->lang['no_info'];
    	$info['icq_number']  = $member['icq_number'] ? $member['icq_number'] : $ibforums->lang['no_info'];
    	$info['yahoo']       = $member['yahoo']      ? $member['yahoo']      : $ibforums->lang['no_info'];
    	$info['location']    = $member['location']   ? $member['location']   : $ibforums->lang['no_info'];
    	$info['interests']   = $member['interests']  ? $member['interests']  : $ibforums->lang['no_info'];
    	$info['msn_name']    = $member['msnname']    ? $member['msnname']    : $ibforums->lang['no_info'];
    	$info['website']     = $member['website']    ? "<a href='{$member['website']}' target='_blank'>{$member['website']}</a>" : $ibforums->lang['no_info'];
    	$info['mid']         = $member['id'];
    	$info['has_blog']    = $member['has_blog'];

    	if (!$member['hide_email'])
    	{
			$info['email'] = "<a href='javascript:redirect_to(\"&amp;act=Mail&amp;CODE=00&amp;MID={$member['id']}\",1);'>{$ibforums->lang['click_here']}</a>";
		}
		else
		{
			$info['email'] = $ibforums->lang['private'];
		}

    	$this->load_photo($id);

    	if ( $this->has_photo == TRUE )
    	{
    		$photo = $this->html->get_photo( $this->show_photo, $this->show_width, $this->show_height );
    	}
    	else
    	{
    		$photo = "<{NO_PHOTO}>";
    	}

    	if ($ibforums->input['download'] == 1)
    	{
    		$photo = str_replace( "<{NO_PHOTO}>", "No Photo Available", $photo );
    		$html  = $this->html->show_card_download( $member['name'], $photo, $info );
    		$html  = str_replace( "<!--CSS-->", $ibforums->skin['_css'], $html );

    		//-----------------------------------------
    		// Macros
    		//-----------------------------------------

    		$macros = unserialize(stripslashes($ibforums->skin['_macro']));

    		if ( is_array( $macros ) )
			{
				foreach( $macros as $i => $row )
				{
					if ($row['macro_value'] != "")
					{
						$html = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $html );
					}
				}
			}

			//-----------------------------------------
			// Images
			//-----------------------------------------

			$html = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $html );

    		if ( ! $ibforums->vars['ipb_img_url'] )
			{
				$ibforums->vars['ipb_img_url'] = preg_replace( "#/$#", "", $ibforums->vars['board_url'] ) . '/';
			}

			$html = preg_replace( "#img\s+?src=[\"']style_(images|avatars|emoticons)(.+?)[\"'](.+?)?".">#is", "img src=\"".$ibforums->vars['ipb_img_url']."style_\\1\\2\"\\3>", $html );

    		//-----------------------------------------
    		// Download
    		//-----------------------------------------

			@header("Content-type: unknown/unknown");
			@header("Content-Disposition: attachment; filename={$member['name']}.html");
			print $html;
			exit();
    	}
    	else
    	{
			$html  = $this->html->show_card( $member['name'], $photo, $info );

			$print->pop_up_window( $ibforums->lang['photo_title'], $html );
    	}
    }

 	/*-------------------------------------------------------------------------*/
 	// VIEW PHOTO:
 	/*-------------------------------------------------------------------------*/

 	function show_photo()
 	{
		global $ibforums, $DB, $std, $print;

 		$info = array();

 		if ($ibforums->member['g_mem_info'] != 1)
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    	}

 		//-----------------------------------------
    	// Check input..
    	//-----------------------------------------

    	$id = intval($ibforums->input['MID']);

    	if ( empty($id) )
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'incorrect_use' ) );
    	}

    	$this->load_photo($id);

    	if ( $this->has_photo == TRUE )
    	{
    		$photo = $this->html->get_photo( $this->show_photo, $this->show_width, $this->show_height );
    	}
    	else
    	{
    		$photo = "<{NO_PHOTO}>";
    	}

    	$html  = $this->html->show_photo( $this->photo_member['name'], $photo );

    	$print->pop_up_window( $ibforums->lang['photo_title'], $html );
    }

    /*-------------------------------------------------------------------------*/
 	// FUNC: RETURN PHOTO
 	/*-------------------------------------------------------------------------*/

    function load_photo($id, $member=array())
    {
		global $ibforums, $DB, $std, $print;

    	$this->show_photo  = "";
    	$this->show_height = "";
    	$this->show_width  = "";

    	if ( ! isset( $member['photo_type'] ) )
    	{
			$DB->cache_add_query( 'profile_get_all', array( 'mid' => $id ) );

    		$DB->cache_exec_query();

			$this->photo_member = $DB->fetch_row();
    	}
    	else
    	{
    		$this->photo_member = $member;
    	}

    	if ( $this->photo_member['photo_type'] and $this->photo_member['photo_location'] )
    	{
    		$this->has_photo = TRUE;

    		list( $show_width, $show_height ) = explode( ",", $this->photo_member['photo_dimensions'] );

    		if ($this->photo_member['photo_type'] == 'url')
    		{
    			$this->show_photo = $this->photo_member['photo_location'];
    		}
    		else
    		{
    			$this->show_photo = $ibforums->vars['upload_url']."/".$this->photo_member['photo_location'];
    		}

    		if ( $show_width > 0 )
    		{
    			$this->show_width = "width='$show_width'";
    		}

    		if ( $show_height > 0 )
    		{
    			$this->show_height = "height='$show_height'";
    		}
    	}
    }

 	/*-------------------------------------------------------------------------*/
 	// VIEW MAIN PROFILE:
 	/*-------------------------------------------------------------------------*/

 	function view_profile()
 	{
		global $ibforums, $DB, $std, $print, $forums;

 		$this->topic_html = $std->load_template('skin_topic');

 		$info = array();

 		if ($ibforums->member['g_mem_info'] != 1)
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    	}

 		//-----------------------------------------
    	// Check input..
    	//-----------------------------------------

    	$id = intval($ibforums->input['MID']);

    	if ( ! $id )
    	{
    		$std->boink_it( $ibforums->base_url );
    	}

    	//-----------------------------------------
    	// Get all member information
    	//-----------------------------------------

    	$DB->cache_add_query( 'profile_get_all', array( 'mid' => $id ) );

    	$DB->cache_exec_query();

    	$member = $DB->fetch_row();

    	if ( empty( $member['id'] ) )
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'incorrect_use' ) );
    	}

    	//-----------------------------------------
    	// Play it safe
    	//-----------------------------------------

    	$member['password'] = "";

    	//-----------------------------------------
    	// Most posted forum
    	//-----------------------------------------

    	$forum_ids = array('0');

    	foreach( $forums->forum_by_id as $i => $r )
    	{
    		if ( $std->check_perms($r['read_perms']) == TRUE )
    		{
    			$forum_ids[] = $r['id'];
    		}
    	}

    	$DB->cache_add_query( 'profile_get_favourite', array( 'mid' => $member['id'], 'fid_array' => $forum_ids ) );

    	$DB->cache_exec_query();

    	$favourite   = $DB->fetch_row();

    	//-----------------------------------------
    	// Post count stats
    	//-----------------------------------------

    	$percent = 0;

    	$DB->simple_construct( array( 'select' => 'COUNT(*) as total_posts',
    								  'from'   => 'posts',
    								  'where'  => "author_id={$member['id']}" ) );
		$DB->simple_exec();

    	$total_posts = $DB->fetch_row();

    	$board_posts = $ibforums->cache['stats']['total_topics'] + $ibforums->cache['stats']['total_replies'];

    	if ($total_posts['total_posts'] > 0)
    	{
    		$percent = round( $favourite['f_posts'] / $total_posts['total_posts'] * 100 );
    	}

    	if ($member['posts'] and $board_posts)
    	{
    		$info['posts_day'] = round( $member['posts'] / (((time() - $member['joined']) / 86400)), 1);
    		$info['total_pct'] = sprintf( '%.2f', ( $member['posts'] / $board_posts * 100 ) );
    	}

    	if ($info['posts_day'] > $member['posts'])
    	{
    		$info['posts_day'] = $member['posts'];
    	}

    	//-----------------------------------------
    	// Pips / Icon
    	//-----------------------------------------

    	$pips = 0;

		foreach($ibforums->cache['ranks'] as $k => $v)
		{
			if ($member['posts'] >= $v['POSTS'])
			{
				if (!$member['title'])
				{
					$member['title'] = $ibforums->cache['ranks'][ $k ]['TITLE'];
				}

				$pips = $v['PIPS'];
				break;
			}
		}

		if ($ibforums->cache['group_cache'][ $member['mgroup'] ]['g_icon'])
		{
			$member['member_rank_img'] = $this->topic_html->member_rank_img($ibforums->cache['group_cache'][ $member['mgroup'] ]['g_icon']);
		}
		else if ($pips)
		{
			if ( is_numeric( $pips ) )
			{
				for ($i = 1; $i <= $pips; ++$i)
				{
					$member['member_rank_img'] .= "<{A_STAR}>";
				}
			}
			else
			{
				$member['member_rank_img'] = $this->topic_html->member_rank_img('style_images/<#IMG_DIR#>/folder_team_icons/'.$pips);
			}
		}

    	//-----------------------------------------
    	// More info...
    	//-----------------------------------------

    	$info['posts']       = $member['posts'] ? $member['posts'] : 0;
    	$info['name']        = $member['name'];
    	$info['mid']         = $member['id'];
    	$info['fav_forum']   = $ibforums->cache['forum_cache'][ $favourite['forum_id'] ]['name'];
    	$info['fav_id']      = $favourite['forum_id'];
    	$info['fav_posts']   = $favourite['f_posts'];
    	$info['percent']     = $percent;
    	$info['group_title'] = $ibforums->cache['group_cache'][ $member['mgroup'] ]['g_title'];
    	$info['board_posts'] = $board_posts;
    	$info['joined']      = $std->get_date( $member['joined'], 'JOINED' );
    	$info['last_active'] = $std->get_date( $member['last_activity'], 'SHORT' );

    	$info['member_title'] = $member['title']     ? $member['title']      : $ibforums->lang['no_info'];

    	$info['aim_name']        = $member['aim_name']   ? $member['aim_name']   : $ibforums->lang['no_info'];
    	$info['icq_number']      = $member['icq_number'] ? $member['icq_number'] : $ibforums->lang['no_info'];
    	$info['yahoo']           = $member['yahoo']      ? $member['yahoo']      : $ibforums->lang['no_info'];
    	$info['location']        = $member['location']   ? $member['location']   : $ibforums->lang['no_info'];
    	$info['interests']       = $member['interests']  ? $member['interests']  : $ibforums->lang['no_info'];
    	$info['msn_name']        = $member['msnname']    ? $member['msnname']    : $ibforums->lang['no_info'];
    	$info['member_rank_img'] = $member['member_rank_img'];
    	$info['has_blog']        = $member['has_blog'];

    	//-----------------------------------------
		// Online, offline?
		//-----------------------------------------

		$time_limit = time() - $ibforums->vars['au_cutoff'] * 60;

		$info['online_status_indicator'] = '<{PB_USER_OFFLINE}>';
		$info['online_extra']            = '('.$ibforums->lang['online_offline'].')';

		list( $be_anon, $loggedin ) = explode( '&', $member['login_anonymous'] );

		if ( ( $member['last_visit'] > $time_limit or $member['last_activity'] > $time_limit ) AND $be_anon != 1 AND $loggedin == 1 )
		{
			$info['online_status_indicator'] = '<{PB_USER_ONLINE}>';

			//-----------------------------------------
			// Where?
			//-----------------------------------------

			$where = "";

			if ( $member['in_topic'] )
			{
				$topic = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.$member['in_topic'] ) );

				if ( $topic['tid'] )
				{
					if ( $std->check_perms($ibforums->cache['forum_cache'][ $topic['forum_id'] ]['read_perms']) == TRUE )
					{
						$where = $ibforums->lang['wol_topic'].': '."<a href='{$ibforums->base_url}showtopic={$topic['tid']}'>{$topic['title']}</a>";
					}
				}
			}
			else if ( $member['in_forum'] )
			{
				if ( $std->check_perms($ibforums->cache['forum_cache'][ $member['in_forum'] ]['read_perms']) == TRUE )
				{
					$where = $ibforums->lang['wol_forum'].' '.$ibforums->cache['forum_cache'][ $member['in_forum'] ]['name'];
				}
			}
			else if ( strstr( strtolower($member['sesslocation']), 'usercp' ) or strstr( strtolower($member['sesslocation']), 'msg' ) )
			{
				$where = $ibforums->lang['wol_ucp'];
			}
			else if ( strstr( strtolower($member['sesslocation']), 'profile' )  )
			{
				$where = $ibforums->lang['wol_profile'];
			}
			else if ( strstr( strtolower($member['sesslocation']), 'search' )  )
			{
				$where = $ibforums->lang['wol_search'];
			}

			if ( ! $where )
			{
				$where = $ibforums->lang['wol_index'];
			}

			$info['online_extra'] = '('.$where.')';
		}

    	//-----------------------------------------
    	// Time...
    	//-----------------------------------------

    	$ibforums->vars['time_adjust'] = $ibforums->vars['time_adjust'] == "" ? 0 : $ibforums->vars['time_adjust'];

    	if ($member['dst_in_use'] == 1)
    	{
    		$member['time_offset'] += 1;
    	}

    	$info['local_time']  = $member['time_offset'] != "" ? gmdate( $ibforums->vars['clock_long'], time() + ($member['time_offset']*3600) + ($ibforums->vars['time_adjust'] * 60) ) : $ibforums->lang['no_info'];

    	$info['avatar']      = $std->get_avatar( $member['avatar_location'] , 1, $member['avatar_size'], $member['avatar_type'] );

    	//-----------------------------------------
    	// Siggy
    	//-----------------------------------------

    	$info['signature']   = $member['signature'];

    	$this->parser->pp_do_html = intval($ibforums->vars['sig_allow_html']);

		$info['signature'] = $this->parser->post_db_parse($info['signature']);

    	//-----------------------------------------
    	// site
    	//-----------------------------------------

    	if ( $member['website'] and preg_match( "/^http:\/\/\S+$/", $member['website'] ) )
    	{
			$info['homepage'] = "<a href='{$member['website']}' target='_blank'>{$member['website']}</a>";
		}
		else
		{
			$info['homepage'] = $ibforums->lang['no_info'];
		}

    	//-----------------------------------------
    	// Birthday
    	//-----------------------------------------

    	if ($member['bday_month'])
    	{
    		$info['birthday'] = $member['bday_day']." ".$ibforums->lang[ 'M_'.$member['bday_month'] ]." ".$member['bday_year'];
    	}
    	else
    	{
    		$info['birthday'] = $ibforums->lang['no_info'];
    	}

    	//-----------------------------------------
    	// Email
    	//-----------------------------------------

    	if ( ! $member['hide_email'] )
    	{
			$info['email'] = "<a href='{$ibforums->base_url}act=Mail&amp;CODE=00&amp;MID={$member['id']}'>{$ibforums->lang['email']}</a>";
		}
		else
		{
			$info['email'] = $ibforums->lang['private'];
		}

		//-----------------------------------------
		// Get photo and show profile:
		//-----------------------------------------

		$this->load_photo( $member['id'], $member );

		if ( $this->has_photo == TRUE )
    	{
    		$info['photo'] = $this->html->get_photo( $this->show_photo, $this->show_width, $this->show_height );
    	}
    	else
    	{
    		$info['photo'] = "";
    	}

    	$info['base_url'] = $ibforums->base_url;

    	$info['posts'] = $std->do_number_format($info['posts']);

    	//-----------------------------------------
    	// Output
    	//-----------------------------------------

    	$this->output .= $this->html->show_profile( $info, $std->return_md5_check() );

    	//-----------------------------------------
    	// Is this our profile?
    	//-----------------------------------------

    	if ($member['id'] == $ibforums->member['id'])
    	{
    		$this->output = preg_replace( "/<!--MEM OPTIONS-->/", $this->html->user_edit($info), $this->output );
    	}

        //-----------------------------------------
    	// Get custom profile information
    	//-----------------------------------------

    	require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->member_id   = $ibforums->member['id'];
    	$fields->mem_data_id = $member['id'];
    	$fields->cache_data  = $ibforums->cache['profilefields'];
    	$fields->admin       = intval($ibforums->member['g_access_cp']);
    	$fields->supmod      = intval($ibforums->member['g_is_supmod']);

    	$fields->init_data();
    	$fields->parse_to_view();

    	foreach( $fields->out_fields as $id => $data )
    	{
    		if ( ! $data )
    		{
    			$data = $ibforums->lang['no_info'];
    		}

			$custom_out .= $this->html->custom_field( $fields->field_names[ $id ], nl2br($data) );
    	}

    	if ($custom_out != "")
    	{
    		$this->output = str_replace( "<!--{CUSTOM.FIELDS}-->", $custom_out, $this->output );
    	}
    	else
    	{
    		$this->output = str_replace( "<!--{CUSTOM.FIELDS}-->", $this->html->no_custom_information(), $this->output );
    	}

    	//-----------------------------------------
    	// Warning stuff!!
    	//-----------------------------------------

    	$pass = 0;
    	$mod  = 0;

    	if ( $ibforums->vars['warn_on'] and ( ! stristr( $ibforums->vars['warn_protected'], ','.$member['mgroup'].',' ) ) )
		{
			if ($ibforums->member['id'])
			{
				if ( $ibforums->member['g_is_supmod'] == 1 )
				{
					$pass = 1;
					$mod  = 1;
				}

				if ( $pass == 0 and ( $ibforums->vars['warn_show_own'] and ( $member['id'] == $ibforums->member['id'] ) ) )
				{
					$pass = 1;
				}

				if ( $pass == 1 )
				{
					// Work out which image to show.

					if ( ! $ibforums->vars['warn_show_rating'] )
					{
						if ( $member['warn_level'] < 1 )
						{
							$member['warn_img'] = '<{WARN_0}>';
						}
						else if ( $member['warn_level'] >= $ibforums->vars['warn_max'] )
						{
							$member['warn_img']     = '<{WARN_5}>';
							$member['warn_percent'] = 100;
						}
						else
						{
							$member['warn_percent'] = $member['warn_level'] ? sprintf( "%.0f", ( ($member['warn_level'] / $ibforums->vars['warn_max']) * 100) ) : 0;

							if ( $member['warn_percent'] > 100 )
							{
								$member['warn_percent'] = 100;
							}

							if ( $member['warn_percent'] >= 81 )
							{
								$member['warn_img'] = '<{WARN_5}>';
							}
							else if ( $member['warn_percent'] >= 61 )
							{
								$member['warn_img'] = '<{WARN_4}>';
							}
							else if ( $member['warn_percent'] >= 41 )
							{
								$member['warn_img'] = '<{WARN_3}>';
							}
							else if ( $member['warn_percent'] >= 21 )
							{
								$member['warn_img'] = '<{WARN_2}>';
							}
							else if ( $member['warn_percent'] >= 1 )
							{
								$member['warn_img'] = '<{WARN_1}>';
							}
							else
							{
								$member['warn_img'] = '<{WARN_0}>';
							}
						}

						if ( $member['warn_percent'] < 1 )
						{
							$member['warn_percent'] = 0;
						}

						if ( $mod == 1 )
						{
							$this->output = str_replace( "<!--{WARN_LEVEL}-->", $this->html->warn_level($member['id'], $member['warn_img'], $member['warn_percent']), $this->output );
						}
						else
						{
							$this->output = str_replace( "<!--{WARN_LEVEL}-->", $this->html->warn_level_no_mod($member['id'], $member['warn_img'], $member['warn_percent']), $this->output );
						}
					}
					else
					{
						// Rating mode:

						if ( $mod == 1 )
						{
							$this->output = str_replace( "<!--{WARN_LEVEL}-->", $this->html->warn_level_rating($member['id'], $member['warn_level'], $ibforums->vars['warn_min'], $ibforums->vars['warn_max']), $this->output );
						}
						else
						{
							$this->output = str_replace( "<!--{WARN_LEVEL}-->", $this->html->warn_level_rating_no_mod($member['id'], $member['warn_level'], $ibforums->vars['warn_min'], $ibforums->vars['warn_max']), $this->output );
						}
					}
				}
			}
    	}

 		$this->page_title = $ibforums->lang['page_title'];
 		$this->nav        = array( $ibforums->lang['page_title'] );
 	}



}

?>