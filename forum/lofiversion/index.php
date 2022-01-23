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
|   > LO-FI VERSION!
|   > Script written by Matt Mecham
|   > Date started: 11th March 2004
|   > Interesting fact: Wrote this while listening to the Stereophonic's
|   > 'Performance and Cocktails' CD. That was when they were good.
|   > Lo-fi feature took about 1.5 days to write. That's a lot of CD
|   > repeating...
+--------------------------------------------------------------------------
*/

//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------

define( 'LOFI_NAME'  , 'lofiversion' );

if ( substr(PHP_OS, 0, 3) == 'WIN' OR strstr( php_sapi_name(), 'cgi') OR php_sapi_name() == 'apache2filter' )
{
	define( 'THIS_PATH', str_replace( '\\', '/', dirname( __FILE__ ) ) .'/' );
	define( 'ROOT_PATH', str_replace( LOFI_NAME, '', THIS_PATH ) );
	define( 'SERVER'   , 'WIN' );
}
else
{
	define( 'THIS_PATH', './'  );
	define( 'ROOT_PATH', '../' );
	define( 'SERVER'   , 'UNX' );
}

define( 'KERNEL_PATH', ROOT_PATH.'ips_kernel/' );

//-----------------------------------------------
// NO USER EDITABLE SECTIONS BELOW
//-----------------------------------------------

define ( 'IN_IPB', 1 );
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
	var $version    = "v2.0.3 ";
	var $lastclick    = "";
	var $location     = "";
	var $debug_html   = "";
	var $perm_id      = "";
	var $forum_read   = array();
	var $topic_cache  = "";
	var $session_type = "";
	var $skin_global  = "";
	var $loaded_templates = array();

	function info()
	{
		global $sess, $std, $DB, $INFO;

		$this->vars = &$INFO;

		$this->vars['AVATARS_URL']     = 'style_avatars';
		$this->vars['EMOTICONS_URL']   = 'style_emoticons/<#EMO_DIR#>';
		$this->vars['mime_img']        = 'style_images/<#IMG_DIR#>/folder_mime_types';

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
$DB->obj['query_cache_file'] = ROOT_PATH.'sources/sql/'.strtolower($INFO['sql_driver']).'_queries.php';
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
require ROOT_PATH."sources/classes/class_display.php";
require ROOT_PATH."sources/classes/class_session.php";
require ROOT_PATH."sources/classes/class_forums.php";

$std    = new FUNC;
$print  = new display();
$sess   = new session();
$forums = new forum_functions();

//--------------------------------
//  Set up our vars
//--------------------------------

$ibforums->input = $std->parse_incoming();


//===========================================================================
// Get cache...
//===========================================================================

$DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key IN ( ".$choice[ strtolower($ibforums->input['act']) ][2]."'attachtypes','bbcode', 'multimod','ranks','profilefields','banfilters', 'settings', 'group_cache', 'systemvars', 'skin_id_cache', 'forum_cache', 'moderators', 'stats', 'languages' )" ) );
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
//  Initialize the FUNC
//--------------------------------

$std->FUNC_init();

//--------------------------------
//  The rest :D
//--------------------------------

$ibforums->member     = $sess->authorise();
$std->load_skin();

$ibforums->vars['display_max_topics'] = 150;
$ibforums->vars['display_max_posts']  = 50;

//--------------------------------
//  Initialize the forums
//--------------------------------

$forums->strip_invisible = 1;
$forums->forums_init();

$ibforums->session_id = "";
$ibforums->base_url   = $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?';

//--------------------------------
//  Banned?
//--------------------------------

if ( is_array( $ibforums->cache['banfilters'] ) and count( $ibforums->cache['banfilters'] ) )
{
	foreach ($ibforums->cache['banfilters'] as $ip)
	{
		$ip = str_replace( '\*', '.*', preg_quote($ip, "/") );

		if ( preg_match( "/^$ip$/", $ibforums->input['IP_ADDRESS'] ) )
		{
			fatal_error("You do not have permission to view this page");
		}
	}
}

//--------------------------------
//  Do we have permission to view
//  the board?
//--------------------------------

if ($ibforums->member['g_view_board'] != 1)
{
	$std->boink_it( $ibforums->base_url );
}

//--------------------------------
//  Is the board offline?
//--------------------------------

if ($ibforums->vars['board_offline'] == 1)
{
	if ($ibforums->member['g_access_offline'] != 1)
	{
		$std->boink_it( $ibforums->base_url );
	}
}

//--------------------------------
//  Is log in enforced?
//--------------------------------

if ( (! $ibforums->member['id']) and ($ibforums->vars['force_login'] == 1) )
{
	$std->boink_it( $ibforums->base_url );

}

//===========================================================================
// DO STUFF!
//===========================================================================

//--------------------------------
// Require 'skin'
//--------------------------------

require_once( THIS_PATH.'lofi_skin.php' );

//--------------------------------
// Not index.php/ ? Redirect
// We do this so we can use relative
// links...
//--------------------------------

$main_string = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];

if ( SERVER == 'WIN' )
{
	$winpath     = $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php?';
	$main_string = $_SERVER['QUERY_STRING'];
}
else
{
	if ( strpos( $main_string, '/'.LOFI_NAME.'/index.php/' ) === FALSE  )
	{
		$std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php/' );
	}

	if ( strstr( $main_string, "/" ) )
	{
		$main_string = str_replace( "/", "", strrchr( $main_string, "/" ) );
	}
}

$main_string = str_replace( ".html", "", $main_string );

$action = 'index';
$id    = 0;
$st    = 0;

//--------------------------------
// Pages?
//--------------------------------

if ( strstr( $main_string, "-" ) )
{
	list( $main, $start ) = explode( "-", $main_string );

	$main_string = $main;
	$st          = $start;
}

$st = intval($st);

//--------------------------------
// What we doing?
//--------------------------------

if ( preg_match( "#t\d#", $main_string ) )
{
	$action = 'topic';
	$id    = intval( preg_replace( "#t(\d+)#", "\\1", $main_string ) );
}
if ( preg_match( "#f\d#", $main_string ) )
{
	$action = 'forum';
	$id    = intval( preg_replace( "#f(\d+)#", "\\1", $main_string ) );
}


//--------------------------------
// Do it!
//--------------------------------

$output = "";

switch ( $action )
{
	case 'forum':
		$ibforums->real_link = $ibforums->base_url.'showforum='.$id;
		$output = get_forum_page($id, $st);
		break;
	case 'topic':
		$ibforums->real_link = $ibforums->base_url.'showtopic='.$id;
		$output = get_topic_page($id, $st);
		break;
	default:
		$ibforums->real_link = $ibforums->base_url;
		$output = get_index_page();
		break;
}

print_it($output);


//--------------------------------
// Board index
//--------------------------------

function get_index_page()
{
	global $ibforums, $std, $DB, $forums, $LOFISKIN;

	return LOFISKIN_forums( _get_forums() );
}

//--------------------------------
// Forums index
//--------------------------------

function get_forum_page($id, $st)
{
	global $ibforums, $std, $DB, $forums, $LOFISKIN, $navarray, $winpath;

	$output = "";

	if ( $std->check_perms($forums->forum_by_id[$id]['read_perms']) != TRUE and ( ! $forums->forum_by_id[$id]['permission_showtopic'] ) )
	{
		$std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php' );
	}

	//--------------------------------
	// Passy?
	//--------------------------------

	if ( $forums->forum_by_id[$id]['password'] != '' )
	{
		$std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php' );
	}

	//--------------------------------
	// Nav array...
	//--------------------------------

	$navarray = _get_nav_array($id);

	$ibforums->title = $forums->forum_by_id[ $id ]['name'];

	if ( ! $forums->forum_by_id[ $id ]['sub_can_post'] )
	{
		//--------------------------------
		// Show forums?
		//--------------------------------

		if ( is_array($forums->forum_cache[ $id ]) and count($forums->forum_cache[ $id ]) )
		{
			$html_string .= LOFISKIN_forums_entry_first($forums->forum_by_id[ $id ], $winpath);

			$depth_guide = "";

			foreach( $forums->forum_cache[ $id ] as $cid => $forum_data )
			{
				$forum_data['total_posts'] = intval( $forum_data['topics'] + $forum_data['posts'] );

				$html_string .= LOFISKIN_forums_entry($depth_guide, $forum_data, $winpath );

				$html_string = _get_forums_internal( $forum_data['id'], $html_string, "   ".$depth_guide );
			}

			$html_string .= LOFISKIN_forums_entry_end($depth_guide);
		}

		$output = $html_string;

		//--------------------------------
		// Return..
		//--------------------------------

		return LOFISKIN_forums($output);
	}
	else
	{
		//--------------------------------
		// Show topics...
		//--------------------------------

		$ibforums->pages = _get_pages( $forums->forum_by_id[ $id ]['topics'], $ibforums->vars['display_max_topics'], 'f'.$id );

		if ( ! $ibforums->member['g_other_topics'])
		{
			$query = " and starter_id=".$ibforums->member['id'];
		}

		//--------------------------------
		// Topics...
		//--------------------------------

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'topics',
									  'where'  => "approved=1 and forum_id=$id".$query,
									  'order'  => 'pinned desc, last_post desc',
									  'limit'  => array( $st, $ibforums->vars['display_max_topics'] )
							 )      );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			if ( $r['pinned'] )
			{
				$r['_prefix'] = 'Pinned: ';;
			}
			else
			{
				$r['_prefix'] = "";
			}

			if ($r['state'] == 'link')
			{
				$t_array = explode("&", $r['moved_to']);
				$r['tid']       = $t_array[0];
				$r['forum_id']  = $t_array[1];
				$r['title']     = $r['title'];
				$r['posts']     = '--';
				$r['_prefix']   = 'Moved: ';
			}

			$output .= LOFISKIN_topics_entry($r, $winpath);
		}

		//--------------------------------
		// Return..
		//--------------------------------

		return LOFISKIN_topics($output);
	}
}

//--------------------------------
// Topics index
//--------------------------------

function get_topic_page($id, $st)
{
	global $ibforums, $std, $DB, $forums, $LOFISKIN, $navarray, $winpath;

	$output = "";

	//--------------------------------
	// Get post_parser
	//--------------------------------

	require_once( ROOT_PATH."sources/lib/post_parser.php" );
	$parser = new post_parser();

	//--------------------------------
	// get topic
	//--------------------------------

	$topic = $DB->simple_exec_query( array( 'select' => '*', 'from'   => 'topics', 'where'  => "tid=".$id." and approved=1" ) );

	if ( ! $topic['tid'] )
	{
		$std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php' );
	}

	if ( ! $forums->forum_by_id[ $topic['forum_id'] ] )
	{
		$std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php' );
	}

	if ( $forums->forums_check_access( $topic['forum_id'], 0 ) )
	{
		$std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php' );
	}

	if ( $forums->read_topic_only )
	{
		$std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php' );
	}

	$ibforums->pages = _get_pages( $topic['posts'], $ibforums->vars['display_max_posts'], 't'.$id );

	$ibforums->title = $topic['title'];

	//--------------------------------
	// get posts...
	//--------------------------------

	$DB->simple_construct( array( 'select' => '*',
								  'from'   => 'posts',
								  'where'  => "topic_id={$id} AND queued <> 1",
								  'order'  => 'pid',
								  'limit'  => array( $st, $ibforums->vars['display_max_posts'] )
						 )     );

	$DB->simple_exec();

	while( $r = $DB->fetch_row() )
	{
		$r['post']      = nl2br( $parser->strip_all_tags_to_formatted( $r['post'] ) );
		$r['post_date'] = $std->get_date( $r['post_date'], 'LONG', 1 );

		$parser->pp_do_html  = ( $forums->forum_by_id[ $topic['forum_id'] ]['use_html'] and $ibforums->cache['group_cache'][ $poster['mgroup'] ]['g_dohtml'] and $r['post_htmlstate'] ) ? 1 : 0;
		$parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
		$parser->pp_nl2br    = $r['post_htmlstate'] == 2 ? 1 : 0;

		$r['post'] = $parser->post_db_parse( $r['post'] );

		//--------------------------------
		// Manage POST / TOPIC tags index.php?act=findpost&pid=415
		//--------------------------------

		$r['post'] = str_replace( "index.{$ibforums->vars['php_ext']}?showtopic="       , $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?showtopic='       , $r['post'] );
		$r['post'] = str_replace( "index.{$ibforums->vars['php_ext']}?act=findpost&pid=", $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?act=findpost&pid=', $r['post'] );

		$output .= LOFISKIN_posts_entry($r, $winpath);
	}

	//--------------------------------
	// Nav array...
	//--------------------------------


	$navarray   = _get_nav_array( $topic['forum_id'] );

	return $output;

}

//--------------------------------
// Print it
//--------------------------------

function print_it($content, $title='')
{
	global $ibforums, $std, $DB, $forums, $LOFISKIN, $navarray, $print;

	$fullurl   = $ibforums->vars['board_url'].'/'.LOFI_NAME.'/';

	$copyright = "Invision Power Board &copy; 2001-".date("Y")." <a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>";

	if ( $ibforums->vars['ipb_copy_number'] )
	{
		$copyright = "";
	}

	//--------------------------------
	// Nav
	//--------------------------------

	$nav = "<a href='./'>".$ibforums->vars['board_name']."</a>";

	if ( count($navarray) )
	{
		$nav .= " &gt; " . implode( " &gt; ", $navarray );
	}

	$title = $ibforums->title ? $ibforums->vars['board_name'].' &gt; '.$ibforums->title : $ibforums->vars['board_name'];

	$pages = "";

	if ( $ibforums->pages )
	{
		$pages = LOFISKIN_pages( $ibforums->pages );
	}

	$output = str_replace( '<% TITLE %>'    , $title    , $LOFISKIN['wrapper'] );
	$output = str_replace( '<% CONTENT %>'  , $content  , $output );
	$output = str_replace( '<% FULL_URL %>' , $fullurl  , $output );
	$output = str_replace( '<% COPYRIGHT %>', $copyright, $output );
	$output = str_replace( '<% NAV %>'      , $nav      , $output );
	$output = str_replace( '<% LINK %>'     , $ibforums->real_link, $output );
	$output = str_replace( '<% LARGE_TITLE %>', $ibforums->title ? $ibforums->title : $ibforums->vars['board_name'], $output );
	$output = str_replace( '<% PAGES %>'     , $pages, $output );

	//-----------------------------------------
	// Macros
	//-----------------------------------------

	$print->_unpack_macros();

	if ( is_array( $print->macros ) )
	{
		foreach( $print->macros as $i => $row )
		{
			if ( $row['macro_value'] != "" )
			{
				$output = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $output );
			}
		}
	}

	$output = preg_replace( "#([^/])style_images/(<\#IMG_DIR\#>|".preg_quote($ibforums->skin['_imagedir'], '/').")#is", "\\1".$ibforums->vars['board_url']."/style_images/\\2", $output );
	$output = str_replace( "style_emoticons/", $ibforums->vars['board_url']."/style_emoticons/", $output );

	$output = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $output );
	$output = str_replace( "<#EMO_DIR#>", $ibforums->skin['_emodir']  , $output );



	print $output;
}






//--------------------------------
// Recursively get forums
//--------------------------------

function _get_forums()
{
	global $ibforums, $forums, $LOFISKIN, $winpath;

	foreach( $forums->forum_cache['root'] as $id => $forum_data )
	{
		if ( is_array($forums->forum_cache[ $forum_data['id'] ]) and count($forums->forum_cache[ $forum_data['id'] ]) )
		{
			$html_string .= LOFISKIN_forums_entry_first($forum_data, $winpath);

			$depth_guide = "";

			if ( is_array( $forums->forum_cache[ $forum_data['id'] ] ) )
			{
				foreach( $forums->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
				{
					if ( ! $forum_data['redirect_on'] )
					{
						$forum_data['total_posts'] = intval( $forum_data['topics'] + $forum_data['posts'] );

						$html_string .= LOFISKIN_forums_entry($depth_guide, $forum_data, $winpath );

						$html_string = _get_forums_internal( $forum_data['id'], $html_string, "   ".$depth_guide );
					}
				}
			}

			$html_string .= LOFISKIN_forums_entry_end($depth_guide);
		}
	}

	return $html_string;
}

function _get_forums_internal($root_id, $html_string="", $depth_guide="")
{
	global $ibforums, $forums, $LOFISKIN, $winpath;

	if ( is_array( $forums->forum_cache[ $root_id ] ) )
	{
		$html_string .=  LOFISKIN_forums_entry_start($depth_guide);

		foreach( $forums->forum_cache[ $root_id ] as $id => $forum_data )
		{
			if ( ! $forum_data['redirect_on'] )
			{
				$forum_data['total_posts'] = intval( $forum_data['topics'] + $forum_data['posts'] );

				$html_string .= LOFISKIN_forums_entry($depth_guide, $forum_data, $winpath );

				$html_string = _get_forums_internal( $forum_data['id'], $html_string, "    ".$depth_guide );
			}
		}

		$html_string .= LOFISKIN_forums_entry_end($depth_guide);
	}

	return $html_string;
}



function _get_nav_array($id)
{
	global $ibforums, $forums, $LOFISKIN, $winpath;

	$navarray[] = "<a href='{$winpath}f{$id}.html'>{$forums->forum_by_id[$id]['name']}</a>";

	$ids = $forums->forums_get_parents( $id );

	if ( is_array($ids) and count($ids) )
	{
		foreach( $ids as $id )
		{
			$data = $forums->forum_by_id[$id];

			$navarray[] = "<a href='{$winpath}f{$data['id']}.html'>{$data['name']}</a>";
		}
	}

	return array_reverse($navarray);
}


function _get_pages( $total, $pp, $id )
{
	global $ibforums, $forums, $LOFISKIN, $navarray, $winpath;

	$page_array = array();

	//-----------------------------------------------
	// Get the number of pages
	//-----------------------------------------------

	$pages = ceil( $total / $pp );

	$pages = $pages ? $pages : 1;

	if ( $pages < 2 )
	{
		return "";
	}

	//-----------------------------------------------
	// Loppy loo
	//-----------------------------------------------

	if ($pages > 1)
	{
		for( $i = 0; $i <= $pages - 1; ++$i )
		{
			$RealNo = $i * $pp;
			$PageNo = $i+1;

			$page_array[] = "<a href='{$winpath}{$id}-{$RealNo}.html'>{$PageNo}</a>";
		}

	}

	return implode( ", ", $page_array );
}

//+-------------------------------------------------
// GLOBAL ROUTINES
//+-------------------------------------------------

function fatal_error($message="", $help="")
{
	echo("$message<br><br>$help");
	exit;
}




?>