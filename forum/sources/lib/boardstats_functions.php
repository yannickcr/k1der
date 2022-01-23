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
|   > Board index functions module
|   > Module written by Matt Mecham
|   > Date started: 18th November 2003
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Fri 21 May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}


class boardstats_functions
{

	var $class    = "";
	var $sep_char = "";

	var $users_online  = "";
	var $total_posts   = "";
	var $total_members = "";

	/*-------------------------------------------------------------------------*/
	// register_class
	// ------------------
	// Register a $this-> class with this module
	/*-------------------------------------------------------------------------*/

	function register_class(&$class)
	{
		$this->class = $class;

		$this->sep_char = '<{ACTIVE_LIST_SEP}>';
	}

	/*-------------------------------------------------------------------------*/
	//
	// DISPLAY ACTIVE USERS
	//
	/*-------------------------------------------------------------------------*/

	function active_users()
	{
		global $DB, $std, $ibforums;

		$active = array( 'TOTAL'   => 0 ,
						 'NAMES'   => "",
						 'GUESTS'  => 0 ,
						 'MEMBERS' => 0 ,
						 'ANON'    => 0 ,
					   );

		$stats_html = "";

		if ($ibforums->vars['show_active'])
		{
			if ($ibforums->vars['au_cutoff'] == "")
			{
				$ibforums->vars['au_cutoff'] = 15;
			}

			//-----------------------------------------
			// Get the users from the DB
			//-----------------------------------------

			$cut_off = $ibforums->vars['au_cutoff'] * 60;
			$time    = time() - $cut_off;
			$rows    = array();
			$ar_time = time();

			if ( $ibforums->member['id'] )
			{
				$rows = array( $ar_time => array( 'login_type'   => substr($ibforums->member['login_anonymous'],0, 1),
												  'running_time' => $ar_time,
												  'member_id'    => $ibforums->member['id'],
												  'member_name'  => $ibforums->member['name'],
												  'member_group' => $ibforums->member['mgroup'] ) );
			}

			$DB->simple_construct( array( 'select' => 'id, member_id, member_name, login_type, running_time, member_group',
										  'from'   => 'sessions',
										  'where'  => "running_time > $time",
										  //'order'  => "running_time DESC" // Sort in PHP to avoid filesort in SQL
								 )      );


			$DB->simple_exec();

			//-----------------------------------------
			// FETCH...
			//-----------------------------------------

			while ($r = $DB->fetch_row() )
			{
				$rows[ $r['running_time'].'.'.$r['id'] ] = $r;
			}

			krsort( $rows );

			//-----------------------------------------
			// cache all printed members so we
			// don't double print them
			//-----------------------------------------

			$cached = array();

			foreach ( $rows as $result )
			{
				$last_date = $std->get_time( $result['running_time'] );

				//-----------------------------------------
				// Bot?
				//-----------------------------------------

				if ( strstr( $result['id'], '_session' ) )
				{
					//-----------------------------------------
					// Seen bot of this type yet?
					//-----------------------------------------

					$botname = preg_replace( '/^(.+?)=/', "\\1", $result['id'] );

					if ( ! $cached[ $result['member_name'] ] )
					{
						if ( $ibforums->vars['spider_anon'] )
						{
							if ( $ibforums->member['mgroup'] == $ibforums->vars['admin_group'] )
							{
								$active['NAMES'] .= "{$result['member_name']}*{$this->sep_char} \n";
							}
						}
						else
						{
							$active['NAMES'] .= "{$result['member_name']}{$this->sep_char} \n";
						}

						$cached[ $result['member_name'] ] = 1;
					}
					else
					{
						//-----------------------------------------
						// Yup, count others as guest
						//-----------------------------------------

						$active['GUESTS']++;
					}
				}

				//-----------------------------------------
				// Guest?
				//-----------------------------------------

				else if ($result['member_id'] == 0 )
				{
					$active['GUESTS']++;
				}

				//-----------------------------------------
				// Member?
				//-----------------------------------------

				else
				{
					if ( empty( $cached[ $result['member_id'] ] ) )
					{
						$cached[ $result['member_id'] ] = 1;

						$result['prefix'] = $ibforums->cache['group_cache'][ $result['member_group'] ]['prefix'];
						$result['suffix'] = $ibforums->cache['group_cache'][ $result['member_group'] ]['suffix'];

						if ($result['login_type'])
						{
							if ( ($ibforums->member['mgroup'] == $ibforums->vars['admin_group']) and ($ibforums->vars['disable_admin_anon'] != 1) )
							{
								$active['NAMES'] .= "<a href='{$ibforums->base_url}showuser={$result['member_id']}' title='$last_date'>{$result['prefix']}{$result['member_name']}{$result['suffix']}</a>*{$this->sep_char} \n";
								$active['ANON']++;
							}
							else
							{
								$active['ANON']++;
							}
						}
						else
						{
							$active['MEMBERS']++;
							$active['NAMES'] .= "<a href='{$ibforums->base_url}showuser={$result['member_id']}' title='$last_date'>{$result['prefix']}{$result['member_name']}{$result['suffix']}</a>{$this->sep_char} \n";
						}
					}
				}
			}

			$active['NAMES'] = preg_replace( "/".preg_quote($this->sep_char)."$/", "", trim($active['NAMES']) );

			$active['TOTAL'] = $active['MEMBERS'] + $active['GUESTS'] + $active['ANON'];

			$this->users_online = $active['TOTAL'];

			//-----------------------------------------
			// Show a link?
			//-----------------------------------------

			if ($ibforums->vars['allow_online_list'])
			{
				$active['links'] = $this->class->html->active_user_links();
			}

			$ibforums->lang['active_users'] = sprintf( $ibforums->lang['active_users'], $ibforums->vars['au_cutoff'] );

			return $this->class->html->ActiveUsers($active, $ibforums->vars['au_cutoff']);
		}

	}

	/*-------------------------------------------------------------------------*/
	//
	// SHOW CALENDAR EVENTS
	//
	/*-------------------------------------------------------------------------*/

	function show_calendar_events()
	{
		global $DB, $ibforums, $std;

		if ($ibforums->vars['show_birthdays'] or $ibforums->vars['show_calendar'] )
		{
			$a = explode( ',', gmdate( 'Y,n,j,G,i,s', time() + $std->get_time_offset() ) );

			$day   = $a[2];
			$month = $a[1];
			$year  = $a[0];

			$birthstring = "";
			$count       = 0;
			$users       = array();

			if ( $ibforums->vars['show_birthdays'] )
			{
				//-----------------------------------------
				// Not caching?
				//-----------------------------------------

				if ( ! $ibforums->vars['cache_calendar'] )
				{
					$DB->simple_construct( array( 'select' => 'id, name, bday_day, bday_month, bday_year', 'from' => 'members', 'where' => "bday_day=$day and bday_month=$month" ) );
					$DB->simple_exec();

					while( $r = $DB->fetch_row() )
					{
						$users[] = $r;
					}
				}

				//-----------------------------------------
				// Or caching...
				//-----------------------------------------

				else
				{
					if ( count( $ibforums->cache['birthdays'] ) )
					{
						foreach( $ibforums->cache['birthdays'] as $id => $u )
						{
							if ( $u['bday_day'] == $day and $u['bday_month'] == $month )
							{
								$users[] = $u;
							}
						}
					}
				}

				//-----------------------------------------
				// Spin and print...
				//-----------------------------------------

				foreach ( $users as $id => $user )
				{
					$birthstring .= "<a href='{$ibforums->base_url}showuser={$user['id']}'>{$user['name']}</a>";

					if ($user['bday_year'])
					{
						$pyear = $year - $user['bday_year'];
						$birthstring .= "(<b>$pyear</b>)";
					}

					$birthstring .= $this->sep_char."\n";

					$count++;
				}

				//-----------------------------------------
				// Fix up string...
				//-----------------------------------------

				$birthstring = preg_replace( "/".$this->sep_char."$/", "", trim($birthstring) );

				$lang = $ibforums->lang['no_birth_users'];

				if ($count > 0)
				{
					$lang = ($count > 1) ? $ibforums->lang['birth_users'] : $ibforums->lang['birth_user'];
					$stats_html .= $this->class->html->birthdays( $birthstring, $count, $lang  );
				}
				else
				{
					$count = "";

					if ( ! $ibforums->vars['autohide_bday'] )
					{
						$stats_html .= $this->class->html->birthdays( $birthstring, $count, $lang  );
					}
				}
			}
		}


		//-----------------------------------------
		// Are we viewing the calendar?
		//-----------------------------------------

		if ($ibforums->vars['show_calendar'])
		{
			if ($ibforums->vars['calendar_limit'] < 2)
			{
				$ibforums->vars['calendar_limit'] = 2;
			}

			$our_unix = mktime( 0, 0, 1, $month, $day, $year);
			$max_date = $our_unix + ($ibforums->vars['calendar_limit'] * 86400);
			$events   = array();
			$show_events = array();

			//-----------------------------------------
			// Not caching?
			//-----------------------------------------

			if ( ! $ibforums->vars['cache_calendar'] )
			{
				$DB->simple_construct( array( 'select' => 'eventid, title, read_perms, priv_event, userid, unix_stamp',
											  'from'   => 'calendar_events',
											  'where'  => "unix_stamp >= $our_unix and unix_stamp <= $max_date",
											  'order'  => 'unix_stamp'
									 )      );
				$DB->simple_exec();

				while( $r = $DB->fetch_row() )
				{
					$events[] = $r;
				}
			}

			//-----------------------------------------
			// Or caching...
			//-----------------------------------------

			else
			{
				if ( count( $ibforums->cache['calendar'] ) )
				{
					foreach( $ibforums->cache['calendar'] as $id => $u )
					{
						if ( $u['unix_stamp'] >= $our_unix and $u['unix_stamp'] <= $max_date )
						{
							$events[] = $u;
						}
					}
				}
			}

			//-----------------------------------------
			// Print...
			//-----------------------------------------

			foreach( $events as $e => $event )
			{
				if ($event['priv_event'] == 1 and $ibforums->member['id'] != $event['userid'])
				{
					continue;
				}

				//-----------------------------------------
				// Do we have permission to see the event?
				//-----------------------------------------

				if ( $event['read_perms'] != '*' )
				{
					if ( ! preg_match( "/(^|,)".$ibforums->member['mgroup']."(,|$)/", $event['read_perms'] ) )
					{
						continue;
					}
				}

				if ( ! $event['_recurring'] )
				{
					$c_time = date( 'j-F-y', $event['unix_stamp']);
				}

				$show_events[] = "<a href='{$ibforums->base_url}act=calendar&amp;code=showevent&amp;eventid={$event['eventid']}' title='$c_time'>".$event['title']."</a>";
			}

			$ibforums->lang['calender_f_title'] = sprintf( $ibforums->lang['calender_f_title'], $ibforums->vars['calendar_limit'] );

			if ( count($show_events) > 0 )
			{
				$event_string = implode( $this->sep_char.' ', $show_events );
				$stats_html .= $this->class->html->calendar_events( $event_string  );
			}
			else
			{
				if ( ! $ibforums->vars['autohide_calendar'] )
				{
					$event_string = $ibforums->lang['no_calendar_events'];
					$stats_html .= $this->class->html->calendar_events( $event_string  );
				}
			}
		}

		return $stats_html;

	}

	/*-------------------------------------------------------------------------*/
	//
	// SHOW TOTALS
	//
	/*-------------------------------------------------------------------------*/

	function show_totals()
	{
		global $DB, $ibforums, $std;

		if ($ibforums->vars['show_totals'])
		{
			if ( ! is_array( $ibforums->cache['stats'] ) )
			{
				$ibforums->cache['stats'] = array();

				$DB->simple_exec_query( array( 'delete' => 'cache_store', 'where' => "cs_key='stats'" ) );
				$DB->do_insert( 'cache_store', array( 'cs_array' => 1, 'cs_key' => 'stats', 'cs_value' => addslashes(serialize($ibforums->cache['stats'])) ) );
			}

			$stats = $ibforums->cache['stats'];

			//-----------------------------------------
			// Update the most active count if needed
			//-----------------------------------------

			if ($this->users_online > $stats['most_count'])
			{
				$stats['most_count'] = $this->users_online;
				$stats['most_date']  = time();

				$DB->do_update( 'cache_store',
								array( 'cs_array' => 1, 'cs_value' => addslashes(serialize($stats)) ),
							    "cs_key='stats'"
							  );
			}

			$most_time = $std->get_date( $stats['most_date'], 'LONG' );

			$ibforums->lang['most_online'] = str_replace( "<#NUM#>" ,   $std->do_number_format($stats['most_count'])  , $ibforums->lang['most_online'] );
			$ibforums->lang['most_online'] = str_replace( "<#DATE#>",                   $most_time                    , $ibforums->lang['most_online'] );

			$total_posts = $stats['total_replies'] + $stats['total_topics'];

			$total_posts        = $std->do_number_format($total_posts);
			$stats['mem_count'] = $std->do_number_format($stats['mem_count']);

			$this->total_posts    = $total_posts;
			$this->total_members  = $stats['mem_count'];

			$link = $ibforums->base_url."showuser=".$stats['last_mem_id'];

			$ibforums->lang['total_word_string'] = str_replace( "<#posts#>" , "$total_posts"          , $ibforums->lang['total_word_string'] );
			$ibforums->lang['total_word_string'] = str_replace( "<#reg#>"   , $stats['mem_count']     , $ibforums->lang['total_word_string'] );
			$ibforums->lang['total_word_string'] = str_replace( "<#mem#>"   , $stats['last_mem_name'] , $ibforums->lang['total_word_string'] );
			$ibforums->lang['total_word_string'] = str_replace( "<#link#>"  , $link                   , $ibforums->lang['total_word_string'] );

			$stats_html .= $this->class->html->ShowStats($ibforums->lang['total_word_string']);

		}

		return $stats_html;

	}



}




?>