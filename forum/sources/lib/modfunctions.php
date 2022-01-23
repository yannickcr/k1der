<?php
/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.ibforums.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Email: matt@ibforums.com
|   Licence Info: http://www.invisionpower.com
+---------------------------------------------------------------------------
|
|   > Moderator Core Functions
|   > Module written by Matt Mecham
|   > DBA Checked: Fri 21 May 2004
|
+--------------------------------------------------------------------------
| NOTE:
| This module does not do any access/permission checks, it merely
| does what is asked and returns - see function for more info
+--------------------------------------------------------------------------
*/

class modfunctions
{
	//-----------------------------------------
	// @modfunctions: constructor
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE)
	//-----------------------------------------

	var $topic = "";
	var $forum = "";
	var $error = "";

	var $auto_update = FALSE;

	var $stm   = "";
	var $upload_dir = "";

	var $moderator  = "";

	function modfunctions()
	{
		global $ibforums;

		$this->error = "";

		$this->upload_dir = $ibforums->vars['upload_dir'];

		return TRUE;
	}

	//-----------------------------------------
	// @init: initialize module (allows us to create new obj)
	// -----------
	// Accepts: References to @$forum [ @$topic , @$moderator ]
	// Returns: NOTHING (TRUE)
	//-----------------------------------------

	function init($forum="", $topic="", $moderator="")
	{
		$this->forum = $forum;

		if ( is_array($topic) )
		{
			$this->topic = $topic;
		}

		if ( is_array($moderator) )
		{
			$this->moderator = $moderator;
		}

		return TRUE;
	}


	//-----------------------------------------
	// @post_delete: delete post ID(s)
	// -----------
	// Accepts: $id (array | string)
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function post_delete($id)
	{
		global $std, $ibforums, $DB;

		$posts      = array();
		$attach_tid = array();
		$topics     = array();

		$this->error = "";

		if ( is_array( $id ) )
		{
			if ( count($id) > 0 )
			{
				$pid = " IN(".implode(",",$id).")";
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			if ( intval($id) )
			{
				$pid   = "=$id";
			}
			else
			{
				return FALSE;
			}
		}

		//-----------------------------------------
		// Get Stuff
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'pid, topic_id', 'from' => 'posts', 'where' => 'pid'.$pid ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$posts[ $r['pid'] ]       = $r['topic_id'];
			$topics[ $r['topic_id'] ] = 1;
		}

		//-----------------------------------------
		// Is there an attachment to this post?
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_pid".$pid ) );
		$DB->simple_exec();

		$attach_ids = array();

		while ( $killmeh = $DB->fetch_row( ) )
		{
			if ( $killmeh['attach_location'] )
			{
				@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_location'] );
			}
			if ( $killmeh['attach_thumb_location'] )
			{
				@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_thumb_location'] );
			}

			$attach_ids[] = $killmeh['attach_id'];
			$attach_tid[ $posts[ $killmeh['attach_pid'] ] ] = $posts[ $killmeh['attach_pid'] ];
		}

		if ( count($attach_ids) )
		{
			$DB->simple_exec_query( array( 'delete' => 'attachments', 'where' => "attach_id IN(".implode(",",$attach_ids).")" ) );

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

		//-----------------------------------------
		// delete the post
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'posts', 'where' => "pid".$pid ) );

		//-----------------------------------------
		// Update the stats
		//-----------------------------------------

		$ibforums->cache['stats']['total_replies'] -= count($posts);

		$std->update_cache( array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 0 ) );

		//-----------------------------------------
		// Update all relevant topics
		//-----------------------------------------

		foreach( array_keys($topics) as $tid )
		{
			$this->rebuild_topic($tid);
		}

		$this->add_moderate_log("", "", "", $pid, "Deleted posts ($pid)");
	}

	//-----------------------------------------
	// @topic_add_reply: Appends topic with reply
	// -----------
	// Accepts: $post, $tids = array( 'tid', 'forumid' );
	//
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function rebuild_topic($tid, $doforum=1)
	{
		global $std, $ibforums, $DB, $forums;

		$tid = intval($tid);

		//-----------------------------------------
		// Get the correct number of replies
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'COUNT(*) as posts', 'from' => 'posts', 'where' => "topic_id=$tid and queued != 1" ) );
		$DB->simple_exec();

		$posts = $DB->fetch_row();

		$pcount = intval( $posts['posts'] - 1 );

		//-----------------------------------------
		// Get the correct number of queued replies
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'COUNT(*) as posts', 'from' => 'posts', 'where' => "topic_id=$tid and queued=1" ) );
		$DB->simple_exec();

		$qposts = $DB->fetch_row();

		$qpcount = intval( $qposts['posts'] );

		//-----------------------------------------
		// Get last post info
		//-----------------------------------------

		$DB->cache_add_query( 'mod_func_get_last_post', array( 'tid' => $tid ) );
		$DB->cache_exec_query();

		$last_post = $DB->fetch_row();

		//-----------------------------------------
		// Get first post info
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'post_date, author_id, author_name, pid',
									  'from'   => 'posts',
									  'where'  => "topic_id=$tid",
									  'order'  => 'pid ASC',
									  'limit'  => array(0,1) ) );
		$DB->simple_exec();

		$first_post = $DB->fetch_row();

		//-----------------------------------------
		// Get number of attachments
		//-----------------------------------------

		$DB->cache_add_query( 'mod_func_get_attach_count', array( 'tid' => $tid ) );
		$DB->cache_exec_query();

		$attach = $DB->fetch_row();

		//-----------------------------------------
		// Update topic
		//-----------------------------------------

		$DB->do_update( 'topics', array( 'last_post'         => $last_post['post_date'],
										 'last_poster_id'    => $last_post['author_id'],
										 'last_poster_name'  => $last_post['author_name'],
										 'topic_queuedposts' => $qpcount,
										 'posts'             => $pcount,
										 'starter_id'        => $first_post['author_id'],
										 'starter_name'      => $first_post['author_name'],
										 'start_date'        => $first_post['post_date'],
										 'topic_firstpost'   => $first_post['pid'],
										 'topic_hasattach'   => intval($attach['count'])
									   ), 'tid='.$tid );

		//-----------------------------------------
		// Update first post
		//-----------------------------------------

		if ( $first_post['new_topic'] != 1 and $first_post['pid'] )
		{
			$DB->do_shutdown_update( 'posts', array( 'new_topic' => 0 ), 'topic_id='.$tid );
			$DB->do_shutdown_update( 'posts', array( 'new_topic' => 1 ), 'pid='.$first_post['pid'] );
		}

		//-----------------------------------------
		// If we deleted the last post in a topic that was
		// the last post in a forum, best update that :D
		//-----------------------------------------

		if ( ($forums->forums_by_id[ $last_post['forum_id'] ]['last_id'] == $tid) AND ($doforum == 1) )
		{
			$tt = $DB->simple_exec_query( array( 'select' => 'title, tid, last_post, last_poster_id, last_poster_name',
												 'from'   => 'topics',
												 'where'  => 'forum_id='.$last_post['forum_id'].' and approved=1',
												 'order'  => 'last_post desc',
												 'limit'  => array( 0,1 )
										)      );

			$dbs = array(
						 'last_title'       => $tt['title']            ? $tt['title']            : "",
						 'last_id'          => $tt['tid']              ? $tt['tid']              : "",
						 'last_post'        => $tt['last_post']        ? $tt['last_post']        : "",
						 'last_poster_name' => $tt['last_poster_name'] ? $tt['last_poster_name'] : "",
						 'last_poster_id'   => $tt['last_poster_id']   ? $tt['last_poster_id']   : "",
						);

			$DB->do_update( 'forums', $dbs, "id=".intval($this->forum['id']) );

			//-----------------------------------------
			// Update forum cache
			//-----------------------------------------

			foreach( $dbs as $k => $v )
			{
				$ibforums->cache['forum_cache'][ $this->forum['id'] ][ $k ] = $v;
			}

			$std->update_cache( array( 'name' => 'forum_cache', 'array' => 1, 'deletefirst' => 0 ) );
		}
	}

	//-----------------------------------------
	// @topic_add_reply: Appends topic with reply
	// -----------
	// Accepts: $post, $tids = array( 'tid', 'forumid' );
	//
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function topic_add_reply($post="", $tids=array(), $incpost=0)
	{
		global $std, $ibforums, $DB;

		if ( $post == "" )
		{
			return FALSE;
		}

		if ( count( $tids ) < 1 )
		{
			return FALSE;
		}

		$post = array(
					  'author_id'   => $ibforums->member['id'],
					  'use_sig'     => 1,
					  'use_emo'     => 1,
					  'ip_address'  => $ibforums->input['IP_ADDRESS'],
					  'post_date'   => time(),
					  'icon_id'     => 0,
					  'post'        => $post,
					  'author_name' => $ibforums->member['name'],
					  'topic_id'    => "",
					  'queued'      => 0,
					 );

		//-----------------------------------------
		// Add posts...
		//-----------------------------------------

		$seen_fids = array();
		$add_posts = 0;

		foreach( $tids as $row )
		{
			$tid = intval($row[0]);
			$fid = intval($row[1]);
			$pa  = array();
			$ta  = array();

			if ( ! in_array( $fid, $seen_fids ) )
			{
				$seen_fids[] = $fid;
			}

			if ( $tid and $fid )
			{
				$pa = $post;
				$pa['topic_id'] = $tid;

				$DB->do_insert( 'posts', $pa );

				$ta = array (
							  'last_poster_id'   => $ibforums->member['id'],
							  'last_poster_name' => $ibforums->member['name'],
							  'last_post'        => $pa['post_date'],
							);

				$db_string = $DB->compile_db_update_string( $ta );

				$DB->simple_exec_query( array( 'update' => 'topics', 'set' => $db_string.", posts=posts+1", 'where' => 'tid='.$tid ) );

				$add_posts++;
			}
		}

		if ( $this->auto_update != FALSE )
		{
			if ( count($seen_fids) > 0 )
			{
				foreach( $seen_fids as $id )
				{
					$this->forum_recount( $id );
				}
			}
		}

		if ( $add_posts > 0 )
		{
			$ibforums->cache['stats']['total_replies'] += $add_posts;

			$std->update_cache( array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 0 ) );

			//-----------------------------------------
			// Update current members stuff
			//-----------------------------------------

			$pcount = "";
			$mgroup = "";


			if ( ($this->forum['inc_postcount']) and ($incpost != 0) )
			{
				//-----------------------------------------
				// Increment the users post count
				//-----------------------------------------

				$pcount = "posts=posts+".$add_posts.", ";
			}

			//-----------------------------------------
			// Are we checking for auto promotion?
			//-----------------------------------------

			if ($ibforums->member['g_promotion'] != '-1&-1')
			{
				list($gid, $gposts) = explode( '&', $ibforums->member['g_promotion'] );

				if ( $gid > 0 and $gposts > 0 )
				{
					if ( $ibforums->member['posts'] + $add_posts >= $gposts )
					{
						$mgroup = "mgroup='$gid', ";
					}
				}
			}

			$DB->simple_exec_query( array( 'update' => 'members', 'set' => $pcount.$mgroup."last_post=".time(), 'where' => "id=".$ibforums->member['id'] ) );
		}

		return TRUE;
	}

	//-----------------------------------------
	// @topic_close: close topic ID's
	// -----------
	// Accepts: Array ID's | Single ID
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function topic_close($id)
	{
		global $ibforums, $DB;

		$this->stm_init();
		$this->stm_add_close();
		$this->stm_exec($id);
	}


	//-----------------------------------------
	// @topic_open: open topic ID's
	// -----------
	// Accepts: Array ID's | Single ID
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function topic_open($id)
	{
		global $ibforums, $DB;

		$this->stm_init();
		$this->stm_add_open();
		$this->stm_exec($id);
	}

	//-----------------------------------------
	// @topic_pin: pin topic ID's
	// -----------
	// Accepts: Array ID's | Single ID
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function topic_pin($id)
	{
		global $ibforums, $DB;

		$this->stm_init();
		$this->stm_add_pin();
		$this->stm_exec($id);
	}

	//-----------------------------------------
	// @topic_unpin: unpin topic ID's
	// -----------
	// Accepts: Array ID's | Single ID
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function topic_unpin($id)
	{
		global $ibforums, $DB;

		$this->stm_init();
		$this->stm_add_unpin();
		$this->stm_exec($id);
	}


	//-----------------------------------------
	// @topic_delete: deletetopic ID(s)
	// -----------
	// Accepts: $id (array | string)
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function topic_delete($id, $nostats=0)
	{
		global $std, $ibforums, $DB;

		$posts  = array();
		$attach = array();

		$this->error = "";

		if ( is_array( $id ) )
		{
			if ( count($id) > 0 )
			{
				$tid = " IN(".implode(",",$id).")";
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			if ( intval($id) )
			{
				$tid   = "=$id";
			}
			else
			{
				return FALSE;
			}
		}

		//-----------------------------------------
		// Remove polls assigned to this topic
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'polls', 'where' => "tid".$tid ) );

		//-----------------------------------------
		// Remove polls assigned to this topic
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'voters', 'where' => "tid".$tid ) );

		//-----------------------------------------
		// Remove polls assigned to this topic
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'topics', 'where' => "tid".$tid ) );

		//-----------------------------------------
		// Get PIDS for attachment deletion
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'pid', 'from' => 'posts', 'where' => "topic_id".$tid ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$posts[] = $r['pid'];
		}

		//-----------------------------------------
		// Remove the attachments
		//-----------------------------------------

		if ( count( $posts ) )
		{
			$DB->simple_construct( array( "select" => '*', 'from' => 'attachments', 'where' => "attach_pid IN (".implode(",",$posts).")" ) );
			$o = $DB->simple_exec();

			while ( $killmeh = $DB->fetch_row( $o ) )
			{
				if ( $killmeh['attach_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_location'] );
				}
				if ( $killmeh['attach_thumb_location'] )
				{
					@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_thumb_location'] );
				}

				$attach[] = $killmeh['attach_id'];
			}

			if ( count( $attach ) )
			{
				$DB->simple_construct( array( 'delete' => 'attachments', 'where' => "attach_id IN (".implode(",",$attach).")" ) );
				$DB->simple_exec();
			}
		}

		//-----------------------------------------
		// Remove the posts
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'posts', 'where' => "topic_id".$tid ) );

		//-----------------------------------------
		// Recount forum...
		//-----------------------------------------

		if ( $nostats == 0 )
		{
			if ( $this->forum['id'] )
			{
				$this->forum_recount( $this->forum['id'] );
			}

			$this->stats_recount();
		}
	}


	//-----------------------------------------
	// @topic_move: move topic ID(s)
	// -----------
	// Accepts: $topics (array | string) $source,
	//          $moveto
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function topic_move($topics, $source, $moveto, $leavelink=0)
	{
		global $std, $ibforums, $DB, $forums;

		$this->error = "";

		$source = intval($source);
		$moveto = intval($moveto);

		if ( is_array( $topics ) )
		{
			if ( count($topics) > 0 )
			{
				$tid = " IN(".implode(",",$topics).")";
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			if ( intval($topics) )
			{
				$tid   = "=$topics";
			}
			else
			{
				return FALSE;
			}
		}

		//-----------------------------------------
		// Update the topic
		//-----------------------------------------

		$DB->do_update( 'topics', array( 'forum_id' => $moveto ), "forum_id=$source AND tid".$tid );

		//-----------------------------------------
		// Update the polls
		//-----------------------------------------

		$DB->do_update( 'polls', array( 'forum_id' => $moveto ), "forum_id=$source AND tid".$tid );

		//-----------------------------------------
		// Are we leaving a stink er link?
		//-----------------------------------------

		if ( $leavelink != 0 )
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => "tid".$tid ) );
			$oq = $DB->simple_exec();

			while ( $row = $DB->fetch_row($oq) )
			{
				$DB->do_insert( 'topics', array (
												  'title'            => $row['title'],
												  'description'      => $row['description'],
												  'state'            => 'link',
												  'posts'            => 0,
												  'views'            => 0,
												  'starter_id'       => $row['starter_id'],
												  'start_date'       => $row['start_date'],
												  'starter_name'     => $row['starter_name'],
												  'last_post'        => $row['last_post'],
												  'forum_id'         => $source,
												  'approved'         => 1,
												  'pinned'           => 0,
												  'moved_to'         => $row['tid'].'&'.$moveto,
												  'last_poster_id'   => $row['last_poster_id'],
												  'last_poster_name' => $row['last_poster_name']
									  )        );
			}

		}

		//-----------------------------------------
		// Sort out subscriptions
		//-----------------------------------------

		$DB->cache_add_query( 'mod_func_get_topic_tracker', array( 'tid' => $tid ) );
		$DB->cache_exec_query();

		$trid_to_delete = array();

		while ( $r = $DB->fetch_row() )
		{
			//-----------------------------------------
			// Match the perm group against forum_mask
			//-----------------------------------------

			$perm_id = $r['g_perm_id'];

			if ( $r['org_perm_id'] )
			{
				$perm_id = $r['org_perm_id'];
			}

			$pass = 0;

			$forum_perm_array = explode( ",", $forums->forum_by_id[ $r['forum_id'] ]['read_perms'] );

			foreach( explode( ',', $perm_id ) as $u_id )
			{
				if ( in_array( $u_id, $forum_perm_array ) )
				{
					$pass = 1;
				}
			}

			if ( $pass != 1 )
			{
				$trid_to_delete[] = $r['trid'];
			}
		}

		if ( count($trid_to_delete) > 0 )
		{
			$DB->simple_exec_query( array( 'delete' => 'tracker', 'where' => "trid IN(".implode(',', $trid_to_delete ).")" ) );
		}

		return TRUE;
	}

	//-----------------------------------------
	// @stats_recount: Recount all topics & posts
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stats_recount()
	{
		global $ibforums, $DB, $std;

		if ( ! is_array($ibforums->cache['stats']) )
		{
			$stats = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='stats'" ) );

			$ibforums->cache['stats'] = unserialize(stripslashes($stats['cs_value']));
		}

		$topics = $DB->simple_exec_query( array( 'select' => 'COUNT(*) as tcount',
												 'from'   => 'topics',
												 'where'  => 'approved=1' ) );

		$posts  = $DB->simple_exec_query( array( 'select' => 'SUM(posts) as replies',
												 'from'   => 'topics',
												 'where'  => 'approved=1' ) );

		$ibforums->cache['stats']['total_topics']  = $topics['tcount'];
		$ibforums->cache['stats']['total_replies'] = $posts['replies'];

		$std->update_cache( array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 0 ) );
	}


	//-----------------------------------------
	// @forum_recount: Recount topic & posts in a forum
	// -----------
	// Accepts: forum_id
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function forum_recount($fid="")
	{
		global $ibforums, $DB, $std;

		$fid = intval($fid);

		if ( ! $fid )
		{
			if ( $this->forum['id'] )
			{
				$fid = $this->forum['id'];
			}
			else
			{
				return FALSE;
			}
		}

		//-----------------------------------------
		// Get the topics..
		//-----------------------------------------

		$topics = $DB->simple_exec_query( array( 'select' => 'COUNT(*) as count',
												 'from'   => 'topics',
												 'where'  => "approved=1 and forum_id=$fid" ) );

		//-----------------------------------------
		// Get the QUEUED topics..
		//-----------------------------------------

		$queued_topics = $DB->simple_exec_query( array( 'select' => 'COUNT(*) as count',
														'from'   => 'topics',
														'where'  => "approved=0 and forum_id=$fid" ) );

		//-----------------------------------------
		// Get the posts..
		//-----------------------------------------

		$posts = $DB->simple_exec_query( array( 'select' => 'SUM(posts) as replies',
												'from'   => 'topics',
												'where'  => "approved=1 and forum_id=$fid" ) );

		//-----------------------------------------
		// Get the QUEUED posts..
		//-----------------------------------------

		$queued_posts = $DB->simple_exec_query( array( 'select' => 'SUM(topic_queuedposts) as replies',
													   'from'   => 'topics',
													   'where'  => "forum_id=$fid" ) );

		//-----------------------------------------
		// Get the forum last poster..
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'tid, title, last_poster_id, last_poster_name, last_post',
									  'from'   => 'topics',
									  'where'  => "approved=1 and forum_id=$fid",
									  'order'  => 'last_post DESC',
									  'limit'  => array( 0,1 ) ) );

		$DB->simple_exec();

		$last_post = $DB->fetch_row();

		//-----------------------------------------
		// Reset this forums stats
		//-----------------------------------------

		$dbs = array(
					  'last_poster_id'   => $last_post['last_poster_id'],
					  'last_poster_name' => $last_post['last_poster_name'],
					  'last_post'        => $last_post['last_post'],
					  'last_title'       => $last_post['title'],
					  'last_id'          => $last_post['tid'],
					  'topics'           => intval($topics['count']),
					  'posts'            => intval($posts['replies']),
					  'queued_topics'    => intval($queued_topics['count']),
					  'queued_posts'     => intval($queued_posts['replies']),
					);

		$DB->do_update( 'forums', $dbs, "id=".$fid );

		//-----------------------------------------
		// Update forum cache
		//-----------------------------------------

		foreach( $dbs as $k => $v )
		{
			$ibforums->cache['forum_cache'][ $fid ][ $k ] = $v;
		}

		$std->update_cache( array( 'name' => 'forum_cache', 'array' => 1, 'deletefirst' => 0 ) );

		return TRUE;

	}


	//-----------------------------------------
	// @stm_init: Clear statement ready for multi-actions
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_init()
	{
		$this->stm = array();

		return TRUE;
	}

	//-----------------------------------------
	// @stm_exec: Executes stored statement
	// -----------
	// Accepts: Array ID's | Single ID
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_exec($id)
	{
		global $ibforums, $DB;

		if ( count($this->stm) < 1 )
		{
			return FALSE;
		}

		$final_array = array();

		foreach( $this->stm as $idx => $real_array )
		{
			foreach( $real_array as $k => $v )
			{
				$final_array[ $k ] = $v;
			}
		}

		$db_string = $DB->compile_db_update_string( $final_array );

		if ( is_array($id) )
		{
			if ( count($id) > 0 )
			{
				$DB->simple_exec_query( array( 'update' => 'topics', 'set' => $db_string, 'where' => "tid IN(".implode( ",", $id ).")" ) );
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else if ( intval($id) != "" )
		{
			$DB->simple_exec_query( array( 'update' => 'topics', 'set' => $db_string, 'where' => "tid=".intval($id) ) );
		}
		else
		{
			return FALSE;
		}
	}


	//-----------------------------------------
	// @stm_add_pin: add pin command to statement
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_pin()
	{
		$this->stm[] = array( 'pinned' => 1 );

		return TRUE;
	}

	//-----------------------------------------
	// @stm_add_unpin: add unpin command to statement
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_unpin()
	{
		$this->stm[] = array( 'pinned' => 0 );

		return TRUE;
	}

	//-----------------------------------------
	// @stm_add_close: add close command to statement
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_close()
	{
		$this->stm[] = array( 'state' => 'closed' );

		return TRUE;
	}

	//-----------------------------------------
	// @stm_add_open: add open command to statement
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_open()
	{
		$this->stm[] = array( 'state' => 'open' );

		return TRUE;
	}

	//-----------------------------------------
	// @stm_add_title: add edit title command to statement
	// -----------
	// Accepts: new_title
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_title($new_title='')
	{
		if ( $new_title == "" )
		{
			return FALSE;
		}

		$this->stm[] = array( 'title' => $new_title );

		return TRUE;
	}

	//-----------------------------------------
	// @stm_add_desc: add edit desc command to statement
	// -----------
	// Accepts: new_title
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_desc($new_desc='')
	{
		if ( $new_desc == "" )
		{
			return FALSE;
		}

		$this->stm[] = array( 'description' => $new_desc );

		return TRUE;
	}

	//-----------------------------------------
	// @stm_add_approve: Approve topic
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_approve()
	{
		$this->stm[] = array( 'approved' => 1 );

		return TRUE;
	}

	//-----------------------------------------
	// @stm_add_unapprove: Unapprove topic
	// -----------
	// Accepts: NOTHING
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function stm_add_unapprove()
	{
		$this->stm[] = array( 'approved' => 0 );

		return TRUE;
	}

	//-----------------------------------------
	// @sql_prune_create: returns formatted SQL statement
	// -----------
	// Accepts: forum_id, poss_starter_id, poss_topic_state, poss_post_min
	//			poss_date_expiration, poss_ignore_pin_state
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function sql_prune_create( $forum_id, $starter_id="", $topic_state="", $post_min="", $date_exp="", $ignore_pin="" )
	{
		$sql = "SELECT tid FROM ibf_topics WHERE approved=1 and forum_id=".intval($forum_id);

		if ( intval($date_exp) )
		{
			$sql .= " AND last_post < $date_exp";
		}

		if ( intval($starter_id) )
		{
			$sql .= " AND starter_id=$starter_id";

		}

		if ( intval($post_min) )
		{
			$sql .= " AND posts < $post_min";
		}

		if ($topic_state != 'all')
		{
			if ($topic_state)
			{
				$sql .= " AND state='$topic_state'";
			}
		}

		if ( $ignore_pin != "" )
		{
			$sql .= " AND pinned <> 1";
		}

		return $sql;

	}

	//-----------------------------------------
	// @mm_authorize: Authorizes current member
	// -----------
	// Accepts: (NOTHING: Should already be passed to init)
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function mm_authorize()
	{
		global $ibforums, $std;

		$pass_go = FALSE;

		if ( $ibforums->member['id'] )
		{
			if ( $ibforums->member['g_is_supmod'] )
			{
				$pass_go = TRUE;
			}
			else if ( $this->moderator['can_mm'] == 1 )
			{
				$pass_go = TRUE;
			}
		}

		return $pass_go;
	}

	//-----------------------------------------
	// @mm_check_id_in_forum: Checks to see if mm_id is in
    //                        the forum saved topic_mm_id
	// -----------
	// Accepts: (forum_topic_mm_id , this_mm_id)
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function mm_check_id_in_forum( $fid, $mm_data)
	{
		$retval = FALSE;

		if (  $mm_data['mm_forums'] == '*' OR strstr( ",". $mm_data['mm_forums'].",", ",".$fid."," ) )
		{
			$retval = TRUE;
		}

		return $retval;
	}

	//-----------------------------------------
	// @add_moderate_log: Adds entry to mod log
	// -----------
	// Accepts: (forum_id, topic_id, topic_title, post_id, title)
	// Returns: NOTHING (TRUE/FALSE)
	//-----------------------------------------

	function add_moderate_log($fid, $tid, $pid, $t_title, $mod_title='Unknown')
	{
		global $std, $ibforums, $DB;

		$DB->do_insert( 'moderator_logs', array (
												  'forum_id'    => $fid,
												  'topic_id'    => $tid,
												  'post_id'     => $pid,
												  'member_id'   => $ibforums->member['id'],
												  'member_name' => $ibforums->member['name'],
												  'ip_address'  => $ibforums->input['IP_ADDRESS'],
												  'http_referer'=> $_SERVER['HTTP_REFERER'],
												  'ctime'       => time(),
												  'topic_title' => $t_title,
												  'action'      => $mod_title,
												  'query_string'=> $_SERVER['QUERY_STRING'],
											  )  );
	}


}









?>