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
|   > Show all the members
|   > Module written by Matt Mecham
|   > Date started: 20th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class memberlist {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";

    var $first       = 0;
    var $max_results = 50;
    var $sort_key    = 'name';
    var $sort_order  = 'asc';
    var $filter      = 'ALL';

    var $mem_titles = array();
    var $mem_groups = array();

    var $ucp_html   = "";
    var $topic      = "";

    /*-------------------------------------------------------------------------*/
	// Auto-run
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_mlist', $ibforums->lang_id );

    	$this->html = $std->load_template('skin_mlist');

   allow guest access
 /*	if ($ibforums->member['g_mem_info'] != 1)
 		{
 			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    	}*/

    	//-----------------------------------------
    	// Grab topics.php to parse member
    	//-----------------------------------------

    	require_once( ROOT_PATH.'sources/topics.php' );
    	$this->topic = new topics();
    	$this->topic->topic_init( 1 );

    	$see_groups = array();

    	//-----------------------------------------
    	// Get the member groups, member titles stuff
    	//-----------------------------------------

    	foreach( $ibforums->cache['group_cache'] as $id => $row )
    	{
    		if ( $row['g_hide_from_list'] )
    		{
    			continue;
    		}

    		$see_groups[] = $row['g_id'];

    		$this->mem_groups[ $row['g_id'] ] = array( 'TITLE'  => $row['g_title'],
    												   'ICON'   => $row['g_icon'],
    											     );
    	}

    	$the_filter  = array( 'ALL' => $ibforums->lang['show_all'] );

    	foreach($this->mem_groups as $id => $data)
    	{
    		if ($id == $ibforums->vars['guest_group'])
    		{
    			continue;
    		}

    		$the_filter[$id] = $data['TITLE'];
    	}

    	$group_string = implode( ",", $see_groups );

    	//-----------------------------------------
    	// Test for input
    	//-----------------------------------------

    	if (isset($ibforums->input['st']))          $this->first       = intval($ibforums->input['st']);
    	if (isset($ibforums->input['max_results'])) $this->max_results = $ibforums->input['max_results'];
    	if (isset($ibforums->input['sort_key']))    $this->sort_key    = $ibforums->input['sort_key'];
    	if (isset($ibforums->input['sort_order']))  $this->sort_order  = $ibforums->input['sort_order'];
    	if (isset($ibforums->input['filter']))      $this->filter      = $ibforums->input['filter'];

    	//-----------------------------------------
    	// Init some arrays
    	//-----------------------------------------

    	$the_sort_key = array( 'name'    => 'sort_by_name',
    						   'posts'   => 'sort_by_posts',
    						   'joined'  => 'sort_by_joined',
    						 );

    	$the_max_results = array( 10  => '10',
    							  20  => '20',
    							  30  => '30',
    							  40  => '40',
    							  50  => '50',
    						    );

    	$the_sort_order = array(  'desc' => 'descending_order',
    							  'asc'  => 'ascending_order',
    						   );

    	//-----------------------------------------
    	// Start the form stuff
    	//-----------------------------------------

    	$filter_html      = "<select name='filter' class='forminput'>\n";
    	$sort_key_html    = "<select name='sort_key' class='forminput'>\n";
    	$max_results_html = "<select name='max_results' class='forminput'>\n";
    	$sort_order_html  = "<select name='sort_order' class='forminput'>\n";

    	foreach ($the_sort_order as $k => $v)
    	{
			$sort_order_html .= $k == $this->sort_order ? "<option value='$k' selected>" . $ibforums->lang[ $the_sort_order[ $k ] ] . "</option>\n"
											            : "<option value='$k'>"          . $ibforums->lang[ $the_sort_order[ $k ] ] . "</option>\n";
		}
     	foreach ($the_filter as $k => $v)
     	{
			$filter_html .= $k == $this->filter  ? "<option value='$k' selected>"         . $the_filter[ $k ] . "</option>\n"
											            : "<option value='$k'>"          . $the_filter[ $k ] . "</option>\n";
		}
    	foreach ($the_sort_key as $k => $v)
    	{
			$sort_key_html .= $k == $this->sort_key ? "<option value='$k' selected>"     . $ibforums->lang[ $the_sort_key[ $k ] ] . "</option>\n"
											            : "<option value='$k'>"          . $ibforums->lang[ $the_sort_key[ $k ] ] . "</option>\n";
		}
    	foreach ($the_max_results as $k => $v)
    	{
			$max_results_html .= $k == $this->max_results ? "<option value='$k' selected>". $the_max_results[ $k ] . "</option>\n"
											            : "<option value='$k'>"          . $the_max_results[ $k ] . "</option>\n";
		}

		$ibforums->lang['sorting_text'] = preg_replace( "/<#FILTER#>/"      , $filter_html."</select>"     , $ibforums->lang['sorting_text'] );
    	$ibforums->lang['sorting_text'] = preg_replace( "/<#SORT_KEY#>/"    , $sort_key_html."</select>"   , $ibforums->lang['sorting_text'] );
    	$ibforums->lang['sorting_text'] = preg_replace( "/<#SORT_ORDER#>/"  , $sort_order_html."</select>" , $ibforums->lang['sorting_text'] );
    	$ibforums->lang['sorting_text'] = preg_replace( "/<#MAX_RESULTS#>/" , $max_results_html."</select>", $ibforums->lang['sorting_text'] );

    	$error = 0;

    	if ( ! isset($the_sort_key[ $this->sort_key ]) )       $error = 1;
    	if ( ! isset($the_sort_order[ $this->sort_order ]) )   $error = 1;
    	if ( ! isset($the_filter[ $this->filter ]) )           $error = 1;
    	if ( ! isset($the_max_results[ $this->max_results ]) ) $error = 1;

    	//-----------------------------------------
    	// Error?
    	//-----------------------------------------

    	if ($error == 1 )
    	{
    		if ( $ibforums->input['b'] == 1 )
    		{
    			$std->Error( array( LEVEL=> 1, MSG =>'ml_error') );
    		}
    		else
    		{
    			$std->Error( array( LEVEL=> 5, MSG =>'incorrect_use') );
    		}
    	}

    	//-----------------------------------------
    	// Quick form?
    	//-----------------------------------------

    	$quick_jump = "";

    	for ( $i = 65; $i <= 90; $i++ )
    	{
    		$letter      = strtolower(chr($i));
    		$selected    = $ibforums->input['quickjump'] == $letter ? ' selected="selected"' : '';
    		$quick_jump .= $this->html->mlist_quick_jump_entry( $letter, $selected );
    	}

    	//-----------------------------------------
    	// Get custom profile information
    	//-----------------------------------------

		$custom_fields = "";

    	$this->topic->custom_fields->admin       = intval($ibforums->member['g_access_cp']);
    	$this->topic->custom_fields->supmod      = intval($ibforums->member['g_is_supmod']);
    	$this->topic->custom_fields->member_id   = $ibforums->member['id'];
    	$this->topic->custom_fields->mem_data_id = 0;

    	$this->topic->custom_fields->init_data();
    	$this->topic->custom_fields->parse_to_edit();

    	//-----------------------------------------
    	// Query..
    	//-----------------------------------------

    	$query        = array();
    	$url          = array();
    	$query_string = "";

    	//-----------------------------------------
    	// Quick jump rehash...
    	//-----------------------------------------

    	if ( $ibforums->input['qjbutton'] and $ibforums->input['quickjump'] )
    	{
    		$ibforums->input['name_box'] = 'begins';
    		$ibforums->input['name']     = $ibforums->input['quickjump'];
    	}

    	//-----------------------------------------
    	// Member Groups...
    	//-----------------------------------------

    	if ($this->filter != 'ALL')
    	{
    		if ( ! preg_match( "/(^|,)".$this->filter."(,|$)/", $group_string ) )
    		{
    			$query[] = "m.mgroup IN($group_string)";
    		}
    		else
    		{
    			$query[] = "m.mgroup='".$this->filter."' ";
    		}
    	}

    	//-----------------------------------------
    	// Build query
    	//-----------------------------------------

    	$dates = array( 'lastpost', 'lastvisit', 'joined' );

    	$mapit = array( 'aim'       => 'me.aim_name',
    					'yahoo'     => 'me.yahoo',
    					'icq'       => 'me.icq_number',
    					'msn'       => 'me.msnname',
    					'posts'     => 'm.posts',
    					'joined'    => 'm.joined',
    					'lastpost'  => 'm.last_post',
    					'lastvisit' => 'm.last_visit',
    					'signature' => 'me.signature',
    					'homepage'  => 'me.website',
    					'name'      => 'm.name',
    					'photoonly' => 'me.photo_location',
    				  );

    	//-----------------------------------------
    	// Do search
    	//-----------------------------------------

    	foreach( $mapit as $in => $tbl )
    	{
    		$inbit = $std->clean_value(trim(urldecode(stripslashes($ibforums->input[ $in ]))));

    		$url[] = $in.'='.$ibforums->input[ $in ];

    		//-----------------------------------------
    		// Name...
    		//-----------------------------------------

    		if ( $in == 'name' and $inbit != "" )
			{
				if ( $ibforums->input['name_box'] == 'begins' )
				{
					$query[] = "m.name LIKE '".$inbit."%'";
				}
				else
				{
					$query[] = "m.name LIKE '%".$inbit."%'";
				}
			}
			else if ( $in == 'posts' and intval($inbit) > 0 )
			{
				$ltmt = $ibforums->input[ $in .'_ltmt' ] == 'lt' ? '<' : '>';
				$query[]  = $tbl. ' '.$ltmt.' '.intval($inbit);
				$url[]    = $in .'_ltmt=' . $ibforums->input[ $in .'_ltmt' ];
			}
			else if ( in_array( $in, $dates ) and $inbit )
			{
				list( $month, $day, $year ) = explode( '-', $ibforums->input[ $in ] );

				$month = intval($month);
				$day   = intval($day);
				$year  = intval($year);

				if ( ! checkdate( $month, $day, $year ) )
				{
					continue;
				}

				$time_int = mktime( 0, 0 ,0,$month, $day, $year );

				$ltmt = $ibforums->input[ $in .'_ltmt' ] == 'lt' ? '<' : '>';

				$query[]  = $tbl. ' '.$ltmt.' '.$time_int;
				$url[]    = $in .'_ltmt=' . $ibforums->input[ $in .'_ltmt' ];
			}
			else if ( $in == 'photoonly' )
			{
				if ( $ibforums->input['photoonly'] == 1 )
				{
					$query[] = $tbl. "<> ''";
				}
			}
			else if ( $inbit != "" )
			{
				$query[] = $tbl. " LIKE '%{$inbit}%'";
			}
    	}

    	//-----------------------------------------
    	// Custom fields?
    	//-----------------------------------------

    	if ( count( $this->topic->custom_fields->out_fields ) )
    	{
    		foreach( $this->topic->custom_fields->out_fields as $id => $data )
    		{
    			if ( $ibforums->input['field_'.$id] )
    			{
    				$query[] = "p.field_{$id} LIKE '{$ibforums->input['field_'.$id]}%'";
    				$url[]   = "field_{$id}=".$ibforums->input['field_'.$id];
    			}
    		}
    	}

    	//-----------------------------------------
    	// Finish query
    	//-----------------------------------------

    	if ( count($query) )
    	{
    		$query_string = "AND ".implode( " AND ", $query );
    	}

    	//-----------------------------------------
    	// Count...
    	//-----------------------------------------

    	$DB->cache_add_query( 'mlist_count', array( 'query' => $query_string ) );
    	$DB->cache_exec_query();

    	$max = $DB->fetch_row();

		$links = $std->build_pagelinks(  array( 'TOTAL_POSS'  => $max['total_members'],
												'PER_PAGE'    => $this->max_results,
												'CUR_ST_VAL'  => $this->first,
												'L_SINGLE'     => "",
												'L_MULTI'      => $ibforums->lang['pages'],
												'BASE_URL'     => $ibforums->base_url."&amp;name_box={$ibforums->input['name_box']}&amp;sort_key={$this->sort_key}&amp;sort_order={$this->sort_order}&amp;filter={$this->filter}&amp;act=members&amp;max_results={$this->max_results}&amp;".implode( '&amp;', $url )
											  )
									   );

		$this->output = $this->html->mlist_start();

		$this->output .= $this->html->mlist_page_header( $pages, $quick_jump );

		//-----------------------------------------
    	// Get custom profile information
    	//-----------------------------------------

    	$custom_fields = "";

    	if ( count( $this->topic->custom_fields->out_fields ) )
    	{
			foreach( $this->topic->custom_fields->out_fields as $id => $data )
			{
				if ( $this->topic->custom_fields->cache_data[ $id ]['pf_type'] == 'drop' )
				{
					$tmp = $this->html->mlist_custom_field_dropdown( 'field_'.$id, $data );
				}
				else
				{
					$tmp = $this->html->mlist_custom_field_textinput( 'field_'.$id );
 				}

 				$custom_fields .= $this->html->mlist_custom_field_entry( $this->topic->custom_fields->field_names[ $id ], $tmp );
			}
		}

		//-----------------------------------------
		// START THE LISTING
		//-----------------------------------------

		if ( $max['total_members'] > 0 )
		{
			$DB->cache_add_query( 'mlist_get_members', array( 'query'   => $query_string,
															  'sort'    => $this->sort_key,
															  'order'   => $this->sort_order,
															  'limit_a' => $this->first,
															  'limit_b' => $this->max_results ) );
			$DB->cache_exec_query();

			while ($member = $DB->fetch_row() )
			{
				$member = $this->topic->parse_member( $member );

				$member['joined'] = $std->get_date( $member['joined'], 'JOINED' );

				$member['group']  = $this->mem_groups[ $member['mgroup'] ]['TITLE'];

				if ($member['photo_type'] and $member['photo_location'])
				{
					$member['camera'] = "<a href=\"javascript:PopUp('{$ibforums->base_url}act=Profile&amp;CODE=showphoto&amp;MID={$member['id']}','Photo','200','250','0','1','1','1')\"><{CAMERA}></a>";
				}

				$member['posts'] = $std->do_number_format($member['posts']);

				//-----------------------------------------
				// Bug fix... name-- breaks formatting
				// xhmlt invalid..
				//-----------------------------------------

				$member['name'] = str_replace( '--', '&#45;&#45;', $member['name'] );

				$this->output .= $this->html->mlist_show_row($member);

			}
		}
		else
		{
			$this->output .= $this->html->mlist_no_results();
		}

		$checked = $ibforums->input['photoonly'] == 1 ? 'checked="checked"' : "";

		//-----------------------------------------
		// Print bottom...
		//-----------------------------------------

		$this->output .= $this->html->mlist_page_end( $checked );

		$this->output .= $this->html->mlist_end( array( 'SHOW_PAGES' => $links) );

		if ( $custom_fields )
		{
			$this->output = str_replace( '<!--CUSTOM_FIELDS-->', $this->html->mlist_custom_field_wrap($custom_fields), $this->output );
		}

    	//-----------------------------------------
    	// Push to print handler
    	//-----------------------------------------

    	$print->add_output( $this->output );
        $print->do_output( array( 'TITLE' => $ibforums->lang['page_title'], 'JS' => 0, 'NAV' => array( $ibforums->lang['page_title'] ) ) );
 	}

}

?>