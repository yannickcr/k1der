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

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class core_functions
{
	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function core_functions()
	{

	}

	/*-------------------------------------------------------------------------*/
	// FINISH _EVERYTHING_
	/*-------------------------------------------------------------------------*/

	function upgrade_complete()
	{
		global $ibforums, $DB, $std;

		$ibforums->template->content .= "
			<div class='tableborder'>
			 <div class='maintitle'>IPB Upgrade Complete!</div>
			 <div class='tdrow1' style='padding:6px'>You have now been upgraded!
			 <br /><br />
			 You may want to disable permissions on the script 'upgrade/index.php' to increase security or rename the 'upgrade' directory.
			 </div>
			</div>
			";

		$ibforums->template->output();
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD SETTINGS
	/*-------------------------------------------------------------------------*/

	function rebuild_settings()
	{
		global $ibforums, $DB, $std;

		$updated     = 0;
		$inserted    = 0;
		$need_update = array();

		if ( ! @file_exists( THIS_PATH.'upg_'.$ibforums->current_version.'/ipb_settings_partial.xml' ) )
		{
			$ibforums->core->redirect( "index.php?act=recache&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "No settings to import or update, proceeding to rebuild caches..." );
		}

		$content = implode( '', @file( THIS_PATH.'upg_'.$ibforums->current_version.'/ipb_settings_partial.xml' ) );

		//-------------------------------
		// Get current settings.
		//-------------------------------

		$cur_settings = array();

		$DB->simple_construct( array( 'select' => 'conf_id, conf_key',
									  'from'   => 'conf_settings',
									  'order'  => 'conf_id' ) );

		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$cur_settings[ $r['conf_key'] ] = $r['conf_id'];
		}

		//-------------------------------
		// Get xml mah-do-dah
		//-------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-------------------------------
		// Unpack the datafile
		//-------------------------------

		$xml->xml_parse_document( $content );

		//-------------------------------
		// pArse
		//-------------------------------

		$fields = array( 'conf_title'   , 'conf_description', 'conf_group'    , 'conf_type'    , 'conf_key'        , 'conf_default',
						 'conf_extra'   , 'conf_evalphp'    , 'conf_protected', 'conf_position', 'conf_start_group', 'conf_end_group',
						 'conf_help_key', 'conf_add_cache' );

		foreach( $xml->xml_array['settingexport']['settinggroup']['setting'] as $id => $entry )
		{
			$newrow = array();

			//-----------------------------------
			// Make PHP slashes safe
			//-----------------------------------

			$entry['conf_evalphp']['VALUE'] = str_replace( '\\', '\\\\', $entry['conf_evalphp']['VALUE'] );

			foreach( $fields as $f )
			{
				$newrow[$f] = $entry[ $f ]['VALUE'];
			}

			if ( $cur_settings[ $entry['conf_key']['VALUE'] ] )
			{
				//-----------------------------------
				// Update
				//-----------------------------------

				$DB->do_update( 'conf_settings', $newrow, 'conf_id='.$cur_settings[ $entry['conf_key']['VALUE'] ] );
				$updated++;
			}
			else
			{
				//-----------------------------------
				// INSERT
				//-----------------------------------

				$DB->do_insert( 'conf_settings', $newrow );
				$inserted++;
			}

			$need_update[ $entry['conf_group']['VALUE'] ] = $entry['conf_group']['VALUE'];
		}

		//-----------------------------------------
		// Update group counts...
		//-----------------------------------------

		if ( count( $need_update ) )
		{
			foreach( $need_update as $id )
			{
				$conf = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'conf_settings', 'where' => 'conf_group='.$id ) );

				$count = intval($conf['count']);

				$DB->do_update( 'conf_settings_titles', array( 'conf_title_count' => $count ), 'conf_title_id='.$id );
			}
		}

		//-----------------------------------
		// Boink..
		//-----------------------------------

		$ibforums->core->redirect( "index.php?act=recache&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "$updated settings updated $inserted settings inserted, proceeding to rebuild caches..." );

	}

	/*-------------------------------------------------------------------------*/
	// REBUILD TEMPLATES
	/*-------------------------------------------------------------------------*/

	function rebuild_templates_cache()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------
		// Get ACP library
		//-----------------------------------

		require_once( ROOT_PATH.'sources/admin/admin_cache_functions.php' );
		$acp = new admin_cache_functions();

		$justdone = intval($ibforums->input['justdone']);
		$justdone = $justdone ? $justdone : 1;

		//-----------------------------------
		// Get skins
		//-----------------------------------

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'skin_sets',
									  'where'  => 'set_skin_set_id > '.$justdone,
									  'order'  => 'set_skin_set_id',
									  'limit'  => array( 0, 1 )
						     )      );

		$DB->simple_exec();

		//-----------------------------------
		// Got a biggun?
		//-----------------------------------

		$r = $DB->fetch_row();

		if ( $r['set_skin_set_id'] )
		{
			$acp->_rebuild_all_caches( array($r['set_skin_set_id']) );

			$extra = implode( "<br />", $acp->messages );

			$ibforums->core->redirect( "index.php?act=templatescache&justdone={$r['set_skin_set_id']}&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "Rebuild cache for skin set {$r['set_name']}<br />{$extra}<br />Proceeding to the next skin..." );
		}
		else
		{
			$ibforums->core->redirect( "index.php?act=finish&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "No more skins to rebuild..." );
		}
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD TEMPLATES
	/*-------------------------------------------------------------------------*/

	function rebuild_templates()
	{
		global $ibforums, $DB, $std;

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
			install_error("Error with ipb_templates.xml - could not process XML properly");
		}

		foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $id => $entry )
		{

			$row = $DB->simple_exec_query( array( 'select' => 'suid',
												  'from'   => 'skin_templates',
												  'where'  => "group_name='{$entry[ 'group_name' ]['VALUE']}' AND func_name='{$entry[ 'func_name' ]['VALUE']}' and set_id=1"
										 )      );

			if ( $row['suid'] )
			{
				$DB->do_update( 'skin_templates', array( 'func_data'       => $entry[ 'func_data' ]['VALUE'],
														 'section_content' => $entry[ 'section_content' ]['VALUE'],
														 'updated'         => time()
													   )
											    , 'suid='.$row['suid'] );
			}
			else
			{
				$DB->do_insert( 'skin_templates', array( 'func_data'       => $entry[ 'func_data' ]['VALUE'],
														 'func_name'       => $entry[ 'func_name' ]['VALUE'],
														 'section_content' => $entry[ 'section_content' ]['VALUE'],
														 'group_name'      => $entry[ 'group_name' ]['VALUE'],
														 'updated'         => time(),
														 'set_id'          => 1
							 )                         );
			}
		}

		//-----------------------------------
		// Boink..
		//-----------------------------------

		$ibforums->core->redirect( "index.php?act=templatescache&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "Master templates rebuilt, proceeding to recache templates..." );
	}

	/*-------------------------------------------------------------------------*/
	// REBUILD CACHES
	/*-------------------------------------------------------------------------*/

	function rebuild_caches()
	{
		global $ibforums, $DB, $std;

		//-------------------------------------------------------------
		// BBCODE
		//-------------------------------------------------------------

		$ibforums->cache['bbcode'] = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode' ) );
		$bbcode = $DB->simple_exec();

		while ( $r = $DB->fetch_row($bbcode) )
		{
			$ibforums->cache['bbcode'][] = $r;
		}

		$std->update_cache( array( 'name' => 'bbcode', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Forum cache
		//-------------------------------------------------------------

		$std->update_forum_cache();

		//-------------------------------------------------------------
		// Group Cache
		//-------------------------------------------------------------

		$ibforums->cache['group_cache'] = array();

		$DB->simple_construct( array( 'select' => "*",
									  'from'   => 'groups'
							 )      );

		$DB->simple_exec();

		while ( $i = $DB->fetch_row() )
		{
			$ibforums->cache['group_cache'][ $i['g_id'] ] = $i;
		}

		$std->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Systemvars
		//-------------------------------------------------------------

		$ibforums->cache['systemvars'] = array();

		$result = $DB->simple_exec_query( array( 'select' => 'count(*) as cnt', 'from' => 'mail_queue' ) );

		$ibforums->cache['systemvars']['mail_queue'] = intval( $result['cnt'] );
		$ibforums->cache['systemvars']['task_next_run'] = time() + 3600;

		$std->update_cache( array( 'name' => 'systemvars', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Ranks
		//-------------------------------------------------------------

		$ibforums->cache['ranks'] = array();

		$DB->simple_construct( array( 'select' => 'id, title, pips, posts',
									  'from'   => 'titles',
									  'order'  => "posts DESC",
							)      );

		$DB->simple_exec();

		while ($i = $DB->fetch_row())
		{
			$ibforums->cache['ranks'][ $i['id'] ] = array(
														  'TITLE' => $i['title'],
														  'PIPS'  => $i['pips'],
														  'POSTS' => $i['posts'],
														);
		}

		$std->update_cache( array( 'name' => 'ranks', 'array' => 1, 'deletefirst' => 1 ) );


		//-------------------------------------------------------------
		// SETTINGS
		//-------------------------------------------------------------

		$ibforums->cache['settings'] = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_add_cache=1' ) );
		$info = $DB->simple_exec();

		while ( $r = $DB->fetch_row($info) )
		{
			$ibforums->cache['settings'][ $r['conf_key'] ] = $r['conf_value'] != "" ? $r['conf_value'] : $r['conf_default'];
		}

		$std->update_cache( array( 'name' => 'settings', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// EMOTICONS
		//-------------------------------------------------------------

		$ibforums->cache['emoticons'] = array();

		$DB->simple_construct( array( 'select' => 'typed,image,clickable,emo_set', 'from' => 'emoticons' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['emoticons'][] = $r;
		}

		$std->update_cache( array( 'name' => 'emoticons', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// LANGUAGES
		//-------------------------------------------------------------

		$ibforums->cache['languages'] = array();

		$DB->simple_construct( array( 'select' => 'ldir,lname', 'from' => 'languages' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['languages'][] = $r;
		}

		$std->update_cache( array( 'name' => 'languages', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// ATTACHMENT TYPES
		//-------------------------------------------------------------

		$ibforums->cache['attachtypes'] = array();

		$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}

		$std->update_cache( array( 'name' => 'attachtypes', 'array' => 1, 'deletefirst' => 1 ) );

		$ibforums->core->redirect( "index.php?act=templates&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "Caches rebuilt, proceeding to rebuild templates..." );

	}

	/*-------------------------------------------------------------------------*/
	// MODULE RUN - Look for next, or finish up...
	/*-------------------------------------------------------------------------*/

	function module_complete()
	{
		global $ibforums, $DB, $std;

		//------------------------------------------
		// Update DB
		//------------------------------------------

		$DB->do_insert( 'upgrade_history', array( 'upgrade_version_id'    => $ibforums->current_upgrade,
												  'upgrade_version_human' => $ibforums->versions[ $ibforums->current_upgrade ],
												  'upgrade_date'          => time(),
												  'upgrade_mid'           => $ibforums->input['mid']
				      )                         );

		//------------------------------------------
		// Anymore to run?
		//------------------------------------------

		if ( $ibforums->last_poss_id != $ibforums->current_upgrade )
		{
			$ibforums->core->redirect( "index.php?act=work&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "Upgrade module complete, moving on to the next upgrade module...." );
		}
		else
		{
			$ibforums->template->content .= "
			<div class='tableborder'>
			 <div class='maintitle'>IPB Upgrade Complete</div>
			 <div class='tdrow1' style='padding:6px'>You have now been upgraded from {$ibforums->versions[$ibforums->current_version]} to {$ibforums->versions[$ibforums->current_upgrade]}
			 <br /><br />
			 The next few final steps will check for updated settings and recache your cached data (forums, groups, moderators, etc) and rebuild your master templates to ensure
			 that all the template additions and modifications are updated.
			 <br /><br />
			 <div align='center'><span style='font-weight:bold;font-size:14px'>&raquo; <a href='index.php?act=settings&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}'>Proceed...</a></span></div>
			 </div>
			</div>
			";

			$ibforums->template->output();

		}
	}

	/*-------------------------------------------------------------------------*/
	// Redirect
	/*-------------------------------------------------------------------------*/

	function redirect($url, $text, $time=2)
	{
		global $ibforums, $DB, $std;


		$ibforums->template->content .= "<meta http-equiv='refresh' content=\"{$time}; url={$url}\">
										 <div class='tableborder'>
										 <div class='maintitle'>Redirecting</div>
										 <div class='tdrow1' style='padding:8px'>
										  <div style='font-size:12px'>$text
										  <br />
										  <br />
										  <center><a href='{$url}'>Click here if not redirected...</a></center>
										  </div>
										 </div>
										</div>";

		$ibforums->template->output("Redirecting...");
	}

	/*-------------------------------------------------------------------------*/
	// SHOW LOG IN SCREEN
	/*-------------------------------------------------------------------------*/

	function login_screen($msg='')
	{
		global $ibforums, $DB, $std;

		if ( ! file_exists( ROOT_PATH.'ipb_templates.xml' ) )
		{
			$msg .= "<div><b>Cannot locate XML templates</b><br />This should be located in 'ipb_templates.xml' please ensure this file is uploaded (recreating the file structure if needed) before continuing.
					 <br /><strong>Failure to upload this file will mean that your templates will not be updated.</strong></div>";
		}

		if ( $msg != "" )
		{
			$msg = "<div class='warnbox'>$msg</div><br />";
		}

		$ibforums->template->content .= "
				<form action='index.php?act=login' method='post' name='theAdminForm'>
				{$msg}
				<div>
				<strong>You must log in with your forums administrative log in details to access the upgrade system.
				<br />Upgrading from {$ibforums->versions[$ibforums->current_version]} to  {$ibforums->versions[$ibforums->current_upgrade]}</strong>
				</div>
				<br />
				<div class='tableborder'>
				<div class='maintitle'>Verification Required - Please Log In</div>
				<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'>
				<tr>
				<td class='tdrow1'  width='40%'  valign='middle'>Your Forums Username:</td>
				<td class='tdrow2'  width='60%'  valign='middle'><input type='text' style='width:100%' name='username' value=''></td>
				</tr>
				<tr>
				<td class='tdrow1'  width='40%'  valign='middle'>Your Forums Password:</td>
				<td class='tdrow2'  width='60%'  valign='middle'><input type='password' style='width:100%' name='password' value=''></td>
				</tr>
				<tr>
				<td class='pformstrip' colspan='2'><div align='center'><input type='submit' value='Log in' id='button' accesskey='s'></div></td>
				</tr>
				</table>
				</div>
				</form>";

		$ibforums->template->output("Log In");

	}

	/*-------------------------------------------------------------------------*/
	// Authorise da membah
	/*-------------------------------------------------------------------------*/

	function get_member()
	{
		global $std, $DB, $ibforums;

		$member = array( 'id' => 0 );

		$ibforums->loginkey  = $this->check_md5( $ibforums->input['loginkey']  );
		$ibforums->securekey = $this->check_md5( $ibforums->input['securekey'] );
		$ibforums->member_id = trim(intval($ibforums->input['mid'] ) );

		if ( ! $ibforums->loginkey or ! $ibforums->securekey )
		{
			return $member;
		}

		$DB->query( "SELECT m.*, g.* FROM ibf_members m
					  LEFT JOIN ibf_groups g ON ( m.mgroup=g.g_id )
					 WHERE member_login_key='{$ibforums->loginkey}' and id='{$ibforums->member_id}'" );

		$member = $DB->fetch_row();

		return $member;
	}

	/*-------------------------------------------------------------------------*/
	// Get the current version and the next version to upgrade to..
	/*-------------------------------------------------------------------------*/

	function get_version_latest()
	{
		global $ibforums;

		$ibforums->current_version = '';
		$ibforums->current_upgrade = '';

		//------------------------------------------
		// Copy & pop DB array and get next
		// upgrade script
		//------------------------------------------

		$tmp = $ibforums->db_contents;

		$ibforums->current_version = array_pop( $tmp );

		//------------------------------------------
		// Get the next upgrade script
		//------------------------------------------

		ksort( $ibforums->dir_contents );

		foreach( $ibforums->dir_contents as $i => $a )
		{
			if ( $a > $ibforums->current_version )
			{
				if ( ! $ibforums->current_upgrade )
				{
					$ibforums->current_upgrade  = $a;
				}

				$ibforums->modules_to_run[] = $ibforums->versions[ $a ];
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// GET INFO FROM THE DERTABASTIC
	/*-------------------------------------------------------------------------*/

	function get_db_structure()
	{
		global $std, $DB, $ibforums;

		$vers = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'upgrade_history', 'order' =>  'upgrade_version_id ASC' ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$vers[ $r['upgrade_version_id'] ] = $r['upgrade_version_id'];
		}

		return $vers;
	}

	/*-------------------------------------------------------------------------*/
	// Get dir structure..
	/*-------------------------------------------------------------------------*/

	function get_dir_structure()
	{
		$return = array();

		//------------------------------------------
 		// Get the folder names
 		//------------------------------------------

 		$dh = opendir( THIS_PATH );

 		while ( $file = readdir( $dh ) )
 		{
			if ( is_dir( THIS_PATH."/".$file ) )
			{
				if ( $file != "." && $file != ".." )
				{
					if ( strstr( $file, 'upg_' ) )
					{
						$tmp = str_replace( "upg_", "", $file );
						$return[ $tmp ] = $tmp;
					}
				}
			}
 		}

 		closedir( $dh );

 		sort($return);

 		return $return;
	}

	/*-------------------------------------------------------------------------*/
	// Check to see if its a 'real' MD5
	/*-------------------------------------------------------------------------*/

	function check_md5($t)
	{
		$t = preg_replace( "#[^a-z0-9]#", "", trim($t) );

		if ( strlen($t) != 32 )
		{
			return '';
		}
		else
		{
			return $t;
		}
	}

}


?>