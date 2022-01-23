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
|   > Admin HTML stuff library
|   > Script written by Matt Mecham
|   > Date started: 1st march 2002
|
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


class admin_skin {

	var $base_url;
	var $img_url;
	var $has_title;
	var $td_widths = array();
	var $td_header = array();
	var $td_colspan;
	var $valid_hostnames = array();

	function admin_skin()
	{
		global $ibforums;

		$this->base_url = $ibforums->base_url;
		$this->img_url  = $ibforums->skin_url;
	}

	//-----------------------------------------
	//--------------------------------------------------------------------
	// Javascript elements
	//-----------------------------------------
	//--------------------------------------------------------------------

	function js_make_button($text="", $url="", $css='realbutton', $title="")
	{
		return "<input type='button' class='{$css}' value='{$text}' onclick='self.location.href=\"{$url}\"' title='$title' />";

	}

	function js_pop_win()
	{

		return "
				<script language='javascript'>
				<!--
					function pop_win(theUrl, winName, theWidth, theHeight)
					{
						 	if (winName == '') { winName = 'Preview'; }
						 	if (theHeight == '') { theHeight = 400; }
						 	if (theWidth == '') { theWidth = 400; }

						 	window.open('{$this->base_url}'+theUrl,winName,'width='+theWidth+',height='+theHeight+',resizable=yes,scrollbars=yes');
					}

				//-->
				</script>
				";

		}

	function js_help_link($help="", $text="Quick Help")
	{
		return "( <a href='#' onClick=\"window.open('{$this->base_url}&act=quickhelp&id=$help','Help','width=250,height=400,resizable=yes,scrollbars=yes'); return false;\">$text</a> )";

	}

	function js_template_tools()
	{
		global $ibforums;

		return "
				<script language='javascript'>
				<!--
					function restore(suid, expand)
					{
						 if (confirm(\"Are you sure you want to restore the template?\\nALL UNSAVED CHANGES WILL BE LOST!\"))
						 {
          					window.location = '{$this->base_url}&act=templ&code=edit_bit&type=single&id={$ibforums->input['id']}&p={$ibforums->input['p']}&suid=' + suid + '&expand=' + expand;
       					 }
       					 else
       					 {
          					alert (\"Restore Cancelled\");
      					 }
      				}

      				var template_bit_ids = '<!--IPB.TEMPLATE_BIT_IDS-->';
				//-->
				</script>
				";

	}


	function js_checkdelete($txt="Are you sure you wish to remove this?")
	{

		return "
				<script language='javascript'>
				<!--
				function checkdelete(theURL) {

					final_url = \"{$this->base_url}&\" + theURL;

					if ( confirm('{$txt}\\nIt cannot be undone!') )
					{
						document.location.href=final_url;
					}
					else
					{
						alert('Ok, remove cancelled!');
					}
				}
				//-->
				</script>
				";
	}



	function js_no_specialchars()
	{
		return "
				<script language='javascript'>
				<!--
				function no_specialchars(type) {

			      var name;

				  if (type == 'sets')
				  {
				  	var field = document.theAdminForm.sname;
				  	name = 'Skin Set Title';
				  }

				  if (type == 'wrapper')
				  {
				  	var field = document.theAdminForm.name;
				  	name = 'Wrapper Title';
				  }

				  if (type == 'csssheet')
				  {
				  	var field = document.theAdminForm.name;
				  	name = 'StyleSheet Title';
				  }

				  if (type == 'templates')
				  {
				  	var field = document.theAdminForm.skname;
				  	name = 'Template Set Name';
				  }

				  if (type == 'images')
				  {
				  	var field = document.theAdminForm.setname;
				  	name = 'Image & Macro Set Title';
				  }

				  var valid = 'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.()[]:;~+-_';
				  var ok = 1;
				  var temp;

				  for (var i=0; i < field.value.length; i++) {
				      temp = \"\" + field.value.substring(i,i+1);
				      if (valid.indexOf(temp) == \"-1\")
				      {
				      	ok = 0;
				      }
				  }
				  if (ok == 0)
				  {
				  	alert('Invalid entry for: ' + name + ', you can only use alphanumerics and the following special characters.\\n. ( ) : ; ~ + - _');
				  	return false;
				  } else {
				  	return true;
				  }
				}
				//-->
				</script>
				";
	}

	function make_page_jump($tp="", $pp="", $ub="" )
	{
		global $IN, $INFO;
		return "<a href='#' title=\"Jump to a page...\" onclick=\"multi_page_jump('$ub',$tp,$pp);\">Pages:</a>";
	}


	//-----------------------------------------
	//--------------------------------------------------------------------
	// FORM ELEMENTS
	//-----------------------------------------
	//--------------------------------------------------------------------

	function start_form($hiddens="", $name='theAdminForm', $js="") {
		global $IN, $INFO;

		$form = "<form action='{$this->base_url}' method='post' name='$name' $js>
				 ";

		if (is_array($hiddens))
		{
			foreach ($hiddens as $k => $v) {
				$form .= "\n<input type='hidden' name='{$v[0]}' value='{$v[1]}'>";
			}
		}

		return $form;

	}

	//-----------------------------------------

	function form_hidden($hiddens="") {

		if (is_array($hiddens))
		{
			foreach ($hiddens as $k => $v) {
				$form .= "\n<input type='hidden' name='{$v[0]}' value='{$v[1]}'>";
			}
		}

		return $form;
	}


	//-----------------------------------------

	function end_form($text = "", $js = "", $extra = "")
	{
		// If we have text, we print another row of TD elements with a submit button

		$html    = "";
		$colspan = "";

		if ($text != "")
		{
			if ($this->td_colspan > 0)
			{
				$colspan = " colspan='".$this->td_colspan."' ";
			}

			$html .= "<tr><td align='center' class='pformstrip'".$colspan."><input type='submit' value='$text'".$js." id='button' accesskey='s'>{$extra}</td></tr>\n";
		}

		$html .= "</form>";

		return $html;

	}

	//-----------------------------------------

	function end_form_standalone($text = "", $js = "")
	{

		$html    = "";
		$colspan = "";

		if ($text != "")
		{
			$html .= "<div class='tableborder'><div align='center' class='pformstrip'><input type='submit' value='$text'".$js." id='button' accesskey='s'></div></div>\n";
		}

		$html .= "</form>";

		return $html;

	}

	//-----------------------------------------

	function form_upload($name="FILE_UPLOAD", $js="") {

		if ($js != "")
		{
			$js = ' '.$js.' ';
		}

		return "<input class='textinput' type='file' $js size='30' name='$name'>";

	}

	//-----------------------------------------

	function form_input($name, $value="", $type='text', $js="", $size="30") {

		if ($js != "")
		{
			$js = ' '.$js.' ';
		}

		return "<input type='$type' name='$name' value='$value' size='$size'".$js." class='textinput'>";

	}

	function form_simple_input($name, $value="", $size='5') {

		return "<input type='text' name='$name' value='$value' size='$size' class='textinput'>";

	}

	//-----------------------------------------

	function form_textarea($name, $value="", $cols='60', $rows='5', $wrap='soft', $id="", $style="") {

		if ( $id )
		{
			$id = "id='$id'";
		}

		if ( $style )
		{
			$style = "style='$style'";
		}

		return "<textarea name='$name' cols='$cols' rows='$rows' wrap='$wrap' $id $style class='multitext'>$value</textarea>";

	}

	//-----------------------------------------

	function form_dropdown($name, $list=array(), $default_val="", $js="", $css="") {

		if ($js != "")
		{
			$js = ' '.$js.' ';
		}

		if ($css != "")
		{
			$css = ' class="'.$css.'" ';
		}

		$html = "<select name='$name'".$js." $css class='dropdown'>\n";

		foreach ($list as $k => $v)
		{

			$selected = "";

			if ( ($default_val != "") and ($v[0] == $default_val) )
			{
				$selected = ' selected';
			}

			$html .= "<option value='".$v[0]."'".$selected.">".$v[1]."</option>\n";
		}

		$html .= "</select>\n\n";

		return $html;


	}

	//-----------------------------------------

	function form_multiselect($name, $list=array(), $default=array(), $size=5, $js="") {

		if ($js != "")
		{
			$js = ' '.$js.' ';
		}

		//$html = "<select name='$name".'[]'."'".$js." id='dropdown' multiple='multiple' size='$size'>\n";
		$html = "<select name='$name"."'".$js." class='dropdown' multiple='multiple' size='$size'>\n";
		foreach ($list as $k => $v)
		{

			$selected = "";

			if ( count($default) > 0 )
			{
				if ( in_array( $v[0], $default ) )
				{
					$selected = ' selected="selected"';
				}
			}

			$html .= "<option value='".$v[0]."'".$selected.">".$v[1]."</option>\n";
		}

		$html .= "</select>\n\n";

		return $html;


	}

	//-----------------------------------------

	function form_yes_no( $name, $default_val="", $js=array() ) {

		$y_js = "";
		$n_js = "";

		if ( $js['yes'] != "" )
		{
			$y_js = $js['yes'];
		}

		if ( $js['no'] != "" )
		{
			$n_js = $js['no'];
		}

		$yes = "Yes &nbsp; <input type='radio' name='$name' value='1' $y_js id='green'>";
		$no  = "<input type='radio' name='$name' value='0' $n_js id='red'> &nbsp; No";



		if ($default_val == 1)
		{

			$yes = "Yes &nbsp; <input type='radio' name='$name' value='1'$y_js checked id='green'>";
		}
		else
		{
			$no  = "<input type='radio' name='$name' value='0' checked $n_js id='red'> &nbsp; No";
		}


		return $yes.'&nbsp;&nbsp;&nbsp;'.$no;

	}

	//-----------------------------------------

	function form_checkbox( $name, $checked=0, $val=1, $js=array() ) {

		if ($checked == 1)
		{

			return "<input type='checkbox' name='$name' value='$val' checked='checked'>";
		}
		else
		{
			return "<input type='checkbox' name='$name' value='$val'>";
		}

	}

	//-----------------------------------------

	function build_group_perms( $show='*', $read='*', $write='*', $reply='*', $upload='*' )
	{
		global $DB;


		$html = "

				<script language='Javascript1.1'>
				<!--

				function check_all(str_part) {

					var f = document.theAdminForm;

					for (var i = 0 ; i < f.elements.length; i++)
					{
						var e = f.elements[i];

						if ( (e.name != 'UPLOAD_ALL') && (e.name != 'READ_ALL') && (e.name != 'REPLY_ALL') && (e.name != 'START_ALL') && (e.name != 'SHOW_ALL') && (e.type == 'checkbox') && (! e.disabled) )
						{
							s = e.name;
							a = s.substring(0, 4);

							if (a == str_part)
							{
								e.checked = true;
							}
						}
					}
				}

				function obj_checked(IDnumber) {

					var f = document.theAdminForm;

					str_part = '';

					if (IDnumber == 1) { str_part = 'READ' }
					if (IDnumber == 2) { str_part = 'REPL' }
					if (IDnumber == 3) { str_part = 'STAR' }
					if (IDnumber == 4) { str_part = 'UPLO' }
					if (IDnumber == 5) { str_part = 'SHOW' }

					totalboxes = 0;
					total_on   = 0;

					for (var i = 0 ; i < f.elements.length; i++)
					{
						var e = f.elements[i];

						if ( (e.name != 'UPLOAD_ALL') && (e.name != 'READ_ALL') && (e.name != 'REPLY_ALL') && (e.name != 'START_ALL') && (e.name != 'SHOW_ALL') && (e.type == 'checkbox') )
						{
							s = e.name;
							a = s.substring(0, 4);

							if (a == str_part)
							{
								totalboxes++;

								if (e.checked)
								{
									total_on++;
								}
							}
						}
					}

					if (totalboxes == total_on)
					{
						if (IDnumber == 1) { f.READ_ALL.checked  = true; }
						if (IDnumber == 2) { f.REPLY_ALL.checked = true; }
						if (IDnumber == 3) { f.START_ALL.checked = true; }
						if (IDnumber == 4) { f.UPLOAD_ALL.checked = true; }
						if (IDnumber == 5) { f.SHOW_ALL.checked  = true; }
					}
					else
					{
						if (IDnumber == 1) { f.READ_ALL.checked  = false; }
						if (IDnumber == 2) { f.REPLY_ALL.checked = false; }
						if (IDnumber == 3) { f.START_ALL.checked = false; }
						if (IDnumber == 4) { f.UPLOAD_ALL.checked = false; }
						if (IDnumber == 5) { f.SHOW_ALL.checked  = false; }
					}

				}

				function checkcol(IDnumber,status) {

					var f = document.theAdminForm;

					str_part = '';

					if (IDnumber == 1) { str_part = 'READ' }
					if (IDnumber == 2) { str_part = 'REPL' }
					if (IDnumber == 3) { str_part = 'STAR' }
					if (IDnumber == 4) { str_part = 'UPLO' }
					if (IDnumber == 5) { str_part = 'SHOW' }

					for (var i = 0 ; i < f.elements.length; i++)
					{
						var e = f.elements[i];

						if ( (e.name != 'UPLOAD_ALL') && (e.name != 'READ_ALL') && (e.name != 'REPLY_ALL') && (e.name != 'START_ALL') && (e.name != 'SHOW_ALL') && (e.type == 'checkbox') )
						{
							s = e.name;
							a = s.substring(0, 4);

							if (a == str_part)
							{
								if ( status == 1 )
								{
									e.checked = true;
									if (IDnumber == 1) { f.READ_ALL.checked  = true; }
									if (IDnumber == 2) { f.REPLY_ALL.checked = true; }
									if (IDnumber == 3) { f.START_ALL.checked = true; }
									if (IDnumber == 4) { f.UPLOAD_ALL.checked = true; }
									if (IDnumber == 5) { f.SHOW_ALL.checked   = true; }
								}
								else
								{
									e.checked = false;
									if (IDnumber == 1) { f.READ_ALL.checked  = false; }
									if (IDnumber == 2) { f.REPLY_ALL.checked = false; }
									if (IDnumber == 3) { f.START_ALL.checked = false; }
									if (IDnumber == 4) { f.UPLOAD_ALL.checked = false; }
									if (IDnumber == 5) { f.SHOW_ALL.checked   = false; }
								}
							}
						}
					}
				}

				function checkrow(IDnumber,status) {

					var f = document.theAdminForm;

					str_part = '';

					if ( status == 1 )
					{
						mystat = 'true';
					}
					else
					{
						mystat = 'false';
					}

					eval( 'f.READ_'+IDnumber+'.checked='+mystat );
					eval( 'f.REPLY_'+IDnumber+'.checked='+mystat );
					eval( 'f.START_'+IDnumber+'.checked='+mystat );
					eval( 'f.UPLOAD_'+IDnumber+'.checked='+mystat );
					eval( 'f.SHOW_'+IDnumber+'.checked='+mystat );

					obj_checked(1);
					obj_checked(2);
					obj_checked(3);
					obj_checked(4);
					obj_checked(5);
				}

				//-->

				</script>

				";

		$html .= $this->add_td_basic( "GLOBAL: All current and future permission masks", "left", "pformstrip" );

		//-----------------------------------------

		if ($show == '*')
		{
			$html_show = "<input type='checkbox' onClick='check_all(\"SHOW\")' name='SHOW_ALL' value='1' checked>\n";
		}
		else
		{
			$html_show = "<input type='checkbox' onClick='check_all(\"SHOW\")' name='SHOW_ALL' value='1'>\n";
		}

		//-----------------------------------------

		if ($read == '*')
		{
			$html_read = "<input type='checkbox' onClick='check_all(\"READ\")' name='READ_ALL' value='1' checked>\n";
		}
		else
		{
			$html_read = "<input type='checkbox' onClick='check_all(\"READ\")' name='READ_ALL' value='1'>\n";
		}

		//-----------------------------------------

		if ($reply == '*')
		{
			$html_reply = "<input type='checkbox' onClick='check_all(\"REPL\")' name='REPLY_ALL' value='1' checked>\n";
		}
		else
		{
			$html_reply = "<input type='checkbox' onClick='check_all(\"REPL\")' name='REPLY_ALL' value='1'>\n";
		}

		//-----------------------------------------

		if ($write == '*')
		{
			$html_start = "<input type='checkbox' onClick='check_all(\"STAR\")' name='START_ALL' value='1' checked>\n";
		}
		else
		{
			$html_start = "<input type='checkbox' onClick='check_all(\"STAR\")' name='START_ALL' value='1'>\n";
		}

		if ($upload == '*')
		{
			$html_upload = "<input type='checkbox' onClick='check_all(\"UPLO\")' name='UPLOAD_ALL' value='1' checked>\n";
		}
		else
		{
			$html_upload = "<input type='checkbox' onClick='check_all(\"UPLO\")' name='UPLOAD_ALL' value='1'>\n";
		}

		//-----------------------------------------

		$html .= $this->add_td_row( array(   "<b>All current and future permission masks</b>",
											 "<center id='mgyellow'>$html_show</center>",
											 "<center id='mgblue'>$html_read</center>",
											 "<center id='mggreen'>$html_reply</center>",
											 "<center id='mgred'>$html_start</center>",
											 "<center id='memgroup'>$html_upload</center>",
								 )       );

		//-----------------------------------------

		$html .= $this->add_td_basic( "OR: Adjust permissions per mask below", "left", "pformstrip" );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'order' => "perm_name ASC" ) );
		$DB->simple_exec();

		while ( $data = $DB->fetch_row() )
		{
			if ($show == '*')
			{
				$html_show = "<input type='checkbox' name='SHOW_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(5)\">";
			}
			else if ( preg_match( "/(^|,)".$data['perm_id']."(,|$)/", $show ) )
			{
				$html_show = "<input type='checkbox' name='SHOW_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(5)\">";
			}
			else
			{
				$html_show = "<input type='checkbox' name='SHOW_{$data['perm_id']}' value='1' onclick=\"obj_checked(5)\">";
			}

			//-----------------------------------------

			if ($read == '*')
			{
				$html_read = "<input type='checkbox' name='READ_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(1)\">";
			}
			else if ( preg_match( "/(^|,)".$data['perm_id']."(,|$)/", $read ) )
			{
				$html_read = "<input type='checkbox' name='READ_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(1)\">";
			}
			else
			{
				$html_read = "<input type='checkbox' name='READ_{$data['perm_id']}' value='1' onclick=\"obj_checked(1)\">";
			}

			//-----------------------------------------

			if ($reply == '*')
			{
				$html_reply = "<input type='checkbox' name='REPLY_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(2)\">";
			}
			else if ( preg_match( "/(?:^|,)".$data['perm_id']."(?:,|$)/", $reply ) )
			{
				$html_reply = "<input type='checkbox' name='REPLY_{$data['perm_id']}' value='1' onclick=\"obj_checked(2)\" checked>";
			}
			else
			{
				$html_reply = "<input type='checkbox' name='REPLY_{$data['perm_id']}' value='1' onclick=\"obj_checked(2)\">";
			}

			//-----------------------------------------

			if ($write == '*')
			{
				$html_start = "<input type='checkbox' name='START_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(3)\">";
			}
			else if ( preg_match( "/(?:^|,)".$data['perm_id']."(?:,|$)/", $write ) )
			{
				$html_start = "<input type='checkbox' name='START_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(3)\">";
			}
			else
			{
				$html_start = "<input type='checkbox' name='START_{$data['perm_id']}' value='1' onclick=\"obj_checked(3)\">";
			}

			//-----------------------------------------

			if ($upload == '*')
			{
				$html_upload = "<input type='checkbox' name='UPLOAD_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(4)\">";
			}
			else if ( preg_match( "/(?:^|,)".$data['perm_id']."(?:,|$)/", $upload ) )
			{
				$html_upload = "<input type='checkbox' name='UPLOAD_{$data['perm_id']}' value='1' checked onclick=\"obj_checked(4)\">";
			}
			else
			{
				$html_upload = "<input type='checkbox' name='UPLOAD_{$data['perm_id']}' value='1' onclick=\"obj_checked(4)\">";
			}

			$html .= $this->add_td_row( array(   "<div align='right' style='font-weight:bold'>{$data['perm_name']} &nbsp; <input type='button' id='button' value='+' onclick='checkrow({$data['perm_id']},1)' />&nbsp;<input type='button' id='button' value='-' onclick='checkrow({$data['perm_id']},0)' /></div>",
												 "<center id='mgyellow'>$html_show</center>",
												 "<center id='mgblue'>$html_read</center>",
											 	 "<center id='mggreen'>$html_reply</center>",
											  	 "<center id='mgred'>$html_start</center>",
											     "<center id='memgroup'>$html_upload</center>",
									  )       );
		}

		$html .= $this->add_td_row( array(   "&nbsp;",
											 "<center><input type='button' id='button' value='+' onclick='checkcol(5,1)' />&nbsp;<input type='button' id='button' value='-' onclick='checkcol(5,0)' /></center>",
											 "<center><input type='button' id='button' value='+' onclick='checkcol(1,1)' />&nbsp;<input type='button' id='button' value='-' onclick='checkcol(1,0)' /></center>",
											 "<center><input type='button' id='button' value='+' onclick='checkcol(2,1)' />&nbsp;<input type='button' id='button' value='-' onclick='checkcol(2,0)' /></center>",
											 "<center><input type='button' id='button' value='+' onclick='checkcol(3,1)' />&nbsp;<input type='button' id='button' value='-' onclick='checkcol(3,0)' /></center>",
											 "<center><input type='button' id='button' value='+' onclick='checkcol(4,1)' />&nbsp;<input type='button' id='button' value='-' onclick='checkcol(4,0)' /></center>",
								  )       );

		return $html;

	}


	//-----------------------------------------
	//--------------------------------------------------------------------
	// SCREEN ELEMENTS
	//-----------------------------------------
	//--------------------------------------------------------------------

	function add_subtitle($title="",$id="subtitle", $colspan="") {

		if ($colspan != "")
		{
			$colspan = " colspan='$colspan' ";
		}

		return "\n<tr><td id='$id'".$colspan.">$title</td><tr>\n";

	}

	//-----------------------------------------

	function start_table( $title="", $desc="") {

		if ($title != "")
		{
			$this->has_title = 1;
			$html .= "<div class='tableborder'>
						<div class='maintitle'>$title</div>\n";

			if ( $desc != "" )
			{
				$html .= "<div class='pformstrip'>$desc</div>\n";
			}
		}



		$html .= "\n<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'>";


		if (isset($this->td_header[0]))
		{
			$html .= "<tr>\n";

			// Auto remove two &nbsp; only headers..

			if ( $this->td_header[0][0] == '&nbsp;' && $this->td_header[1][0] == '&nbsp;' && ( ! isset( $this->td_header[2][0] ) ) )
			{
				$this->td_header[0][0] = '{none}';
				$this->td_header[1][0] = '{none}';
			}

			foreach ($this->td_header as $td)
			{
				if ($td[1] != "")
				{
					$width = " width='{$td[1]}' ";
				}
				else
				{
					$width = "";
				}

				if ($td[0] != '{none}')
				{
					$html .= "<td class='titlemedium'".$width."align='center'>{$td[0]}</td>\n";
				}

				$this->td_colspan++;
			}

			$html.= "</tr>\n";
		}

		return $html;

	}

	//-----------------------------------------

	function add_standalone_row($text = "", $align='center', $class='pformstrip')
	{
		return "<div class='tableborder'><div align='{$align}' class='{$class}'>{$text}</div></div>\n";
	}

	//-----------------------------------------


	function add_td_row( $array, $css="", $align='middle' ) {

		if (is_array($array))
		{
			$html = "<tr>\n";

			$count = count($array);

			$this->td_colspan = $count;

			for ($i = 0; $i < $count ; $i++ )
			{

				$td_col = $i % 2 ? 'tdrow2' : 'tdrow1';

				if ($css != "")
				{
					$td_col = $css;
				}

				if (is_array($array[$i]))
				{
					$text    = $array[$i][0];
					$colspan = $array[$i][1];
					$td_col  = $array[$i][2] != "" ? $array[$i][2] : $td_col;

					$html .= "<td class='$td_col' colspan='$colspan' valign='$align'>".$text."</td>\n";
				}
				else
				{
					if ($this->td_header[$i][1] != "")
					{
						$width = " width='{$this->td_header[$i][1]}' ";
					}
					else
					{
						$width = "";
					}

					$html .= "<td class='$td_col' $width valign='$align'>".$array[$i]."</td>\n";
				}
			}

			$html .= "</tr>\n";

			return $html;
		}

	}

	//-----------------------------------------

	function add_td_basic($text="",$align="left",$id="tdrow1") {

		$html    = "";
		$colspan = "";

		if ($text != "")
		{
			if ($this->td_colspan > 0)
			{
				$colspan = " colspan='".$this->td_colspan."' ";
			}


			$html .= "<tr><td align='$align' class='$id'".$colspan.">$text</td></tr>\n";
		}

		return $html;

	}

	//-----------------------------------------

	function add_td_spacer() {

		if ($this->td_colspan > 0)
		{
			$colspan = " colspan='".$this->td_colspan."' ";
		}

		return "<tr><td".$colspan."><br /></td></tr>";

	}



	//-----------------------------------------

	function end_table() {

		$this->td_header = array();  // Reset TD headers

		if ($this->has_title == 1)
		{
			$this->has_title = 0;

			return "</table></div><br />\n\n";
		}
		else
		{
			return "</table>\n\n";
		}

	}


	//-----------------------------------------


	function print_top($title="",$desc="")
	{
		global $ibforums;

		return "<html>
		          <head><title>Menu</title>
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
				   var ipb_var_cookie_domain = \"{$ibforums->vars['cookie_domain']}\";
				   var ipb_var_cookie_path   = \"{$ibforums->vars['cookie_path']}\";
				   //-->
				  </script>
				  <script type=\"text/javascript\" src='jscripts/ipb_global.js'></script>
				  <script type=\"text/javascript\" src='{$ibforums->skin_url}/acp_js.js'></script>
				  </head>

				 <body {$this->top_extra}>

				 <div id='logostrip'>
				  <div id='logostripinner'><div style='font-weight:bold;font-size:12px;color:#FFF;padding-top:14px;padding-left:4px'>$title</div></div>
				 </div>

				 <table width='100%' cellspacing='6' id='submenu'>
				 <tr>
				  <td><a href='{$this->base_url}&act=menu&show=all' target='menu'>Expand Menu</a> &middot; <a href='{$this->base_url}&act=menu&show=none' target='menu'>Reduce Menu</a></td>
				  <td align='right'><a href='#' onclick='window.location=window.location'>Reload Window</a> &middot; <a href='{$this->base_url}&act=index' target='body'>ACP Home</a> &middot; <a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}' target='_blank'>Board Home</a></td>
				 </tr>
				 </table>
				 <!--NAV-->
				 <div id='description'>$desc</div>
				 <!--IPB.ERROR-->
				 <!--IPB.MESSAGE-->
				 <br />";

	}

	function wrap_nav($links)
	{
		return "\n<div class='navstrip'>$links</div>\n";
	}

	//-----------------------------------------

	function print_foot() {

		return "<br />
				<div align='right' id='jwrap'><strong>Quick Jump</strong> <!--JUMP--></div>
				<div class='copy' align='center'>Invision Power Board &copy ".date("Y")." <a href='http://www.invisionpower.com' target='_blank'>IPS, Inc.</a></div>
				 </body>
				 </html>";
	}


	//-----------------------------------------




	//{ background-color:#C2CFDF; font-weight:bold; font-size:12px; color:#000055 }


	function menu_top()
	{
		global $ibforums;

		return "<html>
		          <head><title>Menu</title>
		          <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$ibforums->skin_url}/acp_css.css\" />
		          <script type=\"text/javascript\">
				  <!--
				   var ipb_var_st       = \"{$ibforums->input['st']}\";
				   var ipb_lang_tpl_q1  = \"{$ibforums->lang['tpl_q1']}\";
				   var ipb_var_phpext   = \"{$ibforums->vars['php_ext']}\";
				   var ipb_var_base_url = \"{$ibforums->base_url}\";
				   var ipb_var_cookieid = \"{$ibforums->vars['cookie_id']}\";
				   var ipb_var_cookie_domain = \"{$ibforums->vars['cookie_domain']}\";
				    var ipb_var_cookie_path   = \"{$ibforums->vars['cookie_path']}\";
				   var menu_ids         = \"<!--{IDS}-->\";
				   //-->
				  </script>
				  <script type=\"text/javascript\" src='jscripts/ipb_global.js'></script>
				  <script type=\"text/javascript\" src='{$ibforums->skin_url}/acp_js.js'></script>
				  </head>
				 <body>
				 <div align='center' id='logostrip'><img src='{$this->img_url}/logo4.gif' border='0'></div>
				 <div class='tableborder'>
				  <div class='menulinkwrap'>
				   &nbsp;<img src='{$this->img_url}/item.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='javascript:expandmenu();'>Expand</a> &middot; <a href='javascript:collapsemenu();'>Collapse</a> Menu
				   <br />&nbsp;<img src='{$this->img_url}/item.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='{$this->base_url}&act=index' target='body'>ACP</a> &middot; <a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}' target='body'>Board</a> Home
				   <br />&nbsp;<img src='{$this->img_url}/item.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='{$this->base_url}&act=ips&code=docs' target='body' style='text-decoration:none'>IPB Documentation</a>
				   <br />&nbsp;<img src='{$this->img_url}/item.gif' border='0' alt='' valign='absmiddle'>&nbsp;<a href='{$this->base_url}&act=op&code=phpinfo' style='text-decoration:none' target='body'>PHP Info</a>
				  </div>
				 </div>
				 <br />
				";

	}

	function get_css() { }

	//-----------------------------------------

	function menu_foot() {

		return "
				</body>
				 </html>";
	}


	//-----------------------------------------


	function menu_cat_wrap($show, $name="", $links="", $id = "", $desc, $color='#FFF', $extra='') {
		global $IN;

		return "$extra
				<div style='padding:4px;background-color:$color'>
				<div class='tableborder' style='display:{$show['div_fo']}' id='fo_{$id}'>
				  <div class='menumaintopon'>
				    <a href='#' onclick=\"togglemenucategory({$id}, 0); return false;\"><img src='{$this->img_url}/minus.gif' border='0' alt='Collapse Category' title='Collapse Category'></a>
				    <a href='#' title='$desc' onclick=\"togglemenucategory({$id}, 0); return false;\">$name</a>
				  </div>
				  <div class='menulinkwrap'>$links</div>
				</div>
				<div class='tableborder' style='display:{$show['div_fc']}' id='fc_{$id}'>
				  <div class='menumaintopoff'>
				    <a href='#' onclick=\"togglemenucategory({$id}, 1); return false;\"><img src='{$this->img_url}/plus.gif' border='0' alt='Collapse Category' title='Collapse Category'></a>
				    <a href='#' title='$desc' onclick=\"togglemenucategory({$id}, 1); return false;\">$name</a>
				  </div>
				</div>
				</div>\n";


	}



	//-----------------------------------------

	function menu_cat_link($pid, $cid, $url="", $name="", $urltype=0, $isredirect=0)
	{
		global $INFO;

		if ( $urltype == 1 )
		{
			$theurl = $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?';
		}
		else
		{
			$theurl = $this->base_url;
		}

		if ( $isredirect )
		{
			$icon      = "<img src='{$this->img_url}/menu_shortcut.gif' border='0' alt='' valign='absmiddle'>";
			$extra_css = ';font-style:italic';
		}
		else
		{
			$icon      = "<img src='{$this->img_url}/item.gif' border='0' alt='' valign='absmiddle'>";
			$extra_css = "";
		}

		return "<div class='menulinkoff' id='m_{$cid}_{$pid}' onmouseout=\"change_cell_color('m_{$cid}_{$pid}', 'menulinkoff');\" onmouseover=\"change_cell_color('m_{$cid}_{$pid}', 'menulinkon');\">&nbsp;{$icon}&nbsp;<a href='{$theurl}&$url' target='body' style='text-decoration:none{$extra_css}'>$name</a></div>\n";

	}


	//-----------------------------------------

	function frame_set()
	{
		global $ibforums, $std;

		//-----------------------------------------
		// Carry on
		//-----------------------------------------

		$extra_query = 'act=index';

		if ( $ibforums->input['act'] != 'idx' )
		{
			$extra_query = str_replace( '&amp;', '&', $std->clean_value($_SERVER['QUERY_STRING']) );
			$extra_query = str_replace( "{$ibforums->vars['board_url']}"           , "" , $extra_query );
			$extra_query = preg_replace( "!/?admin\.{$ibforums->vars['php_ext']}!i", "" , $extra_query );
			$extra_query = preg_replace( "!^\?!"                                   , "" , $extra_query );
			$extra_query = str_replace( "printframes=1"                            , "" , $extra_query );
			$extra_query = preg_replace( "!adsess=(\w){32}!"                       , "" , $extra_query );
			$extra_query = preg_replace( "!s=(\w){32}!"                            , "" , $extra_query );
		}

		$frames = "<html>
		   			 <head><title>Invision Power Board Administration Center</title></head>
					   <frameset cols='185, *' frameborder='no' border='0' framespacing='0'>
					   	<frame name='menu' noresize scrolling='auto' src='{$this->base_url}&act=menu'>
					   	<frame name='body' noresize scrolling='auto' src='{$this->base_url}&{$extra_query}'>
					   </frameset>
				   </html>";

		return $frames;

	}

	function skin_jump_menu_wrap()
	{
		global $ibforums, $DB;

		return "<br /><div align='center' style='width:250px;margin-left:auto;margin-right:auto;'>
			   <div style='padding:3px 0px 3px 0px;border:1px solid #AAA;'>
			   <div class='tablepad' align='center'>".$ibforums->admin->skin_jump_menu()."</div></div></div>";

	}

}






?>