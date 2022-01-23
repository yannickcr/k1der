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
|   > Admin forum functions library
|   > Script written by Matt Mecham
|   > Date started: 19th November 2003
|
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


class admin_forum_functions
{
	var $type     = "";
	var $printed  = 0;
	var $show_all = 0;
	var $skins    = array();
	var $need_desc = array();
	//-----------------------------------------
	// Forum - Build Children (of the CORN!!!)
	//-----------------------------------------

	function forum_build_children($root_id, $temp_html="", $depth_guide="")
	{
		global $ibforums, $forums;

		if ( is_array( $forums->forum_cache[ $root_id ] ) )
		{
			foreach( $forums->forum_cache[ $root_id ] as $id => $forum_data )
			{
				if ( $ibforums->vars['forum_cache_minimum'] )
				{
					$forum_data['description'] = "<!--DESCRIPTION:{$forum_data['id']}-->";
					$this->need_desc[] = $forum_data['id'];
				}

				$temp_html .= $this->render_forum($forum_data, $depth_guide);

				$temp_html = $this->forum_build_children( $forum_data['id'], $temp_html, $depth_guide . $forums->depth_guide );
			}
		}

		return $temp_html;
	}

	//-----------------------------------------
	// Forum - Render forum entry (NOT of the CORN!!!)
	//-----------------------------------------

	function render_forum($r, $depth_guide="")
	{
		global $ibforums, $std, $forums;

 		if ( $this->type == 'manage' )
		{
			if ( ! is_array($ibforums->adskin->td_header[0]) )
			{
				$ibforums->adskin->td_header[0] = array( "{none}", "35%" );
				$ibforums->adskin->td_header[1] = array( "{none}", "25%" );
				$ibforums->adskin->td_header[2] = array( "{none}", "15%" );
				$ibforums->adskin->td_header[3] = array( "{none}", "25%" );
			}

			$skin_stuff = "";
			$trash_can  = "";

			if ( ($r['skin_id'] != "") and ($r['skin_id'] >= 0) )
			{
				$skin_stuff = "<br>[ "."Using Skin Set: ".$this->skins[$r['skin_id']]." ]";
			}

			$redirect =  ($r['redirect_on'] == 1) ? ' (Redirect Forum)' : '';

			if ( $r['id'] == $ibforums->vars['forum_trash_can_id'] )
			{
				$trash_can = "&nbsp;<img src='{$ibforums->adskin->img_url}/acp_trashcan.gif' border='0' title='This is the trashcan forum' />&nbsp;";
			}

			if ( ! $this->show_all )
			{
				$children = $forums->forums_get_children( $r['id'] );

				$sub       = array();
				$subforums = "";
				$count     = 0;

				if ( count($children) )
				{
					$r['name'] = "<a href='{$ibforums->base_url}&act=forum&f={$r['id']}'>".$r['name']."</a>";

					foreach ( $children as $cid )
					{
						$count++;

						$cfid = $cid;

						if ( $count == count($children) )
						{
							//-----------------------------------------
							// Last subforum, link to parent
							// forum...
							//-----------------------------------------

							if ( ! $cfid = $children[ $count - 2 ] )
							{
								$cfid = $r['id'];
							}
						}

						$sub[] = "<a href='{$ibforums->base_url}&act=forum&f={$forums->forum_by_id[$cid]['parent_id']}'>".$forums->forum_by_id[$cid]['name']."</a>";
					}
				}

				if ( count( $sub ) )
				{
					$subforums = '<br /><br />Sub-forums: '.implode( ", ", $sub );
				}

				$desc = "<div class='graytext'>{$r['description']}{$subforums}</div>";
			}
			else
			{
				$desc = "";
			}

			return $ibforums->adskin->add_td_row( array(
											 "<b>{$depth_guide}{$trash_can}{$r['name']}</b>$redirect $skin_stuff{$desc}",
											 "<center><b><a href='{$ibforums->base_url}&act=forum&code=edit&f={$r['id']}'>Settings</a></b>".
											 " | <a href='{$ibforums->base_url}&act=forum&code=pedit&f={$r['id']}'>Permissions</a></center>",

											 array("<div align='center'><a href='{$ibforums->base_url}&act=forum&code=frules&f={$r['id']}'><img src='{$ibforums->adskin->img_url}/acp_rules.gif' border='0' title='Forum Rules'></a>&nbsp;&nbsp;".
											 "<a href='{$ibforums->base_url}&act=forum&code=skinedit&f={$r['id']}'><img src='{$ibforums->adskin->img_url}/acp_edit.gif' border='0' title='Skin Options'></a>&nbsp;&nbsp;".
											 "<a href='{$ibforums->base_url}&act=forum&code=recount&f={$r['id']}'><img src='{$ibforums->adskin->img_url}/acp_resync.gif' border='0' title='Resynchronise'></a></div>", 1, 'tdrow3' ),

											 "<center><a href='{$ibforums->base_url}&act=forum&code=delete&f={$r['id']}'>Delete</a>".
											 " | <b><a href='{$ibforums->base_url}&act=forum&code=empty&f={$r['id']}'>Empty Forum</a></b></center>",
								   )      );
		}

		//-----------------------------------------
		// REORDER
		//-----------------------------------------

		else if ( $this->type == 'reorder' )
		{
			if ( ! is_array($ibforums->adskin->td_header[0]) )
			{
				$ibforums->adskin->td_header[0] = array( "{none}", "10%" );
				$ibforums->adskin->td_header[1] = array( "{none}", "90%" );
			}

			$this->printed++;

			$no_root = count( $forums->forums_get_children($ibforums->input['f']) );

			$reorder = "<select id='realbutton' name='f_{$r['id']}'>";

			for( $i = 1 ; $i <= $no_root ; $i++ )
			{
				$sel = "";

				if ( $this->printed == $i )
				{
					$sel =  'selected="selected" ';
				}

				$reorder .= "\n<option value='$i'{$sel}>$i</option>";
			}

			$reorder .= "</select>\n";

			return $ibforums->adskin->add_td_row( array(
											 $reorder,
											 "<b>{$depth_guide}".$r['name']."</b>",
								   )      );

		}

		//-----------------------------------------
		// MODERATOR
		//-----------------------------------------

		else if ( $this->type == 'moderator' )
		{
			if ( ! is_array($ibforums->adskin->td_header[0]) )
			{
				$ibforums->adskin->td_header[] = array( "Add"                , "5%" );
				$ibforums->adskin->td_header[] = array( "Forum Name"         , "40%" );
				$ibforums->adskin->td_header[] = array( "Current Moderators" , "55%" );
			}

			$mod_string = "";

			foreach( $this->moderators as $phpid => $data )
			{
				if ($data['forum_id'] == $r['id'])
				{
					if ($data['is_group'] == 1)
					{
						$mod_string .= "<tr>
										 <td width='60%'>Group: {$data['group_name']}</td>
										 <td width='20%'><a href='{$ibforums->base_url}&act=mod&code=remove&mid={$data['mid']}'>Remove</a></td>
										 <td width='20%'><a href='{$ibforums->base_url}&act=mod&code=edit&mid={$data['mid']}'>Edit</a></td>
										</tr>";
					}
					else
					{
						$mod_string .= "<tr>
										 <td width='60%'>{$data['member_name']}</td>
										 <td width='20%'><a href='{$ibforums->base_url}&act=mod&code=remove&mid={$data['mid']}'>Remove</a></td>
										 <td width='20%'><a href='{$ibforums->base_url}&act=mod&code=edit&mid={$data['mid']}'>Edit</a></td>
										</tr>";
					}
				}
			}

			if ($mod_string != "")
			{
				$these_mods = "<table cellpadding='3' cellspacing='0' width='100%' align='center'>".$mod_string."</table>";
			}
			else
			{
				$these_mods = "<center><i>Unmoderated</i></center>";
			}

			//-----------------------------------------
			// Subforums..
			//-----------------------------------------

			if ( ! $this->show_all )
			{
				$children = $forums->forums_get_children( $r['id'] );

				$sub       = array();
				$subforums = "";
				$count     = 0;

				if ( count($children) )
				{
					$r['name'] = "<a href='{$ibforums->base_url}&act=forum&f={$r['id']}'>".$r['name']."</a>";

					foreach ( $children as $cid )
					{
						$count++;

						$cfid = $cid;

						if ( $count == count($children) )
						{
							//-----------------------------------------
							// Last subforum, link to parent
							// forum...
							//-----------------------------------------

							if ( ! $cfid = $children[ $count - 2 ] )
							{
								$cfid = $r['id'];
							}
						}

						$sub[] = "<a href='{$ibforums->base_url}&act=forum&f={$forums->forum_by_id[$cid]['parent_id']}'>".$forums->forum_by_id[$cid]['name']."</a>";
					}
				}

				if ( count( $sub ) )
				{
					$subforums = '<br /><br />Sub-forums: '.implode( ", ", $sub );
				}

				$desc = "<div class='graytext'>{$r['description']}{$subforums}</div>";
			}
			else
			{
				$desc = "";
			}

			return  $ibforums->adskin->add_td_row( array(
														 "<center><input type='checkbox' name='add_{$r['id']}' value='1' /></center>",
														 "<b>{$depth_guide}".$r['name']."</b>{$desc}",
														 $these_mods
											   )     );
		}

	}

	//-----------------------------------------
	// Forum - SHOW CAT(MEOW) (MEOW _ WOOF :&)
	//-----------------------------------------

	function forum_show_cat($r, $show_buttons=1, $show_reorder=0)
	{
		global $ibforums, $std, $forums;

		if ( $this->type == 'manage' )
		{
			$this->printed++;

			$no_root = count( $forums->forum_cache['root'] );

			$reorder = "<select id='editbutton' name='f_{$r['id']}'>";

			for( $i = 1 ; $i <= $no_root ; $i++ )
			{
				$sel = "";

				if ( $this->printed == $i )
				{
					$sel =  'selected="selected" ';
				}

				$reorder .= "\n<option value='$i'{$sel}>$i</option>";
			}

			$reorder .= "</select>\n";

			if ( $show_buttons )
			{
				$html = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						 <tr>
						  <td align='left' width='40%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;' title='ID: {$r['id']}'>{$r['name']}</td>
						  <td align='right' width='60%'>".
						  $ibforums->adskin->js_make_button("New Forum"  , $ibforums->base_url."&act=forum&code=new&p={$r['id']}", 'realdarkbutton')."&nbsp;".
						  $ibforums->adskin->js_make_button("Permissions", $ibforums->base_url."&act=forum&code=pedit&f={$r['id']}", 'realdarkbutton')."&nbsp;".
						  $ibforums->adskin->js_make_button("Edit"       , $ibforums->base_url."&act=forum&code=edit&f={$r['id']}")."&nbsp;".
						  $ibforums->adskin->js_make_button("Skin"       , $ibforums->base_url."&act=forum&code=skinedit&f={$r['id']}")."&nbsp;".
						  $ibforums->adskin->js_make_button("Delete"     , $ibforums->base_url."&act=forum&code=delete&f={$r['id']}")."&nbsp;".
						   $reorder .
						  "&nbsp;&nbsp;</td>
						 </tr>
						 </table>";
			}
			else if ( $show_reorder )
			{
				$html = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						 <tr>
						  <td align='left' width='40%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;' title='ID: {$r['id']}'>{$r['name']}</td>
						  <td align='right' width='60%'>".
						  $ibforums->adskin->js_make_button("Re-order Children", $ibforums->base_url."&act=forum&code=reorder&f={$r['id']}&sub=1", 'realdarkbutton')."&nbsp;".
						  "&nbsp;&nbsp;</td>
						 </tr>
						 </table>";
			}
			else
			{
				$html = $r['name'];
			}

			$ibforums->html .= $ibforums->adskin->start_table($html);
		}
		else if ( $this->type == 'reorder' )
		{
			$ibforums->html .= $ibforums->adskin->start_table($r['name']);
		}
		else if ( $this->type == 'moderator' )
		{
			$ibforums->html .= $ibforums->adskin->start_table($r['name']);
		}

	}

	//-----------------------------------------
	// Forum - END CAT(MEOW) (MEOW _ WOOF MOO :&)
	//-----------------------------------------

	function forum_end_cat($r=array())
	{
		global $ibforums, $std, $forums;

		if ( $this->type == 'manage' )
		{
			$ibforums->html .= $ibforums->adskin->end_table();
		}
		else if ( $this->type == 'reorder' )
		{
			$ibforums->html .= $ibforums->adskin->end_table();
		}
		else if ( $this->type == 'moderator' )
		{
			$ibforums->html .= $ibforums->adskin->end_table();
		}
	}

	//-----------------------------------------
	// LSIT PIST FORUMZ!!!!@@@!1!!1!
	//-----------------------------------------

	function forums_list_forums()
	{
		global $ibforums, $DB,  $std, $forums;

		if ( $this->type == 'manage' )
		{
			foreach( $ibforums->cache['skin_id_cache'] as $id => $data )
			{
				$this->skins[ $id ] = $data['set_name'];
			}
		}

		$temp_html = "";

		$fid = intval( $ibforums->input['f'] );

		if ( $this->show_all )
		{
			foreach( $forums->forum_cache['root'] as $id => $forum_data )
			{
				$cat_data = $forum_data;

				$depth_guide = "";

				if ( is_array( $forums->forum_cache[ $forum_data['id'] ] ) )
				{
					foreach( $forums->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
					{
						if ( $ibforums->vars['forum_cache_minimum'] )
						{
							$forum_data['description'] = "<!--DESCRIPTION:{$forum_data['id']}-->";
							$this->need_desc[] = $forum_data['id'];
						}

						$temp_html .= $this->render_forum($forum_data, $depth_guide);

						$temp_html = $this->forum_build_children( $forum_data['id'], $temp_html, '<span style="color:gray">&#0124;</span>'.$depth_guide . $forums->depth_guide );
					}
				}


				$this->forum_show_cat($cat_data);
				$ibforums->html .= $temp_html;
				$this->forum_end_cat($cat_data);

				unset($temp_html);
			}
		}
		else if ( ! $fid )
		{
			foreach( $forums->forum_cache[ 'root' ] as $id => $forum_data )
			{
				$cat_data = $forum_data;

				$depth_guide = "";

				if ( is_array( $forums->forum_cache[ $forum_data['id'] ] ) )
				{
					foreach( $forums->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
					{
						if ( $ibforums->vars['forum_cache_minimum'] )
						{
							$forum_data['description'] = "<!--DESCRIPTION:{$forum_data['id']}-->";
							$this->need_desc[] = $forum_data['id'];
						}

						$temp_html .= $this->render_forum($forum_data, $depth_guide);

						//$temp_html = $this->forum_build_children( $forum_data['id'], $temp_html, '<span style="color:gray">&#0124;</span>'.$depth_guide . $forums->depth_guide );
					}
				}

				$this->forum_show_cat($cat_data);
				$ibforums->html .= $temp_html;
				$this->forum_end_cat($cat_data);


				unset($temp_html);
			}
		}
		else
		{
			//foreach( $forums->forum_cache[ $fid ] as $id => $forum_data )
			//{
				$cat_data = $forum_data;

				$depth_guide = "";

				if ( is_array( $forums->forum_cache[ $fid ] ) )
				{
					$cat_data = $forums->forum_by_id[ $fid ];

					$depth_guide = "";

					foreach( $forums->forum_cache[ $fid ] as $id => $forum_data )
					{
						if ( $ibforums->vars['forum_cache_minimum'] )
						{
							$forum_data['description'] = "<!--DESCRIPTION:{$forum_data['id']}-->";
							$this->need_desc[] = $forum_data['id'];
						}

						$temp_html .= $this->render_forum($forum_data, $depth_guide);

						//$temp_html = $this->forum_build_children( $forum_data['id'], $temp_html, '<span style="color:gray">&#0124;</span>'.$depth_guide . $forums->depth_guide );
					}
				}


				$this->forum_show_cat($forums->forum_by_id[ $fid ], 0, 1 );
				$ibforums->html .= $temp_html;
				$this->forum_end_cat($forums->forum_by_id[ $fid ]);


				unset($temp_html);
			//}
		}

		//-----------------------------------------
        // Get descriptions?
        //-----------------------------------------

        if ( $ibforums->vars['forum_cache_minimum'] and count($this->need_desc) )
        {
        	$DB->simple_construct( array( 'select' => 'id,description', 'from' => 'forums', 'where' => 'id IN('.implode( ',', $this->need_desc ) .')' ) );
        	$DB->simple_exec();

        	while( $r = $DB->fetch_row() )
        	{
        		$ibforums->html = str_replace( "<!--DESCRIPTION:{$r['id']}-->", $r['description'], $ibforums->html );
        	}
        }
	}



	/*-------------------------------------------------------------------------*/
	// forum jumpee
	// ------------------
	// Builds the forum jumpee dunnit
	/*-------------------------------------------------------------------------*/

	function ad_forums_forum_list($restrict=0)
	{
		global $ibforums, $forums;

		if ( $restrict != 1 )
		{
			$jump_array[] = array( '-1', 'Make Root (Category)' );
		}
		else
		{
			$jump_array = array();
		}

		foreach( $forums->forum_cache['root'] as $id => $forum_data )
		{
			$jump_array[] = array( $forum_data['id'], $forum_data['name'] );

			$depth_guide = $forums->depth_guide;

			if ( is_array( $forums->forum_cache[ $forum_data['id'] ] ) )
			{
				foreach( $forums->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
				{
					$jump_array[] = array( $forum_data['id'], $depth_guide.$forum_data['name'] );

					$jump_array = $this->forums_forum_list_internal( $forum_data['id'], $jump_array, $depth_guide . $forums->depth_guide );
				}
			}
		}

		return $jump_array;
	}

	function forums_forum_list_internal($root_id, $jump_array=array(), $depth_guide="")
	{
		global $ibforums, $forums;

		if ( is_array( $forums->forum_cache[ $root_id ] ) )
		{
			foreach( $forums->forum_cache[ $root_id ] as $id => $forum_data )
			{
				$jump_array[] = array( $forum_data['id'], $depth_guide.$forum_data['name'] );

				$jump_array = $this->forums_forum_list_internal( $forum_data['id'], $jump_array, $depth_guide . $forums->depth_guide );
			}
		}


		return $jump_array;
	}


	/*-------------------------------------------------------------------------*/
	// forum return data
	// ------------------
	// Returns forum data
	/*-------------------------------------------------------------------------*/

	function ad_forums_forum_data()
	{
		global $ibforums, $forums;

		foreach( $forums->forum_cache['root'] as $id => $forum_data )
		{
			$forum_data['depthed_name'] = $forum_data['name'];
			$forum_data['root_forum']   = 1;

			$jump_array[ $forum_data['id'] ] = $forum_data;

			$depth_guide = $forums->depth_guide;

			if ( is_array( $forums->forum_cache[ $forum_data['id'] ] ) )
			{
				foreach( $forums->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
				{
					$forum_data['depthed_name'] = $depth_guide.$forum_data['name'];

					$jump_array[ $forum_data['id'] ] = $forum_data;

					$jump_array = $this->forums_forum_data_internal( $forum_data['id'], $jump_array, $depth_guide . $forums->depth_guide );
				}
			}
		}

		return $jump_array;
	}

	function forums_forum_data_internal($root_id, $jump_array=array(), $depth_guide="")
	{
		global $ibforums, $forums;

		if ( is_array( $forums->forum_cache[ $root_id ] ) )
		{
			foreach( $forums->forum_cache[ $root_id ] as $id => $forum_data )
			{
				$forum_data['depthed_name'] = $depth_guide.$forum_data['name'];

				$jump_array[ $forum_data['id'] ] = $forum_data;

				$jump_array = $this->forums_forum_data_internal( $forum_data['id'], $jump_array, $depth_guide . $forums->depth_guide );
			}
		}


		return $jump_array;
	}




}





?>