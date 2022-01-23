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
|   > Add POLL module
|   > Module written by Matt Mecham
|   > DBA Checked: Fri 21 May 2004
|
+--------------------------------------------------------------------------
|
|   QUOTE OF THE MODULE: (Taken from BtVS)
|   --------------------
|	Drusilla: I'm naming all the stars...
|   Spike: You can't see the stars love, That's the ceiling. Also, it's day.
|
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class poll {


	var $topic = array();
	var $poll  = array();
	var $upload = array();
	var $poll_count = 0;
	var $poll_choices = "";

	function auto_run() {
		global $ibforums, $std, $DB, $print;

		$ibforums->lang      = $std->load_words($ibforums->lang, 'lang_post', $ibforums->lang_id);

		// Lets do some tests to make sure that we are allowed to start a new topic

		if (! $ibforums->member['g_vote_polls'])
		{
			$std->Error( array( LEVEL => 1, MSG => 'no_reply_polls') );
		}

		// Did we choose a choice?

		if (!$ibforums->input['nullvote'])
		{
			if (! isset($ibforums->input['poll_vote']) )
			{
				$std->Error( array( LEVEL => 1, MSG => 'no_vote') );
			}
		}

		// Make sure we have a valid poll id

       	$ibforums->input['t'] = intval($ibforums->input['t']);

		if (! $ibforums->input['t'] )
		{
			$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
		}

   		// Load the topic and poll

   		$DB->cache_add_query( 'poll_get_poll_with_topic', array( 'tid' => $ibforums->input['t'] ) );
		$DB->cache_exec_query();

   		$this->topic = $DB->fetch_row();

   		if (! $this->topic['tid'] )
   		{
   			$std->Error( array( LEVEL => 1, MSG => 'poll_none_found') );
   		}

   		if ($this->topic['state'] != 'open')
   		{
   			$std->Error( array( LEVEL => 1, MSG => 'locked_topic') );
   		}

		// Have we voted before?

		$DB->simple_construct( array( 'select' => 'member_id', 'from' => 'voters', 'where' => "tid={$this->topic['tid']} and member_id=".$ibforums->member['id'] ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{
			$std->Error( array( LEVEL => 1, MSG => 'poll_you_voted') );
		}

		// If we're here, lets add the vote

		$DB->do_insert( 'voters', array( 'member_id'  => $ibforums->member['id'],
										 'ip_address' => $ibforums->input['IP_ADDRESS'],
										 'tid'        => $this->topic['tid'],
										 'forum_id'   => $this->topic['forum_id'],
										 'vote_date'  => time(),
										) );

		// If this isn't a null vote...

		if ( ! $ibforums->input['nullvote'] )
		{
			$poll_answers = unserialize(stripslashes($this->topic['choices']));
        	reset($poll_answers);
        	$new_poll_array = array();
        	foreach ($poll_answers as $entry)
        	{
        		$id     = $entry[0];
        		$choice = $entry[1];
        		$votes  = $entry[2];

        		if ($id == $ibforums->input['poll_vote'])
        		{
        			$votes++;
        		}

        		$new_poll_array[] = array( $id, $choice, $votes);
        	}

        	$this->topic['choices'] = addslashes(serialize($new_poll_array));

        	$DB->simple_exec_query( array( 'update' => 'polls',
        								   'set'    => "votes=votes+1,choices='{$this->topic['choices']}'",
        								   'where'  => "pid={$this->topic['poll_id']}" ) );


        	if ($this->topic['allow_pollbump'])
        	{
        		$this->topic['last_vote'] = time();
        		$this->topic['last_post'] = time();

				$DB->do_update( 'topics', array( 'last_vote' => $this->topic['last_vote'], 'last_post' => $this->topic['last_post'] ), 'tid='.$this->topic['tid'] );
        	}
        	else
        	{
        		$this->topic['last_vote'] = time();

        		$DB->do_update( 'topics', array( 'last_vote' => $this->topic['last_vote'], 'last_post' => $this->topic['last_post'] ), 'tid='.$this->topic['tid'] );
        	}
        }

		$lang = $ibforums->input['nullvote'] ? $ibforums->lang['poll_viewing_results'] : $ibforums->lang['poll_vote_added'];

		$print->redirect_screen( $lang , "act=ST&f={$this->topic['forum_id']}&t={$this->topic['tid']}&st={$ibforums->input['st']}" );


	}

}

?>