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
|   > Find-a-post module (a.k.a: The smallest IPB class ever)
|   > Module written by Matt Mecham
|   > Date started: 14th April 2004
|   > Interesting Fact: I've had iTunes playing every Radiohead tune
|   > I own for about a week now. Thats a lot of repeats. Got some
|   > cool rare tracks though. Every album+rare+b sides = 6.7 hours
|   > music. Not bad. I need to get our more. No, you can't take the
|   > laptop with you - nerd.
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

class findpost
{
	var $post;

    function auto_run()
    {
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Find a post
		// Don't really need to check perms 'cos topic
		// will do that for us. Woohoop
		//-----------------------------------------

		$pid = intval($ibforums->input['pid']);

		if ( ! $pid )
		{
			$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
		}

		//-----------------------------------------
		// Get topic...
		//-----------------------------------------

		$post = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'posts', 'where' => 'pid='.$pid ) );

		if ( ! $post['topic_id'] )
		{
			$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
		}

		$DB->simple_construct( array( 'select' => 'COUNT(*) as posts',
									  'from'   => 'posts',
									  'where'  => "topic_id=".$post['topic_id']." AND pid <= ".$pid,
							)      );

		$DB->simple_exec();

		$cposts = $DB->fetch_row();

		if ( (($cposts['posts']) % $ibforums->vars['display_max_posts']) == 0 )
		{
			$pages = ($cposts['posts']) / $ibforums->vars['display_max_posts'];
		}
		else
		{
			$number = ( ($cposts['posts']) / $ibforums->vars['display_max_posts'] );
			$pages = ceil( $number);
		}

		$st = ($pages - 1) * $ibforums->vars['display_max_posts'];

		$std->boink_it($ibforums->base_url."showtopic=".$post['topic_id']."&st=$st&p=$pid"."&#entry".$pid);
 	}
}

?>