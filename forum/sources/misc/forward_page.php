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
|   > Forward topic to a friend module
|   > Module written by Matt Mecham
|   > Date started: 21st March 2002
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

class forwardpage {

    var $output    = "";
    var $base_url  = "";
    var $html      = "";

    var $forum     = array();
    var $topic     = array();
    var $category  = array();


    /*-------------------------------------------------------------------------*/
	//
	// Our constructor, load words, load skin, print the topic listing
	//
	/*-------------------------------------------------------------------------*/

    function forwardpage()
    {
		global $ibforums, $DB, $std, $print, $forums;

        //-----------------------------------------
		// Compile the language file
		//-----------------------------------------

        $ibforums->lang = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id);

        $this->html     = $std->load_template('skin_emails');

        //-----------------------------------------
        // Check the input
        //-----------------------------------------

        $ibforums->input['t'] = intval($ibforums->input['t']);
        $ibforums->input['f'] = intval($ibforums->input['f']);

        if ( !$ibforums->input['t'] )
        {
            $std->Error( array( LEVEL => 1, MSG => 'missing_files') );
        }

        //-----------------------------------------
        // Get the topic details
        //-----------------------------------------

        $DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => "tid=".intval($ibforums->input['t']) ) );
		$DB->simple_exec();

        $this->topic = $DB->fetch_row();

        $this->forum = $forums->forum_by_id[ $this->topic['forum_id'] ];

        //-----------------------------------------
        // Error out if we can not find the forum
        //-----------------------------------------

        if ( ! $this->forum['id'] )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
        }

        //-----------------------------------------
        // Error out if we can not find the topic
        //-----------------------------------------

        if (!$this->topic['tid'])
        {
        	$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
        }

        $this->base_url    = $ibforums->base_url;

        $this->base_url_NS = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}";

        //-----------------------------------------
        // Check viewing permissions, private forums,
        // password forums, etc
        //-----------------------------------------

        if (! $ibforums->member['id'] )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_guests') );
        }

        $forums->forums_check_access( $this->forum['id'] );

        //-----------------------------------------
		// What to do?
		//-----------------------------------------

		if ($ibforums->input['CODE'] == '01')
		{
			$this->send_email();
		}
		else
		{
			$this->show_form();
		}
	}


	function send_email()
	{
		global $std, $ibforums, $DB, $print;

		require ROOT_PATH."sources/classes/class_email.php";

		$this->email = new emailer();

		$lang_to_use = 'en';

		$DB->query("SELECT lid, ldir, lname FROM ibf_languages");

		while ( $l = $DB->fetch_row() )
		{
			if ($ibforums->input['lang'] == $l['ldir'])
			{
				$lang_to_use = $l['ldir'];
			}
		}

		$check_array = array ( 'to_name'   =>  'stf_no_name',
							   'to_email'  =>  'stf_no_email',
							   'message'   =>  'stf_no_msg',
							   'subject'   =>  'stf_no_subject'
							 );

		foreach ($check_array as $input => $msg)
		{
			if (empty($ibforums->input[$input]))
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => $msg) );
			}
		}

		$to_email = $std->clean_email($ibforums->input['to_email']);

		if (! $to_email )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_email' ) );
		}

		$this->email->get_template("forward_page", $lang_to_use);

		$this->email->build_message( array(
											'THE_MESSAGE'     => str_replace( "<br />", "\n", $ibforums->input['message'] ),
											'TO_NAME'         => $ibforums->input['to_name'],
											'FROM_NAME'       => $ibforums->member['name'],
										  )
									);

		$this->email->subject = $ibforums->input['subject'];
		$this->email->to      = $ibforums->input['to_email'];
		$this->email->from    = $ibforums->member['email'];
		$this->email->send_mail();

		$print->redirect_screen( $ibforums->lang['redirect'], "showtopic=".$this->topic['tid']."&st=".$ibforums->input['st'] );

	}





	function show_form()
	{
		global $std, $ibforums, $DB, $print;

		require ROOT_PATH."lang/".$ibforums->lang_id."/lang_email_content.php";

		$ibforums->lang['send_text'] = $lang['send_text'];

		$lang_array = unserialize(stripslashes($ibforums->vars['languages']));

		$lang_select = "<select name='lang' class='forminput'>\n";

		$DB->query("SELECT lid, ldir, lname FROM ibf_languages");

		while ( $l = $DB->fetch_row() )
		{
			$lang_select .= $l['ldir'] == $ibforums->member['language'] ? "<option value='{$l['ldir']}' selected>{$l['lname']}</option>"
																		: "<option value='{$l['ldir']}'>{$l['lname']}</option>";
		}

 		$lang_select .= "</select>";

		$ibforums->lang['send_text'] = preg_replace( "/<#THE LINK#>/" , $this->base_url_NS."?act=ST&f=".$this->forum['id']."&t=".$this->topic['tid'], $ibforums->lang['send_text'] );
		$ibforums->lang['send_text'] = preg_replace( "/<#USER NAME#>/", $ibforums->member['name'], $ibforums->lang['send_text'] );

		$this->output = $this->html->forward_form( $this->topic['title'], $ibforums->lang['send_text'], $lang_select  );

		$this->page_title  = $ibforums->lang['title'];

		$this->nav         = array ( "<a href='{$this->base_url}act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",  "<a href='".$this->base_url."act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>", $ibforums->lang['title'] );

		$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );

	}







//-----------------------------------------


	function check_access()
	{
		global $ibforums, $std, $HTTP_COOKIE_VARS;

		$return = 1;

		if ( $std->check_perms($this->forum['read_perms']) == TRUE )
		{
			$return = 0;
		}

		if ($this->forum['password'])
		{
			if ($HTTP_COOKIE_VARS[ $ibforums->vars['cookie_id'].'iBForum'.$this->forum['id'] ] == $this->forum['password']) {
				$return = 0;
			} else {
				$return = 1;
			}
		}

		return $return;

	}
}

?>