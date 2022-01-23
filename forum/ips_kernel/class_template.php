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
|   > Template Engine module (KERNEL)
|   > Module written by Matt Mecham
|   > Date started: 5th January 2004
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
|   New template module to build, rebuild and generate caches of templates
|   which include the new IPB HTML Logic system.
|   Example:
|
|    <if="ibf.vars['threaded_per_page'] == 10">
|      html here
|    </if>
|    <else if="ibf.vars['threaded_per_page'] >= 100 or some_var == 'this'">
|      html here
|    </if>
|    <else if="show['this'] > 100">
|      html here
|    </if>
|    <else>
|     html here
|    </else>
|
+--------------------------------------------------------------------------
*/

class class_template
{
	var $root_path   = './';
	var $cache_dir   = 'skin_cache';
	var $cache_id    = '1';
	var $database_id = '1';

	var $cache_path  = '';

	function class_template()
	{
		$this->cache_path = $this->root_path . $this->cache_dir . '/cacheid_' . $this->cache_id;
	}

	//===================================================
	// Convert HTML to PHP cache file
	//===================================================

	function convert_html_to_php($func_name, $func_data, $func_html)
	{
		//-------------------------------
		// Make sure we have ="" on each
		// func data
		//-------------------------------

		$func_data = preg_replace( "#".'\$'."(\w+)(,|$)#i", "\$\\1=\"\"\\2", str_replace( " ", "", $func_data ) );

		$top    = "//===========================================================================\n".
			      "// {$func_name}\n".
			      "//===========================================================================\n";

		$start  = "function {$func_name}($func_data) {\nglobal \$ibforums;\n\$IPBHTML = \"\";\n//--starthtml--//\n";
		$middle = $this->build_section_html_to_php($func_html);
		$end    = "\n//--endhtml--//\nreturn \$IPBHTML;\n}\n";

		return $top.$start.$middle.$end;
	}

	//===================================================
	// Alias: Convert PHP to HTML
	//===================================================

	function convert_php_to_html($php)
	{
		return $this->_convert_php_to_html($php);
	}

	//===================================================
	// Build Section: PHP to HTML
	// - Updates HTML database with PHP cache file
	//===================================================

	function build_section_php_to_html($php="")
	{

	}

	//===================================================
	// Build Section: HTML to PHP
	// - Makes PHP cache of raw HTML
	//===================================================

	function build_section_html_to_php($html="")
	{

		if ( preg_match( "#<if=[\"'].+?[\"']>#si", $html ) )
		{
			//----------------------------------------
			// Does it have logic sections?
			//----------------------------------------

			$html = $this->_convert_html_to_php($html);

			//----------------------------------------
			// Non-logic from top?
			//----------------------------------------

			$html = preg_replace( "#^(.+?)(//startif)#ise", "\$this->_wrap_in_php('\\1', '\\2');", $html );

			//----------------------------------------
			// Non-logic from between?
			//----------------------------------------

			$html = preg_replace( "#(}//endif|}//endelse)(.+?)(//startif|else)#ise", "\$this->_wrap_in_php('\\2', '\\3', '\\1');", $html );

			//$html = str_replace( "}//endif//startif", "}//endif\n//startif", $html );

			//----------------------------------------
			// Non-logic from after? ENDELSE
			//----------------------------------------

			if ( preg_match( "#^(.*)(}//endelse)(.+?)$#is", $html, $match ) )
			{
				if ( $match[1] and $match[2] and ( ! strstr( $match[3], "<<<EOF\n" ) ) )
				{
					$html = preg_replace( "#^(.*}//endelse)(.+?)$#ise", "\$this->_wrap_in_php('\\2', '', '\\1');", $html );
				}
			}

			//----------------------------------------
			// Non-logic from after? ENDIF
			//----------------------------------------

			if ( preg_match( "#^(.*)(}//endif)(.+?)$#is", $html, $match ) )
			{
				if ( $match[1] and $match[2] and ( ! strstr( $match[3], "<<<EOF\n" ) ) )
				{
					$html = preg_replace( "#^(.*}//endif)(.+?)$#ise", "\$this->_wrap_in_php('\\2', '', '\\1');", $html );
				}
			}
		}
		else
		{
			$html = $this->_wrap_in_php( $html );
		}

		//----------------------------------------
		// Unconvert special tags
		//----------------------------------------

		$html = $this->unconvert_tags($html);

		return $html;
	}

	//===================================================
	// Convert special tags into HTML safe versions
	//===================================================

	function convert_tags($t="")
	{
		$t = preg_replace( "/{?\\\$ibforums->base_url}?/"             , "{ipb.script_url}" , $t );
		$t = preg_replace( "/{?\\\$ibforums->session_id}?/"           , "{ipb.session_id}" , $t );
		$t = preg_replace( "#\\\$ibforums->(member|vars|skin|lang|input)#i" , "ipb.\\1"          , $t );

		//----------------------------------------
		// Make some tags safe..
		//----------------------------------------

		$t = preg_replace( "/\{ipb\.vars\[(['\"])?(sql_driver|sql_host|sql_database|sql_pass|sql_user|sql_port|sql_tbl_prefix|smtp_host|smtp_port|smtp_user|smtp_pass|html_dir|base_dir|upload_dir)(['\"])?\]\}/", "" , $t );

		return $t;
	}

	//===================================================
	// Uncovert them back again
	//===================================================

	function unconvert_tags($t="")
	{
		//----------------------------------------
		// Make some tags safe..
		//----------------------------------------

		$t = preg_replace( "/\{ipb\.vars\[(['\"])?(sql_driver|sql_host|sql_database|sql_pass|sql_user|sql_port|sql_tbl_prefix|smtp_host|smtp_port|smtp_user|smtp_pass|html_dir|base_dir|upload_dir)(['\"])?\]\}/", "" , $t );

		$t = preg_replace( "/{ipb\.script_url}/i"           , '{$ibforums->base_url}'  , $t);
		$t = preg_replace( "/{ipb\.session_id}/i"           , '{$ibforums->session_id}', $t);
		$t = preg_replace( "#ipb\.(member|vars|skin|lang|input)#i", '$ibforums->\\1'         , $t );

		return $t;
	}

	//===================================================
	// Wrap HTML into PHP
	//===================================================

	function _wrap_in_php( $html, $after="", $before="" )
	{
		$html = $this->_trim_newlines($this->_trim_slashes($html));

		$before = $this->_trim_slashes($before);
		$after  = $this->_trim_slashes($after);

		if ( ! strstr( $before, "\n" ) )
		{
			$before .= "\n";
		}

		if ( ! trim($html) )
		{
			return $before.$html.$after;
		}

		return $before."\n\$IPBHTML .= <<<EOF\n$html\nEOF;\n".$after;
	}


	//===================================================
	// Convert: HTML Logic to PHP logic
	//===================================================

	function _convert_html_to_php($html)
	{
		$html = $this->_trim_slashes($html);
		$html = preg_replace( "#(?:\s+?)?<if=[\"'](.+?)[\"']>(.+?)</if>#ise", "\$this->_statement_if('\\1', '\\2')", $html );
		$html = preg_replace( "#(?:\s+?)?<else if=[\"'](.+?)[\"']>(.+?)</if>#ise", "\$this->_statement_elseif('\\1', '\\2')", $html );
		$html = preg_replace( "#(?:\s+?)?<else>(.+?)</else>#ise", "\$this->_statement_else('\\1')", $html );

		return $html;
	}

	//===================================================
	// Convert: PHP logic to HTML logic
	//===================================================

	function _convert_php_to_html($php)
	{
		$php = preg_replace( "#else if\s+?\((.+?)\)\s+?{(.+?)}//endif(\n)?#ise", "\$this->_reverse_if('\\1', '\\2', 'else if')", $php );
		$php = preg_replace( "#//startif\nif\s+?\((.+?)\)\s+?{(.+?)}//endif(\n)?#ise", "\$this->_reverse_if('\\1', '\\2', 'if')", $php );
		$php = preg_replace( "#else\s+?{(.+?)}//endelse(\n)?#ise", "\$this->_reverse_else( '\\1' )", $php );

		//----------------------------------------
		// Parse raw sections
		//----------------------------------------

		$php = $this->_reverse_ipbhtml($php);

		//----------------------------------------
		// Convert ipb-htmllogic tags
		//----------------------------------------

		//$php = preg_replace( "#//start-htmllogic#i" , "<ipb-htmllogic>", $php );
		//$php = preg_replace( "#//end-htmllogic#i", "</ipb-htmllogic>"  , $php );

		//----------------------------------------
		// Remove start ifs
		//----------------------------------------

		$php = str_replace( "//startif\n", "\n", $php );

		//----------------------------------------
		// Remove extra spaces
		//----------------------------------------

		$php = preg_replace( "#(</if>|</else>)\s+?(<if|<else)#is", "\\1\n\\2", $php );

		//----------------------------------------
		// Make safe special $Ibforums vars
		//----------------------------------------

		$php = $this->convert_tags($php);

		return $php;
	}

	//===================================================
	// Reverse: PHP IF to HTML IF
	//===================================================

	function _reverse_if( $code, $php, $start='if' )
	{
		$code = $this->_trim_slashes(trim($code));
		$code = preg_replace( "/(^|and|or)(\s+)(.+?)(\s|$)/ise", "\$this->_reverse_prep_left('\\3', '\\1', '\\2', '\\4')", ' '.$code );

		$php = $this->_reverse_ipbhtml($php);

		return "<".$start."=\"".trim($code)."\">\n".$php."\n</if>\n";

	}

	//===================================================
	// Reverse: PHP else to HTML else
	//===================================================

	function _reverse_else( $php )
	{
		$php = $this->_trim_slashes(trim($php));

		$php = $this->_reverse_ipbhtml($php);

		return "<else>\n".$php."\n</else>\n";

	}

	//===================================================
	// Reverse: $IPBHTML to normal $HTML
	//===================================================

	function _reverse_ipbhtml( $code )
	{
		$code = $this->_trim_slashes($code);

		$code = preg_replace("/".'\$'."IPBHTML\s+?\.?=\s+?<<<EOF(.+?)EOF;\s?/si", "\\1", $code );

		$code = trim($code);
		$code = $this->_trim_newlines($code);

		return $code;
	}

	//===================================================
	// Reverse PHP IF code to HTML code
	//===================================================

	function _reverse_prep_left($left, $andor="", $fs="", $ls="")
	{
		$left = trim($this->_trim_slashes($left));

		if ( preg_match( "/".'\$'."ibforums->/", $left ) )
		{
			$left = preg_replace( "/".'\$'."ibforums->(.+?)$/", 'ipb.'."\\1", $left );
		}
		else
		{
			$left = str_replace( '$', '', $left );
		}

		return $andor.$fs.$left.$ls;
	}

	//===================================================
	// Statement: Return PHP 'IF' statement
	//===================================================

	function _statement_if( $code, $html )
	{
		$html = $this->_func_prep_html($html);
		$code = $this->_func_prep_if($code);

		return "\n//startif\nif ( $code )\n{\n\$IPBHTML .= <<<EOF\n$html\nEOF;\n}//endif\n";
	}

	//===================================================
	// Statement: Return PHP 'ELSE IF' statement
	//===================================================

	function _statement_elseif( $code, $html )
	{
		$html = $this->_func_prep_html($html);
		$code = $this->_func_prep_if($code);

		return "\nelse if ( $code )\n{\n\$IPBHTML .= <<<EOF\n$html\nEOF;\n}//endif\n";
	}

	//===================================================
	// Statement: Return PHP 'ELSE' statement
	//===================================================

	function _statement_else( $html )
	{
		$html = $this->_func_prep_html($html);

		return "\nelse\n{\n\$IPBHTML .= <<<EOF\n$html\nEOF;\n}//endelse\n";
	}


	//===================================================
	// Strip leading newlines, etc
	//===================================================

	function _func_prep_html($html)
	{
		$html = trim($this->_trim_slashes($html));

		//$html = preg_replace( '/"/', '\\"', $html );

		return $html;
	}

	//===================================================
	// Sort out left bit of comparison
	//===================================================

	function _func_prep_left($left, $andor="", $fs="", $ls="")
	{
		$left = trim($this->_trim_slashes($left));

		if ( preg_match( "/^ipb\./", $left ) )
		{
			$left = preg_replace( "/^ipb\.(.+?)$/", '$ibforums->'."\\1", $left );
		}
		else
		{
			$left = '$'.$left;
		}

		return $andor.$fs.$left.$ls;
	}

	//===================================================
	// Statement: Prep AND OR, etc
	//===================================================

	function _func_prep_if( $code )
	{
		$code = $this->_trim_slashes($code);

		$code = preg_replace( "/(^|and|or)(\s+)(.+?)(\s|$)/ise", "\$this->_func_prep_left('\\3', '\\1', '\\2', '\\4')", ' '.$code );

		return trim($code);
	}

	//===================================================
	// Remove leading and trailing newlines
	//===================================================

	function _trim_newlines($code)
	{
		$code = preg_replace("/^\n{1,}/s", "", $code );
		$code = preg_replace("/\n{1,}$/s", "", $code );
		return $code;
	}

	//===================================================
	// Remove preg_replace/e slashes
	//===================================================

	function _trim_slashes($code)
	{
		$code = str_replace( '\"' , '"', $code );
		$code = str_replace( "\\'", "'", $code );
		return $code;
	}


}





?>