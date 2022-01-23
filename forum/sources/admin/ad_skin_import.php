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
|   > Import functions
|   > Module written by Matt Mecham
|   > Date started: 22nd April 2002
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class ad_skin_import {

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
			case 'export':
				$this->do_export();
				break;

			case 'exportimages':
				$this->do_export_images();
				break;

			case 'importtemplates':
				$this->import_xml_templates();
				break;

			case 'importimages':
				$this->import_xml_images();
				break;

			//-----------------------------------------
			default:
				$this->show_export_page();
				break;
		}

	}

	//-----------------------------------------
	// PERFORM IMPORT IMAGES
	//-----------------------------------------

	function import_xml_images()
	{
		global $ibforums, $DB, $std;

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			//-----------------------------------------
			// check and load from server
			//-----------------------------------------

			if ( ! $ibforums->input['skin_location'] )
			{
				$ibforums->main_msg = "No upload file was found and no filename was specified.";
				$this->show_export_page();
			}

			if ( ! file_exists( ROOT_PATH . $ibforums->input['skin_location'] ) )
			{
				$ibforums->main_msg = "Could not find the file to open at: " . ROOT_PATH . $ibforums->input['skin_location'];
				$this->show_export_page();
			}

			if ( preg_match( "#\.gz$#", $ibforums->input['skin_location'] ) )
			{
				if ( $FH = @gzopen( ROOT_PATH.$ibforums->input['skin_location'], 'rb' ) )
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
				if ( $FH = @fopen( ROOT_PATH.$ibforums->input['skin_location'], 'rb' ) )
				{
					$content = @fread( $FH, filesize(ROOT_PATH.$ibforums->input['skin_location']) );
					@fclose( $FH );
				}
			}

			$tmp_name = str_replace( ".gz", '', $ibforums->input['skin_location'] );
		}
		else
		{
			//-----------------------------------------
			// Get uploaded schtuff
			//-----------------------------------------

			$tmp_name = $_FILES['FILE_UPLOAD']['name'];
			$tmp_name = preg_replace( "#\.gz$#", "", $tmp_name );

			$content = $ibforums->admin->import_xml( $tmp_name );
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		require( KERNEL_PATH.'class_xmlarchive.php' );

		$xmlarchive = new class_xmlarchive( KERNEL_PATH );

		$xmlarchive->xml_read_archive_data( $content );

		//-----------------------------------------
		// Set up..
		//-----------------------------------------

		$safename = $ibforums->input['skin_name'] ? $ibforums->input['skin_name'] : preg_replace( "#ipb_images-(.+?)\.xml#i", "\\1", $tmp_name );
		$safename = substr( str_replace( " ", "", strtolower( preg_replace( "[^a-zA-Z0-9]", "", $safename ) ) ), 0, 10 );
		$images   = array();

		foreach( $xmlarchive->file_array as $f )
		{
			if ( $f['content'] and $f['filename'] )
			{
				$images[ $f['filename'] ] = array( 'content' => $f['content'],
												   'path'    => $f['path']
												 );
			}
		}

		//-----------------------------------------
		// Got owt?
		//-----------------------------------------

		if ( ! count($images) )
		{
			$ibforums->main_msg = "There were no images to import from that XMLarchive.";
			$this->show_export_page();
		}

		//-----------------------------------------
		// Reet- test to see if we
		// can create dirs
		//-----------------------------------------

		if ( ! is_writable( CACHE_PATH.'style_images' ) )
		{
			$ibforums->main_msg = 'We cannot create a new folder in the "style_images" folder - please check the CHMOD value of that folder and change to 0777 is required.';
			$this->show_export_page();
		}

		//-----------------------------------------
		// Check to make sure we're not
		// creating a DUPE!
		//-----------------------------------------

		if ( file_exists( CACHE_PATH.'style_images/'.$safename ) )
		{
			$safename .= time();
		}

		//-----------------------------------------
		// Create
		//-----------------------------------------

		if ( ! @mkdir( CACHE_PATH.'style_images/'.$safename, 0777 ) )
		{
			$ibforums->main_msg = "We are unable to create a directory in the 'style_images' folder.";
			$this->show_export_page();
		}
		else
		{
			@chmod( CACHE_PATH.'style_images/'.$safename, 0777 );
		}

		foreach( $images as $filename => $data )
		{
			//-----------------------------------------
			// Do we have a duuuur?
			//-----------------------------------------

			if ( $data['path'] )
			{
				if ( ! file_exists( CACHE_PATH.'style_images/'.$safename.'/'.$data['path'] ) )
				{
					@mkdir( CACHE_PATH.'style_images/'.$safename.'/'.$data['path'], 0777 );
					@chmod( CACHE_PATH.'style_images/'.$safename.'/'.$data['path'], 0777 );
				}

				$filename = $data['path'] . '/'. $filename;
			}

			$content = $data['content'];

			if ( $content )
			{
				if ( $FH = @fopen( CACHE_PATH.'style_images/'.$safename.'/'.$filename, 'wb' ) )
				{
					if ( @fwrite( $FH, $content ) )
					{
						@fclose( $FH );
					}
				}
			}
		}

		//-----------------------------------------
		// all done?
		//-----------------------------------------

		$ibforums->main_msg = "Image set imported!";
		$this->show_export_page();
	}

	//-----------------------------------------
	// PERFORM IMPORT TEMPLATES
	//-----------------------------------------

	function import_xml_templates()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get default skin
		//-----------------------------------------

		$default = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_default=1' ) );

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			//-----------------------------------------
			// check and load from server
			//-----------------------------------------

			if ( ! $ibforums->input['skin_location'] )
			{
				$ibforums->main_msg = "No upload file was found and no filename was specified.";
				$this->show_export_page();
			}

			if ( ! file_exists( ROOT_PATH . $ibforums->input['skin_location'] ) )
			{
				$ibforums->main_msg = "Could not find the file to open at: " . ROOT_PATH . $ibforums->input['skin_location'];
				$this->show_export_page();
			}

			if ( preg_match( "#\.gz$#", $ibforums->input['skin_location'] ) )
			{
				if ( $FH = @gzopen( ROOT_PATH.$ibforums->input['skin_location'], 'rb' ) )
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
				if ( $FH = @fopen( ROOT_PATH.$ibforums->input['skin_location'], 'rb' ) )
				{
					$content = @fread( $FH, filesize(ROOT_PATH.$ibforums->input['skin_location']) );
					@fclose( $FH );
				}
			}

			$tmp_name = str_replace( ".gz", '', $ibforums->input['skin_location'] );

		}
		else
		{
			//-----------------------------------------
			// Get uploaded schtuff
			//-----------------------------------------

			$tmp_name = $_FILES['FILE_UPLOAD']['name'];
			$tmp_name = preg_replace( "#\.gz$#", "", $tmp_name );

			$content = $ibforums->admin->import_xml( $tmp_name );
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		require( KERNEL_PATH.'class_xmlarchive.php' );

		$xmlarchive = new class_xmlarchive( KERNEL_PATH );

		$xmlarchive->xml_read_archive_data( $content );

		//-----------------------------------------
		// Get the XML documents
		//-----------------------------------------

		$import_xml = array();

		foreach( $xmlarchive->file_array as $f )
		{
			$import_xml[ $f['filename'] ] = $f['content'];
		}

		//-----------------------------------------
		// Import INFO
		//-----------------------------------------

		if ( $import_xml[ 'ipb_info.xml' ] != '' )
		{
			$info_xml = $this->_extract_xml_info( $import_xml[ 'ipb_info.xml' ], $xml, $xmlarchive );
		}

		if ( ! is_array( $info_xml ) and ! count( $info_xml ) )
		{
			$ibforums->main_msg = "The XMLarchive import doesn't appear to be valid - please check the file and try again.";
			$this->show_export_page();
		}

		//-----------------------------------------
		// Import Templates
		//-----------------------------------------

		if ( $import_xml[ 'ipb_templates.xml' ] != '' )
		{
			$templates_xml = $this->_extract_xml_templates( $import_xml[ 'ipb_templates.xml' ], $xml, $xmlarchive );
		}

		//("content-type: text/plain"); print_r($templates_xml); exit();

		//-----------------------------------------
		// Import CSS
		//-----------------------------------------

		if ( $import_xml[ 'ipb_css.xml' ] != '' )
		{
			$css_xml = $this->_extract_xml_css( $import_xml[ 'ipb_css.xml' ], $xml, $xmlarchive );
		}

		//-----------------------------------------
		// Import Macro
		//-----------------------------------------

		if ( $import_xml[ 'ipb_macro.xml' ] != '' )
		{
			$macro_xml = $this->_extract_xml_macros( $import_xml[ 'ipb_macro.xml' ], $xml, $xmlarchive );
		}

		//-----------------------------------------
		// Import WRAPPER
		//-----------------------------------------

		if ( $import_xml[ 'ipb_wrapper.xml' ] != '' )
		{
			$wrapper_xml = $this->_extract_xml_wrapper( $import_xml[ 'ipb_wrapper.xml' ], $xml, $xmlarchive );
		}

		//-----------------------------------------
		// Add new skin!
		//-----------------------------------------

		$DB->do_insert( 'skin_sets', array( 'set_name'            => $ibforums->input['skin_name'] ? $ibforums->input['skin_name'] : $info_xml['set_name'].' (Import)',
											'set_hidden'          => 0,
											'set_default'         => 0,
											'set_css_method'      => $default['set_css_method'],
											'set_skin_set_parent' => -1,
											'set_author_email'    => $info_xml['set_author_email'],
											'set_author_name'     => $info_xml['set_author_name'],
											'set_author_url'      => $info_xml['set_author_url'],
											'set_css'             => $css_xml,
											'set_wrapper'         => $wrapper_xml,
											'set_css_updated'     => time(),
											'set_emoticon_folder' => $default['set_emoticon_folder'],
											'set_image_dir'       => $default['set_image_dir']
					 )                   );

		$new_skin_id = $DB->get_insert_id();

		//-----------------------------------------
		// Insert templates...
		//-----------------------------------------

		if ( is_array( $templates_xml ) and count( $templates_xml ) )
		{
			foreach( $templates_xml as $t )
			{
				$DB->do_insert( 'skin_templates', array( 'set_id'          => $new_skin_id,
													     'group_name'      => $t['group_name'],
													     'section_content' => $t['section_content'],
													     'func_name'       => $t['func_name'],
													     'func_data'       => $t['func_data'],
													     'updated'         => time(),
													     'can_remove'      => 1,
							  )                         );
			}
		}

		//-----------------------------------------
		// Insert Macros
		//-----------------------------------------

		if ( is_array( $macro_xml ) and count( $macro_xml ) )
		{
			foreach( $macro_xml as $t )
			{
				if ( ! $t['macro_value'] and ! $t['macro_replace'] )
				{
					continue;
				}

				$DB->do_insert( 'skin_macro', array( 'macro_set'        => $new_skin_id,
													 'macro_value'      => $t['macro_value'],
													 'macro_replace'    => $t['macro_replace'],
													 'macro_can_remove' => 1,
							  )                    );
			}
		}

		//-----------------------------------------
		// Rebuild caches
		//-----------------------------------------

		$ibforums->cache_func->_rebuild_all_caches( array($new_skin_id) );

		//-----------------------------------------
		// DONE!
		//-----------------------------------------

		$ibforums->main_msg = 'Skin Set Imported! (id: '.$new_skin_id.')';

		$ibforums->main_msg .= "<br />".implode( "<br />", $ibforums->cache_func->messages );

		$this->show_export_page();
	}


	//-----------------------------------------
	// PERFORM EXPORT IMAGES THING YES!
	//-----------------------------------------

	function do_export_images()
	{
		global $ibforums, $DB, $std;

		$skin_dir = $ibforums->input['skin_dirs'];

		if ( ! @file_exists( CACHE_PATH.'style_images/'.$skin_dir ) )
		{
			$ibforums->main_msg = "We cannot locate the selected image directory - please try another";
			$this->show_export_page();
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		require( KERNEL_PATH.'class_xmlarchive.php' );

		$xmlarchive = new class_xmlarchive( KERNEL_PATH );

		$xmlarchive->strip_path = CACHE_PATH.'style_images/'.$skin_dir;

		$xmlarchive->xml_add_directory( CACHE_PATH.'style_images/'.$skin_dir );

		$xmlarchive->xml_create_archive();

		$contents = $xmlarchive->xml_get_contents();

		$ibforums->admin->show_download( $contents, 'ipb_images-'.$skin_dir.'.xml' );
	}

	//-----------------------------------------
	// PERFORM EXPORT!
	//-----------------------------------------

	function do_export()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get current skin
		//-----------------------------------------

		$skin = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.intval($ibforums->input['skin_id']) ) );

		$current  = $skin['set_skin_set_id'];
		$parent   = $skin['set_skin_set_parent'];

		$safename = substr( str_replace( " ", "", strtolower( preg_replace( "[^a-zA-Z0-9]", "", $skin['set_name'] ) ) ), 0, 10 );

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		require( KERNEL_PATH.'class_xmlarchive.php' );

		$xmlarchive = new class_xmlarchive( KERNEL_PATH );

		//-----------------------------------------
		// Get data
		//-----------------------------------------

		$templates_xml = $this->_export_get_templates( $skin, $xml, $parent );
		$css_xml       = $this->_export_get_css(       $skin, $xml, $parent );
		$macro_xml     = $this->_export_get_macro(     $skin, $xml, $parent );
		$wrapper_xml   = $this->_export_get_wrapper(   $skin, $xml, $parent );
		$info_xml      = $this->_export_get_info(      $skin, $xml, $parent );

		//header("Content-Type: text/plain");
		//print $templates_xml."\n\n".$css_xml."\n\n".$macro_xml."\n\n".$wrapper_xml."\n\n".$info_xml;
		//exit();

		//-----------------------------------------
		// Format XMLarchive
		//-----------------------------------------

		$xmlarchive->xml_add_file_contents( $info_xml     , 'ipb_info.xml');
		$xmlarchive->xml_add_file_contents( $templates_xml, 'ipb_templates.xml');
		$xmlarchive->xml_add_file_contents( $css_xml      , 'ipb_css.xml'      );
		$xmlarchive->xml_add_file_contents( $macro_xml    , 'ipb_macro.xml'    );
		$xmlarchive->xml_add_file_contents( $wrapper_xml  , 'ipb_wrapper.xml'  );

		$xmlarchive->xml_create_archive();

		$skin_xmlfile = $xmlarchive->xml_get_contents();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$ibforums->admin->show_download( $skin_xmlfile, 'ipb_skin-'.$safename.'.xml' );
	}

	/*-------------------------------------------------------------------------*/
	// _EXTRACT MACROS
	/*-------------------------------------------------------------------------*/

	function _extract_xml_macros( $content, $xml, $xmlarchive )
	{
		global $ibforums, $std, $DB;

		$return = array();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $content );

		if ( ! is_array( $xml->xml_array['macroexport']['macrogroup']['macro'][0] ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $xml->xml_array['macroexport']['macrogroup']['macro'];

			unset($xml->xml_array['macroexport']['macrogroup']['macro']);

			$xml->xml_array['macroexport']['macrogroup']['macro'][0] = $tmp;
		}

		if ( is_array( $xml->xml_array['macroexport']['macrogroup']['macro'] ) and count( $xml->xml_array['macroexport']['macrogroup']['macro']  ) )
		{
			foreach( $xml->xml_array['macroexport']['macrogroup']['macro'] as $idx => $entry )
			{
				$return[] = array( 'macro_value'   => $entry['macro_value']['VALUE'],
								   'macro_replace' => $entry['macro_replace']['VALUE'],
								 );
			}
		}

		return $return;
	}

	/*-------------------------------------------------------------------------*/
	// _EXTRACT WRAPPER
	/*-------------------------------------------------------------------------*/

	function _extract_xml_wrapper( $content, $xml, $xmlarchive )
	{
		global $ibforums, $std, $DB;

		$return = array();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $content );

		if ( ! is_array( $xml->xml_array['wrapperexport']['wrappergroup']['wrapper'][0]  ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $xml->xml_array['wrapperexport']['wrappergroup']['wrapper'];

			unset($xml->xml_array['wrapperexport']['wrappergroup']['wrapper']);

			$xml->xml_array['wrapperexport']['wrappergroup']['wrapper'][0] = $tmp;
		}

		if ( is_array( $xml->xml_array['wrapperexport']['wrappergroup']['wrapper'] ) )
		{
			foreach( $xml->xml_array['wrapperexport']['wrappergroup']['wrapper'] as $idx => $entry )
			{
				$return = $entry['wrappercontent']['VALUE'];
			}
		}

		return $return;
	}

	/*-------------------------------------------------------------------------*/
	// _EXTRACT CSS
	/*-------------------------------------------------------------------------*/

	function _extract_xml_css( $content, $xml, $xmlarchive )
	{
		global $ibforums, $std, $DB;

		$return = array();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $content );

		if ( ! is_array( $xml->xml_array['cssexport']['cssgroup']['css'][0]  ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $xml->xml_array['cssexport']['cssgroup']['css'];

			unset($xml->xml_array['cssexport']['cssgroup']['css']);

			$xml->xml_array['cssexport']['cssgroup']['css'][0] = $tmp;
		}

		if ( is_array( $xml->xml_array['cssexport']['cssgroup']['css'] )  )
		{
			foreach( $xml->xml_array['cssexport']['cssgroup']['css'] as $idx => $entry )
			{
				$return = $entry['csscontent']['VALUE'];
			}
		}

		return $return;
	}

	/*-------------------------------------------------------------------------*/
	// _EXTRACT TEMPLATES
	/*-------------------------------------------------------------------------*/

	function _extract_xml_templates( $content, $xml, $xmlarchive )
	{
		global $ibforums, $std, $DB;

		$return = array();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $content );

		if ( ! is_array( $xml->xml_array['templateexport']['templategroup']['template'][0] ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $xml->xml_array['templateexport']['templategroup']['template'];

			unset($xml->xml_array['templateexport']['templategroup']['template']);

			$xml->xml_array['templateexport']['templategroup']['template'][0] = $tmp;
		}

		if ( is_array( $xml->xml_array['templateexport']['templategroup']['template'] ) )
		{
			foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $idx => $entry )
			{
				if ( ! $entry[ 'func_name' ]['VALUE'] )
				{
					continue;
				}

				$return[] = array( 'group_name'      => $entry[ 'group_name' ]['VALUE'],
								   'section_content' => $entry[ 'section_content' ]['VALUE'],
								   'func_name'       => $entry[ 'func_name' ]['VALUE'],
								   'func_data'       => $entry[ 'func_data' ]['VALUE']
								 );
			}
		}

		return $return;
	}


	/*-------------------------------------------------------------------------*/
	// _EXTRACT INFO
	/*-------------------------------------------------------------------------*/

	function _extract_xml_info( $content, $xml, $xmlarchive )
	{
		global $ibforums, $std, $DB;

		$return = array();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $content );

		if ( ! is_array( $xml->xml_array['infoexport']['infogroup']['info'][0]  ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $xml->xml_array['infoexport']['infogroup']['info'];

			unset($xml->xml_array['infoexport']['infogroup']['info']);

			$xml->xml_array['infoexport']['infogroup']['info'][0] = $tmp;
		}

		if ( is_array( $xml->xml_array['infoexport']['infogroup']['info'] )  )
		{
			foreach( $xml->xml_array['infoexport']['infogroup']['info'] as $idx => $entry )
			{
				$return[ 'set_name' ]         = $entry['set_name']['VALUE'];
				$return[ 'set_author_email' ] = $entry['set_author_email']['VALUE'];
				$return[ 'set_author_name' ]  = $entry['set_author_name']['VALUE'];
				$return[ 'set_author_url' ]   = $entry['set_author_url']['VALUE'];
			}
		}

		return $return;
	}

	/*-------------------------------------------------------------------------*/
	// _EXPORT INFO (internal)
	/*-------------------------------------------------------------------------*/

	function _export_get_info( $skin, $xml, $parent )
	{
		global $ibforums, $std, $DB;

		$xml->xml_set_root(  'infoexport', array( 'exported' => time() ) );
		$xml->xml_add_group( 'infogroup' );

		$content[] = $xml->xml_build_simple_tag( 'set_name'        , $skin['set_name'] );
		$content[] = $xml->xml_build_simple_tag( 'set_author_email', $skin['set_author_email'] );
		$content[] = $xml->xml_build_simple_tag( 'set_author_name' , $skin['set_author_name'] );
		$content[] = $xml->xml_build_simple_tag( 'set_author_url'  , $skin['set_author_url'] );

		$entry[]   = $xml->xml_build_entry( 'info', $content );

		$xml->xml_add_entry_to_group( 'infogroup', $entry );

		$xml->xml_format_document();

		$info_xml = $xml->xml_document;

		return $info_xml;
	}

	/*-------------------------------------------------------------------------*/
	// _EXPORT WRAPPER (internal)
	/*-------------------------------------------------------------------------*/

	function _export_get_wrapper( $skin, $xml, $parent )
	{
		global $ibforums, $std, $DB;

		$raw_wrapper = "";

		if ( $skin['set_wrapper'] )
		{
			$raw_wrapper = $skin['set_wrapper'];
		}

		if ( $ibforums->input['skin_options'] != 'noparent' )
		{
			if ( $parent > 1 )
			{
				$wrapper_parent = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$parent ) );
			}

			if ( $wrapper_parent['set_wrapper'] )
			{
				$raw_wrapper = $wrapper_parent['set_wrapper'];
			}

		}

		$xml->xml_set_root(  'wrapperexport', array( 'exported' => time() ) );
		$xml->xml_add_group( 'wrappergroup' );

		$content[] = $xml->xml_build_simple_tag( 'wrappercontent', $raw_wrapper );

		$entry[]   = $xml->xml_build_entry( 'wrapper', $content );

		$xml->xml_add_entry_to_group( 'wrappergroup', $entry );

		$xml->xml_format_document();

		$wrapper_xml = $xml->xml_document;

		return $wrapper_xml;
	}

	/*-------------------------------------------------------------------------*/
	// _EXPORT MACRO (internal)
	/*-------------------------------------------------------------------------*/

	function _export_get_macro( $skin, $xml, $parent )
	{
		global $ibforums, $std, $DB;

		$macro_xml = "";
		$macros    = array();

		if ( $ibforums->input['skin_options'] != 'noparent' )
		{
			//-----------------------------------------
			// Get parent macros
			//-----------------------------------------

			if ( $parent > 1 )
			{
				$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_macro', 'where' => 'macro_set='.$parent ) );
				$DB->simple_exec();

				while ( $r = $DB->fetch_row() )
				{
					$macros[ strtolower( $r['macro_value'] ) ] = $r;
				}
			}
		}

		//-----------------------------------------
		// Get this set macro
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_macro', 'where' => 'macro_set='.$skin['set_skin_set_id'] ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$macros[ strtolower( $r['macro_value'] ) ] = $r;
		}

		//-----------------------------------------
		// Format macros into XML
		//-----------------------------------------

		if ( count( $macros ) )
		{
			$xml->xml_set_root(  'macroexport', array( 'exported' => time() ) );
			$xml->xml_add_group( 'macrogroup' );

			foreach( $macros as $key => $data )
			{
				$content = array();

				$content[] = $xml->xml_build_simple_tag( 'macro_value'  , $data['macro_value'] );
				$content[] = $xml->xml_build_simple_tag( 'macro_replace', $data['macro_replace'] );

				$entry[] = $xml->xml_build_entry( 'macro', $content );
			}

			$xml->xml_add_entry_to_group( 'macrogroup', $entry );

			$xml->xml_format_document();

			$macro_xml = $xml->xml_document;
		}

		return $macro_xml;
	}

	/*-------------------------------------------------------------------------*/
	// _EXPORT CSS (internal)
	/*-------------------------------------------------------------------------*/

	function _export_get_css( $skin, $xml, $parent )
	{
		global $ibforums, $std, $DB;

		$raw_css = "";

		if ( $ibforums->input['skin_options'] != 'noparent' )
		{
			if ( $parent > 1 )
			{
				$css_parent = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$parent ) );
			}

			if ( $css_parent['set_css'] )
			{
				$raw_css = $css_parent['set_css'];
			}
		}

		if ( $skin['set_css'] )
		{
			$raw_css = $skin['set_css'];
		}

		$xml->xml_set_root(  'cssexport', array( 'exported' => time() ) );
		$xml->xml_add_group( 'cssgroup' );

		$content[] = $xml->xml_build_simple_tag( 'csscontent', $raw_css );

		$entry[]   = $xml->xml_build_entry( 'css', $content );

		$xml->xml_add_entry_to_group( 'cssgroup', $entry );

		$xml->xml_format_document();

		$css_xml = $xml->xml_document;

		return $css_xml;
	}

	/*-------------------------------------------------------------------------*/
	// _EXPORT TEMPLATES (internal)
	/*-------------------------------------------------------------------------*/

	function _export_get_templates( $skin, $xml, $parent )
	{
		global $ibforums, $std, $DB;

		$xml->xml_set_root( 'templateexport', array( 'exported' => time(), 'versionid' => '20000', 'type' => 'export' ) );

		$xml->xml_add_group( 'templategroup' );

		if ( $ibforums->input['skin_options'] == 'noparent' )
		{
			$DB->simple_construct( array( 'select' => 'group_name, section_content, func_name, func_data', 'from' => 'skin_templates', 'where' => 'set_id='.$ibforums->input['skin_id'] ) );
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
		}
		else
		{
			//-----------------------------------------
			// Get template parents
			//-----------------------------------------

			$all_templates = array();

			if ( $parent > 1 )
			{
				$DB->simple_construct( array( 'select' => 'group_name, section_content, func_name, func_data', 'from' => 'skin_templates', 'where' => 'set_id='.$parent ) );
				$DB->simple_exec();

				while ( $r = $DB->fetch_row() )
				{
					$all_templates[ strtolower( $r['group_name'] ) .','. strtolower( $r['func_name'] ) ] = $r;
				}
			}

			$DB->simple_construct( array( 'select' => 'group_name, section_content, func_name, func_data', 'from' => 'skin_templates', 'where' => 'set_id='.$ibforums->input['skin_id'] ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$all_templates[ strtolower( $r['group_name'] )  .','. strtolower( $r['func_name'] ) ] = $r;
			}

			if ( count( $all_templates ) )
			{
				foreach( $all_templates as $name => $r )
				{
					$content = array();

					foreach ( $r as $k => $v )
					{
						$content[] = $xml->xml_build_simple_tag( $k, $v );
					}

					$entry[] = $xml->xml_build_entry( 'template', $content );
				}

				$xml->xml_add_entry_to_group( 'templategroup', $entry );
			}
		}

		$xml->xml_format_document();

		$templates_xml = $xml->xml_document;

		return $templates_xml;
	}


	//-----------------------------------------
	// SHOW EXPORT PAGE
	//-----------------------------------------

	function show_export_page()
	{
		global $ibforums, $DB, $std;

		$form_array   = array();
		$set_to_image = array();

		$ibforums->admin->page_detail = "You can download skin sets by configuring the form below. The skin XML templates (HTML, Macros, CSS & Wrapper) are independent of the image set.<br />To download a 'full' skin set, you will need to download both the skin XML and the image set.";
		$ibforums->admin->page_title  = "Export Skin Sets";

		//-----------------------------------------
		// Get skin list...
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/classes/class_display.php' );
		$display   = new display();

		$skin_list = $display->_build_skin_list();

		//-----------------------------------------
		// Do we have an incoming ID?
		//-----------------------------------------

		if ( $ibforums->input['id'] )
		{
			$skin_list = str_replace( "value='{$ibforums->input['id']}'", "value='{$ibforums->input['id']}' selected='selected'", $skin_list );
		}

		//-----------------------------------------
		// Get skins...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'order' => 'set_skin_set_id DESC' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$set_to_image[ $r['set_image_dir'] ] = $r['set_name'];
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
				if ( is_dir('./style_images/'.$file) )
				{
					$dirs[] = array( $file, 'Image Set: "'.$file.'" (used in skin: '.$set_to_image[ $file ].')' );
				}
 			}
 		}

 		closedir( $dh );

 		//-----------------------------------------
 		// start output
 		//-----------------------------------------

		$start_form_a = $ibforums->adskin->start_form( array( 1 => array( 'act' , 'import' ),
															  2 => array( 'code', 'export' ),
 												     )      );

 		$start_form_b = $ibforums->adskin->start_form( array( 1 => array( 'act' , 'import' ),
														      2 => array( 'code', 'exportimages' ),
 												     )      );

 		$start_form_c = $ibforums->adskin->start_form( array( 1 => array( 'act' , 'import' ),
															  2 => array( 'code', 'importtemplates' ),
															  3 => array( 'MAX_FILE_SIZE', '10000000000' ),
													 ) , "uploadform", " enctype='multipart/form-data'"     );

		$start_form_d = $ibforums->adskin->start_form( array( 1 => array( 'act' , 'import' ),
															  2 => array( 'code', 'importimages' ),
															  3 => array( 'MAX_FILE_SIZE', '10000000000' ),
													 ) , "uploadform", " enctype='multipart/form-data'"     );

		$ibforums->html .= "<div class='tableborder'>
							 <div class='maintitle'>Exporting...</div>
							 <div class='tablepad'>
							 <br />
							 <fieldset>
							  <legend><strong>Export Skin Templates</strong>
							  $start_form_a
							  <div style='border:1px solid #D1DCEB'>
							  <table cellpadding='4' cellspacing='0' width='100%'>
							  <tr>
							   <td class='tdrow1' width='40%'><b>Export Which Skin Template Set?</b><div class='graytext'>Please select which skin set (HTML templates, CSS, Macros & wrapper) you wish to export.</div></td>
							   <td class='tdrow2' width='60%'><select name='skin_id' class='dropdown'>{$skin_list}</select></td>
							 </tr>
							 <tr>
							   <td class='tdrow1' width='40%'><b>Export Options</b><div class='graytext'>Please choose how deep this export should look for customizations.</div></td>
							   <td class='tdrow2' width='60%'>".$ibforums->adskin->form_dropdown("skin_options",
																								   array( 0 => array( 'noparent'  , 'Export customizations in this skin only' ),
																										  1 => array( 'yesparent' , 'Export customizations in this skin and any parent skins' )
																										)
																								 ) ."</td>
							 </tr>
							 </table>
							 <div align='center' class='pformstrip'><input type='submit' class='realbutton' value='EXPORT SKIN XML' /></div>
							 </div>
							 </form>
							 </fieldset>
							 <br />
							 <fieldset>
							  <legend><strong>Export Skin Images</strong>
							  $start_form_b
							  <div style='border:1px solid #D1DCEB'>
							  <table cellpadding='4' cellspacing='0' width='100%'>
							  <tr>
							   <td class='tdrow1' width='40%'><b>Export Which Image Set?</b><div class='graytext'>Image sets are exported into the XMLarchive format. This is not suitable for safemode users and in such a case we recommend you manually download via FTP and ZIP the images.</div></td>
							   <td class='tdrow2' width='60%'>". $ibforums->adskin->form_dropdown( 'skin_dirs', $dirs )."</td>
							 </tr>
							 </table>
							 <div align='center' class='pformstrip'><input type='submit' class='realbutton' value='EXPORT SKIN IMAGES' /></div>
							 </div>
							 </form>
							 </fieldset>
							</div>
							</div>

							<br />

							<div class='tableborder'>
							 <div class='maintitle'>Importing...</div>
							 <div class='tablepad'>
							 <br />
							 <fieldset>
							  <legend><strong>Import Skin Templates</strong>
							  $start_form_c
							  <div style='border:1px solid #D1DCEB'>
							  <table cellpadding='4' cellspacing='0' width='100%'>
							  <tr>
							   <td class='tdrow1' width='40%'><b>Upload XML Template set</b><div style='color:gray'>The file must begin with 'ipb_skin-' and end with either '.xml' or '.xml.gz'</div></td>
							   <td class='tdrow2' width='60%'>". $ibforums->adskin->form_upload(  ) ."</td>
							 </tr>
							 <tr>
							   <td class='tdrow1' width='40%'><b><u>OR</u> enter the filename of the XML Template Set</b><div style='color:gray'>The file must be uploaded into the forum's root folder</div></td>
							   <td class='tdrow2' width='60%'>".$ibforums->adskin->form_input( 'skin_location'  )."</td>
							 </tr>
							 <tr>
							   <td class='tdrow1' width='40%'><b>New Skin Set Name?</b><div style='color:gray'>Leave blank to use the skin name from the XMLarchive</div></td>
							   <td class='tdrow2' width='60%'>".$ibforums->adskin->form_input( 'skin_name'  )."</td>
							 </tr>
							 </table>
							 <div align='center' class='pformstrip'><input type='submit' class='realbutton' value='IMPORT SKIN XML' /></div>
							 </div>
							 </form>
							 </fieldset>

							 <br />
							 <fieldset>
							  <legend><strong>Import Skin Images</strong>
							  $start_form_d
							  <div style='border:1px solid #D1DCEB'>
							  <table cellpadding='4' cellspacing='0' width='100%'>
							  <tr>
							   <td class='tdrow1' width='40%'><b>Upload XMLarchive Image set</b><div style='color:gray'>The file must begin with 'ipb_images-' and end with either '.xml' or '.xml.gz'</div></td>
							   <td class='tdrow2' width='60%'>". $ibforums->adskin->form_upload(  ) ."</td>
							 </tr>
							 <tr>
							   <td class='tdrow1' width='40%'><b><u>OR</u> enter the filename of the XMLarchive Image Set</b><div style='color:gray'>The file must be uploaded into the forum's root folder</div></td>
							   <td class='tdrow2' width='60%'>".$ibforums->adskin->form_input( 'skin_location'  )."</td>
							 </tr>
							 <tr>
							   <td class='tdrow1' width='40%'><b>New Image Set Directory Name?</b><div style='color:gray'>Leave blank to use the set name from the XMLarchive</div></td>
							   <td class='tdrow2' width='60%'>".$ibforums->adskin->form_input( 'skin_name'  )."</td>
							 </tr>
							 </table>
							 <div align='center' class='pformstrip'><input type='submit' class='realbutton' value='IMPORT SKIN IMAGES' /></div>
							 </div>
							 </form>
							 </fieldset>

							 </div>
							</div>";


		$ibforums->admin->output();

	}



}


?>