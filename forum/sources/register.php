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
|   > Registration functions
|   > Module written by Matt Mecham
|   > Date started: 16th February 2002
|
|	> Module Version Number: 1.0.0
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class register {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";
    var $email      = "";
    var $modules    = "";

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_register', $ibforums->lang_id );

    	$this->html = $std->load_template('skin_register');

    	$this->base_url        = $ibforums->base_url;
    	$this->base_url_nosess = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}";

    	//-----------------------------------------
    	// Get the emailer module
		//-----------------------------------------

		require ROOT_PATH."sources/classes/class_email.php";

		$this->email = new emailer();

		if ( USE_MODULES == 1 )
		{
			require ROOT_PATH."modules/ipb_member_sync.php";

			$this->modules = new ipb_member_sync();
		}

		//-----------------------------------------
		// Board offline?
		//-----------------------------------------

		if ($ibforums->vars['board_offline'] == 1)
		{
			if ($ibforums->member['g_access_offline'] != 1)
			{
				$ibforums->vars['no_reg'] = 1;
			}
		}

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['CODE'])
    	{
    		case '02':
    			$this->create_account();
    			break;

    		case '03':
    			$this->validate_user();
    			break;

    		case '05':
    			$this->show_manual_form();
    			break;

    		case '06':
    			$this->show_manual_form('lostpass');
    			break;

    		case 'lostpassform':
    			$this->show_manual_form('lostpass');
    			break;

    		case '07':
    			$this->show_manual_form('newemail');
    			break;

    		case '10':
    			$this->lost_password_start();
    			break;
    		case '11':
    			$this->lost_password_end();
    			break;

    		case '12':
    			$this->coppa_perms_form();
    			break;

    		case 'coppa_two':
    			$this->coppa_two();
    			break;

    		case 'image':
    			$this->show_image();
    			break;

    		case 'reval':
    			$this->revalidate_one();
    			break;

    		case 'reval2':
    			$this->revalidate_two();
    			break;

    		default:
    			if ($ibforums->vars['use_coppa'] == 1 and $ibforums->input['coppa_pass'] != 1)
    			{
    				$this->coppa_start();
    			}
    			else
    			{
    				$this->show_reg_form();
    			}
    			break;
    	}

    	// If we have any HTML to print, do so...

    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
 	}

 	/*-------------------------------------------------------------------------*/
	// Show "check revalidate form" er.. form. thing.
	// ------------------
	//
	/*-------------------------------------------------------------------------*/

	function revalidate_one($errors="")
	{
		global $ibforums, $DB;

		if ($errors != "")
    	{
    		$this->output .= $this->html->errors( $ibforums->lang[$errors] );
    	}

    	$name = $ibforums->member['id'] == "" ? '' : $ibforums->member['name'];

		$this->output     .= $this->html->show_revalidate_form($name);
		$this->page_title = $ibforums->lang['rv_title'];
		$this->nav        = array( $ibforums->lang['rv_title'] );
	}

	function revalidate_two()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Check in the DB for entered member name
		//-----------------------------------------

		if ( $_POST['username'] == "" )
		{
			$this->revalidate_one('err_no_username');
			return;
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "name='".$ibforums->input['username']."'" ) );
		$DB->simple_exec();

		if ( ! $member = $DB->fetch_row() )
		{
			$this->revalidate_one('err_no_username');
			return;
		}

		//-----------------------------------------
		// Check in the DB for any validations
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'validating', 'where' => "member_id=".intval($member['id']) ) );
		$DB->simple_exec();

		if ( ! $val = $DB->fetch_row() )
		{
			$this->revalidate_one('err_no_validations');
			return;
		}

		//-----------------------------------------
		// Which type is it then?
		//-----------------------------------------

		if ( $val['lost_pass'] == 1 )
		{
			$this->email->get_template("lost_pass");

			$this->email->build_message( array(
												'NAME'         => $member['name'],
												'THE_LINK'     => $this->base_url_nosess."?act=Reg&CODE=lostpassform&uid=".$member['id']."&aid=".$val['vid'],
												'MAN_LINK'     => $this->base_url_nosess."?act=Reg&CODE=lostpassform",
												'EMAIL'        => $member['email'],
												'ID'           => $member['id'],
												'CODE'         => $val['vid'],
												'IP_ADDRESS'   => $ibforums->input['IP_ADDRESS'],
											  )
										);

			$this->email->subject = $ibforums->lang['lp_subject'].' '.$ibforums->vars['board_name'];
			$this->email->to      = $member['email'];

			$this->email->send_mail();
		}
		else if ( $val['new_reg'] == 1 )
		{
			$this->email->get_template("reg_validate");

			$this->email->build_message( array(
												'THE_LINK'     => $this->base_url_nosess."?act=Reg&CODE=03&uid=".$member['id']."&aid=".$val['vid'],
												'NAME'         => $member['name'],
												'MAN_LINK'     => $this->base_url_nosess."?act=Reg&CODE=05",
												'EMAIL'        => $member['email'],
												'ID'           => $member['id'],
												'CODE'         => $val['vid'],
											  )
										);

			$this->email->subject = $ibforums->lang['email_reg_subj']." ".$ibforums->vars['board_name'];
			$this->email->to      = $member['email'];

			$this->email->send_mail();
		}
		else if ( $val['email_chg'] == 1 )
		{
			$this->email->get_template("newemail");

			$this->email->build_message( array(
												'NAME'         => $member['name'],
												'THE_LINK'     => $this->base_url_nosess."?act=Reg&CODE=03&type=newemail&uid=".$member['id']."&aid=".$val['vid'],
												'ID'           => $member['id'],
												'MAN_LINK'     => $this->base_url_nosess."?act=Reg&CODE=07",
												'CODE'         => $val['vid'],
											  )
										);

			$this->email->subject = $ibforums->lang['ne_subject'].' '.$ibforums->vars['board_name'];
			$this->email->to      = $member['email'];

			$this->email->send_mail();
		}
		else
		{
			$this->revalidate_one('err_no_validations');
			return;
		}

		$this->output .= $this->html->show_revalidated();

		$this->page_title = $ibforums->lang['rv_title'];
		$this->nav        = array( $ibforums->lang['rv_title'] );
	}


 	/*-------------------------------------------------------------------------*/
	// Coppa Start
	// ------------------
	// Asks the registree if they are an old git or not
	/*-------------------------------------------------------------------------*/

	function coppa_perms_form()
	{
		global $ibforums, $DB, $std;

		echo($this->html->coppa_form());
		exit();
	}



	function coppa_start()
	{
		global $ibforums, $DB, $std;

		$coppa_date = date( 'j-F y', mktime(0,0,0,date("m"),date("d"),date("Y")-13) );

		$ibforums->lang['coppa_form_text'] = str_replace( "<#FORM_LINK#>", "<a href='{$ibforums->base_url}act=Reg&amp;CODE=12'>{$ibforums->lang['coppa_link_form']}</a>", $ibforums->lang['coppa_form_text']);

		$this->output .= $this->html->coppa_start($coppa_date);

		$this->page_title = $ibforums->lang['coppa_title'];

    	$this->nav        = array( $ibforums->lang['coppa_title'] );
 	}

 	function coppa_two()
	{
		global $ibforums, $DB, $std;

		$ibforums->lang['coppa_form_text'] = str_replace( "<#FORM_LINK#>", "<a href='{$ibforums->base_url}act=Reg&amp;CODE=12'>{$ibforums->lang['coppa_link_form']}</a>", $ibforums->lang['coppa_form_text']);

		$this->output .= $this->html->coppa_two();

		$this->page_title = $ibforums->lang['coppa_title'];

    	$this->nav        = array( $ibforums->lang['coppa_title'] );
 	}

 	/*-------------------------------------------------------------------------*/
	// lost_password_start
	// ------------------
	// Simply shows the lostpassword form
	// What do you want? Blood?
	/*-------------------------------------------------------------------------*/

	function lost_password_start($errors="")
	{
		global $ibforums, $DB, $std;

		if ($ibforums->vars['bot_antispam'])
		{
			//-----------------------------------------
			// Sort out the security code
			//-----------------------------------------

			$r_date = time() - (60*60*6);

			//-----------------------------------------
			// Remove old reg requests from the DB
			//-----------------------------------------

			$DB->simple_exec_query( array( 'delete' => 'reg_antispam', 'where' => "ctime < '$r_date'" ) );

			//-----------------------------------------
			// Set a new ID for this reg request...
			//-----------------------------------------

			$regid = md5( uniqid(microtime()) );

			//-----------------------------------------
			// Set a new 6 character numerical string
			//-----------------------------------------

			mt_srand ((double) microtime() * 1000000);

			$reg_code = mt_rand(100000,999999);

			//-----------------------------------------
			// Insert into the DB
			//-----------------------------------------

			$DB->do_insert( 'reg_antispam', array (
												   'regid'      => $regid,
												   'regcode'    => $reg_code,
												   'ip_address' => $ibforums->input['IP_ADDRESS'],
												   'ctime'      => time(),
									   )       );
		}

		$this->page_title = $ibforums->lang['lost_pass_form'];

    	$this->nav        = array( $ibforums->lang['lost_pass_form'] );

    	if ($errors != "")
    	{
    		$this->output .= $this->html->errors( $ibforums->lang[$errors]);
    	}

    	$this->output    .= $this->html->lost_pass_form($regid);

    	if ($ibforums->vars['bot_antispam'] == 'gd')
		{
			$this->output = str_replace( "<!--{REG.ANTISPAM}-->", $this->html->bot_antispam_gd( $regid ), $this->output );
		}
		else if ($ibforums->vars['bot_antispam'] == 'gif')
		{
			$this->output = str_replace( "<!--{REG.ANTISPAM}-->", $this->html->bot_antispam( $regid ), $this->output );
		}
    }


    /*-------------------------------------------------------------------------*/
    // LOST PASSWORD: SEND
    /*-------------------------------------------------------------------------*/

    function lost_password_end()
    {
		global $ibforums, $DB, $std, $print;

    	if ($ibforums->vars['bot_antispam'])
		{
			//-----------------------------------------
			// Security code stuff
			//-----------------------------------------

			if ($ibforums->input['regid'] == "")
			{
				$this->lost_password_start('err_reg_code');
				return;
			}

			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'reg_antispam',
										  'where'  => "regid='".trim(addslashes($ibforums->input['regid']))."'"
								 )      );

			$DB->simple_exec();

			if ( ! $row = $DB->fetch_row() )
			{
				$this->show_reg_form('err_reg_code');
				return;
			}

			if ( trim( intval($ibforums->input['reg_code']) ) != $row['regcode'] )
			{
				$this->lost_password_start('err_reg_code');
				return;
			}
		}

    	//-----------------------------------------
    	// Back to the usual programming! :o
    	//-----------------------------------------

    	if ($_POST['member_name'] == "")
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_username' ) );
    	}

    	//-----------------------------------------
		// Check for input and it's in a valid format.
		//-----------------------------------------

		$member_name = trim(strtolower($ibforums->input['member_name']));

		if ($member_name == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_username' ) );
		}

    	//-----------------------------------------
		// Attempt to get the user details from the DB
		//-----------------------------------------

		if ( $ibforums->vars['converge_login_method'] == 'username' )
		{

			$DB->simple_construct( array( 'select' => 'name, id, email, mgroup', 'from' => 'members', 'where' => "LOWER(name)='$member_name'" ) );
			$DB->simple_exec();
		}
		else
		{
			$DB->simple_construct( array( 'select' => 'name, id, email, mgroup', 'from' => 'members', 'where' => "LOWER(email)='$member_name'" ) );
			$DB->simple_exec();
		}

		if ( ! $DB->get_num_rows() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
		}
		else
		{
			$member = $DB->fetch_row();

			//-----------------------------------------
			// Is there a validation key? If so, we'd better not touch it
			//-----------------------------------------

			if ($member['id'] == "")
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
			}

			$validate_key = md5( $std->make_password() . time() );

			//-----------------------------------------
			// Update the DB for this member.
			//-----------------------------------------

			$db_str = array(
							'vid'         => $validate_key,
							'member_id'   => $member['id'],
							'real_group'  => $member['mgroup'],
							'temp_group'  => $member['mgroup'],
							'entry_date'  => time(),
							'coppa_user'  => 0,
							'lost_pass'   => 1,
							'ip_address'  => $ibforums->input['IP_ADDRESS']
						   );

			$DB->do_insert( 'validating', $db_str );

			//-----------------------------------------
			// Send out the email.
			//-----------------------------------------

    		$this->email->get_template("lost_pass");

			$this->email->build_message( array(
												'NAME'         => $member['name'],
												'PASSWORD'     => $new_pass,
												'THE_LINK'     => $this->base_url_nosess."?act=Reg&CODE=lostpassform&uid=".$member['id']."&aid=".$validate_key,
												'MAN_LINK'     => $this->base_url_nosess."?act=Reg&CODE=lostpassform",
												'EMAIL'        => $member['email'],
												'ID'           => $member['id'],
												'CODE'         => $validate_key,
												'IP_ADDRESS'   => $ibforums->input['IP_ADDRESS'],
											  )
										);

			$this->email->subject = $ibforums->lang['lp_subject'].' '.$ibforums->vars['board_name'];
			$this->email->to      = $member['email'];

			$this->email->send_mail();

			$this->output = $this->html->show_lostpasswait( $member );
		}

    	$this->page_title = $ibforums->lang['lost_pass_form'];
    }

 	/*-------------------------------------------------------------------------*/
	// show_reg_form
	// ------------------
	// Simply shows the registration form, no - really! Thats
	// all it does. It doesn't make the tea or anything.
	// Just the registration form, no more - no less.
	// Unless your server went down, then it's just useless.
	/*-------------------------------------------------------------------------*/

    function show_reg_form($errors = "")
    {
		global $ibforums, $DB, $std;

    	if ( $ibforums->vars['no_reg'] == 1 )
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'reg_off' ) );
    	}

    	$coppa = ($ibforums->input['coppa_user'] == 1) ? 1 : 0;

    	//-----------------------------------------
    	// Read T&Cs yet?
    	//-----------------------------------------

    	if ( ! $ibforums->input['termsread'] )
    	{
    		//-----------------------------------------
			// Temp Fix to HiveMail..
			// If we have a logged in member, and a mailsign up call,
			// we don't want to show the terms. Boink.
			//-----------------------------------------

			if ( $ibforums->member['id'] && $ibforums->input['mailsignup'])
			{
				$std->boink_it( $ibforums->base_url.'act=Reg&mailsignup=1&coppa_pass=1&termsread=1&agree_to_terms=1' );
			}

    		$cache = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings', 'where' => "conf_key='reg_rules'" ) );

    		$text  = $cache['conf_value'] ? $cache['conf_value'] : $cache['conf_default'];

    		$this->page_title = $ibforums->lang['registration_form'];
    		$this->nav        = array( $ibforums->lang['registration_form'] );

    		$this->output .= $this->html->show_terms( $std->my_nl2br($text), $coppa );
    		return;
    	}
    	else
    	{
			//-----------------------------------------
			// Did we agree to the t&c?
			//-----------------------------------------

			if ( ! $ibforums->input['agree_to_terms'] )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'reg_no_agree', 'EXTRA' => $ibforums->base_url ) );
			}
    	}

    	if ($ibforums->vars['reg_auth_type'])
    	{
    		$ibforums->lang['std_text'] .= "<br />" . $ibforums->lang['email_validate_text'];
    	}

    	$this->bash_dead_validations();

    	//-----------------------------------------
		// Clean out anti-spam stuffy
		//-----------------------------------------

		if ($ibforums->vars['bot_antispam'])
		{
			// Set a new ID for this reg request...

			$regid = md5( uniqid(microtime()) );

			// Set a new 6 character numerical string

			mt_srand ((double) microtime() * 1000000);

			$reg_code = mt_rand(100000,999999);

			// Insert into the DB

			$DB->do_insert( 'reg_antispam', array (
												   'regid'      => $regid,
												   'regcode'    => $reg_code,
												   'ip_address' => $ibforums->input['IP_ADDRESS'],
												   'ctime'      => time(),
									   )       );
		}

    	//-----------------------------------------
		// Custom profile fields stuff
		//-----------------------------------------

		$required_output = "";
		$optional_output = "";

		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->cache_data  = $ibforums->cache['profilefields'];

    	$fields->init_data();
    	$fields->parse_to_register();

    	foreach( $fields->out_fields as $id => $data )
    	{
    		if ( $fields->cache_data[ $id ]['pf_not_null'] == 1 )
			{
				$ftype = 'required_output';
			}
			else
			{
				$ftype = 'optional_output';
			}

    		if ( $fields->cache_data[ $id ]['pf_type'] == 'drop' )
			{
				$form_element = $this->html->field_dropdown( 'field_'.$id, $data );
			}
			else if ( $fields->cache_data[ $id ]['pf_type'] == 'area' )
			{
				$data = $ibforums->input['field_'.$id] ? $ibforums->input['field_'.$id] : $data;
				$form_element = $this->html->field_textarea( 'field_'.$id, $data );
			}
			else
			{
				$data = $ibforums->input['field_'.$id] ? $ibforums->input['field_'.$id] : $data;
				$form_element = $this->html->field_textinput( 'field_'.$id, $data );
			}

			${$ftype} .= $this->html->field_entry( $fields->field_names[ $id ], $fields->field_desc[ $id ], $form_element );
    	}

    	$this->page_title = $ibforums->lang['registration_form'];
    	$this->nav        = array( $ibforums->lang['registration_form'] );

    	if ($errors != "")
    	{
    		$this->output .= $this->html->errors( $ibforums->lang[$errors]);
    	}

    	$this->output    .= $this->html->ShowForm( array( 'TEXT'        => $ibforums->lang['std_text'],
    												      'coppa_user'  => $coppa,
    											 )      );

    	if ($ibforums->vars['bot_antispam'] == 'gd')
		{
			$this->output = str_replace( "<!--{REG.ANTISPAM}-->", $this->html->bot_antispam_gd( $regid ), $this->output );
		}
		else if ($ibforums->vars['bot_antispam'] == 'gif')
		{
			$this->output = str_replace( "<!--{REG.ANTISPAM}-->", $this->html->bot_antispam( $regid ), $this->output );
		}

    	if ($required_output != "")
		{
			$this->output = str_replace( "<!--{REQUIRED.FIELDS}-->", "\n".$required_output, $this->output );
		}

		if ($optional_output != "")
		{
			$this->output = str_replace( "<!--{OPTIONAL.FIELDS}-->", $this->html->optional_title()."\n".$optional_output, $this->output );
		}

		//-----------------------------------------
		// Time zone...
		//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_ucp', $ibforums->lang_id );

		$offset = ( $ibforums->input['time_offset'] != "" ) ? $ibforums->input['time_offset'] : $ibforums->vars['time_offset'];

 		$time_select = "<select name='time_offset' class='forminput'>";

 		foreach( $ibforums->lang as $off => $words )
 		{
 			if (preg_match("/^time_([\d\.\-]+)$/", $off, $match))
 			{
				$time_select .= $match[1] == $offset ? "<option value='{$match[1]}' selected='selected'>$words</option>"
												     : "<option value='{$match[1]}'>$words</option>";
 			}
 		}

 		$time_select .= "</select>";

 		$this->output = str_replace( "<!--{TIME_ZONE}-->", "\n".$time_select, $this->output );

		//-----------------------------------------
		// Boxes checked?
		//-----------------------------------------

		$admin_checked = 'checked="checked"';

		if ( $ibforums->input['CODE'] == '02' )
		{
			//-----------------------------------------
			// Form submitted...
			//-----------------------------------------

			if ( ! $ibforums->input['allow_admin_mail'] )
			{
				$admin_checked = '';
			}
		}

		$member_checked = $ibforums->input['allow_member_mail'] ? 'checked="checked"' : '';
		$dst_checked    = $ibforums->input['dst']               ? 'checked="checked"' : '';

		$this->output = str_replace( "<!--[admin.checked]-->" , $admin_checked , $this->output );
		$this->output = str_replace( "<!--[member.checked]-->", $member_checked, $this->output );
		$this->output = str_replace( "<!--[dst.checked]-->"   , $dst_checked   , $this->output );

		//-----------------------------------------
		// Subscribe on register?
		//-----------------------------------------

		$all_currency = array();
		$def_currency = "";
		$subs         = array();
		$subs_output  = "";
		$desc_output  = "";

		if ( $ibforums->vars['subsm_show_reg'] )
		{
			$ibforums->lang = $std->load_words($ibforums->lang, 'lang_subscriptions', $ibforums->lang_id );

			//-----------------------------------------
			// Get currency buns!
			// Ok, we did that joke in another module and it
			// wasn't funny then
			//-----------------------------------------

    		$DB->simple_construct( array( 'select' => '*', 'from' => 'subscription_currency' ) );
    		$DB->simple_exec();

			while ( $c = $DB->fetch_row() )
			{
				$all_currency[ $c['subcurrency_code'] ] = $c;

				if ( $c['subcurrency_default'] )
				{
					$def_currency = $c;
				}
			}

			//-----------------------------------------
			// Get subscription packages
			//-----------------------------------------

			$sub_output = $this->html->subsm_start( $def_currency['subcurrency_code'] );

			//-----------------------------------------
			// Enforcing?
			//-----------------------------------------

			if ( ! $ibforums->vars['subsm_enforce'] )
			{
				$sub_output .= $this->html->subsm_row( '0', $ibforums->lang['subsm_none'], '0.00', $ibforums->lang['subsm_na'] );
			}

			$DB->simple_construct( array( 'select' => '*', 'from' => 'subscriptions', 'order' => 'sub_cost' ) );
    		$DB->simple_exec();

			while ( $row = $DB->fetch_row() )
			{
				$duration = $row['sub_length'];

				if ( $duration > 1 )
				{
					$duration .= ' '.$ibforums->lang[ 'timep_'.$row['sub_unit'] ];
				}
				else
				{
					$duration .= ' '.$ibforums->lang[ 'time_'.$row['sub_unit'] ];
				}

				$sub_output .= $this->html->subsm_row( $row['sub_id'],
													   $row['sub_title'],
													   sprintf( "%.2f", $row['sub_cost']  * $def_currency['subcurrency_exchange'] ),
													   $duration
													 );
				$desc_output .= "\nvar subsdesc_{$row['sub_id']} = '".str_replace( "'", "\\'", strip_tags( str_replace( "\n", '\n', $std->my_br2nl($row['sub_desc']) ) ) )."';";
			}

			$sub_output .= $this->html->subsm_end();

			//-----------------------------------------
			// Parse 'n show
			//-----------------------------------------

			if ( $sub_output )
			{
				$this->output = str_replace( '<!--{SUBS.MANAGER}-->', $sub_output , $this->output );
				$this->output = str_replace( '<!--{SUBS.JSCRIPT}-->', str_replace( "\r", "", $desc_output ), $this->output );
			}

		}

		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);
    		$this->modules->on_register_form();
   		}
   	}

   	/*-------------------------------------------------------------------------*/
	// create_account
	// ------------------
	// Now this is a really good subroutine. It adds the member
	// to the members table in the database. Yes, really fancy
	// this one. It also finds the time to see if we need to
	// check any email verification type malarky before we
	// can use this brand new account. It's like buying a new
	// car and getting it towed home and being told the keys
	// will be posted later. Although you can't polish this
	// routine while you're waiting.
	/*-------------------------------------------------------------------------*/

	function create_account()
	{
		global $ibforums, $std, $DB, $print;

		if ($_POST['act'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		if ($ibforums->vars['no_reg'] == 1)
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'reg_off' ) );
    	}

    	$coppa = ($ibforums->input['coppa_user'] == 1) ? 1 : 0;

		//-----------------------------------------
		// Custom profile field stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->cache_data  = $ibforums->cache['profilefields'];

    	$fields->init_data();
    	$fields->parse_to_save( 1 );

		//-----------------------------------------
		// Check...
		//-----------------------------------------

		if ( count( $fields->error_fields['empty'] ) )
		{
			$this->show_reg_form('err_complete_form');
			return;
		}

		if ( count( $fields->error_fields['invalid'] ) )
		{
			$this->show_reg_form('err_invalid');
			return;
		}

		if ( count( $fields->error_fields['toobig'] ) )
		{
			$this->show_reg_form('err_cf_to_long');
			return;
		}

		//-----------------------------------------
		// Trim off the username and password
		//-----------------------------------------

		$in_username = str_replace( '|', '&#124;' , $ibforums->input['UserName'] );

		//-----------------------------------------
		// Remove multiple spaces in the username
		//-----------------------------------------

		$in_username = preg_replace( "/\s{2,}/", " ", $in_username );

		//-----------------------------------------
		// Remove 'sneaky' spaces
		//-----------------------------------------

		if ( $ibforums->vars['strip_space_chr'] )
    	{
			$in_username = str_replace( chr(160), ' ', $in_username );
			$in_username = str_replace( chr(173), ' ', $in_username );
		}

		//-----------------------------------------
		// Trim up..
		//-----------------------------------------

		$in_username  = trim($in_username);

		//-----------------------------------------
		// Test unicode name too
		//-----------------------------------------

		$unicode_name = preg_replace('/&#([0-9]+);/esi', "chr('\\1')", $in_username);

		$in_password = trim($ibforums->input['PassWord']);
		$in_email    = strtolower( trim($ibforums->input['EmailAddress']) );

		$ibforums->input['EmailAddress_two'] = strtolower( trim($ibforums->input['EmailAddress_two']) );

		if ($ibforums->input['EmailAddress_two'] != $in_email)
		{
			$this->show_reg_form('err_email_address_match');
			return;
		}

		//-----------------------------------------
		// More unicode..
		//-----------------------------------------

		$len_u = $in_username;

		$len_u = preg_replace("/&#([0-9]+);/", "-", $len_u );

		$len_p = $in_password;

		$len_p = preg_replace("/&#([0-9]+);/", "-", $len_p );

		//-----------------------------------------
		// Check for errors in the input.
		//-----------------------------------------

		if (empty($in_username))
		{
			$this->show_reg_form('err_no_username');
			return;
		}
		if (strlen($len_u) < 3)
		{
			$this->show_reg_form('err_no_username');
			return;
		}
		if (strlen($len_u) > 32)
		{
			$this->show_reg_form('err_no_username');
			return;
		}
		if (empty($in_password))
		{
			$this->show_reg_form('err_no_password');
			return;
		}
		if (strlen($len_p) < 3)
		{
			$this->show_reg_form('err_no_password');
			return;
		}
		if (strlen($len_p) > 32)
		{
			$this->show_reg_form('err_no_password');
			return;
		}
		if ($ibforums->input['PassWord_Check'] != $in_password)
		{
			$this->show_reg_form('err_pass_match');
			return;
		}
		if (strlen($in_email) < 6)
		{
			$this->show_reg_form('err_invalid_email');
			return;
		}

		//-----------------------------------------
		// Check the email address
		//-----------------------------------------

		$in_email = $std->clean_email($in_email);

		if ( ! $in_email )
		{
			$this->show_reg_form('err_invalid_email');
			return;
		}

		//-----------------------------------------
		// Is this name already taken?
		//-----------------------------------------

		$DB->cache_add_query( 'login_getmember', array( 'username' => strtolower($in_username) ) );
		$DB->cache_exec_query();

		$name_check = $DB->fetch_row();

		if ($name_check['id'])
		{
			$this->show_reg_form('err_user_exists');
			return;
		}

		//-----------------------------------------
		// Special chars?
		//-----------------------------------------

		if ( $unicode_name != $in_username )
		{
			$DB->cache_add_query( 'login_getmember', array( 'username' => $DB->add_slashes(strtolower($unicode_name) ) ));
			$DB->cache_exec_query();

			$name_check = $DB->fetch_row();

			if ($name_check['id'])
			{
				$this->show_reg_form('err_user_exists');
				return;
			}
		}

		if (strtolower($in_username) == 'guest')
		{
			$this->show_reg_form('err_user_exists');
			return;
		}

		//-----------------------------------------
		// Is this email addy taken? CONVERGE THIS??
		//-----------------------------------------

		if ( $ibforums->converge->converge_check_for_member_by_email( $in_email ) == TRUE )
		{
			$this->show_reg_form('err_email_exists');
			return;
		}

		//-----------------------------------------
		// Load ban filters
		//-----------------------------------------

		$banfilters = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'banfilters' ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$banfilters[ $r['ban_type'] ][] = $r['ban_content'];
		}

		//-----------------------------------------
		// Are they banned [IP]?
		//-----------------------------------------

		if ( is_array( $banfilters['ip'] ) and count( $banfilters['ip'] ) )
		{
			foreach ($banfilters['ip'] as $ip)
			{
				$ip = str_replace( '\*', '.*', preg_quote($ip, "/") );

				if ( preg_match( "/^$ip/", $ibforums->input['IP_ADDRESS'] ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'you_are_banned', 'INIT' => 1 ) );
				}
			}
		}

		//-----------------------------------------
		// Are they banned [NAMES]?
		//-----------------------------------------

		if ( is_array( $banfilters['name'] ) and count( $banfilters['name'] ) )
		{
			foreach ( $banfilters['name'] as $n )
			{
				if ( $n == "" )
				{
					continue;
				}

				if ( preg_match( "/".preg_quote($n, '/' )."/i", $in_username ) )
				{
					$this->show_reg_form('err_user_exists');
					return;
				}
			}
		}

		//-----------------------------------------
		// Are they banned [EMAIL]?
		//-----------------------------------------

		if ( is_array( $banfilters['email'] ) and count( $banfilters['email'] ) )
		{
			foreach ( $banfilters['email'] as $email )
			{
				$email = preg_replace( "/\*/", '.*' , $email );

				if ( preg_match( "/$email/", $in_email ) )
				{
					$std->Error( array( LEVEL => 1, MSG => 'you_are_banned' ) );
				}
			}
		}

		//-----------------------------------------
		// Check the reg_code
		//-----------------------------------------

		if ($ibforums->vars['bot_antispam'])
		{
			if ($ibforums->input['regid'] == "")
			{
				$this->show_reg_form('err_reg_code');
				return;
			}

			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'reg_antispam',
										  'where'  => "regid='".trim(addslashes($ibforums->input['regid']))."'"
								 )      );

			$DB->simple_exec();

			if ( ! $row = $DB->fetch_row() )
			{
				$this->show_reg_form('err_reg_code');
				return;
			}

			if ( trim( intval($ibforums->input['reg_code']) ) != $row['regcode'] )
			{
				$this->show_reg_form('err_reg_code');
				return;
			}

			$DB->simple_exec_query( array( 'delete' => 'reg_antispam', 'where' => "regid='".trim(addslashes($ibforums->input['regid']))."'" ) );
		}

		//-----------------------------------------
		// Build up the hashes
		//-----------------------------------------

		$mem_group = $ibforums->vars['member_group'];

		//-----------------------------------------
		// Are we asking the member or admin to preview?
		//-----------------------------------------

		if ($ibforums->vars['reg_auth_type'])
		{
			$mem_group = $ibforums->vars['auth_group'];
		}
		else if ($coppa == 1)
		{
			$mem_group = $ibforums->vars['auth_group'];
		}
		else if ( $ibforums->vars['subsm_enforce'] )
		{
			$mem_group = $ibforums->vars['subsm_nopkg_group'];
		}

		$member = array(
						 'name'             => $in_username,
						 'member_login_key' => $ibforums->converge->generate_auto_log_in_key(),
						 'email'            => $in_email,
						 'mgroup'           => $mem_group,
						 'posts'            => 0,
						 'joined'           => time(),
						 'ip_address'       => $ibforums->input['IP_ADDRESS'],
						 'time_offset'      => $ibforums->input['time_offset'],
						 'view_sigs'        => 1,
						 'email_pm'         => 1,
						 'view_img'         => 1,
						 'view_avs'         => 1,
						 'restrict_post'    => 0,
						 'view_pop'         => 1,
						 'msg_total'        => 0,
						 'new_msg'          => 0,
						 'coppa_user'       => $coppa,
						 'language'         => $ibforums->vars['default_language'],
						 'dst_in_use'       => intval( $ibforums->input['dst'] ),
						 'allow_admin_mails'=> intval( $ibforums->input['allow_admin_mail'] ),
						 'hide_email'       => $ibforums->input['allow_member_mail'] ? 0 : 1,
						 'subs_pkg_chosen'  => intval( $ibforums->input['subspackage'] )
					   );

		$salt     = $ibforums->converge->generate_password_salt(5);
		$passhash = $ibforums->converge->generate_compiled_passhash( $salt, md5($in_password) );

		$converge = array( 'converge_email'     => $in_email,
						   'converge_joined'    => time(),
						   'converge_pass_hash' => $passhash,
						   'converge_pass_salt' => str_replace( '\\', "\\\\", $salt )
						 );

		//-----------------------------------------
		// Insert: CONVERGE
		//-----------------------------------------

		$DB->do_insert( 'members_converge', $converge );

		//-----------------------------------------
		// Get converges auto_increment user_id
		//-----------------------------------------

		$member_id    = $DB->get_insert_id();
		$member['id'] = $member_id;

		//-----------------------------------------
		// Insert: MEMBERS
		//-----------------------------------------

		$DB->force_data_type = array( 'name' => 'string' );

		$DB->do_insert( 'members', $member );

		//-----------------------------------------
		// Insert: MEMBER EXTRA
		//-----------------------------------------

		$DB->do_insert( 'member_extra', array( 'id' => $member_id, 'vdirs' => 'in:Inbox|sent:Sent Items' ) );

		//-----------------------------------------
		// Insert into the custom profile fields DB
		//-----------------------------------------

		// Ensure deleted members profile fields are removed.

		$DB->simple_exec_query( array( 'delete' => 'pfields_content', 'where' => 'member_id='.$member['id'] ) );

		$fields->out_fields['member_id'] = $member['id'];

		$DB->do_insert( 'pfields_content', $fields->out_fields );

		//-----------------------------------------
		// Use modules?
		//-----------------------------------------

		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);

			$member['password'] = trim($ibforums->input['PassWord']);

    		$this->modules->on_create_account($member);

    		if ( $this->modules->error == 1 )
    		{
    			return;
    		}

    		$member['password'] = "";
   		}

		//-----------------------------------------
		// Validation key
		//-----------------------------------------

		$validate_key = md5( $std->make_password() . time() );
		$time         = time();

		if ($coppa != 1)
		{
			if ( ($ibforums->vars['reg_auth_type'] == 'user') or ($ibforums->vars['reg_auth_type'] == 'admin') )
			{

				// We want to validate all reg's via email, after email verificiation has taken place,
				// we restore their previous group and remove the validate_key

				$DB->do_insert( 'validating', array (
													  'vid'         => $validate_key,
													  'member_id'   => $member['id'],
													  'real_group'  => $ibforums->vars['member_group'],
													  'temp_group'  => $ibforums->vars['auth_group'],
													  'entry_date'  => $time,
													  'coppa_user'  => $coppa,
													  'new_reg'     => 1,
													  'ip_address'  => $member['ip_address']
											)       );


				if ( $ibforums->vars['reg_auth_type'] == 'user' )
				{
					$this->email->get_template("reg_validate");

					$this->email->build_message( array(
														'THE_LINK'     => $this->base_url_nosess."?act=Reg&CODE=03&uid=".urlencode($member_id)."&aid=".urlencode($validate_key),
														'NAME'         => $member['name'],
														'MAN_LINK'     => $this->base_url_nosess."?act=Reg&CODE=05",
														'EMAIL'        => $member['email'],
														'ID'           => $member_id,
														'CODE'         => $validate_key,
													  )
												);

					$this->email->subject = "Registration at ".$ibforums->vars['board_name'];
					$this->email->to      = $member['email'];

					$this->email->send_mail();

					$this->output     = $this->html->show_authorise( $member );

				}
				else if ( $ibforums->vars['reg_auth_type'] == 'admin' )
				{
					$this->output     = $this->html->show_preview( $member );
				}

				if ($ibforums->vars['new_reg_notify'])
				{

					$date = $std->get_date( time(), 'LONG', 1 );

					$this->email->get_template("admin_newuser");

					$this->email->build_message( array(
														'DATE'         => $date,
														'MEMBER_NAME'  => $member['name'],
													  )
												);

					$this->email->subject = "New Registration at ".$ibforums->vars['board_name'];
					$this->email->to      = $ibforums->vars['email_in'];
					$this->email->send_mail();
				}

				$this->page_title = $ibforums->lang['reg_success'];

				$this->nav        = array( $ibforums->lang['nav_reg'] );
			}

			else
			{
				//-----------------------------------------
				// We don't want to preview, or get them to validate via email.
				//-----------------------------------------

				$ibforums->cache['stats']['last_mem_name'] = $member['name'];
				$ibforums->cache['stats']['last_mem_id']   = $member['id'];
				$ibforums->cache['stats']['mem_count']    += 1;

				$std->update_cache(  array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 0 ) );

				if ($ibforums->vars['new_reg_notify'])
				{
					$date = $std->get_date( time(), 'LONG', 1 );

					$this->email->get_template("admin_newuser");

					$this->email->build_message( array(
														'DATE'         => $date,
														'MEMBER_NAME'  => $member['name'],
													  )
												);

					$this->email->subject = "New Registration at ".$ibforums->vars['board_name'];
					$this->email->to      = $ibforums->vars['email_in'];
					$this->email->send_mail();
				}

				$std->my_setcookie("member_id"   , $member['id']              , 1);
				$std->my_setcookie("pass_hash"   , $member['member_login_key'], 1);

				$std->boink_it($ibforums->base_url.'&act=Login&CODE=autologin&fromreg=1');
			}
		}
		else
		{
			// This is a COPPA user, so lets tell them they registered OK and redirect to the form.

			$DB->do_insert( 'validating', array (
												  'vid'         => $validate_key,
												  'member_id'   => $member['id'],
												  'real_group'  => $ibforums->vars['member_group'],
												  'temp_group'  => $ibforums->vars['auth_group'],
												  'entry_date'  => $time,
												  'coppa_user'  => $coppa,
												  'new_reg'     => 1,
												  'ip_address'  => $member['ip_address']
										)       );

			$print->redirect_screen( $ibforums->lang['cp_success'], 'act=Reg&CODE=12' );
		}
	}

    /*-------------------------------------------------------------------------*/
	// validate_user
	// ------------------
	// Leave a message after the tone, and I'll amuse myself
	// by pulling faces when hearing the message later.
	/*-------------------------------------------------------------------------*/

	function validate_user()
	{
		global $ibforums, $std, $DB, $print, $HTTP_POST_VARS;

		//-----------------------------------------
		// Check for input and it's in a valid format.
		//-----------------------------------------

		$in_user_id      = intval(trim(urldecode($ibforums->input['uid'])));
		$in_validate_key = trim(urldecode($ibforums->input['aid']));
		$in_type         = trim($ibforums->input['type']);

		if ($in_type == "")
		{
			$in_type = 'reg';
		}

		//-----------------------------------------
		// check input
		//-----------------------------------------

		if (! preg_match( "/^(?:[\d\w]){32}$/", $in_validate_key ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'data_incorrect' ) );
		}

		if (! preg_match( "/^(?:\d){1,}$/", $in_user_id ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'data_incorrect' ) );
		}

		//-----------------------------------------
		// Attempt to get the profile of the requesting user
		//-----------------------------------------

		$member = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'members', 'where' => 'id='.$in_user_id ) );

		if ( ! $member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_no_mem' ) );
		}

		//-----------------------------------------
		// Get validating info..
		//-----------------------------------------

		if ( $in_type == 'lostpass' )
		{
			$validate = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'validating', 'where' => 'member_id='.$in_user_id.' and lost_pass=1' ) );
		}
		else
		{
			$validate = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'validating', 'where' => 'member_id='.$in_user_id ) );
		}

		if ( ! $validate['member_id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_no_key' ) );
		}

		if (($validate['new_reg'] == 1) && ($ibforums->vars['reg_auth_type'] == "admin"))
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_no_key_not_allow' ) );
		}

		if ($validate['vid'] != $in_validate_key)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_key_wrong' ) );
		}
		else
		{
			//-----------------------------------------
			// REGISTER VALIDATE
			//-----------------------------------------

			if ($in_type == 'reg')
			{
				if ( $validate['new_reg'] != 1 )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_no_key' ) );
				}

				if (empty($validate['real_group']))
				{
					$validate['real_group'] = $ibforums->vars['member_group'];
				}

				$DB->do_update( 'members', array( 'mgroup' => intval($validate['real_group']) ), 'id='.intval($member['id']) );

				if ( USE_MODULES == 1 )
				{
					$this->modules->register_class(&$this);
    				$this->modules->on_group_change($member['id'], $validate['real_group']);
    			}

				//-----------------------------------------
				// Update the stats...
				//-----------------------------------------

				$ibforums->cache['stats']['last_mem_name'] = $member['name'];
				$ibforums->cache['stats']['last_mem_id']   = $member['id'];
				$ibforums->cache['stats']['mem_count']    += 1;

				$std->update_cache(  array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 0 ) );

				$std->my_setcookie("member_id"   , $member['id']              , 1);
				$std->my_setcookie("pass_hash"   , $member['member_login_key'], 1);

				//-----------------------------------------
				// Remove "dead" validation
				//-----------------------------------------

				$DB->simple_exec_query( array( 'delete' => 'validating', 'where' => "vid='".$validate['vid']."' OR (member_id={$member['id']} AND new_reg=1)" ) );

				$this->bash_dead_validations();

				$std->boink_it($ibforums->base_url.'&act=Login&CODE=autologin&fromreg=1');

			}

			//-----------------------------------------
			// LOST PASS VALIDATE
			//-----------------------------------------

			else if ($in_type == 'lostpass')
			{
				if ($validate['lost_pass'] != 1)
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'lp_no_pass' ) );
				}

				if ( $_POST['pass1'] == "" )
				{
					$std->Error( array( LEVEL => 1, MSG => 'pass_blank' ) );
				}

				if ( $_POST['pass2'] == "" )
				{
					$std->Error( array( LEVEL => 1, MSG => 'pass_blank' ) );
				}

				$pass_a = trim($ibforums->input['pass1']);
				$pass_b = trim($ibforums->input['pass2']);

				if ( strlen($pass_a) < 3 )
				{
					$std->Error( array( LEVEL => 1, MSG => 'pass_too_short' ) );
				}

				if ( $pass_a != $pass_b )
				{
					$std->Error( array( LEVEL => 1, MSG => 'pass_no_match' ) );
				}

				$new_pass = md5($pass_a);

				$ibforums->converge->converge_update_password( $new_pass, $member['email'] );

				$std->my_setcookie("member_id"   , $member['id']              , 1);
				$std->my_setcookie("pass_hash"   , $member['member_login_key'], 1);

				//-----------------------------------------
				// Remove "dead" validation
				//-----------------------------------------

				$DB->simple_exec_query( array( 'delete' => 'validating', 'where' => "vid='".$validate['vid']."' OR (member_id={$member['id']} AND lost_pass=1)" ) );

				$this->bash_dead_validations();

				$std->boink_it($ibforums->base_url.'&act=Login&CODE=autologin&frompass=1');

			}

			//-----------------------------------------
			// EMAIL ADDY CHANGE
			//-----------------------------------------

			else if ($in_type == 'newemail')
			{
				if ( $validate['email_chg'] != 1 )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_no_key' ) );
				}

				if (empty($validate['real_group']))
				{
					$validate['real_group'] = $ibforums->vars['member_group'];
				}

				$DB->do_update( 'members', array( 'mgroup' => intval($validate['real_group']) ), 'id='.intval($member['id']) );

				if ( USE_MODULES == 1 )
				{
					$this->modules->register_class(&$this);
    				$this->modules->on_group_change($member['id'], $validate['real_group']);
    			}

				$std->my_setcookie("member_id"   , $member['id']              , 1);
				$std->my_setcookie("pass_hash"   , $member['member_login_key'], 1);

				//-----------------------------------------
				// Remove "dead" validation
				//-----------------------------------------

				$DB->simple_exec_query( array( 'delete' => 'validating', 'where' => "vid='".$validate['vid']."' OR (member_id={$member['id']} AND email_chg=1)" ) );

				$this->bash_dead_validations();

				$std->boink_it($ibforums->base_url.'&act=Login&CODE=autologin&fromemail=1');
			}
		}
	}

    /*-------------------------------------------------------------------------*/
	// show_board_rules
	// ------------------
	// o_O  ^^
	/*-------------------------------------------------------------------------*/

	function show_board_rules()
	{
		global $ibforums, $DB;
	}

	/*-------------------------------------------------------------------------*/
	// Manual Lost Password Form
	/*-------------------------------------------------------------------------*/

	function show_manual_form($type='reg')
	{
		global $ibforums, $std, $DB;

		if ( $type == 'lostpass' )
		{
			$this->output = $this->html->show_lostpass_form();

			//-----------------------------------------
			// Check for input and it's in a valid format.
			//-----------------------------------------

			if ( $ibforums->input['uid'] AND $ibforums->input['aid'] )
			{

				$in_user_id      = intval(trim(urldecode($ibforums->input['uid'])));
				$in_validate_key = trim(urldecode($ibforums->input['aid']));
				$in_type         = trim($ibforums->input['type']);

				if ($in_type == "")
				{
					$in_type = 'reg';
				}

				//-----------------------------------------
				// Check and test input
				//-----------------------------------------

				if (! preg_match( "/^(?:[\d\w]){32}$/", $in_validate_key ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'data_incorrect' ) );
				}

				if (! preg_match( "/^(?:\d){1,}$/", $in_user_id ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'data_incorrect' ) );
				}

				//-----------------------------------------
				// Attempt to get the profile of the requesting user
				//-----------------------------------------

				$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "id=$in_user_id" ) );

				$DB->simple_exec();

				if ( ! $member = $DB->fetch_row() )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_no_mem' ) );
				}

				//-----------------------------------------
				// Get validating info..
				//-----------------------------------------

				$validate = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'validating', 'where' => "member_id=$in_user_id and vid='$in_validate_key' and lost_pass=1" ) );

				if ( ! $validate['member_id'] )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'auth_no_key' ) );
				}

				$this->output = str_replace( "<!--IBF.INPUT_TYPE-->", $this->html->show_lostpass_form_auto($in_validate_key, $in_user_id), $this->output );
			}
			else
			{
				$this->output = str_replace( "<!--IBF.INPUT_TYPE-->", $this->html->show_lostpass_form_manual(), $this->output );
			}
		}
		else
		{
			$this->output     = $this->html->show_dumb_form($type);
		}

		$this->page_title = $ibforums->lang['activation_form'];
		$this->nav        = array( $ibforums->lang['activation_form'] );
	}





	function show_image()
	{
		global $ibforums, $DB, $std;

		if ( $ibforums->input['rc'] == "" )
		{
			return false;
		}

		// Get the info from the db

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'reg_antispam',
									  'where'  => "regid='".trim(addslashes($ibforums->input['rc']))."'"
							 )      );

		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			return false;
		}

		//-----------------------------------------
		// Using GD?
		//-----------------------------------------

		if ( $ibforums->vars['bot_antispam'] == 'gd' )
		{
			$std->show_gd_img($row['regcode']);
		}
		else
		{
			//-----------------------------------------
			// Using normal then, check for "p"
			//-----------------------------------------

			if ( $ibforums->input['p'] == "" )
			{
				return false;
			}

			$p = intval($ibforums->input['p']) - 1; //substr starts from 0, not 1 :p

			$this_number = substr( $row['regcode'], $p, 1 );

			$std->show_gif_img($this_number);
		}
	}



	function bash_dead_validations()
	{
		global $ibforums, $std, $DB;

		$mids = array();
		$vids = array();

		// If enabled, remove validating new_reg members & entries from members table

		if ( intval($ibforums->vars['validate_day_prune']) > 0 )
		{
			$less_than = time() - $ibforums->vars['validate_day_prune']*86400;

			$DB->cache_add_query( 'register_get_dead_validating', array( 'less_than' => $less_than ) );
			$DB->cache_exec_query();

			while( $i = $DB->fetch_row() )
			{
				if ( intval($i['posts']) < 1 )
				{
					$mids[] = $i['member_id'];
					$vids[] = "'".$i['vid']."'";
				}
			}

			// Remove non-posted validating members

			if ( count($mids) > 0 )
			{
				$DB->simple_exec_query( array( 'delete' => 'members_converge', 'where' => "converge_id IN(".implode(",",$mids).")" ) );
				$DB->simple_exec_query( array( 'delete' => 'members'         , 'where' => "id IN(".implode(",",$mids).")" ) );
				$DB->simple_exec_query( array( 'delete' => 'member_extra'    , 'where' => "id IN(".implode(",",$mids).")" ) );
				$DB->simple_exec_query( array( 'delete' => 'pfields_content' , 'where' => "member_id IN(".implode(",",$mids).")" ) );
				$DB->simple_exec_query( array( 'delete' => 'validating'      , 'where' => "vid IN(".implode(",",$vids).")" ) );

				if ( USE_MODULES == 1 )
				{
					$this->modules->register_class(&$this);
					$this->modules->on_delete($mids);
				}
			}
		}
	}

}

?>