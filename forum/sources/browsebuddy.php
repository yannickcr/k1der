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
|   > Browse Buddy Module
|   > Module written by Matt Mecham
|   > Date started: 2nd July 2002
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

class assistant {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";

	/*-------------------------------------------------------------------------*/
	// AUTO RUN
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_buddy', $ibforums->lang_id );

    	$this->html = $std->load_template('skin_buddy');

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['code']) {

    		default:
    			$this->splash();
    			break;
    	}

    	//-----------------------------------------
    	// If we have any HTML to print, do so...
    	//-----------------------------------------

    	$this->output = str_replace( "<!--CLOSE.LINK-->", $this->html->closelink(), $this->output );

    	$print->pop_up_window($ibforums->lang['page_title'], $this->html->buddy_js().$this->output);


 	}

 	/*-------------------------------------------------------------------------*/
 	// SPLASH
 	/*-------------------------------------------------------------------------*/

 	function splash()
 	{
		global $ibforums, $DB, $std, $forums;

 		//-----------------------------------------
 		// Is this a guest? If so, get 'em to log in.
 		//-----------------------------------------

 		if ( ! $ibforums->member['id'] )
 		{
 			$this->output = $this->html->login();
 			return;
 		}
 		else
 		{
 			//-----------------------------------------
 			// Get the forums we're allowed to search in
 			//-----------------------------------------

 			$allow_forums   = array();

 			$allow_forums[] = '0';

 			foreach( $forums->forum_by_id as $id => $data )
			{
				$allow_forums[] = $data['id'];
			}

 			$forum_string = implode( ",", $allow_forums );

 			//-----------------------------------------
 			// Get the number of posts since the last visit.
 			//-----------------------------------------

 			if ( ! $ibforums->member['last_visit'] )
 			{
 				$ibforums->member['last_visit'] = time() - 3600;
 			}

 			if ( $ibforums->forum_read[0] > $ibforums->member['last_visit'] )
			{
				$ibforums->member['last_visit'] = $ibforums->forum_read[0];
			}

 			$DB->cache_add_query( 'buddy_posts_last_visit', array( 'last_visit' => $ibforums->member['last_visit'], 'forum_string' => $forum_string ) );
			$DB->cache_exec_query();

 			$posts = $DB->fetch_row();

 			$posts_total = intval($posts['posts']);

 			//-----------------------------------------
 			// Get the number of posts since the last visit to topics we've started.
 			//-----------------------------------------

 			$DB->simple_construct( array( 'select' => 'count(*) as replies',
 										  'from'   => 'topics',
 										  'where'  => "last_post > {$ibforums->member['last_visit']}
														AND approved=1 AND forum_id IN($forum_string)
														AND posts > 0
														AND starter_id={$ibforums->member['id']}" ) );

 			$DB->simple_exec();

 			$topic = $DB->fetch_row();

 			$topics_total = ($topic['replies'] < 1) ? ucfirst($ibforums->lang['none']) : $topic['replies'];

 			$text = $ibforums->lang['no_new_posts'];

 			if ($posts_total > 0)
 			{
 				$ibforums->lang['new_posts']  = sprintf($ibforums->lang['new_posts'] , $posts_total  );
 				$ibforums->lang['my_replies'] = sprintf($ibforums->lang['my_replies'], $topics_total );

 				$ibforums->lang['new_posts'] .= $this->html->append_view("&act=Search&CODE=getnew");

 				if ($topic['replies'] > 0)
 				{
 					$ibforums->lang['my_replies'] .= $this->html->append_view("&act=Search&CODE=getreplied");
 				}

 				$text = $this->html->build_away_msg();
 			}

 			$this->output = $this->html->main($text);
 		}
 	}
}

?>