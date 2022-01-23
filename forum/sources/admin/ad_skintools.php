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
|   > Skin Tools
|   > Module written by Matt Mecham
|   > Date started: 22nd January 2004
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

class ad_skintools {

	var $base_url;
	var $db_html_files = "";
	var $ff_html_files = "";
	var $skin_id       = "";
	var $ff_fixes      = array();
	var $log           = array();

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "Please read the instructions for each tool carefully.";
		$ibforums->admin->page_title  = "Skin Set Tools";

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
			case 'rebuildcaches':
				$this->rebuildcaches();
				break;

			case 'rebuildmaster':
				$this->rebuildmaster();
				break;

			case 'rebuildmasterhtml':
				$this->rebuildmaster_html();
				break;

			case 'changemember':
				$this->change_member();
				break;

			//-----------------------------------------
			// Search stuff
			//-----------------------------------------

			case 'searchsplash':
				$this->searchreplace_start();
				break;

			case 'simplesearch':
				$this->simple_search();
				break;

			case 'searchandreplace':
				$this->search_and_replace();
				break;

			//-----------------------------------------
			// Search stuff
			//-----------------------------------------

			case 'easylogo':
				$this->easy_logo_start();
				break;
			case 'easylogo_complete':
				$this->easy_logo_complete();
				break;
			default:
				$this->show_intro();
				break;
		}

	}

	//-----------------------------------------
	// EASY LOGO CHANGER (COMPLETE)
	//-----------------------------------------

	function easy_logo_complete()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Init
		//-----------------------------------------

		$master = array();

		//-----------------------------------------
		// Check id
		//-----------------------------------------

		if ( ! $ibforums->input['set_skin_set_id'] )
		{
			$ibforums->main_msg = "No skin set ID was passed. Please ensure you actually chose a skin set to edit";
			$this->easy_logo_start();
		}

		//-----------------------------------------
		// Grab the default template bit
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "group_name='skin_global' AND func_name='global_board_header'" ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$master[ $r['set_id'] ] = $r;
		}

		if ( ! is_array($master[ $ibforums->input['set_skin_set_id'] ]) )
		{
			$final_html = $master[1]['section_content'];
		}
		else
		{
			$final_html = $master[ $ibforums->input['set_skin_set_id'] ]['section_content'];
		}

		if ( ! strstr( $final_html, '<!--ipb.logo.end-->' ) )
		{
			$ibforums->main_msg = "Cannot locate the logo image tags for this skin set - please make sure your templates are up to date.";
			$this->easy_logo_start();
		}

		//-----------------------------------------
		// Check for content
		//-----------------------------------------

		if ( ! $_POST['headerhtml'] )
		{
			//$ibforums->main_msg = "The basic content section was left empty!";
			//$this->easy_logo_start();
		}

		if ( ! $_POST['javascripthtml'] )
		{
			//$ibforums->main_msg = "The javascript content section was left empty!";
			//$this->easy_logo_start();
		}

		if ( ! $_POST['leftlinkshtml'] )
		{
			//$ibforums->main_msg = "The left links content section was left empty!";
			//$this->easy_logo_start();
		}

		if ( ! $_POST['rightlinkshtml'] )
		{
			//$ibforums->main_msg = "The right links content section was left empty!";
			//$this->easy_logo_start();
		}

		//-----------------------------------------
		// Check for tags
		//-----------------------------------------

		foreach( array( 'JAVASCRIPT', 'BOARD_LOGO', 'LEFT_HAND_SIDE_LINKS', 'RIGHT_HAND_SIDE_LINKS' ) as $hehe )
		{
			if ( ! strstr( $_POST['headerhtml'], '<{'.$hehe.'}>' ) )
			{
				//$ibforums->main_msg = "The &lt;{".$hehe."}&gt; tag is missing!";
				//$this->easy_logo_start();
			}
		}

		//-----------------------------------------
		// Upload or new logo?
		//-----------------------------------------

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			if ( ! $_POST['logo_url'] )
			{
				$ibforums->main_msg = "You must either upload a new logo or enter a URL";
				$this->easy_logo_start();
			}

			$newlogo = $_POST['logo_url'];
		}
		else
		{
			if ( ! is_writable( CACHE_PATH.'style_images' ) )
			{
				$ibforums->main_msg = "You must ensure that 'style_images' has the correct CHMOD value to allow PHP to write into it. Try 0777 if all else fails.";
				$this->easy_logo_start();
			}

			//-----------------------------------------
			// Upload
			//-----------------------------------------

			$FILE_NAME = $_FILES['FILE_UPLOAD']['name'];
			$FILE_SIZE = $_FILES['FILE_UPLOAD']['size'];
			$FILE_TYPE = $_FILES['FILE_UPLOAD']['type'];

			//-----------------------------------------
			// Naughty Opera adds the filename on the end of the
			// mime type - we don't want this.
			//-----------------------------------------

			$FILE_TYPE = preg_replace( "/^(.+?);.*$/", "\\1", $FILE_TYPE );

			//-----------------------------------------
			// Correct file type?
			//-----------------------------------------

			if ( ! preg_match( "#\.(?:gif|jpg|jpeg|png)$#is", $FILE_NAME ) )
			{
				$ibforums->main_msg = "The file you uploaded is not in the correct format. It has to be either a GIF, JPEG or PNG image.";
				$this->easy_logo_start();
			}

			if ( move_uploaded_file( $_FILES[ 'FILE_UPLOAD' ]['tmp_name'], CACHE_PATH."style_images/".$FILE_NAME) )
			{
				@chmod( CACHE_PATH."style_images/".$FILE_NAME, 0777 );
			}
			else
			{
				$ibforums->main_msg = "The upload failed. Please check permissions on the 'style_images' directory and make sure the uploaded file is less that 2mb in size.";
				$this->easy_logo_start();
			}

			$newlogo = "style_images/".urlencode($FILE_NAME);
		}

		//-----------------------------------------
		// Convert back stuff
		//-----------------------------------------

		foreach( array( 'headerhtml', 'javascripthtml', 'leftlinkshtml', 'rightlinkshtml' ) as $mail )
		{
			//$_POST[ $mail ] = $ibforums->admin->form_to_text( $_POST[ $mail ] );
			//$_POST[ $mail ] = str_replace( "\r\n", "\n", $_POST[ $mail ] );
		}

		//-----------------------------------------
		// Okay! Form the template
		//-----------------------------------------

		//$final_html = $_POST['headerhtml'];
		//$final_html = str_replace( "<{BOARD_LOGO}>", "<!--ipb.logo.start--><img src='$newlogo' alt='IPB' style='vertical-align:top' border='0' /><!--ipb.logo.end-->"      , $final_html );
		//$final_html = str_replace( "<{JAVASCRIPT}>", "<!--ipb.javascript.start-->\n{$_POST['javascripthtml']}\n<!--ipb.javascript.end-->"       , $final_html );
		//$final_html = str_replace( "<{LEFT_HAND_SIDE_LINKS}>", "<!--ipb.leftlinks.start-->{$_POST['leftlinkshtml']}<!--ipb.leftlinks.end-->"    , $final_html );
		//$final_html = str_replace( "<{RIGHT_HAND_SIDE_LINKS}>", "<!--ipb.rightlinks.start-->{$_POST['rightlinkshtml']}<!--ipb.rightlinks.end-->", $final_html );

		$final_html = preg_replace( "#<!--ipb.logo.start-->.+?<!--ipb.logo.end-->#si", "<!--ipb.logo.start--><img src='$newlogo' alt='IPB' style='vertical-align:top' border='0' /><!--ipb.logo.end-->"      , $final_html );

		//-----------------------------------------
		// Update the DeeBee
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => "set_id={$ibforums->input['set_skin_set_id']} AND group_name='skin_global' AND func_name='global_board_header'" ) );

		$DB->do_insert( 'skin_templates', array( 'section_content' => $final_html,
												 'set_id'          => $ibforums->input['set_skin_set_id'],
												 'group_name'      => 'skin_global',
												 'func_name'       => 'global_board_header'
					 )                         );

		$ibforums->cache_func->_rebuild_all_caches(array($ibforums->input['set_skin_set_id']));

		$ibforums->main_msg = 'Logo Changed and Skin Set Caches Rebuilt (id: '.$ibforums->input['set_skin_set_id'].')';

		$ibforums->main_msg .= "<br />".implode("<br />", $ibforums->cache_func->messages);

		$this->easy_logo_start();
	}

	//-----------------------------------------
	// EASY LOGO CHANGER (START)
	//-----------------------------------------

	function easy_logo_start()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Init
		//-----------------------------------------

		$master    = array();
		$skin_list = "";
		$html      = array();

		//-----------------------------------------
		// Grab the default template bit
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "group_name='skin_global' AND func_name='global_board_header'" ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$master[ $r['set_id'] ] = $r;
		}

		if ( ! $master[1]['section_content'] )
		{
			$ibforums->main_msg = "Cannot locate the master template bit 'global_board_header'";
			$this->show_intro();
		}

		if ( ! strstr( $master[1]['section_content'], '<!--ipb.logo.end-->' ) )
		{
			$ibforums->main_msg = "Cannot locate the logo image tags - please make sure your templates are up to date.";
			$this->show_intro();
		}

		//-----------------------------------------
		// Get Skin Names
		//-----------------------------------------

		$skin_list = $this->_get_skinlist();

		//-----------------------------------------
		// get URL
		//-----------------------------------------

		preg_match( "#<!--ipb.logo.start--><img src=[\"'](.+?)[\"'].+?<!--ipb.logo.end-->#si", $master[1]['section_content'], $match );

		$current_img_url = $match[1];

		//-----------------------------------------
		// get current HTML
		//-----------------------------------------

		$current_html = $master[1]['section_content'];

		$current_html = preg_replace( "#<!--ipb.javascript.start-->.+?<!--ipb.javascript.end-->#is"               , "<{JAVASCRIPT}>"                   , $current_html );
		$current_html = preg_replace( "#<!--ipb.logo.start--><img src=[\"'](.+?)[\"'].+?<!--ipb.logo.end-->#si"   , "<{BOARD_LOGO}>"                   , $current_html );
		$current_html = preg_replace( "#<!--ipb.leftlinks.start-->.+?<!--ipb.leftlinks.end-->#si"                 , "<{LEFT_HAND_SIDE_LINKS}>"         , $current_html );
		$current_html = preg_replace( "#<!--ipb.rightlinks.start-->.+?<!--ipb.rightlinks.end-->#si"               , "<{RIGHT_HAND_SIDE_LINKS}>"        , $current_html );

		//-----------------------------------------
		// Regex out me bits
		//-----------------------------------------

		preg_match( "#<!--ipb.javascript.start-->(.+?)<!--ipb.javascript.end-->#si", $master[1]['section_content'], $match );
		$html['javascript'] = $ibforums->admin->text_to_form($match[1]);

		preg_match( "#<!--ipb.leftlinks.start-->(.+?)<!--ipb.leftlinks.end-->#si"  , $master[1]['section_content'], $match );
		$html['leftlinks']  = $ibforums->admin->text_to_form($match[1]);

		preg_match( "#<!--ipb.rightlinks.start-->(.+?)<!--ipb.rightlinks.end-->#si"  , $master[1]['section_content'], $match );
		$html['rightlinks']  = $ibforums->admin->text_to_form($match[1]);

		$current_html        = $ibforums->admin->text_to_form($current_html);

		//-----------------------------------------
		// Can we upload into style_images?
		//-----------------------------------------

		if ( ! is_writable( CACHE_PATH.'style_images' ) )
		{
			$warning = "<div class='redbox' style='padding:4px'><strong>WARNING: Unable to upload into 'style_images'. If you wish to upload a file, please CHMOD that directory now!</strong></div>";
		}

		//-----------------------------------------
		// Start the form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'skintools'          ),
															     2 => array( 'code' , 'easylogo_complete'  ),
															     3 => array( 'MAX_FILE_SIZE', '10000000000' ),
													 ) , "uploadform", " enctype='multipart/form-data'"     );


		$ibforums->html .= "<div class='tableborder'>
							<div class='maintitle'>Easy Logo Changer</div>
							<div class='tablepad'>
							$warning
							<fieldset class='tdfset'>
							 <legend><strong>Configuration</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tdrow1'>Apply to which skin set?<div class='graytext'>If you've already modified the board header via the template editing section, this will overwrite your modifications</div></td>
							   <td width='60%' class='tdrow1'>$skin_list</td>
							 </tr>
							 <tr>
							   <td width='40%' class='tdrow1'>URL to new logo<div class='graytext'>You can use a relative URL or a full URL starting with http://</div></td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_simple_input('logo_url', $_POST['logo_url'] ? $_POST['logo_url'] : $current_img_url, '60' )."</td>
							 </tr>
							 <tr>
							   <td width='40%' class='tdrow1'><b><u>OR</u></b> upload a new logo<div class='graytext'>Browse your computer for a logo to upload. Filename must end in .gif, .jpg, .jpeg or .png</div></td>
							   <td width='60%' class='tdrow1'>".$ibforums->adskin->form_upload()."</td>
							 </tr>
							</table>
							</fieldset>
							<!--<fieldset class='tdfset'>
							 <legend><strong>Global Board Header HTML</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='100%' align='center' class='tdrow1'><div align='left'><b>Basic Layout</b></div><br />".$ibforums->adskin->form_textarea( 'headerhtml', $_POST['headerhtml'] ? $_POST['headerhtml'] : $current_html, 80, 10, 'soft', '', 'width:100%')."</td>
							 </tr>
							 <tr>
							   <td width='100%' align='center' class='tdrow1'><div align='left'><b>&lt;{JAVASCRIPT}&gt;</b></div><br />".$ibforums->adskin->form_textarea( 'javascripthtml', $_POST['javascripthtml'] ? $_POST['javascripthtml'] : $html['javascript'], 80, 5, 'soft', '', 'width:100%')."</td>
							 </tr>
							 <tr>
							   <td width='100%' align='center' class='tdrow1'><div align='left'><b>&lt;{LEFT_HAND_SIDE_LINKS}&gt;</b></div><br />".$ibforums->adskin->form_textarea( 'leftlinkshtml', $_POST['leftlinkshtml'] ? $_POST['leftlinkshtml'] : $html['leftlinks'], 80, 5, 'soft', '', 'width:100%')."</td>
							 </tr>
							 <tr>
							   <td width='100%' align='center' class='tdrow1'><div align='left'><b>&lt;{RIGHT_HAND_SIDE_LINKS}&gt;</b></div><br />".$ibforums->adskin->form_textarea( 'rightlinkshtml', $_POST['rightlinkshtml'] ? $_POST['rightlinkshtml'] : $html['rightlinks'], 80, 5, 'soft', '', 'width:100%')."</td>
							 </tr>
							</table>
							</fieldset>-->
							</div>
							</div>";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->end_form_standalone("Complete Edit");

		//-----------------------------------------

		$ibforums->admin->output();
	}

	//-----------------------------------------
	// REBUILD MASTER HTML
	//-----------------------------------------

	function rebuildmaster_html()
	{
		global $ibforums, $DB, $std;

		$master  = array();
		$inserts = 0;
		$updates = 0;

		//-----------------------------------------
		// Template here?
		//-----------------------------------------

		if ( ! file_exists( ROOT_PATH.'ipb_templates.xml' ) )
		{
			$ibforums->main_msg = "ipb_templates.xml cannot be found in the forums root directory. Please check, upload or try again";
			$this->show_intro();
		}

		//-----------------------------------------
		// First, get all the default bits
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'suid,group_name,func_name', 'from' => 'skin_templates', 'where' => 'set_id=1' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$master[ strtolower( $r['group_name'] ) ][ strtolower( $r['func_name'] ) ] = $r['suid'];
		}

		//-----------------------------------------
		// Get XML
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Get XML file (TEMPLATES)
		//-----------------------------------------

		$xmlfile = ROOT_PATH.'ipb_templates.xml';

		$setting_content = implode( "", file($xmlfile) );

		//-----------------------------------------
		// Unpack the datafile (TEMPLATES)
		//-----------------------------------------

		$xml->xml_parse_document( $setting_content );

		//-----------------------------------------
		// (TEMPLATES)
		//-----------------------------------------

		if ( ! is_array( $xml->xml_array['templateexport']['templategroup']['template'] ) )
		{
			$ibforums->main_msg = "Error with ipb_templates.xml - could not process XML properly";
			$this->show_intro();
		}

		foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $id => $entry )
		{
			$newrow = array();

			$newrow['group_name']      = $entry[ 'group_name' ]['VALUE'];
			$newrow['section_content'] = $entry[ 'section_content' ]['VALUE'];
			$newrow['func_name']       = $entry[ 'func_name' ]['VALUE'];
			$newrow['func_data']       = $entry[ 'func_data' ]['VALUE'];
			$newrow['set_id']          = 1;
			$newrow['updated']         = time();

			if ( $master[ strtolower( $newrow['group_name'] ) ][ strtolower( $newrow['func_name'] ) ] )
			{
				//-----------------------------------------
				// Update
				//-----------------------------------------

				$updates++;

				$DB->do_update( 'skin_templates', $newrow, 'suid='.$master[ strtolower( $newrow['group_name'] ) ][ strtolower( $newrow['func_name'] ) ] );
			}
			else
			{
				//-----------------------------------------
				// Insert
				//-----------------------------------------

				$inserts++;

				$DB->do_insert( 'skin_templates', $newrow );
			}
		}

		$ibforums->main_msg = "Master template set rebuilt!<br />$updates updated template bits, $inserts new template bits";

		$this->show_intro();
	}

	//-----------------------------------------
	// COMPLEX SEARCH
	//-----------------------------------------

	function search_and_replace()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get $skin_names stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/skin_info.php' );

		$SEARCH_set  = intval( $ibforums->input['set_skin_set_id'] );

		//-----------------------------------------
		// Get set stuff
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$SEARCH_set ) );

		//-----------------------------------------
		// Clean up before / after
		//-----------------------------------------

		$before = $std->txt_stripslashes($_POST['searchfor']);
		$after  = $std->txt_stripslashes($_POST['replacewith']);
		$before = str_replace( '"', '\"', $before );
		$after  = str_replace( '"', '\"', $after  );

		if ( ! $before )
		{
			$ibforums->main_msg = "You must enter a 'search for' string before continuing.";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Clean up regex
		//-----------------------------------------

		if ( $ibforums->input['regexmode'] )
		{
			$before = str_replace( '#', '\#', $before );

			//-----------------------------------------
			// Test to ensure they are legal
			// - catch warnings, etc
			//-----------------------------------------

			ob_start();
			eval( "preg_replace( \"#{$before}#i\", \"{$after}\", '' );");
			$return = ob_get_contents();
			ob_end_clean();

			if ( $return )
			{
				$ibforums->main_msg = "There was an error processing the 'search for' and 'replace with' variables - please ensure that they are legal regular expressions before continuing.";
				$this->searchreplace_start();
			}
		}

		//-----------------------------------------
		// we're here, so it's good
		//-----------------------------------------

		$templates = array();
		$matches   = 0;

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => 'set_id='.$SEARCH_set ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			if ( $ibforums->input['regexmode'] )
			{
				if ( preg_match( "#{$before}#i", $r['section_content'] ) )
				{
					$templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
					$matches++;
				}
			}
			else if ( strstr( $r['section_content'], $before ) )
			{
				$templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
				$matches++;
			}
		}

		//-----------------------------------------
		// No matches...
		//-----------------------------------------

		if ( ! count($templates) )
		{
			$ibforums->html .= "<div class='tableborder'>
								 <div class='maintitle'>Search & Replace Results</div>
								 <div class='tablepad'>
								  <b>You searched for: ".stripslashes(htmlspecialchars($before))."</b>
								  <br />
								  <br />
								  Unfortunately your search didn't return any matches. Please try again and broaden your search terms.
								 </div>
								</div>";

			$ibforums->admin->output();
		}

		//-----------------------------------------
		// Swapping or showing?
		//-----------------------------------------

		if ( $ibforums->input['testonly'] )
		{
			$ibforums->html .= "<div class='tableborder'>
								 <div class='maintitle'>Search & Replace Results</div>
								 <div class='tablepad' style='padding:5px'><b style='font-size:12px'>{$matches} matches for '".htmlentities($before)."' to be replaced with '".htmlentities($after)."'</b><br /><br />";

			//-----------------------------------------
			// Go fru dem all and print..
			//-----------------------------------------

			foreach( $templates as $group => $d )
			{
				foreach( $templates[ $group ] as $tmp_name => $tmp_data )
				{
					if ( isset($skin_names[ $group ]) )
					{
						$group_name = $skin_names[ $group ][0];
					}
					else
					{
						$group_name = $group;
					}

					$html = $tmp_data['section_content'];

					//-----------------------------------------
					// Decode...
					//-----------------------------------------

					$hl    = $before;
					$after = str_replace( '\\\\', '\\\\\\', $after );

					if ( ! $after )
					{
						$hl   = preg_replace( "#\((.+?)\)#s", "(?:\\1)", $hl );
						$html = preg_replace( "#({$hl})#si" , '{#-^--opentag--^-#}'."\\1".'{#-^--closetag--^-#}', $html );
					}
					else
					{
						//-----------------------------------------
						// Wrap tags (so we don't use
						// < >, etc )
						//-----------------------------------------

						$html = preg_replace( "#{$hl}#si", '{#-^--opentag--^-#}'.$after.'{#-^--closetag--^-#}', $html );
					}




					//-----------------------------------------
					// Clean up..
					//-----------------------------------------

					$html = str_replace( "{#-^--opentag--^-#}\\", '{#-^--opentag--^-#}', $html );

					//-----------------------------------------
					// convert to printable html
					//-----------------------------------------

					$html = str_replace( "<" , "&lt;"  , $html);
					$html = str_replace( ">" , "&gt;"  , $html);
					$html = str_replace( "\"", "&quot;", $html);

					$html = preg_replace( "!&lt;\!--(.+?)(//)?--&gt;!s"              , "&#60;&#33;<span style='color:red'>--\\1--\\2</span>&#62;", $html );
					$html = preg_replace( "#&lt;([^&<>]+)&gt;#s"                     , "<span style='color:blue'>&lt;\\1&gt;</span>"             , $html );   //Matches <tag>
					$html = preg_replace( "#&lt;([^&<>]+)=#s"                        , "<span style='color:blue'>&lt;\\1</span>="                , $html );   //Matches <tag
					$html = preg_replace( "#&lt;/([^&]+)&gt;#s"                      , "<span style='color:blue'>&lt;/\\1&gt;</span>"            , $html );   //Matches </tag>
					$html = preg_replace( "!=(&quot;|')([^<>])(&quot;|')(\s|&gt;)!s" , "=\\1<span style='color:purple'>\\2</span>\\3\\4"         , $html );   //Matches ='this'

					//-----------------------------------------
					// convert back wrap tags
					//-----------------------------------------

					$html = str_replace( '{#-^--opentag--^-#}' , "<span style='color:red;font-weight:bold;background-color:yellow'>", $html );
					$html = str_replace( '{#-^--closetag--^-#}', "</span>", $html );

					$ibforums->html .= "<div class='tableborder'>
										 <div class='maintitle'>{$group_name} &middot; {$tmp_data['func_name']}</div>
										 <div class='tdrow2' style='height:100px;overflow:auto'><pre>{$html}</pre></div>
										</div>
										<br />";
				}
			}

			$ibforums->html .= "</div></div>";

			$ibforums->admin->nav[] = array( "", "Search results from set ".$this_set['set_name'] );

			$ibforums->admin->output();
		}
		else
		{
			//-----------------------------------------
			// Jus' do iiit
			//-----------------------------------------

			$after  = str_replace( '\\\\', '\\\\\\', $after );
			$report = array();

			foreach( $templates as $group => $d )
			{
				foreach( $templates[ $group ] as $tmp_name => $tmp_data )
				{
					if ( $ibforums->input['regexmode'] )
					{
						$tmp_data['section_content'] = preg_replace( "#{$before}#si", $after, $tmp_data['section_content'] );

					}
					else
					{
						$tmp_data['section_content'] = str_replace( $before, $after, $tmp_data['section_content'] );
					}

					//-----------------------------------------
					// Update DB
					//-----------------------------------------

					$DB->do_update( 'skin_templates', array( 'section_content' => $tmp_data['section_content'] ), 'suid='.$tmp_data['suid'] );

					$report[] = $tmp_data['func_name'].' updated...';
				}
			}

			//-----------------------------------------
			// Recache skin template..
			//-----------------------------------------

			$ibforums->cache_func->_recache_templates( $SEARCH_set, $this_set['set_skin_set_parent'] );
			$report[] = "Templates recached for set {$this_set['set_name']}";

			$ibforums->main_msg = implode( "<br />", $report );
			$this->searchreplace_start();
		}
	}

	//-----------------------------------------
	// SIMPLE SEARCH
	//-----------------------------------------

	function simple_search()
	{
		global $ibforums, $DB, $std;

		$templates = array();
		$final     = array();
		$matches   = array();

		//-----------------------------------------
		// Get $skin_names stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/skin_info.php' );

		//-----------------------------------------
		// Get stuff
		//-----------------------------------------

		$rawword = $_GET['searchkeywords'] ? urldecode( $_GET['searchkeywords'] ) : $_POST['searchkeywords'];

		$SEARCH_word = trim( $std->txt_safeslashes( $rawword ) );
		$SEARCH_safe = urlencode( $SEARCH_word );
		$SEARCH_all  = intval( $ibforums->input['searchall'] );
		$SEARCH_set  = intval( $ibforums->input['set_skin_set_id'] );

		$this->unaltered    = "<img src='{$ibforums->skin_url}/skin_item_unaltered.gif' border='0' alt='-' title='Unaltered from parent skin set' />&nbsp;";
		$this->altered      = "<img src='{$ibforums->skin_url}/skin_item_altered.gif' border='0' alt='+' title='Altered from parent skin set' />&nbsp;";
		$this->inherited    = "<img src='{$ibforums->skin_url}/skin_item_inherited.gif' border='0' alt='|' title='Inherited from parent skin set' />&nbsp;";

		//-----------------------------------------
		// check (please?)
		//-----------------------------------------

		if ( ! $SEARCH_word )
		{
			$ibforums->main_msg = "You must enter a search word";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Get set stuff
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$SEARCH_set ) );

		if ( ! $this_set['set_skin_set_id'] )
		{
			$ibforums->main_msg = "No such set was found in the DB";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Get templates from DB
		//-----------------------------------------

		if ( $SEARCH_all )
		{
			$templates = $ibforums->cache_func->_get_templates( $this_set['set_skin_set_id'], $this_set['set_skin_set_parent'], 'all' );
		}
		else
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => 'set_id='.$SEARCH_set ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
			}
		}

		if ( ! count( $templates ) )
		{
			$ibforums->main_msg = "Couldn't locate any templates to search in!";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Go fru dem all and search
		//-----------------------------------------

		foreach( $templates as $group => $d )
		{
			foreach( $templates[ $group ] as $tmp_name => $tmp_data )
			{
				if ( strstr( strtolower( $tmp_data['section_content'] ), strtolower( $SEARCH_word ) ) )
				{
					$final[ $group ][] = $tmp_data;
					$matches[ $group ]++;
				}
			}
		}

		//-----------------------------------------
		// Print..
		//-----------------------------------------

		if ( ! count($final) )
		{
			$ibforums->html .= "<div class='tableborder'>
								 <div class='maintitle'>Search Results</div>
								 <div class='tablepad'>
								  <b>You searched for: {$SEARCH_word}</b>
								  <br />
								  <br />
								  Unfortunately your search didn't return any matches. Please try again and broaden your search terms.
								 </div>
								</div>";

			$ibforums->admin->output();
		}

		//-----------------------------------------
		// Get all possible groups...
		//-----------------------------------------

		$group_titles = $ibforums->cache_func->_get_templates( $this_set['set_skin_set_id'], $this_set['set_skin_set_parent'], 'groups' );

		foreach( $group_titles as $title => $g )
		{
			//-----------------------------------------
			// Fix up names
			//-----------------------------------------

			$g['easy_name'] = "<b>".$g['group_name']."</b>";

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

		usort($groups, array( 'ad_skintools', 'perly_alpha_sort' ) );

		//-----------------------------------------
		// Start the output
		//-----------------------------------------

		$ibforums->html .= "<div class='tableborder'>
							 <div class='maintitle'>Search results for: '".htmlentities($SEARCH_word)."'</div>";

		foreach( $groups as $group )
		{
			$eid          = $group['suid'];
			$exp_content  = "";
			$search_match = intval( $matches[ $group['group_name'] ] );

			if ( count( $final[ $group['group_name'] ] ) )
			{
				//-----------------------------------------
				// Get master template names..
				//-----------------------------------------

				$ibforums->html .= $ibforums->adskin->js_checkdelete();

				$exp_content = "<a name='{$group['group_name']}'></a>
								<div style='padding:4px;border-bottom:1px solid #999;background:#EEE'>
								<table width='100%' cellspacing='0' cellpadding='0' border='0'>
								<tr>
								 <td align='center' width='1%'><img src='{$ibforums->skin_url}/folder_with_page.gif' alt='Template Group' style='vertical-align:middle' /></td>
								 <td align='left' width='60%'>
								  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a style='font-size:11px' href='#' onclick=\"toggleview('popbox_{$group['group_name']}'); return false;\">{$group['easy_name']}</a> ({$search_match} matches)
								  </td>
								 <td align='right' width='40%'>{$group['easy_preview']}</td>
								</tr>
								</table>
								</div>
								<div style='margin-left:25px;background:#EEE;border:1px solid #555;display:none;' id='popbox_{$group['group_name']}'>
								   <div style='background-color:#CCC' class='skineditortopstrip'><b>{$group['easy_name']}</b></div>
								   <form name='mutliact_{$group['group_name']}' action='{$ibforums->adskin->base_url}&act=templ&code=edit_bit&expand={$group['group_name']}&id={$SEARCH_set}&parent={$this_set['set_skin_set_parent']}' method='post'>
								   <table cellspacing='0' width='100%' cellpadding='2'>
								   <!--CONTENT-->
								   </table>
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

				foreach( $final[ $group['group_name'] ] as $eye => $i )
				{
					$sec_arry[ $i['suid'] ] = $i;
					$sec_arry[ $i['suid'] ]['easy_name'] = $i['func_name'];
				}

				//-----------------------------------------
				// Sort by easy_name
				//-----------------------------------------

				usort($sec_arry, array( 'ad_skintools', 'perly_alpha_sort' ) );

				//-----------------------------------------
				// Loop and print main display
				//-----------------------------------------

				foreach( $sec_arry as $id => $sec )
				{
					$custom_bit    = "";

					//-----------------------------------------
					// Altered?
					//-----------------------------------------

					if ( $sec['set_id'] == $SEARCH_set )
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

					if ( $sec['set_id'] == $SEARCH_set )
					{
						$remove_button = "<a title='Revert Customization' href=\"javascript:checkdelete('act=templ&code=remove_bit&suid={$sec['suid']}&id={$SEARCH_set}&parent={$this_set['set_skin_set_parent']}&expand={$group['group_name']}')\"><img src='{$ibforums->skin_url}/te_revert.gif' alt='X' border='0' /></a>&nbsp;";
					}

					$temp .= "
								<tr>
								 <td width='2%' style='background-color:$css_info' align='center'><img src='{$ibforums->skin_url}/file.gif' title='Template Set:{$sec['set_id']}' alt='Template' style='vertical-align:middle' /></td>
								 <td width='88%' style='background-color:$css_info'><input type='checkbox' style='background-color:$css_info' name='cb_{$sec['suid']}' value='1' />&nbsp;{$altered_image}<a href='{$ibforums->adskin->base_url}&act=templ&code=edit_bit&suid={$sec['suid']}&id={$SEARCH_set}&parent={$this_set['set_skin_set_parent']}&expand={$group['group_name']}&type=single' title='template bit name: {$sec['func_name']}'>{$sec['easy_name']}</a>{$custom_bit}</td>
								 <td width='10%' style='background-color:$css_info' align='right' nowrap='nowrap'>
								   $remove_button
								   <a style='text-decoration:none' title='Preview template bit as text' href='javascript:pop_win(\"act=rtempl&code=preview&suid={$sec['suid']}&type=text&hl=".str_replace('%22', '{-22-}', $SEARCH_safe)."\",\"Preview{$sec['suid']}\", 500,400)'><img src='{$ibforums->skin_url}/te_text.gif' border='0' alt='Text Preview'></a>
								   <a style='text-decoration:none' title='Preview template bit as HTML' href='javascript:pop_win(\"act=rtempl&code=preview&suid={$sec['suid']}&type=css\",\"Preview{$sec['suid']}\", 500,400)'><img src='{$ibforums->skin_url}/te_html.gif' border='0' alt='HTML Preview'>&nbsp;</a>
								 </td>
								</tr>
							";
				}


				$ibforums->html .= str_replace( "<!--CONTENT-->", $temp, $exp_content );
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

		$ibforums->admin->nav[] = array( "", "Search results from set ".$this_set['set_name'] );
		$ibforums->admin->output();

	}

	//-----------------------------------------
	// SEARCH & REPLACE SPLASH
	//-----------------------------------------

	function searchreplace_start()
	{
		global $ibforums, $DB, $std;

		$skin_list = $this->_get_skinlist();

		$ibforums->admin->page_detail = "These tools will allow you to search for keywords and bulk replace HTML.";
		$ibforums->admin->page_title  = "Skin Search & Replace";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'skintools'     ),
															     2 => array( 'code' , 'simplesearch'  ),
													    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Simple Search" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search for...</b><br /><span style='color:gray'>Enter a simple keyword or block of HTML to search for</span>",
															       $ibforums->adskin->form_simple_input( 'searchkeywords', '', 30 )
													    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search in set...</b>",
															     $skin_list
															     ."<br /><input type='checkbox' name='searchall' value='1'> Search in selected set and all parents including the master set."
													    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Search and replace
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'skintools'     ),
															     2 => array( 'code' , 'searchandreplace'  ),
													    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search and Replace" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search for...</b><br /><span style='color:gray'>Enter a keyword or a block of HTML to search for.<br />If enabling 'regex mode' you may enter a regular expression here.</span>",
															      $ibforums->adskin->form_textarea( 'searchfor', $_POST['searchfor'] )
													    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Replace with...</b><br /><span style='color:gray'>Enter the replacement block of HTML<br />If enabling 'regex mode' you may enter a regular expression here.</span>",
															     $ibforums->adskin->form_textarea( 'replacewith', $_POST['replacewith'] )
													    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search in set...</b><br /><span style='color:gray'>NOTE: The search and replace will only work on the specified skin set. The parent and master skin sets will NOT be searched or any replacements made on them.</span>",
															     $skin_list
													    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Test Search and Replace Only?</b><br /><span style='color:gray'>If yes, no replacements will be made and you will be able to preview the changes.</span>",
															      $ibforums->adskin->form_yes_no( 'testonly', 1 )
													    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Enable 'regex' mode?</b><br /><span style='color:gray'>If yes, you may use 'regex' in your search and replacements.
																 <br />Example:- Replace all &lt;br&gt; or &lt;br /&gt; with &lt;br clear='all' /&gt;
																 <br />Search for: <b>&lt;(br)&#92;s?/?&gt;</b>
																 <br />Replace with: <b>&lt;&#92;&#92;1 clear='all' /&gt;</b></span>",
															      $ibforums->adskin->form_yes_no( 'regexmode', 0 )
													    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}

	//-----------------------------------------
	// Swap members...
	//-----------------------------------------

	function change_member()
	{
		global $ibforums, $DB, $std;

		$old_id = intval($ibforums->input['set_skin_set_id']);
		$new_id = intval($ibforums->input['set_skin_set_id2']);

		if ($new_id == 'n')
		{
			$DB->do_update( 'members', array( 'skin' => '' ), 'skin='.$old_id );
		}
		else
		{
			$DB->do_update( 'members', array( 'skin' => $new_id ), 'skin='.$old_id );
		}

		$ibforums->main_msg = "Members updated";

		$this->show_intro();
	}

	//-----------------------------------------
	// REBUILD MASTER
	//-----------------------------------------

	function rebuildmaster()
	{
		global $ibforums, $DB, $std;

		$pid = intval($ibforums->input['phplocation']);
		$cid = intval($ibforums->input['csslocation']);

		if ( $ibforums->input['phpyes'] )
		{
			if ( ! file_exists( CACHE_PATH.'skin_cache/cacheid_'.$pid ) )
			{
				$ibforums->main_msg = 'IPB cannot rebuild the master templates as the folder "cacheid_$pid" does not exist';
			}

			$ibforums->cache_func->_rebuild_templates_from_php($pid);

			$ibforums->main_msg = 'Attempting to rebuild master set from PHP cache files...';

			$ibforums->main_msg .= "<br />".implode("<br />", $ibforums->cache_func->messages);
		}

		if ( $ibforums->input['cssyes'] )
		{
			if ( ! file_exists( CACHE_PATH.'style_images/css_'.$cid.'.css' ) )
			{
				$ibforums->main_msg = 'IPB cannot rebuild the master CSS as the CSS "css_$cid" does not exist';
			}

			$css = @file_get_contents( CACHE_PATH.'style_images/css_'.$cid.'.css' );

			if ( ! $css )
			{
				$ibforums->main_msg = 'IPB cannot rebuild the master CSS as the CSS "css_$cid" appears to be empty.';
			}

			$css = preg_replace( "#^.*\*~START CSS~\*/#s", "", $css );

			//-----------------------------------------
			// Attempt to rearrange style_images dir stuff
			//-----------------------------------------

			$ibforums->main_msg = 'Attempting to rebuild master CSS from CSS cache files...';

			$css = preg_replace( "#url\((.+?)/(.+?)\)#is", "url(style_images/1/\\2)", $css );

			$DB->do_update( 'skin_sets', array( 'set_css' => $css, 'set_css_updated' => time() ), 'set_skin_set_id=1' );

			$ibforums->cache_func->_write_css_to_cache(1);

			$ibforums->main_msg .= "<br />".implode("<br />", $ibforums->cache_func->messages);
		}

		$this->show_intro();
	}

	//-----------------------------------------
	// REBUILD CACHES
	//-----------------------------------------

	function rebuildcaches()
	{
		global $ibforums, $DB, $std;

		$ibforums->cache_func->_rebuild_all_caches(array($ibforums->input['set_skin_set_id']));

		$ibforums->main_msg = 'Skin Set Caches Rebuilt (id: '.$ibforums->input['set_skin_set_id'].')';

		$ibforums->main_msg .= "<br />".implode("<br />", $ibforums->cache_func->messages);

		$this->show_intro();
	}

	//-----------------------------------------
	// SHOW MAIN SCREEN
	//-----------------------------------------

	function show_intro()
	{
		global $ibforums, $DB, $std;

		$skin_list = $this->_get_skinlist();

		//-----------------------------------------
		// REBUILD MASTER TEMPLATES
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
															     2 => array( 'code' , 'rebuildmasterhtml'  ),
													    )      );

		$ibforums->adskin->td_header[] = array( "{none}"  , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rebuild Master Templates" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Running this tool will rebuild your master HTML templates that all your skins inherit from.</b>
																  <br />After running, you may wish to rebuild your skin set caches to update them with the changes.
																  <br />Make sure you've got 'ipb_templates.xml' uploaded into your forum root.",
													    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Run tool...");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// REBUILD CACHES
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
															     2 => array( 'code' , 'rebuildcaches'  ),
													    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rebuild Skin Set Cache" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rebuild skin set cache on set...</b><br /><span style='color:gray'>This option will rebuild the template HTML, wrapper, macro and css caches of this set and any children.</span><br />[ <a href='{$ibforums->base_url}&act=sets&code=rebuildalltemplates'>Rebuild All</a> ]",
															     $skin_list
													    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Run tool...");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// CHANGE MEMBERS
		//-----------------------------------------

		$dd_two = str_replace( "select name='set_skin_set_id'", "select name='set_skin_set_id2'", $skin_list );
		$dd_two = str_replace( "<!--DD.OPTIONS-->", "<option value='n'>None - use the admin defaults</option>", $dd_two );

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
															     2 => array( 'code' , 'changemember'  ),
													    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Update Members Skin Choice" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Where the member currently uses...</b>",
															     $skin_list
													    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Make them use...</b>",
															     $dd_two
													    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Run tool...");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// REBUILD MASTER
		//-----------------------------------------

		if ( IN_DEV )
		{
			$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
														             2 => array( 'code' , 'rebuildmaster'  ),
														    )      );

			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Rebuild Master Skin Set" );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rebuild 'IPB Master Skin Set' FROM CSS AND PHP files.</b><br /><span style='color:gray'>This option will rebuild the template HTML for the master skin set. USE VERY CAREFULLY!</span>",
																   "<input type='checkbox' name='phpyes' value='1' /> PHP cache dir.: skin_cache/cacheid_ ".$ibforums->adskin->form_simple_input( 'phplocation', '1', 3 )."<br />".
																   "<input type='checkbox' name='cssyes' value='1' /> CSS cache file: style_images/css_ ".$ibforums->adskin->form_simple_input( 'csslocation', '1',3 )
														  )      );

			$ibforums->html .= $ibforums->adskin->end_form("Run tool...");

			$ibforums->html .= $ibforums->adskin->end_table();
		}

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// Get dropdown of skin
	//-----------------------------------------

	function _get_skinlist()
	{
		global $ibforums, $DB, $std;

		$skin_sets = array();
		$skin_list = "<select name='set_skin_set_id' class='dropdown'><!--DD.OPTIONS-->";

		//-----------------------------------------
		// Get formatted list of skin sets
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'order' => 'set_skin_set_parent, set_skin_set_id' ) );
		$DB->simple_exec();

		while ( $s = $DB->fetch_row() )
		{
			$skin_sets[ $s['set_skin_set_id'] ] = $s;
			$skin_sets[ $s['set_skin_set_parent'] ]['_children'][] = $s['set_skin_set_id'];
		}

		//-----------------------------------------
		// Roots
		//-----------------------------------------

		foreach( $skin_sets as $id => $data )
		{
			if ( $data['set_skin_set_parent'] < 1 and $id > 1 )
			{
				$skin_list .= "\n<option value='$id'>{$data['set_name']}</option><!--CHILDREN:{$id}-->";
			}
		}

		//-----------------------------------------
		// Kids...
		//-----------------------------------------

		foreach( $skin_sets as $id => $data )
		{
			if ( is_array( $data['_children'] ) and count( $data['_children'] ) > 0 )
			{
				$html = "";

				foreach( $data['_children'] as $cid )
				{
					$html .= "\n<option value='$cid'>---- {$skin_sets[ $cid ]['set_name']}</option>";
				}

				$skin_list = str_replace( "<!--CHILDREN:{$id}-->", $html, $skin_list );
			}
		}

		$skin_list .= "</select>";

		return $skin_list;
	}

	//-----------------------------------------
	// Sort by group name
	//-----------------------------------------

	function perly_alpha_sort($a, $b)
	{
		return strcmp($a['easy_name'], $b['easy_name']);
	}

}


?>