<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Admin wrapper script
|   > Script written by Matt Mecham
|   > Date started: 1st March 2002
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------
 
// Are we running this on a lycos / tripod server?
// If so, change the following to a 1.

$is_on_tripod = 0;
 
// Root path

define( 'ROOT_PATH', './' );
define( 'KERNEL_PATH', ROOT_PATH.'ips_kernel/' );

// Check IP address to see if they match?
// this may cause problems for users on proxies
// where the IP address changes during a session

$check_ip = 1;

// Use GZIP content encoding for fast page generation
// in the admin center?

$use_gzip = 1;

// Enable module usage?
// (Vital for some mods and IPB enhancements)

define ( 'USE_MODULES', 1 );

// Enable custom error handling?
// Useful to trap skin errors, etc

define( 'CUSTOM_ERROR', 1 );

// You really don't want to turn this on

define( 'TRIAL_VERSION', 0 );

//-----------------------------------------------
// NO USER EDITABLE SECTIONS BELOW
//-----------------------------------------------

define ( 'IN_ACP', 1 );
define ( 'IN_IPB', 1 );
define ( 'IN_DEV', 0 );

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

if ( CUSTOM_ERROR )
{
	set_error_handler("my_error_handler");
}

if ( $is_on_tripod != 1 )
{
	if (function_exists('ini_get'))
	{
		$safe_switch = @ini_get("safe_mode") ? 1 : 0;
	}
	else
	{
		$safe_switch = 1;
	}
}
else
{
	$safe_switch = 1;
}

define( 'SAFE_MODE_ON', $safe_switch );

if (function_exists("set_time_limit") == 1 and SAFE_MODE_ON == 0)
{
  @set_time_limit(0);
}


class Debug
{
    function startTimer()
    {
        global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }
    function endTimer()
    {
        global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round (($endtime - $starttime), 5);
        return $totaltime;
    }
}

class info {

	var $vars       = "";
	var $version    = 'v2.0.4';
	var $acpversion = '20014';
	var $base_url   = '';
	var $main_error = "";
	var $main_msg   = "";
	var $skin       = array();
	var $member     = array();
	var $loaded_templates = array();
	
	function info($INFO)
	{
		$this->vars = $INFO;
		
		if ( defined($ibforums->vars['safe_mode_skins']) )
		{
			define( 'SAFE_MODE_SKINS', $ibforums->vars['safe_mode_skins'] );
		}
		else
		{
			define( 'SAFE_MODE_SKINS', SAFE_MODE_ON );
		}
		
		$this->vars['AVATARS_URL']     = 'style_avatars';
		$this->vars['EMOTICONS_URL']   = 'style_emoticons/<#EMO_DIR#>';
		$this->vars['mime_img']        = 'style_images/<#IMG_DIR#>/folder_mime_types';
	}
}
		
//--------------------------------
// Import $INFO, now!
//--------------------------------
 
require ROOT_PATH."conf_global.php";

$ibforums = new info($INFO);

//--------------------------------
// The clocks a' tickin'
//--------------------------------

$Debug = new Debug;
$Debug->startTimer();

//--------------------------------
// Load up our database library
//--------------------------------
 
$INFO['sql_driver'] = ! $INFO['sql_driver'] ? 'mysql' : strtolower($INFO['sql_driver']);

require ( KERNEL_PATH.'class_db_'.$INFO['sql_driver'].".php" );

$DB = new db_driver;

$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->obj['sql_tbl_prefix']   = $INFO['sql_tbl_prefix'];
$DB->obj['query_cache_file'] = ROOT_PATH.'sources/sql/'.$INFO['sql_driver'].'_admin_queries.php';
$DB->obj['use_shutdown']     = 0;

//-----------------------------------
// Required vars?
//-----------------------------------

if ( is_array( $DB->connect_vars ) and count( $DB->connect_vars ) )
{
	foreach( $DB->connect_vars as $k => $v )
	{
		$DB->connect_vars[ $k ] = $INFO[ $k ];
	}
}

//--------------------------------
// Get a DB connection
//--------------------------------

$DB->connect();

//--------------------------------
// Make CONSTANT
//--------------------------------

define( 'SQL_PREFIX', $DB->obj['sql_tbl_prefix'] );
define( 'SQL_DRIVER', $INFO['sql_driver']        );

//===========================================================================
// Get cache...
//===========================================================================

$DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key IN ( 'settings', 'group_cache', 'systemvars', 'skin_id_cache', 'forum_cache', 'moderators', 'stats' )" ) );
$DB->simple_exec();

while ( $r = $DB->fetch_row() )
{
	if ( $r['cs_key'] == 'settings' and $r['cs_value'] )
	{
		$tmp = unserialize(stripslashes($r['cs_value']));
		
		if ( is_array( $tmp ) and count( $tmp ) )
		{
			foreach( $tmp as $k => $v )
			{
				$ibforums->vars[ $k ] = $v;
			}
		}
		
		unset( $tmp );
	}
	else
	{
		if ( $r['cs_array'] )
		{
			$ibforums->cache[ $r['cs_key'] ] = unserialize(stripslashes($r['cs_value']));
		}
		else
		{
			$ibforums->cache[ $r['cs_key'] ] = $r['cs_value'];
		}
	}
}

//--------------------------------
// Load up our classes (compiled into one package)
//--------------------------------
 
require ROOT_PATH."sources/functions.php";
require ROOT_PATH."sources/classes/class_session.php";
require ROOT_PATH."sources/classes/class_forums.php";
require KERNEL_PATH."class_converge.php";

$std     = new FUNC;
$forums  = new forum_functions();
$sess    = new session();

//--------------------------------
// Set up our vars $HTTP_POST_VARS['adsess']
//--------------------------------

$ibforums->input = $std->parse_incoming();

//--------------------------------
//  Initialize the FUNC
//--------------------------------

$std->FUNC_init();

//--------------------------------
//  Set converge
//--------------------------------

$ibforums->converge = new class_converge( $DB );

//--------------------------------
// Message in a bottle?
//--------------------------------

if ( $ibforums->input['messageinabottleacp'] )
{
	$ibforums->input['messageinabottleacp'] = $std->clean_evil_tags( $std->txt_UNhtmlspecialchars( urldecode($ibforums->input['messageinabottleacp']) ) );
	$ibforums->main_msg = $ibforums->input['messageinabottleacp'];
}

//--------------------------------
// Fix up base URLs
//--------------------------------

$ibforums->base_url = $ibforums->vars['board_url']."/admin.".$ibforums->vars['php_ext'].'?adsess='.$ibforums->input['adsess'];
$ibforums->skin_url = $ibforums->vars['board_url']."/skin_acp/IPB2_Standard";

//--------------------------------
// Set up cache path
//--------------------------------

if ( $ibforums->vars['ipb_cache_path'] )
{
	define( 'CACHE_PATH', $ibforums->vars['ipb_cache_path'] );
}
else
{
	define( 'CACHE_PATH', ROOT_PATH );
}


//--------------------------------
//  Upload dir?
//--------------------------------

$ibforums->vars['upload_dir'] = $ibforums->vars['upload_dir'] ? $ibforums->vars['upload_dir'] : ROOT_PATH.'uploads';

//--------------------------------
// Import $PAGES and $CATS
//--------------------------------
 
require ROOT_PATH."sources/admin/admin_pages.php";

//--------------------------------
// Import Admin Functions
//--------------------------------
 
require ROOT_PATH."sources/admin/admin_functions.php";

$ibforums->admin = new admin_functions();

//------------------------------------------------
// Sort out settings cookie
//------------------------------------------------

$ibforums->vars['menu']    = 0;
$ibforums->vars['tx']      = 80;
$ibforums->vars['ty']      = 40;
$ibforums->vars['preview'] = "";

//------------------------------------------------
// Load skin
//------------------------------------------------

$std->load_skin();
$std->load_template( 'skin_global' );

//--------------------------------
// Import Skinable elements
//--------------------------------
 
require ROOT_PATH."sources/admin/admin_skin.php";

$ibforums->adskin = new admin_skin();


//------------------------------------------------
// Admin.php Rules:
//
// No adsess number?
// -----------------
//
// Then we log into the admin CP
//
// Got adsess number?
// ------------------
//
// Then we check the cookie "ad_login" for a session key.
//
// If this session key matches the one stored in the admin_sessions
// table, then we check the data against the data stored in the 
// profiles table.
//
// The session key and ad_sess keys are generated each time we log in.
//
// If we don't have a valid adsess in the URL, then we ask for a log in.
//
//------------------------------------------------

$session_validated = 0;
$this_session      = array();

$validate_login = 0;

if ($ibforums->input['login'] != 'yes')
{

	if ( ! $ibforums->input['adsess'] )
	{
		//----------------------------------
		// No URL adsess found, lets log in.
		//----------------------------------
		
		do_login("No administration session found");
	}
	else
	{
		//----------------------------------
		// We have a URL adsess, lets verify...
		//----------------------------------
		
		$DB->query("SELECT * FROM ibf_admin_sessions WHERE session_id='".$ibforums->input['adsess']."'");
		
		$row = $DB->fetch_row();
		
		if ($row['session_id'] == "")
		{
			//----------------------------------
			// Fail-safe, no DB record found, lets log in..
			//----------------------------------
			
			do_login("Could not retrieve session record");
		}
		else if ($row['session_member_id'] == "")
		{
		
			//----------------------------------
			// No member ID is stored, log in!
			//----------------------------------
			
			do_login("Could not retrieve a valid member id");
			
		}
		else
		{
			//----------------------------------
			// Key is good, check the member details
			//----------------------------------
			
			$DB->query("SELECT m.*, g.* FROM ibf_members m, ibf_groups g WHERE id=".intval($row['session_member_id'])." and m.mgroup=g.g_id");
			
			$ibforums->member = $DB->fetch_row();
			
			//----------------------------------
			// Get perms
			//----------------------------------
			
			$sess->member     = $ibforums->member;
			$sess->build_group_permissions();
			$ibforums->member = $sess->member;
			
			if ($ibforums->member['id'] == "")
			{
				//----------------------------------
				// Ut-oh, no such member, log in!
				//----------------------------------
				
				do_login("Member ID invalid");
				
			}
			else
			{
				//----------------------------------
				// Member found, check passy
				//----------------------------------
				
				if ($row['session_member_login_key'] != $ibforums->member['member_login_key'])
				{
					//----------------------------------
					// Passys don't match..
					//----------------------------------
					
					do_login("Session member password mismatch");
					
				}
				else
				{
					//----------------------------------
					// Do we have admin access?
					//----------------------------------
					
					if ($ibforums->member['g_access_cp'] != 1)
					{
						do_login("You do not have access to the administrative CP");
					}
					else
					{
						$session_validated = 1;
						$this_session      = $row;
					}
				}
			}
		}
	}
}
else 
{
	//----------------------------------
	// We must have submitted the form
	// time to check some details.
	//----------------------------------
	
	$ibforums->input['username'] = str_replace( '|', '&#124;', $ibforums->input['username'] );
	
	if ( empty($ibforums->input['username']) )
	{
		do_login("You must enter a username before proceeding");
	}
	
	if ( empty($ibforums->input['password']) )
	{
		do_login("You must enter a password before proceeding");
	}
	
	//----------------------------------
	// Attempt to get the details from the
	// DB
	//----------------------------------
	
	$DB->cache_add_query( 'login_getmember', array( 'username' => strtolower($ibforums->input['username']) ) );
	$DB->cache_exec_query();
		
	$mem = $DB->fetch_row();
	
	//----------------------------------
	// Get perms
	//----------------------------------
	
	$sess->member = $mem;
	$sess->build_group_permissions();
	$mem          = $sess->member;
	
	if ( empty($mem['id']) )
	{
		do_login("Could not find a record matching that username, please check the spelling");
	}
	
	//----------------------------------
	// Load converge member
	//----------------------------------
	
	$ibforums->converge->converge_load_member($mem['email']);
		
	if ( ! $ibforums->converge->member['converge_id'] )
	{
		do_login("Could not find a record matching that username, please check the spelling");
	}
	
	//----------------------------------
	// Check converge pass
	//----------------------------------
	
	$pass = md5( $ibforums->input['password'] );
	
	//------------------------------
	// Check password...
	//------------------------------
	
	if ( $ibforums->converge->converge_authenticate_member( $pass ) != TRUE )
	{
		do_login("The password you entered is not correct");
	}
	else
	{
		if ($mem['g_access_cp'] != 1)
		{
			do_login("You do not have access to the administrative CP");
		}
		else
		{
			
			//----------------------------------
			// Fix up query string...
			//----------------------------------
			
			$extra_query = "";
			
			if ( $_POST['qstring'] )
			{
				$extra_query = $_POST['qstring'];
				$extra_query = str_replace( "{$ibforums->vars['board_url']}"           , "" , $extra_query );
				$extra_query = preg_replace( "!/?admin\.{$ibforums->vars['php_ext']}!i", "" , $extra_query );
				$extra_query = preg_replace( "!^\?!"                                   , "" , $extra_query );
				$extra_query = preg_replace( "!adsess=(\w){32}!"                       , "" , $extra_query );
				$extra_query = preg_replace( "!s=(\w){32}!"                            , "" , $extra_query );
			}
			
			//----------------------------------
			// All is good, rejoice as we set a
			// session for this user
			//----------------------------------
			
			$sess_id = md5( uniqid( microtime() ) );
			
			$db_string = $DB->compile_db_insert_string( array (
																'session_id'                => $sess_id,
																'session_ip_address'        => $ibforums->input['IP_ADDRESS'],
																'session_member_name'       => $mem['name'],
																'session_member_id'         => $mem['id'],
																'session_member_login_key'  => $mem['member_login_key'],
																'session_location'          => 'index',
																'session_log_in_time'       => time(),
																'session_running_time'      => time(),
													  )        );
													  
			$DB->query("INSERT INTO ibf_admin_sessions (".$db_string['FIELD_NAMES'].") VALUES (".$db_string['FIELD_VALUES'].")");
		
			$ibforums->input['AD_SESS'] = $sess_id;
			
			// Print the "well done page"
			
			$ibforums->admin->page_title = "Log in successful";
			
			$ibforums->admin->page_detail = "Taking you to the administrative control panel";
			
			$ibforums->html .= $ibforums->adskin->start_table("Proceed");
			
			$ibforums->html .= "<tr><td id='tdrow1'><meta http-equiv='refresh' content='2; url=".$ibforums->vars['board_url']."/admin.".$ibforums->vars['php_ext']."?printframes=1&adsess=".$ibforums->input['AD_SESS']."&{$extra_query}'><a href='".$ibforums->vars['board_url']."/admin.".$ibforums->vars['php_ext']."?printframes=1&adsess=".$ibforums->input['AD_SESS']."&{$extra_query}'>( Click here if you do not wish to wait )</a></td></tr>";
			
			$ibforums->html .= $ibforums->adskin->end_table();
			
			$ibforums->admin->output();
		
		}
		
	}
		
}


//----------------------------------
// Ok, so far so good. If we have a 
// validate session, check the running
// time. if it's older than 2 hours,
// ask for a log in
//----------------------------------


if ($session_validated == 1)
{
	if ( $this_session['session_running_time'] < ( time() - 60*60*2) )
	{
		$session_validated = 0;
		do_login("This administration session has expired");
	}
	
	//------------------------------
	// Are we checking IP's?
	//------------------------------
	
	else if ($check_ip == 1)
	{
		if ($this_session['session_ip_address'] != $ibforums->input['IP_ADDRESS'])
		{
			$session_validated = 0;
			do_login("Your current IP address does not match the one in our records");
		}
	}
}

if ($session_validated == 1 )
{
	//------------------------------
	// If we get this far, we're good to go..
	//------------------------------
	
	$ibforums->input['AD_SESS'] = $ibforums->input['adsess'];
	
	//------------------------------
	// Lets update the sessions table:
	//------------------------------
	
	$DB->do_update( 'admin_sessions',
					array( 'session_running_time' => time(),
						   'session_location'     => $ibforums->input['act']
						 ),
					       'session_member_id='.intval($ibforums->member['id'])." and session_id='".$ibforums->input['AD_SESS']."'" );
		
	do_admin_stuff();
	
}
else
{
	//------------------------------
	// Session is not validated...
	//------------------------------
	
	do_login("Session not validated - please attempt to log in again");
	
}



function do_login($message="")
{
	global $ibforums, $DB, $ADMIN, $SKIN, $std;
	
	//-------------------------------------------------------
	// Remove all out of date sessions, like a good boy. Woof.
	//-------------------------------------------------------
	
	$cut_off_stamp = time() - 60*60*2;
	
	$DB->query("DELETE FROM ibf_admin_sessions WHERE session_log_in_time < $cut_off_stamp");
	
	//+------------------------------------------------------
	
	$ibforums->admin->page_detail = "You must have administrative access to successfully log into the Invision Board Admin CP.<br>Please enter your forums username and password below";
	
	if ($message != "")
	{
		$ibforums->admin->page_detail .= "<br><br><span style='color:red;font-weight:bold'>$message</span>";
	}
	
	$ibforums->html .= "<script language='javascript'>
					  <!--
					  	if (top.location != self.location) { top.location = self.location }
					  //-->
					 </script>
					 ";
	//+------------------------------------------------------
	//| SEMI-AUTO Log in ma-thingy?
	//+------------------------------------------------------
	
	$name  = "";
	$extra = "";
					 
	$mid = intval( $std->my_getcookie('member_id') );
	
	if ( $mid > 0 )
	{
		$DB->query("SELECT m.id, m.name, m.mgroup, g.g_access_cp FROM ibf_members m, ibf_groups g WHERE m.id=$mid AND g.g_id=m.mgroup AND g.g_access_cp=1");
		
		if ( $r = $DB->fetch_row() )
		{
			$name  = $r['name'];
			$extra = 'onload="document.theAdminForm.password.focus();"';
		}
	}
	
	//+------------------------------------------------------
	//| SHW DA FRM (txt msg stylee)
	//+------------------------------------------------------
	
	$qs = str_replace( '&amp;', '&', $std->clean_value(urldecode($_SERVER['QUERY_STRING'])) );
	$qs = str_replace( 'adsess=', 'old_adsess=', $qs );
	$qs = str_replace( 'act=menu', '', $qs );
	
	$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array('login'  , 'yes'),
															 2 => array('qstring', $qs  )
													)      );
	
	$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
	$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
	
	$ibforums->html .= $ibforums->adskin->start_table( "Verification Required" );
	
	$ibforums->html .= $ibforums->adskin->add_td_row( array( "Your Forums Username:",
																  "<input type='text' style='width:100%' name='username' value='$name'>",
														 )      );
		
	$ibforums->html .= $ibforums->adskin->add_td_row( array( "Your Forums Password:",
																  "<input type='password' style='width:100%' name='password' value=''>",
														 )      );
									 
	$ibforums->html .= $ibforums->adskin->end_form("Log in");
	
	$ibforums->html .= $ibforums->adskin->end_table();
	
	$ibforums->adskin->top_extra = $extra;
	
	$ibforums->admin->no_jump = 1;
		
	$ibforums->admin->output();

}

//------------------------------------------------------
// Core admin look-up
//------------------------------------------------------

function do_admin_stuff()
{
	global $ibforums, $DB, $std, $forums;
	
	//------------------------------------------------------
	// Require global words.
	//------------------------------------------------------
	
	 if ($ibforums->vars['default_language'] == "")
	 {
		 $ibforums->vars['default_language'] = 'en';
	 }
	 
	 $ibforums->lang_id = $ibforums->member['language'] ? $ibforums->member['language'] : $ibforums->vars['default_language'];
	 
	 if ( ($ibforums->lang_id != $ibforums->vars['default_language']) and (! is_dir( ROOT_PATH."lang/".$ibforums->lang_id ) ) )
	 {
		 $ibforums->lang_id = $ibforums->vars['default_language'];
	 }
			 
	 $ibforums->lang = $std->load_words($ibforums->lang, 'lang_global', $ibforums->lang_id);

	//------------------------------------------------------
	// Require..
	//------------------------------------------------------
	
	require_once( ROOT_PATH.'sources/admin/admin_cache_functions.php' );
	$ibforums->cache_func = new admin_cache_functions();
	
	//------------------------------------------------------
	// Reg'd?
	//------------------------------------------------------
	
	if ( $ibforums->vars['ipb_reg_number'] )
	{
		list( $a, $b, $c, $d, $e ) = explode( '-', $ibforums->vars['ipb_reg_number'] );
		
		if ( strlen($e) > 9 )
		{
			if ( time() > $e )
			{
				require_once( ROOT_PATH.'sources/admin/ad_settings.php' );
				$settings = new ad_settings();
				$DB->do_update( 'conf_settings', array( 'conf_value' => '' ), 'conf_key="ipb_reg_number"' );
				$settings->setting_rebuildcache();
			}
		}
	}

	//------------------------------------------------------
	//  What do you want to require today?
	//------------------------------------------------------
	
	$choice = array(
					 "idx"       => array( "doframes"        , 'doframes' ),
					 "menu"      => array( "menu"            , 'menu' ),
					 "index"     => array( "index"           , 'index' ),
					 "cat"       => array( "categories"      , 'categories' ),
					 "forum"     => array( "forums"          , 'forums' ),
					 "mem"       => array( "member"          , 'member' ),
					 'group'     => array( "groups"          , 'groups' ),
					 'mod'       => array( 'moderator'       , 'moderator' ),
					 'op'        => array( 'settings'        , 'settings' ),
					 'help'      => array( 'help'            , 'help' ),
					 'skin'      => array( 'skins'           , 'skins' ),
					 'wrap'      => array( 'wrappers'        , 'wrappers' ),
					 'style'     => array( 'stylesheets'     , 'stylesheets' ),
					 'image'     => array( 'imagemacros'     , 'imagemacros' ),
					 'sets'      => array( 'stylesets'       , 'stylesets' ),
					 'templ'     => array( 'templates'       , 'templates' ),
					 'rtempl'    => array( 'remote_template' , 'remote_template' ),
					 'lang'      => array( 'languages'       , 'languages' ),
					 'import'    => array( 'skin_import'     , 'skin_import' ),
					 'modlog'    => array( 'modlogs'         , 'modlogs' ),
					 'field'     => array( 'profilefields'   , 'profilefields' ),
					 'stats'     => array( "statistics"      , 'statistics' ),
					 'quickhelp' => array( "quickhelp"       , 'quickhelp' ),
					 'adminlog'  => array( "adminlogs"       , 'adminlogs' ),
					 'ips'       => array( 'ips'             , 'ips' ),
					 'mysql'     => array( 'mysql'           , 'mysql' ),
					 'pin'       => array( 'plugins'         , 'plugins' ),
					 'emaillog'  => array( 'emaillogs'       , 'emaillogs' ),
					 'multimod'  => array( 'multi_moderate'  , 'multi_moderate' ),
					 'prefs'     => array( "prefs"           , 'prefs' ),
					 'spiderlog' => array( "spiderlogs"      , 'spiderlogs' ),
					 'warnlog'   => array( "warnlogs"        , 'warnlogs' ),
					 'csite'     => array( 'ad_dynamiclite'  , 'ad_dynamiclite' ),
					 'msubs'     => array( 'subsmanager'     , 'subsmanager' ),
					 'mtools'    => array( 'member_tools'    , 'member_tools' ),
					 'skinfix'   => array( 'skinfix'         , 'skinfix' ),
					 'skintools' => array( 'skintools'       , 'skintools'),
					 'task'      => array( 'task_manager'    , 'task_manager' ),
					 'admin'     => array( 'administration'  , 'administration' ),
					 'rebuild'   => array( 'rebuild'         , 'rebuild'        ),
					 'attach'    => array( 'attachments'     , 'attachments'    ),
					 'postoffice'=> array( 'postoffice'      , 'postoffice'     ),
					 'emailerror'=> array( 'emailerror'      , 'emailerror'     ),
					 'gallery'   => array( 'gallery'         , 'gallery' ),
					 'blog'		 => array( 'blog'	         , 'blog' ),
				   );
	
					
	//---------------------------------------------------
	// Check to make sure the array key exits..
	//---------------------------------------------------
	
	if (! isset($choice[ $ibforums->input['act'] ][0]) )
	{
		$ibforums->input['act'] = 'idx';
	}
	
	// Require and run
	
	if ($ibforums->input['act'] == 'idx' or $ibforums->input['printframes'] == 1)
	{
		print $ibforums->adskin->frame_set();
		exit;
	}
	else if ($ibforums->input['act'] == 'menu')
	{
		$ibforums->admin->menu();
	}
	else if ($ibforums->input['act'] == 'csite')
	{
		require ROOT_PATH."sources/dynamiclite/ad_dynamiclite.php";
	}
	else
	{
		require ROOT_PATH."sources/admin/ad_".$choice[ $ibforums->input['act'] ][0].".php";
		
		$choice[ $ibforums->input['act'] ][1] = 'ad_'.$choice[ $ibforums->input['act'] ][1];
		
		$runme = new $choice[ $ibforums->input['act'] ][1];
		$runme->auto_run();
	}
	
}

//+-------------------------------------------------
// Skin emergency mode...
//+-------------------------------------------------

function skin_emergency()
{
	global $DB, $ibforums, $std;
	
	if ( $_GET['skinrebuild'] == 1 )
	{
		print "Attempted to rebuild the skins and failed.<br />Please contact technical support for more assistance.";
		exit();
	}
	
	//-----------------------------------------
	// Rebuild ID cache..
	//-----------------------------------------
	
	require_once( ROOT_PATH.'sources/admin/admin_cache_functions.php' );
    $adcache = new admin_cache_functions();
	$ibforums->cache['skin_id_cache'] = $adcache->_rebuild_skin_id_cache();
	
	//-----------------------------------------
	// Attempt to recache the default skin
	//-----------------------------------------
	
	foreach( $ibforums->cache['skin_id_cache'] as $sid => $data )
	{
		if ( $data['set_default'] )
		{
			$default_skin = $data['set_skin_set_id'];
		}
	}
	
	//-----------------------------------------
	// Load library
	//-----------------------------------------
	
	require_once( ROOT_PATH.'sources/admin/admin_cache_functions.php' );
	$ibforums->cache_func = new admin_cache_functions();
	
	$ibforums->cache_func->_rebuild_all_caches( array($default_skin) );
	
	//-----------------------------------------
	// Try to turn on safe mode
	//-----------------------------------------
	
	if ( ! @file_exists( CACHE_PATH.'skin_cache/cacheid_'.$default_skin.'/skin_global.php' ) )
	{
		if ( $ibforums->vars['safe_mode_skins'] != 1 )
		{
			$DB->do_update( "conf_settings", array( 'conf_value' => 1 ), "conf_key='safe_mode_skins'" );
			
			require_once( ROOT_PATH.'sources/admin/ad_settings.php' );
			$adsettings = new ad_settings();
			$adsettings->setting_rebuildcache();
		}
	}
	
	//-----------------------------------------
	// Update panic message
	//-----------------------------------------
	
	$DB->simple_exec_query( array( "delete" => "cache_store", "where" => "cs_key='skinpanic'" ) );
	$DB->do_insert( 'cache_store', array( 'cs_value' => 'rebuildemergency', 'cs_key' => 'skinpanic' ) );
	
	$std->boink_it( $ibforums->base_url.'&skinrebuild=1' );	
}

//+-------------------------------------------------
// GLOBAL ROUTINES
//+-------------------------------------------------

function fatal_error($message="", $help="") {
	echo("$message<br><br>$help");
	exit;
}

//+-------------------------------------------------
// Custom error handler
//+-------------------------------------------------

function my_error_handler( $errno, $errstr, $errfile, $errline )
{
	// Did we turn off errors with @?
	
	if ( ! error_reporting() )
	{
		return;
	}
	
	$errfile = str_replace( @getcwd(), "", $errfile );
	
	switch ($errno)
	{
  		case E_ERROR:
   			echo "<b>FATAL</b> [$errno] $errstr<br />\n";
   			echo "  Fatal error in line $errline of file $errfile";
   			echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
   			echo "Aborting...<br />\n";
   			exit(1);
   		break;
  		case E_WARNING:
  			if ( strstr( $errstr, 'load_template(./skin_cache/cacheid_' ) )
  			{
  				skin_emergency();
  			}
  			else
  			{
   				echo "<b>IPB WARNING</b> [$errno] $errstr (Line: $errline of $errfile)<br />\n";
   			}
   		break;
 		default:
   			//echo "Unkown error type: [$errno] $errstr<br />\n";
   		break;
	}
}

?>