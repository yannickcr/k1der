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
|   > Skin -> Templates pop up functions
|   > Module written by Matt Mecham
|   > Date started: 9th July 2002
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class ad_remote_template {

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
			case 'preview':
				$this->do_preview();
				break;

			case 'edit_bit':
				$this->edit_bit();
				break;

			case 'macro_one':
				$this->macro_one();
				break;

			case 'macro_two':
				$this->macro_two();
				break;

			case 'compare':
				$this->compare_frames();
				break;

			case 'dotop':
				$this->print_compare_top();
				break;

			case 'donew':
				$this->print_compare_new();
				break;

			//-----------------------------------------

			case 'search':
				$this->search_frames();
				break;

			case 'searchbox':
				$this->print_search_box();
				break;

			case 'searchlinks':
				$this->print_searchlinks();
				break;

			//-----------------------------------------

			case 'css_search':
				$this->css_search_frames();
				break;

			case 'csssearchlinks':
				$this->print_css_searchlinks();
				break;

			case 'previewstate':
				$this->do_message("Preview Window");
				break;

			//-----------------------------------------

			case 'css_preview':
				$this->css_preview();
				break;

			default:
				exit();
				break;
		}

	}

	//-----------------------------------------

	function css_search_frames()
	{
		global $ibforums, $DB,  $std, $HTTP_GET_VARS;

		print "<html>
				 <head><title>Search</title></head>
				   <frameset cols='200, *' frameborder='no' border='1' framespacing='0'>
					<frame name='links' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=csssearchlinks&id={$ibforums->input['id']}&element={$_GET['element']}'>
					<frame name='preview' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=previewstate'>
				   </frameset>
			   </html>";

		exit();
	}

	//-----------------------------------------

	function css_preview()
	{
		global $ibforums, $DB,  $std, $HTTP_GET_VARS;

		//-----------------------------------------
		// GET THE TEMPLATES THAT THIS CSS USES
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.intval( $ibforums->input['id']) ) );

		$skin = $DB->fetch_row();

		$element = trim(stripslashes($_GET['element']));

		list($type, $name) = explode( "|", $element );

		$like = "class='{$name}'";
		$first = '.';

		if ($type == 'id')
		{
			$like = "id='{$name}'";
			$first = '#';
		}

		preg_match( "/($first"."$name)\s{0,}\{(.+?)\}/s", $this_set['set_cache_css'], $match );

		$defs = explode( ";", str_replace( "\n\n", "\n", str_replace( "\r\n", "\n", trim($match[2]) ) ) );

		$def_output = "";

		foreach($defs as $bit)
		{
			list($type, $value) = explode( ":", trim($bit) );

			$type = trim($type);

			$value = trim($value);

			if ($type != "" and $value != "")
			{
			    $extra = "";

				if ($type == 'color' or $type == 'background-color')
				{
					$extra = "&nbsp;&nbsp;&nbsp;<input type='text' size='6' style='background-color:$value' readonly>";
				}

				$def_output .= "<tr><td width='40%'><b>$type</b></td><td width='60%'>$value $extra</td></tr>\n";
			}
		}

		$css = "\n<style>\n<!--\n".preg_replace( "#url\(([\"'])?#i", "url(\\1style_images/", $this_set['set_cache_css'] )."\n//-->\n</style>";

    	$html = "<html>
    	           <head>
    	              <title>CSS Preview</title>
    	              $css
    	           </head>
    	           <body topmargin='0' leftmargin='0' rightmargin='0' marginwidth='0' marginheight='0' alink='#000000' vlink='#000000'>
    	           <table border='1' width='95%' cellspacing='0' cellpadding='4' align='center'>
    	           <tr>
    	            <td bgcolor='#EEEEEE' style='font-size:14px'><b>Preview CSS Element '$name'<br>From style sheet '{$this_set['set_name']}'</b></td>
    	           </tr>
    	           </table>
    	           <br>
    	           <table border='1' width='95%' cellspacing='0' cellpadding='4' align='center'>
    	           <tr>
    	            <td><b>Preview</b></td>
    	           </tr>
    	           <tr>
    	           	<td $like>Cozy Lummux Gives Smart Squid Who Asks For Job Pen ([Indeed!])</td>
    	           	</tr>
    	           	</table>
    	           	<br>
    	           	<table border='1' width='95%' cellspacing='0' cellpadding='4' align='center'>
    	            <tr>
    	             <td colspan='2'><b>Formatted CSS Definition</b></td>
    	            </tr>
    	              $def_output
    	           </table>
    	           </body>
    	         </html>
    	        ";

		print $html;

		exit();

    }

   	//-----------------------------------------
   	//---------------------------------------

	function print_css_searchlinks()
	{
		global $ibforums, $DB,  $std, $HTTP_GET_VARS;

		//-----------------------------------------
		// GET THE TEMPLATES THAT THIS CSS USES
		//-----------------------------------------

		$element = trim(stripslashes($_GET['element']));

		list($type, $name) = explode( "|", $element );

		$like = "class=_{$name}";

		if ($type == 'id')
		{
			$like = "id=_{$name}";
		}

		require './sources/admin/skin_info.php';

		//die("SELECT suid, set_id, group_name, func_name FROM ibf_skin_templates WHERE set_id='".$set['tmpl_id']."' AND section_content LIKE '%".$like."%' ORDER BY group_name");

		$DB->query("SELECT suid, set_id, group_name, func_name FROM ibf_skin_templates WHERE set_id='".$ibforums->input['id']."' AND section_content LIKE '%".$like."%' ORDER BY group_name");

		if (! $DB->get_num_rows() )
		{
			$this->do_message("No matches for that string in template set ID {$set['set_id']}");
		}

		$results = array();

		while ( $r = $DB->fetch_row() )
		{
			if ( ! isset($result[ $r['group_name'] ]) )
			{
				$result[ $r['group_name'] ] = array();
			}

			$result[ $r['group_name'] ][] = array( 'suid' => $r['suid'], 'func_name' => $r['func_name'] );

		}

		$ibforums->html .= $ibforums->adskin->start_table();

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search Results</b>" )  , 'title' );

		foreach( $result as $group_name => $sub_array )
		{
			if ( isset($skin_names[ $group_name ]) )
			{
				$group_name = $skin_names[ $group_name ][0];
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>$group_name</b>" )  , 'catrow' );

			if (is_array($sub_array) and count($sub_array) > 0 )
			{
				foreach( $sub_array as $idx => $data )
				{
					$ibforums->html .= $ibforums->adskin->add_td_row( array( "+ <a href='{$ibforums->adskin->base_url}&act=rtempl&code=preview&suid={$data['suid']}&type=text&hl=".urlencode($name)."' target='preview'>{$data['func_name']}</a>" )  );
				}
			}
		}



		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();

	}

	/*-------------------------------------------------------------------------*/
	// PRINT THE SEARCH RESULT LINKS
	/*-------------------------------------------------------------------------*/

	function print_searchlinks()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Printing no results?
		//-----------------------------------------

		if ($ibforums->input['bypass'] == 1)
		{
			$this->do_message("No Search Results");
		}

		//-----------------------------------------
		// Clean up search post
		//-----------------------------------------

		$search_text = trim($this->unconvert_tags(stripslashes($_POST['search'])));

		$search_text = str_replace( "\$", "\\$", $search_text);
		$search_text = str_replace( "'" , "\\'", $search_text);

		if ($search_text == "")
		{
			$this->do_message("Please enter a string to search");
		}

		require './sources/admin/skin_info.php';

		//-----------------------------------------
		// Get template bits
		//-----------------------------------------

		$DB->query("SELECT suid, set_id, group_name, func_name FROM ibf_skin_templates WHERE set_id=".$ibforums->input['set_id']." AND section_content LIKE '%$search_text%' ORDER BY group_name");

		if ( ! $DB->get_num_rows() )
		{
			$this->do_message("No matches for that string");
		}

		$results = array();

		//-----------------------------------------
		// Loop..
		//-----------------------------------------

		while ( $r = $DB->fetch_row() )
		{
			if ( ! isset($result[ $r['group_name'] ]) )
			{
				$result[ $r['group_name'] ] = array();
			}

			$result[ $r['group_name'] ][] = array( 'suid' => $r['suid'], 'func_name' => $r['func_name'] );

		}

		//-----------------------------------------
		// Print..
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table();

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search Results</b>" )  , 'title' );

		foreach( $result as $group_name => $sub_array )
		{
			if ( isset($skin_names[ $group_name ]) )
			{
				$group_name = $skin_names[ $group_name ][0];
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>$group_name</b>" )  , 'catrow' );

			if (is_array($sub_array) and count($sub_array) > 0 )
			{
				foreach( $sub_array as $idx => $data )
				{
					$ibforums->html .= $ibforums->adskin->add_td_row( array( "+ <a href='{$ibforums->adskin->base_url}&act=rtempl&code=preview&suid={$data['suid']}&set_id={$ibforums->input['set_id']}&type=text&hl=".urlencode(stripslashes($_POST['search']))."' target='preview'>{$data['func_name']}</a>" )  );
				}
			}
		}

		//-----------------------------------------
		// Done..
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();

	}

	//-----------------------------------------

	function do_message($message="")
	{
		global $ibforums, $DB,  $std;

		$ibforums->html = "<tr><td id='tdrow1' height='100%' align='center' valign='middle'><br><br><b>$message</b><br><br>&nbsp;</td></tr>";

		$ibforums->admin->print_popup();

	}

	//-----------------------------------------

	function print_search_box()
	{
		global $ibforums, $DB,  $std;

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'searchlinks'   ),
												                 2 => array( 'act'   , 'rtempl'        ),
												                 3 => array( 'set_id', $ibforums->input['set_id']   )
									                    )  ,'theform', 'target="links"'    );

		//-----------------------------------------


		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table(  );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Enter text to search for</b><br>".
										                         $ibforums->adskin->form_input( 'search' )
										                )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search!");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();

	}

	//-----------------------------------------

	function search_frames()
	{
		global $ibforums, $DB,  $std;

		$std->boink_it( $ibforums->base_url.'&act=skintools&code=searchsplash' );


		exit();
		print "<html>
				 <head><title>Search</title></head>
				   <frameset cols='200, *' frameborder='no' border='1' framespacing='0'>
					 <frameset rows='*, 100' frameborder='no' border='1' framespacing='0'>
					   <frame name='links' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=searchlinks&set_id={$ibforums->input['set_id']}&bypass=1'>
					   <frame name='searchbox' scrolling='no' src='{$ibforums->adskin->base_url}&act=rtempl&code=searchbox&set_id={$ibforums->input['set_id']}'>
					 </frameset>
					<frame name='preview' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=previewstate'>
				   </frameset>
			   </html>";

		exit();
	}

	//-----------------------------------------

	function compare_frames()
	{
		global $ibforums, $DB,  $std;

		print "<html>
				 <head>
				  <title>Compare</title>
				 </head>
				   <frameset cols='50%, 50%' frameborder='yes' border='1' framespacing='0'>
				     <frameset rows='30, *' frameborder='yes' border='1' framespacing='0'>
				       <frame name='origtop' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=dotop&content=orig'>
				       <frame name='origbot' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=preview&suid={$ibforums->input['suid']}&type=css'>
				     </frameset>
					<frameset rows='30, *' frameborder='yes' border='1' framespacing='0'>
				       <frame name='newtop' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=dotop&content=new'>
				       <frame name='newbot' scrolling='auto' src='{$ibforums->adskin->base_url}&act=rtempl&code=donew&suid={$ibforums->input['suid']}'>
				     </frameset>
				   </frameset>
			   </html>";

		exit();
	}

	//-----------------------------------------

	function print_compare_top()
	{
		global $ibforums, $DB,  $std;

		$content = $ibforums->input['content'] == 'orig' ? 'Original Template' : 'Current Template';

		print "<html>
			   <body marginheight='0' marginwidth='0' leftmargin='0' topmargin='0' bgcolor='#000055'>
			   <center><font face='verdana' size='2' color='white'><b>$content</b></font></center>
			   </body></html>";

		exit();
	}

	//-----------------------------------------

	function print_compare_new()
	{
		global $ibforums, $DB,  $std;

		$template = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "suid=".intval($ibforums->input['suid']) ) );

		if ( ! $template['suid'] )
		{
			$ibforums->admin->error("You must specify an existing template set ID, go back and try again");
		}

		$skin = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$template['set_id'] ) );

		$css = $skin['set_cache_css'];

		$css_text = "\n<style>\n<!--\n".str_replace( "<#IMG_DIR#>", "style_images/".$r['set_image_dir'], $css )."\n//-->\n</style>";

		print "<html><head>
				$css_text

				</head>
				<body>
				<script language='Javascript'>

					templatedata = window.parent.opener.document.theform.txt{$ibforums->input['suid']}.value;

					document.write( templatedata);
					document.close();

				</script>
				</body></html>
				";

		exit();


	}

	//-----------------------------------------

	function macro_one($msg="")
	{
		global $ibforums, $DB,  $std;

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'macro_two'   ),
																 2 => array( 'act'   , 'rtempl'       ),
																 3 => array( 'suid'  , $ibforums->input['suid']   )
														)      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Macro Look-up" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Enter the Macro to look up</b><br>(EG: {ibf.skin.set_image_dir})",
																 $ibforums->adskin->form_input( 'lookup', $ibforums->input['lookup'] )
														)      );

		$ibforums->html .= $ibforums->adskin->end_form("Look-up");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();

	}

	//-----------------------------------------

	function macro_two()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['lookup'] == "")
		{
			$ibforums->admin->error("You must enter a macro to look up", 1);
		}

		$is_valid = 0;
		$macro    = "";
		$extra    = "";

		if ( preg_match( "/&lt;\{(\S+)\}&gt;/", $ibforums->input['lookup'], $match ) )
		{
			$is_valid = 1;
			$macro    = $match[1];
		}

		else if ( preg_match( "/{?(?:ibf|ipb)\.(skin|lang|vars|member)(.+?)}?$/", $ibforums->input['lookup'], $match ) )
		{
			$is_valid = 1;
			$macro    = $match[2];
			$extra    = $match[1];
			$macro    = str_replace( "&#39;", "'", $macro );
			$macro    = preg_replace( "#[\[\]\"'\.]#", "", $macro );
		}
		else
		{
			$is_valid = 0;
		}

		if ($is_valid != 1)
		{
			$this->macro_one("Wrong format");
		}

		if ($extra != "")
		{
			if ($extra == 'member')
			{
				if (isset($ibforums->member[$macro]))
				{
					if ($ibforums->member[$macro] == "")
					{
						$answer = "";
					}
					else
					{
						$answer = $ibforums->member[$macro];
					}
				}
				$result = "Loaded Member Information";
			}
			else if ($extra == 'vars')
			{
				//-----------------------------------------
				// Filter out sensitive stuff
				//-----------------------------------------

				$safe_INFO['board_name'] = $ibforums->vars['board_name'];
				$safe_INFO['board_url']  = $ibforums->vars['board_url'];

				$answer = $safe_INFO[$macro];

				$result = "Config Variable Information (May be protected)";
			}
			else if ($extra == 'lang')
			{
				$result = "Language Text";

				$DB->simple_construct( array( 'select' => 'group_name', 'from' => 'skin_templates', 'where' => "suid='{$ibforums->input['suid']}'" ) );
				$DB->simple_exec();

				if ( $r = $DB->fetch_row() )
				{
					$filename = preg_replace( "/^skin_/", "lang_", $r['group_name'] );

					if ( @file_exists( "./lang/en/$filename".".php" ) )
					{
						require "./lang/en/$filename".".php";

						$answer = $lang[$macro];
					}
				}
			}
		}
		else
		{
			//-----------------------------------------
			// Is macro
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => 'set_id', 'from' => 'skin_templates', 'where' => "suid='{$ibforums->input['suid']}'" ) );
			$DB->simple_exec();

			$template = $DB->fetch_row();

			$DB->simple_construct( array( 'select' => 'macro_value, macro_replace',
										  'from'   => 'skin_macro',
										  'where'  => "macro_set IN( 1,{$template['set_id']}) AND macro_value='$macro'",
										  'order'  => "macro_set DESC" ) );
			$DB->simple_exec();

			if ($val = $DB->fetch_row())
			{
				$answer = htmlentities($val['macro_replace']);
				$result = "From Macro Set";
			}
			else
			{
				$answer = "Macro not found";
			}
		}

		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Macro Look-up Result <a href='{$ibforums->adskin->base_url}&act=rtempl&code=macro_one&suid={$ibforums->input['suid']}'>Go Again</a>" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Entered Macro</b>", $ibforums->input['lookup'] ) );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Macro Type</b>", $result )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Result</b><br>May be blank if no info", $answer )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();



	}

	/*-------------------------------------------------------------------------*/
	// SHOW PREVIEW
	/*-------------------------------------------------------------------------*/

	function do_preview()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check...
		//-----------------------------------------

		if ($ibforums->input['suid'] == "")
		{
			$ibforums->admin->error("You must specify an existing template set ID, go back and try again");
		}

		//-----------------------------------------
		// Get from DB
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "suid='".$ibforums->input['suid']."'" ) );
		$DB->simple_exec();

		if ( ! $template = $DB->fetch_row() )
		{
			$ibforums->admin->error("You must specify an existing template set ID, go back and try again");
		}

		//-----------------------------------------
		// Get skin set details
		//-----------------------------------------

		$this_set = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$template['set_id'] ) );

		$macros = unserialize(stripslashes($this_set['set_cache_macro']));

		if ( is_array($macros) and count($macros) )
		{
			foreach( $macros as $id => $data )
			{
				$macro_orig[] = "<{".$mc['macro_value']."}>";
				$macro_repl[] = $mc['macro_replace'];
			}
		}

		$table = "<table width='100%' bgcolor='black' cellpadding='4' style='font-family:verdana, arial;font-size:11px;color:white'>
				  <tr>
				   <td align='center' style='font-family:verdana, arial;font-size:11px;color:white'>Template Group: {$template['group_name']} : Template bit: {$template['func_name']}</td>
				  </tr>
				  <tr>
				   <td align='center' style='font-family:verdana, arial;font-size:11px;color:white'>View as [ <a href='{$ibforums->base_url}&act=rtempl&code=preview&suid={$ibforums->input['suid']}&type=text' style='font-family:verdana, arial;font-size:11px;color:white'>Text</a> | <a href='{$ibforums->base_url}&act=rtempl&code=preview&suid={$ibforums->input['suid']}&type=html' style='font-family:verdana, arial;font-size:11px;color:white'>HTML</a> | <a href='{$ibforums->base_url}&act=rtempl&code=preview&suid={$ibforums->input['suid']}&type=css' style='font-family:verdana, arial;font-size:11px;color:white'>HTML with CSS</a> ]</td>
				  </tr>
				  </table>
				  <br><br>
				  ";

		if ($ibforums->input['type'] == 'text')
		{
			@header("Content-type: text/html");
			print $table;
			$html = $this->convert_tags($template['section_content']);

			$html = str_replace( "<" , "&lt;"  , $html);
			$html = str_replace( ">" , "&gt;"  , $html);
			$html = str_replace( "\"", "&quot;", $html);

			if ($_GET['hl'] != "")
			{
				$hl = str_replace( '{-22-}', '"', urldecode(stripslashes($_GET['hl'])) );

				$hl = str_replace( "<" , "&lt;"  , $hl);
				$hl = str_replace( ">" , "&gt;"  , $hl);
				$hl = str_replace( "\"", "&quot;", $hl);

				$html = str_replace( $hl, "<span style='color:red;font-weight:bold;background-color:yellow'>$hl</span>", $html );
			}

			$html = preg_replace( "!&lt;\!--(.+?)(//)?--&gt;!s"                    , "&#60;&#33;<span style='color:red'>--\\1--\\2</span>&#62;", $html );
			$html = preg_replace( "#&lt;([^&<>]+)&gt;#s"                           , "&lt;<span style='color:blue'>\\1</span>&gt;"        , $html );   //Matches <tag>
			$html = preg_replace( "#&lt;([^&<>]+)=#s"                              , "&lt;<span style='color:blue'>\\1</span>="           , $html );   //Matches <tag
			$html = preg_replace( "#&lt;/([^&]+)&gt;#s"                            , "&lt;/<span style='color:blue'>\\1</span>&gt;"       , $html );   //Matches </tag>
			$html = preg_replace( "!=(&quot;|')([^<>])(&quot;|')(\s|&gt;)!s"   , "=\\1<span style='color:purple'>\\2</span>\\3\\4"       , $html );   //Matches ='this'

			$html = str_replace( "\n", "<br>", str_replace("\r\n", "\n", $html ) );

			print "<pre>".$html."</pre>";
			exit();

		}
		else if ($ibforums->input['type'] == 'html')
		{
			@header("Content-type: text/html");
			print $table;
			print $this->convert_tags($template['section_content']);

			exit();
		}
		else if($ibforums->input['type'] == 'css')
		{
			$css = $this_set['set_cache_css'];

			$css_text = "\n<style>\n<!--\n".str_replace( "<#IMG_DIR#>", "style_images/".$r['img_dir'], $css)."\n//-->\n</style>";

			@header("Content-type: text/html");
			print "<html><head><title>Preview</title>$css_text</head><body>$table \n";
			print str_replace( $macro_orig, $macro_repl, $this->convert_tags($template['section_content']) );

			exit();

		}



	}


	//-----------------------------------------



	function convert_tags($t="")
	{
		if ($t == "")
		{
			return "";
		}

		$t = preg_replace( "/{?\\\$ibforums->base_url}?/"            , "{ibf.script_url}"   , $t );
		$t = preg_replace( "/{?\\\$ibforums->session_id}?/"          , "{ibf.session_id}"   , $t );
		$t = preg_replace( "/{?\\\$ibforums->skin\['?(\w+)'?\]}?/"   , "{ibf.skin.\\1}"      , $t );
		$t = preg_replace( "/{?\\\$ibforums->lang\['?(\w+)'?\]}?/"   , "{ibf.lang.\\1}"      , $t );
		$t = preg_replace( "/{?\\\$ibforums->vars\['?(\w+)'?\]}?/"   , "{ibf.vars.\\1}"      , $t );
		$t = preg_replace( "/{?\\\$ibforums->member\['?(\w+)'?\]}?/" , "{ibf.member.\\1}"    , $t );

		return $t;

	}

	function unconvert_tags($t="")
	{
		if ($t == "")
		{
			return "";
		}

		$t = preg_replace( "/{ibf\.script_url}/i"   , '{$ibforums->base_url}'         , $t);
		$t = preg_replace( "/{ibf\.session_id}/i"   , '{$ibforums->session_id}'       , $t);
		$t = preg_replace( "/{ibf\.skin\.(\w+)}/"   , '{$ibforums->skin[\''."\\1".'\']}'   , $t);
		$t = preg_replace( "/{ibf\.lang\.(\w+)}/"   , '{$ibforums->lang[\''."\\1".'\']}'   , $t);
		$t = preg_replace( "/{ibf\.vars\.(\w+)}/"   , '{$ibforums->vars[\''."\\1".'\']}'   , $t);
		$t = preg_replace( "/{ibf\.member\.(\w+)}/" , '{$ibforums->member[\''."\\1".'\']}' , $t);

		return $t;

	}


}


?>