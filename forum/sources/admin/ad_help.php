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
|   > Help Control functions
|   > Module written by Matt Mecham
|   > Date started: 2nd April 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class ad_help
{
	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'edit':
				$this->show_form('edit');
				break;
			case 'new':
				$this->show_form('new');
				break;

			case 'doedit':
				$this->doedit();
				break;

			case 'donew':
				$this->doadd();
				break;

			case 'remove':
				$this->remove();
				break;

			//-----------------------------------------
			default:
				$this->list_files();
				break;
		}

	}

	//-----------------------------------------
	// HELP FILE FUNCTIONS
	//-----------------------------------------

	function doedit()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must pass a valid emoticon id, silly!");
		}

		$text  = preg_replace( "/\n/", "<br>", stripslashes($_POST['text'] ) );
		//$title = preg_replace( "/\n/", "<br>", stripslashes($HTTP_POST_VARS['title'] ) );
		$desc  = preg_replace( "/\n/", "<br>", stripslashes($_POST['description'] ) );

		$text  = preg_replace( "/\\\/", "&#092;", $text );

		$DB->do_update( 'faq', array( 'title'       => $ibforums->input['title'],
									  'text'        => $text,
									  'description' => $desc,
							 ) , "id=".intval($ibforums->input['id'])     );

		$ibforums->admin->save_log("Edited help files");

		$std->boink_it($ibforums->adskin->base_url."&act=help");
		exit();


	}

	//=====================================================


	function show_form($type='new')
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You may add/edit and remove help files below.";
		$ibforums->admin->page_title  = "Help File Management";

		//-----------------------------------------

		if ($type != 'new')
		{

			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("You must pass a valid help file id, silly!");
			}

			//-----------------------------------------

			$DB->simple_construct( array( 'select' => '*', 'from' => 'faq', 'where' => "id=".intval($ibforums->input['id']) ) );
			$DB->simple_exec();

			if ( ! $r = $DB->fetch_row() )
			{
				$ibforums->admin->error("We could not find that help file in the database");
			}

			//-----------------------------------------

			$button = 'Edit this Help File';
			$code   = 'doedit';
		}
		else
		{
			$r = array();
			$button = 'Add this Help File';
			$code   = 'donew';
		}

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $code ),
												  2 => array( 'act'   , 'help'     ),
												  3 => array( 'id'    , $ibforums->input['id'] ),
									     )      );



		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "20%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "80%" );

		$r['text'] = preg_replace( "/<br>/i", "\n", stripslashes($r['text']) );

 		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( $button );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Help File Title",
												  $ibforums->adskin->form_input('title'  , stripslashes($r['title']) ),
										 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Help File Description",
												  $ibforums->adskin->form_textarea('description', stripslashes($r['description']) ),
										 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Help File Text",
												  $ibforums->adskin->form_textarea('text', $r['text'], "60", "10" ),
										 )      );

		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//=====================================================

	function remove()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must pass a valid help file id, silly!");
		}

		$DB->simple_exec_query( array( 'delete' => 'faq', 'where' => "id=".$ibforums->input['id'] ) );

		$ibforums->admin->save_log("Removed a help file");

		$std->boink_it($ibforums->adskin->base_url."&act=help");
		exit();


	}

	//=====================================================

	function doadd()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['title'] == "")
		{
			$ibforums->admin->error("You must enter a title, silly!");
		}



		$text  = preg_replace( "/\n/", "<br>", stripslashes($_POST['text'] ) );
		$title = preg_replace( "/\n/", "<br>", stripslashes($_POST['title'] ) );
		$desc  = preg_replace( "/\n/", "<br>", stripslashes($_POST['description'] ) );

		$text  = preg_replace( "/\\\/", "&#092;", $text );

		$DB->do_insert( 'faq', array( 'title'       => $title,
									  'text'        => $text,
									  'description' => $desc,
							 )      );

		$ibforums->admin->save_log("Added a help file");

		$std->boink_it($ibforums->adskin->base_url."&act=help");
		exit();


	}

	//=====================================================

	function list_files()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You may add/edit and remove help files below.";
		$ibforums->admin->page_title  = "Help File Management";

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Title"  , "50%" );
		$ibforums->adskin->td_header[] = array( "Edit"   , "30%" );
		$ibforums->adskin->td_header[] = array( "Remove" , "20%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Current Help Files" );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'faq', 'order' => "id" ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{
			while ( $r = $DB->fetch_row() )
			{

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".stripslashes($r['title'])."</b><br>".stripslashes($r['description']),
														  "<center><a href='".$ibforums->adskin->base_url."&act=help&code=edit&id={$r['id']}'>Edit</a></center>",
														  "<center><a href='".$ibforums->adskin->base_url."&act=help&code=remove&id={$r['id']}'>Remove</a></center>",
												 )      );



			}
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic("<a href='".$ibforums->adskin->base_url."&act=help&code=new'>Add New Help File</a>", "center", "title" );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->admin->output();

	}


}


?>