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
|   > Access the help files
|   > Module written by Matt Mecham
|   > Date started: 24th February 2002
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

class help {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";

	/*-------------------------------------------------------------------------*/
	// Auto run
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_help', $ibforums->lang_id );

    	$this->html = $std->load_template('skin_help');

    	$this->base_url  = $ibforums->base_url;

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['CODE'])
    	{
    		case '01':
    			$this->show_section();
    			break;
    		case '02':
    			$this->do_search();
    			break;
    		default:
    			$this->show_titles();
    			break;
    	}

    	//-----------------------------------------
    	// If we have any HTML to print, do so...
    	//-----------------------------------------

    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
 	}

 	/*-------------------------------------------------------------------------*/
 	// Show Titles
 	/*-------------------------------------------------------------------------*/

 	function show_titles()
 	{
		global $ibforums, $DB, $std;

 		$seen = array();

 		$this->output = $this->html->start( $ibforums->lang['page_title'], $ibforums->lang['help_txt'], $ibforums->lang['choose_file'] );

 		$DB->simple_construct( array( 'select' => 'id, title, description',
 									  'from'   => 'faq',
 									  'order'  => 'title ASC'
 							 )      );
 		$DB->simple_exec();

 		$cnt = 0;

 		while ($row = $DB->fetch_row() )
 		{

 			if (isset($seen[ $row['title'] ]) )
 			{
 				continue;
 			}
 			else
 			{
 				$seen[ $row['title'] ] = 1;
 			}

 			$row['CELL_COLOUR'] = $cnt % 2 ? 'row1' : 'row2';

 			$cnt++;

 			$this->output .= $this->html->row($row);

 		}

 		$this->output .= $this->html->help_end();

 		$this->page_title = $ibforums->lang['page_title'];
 		$this->nav        = array( $ibforums->lang['page_title'] );
 	}

	/*-------------------------------------------------------------------------*/
	// Show section
	/*-------------------------------------------------------------------------*/

 	function show_section()
 	{
		global $ibforums, $DB, $std;

 		$id = $ibforums->input['HID'];

 		if (! preg_match( "/^(\d+)$/" , $id ) )
 		{
 			$this->show_titles();
 			return;
 		}

 		$DB->simple_construct( array( 'select' => 'id, title, text',
 									  'from'   => 'faq',
 									  'where'  => "ID='$id'"
 							 )      );
 		$DB->simple_exec();

 		$topic = $DB->fetch_row();

 		$this->output  = $this->html->start( $ibforums->lang['help_topic'], $ibforums->lang['topic_text'], $topic['title'] );
 		$this->output .= $this->html->display( $std->text_tidy( $topic['text'] ) );

 		$this->output .= $this->html->help_end();

 		$this->page_title = $ibforums->lang['help_topic'];
 		$this->nav        = array( "<a href='{$this->base_url}&amp;act=Help'>{$ibforums->lang['help_topics']}</a>", $ibforums->lang['help_topic'] );
 	}

    /*-------------------------------------------------------------------------*/
    // Do search
    /*-------------------------------------------------------------------------*/

 	function do_search()
 	{
		global $ibforums, $DB, $std;

 		if (empty( $ibforums->input['search_q'] ) )
 		{
 			$std->Error( array( LEVEL => 1, MSG => 'no_help_file') );
 		}

 		$search_string = strtolower( str_replace( "*" , "%", $ibforums->input['search_q'] ) );
 		$search_string = preg_replace( "/[<>\!\@£\$\^&\+\=\=\[\]\{\}\(\)\"':;\.,\/]/", "", $search_string );

 		$seen = array();

 		$this->output = $this->html->start( $ibforums->lang['page_title'], $ibforums->lang['results_txt'], $ibforums->lang['search_results'] );

 		$DB->cache_add_query( 'help_search', array( 'search_string' => $search_string ) );
		$DB->cache_exec_query();

 		$cnt = 0;

 		while ($row = $DB->fetch_row() )
 		{

 			if (isset($seen[ $row['title'] ]) )
 			{
 				continue;
 			}
 			else
 			{
 				$seen[ $row['title'] ] = 1;
 			}

 			$row['CELL_COLOUR'] = $cnt % 2 ? 'row1' : 'row2';

 			$cnt++;

 			$this->output .= $this->html->row($row);

 		}

 		if ($cnt == 0)
 		{
 			$this->output .= $this->html->no_results();
 		}

 		$this->output .= $this->html->help_end();

 		$this->page_title = $ibforums->lang['page_title'];
 		$this->nav        = array( "<a href='{$this->base_url}&amp;act=Help'>{$ibforums->lang['help_topics']}</a>", $ibforums->lang['results_title'] );
 	}


}

?>