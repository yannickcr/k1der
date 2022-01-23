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
|   > IPB UPGRADE LOADER
|   > Script written by Matt Mecham
|   > Date started: 21st April 2004
|   > Interesting fact: Radiohead rock (still)
+--------------------------------------------------------------------------
*/

//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------

define( 'THIS_PATH'  , './'  );
define( 'ROOT_PATH'  , "../" );
define( 'KERNEL_PATH', ROOT_PATH.'ips_kernel/' );
define( 'CACHE_PATH' , ROOT_PATH );

//-----------------------------------------------
// NO USER EDITABLE SECTIONS BELOW
//-----------------------------------------------

define ( 'IN_IPB', 0 );
define ( 'IN_DEV', 0 );
define ( 'SAFE_MODE_ON', 0 );

// You really don't want to turn this on

define( 'TRIAL_VERSION', 0 );

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

//===========================================================================
// MAIN PROGRAM
//===========================================================================

if ( TRIAL_VERSION )
{
	print "This feature is disabled in the trial version.";
	exit();
}

//--------------------------------
// Import $INFO, now!
//--------------------------------

$INFO = array();

require ROOT_PATH."conf_global.php";

//--------------------------------
// The clocks a' tickin'
//--------------------------------

$Debug = new Debug;
$Debug->startTimer();

//--------------------------------
// Load the DB driver and such
//--------------------------------

$INFO['sql_driver'] = !$INFO['sql_driver'] ? 'mysql' : $INFO['sql_driver'];
$INFO['sql_driver'] = strtolower($INFO['sql_driver']);

if ( ! @file_exists( ROOT_PATH.'sources/sql/'.$INFO['sql_driver'].'_admin_queries.php' ) )
{
	print "Cannot find the file: ".ROOT_PATH.'sources/sql/'.$INFO['sql_driver']."_admin_queries.php - make sure the file exists before continuing.";
	exit();
}

require ( KERNEL_PATH.'class_db_'.strtolower($INFO['sql_driver']).".php" );

$DB = new db_driver;

$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->obj['sql_tbl_prefix']   = $INFO['sql_tbl_prefix'];
$DB->obj['query_cache_file'] = ROOT_PATH.'sources/sql/'.$INFO['sql_driver'].'_admin_queries.php';
$DB->obj['use_shutdown']     = 0;
$DB->obj['debug']            = 0;

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
// Wrap it all up in a nice easy to
// transport super class
//--------------------------------

$ibforums = new info();

//--------------------------------
// Require our global functions
//--------------------------------

require ROOT_PATH."sources/functions.php";
require KERNEL_PATH."class_converge.php";
require THIS_PATH."core/functions.php";
require THIS_PATH."core/template.php";

$std    = new FUNC;

//--------------------------------
// Get settings
//--------------------------------

$DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key IN ('settings')" ) );
$DB->simple_exec();

while ( $r = $DB->fetch_row() )
{
	if ( $r['cs_key'] == 'settings' )
	{
		$tmp = unserialize( $std->txt_safeslashes($r['cs_value']) );

		if ( is_array( $tmp ) and count( $tmp ) )
		{
			foreach( $tmp as $k => $v )
			{
				$ibforums->vars[ $k ] = stripslashes($v);
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
//  Set up our vars
//--------------------------------

$ibforums->input    = $std->parse_incoming();
$ibforums->core     = new core_functions();
$ibforums->template = new template();

//--------------------------------
// Get dir contents
//--------------------------------

$ibforums->dir_contents = $ibforums->core->get_dir_structure();

//--------------------------------
// Get DB contents
//--------------------------------

$ibforums->db_contents  = $ibforums->core->get_db_structure();

//--------------------------------
// Get latest ID...
//--------------------------------

$ibforums->last_poss_id = array_shift( array_reverse($ibforums->dir_contents) );

//--------------------------------
// Get datafile
//--------------------------------

if ( ! $ibforums->last_poss_id )
{
	$ibforums->core->login_screen( "An error has occured, we are unable to determine the current version or if there are any required upgrade files left to run" );
}

if ( file_exists( THIS_PATH.'upg_'.$ibforums->last_poss_id.'/version_history.php' ) )
{
	require_once( THIS_PATH.'upg_'.$ibforums->last_poss_id.'/version_history.php' );

	$ibforums->versions = $import_versions;
}
else
{
	$ibforums->core->login_screen( "Could not locate the required upgrade script: 'upg_{$ibforums->last_poss_id}/version_history.php'");
}

//--------------------------------
// Version logic check
//--------------------------------

$ibforums->core->get_version_latest();

if ( ! $ibforums->current_version )
{
	$ibforums->core->login_screen( "An error has occured, we are unable to determine the current version or if there are any required upgrade files left to run" );
}

if ( ! $ibforums->input['act'] )
{
	if ( $ibforums->current_version >= $ibforums->current_upgrade )
	{
		$ibforums->core->login_screen( "This IPB installation is up-to-date and there are no more upgrades required" );
	}
}

//--------------------------------
//  Get our loverly member
//--------------------------------

$ibforums->member = $ibforums->core->get_member();

//--------------------------------
//  Set converge
//--------------------------------

$ibforums->converge = new class_converge( $DB );

//--------------------------------
// Log in?
//--------------------------------

if ( $ibforums->input['act'] != 'login' )
{
	if ( ! $ibforums->member['id'] )
	{
		$ibforums->core->login_screen("You do not have access to this upgrade system.");
	}

	if ( $std->return_md5_check() != $ibforums->securekey )
	{
		$ibforums->core->login_screen("You do not have access to this upgrade system!");
	}

	if ( ! $ibforums->member['g_access_cp'] )
	{
		$ibforums->core->login_screen('You must be an admin to access this upgrade script');
	}
}
else
{
	//----------------------------------
	// We must have submitted the form
	// time to check some details.
	//----------------------------------

	if ( empty($ibforums->input['username']) )
	{
		$ibforums->core->login_screen("You must enter a username before proceeding");
	}

	if ( empty($ibforums->input['password']) )
	{
		$ibforums->core->login_screen("You must enter a password before proceeding");
	}

	//----------------------------------
	// Attempt to get the details from the
	// DB
	//----------------------------------

	$DB->query("SELECT m.*, g.* FROM ibf_members m, ibf_groups g WHERE LOWER(name)='".strtolower($ibforums->input['username'])."' and m.mgroup=g.g_id");

	$mem = $DB->fetch_row();

	//----------------------------------
	// Get perms
	//----------------------------------

	if ( empty($mem['id']) )
	{
		$ibforums->core->login_screen("Could not find a record matching that username, please check the spelling");
	}

	//----------------------------------
	// Load converge member
	//----------------------------------

	$ibforums->converge->converge_load_member($mem['email']);

	if ( ! $ibforums->converge->member['converge_id'] )
	{
		$ibforums->core->login_screen("Could not find a record matching that username, please check the spelling");
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
		$ibforums->core->login_screen("The password you entered is not correct");
	}
	else
	{
		if ($mem['g_access_cp'] != 1)
		{
			$ibforums->core->login_screen("You do not have access to the administrative CP");
		}
		else
		{

			//----------------------------------
			// All is good, bounce onwards...
			//----------------------------------

			$ibforums->member = $mem;

			$ibforums->core->redirect( "index.php?act=work&loginkey={$mem['member_login_key']}&securekey=".$std->return_md5_check()."&mid={$mem['id']}", "Thanks for logging in...." );
		}
	}
}

//===========================================================================
// MAIN LOGIC...
//===========================================================================

//--------------------------------
// Get and run upgrade script
//--------------------------------

if ( ! in_array( $ibforums->input['act'], $ibforums->safe_act ) )
{
	if ( file_exists( THIS_PATH.'upg_'.$ibforums->current_upgrade.'/version_upgrade.php' ) )
	{
		require_once( THIS_PATH.'upg_'.$ibforums->current_upgrade.'/version_upgrade.php' );
		$runme = new version_upgrade();
	}
	else
	{
		$ibforums->core->login_screen( "Could not locate the required upgrade script: 'upg_{$ibforums->current_upgrade}/version_upgrade.php'");
	}
}

// Module(work) -> done -> settings -> recache -> templates -> templatesrecache -> finish

switch ($ibforums->input['act'])
{
	case 'recache':
		$ibforums->core->rebuild_caches();
		break;
	case 'templates':
		$ibforums->core->rebuild_templates();
		break;
	case 'templatescache':
		$ibforums->core->rebuild_templates_cache();
		break;
	case 'settings':
		$ibforums->core->rebuild_settings();
		break;
	case 'finish':
		$ibforums->core->upgrade_complete();
		break;
	case 'done':
		$ibforums->core->module_complete();
		break;
	case 'work':
		$runme->auto_run();
		break;
	default:
		$ibforums->core->login_screen();
		break;
}

/*-------------------------------------------------------------------------*/
// Major fatal and possibly dangerous error...
/*-------------------------------------------------------------------------*/

function fatal_error($msg="")
{
	print $msg;
	exit();
}



/*-------------------------------------------------------------------------*/
// DEBUG CLASS
/*-------------------------------------------------------------------------*/

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

/*-------------------------------------------------------------------------*/
// INFO CLASS
/*-------------------------------------------------------------------------*/

class info {

	var $member          = array();
	var $input           = array();
	var $base_url        = "";
	var $vars            = "";
	var $upgrade_history = array();
	var $current_version = '';
    var $loginkey        = '';
    var $securekey       = '';
    var $current_action  = '';
    var $safe_act        = array( 'recache', 'templates', 'templatescache', 'settings', 'finish' );

	function info()
	{
		global $sess, $std, $DB, $INFO;

		$this->vars = &$INFO;
	}
}


?>