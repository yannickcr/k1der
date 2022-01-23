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
|   > Language functions
|   > Module written by Matt Mecham
|   > Date started: 22nd April 2002
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

class ad_languages {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		if ( TRIAL_VERSION )
		{
			print "This feature is disabled in the trial version.";
			exit();
		}

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

			case 'add':
				$this->add_language();
				break;

			case 'edit':
				$this->do_form('edit');
				break;

			case 'edit2':
				$this->show_file();
				break;

			case 'doadd':
				$this->save_wrapper('add');
				break;

			case 'doedit':
				$this->save_langfile();
				break;

			case 'remove':
				$this->remove();
				break;

			case 'editinfo':
				$this->edit_info();
				break;

			case 'export':
				$this->export();
				break;

			case 'import':
				$this->import();
				break;

			case 'doimport':
				$this->doimport();
				break;

			case 'makedefault':
				$this->make_default();
				break;

			case 'swap':
				$this->member_swap();
				break;
			//-----------------------------------------
			default:
				$this->list_current();
				break;
		}

	}

	//-----------------------------------------
	// Swap members choice
	//-----------------------------------------

	function member_swap()
	{
		global $ibforums, $DB, $std;

		$new_dir = "";
		$old_dir = "";

		if ( $ibforums->input['old'] and $ibforums->input['new'] )
		{
			if ( $ibforums->input['old'] != 'none' )
			{
				$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid IN ( {$ibforums->input['old']}, {$ibforums->input['new']})" ) );
				$DB->simple_exec();
			}
			else
			{
				$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid={$ibforums->input['new']}" ) );
				$DB->simple_exec();
			}

			while ( $r = $DB->fetch_row() )
			{
				if (  $r['lid'] == $ibforums->input['old'] )
				{
					$old_dir = $r['ldir'];
				}

				if (  $r['lid'] == $ibforums->input['new'] )
				{
					$new_dir = $r['ldir'];
				}
			}

			if ( $new_dir and $old_dir )
			{
				$DB->do_update( 'members', array( 'language' => $new_dir ), "language='{$old_dir}'" );
			}
			else if ( $ibforums->input['old'] == 'none' )
			{
				$DB->do_update( 'members', array( 'language' => $new_dir ), "language='' or language IS NULL" );
			}
		}

		$ibforums->main_msg = "Member's language choice updated";
		$this->list_current();
	}

	//-----------------------------------------
	// Rebuild CACHE
	//-----------------------------------------

	function rebuild_cache()
	{
		global $ibforums, $DB, $std;

		$ibforums->cache['languages'] = array();

		$DB->simple_construct( array( 'select' => 'ldir,lname', 'from' => 'languages' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['languages'][] = $r;
		}

		$std->update_cache( array( 'name' => 'languages', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------

	function make_default()
	{
		global $ibforums, $DB,  $std;

		$new_dir = stripslashes(urldecode(trim($_GET['id'])));

		if ($new_dir == "")
		{
			$ibforums->admin->error("Could not resolve the new ID for the default lang pack stuff thingy thanks");
		}

		// Update conf file

		$ibforums->admin->rebuild_config( array( 'default_language' => $new_dir ) );

		// Bring it all back to yoooo!

		$std->boink_it($ibforums->adskin->base_url."&act=lang");

	}


	/*-------------------------------------------------------------------------*/
	// IMPORT - DO IT
	/*-------------------------------------------------------------------------*/

	function doimport()
	{
		global $ibforums, $DB,  $std;

		$messages = array();

		//-----------------------------------------
		// Check
		//-----------------------------------------

		if ( ! $ibforums->input['lang_name'] )
		{
			$ibforums->admin->error("You must enter a name for this language import!");
		}

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			//-----------------------------------------
			// check and load from server
			//-----------------------------------------

			if ( ! $ibforums->input['lang_location'] )
			{
				$ibforums->main_msg = "No upload file was found and no filename was specified.";
				$this->import();
			}

			if ( ! file_exists( ROOT_PATH . $ibforums->input['lang_location'] ) )
			{
				$ibforums->main_msg = "Could not find the file to open at: " . ROOT_PATH . $ibforums->input['lang_location'];
				$this->import();
			}

			if ( preg_match( "#\.gz$#", $ibforums->input['lang_location'] ) )
			{
				if ( $FH = @gzopen( ROOT_PATH.$ibforums->input['lang_location'], 'rb' ) )
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
				if ( $FH = @fopen( ROOT_PATH.$ibforums->input['lang_location'], 'rb' ) )
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
		// Check dirs, etc
		//-----------------------------------------

		$safename = substr( str_replace( " ", "", strtolower( preg_replace( "[^a-zA-Z0-9]", "", $ibforums->input['lang_name'] ) ) ), 0, 10 );

		if ( @file_exists( ROOT_PATH.'lang/'.$safename ) )
		{
			$safename = $safename . substr( time(), 5, 10 );
		}

		if ( ! $content )
		{
			$ibforums->main_msg = "The XML file appears to be empty - please check the form and try again";
			$this->import();
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

		$lang_array = array();

		foreach( $xml->xml_array['languageexport']['languagegroup']['langbit'] as $idx => $entry )
		{
			$key   = $entry['key']['VALUE'];
			$value = $entry['value']['VALUE'];
			$file  = $entry['file']['VALUE'];

			$lang_array[ $file ][ $key ] = $value;
		}

		//-----------------------------------------
		// Sort...
		//-----------------------------------------

		ksort($lang_array);

		if ( ! count( $lang_array ) )
		{
			$ibforums->main_msg = "The XML file appears to be empty - please check the form and try again";
			$this->import();
		}

		//-----------------------------------------
		// Attempt dir creation
		//-----------------------------------------

		if ( ! @mkdir( ROOT_PATH.'lang/'.$safename, 0777 ) )
		{
			$ibforums->main_msg = "Cannot create the directory '$safename' in the './lang' directory - please check directory permissions on 'lang' and try again.";
			$this->import();
		}
		else
		{
			@chmod( ROOT_PATH.'lang/'.$safename, 0777 );
		}

		//print "<pre>"; print_r( $new_file_array ); exit();

		//-----------------------------------------
		// Loop, sort - compile and save
		//-----------------------------------------

		foreach( $lang_array as $file => $data )
		{
			$new_file_array = array();

			$real_name      = 'lang_'.$file.'.php';

			foreach( $lang_array[ $file ] as $k => $v )
			{
				$new_file_array[ $k ] = $v;
			}

			ksort($new_file_array);

			if ( count( $new_file_array ) )
			{
				$file_contents = "<?php\n\n";;

				foreach( $new_file_array as $k => $v )
				{
					$file_contents .= "\n".'$lang['."'$k'".']  = "'.addslashes($v).'";';
				}

				$file_contents .= "\n?".">";

				if ( $FH = @fopen( ROOT_PATH.'lang/'.$safename.'/'.$real_name, 'w' ) )
				{
					@fwrite( $FH, $file_contents );
					@fclose( $FH );

					$messages[] = "'lang_{$file}.php' imported correctly!";
				}
				else
				{
					$messages[] = "Cannot create 'lang_{$file}.php' - skipping...";
				}

			}
			else
			{
				$messages[] = "'lang_{$file}.php' appears to be empty - skipping...";
			}

			unset($new_file_array);
			unset($file_contents);
		}

		//-----------------------------------------
		// Write to DB
		//-----------------------------------------

		$DB->do_insert( 'languages', array(
											'ldir'    => $safename,
											'lname'   => $ibforums->input['lang_name'],
											'lauthor' => $xml->xml_array['languageexport']['ATTRIBUTES']['author'],
											'lemail'  => $xml->xml_array['languageexport']['ATTRIBUTES']['email'],
					  )                   );

		$this->rebuild_cache();

		$ibforums->main_msg = "Import attempt completed<br />".implode( "\n<br />", $messages );
		$this->import();
	}

	/*-------------------------------------------------------------------------*/
	// Import XML Archive (FORM)
	/*-------------------------------------------------------------------------*/

	function import()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "This section will allow you to import an XML file containing all the language data.";
		$ibforums->admin->page_title  = "Language Pack Import";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'          , 'doimport'    ),
																 2 => array( 'act'           , 'lang'        ),
																 3 => array( 'MAX_FILE_SIZE' , '10000000000' ),
													 ) , "uploadform", " enctype='multipart/form-data'"      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "50%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "50%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Import an XML language file" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Upload XML language file from your computer</b><div style='color:gray'>The file must begin with 'ipb_language' and end with either '.xml' or '.xml.gz'</div>" ,
										  				         $ibforums->adskin->form_upload(  )
								                        )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b><u>OR</u> enter the filename of the XML language file</b><div style='color:gray'>The file must be uploaded into the forum's root folder</div>" ,
										  				         $ibforums->adskin->form_input( 'lang_location', 'ipb_language.xml.gz'  )
								                        )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Enter the name for this new language set</b><div style='color:gray'>For example: English, US, German, DE...</div>" ,
										  				         $ibforums->adskin->form_input( 'lang_name', ''  )
								                        )      );

		$ibforums->html .= $ibforums->adskin->end_form("Import XML Language Set");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}


	/*-------------------------------------------------------------------------*/
	// EXPORT: Export languages into XML download
	/*-------------------------------------------------------------------------*/

	function export()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// check
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing language set ID, go back and try again");
		}

		//-----------------------------------------
		// Get data from DB
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the information from the database");
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Set up..
		//-----------------------------------------

		$lang_dir = ROOT_PATH."lang/".$row['ldir'];

		if ( ! is_dir($lang_dir) )
		{
			$ibforums->admin->error("Could not locate $lang_dir, is the directory there?");
		}

		$lang_files = array( 'boards','buddy','calendar','emails', 'email_content', 'error','forum','global','help','legends','login','mlist',
							 'mod','msg','online','portal','post','printpage','profile','register','search','stats','subscriptions','topic','ucp'
						   );

		//-----------------------------------------
		// Start XML
		//-----------------------------------------

		$xml->xml_set_root( 'languageexport', array( 'exported' => time(), 'author' => $row['lauthor'], 'email' => $row['lemail'] ) );

		$xml->xml_add_group( 'languagegroup' );

		//-----------------------------------------
		// Get all the lang bits
		//-----------------------------------------

		foreach( $lang_files as $file )
		{
			if ( @is_file( $lang_dir.'/lang_'.$file.'.php' ) )
			{
				$lang = array();

				require( $lang_dir.'/lang_'.$file.'.php' );

				foreach( $lang as $k => $v )
				{
					$content   = array();

					$content[] = $xml->xml_build_simple_tag( 'key'  , $k    );
					$content[] = $xml->xml_build_simple_tag( 'value', $v    );
					$content[] = $xml->xml_build_simple_tag( 'file' , $file );

					$entry[] = $xml->xml_build_entry( 'langbit', $content );
				}
			}
		}

		$xml->xml_add_entry_to_group( 'languagegroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$ibforums->admin->show_download( $xml->xml_document, 'ipb_language.xml' );
	}





	function show_file()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing language set ID, go back and try again");
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the information from the database");
		}

		//-----------------------------------------

		$lang_dir   = ROOT_PATH."lang/".$row['ldir'];

		$form_array = array();

		$lang_file = $lang_dir."/".$ibforums->input['lang_file'];


		if ( ! is_writeable($lang_dir) )
		{
			$ibforums->admin->error("Cannot write into '$lang_dir', please check the CHMOD value, and if needed, CHMOD to 0777 via FTP. IBF cannot do this for you.");
		}

		if (! file_exists($lang_file) )
		{
			$ibforums->admin->error("Cannot locate {$ibforums->input['lang_file']} in '$lang_dir', please go back and check the input");
		}
		else
		{
			require $lang_file;
		}

		if ($ibforums->input['lang_file'] == 'email_content.php')
		{
			$is_email = 1;
		}

		if ( ! is_writeable($lang_file) )
		{
			$ibforums->admin->error("Cannot write to '$lang_file', please check the CHMOD value, and if needed, CHMOD to 0777 via FTP. IBF cannot do this for you.");
		}


		$ibforums->admin->page_detail = "You may edit any of the language information below.";
		$ibforums->admin->page_title  = "Edit Language set: ".$row['lname'];

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'      , 'doedit'    ),
																 2 => array( 'act'       , 'lang'      ),
																 3 => array( 'id'        , $ibforums->input['id']   ),
																 4 => array( 'lang_file' , $ibforums->input['lang_file']   ),
														)      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Block Name" , "20%" );
		$ibforums->adskin->td_header[] = array( "Content"    , "80%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Language Text: ".$ibforums->input['lang_file'] );

		foreach($lang as $k => $v)
		{
			//-----------------------------------------
			// Swop < and > into ascii entities
			// to prevent textarea breaking html
			//-----------------------------------------

			$v = stripslashes($v);

			$v = preg_replace("/&/", "&#38;", $v );
			$v = preg_replace("/</", "&#60;", $v );
			$v = preg_replace("/>/", "&#62;", $v );
			$v = preg_replace("/'/", "&#39;", $v );

			$rows = 5;

			$cols = 70;

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
																	  "&lt;ibf.lang.<b>".$k."</b>&gt;",
																	  $ibforums->adskin->form_textarea('XX_'.$k, $v, $cols, $rows),
														   )      );
		}

		$ibforums->html .= $ibforums->adskin->end_form("Edit this file");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// Edit language pack information
	//-----------------------------------------

	function edit_info()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing language set ID, go back and try again");
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the information from the database");
		}

		$final['lname'] = stripslashes($_POST['lname']);

		if (isset($_POST['lname']))
		{
			$final['lauthor'] = stripslashes($_POST['lauthor']);
			$final['lemail']  = stripslashes($_POST['lemail']);
		}

		$DB->do_update( 'languages', $final, "lid='".$ibforums->input['id']."'" );

		$this->rebuild_cache();

		$ibforums->admin->done_screen("Language pack information updated", "Manage language sets", "act=lang" );

	}

	//-----------------------------------------
	// Add language pack
	//-----------------------------------------


	function add_language()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing language set ID, go back and try again");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		//-----------------------------------------

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query that language set from the DB, so there");
		}

		//-----------------------------------------

		//-----------------------------------------

		if ( ! is_writeable(ROOT_PATH.'lang') )
		{
			$ibforums->admin->error("The directory 'lang' is not writeable by this script. Please check the permissions on that directory. CHMOD to 0777 if in doubt and try again");
		}

		//-----------------------------------------

		if ( ! is_dir(ROOT_PATH.'lang/'.$row['ldir']) )
		{
			$ibforums->admin->error("Could not locate the original language set to copy, please check and try again");
		}

		//-----------------------------------------

		$row['lname'] = $row['lname'].".2";

		// Insert a new row into the DB...

		$final = array();

		foreach($row as $k => $v)
		{
			if ($k == 'lid')
			{
				continue;
			}
			else
			{
				$final[ $k ] = $v;
			}
		}

		$DB->do_insert( 'languages', $final );

		$new_id = $DB->get_insert_id();

		//-----------------------------------------

		if ( ! $ibforums->admin->copy_dir( $ibforums->vars['base_dir'].'lang/'.$row['ldir'] , $ibforums->vars['base_dir'].'lang/'.$new_id ) )
		{
			$DB->simple_exec_query( array( 'delete' => 'languages', 'where' => "lid='$new_id'" ) );

			$ibforums->admin->error( $ibforums->admin->errors );
		}
		else
		{
			$DB->do_update( 'languages', array( 'ldir' => $new_id ), "lid='$new_id'" );
		}

		//-----------------------------------------
		// Pass to edit / add form...
		//-----------------------------------------

		$this->rebuild_cache();

		$this->do_form('add', $new_id);

	}

	//-----------------------------------------
	// REMOVE WRAPPERS
	//-----------------------------------------

	function remove()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		//-----------------------------------------


		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing image set ID, go back and try again");
		}

		if ($ibforums->input['id'] == 1)
		{
			$ibforums->admin->error("You cannot remove this language pack.");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the language information from the database");
		}

		// Is it default??????????????? ok enuff

		if ($ibforums->vars['default_language'] == "")
		{
			$ibforums->vars['default_language'] = 'en';
		}

		if ($row['ldir'] == $ibforums->vars['default_language'])
		{
			$ibforums->admin->error("You cannot remove this language pack while it is the default language directory. Please select another pack to be the default and try again");
		}

		$DB->do_update( 'members', array( 'language' => $ibforums->vars['default_language'] ), "language='{$row['ldir']}'" );

		if ( $ibforums->admin->rm_dir( $ibforums->vars['base_dir']."lang/".$row['ldir'] ) )
		{
			$DB->simple_exec_query( array( 'delete' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );

			$this->rebuild_cache();

			$std->boink_it($ibforums->adskin->base_url."&act=lang");
			exit();
		}
		else
		{
			$ibforums->admin->error("Could not remove the language pack files, please check the CHMOD permissions to ensure that this script has the correct permissions to allow this");
		}
	}



	//-----------------------------------------
	// ADD / EDIT IMAGE SETS
	//-----------------------------------------

	function save_langfile()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing language set ID, go back and try again");
		}

		if ($ibforums->input['lang_file'] == "")
		{
			$ibforums->admin->error("You must specify an existing language filename, go back and try again");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the language information from the database");
		}

		$lang_file = ROOT_PATH."lang/".$row['ldir']."/".$ibforums->input['lang_file'];

		if (! file_exists( $lang_file ) )
		{
			$ibforums->admin->error("Could not locate $lang_file, is it there?");
		}

		if (! is_writeable( $lang_file ) )
		{
			$ibforums->admin->error("Cannot write to $lang_file, please chmod to 0666 or better and try again");
		}

		$barney = array();

		foreach ($ibforums->input as $k => $v)
		{
			if ( preg_match( "/^XX_(\S+)$/", $k, $match ) )
			{
				if ( isset($ibforums->input[ $match[0] ]) )
				{
					$v = preg_replace("/&#39;/", "'", stripslashes($_POST[ $match[0] ]) );
					$v = preg_replace("/&#60;/", "<",  $v );
					$v = preg_replace("/&#62;/", ">", $v );
					$v = preg_replace("/&#38;/", "&", $v );
					$v = preg_replace("/\r/", "", $v );

					$barney[ $match[1] ] = $v;
				}
			}
		}

		if ( count($barney) < 1 )
		{
			$ibforums->admin->error("Oopsie, something has gone wrong - did you leave all the fields blank?");
		}

		$start = "<?php\n\n";

		foreach($barney as $key => $text)
		{
			$text   = preg_replace("/\n{1,}$/", "", $text);
			$start .= "\n".'$lang['."'$key'".']  = "'.addslashes($text).'";';
		}

		$start .= "\n\n?".">";

		if ($fh = fopen( $lang_file, 'w') )
		{
			fwrite($fh, $start );
			fclose($fh);
		}
		else
		{
			$ibforums->admin->error("Could not write back to $lang_file");
		}

		if ( $ibforums->input['id'] )
		{
			$ibforums->admin->done_screen("Set updated", "Manage Language Sets", "act=lang&code=edit&id={$ibforums->input['id']}", 'redirect' );
		}
		else
		{
			$ibforums->admin->done_screen("Set updated", "Manage Language Sets", "act=lang", 'redirect' );
		}
	}

	//-----------------------------------------
	// EDIT SPLASH
	//-----------------------------------------

	function do_form( $method='add', $id="" )
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------

		if ($id != "")
		{
			$ibforums->input['id'] = $id;
		}

		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("You must specify an existing language set ID, go back and try again");
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$ibforums->input['id']."'" ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not query the information from the database");
		}

		//-----------------------------------------

		$lang_dir = $ibforums->vars['base_dir']."lang/".$row['ldir'];

		$form_array = array();

		if ($method != 'add')
		{
			if ( ! is_writeable($lang_dir) )
			{
				$ibforums->admin->error("Cannot write into '$lang_dir', please check the CHMOD value, and if needed, CHMOD to 0777 via FTP. IBF cannot do this for you.");
			}
		}

		//-----------------------------------------

		if ( is_dir($lang_dir) )
		{
			$handle = opendir($lang_dir);

			while (($filename = readdir($handle)) !== false)
			{
				if (($filename != ".") && ($filename != ".."))
				{
					if (preg_match("/^index/", $filename))
					{
						continue;
					}

					if (preg_match("/\.php$/", $filename))
					{
						$form_array[] = array( $filename, preg_replace( "/\.php$/", "", $filename ) );
					}
				}
			}

			closedir($handle);
		}

		if ($row['lauthor'] and $row['lemail'])
		{
			$author = "<br><br>This language set <b>'{$row['lname']}'</b> was created by <a href='mailto:{$row['lemail']}' target='_blank'>{$row['lauthor']}</a>";
		}
		else if ($row['lauthor'])
		{
			$author = "<br><br>This language set <b>'{$row['lname']}'</b> was created by {$row['lauthor']}";
		}

		//-----------------------------------------

		$ibforums->admin->page_detail = "Please choose which language section you wish to edit below.$author $url";
		$ibforums->admin->page_title  = "Edit Language set";

		//-----------------------------------------

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'editinfo'    ),
																 2 => array( 'act'   , 'lang'       ),
																 3 => array( 'id'    , $ibforums->input['id']     ),
														)      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Edit language set information" );


		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<b>Language Set Name</b>",
													$ibforums->adskin->form_input('lname', $row['lname']),
									     )      );

		if ($method == 'add')
		{

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
														"<b>Language set author name:</b>",
														$ibforums->adskin->form_input('lauthor', $row['lauthor']),
											 )      );

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
														"<b>Language set author email:</b>",
														$ibforums->adskin->form_input('lemail', $row['lemail']),
											 )      );

		}

		$ibforums->html .= $ibforums->adskin->end_form("Edit language set details");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'edit2'    ),
												  2 => array( 'act'   , 'lang'     ),
												  3 => array( 'id'    , $ibforums->input['id']   ),
									     )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Edit language files in set '".$row['lname']."'" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<b>Please select a language file to edit</b>",
													$ibforums->adskin->form_dropdown('lang_file', $form_array),
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Edit this language file");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->vars['default_language'] == "")
		{
			$ibforums->vars['default_language'] = 'en';
		}

		$form_array = array();

		$ibforums->admin->page_detail = "You can edit, remove and create new language packs from this section";
		$ibforums->admin->page_title  = "Manage Language Sets";

		//-----------------------------------------

		$DB->cache_add_query( 'languages_list_current', array() );
		$DB->cache_exec_query();

		$used_ids = array();
		$show_array = array();

		$ibforums->html .= $ibforums->adskin->js_checkdelete();

		if ( $DB->get_num_rows() )
		{

			$ibforums->adskin->td_header[] = array( "Title"        , "40%" );
			$ibforums->adskin->td_header[] = array( "Members"      , "30%" );
			$ibforums->adskin->td_header[] = array( "Export"       , "10%" );
			$ibforums->adskin->td_header[] = array( "Edit"         , "10%" );
			$ibforums->adskin->td_header[] = array( "Remove"       , "10%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Current Language Packs In Use" );

			while ( $r = $DB->fetch_row() )
			{

				if ($ibforums->vars['default_language'] == $r['ldir'])
				{
					$root = "<span style='color:red;font-weight:bold'> (Default Language)</span>";
				}
				else
				{
					$root = " ( <a href='{$ibforums->adskin->base_url}&act=lang&code=makedefault&id=".urlencode($r['ldir'])."'>Make Default Language</a> )";
				}

				$show_array[ $r['lid'] ] .= stripslashes($r['lname'])."<br>";

				if ( in_array( $r['lid'], $used_ids ) )
				{
					continue;
				}

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".stripslashes($r['lname'])."</b> $root",
														  "<center>{$r['mcount']}</center>",
														  "<center><a href='".$ibforums->adskin->base_url."&act=lang&code=export&id={$r['lid']}'>Export</a></center>",
														  "<center><a href='".$ibforums->adskin->base_url."&act=lang&code=edit&id={$r['lid']}'>Edit</a></center>",
														  "<center><a href='javascript:checkdelete(\"act=lang&code=remove&id={$r['lid']}\")'>Remove</a></center>",
												 )      );

				$used_ids[] = $r['lid'];

				$form_array[] = array( $r['lid'], $r['lname'] );

			}

			$ibforums->html .= $ibforums->adskin->end_table();
		}

		if ( count($used_ids) < 1 )
		{
			$used_ids[] = '0';
		}

		$DB->simple_construct( array( 'select' => 'lid, ldir, lname', 'from' => 'languages', 'where' => "lid NOT IN(".implode(",",$used_ids).")" ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{

			$ibforums->adskin->td_header[] = array( "Title"  , "40%" );
			$ibforums->adskin->td_header[] = array( "Export" , "10%" );
			$ibforums->adskin->td_header[] = array( "Edit"   , "30%" );
			$ibforums->adskin->td_header[] = array( "Remove" , "20%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Current Unallocated Language Packs" );



			while ( $r = $DB->fetch_row() )
			{

				if ($ibforums->vars['default_language'] == $r['ldir'])
				{
					$root = "<span style='color:red;font-weight:bold'> (Default Language)</span>";
				}
				else
				{
					$root = " ( <a href='{$ibforums->adskin->base_url}&act=lang&code=makedefault&id=".urlencode($r['ldir'])."'>Make Default Language</a> )";
				}

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".stripslashes($r['lname'])."</b> $root",
														  "<center><a href='".$ibforums->adskin->base_url."&act=lang&code=export&id={$r['lid']}'>Export</a></center>",
														  "<center><a href='".$ibforums->adskin->base_url."&act=lang&code=edit&id={$r['lid']}'>Edit</a></center>",
														  "<center><a href='javascript:checkdelete(\"act=lang&code=remove&id={$r['lid']}\")'>Remove</a></center>",
												 )      );

				$form_array[] = array( $r['lid'], $r['lname'] );

			}

			$ibforums->html .= $ibforums->adskin->end_table();
		}

		//-----------------------------------------
		// Create new set?
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'add'     ),
												  				 2 => array( 'act'   , 'lang'    ),
									     				)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Create Language Set" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Base new language set on...</b>" ,
										  		 			      $ibforums->adskin->form_dropdown( "id", $form_array)
								 						)      );

		$ibforums->html .= $ibforums->adskin->end_form("Create new Language set");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Create new set?
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'swap'     ),
												  				 2 => array( 'act'   , 'lang'    ),
									     				)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Swap Member's Language Choice" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Member's that now use language set...</b>" ,
										  		 			      $ibforums->adskin->form_dropdown( "old", array_merge( array( -1 => array( 'none', 'No preference stored - using default' ) ), $form_array ) )
								 						)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Update to use language set...</b>" ,
										  		 			      $ibforums->adskin->form_dropdown( "new", $form_array)
								 						)      );

		$ibforums->html .= $ibforums->adskin->end_form("Swap Member&#039;s Choice");

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();

	}


}


?>