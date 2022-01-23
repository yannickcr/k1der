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
|   > Searching procedures
|   > Module written by Matt Mecham
|   > Date started: 24th February 2002
|
|	> Module Version Number: 1.1.0
|   > DBA Checked: Thu 20 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class search {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";
    
    var $first      = 0;
    
    var $search_type = 'posts';
    var $sort_order  = 'desc';
    var $sort_key    = 'last_post';
    var $search_in   = 'posts';
    var $prune       = '0';
    var $st_time     = array();
    var $end_time    = array();
    var $st_stamp    = "";
    var $end_stamp   = "";
    var $result_type = "topics";
    var $parser      = "";
    var $load_lib    = 'search_mysql_man';
    var $lib         = "";
    
    var $mysql_version   = "";
	var $true_version    = "";
	
	// max number of results
	
	var $resultlimit     = 1000;
    
    /*-------------------------------------------------------------------------*/
    // Auto run
    /*-------------------------------------------------------------------------*/
    
    function auto_run()
    {
		global $ibforums, $DB, $std, $print;
    	
    	if (! $ibforums->vars['allow_search'])
    	{
    		$std->Error( array( LEVEL => 1, MSG => 'search_off') );
    	}
    	
    	if ($ibforums->member['g_use_search'] != 1)
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    	}
    	
    	if ( $read = $std->my_getcookie('topicsread') )
        {
        	$this->read_array = unserialize(stripslashes($read));
        }
        
        $ibforums->forum_jump = $std->build_forum_jump();
    	
    	//-----------------------------------------
		// Get the SQL version.
		//-----------------------------------------
		
		$DB->sql_get_version();
		
		$this->true_version  = $DB->true_version;
		$this->mysql_version = $DB->mysql_version;
		
    	//-----------------------------------------
    	// Sort out the required search library
    	//-----------------------------------------
    	
    	$method = isset($ibforums->vars['search_sql_method']) ? $ibforums->vars['search_sql_method'] : 'man';
    	$sql    = isset($ibforums->vars['sql_driver'])        ? $ibforums->vars['sql_driver']        : 'mysql';
    	
    	$this->load_lib = 'search_'.strtolower($sql).'_'.$method.'.php';
    	
    	require ( ROOT_PATH."sources/lib/".$this->load_lib );
    	
    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------
    	
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_search', $ibforums->lang_id );
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_forum' , $ibforums->lang_id );
    	
    	$this->html = $std->load_template('skin_search'); 
    	
    	$this->base_url = $ibforums->base_url;
    	
    	//-----------------------------------------
    	// Suck in libby
    	//-----------------------------------------
    	
    	$this->lib = new search_lib(&$this);
    	
    	$this->first = intval($ibforums->input['st']);
    	
    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------
    	
    	if (! isset($ibforums->member['g_use_search']) )
    	{
    		$std->Error( array( LEVEL => 1, MSG => 'cant_use_feature') );
    	}
    	
    	switch($ibforums->input['CODE']) {
    		case '01':
    			$this->do_search();
    			break;
    		case 'getnew':
    			$this->get_new_posts();
    			break;
    		case 'show':
    			$this->show_results();
    			break;
    		case 'getreplied':
    			$this->get_replies();
    			break;
    		case 'lastten':
    			$this->get_last_ten();
    			break;
    		case 'getalluser':
    			$this->get_all_user();
    			break;
    		case 'simpleresults':
    			$this->show_simple_results();
    			break;
    		case 'explain':
    			$this->show_boolean_explain();
    			break;
    		case 'searchtopic':
    			$this->search_topic();
    			break;
    		case 'gettopicsuser':
    			$this->get_topics_user();
    			break;
    		case 'getactive':
    			$ibforums->input['active'] = 1;
    			$this->get_new_posts();
    			break;
    		default:
    			$this->show_form();
    			break;
    	}
    	
    	// If we have any HTML to print, do so...
    	
    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
 	}
 	
 	/*-------------------------------------------------------------------------*/
	// Do simple search
	/*-------------------------------------------------------------------------*/
	
	function show_simple_results()
	{
		global $ibforums, $DB, $std, $print;
    	
    	$result = $this->lib->do_simple_search();
    }
    
    /*-------------------------------------------------------------------------*/
	// Search topic
	/*-------------------------------------------------------------------------*/
	
	function search_topic()
	{
		global $ibforums, $DB, $std, $print;
    	
    	$this->topic_id          = intval($ibforums->input['topic']);
    	$this->topic_search_only = 1;
    	$this->result_type       = 'posts';
    	$this->search_type       = 'posts';
    	$this->search_in         = 'posts';
    	
    	$this->do_search();
    }
    
     /*-------------------------------------------------------------------------*/
	// Get all posts by a member
	/*-------------------------------------------------------------------------*/
 	
 	function get_topics_user()
 	{
		global $ibforums, $DB, $std, $print;
		
		//-----------------------------------------
		// Do we have flood control enabled?
		//-----------------------------------------
		
		if ($ibforums->member['g_search_flood'] > 0)
		{
			$flood_time = time() - $ibforums->member['g_search_flood'];
			
			//-----------------------------------------
			// Get any old search results..
			//-----------------------------------------
			
			$DB->simple_construct( array( 'select' => 'id',
										  'from'   => 'search_results',
										  'where'  => "(member_id='".$ibforums->member['id']."' OR ip_address='".$ibforums->input['IP_ADDRESS']."') AND search_date > '$flood_time'" ) );
			$DB->simple_exec();
		
			if ( $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_flood', 'EXTRA' => $ibforums->member['g_search_flood']) );
			}
		}
		
		$ibforums->input['forums'] = 'all';
		
		$forums = $this->get_searchable_forums();
		
		$mid    = intval($ibforums->input['mid']);
		
		//-----------------------------------------
		// Do we have any forums to search in?
		//-----------------------------------------
		
		if ($forums == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_forum') );
		}
		
		if ($mid == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
	
		//-----------------------------------------
		// Get the topic ID's to serialize and store into
		// the database
		//-----------------------------------------
		
		$DB->simple_construct( array( 'select' => 'count(*) as count', 'from' => 'topics t', 'where' => "t.approved=1 AND t.forum_id IN($forums) AND t.starter_id=$mid" ) );
		$DB->simple_exec();
		
		$row = $DB->fetch_row();
	
		$results = intval($row['count']);
		
		//-----------------------------------------
		// Do we have any results?
		//-----------------------------------------
		
		if ( ! $results )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
		
		//-----------------------------------------
		// Cache query
		//-----------------------------------------
		
		$DB->simple_construct( array( 'select' => 't.*, t.title as topic_title',
									  'from'   => 'topics t',
									  'where'  => "t.approved=1 AND t.forum_id IN($forums) AND t.starter_id=$mid",
									  'order'  => "t.last_post DESC" ) );
		
		
		$query_to_cache = $DB->cur_query;
		$DB->flush_query();
		
		//-----------------------------------------
		// If we are still here, store the data into the database...
		//-----------------------------------------
		
		$unique_id = md5(uniqid(microtime(),1));
		
		$DB->do_insert( 'search_results', array (
												  'id'          => $unique_id,
												  'search_date' => time(),
												  'post_max'    => $results,
												  'sort_key'    => $this->sort_key,
												  'sort_order'  => $this->sort_order,
												  'member_id'   => $ibforums->member['id'],
												  'ip_address'  => $ibforums->input['IP_ADDRESS'],
												  'query_cache' => $query_to_cache
										 )        );
		
		$std->boink_it( $ibforums->base_url."act=Search&nav=au&CODE=show&searchid=$unique_id&search_in=topics&result_type=topics" );
	}
    
    /*-------------------------------------------------------------------------*/
	// Get all posts by a member
	/*-------------------------------------------------------------------------*/
 	
 	function get_all_user()
 	{
		global $ibforums, $DB, $std, $print;
		
		//-----------------------------------------
		// Do we have flood control enabled?
		//-----------------------------------------
		
		if ($ibforums->member['g_search_flood'] > 0)
		{
			$flood_time = time() - $ibforums->member['g_search_flood'];
			
			// Get any old search results..
			
			$DB->simple_construct( array( 'select' => 'id',
										  'from'   => 'search_results',
										  'where'  => "(member_id='".$ibforums->member['id']."' OR ip_address='".$ibforums->input['IP_ADDRESS']."') AND search_date > '$flood_time'" ) );
			$DB->simple_exec();
			
			if ( $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_flood', 'EXTRA' => $ibforums->member['g_search_flood']) );
			}
		}
		
		$ibforums->input['forums'] = 'all';
		
		$forums = $this->get_searchable_forums();
		
		$mid    = intval($ibforums->input['mid']);
		
		//-----------------------------------------
		// Do we have any forums to search in?
		//-----------------------------------------
		
		if ($forums == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_forum') );
		}
		
		if ($mid == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
	
		//-----------------------------------------
		// Get the topic ID's to serialize and store into
		// the database
		//-----------------------------------------
		
		$DB->cache_add_query( 'search_get_all_user_count', array( 'mid' => $mid, 'forums' => $forums ) );
		$DB->cache_exec_query();
	
		$row = $DB->fetch_row();
	
		$results = intval($row['count']);
		
		//-----------------------------------------
		// Do we have any results?
		//-----------------------------------------
		
		if ( ! $results )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
		
		//-----------------------------------------
		// Cache query
		//-----------------------------------------
		
		$DB->cache_add_query( 'search_get_all_user_query', array( 'mid' => $mid, 'forums' => $forums ) );
		
		$query_to_cache = $DB->cur_query;
		$DB->flush_query();
		
		//-----------------------------------------
		// If we are still here, store the data into the database...
		//-----------------------------------------
		
		$unique_id = md5(uniqid(microtime(),1));
		
		$DB->do_insert( 'search_results', array (
												 'id'          => $unique_id,
												 'search_date' => time(),
												 'post_max'    => $results,
												 'sort_key'    => $this->sort_key,
												 'sort_order'  => $this->sort_order,
												 'member_id'   => $ibforums->member['id'],
												 'ip_address'  => $ibforums->input['IP_ADDRESS'],
												 'query_cache' => $query_to_cache
										)        );
		
		$std->boink_it( $ibforums->base_url."act=Search&nav=au&CODE=show&searchid=$unique_id&search_in=posts&result_type=posts" );
	}
 	
 	/*-------------------------------------------------------------------------*/
 	// Get new posts
 	/*-------------------------------------------------------------------------*/
 	
 	function get_new_posts()
 	{
		global $ibforums, $DB, $std, $HTTP_POST_VARS, $print;
		
		if ( ! $ibforums->member['id'] and ! $ibforums->input['active'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
		
		//-----------------------------------------
		// Do we have flood control enabled?
		//-----------------------------------------
		
		if ($ibforums->member['g_search_flood'] > 0)
		{
			$flood_time = time() - $ibforums->member['g_search_flood'];
			
			// Get any old search results..
			
			$DB->simple_construct( array( 'select' => 'id',
										  'from'   => 'search_results',
										  'where'  => "(member_id='".$ibforums->member['id']."' OR ip_address='".$ibforums->input['IP_ADDRESS']."') AND search_date > '$flood_time'" ) );
			$DB->simple_exec();
			
			if ( $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_flood', 'EXTRA' => $ibforums->member['g_search_flood']) );
			}
		}
		
		$last_time = $ibforums->member['last_visit'];
		
		if ( $ibforums->forum_read[0] > $last_time )
		{
			$last_time = $ibforums->forum_read[0];
		}
		
		//-----------------------------------------
		// Are we getting 'active topics'?
		//-----------------------------------------
		
		if ( $ibforums->input['active'] )
		{
			if ( $ibforums->input['lastdate'] )
			{
				$last_time = time() - intval($ibforums->input['lastdate']);
			}
			else
			{
				$last_time = time() - 86400;
			}
		}
		
		$ibforums->input['forums'] = 'all';
		$ibforums->input['nav']    = 'lv';
		
		$forums = $this->get_searchable_forums();
		
		//-----------------------------------------
		// Do we have any forums to search in?
		//-----------------------------------------
		
		if ($forums == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_forum') );
		}
		
		//-----------------------------------------
		// Get the topic ID's to serialize and store into
		// the database
		//-----------------------------------------
		
		$DB->simple_construct( array( 'select' => 'count(*) as count', 'from' => 'topics', 'where' => "approved=1 AND forum_id IN($forums) AND last_post > '".$last_time."'" ) );
		$DB->simple_exec();
		
		$row = $DB->fetch_row();
		
		$results = intval($row['count']);
		
		//-----------------------------------------
		// Do we have any results?
		//-----------------------------------------
		
		if ( ! $results )
		{
			//$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
		
		//-----------------------------------------
		// Cache query
		//-----------------------------------------
		
		$DB->simple_construct( array( 'select' => 't.*, t.title as topic_title',
									  'from'   => 'topics t',
									  'where'  => "t.approved=1 AND t.forum_id IN($forums) AND t.last_post > {$last_time}",
									  'order'  => "t.last_post DESC" ) );
		
		
		$query_to_cache = $DB->cur_query;
		$DB->flush_query();
		
		//-----------------------------------------
		// If we are still here, store the data into the database...
		//-----------------------------------------
		
		$unique_id = md5(uniqid(microtime(),1));
		
		$DB->do_insert( 'search_results', array (
												 'id'          => $unique_id,
												 'search_date' => time(),
												 'post_max'    => $results,
												 'sort_key'    => $this->sort_key,
												 'sort_order'  => $this->sort_order,
												 'member_id'   => $ibforums->member['id'],
												 'ip_address'  => $ibforums->input['IP_ADDRESS'],
												 'query_cache' => $query_to_cache
										)        );
		
		$std->boink_it( $ibforums->base_url."act=Search&nav=lv&CODE=show&searchid=$unique_id&search_in=topics&result_type=topics&lastdate={$ibforums->input['lastdate']}" );
	}
 	
 	/*-------------------------------------------------------------------------*/
 	// Last 10 posts
 	/*-------------------------------------------------------------------------*/
 	
 	function get_last_ten()
 	{
		global $ibforums, $DB, $std, $forums, $print;
		
		//-----------------------------------------
		// Do we have flood control enabled?
		//-----------------------------------------
		
		if ($ibforums->member['g_search_flood'] > 0)
		{
			$flood_time = time() - $ibforums->member['g_search_flood'];
			
			// Get any old search results..
			
			$DB->simple_construct( array( 'select' => 'id',
										  'from'   => 'search_results',
										  'where'  => "(member_id='".$ibforums->member['id']."' OR ip_address='".$ibforums->input['IP_ADDRESS']."') AND search_date > '$flood_time'" ) );
			$DB->simple_exec();
			
			if ( $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_flood', 'EXTRA' => $ibforums->member['g_search_flood']) );
			}
		}
		
		$ibforums->input['forums'] = 'all';
		
		$forums = $this->get_searchable_forums();
		
		$mid    = $ibforums->member['id'];
		
		//-----------------------------------------
		// Do we have any forums to search in?
		//-----------------------------------------
		
		if ($forums == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_forum') );
		}
		
		//-----------------------------------------
		// Cache query
		//-----------------------------------------
		
		$DB->cache_add_query( 'search_get_last_ten', array( 'mid' => $mid, 'forums' => $forums ) );
		
		$query_to_cache = $DB->cur_query;
		$DB->flush_query();
		
		//-----------------------------------------
		// If we are still here, store the data into the database...
		//-----------------------------------------
		
		$unique_id = md5(uniqid(microtime(),1));
		
		$DB->do_insert( 'search_results', array (
												 'id'          => $unique_id,
												 'search_date' => time(),
												 'post_max'    => 10,
												 'sort_key'    => $this->sort_key,
												 'sort_order'  => $this->sort_order,
												 'member_id'   => $ibforums->member['id'],
												 'ip_address'  => $ibforums->input['IP_ADDRESS'],
												 'query_cache' => $query_to_cache
										)        );
		
		$std->boink_it( $ibforums->base_url."act=Search&nav=au&CODE=show&searchid=$unique_id&search_in=posts&result_type=posts" );
	}
 	
 	/*-------------------------------------------------------------------------*/
 	// Get all replies
 	/*-------------------------------------------------------------------------*/
 	
 	function get_replies()
 	{
		global $ibforums, $DB, $std, $HTTP_POST_VARS, $print;
		
		//-----------------------------------------
		// Do we have flood control enabled?
		//-----------------------------------------
		
		if ($ibforums->member['g_search_flood'] > 0)
		{
			$flood_time = time() - $ibforums->member['g_search_flood'];
			
			// Get any old search results..
			
			$DB->simple_construct( array( 'select' => 'id',
										  'from'   => 'search_results',
										  'where'  => "(member_id='".$ibforums->member['id']."' OR ip_address='".$ibforums->input['IP_ADDRESS']."') AND search_date > '$flood_time'" ) );
			$DB->simple_exec();
			
			if ( $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_flood', 'EXTRA' => $ibforums->member['g_search_flood']) );
			}
		}
		
		$ibforums->input['forums'] = 'all';
		$ibforums->input['nav']    = 'lv';
		
		$forums = $this->get_searchable_forums();
		
		if ( $ibforums->forum_read[0] > $ibforums->member['last_visit'] )
		{
			$ibforums->member['last_visit'] = $ibforums->forum_read[0];
		}
			
		//-----------------------------------------
		// Do we have any forums to search in?
		//-----------------------------------------
		
		if ($forums == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_forum') );
		}
	
		//-----------------------------------------
		// Get the topic ID's to serialize and store into
		// the database
		//-----------------------------------------
		
		$DB->simple_construct( array( 'select' => 'tid',
									  'from'   => 'topics',
									  'where'  => "starter_id={$ibforums->member['id']} AND last_post > {$ibforums->member['last_visit']} AND forum_id IN($forums) AND approved=1" ) );
		$DB->simple_exec();
		
		$max_hits = $DB->get_num_rows();
		
		$topics  = "";
		
		while ($row = $DB->fetch_row() )
		{
			$topics .= $row['tid'].",";
		}
	
		$DB->free_result();
		
		$topics  = preg_replace( "/,$/", "", $topics );
		
		//-----------------------------------------
		// Do we have any results?
		//-----------------------------------------
		
		if ($topics == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
		
		//-----------------------------------------
		// If we are still here, store the data into the database...
		//-----------------------------------------
		
		$unique_id = md5(uniqid(microtime(),1));
		
		$DB->do_insert( 'search_results', array (
												 'id'         => $unique_id,
												 'search_date'=> time(),
												 'topic_id'   => $topics,
												 'topic_max'  => $max_hits,
												 'sort_key'   => $this->sort_key,
												 'sort_order' => $this->sort_order,
												 'member_id'  => $ibforums->member['id'],
												 'ip_address' => $ibforums->input['IP_ADDRESS'],
										)        );
		
		$print->redirect_screen( $ibforums->lang['search_redirect'] , "act=Search&nav=gr&CODE=show&searchid=$unique_id&search_in=posts&result_type=topics" );
		exit();
	}
	
	/*-------------------------------------------------------------------------*/
	// Show pop-up window
	/*-------------------------------------------------------------------------*/
	
	function show_boolean_explain()
 	{
		global $DB, $std, $ibforums, $print;
 		
 		$print->pop_up_window( $ibforums->lang['be_link'], $this->html->boolean_explain_page() );
 	}
	
	/*-------------------------------------------------------------------------*/
	// Show main form
	/*-------------------------------------------------------------------------*/
 	
 	function show_form()
 	{
		global $DB, $std, $ibforums, $forums;
 		
		$the_html = $forums->forums_forum_jump(1, 1);
		
		if ( ! $ibforums->input['f'] )
		{
			$init_sel = ' selected="selected"';
		}
		
		$forums   = "<select name='forums[]' class='forminput' size='10' multiple='multiple'>\n"
		           ."<option value='all'".$init_sel.">".$ibforums->lang['all_forums']."</option>"
		           . $the_html
		           . "</select>";
		
		if ( $ibforums->input['mode'] == 'simple' )
		{
			if ( $ibforums->vars['search_sql_method'] == 'ftext' )
			{
				$this->output = $this->html->simple_form($forums);
			}
			else
			{
				$this->output = $this->html->Form($forums);
			}
		}
		else if ( $ibforums->input['mode'] == 'adv' )
		{
			$this->output = $this->html->Form($forums);
			
			if ( $ibforums->vars['search_sql_method'] == 'ftext' )
			{
				$this->output = str_replace( "<!--IBF.SIMPLE_BUTTON-->", $this->html->form_simple_button(), $this->output );
			}
		}
		else
		{
			// No mode specified..
			
			if ( $ibforums->vars['search_default_method'] == 'simple' )
			{
				if ( $ibforums->vars['search_sql_method'] == 'ftext' )
				{
					$this->output = $this->html->simple_form($forums);
				}
				else
				{
					$this->output = $this->html->Form($forums);
				}
			}
			else
			{
				// Default..
				
				$this->output = $this->html->Form($forums);
				
				if ( $ibforums->vars['search_sql_method'] == 'ftext' )
				{
					$this->output = str_replace( "<!--IBF.SIMPLE_BUTTON-->", $this->html->form_simple_button(), $this->output );
				}
			}
		}
		
		if ( ( $DB->sql_can_fulltext_boolean() == TRUE ) AND $ibforums->vars['search_sql_method'] == 'ftext' )
		{
			$this->output = str_replace( "<!--IBF.BOOLEAN_EXPLAIN-->", $this->html->boolean_explain_link(), $this->output );
		}
		
		$this->page_title = $ibforums->lang['search_title'];
		$this->nav        = array( $ibforums->lang['search_form'] );
 	}
 	
 	/*-------------------------------------------------------------------------*/
 	// DO MAIN SEARCH
 	/*-------------------------------------------------------------------------*/

	function do_search()
	{
		global $ibforums, $DB, $std, $HTTP_POST_VARS, $print;
		
		//-----------------------------------------
		// Do we have flood control enabled?
		//-----------------------------------------
		
		if ($ibforums->member['g_search_flood'] > 0)
		{
			$flood_time = time() - $ibforums->member['g_search_flood'];
			
			// Get any old search results..
			
			$DB->simple_construct( array( 'select' => 'id',
										  'from'   => 'search_results',
										  'where'  => "(member_id='".$ibforums->member['id']."' OR ip_address='".$ibforums->input['IP_ADDRESS']."') AND search_date > '$flood_time'" ) );
			$DB->simple_exec();
			
			if ( $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_flood', 'EXTRA' => $ibforums->member['g_search_flood']) );
			}
		}
		
		//-----------------------------------------
		// init main search
		//-----------------------------------------
		
		$result = $this->lib->do_main_search();
		
		//-----------------------------------------
		// Do we have any results?
		//-----------------------------------------
		
		if ($result['topic_id'] == "" and $result['post_id'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
		
		//-----------------------------------------
		// If we are still here, store the data into the database...
		//-----------------------------------------
		
		$unique_id = md5(uniqid(microtime(),1));
		
		$DB->do_insert( 'search_results', array (
												  'id'          => $unique_id,
												  'search_date' => time(),
												  'topic_id'    => $result['topic_id'],
												  'topic_max'   => $result['topic_max'],
												  'sort_key'    => $this->sort_key,
												  'sort_order'  => $this->sort_order,
												  'member_id'   => $ibforums->member['id'],
												  'ip_address'  => $ibforums->input['IP_ADDRESS'],
												  'post_id'     => $result['post_id'],
												  'post_max'    => $result['post_max'],
												  'query_cache' => $result['query_cache'],
									  )        );
		
		$print->redirect_screen( $ibforums->lang['search_redirect'] , "act=Search&CODE=show&searchid=$unique_id&search_in=".$this->search_in."&result_type=".$this->result_type."&highlite=".urlencode(trim($result['keywords'])) );
	}
	
	/*-------------------------------------------------------------------------*/
	// Show Results
	// Shows the results of the search
	/*-------------------------------------------------------------------------*/
	
	function show_results()
	{
		global $ibforums, $DB, $std, $forums, $HTTP_POST_VARS;
		
		$this->cached_query   = 0;
		$this->cached_matches = 0;
		
		//-----------------------------------------
		// Grab the post parser
		//-----------------------------------------
		
		require_once( ROOT_PATH."sources/lib/post_parser.php" );
       	$this->parser = new post_parser();
       	
       	//-----------------------------------------
       	// Grab forums lib
       	//-----------------------------------------
       	
       	require_once( ROOT_PATH."sources/forums.php" );
       	$this->forums = new forums();
       	$this->forums->init();
       	
       	//-----------------------------------------
       	// Start...
       	//-----------------------------------------
		
        $this->result_type  = $ibforums->input['result_type'];
        $this->search_in    = $ibforums->input['search_in'];
		
		//-----------------------------------------
		// We have a search ID, so lets get the parsed results.
		//-----------------------------------------
		
		$this->unique_id = $ibforums->input['searchid'];
		
		if ($this->unique_id == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
		}
		
		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'search_results',
									  'where'  => "id='{$this->unique_id}'" ) );
		$DB->simple_exec();
		
		$sr = $DB->fetch_row();
		
		$this->sort_order   = $sr['sort_order'];
		$this->sort_key     = $sr['sort_key'];
		$this->more_results = $sr['query_cache'] == 1 ? 1 : 0;
		
		//-----------------------------------------
		// Cached query or PID/TID list?
		// query_cache == 1 if more than 300 results
		//-----------------------------------------
		
		if ( $sr['query_cache'] and $sr['query_cache'] != 1 )
		{
			$this->cached_query   = $sr['query_cache'];
			$this->cached_matches = $sr['post_max'];
			
			$DB->cur_query = $this->cached_query;
			$DB->simple_limit_with_check($this->first, "25");
			$this->cached_query = $DB->cur_query;
			$DB->flush_query();
		}
		else
		{
			$topics         = $sr['topic_id'];
			$topic_max_hits = $sr['topic_max'];
			$posts          = $sr['post_id'];
			$post_max_hits  = $sr['post_max'];
			
			//-----------------------------------------
			// Build array
			//-----------------------------------------
			
			$topic_array = array();
			$post_array  = array();
			
			if ( $topics )
			{
				foreach( explode( ",", $topics ) as $t )
				{
					$topic_array[ $t ] = $t;
				}
			}
			
			if ( $posts )
			{
				foreach( explode( ",", $posts ) as $t )
				{
					$post_array[ $t ] = $t;
				}
			}
			
			//-----------------------------------------
			// Anything left to show?
			//-----------------------------------------
			
			if ( ! $topics and ! $posts )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
			}
		}
		
		$url_words = $this->convert_highlite_words($ibforums->input['highlite']);
		
		if ($this->result_type == 'topics')
		{
			//-----------------------------------------
			// CACHED QUERY?
			//-----------------------------------------
			
			if ( $this->cached_query )
			{
				$this->output .= $this->start_page($this->cached_matches);
				
				$DB->prefix_changed = 1;
				$DB->query( $this->cached_query );
				$DB->prefix_changed = 0;
			}
			//-----------------------------------------
			// PID / TID
			//-----------------------------------------
			
			else if ($this->search_in == 'titles')
			{
				if ( ! $topics )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
				}
			
				$this->output .= $this->start_page($topic_max_hits);
				
				$DB->simple_construct( array( 'select' => 't.*',
											  'from'   => 'topics t',
											  'where'  => "t.tid IN({$topics})",
											  'order'  => "t.pinned DESC, t.".$this->sort_key." ".$this->sort_order,
											  'limit'  => array( $this->first, 25 )
									 )      );
				$DB->simple_exec();
			}
			else
			{
				//-----------------------------------------
				// Add posts to the mix
				//-----------------------------------------
				
				if ($posts)
				{
					$DB->simple_construct( array( 'select' => 'topic_id', 'from' => 'posts', 'where' => "pid IN({$posts})" ) );
					$DB->simple_exec();
				
					while ( $pr = $DB->fetch_row() )
					{
						$topic_array[ $pr['topic_id'] ] = $pr['topic_id'];
					}
					
					$topics         = implode( ",", $topic_array );
					$topic_max_hits = count( $topic_array );
				}
				
				$this->output .= $this->start_page($topic_max_hits);
				
				$DB->simple_construct( array( 'select' => 't.*',
											  'from'   => 'topics t',
											  'where'  => "t.tid IN({$topics})",
											  'order'  => "t.pinned DESC, t.".$this->sort_key." ".$this->sort_order,
											  'limit'  => array( $this->first, 25 )
									 )      );
				$DB->simple_exec();
			}
			
			//-----------------------------------------
			// PRINT: Any returned rows?
			//-----------------------------------------
			
			if ( $DB->get_num_rows() )
			{
				while ( $row = $DB->fetch_row() )
				{
					$row['keywords'] = $url_words;
					$this->output   .= $this->html->RenderRow( $this->parse_entry($row) );
				
				}
			}
			else
			{
				if ( ! $ibforums->input['lastdate'] )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
				}
				else
				{
					$this->output .= $this->html->no_results_row(); 
				}
			}
			
			//-----------------------------------------
			// PRINT: End the page
			//-----------------------------------------
			
			$this->output .= $this->html->end_results_table(array( 'SHOW_PAGES' => $this->links ));
		}
		
		//-----------------------------------------
		// Results as posts...
		//-----------------------------------------
		
		else
		{
			//-----------------------------------------
			// Grab topic lib
			//-----------------------------------------
			
			require_once( ROOT_PATH.'sources/topics.php' );
			$this->topics = new topics();
			$this->topics->topic_init();
			
			$attach_pids = array();
			
			//-----------------------------------------
			// CACHED QUERY?
			//-----------------------------------------
			
			if ( $this->cached_query )
			{
				$this->output .= $this->start_page($this->cached_matches, 1);
				
				$DB->query( $this->cached_query );
			}
			//-----------------------------------------
			// PID / TID
			//-----------------------------------------
			
			else
			{
				//-----------------------------------------
				// Start...
				//-----------------------------------------
				
				if ($this->search_in == 'titles')
				{
					$this->output .= $this->start_page($topic_max_hits, 1);
					
					$DB->cache_add_query( 'search_main_in_titles', array( 'topics' => $topics, 'limit_a' => $this->first ) );
					$DB->cache_exec_query();
				}
				else
				{
					//-----------------------------------------
					// Add Topics
					//-----------------------------------------
					
					if ($topics)
					{
						$DB->simple_construct( array( 'select' => 'pid', 'from' => 'posts', 'where' => "topic_id IN({$topics}) AND new_topic=1" ) );
						$DB->simple_exec();
					
						while ( $pr = $DB->fetch_row() )
						{
							$post_array[ $pr['pid'] ] = $pr['pid'];
						}
						
						$posts         = implode( ",", $post_array );
						$post_max_hits = count( $post_array );
					}
					
					$this->output .= $this->start_page($post_max_hits, 1);
					
					$DB->cache_add_query( 'search_main_in_posts', array( 'posts' => $posts, 'limit_a' => $this->first ) );
					$DB->cache_exec_query();
				}
			}
			
			while ( $row = $DB->fetch_row() )
			{
				$row['keywords']  = $url_words;
				$row['post_date'] = $std->get_date( $row['post_date'],'LONG' );
				
				//-----------------------------------------
				// Parse HTML tag on the fly
				//-----------------------------------------
				
				$this->parser->pp_do_html  = ( $forums->forum_by_id[$row['forum_id']]['use_html'] and $ibforums->cache['group_cache'][ $row['mgroup'] ]['g_dohtml'] and $row['post_htmlstate'] ) ? 1 : 0;
				$this->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
				$this->parser->pp_nl2br    = $row['post_htmlstate'] == 2 ? 1 : 0;
				
				$row['post'] = $this->parser->post_db_parse( $row['post'] );
				
				//-----------------------------------------
				// Attachments?
				//-----------------------------------------
				
				if ( strstr( $row['post'], '[attachmentid=' ) )
				{
					$attach_pids[] = $row['pid'];
				}
				
				//-----------------------------------------
				// Parse Member
				//-----------------------------------------
				
				$member = $this->topics->parse_member( $row );
				
				//-----------------------------------------
				// Do word wrap?
				//-----------------------------------------
				
				if ( $ibforums->vars['post_wordwrap'] > 0 )
				{
					$row['post'] = $this->parser->my_wordwrap( $row['post'], $ibforums->vars['post_wordwrap']) ;
				}
				
				//-----------------------------------------
				// Do word wrap?
				//-----------------------------------------
				
				$this->output .= $this->html->RenderPostRow( $this->parse_entry($row, 1), $member );
			
			}
			
			//-----------------------------------------
			// Add in attachments?
			//-----------------------------------------
			
			if ( count( $attach_pids ) )
			{
				$this->output = $this->topics->parse_attachments( $this->output, $attach_pids );
			}
			
			$this->output .= $this->html->end_results_table(array( 'SHOW_PAGES' => $this->links ), 1 );
		}
		
		$this->page_title = $ibforums->lang['search_results'];
		
		if ( $ibforums->input['nav'] == 'lv' )
		{
			if ( $ibforums->input['lastdate'] )
			{
				$this->nav = array( $ibforums->lang['nav_au'] );
			}
			else
			{
				$this->nav = array( $ibforums->lang['nav_since_lv'] );
			}
		}
		else if ( $ibforums->input['nav'] == 'lt' )
		{
			$this->nav = array( $ibforums->lang['nav_lt'] );
		}
		else
		{
			$this->nav = array( "<a href='{$this->base_url}&act=Search'>{$ibforums->lang['search_form']}</a>", $ibforums->lang['search_title'] );
		}
		
		//-----------------------------------------
		// Active topics fing?
		//-----------------------------------------
		
		if ( $ibforums->input['lastdate'] )
		{
			$this->output = preg_replace( "#(value=[\"']{$ibforums->input['lastdate']}[\"'])#i", "\\1 selected='selected'", $this->output );
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Start the page functions
	/*-------------------------------------------------------------------------*/
	
	function start_page($amount, $is_post = 0)
	{
		global $ibforums, $DB, $std;
		
		$url_words = $this->convert_highlite_words($ibforums->input['highlite']);
		$extra     = $this->more_results ? str_replace( '%num', $this->resultlimit, $ibforums->lang['too_many_children_for_santa'] ) : "";
		
		$this->links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $amount,
													 'PER_PAGE'    => 25,
													 'CUR_ST_VAL'  => $this->first,
													 'L_SINGLE'    => "",
													 'L_MULTI'     => $ibforums->lang['search_pages'],
													 'BASE_URL'    => $this->base_url."act=Search&nav=".$ibforums->input['nav']."&CODE=show&searchid=".$this->unique_id."&search_in=".$this->search_in."&result_type=".$this->result_type."&hl=".$url_words,
											)	   );
									  
		if ($is_post == 0)
		{
			return $this->html->start( array( 'SHOW_PAGES' => $this->links ), $extra  );
		}
		else
		{
			return $this->html->start_as_post( array( 'SHOW_PAGES' => $this->links ), $extra );
		}
	}
    
    /*-------------------------------------------------------------------------*/
    // Parse search result entry
    /*-------------------------------------------------------------------------*/
    
	function parse_entry($topic, $view_as_post=0)
	{
		global $DB, $std, $ibforums, $forums;
		
		$ibforums->input['last_visit'] = $ibforums->forum_read[ $topic['forum_id'] ] > $ibforums->input['last_visit']
        						       ? $ibforums->forum_read[ $topic['forum_id'] ] : $ibforums->input['last_visit'];
		
		//-----------------------------------------
		// Over ride with 'master' cookie?
		//-----------------------------------------
		
		if ( $ibforums->forum_read[0] > $ibforums->forum_read[ $topic['forum_id'] ] )
		{
			$ibforums->forum_read[ $topic['forum_id'] ] = $ibforums->forum_read[0];
		}
		
		//-----------------------------------------
		// Disable DB tracking...
		//-----------------------------------------
		
		$tmp = $ibforums->vars['db_topic_read_cutoff'];
		$ibforums->vars['db_topic_read_cutoff'] = 0;
		
		//-----------------------------------------
		// Stop search from marking forum as read
		//-----------------------------------------
		
		$this->forums->new_posts = 1;
		
		$topic = $this->forums->parse_data( $topic );
		
		$ibforums->vars['db_topic_read_cutoff'] = $tmp;
		
		if ($topic['pinned'] == 1)
		{
			$topic['prefix']     = $ibforums->vars['pre_pinned'];
			$topic['topic_icon'] = "<{B_PIN}>";
		}
		
		//-----------------------------------------
		// Extra processing for posts..
		//-----------------------------------------
		
		if ($view_as_post == 1)
		{
			if ( $ibforums->vars['search_post_cut'] )
			{
				$topic['post'] = substr( $this->parser->unconvert($topic['post'] ), 0, $ibforums->vars['search_post_cut']) . '...';
				$topic['post'] = str_replace( "\n", "<br />", $topic['post'] );
			}
			
			if ($topic['author_id'])
			{
				$topic['author_name'] = "<b><a href='{$this->base_url}showuser={$topic['author_id']}'>{$topic['author_name']}</a></b>";
			}
			
			//-----------------------------------------
			// Highlighting?
			//-----------------------------------------
			
			if ($topic['keywords'])
			{
				$keywords = str_replace( "+", " ", $topic['keywords'] );
				
				if ( preg_match("/,(and|or),/i", $keywords) )
				{
					while ( preg_match("/,(and|or),/i", $keywords, $match) )
					{
						$word_array = explode( ",".$match[1].",", $keywords );
						
						if (is_array($word_array))
						{
							foreach ($word_array as $keywords)
							{
								$topic['post'] = preg_replace( "/(^|\s|,!|;)(".preg_quote($keywords, "/").")(\s|,|!|&|$)/i", "\\1<span class='searchlite'>\\2</span>\\3", $topic['post'] );
							}
						}
					}
				}
				else
				{
					$topic['post'] = preg_replace( "/(^|\s|,!|;)(".preg_quote($keywords, "/").")(\s|,|!|&|$)/i", "\\1<span class='searchlite'>\\2</span>\\3", $topic['post'] );
				}
			}
		}
		
		if ( ! $ibforums->member['view_img'] )
		{
			//-----------------------------------------
			// unconvert smilies first, or it looks a bit crap.
			//-----------------------------------------
			
			$topic['post'] = preg_replace( "#<!--emo&(.+?)-->.+?<!--endemo-->#", "\\1" , $topic['post'] );
			
			$topic['post'] = preg_replace( "/<img src=[\"'](.+?)[\"'].+?".">/", "(IMG:<a href='\\1' target='_blank'>\\1</a>)", $topic['post'] );
		}
		
		$topic['forum_full_name'] = $forums->forum_by_id[ $topic['forum_id'] ]['name'];
		
		if ( strlen($topic['forum_full_name']) > 50 )
		{
			$topic['forum_name'] = substr( $topic['forum_full_name'], 0, 47 ) .'...';
		}
		else
		{
			$topic['forum_name'] = $topic['forum_full_name'];
		}
		
		//$this->parser->pp_do_html  = ( $forums->forum_by_id[ $topic['forum_id'] ]['use_html'] and $ibforums->cache['group_cache'][ $ibforums->member['mgroup'] ]['g_dohtml'] and $topic['post_htmlstate'] ) ? 1 : 0;
		//$this->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
		//$this->parser->pp_nl2br    = $topic['post_htmlstate'] == 2 ? 1 : 0;
		
		//$topic['post'] = $this->parser->post_db_parse( $topic['post'] );
		
		return $topic;
	}
        
    /*-------------------------------------------------------------------------*/
    // Filter keywords
    /*-------------------------------------------------------------------------*/
        
    function filter_keywords($words="", $name=0)
    {
    	// force to lowercase and swop % into a safer version
    	
    	$words = trim( strtolower( str_replace( "%", "\\%", $words) ) );
    	
    	// Remove trailing boolean operators
    	
    	$words = preg_replace( "/\s+(and|or)$/" , "" , $words );
    	
    	// Swop wildcard into *SQL percent
    	
    	//$words = str_replace( "*", "%", $words );
    	
    	// Make safe underscores
    	
    	$words = str_replace( "_", "\\_", $words );
    	
    	$words = str_replace( '|', "&#124;", $words );
    	
    	// Remove crap
    	
    	if ($name == 0)
    	{
    		$words = preg_replace( "/[\|\[\]\{\}\(\)\,:\\\\\/\"']|&quot;/", "", $words );
    	}
    	
    	// Remove common words.. (should be expanded upon in a later release to return 'not searchable word'
    	
    	$words = preg_replace( "/^(?:img|quote|code|html|javascript|a href|color|span|div|border|style)$/", "", $words );
    	
    	return " ".preg_quote($words)." ";
    }
    
    /*-------------------------------------------------------------------------*/
    // Filter keywords
    /*-------------------------------------------------------------------------*/
    
    function filter_ftext_keywords($words="")
    {
		global $ibforums;
    	
    	// force to lowercase and swop % into a safer version
    	
    	$words = trim($words);
    	$words = str_replace( '|', "&#124;", $words );
    	
    	// Remove crap
    	
    	$words = str_replace( "&quot;", '"', $words );
    	$words = str_replace( "&gt;"  , ">", $words );
    	$words = str_replace( "%"     , "" , $words );
    	
    	//-----------------------------------------
    	// If it's a phrase in quotes..
    	//-----------------------------------------
    	
    	if ( preg_match( "#^\"(.+?)\"$#", $words ) )
    	{
    		return $words;
    	}
    	
    	// Remove common words..
    	
    	$words = preg_replace( "/^(?:img|quote|code|html|javascript|a href|color|span|div|border|style)$/", "", $words );
    	
    	// OK, lets break up the keywords
    	
    	// this or that and this not me
    	
    	$words = preg_replace( "/\s+and\s+/i", " ", $words );
    	
    	// this or that this not me
    	
    	$words = preg_replace( "/\s+not\s+/i", " -", $words );
    	
    	// this or that this -me
    	
    	$words = preg_replace( "/\s+or\s+/i", ' ~', $words );
    	
    	// this ~that this -me
    	
    	# Was added as a bug fix but really this causes more problems
    	# than it solves. Complaint was that it should default to AND
    	# matching, not OR matching. Problem is that it doesn't then
    	# give a "true" search as one would expect Google to do.
    	
    	//$words = preg_replace( "/\s+(?!-|~)/", " +", $words );
    	
    	
    	// this ~that +this -me
    	
    	$words = preg_replace( "/~/", "", $words );
    	
    	// this that +this -me
    	
    	return $words;
    }
    
    /*-------------------------------------------------------------------------*/
    // Make the hl words nice and stuff
    /*-------------------------------------------------------------------------*/
    
    function convert_highlite_words($words="")
    {
    	global $ibforums, $DB, $std, $forums;
    	
    	$words = $std->clean_value(trim(urldecode($words)));
    	
    	// Convert booleans to something easy to match next time around
    	
    	$words = preg_replace("/\s+(and|or)(\s+|$)/i", ",\\1,", $words);
    	
    	// Convert spaces to plus signs
    	
    	$words = preg_replace("/\s/", "+", $words);
    	
    	return $words;
    }
        
    /*-------------------------------------------------------------------------*/
    // Get the searchable forums
    /*-------------------------------------------------------------------------*/
        
    function get_searchable_forums()
    {
		global $ibforums, $DB, $std, $forums;
    	
    	$forumids = array();
    	
    	//-----------------------------------------
    	// Check for an array
    	//-----------------------------------------
    	
    	if ( is_array( $_POST['forums'] )  )
    	{
    	
    		if ( in_array( 'all', $_POST['forums'] ) )
    		{
    			//-----------------------------------------
    			// Searching all forums..
    			//-----------------------------------------
    			
    			foreach( $forums->forum_by_id as $id => $data )
    			{
    				$forumids[] = $data['id'];
    			}
    		}
    		else
    		{
				//-----------------------------------------
				// Go loopy loo
				//-----------------------------------------
				
				foreach( $_POST['forums'] as $l )
				{
					if ( $forums->forum_by_id[ $l ] )
					{
						$forumids[] = intval($l);
					}
				}
				
				//-----------------------------------------
				// Do we have cats? Give 'em to Charles!
				//-----------------------------------------
				
				if ( count( $forumids  ) )
				{
					foreach( $forumids as $f )
					{
						$children = $forums->forums_get_children( $f );
						
						if ( is_array($children) and count($children) )
						{
							$forumids  = array_merge( $forumids , $children );
						}
					}
				}
				else
				{
					//-----------------------------------------
					// No forums selected / we have available
					//-----------------------------------------
					
					return;
				}
    		}
		}
		else
		{
			//-----------------------------------------
			// Not an array...
			//-----------------------------------------
			
			if ( $ibforums->input['forums'] == 'all' )
			{
				foreach( $forums->forum_by_id as $id => $data )
    			{
    				$forumids[] = $data['id'];
    			}
			}
			else
			{
				if ( $ibforums->input['forums'] != "" )
				{
					$l = intval($ibforums->input['forums']);
					
					//-----------------------------------------
					// Single forum
					//-----------------------------------------
					
					if ( $forums->forum_by_id[ $l ] )
					{
						$forumids[] = intval($l);
					}
					
					if ( $ibforums->input['searchsubs'] == 1 )
					{
						$children = $forums->forums_get_children( $l );
						
						if ( is_array($children) and count($children) )
						{
							$forumids  = array_merge( $forumids , $children );
						}
					}
				}
			}
		}
		
		$final = array();
		
		foreach( $forumids  as $f )
		{
			if ( $this->check_access($forums->forum_by_id[ $f ] ) == TRUE )
			{
				$final[] = $f;
			}
		}
    	
    	return implode( "," , $final );
    }
        
    /*-------------------------------------------------------------------------*/
    // Check password...
    /*-------------------------------------------------------------------------*/
    
    function check_access($i)
    {
		global $std, $ibforums, $forums;
    	
    	$can_read = FALSE;
  
    	if ( $std->check_perms( $i['read_perms'] ) == TRUE )
    	{
    		$can_read = TRUE;
    	}
    	else
    	{
    		$can_read = FALSE;
    	}
        
        if ( $i['password'] != "" and $can_read == TRUE )
		{
			if ( $forums->forums_compare_password( $i['id'] ) == TRUE )
			{
				$can_read = TRUE;
			}
			else
			{
				$can_read = FALSE;
			}
		}
		
		return $can_read;
	}
        
}

?>