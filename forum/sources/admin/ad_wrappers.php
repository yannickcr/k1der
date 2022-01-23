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
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class ad_wrappers {

	var $base_url;
	var $template = "";
	var $functions = "";

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Get the libraries
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/admin_template_functions.php' );

		$this->functions = new admin_template_functions();

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
			case 'floateditor':
				$this->functions->build_editor_area_floated(1);
				break;

			case 'edit':
				$this->do_form('edit');
				break;

			case 'doedit':
				$this->save_wrapper('edit');
				break;

			case 'export':
				$this->export();

			default:
				print "No action chosen"; exit();
				break;

			//case 'wrapper':
			//	$this->list_wrappers();
			//	break;
			//case 'add':
			//	$this->add_splash();
			//	break;
			//case 'doadd':
			//	$this->save_wrapper('add');
			//	break;
			//case 'remove':
			//	$this->remove();
			//	break;
		}

	}


	//-----------------------------------------
	// ADD / EDIT WRAPPERS
	//-----------------------------------------

	function save_wrapper( $type='add' )
	{
		global $ibforums, $DB,  $std;

		if ($type == 'edit')
		{
			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("You must specify an existing wrapper ID, go back and try again");
			}
		}

		if ($ibforums->input['txtwrapper'] == "")
		{
			$ibforums->admin->error("You can't have an empty template, can you?");
		}

		$tmpl = $ibforums->admin->form_to_text( $std->txt_stripslashes($_POST['txtwrapper']) );

		if ( ! preg_match( "/<% BOARD %>/", $tmpl ) )
		{
			$ibforums->admin->error("You cannot remove the &lt% BOARD %> tag silly!");
		}

		if ( ! preg_match( "/<% COPYRIGHT %>/", $tmpl ) )
		{
			$ibforums->admin->error("You cannot remove the &lt% COPYRIGHT %> tag silly!");
		}

		$DB->do_update( 'skin_sets', array( 'set_wrapper' => $tmpl ), 'set_skin_set_id='.$ibforums->input['id'] );

		$ibforums->cache_func->_recache_wrapper( $ibforums->input['id'] );

		//-----------------------------------------
		// Done
		//-----------------------------------------

		if ( ! $ibforums->input['savereload'] )
		{
			$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
			$ibforums->main_msg = "Board Header and Footer Wrapper Updated";
			$ibforums->admin->done_screen("Board Header and Footer Wrapper Updated", "Skin Manager Home", "act=sets", "redirect" );
		}
		else
		{
			//-----------------------------------------
			// Reload edit window
			//-----------------------------------------

			$ibforums->main_msg = "Board Header and Footer Wrapper updated";
			$this->do_form('edit');
		}

	}

	//-----------------------------------------
	// FORM
	//-----------------------------------------

	function do_form( $type='add' )
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check input
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		//-----------------------------------------
		// check tree...
		//-----------------------------------------

		$found_id      = "";
		$found_content = "";
		$this_set      = "";

		if ( $ibforums->input['p'] > 0 )
		{
			$in = ','.$ibforums->input['p'];
		}

		//-----------------------------------------
		// Query
		//-----------------------------------------

		$DB->cache_add_query( 'stylesheets_do_form_concat', array( 'id' => $ibforums->input['id'], 'parent' => $in ) );
		$DB->cache_exec_query();

		//-----------------------------------------
		// check tree...
		//-----------------------------------------

		while( $row = $DB->fetch_row() )
		{
			if ( $row['set_wrapper'] and ! $found_id )
			{
				$found_id      = $row['set_skin_set_id'];
				$found_content = $row['set_wrapper'];
			}

			if ( $ibforums->input['id'] == $row['set_skin_set_id'] )
			{
				$this_set = $row;
			}
		}

		if ($type == 'add')
		{
			$code = 'doadd';
			$button = 'Create Wrapper';
		}
		else
		{
			$code = 'doedit';
			$button = 'Save Wrapper';
		}

		//-----------------------------------------
		// Header
		//-----------------------------------------

		$ibforums->admin->page_detail = "You may use HTML fully when adding or editing wrappers.";
		$ibforums->admin->page_title  = "Editing Board Header and Footer Wrapper";

		if ( $found_id == 1 )
		{
			$ibforums->admin->page_detail .= "<br /><strong>This is a copy of the master wrapper, editing it below will copy the changes to a new wrapper unique to this skin set and any children of this set
											  will inherit this wrapper</strong>";
		}

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->js_no_specialchars();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $code      ),
																 2 => array( 'act'   , 'wrap'     ),
																 3 => array( 'id'    , $ibforums->input['id']   ),
																 4 => array( 'fid'   , $found_id  ),
														), "theform"     );

		//-----------------------------------------
		// Stop /textarea murdering layout
		//-----------------------------------------

		$found_content = $ibforums->admin->text_to_form( $found_content );

		//-----------------------------------------
		// Editor section
		//-----------------------------------------

		$ibforums->html .= $this->functions->build_generic_editor_area( array( 'act' => 'wrap', 'title' => '', 'textareaname' => 'wrapper', 'textareainput' => $found_content ) );

		$formbuttons = "<div align='center' class='pformstrip'>
						<input type='submit' name='submit' value='$button' class='realdarkbutton'>
						<input type='submit' name='savereload' value='Save and Reload Wrapper' class='realdarkbutton'>
						</div></form>\n";

		$ibforums->html = str_replace( '<!--IPB.EDITORBOTTOM-->', $formbuttons, $ibforums->html );


		$ibforums->html .= $ibforums->adskin->skin_jump_menu_wrap();

		//-----------------------------------------
		// Output
		//-----------------------------------------

		$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
		$ibforums->admin->nav[] = array( '' ,'Editing Board Wrapper in set '.$this_set['set_name'] );

		$ibforums->admin->output();
	}



}


?>