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
|   > Show online users
|   > Module written by Matt Mecham
|   > Date started: 12th March 2002
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

class online
{

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";
    var $first      = 0;
    var $perpage    = 25;

    var $forums     = array();
    var $cats       = array();
    var $sessions   = array();
    var $where      = array();

    var $seen_name  = array();

    /*-------------------------------------------------------------------------*/
	// AUTO RUN
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	//-----------------------------------------
    	// Are we allowed to see the online list?
    	//-----------------------------------------

    	$ibforums->input['st'] = intval($ibforums->input['st']);

    	if ( $ibforums->vars['allow_online_list'] != 1 )
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
    	}

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_online', $ibforums->lang_id);

    	$this->html = $std->load_template('skin_online');

    	$this->base_url        = $ibforums->base_url;

    	//-----------------------------------------
    	// Build up our language hash
    	//-----------------------------------------

    	foreach ($ibforums->lang as $k => $v)
    	{
    		if ( preg_match( "/^WHERE_(\w+)$/", $k, $match ) )
    		{
    			$this->where[ $match[1] ] = $ibforums->lang[$k];
    		}
    	}

    	unset($match);

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['CODE'])
    	{
    		case 'listall':
    			$this->list_all();
    			break;
    		case '02':
    			$this->list_forum();
    			break;
    		default:
    			$this->list_all();
    			break;
    	}

    	//-----------------------------------------
    	// If we have any HTML to print, do so...
    	//-----------------------------------------

    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
 	}


	/*-------------------------------------------------------------------------*/
	// list_all
	// ------------------
	// List all online users
	/*-------------------------------------------------------------------------*/

	function list_all()
	{
		global $ibforums, $DB, $std, $forums;

		$this->first   = intval($ibforums->input['st']);

		$show_mem      = array( 'reg', 'guest', 'all' );
		$sort_order    = array( 'desc', 'asc' );
		$sort_key      = array( 'click', 'name' );

		$show_mem_value   = $ibforums->input['show_mem']   ? $ibforums->input['show_mem']   : 'all';
		$sort_order_value = $ibforums->input['sort_order'] ? $ibforums->input['sort_order'] : 'desc';
		$sort_key_value   = $ibforums->input['sort_key']   ? $ibforums->input['sort_key']   : 'click';

		$show_mem_html   = "";
		$sort_order_html = "";
		$sort_key_html   = "";

		$oo = "<option ";
		$oc = "</option>\n";

		foreach( $show_mem as $k )
		{
			$s = "";

			if ( $show_mem_value == $k )
			{
				$s = ' selected="selected" ';
			}

			$show_mem_html .= $oo.'value="'.$k.'"'.$s.'>'.$ibforums->lang['s_show_mem_'.$k].$oc;
		}

		foreach( $sort_order as $k )
		{
			$s = "";

			if ( $sort_order_value == $k )
			{
				$s = ' selected="selected" ';
			}

			$sort_order_html .= $oo.'value="'.$k.'"'.$s.'>'.$ibforums->lang['s_sort_order_'.$k].$oc;
		}

		foreach( $sort_key as $k )
		{
			$s = "";

			if ( $sort_key_value == $k )
			{
				$s = ' selected="selected" ';
			}

			$sort_key_html .= $oo.'value="'.$k.'"'.$s.'>'.$ibforums->lang['s_sort_key_'.$k].$oc;
		}

		if ($ibforums->vars['au_cutoff'] == "")
		{
			$ibforums->vars['au_cutoff'] = 15;
		}

		$cut_off = $ibforums->vars['au_cutoff'] * 60;
		$t_time  = time() - $cut_off;

		$db_order = $sort_order_value == 'asc' ? 'asc' : 'desc';
		$db_key   = $sort_key_value   == 'click' ? 'running_time' : 'member_name';

		switch ($show_mem_value)
		{
			case 'reg':
				$db_mem = " AND s.member_group <> {$ibforums->vars['guest_group']}";
				break;
			case 'guest':
				$db_mem = " AND s.member_group = {$ibforums->vars['guest_group']} ";
				break;
			default:
				$db_mem = "";
				break;
		}

		$max   = $DB->simple_exec_query( array( 'select' => 'COUNT(*) as total_sessions', 'from' => 'sessions s', 'where' => "running_time > $t_time".$db_mem ) );

		$links = $std->build_pagelinks(  array( 'TOTAL_POSS'  => $max['total_sessions'],
												'PER_PAGE'    => 25,
												'CUR_ST_VAL'  => $this->first,
												'L_SINGLE'     => "",
												'L_MULTI'      => $ibforums->lang['pages'],
												'BASE_URL'     => $this->base_url."act=Online&amp;CODE=listall&amp;sort_key=$sort_key_value&amp;sort_order=$sort_order_value&amp;show_mem=$show_mem_value"
											  )
									   );

		$this->output = $this->html->Page_header($links);

		// Grab all the current sessions.

		$final      = array();
		$tid_array  = array();
		$topics     = array();

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'sessions s',
									  'where'  => "s.running_time > $t_time $db_mem",
									  'order'  => "$db_key $db_order",
									  'limit'  => array( $this->first, 25 )
							)       );

		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$r['prefix'] = $ibforums->cache['group_cache'][ $r['member_group'] ]['prefix'];
			$r['suffix'] = $ibforums->cache['group_cache'][ $r['member_group'] ]['suffix'];

			$final[] = $r;

			if ($r['in_topic'] != "")
			{
				$tid_array[ $r['in_topic'] ] = $r['in_topic'];
			}
		}

		if ( count($tid_array) > 0 )
		{
			$tid_string = implode( ",", $tid_array );

			$DB->simple_construct( array( 'select' => 'tid, title', 'from' => 'topics', 'where' => "tid IN ($tid_string)" ) );
			$DB->simple_exec();

			while ( $t = $DB->fetch_row() )
			{
				$topics[ $t['tid'] ] = $t['title'];
			}
		}

		foreach( $final as $idx => $sess )
		{
			//-----------------------------------------
			// Is this a member, and have we seen them before?
			// Proxy servers, etc can confuse the session handler,
			// creating duplicate session IDs for the same user when
			// their IP address changes.
			//-----------------------------------------

			$inv    = '';

			if ( strstr( $sess['id'], '_session' ) )
			{
				$sess['is_bot'] = 1;

				if ( $ibforums->vars['spider_anon'] )
				{
					if ( $ibforums->member['mgroup'] == $ibforums->vars['admin_group'] )
					{
						$inv = '*';
					}
					else
					{
						$sess['member_id']   = '';
						$sess['member_name'] = '';
						$sess['in_error']    = 1;
					}
				}
			}
			else if ($sess['login_type'] == 1)
			{
				if ( ($ibforums->member['mgroup'] == $ibforums->vars['admin_group']) and ($ibforums->vars['disable_admin_anon'] != 1) )
				{
					$inv = '*';
				}
				else
				{
					$sess['member_id']   = '';
					$sess['member_name'] = '';
					$sess['in_error']    = 1;
					$sess['prefix']      = "";
					$sess['suffix']      = "";
				}
			}

			//-----------------------------------------
			// ICheck for dupes
			//-----------------------------------------

			 if ( ! empty($sess['member_name']) )
			 {
				 if (isset($this->seen_name[ $sess['member_name'] ]) )
				 {
					 continue;
				 }
				 else
				 {
					 $this->seen_name[ $sess['member_name'] ] = 1;
				 }
			 }

			//-----------------------------------------
			// Figure out location
			//-----------------------------------------

			if ( $sess['in_error'] )
			{
				$line = " {$ibforums->lang['board_index']}";
			}
			else if (isset($sess['location']))
			{
				$line = "";

				list($act, $pid) = explode( ",", $sess['location'] );

				$fid = $sess['in_forum'];
				$tid = $sess['in_topic'];
				$act = strtolower($act);

				if (isset($act))
				{
					$line = isset($this->where[ $act ]) ? $this->where[ $act ] : $ibforums->lang['board_index'];
 				}

				if ($fid != "" and ($act == 'sf' or $act == 'st' or $act == 'post'))
				{
					$deny = 1;

					$deny = $forums->forums_quick_check_access( $fid );

					if ($deny != 1)
					{
						if ( ($tid > 0) and ($act != 'post') )
						{
							$line .= " <a href='{$this->base_url}showtopic=$tid'>{$topics[$tid]}</a>";
						}
						else
						{
							$line .= " <a href='{$this->base_url}showforum=$fid'>{$forums->forum_by_id[ $fid ]['name']}</a>";
						}
					}
					else
					{
						$line = " {$ibforums->lang['board_index']}";
					}
				}

			}
			else
			{
				$line = " {$ibforums->lang['board_index']}";
			}

			$sess['where_line'] = $line;

			if ( ($ibforums->member['mgroup'] == $ibforums->vars['admin_group']) and ($ibforums->vars['disable_online_ip'] != 1) )
			{
				$sess['ip_address'] = " ( ".$sess['ip_address']." )";
			}
			else
			{
				$sess['ip_address'] = "";
			}

			if ( ($sess['member_id']) )
			{
				$sess['member_name'] = "<a href='{$this->base_url}showuser={$sess['member_id']}'>{$sess['prefix']}{$sess['member_name']}{$sess['suffix']}</a>$inv {$sess['ip_address']}";
			}

			$sess['running_time'] = $std->get_date( $sess['running_time'], 'LONG' );

			$this->output .= $this->do_html_row($sess);

		}

		$this->output .= $this->html->Page_end($show_mem_html, $sort_order_html, $sort_key_html, $links);

		$this->page_title = $ibforums->lang['page_title'];
		$this->nav        = array( $ibforums->lang['page_title']);
	}

	/*-------------------------------------------------------------------------*/
	// Process row
	/*-------------------------------------------------------------------------*/

	function do_html_row($sess)
	{
		global $ibforums;

		if ($sess['member_name'] and $sess['member_id'])
		{
			$sess['msg_icon'] = "<a href='{$this->base_url}act=Msg&amp;CODE=04&amp;MID={$sess['member_id']}'><{P_MSG}></a>";
		}
		else
		{
			if ( ! $sess['is_bot'] )
			{
				$sess['member_name']  = $sess['prefix'].$ibforums->lang['guest'].$sess['suffix']." ".$sess['ip_address'];
				$sess['msg_icon']     = '&nbsp;';
			}
			else
			{
				$sess['member_name']  .= ' '.$sess['ip_address'];
			}
		}

		return $this->html->show_row($sess);
	}


	function list_forum() { }

}

?>