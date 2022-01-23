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
|   > Skin -> Templates functions
|   > Module written by Matt Mecham
|   > Date started: 15th April 2002
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


class ad_templates
{
	var $base_url;
	var $template = "";
	var $functions = "";

	function auto_run()
	{
		global $ibforums, $DB, $std;

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

		require_once( KERNEL_PATH.'class_template.php' );

		$this->template = new class_template();

		$this->template->root_path = ROOT_PATH;

		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/admin_template_functions.php' );

		$this->functions = new admin_template_functions();

		//-----------------------------------------

		$this->unaltered    = "<img src='{$ibforums->skin_url}/skin_item_unaltered.gif' border='0' alt='-' title='Unaltered from parent skin set' />&nbsp;";
		$this->altered      = "<img src='{$ibforums->skin_url}/skin_item_altered.gif' border='0' alt='+' title='Altered from parent skin set' />&nbsp;";
		$this->inherited    = "<img src='{$ibforums->skin_url}/skin_item_inherited.gif' border='0' alt='|' title='Inherited from parent skin set' />&nbsp;";

		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'find_pop':
				$this->find_popular();
				break;

			case 'edit':
				$this->show_cats();
				break;

			case 'doedit':
				$this->do_edit();
				break;

			case 'remove_bit':
				$this->revert_bit();
				break;

			case 'edit_bit':
				$this->edit_bit();
				break;

			case 'floateditor':
				$this->functions->build_editor_area_floated();
				break;

			case 'addbit':
				$this->add_bit();
				break;

			case 'doadd':
				$this->do_add_bit();
				break;

			default:
				print "No action"; exit();
				break;
		}

	}


	//-----------------------------------------
	// Show template groups/categories
	//-----------------------------------------

	function show_cats()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$groups     = array();
		$group_bits = array();

		//-----------------------------------------
		// Get skin set
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id=".$ibforums->input['id'] ) );

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing template set ID, go back and try again");
		}

		//-----------------------------------------
		// Parent?
		//-----------------------------------------

		if ( ! $ibforums->input['p'] )
		{
			if ( $this_set['set_skin_set_parent'] )
			{
				$ibforums->input['p'] = $this_set['set_skin_set_parent'];
			}
		}

		if ( $ibforums->input['p'] > 0 )
		{
			$in = ','.$ibforums->input['p'];
		}

		//-----------------------------------------
		// Get $skin_names stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/skin_info.php' );

		$ibforums->admin->page_detail = "Please choose which section you wish to edit below.$author $url";
		$ibforums->admin->page_title  = "Edit Template sets";

		//-----------------------------------------
		// Get the groups...
		//-----------------------------------------

		$group_titles = $ibforums->cache_func->_get_templates($ibforums->input['id'], $ibforums->input['p'], 'groups');

		foreach( $group_titles as $title => $g )
		{
			//-----------------------------------------
			// Fix up names
			//-----------------------------------------

			$g['easy_name'] = "<b>".$g['group_name']."</b>";
			$g['easy_desc'] = "";

			//-----------------------------------------
			// If available, change group name to easy name
			//-----------------------------------------

			if ( isset($skin_names[ $g['group_name'] ]) )
			{
				$g['easy_name'] = "<b>".$skin_names[ $g['group_name'] ][0]."</b>";
				$g['easy_desc'] = str_replace( '"', '&quot;', $skin_names[ $g['group_name'] ][1] );
			}
			else
			{
				$g['easy_name'] = "<b>".$g['group_name']."</b> (Non-Default Group)";
				$g['easy_desc'] = "<br>This group is not part of the standard Invision Power Board installation and no description is available";
			}

			if ( $skin_names[ $g['group_name'] ][2] )
			{
				$g['easy_preview'] = "<a title='New window: Show relevant IPB page' href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?{$skin_names[ $g['group_name'] ][2]}' target='_blank'><img src='{$ibforums->skin_url}/te_previewon.gif' alt='Preview' border='0' /></a>";
			}
			else
			{
				$g['easy_preview'] = "<img src='{$ibforums->skin_url}/te_previewoff.gif' alt='No preview available' border='0' title='No preview available' />";
			}

			$groups[] = $g;

		}

		//-----------------------------------------
		// Sort by easy_name
		//-----------------------------------------

		usort($groups, array( 'ad_templates', 'perly_alpha_sort' ) );

		//-----------------------------------------
		// Get prefs cookie
		//-----------------------------------------

		If ( $default = $std->my_getcookie('skineditoboxprefs') )
		{
			list( $default_x, $float ) = explode( ",", $default );
			$float     = intval($float);
			$default_x = intval($default_x) ? intval($default_x) : 150;
		}
		else
		{
			$default_x = 150;
			$float     = 0;
		}

		if ( $float )
		{
			$css_width = 'width:450px;position:absolute';
		}
		else
		{
			$css_width = '';
		}

		$ibforums->html .= "<script language='javascript'>
							function pop_win(theUrl)
							{
							   window.open('{$ibforums->adskin->base_url}&'+theUrl,'Preview','width=400,height=450,resizable=yes,scrollbars=yes');
							}

							var toggleon  = 0;
							var start_y   = $default_x;
							var current_y = $default_x;
							var boxpopped = 0;
							var boxfloat  = $float;

							function toggleselectall()
							{
								if ( toggleon )
								{
									toggleon = 0;
									dotoggleselectall(0);
								}
								else
								{
									toggleon = 1;
									dotoggleselectall(1);
								}
							}

							function box_make_smaller()
							{
								divid = my_getbyid( 'popboxinner' );

								if ( current_y <= 100 )
								{
									return;
								}
								else
								{
									current_y -= 50;
									divid.style.height = current_y;
								}

								my_setcookie( 'skineditoboxprefs', current_y+','+boxfloat, 1 );
							}


							function box_make_larger()
							{
								divid = my_getbyid( 'popboxinner' );

								if ( current_y >= 1000 )
								{
									return;
								}
								else
								{
									current_y += 50;
									divid.style.height = current_y;
								}

								my_setcookie( 'skineditoboxprefs', current_y+','+boxfloat, 1 );
							}

							function box_no_scroll()
							{
								divid = my_getbyid( 'popboxinner' );

								if ( ! boxpopped )
								{
									divid.style.height = '';
									divid.style.overflow = '';
									boxpopped = 1;
								}
								else
								{
									divid.style.height   =  current_y;
									divid.style.overflow =  'auto';
									boxpopped = 0;
								}
							}

							function box_float()
							{
								divid = my_getbyid( 'popbox' );

								if ( ! boxfloat )
								{
									divid.style.width    = '450px';
									divid.style.position = 'absolute';
									boxfloat = 1;
								}
								else
								{
									divid.style.width    =  '';
									divid.style.position =  '';
									boxfloat = 0;
								}

								my_setcookie( 'skineditoboxprefs', current_y+','+boxfloat, 1 );
							}

							function dotoggleselectall(selectall)
							{
								var fmobj = document.mutliact;
								for (var i=0;i<fmobj.elements.length;i++)
								{
									var e = fmobj.elements[i];

									if (e.type=='checkbox')
									{
										if ( selectall ) {
										   e.checked = true;
										} else {
										   e.checked = false;
										}
									}
								}
							}
							</script>";

		$qc = $ibforums->adskin->form_dropdown( 'entry', array(
													0 => array( 'boardheader', 'Edit Board Header'             ),
													1 => array( 'catforum'   , 'Edit Category & Forum Table'   ),
													2 => array( 'topiclist'  , 'Edit Topic List Entry & Table' ),
													3 => array( 'postlist'   , 'Edit Post View & Table'        ),
								  )			     );

		$ibforums->html .= "
							<div class='tableborder'>
							<div class='maintitle'>
							<table cellpadding='0' cellspacing='0' border='0' width='100%'>
							<tr>
							<td align='left' width='100%'>
							 <form action='{$ibforums->adskin->base_url}&act=skintools&code=simplesearch&set_skin_set_id={$ibforums->input['id']}&searchall=1' method='post'>
							 <input type='text' size='20' class='realwhitebutton' name='searchkeywords' value='Search templates...' onfocus='this.value=\"\"' />&nbsp;<input type='submit' class='realbutton' value='Go' />
							 </form>
							</td>
							<td align='right' nowrap='nowrap' style='padding-right:2px'>
							 <form action='{$ibforums->adskin->base_url}&act=templ&code=find_pop&id={$ibforums->input['id']}' method='post'>
							 {$qc}&nbsp;<input type='submit' class='realbutton' value='Go' />
							 </form>
							</td>
							</tr>
							</table>
							</div></div>
						   ";

		$ibforums->html .= "<div class='tableborder'>\n<div class='tablepad'>\n";

		//-----------------------------------------
		// Loop and print
		//-----------------------------------------

		foreach( $groups as $group )
		{
			$eid    = $group['suid'];
			$exp_content = "";

			//-----------------------------------------
			// Is this section expanded?
			//-----------------------------------------

			if ($ibforums->input['expand'] == $group['group_name'])
			{
				//-----------------------------------------
				// Get master template names..
				//-----------------------------------------

				$ibforums->html .= $ibforums->adskin->js_checkdelete();

				$master_names = array();

				$DB->simple_construct( array( 'select' => 'func_name', 'from' => 'skin_templates', 'where' => "group_name='{$group['group_name']}' AND set_id=1" ) );
				$DB->simple_exec();

				while ( $m = $DB->fetch_row() )
				{
					$master_names[ $m['func_name'] ] = $m['func_name'];
				}

				$group_bits = $ibforums->cache_func->_get_templates($ibforums->input['id'], $ibforums->input['p'], 'groups', $ibforums->input['expand']);

				$add_button = $ibforums->adskin->js_make_button( 'Add Template Bit', "{$ibforums->adskin->base_url}&act=templ&code=addbit&id={$ibforums->input['id']}&p={$ibforums->input['p']}&expand={$group['group_name']}", "realbutton", "Add template bit" );

				$exp_content = "<a name='{$group['group_name']}'></a>
								<div style='padding:4px;border-bottom:1px solid #999;background:#EEE'>
								<table width='100%' cellspacing='0' cellpadding='0' border='0'>
								<tr>
								 <td align='center' width='1%'><img src='{$ibforums->skin_url}/folder_with_page.gif' alt='Template Group' style='vertical-align:middle' /></td>
								 <td align='left' width='60%'>
								  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a style='font-size:11px' href='{$ibforums->adskin->base_url}&act=templ&code=edit&id={$ibforums->input['id']}&p={$ibforums->input['p']}&expand={$group['group_name']}'  onclick=\"toggleview('popbox'); return false;\">{$group['easy_name']}</a>
								  </td>
								 <td align='right' width='40%'>{$group['easy_preview']}</td>
								</tr>
								</table>
								</div>
								 <div style='margin-left:25px;background:#EEE;border:1px solid #555;{$css_width}' id='popbox'>
								    <div style='background-color:#CCC' class='skineditortopstrip'>
								     <div style='float:right'>
								     <a href='#' onclick=\"box_make_smaller(); return false;\" title='Smaller'><img src='{$ibforums->adskin->img_url}/skineditor_smaller.gif' border='0' alt='Make box smaller' /></a>
								     <a href='#' onclick=\"box_no_scroll(); return false;\" title='Enlarge/Reduce'><img src='{$ibforums->adskin->img_url}/skineditor_enlarge.gif' border='0' alt='Reset' /></a>
								     <a href='#' onclick=\"box_make_larger(); return false;\" title='Larger'><img src='{$ibforums->adskin->img_url}/skineditor_bigger.gif' border='0' alt='Make box bigger' /></a>
								     </div>
								     <div>
								      <a href='#' onclick=\"togglediv('popbox'); return false;\" title='Close Window'><img src='{$ibforums->adskin->img_url}/skineditor_close.gif' border='0' alt='Close' /></a>&nbsp;
								      <a href='#' onclick=\"toggleselectall(); return false;\" title='Check/Uncheck all'><img src='{$ibforums->adskin->img_url}/skineditor_tick.gif' border='0' alt='Check/Uncheck all' /></a>
								      <a href='#' onclick=\"box_float(); return false;\" title='Float'><img src='{$ibforums->adskin->img_url}/skineditor_float.gif' border='0' alt='Float' /></a>
								      &nbsp;<b>{$group['easy_name']}</b>
								     </div>
								    </div>
									<div style='height:{$default_x}px;overflow:auto' id='popboxinner'>
									  <form name='mutliact' action='{$ibforums->adskin->base_url}&act=templ&code=edit_bit&expand={$group['group_name']}&id={$ibforums->input['id']}&p={$ibforums->input['p']}' method='post'>
									  <table cellspacing='0' width='100%' cellpadding='2'>
									  <!--CONTENT-->
									  </table>
									</div>
									<div style='background:#CCC'>
									  <div align='left' style='padding:5px;margin-left:25px'>
									  <div style='float:right'>$add_button</div>
									  <div><input type='submit' class='realbutton' value='Edit Selected' /></div>
									</div>
								  </div>
								  </form>
								  </div>
								";
				$temp = "";
				$sec_arry = array();

				//-----------------------------------------
				// Stuff array to sort on name
				//-----------------------------------------

				foreach( $group_bits as $eye => $i )
				{
					$sec_arry[ $i['suid'] ] = $i;
					$sec_arry[ $i['suid'] ]['easy_name'] = $i['func_name'];

					//-----------------------------------------
					// If easy name is available, use it
					//-----------------------------------------

					if ($bit_names[ $group['group_name'] ][ $i['func_name'] ] != "")
					{
						//$sec_arry[ $i['suid'] ]['easy_name'] = $bit_names[ $group['group_name'] ][ $i['func_name'] ];
					}

				}

				//-----------------------------------------
				// Sort by easy_name
				//-----------------------------------------

				usort($sec_arry, array( 'ad_templates', 'perly_alpha_sort' ) );

				//-----------------------------------------
				// Loop and print main display
				//-----------------------------------------

				foreach( $sec_arry as $id => $sec )
				{
					$sec['easy_name'] = preg_replace( "/^(\d+)\:\s+?/", "", $sec['easy_name'] );

					$custom_bit    = "";

					//-----------------------------------------
					// Altered?
					//-----------------------------------------

					if ( $sec['set_id'] == $ibforums->input['id'] )
					{
						$altered_image = $this->altered;
						$css_info      = '#FCE8E8';
					}
					else if ( $sec['set_id'] == 1 )
					{
						$altered_image = $this->unaltered;
						$css_info      = '#EEE';
					}
					else
					{
						$altered_image = $this->inherited;
						$css_info      = '#FFF2D3';
					}

					$remove_button = "<img src='{$ibforums->skin_url}/blank.gif' alt='' border='0' width='44' height='16' />&nbsp;";

					if ( $sec['set_id'] == $ibforums->input['id'] )
					{
						if ( $master_names[ $sec['func_name'] ] )
						{
							$remove_button = "<a title='Revert Customization' href=\"javascript:checkdelete('act=templ&code=remove_bit&suid={$sec['suid']}&id={$ibforums->input['id']}&p={$ibforums->input['p']}&expand={$group['group_name']}')\"><img src='{$ibforums->skin_url}/te_revert.gif' alt='X' border='0' /></a>&nbsp;";
						}
						else
						{
							$css_info      = '#FFDCD8';
							$custom_bit    = ' (custom bit)';
							$remove_button = "<a title='Remove Custom Bit' href=\"javascript:checkdelete('act=templ&code=remove_bit&suid={$sec['suid']}&id={$ibforums->input['id']}&p={$ibforums->input['p']}&expand={$group['group_name']}')\"><img src='{$ibforums->skin_url}/te_remove.gif' alt='X' border='0' /></a>&nbsp;";
						}
					}

					$temp .= "
								<tr>
								 <td width='2%' style='background-color:$css_info' align='center'><img src='{$ibforums->skin_url}/file.gif' title='Template Set:{$sec['set_id']}' alt='Template' style='vertical-align:middle' /></td>
								 <td width='88%' style='background-color:$css_info'><input type='checkbox' style='background-color:$css_info' name='cb_{$sec['suid']}' value='1' />&nbsp;{$altered_image}<a href='{$ibforums->adskin->base_url}&act=templ&code=edit_bit&suid={$sec['suid']}&p={$ibforums->input['p']}&id={$ibforums->input['id']}&expand={$group['group_name']}&type=single' title='template bit name: {$sec['func_name']}'>{$sec['easy_name']}</a>{$custom_bit}</td>
								 <td width='10%' style='background-color:$css_info' align='right' nowrap='nowrap'>
								   $remove_button
								   <a style='text-decoration:none' title='Preview template bit as text' href='javascript:pop_win(\"act=rtempl&code=preview&suid={$sec['suid']}&type=text\")'><img src='{$ibforums->skin_url}/te_text.gif' border='0' alt='Text Preview'></a>
								   <a style='text-decoration:none' title='Preview template bit as HTML' href='javascript:pop_win(\"act=rtempl&code=preview&suid={$sec['suid']}&type=css\")'><img src='{$ibforums->skin_url}/te_html.gif' border='0' alt='HTML Preview'>&nbsp;</a>
								 </td>
								</tr>
							";
				}


				$ibforums->html .= str_replace( "<!--CONTENT-->", $temp, $exp_content );
			}
			else
			{
				$altered      = sprintf( '%02d', intval($ibforums->cache_func->template_count[$ibforums->input['id']][ $group['group_name'] ]['count']) );
				$original     = sprintf( '%02d', intval($ibforums->cache_func->template_count[1][ $group['group_name'] ]['count']) );
				$inherited    = sprintf( '%02d', intval($ibforums->cache_func->template_count[$ibforums->input['p']][ $group['group_name'] ]['count']) );
				$count_string = "";

				if ( $ibforums->input['p'] > 0 )
				{
					$count_string = "$original {$this->unaltered} $inherited {$this->inherited} $altered {$this->altered}";
				}
				else
				{
					$count_string = "$original {$this->unaltered} $altered {$this->altered}";
				}

				//-----------------------------------------
				// Folder blob
				//-----------------------------------------

				if ( $altered > 0 )
				{
					$folder_blob = $this->altered;
				}
				else if ( $ibforums->input['p'] > 0 and $inherited > 0 )
				{
					$folder_blob = $this->inherited;
				}
				else
				{
					$folder_blob = $this->unaltered;
				}

				//-----------------------------------------
				// Print normal rows
				//-----------------------------------------

				$ibforums->html .= "<div style='padding:4px;border-bottom:1px solid #DDD;'>
									<table width='100%' cellspacing='0' cellpadding='0' border='0'>
									<tr>
									 <td align='center' width='1%'><img src='{$ibforums->skin_url}/folder.gif' alt='Template Group' style='vertical-align:middle' /></td>
									 <td align='left' width='60%'>&nbsp;{$folder_blob}&nbsp;<a style='font-size:11px' title='{$group['easy_desc']}' href='{$ibforums->adskin->base_url}&act=templ&code=edit&id={$ibforums->input['id']}&p={$ibforums->input['p']}&expand={$group['group_name']}#{$group['group_name']}'>{$group['easy_name']}</a></td>
									 <td align='right' width='40%'>($count_string)&nbsp;{$group['easy_preview']}</td>
									</tr>
									</table>
									</div>
									";
			}
		}

		$ibforums->html .= "</div></div>";

		$ibforums->html .= "<br />
							<div><strong>Template HTML Bits:</strong><br />
							{$this->altered} This item has been customized for this skin set.
							<br />{$this->unaltered} This item has not been customized from the master skin set.
							<br />{$this->inherited} This item has inherited customizations from the parent skin set.
							</div>
							<br />";

		$ibforums->html .= $ibforums->adskin->skin_jump_menu_wrap();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
		$ibforums->admin->nav[] = array( '' ,'Managing Template Set "'.$this_set['set_name'].'"' );

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// Sneaky sorting.
	// We use the format "1: name". without this hack
	// 1: name, 2: other name, 11: other name
	// will sort as 1: name, 11: other name, 2: other name
	// There is natsort and such in PHP, but it causes some
	// problems on older PHP installs, this is hackish but works
	// by simply adding '0' to a number less than 2 characters long.
	// of course, this won't work with three numerics in the hundreds
	// but we don't have to worry about more that 99 bits in a template
	// at this stage.
	//-----------------------------------------

	function perly_word_sort($a, $b)
	{
		$nat_a = intval( $a['easy_name'] );
		$nat_b = intval( $b['easy_name'] );

		if (strlen($nat_a) < 2)
		{
			$nat_a = '0'.$nat_a;
		}
		if (strlen($nat_b) < 2)
		{
			$nat_b = '0'.$nat_b;
		}

		return strcmp($nat_a, $nat_b);
	}

	//-----------------------------------------
	// Sort by group name
	//-----------------------------------------

	function perly_alpha_sort($a, $b)
	{
		return strcmp( strtolower($a['easy_name']), strtolower($b['easy_name']) );
	}

	//-----------------------------------------
	// Find popular bits :D
	//-----------------------------------------

	function find_popular()
	{
		global $ibforums, $DB,  $std, $HTTP_COOKIE_VARS;

		//-----------------------------------------
		// Get skin set
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id=".$ibforums->input['id'] ) );

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing template set ID, go back and try again");
		}

		//-----------------------------------------
		// Parent?
		//-----------------------------------------

		if ( ! $ibforums->input['p'] )
		{
			if ( $this_set['set_skin_set_parent'] )
			{
				$ibforums->input['p'] = $this_set['set_skin_set_parent'];
			}
		}

		switch ( $ibforums->input['entry'] )
		{
			case 'boardheader':
				$group_titles = $ibforums->cache_func->_get_templates($ibforums->input['id'], $ibforums->input['p'], 'groups', 'skin_global');

				foreach( $group_titles as $func_name => $r )
				{
					if ( in_array( $func_name, array( 'member_bar', 'global_board_header' ) ) )
					{
						$ibforums->input['cb_'.$r['suid']] = 1;
					}
				}
				break;
			case 'catforum':
				$group_titles = $ibforums->cache_func->_get_templates($ibforums->input['id'], $ibforums->input['p'], 'groups', 'skin_boards');

				foreach( $group_titles as $func_name => $r )
				{
					if ( in_array( $func_name, array( "catheader_expanded", "pagetop",  "end_this_cat", "end_all_cats", "forumrow" ) ) )
					{
						$ibforums->input['cb_'.$r['suid']] = 1;
					}
				}
				break;
			case 'topiclist':
				$group_titles = $ibforums->cache_func->_get_templates($ibforums->input['id'], $ibforums->input['p'], 'groups', 'skin_forum');

				foreach( $group_titles as $func_name => $r )
				{
					if ( in_array( $func_name, array( "pagetop", "tableend", "render_forum_row", "render_pinned_start", "render_pinned_end", "render_pinned_row" ) ) )
					{
						$ibforums->input['cb_'.$r['suid']] = 1;
					}
				}
				break;
			default:
				$group_titles = $ibforums->cache_func->_get_templates($ibforums->input['id'], $ibforums->input['p'], 'groups', 'skin_topic');

				foreach( $group_titles as $func_name => $r )
				{
					if ( in_array( $func_name, array( "topic_page_top_new_mode","topic_page_top_classic", "tablefooter", "renderrow" ) ) )
					{
						$ibforums->input['cb_'.$r['suid']] = 1;
					}
				}
				break;
		}

		$this->edit_bit();
	}

	//-----------------------------------------
	// ADD TEMPLATE BIT
	//-----------------------------------------

	function add_bit()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You may add a template bit using this section.";
		$ibforums->admin->page_title  = "Template Editing";

		$groupname = $ibforums->input['expand'];

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id=".$ibforums->input['id'] ) );

		//-----------------------------------------
		// Sort out group titles
		//-----------------------------------------

		$group_titles = $ibforums->cache_func->_get_templates( $ibforums->input['id'], $ibforums->input['p'], 'groups' );

		$formatted_groups = array();

		foreach ( $group_titles as $name => $d )
		{
			$formatted_groups[] = array( $d['group_name'], $d['group_name'] );
		}

		//-----------------------------------------
		// Good form old nboy
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->js_template_tools();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doadd'    ),
															   2 => array( 'act'   , 'templ'     ),
															   3 => array( 'id'    , $ibforums->input['id']   ),
															   4 => array( 'p'     , $ibforums->input['p']    ),
															   5 => array( 'expand', $ibforums->input['expand'] ),
													  )  , "theform"    );

		//-----------------------------------------
		// Editor prefs strip
		//-----------------------------------------



		$options .= "<fieldset class='tdfset'>
					 <legend><strong>New Template Bit Specifics</strong></legend>
					 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
					 <tr>
					   <td width='40%' class='tdrow1'>New Template Bit Name<br /><span style='color:gray'>Alphanumerics and underscores only, no spaces.</span></td>
					   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_input('func_name', $std->txt_stripslashes($_POST['func_name']))."</td>
					 </tr>
					 <tr>
					   <td width='40%' class='tdrow1'>New Template Bit Incoming Data Variables<br /><span style='color:gray'>Define the variables passed to this template bit.</span></td>
					   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_input('func_data', str_replace( "'", '&#039;', $std->txt_stripslashes($_POST['func_data']) ) )."</td>
					 </tr>
					  <tr>
					   <td width='40%' class='tdrow1'>New Template Bit Group...</td>
					   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_dropdown('group_name', $formatted_groups, $ibforums->input['group_name'] ? $ibforums->input['group_name'] : $ibforums->input['expand'] )."</td>
					 </tr>
					 <tr>
					   <td width='40%' class='tdrow1'>Or Create New Group...<br /><span style='color:gray'>Leave empty to use above group. Alphanumerics and underscores only, no spaces</span></td>
					   <td width='60%' class='tdrow1'>skin_".$ibforums->adskin->form_input('new_group_name', $std->txt_stripslashes($_POST['new_group_name']))."</td>
					 </tr>
					 </table>
					</fieldset>
					<br />";

		$ibforums->html .= $this->functions->build_generic_editor_area( array( 'act' => 'templ', 'title' => 'Add New Template Bit', 'textareaname' => 'newbit', 'textareainput' => $std->txt_stripslashes($_POST['txtnewbit']) ) );

		$ibforums->html = str_replace( '<!--BEFORETEXTAREA-->', $options, $ibforums->html );

		$formbuttons = "<div align='center' class='pformstrip'>
						<input type='submit' name='submit' value='Save Template Bit' class='realdarkbutton'>
						<input type='submit' name='savereload' value='Save and Reload Template Bit' class='realdarkbutton'>
						</div>\n";

		$ibforums->html = str_replace( '<!--IPB.EDITORBOTTOM-->', $formbuttons, $ibforums->html );

		$ibforums->html .= "<div class='tableborder'><div class='catrow2' align='center' style='padding:4px;'><b>Show me the HTML code for:&nbsp;".
							"<select name='htmlcode' onChange=\"document.theform.res.value='&'+document.theform.htmlcode.options[document.theform.htmlcode.selectedIndex].value+';'\" id='multitext'><option value='copy'>&copy;</option>
							<option value='raquo'>&raquo;</option>
							<option value='laquo'>&laquo;</option>
							<option value='#149'>&#149;</option>
							<option value='reg'>&reg;</option>
							</select>&nbsp;&nbsp;<input type='text' name='res' size=20 id='multitext'>
							&nbsp;&nbsp;<input type='button' value='select' id='editbutton' onClick='document.theform.res.focus();document.theform.res.select();'>
							<input type='button' value='Search in Templates'  class='realbutton' title='Search the templates for a string' onClick='pop_win(\"act=rtempl&code=search&suid={$template['suid']}&type=html\", \"Search\", 500,400)'>
							</div></div></form><br />";

		$ibforums->html .= $ibforums->adskin->skin_jump_menu_wrap();

		$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
		$ibforums->admin->nav[] = array( "act=templ&code=edit&id={$ibforums->input['id']}&expand={$groupname}&#{$groupname}", $this_set['set_name'] );
		$ibforums->admin->nav[] = array( '', 'Adding new template bit for set '.$this_set['set_name'] );

		$ibforums->admin->output();
	}

	//-----------------------------------------
	// DO ADD NEW BIT
	//-----------------------------------------

	function do_add_bit()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Check incoming
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id=".$ibforums->input['id'] ) );

		if ( $_POST['new_group_name'] )
		{
			if ( preg_match( "#[^\w_]#s", $_POST['new_group_name'] ) )
			{
				$ibforums->main_msg = 'The new template bit group name must only contain alphanumerics and underscores.';
				$this->add_bit();
			}
		}

		if ( ! $_POST['func_name'] )
		{
			$ibforums->main_msg = 'The new template bit name cannot be empty.';
			$this->add_bit();
		}
		else
		{
			if ( preg_match( "#[^\w_]#s", $_POST['func_name'] ) )
			{
				$ibforums->main_msg = 'The new template bit name must only contain alphanumerics and underscores.';
				$this->add_bit();
			}
		}

		if ( ! trim($_POST['txtnewbit']) )
		{
			$ibforums->main_msg = 'The new template bit HTML cannot be empty.';
			$this->add_bit();
		}

		$new_group_name = strtolower(str_replace( 'skin_', '', trim($ibforums->input['new_group_name']) ));
		$func_name      = strtolower(trim($ibforums->input['func_name']));
		$group_name     = $new_group_name ? 'skin_'.$new_group_name : $ibforums->input['group_name'];
		$func_data      = preg_replace( "#,$#", "", str_replace( '&#039', "'", trim($std->txt_stripslashes($_POST['func_data'])) ) );
		$text           = $std->txt_stripslashes($_POST['txtnewbit']);

		//-----------------------------------------
		// Make sure bit doesn't exist
		//-----------------------------------------

		if ( $row = $DB->simple_exec_query( array( 'select' => 'suid', 'from' => 'skin_templates', 'where' => "set_id={$ibforums->input['id']} AND group_name='$group_name' AND func_name='$func_name'" ) ) )
		{
			$ibforums->main_msg = "The new template bit '$func_name' already exists in group '$group_name'.";
			$this->add_bit();
		}

		//-----------------------------------------
		// INSERT NEW BIT
		//-----------------------------------------

		$DB->do_insert( 'skin_templates', array (
												  'set_id'		    => $ibforums->input['id'],
												  'group_name'      => $group_name,
												  'section_content' => $text,
												  'func_name' 		=> $func_name,
												  'func_data'		=> $func_data,
												  'updated'         => time(),
										)      );

		$new_id = $DB->get_insert_id();

		//-----------------------------------------
		// Rebuild the PHP file
		//-----------------------------------------

		$ibforums->cache_func->_recache_templates( $ibforums->input['id'], $ibforums->input['p'], $real_name );

		//-----------------------------------------
		// Back we go...
		//-----------------------------------------

		if ( ! $ibforums->input['savereload'] )
		{
			$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
			$ibforums->admin->redirect( "act=templ&code=edit&id={$ibforums->input['id']}&expand={$group_name}&#{$group_name}", "Template bit(s) updated, returning to template selection screen" );
		}
		else
		{
			//-----------------------------------------
			// Reload edit window
			//-----------------------------------------

			$ibforums->main_msg = "Template bit(s) updated";

			$ibforums->input[ 'cb_'.$new_id ] = 1;

			$this->edit_bit();
		}
	}




	//-----------------------------------------
	// EDIT TEMPLATES, STEP TWO
	//-----------------------------------------

	function edit_bit()
	{
		global $ibforums, $DB,  $std, $HTTP_COOKIE_VARS;

		$ibforums->admin->page_detail = "You may edit the HTML of this template.";
		$ibforums->admin->page_title  = "Template Editing";

		$template_bit_ids = array();

		//-----------------------------------------
		// Get $skin_names stuff
		//-----------------------------------------

		require './sources/admin/skin_info.php';

		//-----------------------------------------
		// Check for valid input...
		//-----------------------------------------

		$ids = array();

		if ( $ibforums->input['type'] == 'single' )
		{
			if ($ibforums->input['suid'] == "")
			{
				$ibforums->admin->error("You must specify an existing template set ID, go back and try again");
			}
			$ids[] = $ibforums->input['suid'];
		}
		else
		{
			foreach ($ibforums->input as $key => $value)
			{
				if ( preg_match( "/^cb_(\d+)$/", $key, $match ) )
				{
					if ($ibforums->input[$match[0]])
					{
						$ids[] = $match[1];
					}
				}
			}
 		}

 		if ( count($ids) < 1 )
 		{
 			$ibforums->admin->error("No ids selected, please go back and select some before submitting the form");
 		}

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->js_template_tools();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doedit'    ),
																 2 => array( 'act'   , 'templ'     ),
																 3 => array( 'suid'  , $ibforums->input['suid'] ),
																 4 => array( 'type'  , $ibforums->input['type'] ),
																 5 => array( 'id'    , $ibforums->input['id']   ),
																 6 => array( 'p'     , $ibforums->input['p']    ),
														)  , "theform"    );


		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "suid IN (".implode(",",$ids).")" ) );
		$DB->simple_exec();

		while ( $i = $DB->fetch_row() )
		{
			$sec_arry[ $i['suid'] ] = $i;
			$sec_arry[ $i['suid'] ]['easy_name'] = $i['func_name'];

			//-----------------------------------------
			// If easy name is available, use it
			//-----------------------------------------

			if ($bit_names[ $i['group_name'] ][ $i['func_name'] ] != "")
			{
				$sec_arry[ $i['suid'] ]['easy_name'] = $bit_names[ $i['group_name'] ][ $i['func_name'] ];
			}
		}

		//-----------------------------------------
		// Sort by easy_name
		//-----------------------------------------

		usort($sec_arry, array( 'ad_templates', 'perly_word_sort' ) );

		//-----------------------------------------
		// Editor prefs strip
		//-----------------------------------------

		$ibforums->html .= $this->functions->html_build_editor_top();

		//-----------------------------------------
		// Loop and print
		//-----------------------------------------

		foreach( $sec_arry as $id => $template )
		{
			$template['easy_name'] = preg_replace( "/^(\d+)\:\s+?/", "", $template['easy_name'] );

			//-----------------------------------------
			// Swop < and > into ascii entities
			// to prevent textarea breaking html
			//-----------------------------------------

			$setid     = $template['set_id'];
			$groupname = $template['group_name'];

			if ( ! $ibforums->input['error_raw_'.$template['suid']] )
			{
				$templ = $template['section_content'];
			}
			else
			{
				$templ = $ibforums->input['error_raw_'.$template['suid']];
			}

			$templ = preg_replace("/&/", "&#38;", $templ );
			$templ = preg_replace("/</", "&#60;", $templ );
			$templ = preg_replace("/>/", "&#62;", $templ );
			$templ = str_replace( '\n', '&#092;n', $templ );

			//-----------------------------------------
			// Altered?
			//-----------------------------------------

			if ( $template['set_id'] == $ibforums->input['id'] )
			{
				$altered_image = $this->altered;
			}
			else if ( $template['set_id'] == 1 )
			{
				$altered_image = $this->unaltered;
			}
			else
			{
				$altered_image = $this->inherited;
			}

			$ibforums->html .= $this->functions->build_editor_area($templ, $template, $altered_image);

			$template_bit_ids[] = "t{$template['suid']}";
		}

		$ibforums->html .= $this->functions->html_build_editor_bottom();

		$formbuttons = "<div align='center' class='pformstrip'>
						<input type='submit' name='submit' value='Save Template Bit(s)' class='realdarkbutton'>
						<input type='submit' name='savereload' value='Save and Reload Template Bit(s)' class='realdarkbutton'>
						</div>\n";

		$ibforums->html = str_replace( '<!--IPB.EDITORBOTTOM-->', $formbuttons, $ibforums->html );

		$ibforums->html .= "<div class='tableborder'><div class='catrow2' align='center' style='padding:4px;'><b>Show me the HTML code for:&nbsp;".
							"<select name='htmlcode' onChange=\"document.theform.res.value='&'+document.theform.htmlcode.options[document.theform.htmlcode.selectedIndex].value+';'\" id='multitext'><option value='copy'>&copy;</option>
							<option value='raquo'>&raquo;</option>
							<option value='laquo'>&laquo;</option>
							<option value='#149'>&#149;</option>
							<option value='reg'>&reg;</option>
							</select>&nbsp;&nbsp;<input type='text' name='res' size=20 id='multitext'>
							&nbsp;&nbsp;<input type='button' value='select' id='editbutton' onClick='document.theform.res.focus();document.theform.res.select();'>
							<input type='button' value='Search in Templates'  class='realbutton' title='Search the templates for a string' onClick='pop_win(\"act=rtempl&code=search&suid={$template['suid']}&type=html\", \"Search\", 500,400)'>
							</div></div></form><br />";

		//-----------------------------------------
		// Let the JS know which IDs to
		// look for (clever, no?)
		//-----------------------------------------

		$ibforums->html = str_replace( "<!--IPB.TEMPLATE_BIT_IDS-->", implode(",",$template_bit_ids), $ibforums->html );

		$ibforums->html .= $ibforums->adskin->skin_jump_menu_wrap();

		//-----------------------------------------
		// Find easy name for group
		//-----------------------------------------

		$old_groupname = $groupname;

		if ( $skin_names[ $groupname ][0] != "" )
		{
			$groupname = $skin_names[ $groupname ][0];
		}

		$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
		$ibforums->admin->nav[] = array( "act=templ&code=edit&id={$ibforums->input['id']}&expand={$old_groupname}&#{$old_groupname}", $groupname );

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// COMPLETE EDIT
	//-----------------------------------------

	function do_edit()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Check incoming
		//-----------------------------------------

		$ids    = array();
		$cb_ids = array();

		foreach ($ibforums->input as $key => $value)
		{
			if ( preg_match( "/^txt(\d+)$/", $key, $match ) )
			{
				if ($ibforums->input[$match[0]])
				{
					$ids[]    = $match[1];
					$cb_ids[ $match[1] ] = 'cb_'.$match[1];
				}
			}
		}

 		if ( count($ids) < 1 )
 		{
 			$ibforums->admin->error("No ids selected, please go back and select some before submitting the form");
 		}

 		//-----------------------------------------
		// Get the group name, etc
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "suid IN (".implode(",",$ids).")" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$template[ $r['suid'] ] = $r;
			$real_name = $r['group_name'];
		}

		$error_bits = array();

		//-----------------------------------------
		// Process my bits :o
		//-----------------------------------------

		foreach( $ids as $id )
		{
			$text = $std->txt_stripslashes($_POST['txt'.$id]);

			//-----------------------------------------
			// Sw(o|a)p back < & >
			//-----------------------------------------

			$text = preg_replace("/&#60;/", "<", $text);
			$text = preg_replace("/&#62;/", ">", $text);
			$text = preg_replace("/&#38;/", "&", $text);
			$text = str_replace( '&#092;n', '\n',$text );
			$text = str_replace( '\\n'    , '\\\\\\n', $text );

			//-----------------------------------------
			// Convert \r to nowt
			//-----------------------------------------

			$text = preg_replace("/\r/", "", $text);

			$func = preg_replace( "#,$#", "", str_replace( '&#039;', "'", trim($std->txt_stripslashes($_POST['funcdata_'.$id])) ) );

			//-----------------------------------------
			// Test to ensure they are legal
			// - catch warnings, etc
			//-----------------------------------------

			ob_start();
			eval( $this->template->convert_html_to_php( $template[ $id ]['func_name'], $func, $text ) );
			$return = ob_get_contents();
			ob_end_clean();

			if ( $return )
			{
				$error_bits[] = $id;
				continue;
			}

			//-----------------------------------------
			// Is this in our template id group?
			//-----------------------------------------

			if ( $template[ $id ]['set_id'] == $ibforums->input['id'] )
			{
				//-----------------------------------------
				// Okay, update...
				//-----------------------------------------

				$DB->do_update( 'skin_templates', array( 'section_content' => $text, 'updated' => time(), 'func_data' => $func ), 'suid='.$id );
			}
			else
			{
				//-----------------------------------------
				// No? OK - best add it as a 'new' bit
				//-----------------------------------------

				$DB->do_insert( 'skin_templates', array (
														  'set_id'		    => $ibforums->input['id'],
														  'group_name'      => $template[ $id ]['group_name'],
														  'section_content' => $text,
														  'func_name' 		=> $template[ $id ]['func_name'],
														  'func_data'		=> $func,
														  'updated'         => time(),
										        )      );

				if ($ibforums->input['type'] == 'single' )
				{
					$ibforums->input['suid'] = $DB->get_insert_id();
				}
				else
				{
					$cb_ids[ $id ] = 'cb_'.$DB->get_insert_id();
					unset($ibforums->input['cb_'.$id ]);
				}
			}
		}

		//-----------------------------------------
		// Rebuild the PHP file
		//-----------------------------------------

		$ibforums->cache_func->_recache_templates( $ibforums->input['id'], $ibforums->input['p'], $real_name );

		//-----------------------------------------
		// Back we go...
		//-----------------------------------------

		if ( count( $error_bits ) )
		{
			foreach( $error_bits as $id )
			{
				$ibforums->input['cb_'.$id ] = 1;
				$ibforums->input['error_raw_'.$id] = $std->txt_stripslashes($_POST['txt'.$id]);
				$ibforums->main_msg = "These template bits could not be saved because they cause an error when parsed. Please check the data including any HTML logic used and any input data variables.";

				$this->edit_bit();
			}
		}
		else
		{
			if ( ! $ibforums->input['savereload'] )
			{
				$ibforums->admin->nav[] = array( 'act=sets' ,'Skin Manager Home' );
				$ibforums->admin->redirect( "act=templ&code=edit&id={$ibforums->input['id']}&expand={$real_name}&#{$real_name}", "Template bit(s) updated, returning to template selection screen" );
			}
			else
			{
				//-----------------------------------------
				// Reload edit window
				//-----------------------------------------

				$ibforums->main_msg = "Template bit(s) updated";

				foreach( $cb_ids as $i => $cb )
				{
					$ibforums->input[ $cb ] = 1;
				}

				$this->edit_bit();
			}
		}
	}

	//-----------------------------------------
	// REMOVE CUSTOMIZATION
	//-----------------------------------------

	function revert_bit()
	{
		global $ibforums, $DB, $std;

		$suid = $ibforums->input['suid'];

		if ( ! $suid )
		{
			$ibforums->admin->error("You must enter a corrent template bit ID");
		}

		$row = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_templates', 'where' => 'suid='.$suid ) );

		if ( $row['set_id'] == 1 )
		{
			$ibforums->admin->error("You cannot remove a template bit from the master set.");
		}

		$DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => 'suid='.$suid ) );

		//-----------------------------------------
		// Rebuild the PHP file
		//-----------------------------------------

		$ibforums->cache_func->_recache_templates( $ibforums->input['id'], $ibforums->input['p'], $row['group_name'] );

		$ibforums->admin->redirect( "act=templ&code=edit&id={$row['set_id']}&p={$ibforums->input['p']}&expand={$row['group_name']}&#{$row['group_name']}", "Template bit(s) reverted, returning to template selection screen" );
	}


	//====================================================================================================================
	// OLD - DEPRECIATED
	//====================================================================================================================





}


?>