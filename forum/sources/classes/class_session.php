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
|   > SESSION CLASS
|   > Module written by Matt Mecham
|   > Date started: 26th January 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
+--------------------------------------------------------------------------
*/

class session {

    var $ip_address = 0;
    var $user_agent = "";
    var $time_now   = 0;
    var $session_id = 0;
    var $session_dead_id = 0;
    var $session_user_id = 0;
    var $session_user_pass = "";
    var $last_click        = 0;
    var $location          = "";
    var $member            = array();
	var $botmap            = array();
	var $do_update         = 1;

    // No need for a constructor

    /*-------------------------------------------------------------------------*/
    //
    // Authorise
    //
    /*-------------------------------------------------------------------------*/

    function authorise()
    {
		global $DB, $ibforums, $std;

        //-----------------------------------------
        // Before we go any lets check the load settings..
        //-----------------------------------------

        if ($ibforums->vars['load_limit'] > 0)
        {
        	if ( file_exists('/proc/loadavg') )
        	{
        		if ( $fh = @fopen( '/proc/loadavg', 'r' ) )
        		{
        			$data = @fread( $fh, 6 );
        			@fclose( $fh );

        			$load_avg = explode( " ", $data );

        			$ibforums->server_load = trim($load_avg[0]);

        			if ($ibforums->server_load > $ibforums->vars['load_limit'])
        			{
        				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'server_too_busy', 'INIT' => 1 ) );
        			}
        		}
        	}
        	else
        	{
				if ( $serverstats = @exec("uptime") )
				{
					preg_match( "/(?:averages)?\: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $serverstats, $load );

					$ibforums->server_load = $load[1];

					if ($ibforums->server_load > $ibforums->vars['load_limit'])
        			{
        				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'server_too_busy', 'INIT' => 1 ) );
        			}
				}
			}
        }

        //-----------------------------------------
		// Are they banned?
		//-----------------------------------------

		if ( is_array( $ibforums->cache['banfilters'] ) and count( $ibforums->cache['banfilters'] ) )
		{
			foreach ($ibforums->cache['banfilters'] as $ip)
			{
				$ip = str_replace( '\*', '.*', preg_quote($ip, "/") );

				if ( preg_match( "/^$ip$/", $ibforums->input['IP_ADDRESS'] ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'you_are_banned', 'INIT' => 1 ) );
				}
			}
		}

        //-----------------------------------------

        $this->member = array( 'id' => 0, 'name' => "", 'mgroup' => $ibforums->vars['guest_group'] );

        //-----------------------------------------
        // Return as guest if running a task
        //-----------------------------------------

        if ( $ibforums->input['act'] == 'task' )
        {
        	$this->member = $std->set_up_guest();
        	$this->member['mgroup'] = $ibforums->vars['guest_group'];
        	$ibforums->input['last_activity'] = time();
			$ibforums->input['last_visit']    = time();

			return $this->member;
        }

        //-----------------------------------------
        // no new headers if we're simply viewing an attachment..
        //-----------------------------------------

        if ( $ibforums->input['act'] == 'Attach' or $ibforums->input['act'] == 'Reg' )
        {
        	$ibforums->no_print_header = 1;
        }

        //-----------------------------------------
        // no new headers if we're updating chat
        //-----------------------------------------

        if ( $ibforums->input['act'] == 'chat' and $ibforums->input['CODE'] == 'update' )
        {
        	$ibforums->no_print_header = 1;
        	$this->do_update           = 0;
        }

        $_SERVER['HTTP_USER_AGENT'] = $std->clean_value($_SERVER['HTTP_USER_AGENT']);

        $this->ip_address = $ibforums->input['IP_ADDRESS'] ? $ibforums->input['IP_ADDRESS'] : $_SERVER['REMOTE_ADDR'];
        $this->user_agent = substr($_SERVER['HTTP_USER_AGENT'],0,50);
        $this->time_now   = time();

        //-----------------------------------------
        // Manage bots? (tee-hee)
        //-----------------------------------------

        if ( $ibforums->vars['spider_sense'] == 1 and $ibforums->vars['search_engine_bots'] )
        {
        	foreach( explode( "\n", $ibforums->vars['search_engine_bots'] ) as $bot )
        	{
        		list($ua, $n) = explode( "=", $bot );

        		if ( $ua and $n )
        		{
        			$this->bot_map[ strtolower($ua) ] = $n;
        			$this->bot_safe[] = preg_quote( $ua, "/" );
        		}
        	}

        	if ( preg_match( '/('.implode( '|', $this->bot_safe ) .')/i', $_SERVER['HTTP_USER_AGENT'], $match ) )
        	{
        		$DB->simple_construct( array( 'select' => '*',
											  'from'   => 'groups',
											  'where'  =>" g_id=".intval($ibforums->vars['spider_group'])
									 )      );
        		$DB->simple_exec();

        		$group = $DB->fetch_row();

				foreach ($group as $k => $v)
				{
					$this->member[ $k ] = $v;
				}

				$this->member['restrict_post']    = 1;
				$this->member['g_use_search']     = 0;
				$this->member['g_email_friend']   = 0;
				$this->member['g_edit_profile']   = 0;
				$this->member['g_use_pm']         = 0;
				$this->member['g_is_supmod']      = 0;
				$this->member['g_access_cp']      = 0;
				$this->member['g_access_offline'] = 0;
				$this->member['g_avoid_flood']    = 0;
				$this->member['id']               = 0;

				$ibforums->perm_id       = $this->member['g_perm_id'];
       			$ibforums->perm_id_array = explode( ",", $ibforums->perm_id );
       			$ibforums->session_type  = 'cookie';
       			$ibforums->is_bot        = 1;
       			$this->session_id        = "";

       			$agent = trim($match[0]);

       			//-----------------------------------------
       			// Using lofi?
       			//-----------------------------------------

       			if ( strstr( $_SERVER['PHP_SELF'], 'lofiversion' ) )
       			{
       				$qstring = "Lo-Fi: ".str_replace( "/", "", strrchr( $_SERVER['PHP_SELF'], "/" ) );
       			}
       			else
       			{
       				$qstring = str_replace( "'", "", $_SERVER['QUERY_STRING']);
       			}

       			if ( $ibforums->vars['spider_visit'] )
       			{
       				$DB->do_shutdown_insert( 'spider_logs', array (
																	'bot'          => $agent,
																	'query_string' => $qstring,
																	'ip_address'   => $_SERVER['REMOTE_ADDR'],
																	'entry_date'   => time(),
														)        );
       			}

       			if ( $ibforums->vars['spider_active'] )
       			{
       				$DB->simple_construct( array( 'delete' => 'sessions',
												  'where'  => "id='".$agent.'='.str_replace('.','',$this->ip_address )."_session'"
										 )      );
					$DB->simple_shutdown_exec();

       				$this->create_bot_session($agent, $this->bot_map[ strtolower($agent) ]);
       			}

       			return $this->member;
        	}
        }

        //-----------------------------------------
        // Continue!
        //-----------------------------------------

        $cookie = array();
        $cookie['session_id']   = $std->my_getcookie('session_id');
        $cookie['member_id']    = $std->my_getcookie('member_id');
        $cookie['pass_hash']    = $std->my_getcookie('pass_hash');


        if ( $cookie['session_id'] )
        {
        	$this->get_session($cookie['session_id']);
        	$ibforums->session_type = 'cookie';
        }
        elseif ( $ibforums->input['s'] )
        {
        	$this->get_session($ibforums->input['s']);
        	$ibforums->session_type = 'url';
        }
        else
        {
        	$this->session_id = 0;
        }

		//-----------------------------------------
		// Do we have a valid session ID?
		//-----------------------------------------

		if ( $this->session_id )
		{
			// We've checked the IP addy and browser, so we can assume that this is
			// a valid session.

			if ( ($this->session_user_id != 0) and ( ! empty($this->session_user_id) ) )
			{
				// It's a member session, so load the member.

				$this->load_member($this->session_user_id);

				// Did we get a member?

				if ( (! $this->member['id']) or ($this->member['id'] == 0) )
				{
					$this->unload_member();
					$this->update_guest_session();
				}
				else
				{
					$this->update_member_session();
				}
			}
			else
			{
				$this->update_guest_session();
			}

		}
		else
		{
			// We didn't have a session, or the session didn't validate

			// Do we have cookies stored?

			if ($cookie['member_id'] != "" and $cookie['pass_hash'] != "")
			{
				$this->load_member($cookie['member_id']);

				if ( (! $this->member['id']) or ($this->member['id'] == 0) )
				{
					$this->unload_member();
					$this->create_guest_session();
				}
				else
				{
					if ($this->member['member_login_key'] == $cookie['pass_hash'])
					{
						$this->create_member_session();
					}
					else
					{
						$this->unload_member();
						$this->create_guest_session();
					}
				}
			}
			else
			{
				$this->create_guest_session();
			}
		}

		//-----------------------------------------
        // Are we a member of several groups?
        //-----------------------------------------


        if (! $this->member['id'])
        {
        	$this->member = $std->set_up_guest();
        	$this->member['mgroup'] = $ibforums->vars['guest_group'];
        	$ibforums->input['last_activity'] = time();
			$ibforums->input['last_visit']    = time();
        }

        //-----------------------------------------
        // Do we have a cache?
        //-----------------------------------------

        if ( ! is_array( $ibforums->cache['group_cache'] ) )
		{
			$ibforums->cache['group_cache'] = array();

			$DB->simple_construct( array( 'select' => "*",
										  'from'   => 'groups'
								 )      );

			$DB->simple_exec();

			while ( $i = $DB->fetch_row() )
			{
				$ibforums->cache['group_cache'][ $i['g_id'] ] = $i;
			}

			$std->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );
		}

		//-----------------------------------------
        // Set up main 'display' group
        //-----------------------------------------

        $this->member = array_merge( $this->member, $ibforums->cache['group_cache'][ $this->member['mgroup'] ] );

        //-----------------------------------------
		// Sprinkle on some moderator stuff...
		//-----------------------------------------

		if ( $this->member['mgroup'] != $ibforums->vars['guest_group'] )
		{
			if ( $this->member['g_is_supmod'] == 1 )
			{
				$this->member['is_mod'] = 1;
			}
			else if ( count($ibforums->cache['moderators']) )
			{
				foreach( $ibforums->cache['moderators'] as $i => $r )
				{
					if ( $r['member_id'] == $this->member['id'] or $r['group_id'] == $this->member['mgroup'] )
					{
						$this->member['_moderator'][ $r['forum_id'] ] = $r;
						$this->member['is_mod'] = 1;
					}
				}
			}
		}

        //header('content-type:text/plain'); print_r($this->member); exit();

		//-----------------------------------------
        // Are we a member of several groups?
        //-----------------------------------------

		$this->build_group_permissions();

        //-----------------------------------------
        // Synchronise the last visit and activity times if
        // we have some in the member profile
        //-----------------------------------------

        if ($this->member['id'])
        {
        	if ( ! $ibforums->input['last_activity'] )
        	{
				if ($this->member['last_activity'])
				{
					$ibforums->input['last_activity'] = $this->member['last_activity'];
				}
				else
				{
					$ibforums->input['last_activity'] = $this->time_now;
				}
        	}
        	//-----------------------------------------

        	if ( ! $ibforums->input['last_visit'] )
        	{
				if ($this->member['last_visit'])
				{
					$ibforums->input['last_visit'] = $this->member['last_visit'];
				}
				else
				{
					$ibforums->input['last_visit'] = $this->time_now;
				}
        	}

			//-----------------------------------------
			// If there hasn't been a cookie update in 2 hours,
			// we assume that they've gone and come back
			//-----------------------------------------

			if ( ! $this->member['last_visit'] )
			{
				// No last visit set, do so now!

				$DB->simple_construct( array( 'update' => 'members',
											  'lowpro' => 1,
											  'set'    => "last_visit=".$this->time_now.", last_activity=".$this->time_now,
											  'where'  => "id=".$this->member['id']
									 )      );

				$DB->simple_shutdown_exec();

			}
			else if ( (time() - $ibforums->input['last_activity']) > 300 )
			{
				// If the last click was longer than 5 mins ago and this is a member
				// Update their profile.

				list( $be_anon, $loggedin ) = explode( '&', $this->member['login_anonymous'] );

				$DB->simple_construct( array( 'update' => 'members',
											  'lowpro' => 1,
											  'set'    => "login_anonymous='$be_anon&1', last_activity=".$this->time_now,
											  'where'  => "id=".$this->member['id']
									 )      );

				$DB->simple_shutdown_exec();
			}

			//-----------------------------------------
			// Check ban status
			//-----------------------------------------

			if ( $this->member['temp_ban'] )
			{
				$ban_arr = $std->hdl_ban_line(  $this->member['temp_ban'] );

				if ( time() >= $ban_arr['date_end'] )
				{
					// Update this member's profile

					$DB->simple_construct( array( 'update' => 'members',
											      'lowpro' => 1,
												  'set'    => "temp_ban=''",
												  'where'  => "id=".$this->member['id']
										 )      );

					$DB->simple_shutdown_exec();
				}
				else
				{
					$ibforums->member = $this->member; // Set time right
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'account_susp', 'INIT' => 1, 'EXTRA' => $std->get_date($ban_arr['date_end'],'LONG', 1) ) );
				}
			}
		}

		//-----------------------------------------
        // Set a session ID cookie
        //-----------------------------------------

        $std->my_setcookie("session_id", $this->session_id, -1);

        return $this->member;
    }

    /*-------------------------------------------------------------------------*/
    //
    // Build group permissions
    //
    /*-------------------------------------------------------------------------*/

    function build_group_permissions()
    {
		global $ibforums, $std;

    	if ( $this->member['mgroup_others'] )
		{
			$groups_id    = explode( ',', $this->member['mgroup_others'] );
			$exclude      = array( 'g_title', 'g_icon', 'prefix', 'suffix', 'g_promotion', 'g_photo_max_vars' );
			$less_is_more = array( 'g_search_flood' );

			if ( count( $groups_id ) )
			{
				foreach( $groups_id as $pid )
				{
					if ( ! $ibforums->cache['group_cache'][ $pid ]['g_id'] )
					{
						continue;
					}

					//-----------------------------------------
					// Loop through and mix
					//-----------------------------------------

					foreach( $ibforums->cache['group_cache'][ $pid ] as $k => $v )
					{
						if ( ! in_array( $k, $exclude ) )
						{
							//-----------------------------------------
							// Add to perm id list
							//-----------------------------------------

							if ( $k == 'g_perm_id' )
							{
								$this->member['g_perm_id'] .= ','.$v;
							}
							else if ( in_array( $k, $less_is_more ) )
							{
								if ( $v < $this->member[ $k ] )
								{
									$this->member[ $k ] = $v;
								}
							}
							else
							{
								if ( $v > $this->member[ $k ] )
								{
									$this->member[ $k ] = $v;
								}
							}
						}
					}
				}
			}

			//-----------------------------------------
			// Tidy perms_id
			//-----------------------------------------

			$rmp = array();
			$tmp = explode( ',', $std->clean_perm_string($this->member['g_perm_id']) );

			if ( count( $tmp ) )
			{
				foreach( $tmp as $t )
				{
					$rmp[ $t ] = $t;
				}
			}

			if ( count( $rmp ) )
			{
				$this->member['g_perm_id'] = implode( ',', $rmp );
			}
		}

		$ibforums->perm_id       = ( $this->member['org_perm_id'] ) ? $this->member['org_perm_id'] : $this->member['g_perm_id'];

        $ibforums->perm_id_array = explode( ",", $ibforums->perm_id );

    }

    /*-------------------------------------------------------------------------*/
    //
	// Attempt to load a member
	//
	/*-------------------------------------------------------------------------*/

    function load_member($member_id=0)
    {
		global $DB, $std, $ibforums;

    	$member_id = intval($member_id);

     	if ($member_id != 0)
        {
            $DB->cache_add_query( 'session_load_member', array( 'mid' => $member_id ) );

            $DB->simple_exec();

            if ( $DB->get_num_rows() )
            {
            	$this->member = $DB->fetch_row();
            }

            //-----------------------------------------
            // Unless they have a member id, log 'em in as a guest
            //-----------------------------------------

            if ( ($this->member['id'] == 0) or (empty($this->member['id'])) )
            {
				$this->unload_member();
            }
		}

		unset($member_id);
	}

	/*-------------------------------------------------------------------------*/
	//
	// Remove the users cookies
	//
	/*-------------------------------------------------------------------------*/

	function unload_member()
	{
		global $DB, $std, $ibforums;

		// Boink the cookies

		$std->my_setcookie( "member_id" , "0", -1  );
		$std->my_setcookie( "pass_hash" , "0", -1  );

		$this->member['id']       = 0;
		$this->member['name']     = "";
	}

    /*-------------------------------------------------------------------------*/
    //
    // Updates a current session.
    //
    /*-------------------------------------------------------------------------*/

    function update_member_session()
    {
		global $DB, $ibforums;

        if ( ! $this->do_update )
        {
        	return ;
        }

        // Make sure we have a session id.

        if ( ! $this->session_id )
        {
        	$this->create_member_session();
        	return;
        }

        if (empty($this->member['id']))
        {
        	$this->unload_member();
        	$this->create_guest_session();
        	return;
        }


        $DB->do_shutdown_update( 'sessions',
								 array(
										'member_name'  => $this->member['name'],
										'member_id'    => intval($this->member['id']),
										'member_group' => $this->member['mgroup'],
										'in_forum'     => intval($ibforums->input['f']),
										'in_topic'     => intval($ibforums->input['t']),
										'login_type'   => substr($this->member['login_anonymous'],0, 1),
										'running_time' => $this->time_now,
										'location'     => $ibforums->input['act'].",".$ibforums->input['p'].",".$ibforums->input['CODE'],
										'in_error'     => 0,
									  ),
								"id='{$this->session_id}'"
							  );
    }

    /*-------------------------------------------------------------------------*/
    //
    // Update guest session
    //
    /*-------------------------------------------------------------------------*/

    function update_guest_session()
    {
		global $DB, $ibforums, $INFO;

        if ( ! $this->do_update )
        {
        	return ;
        }

        // Make sure we have a session id.

        if ( ! $this->session_id )
        {
        	$this->create_guest_session();
        	return;
        }

        $DB->do_shutdown_update( 'sessions',
								 array(
										'member_name'  => "",
										'member_id'    => 0,
										'member_group' => $ibforums->vars['guest_group'],
										'in_forum'     => intval($ibforums->input['f']),
										'in_topic'     => intval($ibforums->input['t']),
										'login_type'   => substr($this->member['login_anonymous'],0, 1),
										'running_time' => $this->time_now,
										'location'     => $ibforums->input['act'].",".$ibforums->input['p'].",".$ibforums->input['CODE'],
										'in_error'     => 0,
									  ),
								"id='{$this->session_id}'"
							  );
    }


    /*-------------------------------------------------------------------------*/
    //
    // Get a session based on the current session ID
    //
    /*-------------------------------------------------------------------------*/

    function get_session($session_id="")
    {
		global $DB, $ibforums, $std;

        $result = array();

        $query = "";

        $session_id = preg_replace("/([^a-zA-Z0-9])/", "", $session_id);

        if ( $session_id )
        {
			if ($ibforums->vars['match_browser'] == 1)
			{
				$query = " AND browser='".$this->user_agent."'";
			}

			$DB->simple_construct( array( 'select' => 'id, member_id, running_time, location',
										  'from'   => 'sessions',
										  'where'  => "id='".$session_id."' and ip_address='".$this->ip_address."'".$query
								 )      );

			$DB->simple_exec();

			if ( $DB->get_num_rows() != 1 )
			{
				// Either there is no session, or we have more than one session..

				$this->session_dead_id   = $session_id;
				$this->session_id        = 0;
        		$this->session_user_id   = 0;
        		return;
			}
			else
			{
				$result = $DB->fetch_row();

				if ($result['id'] == "")
				{
					$this->session_dead_id   = $session_id;
					$this->session_id        = 0;
					$this->session_user_id   = 0;
					unset($result);
					return;
				}
				else
				{
					$this->session_id        = $result['id'];
					$this->session_user_id   = $result['member_id'];
					$this->last_click        = $result['running_time'];
        			$this->location          = $result['location'];
        			unset($result);
					return;
				}
			}
		}
    }

    /*-------------------------------------------------------------------------*/
    //
    // Creates a member session.
    //
    /*-------------------------------------------------------------------------*/

    function create_member_session()
    {
		global $DB, $INFO, $std, $ibforums;

        if ($this->member['id'])
        {
        	//-----------------------------------------
        	// Remove the defunct sessions
        	//-----------------------------------------

			$ibforums->vars['session_expiration'] = $ibforums->vars['session_expiration'] ? (time() - $ibforums->vars['session_expiration']) : (time() - 3600);

			$DB->simple_construct( array( 'delete' => 'sessions', 'where' => "member_id=".$this->member['id'] ) );

			$DB->simple_exec();

			$this->session_id  = md5( uniqid(microtime()) );

			//-----------------------------------------
        	// Insert the new session
        	//-----------------------------------------

        	$DB->do_shutdown_insert( 'sessions',
									 array(
											'id'           => $this->session_id,
											'member_name'  => $this->member['name'],
											'member_id'    => intval($this->member['id']),
											'member_group' => $this->member['mgroup'],
											'in_forum'     => intval($ibforums->input['f']),
											'in_topic'     => intval($ibforums->input['t']),
											'login_type'   => substr($this->member['login_anonymous'],0, 1),
											'running_time' => $this->time_now,
											'location'     => $ibforums->input['act'].",".$ibforums->input['p'].",".$ibforums->input['CODE'],
											'ip_address'   => $this->ip_address,
											'browser'      => $this->user_agent,
											'in_error'     => 0,
										  )
								  );

			//-----------------------------------------
			// If this is a member, update their last visit times, etc.
			//-----------------------------------------

			if ( time() - $this->member['last_activity'] > 3600 )
			{
				//-----------------------------------------
				// Reset the topics read cookie..
				//-----------------------------------------

				$std->my_setcookie('topicsread', '');

				list( $be_anon, $loggedin ) = explode( '&', $this->member['login_anonymous'] );

				$DB->simple_construct( array( 'update' => 'members',
											  'set'    => "login_anonymous='$be_anon&1', last_visit=last_activity, last_activity=".$this->time_now,
											  'where'  => "id=".$this->member['id']
									 )      );

				$DB->simple_shutdown_exec();

				//-----------------------------------------
				// Fix up the last visit/activity times.
				//-----------------------------------------

				$ibforums->input['last_visit']    = $this->member['last_activity'];
				$ibforums->input['last_activity'] = $this->time_now;
			}
		}
		else
		{
			$this->create_guest_session();
		}
    }

    /*-------------------------------------------------------------------------*/
    //
    // Create guest session
    //
    /*-------------------------------------------------------------------------*/

    function create_guest_session()
    {
		global $DB, $std, $ibforums;

		//-----------------------------------------
		// Remove the defunct sessions
		//-----------------------------------------

		if ( ($this->session_dead_id != 0) and ( ! empty($this->session_dead_id) ) )
		{
			$extra = " or id='".$this->session_dead_id."'";
		}
		else
		{
			$extra = "";
		}

		$ibforums->vars['session_expiration'] = $ibforums->vars['session_expiration'] ? (time() - $ibforums->vars['session_expiration']) : (time() - 3600);

		$DB->simple_construct( array( 'delete' => 'sessions', 'where'  => "ip_address='".$this->ip_address."'".$extra ) );

		$DB->simple_exec();

		$this->session_id  = md5( uniqid(microtime()) );

		//-----------------------------------------
		// Insert the new session
		//-----------------------------------------

		$DB->do_shutdown_insert( 'sessions',
								 array(
										'id'           => $this->session_id,
										'member_name'  => '',
										'member_id'    => 0,
										'member_group' => $ibforums->vars['guest_group'],
										'in_forum'     => intval($ibforums->input['f']),
										'in_topic'     => intval($ibforums->input['t']),
										'login_type'   => 0,
										'running_time' => $this->time_now,
										'location'     => $ibforums->input['act'].",".$ibforums->input['p'].",".$ibforums->input['CODE'],
										'ip_address'   => $this->ip_address,
										'browser'      => $this->user_agent,
										'in_error'     => 0,
									  )
							  );
    }

    /*-------------------------------------------------------------------------*/
    //
    // Creates a BOT session
    //
    /*-------------------------------------------------------------------------*/

    function create_bot_session($bot, $name="")
    {
		global $DB, $std, $ibforums;

        $DB->do_shutdown_insert( 'sessions',
								 array(
										'id'           => $bot.'='.str_replace('.','',$this->ip_address ).'_session',
										'member_name'  => $name ? $name : $bot,
										'member_id'    => 0,
										'member_group' => $ibforums->vars['spider_group'],
										'in_forum'     => intval($ibforums->input['f']),
										'in_topic'     => intval($ibforums->input['t']),
										'login_type'   => $ibforums->vars['spider_anon'],
										'running_time' => $this->time_now,
										'location'     => $ibforums->input['act'].",".$ibforums->input['p'].",".$ibforums->input['CODE'],
										'ip_address'   => $this->ip_address,
										'browser'      => $this->user_agent,
										'in_error'     => 0,
									  )
							  );
    }

    /*-------------------------------------------------------------------------*/
    //
    // Updates a BOT current session.
    //
    /*-------------------------------------------------------------------------*/

    function update_bot_session($bot, $name="")
    {
		global $DB, $ibforums;

       $DB->do_shutdown_update( 'sessions',
								array(
									   'member_name'  => $name ? $name : $bot,
									   'member_id'    => 0,
									   'member_group' => $ibforums->vars['spider_group'],
									   'in_forum'     => intval($ibforums->input['f']),
									   'in_topic'     => intval($ibforums->input['t']),
									   'login_type'   => $ibforums->vars['spider_anon'],
									   'running_time' => $this->time_now,
									   'location'     => $ibforums->input['act'].",".$ibforums->input['p'].",".$ibforums->input['CODE'],
									   'in_error'     => 0,
									 ),
								 "id='".$bot.'='.str_replace('.','',$this->ip_address )."_session'"
							 );
    }


}

?>