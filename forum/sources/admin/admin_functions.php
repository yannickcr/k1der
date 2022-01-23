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
|   > Admin functions library
|   > Script written by Matt Mecham
|   > Date started: 1st march 2002
|
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


class admin_functions {

	var $img_url;
	var $page_title  = "Welcome to the Invision Power Board Administration CP";
	var $page_detail = "You can set up and customize your board from within this control panel.<br><br>Clicking on one of the links in the left menu pane will show you the relevant options for that administration category. Each option will contain further information on configuration, etc.";
	var $html;
	var $errors = "";
	var $nav    = array();
	var $time_offset = 0;
	var $jump_menu = "";
	var $no_jump = 0;
	var $master_skin = array();
	var $depth_guide = '--';
	var $menu_ids    = array();
	function admin_functions()
	{
		global $ibforums;

		$this->base_url = $ibforums->base_url;
		$this->img_url  = $ibforums->skin_url;
	}

	//-----------------------------------------
	// Get mysql version
	//-----------------------------------------

	function get_mysql_version()
	{
		global $ibforums, $DB, $std;

		$DB->sql_get_version();

		$ibforums->true_version  = $DB->true_version;
		$ibforums->mysql_version = $DB->mysql_version;
	}

	//-----------------------------------------
	// Get mysql version
	//-----------------------------------------

	function get_fulltextindex_status()
	{
		global $ibforums, $DB, $std;

		if ( $DB->sql_is_currently_fulltext( 'posts' ) == TRUE )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/*-------------------------------------------------------------------------*/
	// make template / text data safe for forms
	/*-------------------------------------------------------------------------*/

	function text_to_form($t="")
	{
		// Use forward look up to only convert & not &#123;
		//$t = preg_replace("/&(?!#[0-9]+;)/s", '&#38;', $t );

		$t = str_replace("&", "&#38;"    , $t );
		$t = str_replace( "<" , "&#60;"  , $t );
		$t = str_replace( ">" , "&#62;"  , $t );
		$t = str_replace( '"' , "&#34;"  , $t );
		$t = str_replace( "'" , '&#039;' , $t );
		$t = str_replace( "\\", "&#092;" , $t );

		return $t; // A nice cup of?
	}

	/*-------------------------------------------------------------------------*/
	// Converts form data back into raw text
	/*-------------------------------------------------------------------------*/

	function form_to_text($t="")
	{
		$t = str_replace( '\\'  , '\\\\', $t );
		$t = str_replace( "&#38;"  , "&", $t );
		$t = str_replace( "&#60;"  , "<", $t );
		$t = str_replace( "&#62;"  , ">", $t );
		$t = str_replace( "&#34;"  , '"', $t );
		$t = str_replace( "&#039;" , "'", $t );
		$t = str_replace( '&#092;' ,'\\', $t );

		return $t;
	}

	//-----------------------------------------
	// Generate skin list
	//-----------------------------------------

	function skin_get_skin_dropdown()
	{
		global $ibforums;

		$skin_array  = array();

		foreach( $ibforums->cache['skin_id_cache'] as $id => $data )
		{
			if ( $data['set_parent'] < 1 and $id > 1 )
			{
				 $data['set_parent'] = 'root';
			}

			$this->master_skin[ $data['set_parent'] ][ $id ] = $data;
		}

		foreach( $this->master_skin['root'] as $id => $data )
		{
			$skin_array[] = array( $id, $data['set_name'] );

			if ( is_array( $this->master_skin[ $id ] ) )
			{
				foreach( $this->master_skin[ $id ] as $id => $data )
				{
					$skin_array[] = array( $id, $this->depth_guide.$data['set_name'] );

					$skin_array = $this->skin_get_skin_dropdown_recurse( $id, $skin_array, $this->depth_guide.$this->depth_guide );
				}
			}
		}

		return $skin_array;
	}

	function skin_get_skin_dropdown_recurse( $root_id, $skin_array=array(), $depth_guide='' )
	{
		global $ibforums;

		if ( is_array( $this->master_skin[ $root_id ] ) )
		{
			foreach( $this->master_skin[ $root_id ] as $id => $data )
			{
				$skin_array[] = array( $id, $this->depth_guide.$data['set_name'] );

				$skin_array = $this->skin_get_skin_dropdown_recurse( $id, $skin_array, $this->depth_guide.$this->depth_guide );
			}
		}

		return $skin_array;
	}


	//-----------------------------------------
	// IMPORT FUNCTION
	//-----------------------------------------

	function import_xml( $infilename )
	{
		global $ibforums, $DB,  $std;

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
		// Naughty Mozilla likes to use "none" to indicate an empty upload field.
		// I love universal languages that aren't universal.
		//-----------------------------------------

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			continue;
		}

		$content = "";

		if ( strstr( $FILE_NAME, $infilename ) )
		{
			if ( move_uploaded_file( $_FILES[ 'FILE_UPLOAD' ]['tmp_name'], ROOT_PATH."uploads/".$FILE_NAME) )
			{
				if ( $FILE_NAME == $infilename.'.gz' )
				{
					if ( $FH = @gzopen( ROOT_PATH."uploads/".$FILE_NAME, 'rb' ) )
					{
					 	while ( ! @gzeof( $FH ) )
					 	{
					 		$content .= @gzread( $FH, 1024 );
					 	}

						@gzclose( $FH );
					}
				}
				else if ( $FILE_NAME == $infilename )
				{
					if ( $FH = @fopen( ROOT_PATH."uploads/".$FILE_NAME, 'rb' ) )
					{
						$content = @fread( $FH, filesize(ROOT_PATH."uploads/".$FILE_NAME) );
						@fclose( $FH );
					}
				}

				@unlink( ROOT_PATH."uploads/".$FILE_NAME );
			}
		}

		return $content;
	}

	//-----------------------------------------
	// Shows dialogue download box
	//-----------------------------------------

	function show_download( $data, $name, $type="unknown/unknown", $compress=1 )
	{
		//@flush();

		if ( $compress and @function_exists('gzencode') )
		{
			$name .= '.gz';
			//$type = 'application/x-gzip';
		}
		else
		{
			$compress = 0;
		}

		header('Content-Type: '.$type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: attachment; filename="' . $name . '"');

		if ( ! $compress )
		{
			@header('Content-Length: ' . strlen($data) );
		}

		@header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		@header('Pragma: public');

		if ( $compress )
		{
			print gzencode($data);
		}
		else
		{
			print $data;
		}

		exit();
	}

	//-----------------------------------------
	// Makes good raw form text
	//-----------------------------------------

	function make_safe($t)
	{
		$t = stripslashes($t);

		$t = preg_replace( "/\\\/", "&#092;", $t );

		return $t;
	}

	//-----------------------------------------
	// Sets up time offset for ACP use
	//-----------------------------------------


	function get_date($date="", $method="")
	{
		global $ibforums, $DB, $std;

		$this->time_options = array( 'JOINED' => $ibforums->vars['clock_joined'],
									 'SHORT'  => $ibforums->vars['clock_short'],
									 'LONG'   => $ibforums->vars['clock_long']
								   );

		if (!$date)
        {
            return '--';
        }

        if (empty($method))
        {
        	$method = 'LONG';
        }

        $this->time_offset = (($ibforums->member['time_offset'] != "") ? $ibforums->member['time_offset'] : $ibforums->vars['time_offset']) * 3600;

		if ($ibforums->vars['time_adjust'] != "" and $ibforums->vars['time_adjust'] != 0)
		{
			$this->time_offset += ($ibforums->vars['time_adjust'] * 60);
		}

		if ($ibforums->member['dst_in_use'])
		{
			$this->time_offset += 3600;
		}

        return gmdate($this->time_options[$method], ($date + $this->time_offset) );



	}

	//**********************************************/
	// save_log
	//
	// Add an entry into the admin logs, yeah.
	//**********************************************/

	function save_log($action="")
	{
		global $ibforums, $DB;

		$DB->do_insert( 'admin_logs', array(
											'act'        => $ibforums->input['act'],
											'code'       => $ibforums->input['code'],
											'member_id'  => $ibforums->member['id'],
											'ctime'      => time(),
											'note'       => $action,
											'ip_address' => $ibforums->input['IP_ADDRESS'],
								  )       );

		return true;  // to anyone that cares..

	}


	//**********************************************/
	// get_tar_names
	//
	// Simply returns a list of tarballs that start
	// with the given filename
	//**********************************************/

	function get_tar_names($start='lang-')
	{
		global $ibforums;

		// Remove trailing slashes..

		$files = array();

		$dir = $ibforums->vars['base_dir']."archive_in";

		if ( is_dir($dir) )
		{
			$handle = opendir($dir);

			while (($filename = readdir($handle)) !== false)
			{
				if (($filename != ".") && ($filename != ".."))
				{
					if (preg_match("/^$start.+?\.tar$/", $filename))
					{
						$files[] = $filename;
					}
				}
			}

			closedir($handle);

		}

		return $files;

	}

	//**********************************************/
	// copy_dir
	//
	// Copies to contents of a dir to a new dir, creating
	// destination dir if needed.
	//
	//**********************************************/

	function copy_dir($from_path, $to_path, $mode = 0777)
	{
		global $ibforums;

		// Strip off trailing slashes...

		$from_path = preg_replace( "#/$#", "", $from_path);
		$to_path   = preg_replace( "#/$#", "", $to_path);

		if ( ! is_dir($from_path) )
		{
			$this->errors = "Could not locate directory '$from_path'";
			return FALSE;
		}

		if ( ! is_dir($to_path) )
		{
			if ( ! @mkdir($to_path, $mode) )
			{
				$this->errors = "Could not create directory '$to_path' please check the CHMOD permissions and re-try";
				return FALSE;
			}
			else
			{
				@chmod($to_path, $mode);
			}
		}

		//$this_path = getcwd();

		if (is_dir($from_path))
		{
			//chdir($from_path);

			$handle = opendir($from_path);

			while (($file = readdir($handle)) !== false)
			{
				if (($file != ".") && ($file != ".."))
				{
					if ( is_dir( $from_path."/".$file ) )
					{
						$this->copy_dir($from_path."/".$file, $to_path."/".$file);
						//chdir($from_path);
					}

					if ( is_file( $from_path."/".$file ) )
					{
						copy($from_path."/".$file, $to_path."/".$file);
						@chmod($to_path."/".$file, 0777);
					}
				}
			}
			closedir($handle);
		}

		if ($this->errors == "")
		{
			return TRUE;
		}
	}

	//**********************************************/
	// rm_dir
	//
	// Removes directories, if non empty, removes
	// content and directories
	// (Code based on annotations from the php.net
	// manual by pal@degerstrom.com)
	//**********************************************/

	function rm_dir($file)
	{
		global $ibforums;

		$errors = 0;

		// Remove trailing slashes..

		$file = preg_replace( "#/$#", "", $file );

		if ( file_exists($file) )
		{
			// Attempt CHMOD

			@chmod($file, 0777);

			if ( is_dir($file) )
			{
				$handle = opendir($file);

				while (($filename = readdir($handle)) !== false)
				{
					if (($filename != ".") && ($filename != ".."))
					{
						$this->rm_dir($file."/".$filename);
					}
				}

				closedir($handle);

				if ( ! @rmdir($file) )
				{
					$errors++;
				}
			}
			else
			{
				if ( ! @unlink($file) )
				{
					$errors++;
				}
			}
		}

		if ($errors == 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	//**********************************************/
	// rebuild_config:
	//
	// Er, rebuilds the config file
	//
	//**********************************************/

	function rebuild_config( $new = "" )
	{
		global $ibforums, $std, $ADMIN, $root_path;

		//-----------------------------------------
		// Check to make sure this is a valid array
		//-----------------------------------------

		if (! is_array($new) )
		{
			$ibforums->admin->error("Error whilst attempting to rebuild the board config file, attempt aborted");
		}

		//-----------------------------------------
		// Do we have anything to save out?
		//-----------------------------------------

		if ( count($new) < 1 )
		{
			return "";
		}

		//-----------------------------------------
		// Get an up to date copy of the config file
		// (Imports $INFO)
		//-----------------------------------------

		require ROOT_PATH.'conf_global.php';

		//-----------------------------------------
		// Rebuild the $INFO hash
		//-----------------------------------------

		foreach( $new as $k => $v )
		{
			// Update the old...

			$v = preg_replace( "/'/", "\\'" , $v );
			$v = preg_replace( "/\r/", ""   , $v );

			$ibforums->vars[ $k ] = $v;
		}

		//-----------------------------------------
		// Rename the old config file
		//-----------------------------------------

		@rename( ROOT_PATH.'conf_global.php', ROOT_PATH.'conf_global-bak.php' );
		@chmod( ROOT_PATH.'conf_global-bak.php', 0777);

		//-----------------------------------------
		// Rebuild the old file
		//-----------------------------------------

		ksort($ibforums->vars);

		$file_string = "<?php\n";

		foreach( $ibforums->vars as $k => $v )
		{
			if ($k == 'skin' or $k == 'languages')
			{
				// Protect serailized arrays..
				$v = stripslashes($v);
				$v = addslashes($v);
			}

			$file_string .= '$INFO['."'".$k."'".']'."\t\t\t=\t'".$v."';\n";
		}

		$file_string .= "\n".'?'.'>';   // Question mark + greater than together break syntax hi-lighting in BBEdit 6 :p

		if ( $fh = fopen( ROOT_PATH.'conf_global.php', 'w' ) )
		{
			fwrite($fh, $file_string, strlen($file_string) );
			fclose($fh);
		}
		else
		{
			$ibforums->admin->error("Fatal Error: Could not open conf_global for writing - no changes applied. Try changing the CHMOD to 0777");
		}

		// Pass back the new $INFO array to anyone who cares...

		return $ibforums->vars;

	}

	//**********************************************/
	// compile_forum_perms:
	//
	// Returns the READ/REPLY/START DB strings
	//
	//**********************************************/


	function compile_forum_perms()
	{
		global $DB, $ibforums;

		$r_array = array( 'READ' => '', 'REPLY' => '', 'START' => '', 'UPLOAD' => '' );

		if ($ibforums->input['READ_ALL'] == 1)
		{
			$r_array['READ'] = '*';
		}

		if ($ibforums->input['REPLY_ALL'] == 1)
		{
			$r_array['REPLY'] = '*';
		}

		if ($ibforums->input['START_ALL'] == 1)
		{
			$r_array['START'] = '*';
		}

		if ($ibforums->input['UPLOAD_ALL'] == 1)
		{
			$r_array['UPLOAD'] = '*';
		}

		if ($ibforums->input['SHOW_ALL'] == 1)
		{
			$r_array['SHOW'] = '*';
		}

		$DB->simple_construct( array( 'select' => 'perm_id, perm_name', 'from' => 'forum_perms', 'order' => "perm_id" ) );
		$DB->simple_exec();

		while ( $data = $DB->fetch_row() )
		{
			if ($r_array['SHOW'] != '*')
			{
				if ($ibforums->input[ 'SHOW_'.$data['perm_id'] ] == 1)
				{
					$r_array['SHOW'] .= $data['perm_id'].",";
				}
			}
			//-----------------------------------------
			if ($r_array['READ'] != '*')
			{
				if ($ibforums->input[ 'READ_'.$data['perm_id'] ] == 1)
				{
					$r_array['READ'] .= $data['perm_id'].",";
				}
			}
			//-----------------------------------------
			if ($r_array['REPLY'] != '*')
			{
				if ($ibforums->input[ 'REPLY_'.$data['perm_id'] ] == 1)
				{
					$r_array['REPLY'] .= $data['perm_id'].",";
				}
			}
			//-----------------------------------------
			if ($r_array['START'] != '*')
			{
				if ($ibforums->input[ 'START_'.$data['perm_id'] ] == 1)
				{
					$r_array['START'] .= $data['perm_id'].",";
				}
			}
			//-----------------------------------------
			if ($r_array['UPLOAD'] != '*')
			{
				if ($ibforums->input[ 'UPLOAD_'.$data['perm_id'] ] == 1)
				{
					$r_array['UPLOAD'] .= $data['perm_id'].",";
				}
			}
		}

		$r_array['START']   = preg_replace( "/,$/", "", $r_array['START']   );
		$r_array['REPLY']   = preg_replace( "/,$/", "", $r_array['REPLY']   );
		$r_array['READ']    = preg_replace( "/,$/", "", $r_array['READ']    );
		$r_array['UPLOAD']  = preg_replace( "/,$/", "", $r_array['UPLOAD']  );
		$r_array['SHOW']    = preg_replace( "/,$/", "", $r_array['SHOW']    );

		return $r_array;

	}


	//-----------------------------------------
	//------------------------------------------------
	// OUTPUT FUNCTIONS
	//-----------------------------------------
	//------------------------------------------------

	function print_popup()
	{
		global $ibforums, $DB, $std, $use_gzip;

		$html = "<html>
		          <head><title>IPB</title>
		          <meta HTTP-EQUIV=\"Pragma\"  CONTENT=\"no-cache\">
				  <meta HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache\">
				  <meta HTTP-EQUIV=\"Expires\" CONTENT=\"Mon, 06 May 1996 04:57:00 GMT\">
		          <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$ibforums->skin_url}/acp_css.css\" />
		          <script type=\"text/javascript\">
				  <!--
				   var ipb_var_st       = \"{$ibforums->input['st']}\";
				   var ipb_lang_tpl_q1  = \"{$ibforums->lang['tpl_q1']}\";
				   var ipb_var_phpext   = \"{$ibforums->vars['php_ext']}\";
				   var ipb_var_base_url = \"{$ibforums->base_url}\";
				   var ipb_var_cookieid = \"{$ibforums->vars['cookie_id']}\";
				   //-->
				  </script>
				  <script type=\"text/javascript\" src='jscripts/ipb_global.js'></script>
				  <script type=\"text/javascript\" src='{$ibforums->skin_url}/acp_js.js'></script>
				  </head>
				  <body>
				  ";

		$html .= $ibforums->html;

		$html .= "</body></html>";

		print $html;

		exit();
	}


	//-----------------------------------------
	// OUTPUT
	//-----------------------------------------

	function output()
	{
		global $ibforums, $DB, $std, $use_gzip;

		$html  = $ibforums->adskin->print_top($this->page_title, $this->page_detail);
		$html .= $ibforums->html;
		$html .= $ibforums->adskin->print_foot();

		$DB->close_db();

		$navigation = array( "<a href='{$this->base_url}&act=index' target='body'>ACP Home</a>" );

		if ( count($this->nav) > 0 )
		{
			foreach ( $this->nav as $idx => $links )
			{
				if ($links[0] != "")
				{
					$navigation[] = "<a href='{$this->base_url}&{$links[0]}' target='body'>{$links[1]}</a>";
				}
				else
				{
					$navigation[] = $links[1];
				}
			}
		}

		//-----------------------------------------
		// Navigation?
		//-----------------------------------------

		if ( count($navigation) > 0 )
		{
			$html = str_replace( "<!--NAV-->", $ibforums->adskin->wrap_nav( implode( " / ", $navigation ) ), $html );
		}

		//-----------------------------------------
		// Quick Jump?
		//-----------------------------------------

		if ( $this->no_jump != 1 )
		{
			$html = str_replace( "<!--JUMP-->", $this->build_jump_menu(), $html );
		}

		//-----------------------------------------
		// Message in a bottle?
		//-----------------------------------------

		if ( $ibforums->main_msg )
		{
			$message = "<br />
			            <div class='tableborder'>
			             <div class='pformstrip'>IPB Message</div>
			             <div class='tablepad' style='font-size:11px'>{$ibforums->main_msg}</div>
			            </div>";

			$html = str_replace( "<!--IPB.MESSAGE-->", $message, $html );
		}

		//-----------------------------------------
		// Error message?
		//-----------------------------------------

		if ( $ibforums->main_error )
		{
			$error = "<br />
					 <div class='tableborder'>
					  <div class='pformstrip'>IPB ERROR</div>
					  <div class='tablepad' style='font-size:11px'>{$ibforums->main_msg}</div>
					 </div>";
			$html = str_replace( "<!--IPB.ERROR-->", $error, $html );
		}

		if ($use_gzip == 1)
		{
        	$buffer = ob_get_contents();
        	ob_end_clean();
        	@ob_start('ob_gzhandler');
        	print $buffer;
    	}

    	//@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		//@header("Cache-Control: no-cache, must-revalidate");
		//@header("Pragma: no-cache");

		if ( IN_DEV and count( $DB->obj['cached_queries']) )
		{
			$queries = "";

			foreach( $DB->obj['cached_queries'] as $q )
			{
				if ( strlen($q) > 300 )
				{
					$q = substr( $q, 0, 300 ).'...';
				}

				$queries .= htmlspecialchars($q).'<hr />';
			}

			$html .= "<br /><div class='tableborder'><div class='pformstrip'>Queries</div>
							<div class='tdrow1' style='padding:6px'>$queries</div></div>";
		}


    	print $html;

    	exit();

	}

	//**********************************************/
	// Redirect:
	//
	// Shows a redirect screen
	//
	//**********************************************/

	function redirect($url, $text, $is_popup=0, $time=2)
	{
		global $ibforums, $DB, $std;

		$extra = "";

		if ( $ibforums->main_msg )
		{
			$extra = '&messageinabottleacp='.urlencode( $ibforums->main_msg );
		}

		$ibforums->main_msg = "";

		$this->page_title  = "Admin CP Redirection";
		$this->page_detail = "<em>Redirecting...</em>";

		$ibforums->html .= "<meta http-equiv='refresh' content=\"{$time}; url={$ibforums->base_url}&{$url}{$extra}\">
						    <div class='tableborder'>
							<div class='maintitle'>Redirecting</div>
							<div class='tdrow1' style='padding:8px'>
							 <div style='font-size:12px'>$text
							 <br />
							 <br />
							 <center><a href='{$ibforums->base_url}&{$url}'>Click here if not redirected...</a></center>
							 </div>
							</div>
						   </div>";

		if ($is_popup == 0)
		{
			$this->output();
		}
		else
		{
			$this->print_popup();
		}
	}

	//**********************************************/
	// Error:
	//
	// Displays an error
	//
	//**********************************************/

	function error($error="", $is_popup=0)
	{
		global $ibforums, $DB, $std;

		$this->page_title  = "Admin CP Message";
		$this->page_detail = "&nbsp;";

		$ibforums->html .= "<div class='tableborder'>
							<div class='maintitle'>Admin CP Message</div>
							<div class='tdrow1' style='padding:8px'>
							 <span style='font-size:12px'>$error</span>
							</div>
						   </div>";

		if ($is_popup == 0)
		{
			$this->output();
		}
		else
		{
			$this->print_popup();
		}
	}

	//**********************************************/
	// Done Screen:
	//
	// Displays the "done" screen. Really? Yes.
	//
	//**********************************************/

	function done_screen($title, $link_text="", $link_url="", $redirect="")
	{
		global $ibforums, $DB, $std;

		if ( $redirect )
		{
			$this->redirect( $link_url, "<b>$title</b><br />Redirecting to: ".$link_text );
		}

		$this->page_title  = $title;
		$this->page_detail = "The action was executed successfully";

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table("Result");

		$ibforums->html .= $ibforums->adskin->add_td_basic( "<a href='{$this->base_url}&{$link_url}' target='body'>Go to: $link_text</a>", "center" );

		$ibforums->html .= $ibforums->adskin->add_td_basic( "<a href='{$this->base_url}&act=index' target='body'>Go to: Administration Home</a>", "center" );

		$ibforums->html .= $ibforums->adskin->end_table();

		$this->output();

	}

	function info_screen($text="", $title='Safe Mode Restriction Warning')
	{
		global $ibforums, $DB, $std;

		$this->page_title  = $title;
		$this->page_detail = "Please note the following:";

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table("Result");

		$ibforums->html .= $ibforums->adskin->add_td_basic( $text );

		$ibforums->html .= $ibforums->adskin->add_td_basic( "<a href='{$this->base_url}&act=index' target='body'>Go to: Administration Home</a>", "center" );

		$ibforums->html .= $ibforums->adskin->end_table();

		$this->output();

	}


	//**********************************************/
	// MENU:
	//
	// Build the collapsable menu trees
	//
	//**********************************************/

	function menu()
	{
		global $ibforums, $std, $PAGES, $CATS;

		$links = $this->build_tree();

		$html = $ibforums->adskin->menu_top() . $links . $ibforums->adskin->menu_foot();

		$html = str_replace( '<!--{IDS}-->', implode( ',', $this->menu_ids ), $html );

		// Saving cookie?

		if ( $ibforums->vars['menu'] )
		{
			$std->my_setcookie( 'acpmenu', $ibforums->input['show'] );
		}

		print $html;
		exit();


	}

	//-----------------------------------------

	function build_tree()
	{
		global $ibforums, $std, $PAGES, $CATS, $DESC;

		$html  = "";
		$links = "";

		$collapsed_ids = ",".$std->my_getcookie('acpcollapseprefs').",";

		foreach($CATS as $cid => $data)
		{
			$name  = $data[0];
			$color = $data[1];
			$extra = $data[2];

			$this->menu_ids[] = $cid;

			$show['div_fc'] = 'show';
			$show['div_fo'] = 'none';

			$ibforums->admin->jump_menu .= "<optgroup label='$name'>\n";

			if ( strstr( $collapsed_ids, ','.$cid.',' ) )
			{
				$show['div_fc'] = 'none';
				$show['div_fo'] = 'show';
			}

			foreach($PAGES[ $cid ] as $pid => $pdata)
			{
				if ( $pdata[2] != "" )
				{
					if ( ! @is_dir( ROOT_PATH.$pdata[2] ) )
					{
						continue;
					}
				}

				$links .= $ibforums->adskin->menu_cat_link($pid, $cid, $pdata[1], $pdata[0], $pdata[3], $pdata[4]);
			}

			$html .= $ibforums->adskin->menu_cat_wrap( $show, $name, $links, $cid, $DESC[$cid], $color, $extra );

			unset($links);

			$ibforums->admin->jump_menu .= "</optgroup>\n";
		}

		return $html;
	}

	//-----------------------------------------
	// BUILDS JUMP MENU, yay!
	//-----------------------------------------

	function build_jump_menu()
	{
		global $ibforums, $std, $PAGES, $CATS, $DESC;

		$html = "<script type='text/javascript'>
				 function dojump()
				 {
				 	if ( document.jumpmenu.val.options[document.jumpmenu.val.selectedIndex].value != '' )
				 	{
				 		window.location.href = '{$this->base_url}' + '&' + document.jumpmenu.val.options[document.jumpmenu.val.selectedIndex].value;
				 	}
				 }
				 </script>
				 ";

		$html .= "<form name='jumpmenu'>\n<select class='jmenu' name='val'>";

		foreach($CATS as $cid => $name)
		{
			$html .= "<optgroup label='$name[0]'>\n";

			foreach($PAGES[ $cid ] as $pid => $pdata)
			{
				$html .= "<option value='$pdata[1]'>$pdata[0]</option>\n";
			}

			$html .= "</optgroup>\n";
		}

		$html .= "</select>&nbsp;<input type='button' class='jmenubutton' value='Go!' onclick='dojump();' />\n</form>";

		return $html;

	}

	//-----------------------------------------
	// BUILD SKIN JUMP MENU
	//-----------------------------------------

	function skin_jump_menu($set_id="")
	{
		global $ibforums, $DB;

		if ( $set_id == "" )
		{
			$set_id = $ibforums->input['id'];
		}

		$set_id = intval($set_id);

		$r = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$set_id ) );

		$html = "<form name='gobaakaachoo'>
		         <select name='chooseacardanycard' class='realbutton' onchange=\"autojumpmenu(this)\">
		         <option value=''>Set: {$r['set_name']} options</option>
		         <option value=''>-------------------</option>
		         <option value='{$ibforums->adskin->base_url}&act=wrap&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit Board Header & Footer Wrapper</option>
				 <option value='{$ibforums->adskin->base_url}&act=templ&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit Template HTML</option>
				 <option value='{$ibforums->adskin->base_url}&act=style&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit CSS (Advanced Mode)</option>
				 <option value='{$ibforums->adskin->base_url}&act=style&code=colouredit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit CSS (Easy Mode)</option>
				 <option value='{$ibforums->adskin->base_url}&act=image&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Edit Replacement Macros</option>
				 </select>
				 </form>";

		return $html;
	}

}





?>