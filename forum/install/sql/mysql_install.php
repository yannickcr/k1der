<?php

/*
+--------------------------------------------------------------------------
|   INVISION POWER BOARD INSTALLER v2.0
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
+--------------------------------------------------------------------------
|
|   > Script written by Matthew Mecham
|   > Date started: 12th August 2004
|   > MYSQL EXTRA CONFIG / INSTALL FILE
+--------------------------------------------------------------------------
*/

class install_extra
{
	var $errors     = array();
	var $info_extra = array();
	
	function install_extra()
	{
	
	}
	
	/*-------------------------------------------------------------------------*/
	// WHEN SHOWING THE FORM....
	/*-------------------------------------------------------------------------*/
	
	function install_form_extra()
	{
		/*$extra = "<tr>
					<td class='pformleftw'><b>Test Var 1 (Test Var)</b></td>
					<td class='pformright'><input type='text' id='textinput' name='some_test_var' value=''></td>
				  </tr>
				  <tr>
					<td class='pformleftw'><b>Test Var 2 (Other Test)</b></td>
					<td class='pformright'><input type='text' id='textinput' name='some_other_test' value=''></td>
				  </tr>";
	
		return $extra;*/
	
	}
	
	/*-------------------------------------------------------------------------*/
	// WHEN SAVING TO CONF GLOBAL
	// Return errors in $errors[]
	/*-------------------------------------------------------------------------*/
	
	function install_form_process()
	{
		//-----------------------------------------
		// When processed, return all vars to save
		// in conf_global in the array $this->info_extra
		// This will also be saved into $INFO[] for
		// the installer
		//-----------------------------------------
		
		/*if ( ! $_REQUEST['some_test_var'] )
		{
			$this->errors[] = 'You must complete the required SQL section!';
			return;
		}
		
		$this->info_extra['some_test_var']   = $_REQUEST['some_test_var'];
		$this->info_extra['some_other_test'] = $_REQUEST['some_other_test'];*/
	}

}

?>