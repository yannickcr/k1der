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
|   > Administration Module
|   > Module written by Matt Mecham
|   > Date started: 27th January 2004
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

class ad_administration {

	var $functions = "";

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Require and RUN !! THERES A BOMB
		//-----------------------------------------

		$ibforums->admin->page_detail = "The cache manager allows you to view the contents of your cache and update them.";
		$ibforums->admin->page_title  = "Cache Manager";

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------
		// What to do...
		//-----------------------------------------

		switch($ibforums->input['code'])
		{

			case 'cacheend':
				$this->cache_end();
				break;

			case 'viewcache':
				$this->view_cache();
				break;

			case 'bbcode':
				$this->bbcode_start();
			    break;

			case 'bbcode_add':
				$this->bbcode_form('add');
				break;

			case 'bbcode_doadd':
				$this->bbcode_save('add');
				break;

			case 'bbcode_edit':
				$this->bbcode_form('edit');
				break;

			case 'bbcode_doedit':
				$this->bbcode_save('edit');
				break;

			case 'bbcode_test':
				$this->bbcode_test();
				break;

			case 'bbcode_delete':
				$this->bbcode_delete();
				break;

			case 'bbcode_export':
				$this->bbcode_export();
				break;

			case 'bbcode_import':
				$this->bbcode_import();
				break;

			//-----------------------------------------
			// Emu?
			//-----------------------------------------

			case 'emo':
				$this->emoticon_start();
				break;

			case 'emo_packsplash':
				$this->emoticon_pack_splash();
				break;

			case 'emo_packexport':
				$this->emoticon_pack_export();
				break;

			case 'emo_packimport':
				$this->emoticon_pack_import();
				break;

			case 'emo_manage':
				$this->emoticon_manage();
				break;

			case 'emo_doedit':
				$this->emoticon_edit();
				break;

			case 'emo_doadd':
				$this->emoticon_add();
				break;

			case 'emo_remove':
				$this->emoticon_remove();
				break;

			case 'emo_setadd':
				$this->emoticon_setalter($type='add');
				break;

			case 'emo_setedit':
				$this->emoticon_setalter($type='edit');
				break;

			case 'emo_setremove':
				$this->emoticon_setremove();
				break;

			case 'emo_upload':
				$this->emoticon_upload();

			//-----------------------------------------
			// Badword
			//-----------------------------------------

			case 'badword':
				$this->badword_start();
				break;

			case 'badword_add':
				$this->badword_add();
				break;

			case 'badword_remove':
				$this->badword_remove();
				break;

			case 'badword_edit':
				$this->badword_edit();
				break;

			case 'badword_doedit':
				$this->badword_doedit();
				break;

			case 'badword_export':
				$this->badword_export();
				break;

			case 'badword_import':
				$this->badword_import();
				break;

			//-----------------------------------------
			// BAN (d-aid)
			//-----------------------------------------

			case 'ban':
				$this->ban_start();
				break;
			case 'ban_add':
				$this->ban_add();
				break;
			case 'ban_delete':
				$this->ban_delete();
				break;

			default:
				$this->cache_start();
				break;
		}
	}

	//-----------------------------------------
	// BAN: Rebuild cache
	//-----------------------------------------

	function ban_rebuildcache()
	{
		global $ibforums, $std, $DB;

		$ibforums->cache['banfilters'] = array();

		$DB->simple_construct( array( 'select' => 'ban_content', 'from' => 'banfilters', 'where' => "ban_type='ip'" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['banfilters'][] = $r['ban_content'];
		}

		$std->update_cache( array( 'name' => 'banfilters', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	// BAN: DELETE
	//-----------------------------------------

	function ban_delete()
	{
		global $ibforums, $DB,  $std;

		$ids = array();

		foreach ($ibforums->input as $key => $value)
		{
			if ( preg_match( "/^id_(\d+)$/", $key, $match ) )
			{
				if ( $ibforums->input[$match[0]] )
				{
					$ids[] = $match[1];
				}
			}
		}

		if ( count( $ids ) )
		{
			$DB->simple_construct( array( 'delete' => 'banfilters', 'where' => 'ban_id IN('.implode( ",",$ids ).')' ) );
			$DB->simple_exec();
		}

		$this->ban_rebuildcache();

		$ibforums->main_msg = "Ban filters removed";
		$this->ban_start();
	}

	//-----------------------------------------
	// BAN: ADD
	//-----------------------------------------

	function ban_add()
	{
		global $ibforums, $DB,  $std;

		if ( ! $ibforums->input['bantext'] )
		{
			$ibforums->main_msg = "You must enter something to add to the ban filters!";
			$this->ban_start();
		}

		//-----------------------------------------
		// Check for existing entry
		//-----------------------------------------

		$result = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'banfilters', 'where' => "ban_content='{$ibforums->input['bantext']}' and ban_type='{$ibforums->input['bantype']}'" ) );

		if ( $result['ban_id'] )
		{
			$ibforums->main_msg = "Duplicate entry, entry not added to the ban filters database.";
			$this->ban_start();
		}

		$DB->do_insert( 'banfilters', array( 'ban_type' => $ibforums->input['bantype'], 'ban_content' => $ibforums->input['bantext'], 'ban_date' => time() ) );

		$this->ban_rebuildcache();

		$ibforums->main_msg = "Ban filter added";

		$this->ban_start();

	}

	//-----------------------------------------
	// BAN: START
	//-----------------------------------------

	function ban_start()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Ban Control";

		$ibforums->admin->page_detail = "This section allows you to modify, delete or add IP addresses, email addresses and reserved names to the ban filters.
										 <br /><strong>You can use * as a wildcard in any IP or email filter. (Example: 127.0.*, *@yahoo.com)</strong>";

		//-----------------------------------------
		// Get things
		//-----------------------------------------

		$ban = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'banfilters', 'order' => 'ban_date desc' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ban[ $r['ban_type'] ][ $r['ban_id'] ] = $r;
		}

		//-----------------------------------------
		// SHOW THEM!
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'ban_delete'),
												 				 2 => array( 'act'   , 'admin'  ),
									     			    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "1%" );
		$ibforums->adskin->td_header[] = array( "Entry"    , "80%" );
		$ibforums->adskin->td_header[] = array( "Date Added"     , "20%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Ban Control" );

		//-----------------------------------------
		// Banned IP Addresses
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_basic("Banned IP Addresses", "left", "pformstrip");

		if ( is_array( $ban['ip'] ) and count( $ban['ip'] ) )
		{
			foreach ( $ban['ip'] as $id => $entry )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<input type='checkbox' name='id_{$entry['ban_id']}' value='1' />",
																		 $entry['ban_content'],
																	 	 $std->get_date( $entry['ban_date'], 'SHORT' ),
																)      );
			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("No Entered Banned IP Addresses", "left", "tdrow1");
		}

		//-----------------------------------------
		// Banned Email Addresses
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_basic("Banned Email Addresses", "left", "pformstrip");

		if ( is_array( $ban['email'] ) and count( $ban['email'] ) )
		{
			foreach ( $ban['email'] as $id => $entry )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<input type='checkbox' name='id_{$entry['ban_id']}' value='1' />",
																		 $entry['ban_content'],
																	 	 $std->get_date( $entry['ban_date'], 'SHORT' ),
																)      );
			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("No Entered Banned Email Addresses", "left", "tdrow1");
		}

		//-----------------------------------------
		// Banned Names
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_basic("Non-Registerable Names", "left", "pformstrip");

		if ( is_array( $ban['name'] ) and count( $ban['name'] ) )
		{
			foreach ( $ban['name'] as $id => $entry )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<input type='checkbox' name='id_{$entry['ban_id']}' value='1' />",
																		 $entry['ban_content'],
																	 	 $std->get_date( $entry['ban_date'], 'SHORT' ),
																)      );
			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("No Entered Names", "left", "tdrow1");
		}

		$end_it_now = "<div align='left' style='float:left;width:auto;'>
		 			   <input type='submit' value='Delete Selected' class='realdarkbutton' />
					   </div></form>
					   <div align='center'><form method='post' action='{$ibforums->base_url}&act=admin&code=ban_add'><input type='text' size='30' class='textinput' value='' name='bantext' />
					   <select class='dropdown' name='bantype'><option value='ip'>IP Address</option><option value='email'>Email Address</option><option value='name'>Name</option></select>
					   <input type='submit' value='Add New Filter' class='realdarkbutton' /></form></div>";

		$ibforums->html .= $ibforums->adskin->add_td_basic( $end_it_now, "center", "pformstrip");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// BADWORDS: Import
	//-----------------------------------------

	function badword_import()
	{
		global $ibforums, $DB,  $std;

		$content = $ibforums->admin->import_xml( 'ipb_badwords.xml' );

		//-----------------------------------------
		// Got anything?
		//-----------------------------------------

		if ( ! $content )
		{
			$ibforums->main_msg = "Upload failed, ipb_badwords.xml was either missing or empty";
			$this->badword_start();
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
		// Get current badwords
		//-----------------------------------------

		$words = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'badwords', 'order' => 'type' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$words[ $r['type'] ] = 1;
		}

		//-----------------------------------------
		// pArse
		//-----------------------------------------

		foreach( $xml->xml_array['badwordexport']['badwordgroup']['badword'] as $idx => $entry )
		{
			$type    = $entry['type']['VALUE'];
			$swop    = $entry['swop']['VALUE'];
			$m_exact = $entry['m_exact']['VALUE'];

			if ( $words[ $type ] )
			{
				continue;
			}

			if ( $type )
			{
				$DB->do_insert( 'badwords', array( 'type' => $type, 'swop' => $swop, 'm_exact' => $m_exact ) );
			}
		}

		$this->badword_rebuildcache();

		$ibforums->main_msg = "Badword XML file import completed";

		$this->badword_start();

	}

	//-----------------------------------------
	// BADWORD: Export
	//-----------------------------------------

	function badword_export()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Start...
		//-----------------------------------------

		$xml->xml_set_root( 'badwordexport', array( 'exported' => time() ) );

		//-----------------------------------------
		// Get emo group
		//-----------------------------------------

		$xml->xml_add_group( 'badwordgroup' );

		$DB->simple_construct( array( 'select' => 'type, swop, m_exact', 'from' => 'badwords', 'order' => 'type' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$content = array();

			foreach ( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}

			$entry[] = $xml->xml_build_entry( 'badword', $content );
		}

		$xml->xml_add_entry_to_group( 'badwordgroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$ibforums->admin->show_download( $xml->xml_document, 'ipb_badwords.xml' );
	}

	//-----------------------------------------
	// BADWORD: Start
	//-----------------------------------------

	function badword_start()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You can add/edit and remove bad word filters in this section.<br>The badword filter allows you to globally replace words from a members post, signature and topic title.<br><br><b>Loose matching</b>: If you entered 'hell' as a bad word, it will replace 'hell' and 'hello' with either your replacement if entered or 6 hashes (case insensitive)<br><br><b>Exact matching</b>: If you entered 'hell' as a bad word, it will replace 'hell' only with either your replacement if entered or 6 hashes (case insensitive)";
		$ibforums->admin->page_title  = "Bad Word Filter";

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'badword_add' ),
												  			     2 => array( 'act'   , 'admin'       ),
									     			  )      );

		$ibforums->adskin->td_header[] = array( "Before"  , "30%" );
		$ibforums->adskin->td_header[] = array( "After"   , "30%" );
		$ibforums->adskin->td_header[] = array( "Method"  , "20%" );
		$ibforums->adskin->td_header[] = array( "Edit"    , "10%" );
		$ibforums->adskin->td_header[] = array( "Remove"  , "10%" );

		//-----------------------------------------
		// Start table
		//-----------------------------------------

		$export_button = $ibforums->adskin->js_make_button("Export Filters", $ibforums->base_url."&act=admin&code=badword_export");

		$table = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
				  <td align='left' width='100%' style='font-weight:bold;font-size:11px;color:#FFF'>Current Filters</td>
				  <td align='right' nowrap='nowrap' style='padding-right:2px'>{$export_button}</td>
				  </tr>
				  </table>";

		$ibforums->html .= $ibforums->adskin->start_table( $table );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'badwords', 'order' => 'type' ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{
			while ( $r = $DB->fetch_row() )
			{
				$words[] = $r;
			}

			//usort($words, array( 'ad_settings', 'perly_word_sort' ) );

			foreach($words as $idx => $r)
			{

				$replace = $r['swop']    ? stripslashes($r['swop']) : '######';

				$method  = $r['m_exact'] ? 'Exact' : 'Loose';

				$ibforums->html .= $ibforums->adskin->add_td_row( array( stripslashes($r['type']),
														  $replace,
														  $method,
														  "<center><a href='".$ibforums->adskin->base_url."&act=admin&code=badword_edit&id={$r['wid']}'>Edit</a></center>",
														  "<center><a href='".$ibforums->adskin->base_url."&act=admin&code=badword_remove&id={$r['wid']}'>Remove</a></center>",
												 )      );
			}

		}

		$ibforums->html .= $ibforums->adskin->end_table();


		$ibforums->adskin->td_header[] = array( "Before"  , "40%" );
		$ibforums->adskin->td_header[] = array( "After"   , "40%" );
		$ibforums->adskin->td_header[] = array( "Method"  , "20%" );

		//-----------------------------------------
		// Add new filter
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Add a new filter" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( $ibforums->adskin->form_input('before'),
												  $ibforums->adskin->form_input('after'),
												  $ibforums->adskin->form_dropdown( 'match', array( 0 => array( 1, 'Exact'  ), 1 => array( 0, 'Loose' ) ) )
										 )      );

		$ibforums->html .= $ibforums->adskin->end_form('Add Filter');

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// IMPORT: Start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );

		//-----------------------------------------
		// IMPORT: Start output
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'badword_import' ),
															   2 => array( 'act'   , 'admin'      ),
															   3 => array( 'MAX_FILE_SIZE', '10000000000' ),
													  ) , "uploadform", " enctype='multipart/form-data'"     );


		$ibforums->html .= $ibforums->adskin->start_table( "Import a Badword List" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													 		 "<b>Upload XML Badword List</b><div style='color:gray'>Browse your computer for 'ipb_badwords.xml' or 'ipb_badwords.xml.gz'. Duplicate entries will not be imported.</div>",
													  		$ibforums->adskin->form_upload(  )
													   )      );

		$ibforums->html .= $ibforums->adskin->end_form("Import");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// BADWORD: Complete Edit
	//-----------------------------------------

	function badword_doedit()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['before'] == "")
		{
			$ibforums->admin->error("You must enter a word to replace, silly!");
		}

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must pass a valid filter id, silly!");
		}

		$ibforums->input['match'] = $ibforums->input['match'] ? 1 : 0;

		strlen($ibforums->input['swop']) > 1 ?  $ibforums->input['swop'] : "";

		$DB->do_update( 'badwords', array( 'type'    => $ibforums->input['before'],
										   'swop'    => $ibforums->input['after'],
										   'm_exact' => $ibforums->input['match'],
								  ), "wid='".$ibforums->input['id']."'"  );

		$this->badword_rebuildcache();

		$ibforums->main_msg = "Filter edited";

		$this->badword_start();
	}

	//-----------------------------------------
	// BADWORD:  Edit
	//-----------------------------------------

	function badword_edit()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You may edit the chosen filter below";
		$ibforums->admin->page_title  = "Bad Word Filter";

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must pass a valid filter id, silly!");
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'badwords', 'where' => "wid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		if ( ! $r = $DB->fetch_row() )
		{
			$ibforums->admin->error("We could not find that filter in the database");
		}

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'badword_doedit' ),
												 			     2 => array( 'act'   , 'admin'     ),
												  			     3 => array( 'id'    , $ibforums->input['id'] ),
									                    )      );



		$ibforums->adskin->td_header[] = array( "Before"  , "40%" );
		$ibforums->adskin->td_header[] = array( "After"   , "40%" );
		$ibforums->adskin->td_header[] = array( "Method"  , "20%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Edit a filter" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( $ibforums->adskin->form_input('before', stripslashes($r['type']) ),
												  			     $ibforums->adskin->form_input('after' , stripslashes($r['swop']) ),
												  			     $ibforums->adskin->form_dropdown( 'match', array( 0 => array( 1, 'Exact'  ), 1 => array( 0, 'Loose' ) ), $r['m_exact'] )
													    )      );

		$ibforums->html .= $ibforums->adskin->end_form('Edit Filter');

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// BADWORD: Remove badowrd
	//-----------------------------------------

	function badword_remove()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must pass a valid filter id, silly!");
		}

		$DB->simple_exec_query( array( 'delete' => 'badwords', 'where' => "wid='".$ibforums->input['id']."'" ) );

		$this->badword_rebuildcache();

		$ibforums->main_msg = "Filter removed";

		$this->badword_start();
	}

	//-----------------------------------------
	// BADWORD: Add badword
	//-----------------------------------------

	function badword_add()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['before'] == "")
		{
			$ibforums->admin->error("You must enter a word to replace, silly!");
		}

		$ibforums->input['match'] = $ibforums->input['match'] ? 1 : 0;

		strlen($ibforums->input['swop']) > 1 ?  $ibforums->input['swop'] : "";

		$DB->do_insert( 'badwords', array( 'type'    => $ibforums->input['before'],
										   'swop'    => $ibforums->input['after'],
										   'm_exact' => $ibforums->input['match'],
								  )      );

		$this->badword_rebuildcache();

		$ibforums->main_msg = "New filter added";

		$this->badword_start();
	}

	//-----------------------------------------
	// BADWORD Rebuild Cache
	//-----------------------------------------

	function badword_rebuildcache()
	{
		global $ibforums, $std, $DB;

		$ibforums->cache['badwords'] = array();

		$DB->simple_construct( array( 'select' => 'type,swop,m_exact', 'from' => 'badwords' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['badwords'][] = $r;
		}

		$std->update_cache( array( 'name' => 'badwords', 'array' => 1, 'deletefirst' => 1 ) );
	}


	//-----------------------------------------
	// EMOTICON Set add
	//-----------------------------------------

	function emoticon_setalter($type='add')
	{
		global $ibforums, $DB,  $std;

		$name = preg_replace( "/[^a-zA-Z0-9\-_]/", "", $ibforums->input['emoset'] );

		if ($name == "")
		{
			$ibforums->main_msg = "No valid folder name was entered, please try again using only alphanumerics (A-Z, a-z, 0-9)";
			$this->emoticon_start();
		}

		//-----------------------------------------
		// Safe mode?
		//-----------------------------------------

		if ( SAFE_MODE_ON )
		{
			$ibforums->main_msg = "SAFE MODE DETECTED: IPB cannot create or edit folders for you, please create or edit the folder manually using FTP in 'style_emoticons'";
			$this->emoticon_start();
		}

		//-----------------------------------------
		// Directory exists?
		//-----------------------------------------

		if ( file_exists( CACHE_PATH.'style_emoticons/'.$name ) )
		{
			$ibforums->main_msg = "'style_emoticons/$name' already exists, please choose another name.";
			$this->emoticon_start();
		}

		if ( $type == 'add' )
		{
			//-----------------------------------------
			// Create directory?
			//-----------------------------------------

			if ( @mkdir( CACHE_PATH.'style_emoticons/'.$name, 0777 ) )
			{
				@chmod( CACHE_PATH.'style_emoticons/'.$name, 0777 );

				$ibforums->main_msg = "New Folder Added";
				$this->emoticon_start();
			}
			else
			{
				$ibforums->main_msg = "IPB cannot create a new folder for you, please create the folder manually using FTP in 'style_emoticons'";
				$this->emoticon_start();
			}
		}
		else
		{
			if ( ! $ibforums->input['id'] )
			{
				$ibforums->main_msg = "Missing directory name, please try again.";
				$this->emoticon_start();
			}

			//-----------------------------------------
			// Rename directory?
			//-----------------------------------------

			if ( @rename( CACHE_PATH.'style_emoticons/'.$ibforums->input['id'], CACHE_PATH.'style_emoticons/'.$name ) )
			{
				if ( file_exists( CACHE_PATH.'style_emoticons/'.$name ) )
				{
					//-----------------------------------------
					// Update the emos
					//-----------------------------------------

					$DB->do_update( 'emoticons', array( 'emo_set' => $name ), "emo_set='".$ibforums->input['id']."'" );
				}

				$this->emoticon_rebuildcache();

				$ibforums->main_msg = "Folder renamed.";
				$this->emoticon_start();
			}
			else
			{
				$ibforums->main_msg = "IPB cannot rename this folder for you.";
				$this->emoticon_start();
			}
		}
	}

	//-----------------------------------------
	// EMOTICON Edit
	//-----------------------------------------

	function emoticon_edit()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->main_msg = "No emoticon group ID was passed";
			$this->emoticon_start();
		}

		foreach ($ibforums->input as $key => $value)
		{
			if ( preg_match( "/^emo_type_(\d+)$/", $key, $match ) )
			{
				if ( isset( $ibforums->input[$match[0]]) )
				{
					$typed = $ibforums->input[$match[0]];
					$click = $ibforums->input['emo_click_'.$match[1] ];

					$typed = str_replace( '&#092;', "", $typed );

					if ( $typed and $match[1] )
					{
						$DB->do_update( 'emoticons', array( 'clickable' => intval($click), 'typed' => $typed ), 'id='.$match[1] );
					}
				}
			}
		}

		$this->emoticon_rebuildcache();

		$ibforums->main_msg = "Emoticons updated";

		$this->emoticon_manage();

	}

	//-----------------------------------------
	// EMOTICON Remove
	//-----------------------------------------

	function emoticon_remove()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->main_msg = "No emoticon group ID was passed";
			$this->emoticon_start();
		}

		if ($ibforums->input['eid'] == "")
		{
			$ibforums->main_msg = "No emoticon ID was passed";
			$this->emoticon_manage();
		}

		$DB->simple_exec_query( array( 'delete' => 'emoticons', 'where' => "id=".intval($ibforums->input['eid']) ) );

		$this->emoticon_rebuildcache();

		$ibforums->main_msg = "Emoticon removed";

		$this->emoticon_manage();
	}

	//-----------------------------------------
	// EMOTICON ADD
	//-----------------------------------------

	function emoticon_add()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->main_msg = "No emoticon group ID was passed";
			$this->emoticon_start();
		}

		foreach ($ibforums->input as $key => $value)
		{
			if ( preg_match( "/^emo_type_(\d+)$/", $key, $match ) )
			{
				if ( isset( $ibforums->input[$match[0]]) )
				{
					$typed = $ibforums->input[$match[0]];
					$click = $ibforums->input['emo_click_'.$match[1] ];
					$add   = $ibforums->input['emo_add_'.$match[1] ];
					$image = $ibforums->input['emo_image_'.$match[1] ];
					$set   = trim($ibforums->input['id']);

					$typed = str_replace( '&#092;', "", $typed );

					if ( $ibforums->input['addall'] )
					{
						$add = 1;
					}

					if ( $add and $typed and $image )
					{
						$DB->do_insert( 'emoticons', array( 'clickable' => intval($click), 'typed' => $typed, 'image' => $image, 'emo_set' => $set ) );
					}
				}
			}
		}

		$this->emoticon_rebuildcache();

		$ibforums->main_msg = "Emoticons updated";

		$this->emoticon_manage();
	}


	//-----------------------------------------
	// EMOTICON Upload
	//-----------------------------------------

	function emoticon_upload()
	{
		global $ibforums, $DB, $std;

		$overwrite = 1;

		//-----------------------------------------
		// Which folders?
		//-----------------------------------------

		$directories = array();
		$first_dir   = '';

		foreach ($ibforums->input as $key => $value)
		{
			if ( preg_match( "/^dir_(.*)$/", $key, $match ) )
			{
				if ( $ibforums->input[$match[0]] == 1 )
				{
					$directories[] = $match[1];
				}
			}
		}

		if ( ! count( $directories ) )
		{
			$ibforums->main_msg = "You did not choose any emoticon folders to upload into!";
			$this->emoticon_start();
		}

		//-----------------------------------------
		// Excuse me, can you shift?
		//-----------------------------------------

		$first_dir = array_shift( $directories );

		//-----------------------------------------
		// Loopy loo?
		//-----------------------------------------

		foreach( array( 1,2,3,4 ) as $i )
		{
			$field     = 'upload_'.$i;

			$FILE_NAME = $_FILES[$field]['name'];
			$FILE_SIZE = $_FILES[$field]['size'];
			$FILE_TYPE = $_FILES[$field]['type'];

			//-----------------------------------------
			// Naughty Opera adds the filename on the end of the
			// mime type - we don't want this.
			//-----------------------------------------

			$FILE_TYPE = preg_replace( "/^(.+?);.*$/", "\\1", $FILE_TYPE );

			//-----------------------------------------
			// Naughty Mozilla likes to use "none" to indicate an empty upload field.
			// I love universal languages that aren't universal.
			//-----------------------------------------

			if ( $_FILES[$field]['name'] == "" or ! $_FILES[$field]['name'] or ($_FILES[$field]['name'] == "none") )
			{
				continue;
			}

			//-----------------------------------------
			// Copy the upload to the uploads directory
			//-----------------------------------------

			if ( ! @move_uploaded_file( $_FILES[ $field ]['tmp_name'], CACHE_PATH.'style_emoticons/'.$first_dir."/".$FILE_NAME) )
			{
				$ibforums->main_msg = "The upload failed, sorry!";
				$this->emoticon_start();
			}
			else
			{
				@chmod( CACHE_PATH.'style_emoticons/'.$first_dir."/".$FILE_NAME, 0777 );

				//-----------------------------------------
				// Copy to other folders
				//-----------------------------------------

				if ( is_array( $directories ) and count( $directories ) )
				{
					foreach ( $directories as $newdir )
					{
						if ( file_exists( CACHE_PATH.'style_emoticons/'.$newdir."/".$FILE_NAME ) )
						{
							if ( $overwrite != 1 )
							{
								continue;
							}
						}

						if( @copy( CACHE_PATH.'style_emoticons/'.$first_dir."/".$FILE_NAME, CACHE_PATH.'style_emoticons/'.$newdir."/".$FILE_NAME ) )
						{
							@chmod( CACHE_PATH.'style_emoticons/'.$newdir."/".$FILE_NAME, 0777 );
						}
					}
				}
			}
		}

		$ibforums->main_msg = "Uploads complete!";
		$this->emoticon_start();
	}


	//-----------------------------------------
	// EMOTICON Start
	//-----------------------------------------

	function emoticon_start()
	{
		global $ibforums, $DB,  $std;

		if ( ! is_dir( CACHE_PATH. 'style_emoticons') )
		{
			$ibforums->admin->error("Could not locate the emoticons directory - make sure the 'style_emoticons' path is set correctly");
			$ibforums->admin->output();
		}

		//-----------------------------------------
		// Start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"          , "1%" );
		$ibforums->adskin->td_header[] = array( "Emoticon Folder" , "30%" );
		$ibforums->adskin->td_header[] = array( "Upload"          , "5%" );
		$ibforums->adskin->td_header[] = array( "# Disk Folder"   , "15%" );
		$ibforums->adskin->td_header[] = array( "# Emo. Group"    , "15%" );
		$ibforums->adskin->td_header[] = array( "Group Options"   , "20%" );

		//-----------------------------------------
		// Get emoticon count
		//-----------------------------------------

		$DB->cache_add_query( 'admin_emo_count', array() );
		$DB->cache_exec_query();

		while( $r = $DB->fetch_row() )
		{
			$emo_db[ $r['emo_set'] ] = $r;
		}

		//-----------------------------------------
		// Get emoticon folders
		//-----------------------------------------

		$emodirs = array();

		$dh = opendir( CACHE_PATH.'style_emoticons' );

 		while ( $file = readdir( $dh ) )
 		{
 			if (($file != ".") && ($file != ".."))
 			{
				if ( is_dir(CACHE_PATH.'style_emoticons/'.$file) )
				{
					$emodirs[] = $file;
				}
 			}
 		}
 		closedir( $dh );

		//-----------------------------------------
		// Start output
		//-----------------------------------------

		$ibforums->html .= "<script type='text/javascript'>
							function addfolder()
							{
								document.macroform.emoset.value       = '';
								document.macroform.code.value         = 'emo_setadd';
								document.macroform.submitbutton.value = 'Add Folder';
								scroll(0,0);
								togglediv( 'popbox', 1 );
								return false;
							}

							function editfolder(id)
							{
								document.macroform.submitbutton.value = 'Edit Folder Name';
								document.macroform.id.value     = id;
								document.macroform.code.value   = 'emo_setedit';
								document.macroform.emoset.value = id;
								scroll(0,0);
								togglediv( 'popbox', 1 );
								return false;
							}
							</script>
							<div align='center' style='position:absolute;width:99%;display:none;text-align:center' id='popbox'>
							 <form name='macroform' action='{$ibforums->adskin->base_url}' method='post'>
							 <input type='hidden' name='act' value='admin' />
							 <input type='hidden' name='code' value='emo_setadd' />
							 <input type='hidden' name='id' value='' />
							 <table cellspacing='0' width='500' align='center' cellpadding='6' style='background:#EEE;border:2px outset #555;'>
							 <tr>
							  <td width='1%' nowrap='nowrap' valign='top' align='center'>
							   <b>Folder name (alphanumerics only)</b><br><input class='textinput' name='emoset' type='text' size='40' />
							   <br /><br />
							   <center><input type='submit' class='realbutton' value='Add Folder' name='submitbutton' /> <input type='button' class='realdarkbutton' value='Close' onclick=\"togglediv('popbox');\" /></center>
							  </td>
							 </tr>
							 </table>
							 </form>
							</div>";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'emo_upload' ),
																 2 => array( 'act'   , 'admin'      ),
																 3 => array( 'MAX_FILE_SIZE', '10000000000' ),
														) , "uploadform", " enctype='multipart/form-data'"     );

		$ibforums->html .= $ibforums->adskin->start_table( "Current Emoticon Folders" );

		foreach( $emodirs as $dir )
		{
			$files = $this->emoticon_get_folder_contents( $dir );
			$count = intval( count($files) );

			if ( is_writeable( './style_emoticons/'.$dir ) )
			{
				$icon     = 'icon_can_write.gif';
				$title    = 'This folder is writeable and new emoticons can be added';
				$checkbox = "<input type='checkbox' name='dir_{$dir}' value='1' />";
			}
			else
			{
				$icon     = 'icon_cannot_write.gif';
				$title    = 'This folder is NOT writeable and the CHMOD must be changed';
				$checkbox = "-";
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
														  "<center><img src='{$ibforums->skin_url}/emoticon_folder.gif' border='0'></center>",
														  "<table width='100%' border='0'>
														  	<tr>
														  	 <td width='99%' align='left'><a href='{$ibforums->base_url}&act=admin&code=emo_manage&id={$dir}' title='Manage this emoticon set'><b>{$dir}</b></a></td>
														  	 <td width='1%' align='right'><img src='{$ibforums->skin_url}/{$icon}' title='{$title}' alt='$icon' /></td>
														  	</tr>
														  </table>",
														  "<center>{$checkbox}</center>",
														  "<center>{$count}</center>",
														  "<center>".intval($emo_db[ $dir ]['count'])."</center>",
														  "<center><input type='button' class='realbutton' value='Edit' onclick=\"editfolder('$dir');\" />&nbsp;".
														  $ibforums->adskin->js_make_button("Manage"       , $ibforums->base_url."&act=admin&code=emo_manage&id={$dir}").'&nbsp;'.
														  $ibforums->adskin->js_make_button("Remove"       , $ibforums->base_url."&act=admin&code=emo_setremove&id={$dir}")
														  ."</center>"
												           )      );
		}

		$ibforums->html .= $ibforums->adskin->add_td_row( array( array(  "<div align='center'><input type='button' class='realbutton' value='Create New Folder' onclick=\"addfolder();\" /></div>", 6, 'pformstrip' )  ) );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->page_detail = "You may add/edit or remove emoticons in this section.";
		$ibforums->admin->page_title   = "Emoticon Control";

		if ( SAFE_MODE_ON )
		{
			$ibforums->admin->page_detail .= "<b>SAFE MODE ON:</b> The upload functions will not operate as you are running PHP in safe mode.";
			$ibforums->html .= "</form>";
		}
		else
		{
			$ibforums->html .= "<div class='tableborder'>
								 <div class='maintitle'>Upload Emoticons</div>
								 <table width='100%' border='0' cellpadding='4' cellspacing='0'>
								 <tr>
								  <td width='50%' class='tdrow1' align='center'><input type='file' value='{$_POST['upload_1']}' class='realbutton' name='upload_1' size='30' /></td>
								  <td width='50%' class='tdrow2' align='center'><input type='file' class='realbutton' name='upload_2' size='30' /></td>
								 </tr>
								 <tr>
								  <td width='50%' class='tdrow1' align='center'><input type='file' class='realbutton' name='upload_3' size='30' /></td>
								  <td width='50%' class='tdrow2' align='center'><input type='file' class='realbutton' name='upload_4' size='30' /></td>
								 </tr>
								 </table>
								 <div class='pformstrip' align='center'><input type='submit' value='Upload emoticons into checked folders' class='realdarkbutton' /></form></div>
								</div>";
		}

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// EMOTICON Remove set
	//-----------------------------------------

	function emoticon_setremove()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "Remove an IPB emoticon pack.";
		$ibforums->admin->page_title  = "Emoticon Management";

		if ( ! $ibforums->input['id'] )
		{
			$ibforums->main_msg = "No emoticon set was passed.";
			$this->emoticon_start();
		}

		//-----------------------------------------
		// Splash or mash?
		//-----------------------------------------

		if ( $ibforums->input['do'] )
		{
			//-----------------------------------------
			// Do it
			//-----------------------------------------

			if ( $ibforums->input['emo_set'] == 'd' )
			{
				$ibforums->admin->rm_dir( CACHE_PATH.'style_emoticons/'.$ibforums->input['id'] );
				$DB->simple_exec_query( array( 'delete' => 'emoticons', 'where' => "emo_set='{$ibforums->input['id']}'" ) );
			}
			else
			{
				$ibforums->admin->copy_dir( CACHE_PATH.'style_emoticons/'.$ibforums->input['id'], CACHE_PATH.'style_emoticons/'.$ibforums->input['emo_set'] );
				$ibforums->admin->rm_dir( CACHE_PATH.'style_emoticons/'.$ibforums->input['id'] );

				//-----------------------------------------
				// Merge, don't overwrite
				//-----------------------------------------

				$old_emo_db = array();

				$DB->simple_construct( array( 'select' => '*', 'from' => 'emoticons', 'where' => "emo_set='".$ibforums->input['id']."'" ) );
				$DB->simple_exec();

				while( $r = $DB->fetch_row() )
				{
					$old_emo_db[ $r['image'] ] = $r;
				}

				$new_emo_db = array();
				$new_typed  = array();
				$new_image  = array();

				$DB->simple_construct( array( 'select' => '*', 'from' => 'emoticons', 'where' => "emo_set='".$ibforums->input['emo_set']."'" ) );
				$DB->simple_exec();

				while( $r = $DB->fetch_row() )
				{
					$new_emo_db[ $r['image'] ] = $r;
					$new_typed[ $r['typed'] ]  = 1;
					$new_image[ $r['image'] ]  = 1;
				}

				$keep_ids   = array();
				$delete_ids = array();

				foreach( $old_emo_db as $image => $data )
				{
					if ( $new_image[ $image ] or $new_typed[ $data['typed'] ] )
					{
						$delete_ids[] = $data['id'];
					}
					else
					{
						$keep_ids[] = $data['id'];
					}
				}

				//-----------------------------------------
				// Delete...
				//-----------------------------------------

				if ( count($delete_ids) )
				{
					$DB->simple_exec_query( array( 'delete' => 'emoticons', 'where' => "id IN(".implode(",",$delete_ids).")" ) );
				}

				//-----------------------------------------
				// Keep...
				//-----------------------------------------

				if ( count($keep_ids) )
				{
					$DB->do_update( 'emoticons', array( 'emo_set' => $ibforums->input['emo_set'] ), "id IN(".implode(",",$keep_ids).")" );
				}
			}

			$this->emoticon_rebuildcache();

			$ibforums->main_msg = "Emoticon folder removed.";
			$this->emoticon_start();
		}
		else
		{
			//-----------------------------------------
			// Get emoticon folders
			//-----------------------------------------

			$emodirs = array();
			$emodd   = array( 0=> array( 'd', "Don't move, just delete" ) );

			$dh = opendir( CACHE_PATH.'style_emoticons' );

			while ( $file = readdir( $dh ) )
			{
				if (($file != ".") && ($file != ".."))
				{
					if ( is_dir(CACHE_PATH.'style_emoticons/'.$file) )
					{
						if ( $file != $ibforums->input['id'] )
						{
							$emodirs[] = $file;
							$emodd[]   = array( $file, "Move to: ".$file );
						}
					}
				}
			}

			closedir( $dh );

			if ( count ( $emodirs ) < 1 )
			{
				$ibforums->main_msg = "CANNOT REMOVE: This is the last emoticon group and cannot be removed";
				$this->emoticon_start();
			}

			//-----------------------------------------
			// Show form
			//-----------------------------------------

			$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );
			$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );

			//-----------------------------------------
			// EXPORT: Start output
			//-----------------------------------------

			$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'emo_setremove' ),
																	 2 => array( 'act'   , 'admin'         ),
																	 3 => array( 'id'    , $ibforums->input['id'] ),
																	 4 => array( 'do'    , 1               ),
															)      );

			$ibforums->html .= $ibforums->adskin->start_table( "REMOVING EMOTICON SET: ".$ibforums->input['id'] );

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
																  "<b>Move or delete remaining emoticons in this set?</b><div style='color:gray'>If you're moving them to an existing group, only non-duplicate images and activation words will be kept.</div>",
																  $ibforums->adskin->form_dropdown( 'emo_set', $emodd )
														   )      );


			$ibforums->html .= $ibforums->adskin->end_form("REMOVE EMOTICON GROUP");

			$ibforums->html .= $ibforums->adskin->end_table();


			$ibforums->admin->output();
		}
	}

	//-----------------------------------------
	// EMOTICON Import/Export Pack Splash
	//-----------------------------------------

	function emoticon_pack_splash()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "Export or import IPB emoticon packs.";
		$ibforums->admin->page_title  = "Emoticon Management";

		if ( ! is_dir( CACHE_PATH. 'style_emoticons') )
		{
			$ibforums->admin->error("Could not locate the emoticons directory - make sure the 'style_emoticons' path is set correctly");
			$ibforums->admin->output();
		}

		//-----------------------------------------
		// Get emoticon count
		//-----------------------------------------

		$DB->cache_add_query( 'admin_emo_count', array() );
		$DB->cache_exec_query();

		while( $r = $DB->fetch_row() )
		{
			$emo_db[ $r['emo_set'] ] = $r;
		}

		//-----------------------------------------
		// Get emoticon folders
		//-----------------------------------------

		$emodirs = array();
		$emodd   = array();

		$dh = opendir( CACHE_PATH.'style_emoticons' );

 		while ( $file = readdir( $dh ) )
 		{
 			if (($file != ".") && ($file != ".."))
 			{
				if ( is_dir(CACHE_PATH.'style_emoticons/'.$file) )
				{
					$emodirs[] = $file;
					$emodd[]   = array( $file, $file );
				}
 			}
 		}
 		closedir( $dh );

		//-----------------------------------------
		// EXPORT: Start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );

		//-----------------------------------------
		// EXPORT: Start output
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'emo_packexport' ),
															     2 => array( 'act'   , 'admin'      ),
													    )      );

		$ibforums->html .= $ibforums->adskin->start_table( "Export an Emoticon Pack" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													            "<b>Export which emoticon group?</b><div style='color:gray'>An IPB Emoticon Pack is an XMLarchive of the images and activation words (i.e. :smile:)</div>",
													            $ibforums->adskin->form_dropdown( 'emo_set', $emodd )
													   )      );


		$ibforums->html .= $ibforums->adskin->end_form("Export");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// IMPORT: Start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );

		//-----------------------------------------
		// IMPORT: Start output
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'emo_packimport' ),
																 2 => array( 'act'   , 'admin'      ),
																 3 => array( 'MAX_FILE_SIZE', '10000000000' ),
														) , "uploadform", " enctype='multipart/form-data'"     );


		$ibforums->html .= $ibforums->adskin->start_table( "Import an Emoticon Pack" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													  		"<b>Import into which emoticon group?</b><div style='color:gray'>An IPB Emoticon Pack is an XMLarchive of the images and activation words (i.e. :smile:)</div>",
													  		$ibforums->adskin->form_dropdown( 'emo_set', $emodd )
													   )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													  		"<b><u>OR</u> Import into a new group named:</b><div style='color:gray'>Enter the name of the new emoticon group.</div>",
													  		$ibforums->adskin->form_input( 'new_emo_set' )
													   )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													 		 "<b>Overwrite existing images and activation words?</b><div style='color:gray'>If yes, new images replace old</div>",
													  		$ibforums->adskin->form_yes_no( 'overwrite' )
													   )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													 		 "<b>Upload XML Emoticon Archive</b><div style='color:gray'>Browse your computer for 'ipb_emoticons.xml' or 'ipb_emoticons.xml.gz'</div>",
													  		$ibforums->adskin->form_upload(  )
													   )      );

		$ibforums->html .= $ibforums->adskin->end_form("Import");

		$ibforums->html .= $ibforums->adskin->end_table();


		$ibforums->admin->output();

	}

	//-----------------------------------------
	// EMOTICON EXPORT! EXPORT! EX... oh, that's abort isn't it?
	//-----------------------------------------

	function emoticon_pack_export()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		require( KERNEL_PATH.'class_xmlarchive.php' );

		$xmlarchive = new class_xmlarchive();

		//-----------------------------------------
		// Checkdamoonah
		//-----------------------------------------

		if ( ! $ibforums->input['emo_set'] )
		{
			$this->ibforums->msg = "You must specify which emoticon group you wish to export";
		}

		//-----------------------------------------
		// Get emowticuns
		//-----------------------------------------

		$emo_db = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'emoticons', 'where' => "emo_set='".$ibforums->input['emo_set']."'" ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$emo_db[ $r['image'] ] = $r;
		}

		//-----------------------------------------
		// Get ;) :D folders
		//-----------------------------------------

		$emodirs = array();
		$emodd   = array();

		$dh = opendir( CACHE_PATH.'style_emoticons/'.$ibforums->input['emo_set'] );

 		while ( $file = readdir( $dh ) )
 		{
 			if (($file != ".") && ($file != ".."))
 			{
 				if ( $emo_db[ $file ] != "" )
 				{
					$files_to_add[] = CACHE_PATH.'style_emoticons/'.$ibforums->input['emo_set'].'/'.$file;
				}
 			}
 		}

 		closedir( $dh );

		//-----------------------------------------
		// Add um into the ark-hive
		//-----------------------------------------

		foreach( $files_to_add as $f )
		{
			$xmlarchive->xml_add_file( $f );
		}

		//-----------------------------------------
		// Create the database archive...
		//-----------------------------------------

		$xml->xml_set_root( 'emoticonexport', array( 'exported' => time(), 'name' => $ibforums->input['emo_set'] ) );

		//-----------------------------------------
		// Get emo group
		//-----------------------------------------

		$xml->xml_add_group( 'emogroup' );

		foreach( $emo_db as $i => $r )
		{
			$content = array();

			$content[] = $xml->xml_build_simple_tag( 'typed'    , $r['typed'] );
			$content[] = $xml->xml_build_simple_tag( 'image'    , $r['image'] );
			$content[] = $xml->xml_build_simple_tag( 'clickable', $r['clickable'] );

			$entry[] = $xml->xml_build_entry( 'emoticon', $content );
		}

		$xml->xml_add_entry_to_group( 'emogroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Add in emoticons doc to archive
		//-----------------------------------------

		$xmlarchive->xml_add_file_contents( $xml->xml_document, 'emoticon_data.xml' );

		$xmlarchive->xml_create_archive();

		//-----------------------------------------
		// Create archive and send to
		// browser.
		//-----------------------------------------

		$imagearchive = $xmlarchive->xml_get_contents();

		$ibforums->admin->show_download( $imagearchive, 'ipb_emoticons.xml' );

	}


	//-----------------------------------------
	// IMPORT THE EMOTICONS
	//-----------------------------------------

	function emoticon_pack_import()
	{
		global $ibforums, $DB,  $std;

		$content = $ibforums->admin->import_xml( 'ipb_emoticons.xml' );

		//-----------------------------------------
		// Got anything?
		//-----------------------------------------

		if ( ! $content )
		{
			$ibforums->main_msg = "Upload failed, ipb_emoticons.xml was either missing or empty";
			$this->emoticon_pack_splash();
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		require( KERNEL_PATH.'class_xmlarchive.php' );

		$xmlarchive = new class_xmlarchive();

		$xmlarchive->xml_read_archive_data( $content );

		//-----------------------------------------
		// Get the datafile
		//-----------------------------------------

		$emoticons     = array();
		$emoticon_data = array();

		foreach( $xmlarchive->file_array as $f )
		{
			if ( $f['filename'] == 'emoticon_data.xml' )
			{
				$emoticon_data = $f['content'];
			}
			else
			{
				$emoticons[ $f['filename'] ] = $f['content'];
			}
		}

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $emoticon_data );

		//-----------------------------------------
		//  New set, old set - we're set!
		//-----------------------------------------

		if ( ! $ibforums->input['emo_set'] and ! $ibforums->input['new_emo_set'] )
		{
			$this->ibforums->msg = "You must specify which emoticon group you wish to import into";
		}

		$emo_set_dir = $ibforums->input['emo_set'];

		$ibforums->input['new_emo_set'] = preg_replace( "/[^a-zA-Z0-9\-_]/", "",$ibforums->input['new_emo_set'] );

		if ( $ibforums->input['new_emo_set'] )
		{
			$emo_set_dir = $ibforums->input['new_emo_set'];

			//-----------------------------------------
			// Directory exists?
			//-----------------------------------------

			if ( file_exists( CACHE_PATH.'style_emoticons/'.$emo_set_dir ) )
			{
				$ibforums->main_msg = "'style_emoticons/$emo_set_dir' already exists, please choose another name.";
				$this->emoticon_pack_splash();
			}

			//-----------------------------------------
			// Create directory?
			//-----------------------------------------

			if ( @mkdir( CACHE_PATH.'style_emoticons/'.$emo_set_dir, 0777 ) )
			{
				@chmod( CACHE_PATH.'style_emoticons/'.$emo_set_dir, 0777 );
			}
			else
			{
				$ibforums->main_msg = "IPB cannot create a new folder for you, please create the folder manually using FTP in 'style_emoticons'";
				$this->emoticon_pack_splash();
			}
		}

		//-----------------------------------------
		// Are we over writing?
		//-----------------------------------------

		$emo_image = array();
		$emo_typed = array();

		if ( $ibforums->input['overwrite'] != 1  )
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'emoticons', 'where' => "emo_set='".$emo_set_dir."'" ) );
			$DB->simple_exec();

			while( $r = $DB->fetch_row() )
			{
				$emo_image[ $r['image'] ] = 1;
				$emo_typed[ $r['typed'] ] = 1;
			}
		}

		foreach( $xml->xml_array['emoticonexport']['emogroup']['emoticon'] as $idx => $entry )
		{
			$image = $entry['image']['VALUE'];
			$typed = $entry['typed']['VALUE'];
			$click = $entry['clickable']['VALUE'];

			if ( $emo_image[ $image ] or $emo_typed[ $typed ] )
			{
				continue;
			}

			@unlink( CACHE_PATH.'style_emoticons/'.$emo_set_dir.'/'.$image );

			$DB->simple_exec_query( array( 'delete' => 'emoticons', 'where' => "typed='$typed' and image='$image' and emo_set='$emo_set_dir'" ) );

			if ( $FH = fopen( CACHE_PATH.'style_emoticons/'.$emo_set_dir.'/'.$image, 'wb' ) )
			{
				if ( fwrite( $FH, $emoticons[ $image ] ) )
				{
					fclose( $FH );

					$DB->do_insert( 'emoticons', array( 'typed' => $typed, 'image' => $image, 'clickable' => $click, 'emo_set' => $emo_set_dir ) );
				}
			}
		}

		$this->emoticon_rebuildcache();

		$ibforums->main_msg = "Emoticon XMLarchive import completed";

		$this->emoticon_start();

	}


	//-----------------------------------------
	// EMOTICON Manage
	//-----------------------------------------

	function emoticon_manage()
	{
		global $ibforums, $DB,  $std;

		$ibforums->input['id'] = trim($ibforums->input['id']);

		$ibforums->admin->nav[] = array( 'act=admin&code=emo', 'Emoticons Manager' );
		$ibforums->admin->nav[] = array( '', 'Managing Set '.$ibforums->input['id'] );

		$ibforums->admin->page_detail = "You may add/edit or remove emoticons in this section.<br>Clickable refers to emoticons that are in the posting screens 'Clickable Emoticons' table.";
		$ibforums->admin->page_title  = "Emoticon Control";

		//-----------------------------------------
		// Get emoticons for this group
		//-----------------------------------------

		$emo_db   = array();
		$emo_file = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'emoticons', 'where' => "emo_set='".$ibforums->input['id']."'", 'order' => 'clickable DESC' ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$emo_db[ $r['image'] ] = $r;
		}

		$emo_file  = array();
		$emo_rfile = $this->emoticon_get_folder_contents( $ibforums->input['id'] );

		foreach( $emo_rfile as $ef )
		{
			$emo_file[ $ef ] = $ef;
		}

		//-----------------------------------------
		// Start output
		//-----------------------------------------

		$per_row  = 5;
		$td_width = 100 / $per_row;

		$ibforums->html .= "<div class='tableborder'>
							 <div class='maintitle'>Assigned Emoticons in set '{$ibforums->input['id']}'</div>
							 <form action='{$ibforums->base_url}&act=admin&code=emo_doedit&id={$ibforums->input['id']}' method='post'>
							 <table cellpadding='4' cellspacing='0' border='0' width='100%'>
						   ";

		$count      = 0;
		$smilies    = "<tr align='center'>\n";
		$poss_names = array();

		foreach( $emo_db as $image => $data )
		{
			$count++;

			unset( $emo_file[ $image ] );

			if ( $data['clickable'] )
			{
				$click = 'checked="checked"';
				$class = 'tdrow1';
			}
			else
			{
				$click = '';
				$class = 'tdrow2';
			}

			$smilies .= "<td width='{$td_width}%' align='center' class='$class'>
						  <fieldset>
						  	<legend><strong>{$image}</strong></legend>
						  	<img src='style_emoticons/{$ibforums->input['id']}/{$image}' border='0' />&nbsp;&nbsp;&nbsp;&nbsp;<a href='{$ibforums->base_url}&act=admin&code=emo_remove&eid={$data['id']}&id={$ibforums->input['id']}' title='Remove this emoticon'><img src='{$ibforums->skin_url}/emo_delete.gif' border='0' alt='Delete' /></a>
						  	<br />
						  	<input type='textinput' class='realbutton' size='10' name='emo_type_{$data['id']}' value='{$data['typed']}' />
						  	<br /><br />Clickable? <input type='checkbox'  name='emo_click_{$data['id']}' value='1' {$click} />
						  </fieldset>
						 </td>";

			if ($count == $per_row )
			{
				$smilies .= "</tr>\n\n<tr align='center'>";
				$count = 0;
			}

			$poss_names[$data['typed']] = $data['typed'];
		}

		if ( $count > 0 and $count != $per_row )
		{
			for ($i = $count ; $i < $per_row ; ++$i)
			{
				$smilies .= "<td class='tdrow2'>&nbsp;</td>\n";
			}

			$smilies .= "</tr>";
		}


		$ibforums->html .= $smilies;

		$ibforums->html .= "</table>
							<div class='pformstrip' align='center'><input type='submit' class='realbutton' value='Update Emoticons' /></form></div></div><br />";


		//-----------------------------------------
		// Images left in the dir?
		//-----------------------------------------

		if ( count( $emo_file ) )
		{
			$ibforums->html .= "<div class='tableborder'>
								<div class='maintitle'>Unassigned images in folder '{$ibforums->input['id']}'</div>
								<form action='{$ibforums->base_url}&act=admin&code=emo_doadd&id={$ibforums->input['id']}' method='post'>
								<table cellpadding='4' cellspacing='0' border='0' width='100%'>
							  ";

			$count   = 0;
			$smilies = "<tr align='center'>\n";

			$master_count = 0;

			foreach( $emo_file as $image )
			{
				$count++;
				$master_count++;

				$poss_name = ':'.preg_replace( "/(.*)(\..+?)$/", "\\1", $image ).':';

				if ( $poss_names[ $poss_name ] )
				{
					$poss_name = preg_replace( "/:$/", "2:", $poss_name );
				}

				$smilies .= "<td width='{$td_width}%' align='center' class='tdrow1'>
							  <fieldset>
								<legend><strong>{$image}</strong></legend>
								<img src='style_emoticons/{$ibforums->input['id']}/{$image}' border='0' />&nbsp;&nbsp;<b>Add</b> <input name='emo_add_{$master_count}' type='checkbox' value='1' />
								<br />
								Type: <input type='textinput' class='realbutton' size='10' name='emo_type_{$master_count}' value='$poss_name' />
								<br /><br />Clickable? <input type='checkbox' name='emo_click_{$master_count}' value='1' />
								<input type='hidden' name='emo_image_{$master_count}' value='{$image}' />
							  </fieldset>
							 </td>";

				if ($count == $per_row )
				{
					$smilies .= "</tr>\n\n<tr align='center'>";
					$count = 0;
				}
			}

			if ( $count > 0 and $count != $per_row )
			{
				for ($i = $count ; $i < $per_row ; ++$i)
				{
					$smilies .= "<td class='tdrow1'>&nbsp;</td>\n";
				}

				$smilies .= "</tr>";
			}


			$ibforums->html .= $smilies;

			$ibforums->html .= "</table>
								<div class='pformstrip' align='center'><input type='submit' class='realbutton' value='Add Checked Emoticons' />&nbsp;&nbsp;<input type='submit' name='addall' class='realbutton' value='Add All Emoticons' /></form></div></div>";
		}

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// EMOTICON Rebuild Cache
	//-----------------------------------------

	function emoticon_rebuildcache()
	{
		global $ibforums, $std, $DB;

		$ibforums->cache['emoticons'] = array();

		$DB->simple_construct( array( 'select' => 'typed,image,clickable,emo_set', 'from' => 'emoticons' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['emoticons'][] = $r;
		}

		$std->update_cache( array( 'name' => 'emoticons', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	// EMOTICON Get folder contents
	//-----------------------------------------

	function emoticon_get_folder_contents($folder='default')
	{
		global $ibforums, $std, $DB;

		$files = array();

		//-----------------------------------------
		// Get emoticon folders
		//-----------------------------------------

		$dh = opendir( CACHE_PATH.'style_emoticons/'.$folder );

 		while ( $file = readdir( $dh ) )
 		{
 			if ( ($file != ".") && ($file != "..") )
 			{
				if ( preg_match( "/\.(?:gif|jpg|jpeg|png|swf)$/i", $file ) )
				{
					$files[] = $file;
				}
 			}
 		}

 		closedir( $dh );

 		return $files;
 	}

 	//-----------------------------------------
	// BBCODE: Import
	//-----------------------------------------

	function bbcode_import()
	{
		global $ibforums, $DB,  $std;

		$content = $ibforums->admin->import_xml( 'ipb_bbcode.xml' );

		//-----------------------------------------
		// Got anything?
		//-----------------------------------------

		if ( ! $content )
		{
			$ibforums->main_msg = "Upload failed, ipb_bbcode.xml was either missing or empty";
			$this->bbcode_start();
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
		// Get current badwords
		//-----------------------------------------

		$tags = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$tags[ $r['bbcode_tag'] ] = 1;
		}

		//-----------------------------------------
		// pArse
		//-----------------------------------------

		if ( ! is_array( $xml->xml_array['bbcodeexport']['bbcodegroup']['bbcode'][0]  ) )
		{
			$xml->xml_array['bbcodeexport']['bbcodegroup']['bbcode'][0] = $xml->xml_array['bbcodeexport']['bbcodegroup']['bbcode'];
		}

		foreach( $xml->xml_array['bbcodeexport']['bbcodegroup']['bbcode'] as $idx => $entry )
		{
			$bbcode_title     = $entry['bbcode_title']['VALUE'];
			$bbcode_desc      = $entry['bbcode_desc']['VALUE'];
			$bbcode_tag       = $entry['bbcode_tag']['VALUE'];
			$bbcode_replace   = $entry['bbcode_replace']['VALUE'];
			$bbcode_useoption = $entry['bbcode_useoption']['VALUE'];
			$bbcode_example   = $entry['bbcode_example']['VALUE'];

			if ( $tags[ $bbcode_tag ] )
			{
				continue;
			}

			if ( $bbcode_tag )
			{
				$bbarray = array(
								 'bbcode_title'     => $bbcode_title,
								 'bbcode_desc'      => $bbcode_desc,
								 'bbcode_tag'       => $bbcode_tag,
								 'bbcode_replace'   => $std->txt_safeslashes($bbcode_replace),
								 'bbcode_useoption' => $bbcode_useoption,
								 'bbcode_example'   => $bbcode_example,
								);

				$DB->do_insert( 'custom_bbcode', $bbarray );
			}
		}

		$this->bbcode_rebuildcache();

		$ibforums->main_msg = "BBCode XML file import completed";

		$this->bbcode_start();

	}

	//-----------------------------------------
	// BBCODE: Export
	//-----------------------------------------

	function bbcode_export()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Start...
		//-----------------------------------------

		$xml->xml_set_root( 'bbcodeexport', array( 'exported' => time() ) );

		//-----------------------------------------
		// Get emo group
		//-----------------------------------------

		$xml->xml_add_group( 'bbcodegroup' );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$content = array();

			foreach ( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}

			$entry[] = $xml->xml_build_entry( 'bbcode', $content );
		}

		$xml->xml_add_entry_to_group( 'bbcodegroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$ibforums->admin->show_download( $xml->xml_document, 'ipb_bbcode.xml' );
	}

	//-----------------------------------------
	// BBCODE Remove
	//-----------------------------------------

	function bbcode_delete()
	{
		global $ibforums, $DB, $std;

		if ( ! $ibforums->input['id'] )
		{
			$ibforums->main_msg = "No ID was passed, please try again.";
			$this->bbcode_start();
		}

		$DB->simple_exec_query( array( 'delete' => 'custom_bbcode', 'where' => 'bbcode_id='.$ibforums->input['id'] ) );

		$this->bbcode_rebuildcache();

		$this->bbcode_start();
	}

	//-----------------------------------------
	// BBCODE Rebuild Cache
	//-----------------------------------------

	function bbcode_rebuildcache()
	{
		global $ibforums, $DB, $std;

		$ibforums->cache['bbcode'] = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode' ) );
		$bbcode = $DB->simple_exec();

		while ( $r = $DB->fetch_row($bbcode) )
		{
			$ibforums->cache['bbcode'][] = $r;
		}

		$std->update_cache( array( 'name' => 'bbcode', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	// BBCODE Test
	//-----------------------------------------

	function bbcode_test()
	{
		global $ibforums, $DB, $std;

		$DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode', 'order' => 'bbcode_title' ) );
		$DB->simple_exec();

		$t = $_POST['bbtest'];

		while ( $row = $DB->fetch_row() )
		{
			if ( substr_count( $row['bbcode_replace'], '{content}' ) > 1 )
			{
				//-----------------------------------------
				// Slightly slower
				//-----------------------------------------

				if ( $row['bbcode_useoption'] )
				{
					preg_match_all( "#(\[".$row['bbcode_tag']."=(?:&quot;|&\#39;)?(.+?)(?:&quot;|&\#39;)?\])(.+?)(\[/".$row['bbcode_tag']."\])#si", $t, $match );

					for ($i=0; $i < count($match[0]); $i++)
					{
						$tmp = $row['bbcode_replace'];
						$tmp = str_replace( '{option}' , $match[2][$i], $tmp );
						$tmp = str_replace( '{content}', $match[3][$i], $tmp );
						$t   = str_replace( $match[0][$i], $tmp, $t );
					}
				}
				else
				{
					preg_match_all( "#(\[".$row['bbcode_tag']."\])(.+?)(\[/".$row['bbcode_tag']."\])#si", $t, $match );

					for ($i=0; $i < count($match[0]); $i++)
					{
						$tmp = $row['bbcode_replace'];
						$tmp = str_replace( '{content}', $match[2][$i], $tmp );
						$t   = str_replace( $match[0][$i], $tmp, $t );
					}
				}
			}
			else
			{
				$replace = explode( '{content}', $row['bbcode_replace'] );

				if ( $row['bbcode_useoption'] )
				{
					$t = preg_replace( "#\[".$row['bbcode_tag']."=(?:&quot;|&\#39;)?(.+?)(?:&quot;|&\#39;)?\]#si", str_replace( '{option}', "\\1", $replace[0] ), $t );
				}
				else
				{
					$t = preg_replace( '#\['.$row['bbcode_tag'].'\]#i' , $replace[0], $t );
				}

				$t = preg_replace( '#\[/'.$row['bbcode_tag'].'\]#i', $replace[1], $t );
			}
		}

		$ibforums->main_msg = "<b>BBCode Test:</b><br /><br />".$t;

		$this->bbcode_start();
	}

	//-----------------------------------------
	// BBCODE Save Form
	//-----------------------------------------

	function bbcode_save($type='add')
	{
		global $ibforums, $DB, $std;

		if ( $type == 'edit' )
		{
			if ( ! $ibforums->input['id'] )
			{
				$ibforums->main_msg = "No ID was passed, please try again";
				$this->bbcode_form($type);
			}
		}

		//-----------------------------------------
		// check...
		//-----------------------------------------

		if ( ! $ibforums->input['bbcode_title'] or ! $ibforums->input['bbcode_tag'] or ! $ibforums->input['bbcode_replace'] )
		{
			$ibforums->main_msg = "You must complete the form fully.";
			$this->bbcode_form($type);
		}

		if ( ! strstr( $ibforums->input['bbcode_replace'], '{content}' ) )
		{
			$ibforums->main_msg = "You must use {content} somewhere in the BBCode replacement section.";
			$this->bbcode_form($type);
		}

		if ( ! strstr( $ibforums->input['bbcode_replace'], '{option}' ) AND $ibforums->input['bbcode_useoption'] )
		{
			$ibforums->main_msg = "You must use {option} somewhere in the BBCode replacement section or set 'Use Option in tag?' to 'no'.";
			$this->bbcode_form($type);
		}

		$array = array( 'bbcode_title'     => $ibforums->input['bbcode_title'],
						'bbcode_desc'      => $std->txt_safeslashes( $_POST['bbcode_desc'] ),
						'bbcode_tag'       => $ibforums->input['bbcode_tag'],
						'bbcode_replace'   => $std->txt_safeslashes( $_POST['bbcode_replace'] ),
						'bbcode_example'   => $std->txt_safeslashes( $_POST['bbcode_example'] ),
						'bbcode_useoption' => $ibforums->input['bbcode_useoption'] );

		if ( $type == 'add' )
		{
			$DB->do_insert( 'custom_bbcode', $array );
			$ibforums->main_msg = 'New BBCode Added';
		}
		else
		{
			$DB->do_update( 'custom_bbcode', $array, 'bbcode_id='.$ibforums->input['id'] );
			$ibforums->main_msg = 'Custom BBCode Edited';
		}

		$this->bbcode_rebuildcache();

		$this->bbcode_start();

	}


	//-----------------------------------------
	// BBCODE Start Form
	//-----------------------------------------

	function bbcode_form($type='add')
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_detail = "The BBCode manager allows you to add new custom BBCode.";
		$ibforums->admin->page_title  = "BBCode Manager";

		if ( $type == 'edit' )
		{
			if ( ! $ibforums->input['id'] )
			{
				$ibforums->main_msg = "No ID was passed, please try again";
				$this->bbcode_start();
			}

			$bbcode = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'custom_bbcode', 'where' => 'bbcode_id='.$ibforums->input['id'] ) );

			$button = "Edit BBCode";
			$code   = 'bbcode_doedit';
			$title  = "Editing BBCode: ".$bbcode['bbcode_title'];
		}
		else
		{
			$bbcode = array();
			$code   = 'bbcode_doadd';
			$title  = "Adding a new custom BBCode";
			$button = "Add BBCode";
		}

		//-----------------------------------------
		// Show the codes mahn!
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'admin'    ),
															   2 => array( 'code' , $code      ),
															   3 => array( 'id'   , $ibforums->input['id'] )
													  )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( $title );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Custom BBCode Title</b><div style='color:gray'>Used on the BBCode pop-up legend</div>",
															   $ibforums->adskin->form_input( 'bbcode_title', $ibforums->input['bbcode_title'] ? $ibforums->input['bbcode_title'] : $bbcode['bbcode_title'] )
													 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Custom BBCode Description</b><div style='color:gray'>Used on the BBCode pop-up legend</div>",
															   $ibforums->adskin->form_textarea( 'bbcode_desc', $ibforums->input['bbcode_desc'] ? $ibforums->input['bbcode_desc'] : $bbcode['bbcode_desc'] )
													 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Custom BBCode Example</b><div style='color:gray'>Used on the BBCode pop-up legend<br />Use the tag in the example: [tag]This is an example![/tag]</div>",
															   $ibforums->adskin->form_textarea( 'bbcode_example', $ibforums->input['bbcode_example'] ? $ibforums->input['bbcode_example'] : $bbcode['bbcode_example'] )
													 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Custom BBCode Tag</b><div style='color:gray'>Example: For [tag] enter <b>tag</b></div>",
															   '[ '.$ibforums->adskin->form_simple_input( 'bbcode_tag', $ibforums->input['bbcode_tag'] ? $ibforums->input['bbcode_tag'] : $bbcode['bbcode_tag'], 10).' ]'
													 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Use Option in tag?</b><div style='color:gray'>Use to create [tag=option] style tags</div>",
															   $ibforums->adskin->form_yes_no( 'bbcode_useoption', $ibforums->input['bbcode_useoption'] ? $ibforums->input['bbcode_useoption'] : $bbcode['bbcode_useoption'] )
													 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Custom BBCode Replacement</b><div style='color:gray'>&lt;tag&gt;{content}&lt;/tag&gt;<br />&lt;tag thing='{option}'&gt;{content}&lt;/tag&gt;</div>",
															   $ibforums->adskin->form_textarea( 'bbcode_replace', $ibforums->input['bbcode_replace'] ? $ibforums->input['bbcode_replace'] : $bbcode['bbcode_replace'] )
													 )      );


		$ibforums->html .= $ibforums->adskin->end_form( $button );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= "<br /><div class='tableborder'><div class='tdrow1' style='padding:6px'><b>More Information</b><br />When adding the BBCode replacement, don't forget to add the {content} block where you wish the tag content to go when parsed.<br />
						    If you are using an option <b>[tag=option][/tag]</b> tag, don't forget to add in {option} in the BBCode replacement where you want the option to go.</div></div>";

		//-----------------------------------------

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// BBCODE Splash
	//-----------------------------------------

	function bbcode_start()
	{
		global $ibforums, $DB, $std;

		$ibforums->admin->page_detail = "The BBCode manager allows you to add new custom BBCode.";
		$ibforums->admin->page_title  = "BBCode Manager";

		//-----------------------------------------
		// Show the codes mahn!
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Title"    , "30%" );
		$ibforums->adskin->td_header[] = array( "Tag"      , "40%" );
		$ibforums->adskin->td_header[] = array( "Options"  , "30%" );

		$export_button = $ibforums->adskin->js_make_button("Export BBCode", $ibforums->base_url."&act=admin&code=bbcode_export");

		$table = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
				  <td align='left' width='100%' style='font-weight:bold;font-size:11px;color:#FFF'>Your custom BBCodes</td>
				  <td align='right' nowrap='nowrap' style='padding-right:2px'>{$export_button}</td>
				  </tr>
				  </table>";

		$ibforums->html .= $ibforums->adskin->start_table( $table );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode', 'order' => 'bbcode_title' ) );
		$DB->simple_exec();

		while ( $row = $DB->fetch_row() )
		{
			if ( $row['bbcode_useoption'] )
			{
				$option = '={option}';
			}
			else
			{
				$option = '';
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$row['bbcode_title']."</b>",
																	 '['.$row['bbcode_tag'].$option.']{content}[/'.$row['bbcode_tag'].']',
																	 "<div align='center'>".
																	  $ibforums->adskin->js_make_button("Edit"  , $ibforums->base_url."&act=admin&code=bbcode_edit&id={$row['bbcode_id']}").'&nbsp;'.
																	  $ibforums->adskin->js_make_button("Delete", $ibforums->base_url."&act=admin&code=bbcode_delete&id={$row['bbcode_id']}")
																	 ."</div>",
														   )      );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= "<div class='tableborder'><div class='pformstrip' align='center'>".$ibforums->adskin->js_make_button("Add New BBCode"  , $ibforums->base_url."&act=admin&code=bbcode_add")."</div></div>";

		$ibforums->html .= "<br />";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'admin'       ),
															     2 => array( 'code' , 'bbcode_test' ),
													  )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Test your custom BBCode" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Test your BBCode",
																$ibforums->adskin->form_textarea( 'bbtest', $_POST['bbtest'] ),
														 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Run Test");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// IMPORT: Start table
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );

		//-----------------------------------------
		// IMPORT: Start output
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'bbcode_import' ),
															   2 => array( 'act'   , 'admin'      ),
															   3 => array( 'MAX_FILE_SIZE', '10000000000' ),
													  ) , "uploadform", " enctype='multipart/form-data'"     );


		$ibforums->html .= $ibforums->adskin->start_table( "Import a BBCode List" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													 		 "<b>Upload XML BBCode List</b><div style='color:gray'>Browse your computer for 'ipb_bbcode.xml' or 'ipb_bbcode.xml.gz'. Duplicate [tag] entries will not be imported.</div>",
													  		 $ibforums->adskin->form_upload(  )
													   )      );

		$ibforums->html .= $ibforums->adskin->end_form("Import");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// DO VIEW
	//-----------------------------------------

	function view_cache()
	{
		global $ibforums, $DB, $std;

		if ( ! $ibforums->input['id'] )
		{
			$ibforums->main_msg = "No ID was passed, please try again";
			$this->cache_start();
		}

		$row = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='{$ibforums->input['id']}'" ) );

		ob_start();
		if ( $row['cs_array'] )
		{
			print_r( unserialize($std->txt_stripslashes($row['cs_value'])) );
		}
		else
		{
			print $row['cs_value'];
		}

		$out = ob_get_contents();
		ob_end_clean();

		$ibforums->html = "<pre>".htmlspecialchars($out)."</pre>";

		$ibforums->admin->print_popup();

	}


	//-----------------------------------------
	// DO UPDATE
	//-----------------------------------------

	function cache_end()
	{
		global $ibforums, $DB, $std;

		$action = "";

		foreach( $ibforums->input as $k => $v )
		{
			if ( strstr( $k, 'update_' ) and $v != "" )
			{
				$action = str_replace( 'update_', '', $k );
				break;
			}
		}

		switch ( $action )
		{
			//-----------------------------------------
			// Forum cache
			//-----------------------------------------

			case 'forum_cache':

				$std->update_forum_cache();

				$ibforums->main_msg = 'Forum Cache Updated';
				break;

			//-----------------------------------------
			// Group Cache
			//-----------------------------------------

			case 'group_cache':
				$ibforums->cache['group_cache'] = array();

				$DB->simple_construct( array( 'select' => "*",
											  'from'   => 'groups'
									 )      );

				$DB->simple_exec();

				while ( $i = $DB->fetch_row() )
				{
					$ibforums->cache['group_cache'][ $i['g_id'] ] = $i;
				}

				$std->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Group Cache Updated';
				break;

			//-----------------------------------------
			// Systemvars
			//-----------------------------------------

			case 'systemvars':
				$ibforums->cache['systemvars'] = array();

				$result = $DB->simple_exec_query( array( 'select' => 'count(*) as cnt', 'from' => 'mail_queue' ) );

				$ibforums->cache['systemvars']['mail_queue'] = intval( $result['cnt'] );

				$std->update_cache( array( 'name' => 'systemvars', 'array' => 1, 'deletefirst' => 1 ) );

				require_once( ROOT_PATH.'sources/lib/task_functions.php' );
				$task = new task_functions();
				$task->save_next_run_stamp();

				$ibforums->main_msg = 'System Variables Updated';
				break;

			//-----------------------------------------
			// Skin ID cache
			//-----------------------------------------

			case 'skin_id_cache':
				require_once( ROOT_PATH.'sources/admin/admin_cache_functions.php' );
    			$admin = new admin_cache_functions();

    			$admin->_rebuild_skin_id_cache();

				$ibforums->main_msg = 'Skin ID Cache Updated';
				break;

			//-----------------------------------------
			// Moderators
			//-----------------------------------------

			case 'moderators':

				$ibforums->cache['moderators'] = array();

				$DB->simple_construct( array( 'select' => "*",
											  'from'   => 'moderators'
									 )      );

				$DB->simple_exec();

				while ( $i = $DB->fetch_row() )
				{
					$ibforums->cache['moderators'][ $i['mid'] ] = $i;
				}

				$std->update_cache( array( 'name' => 'moderators', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Moderators Updated';
				break;

			//-----------------------------------------
			// Stats
			//-----------------------------------------

			case 'stats':




				$ibforums->main_msg = 'Statistics Updated';
				break;

			//-----------------------------------------
			// Ranks
			//-----------------------------------------

			case 'ranks':

				$ibforums->cache['ranks'] = array();

				$DB->simple_construct( array( 'select' => 'id, title, pips, posts',
											  'from'   => 'titles',
											  'order'  => "posts DESC",
									)      );

				$DB->simple_exec();

				while ($i = $DB->fetch_row())
				{
					$ibforums->cache['ranks'][ $i['id'] ] = array(
																  'TITLE' => $i['title'],
																  'PIPS'  => $i['pips'],
																  'POSTS' => $i['posts'],
																);
				}

				$std->update_cache( array( 'name' => 'ranks', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Ranks Updated';
				break;

			//-----------------------------------------
			// Profile Fields
			//-----------------------------------------

			case 'profilefields':

				$ibforums->cache['profilefields'] = array();

				$DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'order' => 'pf_position' ) );

				$DB->simple_exec();

				while ( $r = $DB->fetch_row() )
				{
					$ibforums->cache['profilefields'][ $r['pf_id'] ] = $r;
				}

				$std->update_cache( array( 'name' => 'profilefields', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Profile Fields Updated';
				break;

			//-----------------------------------------
			// Calendar
			//-----------------------------------------

			case 'calendar':

				require_once( ROOT_PATH.'sources/lib/task_functions.php' );
				$func = new task_functions();

				require_once( ROOT_PATH.'sources/tasks/calendarevents.php' );

				$task = new task_item();
				$task->register_class( $func );
				$task->restrict_log = 1;
				$task->run_task();

				$ibforums->main_msg = 'Calendar Events Updated';
				break;

			//-----------------------------------------
			// Birthdays
			//-----------------------------------------

			case 'birthdays':

				require_once( ROOT_PATH.'sources/lib/task_functions.php' );
				$func = new task_functions();

				require_once( ROOT_PATH.'sources/tasks/calendarevents.php' );

				$task = new task_item();
				$task->register_class( $func );
				$task->restrict_log = 1;
				$task->run_task();

				$ibforums->main_msg = 'Birthdays Updated';
				break;

			//-----------------------------------------
			// Multimoderation
			//-----------------------------------------

			case 'multimod':

				$ibforums->cache['multimod'] = array();

				$DB->simple_construct( array(
										 'select' => '*',
										 'from'   => 'topic_mmod',
										 'order'  => 'mm_title'
								 )      );

				$DB->simple_exec();

				while ($i = $DB->fetch_row())
				{
					$ibforums->cache['multimod'][ $i['mm_id'] ] = $i;
				}

				$std->update_cache( array( 'name' => 'multimod', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Multi-Moderation Updated';
				break;

			//-----------------------------------------
			// BBCODE
			//-----------------------------------------

			case 'bbcode':

				$this->bbcode_rebuildcache();

				$ibforums->main_msg = 'BBCode Updated';
				break;

			//-----------------------------------------
			// SETTINGS
			//-----------------------------------------

			case 'settings':

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

					$ibforums->cache['settings'][ $r['conf_key'] ] = $value;
				}

				$std->update_cache( array( 'name' => 'settings', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Settings Updated';
				break;

			//-----------------------------------------
			// EMOTICONS
			//-----------------------------------------

			case 'emoticons':

				$this->emoticon_rebuildcache();

				$ibforums->main_msg = 'Emoticons Updated';
				break;

			//-----------------------------------------
			// BADWORDS
			//-----------------------------------------

			case 'badwords':

				$this->badword_rebuildcache();

				$ibforums->main_msg = 'Badwords Updated';
				break;

			//-----------------------------------------
			// LANGUAGES
			//-----------------------------------------

			case 'languages':

				$ibforums->cache['languages'] = array();

				$DB->simple_construct( array( 'select' => 'ldir,lname', 'from' => 'languages' ) );
				$DB->simple_exec();

				while ( $r = $DB->fetch_row() )
				{
					$ibforums->cache['languages'][] = $r;
				}

				$std->update_cache( array( 'name' => 'languages', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Languages Updated';
				break;

			//-----------------------------------------
			// BAN FILTERS
			//-----------------------------------------

			case 'banfilters':

				$this->ban_rebuildcache();

				$ibforums->main_msg = 'Banfilters Updated';
				break;

			//-----------------------------------------
			// ATTACHMENT TYPES
			//-----------------------------------------

			case 'attachtypes':
				$ibforums->cache['attachtypes'] = array();

				$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
				$DB->simple_exec();

				while ( $r = $DB->fetch_row() )
				{
					$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
				}

				$std->update_cache( array( 'name' => 'attachtypes', 'array' => 1, 'deletefirst' => 1 ) );

				$ibforums->main_msg = 'Attachment Types Updated';
				break;

			//-----------------------------------------
			// Announcements
			//-----------------------------------------

			case 'announcements':

				require_once( ROOT_PATH.'sources/announcements.php' );
				$announcements = new announcements();
				$announcements->announce_recache();

				$ibforums->main_msg = 'Announcements Updated';
				break;

			default:
				$ibforums->main_msg = 'No valid cache was specified to update';
				break;
		}

		$this->cache_start();

	}

	//-----------------------------------------
	// SHOW CACHE FORM
	//-----------------------------------------

	function cache_start()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Map
		//-----------------------------------------

		$map = array( 'forum_cache'   => 'All forum information and data',
					  'group_cache'   => 'All member group infomation and data',
					  'systemvars'    => 'System runtime variables',
					  'skin_id_cache' => 'Skin set information and data',
					  'moderators'    => 'All moderator information and data',
					  'stats'         => 'Board stats, such as total posts, etc',
					  'ranks'         => 'Member titles and rank information',
					  'profilefields' => 'Custom profile field information',
					  'birthdays'     => 'Members birthdays',
					  'calendar'      => 'Forthcoming calendar events',
					  'multimod'      => 'Multi-moderation information and data',
					  'bbcode'        => "Custom BBCode information and data",
					  'settings'      => "Board settings and variables",
					  'emoticons'     => 'Emoticon information and data',
					  'badwords'      => 'Bad Word Filters information and data',
					  'languages'     => 'Language Set information',
					  'banfilters'    => 'Banned IP addresses',
					  'attachtypes'   => 'Attachment Types information',
					  'announcements' => 'Announcements cache',
					);


		//-----------------------------------------
		// REBUILD CACHES
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'admin'    ),
															     2 => array( 'code' , 'cacheend' ),
												    	  )      );

		$ibforums->adskin->td_header[] = array( "Title"    , "60%" );
		$ibforums->adskin->td_header[] = array( "Size"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Options"  , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Your Cache" );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'order' => 'cs_key' ) );
		$DB->simple_exec();

		$used = array();

		while ( $row = $DB->fetch_row() )
		{
			if ( ! in_array( $row['cs_key'], array_keys( $map ) ) )
			{
				continue;
			}

			$used[ $row['cs_key'] ] = $row['cs_key'];

			$size = ceil( intval( strlen( $row['cs_value'] ) ) / 1024 );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$row['cs_key']."</b><div style='color:gray'>{$map[ $row['cs_key'] ]}</div>",
																   $size.' kb',
																   "<div align='center'>
																    <input type='submit' name='update_{$row['cs_key']}' value='Update' class='realbutton' />
																    <input type='button' onclick=\"pop_win('act=admin&code=viewcache&id={$row['cs_key']}','Preview', 400,600)\" value='View' class='realbutton' />
																   </div>",
														 )      );
		}

		if ( count( $used ) != count( $map ) )
		{
			foreach( $map as $k => $v )
			{
				if ( in_array( $k, array_keys( $used ) ) )
				{
					continue;
				}
				else
				{
					$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$k."</b><div style='color:gray'>{$map[ $k ]}</div>",
																	   '0 kb',
																	   "<div align='center'>
																		<input type='submit' name='update_{$k}' value='Update' class='realbutton' />
																		<input type='button' onclick=\"pop_win('act=admin&code=viewcache&id={$k}','Preview', 400,600)\" value='View' class='realbutton' />
																	   </div>",
															 )      );
				}

			}
		}

		$ibforums->html .= $ibforums->adskin->end_form();

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->admin->output();

	}

	function perly_length_sort($a, $b)
	{
		if ( strlen($a['typed']) == strlen($b['typed']) )
		{
			return 0;
		}
		return ( strlen($a['typed']) > strlen($b['typed']) ) ? -1 : 1;
	}

	function perly_word_sort($a, $b)
	{
		if ( strlen($a['type']) == strlen($b['type']) )
		{
			return 0;
		}
		return ( strlen($a['type']) > strlen($b['type']) ) ? -1 : 1;
	}






}


?>