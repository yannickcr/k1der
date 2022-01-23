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
|   > IPB UPGRADE 1.x -> 2.0!
|   > Script written by Matt Mecham
|   > Date started: 21st April 2004
|   > Interesting fact: Radiohead rock (Rain down, Rain down,
					   come on rain down. On me. From a great height)
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
define ( 'THIS_SCRIPT' , 'upgrade.php' );

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);
@set_time_limit(0);

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
$INFO['sql_driver'] = strtolower($INFO['sql_driver']);

if ( ! @file_exists( ROOT_PATH.'sources/sql/'.strtolower($INFO['sql_driver']).'_admin_queries.php' ) )
{
	print "Cannot find the file: ".ROOT_PATH.'sources/sql/'.strtolower($INFO['sql_driver'])."_admin_queries.php - make sure the file exists before continuing.";
	exit();
}

require ( KERNEL_PATH.'class_db_'.strtolower($INFO['sql_driver']).".php" );

$DB = new db_driver;

$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->obj['sql_tbl_prefix']   = $INFO['sql_tbl_prefix'];
$DB->obj['query_cache_file'] = ROOT_PATH.'sources/sql/'.strtolower($INFO['sql_driver']).'_admin_queries.php';
$DB->obj['use_shutdown']     = 0;
$DB->obj['debug']            = 0;

//--------------------------------
// Get a DB connection
//--------------------------------

$DB->connect();

define ( 'SQL_DRIVER', strtolower($INFO['sql_driver']) );

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
//  Set up our vars
//--------------------------------

$ibforums->input     = $std->parse_incoming();
$ibforums->core      = new core_functions();
$ibforums->template  = new template();
$ibforums->base_url  = THIS_SCRIPT.'?';
$ibforums->next_step = intval($ibforums->input['step']) + 1;

//--------------------------------
// GET QUERIES FILE (1.2 or 1.3)?
// If we have subs table, it's 1.3
//--------------------------------

if ( ! $ibforums->input['version'] )
{
	if ( _check_for_two() )
	{
		$ibforums->input['version'] = '200';
		print "You appear to be running IPB 2.0.0 already and no upgrade is required.";
		exit();
	}
	else if ( $DB->field_exists( 'sub_id', SQL_PREFIX.'subscriptions' ) )
	{
		$ibforums->input['version'] = '103';
	}
	else if ( $DB->field_exists( 'perm_id', SQL_PREFIX.'forum_perms' ) )
	{
		$ibforums->input['version'] = '102';
	}
	else
	{
		$ibforums->input['version'] = '101';
	}
}

switch( $ibforums->input['version'] )
{
	case '103':
		define( 'UPGRADE_FROM', '1.3' );
		require_once( THIS_PATH.'pre_20000/10003_'.strtolower($INFO['sql_driver']).'_queries.php' );
		break;
	case '102':
		define( 'UPGRADE_FROM', '1.2' );
		require_once( THIS_PATH.'pre_20000/10002_'.strtolower($INFO['sql_driver']).'_queries.php' );
		break;
	case '101':
		define( 'UPGRADE_FROM', '1.1' );
		require_once( THIS_PATH.'pre_20000/10001_'.strtolower($INFO['sql_driver']).'_queries.php' );
		break;
}

$ibforums->sql = new upgrade_sql();

//--------------------------------
// Quitting on this step?
//--------------------------------

if ( $ibforums->input['dieafterstep'] )
{
	if ( $ibforums->input['step']  - 1 == $ibforums->input['dieafterstep'] )
	{
		$ibforums->template->content .= "
			 <div class='tableborder'>
			  <div class='maintitle'>You have chosen to stop before step {$ibforums->input['step']}</div>
			  <div class='tdrow1' style='padding:6px'>The upgrade script has been been stopped as requested...
			  </div>
			 </div>
			 ";

		$ibforums->template->output();
		exit();
	}
}

//--------------------------------
// What in gods name are we doing?
//--------------------------------

switch ($ibforums->input['act'])
{
	case 'work':

		if ( intval( $ibforums->input['step'] ) )
		{
			$func_to_run = 'step_'.$ibforums->input['step'];
		}

		$func_to_run();

		break;

	default:
		show_intro();
		break;
}

/*-------------------------------------------------------------------------*/
// STEP 25: COMPLETED - YAY!!
/*-------------------------------------------------------------------------*/

function step_25()
{
	global $ibforums, $std, $DB;

	//-----------------------------------------
	// Tidy up old skins
	//-----------------------------------------

	$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets' ) );
	$DB->simple_exec();

	$skins = array();

	while( $r = $DB->fetch_row() )
	{
		$skins[] = $r['set_skin_set_id'];
	}

	if ( count( $skins ) )
	{
		$DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => 'set_id NOT IN('.implode( ',', $skins ).')' ) );
		$DB->simple_exec_query( array( 'delete' => 'skin_macro'    , 'where' => 'macro_set NOT IN('.implode( ',', $skins ).')' ) );
	}

	$DB->do_update( 'members', array( 'skin' => '' ) );

	//-----------------------------------------
	// Convert major rows
	//-----------------------------------------

	$major_pain_in_the_ass[] = array( 'global_board_header'    , 'skin_global', 'BoardHeader' );
	$major_pain_in_the_ass[] = array( 'catheader_expanded'     , 'skin_boards', 'CatHeader_Expanded' );
	$major_pain_in_the_ass[] = array( 'pagetop'                , 'skin_boards', 'PageTop' );
	$major_pain_in_the_ass[] = array( 'forumrow'               , 'skin_boards', 'ForumRow' );
	$major_pain_in_the_ass[] = array( 'pagetop'                , 'skin_forum' , 'PageTop' );
	$major_pain_in_the_ass[] = array( 'tableend'               , 'skin_forum' , 'TableEnd' );
	$major_pain_in_the_ass[] = array( 'render_forum_row'       , 'skin_forum' , 'RenderRow' );
	$major_pain_in_the_ass[] = array( 'topic_page_top_new_mode', 'skin_topic' , 'PageTop' );
	$major_pain_in_the_ass[] = array( 'tablefooter'            , 'skin_topic' , 'TableFooter' );
	$major_pain_in_the_ass[] = array( 'renderrow'              , 'skin_topic' , 'RenderRow' );

	foreach( $major_pain_in_the_ass as $relief => $preperation_h )
	{
		$DB->do_update( 'skin_templates', array( 'func_name' => $preperation_h[0] ), "group_name='{$preperation_h[1]}' and func_name='{$preperation_h[2]}'" );
	}

	//-----------------------------------------
	// Update 'portal' skin
	//-----------------------------------------

	$DB->do_update( 'skin_templates', array( 'group_name' => 'skin_portal' ), "group_name='skin_csite'" );

	//-----------------------------------------
	// Drop old tables
	//-----------------------------------------

	$DB->sql_drop_table( 'templates' );
	$DB->sql_drop_table( 'macro_name' );
	$DB->sql_drop_table( 'skins' );
	$DB->sql_drop_table( 'css' );

	$ibforums->template->content .= "
		<div class='tableborder'>
		 <div class='maintitle'>Your upgrade is complete!</div>
		 <div class='tdrow1' style='padding:6px'>The upgrade script has been run successfully and you're now running IPB 2.0.0</b>
		 <br /><br />All of your current skins have been moved to a pre 2.0 parent. To use these skins, you'll need to first to the following:
		 <ul>
		  <li> Log into your Admin Control Panel
		  <li> Click on 'Skins & Templates' and then on 'Skin Tools'
		  <li> Use the tool 'Rebuild skin set cache on set...' on each of your original skins to rebuild their caches
		 </ul>
		 This will complete the import of your old skins. You will need to edit them to add in the new IPB 2.0.0 sections and to account for some of the renaming procedures.
		 </div>
		</div>
		";

	$ibforums->template->output();
}

/*-------------------------------------------------------------------------*/
// STEP 24: RECACHE & REBUILD
/*-------------------------------------------------------------------------*/

function step_24()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_24();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Rebuild completed - finishing up...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
	}
}



/*-------------------------------------------------------------------------*/
// STEP 23: IMPORT SETTINGS
/*-------------------------------------------------------------------------*/

function step_23()
{
	global $INFO, $ibforums, $std, $DB;

	//-----------------------------------
	// Get XML
	//-----------------------------------

	require_once( KERNEL_PATH.'class_xml.php' );

	$xml = new class_xml();

	//-----------------------------------
	// Get XML
	//-----------------------------------

	require_once( KERNEL_PATH.'class_xml.php' );

	$xml = new class_xml();

	//-----------------------------------
	// Get XML file
	//-----------------------------------

	$xmlfile = ROOT_PATH.'install/installfiles/ipb_settings.xml';

	$setting_content = implode( "", file($xmlfile) );

	//-------------------------------
	// Unpack the datafile
	//-------------------------------

	$xml->xml_parse_document( $setting_content );

	//-------------------------------
	// pArse
	//-------------------------------

	$fields = array( 'conf_title', 'conf_description', 'conf_group', 'conf_type', 'conf_key', 'conf_value', 'conf_default',
					 'conf_extra', 'conf_evalphp', 'conf_protected', 'conf_position', 'conf_start_group', 'conf_end_group', 'conf_help_key', 'conf_add_cache' );


	if ( ! is_array( $xml->xml_array['settingexport']['settinggroup']['setting'] ) )
	{
		show_error("Error with ipb_settings.xml - could not process XML properly");
	}

	foreach( $xml->xml_array['settingexport']['settinggroup']['setting'] as $id => $entry )
	{
		if ( ! $entry['conf_key']['VALUE'] )
		{
			continue;
		}

		$newrow = array();

		$entry['conf_value']['VALUE'] = "";

		if ( $INFO[ $entry['conf_key']['VALUE'] ] != "" and $INFO[ $entry['conf_key']['VALUE'] ] != $entry['conf_default']['VALUE'] )
		{
			$entry['conf_value']['VALUE'] = $INFO[ $entry['conf_key']['VALUE'] ];
		}

		//-----------------------------------
		// Special considerations?
		//-----------------------------------

		if ( $entry['conf_key']['VALUE'] == 'img_ext' )
		{
			$entry['conf_value']['VALUE'] = str_replace( '|', ',', $entry['conf_value']['VALUE'] );
		}
		else if ( $entry['conf_key']['VALUE'] == 'photo_ext' )
		{
			$entry['conf_value']['VALUE'] = str_replace( '|', ',', $entry['conf_value']['VALUE'] );
		}
		else if ( $entry['conf_key']['VALUE'] == 'avatar_ext' )
		{
			$entry['conf_value']['VALUE'] = str_replace( '|', ',', $entry['conf_value']['VALUE'] );
		}

		//-----------------------------------
		// Make PHP slashes safe
		//-----------------------------------

		$entry['conf_evalphp']['VALUE'] = str_replace( '\\', '\\\\', $entry['conf_evalphp']['VALUE'] );

		foreach( $fields as $f )
		{
			$newrow[$f] = $entry[ $f ]['VALUE'];
		}

		$db_string = $DB->compile_db_insert_string($newrow);
		$query = "INSERT INTO ".SQL_PREFIX."conf_settings (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")";

		if ( ! $DB->query($query) )
		{
			show_error($query."<br /><br />".$DB->error);
		}
	}

	$msg = "<b>Settings imported, recache & rebuild next...</b>";
	$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg );
}

/*-------------------------------------------------------------------------*/
// STEP 22: IMPORT SKINS & SETTINGS
/*-------------------------------------------------------------------------*/

function step_22()
{
	global $ibforums, $std, $DB;

	//-----------------------------------------
	// Get old skins data
	//-----------------------------------------

	$DB->simple_construct( array( 'select' => '*', 'from' => 'skins' ) );
	$outer = $DB->simple_exec();

	while( $r = $DB->fetch_row( $outer ) )
	{
		//-----------------------------------------
		// Get CSS
		//-----------------------------------------

		$css = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'css', 'where' => 'cssid='.$r['css_id'] ) );

		//-----------------------------------------
		// Get Wrapper
		//-----------------------------------------

		$wrapper = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'templates', 'where' => 'tmid='.$r['tmpl_id'] ) );

		//-----------------------------------------
		// Insert...
		//-----------------------------------------

		$DB->do_insert( 'skin_sets', array(
											'set_name'            => $r['sname'],
											'set_image_dir'       => $r['img_dir'],
											'set_hidden'          => 1,
											'set_default'         => 0,
											'set_css_method'      => 0,
											'set_skin_set_parent' => 3,
											'set_author_email'    => '',
											'set_author_name'     => 'IPB 2.0 Import',
											'set_author_url'      => '',
											'set_css'             => stripslashes($css['css_text']),
											'set_wrapper'         => stripslashes($wrapper['template']),
											'set_emoticon_folder' => 'default',
					 )                    );

		$new_id = $DB->get_insert_id();

		//-----------------------------------------
		// Update templates
		//-----------------------------------------

		$DB->do_update( 'skin_templates', array( 'set_id' => $new_id ), 'set_id='.$r['set_id'] );

		//-----------------------------------------
		// Update macros
		//-----------------------------------------

		$DB->do_update( 'skin_macro', array( 'macro_set' => $new_id ), 'macro_set='.$r['set_id'] );
	}

	//-----------------------------------
	// Get XML
	//-----------------------------------

	require_once( KERNEL_PATH.'class_xml.php' );

	$xml = new class_xml();

	//-----------------------------------
	// Get XML file (TEMPLATES)
	//-----------------------------------

	$xmlfile = ROOT_PATH.'ipb_templates.xml';

	$setting_content = implode( "", file($xmlfile) );

	//-------------------------------
	// Unpack the datafile (TEMPLATES)
	//-------------------------------

	$xml->xml_parse_document( $setting_content );

	//-------------------------------
	// (TEMPLATES)
	//-------------------------------

	if ( ! is_array( $xml->xml_array['templateexport']['templategroup']['template'] ) )
	{
		show_error("Error with ipb_templates.xml - could not process XML properly");
	}

	foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $id => $entry )
	{
		$newrow = array();

		$newrow['group_name']      = $entry[ 'group_name' ]['VALUE'];
		$newrow['section_content'] = $entry[ 'section_content' ]['VALUE'];
		$newrow['func_name']       = $entry[ 'func_name' ]['VALUE'];
		$newrow['func_data']       = $entry[ 'func_data' ]['VALUE'];
		$newrow['set_id']          = 1;
		$newrow['updated']         = time();

		$db_string = $DB->compile_db_insert_string($newrow);
		$query = "INSERT INTO ".SQL_PREFIX."skin_templates (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")";

		if ( ! $DB->query($query) )
		{
			show_error($query."<br /><br />".$DB->error);
		}
	}

	//-------------------------------
	// GET MACRO
	//-------------------------------

	$xmlfile = ROOT_PATH.'install/installfiles/ipb_macro.xml';

	$setting_content = implode( "", file($xmlfile) );

	//-------------------------------
	// Unpack the datafile (MACRO)
	//-------------------------------

	$xml->xml_parse_document( $setting_content );

	//-------------------------------
	// (MACRO)
	//-------------------------------

	if ( ! is_array( $xml->xml_array['macroexport']['macrogroup']['macro'] ) )
	{
		show_error("Error with ipb_macro.xml - could not process XML properly");
	}

	foreach( $xml->xml_array['macroexport']['macrogroup']['macro'] as $id => $entry )
	{
		$newrow = array();

		$newrow['macro_value']   = $entry[ 'macro_value' ]['VALUE'];
		$newrow['macro_replace'] = $entry[ 'macro_replace' ]['VALUE'];
		$newrow['macro_set']     = 1;

		$db_string = $DB->compile_db_insert_string($newrow);
		$query = "INSERT INTO ".SQL_PREFIX."skin_macro (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")";

		if ( ! $DB->query($query) )
		{
			show_error($query."<br /><br />".$DB->error);
		}
	}

	//-------------------------------
	// WRAPPER / CSS
	//-------------------------------

	require_once( ROOT_PATH.'install/installfiles/components.php' );

	$wrapper_record = array( 'set_wrapper'	=>	$WRAPPER,
							 'set_css'		=>	$CSS,
						   );

	$str = $DB->compile_db_update_string($wrapper_record);
	$query = "UPDATE ".SQL_PREFIX."skin_sets set ".$str." where set_skin_set_id=1";

	if ( ! $DB->query($query) )
	{
		show_error($query."<br /><br />".$DB->error);
	}

	//-----------------------------------------
	// Import XML
	//-----------------------------------------

	$msg = "<b>Skins imported, importing settings...</b>";
	$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
}


/*-------------------------------------------------------------------------*/
// STEP 21: OPTIMIZE: Part 2
/*-------------------------------------------------------------------------*/

function step_21()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_21();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Optimization completed, new skins import next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}


/*-------------------------------------------------------------------------*/
// STEP 20: OPTIMIZE: Part 1
/*-------------------------------------------------------------------------*/

function step_20()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_20();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Optimization started...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 19: DROP TABLES
/*-------------------------------------------------------------------------*/

function step_19()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_19();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Old tables dropped, optimization next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 18: INSERTS
/*-------------------------------------------------------------------------*/

function step_18()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_18();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Inserts completed, dropping old tables next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 17: ALTER OTHER TABLES II
/*-------------------------------------------------------------------------*/

function step_17()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_17();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Other tables altered, converting forums next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 16: ALTER MEMBERS TABLE II
/*-------------------------------------------------------------------------*/

function step_16()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_16();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Members table altered, other tables next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 15: ALTER TOPIC TABLE II
/*-------------------------------------------------------------------------*/

function step_15()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_15();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Topic table altered, altering members table next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 14: ALTER POST TABLE II
/*-------------------------------------------------------------------------*/

function step_14()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_14();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Post table altered, altering topic table next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}


/*-------------------------------------------------------------------------*/
// STEP 13: CONVERT TOPIC MULTI_MODS
/*-------------------------------------------------------------------------*/

function step_13()
{
	global $ibforums, $std, $DB;

	$f = $DB->query("SELECT * FROM ibf_forums");

	$final = array();

	while ( $r = $DB->fetch_row($f) )
	{
		$mmids = preg_split( "/,/", $r['topic_mm_id'], -1, PREG_SPLIT_NO_EMPTY );

		if ( is_array( $mmids ) )
		{
			foreach( $mmids as $m )
			{
				$final[ $m ][] = $r['id'];
			}
		}
	}

	$real_final = array();

	foreach( $final as $id => $forums_ids )
	{
		$ff = implode( ",",$forums_ids );

		$DB->do_update( 'topic_mmod', array( 'mm_forums' => $ff ), 'mm_id='.$id );
	}

	$msg = "<b>Topic multi-moderation converted, alterting tables, stage 2...</b>";
	$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
}

/*-------------------------------------------------------------------------*/
// STEP 12: CONVERT PMs
/*-------------------------------------------------------------------------*/

function step_12()
{
	global $ibforums, $std, $DB;

	$start = intval($_GET['st']);
	$lend  = 300;
	$end   = $start + $lend;

	//-----------------------------------------
	// In steps...
	//-----------------------------------------

	$DB->simple_construct( array( 'select' => '*', 'from' => 'messages', 'limit' => array( $start, $lend ) ) );
	$o = $DB->simple_exec();

	//-----------------------------------------
	// Do it...
	//-----------------------------------------

	if ( $DB->get_num_rows() )
	{
		//-----------------------------------------
		// Got some to convert!
		//-----------------------------------------

		$ibforums->next_step--;

		while ( $r = $DB->fetch_row($o) )
		{
			if ( ! $r['msg_date'] )
			{
				$r['msg_date'] = $r['read_date'];
			}

			if ( $r['vid'] != 'sent' )
			{
				$DB->do_insert( 'message_text',
								array( 'msg_date'          => $r['msg_date'],
									   'msg_post'          => stripslashes($r['message']),
									   'msg_cc_users'      => $r['cc_users'],
									   'msg_author_id'     => $r['from_id'],
									   'msg_sent_to_count' => 1,
									   'msg_deleted_count' => 0,
							  )      );

				$msg_id = $DB->get_insert_id();

				$DB->do_insert( 'message_topics',
								array( 'mt_msg_id'     => $msg_id,
									   'mt_date'       => $r['msg_date'],
									   'mt_title'      => $r['title'],
									   'mt_from_id'    => $r['from_id'],
									   'mt_to_id'      => $r['recipient_id'],
									   'mt_vid_folder' => $r['vid'],
									   'mt_read'       => $r['read_state'],
									   'mt_tracking'   => $r['tracking'],
									   'mt_owner_id'   => $r['recipient_id'],
							 )        );
			}
			else
			{
				$DB->do_insert( 'message_text',
								array( 'msg_date'          => $r['msg_date'],
									   'msg_post'          => stripslashes($r['message']),
									   'msg_cc_users'      => $r['cc_users'],
									   'msg_author_id'     => $r['from_id'],
									   'msg_sent_to_count' => 1,
									   'msg_deleted_count' => 0,
							  )      );

				$msg_id = $DB->get_insert_id();

				$DB->do_insert( 'message_topics',
								array( 'mt_msg_id'     => $msg_id,
									   'mt_date'       => $r['msg_date'],
									   'mt_title'      => $r['title'],
									   'mt_from_id'    => $r['from_id'],
									   'mt_to_id'      => $r['recipient_id'],
									   'mt_vid_folder' => $r['vid'],
									   'mt_read'       => $r['read_state'],
									   'mt_tracking'   => $r['tracking'],
									   'mt_owner_id'   => $r['from_id'],
							 )        );
			}

		}

		$msg = "<b>Personal messages: $start to $end completed....</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}&st={$end}", $msg  );
	}
	else
	{
		$msg = "<b>Personal messages converted, proceeding to update topic multi-moderation...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
	}
}


/*-------------------------------------------------------------------------*/
// STEP 11: CONVERGE
/*-------------------------------------------------------------------------*/

function step_11()
{
	global $ibforums, $std, $DB;

	$start = intval($_GET['st']);
	$lend  = 300;
	$end   = $start + $lend;

	//-----------------------------------------
	// In steps...
	//-----------------------------------------

	require_once( ROOT_PATH."ips_kernel/class_converge.php" );
	$converge = new class_converge($DB);

	$max = 0;

	$DB->query("SELECT id FROM ibf_members where id > $end");

	$max = $DB->fetch_row();

	$o = $DB->query( $ibforums->sql->sql_members_converge( $start, $end ) );

	$found = 0;

	//-----------------------------------------
	// Do it...
	//-----------------------------------------

	while ( $r = $DB->fetch_row($o) )
	{
		if ( ! $r['cid'] or ! $r['id'] )
		{
			$r['password'] = $r['password'] ? $r['password'] : $r['legacy_password'];

			$salt = $converge->generate_password_salt(5);
			$salt = str_replace( '\\', "\\\\", $salt );

			$DB->do_insert( 'members_converge',
							array( 'converge_id'        => $r['id'],
								   'converge_email'     => $r['email'],
								   'converge_joined'    => $r['joined'],
								   'converge_pass_hash' => md5( md5($salt) . $r['password'] ),
								   'converge_pass_salt' => $salt
						 )       );

			$DB->do_update( 'members', array( 'member_login_key' => $converge->generate_auto_log_in_key() ), 'id='.$r['id'] );
		}

		$found++;
	}

	if ( ! $found and ! $max['id'] )
	{
		$msg = "<b>Converge completed, converting personal messages...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
	}
	else
	{
		$ibforums->next_step--;

		$msg = "<b>Converge added: $start to $end completed....</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}&st={$end}", $msg  );
	}
}


/*-------------------------------------------------------------------------*/
// STEP 10: CHECK EMAIL ADDRESSES
/*-------------------------------------------------------------------------*/

function step_10()
{
	global $ibforums, $std, $DB;

	$start = intval($_GET['st']);
	$lend  = 300;
	$end   = $start + $lend;

	//-----------------------------------------
	// In steps...
	//-----------------------------------------

	$o = $DB->query( $ibforums->sql->sql_members_email( $lend ) );

	//-----------------------------------------
	// Do it...
	//-----------------------------------------

	while ( $r = $DB->fetch_row($o) )
	{
		if ( $r['count'] < 2 )
		{
			break;
		}
		else
		{
			$dupe_emails[] = $r['email'];
		}
	}

	if ( count( $dupe_emails ) )
	{
		foreach( $dupe_emails as $email )
		{
			$first = 0;

			$DB->query( "SELECT id, name, email FROM ibf_members WHERE email='{$email}' ORDER BY joined ASC" );

			while( $r = $DB->fetch_row() )
			{
				// First?

				if ( ! $first )
				{
					$first = 1;
					continue;
				}
				else
				{
					// later dupe..

					$push_auth[] = $r['id'];
				}
			}
		}

		if ( count( $push_auth ) )
		{
			$DB->do_update( 'member_extra', array( 'bio' => 'dupemail' ), 'id IN ('.implode(",", $push_auth).")" );
			$DB->query( $ibforums->sql->sql_members_email_update( $push_auth ) );
		}

		$ibforums->next_step--;

		$msg = "<b>Members email addresses checked $start to $end completed....</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}&st={$end}", $msg  );
	}
	else
	{
		$msg = "<b>Members email addresses checked, adding to converge...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 9: CONVERT MEMBERS
/*-------------------------------------------------------------------------*/

function step_9()
{
	global $ibforums, $std, $DB;

	$start = intval($_GET['st']);
	$lend  = 300;
	$end   = $start + $lend;

	//-----------------------------------------
	// In steps...
	//-----------------------------------------

	$o = $DB->query( $ibforums->sql->sql_members( $start, $lend ) );

	//-----------------------------------------
	// Do it...
	//-----------------------------------------

	if ( $DB->get_num_rows() )
	{
		//-----------------------------------------
		// Got some to convert!
		//-----------------------------------------

		$ibforums->next_step--;

		while ( $r = $DB->fetch_row($o) )
		{
			if ( $r['mextra'] )
			{
				$DB->do_update( 'member_extra',
								array( 'aim_name'        => $r['aim_name'],
									   'icq_number'      => $r['icq_number'],
									   'website'         => $r['website'],
									   'yahoo'           => $r['yahoo'],
									   'interests'       => $r['interests'],
									   'msnname'         => $r['msnname'],
									   'vdirs'           => $r['vdirs'],
									   'location'        => $r['location'],
									   'signature'       => $r['signature'],
									   'avatar_location' => $r['avatar'],
									   'avatar_size'     => $r['avatar_size'],
									   'avatar_type'     => preg_match( "/^upload\:/", $r['avatar'] ) ? 'upload' : ( preg_match( "#^http://#", $r['avatar'] ) ? 'url' : 'local' )
							 ), 'id='.$r['mextra']        );
			}
			else
			{
				$DB->do_insert( 'member_extra',
								array( 'id'              => $r['id'],
									   'aim_name'        => $r['aim_name'],
									   'icq_number'      => $r['icq_number'],
									   'website'         => $r['website'],
									   'yahoo'           => $r['yahoo'],
									   'interests'       => $r['interests'],
									   'msnname'         => $r['msnname'],
									   'vdirs'           => $r['vdirs'],
									   'location'        => $r['location'],
									   'signature'       => $r['signature'],
									   'avatar_location' => $r['avatar'],
									   'avatar_size'     => $r['avatar_size'],
									   'avatar_type'     => preg_match( "/^upload\:/", $r['avatar'] ) ? 'upload' : ( preg_match( "#^http://#", $r['avatar'] ) ? 'url' : 'local' )
							 )  );
			}
		}

		$msg = "<b>Members adjusted $start to $end completed....</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}&st={$end}", $msg  );
	}
	else
	{
		$msg = "<b>Members converted, making members email addresses safe for converge...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
	}
}


/*-------------------------------------------------------------------------*/
// STEP 8: CONVERT ATTACHMENTS
/*-------------------------------------------------------------------------*/

function step_8()
{
	global $ibforums, $std, $DB;

	$start = intval($_GET['st']);
	$lend  = 300;
	$end   = $start + $lend;

	//-----------------------------------------
	// In steps...
	//-----------------------------------------

	$DB->simple_construct( array( "select" => '*',
								  'from'   => 'posts',
								  'where'  => "attach_file != ''",
								  'limit'  => array( $start, $lend ) ) );

	$outer = $DB->simple_exec();

	//-----------------------------------------
	// Do it...
	//-----------------------------------------

	if ( $DB->get_num_rows() )
	{
		//-----------------------------------------
		// Got some to convert!
		//-----------------------------------------

		$ibforums->next_step--;

		while( $r = $DB->fetch_row( $outer ) )
		{
			$image   = 0;
			$ext     = strtolower( str_replace( ".", "", substr( $r['attach_file'], strrpos( $r['attach_file'], '.' ) ) ) );
			$postkey = md5( $r['post_date'].','.$r['pid'] );

			if ( in_array( $ext, array( 'gif', 'jpeg', 'jpg', 'png' ) ) )
			{
				$image = 1;
			}

			$DB->do_insert( 'attachments', array( 'attach_ext'       => $ext,
												  'attach_file'      => $r['attach_file'],
												  'attach_location'  => $r['attach_id'],
												  'attach_is_image'  => $image,
												  'attach_hits'      => $r['attach_hits'],
												  'attach_date'      => $r['post_date'],
												  'attach_pid'       => $r['pid'],
												  'attach_post_key'  => $postkey,
												  'attach_member_id' => $r['author_id'],
												  'attach_approved'  => 1,
												  'attach_filesize'  => @filesize( ROOT_PATH.'uploads/'.$r['attach_id'] ),
						 )                      );

			$DB->do_update( 'posts', array( 'post_key' => $postkey ), 'pid='.$r['pid'] );
			$DB->simple_exec_query( array( 'update' => 'topics', 'set' => 'topic_hasattach=topic_hasattach+1', 'where' => 'tid='.$r['topic_id'] ) );
		}

		$msg = "<b>Attachments $start to $end completed....</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}&st={$end}", $msg );
	}
	else
	{
		$msg = "<b>Attachments converted, converting members...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 7: CONVERT FORUMS
/*-------------------------------------------------------------------------*/

function step_7()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_7();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Forums converted, converting attachments next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg  );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 6: ALTER OTHER TABLES
/*-------------------------------------------------------------------------*/

function step_6()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_6();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Other tables altered, converting forums next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 5: ALTER MEMBERS TABLE
/*-------------------------------------------------------------------------*/

function step_5()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_5();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Members table altered, other tables next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 4: ALTER TOPIC TABLE
/*-------------------------------------------------------------------------*/

function step_4()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_4();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Topic table altered, altering members table next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 3: ALTER POST TABLE
/*-------------------------------------------------------------------------*/

function step_3()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_3();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Post table altered, altering topic table next...</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 2: ADD NEW TABLES
/*-------------------------------------------------------------------------*/

function step_2()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_2();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>New tables created. Altering tables (Part 1, section 1 - post table)</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 1: COPY FORUMS TABLE
/*-------------------------------------------------------------------------*/

function step_1()
{
	global $ibforums, $std, $DB;

	$ibforums->sql->step_1();

	if ( count($ibforums->sql->error) )
	{
		show_error( implode( "<br />", $ibforums->sql->error ) );
	}
	else
	{
		$msg = "<b>Forums table backed up - creating new tables next....</b>";
		$ibforums->core->redirect( "{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}", $msg ."<br /><br />".intval($ibforums->sql->sqlcount)." queries run successfully!" );
	}
}

/*-------------------------------------------------------------------------*/
// STEP 0: SHOW INTRO
/*-------------------------------------------------------------------------*/

function show_intro()
{
	global $ibforums, $std, $DB;

	//-----------------------------------------
	// Check for missing files
	//-----------------------------------------

	$ibforums->template->content .= "
		<div class='tableborder'>
		 <div class='maintitle'>Welcome to the IPB ".UPGRADE_FROM." -> 2.0.0 Upgrade System</div>
		 <div class='tdrow1' style='padding:6px'>This upgrade script will upgrade your ".UPGRADE_FROM.".x board to IPB 2.0.0</b>
		 <br /><br />There are quite a few steps to perform to update your SQL tables and convert the current data over to the new system.
		 <br /><br /><span style='color:red'><b>WARNING: This upgrade script WILL upgrade your current database tables AND there is NO 'downgrade' script!
		 <br />PLEASE ensure that you have a very
		 recent SQL back-up so that you can restore your working board quickly if you need to do so.
		 <br />We cannot stress how important it is to ensure that you have a backup of this database before continuing</span>
		 <br /><br />First step: Copying drastically altered SQL tables for use later in the convert program.
		 <br /><br />
		";

	$warnings   = array();

	$checkfiles = array( ROOT_PATH     ."ipb_templates.xml",
						 ROOT_PATH     ."install/installfiles/components.php",
						 ROOT_PATH     ."install/installfiles/ipb_macro.xml",
						 ROOT_PATH     ."install/installfiles/ipb_settings.xml",
						 ROOT_PATH     ."sources/sql",
						 KERNEL_PATH   ."class_converge.php",
						 KERNEL_PATH   ."class_xml.php",
						 KERNEL_PATH   ."class_db_".SQL_DRIVER.".php",
						 ROOT_PATH     ."conf_global.php",

					  );

	$writeable  = array( ROOT_PATH."conf_global.php",
						 ROOT_PATH."skin_cache/"
					   );

	foreach ( $checkfiles as $cf )
	{
		if ( ! file_exists($cf) )
		{
			$warnings[] = "Cannot locate the file '$cf'.";
		}
	}

	foreach ( $writeable as $cf )
	{
		if ( ! is_writeable($cf) )
		{
			$warnings[] = "Cannot write to the file '$cf'. Please CHMOD to 0777.";
		}
	}

	$phpversion = phpversion();

	//----------------------------------
	// CHECK BASICS
	//----------------------------------

	if ($phpversion < '4.1.0')
	{
		$warnings[] = "<b>You cannot install Invision Power Board. Invision Power Board requires PHP Version 4.1.0 or better.</b>";
	}

	if ( ! function_exists('get_cfg_var') )
	{
		$warnings[] = "<b>You cannot install Invision Power Board. Your PHP installation isn't sufficient to run IPB.</b>";
	}

	if ( ! function_exists('xml_parse_into_struct') )
	{
		$warnings[] = "<b>You cannot install Invision Power Board. IPB requires that the XML functions in PHP are enabled, please ask your host to enable XML.</b>";
	}

	//----------------------------------
	// Got error?
	//----------------------------------

	if ( count($warnings) > 0 )
	{

		$err_string = '&middot;'.implode( "<br /><br />&middot;", $warnings );

		$ibforums->template->content .= "<br /><br />
							    <div class='warnbox'>
							     <strong>Warning!</strong>
							     <b>The following errors must be rectified before continuing!</b>
								 <br><br>
								 $err_string
							    </div>";
	}
	else
	{
		$ibforums->template->content .= "<div align='center'><span style='font-weight:bold;font-size:14px'>&raquo; <a href='{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}'>Proceed...</a></span></div>";
	}

	$ibforums->template->content .= "</div></div>";

	$ibforums->template->output();
}

/*-------------------------------------------------------------------------*/
// check for IPB 2
/*-------------------------------------------------------------------------*/

function _check_for_two()
{
	global $DB, $std, $ibforums;

	if ( ! $DB->field_exists( 'cs_key', SQL_PREFIX.'cache_store' ) )
	{
		return 0;
	}

	$r = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='forum_cache'" ) );

	if ( $r['cs_value'] )
	{
		return 1;
	}
	else
	{
		return 0;
	}
}


/*-------------------------------------------------------------------------*/
// SHOW ERROR WITH CONTINUE
/*-------------------------------------------------------------------------*/

function show_error( $msg )
{
	global $ibforums, $std, $DB;

	$ibforums->template->content .= "
		<div class='tableborder'>
		 <div class='maintitle'>Error!</div>
		 <div class='tdrow1' style='padding:6px'>The following error has been returned:</b>
		 <br /><br />$msg
		 <div align='center'><span style='font-weight:bold;font-size:14px'>&raquo; <a href='{$ibforums->base_url}&act=work&version={$ibforums->input['version']}&step={$ibforums->next_step}&dieafterstep={$ibforums->input['dieafterstep']}'>Continue regardless?</a></span></div>
		 </div>
		</div>
		";

	$ibforums->template->output();
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
    var $safe_act        = array( 'recache', 'templates', 'templatescache', 'finish' );

	function info()
	{
		global $sess, $std, $DB, $INFO;

		$this->vars = &$INFO;
	}
}


?>