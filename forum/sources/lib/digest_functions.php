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
|   > Task Manager functions ( Digest Library )
|   > Script written by Matt Mecham
|   > Date started: 9th June 2004
|   > DBA Checked: 9th June 2004
|
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}



class digest_functions
{
	var $digest_time = 'daily';
	var $digest_type = 'topic';
	var $root_path   = './';
	var $midnight    = 0;
	var $last_week   = 0;
	var $last_day    = 0;
	var $last_time   = 0;

	/*-------------------------------------------------------------------------*/
	//
	// CONSTRUCTOR
	//
	/*-------------------------------------------------------------------------*/

	function digest_functions( $ROOT_PATH )
	{
		global $DB, $std, $ibforums;

		if ( $ROOT_PATH )
		{
			$this->root_path = preg_replace( "#/$#", "", $ROOT_PATH ) .'/';
		}
		else if ( ROOT_PATH )
		{
			$this->root_path = ROOT_PATH;
		}

		//-----------------------------------------
		// Get midnight (GMT - roughly)
		//-----------------------------------------

		$this->midnight = mktime( 0, 0 );

		//-----------------------------------------
		// Midnight today minus a weeks worth of secs
		//-----------------------------------------

		$this->last_week = $this->midnight - 604800;

		//-----------------------------------------
		// Midnight today minus a day worth of secs
		//-----------------------------------------

		$this->last_day  = $this->midnight - 86400;

		$DB->load_cache_file( $this->root_path.'sources/sql/'.SQL_DRIVER.'_extra_queries.php', 'sql_extra_queries' );

		//-----------------------------------------
        // Load the email libby
        //-----------------------------------------

        require_once( $this->root_path.'sources/classes/class_email.php' );
		$this->email = new emailer($this->root_path);
	}

	/*-------------------------------------------------------------------------*/
	//
	// Run the digest
	//
	/*-------------------------------------------------------------------------*/

	function run_digest()
	{
		global $ibforums, $DB, $std;

		$this->last_time = $this->digest_time == 'daily' ? $this->last_day : $this->last_week;

		if ( $this->digest_type == 'topic' )
		{
			$this->_send_topic_digest();
		}
		else
		{
			$this->_send_forum_digest();
		}
	}

	/*-------------------------------------------------------------------------*/
	// TOPIC DIGEST
	/*-------------------------------------------------------------------------*/

	function _send_topic_digest()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get all posts / topics
		//-----------------------------------------

		$DB->cache_add_query( 'digest_get_topics', array(
														  'type'      => $this->digest_time,
														  'last_time' => $this->last_time
														), 'sql_extra_queries' );
		$topic_query = $DB->cache_exec_query();

		//-----------------------------------------
		// Now, loop print and send to subscribers
		//-----------------------------------------

		$main_output = "";
		$count       = 0;
		$cached      = array();
		$subject     = $this->digest_time == 'daily' ? 'digest_topic_daily' : 'digest_topic_weekly';

		while( $t = $DB->fetch_row( $topic_query ) )
		{
			$main_output    = "";
			$others_posted = 0;

			if ( ! $cached[ $t['tid'] ] )
			{
				$topic_title = $t['title'];
				$forum_name  = $ibforums->cache['forum_cache'][ $t['forum_id'] ]['name'];

				//-----------------------------------------
				// Get posts...
				//-----------------------------------------

				$DB->simple_construct( array( 'select' => '*',
											  'from'   => 'posts',
											  'where'  => "topic_id={$t['tid']} AND queued=0 AND post_date > {$this->last_time}",
											  'order'  => 'post_date' ) );

				$post_query = $DB->simple_exec();

				$post_output = "";

				while( $p = $DB->fetch_row( $post_query ) )
				{
					//-----------------------------------------
					// Do we have other posters?
					//-----------------------------------------

					if ( $t['trmid'] != $p['author_id'] )
					{
						$others_posted = 1;
					}

					$post_author  = $p['author_name'];
					$post_date    = $std->get_date( $p['post_date'], 'SHORT' );
					$post_content = $p['post'];

					$post_output .= "\n-------------------------------------------\n"
								 .  "{$post_author} -- {$post_date}\n{$post_content}\n\n";
				}

				//-----------------------------------------
				// Process
				//-----------------------------------------

				$main_output .= "Topic: $topic_title (Forum: $forum_name)\n"
							 .  "=====================================\n"
							 .  $post_output
							 .  "\n=====================================\n";

				$cached[ $t['tid'] ] = $main_output;
			}
			else
			{
				$main_output = $cached[ $t['tid'] ];
			}

			if ( $others_posted )
			{
				$count++;

				//-----------------------------------------
				// Send email...
				//-----------------------------------------

				$this->email->get_template( $subject, $data['language']);

				$this->email->build_message( array(
													'TOPIC_ID'        => $t['tid'],
													'FORUM_ID'        => $t['forum_id'],
													'TITLE'           => $topic_title,
													'NAME'            => $t['name'],
													'CONTENT'         => $main_output,
										   )     );

				$DB->do_insert( 'mail_queue', array( 'mail_to'      => $t['email'],
													 'mail_date'    => time(),
													 'mail_subject' => $this->email->lang_subject,
													 'mail_content' => $std->txt_safeslashes($this->email->message) ) );
			}

		}

		$ibforums->cache['systemvars']['mail_queue'] += $count;

		//-----------------------------------------
		// Update cache with remaning email count
		//-----------------------------------------

		$DB->do_update( 'cache_store', array( 'cs_array' => 1, 'cs_value' => addslashes(serialize($ibforums->cache['systemvars'])) ), "cs_key='systemvars'" );
	}

	/*-------------------------------------------------------------------------*/
	// TOPIC DIGEST
	/*-------------------------------------------------------------------------*/

	function _send_forum_digest()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get all posts / topics
		//-----------------------------------------

		$DB->cache_add_query( 'digest_get_forums', array(
														 'type'      => $this->digest_time
														), 'sql_extra_queries' );
		$forum_query = $DB->simple_exec();

		//-----------------------------------------
		// Now, loop print and send to subscribers
		//-----------------------------------------

		$main_output = "";
		$count       = 0;
		$cached      = array();
		$subject     = $this->digest_time == 'daily' ? 'digest_forum_daily' : 'digest_forum_weekly';

		while( $t = $DB->fetch_row( $forum_query ) )
		{
			$main_output   = "";
			$others_posted = 0;

			if ( ! $cached[ $t['forum_id'] ] )
			{
				$forum_name  = $ibforums->cache['forum_cache'][ $t['forum_id'] ]['name'];

				//-----------------------------------------
				// Get topics...
				//-----------------------------------------

				$DB->cache_add_query( 'digest_get_forums_topics', array(
																		'forum_id'  => $t['forum_id'],
																		'last_time' => $this->last_time
																	  ), 'sql_extra_queries' );
				$topic_query = $DB->cache_exec_query();

				$post_output = "";

				while( $p = $DB->fetch_row( $topic_query ) )
				{
					//-----------------------------------------
					// Do we have other posters?
					//-----------------------------------------

					if ( $t['member_id'] != $p['starter_id'] )
					{
						$others_posted = 1;
					}

					$post_author  = $p['author_name'];
					$post_date    = $std->get_date( $p['post_date'], 'SHORT' );
					$post_content = $p['post'];
					$topic_title  = $p['title'];

					$post_output .= "\n-------------------------------------------\n"
					             .  "Topic: {$topic_title} ({$post_author} -- {$post_date})"
								 .  "\n............................................\n"
								 .  "{$post_content}\n\n";
				}

				//-----------------------------------------
				// Process
				//-----------------------------------------

				$main_output .= "Forum: $forum_name\n"
							 .  "=====================================\n"
							 .  $post_output
							 .  "\n=====================================\n";

				$cached[ $t['forum_id'] ] = $main_output;
			}
			else
			{
				$main_output = $cached[ $t['forum_id'] ];
			}

			if ( $others_posted )
			{
				$count++;

				//-----------------------------------------
				// Send email...
				//-----------------------------------------

				$this->email->get_template( $subject, $data['language']);

				$this->email->build_message( array(
													'FORUM_ID'        => $t['forum_id'],
													'NAME'            => $forum_name,
													'CONTENT'         => $main_output,
										   )     );

				$DB->do_insert( 'mail_queue', array( 'mail_to'      => $t['email'],
													 'mail_date'    => time(),
													 'mail_subject' => $this->email->lang_subject,
													 'mail_content' => $std->txt_safeslashes($this->email->message) ) );
			}

		}

		$ibforums->cache['systemvars']['mail_queue'] += $count;

		//-----------------------------------------
		// Update cache with remaning email count
		//-----------------------------------------

		$DB->do_update( 'cache_store', array( 'cs_array' => 1, 'cs_value' => addslashes(serialize($ibforums->cache['systemvars'])) ), "cs_key='systemvars'" );
	}



}



?>