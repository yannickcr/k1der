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
|   > IPChat functions
|   > Script written by Matt Mecham
|   > Date started: 29th September 2003
|
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}


class chat_functions
{

	var $class  = "";
	var $server = "";
	var $html   = "";

	function chat_functions()
	{
		global $DB, $std, $ibforums;

		$this->server = str_replace( 'http://', '', $ibforums->vars['chat_server_addr'] );
	}

	//-----------------------------------------
	// register_class($class)
	//
	// Register a $this-> with this class
	//
	//-----------------------------------------

	function register_class(&$class)
	{
		$this->class = $class;
	}

	//-----------------------------------------
	// Print online list
	//
	//-----------------------------------------

	function get_online_list()
	{
		global $DB, $std, $ibforums;

		if ( ! $ibforums->vars['chat_who_on'] )
		{
			return;
		}

		//-----------------------------------------
		// Get details from the DB
		//-----------------------------------------

		$DB->query("SELECT * FROM ibf_cache_store WHERE cs_key='chatstat'");

		$row = $DB->fetch_row();

		list( $hits, $time ) = explode( '&', $row['cs_extra'] );

		//-----------------------------------------
		// Do we need to update?
		//-----------------------------------------

		$final = "";
		$time_is_running_out = time();
		$member_ids = array();
		$new        = array();

		if ( $time < time() - ( $ibforums->vars['chat_who_save'] * 60 ) )
		{
			$server_url = 'http://'.$this->server.'/ipc_who.pl?id='.$ibforums->vars['chat_account_no'].'&pw='.$ibforums->vars['chat_pass_md5'];

			if ( $data = @file( $server_url ) )
			{
				if ( count($data) > 0 )
				{
					$hits_left = array_shift($data);
				}

				foreach( $data as $t )
				{
					$t = strtolower(trim($t));
					$t = str_replace( '_', ' ', $t );
					$t = str_replace( '"', '&quot;', $t );

					$new[] = $t;
				}

				$name_string = implode( '","', $new );

				if ( count($new) > 0 )
				{
					$DB->query("SELECT m.id, m.name, m.mgroup FROM ibf_members m
								WHERE lower(name) IN (\"".$name_string."\") ORDER BY m.name");

					while ( $m = $DB->fetch_row() )
					{
						$g = $ibforums->cache['group_cache'][ $m['mgroup'] ];
						$member_ids[] = "<a href=\"{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?showuser={$m['id']}\">{$g['prefix']}{$m['name']}{$g['suffix']}</a>";
					}

					$final = implode( ",\n", $member_ids );

					$final .= '|&|'.intval(count($member_ids));
				}

				$DB->query("UPDATE ibf_cache_store SET cs_value='".addslashes($final)."', cs_extra='{$hits_left}&{$time_is_running_out}' WHERE cs_key='chatstat'");

				$row['cs_value'] = $final;
			}
		}

		//-----------------------------------------
		// Any members to show?
		//-----------------------------------------

		$ibforums->vars['chat_height'] += $ibforums->vars['chat_poppad'] ? $ibforums->vars['chat_poppad'] : 50;
		$ibforums->vars['chat_width']  += $ibforums->vars['chat_poppad'] ? $ibforums->vars['chat_poppad'] : 50;

		$chat_link = ( $ibforums->vars['chat_display'] == 'self' )
				   ? $this->class->html->whoschatting_inline_link()
				   : $this->class->html->whoschatting_popup_link();

		list ($names, $count) = explode( '|&|', $row['cs_value'] );

		if ( $count > 0 )
		{
			$txt = sprintf( $ibforums->lang['whoschatting_delay'], $ibforums->vars['chat_who_save'] );
			$this->html = $this->class->html->whoschatting_show( intval($count), stripslashes($names), $chat_link, $txt );
		}
		else
		{
			if ( ! $ibforums->vars['chat_hide_whoschatting'] )
			{
				$this->html = $this->class->html->whoschatting_empty($chat_link);
			}
		}

		return $this->html;

	}





}



?>