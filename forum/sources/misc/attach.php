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
|   > Attachment Handler module
|   > Module written by Matt Mecham
|   > Date started: 10th March 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class attach {

	/*-------------------------------------------------------------------------*/
	//
	// AUTO RUN
	//
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print, $forums;

        $ibforums->input['id']  = intval($ibforums->input['id']);
        $ibforums->input['tid'] = intval($ibforums->input['tid']);

        //-----------------------------------------
		// Got attachment types?
		//-----------------------------------------

		$ibforums->cache['attachtypes'] = array();

		$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}

		//-----------------------------------------
		// What to do..
		//-----------------------------------------

        switch( $ibforums->input['code'] )
        {
        	case 'showtopic':
        		$this->show_topic_attachments();
        		break;
        	default:
        		$this->show_post_attachment();
        		break;
        }
	}

	/*-------------------------------------------------------------------------*/
	//
	// SHOW TOPIC ATTACHMENTS ( MULTIPLE )
	//
	/*-------------------------------------------------------------------------*/

	function show_topic_attachments()
	{
		global $DB, $ibforums, $std, $forums, $print;

        if ( ! $ibforums->input['tid'] )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }

        //-----------------------------------------
        // get topic..
        //-----------------------------------------

        $topic = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.$ibforums->input['tid'] ) );

        if ( ! $topic['topic_hasattach'] )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }

        //-----------------------------------------
        // Check forum..
        //-----------------------------------------

        if ( ! $forums->forum_by_id[ $topic['forum_id'] ] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		//-----------------------------------------
		// Get forum skin and lang
		//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_forum', $ibforums->lang_id);
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_topic', $ibforums->lang_id);

        $this->html     = $std->load_template('skin_forum');

		//-----------------------------------------
		// aight.....
		//-----------------------------------------

		$this->output .= $this->html->forums_attachments_top($topic['title']);

		$DB->cache_add_query( 'forum_get_attachments', array( 'tid' => $ibforums->input['tid'] ) );

    	$DB->cache_exec_query();

		while ( $row = $DB->fetch_row() )
		{
			if ( $std->check_perms($forums->forum_by_id[ $row['forum_id'] ]['read_perms']) != TRUE )
			{
				continue;
			}

			$row['image']       = $ibforums->cache['attachtypes'][ $row['attach_ext'] ]['atype_img'];

			$row['short_name']  = $std->txt_truncate( $row['attach_file'], 30 );

			$row['attach_date'] = $std->get_date( $row['attach_date'], 'SHORT' );

			$row['real_size']   = $std->size_format( $row['attach_filesize'] );

			$this->output .= $this->html->forums_attachments_row( $row );
		}

		$this->output .= $this->html->forums_attachments_bottom();

		$print->pop_up_window($ibforums->lang['attach_title'], $this->output);
	}

	/*-------------------------------------------------------------------------*/
	//
	// SHOW POST ATTACHMENT ( SINGLE )
	//
	/*-------------------------------------------------------------------------*/

	function show_post_attachment()
	{
		global $DB, $ibforums, $std, $forums, $print;

        if ( ! $ibforums->input['id'] )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }

        //-----------------------------------------
        // get attachment
        //-----------------------------------------

        $DB->simple_construct( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_id=".$ibforums->input['id'] ) );
        $DB->simple_exec();

        if ( ! $attach = $DB->fetch_row() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
		}

        //-----------------------------------------
        // Handle post attachments.
        //-----------------------------------------

        if ( $ibforums->input['type'] == 'post' )
        {
        	//-----------------------------------------
        	// Get post thingy majiggy to check perms
        	//-----------------------------------------

        	$DB->cache_add_query( 'attach_get_perms', array( 'apid' => $attach['attach_pid'] ) );
        	$DB->cache_exec_query();

        	if ( ! $post = $DB->fetch_row() )
        	{
        		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
			}

        	if ( ! $forums->forum_by_id[ $post['forum_id'] ] )
        	{
        		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
        	}
        }
        else if ( $ibforums->input['type'] == 'msg' and $attach['attach_msg'] )
        {
        	$DB->simple_construct( array( 'select' => 'mt_id, mt_owner_id', 'from' => 'message_topics', 'where' => 'mt_owner_id='.$ibforums->member['id'].' AND mt_msg_id='.$attach['attach_msg'] ) );
        	$DB->simple_exec();

        	if ( ! $post = $DB->fetch_row() )
        	{
        		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
			}

        }
        else
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }

        //-----------------------------------------
        // Show attachment
        //-----------------------------------------

        $file = $ibforums->vars['upload_dir']."/".$attach['attach_location'];

		if ( file_exists( $file ) and ( $ibforums->cache['attachtypes'][ $attach['attach_ext'] ]['atype_mimetype'] != "" ) )
		{
			//-----------------------------------------
			// Update the "hits"..
			//-----------------------------------------

			$DB->simple_construct( array( 'update' => 'attachments', 'set' =>"attach_hits=attach_hits+1", 'where' => "attach_id=".$ibforums->input['id'] ) );
			$DB->simple_exec();

			//-----------------------------------------
			// Set up the headers..
			//-----------------------------------------

			//flush();

			@header( "Content-Type: ".$ibforums->cache['attachtypes'][ $attach['attach_ext'] ]['atype_mimetype'].
					 "\nContent-Disposition: inline; filename=\"".$attach['attach_file']
					 ."\"\nContent-Length: ".(string)(filesize( $file ) ) );

			//-----------------------------------------
			// Open and display the file..
			//-----------------------------------------

			$fh = fopen( $file, 'rb' );  // f486f, Set binary for Win even if it's an ascii file, it won't hurt.
			fpassthru( $fh );
			@fclose( $fh );
			exit();
		}
		else
		{
			//-----------------------------------------
			// File does not exist..
			//-----------------------------------------

			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
		}

    }


}

?>