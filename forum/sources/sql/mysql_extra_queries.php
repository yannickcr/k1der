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
|   > MySQL DB Queries abstraction module
|   > Module written by Matt Mecham
|   > Date started: 26th November 2003
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/



class sql_extra_queries extends db_driver
{

     var $db  = "";
     var $tbl = "";

    /*========================================================================*/
    // Set up...
    /*========================================================================*/

    function sql_extra_queries( $obj )
    {
    	$this->db = $obj;

    	if ( ! $this->db->obj['sql_tbl_prefix'] )
    	{
    		$this->db->obj['sql_tbl_prefix'] = 'ibf_';
    	}

    	$this->tbl = $this->db->obj['sql_tbl_prefix'];
    }

    /*========================================================================*/

    function digest_get_topics( $a )
    {
    	return "SELECT tr.trid, tr.topic_id, tr.member_id as trmid, m.name, m.email, m.id, m.email_full, m.language, m.last_activity, t.title, t.*
				FROM ".SQL_PREFIX."tracker tr
				 LEFT JOIN ".SQL_PREFIX."topics t ON (t.tid=tr.topic_id)
				 LEFT JOIN ".SQL_PREFIX."members m ON (tr.member_id=m.id)
				WHERE tr.topic_track_type='{$a['type']}'
				AND t.approved=1
				AND t.last_post > {$a['last_time']}";
    }

    function digest_get_forums_topics( $a )
    {
    	return "SELECT t.*, p.*
			     FROM ".SQL_PREFIX."topics t
			      LEFT JOIN ".SQL_PREFIX."posts p on (t.topic_firstpost=p.pid)
			     WHERE t.forum_id={$a['forum_id']}
			      AND t.last_post > {$a['last_time']}";
    }

    function digest_get_forums( $a )
    {
    	return "SELECT ft.*, m.name, m.id, m.email
    			 FROM ".SQL_PREFIX."forum_tracker ft
    			 LEFT JOIN ".SQL_PREFIX."members m ON (m.id=ft.member_id)
    			 WHERE ft.forum_track_type='{$a['type']}'";
    }

    function acp_postoffice_concat_bit($a)
    {
    	return "CONCAT(',',mgroup_others,',') LIKE '%,{$a['gid']},%'";
    }



} // end class


?>