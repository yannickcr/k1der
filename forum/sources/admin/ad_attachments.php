<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   ========================================
|   by Matthew Mecham
|   (c) 2004 Invision Power Services
|   http://www.ibforums.com
|   ========================================
|   Web: http://www.ibforums.com
|   Email: phpboards@ibforums.com
|   Licence Info: phpib-licence@ibforums.com
+---------------------------------------------------------------------------
|
|   > Admin: Attachment Functions
|   > Module written by Matt Mecham
|   > Date started: Saturday 13th March
|   > (Ooh, 13th! Note: First ever IPB module to be started
|   >  on a Saturday!)
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class ad_attachments {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std, $ibforums;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		$ibforums->admin->nav[] = array( 'act=attach', 'Attachments Manager' );

		$ibforums->admin->page_detail = "This section will allow you to manage your member's attachments and attachment permissions";
		$ibforums->admin->page_title  = "Attachments Manager";

		//-----------------------------------------
		// StRT!
		//-----------------------------------------

		switch( $ibforums->input['code'] )
		{
			case 'types':
				$this->attach_type_start();
				break;
			case 'attach_add':
				$this->attach_type_form('add');
				break;
			case 'attach_doadd':
				$this->attach_type_save('add');
				break;
			case 'attach_edit':
				$this->attach_type_form('edit');
				break;
			case 'attach_delete':
				$this->attach_type_delete();
				break;
			case 'attach_doedit':
				$this->attach_type_save('edit');
				break;
			case 'attach_export':
				$this->attach_type_export();
				break;
			case 'attach_import':
				$this->attach_type_import();
				break;
			//-----------------------------------------
			// Stats
			//-----------------------------------------
			case 'stats':
				$this->attach_stats_start();
				break;
			//-----------------------------------------
			// Bulk Remove
			//-----------------------------------------
			case 'attach_bulk_remove':
				$this->attach_bulk_remove();
				break;
			//-----------------------------------------
			// Search
			//-----------------------------------------
			case 'search':
				$this->attach_search_start();
				break;
			case 'attach_search_complete':
				$this->attach_search_complete();
				break;
			//-----------------------------------------
			// Default:
			//-----------------------------------------
			default:
				$this->attach_type_start();
				break;
		}
	}

	//-----------------------------------------
	//
	// SEARCH: Complete
	//
	//-----------------------------------------

	function attach_search_complete()
	{
		global $ibforums, $DB, $std, $ibforums;

		$show = intval($ibforums->input['show']);

		$show = $show > 100 ? 100 : $show;

		//-----------------------------------------
		// Get attachment details
		//-----------------------------------------

		$ibforums->cache['attachtypes'] = array();

		$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}

		//-----------------------------------------
		// Build URL
		//-----------------------------------------

		$url = "";
		$url_components = array( 'extension', 'filesize', 'filesize_gt', 'days', 'days_gt', 'hits', 'hits_gt', 'filename', 'authorname', 'onlyimage' );

		foreach( $url_components as $u )
		{
			$url .= $u.'='.$ibforums->input[ $u ].'&';
		}

		$url .= 'orderby='.$ibforums->input['orderby'].'&sort='.$ibforums->input['sort'].'&show='.$ibforums->input['show'];

		//-----------------------------------------
		// Build Query
		//-----------------------------------------

		$queryfinal = "";
		$query      = array();

		if ( $ibforums->input['extension'] )
		{
			$query[] = 'a.attach_ext="'.strtolower( str_replace( ".", "", $ibforums->input['extension'] ) ).'"';
		}

		if ( $ibforums->input['filesize'] )
		{
			$gt = $ibforums->input['filesize_gt'] == 'gt' ? '>=' : '<';

			$query[] = "a.attach_filesize $gt ".intval($ibforums->input['filesize']*1024);
		}

		if ( $ibforums->input['days'] )
		{
			$day_break = time() - intval( $ibforums->input['days'] * 86400 );

			$gt = $ibforums->input['days_gt'] == 'lt' ? '>=' : '<';

			$query[] = "a.attach_date $gt ".$day_break;
		}

		if ( $ibforums->input['hits'] )
		{
			$gt = $ibforums->input['hits_gt'] == 'gt' ? '>=' : '<';

			$query[] = "a.attach_hits $gt ".$ibforums->input['hits'];
		}

		if ( $ibforums->input['filename'] )
		{
			$query[] = 'LOWER(a.attach_file) LIKE "%'.strtolower( $ibforums->input['filename'] ).'%"';
		}

		if ( $ibforums->input['authorname'] )
		{
			$query[] = 'LOWER(p.author_name) LIKE "%'.strtolower( $ibforums->input['authorname'] ).'%"';
		}

		if ( $ibforums->input['onlyimage'] )
		{
			$query[] = 'a.attach_is_image=1';
		}

		if ( count($query) )
		{
			$queryfinal = 'AND '. implode( " AND ", $query );
		}

		$DB->cache_add_query( 'attachments_search_complete', array( 'queryfinal' => $queryfinal,
																	'orderby'    => $ibforums->input['orderby'],
																	'sort'       => $ibforums->input['sort'],
																	'limit_b'    => $show ) );
		$DB->cache_exec_query();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'attach_bulk_remove'   ),
												                 2 => array( 'act'   , 'attach'  ),
												                 3 => array( 'return', 'search'   ),
												                 4 => array( 'url'   , $url      ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );
		$ibforums->adskin->td_header[] = array( "Attachment", "20%" );
		$ibforums->adskin->td_header[] = array( "Size"      , "10%" );
		$ibforums->adskin->td_header[] = array( "Author"    , "15%" );
		$ibforums->adskin->td_header[] = array( "Topic"     , "25%" );
		$ibforums->adskin->td_header[] = array( "Posted    ", "25%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Attachments: Search Results" );

		while ( $r = $DB->fetch_row() )
		{
			$r['stitle'] = $std->txt_truncate($r['title'], 30);

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<img src='style_images/{$ibforums->skin['_imagedir']}/{$ibforums->cache['attachtypes'][ $r['attach_ext'] ]['atype_img']}' border='0' />" ,
																	 "<a href='index.php?act=attach&type=post&id={$r['attach_id']}' target='_blank'>{$r['attach_file']}</a>",
																     $std->size_format($r['attach_filesize']),
																     $r['author_name'],
																     "<a href='index.php?showtopic={$r['tid']}&view=findpost&p={$r['attach_pid']}' target='_blank' title='{$r['title']}'>{$r['stitle']}</a>",
																     $std->get_date( $r['post_date'], 'SHORT', 1 ),
																     "<div align='center'><input type='checkbox' name='attach_{$r['attach_id']}' value='1' /></div>",
													        )      );
		}

		$removebutton = "<input type='submit' value='Delete Checked Attachments' class='realdarkbutton'></form>";

		$ibforums->html .= $ibforums->adskin->add_td_basic( $removebutton, "right", "pformstrip");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// PRINT
		//-----------------------------------------

		$ibforums->admin->output();

	}

	//-----------------------------------------
	//
	// SEARCH: Start
	//
	//-----------------------------------------

	function attach_search_start()
	{
		global $ibforums, $DB, $std, $ibforums;

		//-----------------------------------------
		// HEADER
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search Attachments" );

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'attach_search_complete' ),
												                 2 => array( 'act'   , 'attach'  ),
									                    )      );

		$gt_array = array( 0 => array( 'gt', 'More Than' ), 1 => array( 'lt', 'Less Than' ) );

		//-----------------------------------------
		// FORM
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Match File Extension</b><div style='color:gray'>Leave blank to omit</div>",
												 				 $ibforums->adskin->form_simple_input( 'extension', $_POST['extension'], 10 ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Match File Size (in kb)</b><div style='color:gray'>Leave blank to omit</div>",
																 $ibforums->adskin->form_dropdown( 'filesize_gt', $gt_array, $_POST['filesize_gt'] ).' '.
												 				 $ibforums->adskin->form_simple_input( 'filesize', $_POST['filesize'], 10 ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Match Posted <em>n</em> Days</b><div style='color:gray'>Leave blank to omit</div>",
																 $ibforums->adskin->form_dropdown( 'days_gt', $gt_array, $_POST['days_gt'] ).' '.
												 				 $ibforums->adskin->form_simple_input( 'days', $_POST['days'], 10 ).' ago',
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Match Viewed <em>n</em> Times</b><div style='color:gray'>Leave blank to omit</div>",
																 $ibforums->adskin->form_dropdown( 'hits_gt', $gt_array, $_POST['hits_gt'] ).' '.
												 				 $ibforums->adskin->form_simple_input( 'hits', $_POST['hits'], 10 ).' times',
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Match File Name</b><div style='color:gray'>Leave blank to omit</div>",
												 				 $ibforums->adskin->form_simple_input( 'filename', $_POST['filename'], 30 ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Match Post Author Name</b><div style='color:gray'>Leave blank to omit</div>",
												 				 $ibforums->adskin->form_simple_input( 'authorname', $_POST['authorname'], 30 ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Match Only Images?</b><div style='color:gray'>If 'yes', this search will only return image attachments.</div>",
												 				 $ibforums->adskin->form_yes_no( 'onlyimage', $_POST['onlyimage'], 30 ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Order Results By</b>",
																 $ibforums->adskin->form_dropdown( 'orderby', array( 0 => array( 'date'    , 'Attach Date'      ),
																 										             1 => array( 'hits'    , 'Attach Views'     ),
																 										             2 => array( 'filesize', 'Attach File Size' ),
																 										             3 => array( 'file'    , 'Attach File Name' ),
																 										           ), $_POST['orderby'] ).' '.
																 $ibforums->adskin->form_dropdown( 'sort'   , array( 0 => array( 'desc'   , 'Descending [9-0]'  ),
																 													 1 => array( 'asc'    , 'Ascending [0-9]'   ),
																 										           ), $_POST['sort'] )


														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Show <em>n</em> Results</b><div style='color:gray'>Maximum is 100 regardless of your entry</div>",
												 				 $ibforums->adskin->form_simple_input( 'show', $_POST['show'] ? $_POST['show'] : 25, 10 ),
														)      );



		$ibforums->html .= $ibforums->adskin->end_form("Search");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();
	}

	//-----------------------------------------
	//
	// BULK REMOVE
	//
	//-----------------------------------------

	function attach_bulk_remove()
	{
		global $ibforums, $DB, $std, $ibforums;

		foreach ($ibforums->input as $key => $value)
		{
			if ( preg_match( "/^attach_(\d+)$/", $key, $match ) )
			{
				if ( $ibforums->input[$match[0]] )
				{
					$ids[] = $match[1];
				}
			}
		}

		$attach_tid = array();

		if ( count( $ids ) )
		{
			//-----------------------------------------
			// Get attach details?
			//-----------------------------------------

			$DB->cache_add_query( 'attachments_bulk_remove', array( 'ids' => $ids ) );
			$DB->cache_exec_query();

			$attach_ids = array();

			while ( $killmeh = $DB->fetch_row() )
			{
				if ( $killmeh['attach_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_location'] );
				}
				if ( $killmeh['attach_thumb_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_thumb_location'] );
				}

				$attach_tid[ $killmeh['topic_id'] ] = $killmeh['topic_id'];
			}

			$DB->simple_exec_query( array( 'delete' => 'attachments', 'where' => "attach_id IN(".implode(",",$ids).")" ) );

			//-----------------------------------------
			// Recount topic upload marker
			//-----------------------------------------

			require_once( ROOT_PATH.'sources/post.php' );

			$postlib = new post();

			foreach( $attach_tid as $apid => $tid )
			{
				$postlib->pf_recount_topic_attachments($tid);
			}
		}

		$ibforums->main_msg = "Attachments Removed";

		if ( $ibforums->input['return'] == 'stats' )
		{
			$this->attach_stats_start();
		}
		else
		{
			if ( $_POST['url'] )
			{
				foreach( explode( '&', $_POST['url'] ) as $u )
				{
					list ( $k, $v ) = explode( '=', $u );

					$ibforums->input[ $k ] = $v;
				}
			}

			$this->attach_search_complete();
		}
	}

	//-----------------------------------------
	//
	// STATS: Start
	//
	//-----------------------------------------

	function attach_stats_start()
	{
		global $ibforums, $DB, $std, $ibforums;

		//-----------------------------------------
		// Get attachment details
		//-----------------------------------------

		$ibforums->cache['attachtypes'] = array();

		$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}

		$ibforums->adskin->td_header[] = array( "{none}", "30%" );
		$ibforums->adskin->td_header[] = array( "{none}", "20%" );
		$ibforums->adskin->td_header[] = array( "{none}", "30%" );
		$ibforums->adskin->td_header[] = array( "{none}", "20%" );

		//-----------------------------------------
		// Get quick stats
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Attachments: Overview" );

		$stats = $DB->simple_exec_query( array( 'select' => 'count(*) as count, sum(attach_filesize) as sum',
 												'from'   => 'attachments' ) );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Number of Attachments</b>" , $std->do_number_format($stats['count']),
																 "<b>Attachments Disk Usage</b>", $std->size_format($stats['sum']),
													   )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'attach_bulk_remove'   ),
												                 2 => array( 'act'   , 'attach'  ),
												                 3 => array( 'return', 'stats'   ),
									                    )      );

		//-----------------------------------------
		// Recent 5 Attachments
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );
		$ibforums->adskin->td_header[] = array( "Attachment", "20%" );
		$ibforums->adskin->td_header[] = array( "Size"      , "10%" );
		$ibforums->adskin->td_header[] = array( "Author"    , "15%" );
		$ibforums->adskin->td_header[] = array( "Topic"     , "25%" );
		$ibforums->adskin->td_header[] = array( "Posted    ", "25%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Attachments: Last 5 Attached" );

		$DB->cache_add_query( 'attachments_last_x', array( 'order' => 'attach_date' ) );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$r['stitle'] = $std->txt_truncate($r['title'], 30);

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<img src='style_images/{$ibforums->skin['_imagedir']}/{$ibforums->cache['attachtypes'][ $r['attach_ext'] ]['atype_img']}' border='0' />" ,
																	 "<a href='index.php?act=attach&type=post&id={$r['attach_id']}' target='_blank'>{$r['attach_file']}</a>",
																     $std->size_format($r['attach_filesize']),
																     $r['author_name'],
																     "<a href='index.php?showtopic={$r['tid']}&view=findpost&p={$r['attach_pid']}' target='_blank' title='{$r['title']}'>{$r['stitle']}</a>",
																     $std->get_date( $r['post_date'], 'SHORT', 1 ),
																     "<div align='center'><input type='checkbox' name='attach_{$r['attach_id']}' value='1' /></div>",
													        )      );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Largest 5 Attachments
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );
		$ibforums->adskin->td_header[] = array( "Attachment", "20%" );
		$ibforums->adskin->td_header[] = array( "Size"      , "10%" );
		$ibforums->adskin->td_header[] = array( "Author"    , "15%" );
		$ibforums->adskin->td_header[] = array( "Topic"     , "25%" );
		$ibforums->adskin->td_header[] = array( "Posted    ", "25%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Attachments: Largest 5 Topic Attachments" );

		$DB->cache_add_query( 'attachments_last_x', array( 'order' => 'attach_filesize' ) );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$r['stitle'] = $std->txt_truncate($r['title'], 30);

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<img src='style_images/{$ibforums->skin['_imagedir']}/{$ibforums->cache['attachtypes'][ $r['attach_ext'] ]['atype_img']}' border='0' />" ,
																	 "<a href='index.php?act=attach&type=post&id={$r['attach_id']}' target='_blank'>{$r['attach_file']}</a>",
																     $std->size_format($r['attach_filesize']),
																     $r['author_name'],
																     "<a href='index.php?showtopic={$r['tid']}&view=findpost&p={$r['attach_pid']}' target='_blank' title='{$r['title']}'>{$r['stitle']}</a>",
																     $std->get_date( $r['post_date'], 'SHORT', 1 ),
																     "<div align='center'><input type='checkbox' name='attach_{$r['attach_id']}' value='1' /></div>",
													        )      );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Most popular
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );
		$ibforums->adskin->td_header[] = array( "Attachment", "20%" );
		$ibforums->adskin->td_header[] = array( "Viewed"    , "10%" );
		$ibforums->adskin->td_header[] = array( "Author"    , "15%" );
		$ibforums->adskin->td_header[] = array( "Topic"     , "25%" );
		$ibforums->adskin->td_header[] = array( "Posted    ", "25%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "1%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Attachments: Top 5 Most Viewed" );

		$DB->cache_add_query( 'attachments_last_x', array( 'order' => 'attach_hits' ) );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$r['stitle'] = $std->txt_truncate($r['title'], 30);

			$size = $std->size_format($r['attach_filesize']);

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<img src='style_images/{$ibforums->skin['_imagedir']}/{$ibforums->cache['attachtypes'][ $r['attach_ext'] ]['atype_img']}' border='0' />" ,
																	 "<a title='{$size}' href='index.php?act=attach&type=post&id={$r['attach_id']}' target='_blank'>{$r['attach_file']}</a>",
																     $r['attach_hits'],
																     $r['author_name'],
																     "<a href='index.php?showtopic={$r['tid']}&view=findpost&p={$r['attach_pid']}' target='_blank' title='{$r['title']}'>{$r['stitle']}</a>",
																     $std->get_date( $r['post_date'], 'SHORT', 1 ),
																     "<div align='center'><input type='checkbox' name='attach_{$r['attach_id']}' value='1' /></div>",
													        )      );
		}

		$removebutton = "<input type='submit' value='Delete Checked Attachments' class='realdarkbutton'></form>";

		$ibforums->html .= $ibforums->adskin->add_td_basic( $removebutton, "right", "pformstrip");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// PRINT
		//-----------------------------------------

		$ibforums->admin->output();
	}



	//-----------------------------------------
	// TYPE: Import
	//-----------------------------------------

	function attach_type_import()
	{
		global $ibforums, $DB,  $std;

		$content = $ibforums->admin->import_xml( 'ipb_attachtypes.xml' );

		//-----------------------------------------
		// Got anything?
		//-----------------------------------------

		if ( ! $content )
		{
			$ibforums->main_msg = "Upload failed, ipb_attachtypes.xml was either missing or empty";
			$this->attach_type_start();
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->xml_parse_document( $content );

		//-----------------------------------------
		// Get current badwords
		//-----------------------------------------

		$types = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'attachments_type', 'order' => "atype_extension" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$types[ $r['atype_extension'] ] = 1;
		}

		//-----------------------------------------
		// pArse
		//-----------------------------------------

		foreach( $xml->xml_array['attachtypesexport']['attachtypesgroup']['attachtype'] as $idx => $entry )
		{
			$insert_array = array( 'atype_extension' => $entry['atype_extension']['VALUE'],
								   'atype_mimetype'  => $entry['atype_mimetype']['VALUE'],
								   'atype_post'      => $entry['atype_post']['VALUE'],
								   'atype_photo'     => $entry['atype_photo']['VALUE'],
								   'atype_img'       => $entry['atype_img']['VALUE']
								 );

			if ( $types[ $entry['atype_extension']['VALUE'] ] )
			{
				continue;
			}

			if ( $entry['atype_extension']['VALUE'] and $entry['atype_mimetype']['VALUE'] )
			{
				$DB->do_insert( 'attachments_type', $insert_array );
			}
		}

		$this->attach_type_rebuildcache();

		$ibforums->main_msg = "Attachment Types XML file import completed";

		$this->attach_type_start();

	}

	//-----------------------------------------
	//
	// TYPES: Export
	//
	//-----------------------------------------

	function attach_type_export()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Start...
		//-----------------------------------------

		$xml->xml_set_root( 'attachtypesexport', array( 'exported' => time() ) );

		//-----------------------------------------
		// Get emo group
		//-----------------------------------------

		$xml->xml_add_group( 'attachtypesgroup' );

		$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img',
									  'from'   => 'attachments_type',
									  'order'  => "atype_extension" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$content = array();

			foreach ( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}

			$entry[] = $xml->xml_build_entry( 'attachtype', $content );
		}

		$xml->xml_add_entry_to_group( 'attachtypesgroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$ibforums->admin->show_download( $xml->xml_document, 'ipb_attachtypes.xml' );
	}

	//-----------------------------------------
	//
	// TYPES: DELETE
	//
	//-----------------------------------------

	function attach_type_delete()
	{
		global $ibforums, $DB, $std, $ibforums;

		$ibforums->input['id'] = intval($ibforums->input['id']);

		$DB->simple_exec_query( array( 'delete' => 'attachments_type', 'where' => 'atype_id='.$ibforums->input['id'] ) );

		$this->attach_type_rebuildcache();

		$ibforums->main_msg = "Attachment type deleted";

		$this->attach_type_start();
	}

	//-----------------------------------------
	//
	// TYPES: SAVE (edit / add)
	//
	//-----------------------------------------

	function attach_type_save( $type='add' )
	{
		global $ibforums, $DB, $std, $ibforums;

		$ibforums->input['id'] = intval($ibforums->input['id']);

		//-----------------------------------------
		// check basics
		//-----------------------------------------

		if ( ! $ibforums->input['atype_extension'] or ! $ibforums->input['atype_mimetype'] )
		{
			$ibforums->main_msg = "You must enter at least an extension and mime-type before continuing.";
			$this->attach_type_form( $type );
		}

		$save_array = array( 'atype_extension' => str_replace( ".", "", $ibforums->input['atype_extension'] ),
							 'atype_mimetype'  => $ibforums->input['atype_mimetype'],
							 'atype_post'      => $ibforums->input['atype_post'],
							 'atype_photo'     => $ibforums->input['atype_photo'],
							 'atype_img'       => $ibforums->input['atype_img']
						   );

		if ( $type == 'add' )
		{
			//-----------------------------------------
			// Check for existing..
			//-----------------------------------------

			$attach = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'attachments_type', 'where' => "atype_extension='".$save_array['atype_extension']."'" ) );

			if ( $attach['atype_id'] )
			{
				$ibforums->main_msg = "The extension '{$save_array['atype_extension']}' already exists, please choose another extension.";
				$this->attach_type_form($type);
			}

			$DB->do_insert( 'attachments_type', $save_array );

			$ibforums->main_msg = "Attachment type added";

		}
		else
		{
			$DB->do_update( 'attachments_type', $save_array, 'atype_id='.$ibforums->input['id'] );

			$ibforums->main_msg = "Attachment type edited";
		}

		$this->attach_type_rebuildcache();

		$this->attach_type_start();

	}

	//-----------------------------------------
	//
	// TYPES: FORM (edit / add)
	//
	//-----------------------------------------

	function attach_type_form( $type='add' )
	{
		global $ibforums, $DB, $std, $ibforums;

		$ibforums->input['id']     = intval($ibforums->input['id']);
		$ibforums->input['baseon'] = intval($ibforums->input['baseon']);

		if ( $type == 'add' )
		{
			$code   = 'attach_doadd';
			$button = 'Add New Attachment Type';

			if ( $ibforums->input['baseon'] )
			{
				$attach = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'attachments_type', 'where' => 'atype_id='.$ibforums->input['baseon'] ) );
			}
			else
			{
				$attach = array();
			}

			//-----------------------------------------
			// Generate 'base on'
			//-----------------------------------------

			$dd = "";

			$DB->simple_construct( array( 'select' => '*', 'from' => 'attachments_type', 'order' => 'atype_extension' ) );
			$DB->simple_exec();

			while( $r = $DB->fetch_row() )
			{
				$dd .= "<option value='{$r['atype_id']}'>Base on: {$r['atype_extension']}</option>\n";
			}

			$title = "
					  <div style='float:right;width:auto;padding-right:3px;'>
					  <form method='post' action='{$ibforums->base_url}&act=attach&code=attach_add'>
					  <select name='baseon' class='realbutton'>{$dd}</select> &nbsp;<input type='submit' value='Go' class='realdarkbutton' />
					  </form>
					  </div><div style='padding-bottom:5px'>{$button}</div>";

		}
		else
		{
			$code   = 'attach_doedit';
			$button = 'Edit Attachment Type';
			$title  = $button;
			$attach = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'attachments_type', 'where' => 'atype_id='.$ibforums->input['id'] ) );

			if ( ! $attach['atype_id'] )
			{
				$ibforums->main_msg = "No ID was passed, please try again.";
				$this->attach_type_start();
			}
		}

		//-----------------------------------------
		// HEADER
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( $title );

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $code     ),
												                 2 => array( 'act'   , 'attach'  ),
												                 3 => array( 'id'    , $ibforums->input['id'] )
									                    )      );

		//-----------------------------------------
		// FORM
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Attachment File Extension</b><div style='color:gray'>This is the (usually) three character filename suffix.<br />You don't need to add the '.' before the extension</div>",
												 				 $ibforums->adskin->form_simple_input( 'atype_extension', $_POST['atype_extension'] ? $_POST['atype_extension'] : $attach['atype_extension'], 10 ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Attachment Mime-Type</b><div style='color:gray'>Unsure what the correct mime-type is?. <a href='http://www.utoronto.ca/webdocs/HTMLdocs/Book/Book-3ed/appb/mimetype.html' target='_blank'>Try looking here</a></div>",
												 				 $ibforums->adskin->form_simple_input( 'atype_mimetype', $_POST['atype_mimetype'] ? $_POST['atype_mimetype'] : $attach['atype_mimetype'], 40 ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow this attachment in posts?</b>",
												 				 $ibforums->adskin->form_yes_no( 'atype_post', $_POST['atype_post'] ? $_POST['atype_post'] : $attach['atype_post'] ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow this attachment in avatars / personal photos?</b>",
												 				 $ibforums->adskin->form_yes_no( 'atype_photo', $_POST['atype_photo'] ? $_POST['atype_photo'] : $attach['atype_photo'] ),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Attachment Mini-Image</b><div style='color:gray'>This is the little icon that represents the attachment type in a post.</div>",
												 				 $ibforums->adskin->form_simple_input( 'atype_img', $_POST['atype_img'] ? $_POST['atype_img'] : $attach['atype_img'], 40 ),
														)      );

		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();
	}


	//-----------------------------------------
	//
	// TYPES: Start
	//
	//-----------------------------------------

	function attach_type_start()
	{
		global $ibforums, $DB, $std, $ibforums;


		$ibforums->adskin->td_header[] = array( "&nbsp;"        , "1%" );
		$ibforums->adskin->td_header[] = array( "Extension"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Mime-Type"     , "40%" );
		$ibforums->adskin->td_header[] = array( "+Post"         , "10%" );
		$ibforums->adskin->td_header[] = array( "+Avatar"       , "10%" );
		$ibforums->adskin->td_header[] = array( "Options"       , "20%" );

		$export_button = $ibforums->adskin->js_make_button("Export Attachment Types", $ibforums->base_url."&act=attach&code=attach_export");

		$table = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
				  <td align='left' width='100%' style='font-weight:bold;font-size:11px;color:#FFF'>Attachment Types</td>
				  <td align='right' nowrap='nowrap' style='padding-right:2px'>{$export_button}</td>
				  </tr>
				  </table>";

		$ibforums->html .= $ibforums->adskin->start_table( $table );

		//-----------------------------------------
		// Get 'em
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'attachments_type', 'order' => 'atype_extension' ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$checked_img    = "<img src='{$ibforums->adskin->img_url}/acp_check.gif' border='0' alt='X' />";
			$apost_checked  = $r['atype_post']  ? $checked_img : '&nbsp;';
			$aphoto_checked = $r['atype_photo'] ? $checked_img : '&nbsp;';

			$edit   = $ibforums->adskin->js_make_button("Edit", "{$ibforums->base_url}&act=attach&code=attach_edit&id={$r['atype_id']}" );
			$delete = $ibforums->adskin->js_make_button("Delete", "{$ibforums->base_url}&act=attach&code=attach_delete&id={$r['atype_id']}", 'realdarkbutton' );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<img src='style_images/{$ibforums->skin['_imagedir']}/{$r['atype_img']}' border='0' />",
												 					 ".<strong>{$r['atype_extension']}</strong>",
												 					 $r['atype_mimetype'],
												 					 "<div align='center'>{$apost_checked}</div>",
												 					 "<div align='center'>{$aphoto_checked}</div>",
												 					 "<div align='center'>{$edit} &nbsp; &nbsp; {$delete}</div>",
														   )      );
		}

		$add_new = $ibforums->adskin->js_make_button("Add New Attachment Type", "{$ibforums->base_url}&act=attach&code=attach_add" );

		$ibforums->html .= $ibforums->adskin->add_td_basic( $add_new, "center", "pformstrip");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// IMPORT: Start output
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'attach_import' ),
															     2 => array( 'act'   , 'attach'        ),
															     3 => array( 'MAX_FILE_SIZE', '10000000000' ),
													    ) , "uploadform", " enctype='multipart/form-data'"     );


		$ibforums->html .= $ibforums->adskin->start_table( "Import an Attachment Types List" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													 		    "<b>Upload XML Attachment Types List</b><div style='color:gray'>Browse your computer for 'ipb_attachtypes.xml' or 'ipb_attachtypes.xml.gz'. Duplicate entries will not be imported.</div>",
													  		    $ibforums->adskin->form_upload(  )
													    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Import");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->admin->output();
	}

	//-----------------------------------------
	//
	// TYPES: Rebuild Cache
	//
	//-----------------------------------------

	function attach_type_rebuildcache()
	{
		global $ibforums, $std, $DB;

		$ibforums->cache['attachtypes'] = array();

		$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}

		$std->update_cache( array( 'name' => 'attachtypes', 'array' => 1, 'deletefirst' => 1 ) );
	}







}

?>