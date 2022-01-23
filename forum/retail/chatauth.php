<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > IPB CHAT 2004 INTEGRATION
|   > Script written by Matt Mecham
|   > Date started: 20th April 2004
|   > Interesting fact: Radiohead rock
|   > Which Radiohead track features the words "In a city of the future"?
+--------------------------------------------------------------------------
*/

//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------

define( 'ROOT_PATH'  , "../" );
define( 'KERNEL_PATH', ROOT_PATH.'ips_kernel/' );

//-----------------------------------------------
// NO USER EDITABLE SECTIONS BELOW
//-----------------------------------------------

define ( 'IN_IPB', 0 );
define ( 'IN_DEV', 0 );

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

//===========================================================================
// DEBUG CLASS
//===========================================================================

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

//===========================================================================
// INFO CLASS
//===========================================================================

class info {

	var $member       = array();
	var $input        = array();
	var $session_id   = "";
	var $base_url     = "";
	var $vars         = "";
	var $lang_id      = "en";
	var $skin         = "";
	var $lang         = "";
	var $server_load  = 0;

	function info()
	{
		global $sess, $std, $DB, $INFO;

		$this->vars = &$INFO;
	}
}

//===========================================================================
// MAIN PROGRAM
//===========================================================================

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

require ( KERNEL_PATH.'class_db_'.strtolower($INFO['sql_driver']).".php" );

$DB = new db_driver;

$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->obj['sql_tbl_prefix']   = $INFO['sql_tbl_prefix'];
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

$std    = new FUNC;

//--------------------------------
//  Set up our vars
//--------------------------------

$ibforums->input = $std->parse_incoming();

//--------------------------------
//  Set converge
//--------------------------------

$ibforums->converge = new class_converge( $DB );

//===========================================================================
// Get cache...
//===========================================================================

$DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key IN ( ".$choice[ strtolower($ibforums->input['act']) ][2]."'banfilters', 'settings', 'group_cache', 'systemvars', 'skin_id_cache', 'forum_cache', 'moderators', 'stats', 'languages' )" ) );
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

//===========================================================================
// AUTHORIZE...
//===========================================================================

$reply_success = 'Result=Success';
$reply_nouser  = 'Result=UserNotFound';
$reply_nopass  = 'Result=WrongPassword';
$reply_error   = 'Result=Error';

$in_user       = $std->clean_value(urldecode(trim($_GET['user'])));
$in_pass       = $std->clean_value(urldecode(trim($_GET['pass'])));
$in_cookie     = $std->clean_value(urldecode(trim($_GET['cookie'])));
$access_groups = $ibforums->vars['chat04_access_groups'];
$in_md5_pass   = "";
$nametmp       = "";
$in_userid     = 0;
$query         = 0;

if ( preg_match( "/^md5pass\((.+?)\)(.+?)$/", $in_pass, $match ) )
{
	$in_md5_pass = $match[1];
	$in_userid   = intval($match[2]);
}

//----------------------------------------------
// Did we pass a user ID?
//----------------------------------------------

if ( $in_userid )
{
	$query   = "m.id=".$in_userid;
	$in_user = 1;
}
else
{
	$in_user = str_replace( '-', '_', $in_user );
	$timeoff = time() - 3600;
	$query   = "m.name LIKE '".addslashes($in_user)."' AND last_activity > $timeoff";
}

//----------------------------------------------
// Continue..
//----------------------------------------------

if ( $in_user and ! $in_pass )
{
	show_message( $reply_nopass );
	//## EXIT ##
}

if ( $in_user and $in_pass )
{
	//------------------------------------------
	// Attempt to get member...
	//------------------------------------------

	$DB->query("SELECT m.mgroup, m.name, m.id, c.*  FROM ibf_members m
				LEFT JOIN ibf_members_converge c ON (m.id=c.converge_id)
				WHERE $query");

	$member = $DB->fetch_row();

	if ( ! $member['id'] )
	{
		//--------------------------------------
		// Guest...
		//--------------------------------------

		test_for_guest();
	}

	//------------------------------------------
	// Test for MD5 (future proof)
	//------------------------------------------

	if ( ! $in_md5_pass )
	{
		$in_md5_pass = md5( md5( $member['converge_pass_salt'] ) . md5($in_pass) );
	}

	//------------------------------------------
	// PASSWORD?
	//------------------------------------------

	if ( $in_md5_pass == $member['converge_pass_hash'] )
	{
		//--------------------------------------
		// Check for access
		//--------------------------------------

		if ( ! preg_match( "/(^|,)".$member['mgroup']."(,|$)/", $access_groups ) )
		{
			show_message( $reply_error );
			//## EXIT ##
		}
		else
		{
			show_message( $reply_success );
			//## EXIT ##
		}
	}
	else
	{
		show_message( $reply_nopass );
		//## EXIT ##
	}
}
else
{
	//------------------------------------------
	// Guest...
	//------------------------------------------

	test_for_guest();
}

//===========================================================================
// YES TO GUEST OR NO TO PASS GO
//===========================================================================

function test_for_guest()
{
	global $reply_nouser, $reply_error;

	if ( $ibforums->vars['chat04_allow_guest'] )
	{
		show_message( $reply_nouser );
		//## EXIT ##
	}
	else
	{
		show_message( $reply_error );
		//## EXIT ##
	}
}

//===========================================================================
// SHOW MESSAGE
//===========================================================================

function show_message($msg="Result=Error")
{
	@flush();
	echo $msg;
	exit;
}




?>