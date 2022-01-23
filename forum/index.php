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
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE SOFTWARE!
|   http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Wrapper script
|   > Script written by Matt Mecham
|   > Date started: 14th February 2002
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------

// Root path

define( 'ROOT_PATH'  , "./" );
define( 'KERNEL_PATH', ROOT_PATH.'ips_kernel/' );

// Enable module usage?
// (Vital for some mods and IPB enhancements)

define ( 'USE_MODULES', 1 );

// Enable shut down features?
// Uses PHPs register_shutdown_function to save
// low priority tasks until end of exec

define ( 'USE_SHUTDOWN', 1 );

// Enable custom error handling?
// Useful to trap skin errors, etc

define( 'CUSTOM_ERROR', 1 );

// You really don't want to turn this on

define( 'TRIAL_VERSION', 0 );

//-----------------------------------------------
// NO USER EDITABLE SECTIONS BELOW
//-----------------------------------------------

define ( 'IN_IPB', 1 );
define ( 'IN_DEV', 0 );

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

if ( CUSTOM_ERROR )
{
	set_error_handler("my_error_handler");
}

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
	var $version    = "v2.0.4";
	var $lastclick    = "";
	var $location     = "";
	var $debug_html   = "";
	var $perm_id      = "";
	var $forum_read   = array();
	var $topic_cache  = "";
	var $session_type = "";
	var $skin_global  = "";
	var $loaded_templates = array();
	var $converge     = "";
    
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

$INFO['sql_driver'] = ! $INFO['sql_driver'] ? 'mysql' : strtolower($INFO['sql_driver']);

require ( KERNEL_PATH.'class_db_'.$INFO['sql_driver'].".php" );

$DB = new db_driver;

$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->obj['sql_tbl_prefix']   = $INFO['sql_tbl_prefix'];
$DB->obj['query_cache_file'] = ROOT_PATH.'sources/sql/'.$INFO['sql_driver'].'_queries.php';
$DB->obj['use_shutdown']     = USE_SHUTDOWN;

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

//--------------------------------
// Wrap it all up in a nice easy to
// transport super class
//--------------------------------

$ibforums = new info();

//--------------------------------
// Require our global functions
//--------------------------------

require ROOT_PATH."sources/functions.php";
require ROOT_PATH."sources/classes/class_display.php";
require ROOT_PATH."sources/classes/class_session.php";
require ROOT_PATH."sources/classes/class_forums.php";
require KERNEL_PATH."class_converge.php";

$std    = new FUNC;
$print  = new display();
$sess   = new session();
$forums = new forum_functions();

//--------------------------------
//  Set up our vars
//--------------------------------

$ibforums->input = $std->parse_incoming();

//-----------------------------------------
// Make a safe query string
//-----------------------------------------

$ibforums->query_string_safe = str_replace( '&amp;amp;', '&amp;', $std->clean_value( $_SERVER['QUERY_STRING'] ) );

//--------------------------------
//  Set converge
//--------------------------------

$ibforums->converge = new class_converge( $DB );

//===========================================================================
// Generate choice array
//===========================================================================

$choice = array(
                 "idx"        => array( "boards"             , 'boards'       ,"'birthdays', 'calendar'," ),
                 "sf"         => array( "forums"             , 'forums'       ,"'announcements', 'multimod'," ),
                 "sr"         => array( "forums"             , 'forums'        ),
                 "st"         => array( "topics"             , 'topics'       ,"'attachtypes','bbcode', 'multimod','ranks','profilefields'," ),
                 "announce"   => array( "announcements"      , 'announcements',"'bbcode','badwords','emoticons','ranks','profilefields'," ),
                 "login"      => array( "login"              , 'login'         ),
                 "post"       => array( "post"               , 'post'         ,"'attachtypes','badwords','bbcode','emoticons'," ),
                 "poll"       => array( "lib/add_poll"       , 'poll'          ),
                 "reg"        => array( "register"           , 'register'      ),
                 "online"     => array( "online"             , 'online'        ),
                 "members"    => array( "memberlist"         , 'memberlist'   ,"'ranks','profilefields'," ),
                 "help"       => array( "help"               , 'help'          ),
                 "search"     => array( "search"             , 'search'       ,"'attachtypes','multimod','bbcode','ranks','profilefields'," ),
                 "mod"        => array( "moderate"           , 'moderate'     ,"'attachtypes','multimod','bbcode','emoticons'," ),
                 "print"      => array( "misc/print_page"    , 'printpage'    ,"'attachtypes','bbcode', 'multimod','ranks','profilefields'," ),
                 "forward"    => array( "misc/forward_page"  , 'forwardpage'   ),
                 "mail"       => array( "misc/contact_member", 'contactmember' ),
                 "invite"     => array( "misc/contact_member", 'contactmember' ),
                 "icq"        => array( "misc/contact_member", 'contactmember' ),
                 "aol"        => array( "misc/contact_member", 'contactmember' ),
                 "yahoo"      => array( "misc/contact_member", 'contactmember' ),
                 "msn"        => array( "misc/contact_member", 'contactmember' ),
                 "report"     => array( "misc/contact_member", 'contactmember' ),
                 "chat"       => array( "misc/contact_member", 'contactmember' ),
                 "integ"      => array( "misc/contact_member", 'contactmember' ),
                 'boardrules' => array( "misc/contact_member", 'contactmember' ),
                 "msg"        => array( "messenger"          , 'messenger'    ,"'profilefields','attachtypes','badwords','bbcode','emoticons'," ),
                 "usercp"     => array( "usercp"             , 'usercp'       ,"'attachtypes','badwords','bbcode','emoticons'," ),
                 "profile"    => array( "profile"            , 'profile'      ,"'ranks','profilefields','badwords','bbcode','emoticons',"),
                 "track"      => array( "misc/tracker"       , 'tracker'       ),
                 "stats"      => array( "misc/stats"         , 'stats'         ),
                 "attach"     => array( "misc/attach"        , 'attach'       ,"'attachtypes'," ),
                 'legends'    => array( 'misc/legends'       , 'legends'      ,"'badwords','bbcode'  ,'emoticons',"  ),
                 'calendar'   => array( "calendar"           , 'calendar'     ,"'attachtypes','bbcode', 'multimod','emoticons','badwords',"),
                 'buddy'      => array( "browsebuddy"        , 'assistant'     ),
                 'mmod'       => array( "misc/multi_moderate", 'mmod'         ,"'multimod',"),
                 'warn'       => array( "misc/warn"          , 'warn'         ,"'badwords','bbcode'  ,'emoticons',"  ),
                 'home'       => array( 'ipbportal'          , 'ipdl'         ,"'attachtypes','bbcode', 'multimod','ranks','profilefields'," ),
                 'module'     => array( 'modules'            , 'modules'       ),
                 'toutline'   => array( 'topics_outline'     , 'toutline'      ),
                 'task'       => array( 'taskloader'         , 'taskloader'    ),
                 'findpost'   => array( 'findpost'           , 'findpost'      ),
               );

//---------------------------------------------------
// Check to make sure the array key exits..
//---------------------------------------------------

$ibforums->input['_low_act'] = strtolower( $ibforums->input['act'] );

if (! isset($choice[ $ibforums->input['_low_act'] ][0]) )
{
	$ibforums->input['act'] = 'idx';
}

//===========================================================================
//  Short tags...
//===========================================================================

if ( $ibforums->input['showforum'] != "" )
{
	$ibforums->input['act'] = "sf";
	$ibforums->input['f']   = intval($ibforums->input['showforum']);
}
else if ( $ibforums->input['showtopic'] != "")
{
	$ibforums->input['act'] = "st";
	$ibforums->input['t']   = intval($ibforums->input['showtopic']);
	
	//---------------------------------------------------
	// Grab and cache the topic now as we need the 'f' attr for
	// the skins...
	//---------------------------------------------------
	
	$DB->simple_construct( array( 'select' => '*',
								  'from'   => 'topics',
								  'where'  => "tid=".$ibforums->input['t'],
						)      );
						
	$DB->simple_exec();
                       
    $ibforums->topic_cache = $DB->fetch_row();
    $ibforums->input['f']  = $ibforums->topic_cache['forum_id'];
}
else if ( $ibforums->input['showuser'] != "")
{
	$ibforums->input['act'] = "profile";
	$ibforums->input['MID'] = intval($ibforums->input['showuser']);
}
else if ( $ibforums->input['automodule'] != "" )
{
	$ibforums->input['act']    = 'module';
	$ibforums->input['module'] = $ibforums->input['automodule'];
}
else
{
	$ibforums->input['act'] = $ibforums->input['act'] == '' ? "idx" : $ibforums->input['act'];
}


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

if ( ! isset( $ibforums->cache['systemvars'] ) )
{
	$DB->simple_exec_query( array( 'delete' => 'cache_store', 'where' => "cs_key='systemvars'" ) );
	$DB->do_insert( 'cache_store', array( 'cs_key' => 'systemvars', 'cs_value' => addslashes(serialize(array())), 'cs_array' => 1 ) );
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
// Set debug mode
//--------------------------------

$DB->set_debug_mode( $ibforums->vars['sql_debug'] == 1 ? intval($_GET['debug']) : 0 );

//--------------------------------
//  Initialize the FUNC
//--------------------------------

$std->FUNC_init();

//--------------------------------
//  The rest :D
//--------------------------------

$ibforums->member     = $sess->authorise();
$ibforums->lastclick  = $sess->last_click;
$ibforums->location   = $sess->location;
$ibforums->session_id = $sess->session_id; // Used in URLs
$ibforums->my_session = $sess->session_id; // Used in code

//--------------------------------
//  Initialize the forums
//--------------------------------

$forums->strip_invisible = 1;
$forums->forums_init();

//--------------------------------
// Load the skin
//--------------------------------

$std->load_skin();

list($ppu,$tpu) = explode( "&", $ibforums->member['view_prefs'] );
		
$ibforums->vars['display_max_topics'] = ($tpu > 0) ? $tpu : $ibforums->vars['display_max_topics'];
$ibforums->vars['display_max_posts']  = ($ppu > 0) ? $ppu : $ibforums->vars['display_max_posts'];

//===========================================================================
//  Set up the session ID stuff
//===========================================================================

if ( $ibforums->session_type == 'cookie' )
{
	$ibforums->session_id = "";
	$ibforums->base_url   = $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?';
}
else
{
	$ibforums->base_url = $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?s='.$ibforums->session_id.'&amp;';
}

$ibforums->js_base_url = $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?s='.$ibforums->session_id.'&';

//--------------------------------
//  Set up the forum_read cookie
//--------------------------------

$std->hdl_forum_read_cookie();

//===========================================================================
//  Set up defaults
//===========================================================================

$ibforums->skin_id = $ibforums->skin['_setid'];

$ibforums->vars['img_url']       = 'style_images/' . $ibforums->skin['_imagedir'];
$ibforums->vars['AVATARS_URL']   = 'style_avatars';
$ibforums->vars['EMOTICONS_URL'] = 'style_emoticons/<#EMO_DIR#>';
$ibforums->vars['mime_img']      = 'style_images/<#IMG_DIR#>';

//--------------------------------
//  Set up our language choice
//--------------------------------

if ($ibforums->vars['default_language'] == "")
{
	$ibforums->vars['default_language'] = 'en';
}

//--------------------------------
// Did we choose a language?
//--------------------------------

If ( $ibforums->input['setlanguage'] and $ibforums->input['langid'] and $ibforums->member['id'] )
{
	if ( is_array( $ibforums->cache['languages'] ) and count( $ibforums->cache['languages'] ) )
	{
		foreach( $ibforums->cache['languages'] as $idx => $data )
		{
			if ( $data['ldir'] == $ibforums->input['langid'] )
			{
				$DB->do_update( 'members', array( 'language' => $data['ldir'] ), 'id='.$ibforums->member['id'] );
				$ibforums->member['language'] = $data['ldir'];
			}
		}
	}
}


$ibforums->lang_id = $ibforums->member['language'] ? $ibforums->member['language'] : $ibforums->vars['default_language'];

if ( ($ibforums->lang_id != $ibforums->vars['default_language']) and (! is_dir( ROOT_PATH."lang/".$ibforums->lang_id ) ) )
{
	$ibforums->lang_id = $ibforums->vars['default_language'];
}
		
$ibforums->lang = $std->load_words($ibforums->lang, 'lang_global', $ibforums->lang_id);

//--------------------------------
//  Expire subscription?
//--------------------------------

if ( $ibforums->member['sub_end'] != 0 AND ( $ibforums->member['sub_end'] < time() ) )
{
	$std->expire_subscription();
}

//--------------------------------
//  Upload dir?
//--------------------------------

$ibforums->vars['upload_dir'] = $ibforums->vars['upload_dir'] ? $ibforums->vars['upload_dir'] : ROOT_PATH.'uploads';

//===========================================================================
// DECONSTRUCTOR
//===========================================================================

if ( USE_SHUTDOWN and $ibforums->input['act'] != 'task' )
{
	chdir( ROOT_PATH );
	$ROOT_PATH = getcwd();
	
	register_shutdown_function( array( &$std, 'my_deconstructor') );
}

//===========================================================================
// Force log in / board offline?
//===========================================================================

if ($ibforums->input['_low_act']   != 'login'  and
	$ibforums->input['_low_act']   != 'reg'    and
	$ibforums->input['_low_act']   != 'attach' and
	$ibforums->input['_low_act']   != 'task'   and
	( $ibforums->input['_low_act'] != 'module' && $ibforums->input['module'] != 'subscription' ) )
{

	//--------------------------------
	//  Do we have permission to view
	//  the board?
	//--------------------------------
	
	if ($ibforums->member['g_view_board'] != 1)
	{ 
		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_view_board') );
	}
	
	//--------------------------------
	//  Is the board offline?
	//--------------------------------
	
	if ($ibforums->vars['board_offline'] == 1)
	{
		if ($ibforums->member['g_access_offline'] != 1)
		{
			$ibforums->vars['no_reg'] == 1;
			$std->board_offline();
		}
		
	}
	
	//--------------------------------
	//  Is log in enforced?
	//--------------------------------
	
	if ( (! $ibforums->member['id']) and ($ibforums->vars['force_login'] == 1) )
	{
		require ROOT_PATH."sources/login.php";
		$runme = new login();
		$runme->auto_run();
		
	}
	
	//--------------------------------
	// Show PURCHASE screen?
	// Not enforced
	//--------------------------------
	
	if ( ! $ibforums->member['sub_end'] )
	{
		//--------------------------------
		// 1: No enforce, chosen from reg
		//--------------------------------
		
		if ( ! $ibforums->vars['subsm_enforce'] and $ibforums->member['subs_pkg_chosen'] )
		{
			$ibforums->input['act']     = 'module';
			$ibforums->input['module']  = 'subscription';
			$ibforums->input['CODE']    = 'paymentmethod';
			$ibforums->input['sub']     = $ibforums->member['subs_pkg_chosen'];
			$ibforums->input['nocp']    = 1;
			$ibforums->input['msgtype'] = 'fromreg';
		}
	
		//--------------------------------
		// Show PURCHASE screen?
		// Enforced
		//--------------------------------
		
		if ( $ibforums->vars['subsm_enforce'] and $ibforums->member['mgroup'] == $ibforums->vars['subsm_nopkg_group'] )
		{
			$ibforums->input['act']     = 'module';
			$ibforums->input['module']  = 'subscription';
			$ibforums->input['nocp']    = 1;
			$ibforums->input['msgtype'] = 'force';
			
			if ( $ibforums->member['subs_pkg_chosen'] )
			{
				$ibforums->input['CODE']    = 'paymentmethod';
				$ibforums->input['sub']     = $ibforums->member['subs_pkg_chosen'];
			}
		}
	}
}

//===========================================================================
// REQUIRE AND RUN
//===========================================================================                

if ( $ibforums->input['act'] == 'home' )
{
	if ( $ibforums->vars['csite_on'] )
	{
		require ROOT_PATH."sources/ipbportal.php";
		$csite = new ipdl();
		$csite->auto_run();
	}
	else
	{
		require ROOT_PATH."sources/boards.php";
		$runme = new boards();
		$runme->auto_run();
	}
}
else if ( $ibforums->input['act'] == 'module' )
{
	if ( USE_MODULES == 1 )
	{
		require ROOT_PATH."modules/module_loader.php";
		$loader = new module_loader();
	}
	else
	{
		require ROOT_PATH."sources/boards.php";
	}
}
else
{
	// Require and run
	
	require_once( ROOT_PATH."sources/".$choice[ strtolower($ibforums->input['act']) ][0].".php" );
	
	$runme = new $choice[ strtolower($ibforums->input['act']) ][1];
	$runme->auto_run();
}

/*-------------------------------------------------------------------------*/
// GLOBAL ROUTINES
/*-------------------------------------------------------------------------*/

function fatal_error($message="", $help="")
{
	echo("$message<br><br>$help");
	exit;
}

/*-------------------------------------------------------------------------*/
// Custom error handler
/*-------------------------------------------------------------------------*/

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
   			echo "<b>IPB ERROR</b> [$errno] $errstr (Line: $errline of $errfile)<br />\n";
   			exit(1);
   		break;
  		case E_WARNING:
  			if ( strstr( $errstr, 'load_template(./skin_cache/cacheid_' ) )
  			{
  				echo "<div style='font-family:sans-serif'><b>IPB TEMPLATE ERROR:</b> Could not load the required template.
  					  <br /><br />First, try and remove any custom skin settings by clicking <a href='index.php?setskin&id=0'>here</a>
  					  <br /><br />Then, please visit your <a href='admin.php'>Admin Control Panel</a> to repair this template.
  					  <br /><br /><span style='font-size:90%;color:gray'>Error: $errstr</span></div>";
  			}
  			else
  			{
   				echo "<b>IPB WARNING</b> [$errno] $errstr (Line: $errline of $errfile)<br />\n";
   			}
   		break;
 		default:
   			//Do nothing
   		break;
	}
}



?>