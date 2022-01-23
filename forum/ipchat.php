<?

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.0
|   ========================================
|   by Matthew Mecham
|   (c) 2004 Invision Power Services, Inc
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionpower.com
+---------------------------------------------------------------------------
|
|   > IP Chat => IPB Bridge Script
|   > Script written by Matt Mecham
|   > Date started: 17th February 2003 (Updated: 1 July 2004)
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------

// Root path

define( 'ROOT_PATH'  , "./" );
define( 'KERNEL_PATH', ROOT_PATH.'ips_kernel/' );

$CACHE     = array();

//----------------------------------------------
// END OF USER EDITABLE COMPONENTS
//---------------------------------------------

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

require ROOT_PATH."conf_global.php";

define( 'DENIED', 0 );
define( 'ACCESS', 1 );
define( 'ADMIN' , 2 );

//--------------------------------
// Load the DB driver and such
//--------------------------------

$INFO['sql_driver'] = ! $INFO['sql_driver'] ? 'mysql' : strtolower($INFO['sql_driver']);

require ( KERNEL_PATH.'class_db_'.$INFO['sql_driver'].".php" );

$DB = new db_driver;

$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->obj['sql_tbl_prefix']   = $INFO['sql_tbl_prefix'];
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

$DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key IN ( 'settings', 'group_cache', 'systemvars' )" ) );
$DB->simple_exec();

while ( $r = $DB->fetch_row() )
{
	if ( $r['cs_key'] == 'settings' )
	{
		$tmp = unserialize(stripslashes($r['cs_value']));
		
		if ( is_array( $tmp ) and count( $tmp ) )
		{
			foreach( $tmp as $k => $v )
			{
				$INFO[ $k ] = $v;
			}
		}
		
		unset( $tmp );
	}
	else
	{
		if ( $r['cs_array'] )
		{
			$CACHE[ $r['cs_key'] ] = unserialize(stripslashes($r['cs_value']));
		}
		else
		{
			$CACHE[ $r['cs_key'] ] = $r['cs_value'];
		}
	}
}

$allowed_groups        = $INFO['chat_admin_groups'];
$access_groups         = $INFO['chat_access_groups'];
$autologin             = 0;
$allow_guest_access    = strstr( ','.$INFO['chat_access_groups'].',', ','.$INFO['guest_group'].',' ) ? ACCESS : DENIED;

// Stupid PHP changing it's mind on HTTP args

$username  = $_GET['username']  != "" ? $_GET['username'] : $HTTP_GET_VARS['username'];
$password  = $_GET['password']  != "" ? $_GET['password'] : $HTTP_GET_VARS['password'];
$ip        = $_GET['ip']        != "" ? $_GET['ip']       : $HTTP_GET_VARS['ip'];

//----------------------------------------------
// Test for autologin.
//----------------------------------------------

if ( preg_match( "/^(?:[0-9a-z]){32}$/", $password ) )
{
	$autologin = 1;
}


// Remove URL encoding (%20, etc)

$username = clean_value(urldecode(trim($username)));
$password = clean_value(urldecode(trim($password)));
$ip       = clean_value(urldecode(trim($ip)));

//----------------------------------------------
// Main code
//----------------------------------------------

// Start off with the lowest accessibility

$output_int  = $allow_guest_access;
$output_name = "";


//------------------------------
// Attempt to find the user
//------------------------------

$DB->query( "SELECT m.mgroup, m.name, m.id, c.*  FROM ".SQL_PREFIX."members m
			 LEFT JOIN ".SQL_PREFIX."members_converge c ON (m.id=c.converge_id)
			 WHERE m.name='".$DB->add_slashes($username)."'" );

						
if ( ! $member = $DB->fetch_row() )
{
	die_nice();
	
	//-- script exits --//
}


if ( ! $member['id'] )
{
	// No member found - allow guest access?
	
	die_nice($allow_guest_access);
	
	//-- script exits --//
}


//------------------------------
// Update passy
//------------------------------

if ( ! $autologin )
{
	$md5_password = md5( md5( $member['converge_pass_salt'] ) . md5($password) );
}
else
{
	$md5_password = $password;
}

//------------------------------
// Check password - member exists
//------------------------------

if ( $password != "" )
{
	// Password was entered..
	
	if ( $md5_password != $member['converge_pass_hash'] )
	{
		// Password incorrect..
		
		die_nice();
		
		//-- script exits --//
	}
	else
	{
		$output_int = ACCESS;
	}
}
else
{
	// No password entered - die!
	// Do not allow guest access on reg. name
	
	die_nice();
		
	//-- script exits --//
}


//------------------------------
// Do we have any access?
//------------------------------


if ( ! preg_match( "/(^|,)".$member['mgroup']."(,|$)/", $access_groups ) )
{
	die_nice();
}

//------------------------------
// Do we have admin access?
//------------------------------

if ( preg_match( "/(^|,)".$member['mgroup']."(,|$)/", $allowed_groups ) )
{
	$output_int = ADMIN;
}


//------------------------------
// Spill the beans
//------------------------------

print $output_int;

exit();
	 
	 
function die_nice( $access=0 )
{
	// Simply error out silently, showing guest access only for the user
	@mysql_close();
	print $access;
	exit();
}

//------------------------------
// Var cleaner
//------------------------------

function clean_value($val)
{
    global $INFO;
    
	if ($val == "")
	{
		return "";
	}
	
	$val = str_replace( "&#032;", " ", $val );
	
	if ( $INFO['strip_space_chr'] )
	{
		$val = str_replace( chr(0xCA), "", $val );  //Remove sneaky spaces
	}
	
	$val = str_replace( "&"            , "&amp;"         , $val );
	$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
	$val = str_replace( "-->"          , "--&#62;"       , $val );
	$val = preg_replace( "/<script/i"  , "&#60;script"   , $val );
	$val = str_replace( ">"            , "&gt;"          , $val );
	$val = str_replace( "<"            , "&lt;"          , $val );
	$val = str_replace( "\""           , "&quot;"        , $val );
	$val = preg_replace( "/\n/"        , "<br>"          , $val ); // Convert literal newlines
	$val = preg_replace( "/\\\$/"      , "&#036;"        , $val );
	$val = preg_replace( "/\r/"        , ""              , $val ); // Remove literal carriage returns
	$val = str_replace( "!"            , "&#33;"         , $val );
	$val = str_replace( "'"            , "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
	
	// Ensure unicode chars are OK
	
	$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
	
	// Strip slashes if not already done so.
	
	if ( get_magic_quotes_gpc() )
	{
		$val = stripslashes($val);
	}
	
	// Swop user inputted backslashes
	
	$val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val ); 
	
	return $val;
}
	 
?>