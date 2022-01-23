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

		$this->server = str_replace( 'http://', '', $ibforums->vars['chat04_whodat_server_addr'] );
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

		if ( ! $ibforums->vars['chat04_who_on'] )
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

		if ( $time < time() - ( $ibforums->vars['chat04_who_save'] * 60 ) )
		//if ( 1 == 1)
		{
			//-----------------------------------------
			// Get session based info
			//-----------------------------------------

			$timecutoff = time() - ( 30 * 60 );

			$DB->query("SELECT s.member_id as id, s.member_name as name, g.g_id, g.prefix, g.suffix, s.location FROM ibf_sessions s
							 LEFT JOIN ibf_groups g ON (s.member_group=g.g_id)
							WHERE s.member_id > 0 AND s.location LIKE 'chat,%' AND s.running_time > $timecutoff ORDER BY s.member_name");

			while ( $m = $DB->fetch_row() )
			{
				$member_ids[] = "<a href=\"{$ibforums->base_url}showuser={$m['id']}\">{$m['prefix']}{$m['name']}{$m['suffix']}</a>";
			}

			$final = implode( ",\n", $member_ids );

			$final .= '|&|'.intval(count($member_ids));


			if ( ! $hits_left )
			{
				$hits_left = 100000;
			}

			$DB->query("UPDATE ibf_cache_store SET cs_value='".$DB->add_slashes($final)."', cs_extra='{$hits_left}&{$time_is_running_out}' WHERE cs_key='chatstat'");

			$row['cs_value'] = $final;
		}

		//-----------------------------------------
		// Any members to show?
		//-----------------------------------------

		$ibforums->vars['chat04_height'] += $ibforums->vars['chat04_poppad'] ? $ibforums->vars['chat04_poppad'] : 50;
		$ibforums->vars['chat04_width']  += $ibforums->vars['chat04_poppad'] ? $ibforums->vars['chat04_poppad'] : 50;

		$chat_link = ( $ibforums->vars['chat04_display'] == 'self' )
				   ? $this->class->html->whoschatting_inline_link()
				   : $this->class->html->whoschatting_popup_link();

		list ($names, $count) = explode( '|&|', $row['cs_value'] );

		if ( $count > 0 )
		{
			$txt = sprintf( $ibforums->lang['whoschatting_delay'], $ibforums->vars['chat04_who_save'] );
			$this->html = $this->class->html->whoschatting_show( intval($count), stripslashes($names), $chat_link, $txt );
		}
		else
		{
			if ( ! $ibforums->vars['chat04_hide_whoschatting'] )
			{
				$this->html = $this->class->html->whoschatting_empty($chat_link);
			}
		}

		return $this->html;

	}





}



?>