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
|   > Calendar functions library
|   > Module written by Matt Mecham
|   > Date started: 12th June 2002
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

class calendar {

    var $output     = "";
    var $base_url   = "";
    var $html       = "";
    var $page_title = "";
    var $nav;

    var $chosen_month    = "";
    var $chosen_year     = "";
    var $now_date        = "";
    var $our_datestamp   = "";
    var $offset          = "";
    var $start_date      = "";
    var $first_day_array = "";
    var $month_words       = array();
    var $day_words         = array();
    var $query_month_cache = array();
    var $query_bday_cache  = array();

    /*-------------------------------------------------------------------------*/
    // AUTO-RUN
    /*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print, $skin_universal;

        $ibforums->lang = $std->load_words($ibforums->lang, 'lang_calendar', $ibforums->lang_id);
        $ibforums->lang = $std->load_words($ibforums->lang, 'lang_post', $ibforums->lang_id);

        $this->html      = $std->load_template('skin_calendar');

        $this->post_html = $std->load_template('skin_post');

       	$ibforums->vars['bday_show_cal_max'] = 5;

		//-----------------------------------------
        // Prep our chosen dates
        //-----------------------------------------

        // There is something whacky with getdate and GMT
        // This handrolled method seems to take into account
        // DST where getdate refuses.

		$a = explode( ',', gmdate( 'Y,n,j,G,i,s', time() + $std->get_time_offset() ) );

		$this->now_date = array(
								 'year'    => $a[0],
								 'mon'     => $a[1],
								 'mday'    => $a[2],
								 'hours'   => $a[3],
								 'minutes' => $a[4],
								 'seconds' => $a[5]
							   );

        if ( $ibforums->input['year'] )
        {
        	$ibforums->input['y'] = $ibforums->input['year'];
        }

        $this->chosen_month = ( ! intval($ibforums->input['m']) ) ? $this->now_date['mon']  : intval($ibforums->input['m']);
        $this->chosen_year  = ( ! intval($ibforums->input['y']) ) ? $this->now_date['year'] : intval($ibforums->input['y']);

        //-----------------------------------------
        // Make sure the date is in range.
        //-----------------------------------------

        if ( ! checkdate( $this->chosen_month, 1 , $this->chosen_year ) )
        {
        	$this->chosen_month = $this->now_date['mon'];
        	$this->chosen_year  = $this->now_date['year'];
        }

        //-----------------------------------------
        // Get the timestamp for our chosen date
        //-----------------------------------------

        $this->our_datestamp   = mktime( 0,0,1, $this->chosen_month, 1, $this->chosen_year);
        $this->first_day_array = getdate($this->our_datestamp);

        //-----------------------------------------
        // Finally, build up the lang arrays
        //-----------------------------------------

        $this->month_words = array( $ibforums->lang['M_1'] , $ibforums->lang['M_2'] , $ibforums->lang['M_3'] ,
        							$ibforums->lang['M_4'] , $ibforums->lang['M_5'] , $ibforums->lang['M_6'] ,
        							$ibforums->lang['M_7'] , $ibforums->lang['M_8'] , $ibforums->lang['M_9'] ,
        							$ibforums->lang['M_10'], $ibforums->lang['M_11'], $ibforums->lang['M_12'] );

        $this->day_words   = array( $ibforums->lang['D_0'], $ibforums->lang['D_1'], $ibforums->lang['D_2'],
        							$ibforums->lang['D_3'], $ibforums->lang['D_4'], $ibforums->lang['D_5'],
        							$ibforums->lang['D_6'] );

        switch( $ibforums->input['code'] )
        {
        	case 'newevent':
        		$this->new_event();
        		break;

        	case 'showday':
        		$this->show_day();
        		break;

        	case 'showevent':
        		$this->show_event();
        		break;

        	case 'addnewevent':
        		$this->add_new_event();
        		break;

        	case 'birthdays':
        		$this->show_birthdays();
        		break;

        	case 'edit':
        		$this->edit();
        		break;

        	case 'doedit':
        		$this->do_edit();
        		break;

        	case 'showweek':
        		$this->show_week();
        		break;

        	case 'delete':
        		$ibforums->input['eventid'] = intval($ibforums->input['e']);
        		$this->do_edit(1);
        		break;

        	case 'find':
        		$this->find_date();
        		break;

        	default:
        		$this->show_month();
        		break;
        }

        if ($this->page_title == "")
        {
        	$this->page_title = $ibforums->vars['board_name']." ".$ibforums->lang['page_title'];
        }

        if (! is_array($this->nav) )
        {
        	$this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
        }

        $print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
	}

	/*-------------------------------------------------------------------------*/
 	// Find
 	/*-------------------------------------------------------------------------*/

	function find_date()
	{
		global $ibforums, $DB, $std, $print;

		if ( $ibforums->input['what'] )
		{
			if ( $ibforums->input['what'] == 'thismonth' )
			{
				$std->boink_it( $ibforums->base_url."act=calendar&amp;m={$this->now_date['mon']}&amp;y={$this->now_date['year']}" );
			}
			else
			{
				$time = time() + $std->get_time_offset();

				$std->boink_it( $ibforums->base_url."act=calendar&amp;code=showweek&amp;week={$time}" );
			}
		}
		else
		{
			$this->show_month();
		}
	}

	/*-------------------------------------------------------------------------*/
 	// Display Edit / Delete boxes
 	/*-------------------------------------------------------------------------*/

	function edit()
	{
		global $ibforums, $DB, $std, $print;

    	if ( $ibforums->member['g_calendar_post'] != 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		if ( ! $ibforums->member['id'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		$eventid   = intval($ibforums->input['eventid']);

		if ($eventid == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
		}

        $DB->simple_construct( array( 'select' => '*', 'from' => 'calendar_events', 'where' => "eventid=$eventid" ) );
        $DB->simple_exec();

        if ( ! $event = $DB->fetch_row() )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
        }

        $can_edit = 0;

        //-----------------------------------------
        // Do we have permission to edit this event?
        //-----------------------------------------

        if ($ibforums->member['id'] == $event['userid'])
        {
        	$can_edit = 1;
        }
        else if ($ibforums->member['g_is_supmod'] == 1)
        {
        	$can_edit = 1;
        }
        else
        {
        	$can_edit = 0;
        }

        if ($can_edit != 1)
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
        }

        //-----------------------------------------
        // Do we have permission to see the event?
        //-----------------------------------------

        if ( $event['read_perms'] != '*' )
		{
			if ( ! preg_match( "/(^|,)".$ibforums->member['mgroup']."(,|$)/", $event['read_perms'] ) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
			}
		}

		$ibforums->lang['the_max_length'] = $ibforums->vars['max_post_length'] ? $ibforums->vars['max_post_length'] * 1024 : 2140000;

		$this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
		$this->nav[] = $ibforums->lang['edit_event']." ".$event['title'];

		$checked = array( 'normal' => '', 'range' => '', 'recur' => '' );

		if ( $event['event_ranged'] )
		{
			$range_on  = 'show';
			$range_off = 'none';
			$recur_on  = 'none';
			$recur_off = 'show';
			$checked['range'] = 'checked="checked"';
		}
		else if ( $event['event_repeat'] )
		{
			$range_on  = 'none';
			$range_off = 'show';
			$recur_on  = 'show';
			$recur_off = 'none';
			$checked['recur'] = 'checked="checked"';
		}
		else
		{
			$range_on  = 'none';
			$range_off = 'show';
			$recur_on  = 'none';
			$recur_off = 'show';
			$checked['normal'] = 'checked="checked"';
		}

		$recur_menu = "";

		$recur_menu .= $event['repeat_unit'] == 'w' ? "<option value='w' selected='selected'>{$ibforums->lang['fv_days']}</option>"
												    : "<option value='w'>{$ibforums->lang['fv_days']}</option>";

		$recur_menu .= $event['repeat_unit'] == 'm' ? "<option value='m' selected='selected'>{$ibforums->lang['fv_months']}</option>"
												    : "<option value='m'>{$ibforums->lang['fv_months']}</option>";

		$recur_menu .= $event['repeat_unit'] == 'y' ? "<option value='y' selected='selected'>{$ibforums->lang['fv_years']}</option>"
												    : "<option value='y'>{$ibforums->lang['fv_years']}</option>";

		$this->output .= $this->post_html->get_javascript();

		$this->output .= $this->html->calendar_start_edit_form($event['eventid']);
		$this->output .= $this->html->table_top($ibforums->lang['edit_event']);
		$this->output .= $this->html->calendar_delete_box();
		$this->output .= $this->html->calendar_event_title($event['title']);
		$this->output .= $this->html->calendar_choose_date(
															 $this->get_day_dropdown($event['mday']), $this->get_month_dropdown($event['month']), $this->get_year_dropdown($event['year']),
															 $this->get_day_dropdown($event['end_day']), $this->get_month_dropdown($event['end_month']), $this->get_year_dropdown($event['end_year']),
															 $this->get_day_dropdown("1"), $recur_menu,
															 array(
															 		'range_on'  => $range_on,
															 		'range_off' => $range_off,
															 		'recur_on'  => $recur_on,
															 		'recur_off' => $recur_off
															 	  ),
															 $checked,
															 array(
															 	     'bg' => $this->get_color_dropdown('bg',$event['event_bgcolor']),
															 	     'ft' => $this->get_color_dropdown('ft',$event['event_color'])
															 	  ),
															 array(
															 		'd' => $this->get_day_dropdown($event['end_day']),
															 		'm' => $this->get_month_dropdown($event['end_month']),
															 		'y' => $this->get_year_dropdown($event['end_year'])
															 	  )
														  );

		$public  = "";
		$private = "";

		if ($event['priv_event'] == 1)
		{
			$private = ' selected';
		}
		else
		{
			$public = ' selected';
		}

		$this->output .= $this->html->calendar_event_type($public, $private);

		if ($ibforums->member['mgroup'] == $ibforums->vars['admin_group'])
		{
			//-----------------------------------------
			// Get all the group ID's and names from the DB and build the selection box
			//-----------------------------------------

			$group_choices = "";

			foreach( $ibforums->cache['group_cache'] as $id => $r )
			{
				$selected = "";

				if ( preg_match( "/(^|,)".$r['g_id']."(,|$)/", $event['read_perms'] ) )
				{
					$selected = ' selected';
				}

				$group_choices .= "<option value='".$r['g_id']."'".$selected.">".$r['g_title']."</option>\n";
			}

			$this->output .= $this->html->calendar_admin_group_box($group_choices);
		}

		$this->output .= $this->post_html->postbox_buttons( $std->my_br2nl($event['event_text']) );

		$this->output  = str_replace( '<!--IBF.EMO-->'  , $this->post_html->get_box_enableemo( $event['show_emoticons'] ? 'checked="checked"' : "" )  , $this->output );

		$this->output .= $this->html->calendar_end_form($ibforums->lang['calendar_edit_submit']);

 		//-----------------------------------------
 		// Add in the smilies box
 		//-----------------------------------------

 		require_once( ROOT_PATH.'sources/post.php' );
 		$this->post       = new post();
 		$this->post->html = $this->post_html;

 		$this->output = $this->post->html_add_smilie_box( $this->output );
	}

	/*-------------------------------------------------------------------------*/
	// Do the edit
	/*-------------------------------------------------------------------------*/

	function do_edit($del=0)
	{
		global $ibforums, $DB, $std, $print;

    	if ( $ibforums->member['g_calendar_post'] != 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		if ( ! $ibforums->member['id'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		$eventid   = intval($ibforums->input['eventid']);

		if ($eventid == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
		}

        $DB->simple_construct( array( 'select' => '*', 'from' => 'calendar_events', 'where' => "eventid=$eventid" ) );
        $DB->simple_exec();

        if ( ! $event = $DB->fetch_row() )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
        }

		$can_edit = 0;

        //-----------------------------------------
        // Do we have permission to edit this event?
        //-----------------------------------------

        if ($ibforums->member['id'] == $event['userid'])
        {
        	$can_edit = 1;
        }
        else if ($ibforums->member['g_is_supmod'] == 1)
        {
        	$can_edit = 1;
        }
        else
        {
        	$can_edit = 0;
        }

        if ($can_edit != 1)
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
        }

        //-----------------------------------------
        // Are we deleting this event?
        //-----------------------------------------

        if ( $_POST['event_delete'] == 1 or $del == 1 )
        {
        	$DB->simple_exec_query( array( 'delete' => 'calendar_events', 'where' => "eventid=$eventid" ) );

        	$this->_call_recache();

			$print->redirect_screen( $ibforums->lang['delete_event_redirect'] , "&act=calendar" );
        }
        else
        {
			$ibforums->vars['max_post_length'] = $ibforums->vars['max_post_length'] ? $ibforums->vars['max_post_length'] : 2140000;

			//-----------------------------------------
			// Sort out some of the form data, check for
			// posting length, etc.
			//-----------------------------------------

			$allow_emoticons = $ibforums->input['enableemo'] == 'yes'     ? 1 : 0;
			$private_event   = $ibforums->input['e_type']    == 'private' ? 1 : 0;

			//-----------------------------------------
			// Do we have a valid post?
			//-----------------------------------------

			if (strlen( trim($_POST['Post']) ) < 1)
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_post') );
			}

			if (strlen( $_POST['Post'] ) > ($ibforums->vars['max_post_length']*1024))
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'post_too_long') );
			}

			//-----------------------------------------
			// Fix up the Event Title
			//-----------------------------------------

			$ibforums->input['event_title'] = str_replace( "<br>", "", $ibforums->input['event_title'] );

			$ibforums->input['event_title'] = trim(stripslashes($ibforums->input['event_title']));

			if ( (strlen($ibforums->input['event_title']) < 2) or (!$ibforums->input['event_title'])  )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_title_none') );
			}

			if ( strlen($ibforums->input['event_title']) > 64 )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_title_long') );
			}

			//-----------------------------------------
			// Are we an admin, and have we set w/groups
			// can see?
			//-----------------------------------------

			$read_perms = '*';

			if ($ibforums->member['mgroup'] == $ibforums->vars['admin_group'])
			{
				if ( is_array( $_POST['e_groups'] ) )
				{
					$read_perms = implode( ",", $_POST['e_groups'] );
					$read_perms .= ",".$ibforums->vars['admin_group'];
				}

				if ($read_perms == "")
				{
					$read_perms = '*';
				}
			}

			$day   = intval($ibforums->input['e_day']);
			$month = intval($ibforums->input['e_month']);
			$year  = intval($ibforums->input['e_year']);

			$end_day   = "";
			$end_month = "";
			$end_year  = "";

			$end_date     = "";
			$event_ranged = 0;
			$event_repeat = 0;

			if ( $ibforums->input['eventtype'] == 'range' )
			{
				$end_day   = intval($ibforums->input['end_day']);
				$end_month = intval($ibforums->input['end_month']);
				$end_year  = intval($ibforums->input['end_year']);

				if ( $end_year < $year )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
				}

				if ( $end_year == $year )
				{
					if ( $end_month < $month )
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
					}

					if ( $end_month == $month AND $end_day <= $day )
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
					}
				}

				$event_ranged = 1;
			}
			elseif ($ibforums->input['eventtype'] == 'recur')
			{
				$end_day   = intval($ibforums->input['recend_day']);
				$end_month = intval($ibforums->input['recend_month']);
				$end_year  = intval($ibforums->input['recend_year']);

				if ( $ibforums->input['recur_unit'] )
				{
					$event_repeat = 1;
				}

				if ( $end_year < $year )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
				}

				if ( $end_year == $year )
				{
					if ( $end_month < $month )
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
					}

					if ( $end_month == $month AND $end_day <= $day )
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
					}
				}
			}

			//-----------------------------------------
			// Do we have a sensible date?
			//-----------------------------------------

			if ( ! checkdate( $month, $day , $year ) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_date_oor') );
			}

			//-----------------------------------------
			// Add it to the DB
			//-----------------------------------------

			$DB->do_update( 'calendar_events', array (
													   'year'           => $year,
													   'month'          => $month,
													   'mday'           => $day,
													   'title'          => $ibforums->input['event_title'],
													   'event_text'     => $ibforums->input['Post'],
													   'read_perms'     => $read_perms,
													   'priv_event'     => $private_event,
													   'unix_stamp'     => mktime( 0, 0, 1, $month, $day, $year),
													   'show_emoticons' => $allow_emoticons,
													   'end_day'        => $end_day,
													   'end_month'		=> $end_month,
													   'end_year'       => $end_year,
													   'end_unix_stamp' => mktime( 23, 59, 59, $end_month, $end_day, $end_year),
													   'event_ranged'   => $event_ranged,
													   'event_bgcolor'  => $ibforums->input['bg'],
													   'event_color'    => $ibforums->input['ft'],
													   'event_repeat'   => $event_repeat,
													   'repeat_unit'    => $ibforums->input['recur_unit'],
													  )
							, "eventid=$eventid"   );

			$this->_call_recache();

			$print->redirect_screen( $ibforums->lang['edit_event_redirect'] , "&act=calendar&code=showevent&eventid=$eventid" );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Show WEEK
	/*-------------------------------------------------------------------------*/

	function show_week()
	{
		global $ibforums, $DB, $std, $print;

        $in_week  = intval($ibforums->input['week']);

        //-----------------------------------------
        // Get start of week
        //-----------------------------------------

        $startweek = getdate( $in_week );

        //-----------------------------------------
        // Not Sunday? Go back..
        //-----------------------------------------

        $startweek['wday'] = intval($startweek['wday']);

        if ( $startweek['wday'] > 0 )
        {
			while ( $startweek['wday'] != 0 )
			{
				$startweek['wday']--;
				$in_week -= 86400;
			}

			$startweek = getdate( $in_week );
        }

        //-----------------------------------------
        // Get end of week
        //-----------------------------------------

        $endweek   = getdate( $in_week + 604800 );

        $our_timestamp = $in_week;

        //-----------------------------------------
        // Figure out the next / previous links
        //-----------------------------------------

        $prev_month = $this->get_prev_month($this->chosen_month, $this->chosen_year);
        $next_month = $this->get_next_month($this->chosen_month, $this->chosen_year);

        $prev_week = getdate( $in_week - 604800 );
        $next_week = getdate( $in_week + 604800 );

        $this->output .= $this->html->cal_week_content( $startweek['mday'], $this->month_words[$startweek['mon'] - 1], $startweek['year'], $prev_week, $next_week);

        $last_month_id = -1;

		//-----------------------------------------
		// Get events
		//-----------------------------------------

		$seen_days = array(); // Holds yday
		$seen_ids  = array();

        //-----------------------------------------
        // Print each effing day :D
        //-----------------------------------------

        for ( $i = 0 ; $i <= 6 ; $i++ )
        {
        	$today = getdate( $our_timestamp );

        	if ( $last_month_id != $today['mon'] )
        	{
        		$last_month_id = $today['mon'];

        		$cal_output .= $this->html->cal_week_monthbar( $this->month_words[$today['mon'] - 1], $today['year'] );

        		//-----------------------------------------
				// Get the birthdays from the database
				//-----------------------------------------

				if ( $ibforums->vars['show_bday_calendar'] )
				{
					$birthdays = array();

					$this->_get_birthday_sql($today['mon']);

					$birthdays = $this->query_bday_cache[ $today['mon'] ];
				}

				//-----------------------------------------
				// Get the events
				//-----------------------------------------

				$this->_get_events_sql($today['mon'], $today['year']);

				$this->_process_events_sql($today['mon']);

				$recurring = $this->recurring;
				$events    = $this->events;
				$range     = $this->ranged;

				//-----------------------------------------
				// Flip the range array to ensure that old
				// ranges go to the top for consistency
				//-----------------------------------------

				if ( is_array($range) )
				{
					$range = array_reverse($range, TRUE);
				}
			}

        	//-----------------------------------------
			// Ranged Events...
			//-----------------------------------------

			if ( count($range) > 0 )
			{
				foreach( $range as $r => $d)
				{
					$s   = 0;

					if ( ( $our_timestamp >= $d['unix_stamp'] ) AND ($our_timestamp <= $d['end_unix_stamp']) )
					{
						$this_day_events .= $this->html->cal_week_events_wrap_range( "code=showevent&eventid=".$d['eventid'], $d['title'], $d['ft'], $d['bg']);

						$seen_ids[$d['eventid']] = 1;
					}
				}
			}

			//-----------------------------------------
			// Recurring Events...
			//-----------------------------------------

			if ( count($recurring) > 0 )
			{
				foreach( $recurring as $rc )
				{
					$recur_start = getdate( $rc['unix_stamp'] );

					$sh = 0;

					//-----------------------------------------
					// Out of range END?
					//-----------------------------------------

					if ( $today['year'] > $rc['end_year'] )
					{
						continue;
					}
					else if ( $today['year'] == $rc['end_year'] )
					{
						if ( $today['mon'] > $rc['end_month'] )
						{
						   continue;
						}
						else if ( ($today['mon'] == $rc['end_month']) AND ($today['mday'] > $rc['end_day']) )
						{
						   continue;
						}
					}

					//-----------------------------------------
					// Out of range START?
					//-----------------------------------------

					if ( $today['year'] < $rc['year'] )
					{
						continue;
					}
					else if ( $today['year'] == $rc['year'] )
					{
						if ( $today['mon'] < $rc['month'] )
						{
						   continue;
						}
						else if ( ($today['mon'] == $rc['month']) AND ($today['mday'] < $rc['mday']) )
						{
						   continue;
						}
					}

					//-----------------------------------------
					// Lets do da WEEKLY
					//-----------------------------------------

					if ( $rc['repeat_unit'] == 'w' )
					{
						if ( $today['wday'] == $recur_start['wday'])
						{
							$sh = 1;
						}
					}

					//-----------------------------------------
					// Lets do da MONTHLY
					//-----------------------------------------

					elseif ( $rc['repeat_unit'] == 'm' )
					{
						if ( $today['mday'] == $rc['mday'] )
						{
							$sh = 1;
						}
					}

					//-----------------------------------------
					// Lets do da YEARLY
					//-----------------------------------------

					elseif ( $rc['repeat_unit'] == 'y' )
					{
						if ( ($today['mday'] == $rc['mday']) AND ($today['mon'] == $rc['month']) )
						{
							$sh = 1;
						}
					}

					//-----------------------------------------
					// Show it!
					//-----------------------------------------

					if ( $sh == 1 )
					{
						$this_day_events .= $this->html->cal_week_events_wrap("code=showevent&eventid=".$rc['eventid'],$rc['title']);
					}
				}
			}

			//-----------------------------------------
			// Normal Events...
			//-----------------------------------------

			if ( isset($events[ $today['mday'] ]) and is_array($events[ $today['mday'] ]) )
			{
				if ( count( $events[ $today['mday'] ] ) > 0 )
				{
					foreach( $events[ $today['mday'] ] as $idx => $data )
					{
						if ( $seen_ids[ $data['eventid'] ] == 1 )
						{
							continue;
						}

						$this_day_events .= $this->html->cal_week_events_wrap("code=showevent&eventid=".$data['eventid'],$data['title']);
					}
				}
			}

			//-----------------------------------------
			// Birthdays
			//-----------------------------------------

			if ( isset($birthdays[ $today['mday'] ]) and count( $birthdays[ $today['mday'] ] ) > 0 )
			{
				$no_bdays = count($birthdays[ $today['mday'] ]);

				if ( $ibforums->vars['bday_show_cal_max'] and $no_bdays <= $ibforums->vars['bday_show_cal_max'] )
				{
					foreach( $birthdays[ $today['mday'] ] as $idx => $user )
					{
						$this_day_events .= $this->html->cal_week_events_wrap(
																		 "code=birthdays&y=".$today['year']."&m=".$today['mon']."&d=".$today['mday'],
																		 $user['name'].$ibforums->lang['bd_birthday']
																	   );
					}

				}
				else
				{
					$this_day_events .= $this->html->cal_week_events_wrap(
																	 "code=birthdays&y=".$today['year']."&m=".$today['mon']."&d=".$today['mday'],
																	 sprintf( $ibforums->lang['entry_birthdays'], count($birthdays[ $today['mday'] ]) )
																   );
				}
			}

			if ($this_day_events == "")
			{
				$this_day_events = '&nbsp;';
			}

        	$cal_output .= $this->html->cal_week_dayentry( $this->day_words[ $today['wday'] ], $today['mday'], $this->month_words[$today['mon'] - 1], $today['mon'], $today['year'], $this_day_events );

        	$our_timestamp += 86400;

        	unset($this_day_events);
        }

        //-----------------------------------------
        // Switch the HTML tags...
        //-----------------------------------------

        $this->output = str_replace( "<!--IBF.DAYS_CONTENT-->"  , $cal_output, $this->output );

        $this->output = str_replace( "<!--IBF.MONTH_BOX-->"     , $this->get_month_dropdown(), $this->output );
        $this->output = str_replace( "<!--IBF.YEAR_BOX-->"      , $this->get_year_dropdown() , $this->output );

        //-----------------------------------------
        // Get prev / this / next calendars
        //-----------------------------------------

        $this->output = str_replace( "<!--PREV.MONTH-->", $this->get_mini_calendar( $prev_month['month_id'], $prev_month['year_id'] ), $this->output );
        $this->output = str_replace( "<!--THIS.MONTH-->", $this->get_mini_calendar( $this->chosen_month    , $this->chosen_year     ), $this->output );
        $this->output = str_replace( "<!--NEXT.MONTH-->", $this->get_mini_calendar( $next_month['month_id'], $next_month['year_id'] ), $this->output );

        $this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
        $this->nav[] = $this->month_words[$this->chosen_month - 1]." ".$this->chosen_year;

    }

	/*-------------------------------------------------------------------------*/
	// SHOW MONTH
	/*-------------------------------------------------------------------------*/

	function show_month()
	{
		global $ibforums, $DB, $std, $print;

        //-----------------------------------------
        // Figure out the next / previous links
        //-----------------------------------------

        $prev_month = $this->get_prev_month($this->chosen_month, $this->chosen_year);
        $next_month = $this->get_next_month($this->chosen_month, $this->chosen_year);

        $this->output .= $this->html->cal_main_content($this->month_words[$this->chosen_month - 1], $this->chosen_year, $prev_month, $next_month);

        //-----------------------------------------
        // Print the days table top row
        //-----------------------------------------

        $day_output = "";
        $cal_output = "";

		foreach ($this->day_words as $day)
        {
        	$day_output .= $this->html->cal_day_bit($day);
        }

        $cal_output = $this->get_month_events($this->chosen_month, $this->chosen_year);

        //-----------------------------------------
        // Switch the HTML tags...
        //-----------------------------------------

        $this->output = str_replace( "<!--IBF.DAYS_TITLE_ROW-->", $day_output, $this->output );
        $this->output = str_replace( "<!--IBF.DAYS_CONTENT-->"  , $cal_output, $this->output );

        $this->output = str_replace( "<!--IBF.MONTH_BOX-->"     , $this->get_month_dropdown(), $this->output );
        $this->output = str_replace( "<!--IBF.YEAR_BOX-->"      , $this->get_year_dropdown() , $this->output );

        //-----------------------------------------
        // Get prev / this / next calendars
        //-----------------------------------------

        $this->output = str_replace( "<!--PREV.MONTH-->", $this->get_mini_calendar( $prev_month['month_id'], $prev_month['year_id'] ), $this->output );
        $this->output = str_replace( "<!--THIS.MONTH-->", $this->get_mini_calendar( $this->chosen_month    , $this->chosen_year     ), $this->output );
        $this->output = str_replace( "<!--NEXT.MONTH-->", $this->get_mini_calendar( $next_month['month_id'], $next_month['year_id'] ), $this->output );

        $this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
        $this->nav[] = $this->month_words[$this->chosen_month - 1]." ".$this->chosen_year;
    }

    /*-------------------------------------------------------------------------*/
    // POST NEW CALENDAR EVENT
    /*-------------------------------------------------------------------------*/

    function new_event()
    {
		global $ibforums, $DB, $std, $print;

    	if ( $ibforums->member['g_calendar_post'] != 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		if ( ! $ibforums->member['id'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		//-----------------------------------------
		// Got date?
		//-----------------------------------------

		$tmp = explode( "-", $ibforums->input['nd'] );

		$nd = intval( $tmp[2] );
		$nm = intval( $tmp[1] );
		$ny = intval( $tmp[0] );

		$nd = $nd ? $nd : $this->now['mday'];
		$nm = $nm ? $nm : $this->now['mon'];
		$ny = $ny ? $ny : $this->now['year'];

		$ibforums->lang['the_max_length'] = $ibforums->vars['max_post_length'] ? $ibforums->vars['max_post_length'] * 1024 : 2140000;

		$this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
		$this->nav[] = $ibforums->lang['post_new_event'];

		$this->output .= $this->post_html->get_javascript();

		$this->output .= $this->html->calendar_start_form();

		$this->output .= $this->html->table_top($ibforums->lang['post_new_event']);
		$this->output .= $this->html->calendar_event_title();
		$this->output .= $this->html->calendar_choose_date(
															 $this->get_day_dropdown($nd), $this->get_month_dropdown($nm), $this->get_year_dropdown($ny),
															 $this->get_day_dropdown($nd), $this->get_month_dropdown($nm), $this->get_year_dropdown($ny),
															 $this->get_day_dropdown("1"), "<option value='w'>{$ibforums->lang['fv_days']}</option><option value='m'>{$ibforums->lang['fv_months']}</option><option value='y'>{$ibforums->lang['fv_years']}</option>",
															 array( 'range_on' => 'none', 'range_off' => 'show', 'recur_on' => 'none', 'recur_off' => 'show' ),
															 array( 'range' => '', 'recur' => '', 'normal' => 'checked="checked"' ),
															 array( 'bg' => $this->get_color_dropdown('bg','darkblue'), 'ft' => $this->get_color_dropdown('ft','white') ),
															 array(
															 		'd' => $this->get_day_dropdown(),
															 		'm' => $this->get_month_dropdown(),
															 		'y' => $this->get_year_dropdown()
															 	  )
														  );

		$this->output .= $this->html->calendar_event_type();

		if ($ibforums->member['mgroup'] == $ibforums->vars['admin_group'])
		{
			//-----------------------------------------
			// Get all the group ID's and names from
			// the DB and build the selection box
			//-----------------------------------------

			$group_choices = "";

			foreach( $ibforums->cache['group_cache'] as $i => $r )
			{
				$group_choices .= "<option value='".$r['g_id']."'>".$r['g_title']."</option>\n";
			}

			$this->output .= $this->html->calendar_admin_group_box($group_choices);
		}

		$this->output .= $this->post_html->postbox_buttons("");

		$this->output  = str_replace( '<!--IBF.EMO-->'  , $this->post_html->get_box_enableemo( 'checked="checked"' )  , $this->output );

		$this->output .= $this->html->calendar_end_form($ibforums->lang['calendar_submit']);

 		//-----------------------------------------
 		// Add in the smilies box
 		//-----------------------------------------

 		require_once( ROOT_PATH.'sources/post.php' );
 		$this->post       = new post();
 		$this->post->html = $this->post_html;

 		$this->output = $this->post->html_add_smilie_box( $this->output );
	}


    /*-------------------------------------------------------------------------*/
    // ADD NEW CALENDAR EVENT TO THE DB
    /*-------------------------------------------------------------------------*/

    function add_new_event()
    {
		global $ibforums, $DB, $std, $print, $HTTP_POST_VARS;

    	if ( $ibforums->member['g_calendar_post'] != 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		if ( ! $ibforums->member['id'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		$ibforums->vars['max_post_length'] = $ibforums->vars['max_post_length'] ? $ibforums->vars['max_post_length'] : 2140000;

		//-----------------------------------------
		// Sort out some of the form data, check for
		// posting length, etc.
		//-----------------------------------------

		$allow_emoticons = $ibforums->input['enableemo'] == 'yes'     ? 1 : 0;
		$private_event   = $ibforums->input['e_type']    == 'private' ? 1 : 0;

		//-----------------------------------------
		// Do we have a valid post?
		//-----------------------------------------

		if (strlen( trim($_POST['Post']) ) < 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_post') );
		}

		if (strlen( $_POST['Post'] ) > ($ibforums->vars['max_post_length']*1024))
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'post_too_long') );
		}

		//-----------------------------------------
		// Fix up the Event Title
		//-----------------------------------------

		$ibforums->input['event_title'] = str_replace( "<br>", "", $ibforums->input['event_title'] );

		$ibforums->input['event_title'] = trim(stripslashes($ibforums->input['event_title']));

		if ( (strlen($ibforums->input['event_title']) < 2) or (!$ibforums->input['event_title'])  )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_title_none') );
		}

		if ( strlen($ibforums->input['event_title']) > 64 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_title_long') );
		}

		//-----------------------------------------
		// Are we an admin, and have we set w/groups
		// can see?
		//-----------------------------------------

		$read_perms = '*';

		if ($ibforums->member['mgroup'] == $ibforums->vars['admin_group'])
		{
			if ( is_array( $_POST['e_groups'] ) )
			{
				$read_perms = implode( ",", $_POST['e_groups'] );
				$read_perms .= ",".$ibforums->vars['admin_group'];
			}

			if ($read_perms == "")
			{
				$read_perms = '*';
			}
		}

		$day   = intval($ibforums->input['e_day']);
		$month = intval($ibforums->input['e_month']);
		$year  = intval($ibforums->input['e_year']);

		$end_day   = "";
		$end_month = "";
		$end_year  = "";

		$end_date     = "";
		$event_ranged = 0;
		$event_repeat = 0;

		if ( $ibforums->input['eventtype'] == 'range' )
		{
			$end_day   = intval($ibforums->input['end_day']);
			$end_month = intval($ibforums->input['end_month']);
			$end_year  = intval($ibforums->input['end_year']);

			if ( $end_year < $year )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
			}

			if ( $end_year == $year )
			{
				if ( $end_month < $month )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
				}

				if ( $end_month == $month AND $end_day <= $day )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
				}
			}

			$event_ranged = 1;
		}
		elseif ($ibforums->input['eventtype'] == 'recur')
		{
			$end_day   = intval($ibforums->input['recend_day']);
			$end_month = intval($ibforums->input['recend_month']);
			$end_year  = intval($ibforums->input['recend_year']);

			if ( $ibforums->input['recur_unit'] )
			{
				$event_repeat = 1;
			}

			if ( $end_year < $year )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
			}

			if ( $end_year == $year )
			{
				if ( $end_month < $month )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
				}

				if ( $end_month == $month AND $end_day <= $day )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_range_wrong') );
				}
			}
		}

		//-----------------------------------------
		// Do we have a sensible date?
		//-----------------------------------------

		if ( ! checkdate( $month, $day , $year ) )
        {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_date_oor') );
		}

		//-----------------------------------------
		// Add it to the DB
		//-----------------------------------------

		$DB->do_insert( 'calendar_events', array (
												   'userid'         => $ibforums->member['id'],
												   'year'           => $year,
												   'month'          => $month,
												   'mday'           => $day,
												   'title'          => $ibforums->input['event_title'],
												   'event_text'     => $ibforums->input['Post'],
												   'read_perms'     => $read_perms,
												   'unix_stamp'     => mktime( 0, 0, 1, $month, $day, $year),
												   'priv_event'     => $private_event,
												   'show_emoticons' => $allow_emoticons,
												   'end_day'        => $end_day,
												   'end_month'		=> $end_month,
												   'end_year'       => $end_year,
												   'end_unix_stamp' => mktime( 23, 59, 59, $end_month, $end_day, $end_year),
												   'event_ranged'   => $event_ranged,
												   'event_bgcolor'	=> $ibforums->input['bg'],
												   'event_color'	=> $ibforums->input['ft'],
												   'event_repeat'   => $event_repeat,
												   'repeat_unit'    => $ibforums->input['recur_unit'],
										 )       );

		$this->_call_recache();

		$print->redirect_screen( $ibforums->lang['new_event_redirect'] , "&act=calendar" );
	}


	/*-------------------------------------------------------------------------*/
    // SHOW DAYS EVENTS
    /*-------------------------------------------------------------------------*/

    function show_day()
    {
		global $ibforums, $DB, $std, $print;

        $day   = intval($ibforums->input['d']);
		$month = intval($ibforums->input['m']);
		$year  = intval($ibforums->input['y']);

		$day_array = getdate( mktime( 1,1,1, $month, $day, $year) );

		$printed = 0;

		//-----------------------------------------
		// Do we have a sensible date?
		//-----------------------------------------

		if ( ! checkdate( $month, $day , $year ) )
        {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_date_oor') );
		}

		require ROOT_PATH."sources/lib/post_parser.php";

        $this->parser = new post_parser();

        $this->output .= $this->html->cal_page_events_start();

        $seen = array();

        //-----------------------------------------
        // Get recurring events
        //-----------------------------------------

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'calendar_events',
									  'where'  => "event_repeat=1 AND ( repeat_unit IN ('w','m') OR (repeat_unit='y' AND month={$month}) )" ) );
		$DB->simple_exec();

        while ( $rc = $DB->fetch_row() )
        {
			$recur_start = getdate( $rc['unix_stamp'] );

			$sh = 0;

			//-----------------------------------------
			// Out of range END?
			//-----------------------------------------

			if ( $day_array['year'] > $rc['end_year'] )
			{
				continue;
			}
			else if ( $day_array['year'] == $rc['end_year'] )
			{
				if ( $day_array['mon'] > $rc['end_month'] )
				{
				   continue;
				}
				else if ( ($day_array['mon'] == $rc['end_month']) AND ($day_array['mday'] > $rc['end_day']) )
				{
				   continue;
				}
			}

			//-----------------------------------------
			// Out of range START?
			//-----------------------------------------

			if ( $day_array['year'] < $rc['year'] )
			{
				continue;
			}
			else if ( $day_array['year'] == $rc['year'] )
			{
				if ( $day_array['mon'] < $rc['month'] )
				{
				   continue;
				}
				else if ( ($day_array['mon'] == $rc['month']) AND ($day_array['mday'] < $rc['mday']) )
				{
				   continue;
				}
			}

			//-----------------------------------------
			// Lets do da WEEKLY
			//-----------------------------------------

			if ( $rc['repeat_unit'] == 'w' )
			{
				if ( $day_array['wday'] == $recur_start['wday'])
				{
					$sh = 1;
				}
			}

			//-----------------------------------------
			// Lets do da MONTHLY
			//-----------------------------------------

			elseif ( $rc['repeat_unit'] == 'm' )
			{
				if ( $day_array['mday'] == $rc['mday'] )
				{
					$sh = 1;
				}
			}

			//-----------------------------------------
			// Lets do da YEARLY
			//-----------------------------------------

			elseif ( $rc['repeat_unit'] == 'y' )
			{
				if ( ($day_array['mday'] == $rc['mday']) AND ($day_array['mon'] == $rc['month']) )
				{
					$sh = 1;
				}
			}

			//-----------------------------------------
			// Show it!
			//-----------------------------------------

			if ( $sh == 1 )
			{
				$this->output .= $this->make_event_html($rc);

				$seen[ $rc['eventid'] ] = 1;

				$printed++;
			}
		}

        //-----------------------------------------
        // Normal Events
        //-----------------------------------------

        $timenow  =  mktime( 1, 1, 1, $month, $day, $year);

        $DB->simple_construct( array( 'select' => '*',
							 		  'from'   => 'calendar_events',
							  		  'where'  => "month='$month' AND mday='$day' AND year='$year'
        				  				           OR (event_ranged=1 AND ( unix_stamp < $timenow AND end_unix_stamp > $timenow ) )" ) );

		$eq = $DB->simple_exec();

        if ( $DB->get_num_rows($eq) )
        {
			while ( $event = $DB->fetch_row($eq) )
			{
				if ( $event['event_ranged'] == 1 )
				{
					if ( ! $seen[ $event['eventid'] ] )
					{
						$range[$event['eventid']] = $event;
					}
				}
				else
				{
					//-----------------------------------------
					// Is it a private event?
					//-----------------------------------------

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

					if ( ! $seen[ $event['eventid'] ] )
					{
						$this->output .= $this->make_event_html($event);

						$printed++;
						$seen[ $event['eventid'] ] = 1;
					}
				}
			}

			//-----------------------------------------
			// Ranged Events...
			//-----------------------------------------

			if ( count($range) > 0 )
			{
				foreach( $range as $r => $d)
				{
					if ( $day_array['year'] == $d['year'] )
					{
						if ( ($day_array['mon'] == $d['end_month']) AND ( $day_array['mday'] <= $d['end_day'] AND $day_array['mday'] >= $d['mday'] ) )
						{
							$this->output .= $this->make_event_html($d);

							$printed++;
						}
					}
				}
			}
        }

        //-----------------------------------------
        // Do we have any printed events?
        //-----------------------------------------

        if ($printed > 0)
        {
        	$switch = 1;
        }
        else
        {
        	// Error if no birthdays
        	$switch = 0;
        }

        $this->output .= $this->make_birthday_html($month, $day, $switch);

        $this->output .= $this->html->cal_page_events_end();

        $this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
		$this->nav[] = $day." ".$this->month_words[$this->chosen_month - 1]." ".$this->chosen_year;
    }

	/*-------------------------------------------------------------------------*/
    // SHOW A SINGLE EVENT BASED ON eventid
    /*-------------------------------------------------------------------------*/

    function show_event()
    {
		global $ibforums, $DB, $std, $print;

        $eventid   = intval($ibforums->input['eventid']);

		if ($eventid == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
		}

        $DB->simple_construct( array( 'select' => '*', 'from' => 'calendar_events', 'where' => "eventid=$eventid" ) );
		$DB->simple_exec();

        if ( ! $event = $DB->fetch_row() )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
        }

        //-----------------------------------------
        // Is it a private event?
        //-----------------------------------------

        if ($event['priv_event'] == 1 and $ibforums->member['id'] != $event['userid'])
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
        }

        //-----------------------------------------
        // Do we have permission to see the event?
        //-----------------------------------------

        if ( $event['read_perms'] != '*' )
		{
			if ( ! preg_match( "/(^|,)".$ibforums->member['mgroup']."(,|$)/", $event['read_perms'] ) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
			}
		}

        //-----------------------------------------
        // Get the pre parsed event HTML
        //-----------------------------------------

        require "./sources/lib/post_parser.php";

        $this->parser = new post_parser();

        $this->output .= $this->html->cal_page_events_start() . $this->make_event_html($event) . $this->html->cal_page_events_end();

        $this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
		$this->nav[] = $event['title'];

    }

    /*-------------------------------------------------------------------------*/
    // MAKE EVENT HTML (return HTML for bdays)
    /*-------------------------------------------------------------------------*/

    function make_event_html($event)
    {
		global $ibforums, $DB, $std, $print;

        //-----------------------------------------
        // Parse the text
        //-----------------------------------------

        $event['event_text'] = $this->parser->convert( array( 'TEXT'    => $event['event_text'],
															  'SMILIES' => $event['show_emoticons'],
															  'CODE'    => 1,
															  'HTML'    => 0
													 )      );

		//-----------------------------------------
		// Parse HTML tag on the fly
		//-----------------------------------------

		$this->parser->pp_do_html  = 0;
		$this->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
		$this->parser->pp_nl2br    = 1;

		$event['event_text'] = $this->parser->post_db_parse( $event['event_text'] );

		//-----------------------------------------
        // What kind of event is it?
        //-----------------------------------------

        $event_type = $ibforums->lang['public_event'];

        if ($event['priv_event'] == 1)
        {
        	$event_type = $ibforums->lang['private_event'];
        }
        else if ($event['read_perms'] != '*')
        {
        	$event_type = $ibforums->lang['restricted_event'];
        }

        //-----------------------------------------
        // Do we have an edit button?
        //-----------------------------------------

        $edit_button = "";

        //-----------------------------------------
        // Are we a super dooper moderator?
        //-----------------------------------------

        if ($ibforums->member['g_is_supmod'] == 1)
        {
        	$edit_button = $this->html->cal_edit_del_button($event['eventid']);
        }

        //-----------------------------------------
        // Are we the OP of this event?
        //-----------------------------------------

        else if ($ibforums->member['id'] == $event['userid'])
        {
        	$edit_button = $this->html->cal_edit_del_button($event['eventid']);
        }

        //-----------------------------------------
        // Get the member details and stuff
        //-----------------------------------------

        $DB->cache_add_query( 'generic_get_all_member', array( 'mid' => $event['userid'] ) );
		$DB->cache_exec_query();

        $member = $DB->fetch_row();

        $member['joined'] = $std->get_date( $member['joined'], 'JOINED' );

        $member['avatar'] = $std->get_avatar( $member['avatar'], $ibforums->member['view_avs'], $member['avatar_size']);

        $member['posts']  = $std->do_number_format($member['posts']);

        $event['month_text'] = $this->month_words[$event['month'] - 1];

        $ibforums->input['d'] = $event['mday'];
        $ibforums->input['m'] = $event['month'];
        $ibforums->input['y'] = $event['year'];

        $type = $ibforums->lang['se_normal'];
        $de   = "";

        if ($event['event_ranged'] == 1 )
        {
        	$type = $ibforums->lang['se_range'];
        	$de   = $ibforums->lang['se_ends'].' '.date( $ibforums->vars['clock_joined'], $event['end_unix_stamp'] );
        }
        else if ( $event['event_repeat'] == 1 )
        {
        	$type = $ibforums->lang['se_recur'];
        	$de   = $ibforums->lang['se_ends'].' '.date( $ibforums->vars['clock_joined'], $event['end_unix_stamp'] );
        }

        return $this->html->cal_show_event($event, $member, $event_type, $edit_button, $type, $de );
    }

    /*-------------------------------------------------------------------------*/
    // GET MINI CALENDAR
    /*-------------------------------------------------------------------------*/

    function get_mini_calendar($month, $year)
    {
		global $ibforums, $DB, $std;

        foreach ($this->day_words as $day)
        {
        	$cal_output .= $this->html->mini_cal_day_bit( substr($day,0,1) );
        }

        //-----------------------------------------
        // Print the main calendar body
        //-----------------------------------------

        $cal_output .= $this->get_month_events( $month, $year, 1 );

        return $this->html->mini_cal_mini_wrap($this->month_words[$month - 1], $month, $year, $cal_output);
    }

    /*-------------------------------------------------------------------------*/
    // Get BIRTHDAYS
    /*-------------------------------------------------------------------------*/

    function _get_birthday_sql($month)
    {
		global $ibforums, $DB, $std;

    	if ( ! is_array( $this->query_bday_cache[ $month ] ) )
		{
			$this->query_bday_cache[ $month ] = array();

			$DB->simple_construct( array( 'select' => 'bday_day, bday_month, id, name', 'from' => 'members', 'where' => 'bday_month='.$month ) );
			$DB->simple_exec();

			while ($r = $DB->fetch_row())
			{
				$this->query_bday_cache[ $month ][ $r['bday_day'] ][] = $r;
			}
		}
    }

    /*-------------------------------------------------------------------------*/
    // Get EVENTS
    /*-------------------------------------------------------------------------*/

    function _get_events_sql($month, $year)
    {
		global $ibforums, $DB, $std;

    	$timenow  =  mktime( 0, 0, 1   , $month, 1, $year);
        $timethen =  mktime( 23, 59, 59, $month, 0, $year);

		//-----------------------------------------
        // Get the events
        //-----------------------------------------

        if ( ! is_array( $this->query_event_cache[ $month ] ) )
		{
			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'calendar_events',
										  'where'  => "event_repeat <> 1 AND month={$month} AND year={$year}
													   OR (event_ranged=1 AND ( unix_stamp < $timenow AND end_unix_stamp > $timenow ) )
													   OR (event_repeat=1 AND ( repeat_unit IN ('w','m') OR (repeat_unit='y' AND month={$month}) ) )" ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$this->query_event_cache[ $month ][] = $r;
			}
		}
    }

    function _process_events_sql($month)
    {
		global $ibforums, $DB, $std;

    	$this->recurring = array();
    	$this->ranged    = array();
    	$this->events    = array();

    	if ( is_array( $this->query_event_cache[ $month ] ) and count( $this->query_event_cache[ $month ] ) )
    	{
			foreach( $this->query_event_cache[ $month ] as $idx => $event )
			{
				if ( $event['priv_event'] == 1 )
				{
					if ( ! $ibforums->member['id'] )
					{
						continue;
					}

					if ($ibforums->member['id'] != $event['userid'])
					{
						continue;
					}
				}

				if ( $event['read_perms'] != '*' )
				{
					if ( ! preg_match( "/(^|,)".$ibforums->member['mgroup']."(,|$)/", $event['read_perms'] ) )
					{
						continue;
					}
				}

				if ( $event['event_repeat'] == 1 )
				{
					$this->recurring[] = $event;
				}
				else if ( $event['event_ranged'] == 1 )
				{
					$this->ranged[$event['eventid']] = array(
													 'end_unix_stamp'  => $event['end_unix_stamp'],
													 'unix_stamp'      => $event['unix_stamp'],
													 'day'             => $event['mday'],
													 'month'		   => $event['month'],
													 'year'		   	   => $event['year'],
													 'end_day'		   => $event['end_day'],
													 'end_month'	   => $event['end_month'],
													 'end_year'	       => $event['end_year'],
													 'ft'			   => $event['event_color'],
													 'bg'			   => $event['event_bgcolor'],
													 'eventid'		   => $event['eventid'],
													 'title'		   => $event['title'],
												   );
				}
				else
				{
					$this->events[ $event['mday'] ][] = $event;
				}
			}
        }
    }

    /*-------------------------------------------------------------------------*/
    // Get month stuff
    /*-------------------------------------------------------------------------*/

    function get_month_events($month="", $year="", $minical=0)
	{
		global $ibforums, $DB, $std, $print;

        $our_datestamp   = mktime( 1,0,0, $month, 1, $year);

        $first_day_array = getdate($our_datestamp);

        //-----------------------------------------
        // Get the birthdays from the database
        //-----------------------------------------

        if ( $ibforums->vars['show_bday_calendar'] )
        {
			$birthdays = array();

			$this->_get_birthday_sql($month);

			$birthdays = $this->query_bday_cache[ $month ];
		}

        $timenow  =  mktime( 0, 0, 1   , $month, 1, $year);
        $timethen =  mktime( 23, 59, 59, $month, 0, $year);

		//-----------------------------------------
        // Get the events
        //-----------------------------------------

        $this->_get_events_sql($month, $year);

        $this->_process_events_sql($month);

        $recurring = $this->recurring;
        $events    = $this->events;
        $range     = $this->ranged;


        //-----------------------------------------
        // Flip the range array to ensure that old
        // ranges go to the top for consistency
        //-----------------------------------------

        if ( is_array($range) )
        {
       		$range = array_reverse($range, TRUE);
        }

        //-----------------------------------------
        // Get events
        //-----------------------------------------

        $seen_days = array(); // Holds yday
        $seen_ids  = array();

        for ( $c = 0 ; $c < 42; $c++ )
        {
        	$day_array = getdate($our_datestamp);

        	if ( ! $minical )
        	{
        		//print $our_datestamp . ' :: '.$day_array['mon'] . ' - '. $day_array['mday'] . ' - '. $day_array['wday'] . ' - '. $day_array['yday']."<br />";
        	}

        	if ( (($c) % 7 ) == 0 )
        	{
        		//-----------------------------------------
        		// Kill the loop if we are no longer on our month
        		//-----------------------------------------

        		if ($day_array['mon'] != $month)
        		{
        			break;
        		}

        		if ( $minical )
        		{
        			$cal_output .= $this->html->mini_cal_new_row( $our_datestamp );
        		}
        		else
        		{
        			$cal_output .= $this->html->cal_new_row( $our_datestamp );
        		}
        	}

        	//-----------------------------------------
        	// Run out of legal days for this month?
        	// Or have we yet to get to the first day?
        	//-----------------------------------------

        	if ( ($c < $first_day_array['wday']) or ($day_array['mon'] != $month) )
        	{
        		$cal_output .= $minical ? $this->html->mini_cal_blank_cell() : $this->html->cal_blank_cell();
        	}
        	else
        	{
        		if ( $seen_days[ $day_array['yday'] ] == 1 )
				{
					continue;
				}

        		$seen_days[ $day_array['yday'] ] = 1;
        		$this_day_events = "";
        		$cal_date        = $day_array['mday'];

        		//-----------------------------------------
        		// Ranged Events...
        		//-----------------------------------------

        		if ( count($range) > 0 )
        		{
        			foreach( $range as $r => $d)
        			{
        				$s   = 0;

						if ( ( $our_datestamp >= $d['unix_stamp'] ) AND ($our_datestamp <= $d['end_unix_stamp']) )
						{
							$this_day_events .= $this->html->cal_events_wrap_range( "code=showevent&eventid=".$d['eventid'], $d['title'], $d['ft'], $d['bg']);

							$seen_ids[$d['eventid']] = 1;
						}
        			}
        		}

        		$our_datestamp += 86400;

				//-----------------------------------------
        		// Recurring Events...
        		//-----------------------------------------

        		if ( count($recurring) > 0 )
        		{
        			foreach( $recurring as $rc )
        			{
        				$recur_start = getdate( $rc['unix_stamp'] );

        				$sh = 0;

        				//-----------------------------------------
        				// Out of range END?
        				//-----------------------------------------

        				if ( $day_array['year'] > $rc['end_year'] )
        				{
        					continue;
        				}
        				else if ( $day_array['year'] == $rc['end_year'] )
        				{
        					if ( $day_array['mon'] > $rc['end_month'] )
							{
							   continue;
							}
							else if ( ($day_array['mon'] == $rc['end_month']) AND ($day_array['mday'] > $rc['end_day']) )
							{
							   continue;
							}
        				}

        				//-----------------------------------------
        				// Out of range START?
        				//-----------------------------------------

        				if ( $day_array['year'] < $rc['year'] )
        				{
        					continue;
        				}
        				else if ( $day_array['year'] == $rc['year'] )
        				{
        					if ( $day_array['mon'] < $rc['month'] )
							{
							   continue;
							}
							else if ( ($day_array['mon'] == $rc['month']) AND ($day_array['mday'] < $rc['mday']) )
							{
							   continue;
							}
        				}

						//-----------------------------------------
						// Lets do da WEEKLY
						//-----------------------------------------

						if ( $rc['repeat_unit'] == 'w' )
						{
							if ( $day_array['wday'] == $recur_start['wday'])
							{
								$sh = 1;
							}
						}

						//-----------------------------------------
						// Lets do da MONTHLY
						//-----------------------------------------

						elseif ( $rc['repeat_unit'] == 'm' )
						{
							if ( $day_array['mday'] == $rc['mday'] )
							{
								$sh = 1;
							}
						}

						//-----------------------------------------
						// Lets do da YEARLY
						//-----------------------------------------

						elseif ( $rc['repeat_unit'] == 'y' )
						{
							if ( ($day_array['mday'] == $rc['mday']) AND ($day_array['mon'] == $rc['month']) )
							{
								$sh = 1;
							}
						}

						//-----------------------------------------
						// Show it!
						//-----------------------------------------

						if ( $sh == 1 )
						{
							$this_day_events .= $this->html->cal_events_wrap_recurring("code=showevent&eventid=".$rc['eventid'],$rc['title']);
						}
					}
        		}

				//-----------------------------------------
				// Normal Events...
				//-----------------------------------------

				if ( isset($events[ $day_array['mday'] ]) and is_array($events[ $day_array['mday'] ]) )
				{
					if ( count( $events[ $day_array['mday'] ] ) > 0 )
					{
						foreach( $events[ $day_array['mday'] ] as $idx => $data )
						{
							if ( $seen_ids[ $data['eventid'] ] == 1 )
							{
								continue;
							}
							//print 'Event ' . $d['eventid'] . '=' . $our_datestamp . '=' . $d['unix_stamp'] . '<br />';
							$this_day_events .= $this->html->cal_events_wrap("code=showevent&eventid=".$data['eventid'],$data['title']);
						}
					}
				}

				//-----------------------------------------
				// Birthdays
				//-----------------------------------------

				if ( isset($birthdays[ $day_array['mday'] ]) and count( $birthdays[ $day_array['mday'] ] ) > 0 )
				{
					$no_bdays = count($birthdays[ $day_array['mday'] ]);

					if ( $no_bdays )
					{
						if ( $ibforums->vars['bday_show_cal_max'] and $no_bdays <= $ibforums->vars['bday_show_cal_max'] )
						{
							foreach( $birthdays[ $day_array['mday'] ] as $idx => $user )
							{
								$this_day_events .= $this->html->cal_events_wrap(
																				 "code=birthdays&y=".$day_array['year']."&m=".$day_array['mon']."&d=".$day_array['mday'],
																				 $user['name']."'s Birthday"
																			   );
							}

						}
						else
						{
							$this_day_events .= $this->html->cal_events_wrap(
																			 "code=birthdays&y=".$day_array['year']."&m=".$day_array['mon']."&d=".$day_array['mday'],
																			 sprintf( $ibforums->lang['entry_birthdays'], $no_bdays )
																		   );
						}
					}
				}

        		if ($this_day_events != "")
        		{
        			$cal_date = "<a href='{$ibforums->base_url}&act=calendar&code=showday&y=".$year."&m=".$month."&d=".$day_array['mday']."'>{$day_array['mday']}</a>";

        			$this_day_events = $this->html->cal_events_start() . $this_day_events . $this->html->cal_events_end();
        		}

        		if ( ($day_array['mday'] == $this->now_date['mday']) and ($this->now_date['mon'] == $day_array['mon']) and ($this->now_date['year'] == $day_array['year']))
        		{
        			$cal_output .= $minical ? $this->html->mini_cal_date_cell_today($cal_date, $this_day_events) : $this->html->cal_date_cell_today($cal_date, $this_day_events);
        		}
        		else
        		{
        			$cal_output .= $minical ? $this->html->mini_cal_date_cell($cal_date, $this_day_events) : $this->html->cal_date_cell($cal_date, $this_day_events);
        		}

        		unset($this_day_events);
        	}
        }

        return $cal_output;
    }

	/*-------------------------------------------------------------------------*/
    // SHOW BIRTHDAYS
    /*-------------------------------------------------------------------------*/

    function show_birthdays()
    {
		global $ibforums, $DB, $std, $print;

        $day   = intval($ibforums->input['d']);
		$month = intval($ibforums->input['m']);
		$year  = intval($ibforums->input['y']);

		//-----------------------------------------
		// Do we have a sensible date?
		//-----------------------------------------

		if ( ! checkdate( $month, $day , $year ) )
        {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_date_oor') );
		}

        $this->output .= $this->html->cal_page_events_start() . $this->make_birthday_html($month, $day) . $this->html->cal_page_events_end();

        $this->nav[] = "<a href='{$ibforums->base_url}&act=calendar'>{$ibforums->lang['page_title']}</a>";
		$this->nav[] = $ibforums->lang['cal_birthdays']." ".$this->month_words[$this->chosen_month - 1]." ".$this->chosen_year;

    }

    /*-------------------------------------------------------------------------*/
    // MAKE BIRTHDAY HTML (return HTML for bdays)
    /*-------------------------------------------------------------------------*/

    function make_birthday_html($month, $day, $switch=0)
    {
		global $ibforums, $DB, $std, $print;

    	if ( ! $ibforums->vars['show_bday_calendar'] )
    	{
    		return;
    	}

    	//-----------------------------------------
        // Get the birthdays from the database
        //-----------------------------------------

        $birthdays = array();

        $output    = "";

        $DB->simple_construct( array( 'select' => 'bday_day, bday_month, bday_year, id, name',
        							  'from'   => 'members',
        							  'where'  => 'bday_month='.$month . " AND bday_day={$day}" ) );
		$DB->simple_exec();

		if ( ! $DB->get_num_rows() )
		{
			if ($switch == 1)
			{
				return;
			}
			else
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cal_no_events') );
			}
		}
		else
		{
			$output .= $this->html->cal_birthday_start();

			while ( $r = $DB->fetch_row() )
			{
				$age = $this->chosen_year - $r['bday_year'];

				$output .= $this->html->cal_birthday_entry($r['id'], $r['name'], $age);
			}

			$output .= $this->html->cal_birthday_end();
		}

		return $output;
	}

    /*-------------------------------------------------------------------------*/
    // Internal
    /*-------------------------------------------------------------------------*/

    function get_month_dropdown($month="")
    {
		global $ibforums;

    	$return = "";

    	if ($month == "")
    	{
    		$month = $this->chosen_month;
    	}

    	for ( $x = 1 ; $x <= 12 ; $x++ )
    	{
    		$return .= "\t<option value='$x'";
    		$return .= ($x == $month) ? " selected" : "";
    		$return .= ">".$this->month_words[$x-1]."\n";
    	}

    	return $return;
    }


    /*-------------------------------------------------------------------------*/
    // Internal
    /*-------------------------------------------------------------------------*/

    function get_year_dropdown($year="")
    {
		global $ibforums;

    	$return = "";

    	$ibforums->vars['start_year'] = (isset($ibforums->vars['start_year'])) ? $ibforums->vars['start_year'] : 2001;
		$ibforums->vars['year_limit'] = (isset($ibforums->vars['year_limit'])) ? $ibforums->vars['year_limit'] : 5;

    	if ($year == "")
    	{
    		$year = $this->chosen_year;
    	}

    	for ( $x = $ibforums->vars['start_year'] ; $x <= $this->now_date['year'] + $ibforums->vars['year_limit'] ; $x++ )
    	{
    		$return .= "\t<option value='$x'";
    		$return .= ($x == $year) ? " selected" : "";
    		$return .= ">".$x."\n";
    	}

    	return $return;
    }


    /*-------------------------------------------------------------------------*/
    // Internal
    /*-------------------------------------------------------------------------*/

    function get_day_dropdown($day="")
    {
		global $ibforums;

    	if ($day == "")
    	{
    		$day = $this->now_date['mday'];
    	}

    	$return = "";

    	for ( $x = 1 ; $x <= 31 ; $x++ )
    	{
    		$return .= "\t<option value='$x'";
    		$return .= ($x == $day) ? " selected" : "";
    		$return .= ">".$x."\n";
    	}

    	return $return;
    }

    /*-------------------------------------------------------------------------*/
    // Internal
    /*-------------------------------------------------------------------------*/

    function get_color_dropdown($name, $active="")
    {
		global $ibforums;

    	$c = array( 'white', 'black', 'aliceblue', 'lightslategray', 'blue','gray', 'yellow', 'orange', 'red', 'lightblue', 'darkblue', 'aqua', 'green', 'lime', 'maroon', 'navy', 'silver', 'teal' );

    	$return = "<select name='$name' class='forminput' onchange=\"document.REPLIER.style{$name}.style.backgroundColor = this.options[this.selectedIndex].value;\">";

    	foreach( $c as $i )
    	{
    		$sel = "";

    		if ( $active == $i )
    		{
    			$sel = ' selected="selected"';
    		}

    		$return .= "<option value='$i'{$sel}>$i</option>\n";
    	}

    	return $return."</select>";

    }

    /*-------------------------------------------------------------------------*/
    // Internal
    /*-------------------------------------------------------------------------*/

    function get_next_month($month, $year)
    {
    	//-----------------------------------------
        // Figure out the next  links
        //-----------------------------------------

        $next_month = array();

        $next_month['year_id']    = $year;

        $next_month['month_name'] = $this->month_words[$month];
        $next_month['month_id']   = $month + 1;

        if ($this->chosen_month == 12)
        {
        	$next_month['month_name'] = $this->month_words[0];
            $next_month['month_id']   = 1;
            $next_month['year_id']    = $year + 1;
        }

    	return $next_month;
    }

    /*-------------------------------------------------------------------------*/
    // Internal
    /*-------------------------------------------------------------------------*/

    function _call_recache()
    {
		global $ibforums, $std, $DB;

    	require_once( ROOT_PATH.'sources/tasks/calendarevents.php' );

    	$task = new task_item();
    	$task->restrict_log = 1;
    	$task->run_task();
    }

    function get_prev_month($month, $year)
    {
    	//-----------------------------------------
        // Figure out the next / previous links
        //-----------------------------------------

        $prev_month = array();

        $prev_month['year_id']    = $year;

        $prev_month['month_id']   = $month - 1;
        $prev_month['month_name'] = $this->month_words[$month - 2];

        if ($this->chosen_month == 1)
        {
        	$prev_month['month_name'] = $this->month_words[11];
        	$prev_month['month_id']   = 12;
        	$prev_month['year_id']    = $year - 1;

        }

    	return $prev_month;
    }
}

?>