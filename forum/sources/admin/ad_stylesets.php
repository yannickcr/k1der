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


class ad_stylesets
{
	var $base_url;
	var $master_set = 1;

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
			case 'wrapper':
				$this->list_wrappers();
				break;

			case 'addset':
				$this->add_set();
				break;

			case 'edit':
				$this->do_form('edit');
				break;

			case 'doedit':
				$this->save_skin('edit');
				break;

			case 'remove':
				$this->remove_splash();
				break;

			case 'doremove':
				$this->do_remove();
				break;

			//-----------------------------------------

			case 'export':
				$this->export();
				break;

			case 'revertallform':
				$this->revert_all_form();
				break;

			case 'dorevert':
				$this->do_revert_all();
				break;

			case 'toggledefault':
				$this->set_toggle_default();
				break;

			case 'togglevisible':
				$this->set_toggle_visible();
				break;

			//-----------------------------------------
			// Export master
			//-----------------------------------------

			case 'exportmaster':
				$this->export_master();
				break;

			case 'exportmacro':
				$this->export_macro();
				break;

			//-----------------------------------------
			// Rebuild all
			//-----------------------------------------

			case 'rebuildalltemplates':
				$this->rebuild_all_templates();
				break;

			//-----------------------------------------
			// Export bits
			//-----------------------------------------

			case 'exportbitschoose':
				$this->export_bits_choose();
				break;

			case 'exportbitscomplete':
				$this->export_bits_complete();
				break;

			default:
				$this->list_sets();
				break;
		}

	}

	/*-------------------------------------------------------------------------*/
	// EXPORT SOME TEMPLATE BITS TO SQL FILE (COMPLETE)
	/*-------------------------------------------------------------------------*/

	function export_bits_complete()
	{
		global $ibforums, $DB, $std;

		$ids = array();

		//-----------------------------------------
		// get ids...
		//-----------------------------------------

		foreach ($ibforums->input as $key => $value)
		{
			if ( preg_match( "/^id_(\d+)$/", $key, $match ) )
			{
				if ($ibforums->input[$match[0]])
				{
					$ids[] = $match[1];
				}
			}
		}

		//-----------------------------------------
		// Got any?
		//-----------------------------------------

		if ( ! count( $ids ) )
		{
			$ibforums->main_msg = "You must select SOME template bits to export!";
			$this->export_bits_choose();
		}

		$final_sql = "";

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => 'suid IN ('.implode(",",$ids).')' ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$content = preg_replace( "/'/", "\\'", $std->txt_safeslashes( $r['section_content'] ) );
			$datavar = preg_replace( "/'/", "\\'", $std->txt_safeslashes( $r['func_data']       ) );

			$content = str_replace( "\n", '\n', $content );

			$final_sql .= "REPLACE INTO ibf_skin_templates SET set_id=1, group_name='{$r['group_name']}', func_name='{$r['func_name']}', section_content='$content', func_data='$datavar';\n";
		}

		//@header("Content-type: text/plain");
		//print $final_sql;
		//exit();

		//-----------------------------------------
		// Print to browser
		//-----------------------------------------

		$ibforums->admin->show_download( $final_sql, 'templates_update.sql', '', 0 );

	}

	/*-------------------------------------------------------------------------*/
	// EXPORT SOME TEMPLATE BITS TO SQL FILE
	/*-------------------------------------------------------------------------*/

	function export_bits_choose()
	{
		global $ibforums, $DB, $std;

		$all_templates = array();

		$DB->simple_construct( array( 'select' => 'group_name,set_id,suid,func_name', 'from' => 'skin_templates', 'where' => 'set_id=1' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$all_templates[ $r['group_name'] ][] = $r;
		}

		ksort( $all_templates );

		//-----------------------------------------
		// Start output
		//-----------------------------------------

		$ibforums->admin->page_title  = "Export Selected Template bits";
		$ibforums->admin->page_detail = "Check the box of the bit you wish to export.";

		//-----------------------------------------
		// start form
		//-----------------------------------------

		$per_row  = 3;
		$td_width = 100 / $per_row;
		$count    = 0;
		$output   = "<tr align='center'>\n";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'exportbitscomplete' ),
															     2 => array( 'act'   , 'sets'      ),
													    )      );

		$ibforums->html .= "<div class='tableborder'>
							 <div class='maintitle'>Template Bits</div>
							 ";

		foreach( $all_templates as $group_name => $data )
		{
			//-----------------------------------------
			// Start secondary table
			//-----------------------------------------

			$count = 0;

			$output .= "<div class='tableborder'>
						 <div class='titlemedium'>$group_name</div>
						 <table width='100%' cellspacing='1' cellpadding='4' border='0'>
						 <tr>";

			foreach( $all_templates[ $group_name ] as $r )
			{
				$count++;

				$class = $count == 2 ? 'tdrow2' : 'tdrow1';

				$output .= "<td width='{$td_width}%' align='left' class='$class'>
							 <input type='checkbox' style='checkbox' value='1' name='id_{$r['suid']}' /> <strong>{$r['func_name']}</strong>
							</td>";

				if ($count == $per_row )
				{
					$output .= "</tr>\n\n<tr align='center'>";
					$count   = 0;
				}
			}

			if ( $count > 0 and $count != $per_row )
			{
				for ($i = $count ; $i < $per_row ; ++$i)
				{
					$output .= "<td class='tdrow2'>&nbsp;</td>\n";
				}

				$output .= "</tr>";
			}

			$output .= "</tr>\n</table></div>";
		}

		$ibforums->html .= $output;

		$ibforums->html .= "<div class='pformstrip' align='center'><input type='submit' class='realbutton' value='EXPORT SELECTED' /></form></div></div>";

		$ibforums->admin->output();
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD TEMPLATES
	/*-------------------------------------------------------------------------*/

	function rebuild_all_templates()
	{
		global $ibforums, $DB, $std;

		if ( $ibforums->input['removewarning'] == 1 )
		{
			$DB->simple_exec_query( array( "delete" => "cache_store", "where" => "cs_key='skinpanic'" ) );
		}

		$justdone = intval($ibforums->input['justdone']);
		$justdone = $justdone ? $justdone : 1;

		//-----------------------------------------
		// Get skins
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'skin_sets',
									  'where'  => 'set_skin_set_id > '.$justdone,
									  'order'  => 'set_skin_set_id',
									  'limit'  => array( 0, 1 )
						     )      );

		$DB->simple_exec();

		//-----------------------------------------
		// Got a biggun?
		//-----------------------------------------

		$r = $DB->fetch_row();

		if ( $r['set_skin_set_id'] )
		{
			$ibforums->cache_func->_rebuild_all_caches( array($r['set_skin_set_id']) );

			$ibforums->admin->redirect( "act=sets&code=rebuildalltemplates&justdone={$r['set_skin_set_id']}", "Rebuilt cache for skin set {$r['set_name']}<br />Proceeding to the next skin..." );
		}
		else
		{
			$ibforums->main_msg = "All skin templates recached!";
			$this->list_sets();
		}
	}

	//-----------------------------------------
	// Export master skin set.
	//-----------------------------------------

	function export_master()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Start...
		//-----------------------------------------

		$xml->xml_set_root( 'templateexport', array( 'exported' => time(), 'versionid' => '20000', 'type' => 'master' ) );

		//-----------------------------------------
		// Get emo group
		//-----------------------------------------

		$xml->xml_add_group( 'templategroup' );

		$DB->simple_construct( array( 'select' => 'group_name, section_content, func_name, func_data', 'from' => 'skin_templates', 'where' => 'set_id=1' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$content = array();

			foreach ( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}

			$entry[] = $xml->xml_build_entry( 'template', $content );
		}

		$xml->xml_add_entry_to_group( 'templategroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$ibforums->admin->show_download( $xml->xml_document, 'ipb_templates.xml', '', 0 );
	}

	//-----------------------------------------
	// Export master macros
	//-----------------------------------------

	function export_macro()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Start...
		//-----------------------------------------

		$xml->xml_set_root( 'macroexport', array( 'exported' => time() ) );

		//-----------------------------------------
		// Get emo group
		//-----------------------------------------

		$xml->xml_add_group( 'macrogroup' );

		$DB->simple_construct( array( 'select' => 'macro_value,macro_replace', 'from' => 'skin_macro', 'where' => 'macro_set=1' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$content = array();

			foreach ( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}

			$entry[] = $xml->xml_build_entry( 'macro', $content );
		}

		$xml->xml_add_entry_to_group( 'macrogroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$ibforums->admin->show_download( $xml->xml_document, 'ipb_macro.xml', '', 0 );

	}

	//-----------------------------------------
	// ADD SET
	//-----------------------------------------

	function add_set()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Check for input
		//-----------------------------------------

		$new     = array();
		$message = array();

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		if ( $ibforums->input['id'] == 1 )
		{
			$ibforums->main_msg = "You cannot alter the master skin set";
			$this->list_sets();
		}

		if ( ! $ibforums->input['set_name'] )
		{
			$ibforums->main_msg = "You must enter a skin set name.";
			$this->list_sets();
		}

		if ( $ibforums->input['id'] == -1 )
		{
			//-----------------------------------------
			// No parent...
			//-----------------------------------------

			$new['set_skin_set_parent'] = -1;
			$get_from_db = 1;
		}
		else
		{
			$new['set_skin_set_parent'] = $ibforums->input['id'];
			$get_from_db = $ibforums->input['id'];
		}

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$get_from_db ) );

		$new['set_name']          = $ibforums->input['set_name'];
		$new['set_image_dir']     = $this_set['set_image_dir'];
		$new['set_hidden']        = $ibforums->input['hidden'];
		$new['set_default']       = 0;
		$new['set_css_method']    = $this_set['set_css_method'];
		$new['set_cache_css']     = $this_set['set_cache_css'];
		$new['set_cache_macro']   = $this_set['set_cache_macro'];
		$new['set_cache_wrapper'] = $this_set['set_cache_wrapper'];

		$DB->do_insert( 'skin_sets', $new );

		$newid = $DB->get_insert_id();

		//-----------------------------------------
		// Rebuild caches
		//-----------------------------------------

		$ibforums->cache_func->_rebuild_all_caches( array( $newid ) );

		$ibforums->main_msg = '<b>Skin Set Added</b>';

		$ibforums->main_msg .= "<br />".implode("<br />", array_merge( $message, $ibforums->cache_func->messages) );
		$this->list_sets();
	}

	//-----------------------------------------
	// Revert customizations > DO
	//-----------------------------------------

	function do_revert_all()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check for input
		//-----------------------------------------

		$message = array();

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		if ( $ibforums->input['id'] == 1 )
		{
			$ibforums->main_msg = "You cannot alter the master skin set";
			$this->list_sets();
		}

		$id = intval($ibforums->input['id']);

		//-----------------------------------------
		// Delete Templates?
		//-----------------------------------------

		if ( $ibforums->input['html'] )
		{
			$DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => 'set_id='.$id ) );
			$message[] = 'Removed all custom HTML template bits...';
		}

		//-----------------------------------------
		// Delete Macros?
		//-----------------------------------------

		if ( $ibforums->input['macro'] )
		{
			$DB->simple_exec_query( array( 'delete' => 'skin_macro', 'where' => 'macro_set='.$id ) );
			$message[] = 'Removed all custom replacement macros...';
		}

		//-----------------------------------------
		// Delete Wrapper
		//-----------------------------------------

		if ( $ibforums->input['wrapper'] )
		{
			$DB->simple_exec_query( array( 'update' => 'skin_sets', 'set' => "set_wrapper=''", 'where' => 'set_skin_set_id='.$id ) );
			$message[] = 'Removed custom wrapper...';
		}

		//-----------------------------------------
		// Delete Wrapper
		//-----------------------------------------

		if ( $ibforums->input['css'] )
		{
			$DB->simple_exec_query( array( 'update' => 'skin_sets', 'set' => "set_css=''", 'where' => 'set_skin_set_id='.$id ) );
			$message[] = 'Removed custom CSS...';
		}

		//-----------------------------------------
		// Rebuild caches
		//-----------------------------------------

		$ibforums->cache_func->_rebuild_all_caches( array( $id ) );

		$ibforums->main_msg = 'Skin set customizations removed';

		$ibforums->main_msg .= "<br />".implode("<br />", array_merge( $message, $ibforums->cache_func->messages) );
		$this->list_sets();
	}

	//-----------------------------------------
	// Revert customizations form
	//-----------------------------------------

	function revert_all_form()
	{
		global $ibforums, $DB,  $std;

		$templates = 0;
		$macros    = 0;

		//-----------------------------------------
		// Check for input
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		if ( $ibforums->input['id'] == 1 )
		{
			$ibforums->main_msg = "You cannot alter the master skin set";
			$this->list_sets();
		}

		$ibforums->admin->page_detail = "<strong>Please note that this change cannot be undone!</strong>";
		$ibforums->admin->page_title  = "Revert Skin Set Customizations";

		//-----------------------------------------
		// Get macro / template info
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'count(*) as aerosmith', 'from' => 'skin_templates', 'where' => "set_id={$ibforums->input['id']}" ) );
		$DB->simple_exec();

		$r = $DB->fetch_row();
		$templates = intval($r['aerosmith']);

		$DB->simple_construct( array( 'select' => 'count(*) as aerosmith', 'from' => 'skin_macro', 'where' => "macro_set={$ibforums->input['id']}" ) );
		$DB->simple_exec();

		$r = $DB->fetch_row();
		$macros = intval($r['aerosmith']);

		//-----------------------------------------
		// Get the thingies
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id=".$ibforums->input['id'] ) );

		//-----------------------------------------
		// Start the form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code', 'dorevert'                  ),
																 2 => array( 'act' , 'sets'                      ),
																 3 => array( 'id'  , $ibforums->input['id']      ),
														), "theAdminForm"    );

		$none = "<em>No customizations to remove</em>";

		$html    = $templates               ? $ibforums->adskin->form_yes_no('html'   , 0) : $none;
		$macro   = $macros                  ? $ibforums->adskin->form_yes_no('macro'  , 0) : $none;
		$wrapper = $this_set['set_wrapper'] ? $ibforums->adskin->form_yes_no('wrapper', 0) : $none;
		$css     = $this_set['set_css']     ? $ibforums->adskin->form_yes_no('css'    , 0) : $none;

		//-----------------------------------------
		// Start output
		//-----------------------------------------

		$ibforums->html .= "<div class='tableborder'>
							<div class='maintitle'>Reverting Customizations in set {$this_set['set_name']}</div>
							<div class='tablepad'>
							<fieldset class='tdfset'>
							 <legend><strong>Template HTML Customizations</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Revert all template bit HTML customizations?<br /><span style='color:gray'>You have {$templates} template customizations</span></td>
							   <td width='60%' class='tdrow1'>{$html}</td>
							 </tr>
							 </table>
							</fieldset>
							<br />
							<fieldset class='tdfset'>
							 <legend><strong>Macro Replacement Customizations</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Revert all macro customizations?<br /><span style='color:gray'>You have {$macros} macro customizations</span></td>
							   <td width='60%' class='tdrow1'>{$macro}</td>
							 </tr>
							 </table>
							</fieldset>
							<br />
							<fieldset class='tdfset'>
							 <legend><strong>Board Header and Footer Wrapper Customizations</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Revert Board Header and Footer Wrapper?</td>
							   <td width='60%' class='tdrow1'>{$wrapper}</td>
							 </tr>
							 </table>
							</fieldset>
							<br />
							<fieldset class='tdfset'>
							 <legend><strong>Stylesheet (CSS) Customizations</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Revert CSS?</td>
							   <td width='60%' class='tdrow1'>{$css}</td>
							 </tr>
							 </table>
							</fieldset>
							<div style='color:red;text-align:center;font-size:11px;padding:6px'>Please note that all customizations will be lost if set to 'yes'<br /><b>This cannot be undone and there are no more confirmation screens</b></div>
							</div>
							</div>";

		$ibforums->html .= $ibforums->adskin->end_form_standalone("Process");

		//-----------------------------------------
		// Output
		//-----------------------------------------

		$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
		$ibforums->admin->nav[] = array( '' ,'Reverting all customizations in set '.$this_set['set_name'] );

		$ibforums->admin->output();
	}


	//-----------------------------------------
	// REMOVE SKIN SET FORM
	//-----------------------------------------

	function remove_splash()
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_detail = "Please read this page carefully.";
		$ibforums->admin->page_title  = "Removing Skin Set";

		//-----------------------------------------
		// Get this skin set...
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$ibforums->input['id'] ) );

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code', 'doremove'                  ),
																 2 => array( 'act' , 'sets'                      ),
																 3 => array( 'id'  , $ibforums->input['id']      ),
														)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Remove Skin Set {$this_set['set_name']}" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
																"<div style='color:red;font-weight:bold;font-size:11px'>
																PLEASE NOTE: This action cannot be undone</div><br />
																Continuing will permanently remove any customizations to this skin set, including template HTML, CSS, Wrappers and custom macros.
																<br /><br />
																Any children of this skin set will be set as 'root' skins without a parent.
																",
													  )      );

		$ibforums->html .= $ibforums->adskin->end_form("Permanently Remove Set {$this_set['set_name']}");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}

	//-----------------------------------------
	// TOGGLE DEFAULT SKIN
	//-----------------------------------------

	function set_toggle_default()
	{
		global $ibforums, $DB, $std;

		$affected_ids = array();
		$children     = array();
		$message      = array();

		//-----------------------------------------
		// Check for input
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		if ( $ibforums->input['id'] == 1 )
		{
			$ibforums->main_msg = 'You cannot alter the master skin set';
			$this->list_sets();
		}

		//-----------------------------------------
		// Set as default
		//-----------------------------------------

		$DB->do_update( 'skin_sets', array( 'set_default' => 0 ), "" );
		$DB->do_update( 'skin_sets', array( 'set_default' => 1, 'set_hidden' => 0 ), "set_skin_set_id =".$ibforums->input['id'] );

		//-----------------------------------------
		// Rebuild caches and relationships?
		//-----------------------------------------

		$ibforums->cache_func->_rebuild_all_caches( array( $ibforums->input['id'] ) );

		$ibforums->main_msg = 'Skin set to default';
		$this->list_sets();
	}

	//-----------------------------------------
	// TOGGLE VISIBILITY
	//-----------------------------------------

	function set_toggle_visible()
	{
		global $ibforums, $DB, $std;

		$affected_ids = array();
		$children     = array();
		$message      = array();

		//-----------------------------------------
		// Check for input
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		if ( $ibforums->input['id'] == 1 )
		{
			$ibforums->main_msg = 'You cannot alter the master skin set';
			$this->list_sets();
		}

		//-----------------------------------------
		// Get current skin
		//-----------------------------------------

		$skin = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$ibforums->input['id'] ) );

		$hidden = 1;

		if ( $skin['set_hidden'] )
		{
			$hidden = 0;
		}

		$DB->do_update( 'skin_sets', array( 'set_hidden' => $hidden ), 'set_skin_set_id='.$ibforums->input['id'] );

		//-----------------------------------------
		// Rebuild caches and relationships?
		//-----------------------------------------

		$ibforums->cache_func->_rebuild_all_caches( array( $ibforums->input['id'] ) );

		$ibforums->main_msg = 'Skin set visibility changed';
		$this->list_sets();
	}

	//-----------------------------------------
	// DO REMOVE SKIN SET
	//-----------------------------------------

	function do_remove()
	{
		global $ibforums, $DB, $std;

		$affected_ids = array();
		$children     = array();
		$message      = array();

		//-----------------------------------------
		// Check for input
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		if ( $ibforums->input['id'] == 1 )
		{
			$ibforums->main_msg = 'You cannot alter the master skin set';
			$this->list_sets();
		}

		//-----------------------------------------
		// Get this skin set...
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$ibforums->input['id'] ) );

		//-----------------------------------------
		// Can we remove?
		//-----------------------------------------

		if ( $this_set['set_default'] == 1 )
		{
			$ibforums->main_msg = 'IPB cannot remove this skin as it is set as the default skin, please set another skin set to default and try again.';
			$this->list_sets();
		}

		$this_count = $DB->simple_exec_query( array( 'select' => 'count(*) as jazzyjeff', 'from' => 'skin_sets' ) );

		if ( $this_count['jazzyjeff'] == 2 )
		{
			$ibforums->main_msg = 'IPB cannot remove this skin as it is the last editable skin set you currently have.';
			$this->list_sets();
		}

		//-----------------------------------------
		// Get any children
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'set_skin_set_id, set_skin_set_parent', 'from' => 'skin_sets', 'where' => "set_skin_set_parent=".$ibforums->input['id']) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$affected_ids[] = $r['set_skin_set_id'];
			$children[]     = $r['set_skin_set_id'];
		}

		//-----------------------------------------
		// Update children to root
		//-----------------------------------------

		if ( count($children) )
		{
			$DB->do_update( 'skin_sets', array( 'set_skin_set_parent' => '-1' ), 'set_skin_set_id IN ('.implode(',',$children).')' );
		}

		//-----------------------------------------
		// Members using this skin?
		//-----------------------------------------

		$DB->do_update( 'members', array( 'skin' => '' ), 'skin='.$ibforums->input['id'] );

		//-----------------------------------------
		// Delete the skin...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'skin_sets', 'where' => 'set_skin_set_id='.$ibforums->input['id']) );

		//-----------------------------------------
		// Remove macros...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'skin_macro', 'where' => 'macro_set='.$ibforums->input['id']) );

		//-----------------------------------------
		// Remove templates...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => 'set_id='.$ibforums->input['id']) );

		//-----------------------------------------
		// Remove template cache...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'skin_templates_cache', 'where' => 'template_set_id='.$ibforums->input['id']) );

		//-----------------------------------------
		// Remove CSS file...
		//-----------------------------------------

		@unlink( CACHE_PATH.'style_images/css_'.$ibforums->input['id'].'.css' );
		$message[] = 'Clean up: removing CSS cache file...';

		//-----------------------------------------
		// Remove CACHE folder
		//-----------------------------------------

		$ibforums->admin->rm_dir( CACHE_PATH.'skin_cache/cacheid_'.$ibforums->input['id'] );
		$message[] = 'Clean up: removing HTML templates cache folder...';

		//-----------------------------------------
		// Rebuild caches and relationships?
		//-----------------------------------------

		if ( count($affected_ids) )
		{
			$ibforums->cache_func->_rebuild_all_caches($affected_ids);
		}

		$ibforums->cache_func->_rebuild_skin_id_cache();

		$ibforums->main_msg = 'Skin set removed';

		$ibforums->main_msg .= "<br />".implode("<br />", array_merge( $message, $ibforums->cache_func->messages) );
		$this->list_sets();

	}



	//-----------------------------------------
	// ADD / EDIT SKIN SETS
	//-----------------------------------------

	function save_skin( $type='add' )
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Fix up incoming
		//-----------------------------------------

		//img / prt

		if ($type == 'edit')
		{
			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
			}
		}

		if ($ibforums->input['set_name'] == "")
		{
			$ibforums->admin->error("You must specify a name for this skin pack ID");
		}

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id = '.$ibforums->input['id'] ) );

		//-----------------------------------------
		// Init var
		//-----------------------------------------

		$barney = array( 'set_name'            => $std->txt_stripslashes($_POST['set_name']),
						 'set_css_method'      => $ibforums->input['set_css_method'],
						 'set_hidden'          => $ibforums->input['set_hidden'],
						 'set_image_dir'       => $ibforums->input['set_image_dir'],
						 'set_author_email'    => $ibforums->input['set_author_email'],
						 'set_author_url'      => $ibforums->input['set_author_url'],
						 'set_author_name'     => $ibforums->input['set_author_name'],
						 'set_skin_set_parent' => $ibforums->input['set_skin_set_parent'],
						 'set_emoticon_folder' => $ibforums->input['set_emoticon_folder'],
					   );

		if ($type == 'add')
		{


		}
		else
		{
			//-----------------------------------------
			// Did we set it to default?
			//-----------------------------------------

			if ( $ibforums->input['set_default'] )
			{
				$DB->do_update( 'skin_sets', array( 'set_default' => 0 ), 'set_skin_set_id <> '.$ibforums->input['id'] );
				$barney['set_default'] = 1;
			}

			//-----------------------------------------
			// Did the parent change?
			//-----------------------------------------

			$affected_ids = array();

			if ( $ibforums->input['prt'] != $ibforums->input['set_skin_set_parent'] )
			{
				$affected_ids[ $this_set['set_skin_set_id'] ] = $this_set['set_skin_set_id'];

				//-----------------------------------------
				// Any kids?
				//-----------------------------------------

				$children = array();
				$child_id = array();

				$DB->simple_construct( array( 'select' => 'set_skin_set_id', 'from' => 'skin_sets', 'where' => 'set_skin_set_parent='.$this_set['set_skin_set_id'] ) );
				$DB->simple_exec();

				while ( $r = $DB->fetch_row() )
				{
					$children[]      = $r;
					$child_id[]      = $r['set_skin_set_id'];
					$affected_ids[ $r['set_skin_set_id'] ]  = $r['set_skin_set_id'];
				}

				if ( count($children) )
				{
					//-----------------------------------------
					// Move children to direct root children
					//-----------------------------------------

					$DB->simple_exec_query( array( 'update' => 'skin_sets', 'set' => 'set_skin_set_parent = -1', 'where' => 'set_skin_set_id IN ('.implode(",",$child_id).')' ) );
				}
			}

			if ( $ibforums->input['css'] != $ibforums->input['set_css_method'] )
			{
				if ( $ibforums->input['set_css_method'] )
				{
					//-----------------------------------------
					// Caching switched on...
					//-----------------------------------------

					$affected_ids[ $this_set['set_skin_set_id'] ] = $this_set['set_skin_set_id'];
				}
			}

			//-----------------------------------------
			// Img dir changed? recache css
			//-----------------------------------------

			if ( $ibforums->input['img'] != $ibforums->input['set_image_dir'] )
			{
				$affected_ids[ $this_set['set_skin_set_id'] ] = $this_set['set_skin_set_id'];
			}

			$DB->do_update( 'skin_sets', $barney, "set_skin_set_id=".$ibforums->input['id'] );

			//-----------------------------------------
			// Rebuild caches and relationships?
			//-----------------------------------------

			$ibforums->cache_func->_rebuild_all_caches($affected_ids);

			$ibforums->main_msg = 'Skin Settings Updated';

			$ibforums->main_msg .= "<br />".implode("<br />", $ibforums->cache_func->messages);

			$ibforums->admin->done_screen("Skin Set Updated", "Manage Skin Sets", "act=sets", 'redirect' );
		}


	}

	//-----------------------------------------
	// ADD / EDIT SETS
	//-----------------------------------------

	function do_form( $type='add' )
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check for input
		//-----------------------------------------

		$sets     = array();
		$parents  = array( 0=> array( '-1', 'No parent' ) );
		$row      = array();

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing skin set ID, go back and try again");
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$sets[ $r['set_skin_set_id'] ] = $r;

			if ( ($r['set_skin_set_parent'] < 0 and $r['set_skin_set_id'] != 1 ) and ( $ibforums->input['id'] != $r['set_skin_set_id'] ) )
			{
				$parents[] = array( $r['set_skin_set_id'], $r['set_name'] );
			}

			if ( $ibforums->input['id'] == $r['set_skin_set_id'] )
			{
				$row = $r;
			}
		}


		//-----------------------------------------

		if ($type == 'add')
		{
			$code = 'doadd';
			$button = 'Create New Skin Set';
			$row['set_name']    = $row['set_name'];
			$row['set_default'] = 0;
		}
		else
		{
			$code = 'doedit';
			$button = 'Edit Skin Settings';
		}

		//-----------------------------------------
		// Image dir
		//-----------------------------------------

		$dirs = array();

		$dh = opendir( CACHE_PATH.'style_images' );

 		while ( $file = readdir( $dh ) )
 		{
 			if (($file != ".") && ($file != ".."))
 			{
				if ( is_dir(CACHE_PATH.'style_images/'.$file) )
				{
					$dirs[] = array( $file, $file );
				}
 			}
 		}
 		closedir( $dh );

 		//-----------------------------------------
		// Emoticons dir
		//-----------------------------------------

		$emodirs = array();

		$dh = opendir( CACHE_PATH.'style_emoticons' );

 		while ( $file = readdir( $dh ) )
 		{
 			if (($file != ".") && ($file != ".."))
 			{
				if ( is_dir(CACHE_PATH.'style_emoticons/'.$file) )
				{
					$emodirs[] = array( $file, $file );
				}
 			}
 		}
 		closedir( $dh );


		if ( is_writeable( CACHE_PATH."style_images" ) )
		{
			$cssextra = $ibforums->adskin->form_yes_no('set_css_method', $row['set_css_method']);
		}
		else
		{
			$cssextra = "<em>Unavailable, IPB cannot write into your 'style_images' folder</em>";
		}


		//-----------------------------------------

		$ibforums->admin->page_detail = "Please configure the settings below.";
		$ibforums->admin->page_title  = "Manage Skin Sets";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code', $code                       ),
																 2 => array( 'act' , 'sets'                      ),
																 3 => array( 'id'  , $ibforums->input['id']      ),
																 4 => array( 'img' , $row['set_image_dir']       ),
																 5 => array( 'prt' , $row['set_skin_set_parent'] ),
																 6 => array( 'css' , $row['set_css_method']      ),
														), "theAdminForm"    );

		//-----------------------------------------
		// Start output
		//-----------------------------------------

		$ibforums->html .= "<div class='tableborder'>
							<div class='maintitle'>$button</div>
							<div class='tablepad'>
							<fieldset class='tdfset'>
							 <legend><strong>Basics</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Set Title</td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_input('set_name', $row['set_name'])."</td>
							 </tr>
							 <tr>
							   <td width='40%' class='tdrow1'>Hide from members?</td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_yes_no('set_hidden', $row['set_hidden'])."</td>
							 </tr>";

		if ( $row['set_default'] )
		{
			$ibforums->html .= "<tr>
							    <td width='40%' class='tdrow1'>Set as default skin?</td>
							    <td width='60%' class='tdrow1'><i>Skin set as default already.</i></td>";
		}
		else
		{
			$ibforums->html .= "<tr>
							    <td width='40%' class='tdrow1'>Set as default skin?</td>
							    <td width='60%' class='tdrow1'>".$ibforums->adskin->form_checkbox('set_default', $row['set_default'])."</td>";
		}

		$ibforums->html .= "</tr>
							  <tr>
							   <td width='40%' class='tdrow1'>Skin Set Parent?</td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_dropdown('set_skin_set_parent', $parents, $row['set_skin_set_parent'])."</td>
							 </tr>
							 </table>
							</fieldset>
							<br />
							<fieldset class='tdfset'>
							 <legend><strong>CSS Options</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Cache CSS to text files?<br /><span style='color:gray'>This will reduce the size of the HTML as the CSS will be in a browser cachable file.</span>
							   								 </td>
							   <td width='60%' class='tdrow1'>".$cssextra."<br /><span style='color:red'>Warning: Changing this value will re-cache any cached stylesheet information. Please make sure that you have synchronized any cache files with the database.</span></td>
							 </tr>
							 </table>
							</fieldset>
							<br />
							<fieldset class='tdfset'>
							 <legend><strong>Image Options</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Use image directory?<br /><span style='color:gray'>The image directory will be used in place of <#IMG_DIR#> in CSS and macro replacements.</span></td>
							   <td width='60%' class='tdrow1'>style_images/ ".$ibforums->adskin->form_dropdown('set_image_dir', $dirs, $row['set_image_dir'])."</td>
							 </tr>
							  <tr>
							   <td width='40%' class='tdrow1'>Use emoticons set?<br /><span style='color:gray'>Choose which set of emoticons to assign to this skin.</span></td>
							   <td width='60%' class='tdrow1'>style_emoticons/ ".$ibforums->adskin->form_dropdown('set_emoticon_folder', $emodirs, $row['set_emoticon_folder'])."</td>
							 </tr>
							 </table>
							</fieldset>

							<br />
							<fieldset class='tdfset'>
							 <legend><strong>Set Author</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Set Author Name<br /><span style='color:gray'>*Optional</span></td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_input('set_author_name', $row['set_author_name'])."</td>
							 </tr>
							 <tr>
							   <td width='40%' class='tdrow1'>Set Author Email Address<br /><span style='color:gray'>*Optional</span></td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_input('set_author_email', $row['set_author_email'])."</td>
							 </tr>
							 <tr>
							   <td width='40%' class='tdrow1'>Set Author Website Address<br /><span style='color:gray'>*Optional</span></td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_input('set_author_url', $row['set_author_url'])."</td>
							 </tr>
							 </table>
							</fieldset>
							</div>
							</div>";



		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->end_form_standalone($button);

		//-----------------------------------------

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// SHOW ALL SKIN SETS
	//-----------------------------------------

	function list_sets()
	{
		global $ibforums, $DB,  $std;

		$form_array     = array();
		$this_set       = "";
		$forums         = array();
		$forum_skins    = array();
		$macro_array    = array();
		$template_array = array();

		$ibforums->admin->page_detail = "Simply click on the title of the skin set you wish to edit and select from one of the options from the pop-up menu.";
		$ibforums->admin->page_title  = "Manage Skin Sets";

		//-----------------------------------------
		// Get forum names
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, name, skin_id', 'from' => 'forums' ) );
		$DB->simple_exec();

		while ( $f = $DB->fetch_row() )
		{
			$forums[ $f['id'] ] = $f['name'];

			if ( $f['skin_id'] != "")
			{
				$forum_skins[ $f['skin_id'] ][] = $f['name'];
			}
		}

		//-----------------------------------------
		// Get macro / template info
		//-----------------------------------------

		$DB->cache_add_query( 'stylesets_list_sets_templates', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$template_array[ $r['set_id'] ] = 1;
		}

		$DB->cache_add_query( 'stylesets_list_sets_macros', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$macro_array[ $r['macro_set'] ] = 1;
		}


		//-----------------------------------------
		// Start of HTML Output
		//-----------------------------------------

		$ibforums->html .= "<script type='text/javascript'>
							function addnewpop(parentid, menuid)
							{
								if ( menuid )
								{
									togglediv( menuid, 0 );
								}

								document.jsform.id.value = parentid;
								scroll(0,0);
								togglediv( 'popbox', 1 );
								return false;
							}
							</script>
							<div class='tableborder'>
							<div class='maintitle'>
							<div align='center' style='position:absolute;width:99%;display:none;text-align:center' id='popbox'>
							 <form name='jsform' action='{$ibforums->adskin->base_url}' method='post'>
							 <input type='hidden' name='act' value='sets' />
							 <input type='hidden' name='code' value='addset' />
							 <input type='hidden' name='id' value='' />
							 <table cellspacing='0' width='500' align='center' cellpadding='6' style='background:#EEE;border:2px outset #555;'>
							 <tr>
							  <td align='center' valign='top'>
							   <b>New Skin Set Name</b><br><input class='textinput' name='set_name' type='text' size='30' />
							   <br />
							   <input type='checkbox' name='hidden' value='1' /> Make new skin set hidden on creation?
							   <br /><br />
							   <input type='submit' class='realbutton' value='Add New Skin Set' name='submitbutton' /> <input type='button' class='realdarkbutton' value='Close' onclick=\"togglediv('popbox');\" />
							  </td>
							 </tr>
							 </table>
							 </form>
							</div>
							<table cellpadding='0' cellspacing='0' border='0' width='100%'>
							<tr>
							<td align='left' width='100%' style='font-weight:bold;color:white;font-size:12px'>
							Skin Sets
							</td>
							<td align='right' nowrap='nowrap'><input type='button' name='addnew' class='realdarkbutton' value='Add New Skin Set'  onclick=\"addnewpop('-1')\" />&nbsp;</td>
							</tr>
							</table>
							</div></div>
						   ";

		$ibforums->html .= "<div class='tableborder'>\n<div class='tablepad'>\n";

		//-----------------------------------------
		// GET SKINS
		//-----------------------------------------

		$skin_sets  = array();
		$last_id    = 0;
		$default_skin = "";

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'order' => 'set_skin_set_parent, set_skin_set_id ASC' ) );
		$DB->simple_exec();

		$no_sets = 0;
		$i_sets  = 0;

		while ( $row = $DB->fetch_row() )
		{
			$skins[ $row['set_skin_set_id'] ] = $row;

			if ( $row['set_skin_set_parent'] == -1 )
			{
				$no_sets++;
			}
		}

		//-----------------------------------------
		// Loop-de-loop
		//-----------------------------------------

		foreach( $skins as $r )
		{
			$i_sets++;

			$skin_sets[ $r['set_skin_set_id'] ] = $r;

			$skin_sets[ $r['set_skin_set_parent'] ]['_lastid']     = $r['set_skin_set_id'];
			$skin_sets[ $r['set_skin_set_parent'] ]['_children'][] = $r['set_skin_set_id'];

			$extra  = "";
			$forums = "<img src='{$ibforums->skin_url}/skin_notforums.gif' border='0' alt='Not used in forums' title='Not used in forums' />";

			//-----------------------------------------
			// Used in forums?
			//-----------------------------------------

			if ( is_array($forum_skins[ $r['set_skin_set_id'] ]) )
			{
				if ( count($forum_skins[ $r['set_skin_set_id'] ]) > 0 )
				{
					$extra  = "Used in forums:".implode( ",", $forum_skins[ $r['set_skin_set_id'] ] );

					$forums = "<img src='{$ibforums->skin_url}/skin_forums.gif' border='0' alt='' title='$extra' />";
				}
			}

			$this->unaltered    = "<img src='{$ibforums->skin_url}/skin_item_unaltered.gif' border='0' alt='-' title='Unaltered from parent skin set' />&nbsp;";
			$this->altered      = "<img src='{$ibforums->skin_url}/skin_item_altered.gif' border='0' alt='+' title='Altered from parent skin set' />&nbsp;";
			$this->inherited    = "<img src='{$ibforums->skin_url}/skin_item_inherited.gif' border='0' alt='|' title='Inherited from parent skin set' />&nbsp;";

			//-----------------------------------------
			// Default / Hidden?
			//-----------------------------------------

			$div_start    = "<div style='padding-top:1px;padding-bottom:1px;border-bottom:1px solid #DDD;'><img src='{$ibforums->skin_url}/skin_item.gif' alt='' style='vertical-align:middle' />&nbsp;";
			$default      = "<a href='{$ibforums->adskin->base_url}&act=sets&code=toggledefault&id={$r['set_skin_set_id']}' title='Make this skin the default'><img src='{$ibforums->skin_url}/skin_notdefault.gif' border='0' alt='Not Default' /></a>";
			$hidden       = "<a href='{$ibforums->adskin->base_url}&act=sets&code=togglevisible&id={$r['set_skin_set_id']}' title='Toggle visibility'><img src='{$ibforums->skin_url}/skin_visible.gif' border='0' alt='visible' title='Skin not hidden from members' /></a>";
			$folder_icon  = 'skin_folder.gif';
			$menu_text    = "Root Skin";
			$remove_set   = "<a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=sets&code=remove&id={$r['set_skin_set_id']}'>Remove Skin Set...</a>";
			$restore_all  = "";
			$margin_left  = '40';
			$css_extra    = "";
			$newskin      = "";

			//-----------------------------------------
			// Child of master, middle skin
			// or last skin?
			//-----------------------------------------

			if ( $i_sets >= $no_sets )
			{
				$line_image = "<img src='{$ibforums->skin_url}/skin_line_l.gif' border='0' />&nbsp;";
			}
			else
			{
				$line_image = "<img src='{$ibforums->skin_url}/skin_line_t.gif' border='0' />&nbsp;";
			}

			//-----------------------------------------
			// Hidden?
			//-----------------------------------------

			if ($r['set_hidden'] == 1)
			{
				$hidden      = "<a href='{$ibforums->adskin->base_url}&act=sets&code=togglevisible&id={$r['set_skin_set_id']}' title='Toggle visibility'><img src='{$ibforums->skin_url}/skin_invisible.gif' border='0' alt='Invisible' title='Skin hidden from members' /></a>";
				$folder_icon = 'skin_folder_hidden.gif';
				$css_extra   = 'color:#7F7FAA';
			}

			//-----------------------------------------
			// Default?
			//-----------------------------------------

			if ($r['set_default'] == 1)
			{
				$default      = "<img src='{$ibforums->skin_url}/skin_default.gif' border='0' alt='Not Default' title='Default skin' />";
				$default_skin = $r['set_name'];
			}

			//-----------------------------------------
			// IPB Master?
			//-----------------------------------------

			if ( $r['set_skin_set_id'] == 1 )
			{
				$remove_set   = "";
				$folder_icon  = 'skin_folder_master.gif';
				$menu_text    = "Master";
				$margin_left  = '20';
				$line_image   = "";
				$css_extra    = "color:gray";
				$hidden       = "";
				$default      = "";
				$forums       = "";
				$export_button = "";
			}
			else
			{
				//-----------------------------------------
				// Not..
				//-----------------------------------------

				$export_button = $ibforums->adskin->js_make_button( 'Export', "{$ibforums->adskin->base_url}&act=import&code=showexportpage&id={$r['set_skin_set_id']}", "realdarkbutton", "Export skin set" );

				$restore_all   = "<div style='padding-top:1px;padding-bottom:1px;border-bottom:1px solid #DDD;'><img src='{$ibforums->skin_url}/skin_pkg.gif' alt='' style='vertical-align:middle' />&nbsp;<a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=sets&code=revertallform&id={$r['set_skin_set_id']}'>Revert All Skin Customizations...</a></div>";

				if ( $r['set_skin_set_parent'] >= 0 )
				{
					//-----------------------------------------
					// Child though...
					//-----------------------------------------

					if ( $folder_icon != 'skin_folder_hidden.gif' )
					{
						$folder_icon = 'skin_folder_children.gif';
					}

					$menu_text   = "Child's";
					$margin_left = '70';
					$line_image  = "<img src='{$ibforums->skin_url}/skin_line_single.gif' border='0' />";
				}
				else
				{
					$newskin     = "<div style='padding-top:1px;padding-bottom:1px;border-bottom:1px solid #DDD;'><img src='{$ibforums->skin_url}/skin_pkg.gif' alt='' style='vertical-align:middle' />&nbsp;<a style='text-decoration:none;font-weight:bold' href='#' onclick=\"addnewpop('{$r['set_skin_set_id']}','menu_{$r['set_skin_set_id']}')\">Add New Child Skin Set...</a></div>";
				}
			}

			//-----------------------------------------
			// Skin opts
			//-----------------------------------------

			$menulist = "<div id='menu_{$r['set_skin_set_id']}' style='margin-left:{$margin_left}px;display:none;background:#EEE;border:1px solid #555;position:absolute;width:auto;padding:3px 5px 3px 3px;'>";

			if ( $r['set_skin_set_id'] == 1 AND ! IN_DEV )
			{
				$menulist .= "This is the master skin and cannot be edited or removed.<br />If you wish to customize the default skin, please click
							 <br />on the skin set '<!DEFAULT>' and choose from the options.";
			}
			else
			{

				$menulist .= "$div_start<!--ALTERED.wrappper--><a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=wrap&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit $menu_text Board Header & Footer Wrapper</a></div>
							  $div_start<!--ALTERED.templates--><a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=templ&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit $menu_text Template HTML</a></div>
							  $div_start<!--ALTERED.css--><a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=style&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit $menu_text Stylesheet (CSS Advanced Mode)</a></div>
							  $div_start<!--ALTERED.css--><a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=style&code=colouredit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit $menu_text Colours (CSS Easy Mode)</a></div>
							  $div_start<!--ALTERED.macro--><a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=image&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit $menu_text Replacement Macros</a></div>
							  <div style='padding-top:1px;padding-bottom:1px;border-bottom:1px solid #DDD;'><img src='{$ibforums->skin_url}/skin_pkg.gif' alt='' style='vertical-align:middle' />&nbsp;<a style='text-decoration:none;font-weight:bold' href='{$ibforums->adskin->base_url}&act=sets&code=edit&id={$r['set_skin_set_id']}'>Edit Settings...</a></div>
							  {$restore_all}
							  {$newskin}
							  <div style='padding-top:1px;'><img src='{$ibforums->skin_url}/skin_pkg.gif' alt='' style='vertical-align:middle' />&nbsp;{$remove_set}</div>";

			}

			$menulist .= "</div>";

			//-----------------------------------------
			// Add skin row
			//-----------------------------------------

			$skin_sets[ $r['set_skin_set_id'] ]['_html'] = "<div style='padding:4px;border-bottom:1px solid #DDD;'>
															<table width='100%' cellspacing='0' cellpadding='0' border='0'>
															<tr>
															 <td align='left' width='60%'><!--$i_sets,$no_sets-->{$line_image}<!--ID:{$r['set_skin_set_id']}--><img src='{$ibforums->skin_url}/{$folder_icon}' title='Skin Set ID {$r['set_skin_set_id']}' style='vertical-align:middle' />&nbsp;&nbsp;<b><a onclick=\"toggleview('menu_{$r['set_skin_set_id']}'); return false;\" title='Click for skin options' href='#' style='font-size:11px;{$css_extra}'>".$std->txt_stripslashes($r['set_name'])."</a></b>$menulist</td>
															 <td align='right' width='40%'>
															 $forums
															 $hidden
															 $default
															  ". $export_button ."
															</td>
															</tr>
															</table>
															</div>";

			$form_array[] = array( $r['set_skin_set_id'], $r['set_name'] );

		}

		//header("content-type: text/plain"); print_r($skin_sets); exit();

		//-----------------------------------------
		// Show root forums
		//-----------------------------------------

		foreach( $skin_sets as $id => $data )
		{
			if ( $data['set_skin_set_parent'] == -1 )
			{
				$wrapper_icon   = $this->_get_status_of_parent( $data['set_wrapper'] );
				$css_icon       = $this->_get_status_of_parent( $data['set_css'] );
				$templates_icon = $this->_get_status_of_parent( $template_array[ $data['set_skin_set_id'] ] );
				$macro_icon     = $this->_get_status_of_parent( $macro_array[ $data['set_skin_set_id'] ] );

				//-----------------------------------------
				// Fix n' stitch
				//-----------------------------------------

				$data['_html'] = str_replace( '<!--ALTERED.wrappper-->' , $wrapper_icon  , $data['_html'] );
				$data['_html'] = str_replace( '<!--ALTERED.templates-->', $templates_icon, $data['_html'] );
				$data['_html'] = str_replace( '<!--ALTERED.css-->'      , $css_icon      , $data['_html']);
				$data['_html'] = str_replace( '<!--ALTERED.macro-->'    , $macro_icon    , $data['_html'] );

				$ibforums->html .= $data['_html']."\n<!--CHILDREN:{$id}-->";
			}
		}

		//-----------------------------------------
		// Show any children
		//-----------------------------------------

		foreach( $skin_sets as $id => $data )
		{
			if ( is_array( $data['_children'] ) and count( $data['_children'] ) > 0 )
			{
				$html = "";

				foreach( $data['_children'] as $cid )
				{
					$image = "";

					if ( $cid == $data['_lastid'] )
					{
						//-----------------------------------------
						// Last skin, show L
						//-----------------------------------------

						$image = 'skin_line_l.gif';
					}
					else
					{
						//-----------------------------------------
						// First skin, show T
						//-----------------------------------------

						$image = 'skin_line_t.gif';
					}

					$skin_sets[ $cid ]['_html'] = str_replace( "<!--ID:{$cid}-->", "<img src='{$ibforums->skin_url}/{$image}' border='0' />&nbsp;", $skin_sets[ $cid ]['_html'] );

					//-----------------------------------------
					// (un)altered icons:
					//-----------------------------------------

					$wrapper_icon   = $this->_get_status_of_child($skin_sets[ $cid ]['set_wrapper'] , $skin_sets[ $id ]['set_wrapper'] );
					$css_icon       = $this->_get_status_of_child($skin_sets[ $cid ]['set_css']     , $skin_sets[ $id ]['set_css'] );
					$templates_icon = $this->_get_status_of_child($template_array[ $cid ]           , $template_array[ $id ]);
					$macro_icon     = $this->_get_status_of_child($macro_array[ $cid ]              , $macro_array[ $id ]   );

					//-----------------------------------------
					// Fix n' stitch
					//-----------------------------------------

					$skin_sets[ $cid ]['_html'] = str_replace( '<!--ALTERED.wrappper-->' , $wrapper_icon  , $skin_sets[ $cid ]['_html'] );
					$skin_sets[ $cid ]['_html'] = str_replace( '<!--ALTERED.templates-->', $templates_icon, $skin_sets[ $cid ]['_html'] );
					$skin_sets[ $cid ]['_html'] = str_replace( '<!--ALTERED.css-->'      , $css_icon      , $skin_sets[ $cid ]['_html'] );
					$skin_sets[ $cid ]['_html'] = str_replace( '<!--ALTERED.macro-->'    , $macro_icon    , $skin_sets[ $cid ]['_html'] );

					$html .= $skin_sets[ $cid ]['_html'];
				}

				$ibforums->html = str_replace( "<!--CHILDREN:{$id}-->", $html, $ibforums->html );
			}
		}


		$ibforums->html .= "</div>";

		//-----------------------------------------
		// Add in default skin name
		//-----------------------------------------

		$ibforums->html = str_replace( '<!DEFAULT>', $default_skin, $ibforums->html );

		$ibforums->html .= "<div class='pformstrip' style='padding:4px' align='center'>&nbsp;</div></div>";

		$ibforums->html .= "</div>";

		//-----------------------------------------
		// Show altered / unaltered
		// legend
		//-----------------------------------------

		$ibforums->html .= "<br /><div'><strong>Child skin set pop-up menu legend:</strong><br />
							{$this->altered} This item has been customized for this skin set.
							<br />{$this->unaltered} This item has not been customized from the master skin set.
							<br />{$this->inherited} This item has inherited customizations from the parent skin set.
							</div>";


		if ( IN_DEV )
		{
			$ibforums->html .= "<br /><div align='center'>
								DEV: <a href='{$ibforums->base_url}&act=sets&code=exportmaster'>Export Master HTML</a>
								&middot; <a href='{$ibforums->base_url}&act=sets&code=exportbitschoose'>Export Template Bits</a>
								&middot; <a href='{$ibforums->base_url}&act=sets&code=exportmacro'>Export Master Macros</a></div>";
		}

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// Get status of a child
	//-----------------------------------------

	function _get_status_of_child($this_item, $parent_item)
	{
		if ( $this_item )
		{
			return $this->altered;
		}
		else if ( $parent_item )
		{
			return $this->inherited;
		}
		else
		{
			return $this->unaltered;
		}
	}

	//-----------------------------------------
	// Get status of a parent
	//-----------------------------------------

	function _get_status_of_parent($this_item)
	{
		if ( ! $this_item )
		{
			return $this->unaltered;
		}
		else
		{
			return $this->altered;
		}
	}

}


?>