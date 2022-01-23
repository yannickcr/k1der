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
|   > Topic Tracker module
|   > Module written by Matt Mecham
|   > Date started: 5th March 2002
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

class tracker {

    var $output    = "";
    var $base_url  = "";
    var $html      = "";

    var $forum     = array();
    var $topic     = array();
    var $category  = array();
    var $type      = 'topic';
	var $method    = 'delayed';

    function auto_run($is_sub=0)
    {

    	//-----------------------------------------
    	// $is_sub is a boolean operator.
    	// If set to 1, we don't show the "topic subscribed" page
    	// we simply end the subroutine and let the caller finish
    	// up for us.
    	//-----------------------------------------

        global $ibforums, $DB, $std, $print, $forums;

        $ibforums->lang = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id);

        //-----------------------------------------
        // Check the input
        //-----------------------------------------

        if ($ibforums->input['type'] == 'forum')
        {
        	$this->type = 'forum';
        }

        //-----------------------------------------
        // Method..
        //-----------------------------------------

        switch ($ibforums->input['method'])
        {
        	case 'immediate':
        		$this->method = 'immediate';
        		break;
        	case 'delayed':
        		$this->method = 'delayed';
        		break;
        	case 'none':
        		$this->method = 'none';
        		break;
        	case 'daily':
        		$this->method = 'daily';
        		break;
        	case 'weekly':
        		$this->method = 'weekly';
        		break;
        	default:
        		$this->method = 'delayed';
        		break;
        }


        $ibforums->input['t'] = intval($ibforums->input['t']);
        $ibforums->input['f'] = intval($ibforums->input['f']);

        //-----------------------------------------
        // Get the forum info based on the forum ID, get the category name, ID, and get the topic details
        //-----------------------------------------

        if ($this->type == 'forum')
        {
        	$this->topic = $forums->forum_by_id[ $ibforums->input['f'] ];
        }
        else
        {
        	$row = $DB->simple_exec_query( array( 'select' => 'tid, forum_id', 'from' => 'topics', 'where' => 'tid='.$ibforums->input['t'] ) );
        	$this->topic = array_merge( $row, $forums->forum_by_id[ $ibforums->input['f'] ] );
        }

        //-----------------------------------------
        // Error out if we can not find the forum
        //-----------------------------------------

        if ( ! $forums->forum_by_id[ $ibforums->input['f'] ] )
        {
        	if ($is_sub != 1)
        	{
            	$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
            }
            else
            {
            	return;
            }
        }

        //-----------------------------------------
        // Error out if we can not find the topic
        //-----------------------------------------

        if ($this->type != 'forum')
        {
			if ( ! $this->topic['tid'] )
			{
				if ($is_sub != 1)
				{
					$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
				}
				else
				{
					return;
				}
			}
        }

        $this->base_url    = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?s={$ibforums->session_id}";

        $this->base_url_NS = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}";

        //-----------------------------------------
        // Check viewing permissions, private forums,
        // password forums, etc
        //-----------------------------------------

        if (! $ibforums->member['id'] )
        {
        	if ($is_sub != 1)
        	{
            	$std->Error( array( LEVEL => 1, MSG => 'no_guests') );
            }
            else
            {
            	return;
            }
        }

        if ( $std->check_perms( $forums->forum_by_id[ $ibforums->input['f'] ]['read_perms'] ) != TRUE )
        {
			if ($is_sub != 1)
			{
				$std->Error( array( LEVEL => 1, MSG => 'forum_no_access') );
			}
			else
			{
				return;
			}
		}

		if ($this->topic['password'] != "")
		{
			if ( $forums->forums_compare_password($this->topic['fid']) != TRUE )
			{
				$std->Error( array( LEVEL => 1, MSG => 'forum_no_access') );
			}
		}

		//-----------------------------------------
		// Have we already subscribed?
		//-----------------------------------------

		if ($this->type == 'forum')
		{
			$DB->simple_construct( array( 'select' => 'frid',
										  'from'   => 'forum_tracker',
										  'where'  => "forum_id='".$this->topic['id']."' AND member_id='".$ibforums->member['id']."'" ) );
			$DB->simple_exec();
		}
		else
		{
			$DB->simple_construct( array( 'select' => 'trid',
										  'from'   => 'tracker',
										  'where'  => "topic_id='".$this->topic['tid']."' AND member_id='".$ibforums->member['id']."'" ) );
			$DB->simple_exec();
		}

		if ( $DB->get_num_rows() )
		{
			if ($is_sub != 1)
			{
				$std->Error( array( LEVEL => 1, MSG => 'already_sub') );
			}
			else
			{
				return;
			}
		}

		//-----------------------------------------
		// Add it to the DB
		//-----------------------------------------

		if ($this->type == 'forum')
		{

			$DB->do_insert( 'forum_tracker', array (
													  'member_id'        => $ibforums->member['id'],
													  'forum_id'         => $ibforums->input['f'],
													  'start_date'       => time(),
													  'forum_track_type' => $this->method,
										   )       );
		}
		else
		{
			$DB->do_insert( 'tracker', array (
												'member_id'        => $ibforums->member['id'],
												'topic_id'         => $this->topic['tid'],
												'start_date'       => time(),
												'topic_track_type' => $this->method,
									 )       );
		}

		if ($is_sub != 1)
		{
			if ($this->type == 'forum')
			{
				$print->redirect_screen( $ibforums->lang['sub_added'], "act=SF&f={$this->topic['id']}" );
			}
			else
			{
				$print->redirect_screen( $ibforums->lang['sub_added'], "act=ST&f={$this->topic['id']}&t={$this->topic['tid']}&st={$ibforums->input['st']}" );
			}
		}
		else
		{
			return;
		}
	}
}

?>