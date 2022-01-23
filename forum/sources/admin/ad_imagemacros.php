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
|   > Skin -> Image Macro functions
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


class ad_imagemacros {

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
				$this->show_macros();
				break;

			case 'doedit':
				$this->edit_set_name();
				break;

			case 'macroremove':
				$this->macro_remove();
				break;

			case 'doeditmacro':
				$this->macro_edit();
				break;

			case 'doaddmacro':
				$this->macro_add();
				break;

			default:
				print "No action"; exit();
				break;
		}
	}

	//-----------------------------------------
	// Remove macro
	//-----------------------------------------

	function macro_remove()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify an existing macro ID, go back and try again");
		}

		$id = intval($ibforums->input['id']);
		$p  = intval($ibforums->input['p']);

		$DB->simple_exec_query( array( 'delete' => 'skin_macro', 'where' => "macro_id='".$ibforums->input['mid']."'" ) );

		//-----------------------------------------
		// Recache macros
		//-----------------------------------------

		$ibforums->cache_func->_recache_macros($id, $p);

		//-----------------------------------------
		// Bounce back
		//-----------------------------------------

		$ibforums->input['id'] = $id;
		$ibforums->main_msg = "Macro removed!";
		$this->show_macros();
	}

	//-----------------------------------------
	// Apply the edit to the DB
	//-----------------------------------------

	function macro_edit()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify an existing image set ID, go back and try again");
		}

		$id = intval($ibforums->input['id']);
		$p  = intval($ibforums->input['p']);

		$key = $DB->add_slashes( $std->txt_safeslashes($_POST['variable']) );
		$val = $DB->add_slashes( $std->txt_UNhtmlspecialchars($std->txt_safeslashes($_POST['replacement']) ) );

		//-----------------------------------------
		// Get macro for examination..
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_macro', 'where' => "macro_id='".$ibforums->input['mid']."'" ) );
		$DB->simple_exec();

 		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not retrieve the record from the DB");
		}

		//-----------------------------------------
		// Is this our macro set?
		//-----------------------------------------

		if ( $row['macro_set'] == $id )
		{
			//-----------------------------------------
			// Okay, update...
			//-----------------------------------------

			$DB->simple_construct( array( 'update' => 'skin_macro', 'set' => "macro_value='$key', macro_replace='$val'", 'where' => "macro_id=".$ibforums->input['mid'] ) );
			$DB->simple_exec();
		}
		else
		{
			//-----------------------------------------
			// No? OK - best add it as a 'new' macro
			//-----------------------------------------

			$DB->manual_addslashes = 1;
			$DB->do_insert( 'skin_macro', array (
												'macro_value'         => $key,
												'macro_replace'       => $val,
												'macro_can_remove'    => 1,
												'macro_set'           => $id
										)      );

			$DB->manual_addslashes = 0;
		}


		//-----------------------------------------
		// Recache macros
		//-----------------------------------------

		$ibforums->cache_func->_recache_macros($id, $p);

		//-----------------------------------------
		// Bounce back
		//-----------------------------------------

		$ibforums->input['id'] = $id;
		$this->show_macros();
	}

	//-----------------------------------------
	// ADD MACRO
	//-----------------------------------------

	function macro_add()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify an existing macro set ID, go back and try again");
		}

		$id = intval($ibforums->input['id']);
		$p  = intval($ibforums->input['p']);

		$DB->do_insert( 'skin_macro', array (
											 'macro_value'         => $std->txt_safeslashes($_POST['variable']),
											 'macro_replace'       => $std->txt_UNhtmlspecialchars($std->txt_safeslashes($_POST['replacement'])),
											 'macro_can_remove'    => 1,
											 'macro_set'           => $id
									 )      );

		//-----------------------------------------
		// Recache macros
		//-----------------------------------------

		$ibforums->cache_func->_recache_macros($id, $p);

		//-----------------------------------------
		// Bounce back
		//-----------------------------------------

		$ibforums->input['id'] = $id;
		$this->show_macros();

	}



	//-----------------------------------------
	// Show macros
	//-----------------------------------------

	function show_macros()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing macro set ID, go back and try again");
		}

		//-----------------------------------------
		// check tree...
		//-----------------------------------------

		$this_set      = "";

		if ( $ibforums->input['p'] > 0 )
		{
			$in = ','.$ibforums->input['p'];
		}

		//-----------------------------------------
		// Get macros
		//-----------------------------------------

		$macros = $ibforums->cache_func->_get_macros($ibforums->input['id'], $ibforums->input['p']);

		//-----------------------------------------
		// Get img_dir this set is using...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		$skin = $DB->fetch_row();

		$ibforums->admin->page_detail = "To edit a macro, simply click on the 'edit' link of the appropriate macro.";

		$ibforums->admin->page_title  = "Manage Replacement Macros in Set: {$skin['set_name']}";

		//-----------------------------------------
		// Start output
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->js_no_specialchars();
		$ibforums->html .= $ibforums->adskin->js_checkdelete();


		$ibforums->html .= "<script type='text/javascript'>
							function editmacro(id, variable, replace)
							{
								document.macroform.code.value         = 'doeditmacro';
								document.macroform.submitbutton.value = 'Edit This Macro';
								document.macroform.mid.value          = id;
								document.macroform.variable.value     = variable;
								document.macroform.replacement.value  = replace;
								scroll(0,0);
								togglediv( 'popbox', 1 );
								return false;
							}
							function addmacro(id)
							{
								document.macroform.code.value         = 'doaddmacro';
								document.macroform.submitbutton.value = 'Add This Macro';
								document.macroform.mid.value          = id;
								document.macroform.variable.value     = '';
								document.macroform.replacement.value  = '';
								scroll(0,0);
								togglediv( 'popbox', 1 );
								return false;
							}
							function removemacro(url)
							{
								checkdelete(url);
							}
							</script>
							<div class='tableborder'>
							<div class='maintitle'>
							<div align='center' style='position:absolute;width:99%;display:none;text-align:center' id='popbox'>
							 <form name='macroform' action='{$ibforums->adskin->base_url}' method='post'>
							 <input type='hidden' name='act' value='image' />
							 <input type='hidden' name='code' value='' />
							 <input type='hidden' name='mid' value='' />
							 <input type='hidden' name='id' value='{$ibforums->input['id']}' />
							 <input type='hidden' name='p' value='{$ibforums->input['p']}' />
							 <table cellspacing='0' width='500' align='center' cellpadding='6' style='background:#EEE;border:2px outset #555;'>
							 <tr>
							  <td width='1%' nowrap='nowrap' valign='top'>
							   <b>Variable</b><br><input class='textinput' name='variable' type='text' size='20' />
							   <br /><br />
							   <center><input type='submit' class='realbutton' value='Edit Macro' name='submitbutton' /> <input type='button' class='realdarkbutton' value='Close' onclick=\"togglediv('popbox');\" /></center>
							  </td>
							  <td width='99%'><b>Replacement</b><br /><textarea class='textinput' name='replacement' style='width:100%;height:50px'></textarea></td>
							 </tr>
							 </table>
							 </form>
							</div>
							<table cellpadding='0' cellspacing='0' border='0' width='100%'>
							<tr>
							<td align='left' width='100%' style='font-weight:bold;font-size:11px;color:#FFF'>Manage Replacement Macros</td>
							<td align='right' nowrap='nowrap' style='padding-right:2px'><input type='button' class='realdarkbutton' value='Add Macro' onclick=\"addmacro('{$ibforums->input['id']}');\" /></td>
							</tr>
							</table>
							</div>
							</div>

						   ";

		$ibforums->html .= "<div class='tableborder'>\n<div class='tablepad'>\n<table width='100%' cellspacing='0' cellpadding='0' border='0'>";

		$this->unaltered    = "<img src='{$ibforums->skin_url}/skin_item_unaltered.gif' border='0' alt='-' title='Unaltered from parent skin set' />&nbsp;";
		$this->altered      = "<img src='{$ibforums->skin_url}/skin_item_altered.gif' border='0' alt='+' title='Altered from parent skin set' />&nbsp;";
		$this->inherited    = "<img src='{$ibforums->skin_url}/skin_item_inherited.gif' border='0' alt='|' title='Inherited from parent skin set' />&nbsp;";

		//-----------------------------------------
		// Loop and print
		//-----------------------------------------

		foreach( $macros as $name => $row )
		{
			$real = $std->txt_htmlspecialchars( $row['macro_replace'] );

			//-----------------------------------------
			// Altered?
			//-----------------------------------------

			if ( $row['macro_set'] == $ibforums->input['id'] )
			{
				$altered_image = $this->altered;
				$css_info      = '#FFDCD8';
			}
			else if ( $row['macro_set'] == 1 )
			{
				$altered_image = $this->unaltered;
				$css_info      = '#EEE';
			}
			else
			{
				$altered_image = $this->inherited;
				$css_info      = '#FFF2D3';
			}

			//-----------------------------------------
			// Figure out quotes
			//-----------------------------------------

			$out_quote = '"';
			$in_quote  = "'";

			if ( preg_match( "/&#039;/", $real ) )
			{
				$out_quote = "'";
				$in_quote  = '"';
			}

			$preview = str_replace( "<#IMG_DIR#>", $skin['set_image_dir'], $row['macro_replace'] );

			if ( $row['macro_set'] > 1 and $row['macro_set'] == $ibforums->input['id'] )
			{
				$remove_button = "<input type='button' class='realbutton' name='remove' value='Revert' onclick=\"removemacro('act=image&code=macroremove&mid={$row['macro_id']}&id={$ibforums->input['id']}&p={$ibforums->input['p']}');\" />";
			}
			else
			{
				$remove_button = "";
			}

			//-----------------------------------------
			// Not an image?
			//-----------------------------------------

			if ( ! preg_match( "#img\s{1,}src=#i", $row['macro_replace'] ) )
			{
				$preview = substr( $real, 0, 200 );
			}

			$edit_button = "<input type='button' class='realbutton' value='Change' onclick={$out_quote}editmacro( {$in_quote}{$row['macro_id']}{$in_quote}, {$in_quote}{$row['macro_value']}{$in_quote}, {$in_quote}$real{$in_quote});{$out_quote} />";

			//-----------------------------------------
			// Render row
			//-----------------------------------------

			$style = "padding:4px;border-bottom:1px solid #DDD;background:{$css_info}";

			$ibforums->html .= "<tr>
								 <!--<td style='$style' align='center' width='1%'><img src='{$ibforums->skin_url}/skin_macro.gif' alt='Macro' title='ID: {$row['macro_id']}' style='vertical-align:middle' /></td>-->
								 <td style='$style' align='left' width='1%' nowrap='nowrap'>$altered_image
								  &nbsp;&lt;{<span style='font-size:11px;font-weight:bold' title='ID: {$row['macro_id']}. SET: {$row['macro_set']}' href='#' >{$row['macro_value']}</span>}&gt;
								  </td>
								 <td style='$style;padding-right:3px;text-align:center;' align='center' width='99%' align='center'>$preview</td>
								 <td style='$style' align='right' width='40%' nowrap='nowrap'>$remove_button $edit_button</td>
								</tr>

								";
		}


		$ibforums->html .= "</table>
						   </div>
						   <div class='pformstrip' align='center'>
						    <input type='button' class='realdarkbutton' value='Add Macro' onclick=\"addmacro('{$ibforums->input['id']}');\" />
						   </div>
						   </div>";

		$ibforums->html .= $ibforums->adskin->skin_jump_menu_wrap();

		//-----------------------------------------
		// Show altered / unaltered
		// legend
		//-----------------------------------------

		$ibforums->html .= "<br />
							<div><strong>Replace Macro Example</strong><br />
							If you added a key of 'green_font' and a replacement of '&lt;font color='green'>', each instance of <span style='color:red'><b>&lt;{green_font}&gt;</b></span> would be converted to &lt;font color='green'>
							<br /><b>&lt;#IMG_DIR#></b> is available to any macro, this is automatically replaced with the name of the image directory you choose when using this macro set in a skin
							</div><br />
							<div><strong>Replacement Macro Legend:</strong><br />
							{$this->altered} This item has been customized for this skin set.
							<br />{$this->unaltered} This item has not been customized from the master skin set.
							<br />{$this->inherited} This item has inherited customizations from the parent skin set.
							</div>";

		$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
		$ibforums->admin->nav[] = array( '' ,'Editing Replacement Macros in Set '.$skin['set_name'] );

		$ibforums->admin->output();

	}



}


?>