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
|   > Admin Cache functions library
|   > Script written by Matt Mecham
|   > Date started: 20th January 2004
|
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


class admin_cache_functions
{
	var $master_set = 1;
	var $messages   = array();
	var $no_rebuild = 0;
	var $template   = "";

	//-----------------------------------------
	// Constructor
	//-----------------------------------------

	function admin_cache_functions()
	{
		//-----------------------------------------
		// Get the libraries
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_template.php' );

		$this->template = new class_template();

		$this->template->root_path = CACHE_PATH;
	}

	//============================================================================================================
	// WRAPPER FUNCTIONS
	//============================================================================================================

	//-----------------------------------------
	// WRAPPER: Update internal cache
	//-----------------------------------------

	function _recache_wrapper($id, $parent="", $root='1')
	{
		global $ibforums, $std, $DB;

		//-----------------------------------------
		// Get wrapper from tree
		//-----------------------------------------

		$parent_in = $parent ? ','.$parent : '';

		$DB->cache_add_query( 'cache_wrapper', array( 'id' => $id, 'parent_in' => $parent_in, 'root' => $root ) );
		$DB->cache_exec_query();

		$this_set = $DB->fetch_row();

		$wrapper = $this_set['set_wrapper'];

		//-----------------------------------------
		// Get children details
		//-----------------------------------------

		$ids = array( 0 => array( 'set_skin_set_id' => $id ) );

		if ( $id == 1 and IN_DEV )
		{
			$id = -1;
		}

		$DB->cache_add_query( 'cache_empty_wrapper', array( 'parent_id' => $id ) );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$ids[] = $r;
		}

		if ( $id == -1 and IN_DEV )
		{
			$id = 1;
		}

		//-----------------------------------------
		// Update this and child db
		// caches
		//-----------------------------------------

		$theids = array();

		foreach( $ids as $i => $d )
		{
			$theids[] = $d['set_skin_set_id'];
		}

		$DB->do_update( 'skin_sets', array( 'set_cache_wrapper' => $wrapper ), 'set_skin_set_id IN ('.implode(',',$theids).') AND set_skin_set_id != 1' );

		$this->messages[] = "Done wrapper rebuild... (id: ".implode(',',$theids).")";
	}

	//============================================================================================================
	// TEMPLATE FUNCTIONS
	//============================================================================================================

	//-----------------------------------------
	// TEMPLATE: Recache and splash
	//-----------------------------------------

	function _recache_templates( $id, $parent="", $group_only="", $root=1 )
	{
		global $std, $DB, $ibforums;

		$ids = array( 0 => array( 'id' => $id, 'parent' => $parent ) );

		//-----------------------------------------
		// Get any children
		//-----------------------------------------

		if ( $id == 1 and IN_DEV )
		{
			$id = -1;
		}

		$DB->simple_construct( array( 'select' => 'set_skin_set_id, set_skin_set_parent', 'from' => 'skin_sets', 'where' => "set_skin_set_parent=$id" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ids[] = array( 'id' => $r['set_skin_set_id'], 'parent' => $r['set_skin_set_parent'] );
		}

		if ( $id == -1 and IN_DEV )
		{
			$id = 1;
		}

		//-----------------------------------------
		// SPIN TO WIN!
		//-----------------------------------------

		foreach ( $ids as $i => $tid )
		{
			$id     = $tid['id'];
			$parent = $tid['parent'];

			//-----------------------------------------
			// Get template set titles
			//-----------------------------------------

			$group_titles = $this->_get_templates( $id, $parent, 'groups' );

			foreach ( $group_titles as $name => $group )
			{
				//-----------------------------------------
				// Skip if we're only updating
				// one group..
				//-----------------------------------------

				if ( $group_only != '' )
				{
					if ( $group_only != $group['group_name'] )
					{
						continue;
					}
				}

				$this_set_list = array();

				$out = "class {$group['group_name']} {\n\n";

				$templates = $this->_get_templates( $id, $parent, 'groups', $group['group_name'] );

				foreach ($templates as $func_name => $data )
				{
					$out .= $this->template->convert_html_to_php( $data['func_name'], $data['func_data'], $data['section_content'] ) ."\n";

					if ( $data['set_id'] == $id )
					{
						$this_set_list[] = $data['func_name'];
					}
				}

				$out .= "\n\n}";

				//-----------------------------------------
				// Write to the DB...
				//-----------------------------------------

				$DB->simple_exec_query( array( 'delete' => 'skin_templates_cache', 'where' => "template_group_name='{$group['group_name']}' and template_set_id={$id}" ) );

				$DB->do_insert( 'skin_templates_cache', array(
																'template_id'            => md5(uniqid(rand(), true)),
																'template_group_name'    => $group['group_name'],
																'template_group_content' => $out,
																'template_set_id'        => $id,
															 ) );

				//-----------------------------------------
				// Write to the flatfile
				//-----------------------------------------

				$start  = '<'.'?'."php\n";
				$start .= "/*--------------------------------------------------*/\n";
				$start .= "/* FILE GENERATED BY INVISION POWER BOARD           */\n";
				$start .= "/* CACHE FILE: Skin set id: {$id}                     */\n";
				$start .= "/* CACHE FILE: Generated: ".gmdate( "D, d M Y H:i:s \G\M\T" )." */\n";
				$start .= "/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */\n";
				$start .= "/* WRITTEN TO THE DATABASE AUTOMATICALLY            */\n";
				$start .= "/*--------------------------------------------------*/\n\n";

				$end    = "\n\n/*--------------------------------------------------*/\n";
				$end   .= "/*<changed bits>\n".implode(',',$this_set_list)."\n</changed bits>*/\n";
				$end   .= "/* END OF FILE                                      */\n";
				$end   .= "/*--------------------------------------------------*/\n";
				$end   .= "\n?".">";

				$this->_write_template_to_file( $id, $group['group_name'], $start.$out.$end );
			}

			$this->messages[] = "Done HTML templates rebuild... (id: $id)";
		}
	}

	//-----------------------------------------
	// TEMPLATE: write flat-file
	//-----------------------------------------

	function _write_template_to_file($id, $group_name, $content)
	{
		global $DB, $ibforums;

		//-----------------------------------------
		// Check..
		//-----------------------------------------

		if ( $id == 1 AND ! IN_DEV)
		{
			return 0;
		}

		$return     = 0;
		$good_to_go = 0;

		if ( ! SAFE_MODE_ON )
		{
			$good_to_go = 1;

			if ( is_writeable( CACHE_PATH.'skin_cache' ) )
			{
				$good_to_go = 1;

				if ( ! is_dir( CACHE_PATH.'skin_cache/cacheid_'.$id ) )
				{
					if ( ! @ mkdir( CACHE_PATH.'skin_cache/cacheid_'.$id, 0777 ) )
					{
						$good_to_go = 0;
					}
					else
					{
						@chmod( CACHE_PATH.'skin_cache/cacheid_'.$id, 0777 );
						$good_to_go = 1;
					}
				}
				else
				{
					if ( file_exists( CACHE_PATH.'skin_cache/cacheid_'.$id.'/'.$group_name.'.php' ) )
					{
						if ( ! is_writeable( CACHE_PATH.'skin_cache/cacheid_'.$id.'/'.$group_name.'.php' ) )
						{
							$this->messages[] = "::cache_id_{$id}/{$group_name}.php not writeable - cannot cache to PHP files";
							$good_to_go = 0;
						}
						else
						{
							$good_to_go = 1;
						}
					}
					else
					{
						$good_to_go = 1;
					}
				}
			}
			else
			{
				$this->messages[] = "::cache_id_{$id} not writeable - cannot cache to PHP files";
			}
		}

		//-----------------------------------------
		// Write...
		//-----------------------------------------

		if ( $good_to_go )
		{
			if ( $FH = @fopen( CACHE_PATH.'skin_cache/cacheid_'.$id.'/'.$group_name.'.php', 'w' ) )
			{
				fwrite( $FH, $content, strlen($content) );
				fclose( $FH );
				@chmod( CACHE_PATH.'skin_cache/cacheid_'.$id.'/'.$group_name.'.php', 0777 );

				$return = 1;

				$this->messages[] = "Wrote skin_cache/cacheid_{$id}/{$group_name}.php";
			}
		}

		return $return;
	}


	//-----------------------------------------
	// TEMPLATE: Return templates from tree
	//-----------------------------------------

	function _get_templates($id, $parent, $type='all', $group='', $root=1)
	{
		global $ibforums, $DB;

		$templates = array();
		$parent_in = $parent > 0 ? ','.$parent : '';
		$return    = 'all';

		if ( $type == 'groups' and $group == '' )
		{
			//-----------------------------------------
			// Just return group titles
			//-----------------------------------------

			$DB->cache_add_query( 'cache_templates_titles', array( 'id' => $id, 'parent_in' => $parent_in, 'root' => $root ) );
			$newq = $DB->cache_exec_query();

			$return = 'titles';
		}
		else if ( $type == 'groups' and $group != '' )
		{
			//-----------------------------------------
			// Return group template bits
			//-----------------------------------------

			$DB->cache_add_query( 'cache_templates_bits', array( 'id' => $id, 'parent_in' => $parent_in, 'root' => $root, 'group' => $group ) );
			$newq = $DB->cache_exec_query();

			$return = 'group';
		}
		else
		{
			//-----------------------------------------
			// Return all...
			//-----------------------------------------

			$DB->cache_add_query( 'cache_templates_all', array( 'id' => $id, 'parent_in' => $parent_in, 'root' => $root ) );
			$newq = $DB->cache_exec_query();
		}

		//-----------------------------------------
		// Get all results
		//-----------------------------------------

		while ( $r = $DB->fetch_row($newq) )
		{
			if ( $return == 'titles' )
			{
				$templates[ $r['group_name'] ] = $r;
				$this->template_count[ $r['set_id'] ][ $r['group_name'] ]['count']++;
			}
			else if ( $return == 'group' )
			{
				$templates[ strtolower($r['func_name']) ] = $r;
			}
			else
			{
				$templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
			}
		}

		ksort($templates);

		return $templates;
	}

	//-----------------------------------------
	// TEMPLATE: Rebuild templates from PHP files
	//-----------------------------------------

	function _rebuild_templates_from_php($id)
	{
		global $ibforums, $DB, $std;

		$insert = 1;

		//-----------------------------------------
		// Rebuilds the data editable files from the PHP source files
		//-----------------------------------------

		$this->template->cache_id = $id;

		$skin_dir = CACHE_PATH."skin_cache/cacheid_".$id;

		$errors = array();

		$flag = 0;

		//-----------------------------------------
		// Is this a safe mode only skinny poos?
		//-----------------------------------------

		if ( ! file_exists( $skin_dir ) )
		{
			$this->messages[] = "This template set is a safe mode only skin and no PHP skin files exist, there is no need to run this tool on this template set.";
			return 0;
		}


		if ( ! is_readable($skin_dir) )
		{
			$this->messages[] = "FAILD: Cannot write into '$skin_dir', please check the CHMOD value, and if needed, CHMOD to 0777 via FTP. IBF cannot do this for you.";
			return 0;
		}

		if ( is_dir($skin_dir) )
		{
			if ( $handle = opendir($skin_dir) )
			{

				while (($filename = readdir($handle)) !== false)
				{
					if (($filename != ".") && ($filename != ".."))
					{

						if ( preg_match( "/\.php$/", $filename ) )
						{

							$name = preg_replace( "/^(\S+)\.(\S+)$/", "\\1", $filename );

							if ($FH = fopen($skin_dir."/".$filename, 'r') )
							{
								$fdata = fread( $FH, filesize($skin_dir."/".$filename) );
								fclose($FH);
							}
							else
							{
								$this->messages[] = "Could not open $filename for reading, skipping file...";
								continue;
							}

							$fdata = str_replace( "\r", "\n", $fdata );
							$fdata = str_replace( "\n\n", "\n", $fdata );

							if ( ! preg_match( "/\n/", $fdata ) )
							{
								$this->messages[] = "Could not find any line endings in $filename, skipping file...";
								continue;
							}

							$farray = explode( "\n", $fdata );

							//-----------------------------------------

							$functions = array();

							foreach($farray as $f)
							{

								// Skip javascript functions...

								if ( preg_match( "/<script/i", $f ) )
								{
									$script_token = 1;
								}

								if ( preg_match( "/<\/script>/i", $f ) )
								{
									$script_token = 0;
								}

								//-----------------------------------------
								// NOT IN JS
								//-----------------------------------------

								if ($script_token == 0)
								{
									if ( preg_match( "/^function\s*([\w\_]+)\s*\((.*)\)/i", $f, $matches ) )
									{
										$functions[$matches[1]] = '';
										$config[$matches[1]]    = $matches[2];
										$flag = $matches[1];
										continue;
									}
								}
								//-----------------------------------------
								// ARE IN JS
								//-----------------------------------------
								else
								{
									// Make JS safe (UBE - Ugly, but effective)

									$f = preg_replace( "#if\s+?\(#is"  , "i~f~(~"   , $f );
									$f = preg_replace( "#else#is"      , "e~lse~"   , $f );
									$f = preg_replace( "#else\s+?if#is", "e~lse~i~f", $f );
								}

								if ($flag)
								{
									$functions[$flag] .= $f."\n";
									continue;
								}

							}

							//-----------------------------------------
							// Remove current templates for this set...
							//-----------------------------------------

							$DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => "set_id='".$id."' AND group_name='$name'" ) );

							$final = "";
							$flag  = 0;

							foreach($functions as $fname => $ftext)
							{
								preg_match( "#//--starthtml--//(.+?)//--endhtml--//#s", $ftext, $matches );

								$content = str_replace( '\\n' , '\\\\\\n', $this->template->convert_php_to_html($matches[1]) );

								//-----------------------------------------
								// Unconvert JS
								//-----------------------------------------

								$content = str_replace( "i~f~(~"   , "if ("   , $content );
								$content = str_replace( "e~lse~"   , "else"   , $content );
								$content = str_replace( "e~lse~i~f", "else if", $content );

								$DB->do_insert( 'skin_templates', array (
																		 'set_id'          => $id,
																		 'group_name'      => $name,
																		 'section_content' => $content,
																		 'func_name'       => $fname,
																		 'func_data'       => trim($config[$fname]),
																		 'updated'         => time(),
															   )       );
							}

							$functions = array();

							//-----------------------------------------

						} // if *.php

					} // if not dir

				} // while loop

				closedir($handle);

			}
			else
			{
				$this->messages[] = "Could not open directory $skin_dir for reading!.";
				return 0;
			}
		}
		else
		{
			$this->messages[] = "$skin_dir is not a directory, please check the \$root_path variable in admin.php.";
			return 0;
		}

		$this->messages[] = "Completed database rebuild from PHP cache files.";
		return 1;
	}




	//============================================================================================================
	// CSS FUNCTIONS
	//============================================================================================================

	//-----------------------------------------
	// CSS: Write file to cache
	//-----------------------------------------

	function _write_css_to_cache($id, $parent="", $root='1')
	{
		global $ibforums, $std, $DB;

		//-----------------------------------------
		// Get css from tree
		//-----------------------------------------

		$parent_in = $parent ? ','.$parent : '';

		$DB->cache_add_query( 'cache_templates_css', array( 'id' => $id, 'parent_in' => $parent_in, 'root' => $root ) );
		$DB->cache_exec_query();

		$this_set = $DB->fetch_row();

		$css = $this_set['set_css'];

		$DB->simple_construct( array( 'select' => 'set_image_dir', 'from' => 'skin_sets', 'where' => "set_skin_set_id=$id" ) );
		$DB->simple_exec();

		$real_set = $DB->fetch_row();

		//-----------------------------------------
		// Get children details
		//-----------------------------------------

		$ids     = array( $id => array( 'set_skin_set_id' => $id, 'set_image_dir' => $this_set['set_image_dir'] ) );
		$set_ids = array( 0 => $id );

		if ( $id == 1 and IN_DEV )
		{
			$id = -1;
		}

		$DB->cache_add_query( 'cache_empty_css', array ( 'parent_id' => $id ) );
		$DB->cache_exec_query();

		if ( $id == -1 and IN_DEV )
		{
			$id = 1;
		}

		while ( $r = $DB->fetch_row() )
		{
			$ids[ $r['set_skin_set_id'] ] = $r;
			$set_ids[] = $r['set_skin_set_id'];
		}

		//-----------------------------------------
		// Update this and child db
		// caches
		//-----------------------------------------

		if ( count($set_ids) )
		{
			$DB->simple_construct( array( 'select' => 'set_image_dir, set_skin_set_id', 'from' => 'skin_sets', 'where' => 'set_skin_set_id IN('. implode(",",$set_ids) .')' ) );
			$DB->simple_exec();

			while ( $newid = $DB->fetch_row() )
			{
				$ids[ $newid['set_skin_set_id'] ]['set_image_dir'] = $newid['set_image_dir'];
			}
		}

		//-----------------------------------------
		// Make sure inline sheets
		// have the proper style_images folder
		//-----------------------------------------

		$css = str_replace( "url({$real_set['set_image_dir']}/", "url(style_images/<#IMG_DIR#>/", $css );
		$css = str_replace( "url(<#IMG_DIR#>/", "url(style_images/<#IMG_DIR#>/", $css );
		$css = preg_replace( "#url\((style_images/)?\d+?/#i", "url(style_images/<#IMG_DIR#>/", $css );

		if ( $ibforums->vars['ipb_img_url'] )
		{
			$css = str_replace( "url(style_images/", "url({$ibforums->vars['ipb_img_url']}style_images/", $css );
		}

		$DB->do_update( 'skin_sets', array( 'set_cache_css' => $css ), 'set_skin_set_id IN ('.implode(',',$set_ids).') AND set_skin_set_id != 1' );

		//-----------------------------------------
		// Update flat-file caches
		//-----------------------------------------

		$start  = "/*------------------------------------------------------------------*/\n";
		$start .= "/* FILE GENERATED BY INVISION POWER BOARD                           */\n";
		$start .= "/* DO NOT EDIT BY HAND WITHOUT RESYNCHRONISING BACK TO THE DATABASE */\n";
		$start .= "/* OR CHANGES TO THIS FILE WILL BE LOST WHEN NEXT EDITED FROM THE   */\n";
		$start .= "/* ADMIN CONTROL PANEL                                              */\n";
		$start .= "/* STYLE DIRECTORY: <#IMG_DIR#>                                     */\n";
		$start .= "/* CACHE FILE: Generated: ".gmdate( "D, d M Y H:i:s \G\M\T" )." */\n";
		$start .= "/*------------------------------------------------------------------*/\n\n";
		$start .= "/*~START CSS~*/\n\n";

		$css = $std->txt_windowstounix($start.$css);

		if ( file_exists( CACHE_PATH."style_images" ) )
		{
			if ( is_writeable( CACHE_PATH."style_images" ) )
			{
				foreach( $ids as $id )
				{
					@unlink( CACHE_PATH."style_images/css_".$id['set_skin_set_id'].".css" );

					//-----------------------------------------
					// Fix up relative stuff
					//-----------------------------------------

					$thiscss = str_replace( '<#IMG_DIR#>', $id['set_image_dir'], $css );

					if ( ! $ibforums->vars['ipb_img_url'] )
					{
						$thiscss = str_replace( 'style_images/', '', $thiscss );
					}

					//-----------------------------------------
					// Write..
					//-----------------------------------------

					if ( $FH = @fopen( CACHE_PATH."style_images/css_".$id['set_skin_set_id'].".css", 'w' ) )
					{
						@fputs( $FH, $thiscss, strlen($thiscss) );
						@fclose($FH);
						@chmod( CACHE_PATH."style_images/css_".$id['set_skin_set_id'].".css", 0777 );

						$this->messages[] = "Rebuilding css file for css_{$id['set_skin_set_id']}...";
					}
					else
					{
						$this->messages[] = "<br /><b>Cache file css_{$id['set_skin_set_id']} not updated. Check CHMOD permissions on ./style_images and ./style_images/css_{$id['set_skin_set_id']}.css</b>";
					}
				}
			}
			else
			{
				$this->messages[] = "<b>Cache file(s) not updated. Check CHMOD permissions on ./style_images and ./style_images/css_{$id['set_skin_set_id']}.css</b>";
			}
		}
		else
		{
			$this->messages[] = "<b>Cache file(s) not updated. style_images folder not present</b>";
		}
	}


	//============================================================================================================
	// MACRO FUNCTIONS
	//============================================================================================================

	//-----------------------------------------
	// MACRO: Recache macros
	//-----------------------------------------

	function _recache_macros($id, $parent="", $root=1)
	{
		global $DB, $ibforums;

		$ids = array( 0 => array( 'id' => $id, 'parent' => $parent ) );

		//-----------------------------------------
		// Get any children
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'set_skin_set_id, set_skin_set_parent', 'from' => 'skin_sets', 'where' => "set_skin_set_parent=$id" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ids[] = array( 'id' => $r['set_skin_set_id'], 'parent' => $r['set_skin_set_parent'] );
		}

		$DB->manual_addslashes = 1;

		foreach( $ids as $i )
		{
			$macros = $this->_get_macros($i['id'], $i['parent'], $root);
			$this->messages[] = "Done.... (Macro ID: {$i['id']})...";
			$DB->do_update( 'skin_sets', array( 'set_cache_macro' => $DB->add_slashes(serialize($macros)) ), 'set_skin_set_id='.$i['id'] );
		}

		$DB->manual_addslashes = 0;
	}


	//-----------------------------------------
	// MACRO: Return macros from tree
	//-----------------------------------------

	function _get_macros($id, $parent, $root=1)
	{
		global $ibforums, $DB;

		$macros = array();

		$parent_in = $parent ? ','.$parent : '';

		//-----------------------------------------
		// Get macros from db, we want to return macros in order
		// so that skin specific macros are returned over root
		// macros. We use instr( concat() ) to search the parent
		// list for the macro set and return the position. The lower
		// the pos, the higher up the tree. Use DESC on this 'theorder'
		// to return specific macros last so that they overwrite root
		// macros.... (thats the theory)
		//-----------------------------------------

		$DB->cache_add_query( 'cache_templates_macros', array( 'id' => $id, 'parent_in' => $parent_in, 'root' => $root ) );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$macros[ strtolower( $r['macro_value'] ) ] = $r;
		}

		ksort($macros);

		return $macros;
	}

	//============================================================================================================
	// MAIN FUNCTIONS
	//============================================================================================================

	//-----------------------------------------
	// MAIN: Rebuild all caches
	//-----------------------------------------

	function _rebuild_all_caches( $affected )
	{
		global $ibforums, $DB;

		$this->no_rebuild = 1;

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'order' => 'set_skin_set_parent, set_skin_set_id ASC' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$skins[ $r['set_skin_set_id'] ] = $r;
		}

		//-----------------------------------------
		// check all affected ids
		//-----------------------------------------

		foreach( $affected as $aid )
		{
			if ( $aid == 1 and ! IN_DEV )
			{
				continue;
			}

			//-----------------------------------------
			// Custom Macros
			//-----------------------------------------

			$this->messages[] = "Rebuilding Macros for set {$skins[$aid]['set_name']}...";

			$this->_recache_macros($aid, $skins[ $aid ]['set_skin_set_parent']);

			//-----------------------------------------
			// Custom CSS
			//-----------------------------------------

			$this->messages[] = "Rebuilding CSS for set {$skins[$aid]['set_name']}...";

			$this->_write_css_to_cache( $aid, $skins[ $aid ]['set_skin_set_parent'] );

			//-----------------------------------------
			// Custom HTML
			//-----------------------------------------

			$this->messages[] = "Rebuilding HTML templates cache for set {$skins[$aid]['set_name']}...";

			$this->_recache_templates( $aid, $skins[ $aid ]['set_skin_set_parent'] );

			//-----------------------------------------
			// Custom Wrappers
			//-----------------------------------------

			$this->messages[] = "Rebuilding wrappers cache for set {$skins[$aid]['set_name']}...";

			$this->_recache_wrapper( $aid, $skins[ $aid ]['set_skin_set_parent'] );
		}

		$this->messages[] = "Rebuilding the skin set ID relationship cache...";

		$this->_rebuild_skin_id_cache($skins);

		return $message;
	}

	//-----------------------------------------
	// MAIN: Rebuild cache entry
	//-----------------------------------------

	function _rebuild_skin_id_cache($skins=array())
	{
		global $ibforums, $DB, $std;

		$ibforums->cache['skin_id_cache'] = array();

		if ( ! count( $skins ) )
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'order' => 'set_skin_set_parent, set_skin_set_id ASC' ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$skins[ $r['set_skin_set_id'] ] = $r;
			}
		}

		foreach( $skins as $row )
		{
			if ( $row['set_skin_set_parent'] > 1 )
			{
				$parent = ','.$row['set_skin_set_parent'];
			}

			$ibforums->cache['skin_id_cache'][ $row['set_skin_set_id'] ] = array( 'set_skin_set_id'  => $row['set_skin_set_id'],
																				  'set_tree_list'    => $row['set_skin_set_id'].$parent.',1',
																				  'set_hidden'       => $row['set_hidden'],
																				  'set_default'      => $row['set_default'],
																				  'set_parent'       => $row['set_skin_set_parent'],
																				  'set_name'         => $row['set_name'],
																				);
		}

		$std->update_cache( array( 'name' => 'skin_id_cache', 'array' => 1, 'deletefirst' => 1 ) );

		return $ibforums->cache['skin_id_cache'];
	}

}





?>