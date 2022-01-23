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
|   > FORUMS CLASS
|   > Module written by Matt Mecham
|   > Date started: 26th January 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
+--------------------------------------------------------------------------
*/

class forum_functions
{

	var $forum_cache   = array();
	var $forum_by_id   = array();
	var $forum_built   = array();
	var $class         = "";
	var $template_bit  = "";
	var $depth_guide   = "--";
	var $return        = "";
	var $this_forum    = array();
	var $strip_invisible = 0;
	var $mod_cache     = array();
	var $mod_cache_got = 0;
	var $read_topic_only = 0;

	/*-------------------------------------------------------------------------*/
	// register_class
	// ------------------
	// Register a $this-> class with this module
	/*-------------------------------------------------------------------------*/

	function register_class(&$class)
	{
		$this->class = $class;
	}


	/*-------------------------------------------------------------------------*/
	// forums_init
	// ------------------
	// Grab all forums and stuff into array
	/*-------------------------------------------------------------------------*/

	function forums_init()
	{
		global $DB, $std, $ibforums;

		if ( ! is_array( $ibforums->cache['forum_cache'] ) )
		{
			$std->update_forum_cache();
		}

		$hide_parents = ',';

		foreach( $ibforums->cache['forum_cache'] as $i => $f )
		{
			if ( $this->strip_invisible )
			{
				if ( strstr( $hide_parents, ','. $f['parent_id'] .',' ) )
				{
					// Don't show any children of hidden parents
					$hide_parents .= $f['id'].',';
					continue;
				}

				//if ( $f['status'] < 1 )
				//{
				//	$hide_parents .= $f['id'].',';
				//	continue;
				//}

				if ( $f['show_perms'] != '*' )
				{
					if ( $std->check_perms($f['show_perms']) != TRUE )
					{
						$hide_parents .= $f['id'].',';
						continue;
					}
				}
			}

			if ( $f['parent_id'] < 1 )
			{
				$f['parent_id'] = 'root';
			}

			$f['fid'] = $f['id'];

			$this->forum_cache[ $f['parent_id'] ][ $f['id'] ] = $f;
			$this->forum_by_id[ $f['id'] ] = &$this->forum_cache[ $f['parent_id'] ][ $f['id'] ];
		}

	}


	function forums_remove_childless_parents()
	{
		foreach ( $this->forum_cache['root'] as $id => $forum_data )
		{
			if ( ! is_array( $this->forum_cache['root'][ $forum_data['id'] ] ) )
			{
				unset( $this->forum_cache['root'][ $forum_data['id'] ] );
				unset( $this->forum_by_id[ $forum_data['id'] ] );
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// forums_get_moderator_cache
	// ------------------
	// Grab all mods innit
	/*-------------------------------------------------------------------------*/

	function forums_get_moderator_cache()
	{
		global $DB, $ibforums, $std;

		$this->can_see_queued = array();

		if ( ! is_array( $ibforums->cache['moderators'] ) )
		{
			$ibforums->cache['moderators'] = array();

			$DB->simple_construct( array( 'select' => "*",
										  'from'   => 'moderators'
								 )      );

			$DB->simple_exec();

			while ( $i = $DB->fetch_row() )
			{
				$ibforums->cache['moderators'][ $i['mid'] ] = $i;
			}

			$std->update_cache( array( 'name' => 'moderators', 'array' => 1, 'deletefirst' => 1 ) );
		}

		if ( count($ibforums->cache['moderators']) )
		{
			foreach( $ibforums->cache['moderators'] as $i => $r )
			{
				$this->mod_cache[ $r['forum_id'] ][ $r['mid'] ] = array( 'name'  => $r['member_name'],
																		 'memid' => $r['member_id'],
																		 'id'    => $r['mid'],
																		 'isg'   => $r['is_group'],
																		 'gname' => $r['group_name'],
																		 'gid'   => $r['group_id'],
																	   );
			}
		}

		$this->mod_cache_got = 1;
	}


	/*-------------------------------------------------------------------------*/
	// forums_get_moderators
	// ------------------
	// Grab all mods innit
	/*-------------------------------------------------------------------------*/

	function forums_get_moderators($forum_id="")
	{
		global $DB, $ibforums;

		if ( ! $this->mod_cache_got )
		{
			$this->forums_get_moderator_cache();
		}

		$mod_string = "";

		if ($forum_id == "")
		{
			return "";
		}

		if (isset($this->mod_cache[ $forum_id ] ) )
		{
			$mod_string = $ibforums->lang['forum_leader'].' ';

			if (is_array($this->mod_cache[ $forum_id ]) )
			{
				foreach ($this->mod_cache[ $forum_id ] as $moderator)
				{
					if ($moderator['isg'] == 1)
					{
						$mod_string .= "<a href='{$ibforums->base_url}act=Members&amp;max_results=30&amp;filter={$moderator['gid']}&amp;sort_order=asc&amp;sort_key=name&amp;st=0&amp;b=1'>{$moderator['gname']}</a>, ";
					}
					else
					{
						$mod_string .= "<a href='{$ibforums->base_url}showuser={$moderator['memid']}'>{$moderator['name']}</a>, ";
					}
				}

				$mod_string = preg_replace( "!,\s+$!", "", $mod_string );

			}
			else
			{
				if ($moderator['isg'] == 1)
				{
					$mod_string .= "<a href='{$ibforums->base_url}act=Members&amp;max_results=30&amp;filter={$this->mods[$forum_id]['gid']}&amp;sort_order=asc&amp;sort_key=name&amp;st=0&amp;b=1'>{$this->mods[$forum_id]['gname']}</a>, ";
				}
				else
				{
					$mod_string .= "<a href='{$ibforums->base_url}showuser={$this->mods[$forum_id]['memid']}'>{$this->mods[$forum_id]['name']}</a>";
				}
			}
		}

		return $mod_string;

	}

	/*-------------------------------------------------------------------------*/
	// Forums check access
	// ------------------
	// Blah-de-blah
	/*-------------------------------------------------------------------------*/

	function forums_check_access($fid, $prompt_login=0, $in='forum')
	{
		global $ibforums, $std;

		$deny_access = 1;

		if ( $std->check_perms($this->forum_by_id[$fid]['show_perms']) == TRUE )
		{
			if ( $std->check_perms($this->forum_by_id[$fid]['read_perms']) == TRUE )
			{
				$deny_access = 0;
			}
			else
			{
				//-----------------------------------------
				// Can see topics?
				//-----------------------------------------

				if ( $this->forum_by_id[$fid]['permission_showtopic'] )
				{
					$this->read_topic_only = 1;

					if ( $in == 'forum' )
					{
						$deny_access = 0;
					}
					else
					{
						//-----------------------------------------
						// Custom error?
						//-----------------------------------------

						$this->forums_custom_error($fid);

						$deny_access = 1;
					}
				}
				else
				{
					$this->forums_custom_error($fid);

					$deny_access = 1;
				}
			}
		}
		else
		{
			//-----------------------------------------
			// custom error
			//-----------------------------------------

			$this->forums_custom_error($fid);

			$deny_access = 1;
		}


		//-----------------------------------------
		// Do we have permission to even see the password page?
		//-----------------------------------------

		if ($deny_access == 0)
		{
			if ($this->forum_by_id[$fid]['password'])
			{
				if ( $this->forums_compare_password( $fid ) == TRUE )
				{
					$deny_access = 0;
				}
				else
				{
					$deny_access = 1;

					if ( $prompt_login == 1 )
					{
						$this->forums_show_login( $fid );
					}
				}
			}
		}

		if ($deny_access == 1)
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_permission') );
        }
	}

	/*-------------------------------------------------------------------------*/
	// Compare forum pasword
	/*-------------------------------------------------------------------------*/

	function forums_compare_password( $fid )
	{
		global $ibforums, $std;

		$cookie_pass = $std->my_getcookie( 'ipbforumpass_'.$fid );

		if ( trim($cookie_pass) == md5($this->forum_by_id[$fid]['password']) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/*-------------------------------------------------------------------------*/
	// Quick check
	/*-------------------------------------------------------------------------*/

	function forums_quick_check_access($fid)
	{
		global $ibforums, $std;

		$deny_access = 1;

		if ( $std->check_perms($this->forum_by_id[$fid]['show_perms']) == TRUE )
		{
			$deny_access = 0;
		}

		//-----------------------------------------
		// Do we have permission to even see the password page?
		//-----------------------------------------

		if ($deny_access == 0)
		{
			if ($this->forum_by_id[$fid]['password'])
			{
				if ( $this->forums_compare_password( $fid ) == TRUE )
				{
					$deny_access = 0;
				}
				else
				{
					$deny_access = 1;
				}
			}
		}

		return $deny_access;
	}

	/*-------------------------------------------------------------------------*/
	// Forums custom error
	// ------------------
	// Blah-de-blah
	/*-------------------------------------------------------------------------*/

	function forums_custom_error( $fid )
	{
		global $ibforums, $std, $DB;

		$tmp = $DB->simple_exec_query( array( 'select' => 'permission_custom_error', 'from' => 'forums', 'where' => "id=".$fid) );

		if ( $tmp['permission_custom_error'] )
		{
			$ibforums->lang = $std->load_words($ibforums->lang, "lang_error", $ibforums->lang_id);

			//-----------------------------------------
			// Update session
			//-----------------------------------------

    		$DB->do_shutdown_update( 'sessions', array( 'in_error' => 1 ), "id='{$ibforums->my_session}'" );

			list($em_1, $em_2) = explode( '@', $ibforums->vars['email_in'] );

			$html  = $ibforums->skin_global->Error( $tmp['permission_custom_error'], $em_1, $em_2);
			$print = new display();
			$print->add_output($html);
			$print->do_output( array('TITLE' => $ibforums->lang['error_title'] ) );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Forums log in screen
	// ------------------
	// Blah-de-blah
	/*-------------------------------------------------------------------------*/

	function forums_show_login( $fid )
	{
		global $ibforums, $std, $DB, $print;

		if ( ! class_exists( 'skin_forum' ) )
		{
			$this->html = $std->load_template('skin_forum');
			$ibforums->lang = $std->load_words($ibforums->lang, 'lang_forum', $ibforums->lang_id);
		}
		else
		{
			$this->html = new skin_forum();
		}

		if (empty($ibforums->member['id']))
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

		$this->output = $this->html->forum_password_log_in( $fid );

		$print->add_output( $this->output );

        $print->do_output( array( 'TITLE'    => $ibforums->vars['board_name']." -> ".$this->forum_by_id[$fid]['name'],
        					 	  'JS'       => 0,
        					 	  'NAV'      => array(
        					 	  					   "<a href='".$ibforums->base_url."showforum={$fid}'>{$this->forum_by_id[$fid]['name']}</a>",
        					 	  					 ),
        					  ) );

	}

	/*-------------------------------------------------------------------------*/
	// Get parents
	// ------------------
	// Find all the parents of a child without getting the nice lady to
	// use the superstore tannoy to shout "Small ugly boy in tears at reception"
	/*-------------------------------------------------------------------------*/

	function forums_get_parents($root_id, $ids=array())
	{
		if ( $this->forum_by_id[ $root_id ]['parent_id'] and $this->forum_by_id[ $root_id ]['parent_id'] != 'root' )
		{
			$ids[] = $this->forum_by_id[ $root_id ]['parent_id'];

			$ids = $this->forums_get_parents( $this->forum_by_id[ $root_id ]['parent_id'], $ids );
		}

		return $ids;
	}

	/*-------------------------------------------------------------------------*/
	// Gets children (Debug purposes)
	// ------------------
	// Get all meh children
	/*-------------------------------------------------------------------------*/

	function forums_get_children( $root_id, $ids=array() )
	{
		if ( is_array( $this->forum_cache[ $root_id ] ) )
		{
			foreach( $this->forum_cache[ $root_id ] as $id => $forum_data )
			{
				$ids[] = $forum_data['id'];

				$ids = $this->forums_get_children($forum_data['id'], $ids);
			}
		}

		return $ids;
	}


	/*-------------------------------------------------------------------------*/
	// Calcualte Children
	// ------------------
	// Gets cumulative posts/topics - sets new post marker and last topic id
	/*-------------------------------------------------------------------------*/

	function forums_calc_children($root_id, $forum_data=array(), $done_pass=0)
	{
		global $ibforums;

		if ( is_array( $this->forum_cache[ $root_id ] ) )
		{
			foreach( $this->forum_cache[ $root_id ] as $id => $data )
			{
				if ($data['last_post'] > $forum_data['last_post'])
				{
					$forum_data['last_post']        = $data['last_post'];
					$forum_data['fid']              = $data['id'];
					$forum_data['last_id']          = $data['last_id'];
					$forum_data['last_title']       = $data['last_title'];
					$forum_data['password']         = $data['password'];
					$forum_data['last_poster_id']   = $data['last_poster_id'];
					$forum_data['last_poster_name'] = $data['last_poster_name'];
					$forum_data['status']           = $data['status'];
				}

				$forum_data['posts']  += $data['posts'];
				$forum_data['topics'] += $data['topics'];

				if ( $ibforums->member['g_is_supmod'] or $ibforums->member['_moderator'][ $data['id'] ]['post_q'] == 1 )
				{
					$forum_data['queued_posts']  += $data['queued_posts'];
					$forum_data['queued_topics'] += $data['queued_topics'];
				}

				if ( ! $done_pass )
				{
					$forum_data['subforums'][ $data['id'] ] = $this->class->html->show_subforum_link($data['id'],$data['name']);
				}

				$forum_data = $this->forums_calc_children( $data['id'], $forum_data, 1 );
			}
		}

		return $forum_data;
	}

	/*-------------------------------------------------------------------------*/
	// Create forum breadcrumb nav
	// ------------------
	// Simple and effective - just like me :(
	/*-------------------------------------------------------------------------*/

	function forums_breadcrumb_nav($root_id, $url='showforum=')
	{
		global $ibforums;

		$nav_array[] = "<a href='".$ibforums->base_url."$url{$root_id}'>{$this->forum_by_id[$root_id]['name']}</a>";

		$ids = $this->forums_get_parents( $root_id );

		if ( is_array($ids) and count($ids) )
		{
			foreach( $ids as $id )
			{
				$data = $this->forum_by_id[$id];

				$nav_array[] = "<a href='".$ibforums->base_url."$url{$data['id']}'>{$data['name']}</a>";
			}
		}

		return array_reverse($nav_array);
	}

	/*-------------------------------------------------------------------------*/
	// forum jumpee
	// ------------------
	// Builds the forum jumpee dunnit
	/*-------------------------------------------------------------------------*/

	function forums_forum_jump($html=0, $override=0)
	{
		global $ibforums;

		foreach( $this->forum_cache['root'] as $id => $forum_data )
		{
			if ( $forum_data['sub_can_post'] or ( is_array($this->forum_cache[ $forum_data['id'] ]) and count($this->forum_cache[ $forum_data['id'] ]) ) )
			{
				if ($html == 1 or $override == 1)
				{
					$selected = "";

					if ($ibforums->input['f'] and $ibforums->input['f'] == $forum_data['id'])
					{
						$selected = ' selected="selected"';
					}
				}

				$jump_string .= "<option value=\"{$forum_data['id']}\"".$selected.">".$forum_data['name']."</option>\n";

				$depth_guide = $this->depth_guide;

				if ( is_array( $this->forum_cache[ $forum_data['id'] ] ) )
				{
					foreach( $this->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
					{
						if ($html == 1 or $override == 1)
						{
							$selected = "";

							if ($ibforums->input['f'] and $ibforums->input['f'] == $forum_data['id'])
							{
								$selected = ' selected="selected"';
							}
						}

						$jump_string .= "<option value=\"{$forum_data['id']}\"".$selected.">&nbsp;&nbsp;&#0124;".$depth_guide." ".$forum_data['name']."</option>\n";

						$jump_string = $this->forums_forum_jump_internal( $forum_data['id'], $jump_string, $depth_guide . $this->depth_guide, $html, $override );
					}
				}
			}
		}

		return $jump_string;
	}

	function forums_forum_jump_internal($root_id, $jump_string="", $depth_guide="",$html=0, $override=0)
	{
		global $ibforums;

		if ( is_array( $this->forum_cache[ $root_id ] ) )
		{
			foreach( $this->forum_cache[ $root_id ] as $id => $forum_data )
			{
				if ($html == 1 or $override == 1)
				{
					$selected = "";

					if ($ibforums->input['f'] and $ibforums->input['f'] == $forum_data['id'])
					{
						$selected = ' selected="selected"';
					}
				}

				$jump_string .= "<option value=\"{$forum_data['id']}\"".$selected.">&nbsp;&nbsp;&#0124;".$depth_guide." ".$forum_data['name']."</option>\n";

				$jump_string = $this->forums_forum_jump_internal( $forum_data['id'], $jump_string, $depth_guide . $this->depth_guide, $html, $override );
			}
		}


		return $jump_string;
	}

	/*-------------------------------------------------------------------------*/
	// Format Forum
	// ------------------
	// Sorts out last poster, etc
	/*-------------------------------------------------------------------------*/

	function forums_format_lastinfo($forum_data)
	{
		global $std, $DB, $ibforums;

		$show_subforums = 1;

		if ( $std->check_perms($this->forum_by_id[ $forum_data['id'] ]['read_perms']) != TRUE )
		{
			$show_subforums = 0;
		}

		$forum_data['img_new_post'] = $this->forums_new_posts($forum_data);

		if ( $forum_data['img_new_post'] == '<{C_ON}>' )
		{
			$forum_data['img_new_post'] = $this->class->html->forum_img_with_link($forum_data['img_new_post'], $forum_data['id']);
		}
		else if ( $forum_data['img_new_post'] == '<{C_ON_CAT}>' )
		{
			$forum_data['img_new_post'] = $this->class->html->subforum_img_with_link($forum_data['img_new_post'], $forum_data['id']);
		}

		$forum_data['last_post'] = $std->get_date($forum_data['last_post'], 'LONG');

		$forum_data['last_topic'] = $ibforums->lang['f_none'];

		$forum_data['full_last_title'] = $forum_data['last_title'];

		if (isset($forum_data['last_title']) and $forum_data['last_id'])
		{
			$forum_data['last_title'] = strip_tags($forum_data['last_title']);
			$forum_data['last_title'] = str_replace( "&#33;" , "!", $forum_data['last_title'] );
			$forum_data['last_title'] = str_replace( "&quot;", "\"", $forum_data['last_title'] );

			$forum_data['last_title'] = $std->txt_truncate($forum_data['last_title'], 30);

			if ( $forum_data['password'] OR ( $std->check_perms($this->forum_by_id[ $forum_data['fid'] ]['read_perms']) != TRUE AND $this->forum_by_id[ $forum_data['fid'] ]['permission_showtopic'] == 0 ) )
			{
				$forum_data['last_topic'] = $ibforums->lang['f_protected'];
			}
			else
			{
				$forum_data['last_unread'] = $this->class->html->forumrow_lastunread_link($forum_data['id'], $forum_data['last_id']);
				$forum_data['last_topic']  = "<a href='{$ibforums->base_url}showtopic={$forum_data['last_id']}&amp;view=getnewpost' title='{$ibforums->lang['tt_gounread']}: {$forum_data['full_last_title']}'>{$forum_data['last_title']}</a>";
			}
		}


		if ( isset($forum_data['last_poster_name']))
		{
			$forum_data['last_poster'] = $forum_data['last_poster_id'] ? "<a href='{$ibforums->base_url}showuser={$forum_data['last_poster_id']}'>{$forum_data['last_poster_name']}</a>"
																	   : $forum_data['last_poster_name'];
		}
		else
		{
			$forum_data['last_poster'] = $ibforums->lang['f_none'];
		}

		//-----------------------------------------
		// Moderators
		//-----------------------------------------

		$forum_data['moderator'] = $this->forums_get_moderators($forum_data['id']);

		$forum_data['posts']  = $std->do_number_format($forum_data['posts']);
		$forum_data['topics'] = $std->do_number_format($forum_data['topics']);

		if ( $ibforums->vars['disable_subforum_show'] == 0 AND $show_subforums == 1 )
		{
			if ( is_array( $forum_data['subforums'] ) and count( $forum_data['subforums'] ) )
			{
				$forum_data['show_subforums'] = $this->class->html->show_subforum_all_links( implode( ', ', $forum_data['subforums'] ) );
			}
		}

		if ( ( $ibforums->member['g_is_supmod'] or $ibforums->member['_moderator'][ $forum_data['id'] ]['post_q'] == 1 )
		   and ( $forum_data['queued_posts'] or $forum_data['queued_topics'] ) )
		{
			$forum_data['_queued_info'] = $this->class->html->show_queued_info( intval($forum_data['queued_posts']), intval($forum_data['queued_topics']) );
			$forum_data['_queued_img']  = $this->class->html->show_queued_img($forum_data['fid']);
		}

		return $forum_data;
	}

	/*-------------------------------------------------------------------------*/
	//
	// Generate the appropriate folder icon for a forum
	//
	/*-------------------------------------------------------------------------*/

	function forums_new_posts($forum_data)
	{
		global $ibforums, $std;

        $sub = 0;

        if ( count($forum_data['subforums']) )
        {
        	$sub = 1;
        }

        $rtime = $ibforums->input['last_visit'];

        $fid   = $forum_data['fid'] == "" ? $forum_data['id'] : $forum_data['fid'];

        $ftime = $ibforums->forum_read[ $fid ];

        if ( $ibforums->forum_read[0] > $ftime )
		{
			$ftime = $ibforums->forum_read[0];
		}

        $rtime = $ftime > $rtime ? $ftime : $rtime;

        if ($sub == 0)
        {
			if ( ! $forum_data['status'] )
			{
				return "<{C_LOCKED}>";
			}

			$sub_cat_img = '';
        }
        else
        {
        	$sub_cat_img = '_CAT';
        }

        if ($forum_data['password'] and $sub == 0)
        {
            return $forum_data['last_post'] > $rtime ? "<{C_ON_RES}>"
                                                     : "<{C_OFF_RES}>";
        }

        return $forum_data['last_post']  > $rtime ? "<{C_ON".$sub_cat_img."}>"
                                                  : "<{C_OFF".$sub_cat_img."}>";
    }


}


?>