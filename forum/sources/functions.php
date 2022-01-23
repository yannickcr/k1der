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
|   > Multi function library
|   > Module written by Matt Mecham
|   > Date started: 14th February 2002
|
|	> Module Version Number: 1.0.0
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

class FUNC {

	var $time_formats  = array();
	var $time_options  = array();
	var $offset        = "";
	var $offset_set    = 0;
	var $num_format    = "";
	var $allow_unicode = 1;
	var $get_magic_quotes = 0;
	var $today_array   = array();
	
	//-----------------------------------------
	// Set up some standards to save CPU later
	//-----------------------------------------
	
	function FUNC_init()
	{
		global $ibforums;
		
		$this->time_options = array( 'JOINED' => $ibforums->vars['clock_joined'],
									 'SHORT'  => $ibforums->vars['clock_short'],
									 'LONG'   => $ibforums->vars['clock_long']
								   );
								   
		$this->num_format = ($ibforums->vars['number_format'] == 'space') ? ' ' : $ibforums->vars['number_format'];
		
		$this->get_magic_quotes = get_magic_quotes_gpc();
		
		//-----------------------------------------
		// Sort out the accessing IP
		// (Thanks to Cosmos and schickb)
		//-----------------------------------------
		
		$addrs = array();
		
		if ( $ibforums->vars['xforward_matching'] )
		{
			foreach( array_reverse( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) as $x_f )
			{
				$x_f = trim($x_f);
				
				if ( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $x_f ) )
				{
					$addrs[] = $x_f;
				}
			}
		}
		
		$addrs[] = $_SERVER['REMOTE_ADDR'];
		$addrs[] = $_SERVER['HTTP_PROXY_USER'];
		$addrs[] = $_SERVER['HTTP_CLIENT_IP'];
	
		//-----------------------------------------
		// Make sure we take a valid IP address
		//-----------------------------------------
		
		$ibforums->input['IP_ADDRESS'] = preg_replace( "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $this->select_var( $addrs ) );
	}
	
	/*-------------------------------------------------------------------------*/
	//
    // My gmmktime() - PHP func seems buggy
    //
    /*-------------------------------------------------------------------------*/ 
    
	function date_gmmktime( $hour=0, $min=0, $sec=0, $month=0, $day=0, $year=0 )
	{
		// Calculate UTC time offset
		$offset = date( 'Z' );
		
		// Generate server based timestamp
		$time   = mktime( $hour, $min, $sec, $month, $day, $year );
		
		// Calculate DST on / off
		$dst    = intval( date( 'I', $time ) );
		
		return $offset + ($dst * 3600) + $time;
	}
		
	/*-------------------------------------------------------------------------*/
	//
	// Check mod queue status
	//
	/*-------------------------------------------------------------------------*/
	
	function can_queue_posts($fid=0)
	{
		global $ibforums, $DB;
		
		$return = 0;
		
		if ( $ibforums->member['g_is_supmod'] )
		{
			$return = 1;
		}
		else if ( $fid and $ibforums->member['is_mod'] and $ibforums->member['_moderator'][ $fid ]['post_q'] )
		{
			$return = 1;
		}
		
		return $return;
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Check multi mod status
	//
	/*-------------------------------------------------------------------------*/
	
	function get_multimod($fid)
	{
		global $ibforums, $DB, $std;
	
		$mm_array = array();
		
		$pass_go = FALSE;
		
		if ( $ibforums->member['id'] )
		{
			if ( $ibforums->member['g_is_supmod'] )
			{
				$pass_go = TRUE;
			}
			else if ( $ibforums->member['_moderator'][ $fid ]['can_mm'] == 1 )
			{
				$pass_go = TRUE;
			}
		}
		
		if ( $pass_go != TRUE )
		{
			return $mm_array;
		}
		
		if ( ! is_array( $ibforums->cache['multimod'] ) )
        {
        	$ibforums->cache['multimod'] = array();
        	
			$DB->simple_construct( array(
									 'select' => '*',
									 'from'   => 'topic_mmod',
									 'order'  => 'mm_title'
							 )      );
								
			$DB->simple_exec();
						
			while ($i = $DB->fetch_row())
			{
				$ibforums->cache['multimod'][ $i['mm_id'] ] = $i;
			}
			
			$std->update_cache( array( 'name' => 'multimod', 'array' => 1, 'deletefirst' => 1 ) );
        }
		
		//-----------------------------------------
		// Get the topic mod thingies
		//-----------------------------------------
		
		foreach( $ibforums->cache['multimod'] as $i => $r )
		{
			if ( $r['mm_forums'] == '*' OR strstr( ",".$r['mm_forums'].",", ",".$fid."," ) )
			{
				$mm_array[] = array( $r['mm_id'], $r['mm_title'] );
			}
		}
		
		return $mm_array;
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// UPDATE FORUM CACHE
	//
	/*-------------------------------------------------------------------------*/
	
	function update_forum_cache()
	{
		global $ibforums, $DB, $std;
		
		$ignore_me = array( 'redirect_url', 'redirect_loc', 'rules_text', 'permission_custom_error', 'notify_modq_emails' );
		
		if ( $ibforums->vars['forum_cache_minimum'] )
		{
			$ignore_me[] = 'description';
			$ignore_me[] = 'rules_title';
		}
		
		$ibforums->cache['forum_cache'] = array();
			
		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'forums',
									  'order'  => 'parent_id, position'
							 )      );
		$DB->simple_exec();
		
		while( $f = $DB->fetch_row() )
		{
			$fr = array();
			
			$perms = unserialize(stripslashes($f['permission_array']));
			
			//-----------------------------------------
			// Stuff we don't need...
			//-----------------------------------------
			
			foreach( $f as $k => $v )
			{
				if ( in_array( $k, $ignore_me ) )
				{
					continue;
				}
				else
				{
					if ( $v != "" )
					{
						$fr[ $k ] = $v;
					}
				}
			}
			
			$fr['read_perms']   = $perms['read_perms'];
			$fr['reply_perms']  = $perms['reply_perms'];
			$fr['start_perms']  = $perms['start_perms'];
			$fr['upload_perms'] = $perms['upload_perms'];
			$fr['show_perms']   = $perms['show_perms'];
			
			unset($fr['permission_array']);
			
			$ibforums->cache['forum_cache'][ $fr['id'] ] = $fr;
		}
		
		$this->update_cache( array( 'name' => 'forum_cache', 'array' => 1, 'deletefirst' => 1, 'donow' => 1 ) );
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// UPDATE CACHE
	//
	/*-------------------------------------------------------------------------*/
	
	function update_cache( $v=array() )
	{
		global $ibforums, $std, $DB;
		
		//-----------------------------------------
		// Don't cache forums?
		//-----------------------------------------
		
		if ( $v['name'] == 'forum_cache' AND $ibforums->vars['no_cache_forums'] )
		{
			return;
		}
		
		//-----------------------------------------
		// Next...
		//-----------------------------------------
		
		if ( $v['name'] )
		{
			if ( ! $v['value'] )
			{
				$value = $DB->add_slashes(serialize($ibforums->cache[ $v['name'] ]));
			}
			
			$DB->manual_addslashes = 1;
			
			if ( $v['deletefirst'] == 1 )
			{
				if ( $v['donow'] )
				{
					if ( $ibforums->vars['sql_driver'] == 'mysql' )
					{
						$DB->query( "REPLACE INTO ".SQL_PREFIX."cache_store SET cs_key='{$v['name']}', cs_value='$value', cs_array=".intval($v['array']) );
					}
					else
					{
						$DB->simple_construct( array( 'delete' => 'cache_store', 'where' => "cs_key='{$v['name']}'" ) );
						$DB->simple_exec();
					
						$DB->do_insert( 'cache_store', array( 'cs_array' => intval($v['array']), 'cs_key' => $v['name'], 'cs_value' => $value ) );
					}
				}
				else
				{
					if ( $ibforums->vars['sql_driver'] == 'mysql' )
					{
						$DB->cur_query = "REPLACE INTO ".SQL_PREFIX."cache_store SET cs_key='{$v['name']}', cs_value='$value', cs_array=".intval($v['array']);
						$DB->cache_shutdown_exec();
					}
					else
					{
						$DB->simple_construct( array( 'delete' => 'cache_store', 'where' => "cs_key='{$v['name']}'" ) );
						$DB->simple_shutdown_exec();
					
						$DB->do_shutdown_insert( 'cache_store', array( 'cs_array' => intval($v['array']), 'cs_key' => $v['name'], 'cs_value' => $value ) );
					}
				}
			}
			else
			{
				if ( $v['donow'] )
				{
					$DB->do_update( 'cache_store', array( 'cs_array' => intval($v['array']), 'cs_value' => $value ), "cs_key='{$v['name']}'" );
				}
				else
				{
					$DB->do_shutdown_update( 'cache_store', array( 'cs_array' => intval($v['array']), 'cs_value' => $value ), "cs_key='{$v['name']}'" );
				}
			}
			
			$DB->manual_addslashes = 0;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// MY DECONSTRUCTOR
	//
	/*-------------------------------------------------------------------------*/
	
	function my_deconstructor()
	{
		global $ibforums, $std, $DB;
		
		//-----------------------------------------
		// Any shutdown queries
		//-----------------------------------------
		
		$DB->return_die = 0;
		
		if ( count( $DB->obj['shutdown_queries'] ) )
		{
			foreach( $DB->obj['shutdown_queries'] as $q )
			{
				$DB->query( $q );
			}
		}
		
		$DB->return_die = 1;
		
		$DB->obj['shutdown_queries'] = array();
		
		//-----------------------------------------
		// Process mail queue
		//-----------------------------------------
			
		$std->process_mail_queue();
		
		$DB->close_db();
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Process Mail Queue
	//
	/*-------------------------------------------------------------------------*/
	
	function process_mail_queue()
	{
		global $ibforums, $DB, $ROOT_PATH;
		
		//-----------------------------------------
		// Bug in PHP?
		// shutdown func can't use relative
		// path..
		//-----------------------------------------
		
		$ROOT_PATH = $ROOT_PATH ? $ROOT_PATH.'/' : ROOT_PATH;
		
		//-----------------------------------------
		// SET UP
		//-----------------------------------------
		
		$ibforums->vars['mail_queue_per_blob'] = $ibforums->vars['mail_queue_per_blob'] ? $ibforums->vars['mail_queue_per_blob'] : 5;
		
		$ibforums->cache['systemvars']['mail_queue'] = intval($ibforums->cache['systemvars']['mail_queue']);
		
		$sent_ids = array();
		
		if ( $ibforums->cache['systemvars']['mail_queue'] > 0 )
		{
			//-----------------------------------------
			// Require the emailer...
			//-----------------------------------------
			
			require_once( $ROOT_PATH.'sources/classes/class_email.php' );
			$emailer = new emailer($ROOT_PATH);
			
			//-----------------------------------------
			// Get the mail stuck in the queue
			//-----------------------------------------
			
			$DB->simple_construct( array( 'select' => '*', 'from' => 'mail_queue', 'order' => 'mail_id', 'limit' => array( 0, $ibforums->vars['mail_queue_per_blob'] ) ) );
			$DB->simple_exec();
			
			while ( $r = $DB->fetch_row() )
			{
				$data[]     = $r;
				$sent_ids[] = $r['mail_id'];
			}
			
			if ( count($sent_ids) )
			{
				//-----------------------------------------
				// Delete sent mails and update count
				//-----------------------------------------
				
				$ibforums->cache['systemvars']['mail_queue'] = $ibforums->cache['systemvars']['mail_queue'] - count($sent_ids);
				
				$DB->simple_exec_query( array( 'delete' => 'mail_queue', 'where' => 'mail_id IN ('.implode(",", $sent_ids).')' ) );
			
				foreach( $data as $mail )
				{
					if ( $mail['mail_to'] and $mail['mail_subject'] and $mail['mail_content'] )
					{
						$emailer->to      = $mail['mail_to'];
						$emailer->from    = $mail['mail_from'] ? $mail['mail_from'] : $ibforums->vars['email_out'];
						$emailer->subject = $mail['mail_subject'];
						$emailer->message = $mail['mail_content'];
						
						$emailer->send_mail();
					}
				}
			}
			else
			{
				//-----------------------------------------
				// No mail after all?
				//-----------------------------------------
				
				$ibforums->cache['systemvars']['mail_queue'] = 0;
			}
			
			//-----------------------------------------
			// Update cache with remaning email count
			//-----------------------------------------
			
			$DB->do_update( 'cache_store', array( 'cs_array' => 1, 'cs_value' => addslashes(serialize($ibforums->cache['systemvars'])) ), "cs_key='systemvars'" );
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Load a template file from DB or from PHP file
	//
	/*-------------------------------------------------------------------------*/
	
	function load_template( $name, $id='' )
	{
		global $ibforums, $DB;
		
		$tags = 1;
		
		if ( $ibforums->vars['safe_mode_skins'] == 0 AND $ibforums->vars['safe_mode'] == 0 )
		{
			//-----------------------------------------
			// Simply require and return
			//-----------------------------------------
			
			if ( $name != 'skin_global')
			{
				if ( ! in_array( 'skin_global', $ibforums->loaded_templates ) )
				{
					require_once( CACHE_PATH."skin_cache/cacheid_".$ibforums->skin['_skincacheid']."/skin_global.php" );
					
					$ibforums->skin_global        = new skin_global();
					$ibforums->loaded_templates[] = 'skin_global';
				}
				
				$ibforums->loaded_templates[] = $name;
				
				require_once( CACHE_PATH."skin_cache/cacheid_".$ibforums->skin['_skincacheid']."/".$name.".php" );
				
				return new $name();
				
			}
			else
			{
				if ( $name == 'skin_global' )
				{
					$ibforums->loaded_templates[] = 'skin_global';
					
					require_once( CACHE_PATH."skin_cache/cacheid_".$ibforums->skin['_skincacheid']."/skin_global.php" );
					
					$ibforums->skin_global = new skin_global();
					return;
				}
				else
				{
					$ibforums->loaded_templates[] = $name;
					
					require_once( CACHE_PATH."skin_cache/cacheid_".$ibforums->skin['_skincacheid']."/".$name.".php" );
					return new $name();
				}
			}
		}
		else
		{
			//-----------------------------------------
			// We're using safe mode skins, yippee
			// Load the data from the DB
			//-----------------------------------------
			
			$skin_global = "";
			$other_skin  = "";
			$ibforums->skin['_type'] = 'Database Skins';
				
			if ( $ibforums->skin_global == "" and $name != 'skin_global')
			{
				//-----------------------------------------
				// Skin global not loaded...
				//-----------------------------------------
				
				$ibforums->loaded_templates[] = $name;
				$ibforums->loaded_templates[] = 'skin_global';
				
				$DB->simple_construct( array( 'select' => '*',
											  'from'   => 'skin_templates_cache',
											  'where'  => "template_set_id=".$ibforums->skin['_skincacheid']." AND template_group_name IN ('skin_global', '$name')"
									 )      );
									 
				$DB->simple_exec();
				
				while ( $r = $DB->fetch_row() )
				{
					if ( $r['template_group_name'] == 'skin_global' )
					{
						$skin_global = $r['template_group_content'];
					}
					else
					{
						$other_skin  = $r['template_group_content'];
					}
				}
				
				eval($skin_global);
				
				$ibforums->skin_global = new skin_global();
			}
			else
			{
				//-----------------------------------------
				// Skin global is loaded..
				//-----------------------------------------
				
				if ( $name == 'skin_global' and in_array( 'skin_global', $ibforums->loaded_templates ) )
				{
					return;
				}
				
				//-----------------------------------------
				// Load the skin, man
				//-----------------------------------------
				
				$ibforums->loaded_templates[] = $name;
				
				$DB->simple_construct( array( 'select' => '*',
											  'from'   => 'skin_templates_cache',
											  'where'  => "template_set_id=".$ibforums->skin['_skincacheid']." AND template_group_name='$name'"
									 )      );
									 
				$DB->simple_exec();
				
				$r = $DB->fetch_row();
				
				$other_skin  = $r['template_group_content'];
			}
			
			eval($other_skin);
			
			if ( $name == 'skin_global' )
			{
				$ibforums->skin_global = new skin_global();
			}
			else
			{
				return new $name();
			}
		}
	}
	
	/*-------------------------------------------------------------------------*/
    // SKIN, sort out the skin stuff                 
    /*-------------------------------------------------------------------------*/
    
    function load_skin()
    {
		global $ibforums, $DB;
    	
    	$id         = -1;
    	$skin_set   = 0;
    	$from_forum = 0;
    	$ibforums->input['skinid'] = intval($ibforums->input['skinid']);
    	$ibforums->member['skin']  = intval($ibforums->member['skin']);
    	
    	//-----------------------------------------
    	// Do we have a cache?
    	//-----------------------------------------
    	
    	if ( ! is_array( $ibforums->cache['skin_id_cache'] ) )
    	{
    		define ( 'IN_ACP', 1 );
    		require_once( ROOT_PATH.'sources/admin/admin_cache_functions.php' );
    		$admin = new admin_cache_functions();
    		
    		$ibforums->cache['skin_id_cache'] = $admin->_rebuild_skin_id_cache();
       	}
    	
    	//-----------------------------------------
    	// Search bot?
    	//-----------------------------------------
    	
    	if ( ( $ibforums->is_bot == 1 ) and ($ibforums->vars['spider_suit'] != "") )
    	{
    		$skin_set = 1;
    		$id       = $ibforums->vars['spider_suit'];
    	}
    	else
    	{
			//-----------------------------------------
			// Do we have a skin for a particular forum?
			//-----------------------------------------
			
			if ($ibforums->input['f'] and $ibforums->input['act'] != 'UserCP')
			{
				if ( $ibforums->cache['forum_cache'][ $ibforums->input['f'] ]['skin_id'] > 0 )
				{
					$id         = $ibforums->cache['forum_cache'][ $ibforums->input['f'] ]['skin_id'];
					$skin_set   = 1;
					$from_forum = 1;
				}
			}
			
			//-----------------------------------------
			// Are we allowing user chooseable skins?
			//-----------------------------------------
			
			if ($skin_set != 1 and $ibforums->vars['allow_skins'] == 1)
			{
				if ( $ibforums->input['skinid'] )
				{
					$id        = $ibforums->input['skinid'];
					$skin_set  = 1;
				}
				else if ( $ibforums->member['skin'] )
				{
					$id       = $ibforums->member['skin'];
					$skin_set = 1;
				}
			}
    	}
    	
    	//-----------------------------------------
		// Nothing set / hidden and not admin? Choose the default
		//-----------------------------------------
		
		if ( $ibforums->cache['skin_id_cache'][ $id ]['set_hidden'] )
		{
			if ( $from_forum )
			{
				$skin_set = 1;
			}
			else if ( $ibforums->member['g_access_cp'] )
			{
				$skin_set = 1;
			}
			else
			{
				$skin_set = 0;
			}
		}
			
		if ( ! $id OR ! $skin_set OR ! is_array($ibforums->cache['skin_id_cache'][ $id ]) )
		{
			foreach( $ibforums->cache['skin_id_cache'] as $sid => $data )
			{
				if ( $data['set_default'] )
				{
					$id       = $data['set_skin_set_id'];
					$skin_set = 1;
				}
			}
		}
		
		//-----------------------------------------
		// Get the skin
		//-----------------------------------------
    	
		$db_skin = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$id ) );
		
		$ibforums->skin['_css']         = $db_skin['set_cache_css'];
		$ibforums->skin['_wrapper']     = $db_skin['set_cache_wrapper'];
		$ibforums->skin['_macro']       = $db_skin['set_cache_macro'];
		$ibforums->skin['_skincacheid'] = IN_DEV ? 1 : $db_skin['set_skin_set_id'];
		$ibforums->skin['_csscacheid']  = IN_DEV ? 1 : $db_skin['set_skin_set_id'];
		$ibforums->skin['_imagedir']    = $db_skin['set_image_dir'];
		$ibforums->skin['_emodir']      = $db_skin['set_emoticon_folder'];
		$ibforums->skin['_setid']       = $db_skin['set_skin_set_id'];
    	$ibforums->skin['_setname']     = $db_skin['set_name'];
    	$ibforums->skin['_usecsscache'] = $db_skin['set_css_method'] ? 1 : 0;
    	
    	//-----------------------------------------
    	// Setting the skin?
    	//-----------------------------------------
    	
    	if ( ($ibforums->input['setskin']) and ($ibforums->member['id']) )
    	{
    		$DB->simple_construct( array( 'update' => 'members',
										  'set'    => "skin=".intval($id),
										  'where'  => "id=".$ibforums->member['id']
								 )      );
			$DB->simple_exec();
    		
    		$ibforums->member['skin'] = $id;
    	}
    	
    	return $row;
    }
	
	/*-------------------------------------------------------------------------*/
    // Require, parse and return an array containing the language stuff                 
    /*-------------------------------------------------------------------------*/ 
    
    function load_words($current_lang_array, $area, $lang_type) {
    
        require ROOT_PATH."lang/".$lang_type."/".$area.".php";
        
        foreach ($lang as $k => $v)
        {
        	$current_lang_array[$k] = stripslashes($v);
        }
        
        unset($lang);
        
        return $current_lang_array;
    }
	
	/*-------------------------------------------------------------------------*/
	// Truncate text string
	/*-------------------------------------------------------------------------*/
	
	function txt_truncate($text, $limit=30)
	{
		global $ibforums;
		
		if (strlen($text) > $limit)
		{
			$text = substr($text,0, $limit - 3) . "...";
			$text = preg_replace( "/&(#(\d+;?)?)?\.\.\.$/", '...', $text );
		}
		else
		{
			$text = preg_replace( "/&(#(\d+?)?)?$/", '', $text );
		}
		
		return $text;
	}
	
	/*-------------------------------------------------------------------------*/
	// Get new PM notification window
	/*-------------------------------------------------------------------------*/
	
	function get_new_pm_notification()
	{
		global $DB, $ibforums, $skin_universal;
		
		//-----------------------------------------
		// posty parsery
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/lib/post_parser.php' );
		
		$parser = new post_parser();
		
		//-----------------------------------------
		// Get last PM details
		//-----------------------------------------
		
		$DB->cache_add_query( 'msg_get_new_pm_notification', array( 'mid' => $ibforums->member['id'] ) );
		$DB->simple_exec();
		
		$msg = $DB->fetch_row();
		
		if ( ! $msg['msg_id'] and ! $msg['mt_id'] and ! $msg['id'] )
		{
			return '<!-- CANT FIND MESSAGE -->';
		}
		
		$msg['msg_post'] = $parser->strip_all_tags( $msg['msg_post'] );
		
		if ( strlen( $msg['msg_post'] ) > 120 )
		{
			$msg['msg_post'] = substr( $msg['msg_post'], 0, 117 ) . '...';
			$msg['msg_post'] = preg_replace( "/&(#(\d+;?)?)?\.\.\.$/", '...', $msg['msg_post'] );
		}
		
		if ( ! is_array( $ibforums->cache['badwords'] ) )
		{
			$ibforums->cache['badwords'] = array();
			
			$DB->simple_construct( array( 'select' => 'type,swop,m_exact', 'from' => 'badwords' ) );
			$bbcode = $DB->simple_exec();
		
			while ( $r = $DB->fetch_row($bbcode) )
			{
				$ibforums->cache['badwords'][] = $r;
			}
		}
		
		$msg['msg_post'] = $parser->bad_words( $msg['msg_post'] );
		
		if ( $msg['mt_hasattach'] )
		{
			$msg['attach_img'] = '<{ATTACH_ICON}>&nbsp;';
		}
		
		$msg['avatar'] = $this->get_avatar($msg['avatar_location'], 1, $msg['avatar_size'], $msg['avatar_type']);
		
		return $ibforums->skin_global->msg_get_new_pm_notification( $msg );
	}
	
	/*-------------------------------------------------------------------------*/
	// expire_subscription
	// ------------------
	// Remove member's subscription
	/*-------------------------------------------------------------------------*/
	
	function expire_subscription()
	{
		global $DB, $ibforums;
		
		$query = "sub_end=0";
		
		// Get subscription details...
		
		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'subscription_trans',
									  'where'  => "subtrans_state='paid' AND subtrans_member_id={$ibforums->member['id']}"
							 )      );
		$DB->simple_exec();
		
		if ( $row = $DB->fetch_row() )
		{
			if ( $row['subtrans_old_group'] > 0 )
			{
				
				$DB->simple_construct( array( 'select' => 'g_id',
											  'from'   => 'groups',
											  'where'  => "g_id={$row['subtrans_old_group']}"
									 )      );
				$DB->simple_exec();
		
				if ( $group = $DB->fetch_row() )
				{
					$query .= ", mgroup={$row['subtrans_old_group']}";
				}
				else
				{
					// Group has been deleted, reset back to base member group
					
					$query .= ", mgroup={$ibforums->vars['member_group']}";
				}
			}
			
			$DB->simple_construct( array( 'update' => 'subscription_trans',
										  'set'    => "subtrans_state='expired'",
										  'where'  => "subtrans_id={$row['subtrans_id']}"
								 )      );
			$DB->simple_exec();
			
		}
		
		$DB->simple_construct( array( 'update' => 'members',
									  'set'    => $query,
									  'where'  => "id={$ibforums->member['id']}"
							 )      );
		$DB->simple_exec();
    }
	
	/*-------------------------------------------------------------------------*/
	// txt_stripslashes
	// ------------------
	// Make Big5 safe - only strip if not already...
	/*-------------------------------------------------------------------------*/
	
	function txt_stripslashes($t)
	{
		if ( $this->get_magic_quotes )
		{
    		$t = stripslashes($t);
    	}
    	
    	return $t;
    }
	
	/*-------------------------------------------------------------------------*/
	// txt_raw2form
	// ------------------
	// makes _POST text safe for text areas
	/*-------------------------------------------------------------------------*/
	
	function txt_raw2form($t="")
	{
		$t = str_replace( '$', "&#036;", $t);
			
		if ( get_magic_quotes_gpc() )
		{
			$t = stripslashes($t);
		}
		
		$t = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $t );
		
		return $t;
	}
	
	/*-------------------------------------------------------------------------*/
	// Safe Slashes - ensures slashes are saved correctly
	/*-------------------------------------------------------------------------*/
	
	function txt_safeslashes($t="")
	{
		return str_replace( '\\', "\\\\", $this->txt_stripslashes($t));
	}
	
	/*-------------------------------------------------------------------------*/
	// txt_htmlspecialchars
	// ------------------
	// Custom version of htmlspecialchars to take into account mb chars
	/*-------------------------------------------------------------------------*/
	
	function txt_htmlspecialchars($t="")
	{
		// Use forward look up to only convert & not &#123;
		$t = preg_replace("/&(?!#[0-9]+;)/s", '&amp;', $t );
		$t = str_replace( "<", "&lt;"  , $t );
		$t = str_replace( ">", "&gt;"  , $t );
		$t = str_replace( '"', "&quot;", $t );
		$t = str_replace( "'", '&#039;', $t );
		
		return $t; // A nice cup of?
	}
	
	/*-------------------------------------------------------------------------*/
	// txt_UNhtmlspecialchars
	// ------------------
	// Undoes what the above function does. Yes.
	/*-------------------------------------------------------------------------*/
	
	function txt_UNhtmlspecialchars($t="")
	{
		$t = str_replace( "&amp;" , "&", $t );
		$t = str_replace( "&lt;"  , "<", $t );
		$t = str_replace( "&gt;"  , ">", $t );
		$t = str_replace( "&quot;", '"', $t );
		$t = str_replace( "&#039;", "'", $t );
		
		return $t;
	}
	
	/*-------------------------------------------------------------------------*/
	// txt_wintounix
	// ------------------
	// Converts \r\n to \n
	/*-------------------------------------------------------------------------*/
	
	function txt_windowstounix($t="")
	{
		// windows
		$t = str_replace( "\r\n" , "\n", $t );
		// Mac OS 9
		$t = str_replace( "\r"   , "\n", $t );
		return $t;
	}
	
	/*-------------------------------------------------------------------------*/
	// return_md5_check
	// ------------------
	// md5 hash for server side validation of form / link stuff
	/*-------------------------------------------------------------------------*/
	
	function return_md5_check()
	{
		global $ibforums;
		
		if ( $ibforums->member['id'] )
		{
			return md5($ibforums->member['email'].'&'.$ibforums->member['member_login_key'].'&'.$ibforums->member['joined']);
		}
		else
		{
			return md5("this is only here to prevent it breaking on guests");
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// C.O.C.S (clean old comma-delimeted strings)
	// ------------------
	// <>
	/*-------------------------------------------------------------------------*/
	
	function trim_leading_comma($t)
	{
		return preg_replace( "/^,/", "", $t );
	}
	
	function trim_trailing_comma($t)
	{
		return preg_replace( "/,$/", "", $t );
	}
	
	
	function clean_comma($t)
	{
		return preg_replace( "/,{2,}/", ",", $t );
	}
	
	function clean_perm_string($t)
	{
		$t = $this->clean_comma($t);
		$t = $this->trim_leading_comma($t);
		$t = $this->trim_trailing_comma($t);
		
		return $t;
	}
	
	/*-------------------------------------------------------------------------*/
	// size_format
	// ------------------
	// Give it a byte to eat and it'll return nice stuff!
	/*-------------------------------------------------------------------------*/
	
	function size_format($bytes="")
	{
		global $ibforums;
		
		$retval = "";
		
		if ($bytes >= 1048576)
		{
			$retval = round($bytes / 1048576 * 100 ) / 100 . $ibforums->lang['sf_mb'];
		}
		else if ($bytes  >= 1024)
		{
			$retval = round($bytes / 1024 * 100 ) / 100 . $ibforums->lang['sf_k'];
		}
		else
		{
			$retval = $bytes . $ibforums->lang['sf_bytes'];
		}
		
		return $retval;
	}
	
	/*-------------------------------------------------------------------------*/
	// print_forum_rules
	// ------------------
	// Checks and prints forum rules (if required)
	/*-------------------------------------------------------------------------*/
	
	function print_forum_rules($forum)
	{
		global $ibforums, $DB;
		
		$ruleshtml    = "";
		$rules['fid'] = $forum['id'];
		
		if ($forum['show_rules'])
		{
			 if ( $forum['show_rules'] == 2 )
			 {
				if ( $ibforums->vars['forum_cache_minimum'] )
				{
					$tmp = $DB->simple_exec_query( array( 'select' => 'rules_title, rules_text', 'from' => 'forums', 'where' => "id=".$forum['id']) );
					$rules['title'] = $tmp['rules_title'];
			 		$rules['body']  = $tmp['rules_text'];
				}
				else
				{
					$tmp = $DB->simple_exec_query( array( 'select' => 'rules_text', 'from' => 'forums', 'where' => "id=".$forum['id']) );
			 		$rules['body']  = $tmp['rules_text'];
			 		$rules['title'] = $forum['rules_title'];
				}
				
				$ruleshtml = $ibforums->skin_global->forum_show_rules_full($rules);
			 }
			 else
			 {
			 	if ( $ibforums->vars['forum_cache_minimum'] )
				{
					$tmp = $DB->simple_exec_query( array( 'select' => 'rules_title', 'from' => 'forums', 'where' => "id=".$forum['id']) );
					$rules['title'] = $tmp['rules_title'];
				}
				else
				{
			 		$rules['title'] = $forum['rules_title'];
				}
				
				$ruleshtml = $ibforums->skin_global->forum_show_rules_link($rules);
			 }
		}
		
		return $ruleshtml;
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// hdl_ban_line() : Get / set ban info
	// Returns array on get and string on "set"
	//
	/*-------------------------------------------------------------------------*/
	
	function hdl_ban_line($bline)
	{
		global $ibforums;
		
		if ( is_array( $bline ) )
		{
			// Set ( 'timespan' 'unit' )
			
			$factor = $bline['unit'] == 'd' ? 86400 : 3600;
			
			$date_end = time() + ( $bline['timespan'] * $factor );
			
			return time() . ':' . $date_end . ':' . $bline['timespan'] . ':' . $bline['unit'];
		}
		else
		{
			$arr = array();
			
			list( $arr['date_start'], $arr['date_end'], $arr['timespan'], $arr['unit'] ) = explode( ":", $bline );
			
			return $arr;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// check_perms() : Nice little sub to check perms
	// Returns TRUE if access is allowed, FALSE if not.
	//
	/*-------------------------------------------------------------------------*/
	
	function check_perms($forum_perm="")
	{
		global $ibforums;
		
		if ( ! is_array( $ibforums->perm_id_array ) )
		{
			return FALSE;
		}
		
		if ( $forum_perm == "" )
		{
			return FALSE;
		}
		else if ( $forum_perm == '*' )
		{
			return TRUE;
		}
		else
		{
			$forum_perm_array = explode( ",", $forum_perm );
			
			foreach( $ibforums->perm_id_array as $u_id )
			{
				if ( in_array( $u_id, $forum_perm_array ) )
				{
					return TRUE;
				}
			}
			
			// Still here? Not a match then.
			
			return FALSE;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// do_number_format() : Nice little sub to handle common stuff
	//
	/*-------------------------------------------------------------------------*/
	
	function do_number_format($number)
	{
		global $ibforums;
		
		if ($ibforums->vars['number_format'] != 'none')
		{
			return number_format($number , 0, '', $this->num_format);
		}
		else
		{
			return $number;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// hdl_forum_read_cookie()
	//
	/*-------------------------------------------------------------------------*/
	
	function hdl_forum_read_cookie($set="")
	{
		global $ibforums;
		
		if ( $set == "" )
		{
			// Get cookie and return array...
			
			if ( $fread = $this->my_getcookie('forum_read') )
			{ 
				$farray = unserialize(stripslashes($fread));
				
				if ( is_array($farray) and count($farray) > 0 )
				{
					foreach( $farray as $id => $stamp )
					{
						$ibforums->forum_read[$id] = $stamp;
					}
				}
			}
			
			return TRUE;
		}
		else
		{
			// Set cookie...
			
			$fread = addslashes(serialize($ibforums->forum_read));
			
			$this->my_setcookie('forum_read', $fread);
			
			return TRUE;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Return scaled down image
	//
	/*-------------------------------------------------------------------------*/
	
	function scale_image($arg)
	{
		// max_width, max_height, cur_width, cur_height
		
		$ret = array(
					  'img_width'  => $arg['cur_width'],
					  'img_height' => $arg['cur_height']
					);
		
		if ( $arg['cur_width'] > $arg['max_width'] )
		{
			$ret['img_width']  = $arg['max_width'];
			$ret['img_height'] = ceil( ( $arg['cur_height'] * ( ( $arg['max_width'] * 100 ) / $arg['cur_width'] ) ) / 100 );
			$arg['cur_height'] = $ret['img_height'];
			$arg['cur_width']  = $ret['img_width'];
		}
		
		if ( $arg['cur_height'] > $arg['max_height'] )
		{
			$ret['img_height']  = $arg['max_height'];
			$ret['img_width']   = ceil( ( $arg['cur_width'] * ( ( $arg['max_height'] * 100 ) / $arg['cur_height'] ) ) / 100 );
		}
		
		return $ret;
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Show NORMAL created security image(s)...
	//
	/*-------------------------------------------------------------------------*/
	
	function show_gif_img($this_number="")
	{
		global $ibforums, $DB;
		
		$numbers = array( 0 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUDH5hiKsOnmqSPjtT1ZdnnjCUqBQAOw==',
						  1 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUjAEWyMqoXIprRkjxtZJWrz3iCBQAOw==',
						  2 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUDH5hiKubnpPzRQvoVbvyrDHiWAAAOw==',
						  3 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVDH5hiKbaHgRyUZtmlPtlfnnMiGUFADs=',
						  4 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVjAN5mLDtjFJMRjpj1Rv6v1SHN0IFADs=',
						  5 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUhA+Bpxn/DITL1SRjnps63l1M9RQAOw==',
						  6 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVjIEYyWwH3lNyrQTbnVh2Tl3N5wQFADs=',
						  7 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUhI9pwbztAAwP1napnFnzbYEYWAAAOw==',
						  8 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVDH5hiKubHgSPWXoxVUxC33FZZCkFADs=',
						  9 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVDA6hyJabnnISnsnybXdS73hcZlUFADs=',
						);
		
		@header("Content-Type: image/gif");
		echo base64_decode($numbers[ $this_number ]);
		exit();
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Show GD created security image...
	//
	/*-------------------------------------------------------------------------*/
	
	function show_gd_img($content="")
	{
		global $ibforums, $DB;
		
		$content = '  '. preg_replace( "/(\w)/", "\\1 ", $content ) .' ';
		
		@header("Content-Type: image/jpeg");
		
		$tmp_x = 140;
		$tmp_y = 20;
		
		$image_x = 210;
		$image_y = 65;
		
		$circles = 3;
		
		if ( $ibforums->vars['gd_version'] == 1 )
		{
			$tmp = imagecreate($tmp_x, $tmp_y);
			$im  = imagecreate($image_x, $image_y);
		}
		else
		{
			$tmp = imagecreatetruecolor($tmp_x, $tmp_y);
			$im  = imagecreatetruecolor($image_x, $image_y);
		}
		
		$white  = ImageColorAllocate($tmp, 255, 255, 255);
		$black  = ImageColorAllocate($tmp, 0, 0, 0);
		$grey   = ImageColorAllocate($tmp, 210, 210, 210 );
		
		imagefill($tmp, 0, 0, $white);
		
		for ( $i = 1; $i <= $circles; $i++ )
		{
			$values = array(
							0  => rand(0, $tmp_x - 10),
							1  => rand(0, $tmp_y - 3),
							2  => rand(0, $tmp_x - 10),
							3  => rand(0, $tmp_y - 3),
							4  => rand(0, $tmp_x - 10),
							5  => rand(0, $tmp_y - 3),
							6  => rand(0, $tmp_x - 10),
							7  => rand(0, $tmp_y - 3),
							8  => rand(0, $tmp_x - 10),
							9  => rand(0, $tmp_y - 3),
							10 => rand(0, $tmp_x - 10),
							11 => rand(0, $tmp_y - 3),
					     );
	   
			$randomcolor = imagecolorallocate( $tmp, rand(100,255), rand(100,255),rand(100,255) );
			imagefilledpolygon($tmp, $values, 6, $randomcolor );
		}

		imagestring($tmp, 5, 0, 2, $content, $black);
		
		//-----------------------------------------
		// Distort by resizing
		//-----------------------------------------
		
		imagecopyresized($im, $tmp, 0, 0, 0, 0, $image_x, $image_y, $tmp_x, $tmp_y);
		
		imagedestroy($tmp);
		
		$white   = ImageColorAllocate($im, 255, 255, 255);
		$black   = ImageColorAllocate($im, 0, 0, 0);
		$grey    = ImageColorAllocate($im, 100, 100, 100 );
		
		$random_pixels = $image_x * $image_y / 10;
			
		for ($i = 0; $i < $random_pixels; $i++)
		{
			ImageSetPixel($im, rand(0, $image_x), rand(0, $image_y), $black);
		}
		
		$no_x_lines = ($image_x - 1) / 5;
		
		for ( $i = 0; $i <= $no_x_lines; $i++ )
		{
			// X lines
			
			ImageLine( $im, $i * $no_x_lines, 0, $i * $no_x_lines, $image_y, $grey );
			
			// Diag lines
			
			ImageLine( $im, $i * $no_x_lines, 0, ($i * $no_x_lines)+$no_x_lines, $image_y, $grey );
		}
		
		$no_y_lines = ($image_y - 1) / 5;
		
		for ( $i = 0; $i <= $no_y_lines; $i++ )
		{
			ImageLine( $im, 0, $i * $no_y_lines, $image_x, $i * $no_y_lines, $grey );
		}
		
		ImageJPEG($im);
		ImageDestroy($im);
		
		exit();
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Convert newlines to <br /> nl2br is buggy with <br /> on early PHP builds
	//
	/*-------------------------------------------------------------------------*/
	
	function my_nl2br($t="")
	{
		return str_replace( "\n", "<br />", $t );
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Convert <br /> to newlines
	//
	/*-------------------------------------------------------------------------*/
	
	function my_br2nl($t="")
	{
		$t = preg_replace( "#(?:\n|\r)?<br />(?:\n|\r)?#", "\n", $t );
		$t = preg_replace( "#(?:\n|\r)?<br>(?:\n|\r)?#"  , "\n", $t );
		
		return $t;
	}
		
	/*-------------------------------------------------------------------------*/
	//
	// Creates a profile link if member is a reg. member, else just show name
	//
	/*-------------------------------------------------------------------------*/
	
	function make_profile_link($name, $id="")
	{
		global $ibforums;
		
		if ($id > 0)
		{
			return "<a href='{$ibforums->base_url}showuser=$id'>$name</a>";
		}
		else
		{
			return $name;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Redirect using HTTP commands, not a page meta tag.
	//
	/*-------------------------------------------------------------------------*/
	
	function boink_it($url)
	{
		global $ibforums;
		
		// Ensure &amp;s are taken care of
		
		$url = str_replace( "&amp;", "&", $url );
		
		if ($ibforums->vars['header_redirect'] == 'refresh')
		{
			@header("Refresh: 0;url=".$url);
		}
		else if ($ibforums->vars['header_redirect'] == 'html')
		{
			echo("<html><head><meta http-equiv='refresh' content='0; url=$url'></head><body></body></html>");
			exit();
		}
		else
		{
			@header("Location: ".$url);
		}
		exit();
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Create a random 8 character password 
	//
	/*-------------------------------------------------------------------------*/
	
	function make_password()
	{
		$pass = "";
		$chars = array(
			"1","2","3","4","5","6","7","8","9","0",
			"a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
			"k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T",
			"u","U","v","V","w","W","x","X","y","Y","z","Z");
	
		$count = count($chars) - 1;
	
		srand((double)microtime()*1000000);

		for($i = 0; $i < 8; $i++)
		{
			$pass .= $chars[rand(0, $count)];
		}
	
		return($pass);
	}
    
	/*-------------------------------------------------------------------------*/
	//
	// Generate the appropriate folder icon for a topic
	//
	/*-------------------------------------------------------------------------*/
	
	function folder_icon($topic, $dot="", $last_time)
	{
		global $ibforums;
		
		//-----------------------------------------
		// Sort dot
		//-----------------------------------------
		
		if ($dot != "")
		{
			$dot = "_DOT";
		}
		
		if ($topic['state'] == 'closed')
		{
			return "<{B_LOCKED}>";
		}
		
		if ($topic['poll_state'])
		{
		
			if ( ! $ibforums->member['id'] )
			{
				return "<{B_POLL".$dot."}>";
			}
			
			if ($topic['last_post'] > $topic['last_vote'])
			{
				$topic['last_vote'] = $topic['last_post'];
			}
			
			if ($last_time  && ($topic['last_vote'] > $last_time ))
			{
				return "<{B_POLL".$dot."}>";
			}
			if ($last_time  && ($topic['last_vote'] < $last_time ))
			{
				return "<{B_POLL_NN".$dot."}>";
			}
			
			return "<{B_POLL".$dot."}>";
		}
		
		
		if ($topic['state'] == 'moved' or $topic['state'] == 'link')
		{
			return "<{B_MOVED}>";
		}
		
		if ( ! $ibforums->member['id'] )
		{
			return "<{B_NORM".$dot."}>";
		}
		
		if (($topic['posts'] + 1 >= $ibforums->vars['hot_topic']) and ( (isset($last_time) )  && ($topic['last_post'] <= $last_time )))
		{
			return "<{B_HOT_NN".$dot."}>";
		}
		if ($topic['posts'] + 1 >= $ibforums->vars['hot_topic'])
		{
			return "<{B_HOT".$dot."}>";
		}
		if ($last_time  && ($topic['last_post'] > $last_time))
		{
			return "<{B_NEW".$dot."}>";
		}
		
		return "<{B_NORM".$dot."}>";
	}
	
	/*-------------------------------------------------------------------------*/
    // text_tidy:
    // Takes raw text from the DB and makes it all nice and pretty - which also
    // parses un-HTML'd characters. Use this with caution!         
    /*-------------------------------------------------------------------------*/
    
    function text_tidy($txt = "") {
    
    	$trans = get_html_translation_table(HTML_ENTITIES);
    	$trans = array_flip($trans);
    	
    	$txt = strtr( $txt, $trans );
    	
    	$txt = preg_replace( "/\s{2}/" , "&nbsp; "      , $txt );
    	$txt = preg_replace( "/\r/"    , "\n"           , $txt );
    	$txt = preg_replace( "/\t/"    , "&nbsp;&nbsp;" , $txt );
    	//$txt = preg_replace( "/\\n/"   , "&#92;n"       , $txt );
    	
    	return $txt;
    }

    /*-------------------------------------------------------------------------*/
    // Build up page span links                
    /*-------------------------------------------------------------------------*/
    
	function build_pagelinks($data)
	{
		global $ibforums, $skin_universal;

		$work = array();
		
		$section = ($data['leave_out'] == "") ? 2 : $data['leave_out'];  // Number of pages to show per section( either side of current), IE: 1 ... 4 5 [6] 7 8 ... 10
		
		$use_st  = $data['USE_ST'] == "" ? 'st' : $data['USE_ST'];
		
		//-----------------------------------------
		// Get the number of pages
		//-----------------------------------------
		
		if ( $data['TOTAL_POSS'] > 0 )
		{
			$work['pages'] = ceil( $data['TOTAL_POSS'] / $data['PER_PAGE'] );
		}
		
		$work['pages'] = $work['pages'] ? $work['pages'] : 1;
		
		//-----------------------------------------
		// Set up
		//-----------------------------------------
		
		$work['total_page']   = $work['pages'];
		$work['current_page'] = $data['CUR_ST_VAL'] > 0 ? ($data['CUR_ST_VAL'] / $data['PER_PAGE']) + 1 : 1;
		
		//-----------------------------------------
		// Next / Previous page linkie poos
		//-----------------------------------------
		
		$previous_link = "";
		$next_link     = "";
		
		if ( $work['current_page'] > 1 )
		{
			$start = $data['CUR_ST_VAL'] - $data['PER_PAGE'];
			$previous_link = $ibforums->skin_global->pagination_previous_link("{$data['BASE_URL']}&amp;$use_st=$start");
		}
		
		if ( $work['current_page'] < $work['pages'] )
		{
			$start = $data['CUR_ST_VAL'] + $data['PER_PAGE'];
			$next_link = $ibforums->skin_global->pagination_next_link("{$data['BASE_URL']}&amp;$use_st=$start");
		}
		
		//-----------------------------------------
		// Loppy loo
		//-----------------------------------------
		
		if ($work['pages'] > 1)
		{
			$work['first_page'] = $ibforums->skin_global->pagination_make_jump($data['TOTAL_POSS'],$data['PER_PAGE'], $data['BASE_URL'], $work['pages']);
			
			for( $i = 0; $i <= $work['pages'] - 1; ++$i )
			{
				$RealNo = $i * $data['PER_PAGE'];
				$PageNo = $i+1;
				
				if ($RealNo == $data['CUR_ST_VAL'])
				{
					$work['page_span'] .=  $ibforums->skin_global->pagination_current_page($PageNo);
				}
				else
				{
					if ($PageNo < ($work['current_page'] - $section))
					{
						$work['st_dots'] = $ibforums->skin_global->pagination_start_dots($data['BASE_URL']);
						continue;
					}
					
					// If the next page is out of our section range, add some dotty dots!
					
					if ($PageNo > ($work['current_page'] + $section))
					{
						$work['end_dots'] = $ibforums->skin_global->pagination_end_dots("{$data['BASE_URL']}&amp;$use_st=".($work['pages']-1) * $data['PER_PAGE']);
						break;
					}
					
					
					$work['page_span'] .= $ibforums->skin_global->pagination_page_link("{$data['BASE_URL']}&amp;$use_st={$RealNo}",$PageNo);
				}
			}
			
			$work['return']    = $ibforums->skin_global->pagination_compile($work['first_page'],$previous_link,$work['st_dots'],$work['page_span'],$work['end_dots'],$next_link);
		}
		else
		{
			$work['return']    = $data['L_SINGLE'];
		}
	
		return $work['return'];
	}
    
    /*-------------------------------------------------------------------------*/
    // Build the forum jump menu               
    /*-------------------------------------------------------------------------*/ 
    
	function build_forum_jump($html=1, $override=0, $remove_redirects=0)
	{
		global $DB, $ibforums, $forums;
		// $html = 0 means don't return the select html stuff
		// $html = 1 means return the jump menu with select and option stuff
		// $ibforums->vars['short_forum_jump'] = 0;
		
		if ($html == 1) {
		
			$the_html = "<form onsubmit=\"if(document.jumpmenu.f.value == -1){return false;}\" action='{$ibforums->base_url}act=SF' method='get' name='jumpmenu'>
			             <input type='hidden' name='act' value='SF' />\n<input type='hidden' name='s' value='{$ibforums->session_id}' />
			             <select name='f' onchange=\"if(this.options[this.selectedIndex].value != -1){ document.jumpmenu.submit() }\" class='dropdown'>
			             <optgroup label=\"{$ibforums->lang['sj_title']}\">
			              <option value='sj_home'>{$ibforums->lang['sj_home']}</option>
			              <option value='sj_search'>{$ibforums->lang['sj_search']}</option>
			              <option value='sj_help'>{$ibforums->lang['sj_help']}</option>
			             </optgroup>
			             <optgroup label=\"{$ibforums->lang['forum_jump']}\">";
		}
			
		$the_html .= $forums->forums_forum_jump($html, $override, $remove_redirects);
			
		if ($html == 1)
		{
			$the_html .= "</optgroup>\n</select>&nbsp;<input type='submit' value='{$ibforums->lang['jmp_go']}' class='button' /></form>";
		}
		
		return $the_html;
	}
	
	function clean_email($email = "") {

		$email = trim($email);
		
		$email = str_replace( " ", "", $email );
		
		//-----------------------------------------
		// Check for more than 1 @ symbol
		//-----------------------------------------
		
		if ( substr_count( $email, '@' ) > 1 )
		{
			return FALSE;
		}
		
    	$email = preg_replace( "#[\;\#\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/\s]#", "", $email );
    	
    	if ( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $email) )
    	{
    		return $email;
    	}
    	else
    	{
    		return FALSE;
    	}
	}
    
    /*-------------------------------------------------------------------------*/
    // Return a date or '--' if the date is undef.
    // We use the rather nice gmdate function in PHP to synchronise our times
    // with GMT. This gives us the following choices:
    //
    // If the user has specified a time offset, we use that. If they haven't set
    // a time zone, we use the default board time offset (which should automagically
    // be adjusted to match gmdate.             
    /*-------------------------------------------------------------------------*/    
    
    function get_date($date, $method, $norelative=0)
    {
		global $ibforums;
        
        if (!$date)
        {
            return '--';
        }
        
        if (empty($method))
        {
        	$method = 'LONG';
        }
        
        if ($this->offset_set == 0)
        {
        	// Save redoing this code for each call, only do once per page load
        	
			$this->offset = $this->get_time_offset();
			
			if ( $ibforums->vars['time_use_relative'] )
			{
				$this->today_time     = gmdate('d,m,Y', ( time() + $this->offset) );
				$this->yesterday_time = gmdate('d,m,Y', ( (time() - 86400) + $this->offset) );
			}	
			
			$this->offset_set = 1;
        }
        
        if ( $ibforums->vars['time_use_relative'] and ( $norelative != 1 ) )
		{
			$this_time = gmdate('d,m,Y', ($date + $this->offset) );
			
			if ( $this_time == $this->today_time )
			{
				return str_replace( '{--}', $ibforums->lang['time_today'], gmdate($ibforums->vars['time_use_relative_format'], ($date + $this->offset) ) );
			}
			else if  ( $this_time == $this->yesterday_time )
			{
				return str_replace( '{--}', $ibforums->lang['time_yesterday'], gmdate($ibforums->vars['time_use_relative_format'], ($date + $this->offset) ) );
			}
			else
			{
				return gmdate($this->time_options[$method], ($date + $this->offset) );
			}
		}
		else
		{
        	return gmdate($this->time_options[$method], ($date + $this->offset) );
        }
    }
    
    /*-------------------------------------------------------------------------*/
    // Returns the time - tick tock, etc           
    /*-------------------------------------------------------------------------*/   
    
    function get_time($date, $method='h:i A')
    {
		global $ibforums;
        
        if ($this->offset_set == 0)
        {
        	// Save redoing this code for each call, only do once per page load
        	
			$this->offset = $this->get_time_offset();
			
			$this->offset_set = 1;
        }
        
        return gmdate($method, ($date + $this->offset) );
    }
    
    /*-------------------------------------------------------------------------*/
    // Returns the offset needed and stuff - quite groovy.              
    /*-------------------------------------------------------------------------*/    
    
    function get_time_offset()
    {
		global $ibforums;
    	
    	$r = 0;
    	
    	$r = (($ibforums->member['time_offset'] != "") ? $ibforums->member['time_offset'] : $ibforums->vars['time_offset']) * 3600;
			
		if ( $ibforums->vars['time_adjust'] )
		{
			$r += ($ibforums->vars['time_adjust'] * 60);
		}
		
		if ($ibforums->member['dst_in_use'])
		{
			$r += 3600;
		}
    	
    	return $r;
    }
    
    /*-------------------------------------------------------------------------*/
    // Sets a cookie, abstract layer allows us to do some checking, etc                
    /*-------------------------------------------------------------------------*/    
    
    function my_setcookie($name, $value = "", $sticky = 1)
    {
		global $ibforums;
        
        if ( $ibforums->no_print_header )
        {
        	return;
        }
        
        if ($sticky == 1)
        {
        	$expires = time() + 60*60*24*365;
        }

        $ibforums->vars['cookie_domain'] = $ibforums->vars['cookie_domain'] == "" ? ""  : $ibforums->vars['cookie_domain'];
        $ibforums->vars['cookie_path']   = $ibforums->vars['cookie_path']   == "" ? "/" : $ibforums->vars['cookie_path'];
        
        $name = $ibforums->vars['cookie_id'].$name;
      
        @setcookie($name, $value, $expires, $ibforums->vars['cookie_path'], $ibforums->vars['cookie_domain']);
    }
    
    /*-------------------------------------------------------------------------*/
    // Cookies, cookies everywhere and not a byte to eat.                
    /*-------------------------------------------------------------------------*/  
    
    function my_getcookie($name)
    {
		global $ibforums;
    	
    	if ( isset($_COOKIE[$ibforums->vars['cookie_id'].$name]) )
    	{
    		if ( ! in_array( $name, array('topicsread', 'forum_read', 'collapseprefs') ) )
    		{
    			return $this->clean_value(urldecode($_COOKIE[$ibforums->vars['cookie_id'].$name]));
    		}
    		else
    		{
    			return urldecode($_COOKIE[$ibforums->vars['cookie_id'].$name]);
    		}
    	}
    	else
    	{
    		return FALSE;
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // Makes incoming info "safe"              
    /*-------------------------------------------------------------------------*/
    
    function parse_incoming()
    {
		global $ibforums;
    	
    	$this->get_magic_quotes = get_magic_quotes_gpc();
    	
    	$return = array();
    	
		if( is_array($_GET) )
		{
			while( list($k, $v) = each($_GET) )
			{
				if ( is_array($_GET[$k]) )
				{
					while( list($k2, $v2) = each($_GET[$k]) )
					{
						$return[ $this->clean_key($k) ][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$return[ $this->clean_key($k) ] = $this->clean_value($v);
				}
			}
		}
		
		//-----------------------------------------
		// Overwrite GET data with post data
		//-----------------------------------------
		
		if( is_array($_POST) )
		{
			while( list($k, $v) = each($_POST) )
			{
				if ( is_array($_POST[$k]) )
				{
					while( list($k2, $v2) = each($_POST[$k]) )
					{
						$return[ $this->clean_key($k) ][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$return[ $this->clean_key($k) ] = $this->clean_value($v);
				}
			}
		}
		
		$return['request_method'] = strtolower($_SERVER['REQUEST_METHOD']);
		
		return $return;
	}
	
    /*-------------------------------------------------------------------------*/
    // Key Cleaner - ensures no funny business with form elements             
    /*-------------------------------------------------------------------------*/
    
    function clean_key($key)
    {
    	if ($key == "")
    	{
    		return "";
    	}
    	
    	$key = htmlspecialchars(urldecode($key));
    	$key = preg_replace( "/\.\./"           , ""  , $key );
    	$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
    	$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
    	
    	return $key;
    }
    
    /*-------------------------------------------------------------------------*/
    // Clean evil tags
    /*-------------------------------------------------------------------------*/
    
    function clean_evil_tags( $t )
    {
    	$t = preg_replace( "/javascript/i" , "j&#097;v&#097;script", $t );
		$t = preg_replace( "/alert/i"      , "&#097;lert"          , $t );
		$t = preg_replace( "/about:/i"     , "&#097;bout:"         , $t );
		$t = preg_replace( "/onmouseover/i", "&#111;nmouseover"    , $t );
		$t = preg_replace( "/onclick/i"    , "&#111;nclick"        , $t );
		$t = preg_replace( "/onload/i"     , "&#111;nload"         , $t );
		$t = preg_replace( "/onsubmit/i"   , "&#111;nsubmit"       , $t );
		$t = preg_replace( "/<body/i"      , "&lt;body"            , $t );
		$t = preg_replace( "/<html/i"      , "&lt;html"            , $t );
		$t = preg_replace( "/document\./i" , "&#100;ocument."      , $t );
		
		return $t;
    }
    
    /*-------------------------------------------------------------------------*/
    // Clean value
    /*-------------------------------------------------------------------------*/
    
    function clean_value($val)
    {
		global $ibforums;
    	
    	if ($val == "")
    	{
    		return "";
    	}
    
    	$val = str_replace( "&#032;", " ", $val );
    	
    	if ( $ibforums->vars['strip_space_chr'] )
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
    	$val = preg_replace( "/\n/"        , "<br />"        , $val ); // Convert literal newlines
    	$val = preg_replace( "/\\\$/"      , "&#036;"        , $val );
    	$val = preg_replace( "/\r/"        , ""              , $val ); // Remove literal carriage returns
    	$val = str_replace( "!"            , "&#33;"         , $val );
    	$val = str_replace( "'"            , "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
    	
    	// Ensure unicode chars are OK
    	
    	if ( $this->allow_unicode )
		{
			$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
		}
		
		// Strip slashes if not already done so.
		
    	if ( $this->get_magic_quotes )
    	{
    		$val = stripslashes($val);
    	}
    	
    	// Swop user inputted backslashes
    	
    	$val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val ); 
    	
    	return $val;
    }
    
    function remove_tags($text="")
    {
    	// Removes < BOARD TAGS > from posted forms
    	
    	$text = preg_replace( "/(<|&lt;)% (MEMBER BAR|BOARD FOOTER|BOARD HEADER|CSS|JAVASCRIPT|TITLE|BOARD|STATS|GENERATOR|COPYRIGHT|NAVIGATION) %(>|&gt;)/i", "&#60;% \\2 %&#62;", $text );
    	
    	//$text = str_replace( "<%", "&#60;%", $text );
    	
    	return $text;
    }
    
    function is_number($number="")
    {
    
    	if ($number == "") return -1;
    	
    	if ( preg_match( "/^([0-9]+)$/", $number ) )
    	{
    		return $number;
    	}
    	else
    	{
    		return "";
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // MEMBER FUNCTIONS             
    /*-------------------------------------------------------------------------*/
    
    function set_up_guest($name='Guest')
    {
		global $INFO;
    
    	return array( 'name'     => $name,
    				  'id'       => 0,
    				  'password' => "",
    				  'email'    => "",
    				  'title'    => "Unregistered",
    				  'mgroup'    => $INFO['guest_group'],
    				  'view_sigs' => $INFO['guests_sig'],
    				  'view_img'  => $INFO['guests_img'],
    				  'view_avs'  => $INFO['guests_ava'],
    				);
    }
    
    /*-------------------------------------------------------------------------*/
    // GET USER AVATAR         
    /*-------------------------------------------------------------------------*/
    
    function get_avatar($member_avatar="", $member_view_avatars=0, $avatar_dims="x", $avatar_type='')
    {
		global $ibforums;
    	
    	//-----------------------------------------
    	// No avatar?
    	//-----------------------------------------
    	
    	if ( ! $member_avatar or $member_view_avatars == 0 or ! $ibforums->vars['avatars_on'] or preg_match ( "/^noavatar/", $member_avatar ) )
    	{
    		return "";
    	}
    	
    	if ( (preg_match ( "/\.swf/", $member_avatar)) and ($ibforums->vars['allow_flash'] != 1) )
    	{
    		return "";
    	}
    	
    	//-----------------------------------------
    	// Defaults...
    	//-----------------------------------------
    	
    	$davatar_dims    = explode( "x", $ibforums->vars['avatar_dims'] );
		$default_a_dims  = explode( "x", $ibforums->vars['avatar_def'] );
    	$this_dims       = explode( "x", $avatar_dims );
		
		if (!$this_dims[0]) $this_dims[0] = $davatar_dims[0];
		if (!$this_dims[1]) $this_dims[1] = $davatar_dims[1];
		
    	//-----------------------------------------
    	// LEGACY: Determine type
    	//-----------------------------------------
		
		if ( ! $avatar_type )
		{
			if ( preg_match( "/^http:\/\//", $member_avatar ) )
			{
				$avatar_type = 'url';
			}
			else if ( strstr( $member_avatar, "upload:" ) or ( strstr( $member_avatar, 'av-' ) ) )
			{
				$avatar_type   = 'upload';
				$member_avatar = preg_replace( "/^upload:/", "", $member_avatar );
			}
			else
			{
				$avatar_type = 'local';
			}
	 	}
		
		//-----------------------------------------
		// URL avatar?
		//-----------------------------------------
		
		if ( $avatar_type == 'url' )
		{
			if (preg_match ( "/\.swf/", $member_avatar))
			{
				return "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width='{$this_dims[0]}' height='{$this_dims[1]}'>
						<param name='movie' value='{$member_avatar}'><param name='play' value='true'>
						<param name='loop' value='true'><param name='quality' value='high'>
						<embed src='{$member_avatar}' width='{$this_dims[0]}' height='{$this_dims[1]}' play='true' loop='true' quality='high'></embed>
						</object>";
			}
			else
			{
				return "<img src='{$member_avatar}' border='0' width='{$this_dims[0]}' height='{$this_dims[1]}' alt='' />";
			}
		}
		
		//-----------------------------------------
		// Not a URL? Is it an uploaded avatar?
		//-----------------------------------------
			
		else if ( ($ibforums->vars['avup_size_max'] > 1) and ( $avatar_type == 'upload' ) )
		{
			$member_avatar = str_replace( 'upload:', '', $member_avatar );
			
			if ( preg_match ( "/\.swf/", $member_avatar) )
			{
				return "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width='{$this_dims[0]}' height='{$this_dims[1]}'>
						<param name='movie' value='{$ibforums->vars['upload_url']}/$member_avatar'><param name='play' value='true'>
						<param name='loop' value='true'><param name='quality' value='high'>
					    <embed src='{$ibforums->vars['upload_url']}/$member_avatar\' width='{$this_dims[0]}' height='{$this_dims[1]}' play='true' loop='true' quality='high'></embed>
						</object>";
			}
			else
			{
				return "<img src='{$ibforums->vars['upload_url']}/$member_avatar' border='0' width='{$this_dims[0]}' height='{$this_dims[1]}' alt='' />";
			}
		}
		
		//-----------------------------------------
		// No, it's not a URL or an upload, must
		// be a normal avatar then
		//-----------------------------------------
		
		else if ($member_avatar != "")
		{
			//-----------------------------------------
			// Do we have an avatar still ?
		   	//-----------------------------------------
		   	
			return "<img src='{$ibforums->vars['AVATARS_URL']}/{$member_avatar}' border='0' alt='' />";
		}
		else
		{
			//-----------------------------------------
			// No, ok - return blank
			//-----------------------------------------
			
			return "";
		}
    }
 
 	
 	/*-------------------------------------------------------------------------*/
 	// Quick, INIT? a.k.a Just enough information to perform
 	// (Sorry, listening to stereophonics still)
 	/*-------------------------------------------------------------------------*/
 	
 	function quick_init()
 	{
		global $ibforums, $DB;
 		
 		$this->load_skin();
    	    
	   //-----------------------------------------
	   // Grab session cookie
	   //-----------------------------------------
			  
	   $ibforums->session_id = $sess->session_id ? $sess->session_id : $this->my_getcookie('session_id');
	   
	   //-----------------------------------------
	   // Organize default info
	   //-----------------------------------------
	   
	   $ibforums->base_url   = $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?s='.$ibforums->session_id;
	   $ibforums->skin_rid   = $ibforums->skin['set_id'];
	   $ibforums->skin_id    = 's'.$ibforums->skin['set_id'];
	   
	   if ($ibforums->vars['default_language'] == "")
	   {
		   $ibforums->vars['default_language'] = 'en';
	   }
	   
	   $ibforums->lang_id = $ibforums->member['language'] ? $ibforums->member['language'] : $ibforums->vars['default_language'];
	   
	   if ( ($ibforums->lang_id != $ibforums->vars['default_language']) and (! is_dir( ROOT_PATH."lang/".$ibforums->lang_id ) ) )
	   {
		   $ibforums->lang_id = $ibforums->vars['default_language'];
	   }
	   
	   //-----------------------------------------
	   // Get words & skin
	   //-----------------------------------------
	   
	   $ibforums->lang = $this->load_words($ibforums->lang, "lang_global", $ibforums->lang_id);
	   
	   $ibforums->vars['img_url'] = 'style_images/' . $ibforums->skin['_imagedir'];
	   
	   if ( $ibforums->skin_global == "" )
	   {
		   $this->load_template('skin_global');
	   }
 	}
 
    /*-------------------------------------------------------------------------*/
    // ERROR FUNCTIONS             
    /*-------------------------------------------------------------------------*/
    
    function Error($error)
    {
		global $DB, $ibforums, $sess;
    	
    	$override = 0;
    	
    	//-----------------------------------------
    	// Initialize if not done so yet
    	//-----------------------------------------
    	
    	if ( $error['INIT'] == 1)
    	{
    		$this->quick_init();
		}
		else
		{
			$ibforums->session_id = $ibforums->my_session;
		}
		
		if ( $ibforums->skin_global == "" )
		{
			$this->load_template('skin_global');
		}
		
		//-----------------------------------------
		// Get error words
		//-----------------------------------------
		
    	$ibforums->lang = $this->load_words($ibforums->lang, "lang_error", $ibforums->lang_id);
    	
    	list($em_1, $em_2) = explode( '@', $ibforums->vars['email_in'] );
    	
    	$msg = $ibforums->lang[ $error['MSG'] ];
    	
    	//-----------------------------------------
    	// Extra info?
    	//-----------------------------------------
    	
    	if ($error['EXTRA'])
    	{
    		$msg = preg_replace( "/<#EXTRA#>/", $error['EXTRA'], $msg );
    	}
    	
    	//-----------------------------------------
    	// Show error
    	//-----------------------------------------
    	
    	$html = $ibforums->skin_global->Error( $msg, $em_1, $em_2, 1);
    	
    	//-----------------------------------------
    	// If we're a guest, show the log in box..
    	//-----------------------------------------
    	
    	if ($ibforums->member['id'] == "" and $error['MSG'] != 'server_too_busy' and $error['MSG'] != 'account_susp')
    	{
    		$safe_string = str_replace( '&amp;', '&', $this->clean_value($_SERVER['QUERY_STRING']) );
    		
    		$html = str_replace( "<!--IBF.LOG_IN_TABLE-->", $ibforums->skin_global->error_log_in($safe_string), $html);
    		$override = 1;
    	}
    	
    	//-----------------------------------------
    	// Do we have any post data to keepy?
    	//-----------------------------------------
    	
    	if ( $ibforums->input['act'] == 'Post' OR $ibforums->input['act'] == 'Msg' OR $ibforums->input['act'] == 'calendar' )
    	{
    		if ( $_POST['Post'] )
    		{
    			$post_thing = $ibforums->skin_global->error_post_textarea($this->txt_htmlspecialchars($this->txt_stripslashes($_POST['Post'])) );
    			
    			$html = str_replace( "<!--IBF.POST_TEXTAREA-->", $post_thing, $html );
    		}
    	}
    	
    	//-----------------------------------------
    	// Update session
    	//-----------------------------------------
    	
    	$DB->do_shutdown_update( 'sessions', array( 'in_error' => 1 ), "id='{$ibforums->my_session}'" );
    	
    	//-----------------------------------------
    	// Print
    	//-----------------------------------------
    	
    	$print = new display();
    	
    	$print->add_output($html);
    		
    	$print->do_output( array( 'OVERRIDE' => $override, 'TITLE' => $ibforums->lang['error_title'] ) );
    }
    
    /*-------------------------------------------------------------------------*/
    // Show Board Offline
    /*-------------------------------------------------------------------------*/
    
    function board_offline()
    {
		global $DB, $ibforums;
    	
    	$this->quick_init();
    	
    	//-----------------------------------------
    	// Get offline message (not cached)
    	//-----------------------------------------
    	
    	$row = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings', 'where' => "conf_key='offline_msg'" ) );
    	
    	$ibforums->lang = $this->load_words($ibforums->lang, "lang_error", $ibforums->lang_id);
    	
    	$msg = preg_replace( "/\n/", "<br>", stripslashes( $row['conf_value'] ) );
    	
    	$html = $ibforums->skin_global->board_offline( $msg );
    	
    	$print = new display();
    	
    	$print->add_output($html);
    		
    	$print->do_output( array(
    								OVERRIDE   => 1,
    								TITLE      => $ibforums->lang['offline_title'],
    							 )
    					  );
    }
    								
    /*-------------------------------------------------------------------------*/
    // Variable chooser             
    /*-------------------------------------------------------------------------*/
    
    function select_var($array) {
    	
    	if ( !is_array($array) ) return -1;
    	
    	ksort($array);
    	
    	
    	$chosen = -1;  // Ensure that we return zero if nothing else is available
    	
    	foreach ($array as $k => $v)
    	{
    		if (isset($v))
    		{
    			$chosen = $v;
    			break;
    		}
    	}
    	
    	return $chosen;
    }
      
} // end class

?>