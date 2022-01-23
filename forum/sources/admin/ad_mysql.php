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
|   > mySQL Admin Stuff
|   > Module written by Matt Mecham
|   > Date started: 21st October 2002
|
|	> Module Version Number: 1.0.0
|   > Music listen to when coding this: Martin Grech - Open Heart Zoo
|   > Talk about useless information!
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

@set_time_limit(1200);


class ad_mysql {

	var $base_url;
	var $mysql_version   = "";
	var $true_version    = "";
	var $str_gzip_header = "\x1f\x8b\x08\x00\x00\x00\x00\x00";

	function auto_run()
	{
		global $INFO, $ibforums, $DB,  $std, $HTTP_POST_VARS, $HTTP_GET_VARS;

		if ( TRIAL_VERSION )
		{
			print "This feature is disabled in the trial version.";
			exit();
		}

		if ( strtolower($INFO['sql_driver']) != 'mysql' )
		{
			require_once( ROOT_PATH.'sources/admin/ad_'.strtolower($INFO['sql_driver']).'.php' );
			$dbdriver = new ad_sql();
			$dbdriver->auto_run();
		}

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------
		// Make sure we're a root admin, or else!
		//-----------------------------------------

		if ($ibforums->member['mgroup'] != $ibforums->vars['admin_group'])
		{
			$ibforums->admin->error("Sorry, these functions are for the root admin group only");
		}

		//-----------------------------------------
		// Get the mySQL version.
		//-----------------------------------------

		$DB->sql_get_version();

		$this->true_version  = $DB->true_version;
   		$this->mysql_version = $DB->mysql_version;

		switch($ibforums->input['code'])
		{
			case 'dotool':
				$this->run_tool();
				break;

			case 'runtime':
				$this->view_sql("SHOW STATUS");
				break;

			case 'system':
				$this->view_sql("SHOW VARIABLES");
				break;

			case 'processes':
				$this->view_sql("SHOW PROCESSLIST");
				break;

			case 'runsql':
				$q = $_POST['query'] == "" ? urldecode($_GET['query']) : $_POST['query'];
				$this->view_sql(trim(stripslashes($q)));
				break;

			case 'backup':
				$this->show_backup_form();
				break;

			case 'safebackup':
				$this->sbup_splash();
				break;

			case 'dosafebackup':
				$this->do_safe_backup();
				break;

			case 'export_tbl':
				$this->do_safe_backup(trim(urldecode(stripslashes($_GET['tbl']))));
				break;

			//-----------------------------------------
			default:
				$this->list_index();
				break;
		}
	}

	//-----------------------------------------
	// Back up baby, back up
	//-----------------------------------------

	function do_safe_backup($tbl_name="")
	{
		global $ibforums, $DB,  $std;

		if ($tbl_name == "")
		{
			// Auto all tables
			$skip        = intval($ibforums->input['skip']);
			$create_tbl  = intval($ibforums->input['create_tbl']);
			$enable_gzip = intval($ibforums->input['enable_gzip']);
			$filename    = 'ibf_dbbackup';
		}
		else
		{
			// Man. click export
			$skip        = 0;
			$create_tbl  = 0;
			$enable_gzip = 1;
			$filename    = $tbl_name;
		}

		$output = "";

		@header("Pragma: no-cache");

		$do_gzip = 0;

		if( $enable_gzip )
		{
			$phpver = phpversion();

			if($phpver >= "4.0")
			{
				if(extension_loaded("zlib"))
				{
					$do_gzip = 1;
				}
			}
		}

		if( $do_gzip != 0 )
		{
			@ob_start();
			@ob_implicit_flush(0);
			header("Content-Type: text/x-delimtext; name=\"$filename.sql.gz\"");
			header("Content-disposition: attachment; filename=$filename.sql.gz");
		}
		else
		{
			header("Content-Type: text/x-delimtext; name=\"$filename.sql\"");
			header("Content-disposition: attachment; filename=$filename.sql");
		}

		//-----------------------------------------
		// Get tables to work on
		//-----------------------------------------

		if ($tbl_name == "")
		{
			$tmp_tbl = $DB->get_table_names();

			foreach($tmp_tbl as $tbl)
			{
				// Ensure that we're only peeking at IBF tables

				if ( preg_match( "/^".$ibforums->vars['sql_tbl_prefix']."/", $tbl ) )
				{
					// We've started our headers, so print as we go to stop
					// poss memory problems

					$this->get_table_sql($tbl, $create_tbl, $skip);
				}
			}
		}
		else
		{
			$this->get_table_sql($tbl_name, $create_tbl, $skip);
		}

		//-----------------------------------------
		// GZIP?
		//-----------------------------------------

		if($do_gzip)
		{
			$size     = ob_get_length();
			$crc      = crc32(ob_get_contents());
			$contents = gzcompress(ob_get_contents());
			ob_end_clean();
			echo $this->str_gzip_header
				.substr($contents, 0, strlen($contents) - 4)
				.$this->gzip_four_chars($crc)
				.$this->gzip_four_chars($size);
		}

		exit();
	}

	//-----------------------------------------
	// Internal handler to return content from table
	//-----------------------------------------

	function get_table_sql($tbl, $create_tbl, $skip=0)
	{
		global $ibforums, $DB,  $std;

		if ($create_tbl)
		{
			// Generate table structure

			if ( $ibforums->input['addticks'] )
			{
				$DB->query("SHOW CREATE TABLE `".$ibforums->vars['sql_database'].".".$tbl."`");
			}
			else
			{
				$DB->query("SHOW CREATE TABLE ".$ibforums->vars['sql_database'].".".$tbl);
			}

			$ctable = $DB->fetch_row();

			echo $this->sql_strip_ticks($ctable['Create Table']).";\n";
		}

		// Are we skipping? Woohoo, where's me rope?!

		if ($skip == 1)
		{
			if ($tbl == $ibforums->vars['sql_tbl_prefix'].'admin_sessions'
				OR $tbl == $ibforums->vars['sql_tbl_prefix'].'sessions'
				OR $tbl == $ibforums->vars['sql_tbl_prefix'].'reg_anti_spam'
				OR $tbl == $ibforums->vars['sql_tbl_prefix'].'search_results'
			   )
			{
				return $ret;
			}
		}

		// Get the data

		$DB->query("SELECT * FROM $tbl");

		// Check to make sure rows are in this
		// table, if not return.

		$row_count = $DB->get_num_rows();

		if ($row_count < 1)
		{
			return TRUE;
		}

		//-----------------------------------------
		// Get col names
		//-----------------------------------------

		$f_list = "";

		$fields = $DB->get_result_fields();

		$cnt = count($fields);

		for( $i = 0; $i < $cnt; $i++ )
		{
			$f_list .= $fields[$i]->name . ", ";
		}

		$f_list = preg_replace( "/, $/", "", $f_list );

		while ( $row = $DB->fetch_row() )
		{
			//-----------------------------------------
			// Get col data
			//-----------------------------------------

			$d_list = "";

			for( $i = 0; $i < $cnt; $i++ )
			{
				if ( ! isset($row[ $fields[$i]->name ]) )
				{
					$d_list .= "NULL,";
				}
				elseif ( $row[ $fields[$i]->name ] != '' )
				{
					$d_list .= "'".$this->sql_add_slashes($row[ $fields[$i]->name ]). "',";
				}
				else
				{
					$d_list .= "'',";
				}
			}

			$d_list = preg_replace( "/,$/", "", $d_list );

			echo "INSERT INTO $tbl ($f_list) VALUES($d_list);\n";
		}

		return TRUE;

	}

	//-----------------------------------------
	// sql_strip_ticks from field names
	//-----------------------------------------

	function sql_strip_ticks($data)
	{
		return str_replace( "`", "", $data );
	}

	//-----------------------------------------
	// Add slashes to single quotes to stop sql breaks
	//-----------------------------------------

	function sql_add_slashes($data)
	{
		$data = str_replace('\\', '\\\\', $data);
        $data = str_replace('\'', '\\\'', $data);
        $data = str_replace("\r", '\r'  , $data);
        $data = str_replace("\n", '\n'  , $data);

        return $data;
	}

	//-----------------------------------------
	// Almost there!
	//-----------------------------------------

	function sbup_splash()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "This section allows you to backup your database.";

		$ibforums->admin->page_title  = "mySQL ".$this->true_version." Back Up";

		// Check for mySQL version..
		// Might change at some point..

		if ( $this->mysql_version < 3232 )
		{
			$ibforums->admin->error("Sorry, mySQL version of less than 3.23.21 are not support by this backup utility");
		}

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Simple Back Up" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<b>Back Up mySQL Database</b><br><br>Once you have clicked the link below, please wait
													until your browser prompts you with a dialogue box. This may take some time depending on
													the size of the database you are backing up.
													<br><br>
													<b><a href='{$ibforums->base_url}&act=mysql&code=dosafebackup&create_tbl={$ibforums->input['create_tbl']}&addticks={$ibforums->input['addticks']}&skip={$ibforums->input['skip']}&enable_gzip={$ibforums->input['enable_gzip']}'>Click here to start the backup</a></b>"
									     )      );


		$ibforums->html .= $ibforums->adskin->end_table();


		$ibforums->admin->output();


	}


	function show_backup_form()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "This section allows you to backup your database.
							  <br><br><b>Simple Backup</b>
							  <br>This function compiles a single back up file and prompts a browser dialogue box for you to save
							  the file. This is beneficial for PHP safe mode enabled hosts, but can only be used on small databases.
							  <!--<br><br>
							  <b>Advanced Backup</b>
							  <br>This function allows you to split the backup into smaller sections and saves the backup to disk.
							  <br>Note, this can only be used if you do not have PHP safe mode enabled.-->";

		$ibforums->admin->page_title  = "mySQL ".$this->true_version." Back Up";

		// Check for mySQL version..
		// Might change at some point..

		if ( $this->mysql_version < 3232 )
		{
			$ibforums->admin->error("Sorry, mySQL version of less than 3.23.21 are not support by this backup utility");
		}

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'mysql' ),
											  	  2 => array( 'code' , 'safebackup'),
									 	 )      );

		$ibforums->html .= $ibforums->adskin->start_table( "Simple Back Up" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<b>Add 'CREATE TABLE' statements?</b><br>Add backticks around the table name?<br>(if you get a mySQL error, enable this) <input type='checkbox' name='addticks' value=1>",
													$ibforums->adskin->form_yes_no( 'create_tbl', 1),
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<b>Skip non essential data?</b><br>Will not produce insert rows for ibf_sessions, ibf_admin_sessions, ibf_search_results, ibf_reg_anti_spam.",
													$ibforums->adskin->form_yes_no( 'skip', 1),
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<b>GZIP Content?</b><br>Will produce a smaller file if GZIP is enabled.",
													$ibforums->adskin->form_yes_no( 'enable_gzip', 1),
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Start Back Up");
		$ibforums->html .= $ibforums->adskin->end_table();


		$ibforums->admin->output();


	}



	//-----------------------------------------
	// Run mySQL queries
	//-----------------------------------------


	function view_sql($sql)
	{
		global $ibforums, $DB,  $std;

		$limit = 50;
		$start = intval($ibforums->input['st']) == "" ? 0 : intval($ibforums->input['st']);
		$pages = "";

		$ibforums->admin->page_detail = "This section allows you to administrate your mySQL database.$extra";
		$ibforums->admin->page_title  = "mySQL ".$this->true_version." Tool Box";

		$map = array( 'processes' => "SQL Processes",
					  'runtime'   => "SQL Runtime Information",
					  'system'    => "SQL System Variables",
					);

		if ($map[ $ibforums->input['code'] ] != "")
		{
			$tbl_title = $map[ $ibforums->input['code'] ];
			$man_query = 0;
		}
		else
		{
			$tbl_title = "Manual Query";
			$man_query = 1;
		}

		//-----------------------------------------

		if ($man_query == 1)
		{
			$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

			$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'mysql' ),
											      2 => array( 'code' , 'runsql'),
										 )      );

			$ibforums->html .= $ibforums->adskin->start_table( "Run Query" );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<center>".$ibforums->adskin->form_textarea("query", $sql )."</center>" ) );

			$ibforums->html .= $ibforums->adskin->end_form("Run a New Query");
			$ibforums->html .= $ibforums->adskin->end_table();


			// Check for drop, create and flush

			if ( preg_match( "/^DROP|CREATE|FLUSH/i", trim($sql) ) )
			{
				$ibforums->admin->error = "Sorry, those queries are not allowed for your safety";
			}
		}

		//-----------------------------------------

		$DB->return_die = 1;

		$DB->query($sql,1);

		// Check for errors..

		if ( $DB->error != "")
		{
			$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

			$ibforums->html .= $ibforums->adskin->start_table( "SQL Error" );

			$ibforums->html .= $ibforums->adskin->add_td_row( array($DB->error) );

			$ibforums->html .= $ibforums->adskin->end_table();

			$ibforums->admin->output(); // End output and script

		}

		if ( preg_match( "/^INSERT|UPDATE|DELETE|ALTER/i", trim($sql) ) )
		{
			// We can't show any info, and if we're here, there isn't
			// an error, so we're good to go.

			$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

			$ibforums->html .= $ibforums->adskin->start_table( "SQL Query Completed" );

			$ibforums->html .= $ibforums->adskin->add_td_row( array("Query: $sql<br>Executed Successfully") );

			$ibforums->html .= $ibforums->adskin->end_table();

			$ibforums->admin->output(); // End output and script

		}
		else if ( preg_match( "/^SELECT/i", $sql ) )
		{
			// Sort out the pages and stuff
			// auto limit if need be

			if ( ! preg_match( "/LIMIT[ 0-9,]+$/i", $sql ) )
			{
				$rows_returned = $DB->get_num_rows();

				if ($rows_returned > $limit)
				{
					// Get tbl name

					//$tbl_name = preg_replace( "/(".$ibforums->vars['sql_tbl_prefix']."\S+?)([\s\.,]|$)/i", "\\1", $sql );

					// Set up pages.

					$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $rows_returned,
														   'PER_PAGE'    => $limit,
														   'CUR_ST_VAL'  => $start,
														   'L_SINGLE'    => "Single Page",
														   'L_MULTI'     => "Pages: ",
														   'BASE_URL'    => $ibforums->base_url."&act=mysql&code=runsql&query=".urlencode($sql),
														 )
												  );

					$sql .= " LIMIT $start, $limit";

					// Re-run with limit

					$DB->query($sql, 1); /// bypass table swapping
				}
			}

		}

		$fields = $DB->get_result_fields();

		$cnt = count($fields);

		// Print the headers - we don't what or how many so...

		for( $i = 0; $i < $cnt; $i++ )
		{
			$ibforums->adskin->td_header[] = array( $fields[$i]->name , "*" );
		}

		$ibforums->html .= $ibforums->adskin->start_table( "Result: ".$tbl_title );

		if ($links != "")
		{
			$pages = $ibforums->adskin->add_td_basic( $links, 'left', 'tdrow2' );

			$ibforums->html .= $pages;
		}

		while( $r = $DB->fetch_row() )
		{

			// Grab the rows - we don't what or how many so...

			$rows = array();

			for( $i = 0; $i < $cnt; $i++ )
			{
				if ($man_query == 1)
				{
					// Limit output
					if ( strlen($r[ $fields[$i]->name ]) > 200 )
					{
						$r[ $fields[$i]->name ] = substr($r[ $fields[$i]->name ], 0, 200) .'...';
					}
				}

				$rows[] = wordwrap( htmlspecialchars(nl2br($r[ $fields[$i]->name ])) , 50, "<br>", 1 );
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( $rows );

		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// I'm A TOOL!
	//-----------------------------------------

	function run_tool()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "This section allows you to administrate your mySQL database.$extra";
		$ibforums->admin->page_title  = "mySQL ".$this->true_version." Tool Box";

		//-----------------------------------------
		// have we got some there tables me laddo?
		//-----------------------------------------

		$tables = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^tbl_(\S+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$tables[] = $match[1];
 				}
 			}
 		}

 		if ( count($tables) < 1 )
 		{
 			$ibforums->admin->error("You must choose some tables to run this tool on or it's just plain outright silly");
 		}

 		//-----------------------------------------
		// What tool is one running?
		// optimize analyze check repair
		//-----------------------------------------

		if (strtoupper($ibforums->input['tool']) == 'DROP' || strtoupper($ibforums->input['tool']) == 'CREATE' || strtoupper($ibforums->input['tool']) == 'FLUSH')
		{
			$ibforums->admin->error("You can't do that, sorry");
		}

		foreach($tables as $table)
		{
			$DB->query(strtoupper($ibforums->input['tool'])." TABLE $table");

			$fields = $DB->get_result_fields();

			$data = $DB->fetch_row();

			$cnt = count($fields);

			// Print the headers - we don't what or how many so...

			for( $i = 0; $i < $cnt; $i++ )
			{
				$ibforums->adskin->td_header[] = array( $fields[$i]->name , "*" );
			}

			$ibforums->html .= $ibforums->adskin->start_table( "Result: ".$ibforums->input['tool']." ".$table );

			// Grab the rows - we don't what or how many so...

			$rows = array();

			for( $i = 0; $i < $cnt; $i++ )
			{
				$rows[] = $data[ $fields[$i]->name ];
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( $rows );

			$ibforums->html .= $ibforums->adskin->end_table();
		}

		//-----------------------------------------

		$ibforums->admin->output();


	}


	//-----------------------------------------
	// SHOW ALL TABLES AND STUFF!
	// 5 hours ago this seemed like a damned good idea.
	//-----------------------------------------

	function list_index()
	{
		global $ibforums, $DB,  $std;

		$form_array = array();

		if ( $this->mysql_version < 3232 )
		{
			$extra = "<br><b>Note: your version of mySQL has a limited feature set and some tools have been removed</b>";
		}

		$ibforums->admin->page_detail = "This section allows you to administrate your mySQL database.$extra";
		$ibforums->admin->page_title  = "SQL ".$this->true_version." Tool Box";

		//-----------------------------------------
		// Show advanced stuff for mySQL > 3.23.03
		//-----------------------------------------

		$idx_size = 0;
		$tbl_size = 0;


		$ibforums->html .= "
				     <script language='Javascript'>
                     <!--
                     function CheckAll(cb) {
                         var fmobj = document.theForm;
                         for (var i=0;i<fmobj.elements.length;i++) {
                             var e = fmobj.elements[i];
                             if ((e.name != 'allbox') && (e.type=='checkbox') && (!e.disabled)) {
                                 e.checked = fmobj.allbox.checked;
                             }
                         }
                     }
                     function CheckCheckAll(cb) {
                         var fmobj = document.theForm;
                         var TotalBoxes = 0;
                         var TotalOn = 0;
                         for (var i=0;i<fmobj.elements.length;i++) {
                             var e = fmobj.elements[i];
                             if ((e.name != 'allbox') && (e.type=='checkbox')) {
                                 TotalBoxes++;
                                 if (e.checked) {
                                     TotalOn++;
                                 }
                             }
                         }
                         if (TotalBoxes==TotalOn) {fmobj.allbox.checked=true;}
                         else {fmobj.allbox.checked=false;}
                     }
                     //-->
                     </script>
                     ";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'mysql' ),
											      2 => array( 'code' , 'dotool'),
										 ) , "theForm"     );

		if ( $this->mysql_version >= 3230 )
		{

			$ibforums->adskin->td_header[] = array( "Table"      , "20%" );
			$ibforums->adskin->td_header[] = array( "Rows"       , "10%" );
			$ibforums->adskin->td_header[] = array( "Export"     , "10%" );
			$ibforums->adskin->td_header[] = array( '<input name="allbox" type="checkbox" value="Check All" onClick="CheckAll();">'     , "10%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Invision Power Board Tables" );

			$DB->query("SHOW TABLE STATUS FROM `".$ibforums->vars['sql_database']."`");

			while ( $r = $DB->fetch_row() )
			{
				// Check to ensure it's a table for this install...

				if ( ! preg_match( "/^".$ibforums->vars['sql_tbl_prefix']."/", $r['Name'] ) )
				{
					continue;
				}

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b><span style='font-size:12px'><a href='{$ibforums->adskin->base_url}&act=mysql&code=runsql&query=".urlencode("SELECT * FROM {$r['Name']}")."'>{$r['Name']}</a></span></b>",
														  "<center>{$r['Rows']}</center>",
														  "<center><a href='{$ibforums->adskin->base_url}&act=mysql&code=export_tbl&tbl={$r['Name']}'>Export</a></center></b>",
														  "<center><input name=\"tbl_{$r['Name']}\" value=1 type='checkbox' onClick=\"CheckCheckAll();\"></center>",
												 )      );
			}
		}
		else
		{
			// display a basic information table

			$ibforums->adskin->td_header[] = array( "Table"      , "60%" );
			$ibforums->adskin->td_header[] = array( "Rows"       , "30%" );
			$ibforums->adskin->td_header[] = array( '<input name="allbox" type="checkbox" value="Check All" onClick="CheckAll();">'     , "10%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Invision Power Board Tables" );

			$tables = $DB->get_table_names();

			foreach($tables as $tbl)
			{
				// Ensure that we're only peeking at IBF tables

				if ( ! preg_match( "/^".$ibforums->vars['sql_tbl_prefix']."/", $tbl ) )
				{
					continue;
				}

				$DB->query("SELECT COUNT(*) AS Rows FROM $tbl");

				$cnt = $DB->fetch_row();

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b><span style='font-size:12px'>$tbl</span></b>",
														  "<center>{$cnt['Rows']}</center>",
														  "<center><input name='tbl_$tbl' type='checkbox' onClick=\"CheckCheckAll(this);\"></center>",
												 )      );

			}

		}

		//-----------------------------------------
		// Add in the bottom stuff
		//-----------------------------------------

		if ( $this->mysql_version < 3232 )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "<select id='button' name='tool'>
													<option value='optimize'>Optimize Selected Tables</option>
												  </select>
												 <input type='submit' value='Go!' id='button'></form>", "center", "tdrow2" );
		}
		else
		{

			$ibforums->html .= $ibforums->adskin->add_td_basic( "<select id='button' name='tool'>
													<option value='optimize'>Optimize Selected Tables</option>
													<option value='repair'>Repair Selected Tables</option>
													<option value='check'>Check Selected Tables</option>
													<option value='analyze'>Analyze Selected Tables</option>
												  </select>
												 <input type='submit' value='Go!' id='button'></form>", "center", "tdrow2" );
		}

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'act'  , 'mysql' ),
											      2 => array( 'code' , 'runsql'),
										 )      );

		$ibforums->adskin->td_header[] = array( "{none}"      , "30%" );
		$ibforums->adskin->td_header[] = array( "{none}"      , "70%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Run a Query" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Manual Query</b><br>Advanced Users Only",
												  $ibforums->adskin->form_textarea("query", "" ),
												 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Run Query");
		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------

		$ibforums->admin->output();

	}

    function gzip_four_chars($val)
	{
		for ($i = 0; $i < 4; $i ++)
		{
			$return .= chr($val % 256);
			$val     = floor($val / 256);
		}

		return $return;
	}
}


?>