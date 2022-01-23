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
|   > Converge methods (KERNEL)
|   > Module written by Matt Mecham
|   > Date started: 15th March 2004
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/


class class_converge
{
	var $current_db = "";
	var $member     = array();

	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function class_converge(&$DB)
	{
		$this->current_db = $DB;

		// Temp code!
		$this->converge_db = $DB;
	}

	/*-------------------------------------------------------------------------*/
	// Test for converge row
	/*-------------------------------------------------------------------------*/

	function converge_check_for_member_by_email( $email )
	{
		$test = $this->converge_db->simple_exec_query( array( 'select' => 'converge_id', 'from' => 'members_converge', 'where' => "converge_email='$email'" ) );

		if ( $test['converge_id'] )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/*-------------------------------------------------------------------------*/
	// Update converge row (password)
	/*-------------------------------------------------------------------------*/

	function converge_update_password( $new_md5_pass, $email )
	{
		if ( ! $email or ! $new_md5_pass )
		{
			return FALSE;
		}

		if ( $email != $this->member['converge_email'] )
		{
			$temp_member = $this->converge_db->simple_exec_query( array( 'select' => '*', 'from' => 'members_converge', 'where' => "converge_email='$email'" ) );
		}
		else
		{
			$temp_member = $this->member;
		}

		$new_pass = md5( md5( $temp_member['converge_pass_salt'] ) . $new_md5_pass );

		$this->converge_db->do_update( 'members_converge', array( 'converge_pass_hash' => $new_pass ), 'converge_id='.$temp_member['converge_id'] );
	}

	/*-------------------------------------------------------------------------*/
	// Update converge row
	/*-------------------------------------------------------------------------*/

	function converge_update_member($curr_email, $new_email)
	{
		if ( ! $curr_email or ! $new_email )
		{
			return FALSE;
		}

		if ( ! $this->member['converge_id'] )
		{
			$this->converge_load_member( $curr_email );

			if ( ! $this->member['converge_id'] )
			{
				return FALSE;
			}
		}

		$this->converge_db->do_update( 'members_converge', array( 'converge_email' => $new_email ), 'converge_id='.$this->member['converge_id'] );

		return TRUE;
	}

	/*-------------------------------------------------------------------------*/
	// Get converge row
	/*-------------------------------------------------------------------------*/

	function converge_load_member($email)
	{
		if ( ! $email )
		{
			$this->member = array();
		}
		else
		{
			$this->member = $this->converge_db->simple_exec_query( array( 'select' => '*', 'from' => 'members_converge', 'where' => "converge_email='$email'" ) );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Get converge row (based on ID)
	/*-------------------------------------------------------------------------*/

	function converge_load_member_by_id($id)
	{
		$id = intval($id);

		if ( ! $id )
		{
			$this->member = array();
		}
		else
		{
			$this->member = $this->converge_db->simple_exec_query( array( 'select' => '*', 'from' => 'members_converge', 'where' => "converge_id='$id'" ) );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Authenticate password
	/*-------------------------------------------------------------------------*/

	function converge_authenticate_member( $md5_once_password )
	{
		if ( ! $this->member['converge_pass_hash'] )
		{
			return FALSE;
		}

		if ( $this->member['converge_pass_hash'] == $this->generate_compiled_passhash( $this->member['converge_pass_salt'], $md5_once_password ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/*-------------------------------------------------------------------------*/
	// Generate password
	/*-------------------------------------------------------------------------*/

	function generate_compiled_passhash($salt, $md5_once_password)
	{
		return md5( md5( $salt ) . $md5_once_password );
	}

	/*-------------------------------------------------------------------------*/
	// Generate SALT
	/*-------------------------------------------------------------------------*/

	function generate_password_salt($len=5)
	{
		$salt = '';

		srand( (double)microtime() * 1000000 );

		for ( $i = 0; $i < $len; $i++ )
		{
			$num   = rand(33, 126);

			if ( $num == '92' )
			{
				$num = 93;
			}

			$salt .= chr( $num );
		}

		return $salt;
	}

	/*-------------------------------------------------------------------------*/
	// Generate auto log in key (MD5 hash of random 60 char string
	/*-------------------------------------------------------------------------*/

	function generate_auto_log_in_key($len=60)
	{
		$pass = $this->generate_password_salt( 60 );

		return md5($pass);
	}




}

?>