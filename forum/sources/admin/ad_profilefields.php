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
|   > Custom profile field functions
|   > Module written by Matt Mecham
|   > Date started: 24th June 2002
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


class ad_profilefields {

	var $base_url;
	var $func;

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
		// get class
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
		$this->func = new custom_fields( $DB );

		//-----------------------------------------
		// switch-a-magoo
		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'add':
				$this->main_form('add');
				break;

			case 'doadd':
				$this->main_save('add');
				break;

			case 'edit':
				$this->main_form('edit');
				break;

			case 'doedit':
				$this->main_save('edit');
				break;

			case 'delete':
				$this->delete_form();
				break;

			case 'dodelete':
				$this->do_delete();
				break;

			default:
				$this->main_screen();
				break;
		}

	}

	//-----------------------------------------
	//
	// Rebuild cache
	//
	//-----------------------------------------

	function rebuild_cache()
	{
		global $ibforums, $DB, $std;

		$ibforums->cache['profilefields'] = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'order' => 'pf_position' ) );

		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['profilefields'][ $r['pf_id'] ] = $r;
		}

		$std->update_cache( array( 'name' => 'profilefields', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	//
	// Delete a group
	//
	//-----------------------------------------

	function delete_form()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the group ID, please try again");
		}

		$ibforums->admin->page_title = "Deleting a Custom Profile Field";

		$ibforums->admin->page_detail = "Please check to ensure that you are attempting to remove the correct custom profile field as <b>all data will be lost!</b>.";

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($ibforums->input['id']) ) );
		$DB->simple_exec();

		if ( ! $field = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not fetch the row from the database");
		}

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dodelete'  ),
																 2 => array( 'act'   , 'field'     ),
																 3 => array( 'id'    , $ibforums->input['id']   ),
														)      );



		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Removal Confirmation" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Custom Profile field to remove</b>" ,
												                 "<b>".$field['pf_title']."</b>",
									                   )      );

		$ibforums->html .= $ibforums->adskin->end_form("Delete this custom field");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}



	function do_delete()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the field ID, please try again");
		}

		//-----------------------------------------
		// Check to make sure that the relevant groups exist.
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($ibforums->input['id']) ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the ID's passed to deletion");
		}

		$DB->sql_drop_field( 'pfields_content', "field_{$row['pf_id']}" );

		$DB->simple_exec_query( array( 'delete' => 'pfields_data', 'where' => "pf_id=".intval($ibforums->input['id']) ) );

		$this->rebuild_cache();

		$ibforums->admin->done_screen("Profile Field Removed", "Custom Profile Field Control", "act=field", 'redirect' );

	}


	//-----------------------------------------
	//
	// Save changes to DB
	//
	//-----------------------------------------

	function main_save($type='edit')
	{
		global $ibforums, $DB, $std;

		$ibforums->input['id'] = intval($ibforums->input['id']);

		if ($ibforums->input['pf_title'] == "")
		{
			$ibforums->admin->error("You must enter a field title.");
		}

		//-----------------------------------------
		// check-da-motcha
		//-----------------------------------------

		if ($type == 'edit')
		{
			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("Could not resolve the field id");
			}

		}

		$content = "";

		if ( $_POST['pf_content'] != "")
		{
			$content = $this->func->method_format_content_for_save( $_POST['pf_content'] );
		}

		$db_string = array( 'pf_title'        => $ibforums->input['pf_title'],
						    'pf_desc'         => $ibforums->input['pf_desc'],
						    'pf_content'      => $std->txt_stripslashes($content),
						    'pf_type'         => $ibforums->input['pf_type'],
						    'pf_not_null'     => $ibforums->input['pf_not_null'],
						    'pf_member_hide'  => $ibforums->input['pf_member_hide'],
						    'pf_max_input'    => $ibforums->input['pf_max_input'],
						    'pf_member_edit'  => $ibforums->input['pf_member_edit'],
						    'pf_position'     => $ibforums->input['pf_position'],
						    'pf_show_on_reg'  => $ibforums->input['pf_show_on_reg'],
						    'pf_input_format' => $ibforums->input['pf_input_format'],
						    'pf_admin_only'   => $ibforums->input['pf_admin_only'],
						    'pf_topic_format' => $std->txt_stripslashes( $_POST['pf_topic_format']),
						  );


		if ($type == 'edit')
		{
			$DB->do_update( 'pfields_data', $db_string, 'pf_id='.$ibforums->input['id'] );

			$this->rebuild_cache();

			$ibforums->main_msg = "Profile Field Edited";
			$this->main_screen();

		}
		else
		{
			$DB->do_insert( 'pfields_data', $db_string );

			$new_id = $DB->get_insert_id();

			$DB->sql_add_field( 'pfields_content', "field_$new_id", 'text' );

			$DB->sql_optimize_table( 'pfields_content' );

			$this->rebuild_cache();

			$ibforums->main_msg = "Profile Field Added";
			$this->main_screen();
		}
	}


	//-----------------------------------------
	//
	// Add / edit group
	//
	//-----------------------------------------

	function main_form($type='edit')
	{
		global $ibforums, $DB,  $std;

		$ibforums->input['id'] = intval($ibforums->input['id']);

		if ($type == 'edit')
		{
			if ( ! $ibforums->input['id'] )
			{
				$ibforums->admin->error("No group id to select from the database, please try again.");
			}

			$form_code = 'doedit';
			$button    = 'Complete Edit';

		}
		else
		{
			$form_code = 'doadd';
			$button    = 'Add Field';
		}

		//-----------------------------------------
		// get field from db
		//-----------------------------------------

		if ( $ibforums->input['id'] )
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($ibforums->input['id']) ) );
			$DB->simple_exec();

			$fields = $DB->fetch_row();
		}
		else
		{
			$fields = array( 'pf_topic_format' => '{title}: {content}<br />' );
		}

		//-----------------------------------------
		// Top 'o 'the mornin'
		//-----------------------------------------

		if ($type == 'edit')
		{
			$ibforums->admin->page_title = "Editing Profile Field ".$fields['ftitle'];
		}
		else
		{
			$ibforums->admin->page_title = 'Adding a new profile field';
			$fields['pf_title'] = '';
		}

		//-----------------------------------------
		// Wise words
		//-----------------------------------------

		$ibforums->admin->page_detail = "Please double check the information before submitting the form.";

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $form_code  ),
												                 2 => array( 'act'   , 'field'     ),
												                 3 => array( 'id'    , $ibforums->input['id']   ),
									                    )     );

		//-----------------------------------------
		// Format...
		//-----------------------------------------

		$fields['pf_content'] = $this->func->method_format_content_for_edit($fields['pf_content'] );

		//-----------------------------------------
		// Tbl (no ae?)
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Field Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Field Title</b><div class='graytext'>Max characters: 200</div>" ,
												                 $ibforums->adskin->form_input("pf_title", $fields['pf_title'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Description</b><div class='graytext'>Max Characters: 250<br />Can be used to note hidden/required status</div>" ,
												                 $ibforums->adskin->form_input("pf_desc", $fields['pf_desc'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Field Type</b>" ,
																 $ibforums->adskin->form_dropdown("pf_type",
																					  array(
																							   0 => array( 'text' , 'Text Input' ),
																							   1 => array( 'drop' , 'Drop Down Box' ),
																							   2 => array( 'area' , 'Text Area' ),
																						   ),
																					  $fields['pf_type'] )
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Maximum Input</b><div class='graytext'>For text input and text areas (in characters)</div>" ,
												                 $ibforums->adskin->form_input("pf_max_input", $fields['pf_max_input'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Display order</b><div class='graytext'>When editing and displaying (numeric 1 lowest)</div>" ,
												                 $ibforums->adskin->form_input("pf_position", $fields['pf_position'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Expected Input Format</b><div class='graytext'>Use: <b>a</b> for alpha characters<br />Use: <b>n</b> for numerics.<br />Example, for credit card numbers: nnnn-nnnn-nnnn-nnnn<br />Example, Date of Birth: nn-nn-nnnn<br />Leave blank to accept any input</div>" ,
												                 $ibforums->adskin->form_input("pf_input_format", $fields['pf_input_format'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Option Content (for drop downs)</b><div class='graytext'>In sets, one set per line<br>Example for 'Gender' field:<br>m=Male<br>f=Female<br>u=Not Telling<br>Will produce:<br><select name='pants'><option value='m'>Male</option><option value='f'>Female</option><option value='u'>Not Telling</option></select><br>m,f or u stored in database. When showing field in profile, will use value from pair (f=Female, shows 'Female')</div>" ,
												                 $ibforums->adskin->form_textarea("pf_content", $fields['pf_content'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Include on registration page?</b><div class='graytext'>If 'yes', the field will be shown upon registration.</div>" ,
												                 $ibforums->adskin->form_yes_no("pf_show_on_reg", $fields["pf_show_on_reg"] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Field MUST be completed and not left empty?</b><div class='graytext'>If 'yes', an error will be shown if this field is not completed.</div>" ,
												                 $ibforums->adskin->form_yes_no("pf_not_null", $fields['pf_not_null'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Field can be edited by the member?</b><div class='graytext'>If 'no', the member cannot edit the field but Super Moderators and Admins will be able to.</div>" ,
												                 $ibforums->adskin->form_yes_no("pf_member_edit", $fields['pf_member_edit'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Make this a private profile field?</b><div class='graytext'>If yes, field only visible to profile owner, super moderators and admins. If 'no', members can search within this field.</div>" ,
												                 $ibforums->adskin->form_yes_no("pf_member_hide", $fields['pf_member_hide'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Make Admin and Super Moderator Editable/Viewable Only?</b><div class='graytext'>If yes, will override the above options so only admins and super moderators can see and edit this field.</div>" ,
												                 $ibforums->adskin->form_yes_no("pf_admin_only", $fields['pf_admin_only'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Topic View Format?</b><div class='graytext'>Leave blank if you do not wish to add this field in the author details when viewing a topic.<br />{title} is the title of the custom field, {content} is the user added content. {key} is the form select value of the selected item in a dropdown box.<br />Example: {title}:{content}&lt;br /&gt;<br />Example: {title}:&lt;img src='imgs/{key}'&gt;</div>" ,
												                 $ibforums->adskin->form_textarea("pf_topic_format", $fields['pf_topic_format'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------
	//
	// Show "Management Screen
	//
	//-----------------------------------------

	function main_screen()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title   = "Custom Profile Fields";

		$ibforums->admin->page_detail  = "Custom Profile fields can be used to add optional or required fields to be completed when registering or editing a profile. This is useful if you wish to record data from your members that is not already present in the base board.";

		$ibforums->adskin->td_header[] = array( "Field Title"    , "20%" );
		$ibforums->adskin->td_header[] = array( "Type"           , "10%" );
		$ibforums->adskin->td_header[] = array( "REQUIRED"       , "10%" );
		$ibforums->adskin->td_header[] = array( "NOT PUBLIC"     , "10%" );
		$ibforums->adskin->td_header[] = array( "SHOW REG"       , "10%" );
		$ibforums->adskin->td_header[] = array( "ADMIN ONLY"     , "10%" );
		$ibforums->adskin->td_header[] = array( "Edit"           , "10%" );
		$ibforums->adskin->td_header[] = array( "Delete"         , "10%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Custom Profile Field Management" );

		$real_types = array( 'drop' => 'Drop Down Box',
							 'area' => 'Text Area',
							 'text' => 'Text Input',
						   );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'order' => 'pf_position' ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{
			while ( $r = $DB->fetch_row() )
			{

				$hide   = '&nbsp;';
				$req    = '&nbsp;';
				$regi   = '&nbsp;';
				$admin  = '&nbsp;';

				//-----------------------------------------
				// Hidden?
				//-----------------------------------------

				if ($r['pf_member_hide'] == 1)
				{
					$hide = '<center><span style="color:red">Y</span></center>';
				}

				//-----------------------------------------
				// Required?
				//-----------------------------------------

				if ($r['pf_not_null'] == 1)
				{
					$req = '<center><span style="color:red">Y</span></center>';
				}

				//-----------------------------------------
				// Show on reg?
				//-----------------------------------------

				if ($r['pf_show_on_reg'] == 1)
				{
					$regi = '<center><span style="color:red">Y</span></center>';
				}

				//-----------------------------------------
				// Admin only...
				//-----------------------------------------

				if ($r['pf_admin_only'] == 1)
				{
					$admin = '<center><span style="color:red">Y</span></center>';
				}


				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$r['pf_title']}</b><div class='graytext'>{$r['pf_desc']}</div>" ,
																		 "<center>{$real_types[$r['pf_type']]}</center>",
																		 $req,
																		 $hide,
																		 $regi,
																		 $admin,
																		 "<center><a href='{$ibforums->base_url}&act=field&code=edit&id=".$r['pf_id']."'>Edit</a></center>",
																		 "<center><a href='{$ibforums->base_url}&act=field&code=delete&id=".$r['pf_id']."'>Delete</a></center>",
															)      );

			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("None found", "center", "tdrow1");
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic( $ibforums->adskin->js_make_button( 'ADD NEW FIELD', "{$ibforums->base_url}&act=field&code=add" ), "center", "pformstrip");

		$ibforums->html .= $ibforums->adskin->end_table();


		$ibforums->admin->output();


	}
}


?>