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
|   > Admin Template functions library
|   > Script written by Matt Mecham
|   > Date started: 19th November 2003
|
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


class admin_template_functions
{

	var $menu_fontchange  = "";
	var $menu_sizechange  = "";
	var $menu_backchange  = "";
	var $menu_fontcolor   = "";
	var $menu_widthchange = "";
	var $menu_highchange  = "";
	var $default_css      = "";

	//-----------------------------------------
	// Constructor
	//-----------------------------------------

	function admin_template_functions()
	{
		$this->default_css = "font-family:Verdana;font-size:11pt;color:black;background-color:white;border:3px outset #555";
	}

	//-----------------------------------------
	// Build generic editor
	//-----------------------------------------

	function build_generic_editor_area( $data )
	{
		global $ibforums, $std, $DB;

		$return = "";

		$return .= $this->html_build_editor_top();

		$return .= "
					<script language='javascript'>
					<!--
      				var template_bit_ids = 'txt{$data['textareaname']}';
					//-->
					</script>
					<div class='tableborder'>
		            <div class='titlemedium'>
				    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
				    <tr>
				     <td width='1%' align='left' valign='middle'>
				         <input type='button' value='Float'  class='realdarkbutton' title='Open this template editor in a large window' onclick=\"pop_win('act={$data['act']}&code=floateditor&id={$data['textareaname']}', 'Float', 800, 400)\">&nbsp;
				     </td>
				     <td width='95%' align='left' valign='middle'>&nbsp;<b>{$data['title']}</b></td>
				     <td width='5% align='right'  valign='middle' nowrap='nowrap' ><!--TOP.RIGHT--></td>
				   </tr>
				   <!--TR.ROW-->
				   </table>
				   </div>";

		$return .= "<div align='center' style='padding:2px;'>".$ibforums->adskin->form_textarea("txt{$data['textareaname']}", $data['textareainput'], $ibforums->vars['tx'], $ibforums->vars['ty'], 'none', "txt{$data['textareaname']}", $this->default_css )."</div>\n";

		$return .= $this->html_build_editor_bottom();

		return $return;

	}

	//-----------------------------------------
	// Build editor text area table
	//-----------------------------------------

	function build_editor_area( $html, $template=array(), $img="" )
	{
		global $ibforums, $std, $DB;

		$return = "";

		$template['func_data'] = str_replace( "'", '&#039;', $template['func_data'] );

		$spiffy_diffy = "<div align='center' style='position:absolute;width:99%;display:none;text-align:center' id='dv_{$template['suid']}'>
						 <table cellspacing='0' width='500' align='center' cellpadding='6' style='background:#EEE;border:2px outset #555;'>
						 <tr>
						  <td align='center' valign='top'>
						   <b>Advanced: Template Bit Incoming Variables</b><br />Leave alone if unsure. Separate many with a comma.
						   <br /><input class='textinput' name='funcdata_{$template['suid']}' value='{$template['func_data']}' type='text' size='50' />
						   <br /><br />
						   <input type='button' class='realdarkbutton' value='Save and Close' onclick=\"togglediv('dv_{$template['suid']}');\" />
						  </td>
						 </tr>
						 </table>
						</div>";

		$return .= "<div class='tableborder'>
		            <div class='titlemedium'>
				    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
				    <tr>
				     <td width='1%' align='left' valign='middle'>
				         <input type='button' value='Float'  class='realdarkbutton' title='Open this template editor in a large window' onclick=\"pop_win('act=templ&code=floateditor&id={$template['suid']}', 'Float{$template['suid']}', 800, 400)\">&nbsp;
				     </td>
				     <td width='95%' align='left' valign='middle'>&nbsp;{$img}<b>{$template['easy_name']}</b></td>
				     <td width='5% align='right' nowrap='nowrap' >
					  <input type='button' value='Macro Look-up'  class='realbutton' title='View a macro definition' onClick='pop_win(\"act=rtempl&code=macro_one&suid={$template['suid']}\", \"MacroWindow\", 400, 200)'>
					  <input type='button' value='Compare'  class='realbutton' title='Compare the edited version to the original' onClick='pop_win(\"act=rtempl&code=compare&suid={$template['suid']}\", \"CompareWindow\", 500,400)'>
					  <input type='button' value='Restore'  class='realbutton' title='Restore the original, unedited template bit' onClick='restore(\"{$template['suid']}\",\"{$ibforums->input['expand']}\")'>
					  <input type='button' value='View Original'  class='realbutton' title='View the HTML for the unedited template bit' onClick='pop_win(\"act=rtempl&code=preview&suid={$template['suid']}&type=html\", \"OriginalPreview\", 400,400)'>
					  <input type='button' value='Show Data Variables...' class='realdarkbutton' title='View and edit the incoming data variables for this template bit' onclick=\"toggleview('dv_{$template['suid']}')\"/>
					  &nbsp;
					</td>
				   </tr>
				   </table>
				   $spiffy_diffy
				   </div>";

		$return .= "<div align='center' style='padding:2px;'>".$ibforums->adskin->form_textarea("txt{$template['suid']}", $html, $ibforums->vars['tx'], $ibforums->vars['ty'], 'none', "t{$template['suid']}", $this->default_css )."</div>\n";

		$return .= "</div>";

		return $return;

	}

	//-----------------------------------------
	// Build JS for floated window
	//-----------------------------------------

	function build_editor_area_floated($no_buttons=0)
	{
		global $ibforums, $std, $DB;

		$return = "<form name='theform'>";

		$return .= $this->html_build_editor_top();

		$return .= "<div class='tableborder'>
		            <div class='titlemedium' align='right'>";

		if ( $no_buttons == 0 )
		{
			$return .= "
				      <input type='button' value='Search'  class='realbutton' title='Search the templates for a string' onClick='pop_win(\"act=rtempl&code=search&suid={$template['suid']}&type=html\", \"Search\", 500,400)'>
					  <input type='button' value='Macro Look-up'  class='realbutton' title='View a macro definition' onClick='pop_win(\"code=macro_one&suid={$template['suid']}\", \"MacroWindow\", 400, 200)'>
					  <input type='button' value='Compare'  class='realbutton' title='Compare the edited version to the original' onClick='pop_win(\"act=rtempl&code=compare&suid={$template['suid']}\", \"CompareWindow\", 500,400)'>
					  <input type='button' value='Restore'  class='realbutton' title='Restore the original, unedited template bit' onClick='restore(\"{$template['suid']}\",\"{$ibforums->input['expand']}\")'>
					  <input type='button' value='View Original' class='realbutton' title='View the HTML for the unedited template bit' onClick='pop_win(\"act=rtempl&code=preview&suid={$template['suid']}&type=html\", \"OriginalPreview\", 400,400)'>
				   ";
		}

		$return .= "</div>";

		$return .= "<div align='center' style='padding:2px;'>".$ibforums->adskin->form_textarea("templatebit", $html, $ibforums->vars['tx'], $ibforums->vars['ty'], 'none', "templatebit", $this->default_css )."</div>\n";

		$return .= "</div>";

		$return .= "<script type='text/javascript'>
				   		var template_id = '{$ibforums->input['id']}';
				   		var template_bit  = eval(\"opener.document.theform.txt\"+template_id+\".value\");
				   		document.theform.templatebit.value = template_bit;
				   		var template_bit_ids = 'templatebit';

				   		function saveandclose()
				   		{
				   			eval(\"opener.document.theform.txt\"+template_id+\".value = document.theform.templatebit.value\");
				   			window.close();
				   		}
				   </script>
				   ";

		$return .= $this->html_build_editor_bottom();

		$return .= "<br /><div class='tableborder'><div class='catrow2' align='center' style='padding:4px;'><input type='button' onclick='saveandclose()' value='Copy back to original textarea and close window' class='realdarkbutton' /></div></div></form>";

		$ibforums->html = $return;

		$ibforums->admin->print_popup();

	}

	//-----------------------------------------
	// Build editor preferences menus
	//-----------------------------------------

	function build_editor_pref_menus()
	{
		global $ibforums, $std, $DB;

		$this->menu_fontchange =  "<select name='fontchange' class='smalldropdown'>
								   <option value='monaco'>Monaco</option>
								   <option value='courier'>Courier</option>
								   <option value='verdana'>Verdana</option>
								   <option value='arial'>Arial</option>
								   </select>";

		$this->menu_sizechange =  "<select name='sizechange' class='smalldropdown'>
								   <option value='8pt'>8pt</option>
								   <option value='9pt'>9pt</option>
								   <option value='10pt'>10pt</option>
								   <option value='11pt'>11pt</option>
								   <option value='12pt'>12pt</option>
								   </select>";

		$this->menu_backchange =  "<select name='backchange' class='smalldropdown'>
								   <option value='black'>Black</option>
								   <option value='white'>White</option>
								   <option value='#EEEEEE'>Light Gray</option>
								   <option value='gray'>Gray</option>
								   </select>";

		$this->menu_fontcolor  =  "<select name='fontcolor' class='smalldropdown'>
								   <option value='black'>Black</option>
								   <option value='white'>White</option>
								   <option value='blue'>Blue</option>
								   <option value='lightgreen'>Light Green</option>
								   <option value='green'>Green</option>
								   <option value='darkgreen'>Dark Green</option>
								   <option value='gray'>Gray</option>
								   </select>";

		$this->menu_widthchange = "<select name='widthchange' class='smalldropdown'>
								   <option value='100%'>100%</option>
								   <option value='90%'>90%</option>
								   <option value='80%'>80%</option>
								   <option value='70%'>70%</option>
								   <option value='60%'>60%</option>
								   <option value='50%'>50%</option>
								   </select>";

		$this->menu_highchange  = "<select name='highchange' class='smalldropdown'>
								   <option value='50px'>50px</option>
								   <option value='100px'>100px</option>
								   <option value='200px'>200px</option>
								   <option value='300px'>300px</option>
								   <option value='400px'>400px</option>
								   <option value='500px'>500px</option>
								   <option value='600px'>600px</option>
								   <option value='700px'>700px</option>
								   <option value='800px'>800px</option>
								   <option value='900px'>900px</option>
								   <option value='1000px'>1000px</option>
								   </select>";

		if ( $cookie = $std->my_getcookie( 'acpeditorprefs' ) )
		{
			list( $font, $size, $bg, $fc, $width, $height ) = explode( "," ,$cookie );

			$this->default_css  = "font-family:$font;font-size:$size;color:$fc;background-color:$bg;width:$width;height:$height;border:3px outset #555";

			$this->menu_fontchange   = preg_replace( "/(option value='".preg_quote($font)."')/"   , "\\1 selected='selected'", $this->menu_fontchange  );
			$this->menu_sizechange   = preg_replace( "/(option value='".preg_quote($size)."')/"   , "\\1 selected='selected'", $this->menu_sizechange  );
			$this->menu_backchange   = preg_replace( "/(option value='".preg_quote($bg)."')/"     , "\\1 selected='selected'", $this->menu_backchange  );
			$this->menu_fontcolor    = preg_replace( "/(option value='".preg_quote($fc)."')/"     , "\\1 selected='selected'", $this->menu_fontcolor   );
			$this->menu_widthchange  = preg_replace( "/(option value='".preg_quote($width)."')/"  , "\\1 selected='selected'", $this->menu_widthchange );
			$this->menu_highchange   = preg_replace( "/(option value='".preg_quote($height)."')/" , "\\1 selected='selected'", $this->menu_highchange  );
		}
	}


	function html_build_editor_top()
	{
		global $ibforums;

		$this->build_editor_pref_menus();

		return "
				<script type='text/javascript'>
				function toggleprefsmenu()
				{
					closedimage = '{$ibforums->skin_url}/editor_closed.gif';
					openimage   = '{$ibforums->skin_url}/editor_open.gif';
					id          = 'prefsbox';

					if ( itm = my_getbyid(id) )
					{
						if (itm.style.display == \"none\")
						{
							my_show_div(itm);
							document.images.edimage.src = openimage;
						}
						else
						{
							my_hide_div(itm);
							document.images.edimage.src = closedimage;
						}
					}
				}
				</script>
				<div class='tableborder'>
				<div class='maintitle' align='left'><a href='#' onclick=\"toggleprefsmenu()\"><img id='edimage' src='{$ibforums->skin_url}/editor_closed.gif' border='0' alt='Editor Preferences' /></a>
				<div style='border-left:1px solid #B1B1B1;border-right:1px solid #B1B1B1;border-bottom:1px solid #B1B1B1;width:181px;background:#FFF;display:none;position:absolute;' id='prefsbox'>
				  <table cellpadding='4' cellspacing='0' width='100%' border='0'>
				  <tr>
				   <td nowrap='nowrap'>Font Family</td>
				   <td width='100%'>{$this->menu_fontchange}</td>
				  </tr>
				  <tr>
				   <td nowrap='nowrap'>Font Size</td>
				   <td width='100%'>{$this->menu_sizechange}</td>
				  </tr>
				  <tr>
				   <td nowrap='nowrap'>Font Color</td>
				   <td width='100%'>{$this->menu_fontcolor}</td>
				  </tr>
				  <tr>
				   <td nowrap='nowrap'>Background</td>
				   <td width='100%'>{$this->menu_backchange}</td>
				  </tr>
				  <tr>
				   <td nowrap='nowrap'>Area Width</td>
				   <td width='100%'>{$this->menu_widthchange}</td>
				  </tr>
				  <tr>
				   <td nowrap='nowrap'>Area Height</td>
				   <td width='100%'>{$this->menu_highchange}</td>
				  </tr>
				  <tr>
				   <td colspan='2' align='center'>
				   <input type='button' value='Change' class='realbutton' onclick=\"toggleprefsmenu(); changefont();\" />
				   <input type='button' value=' X ' class='realdarkbutton' onclick=\"toggleprefsmenu();\" />
				   </td>
				  </tr>
				  </table>
				</div>
				</div>
				<div class='tablepad'><!--BEFORETEXTAREA-->";
	}

	function html_build_editor_bottom()
	{
		return "<!--IPB.EDITORBOTTOM--></div>\n</div></div>";
	}


}





?>