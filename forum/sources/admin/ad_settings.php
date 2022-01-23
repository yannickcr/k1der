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
|   > Admin Setting functions
|   > Module written by Matt Mecham
|   > Date started: 20th March 2002
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

class ad_settings {

	var $base_url;
	var $in_group  = array();
	var $key_array = array();

	function auto_run()
	{
		global $ibforums, $DB,  $std, $forums, $HTTP_POST_VARS;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------

		$DB->sql_get_version();

		$this->true_version  = $DB->true_version;
		$this->mysql_version = $DB->mysql_version;

		switch($ibforums->input['code'])
		{
			case 'settinggroup_resync':
				$this->settinggroup_resync();
				break;

			case 'settinggroup_delete':
				$this->settinggroup_delete();
				break;

			case 'settinggroup_new':
				$this->settinggroup_form('add');
				break;

			case 'settinggroup_showedit':
				$this->settinggroup_form('edit');
				break;

			case 'settinggroup_add':
				$this->settinggroup_save('add');
				break;

			case 'settinggroup_edit':
				$this->settinggroup_save('edit');
				break;

			case 'settingnew':
				$this->setting_form('add');
				break;

			case 'setting_showedit':
				$this->setting_form('edit');
				break;

			case 'setting_add':
				$this->setting_save('add');
				break;

			case 'setting_edit':
				$this->setting_save('edit');
				break;

			case 'setting_view':
				$this->setting_view();
				break;

			case 'setting_delete':
				$this->setting_delete();
				break;

			case 'setting_revert':
				$this->setting_revert();
				break;

			case 'setting_update':
				$this->setting_update();
				break;

			case 'setting_allexport':
				$this->setting_allexport();
				break;

			case 'findsetting':
				$this->setting_findgroup();
				break;

			case 'setting_someexport_start':
				$this->setting_someexport_start();
				break;

			case 'setting_someexport_complete':
				$this->setting_someexport_complete();
				break;

			case 'settings_do_import':
				$this->settings_do_import();
				break;

			//-----------------------------------------
			// Full text
			//-----------------------------------------

			case 'dofulltext':
				$this->do_fulltext();
				break;

			case 'phpinfo':
				phpinfo();
				exit;
			//-----------------------------------------
			default:
				$this->setting_start();
				break;
		}

	}

	//-----------------------------------------
	//
	// IMPORT PARTIAL SETTINGS
	//
	//-----------------------------------------

	function settings_do_import()
	{
		global $ibforums, $DB, $std;

		$updated     = 0;
		$inserted    = 0;
		$need_update = array();

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			//-----------------------------------------
			// check and load from server
			//-----------------------------------------

			if ( ! $ibforums->input['file_location'] )
			{
				$ibforums->main_msg = "No upload file was found and no filename was specified.";
				$this->import();
			}

			if ( ! file_exists( ROOT_PATH . $ibforums->input['file_location'] ) )
			{
				$ibforums->main_msg = "Could not find the file to open at: " . ROOT_PATH . $ibforums->input['file_location'];
				$this->import();
			}

			if ( preg_match( "#\.gz$#", $ibforums->input['file_location'] ) )
			{
				if ( $FH = @gzopen( ROOT_PATH.$ibforums->input['file_location'], 'rb' ) )
				{
					while ( ! @gzeof( $FH ) )
					{
						$content .= @gzread( $FH, 1024 );
					}

					@gzclose( $FH );
				}
			}
			else
			{
				if ( $FH = @fopen( ROOT_PATH.$ibforums->input['file_location'], 'rb' ) )
				{
					$content = @fread( $FH, filesize(ROOT_PATH.$ibforums->input['lang_location']) );
					@fclose( $FH );
				}
			}
		}
		else
		{
			//-----------------------------------------
			// Get uploaded schtuff
			//-----------------------------------------

			$tmp_name = $_FILES['FILE_UPLOAD']['name'];
			$tmp_name = preg_replace( "#\.gz$#", "", $tmp_name );

			$content  = $ibforums->admin->import_xml( $tmp_name );
		}

		//-----------------------------------------
		// Get current settings.
		//-----------------------------------------

		$cur_settings = array();

		$DB->simple_construct( array( 'select' => 'conf_id, conf_key',
									  'from'   => 'conf_settings',
									  'order'  => 'conf_id' ) );

		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$cur_settings[ $r['conf_key'] ] = $r['conf_id'];
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $content );

		//-----------------------------------------
		// pArse
		//-----------------------------------------

		$fields = array( 'conf_title'   , 'conf_description', 'conf_group'    , 'conf_type'    , 'conf_key'        , 'conf_default',
						 'conf_extra'   , 'conf_evalphp'    , 'conf_protected', 'conf_position', 'conf_start_group', 'conf_end_group',
						 'conf_help_key', 'conf_add_cache' );

		if ( ! is_array( $xml->xml_array['settingexport']['settinggroup']['setting'][0]  ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $xml->xml_array['settingexport']['settinggroup']['setting'];

			unset($xml->xml_array['settingexport']['settinggroup']['setting']);

			$xml->xml_array['settingexport']['settinggroup']['setting'][0] = $tmp;
		}

		foreach( $xml->xml_array['settingexport']['settinggroup']['setting'] as $id => $entry )
		{
			$newrow = array();

			//-----------------------------------------
			// Make PHP slashes safe
			//-----------------------------------------

			$entry['conf_evalphp']['VALUE'] = str_replace( '\\', '\\\\', $entry['conf_evalphp']['VALUE'] );

			foreach( $fields as $f )
			{
				$newrow[$f] = $entry[ $f ]['VALUE'];
			}

			if ( $cur_settings[ $entry['conf_key']['VALUE'] ] )
			{
				//-----------------------------------------
				// Update
				//-----------------------------------------

				$DB->do_update( 'conf_settings', $newrow, 'conf_id='.$cur_settings[ $entry['conf_key']['VALUE'] ] );
				$updated++;
			}
			else
			{
				//-----------------------------------------
				// INSERT
				//-----------------------------------------

				$DB->do_insert( 'conf_settings', $newrow );
				$inserted++;
			}

			$need_update[ $entry['conf_group']['VALUE'] ] = $entry['conf_group']['VALUE'];
		}

		//-----------------------------------------
		// Update group counts...
		//-----------------------------------------

		if ( count( $need_update ) )
		{
			foreach( $need_update as $i => $idx )
			{
				$conf = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'conf_settings', 'where' => 'conf_group='.$idx ) );

				$count = intval($conf['count']);

				$DB->do_update( 'conf_settings_titles', array( 'conf_title_count' => $count ), 'conf_title_id='.$idx );
			}
		}

		//-----------------------------------------
		// Resync
		//-----------------------------------------

		$this->setting_rebuildcache();

		$ibforums->main_msg = "$updated settings updated $inserted settings inserted";

		$this->setting_start();
	}

	//-----------------------------------------
	//
	// EXPORT Some Settings. DO IT NOW YES
	//
	//-----------------------------------------

	function setting_someexport_complete()
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
			$ibforums->main_msg = "You must select SOME settings to export!";
			$this->setting_someexport_start();
		}

		//-----------------------------------------
		// Get XML class
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		$xml->xml_set_root( 'settingexport', array( 'exported' => time() ) );

		//-----------------------------------------
		// Get groups
		//-----------------------------------------

		$xml->xml_add_group( 'settinggroup' );

		$this->setting_get_groups();

		$entry = array();

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'conf_settings',
									  'where'  => "conf_id IN (".implode(",",$ids).")",
									  'order'  => 'conf_position, conf_title' ) );

		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$content = array();

			$r['conf_value'] = '';

			foreach( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}

			$entry[] = $xml->xml_build_entry( 'setting', $content );
		}

		$xml->xml_add_entry_to_group( 'settinggroup', $entry );

		$xml->xml_format_document();

		$doc = $xml->xml_document;

		//-----------------------------------------
		// Print to browser
		//-----------------------------------------

		$ibforums->admin->show_download( $doc, 'ipb_settings_partial.xml', '', 0 );
	}

	//-----------------------------------------
	//
	// EXPORT Some Settings. Co-co-ca-choo
	//
	//-----------------------------------------

	function setting_someexport_start()
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_title  = "Export Selected System Settings";
		$ibforums->admin->page_detail = "Check the box of the setting you wish to export.";

		//-----------------------------------------
		// start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'setting_someexport_complete' ),
															     2 => array( 'act'   , 'op'      ),
													    )      );

		$ibforums->html .= "<div class='tableborder'>
							 <div class='maintitle'>System Settings</div>
							 <table width='100%' cellspacing='1' cellpadding='4' border='0'>";

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'order' => 'conf_id' ) );
		$DB->simple_exec();

		$per_row  = 3;
		$td_width = 100 / $per_row;
		$count    = 0;
		$output   = "<tr align='center'>\n";

		while ( $r = $DB->fetch_row() )
		{
			$count++;

			$class = $count == 2 ? 'tdrow2' : 'tdrow1';

			$output .= "<td width='{$td_width}%' align='left' class='$class'>
						 <input type='checkbox' style='checkbox' value='1' name='id_{$r['conf_id']}' /> <strong>{$r['conf_key']}</strong> - {$r['conf_id']}
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


		$ibforums->html .= $output;

		$ibforums->html .= "</table>
						    <div class='pformstrip' align='center'><input type='submit' class='realbutton' value='EXPORT SELECTED' /></form></div></div>";

		$ibforums->admin->output();

	}

	//-----------------------------------------
	//
	// Find setting group (don't rely on IDs)
	//
	//-----------------------------------------

	function setting_findgroup()
	{
		global $ibforums, $DB, $std;

		if ( ! $ibforums->input['key'] )
		{
			$this->setting_start();
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings_titles' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			if ( strtolower( str_replace( " ", "", trim($r['conf_title_title']) ) ) == urldecode(trim($ibforums->input['key'])) )
			{
				$std->boink_it( $ibforums->base_url.'&act=op&code=setting_view&conf_group='.$r['conf_title_id'] );
				break;
			}
		}

		$this->setting_start();
	}

	//-----------------------------------------
	//
	// Export all to to XML (DEV ONLY)
	//
	//-----------------------------------------

	function setting_allexport()
	{
		global $ibforums, $DB, $std;

		if ( ! IN_DEV )
		{
			$this->setting_start();
		}

		//-----------------------------------------
		// Get XML class
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		$xml->xml_set_root( 'settingexport', array( 'exported' => time() ) );

		//-----------------------------------------
		// Get groups
		//-----------------------------------------

		$xml->xml_add_group( 'settinggroup' );

		$this->setting_get_groups();

		$entry = array();

		foreach( $this->setting_groups as $i => $r )
		{
			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'conf_settings',
										  'where'  => "conf_group='{$r['conf_title_id']}'",
										  'order'  => 'conf_position, conf_title' ) );

			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$content = array();

				$r['conf_value'] = '';

				foreach( $r as $k => $v )
				{
					$content[] = $xml->xml_build_simple_tag( $k, $v );
				}

				$entry[] = $xml->xml_build_entry( 'setting', $content );
			}
		}

		$xml->xml_add_entry_to_group( 'settinggroup', $entry );

		$xml->xml_format_document();

		$doc = $xml->xml_document;

		//-----------------------------------------
		// Print to browser
		//-----------------------------------------

		$ibforums->admin->show_download( $doc, 'ipb_settings.xml', '', 0 );
	}

	//-----------------------------------------
	//
	// Delete setting group
	//
	//-----------------------------------------

	function settinggroup_delete()
	{
		global $ibforums, $DB, $std;

		if ( $ibforums->input['id'] )
		{
			$conf = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'conf_settings', 'where' => 'conf_group='.$ibforums->input['id'] ) );

			$count = intval($conf['count']);

			if ( $count > 0 )
			{
				$ibforums->main_msg = "Cannot remove this setting group as it still contains active settings";
			}
			else
			{
				$DB->simple_exec_query( array( 'delete' => 'conf_settings_titles', 'where' => 'conf_title_id='.$ibforums->input['id'] ) );

				$ibforums->main_msg = "Setting Group Removed";
			}

		}

		$this->setting_start();
	}

	//-----------------------------------------
	//
	// Recount settings
	//
	//-----------------------------------------

	function settinggroup_resync()
	{
		global $ibforums, $DB, $std;

		if ( $ibforums->input['id'] )
		{
			$conf = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'conf_settings', 'where' => 'conf_group='.$ibforums->input['id'] ) );

			$count = intval($conf['count']);

			$DB->do_update( 'conf_settings_titles', array( 'conf_title_count' => $count ), 'conf_title_id='.$ibforums->input['id'] );
		}

		$this->setting_start();
	}

	//-----------------------------------------
	//
	// New setting group form
	//
	//-----------------------------------------

	function settinggroup_form( $type='add' )
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_title  = "System Configuration Settings";
		$ibforums->admin->page_detail = "This section contains all the configuration options for your IPB.";

		if ( $type == 'add' )
		{
			$formcode = 'settinggroup_add';
			$title    = "Create New Board Setting Group";
			$button   = "Create New Setting Group";
		}
		else
		{
			$conf = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings_titles', 'where' => 'conf_title_id='.$ibforums->input['id'] ) );

			if ( ! $conf['conf_title_id'] )
			{
				$ibforums->main_msg = "No ID was passed, please try again.";
				$this->setting_start();
			}

			$formcode = 'settinggroup_edit';
			$title    = "Edit Setting ".$conf['conf_title'];
			$button   = "Save Changes";
		}

		$ibforums->admin->page_detail = '&nbsp;';
		$ibforums->admin->page_title  = $title;

		//-----------------------------------------
		// start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $formcode ),
															     2 => array( 'act'   , 'op'      ),
															     3 => array( 'id'    , $ibforums->input['id'] ),
													    )      );

		//-----------------------------------------
		// start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "60%" );

		//-----------------------------------------
		// um..
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( $title );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Group title?</b>" ,
												  			     $ibforums->adskin->form_input( 'conf_title_title', $_POST['conf_title_title'] ? $_POST['conf_title_title'] : $conf['conf_title_title'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Group Description?</b>" ,
												  			     $ibforums->adskin->form_textarea( 'conf_title_desc', $_POST['conf_title_desc'] ? $_POST['conf_title_desc'] : $conf['conf_title_desc'] )
										 		    	)      );

		//-----------------------------------------
		// er....
		//-----------------------------------------

		if ( IN_DEV )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Group Keyword?</b><div class='graytext'>Used to pull this from the DB without relying on an ID</div>" ,
												  			         $ibforums->adskin->form_input( 'conf_title_keyword', $_POST['conf_title_keyword'] ? $_POST['conf_title_keyword'] : $conf['conf_title_keyword'] )
										 		    	    )      );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Hide from main settings list?</b>" ,
												  			         $ibforums->adskin->form_yes_no( 'conf_title_noshow', $_POST['conf_title_noshow'] ? $_POST['conf_title_noshow'] : $conf['conf_title_noshow'] )
										 		    	    )      );
		}


		$ibforums->html .= $ibforums->adskin->end_form( $button );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// Settings Group Save Form
	//-----------------------------------------

	function settinggroup_save($type='add')
	{
		global $ibforums, $DB, $std;

		if ( $type == 'edit' )
		{
			if ( ! $ibforums->input['id'] )
			{
				$ibforums->main_msg = "No ID was passed, please try again";
				$this->setting_form();
			}
		}

		//-----------------------------------------
		// check...
		//-----------------------------------------

		$array = array( 'conf_title_title'   => $ibforums->input['conf_title_title'],
						'conf_title_desc'    => $std->txt_safeslashes( $_POST['conf_title_desc'] ),
						'conf_title_keyword' => $std->txt_safeslashes( $_POST['conf_title_keyword'] ),
						'conf_title_noshow'  => $ibforums->input['conf_title_noshow'],
					 );


		if ( $type == 'add' )
		{
			$DB->do_insert( 'conf_settings_titles', $array );
			$ibforums->main_msg = 'New Setting Group Added';
		}
		else
		{
			$DB->do_update( 'conf_settings_titles', $array, 'conf_title_id='.$ibforums->input['id'] );
			$ibforums->main_msg = 'Setting Group Edited';
		}

		$this->setting_rebuildcache();

		$this->setting_start();

	}

	//-----------------------------------------
	//
	// New setting form
	//
	//-----------------------------------------

	function setting_form( $type='add' )
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_title  = "System Configuration Settings";
		$ibforums->admin->page_detail = "This section contains all the configuration options for your IPB.";

		if ( $type == 'add' )
		{
			$formcode = 'setting_add';
			$title    = "Create New Board Setting";
			$button   = "Create New Setting";
			$conf     = array( 'conf_group' => $ibforums->input['conf_group'], 'conf_add_cache' => 1 );

			if ( IN_DEV )
			{
				$conf['conf_protected'] = 1;
			}

			if ( $ibforums->input['conf_group'] )
			{
				$max = $DB->simple_exec_query( array( 'select' => 'max(conf_position) as max', 'from' => 'conf_settings', 'where' => 'conf_group='.$ibforums->input['conf_group'] ) );
			}
			else
			{
				$max = $DB->simple_exec_query( array( 'select' => 'max(conf_position) as max', 'from' => 'conf_settings' ) );
			}

			$conf['conf_position'] = $max['max'] + 1;
		}
		else
		{
			$conf = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_id='.$ibforums->input['id'] ) );

			if ( ! $conf['conf_id'] )
			{
				$ibforums->main_msg = "No ID was passed, please try again.";
				$this->setting_start();
			}

			$formcode = 'setting_edit';
			$title    = "Edit Setting ".$conf['conf_title'];
			$button   = "Save Changes";
		}

		$ibforums->admin->page_detail = '&nbsp;';
		$ibforums->admin->page_title  = $title;

		//-----------------------------------------
		// Get groups
		//-----------------------------------------

		$this->setting_get_groups();

		$groups = array();

		foreach( $this->setting_groups as $i => $r )
		{
			$groups[] = array( $r['conf_title_id'], $r['conf_title_title'] );
		}

		//-----------------------------------------
		// Type
		//-----------------------------------------

		$types = array( 0 => array( 'input'   , 'Text Input' ),
						1 => array( 'dropdown', 'Drop Down'  ),
						2 => array( 'yes_no'  , 'Yes/No Radio Buttons'),
						3 => array( 'textarea', 'Textarea'   ),
						4 => array( 'multi'   , 'Multi Select' ),
					 );

		//-----------------------------------------
		// start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $formcode ),
																 2 => array( 'act'   , 'op'      ),
																 3 => array( 'id' , $ibforums->input['id'] ),
														)      );

		//-----------------------------------------
		// start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "60%" );

		//-----------------------------------------
		// um..
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( $title );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting title?</b>" ,
												  			   $ibforums->adskin->form_input( 'conf_title', $_POST['conf_title'] ? $_POST['conf_title'] : $conf['conf_title'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Position?</b>" ,
												  			   $ibforums->adskin->form_input( 'conf_position', $_POST['conf_position'] ? $_POST['conf_position'] : $conf['conf_position'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Description?</b>" ,
												  			   $ibforums->adskin->form_textarea( 'conf_description', $_POST['conf_description'] ? $_POST['conf_description'] : $conf['conf_description'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Group?</b>" ,
												  			   $ibforums->adskin->form_dropdown( 'conf_group', $groups, $_POST['conf_group'] ? $_POST['conf_group'] : $conf['conf_group'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Type?</b>" ,
												  			   $ibforums->adskin->form_dropdown( 'conf_type', $types, $_POST['conf_type'] ? $_POST['conf_type'] : $conf['conf_type'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Key?</b>" ,
												  			   $ibforums->adskin->form_input( 'conf_key', $_POST['conf_key'] ? $_POST['conf_key'] : $conf['conf_key'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Current Value?</b>" ,
												  			   $ibforums->adskin->form_textarea( 'conf_value', $_POST['conf_value'] ? $_POST['conf_value'] : $conf['conf_value'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Default Value?</b>" ,
												  			   $ibforums->adskin->form_textarea( 'conf_default', $_POST['conf_default'] ? $_POST['conf_default'] : $conf['conf_default'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Setting Extra?</b><div style='color:gray'>Use for creating form element extras.<br />Drop down box use: Key=Value; one per line.</div>" ,
												  			   $ibforums->adskin->form_textarea( 'conf_extra', $_POST['conf_extra'] ? $_POST['conf_extra'] : $conf['conf_extra'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Raw PHP code to eval before showing and saving?</b><div style='color:gray'>\$show = 1; is set when showing setting.<br />\$save = 1; is set when saving the setting.<br />Use \$key and \$value when writing PHP code.</div>" ,
												  			   $ibforums->adskin->form_textarea( 'conf_evalphp', $_POST['conf_evalphp'] ? $_POST['conf_evalphp'] : $conf['conf_evalphp'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Start setting group?</b><div style='color:gray'>Enter title here or leave blank to not start a setting group</div>" ,
												  			   $ibforums->adskin->form_input( 'conf_start_group', $_POST['conf_start_group'] ? $_POST['conf_start_group'] : $conf['conf_start_group'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>End setting group?</b><div style='color:gray'>End an opened setting group</div>" ,
												  			   $ibforums->adskin->form_yes_no( 'conf_end_group', $_POST['conf_end_group'] ? $_POST['conf_end_group'] : $conf['conf_end_group'] )
										 		    	)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Optional Help Key</b><div style='color:gray'>'Key' which links to the ACP help system</div>" ,
												  			   $ibforums->adskin->form_input( 'conf_help_key', $_POST['conf_help_key'] ? $_POST['conf_help_key'] : $conf['conf_help_key'] )
										 		    	)      );

		if ( IN_DEV )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Make a default settings (cannot be removed by user)?</b>" ,
												  			       $ibforums->adskin->form_yes_no( 'conf_protected', $_POST['conf_protected'] ? $_POST['conf_protected'] : $conf['conf_protected'] )
										 		    	  )      );
		}

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Add this option into the settings cache?</b>" ,
																  $ibforums->adskin->form_yes_no( 'conf_add_cache', $_POST['conf_add_cache'] ? $_POST['conf_add_cache'] : $conf['conf_add_cache'] )
														 )      );

		$ibforums->html .= $ibforums->adskin->end_form( $button );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// Settings View
	//-----------------------------------------

	function setting_view()
	{
		global $ibforums, $DB, $std, $forums;

		$ibforums->admin->page_title  = "System Configuration Settings";
		$ibforums->admin->page_detail = "This section contains all the configuration options for your IPB.<br />If you wish to leave an entry blank, please use the keyword: <b>{blank}</b> or enter a zero: <b>0</b>.";

		$ibforums->admin->nav[] = array( 'act=op', "View General Settings</a>" );

		$ibforums->input['search'] = trim(urldecode($ibforums->input['search']));

		if ( ! $ibforums->input['conf_group'] and ! $ibforums->input['search'] )
		{
			$ibforums->main_msg = "No group was passed, please try again.";
			$this->setting_start();
		}

		$this->setting_get_groups();

		//-----------------------------------------
		// start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'       , 'setting_update'               ),
																 2 => array( 'act'        , 'op'                           ),
																 3 => array( 'id'         , $ibforums->input['conf_group'] ),
																 4 => array( 'search'     , $ibforums->input['search']     ),
														)      );

		//-----------------------------------------
		// Get settings in group
		//-----------------------------------------

		$start = intval( $ibforums->input['st'] );
		$end   = 50;

		$pages = $std->build_pagelinks( array( 'TOTAL_POSS'  => $this->setting_groups[$ibforums->input['conf_group']]['conf_title_count'],
											   'PER_PAGE'    => $end,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "",
											   'L_MULTI'     => $un_all."Multi Page",
											   'BASE_URL'    => $ibforums->adskin->base_url."&act=op&code=setting_view&search={$ibforums->input['search']}",
											   'search'      => $ibforums->input['search'],
											 )
									  );

		$conf_titles  = array();
		$in_group     = array();
		$last_conf_id = -1;

		//-----------------------------------------
		// Did we search?
		//-----------------------------------------

		if ( $ibforums->input['search'] )
		{
			$keywords = strtolower($ibforums->input['search']);

			$DB->cache_add_query( 'settings_search', array( 'keywords' => $keywords, 'limit_a' => $start, 'limit_b' => $end ) );
    		$DB->cache_exec_query();

			while ( $r = $DB->fetch_row() )
			{
				if ( $r['conf_title_noshow'] == 1 )
				{
					continue;
				}

				$r['conf_start_group']       = "";
				$r['conf_end_group']         = "";
				$conf_entry[ $r['conf_id'] ] = $r;
			}

			if ( ! count( $conf_entry ) )
			{
				$ibforums->main_msg = "Your search for '$keywords' produced no matches.";
				$this->setting_start();
			}

			$title = "Searched for: ".$keywords;
		}

		//-----------------------------------------
		// Or not...
		//-----------------------------------------

		else
		{
			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'conf_settings',
										  'where'  => "conf_group='{$ibforums->input['conf_group']}'",
										  'order'  => 'conf_position, conf_title',
										  'limit'  => array( $start,$end ) ) );

			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$conf_entry[ $r['conf_id'] ] = $r;

				if ( $r['conf_end_group'] )
				{
					$in_g = 0;
				}

				if ( $in_g )
				{
					$this->in_group[] = $r['conf_id'];
				}

				if ( $r['conf_start_group'] )
				{
					$in_g = 1;
				}
			}

			$title = "Settings for group: {$this->setting_groups[$ibforums->input['conf_group']]['conf_title_title']}";
		}

		$key_array = array();

		//-----------------------------------------
		// start table
		//-----------------------------------------

		$ibforums->html .=  "<div class='tableborder'>
							   <div class='maintitle'>
							   <table cellpadding='0' cellspacing='0' border='0' width='100%'>
							   <tr>
								<td align='left' width='70%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>$title</td>
								<td align='right' nowrap='nowrap' width='30%'>";

		if ( ! $ibforums->input['search'] )
		{
			$ibforums->html .=  $ibforums->adskin->js_make_button("Add New Setting"  , $ibforums->base_url."&act=op&code=settingnew&conf_group=".$ibforums->input['conf_group']).'&nbsp;';
			$ibforums->html .=  "<input type='submit' name='reorder' value='Reorder' class='realdarkbutton' />";
		}

		$ibforums->html .= "&nbsp;&nbsp;</td>
						   </tr>
						   </table>
						   </div>
						   ";

		if ( is_array( $conf_entry ) and count( $conf_entry ) )
		{
			foreach( $conf_entry as $id => $r )
			{
				$ibforums->html .= $this->_setting_process_entry( $r );
			}
		}

		$ibforums->html .= "<input type='hidden' name='settings_save' value='".implode(",",$this->key_array)."' />";

		$ibforums->html .= "<div class='pformstrip' align='center'><input type='submit' value='Update Settings' class='realdarkbutton' /></div></div></form>";

		$ibforums->html .= "<br /><br /><div align='right'><b><em>Settings Quick Jump</em></b>".$this->setting_make_dropdown()."</div>";
		$ibforums->admin->output();


	}

	//-----------------------------------------
	// Settings show - core routine...
	//-----------------------------------------

	function _setting_process_entry($r)
	{
		global $ibforums, $DB, $std, $forums;

		$form_element  = "";
		$dropdown      = array();
		$start         = "";
		$end           = "";
		$revert_button = "";
		$tdrow1        = "tdrow1";
		$tdrow2        = "tdrow2";

		$key   = $r['conf_key'];
		$value = $r['conf_value'] != "" ? $r['conf_value'] : $r['conf_default'];

		$show  = 1;

		//-----------------------------------------
		// Default?
		//-----------------------------------------

		$css = "";

		if ( $r['conf_value'] != "" and ( $r['conf_value'] != $r['conf_default'] ) )
		{
			$tdrow1        = "tdrow1shaded";
			$tdrow2        = "tdrow2shaded";
			$revert_button = "<div style='width:auto;float:right;padding-top:2px;'><a href='{$ibforums->base_url}&act=op&code=setting_revert&id={$r['conf_id']}&conf_group={$r['conf_group']}&search={$ibforums->input['search']}' title='Revert to default value'><img src='{$ibforums->skin_url}/te_revert.gif' alt='X' border='0' /></a></div>";
		}

		//-----------------------------------------
		// Evil eval
		//-----------------------------------------

		if ( $r['conf_evalphp'] )
		{
			$show = 1;
			eval( $r['conf_evalphp'] );
		}

		switch( $r['conf_type'] )
		{
			case 'input':
				$form_element = $ibforums->adskin->form_input( $key, str_replace( "'", "&#39;", $value ), 30 );
				break;

			case 'textarea':
				$form_element = $ibforums->adskin->form_textarea( $key, $value, 45, 5 );
				break;

			case 'yes_no':
				$form_element = $ibforums->adskin->form_yes_no( $key, $value );
				break;

			default:

				if ( $r['conf_extra'] )
				{
					if ( $r['conf_extra'] == '#show_forums#' )
					{
						//-----------------------------------------
						// Require the library
						// (Not a building with books)
						//-----------------------------------------

						$forums->forums_init();

						require_once( ROOT_PATH.'sources/admin/admin_forum_functions.php' );

						$aff = new admin_forum_functions();

						$dropdown = $aff->ad_forums_forum_list(1);
					}
					else if ( $r['conf_extra'] == '#show_groups#' )
					{
						$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups' ) );
						$DB->simple_exec();

						while( $row = $DB->fetch_row() )
						{
							$dropdown[] = array( $row['g_id'], $row['g_title'] );
						}
					}
					else if ( $r['conf_extra'] == '#show_skins#' )
					{
						$dropdown = $ibforums->admin->skin_get_skin_dropdown();
					}
					else
					{
						foreach( explode( "\n", $r['conf_extra'] ) as $l )
						{
							list ($k, $v) = explode( "=", $l );
							if ( $k != "" and $v != "" )
							{
								$dropdown[] = array( trim($k), trim($v) );
							}
						}
					}
				}

				if ( $r['conf_type'] == 'dropdown' )
				{
					$form_element = $ibforums->adskin->form_dropdown( $key, $dropdown, $value );
				}
				else
				{
					$form_element = $ibforums->adskin->form_multiselect( $key, $dropdown, explode( ",", $value ), 5 );
				}

				break;
		}

		$delete  = "&#0124; <a href='{$ibforums->base_url}&act=op&code=setting_delete&id={$r['conf_id']}' title='key: {$r['conf_key']}'>Delete</a>";
		$edit    = "<a href='{$ibforums->base_url}&act=op&code=setting_showedit&id={$r['conf_id']}' title='id: {$r['conf_id']}'>Edit</a>";
		$reorder = 1;

		if ( $r['conf_protected'] and ! IN_DEV )
		{
			$delete  = "";
			$edit    = "";
			$reorder = 0;
		}

		if ( $r['conf_start_group'] )
		{
			$start  = "<div style='background-color:#EEF2F7;padding:5px'>
						<div class='tableborder'>
						<div class='pformstrip'>{$r['conf_start_group']}</div>";
		}
		else
		{
			if ( ! in_array( $r['conf_id'], $this->in_group ) and ! $r['conf_end_group'] )
			{
				$start  = "<div style='background-color:#EEF2F7;padding:5px'>
							<div style='border:1px solid #8394B2'>
							";
			}
		}

		if ( $r['conf_end_group'] )
		{
			$end = "</div></div>";
		}
		else
		{
			if ( ! in_array( $r['conf_id'], $this->in_group ) and ! $r['conf_start_group'] )
			{
				$end  = "</div></div>";
			}
		}

		//-----------------------------------------
		// Search hi-lite
		//-----------------------------------------

		if ( $ibforums->input['search'] )
		{
			$r['conf_title']       = preg_replace( "/(".$ibforums->input['search'].")/i", "<span style='background:#FCFDD7'>\\1</span>", $r['conf_title'] );
			$r['conf_description'] = preg_replace( "/(".$ibforums->input['search'].")/i", "<span style='background:#FCFDD7'>\\1</span>", $r['conf_description'] );
		}

		$html .= "$start
							<table cellpadding='5' cellspacing='0' border='0' width='100%'>
							 <tr>
							 <td width='40%' class='$tdrow1'><b>{$r['conf_title']}</b><div style='color:gray'>{$r['conf_description']}</div></td>
							 <td width='45%' class='$tdrow2'>{$revert_button}<div align='left' style='width:auto;'>{$form_element}</div></td>
							 ";

		if ( $edit or $delete )
		{
			$html .= "<td width='10%' class='$tdrow1' align='center'>
								   {$edit}
								   {$delete}
								</td>";
		}

		if ( ! $ibforums->input['search'] and $reorder )
		{
			$html .= "<td width='5%' class='$tdrow2' align='center'><input type='text' size='2' name='cp_{$r['conf_id']}' value='{$r['conf_position']}' class='realdarkbutton' /></td>";
		}

		$html .= "</tr>
				  </table>
				  $end
				 ";

		$this->key_array[] = preg_replace( "/\[\]$/", "", $key );

		return $html;
	}

	//-----------------------------------------
	// Settings Start
	//-----------------------------------------

	function setting_start()
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_title  = "System Configuration Settings";
		$ibforums->admin->page_detail = "This section contains all the configuration options for your IPB.";

		//-----------------------------------------
		// start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "{none}"  , "2%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "88%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "10%" );

		$basic_title = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						<tr>
						 <td align='left' width='40%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>System Settings</td>
						 <td align='right' width='60%'><form method='post' action='{$ibforums->base_url}&act=op&code=setting_view'><input type='text' size='25' onclick='this.value=\"\"' value='Search Settings...' name='search' class='realbutton' />&nbsp;<input type='submit' class='realdarkbutton' value='Go' /></form>"
						 ."&nbsp;&nbsp;</td>
						</tr>
						</table>";

		$ibforums->html .= $ibforums->adskin->start_table($basic_title);

		//-----------------------------------------
		// Get groups
		//-----------------------------------------

		$this->setting_get_groups();

		foreach( $this->setting_groups as $i => $r )
		{
			if ( $r['conf_title_noshow'] )
			{
				$hidden = ' (Hidden)';
			}
			else
			{
				$hidden = '';
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<div align='center'><img src='{$ibforums->adskin->img_url}/settings_folder.gif' border='0' alt='Folder' /></div>",
																   "<a href='{$ibforums->base_url}&act=op&code=setting_view&conf_group={$r['conf_title_id']}'><b>{$r['conf_title_title']}</b></a>$hidden <span style='color:gray'>(".intval($r['conf_title_count'])." settings)</span><div style='color:gray'>{$r['conf_title_desc']}</div>" ,
												  				   array("<div align='center' style='white-space:nowrap'>
												  				          <a href='{$ibforums->base_url}&act=op&code=settinggroup_showedit&id={$r['conf_title_id']}' title='Edit this setting groups details'><img src='{$ibforums->adskin->img_url}/acp_edit.gif' border='0' alt='Edit'  /></a>
																          <a href='{$ibforums->base_url}&act=op&code=settinggroup_delete&id={$r['conf_title_id']}' title='Delete this setting group'><img src='{$ibforums->adskin->img_url}/acp_delete.gif' border='0' alt='Delete'  /></a>
																          <a href='{$ibforums->base_url}&act=op&code=settinggroup_resync&id={$r['conf_title_id']}' title='Recount this setting groups options'><img src='{$ibforums->adskin->img_url}/acp_resync.gif' border='0' alt='Recount'  /></a>
																          </div>", 1, 'tdrow3' )
										 					  )      );
		}

		$ibforums->html .= $ibforums->adskin->add_td_row( array( array("<div align='center' style='white-space:nowrap'>".
																	  $ibforums->adskin->js_make_button("Add Setting Group", $ibforums->base_url."&act=op&code=settinggroup_new")."</div>", 3, 'pformstrip' )
										 					  )      );

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		// Import partial settings?
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'          , 'settings_do_import'    ),
																 2 => array( 'act'           , 'op'        ),
																 3 => array( 'MAX_FILE_SIZE' , '10000000000' ),
													 ) , "uploadform", " enctype='multipart/form-data'"      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "50%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "50%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Import an XML settings file" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Upload XML settings file from your computer</b><div style='color:gray'>Duplicate entries will not be overwritten but the default setting and other options will be updated. The file must begin with 'ipb_' and end with either '.xml' or '.xml.gz'</div>" ,
										  				         $ibforums->adskin->form_upload(  )
								                        )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b><u>OR</u> enter the filename of the XML settings file</b><div style='color:gray'>The file must be uploaded into the forum's root folder</div>" ,
										  				         $ibforums->adskin->form_input( 'file_location', 'ipb_settings_partial.xml'  )
								                        )      );

		$ibforums->html .= $ibforums->adskin->end_form("Import XML settings Set");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Other & Dev options
		//-----------------------------------------

		$ibforums->html .= "<br /><br/ ><div align='center'><a href='{$ibforums->base_url}&act=op&code=setting_someexport_start'>Export Selected Settings</a></div>";

		if ( IN_DEV )
		{
			$ibforums->html .= "<br /><div align='center'>Developer Options: <a href='{$ibforums->base_url}&act=op&code=setting_allexport'>Export all to XML</a></div>";
		}

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// Settings Update
	//-----------------------------------------

	function setting_update( $donothing="" )
	{
		global $ibforums, $DB, $std;

		if ( ! $ibforums->input['id'] and ! $ibforums->input['search'] )
		{
			$ibforums->main_msg = "No ID was passed, please try again";
			$this->setting_start();
		}

		//-----------------------------------------
		// Reorder?
		//-----------------------------------------

		if ( $ibforums->input['reorder'] )
		{
			foreach ($ibforums->input as $key => $value)
			{
				if ( preg_match( "/^cp_(\d+)$/", $key, $match ) )
				{
					if ( isset( $ibforums->input[$match[0]]) )
					{
						$DB->do_update( 'conf_settings', array( 'conf_position' => $ibforums->input[$match[0]] ), 'conf_id='.$match[1] );
					}
				}
			}

			$ibforums->main_msg = "Settings reordered";

			$ibforums->input['conf_group'] = $ibforums->input['id'];

			$this->setting_view();
		}

		//-----------------------------------------
		// check...
		//-----------------------------------------

		$fields = explode(",", trim($ibforums->input['settings_save']) );

		if ( ! count($fields ) )
		{
			$ibforums->main_msg = "No fields were passed to be saved";
			$ibforums->settings_view();
		}

		//-----------------------------------------
		// Get info from DB
		//-----------------------------------------

		$db_fields = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'where' => "conf_key IN ('".implode( "','", $fields )."')" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$db_fields[ $r['conf_key']  ] = $r;
		}

		foreach( $db_fields as $key => $data )
		{
			if ( $data['conf_evalphp'] )
			{
				$save = 1;
				eval( $data['conf_evalphp'] );
			}

			if ( ($_POST[ $key ] != $data['conf_default']) )
			{
				$value = str_replace( "&#39;", "'", $std->txt_safeslashes($_POST[ $key ]) );

				$DB->do_update( 'conf_settings', array( 'conf_value' => $value ), 'conf_id='.$data['conf_id'] );
			}
			else if ( $ibforums->input[ $key ] != "" and ( $ibforums->input[ $key ] == $data['conf_default'] ) and $data['conf_value'] != '' )
			{
				$DB->do_update( 'conf_settings', array( 'conf_value' => '' ), 'conf_id='.$data['conf_id'] );
			}
		}

		$ibforums->input['conf_group'] = $ibforums->input['id'];

		$ibforums->main_msg = "Settings updated";

		$this->setting_rebuildcache();

		if ( ! $donothing )
		{
			$this->setting_view();
		}
	}

	//-----------------------------------------
	// Settings Save Form
	//-----------------------------------------

	function setting_save($type='add')
	{
		global $ibforums, $DB, $std;

		if ( $type == 'edit' )
		{
			if ( ! $ibforums->input['id'] )
			{
				$ibforums->main_msg = "No ID was passed, please try again";
				$this->setting_form();
			}
		}

		//-----------------------------------------
		// check...
		//-----------------------------------------

		$conf_group = $ibforums->input['conf_newgroup'] ? $ibforums->input['conf_newgroup'] : $ibforums->input['conf_group'];

		$array = array( 'conf_title'       => $ibforums->input['conf_title'],
						'conf_description' => $std->txt_safeslashes( $_POST['conf_description'] ),
						'conf_group'       => $ibforums->input['conf_group'],
						'conf_type'        => $ibforums->input['conf_type'],
						'conf_key'         => $ibforums->input['conf_key'],
						'conf_value'       => $std->txt_safeslashes( $_POST['conf_value'] ),
						'conf_default'     => $std->txt_safeslashes( $_POST['conf_default'] ),
						'conf_extra'       => $std->txt_safeslashes( $_POST['conf_extra'] ),
						'conf_evalphp'     => $std->txt_safeslashes( $_POST['conf_evalphp'] ),
						'conf_protected'   => intval( $ibforums->input['conf_protected'] ),
						'conf_position'    => intval( $ibforums->input['conf_position'] ),
						'conf_start_group' => $ibforums->input['conf_start_group'],
						'conf_end_group'   => $ibforums->input['conf_end_group'],
						'conf_help_key'    => $ibforums->input['conf_help_key'],
						'conf_add_cache'   => intval( $ibforums->input['conf_add_cache'] ),
					 );


		if ( $type == 'add' )
		{
			$DB->do_insert( 'conf_settings', $array );
			$ibforums->main_msg = 'New Setting Added';

			$DB->simple_exec_query( array( 'update' => 'conf_settings_titles', 'set' => 'conf_title_count=conf_title_count+1', 'where' => 'conf_title_id='.$ibforums->input['conf_group'] ) );

		}
		else
		{
			$DB->do_update( 'conf_settings', $array, 'conf_id='.$ibforums->input['id'] );
			$ibforums->main_msg = 'Setting Edited';
		}

		$this->setting_rebuildcache();

		$this->setting_view();

	}

	//-----------------------------------------
	// Settings Revert
	//-----------------------------------------

	function setting_revert()
	{
		global $ibforums, $DB, $std;

		$ibforums->input['id'] = intval($ibforums->input['id']);

		if ( ! $ibforums->input['id'] )
		{
			$ibforums->main_msg = "No ID was passed, please try again";
			$this->setting_form();
		}

		$conf = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_id='.$ibforums->input['id'] ) );

		//-----------------------------------------
		// Revert...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'update' => 'conf_settings', 'set' => "conf_value=''", 'where' => 'conf_id='.$ibforums->input['id'] ) );

		$ibforums->main_msg = "Configuration setting reverted back to default.";

		$this->setting_rebuildcache();

		$this->setting_view();

	}

	//-----------------------------------------
	// Settings Delete
	//-----------------------------------------

	function setting_delete()
	{
		global $ibforums, $DB, $std;

		if ( ! $ibforums->input['id'] )
		{
			$ibforums->main_msg = "No ID was passed, please try again";
			$this->setting_form();
		}

		$conf = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_id='.$ibforums->input['id'] ) );

		//-----------------------------------------
		// Delete...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'conf_settings', 'where' => 'conf_id='.$ibforums->input['id'] ) );

		$DB->simple_exec_query( array( 'update' => 'conf_settings_titles', 'set' => 'conf_title_count=conf_title_count-1', 'where' => 'conf_title_id='.$conf['conf_group'] ) );

		$ibforums->main_msg = "Configuration Setting Deleted";

		$this->setting_rebuildcache();

		$ibforums->input['conf_group'] = $conf['conf_group'];
		$this->setting_view();

	}


	//-----------------------------------------
	// BBCODE Rebuild Cache
	//-----------------------------------------

	function setting_rebuildcache()
	{
		global $ibforums, $DB, $std;

		$ibforums->cache['settings'] = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_add_cache=1' ) );
		$info = $DB->simple_exec();

		while ( $r = $DB->fetch_row($info) )
		{
			$value = $r['conf_value'] != "" ?  $r['conf_value'] : $r['conf_default'];

			if ( $value == '{blank}' )
			{
				$value = '';
			}

			$ibforums->cache['settings'][ $r['conf_key'] ] = $std->txt_safeslashes($value);
		}

		$std->update_cache( array( 'name' => 'settings', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	// Setting get cache
	//-----------------------------------------

	function setting_get_groups()
	{
		global $ibforums, $DB, $std;

		$this->setting_groups = array();

		if ( IN_DEV )
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings_titles', 'order' => 'conf_title_title' ) );
			$DB->simple_exec();
		}
		else
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings_titles', 'where' => 'conf_title_noshow=0', 'order' => 'conf_title_title' ) );
			$DB->simple_exec();
		}

		while( $r = $DB->fetch_row() )
		{
			$this->setting_groups[ $r['conf_title_id'] ] = $r;
		}
	}

	//-----------------------------------------
	// Make drop down of available titles
	//-----------------------------------------

	function setting_make_dropdown()
	{
		global $ibforums, $DB, $std;

		if ( ! is_array( $this->setting_groups ) )
		{
			$this->setting_get_groups();
		}

		$ret = "<form method='post' action='{$ibforums->base_url}&act=op&code=setting_view'>
		        <select name='conf_group' class='dropdown'>";

		foreach( $this->setting_groups as $id => $data )
		{
			$ret .= ( $id == $ibforums->input['conf_group'] )
				  ? "<option value='{$id}' selected='selected'>{$data['conf_title_title']}</option>"
				  : "<option value='{$id}'>{$data['conf_title_title']}</option>";
		}

		return $ret."\n</select><input type='submit' id='button' value='Go' /></form>";
	}

	//-----------------------------------------
	// Save full text options
	//-----------------------------------------

	function do_fulltext()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		$ibforums->admin-> get_mysql_version();

		if ( $DB->sql_can_fulltext() )
		{
			// How many posts do we have?

			$DB->simple_construct( array( 'select' => 'COUNT(*) as cnt', 'from' => 'posts' ) );
			$DB->simple_exec();

			$result = $DB->fetch_row();

			// If over 15,000 posts...

			if ( $result['cnt'] > 15000 )
			{
				// Explain how, why and what to do..

				$ibforums->admin->page_detail = "";
				$ibforums->admin->page_title  = "Unable to continue";

				$ibforums->html .= $ibforums->adskin->add_td_basic( $this->return_sql_no_no_cant_do_it_sorry_text(), 'left', 'faker' );

				$ibforums->admin->output();
			}
			else
			{
				// Index away!

				$DB->sql_add_fulltext_index( 'topics', 'title' );
				$DB->sql_add_fulltext_index( 'posts' , 'post' );
			}
		}
		else
		{
			$ibforums->admin->error("Sorry, the version of MySQL that you are using is unable to use FULLTEXT searches");
		}

		$ibforums->admin->save_log("Full Text Options Updated");

		$ibforums->admin->done_screen("Full Text Indexes Rebuilt", "Full Text Settings", "act=op&code=setting_view&conf_group=19", "redirect" );
	}




	//-----------------------------------------
	//
	// Save config. Does the hard work, so you don't have to.
	//
	//-----------------------------------------

	function save_config( $new )
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		$master = array();

		if ( is_array($new) )
		{
			if ( count($new) > 0 )
			{
				foreach( $new as $field )
				{

					// Handle special..

					if ($field == 'img_ext' or $field == 'avatar_ext' or $field == 'photo_ext')
					{
						$_POST[ $field ] = preg_replace( "/[\.\s]/", "" , $_POST[ $field ] );
						$_POST[ $field ] = str_replace('|', "&#124;", $_POST[ $field ]);
						$_POST[ $field ] = preg_replace( "/,/"     , '|', $_POST[ $field ] );
					}
					else if ($field == 'coppa_address')
					{
						$_POST[ $field ] = nl2br( $_POST[ $field ] );
					}

					if ( $field == 'gd_font' OR $field == 'html_dir' OR $field == 'upload_dir')
					{
						$_POST[ $field ] = preg_replace( "/'/", "&#39;", $_POST[ $field ] );
					}
					else
					{
						$_POST[ $field ] = preg_replace( "/'/", "&#39;", stripslashes($_POST[ $field ]) );
					}

					$master[ $field ] = stripslashes($_POST[ $field ]);
				}

				$ibforums->admin->rebuild_config($master);
			}
		}

		$ibforums->admin->save_log("Board Settings Updated, Back Up Written");

		$ibforums->admin->done_screen("Forum Configurations updated", "Administration CP Home", "act=index" );
	}



	function return_sql_no_no_cant_do_it_sorry_text()
	{
return "
<div style='line-height:150%'>
<span id='large'>Unable to automatically create the FULLTEXT indexes</span>
<br /><br />
You have too many posts for an automatic FULLTEXT index creation. It is more than likely that PHP will
time out before the indexes are complete which could cause some index corruption.
<br />
Creating FULLTEXT indexes is a relatively slow process but it's one that's worth doing as it will save you
a lot of time and CPU power when your members search.
<br />
On average, a normal webserver is capable of indexing about 80,000 posts an hour but it is a relatively intense process. If you
are using MySQL 4.0.12+ then this time is reduced substaintially.
<br />
<br />
<strong style='color:red;font-size:14px'>How to manually create the indexes</strong>
<br />
If you have shell (SSH / Telnet) access to mysql, the process is very straightforward. If you do not have access to shell, then you will
have to contact your webhost and ask them to do this for you.
<br /><br />
<strong>Step 1: Initiate mysql</strong>
<br />
In shell type:
<br />
<pre>mysql -u{your_sql_user_name} -p{your_sql_password}</pre>
<br />
Your MySQL username and password can be found in your conf_global.php file
<br />
<br />
<strong>Step 2: Select your database</strong>
<br />
In mysql type:
<br />
<pre>use {your_database_name_here};</pre>
<br />
Make sure you use a trailing semi-colon. Your MySQL database name can be found in conf_global.php
<br /><br />
<strong>Step 3: Indexing the topics table</strong>
<br />
In mysql type:
<br />
<pre>\g alter table ibf_topics add fulltext(title);</pre>
<br />
If you are not using 'ibf_' as your table extension, adjust that query to suit. This query can take a while
depending on the number of topics you have.
<br />
<br />
<strong>Step 4: Indexing the posts table</strong>
<br />
In mysql type:
<br />
<pre>\g alter table ibf_posts add fulltext(post);</pre>
<br />
If you are not using 'ibf_' as your table extension, adjust that query to suit. This query can take a while
depending on the number of posts you have. On average MySQL can index 80,000 posts an hour. If you are using MySQL 4, the time is greatly reduced.
</div>
";
	}
}


?>