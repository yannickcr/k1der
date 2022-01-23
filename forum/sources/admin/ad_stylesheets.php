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
|   > CSS management functions
|   > Module written by Matt Mecham
|   > Date started: 4th April 2002
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


class ad_stylesheets
{

	var $base_url;
	var $template = "";
	var $functions = "";

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
		// Get the libraries
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/admin_template_functions.php' );

		$this->functions = new admin_template_functions();

		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'floateditor':
				$this->functions->build_editor_area_floated(1);
				break;


			case 'edit2':
				$this->do_form('edit');
				break;

			case 'edit':
				$this->do_form('edit');
				break;

			case 'doedit':
				$this->save_css('edit');
				break;

			case 'optimize':
				$this->optimize();
				break;

			case 'easyedit':
				$this->easy_edit();
				break;

			case 'doresync':
				$this->do_resynch();
				break;

			case 'colouredit':
				$this->colouredit();
				break;

			case 'docolour':
				$this->do_colouredit();
				break;

			default:
				print "No action taken"; exit();
				break;

			//case 'wrapper':
			//	$this->list_sheets();
			//	break;
			//case 'add':
			//	$this->do_form('add');
			//	break;
			//case 'doadd':
			//	$this->save_css('add');
			//	break;
			//case 'remove':
			//	$this->remove();
			//	break;
			//case 'css_upload':
			//	$this->css_upload('new');
			//	break;
			//case 'export':
			//	$this->export();
			//	break;
		}

	}

	//-----------------------------------------
	// RESYNCH STYLE SHEETS
	//-----------------------------------------

	function do_resynch()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing wrapper ID, go back and try again");
		}

		//-----------------------------------------

		$DB->query("SELECT cssid, css_text, css_name, css_comments FROM ibf_skin_css WHERE cssid='".$ibforums->input['id']."'");

		if ( ! $cssinfo = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the CSS details from the database");
		}

		if ( $ibforums->input['favour'] == 'cache' )
		{
			$cache_file = ROOT_PATH."cache/css_".$ibforums->input['id'].".css";

			if ( file_exists( $cache_file ) )
			{
				$FH = fopen( $cache_file, 'r' );
				$cache_data = fread( $FH, filesize($cache_file) );
				fclose($FH);
			}
			else
			{
				$ibforums->admin->error("Could not locate cached CSS file @ $cache_file");
			}

			$dbr = $DB->compile_db_update_string( array( 'css_text' => $cache_data ) );

			$DB->query("UPDATE ibf_skin_css SET $dbr WHERE cssid='".$ibforums->input['id']."'");
		}
		else
		{
			$cache_file = ROOT_PATH."cache/css_".$ibforums->input['id'].".css";

			$FH = fopen( $cache_file, 'w' );
			fputs( $FH, $cssinfo['css_text'], strlen($cssinfo['css_text']) );
			fclose($FH);
		}

		if ( $ibforums->input['return'] != 'colouredit' )
		{
			$this->do_form('edit');
		}
		else
		{
			$this->colouredit();
		}
	}



	//-----------------------------------------
	// RESYNCH SPLASH
	//-----------------------------------------

	function resync_splash($db_length, $cache_length, $cache_mtime, $db_mtime, $id, $return="")
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------

		$ibforums->admin->page_detail = "A mismatch has been found between the cached style sheet and the style sheet stored in the database";
		$ibforums->admin->page_title  = "Resynchronise Style Sheet";

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "50%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "50%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doresync'  ),
															   2 => array( 'act'   , 'style'     ),
															   3 => array( 'id'    , $id         ),
															   4 => array( 'return', $return     ),
													  )    );

		$favour = 'db';

		$ibforums->html .= $ibforums->adskin->start_table( "Resynch CSS before editing..." );

		if ( intval($cache_mtime) > intval($db_mtime) )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array(
														"<b>CSS in database last updated:</b> ".$ibforums->admin->get_date($db_mtime, 'LONG'),
														"<b>CSS in database, # characters:</b> $db_length",
											 )      );

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
														"<span style='color:red'><b>CSS in CACHE last updated:</b> ".$ibforums->admin->get_date($cache_mtime, 'LONG')."</span>",
														"<span style='color:red'><b>CSS in CACHE, # characters:</b> $cache_length</span>",
											 )      );
			$favour = 'cache';

		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array(
														"<span style='color:red'><b>CSS in database last updated:</b> ".$ibforums->admin->get_date($db_mtime, 'LONG')."</span>",
														"<span style='color:red'><b>CSS in database, # characters:</b> $db_length</span>",
											 )      );

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
														"<b>CSS in CACHE last updated:</b> ".$ibforums->admin->get_date($cache_mtime, 'LONG'),
														"<b>CSS in CACHE, # characters:</b> $cache_length",
											 )      );
		}

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
														"<b>Resynchronise using....</b>",
														$ibforums->adskin->form_dropdown( 'favour', array(
																							    0 => array( 'cache', 'Overwrite database version with cached version'),
																							    1 => array( 'db'   , 'Update cached version from the database' ),
																							 ), $favour ),
											 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Resynchronise");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();

	}




	//-----------------------------------------
	// OPTIMIZE STYLE SHEET
	//-----------------------------------------

	function optimize()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing CSS ID, go back and try again");
		}

		//-----------------------------------------

		$DB->query("SELECT * from ibf_skin_css WHERE cssid='".$ibforums->input['id']."'");

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the information from the database");
		}

		//-----------------------------------------

		$orig_size = strlen($row['css_text']);

		$orig_text = str_replace( "\r\n", "\n", $row['css_text']);
		$orig_text = str_replace( "\r"  , "\n", $orig_text);
		$orig_text = str_replace( "\n\n", "\n", $orig_text);

		$parsed = array();

		//-----------------------------------------
		// Remove comments
		//-----------------------------------------

		$orig_text = preg_replace( "#/\*(.+?)\*/#s", "", $orig_text );

		//-----------------------------------------
		// Grab all the definitions
		//-----------------------------------------

		preg_match_all( "/(.+?)\{(.+?)\}/s", $orig_text, $match, PREG_PATTERN_ORDER );

		for ( $i = 0 ; $i < count($match[0]); $i++ )
		{
			$match[1][$i] = trim($match[1][$i]);
			$parsed[ $match[1][$i] ] = trim($match[2][$i]);
		}

		//-----------------------------------------

		if ( count($parsed) < 1)
		{
			$ibforums->admin->error("The stylesheet is in a format that Invision Power Board cannot understand, no optimization done.");
		}

		//-----------------------------------------
		// Clean them up
		//-----------------------------------------

		$final = "";

		foreach( $parsed as $name => $p )
		{
			//-----------------------------------------
			// Ignore comments
			//-----------------------------------------

			if ( preg_match( "#^//#", $name) )
			{
				continue;
			}

			//-----------------------------------------
			// Split up the components
			//-----------------------------------------

			$parts = explode( ";", $p);
			$defs  = array();

			foreach( $parts as $part )
			{
				if ($part != "")
				{
					list($definition, $data) = explode( ":", $part );
					$defs[]   = trim($definition).": ".trim($data);
				}
			}

			$final .= $name . " { ".implode("; ", $defs). " }\n";
		}

		$final_size = strlen($final);

		if ($final_size < 1000)
		{
			$ibforums->admin->error("The stylesheet is in a format that Invision Power Board cannot understand, no optimization done.");
		}

		//-----------------------------------------
		// Update the DB
		//-----------------------------------------

		$dbs = $DB->compile_db_update_string( array( 'css_text' => $final ) );

		$DB->query("UPDATE ibf_skin_css SET $dbs WHERE cssid='".$ibforums->input['id']."'");

		$saved    = $orig_size - $final_size;
		$pc_saved = 0;

		if ($saved > 0)
		{
			$pc_saved = sprintf( "%.2f", ($saved / $orig_size) * 100);
		}

		$ibforums->admin->done_screen("Stylesheet updated: Characters Saved: $saved ($pc_saved %)", "Manage Style Sheets", "act=style" );



	}


	//-----------------------------------------
	// ADD / EDIT WRAPPERS
	//-----------------------------------------

	function save_css( $type='add' )
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check input
		//-----------------------------------------

		if ($type == 'edit')
		{
			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("You must specify an existing CSS ID, go back and try again");
			}

		}

		if ($ibforums->input['txtcss'] == "")
		{
			$ibforums->admin->error("You can't have an empty stylesheet, can you?");
		}


		$css = $std->txt_stripslashes($_POST['txtcss']);
		$css = str_replace( '\\', '\\\\', $css );

		$DB->do_update( 'skin_sets', array( 'set_css' => $css, 'set_css_updated' => time() ), 'set_skin_set_id='.$ibforums->input['id'] );

		//-----------------------------------------
		// Update cache?
		//-----------------------------------------

		$extra = "<b>Stylesheet cache file updated</b>";

		$message = $ibforums->cache_func->_write_css_to_cache( $ibforums->input['id'] );

		//-----------------------------------------
		// Back to it...
		//-----------------------------------------

		if ( ! $ibforums->input['savereload'] )
		{
			$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
			$ibforums->main_msg = "Stylesheet updated : $extra";
			$ibforums->admin->redirect( "act=sets", "Stylesheet updated, returning to the skin manager" );
		}
		else
		{
			//-----------------------------------------
			// Reload edit window
			//-----------------------------------------

			$ibforums->main_msg = "Stylesheet updated : $extra";
			$this->do_form('edit');
		}
	}




	//-----------------------------------------
	// Show Add/Edit form
	//-----------------------------------------

	function do_form( $type='add' )
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing wrapper ID, go back and try again");
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
			if ( $row['set_css'] and ! $found_id )
			{
				$found_id      = $row['set_skin_set_id'];
				$found_content = $row['set_css'];
				$found_time    = $row['set_css_updated'];
			}

			if ( $ibforums->input['id'] == $row['set_skin_set_id'] )
			{
				$this_set = $row;
			}
		}

		//-----------------------------------------

		$css    = $found_content;

		$code   = 'doedit';
		$button = 'Save Stylesheet';

		//-----------------------------------------
		// COLURS!ooO!
		//-----------------------------------------

		//.class { definitions }
		//#id { definitions }

		$css_elements = array();

		preg_match_all( "/(\.|\#)(\S+?)\s{0,}\{.+?\}/s", $css, $match );

		for ($i=0; $i < count($match[0]); $i++)
		{
			$type = trim($match[1][$i]);

			$name = trim($match[2][$i]);

			if ($type == '.')
			{
				$css_elements[] = array( 'class|'.$name, $type.$name );
			}
			else
			{
				$css_elements[] = array( 'id|'.$name, $type.$name );
			}
		}

		//-----------------------------------------

		$ibforums->admin->page_detail = "You may use CSS fully when adding or editing stylesheets.";
		$ibforums->admin->page_title  = "Manage Style Sheets";

		//-----------------------------------------

		$ibforums->html .= "<script language='javascript'>
		                 <!--
		                 function cssSearch(theID)
		                 {
		                 	cssChosen = document.cssForm.csschoice.options[document.cssForm.csschoice.selectedIndex].value;

		                 	window.open('{$ibforums->adskin->base_url}&act=rtempl&code=css_search&id='+theID+'&element='+cssChosen,'CSSSEARCH','width=400,height=500,resizable=yes,scrollbars=yes');
		                 }

		                 function cssPreview(theID)
		                 {
		                 	cssChosen = document.cssForm.csschoice.options[document.cssForm.csschoice.selectedIndex].value;

		                 	window.open('{$ibforums->adskin->base_url}&act=rtempl&code=css_preview&id='+theID+'&element='+cssChosen,'CSSSEARCH','width=400,height=500,resizable=yes,scrollbars=yes');
		                 }

		                 //-->
		                 </script>";

		//-----------------------------------------
		// Show the form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->js_no_specialchars();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $code      ),
																 2 => array( 'act'   , 'style'      ),
																 3 => array( 'id'    , $ibforums->input['id']   ),
														), "theform"     );

		//-----------------------------------------
		// Editor section
		//-----------------------------------------

		$ibforums->html .= $this->functions->build_generic_editor_area( array( 'act' => 'style', 'title' => '', 'textareaname' => 'css', 'textareainput' => $css ) );

		$formbuttons = "<div align='center' class='pformstrip'>
						<input type='submit' name='submit' value='$button' class='realdarkbutton'>
						<input type='submit' name='savereload' value='Save and Reload Stylesheet' class='realdarkbutton'>
						</div></form>\n";

		$ibforums->html = str_replace( '<!--IPB.EDITORBOTTOM-->', $formbuttons, $ibforums->html );

		$ibforums->html .= "<br />";

		//-----------------------------------------
		// CSS search form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'css_search' ),
															     2 => array( 'act'   , 'style'      ),
															     3 => array( 'id'    , $ibforums->input['id']    ),
													    ), "cssForm"      );



		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "20%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "80%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Find CSS Usage" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
																 "Show me where...",
																 $ibforums->adskin->form_dropdown('csschoice', $css_elements).' ... is used within the templates &nbsp;'
																.'<input type="button" value="Go!" onClick="cssSearch(\''.$ibforums->input['id'].'\');" id="editbutton">'
																.'&nbsp;<input type="button" value="Preview CSS Style" onClick="cssPreview(\''.$ibforums->input['id'].'\');" id="editbutton">'
													  )      );

		$ibforums->html .= $ibforums->adskin->end_form();

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= $ibforums->adskin->skin_jump_menu_wrap();

		//-----------------------------------------

		$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
		$ibforums->admin->nav[] = array( '' ,'Editing Style Sheet in set '.$this_set['set_name'] );

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// EDIT COLOURS START
	//-----------------------------------------

	function colouredit()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing wrapper ID, go back and try again");
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
			if ( $row['set_css'] and ! $found_id )
			{
				$found_id      = $row['set_skin_set_id'];
				$found_content = $row['set_css'];
				$found_time    = $row['set_css_updated'];
			}

			if ( $ibforums->input['id'] == $row['set_skin_set_id'] )
			{
				$this_set = $row;
			}
		}

		//-----------------------------------------

		$css = $found_content;
		$css = preg_replace( "#/\*.+?\*/#s", "", $css );
		//print "<pre>"; print $css; exit();
		//-----------------------------------------
		// Start the CSS matcher thingy
		//-----------------------------------------

		//.class { definitions }
		//#id { definitions }

		$colours = array();

		//-----------------------------------------
		// Make http:// safe..
		//-----------------------------------------

		$css = str_replace( 'http://', 'http|//', $css );

		preg_match_all( "/([\:\.\#\w\s,]+)\{(.+?)\}/s", $css, $match );

		for ($i=0; $i < count($match[0]); $i++)
		{
			$name    = trim($match[1][$i]);
			$content = trim($match[2][$i]);

			$defs    = explode( ';', $content );

			if ( count( $defs ) > 0 )
			{
				foreach( $defs as $a )
				{
					$a = trim($a);

					if ( $a != "" )
					{
						list( $property, $value ) = explode( ":", $a, 2 );

						$property = trim($property);
						$value    = trim( str_replace( 'http|//', 'http://', $value) );

						if ( $property )
						{
							if ( $property == 'color' or $property == 'background-color' )
							{
								$colours[ $name ][$property] = $value;
							}
							else
							{
								$colours[ $name ]['_extra'] .= $property.':'.$value.';'."\n";
							}
						}
					}
				}
			}
		}

		//print "<pre>"; print_r( $colours ); exit();

		if ( count($colours) < 1 )
		{
			$ibforums->admin->error("CSS all gone wonky! No colours to edit");
		}

		//-----------------------------------------

		// Get $skin_names stuff

		require './sources/admin/skin_info.php';

		$ibforums->admin->page_detail = "You edit the existing colours below. <strong><a href='skin_acp/IPB2_Standard/colours.html' target='_blank'>Launch Colour Picker</a></center></strong>";
		$ibforums->admin->page_title  = "Manage Style Sheets [ Colours ]";



		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'docolour'   ),
																 2 => array( 'act'   , 'style'      ),
																 3 => array( 'id'    , $ibforums->input['id']    ),
														)    );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "{none}" , "100%" );

		//-----------------------------------------

		$ibforums->html .= "<input type='hidden' name='initcol' value='' />
							<input type='hidden' name='initformval' value='' />
							<script type='text/javascript'>
							function updatecolor( id )
							{
								itm = my_getbyid( id );

								if ( itm )
								{
									eval(\"newcol = document.theAdminForm.f\"+id+\".value\");
									itm.style.backgroundColor = newcol;
								}

							}
							function poppicker( initcolor, formfield )
							{
								if ( initcolor )
								{
									document.theAdminForm.initcol.value = initcolor;
								}

								document.theAdminForm.initformval.value = formfield;

								PopUp( 'skin_acp/IPB2_Standard/colours.html', 'PopPicker', 400, 500 );
							}
						    </script>";

		$ibforums->html .= $ibforums->adskin->start_table( "CSS Colours" );
		$ibforums->html .= "<td class='tdrow2'>";

		foreach ( $colours as $prop => $val )
		{
			$tbl_colour = "";
			$tbl_bg     = "";
			$tbl_html   = "";

			$desc = $css_names[ $prop ];

			if ( $desc == "" )
			{
				$desc = 'None available';
			}

			$name = $prop;

			$md5 = md5($name);

			if ( strlen($name) > 80 )
			{
				$name = substr( $name, 0, 80 ) .'...';
			}

			$font_box  = $ibforums->adskin->form_simple_input('f'.$md5.'color'           , $val['color'], "14");
			$bgcol_box = $ibforums->adskin->form_simple_input('f'.$md5.'backgroundcolor' , $val['background-color'], "14");

			$ibforums->html .= "<div class='tdrow1'>
								 <fieldset>
								  <legend><strong style='font-size:14px'>{$name}</strong></legend>
								  <table width='100%' border='0' cellpadding='4' cellspacing='0'>
								  <tr>
								   <td width='40%' valign='top'>
								    <fieldset>
								     <legend><strong>Font Color</strong></legend>
										{$font_box}&nbsp;&nbsp;<input type='text' id='{$md5}color' onclick=\"updatecolor('{$md5}color')\" size='6' style='border:1px solid black;background-color:{$val['color']}' readonly='readonly'>&nbsp;<a href='#' title='launch color picker' onclick=\"poppicker('{$val['color']}', '{$md5}color'); return false;\"><img src='{$ibforums->skin_url}/colorselect.png' border='0' /></a>
									</fieldset>
									<br />
									<fieldset>
									 <legend><strong>Background Color</strong></legend>
			 						    {$bgcol_box}&nbsp;&nbsp;<input type='text' id='{$md5}backgroundcolor'  onclick=\"updatecolor('{$md5}backgroundcolor')\" size='6' style='border:1px solid black;background-color:{$val['background-color']}' readonly='readonly'>&nbsp;<a href='#' title='launch color picker' onclick=\"poppicker('{$val['background-color']}', '{$md5}backgroundcolor'); return false;\"><img src='{$ibforums->skin_url}/colorselect.png' border='0' /></a>
			 						</fieldset>
			 					   </td>
			 					   <td width='60%' valign='top'>
			 					   <fieldset>
									 <legend><strong>Other CSS Attributes</strong></legend>
			 						    <textarea class='textinput' cols='40' rows='5' style='width:100%' name='f{$md5}extra'>{$val['_extra']}</textarea>
			 						</fieldset>
			 					   </td>
			 					  </tr>
			 					  </table>
			 					 </fieldset>
			 					 </div>";
		}

		$ibforums->html .= "</td>";
		$ibforums->html .= $ibforums->adskin->end_form("Edit");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// EDIT COLOURS START
	//-----------------------------------------

	function do_colouredit()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing wrapper ID, go back and try again");
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
			if ( $row['set_css'] and ! $found_id )
			{
				$found_id      = $row['set_skin_set_id'];
				$found_content = $row['set_css'];
				$found_time    = $row['set_css_updated'];
			}

			if ( $ibforums->input['id'] == $row['set_skin_set_id'] )
			{
				$this_set = $row;
			}
		}

		//-----------------------------------------

		$css = $found_content;
		$css = preg_replace( "#/\*.+?\*/#s", "", $css );

		//-----------------------------------------
		// Start the CSS matcher thingy
		//-----------------------------------------

		$css     = str_replace( 'http://', 'http|//', $css );

		$colours = array();

		preg_match_all( "/([\:\.\#\w\s,]+)\{(.+?)\}/s", $css, $match );

		for ($i=0; $i < count($match[0]); $i++)
		{
			$name    = trim($match[1][$i]);
			$content = trim($match[2][$i]);

			$md5     = md5($name);

			$defs    = explode( ';', $content );

			if ( count( $defs ) > 0 )
			{
				foreach( $defs as $a )
				{
					$a = trim($a);

					if ( $a != "" )
					{
						list( $property, $value ) = explode( ":", $a, 2 );

						$property = trim($property);
						$value    = trim( str_replace( 'http|//', 'http://', $value) );

						if ( $property )
						{
							$colours[ $name ][$property] = $value;
						}
					}
				}
			}

			foreach( array( 'color', 'backgroundcolor' ) as $prop )
			{
				if ( isset($_POST['f'.$md5.$prop]) )
				{
					$field = $prop == 'backgroundcolor' ? 'background-color' : $prop;

					$colours[ $name ][$field] = stripslashes($_POST['f'.$md5.$prop]);
				}
			}

			if ( isset( $_POST['f'.$md5.'extra'] ) )
			{
				$tmp = str_replace( "\n", "", $_POST['f'.$md5.'extra'] );
				$tmp = str_replace( "\r", "", $tmp );

				$extra_attr = explode( ";", $tmp );

				if ( is_array( $extra_attr ) and count( $extra_attr ) )
				{
					foreach( $extra_attr as $l )
					{
						$l = str_replace( 'http://', 'http|//', $l );

						list( $p, $v ) = explode( ":", $l );

						$colours[ $name ][ trim($p) ] = trim( str_replace( 'http|//', 'http://', $v) );
					}
				}
			}
		}

		if ( count($colours) < 1 )
		{
			$ibforums->admin->error("CSS all gone wonky! No colours to edit");
		}

		//-----------------------------------------

		unset($name);
		unset($property);

		$final = "";

		foreach( $colours as $name => $property )
		{
			$final .= $name."\n{\n";

			if ( is_array($property) and count($property) > 0 )
			{
				foreach( $property as $key => $value )
				{
					if ( $key AND $value )
					{
						$final .= "\t".$key.": ".$value.";\n";
					}
				}
			}

			$final .= "}\n\n";

		}

		//print "<pre>";
		//print $final;
		//exit();

		$ibforums->input['txtcss']     = $final;
		$_POST['txtcss']               = $final;
		$ibforums->input['savereload'] = 0;
		$this->save_css('edit');

	}



}


?>